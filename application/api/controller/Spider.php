<?php

namespace app\api\controller;

use app\common\controller\Api;

/**
 * 站点通用试采
 */
class Spider extends Api
{
    protected $noNeedLogin = ['probe'];
    protected $noNeedRight = '*';

    /**
     * 自动检测站点类型并进行通用试采
     *
     * @ApiMethod (POST)
     * @ApiParams (name="url", type="string", required=true, description="站点URL")
     */
    public function probe()
    {
        $url = trim((string)$this->request->post('url', $this->request->get('url', '')));
        if (!$url) {
            $this->error(__('Invalid parameters'));
        }

        $normalizedUrl = $this->normalizeUrl($url);
        if (!$normalizedUrl) {
            $this->error(__('Invalid parameters'));
        }

        $home = $this->httpGet($normalizedUrl);
        if (!$home['ok']) {
            $this->error('站点访问失败: ' . $home['message']);
        }

        $html = $home['body'];
        $detected = $this->detectPlatform($html);

        $samples = [
            'shopify'    => $this->probeShopify($normalizedUrl, $detected['shopify']),
            'woocommerce'=> $this->probeWooCommerce($normalizedUrl, $detected['woocommerce']),
            'sitemap'    => $this->probeSitemap($normalizedUrl),
            'jsonld'     => $this->probeJsonLd($html),
        ];

        $this->success('检测成功', [
            'url'      => $normalizedUrl,
            'detected' => $detected,
            'samples'  => $samples,
        ]);
    }

    protected function normalizeUrl($url)
    {
        if (!preg_match('/^https?:\/\//i', $url)) {
            $url = 'https://' . $url;
        }
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }
        return rtrim($url, '/');
    }

    protected function detectPlatform($html)
    {
        $low = strtolower($html);

        return [
            'shopify' => (bool)(strpos($low, 'cdn.shopify.com') !== false || strpos($low, 'shopify.theme') !== false || strpos($low, 'shopify-payment-button') !== false),
            'woocommerce' => (bool)(strpos($low, 'woocommerce') !== false || strpos($low, 'wc-ajax=') !== false || strpos($low, 'wp-content/plugins/woocommerce') !== false),
        ];
    }

    protected function probeShopify($baseUrl, $hint)
    {
        $resp = $this->httpGet($baseUrl . '/products.json?limit=5');
        if (!$resp['ok']) {
            return [
                'detected' => $hint,
                'ok'       => false,
                'message'  => $resp['message'],
                'items'    => [],
            ];
        }

        $json = json_decode($resp['body'], true);
        $products = [];
        if (is_array($json) && !empty($json['products']) && is_array($json['products'])) {
            foreach (array_slice($json['products'], 0, 5) as $row) {
                $products[] = [
                    'id'    => $row['id'] ?? null,
                    'title' => $row['title'] ?? '',
                    'handle'=> $row['handle'] ?? '',
                ];
            }
        }

        return [
            'detected' => $hint || !empty($products),
            'ok'       => !empty($products),
            'message'  => !empty($products) ? 'ok' : '返回成功但未拿到商品列表',
            'items'    => $products,
        ];
    }

    protected function probeWooCommerce($baseUrl, $hint)
    {
        $candidates = [
            '/wp-json/wc/store/products?per_page=5',
            '/wp-json/wp/v2/product?per_page=5',
        ];

        foreach ($candidates as $path) {
            $resp = $this->httpGet($baseUrl . $path);
            if (!$resp['ok']) {
                continue;
            }

            $json = json_decode($resp['body'], true);
            if (!is_array($json) || empty($json)) {
                continue;
            }

            $items = [];
            foreach (array_slice($json, 0, 5) as $row) {
                $items[] = [
                    'id'    => $row['id'] ?? null,
                    'name'  => $row['name'] ?? ($row['title']['rendered'] ?? ''),
                    'slug'  => $row['slug'] ?? '',
                ];
            }

            if (!empty($items)) {
                return [
                    'detected' => true,
                    'ok'       => true,
                    'message'  => 'ok',
                    'endpoint' => $path,
                    'items'    => $items,
                ];
            }
        }

        return [
            'detected' => $hint,
            'ok'       => false,
            'message'  => '未找到可公开访问的WooCommerce商品接口',
            'items'    => [],
        ];
    }

    protected function probeSitemap($baseUrl)
    {
        $candidates = ['/sitemap.xml', '/sitemap_index.xml', '/robots.txt'];

        foreach ($candidates as $path) {
            $resp = $this->httpGet($baseUrl . $path);
            if (!$resp['ok']) {
                continue;
            }

            if ($path === '/robots.txt') {
                preg_match_all('/^sitemap:\s*(.+)$/im', $resp['body'], $matches);
                $links = array_slice(array_values(array_unique($matches[1] ?? [])), 0, 10);
                if (!empty($links)) {
                    return [
                        'ok'    => true,
                        'from'  => $path,
                        'items' => $links,
                    ];
                }
                continue;
            }

            $links = $this->extractXmlLinks($resp['body']);
            if (!empty($links)) {
                return [
                    'ok'    => true,
                    'from'  => $path,
                    'items' => array_slice($links, 0, 10),
                ];
            }
        }

        return [
            'ok'      => false,
            'message' => '未发现可解析sitemap',
            'items'   => [],
        ];
    }

    protected function probeJsonLd($html)
    {
        $items = [];
        if (preg_match_all('/<script[^>]*type=["\']application\/ld\+json["\'][^>]*>(.*?)<\/script>/is', $html, $matches)) {
            foreach ($matches[1] as $raw) {
                $json = json_decode(trim(html_entity_decode($raw)), true);
                if (!$json) {
                    continue;
                }

                $flat = $this->flattenJsonLd($json);
                foreach ($flat as $node) {
                    $type = strtolower((string)($node['@type'] ?? ''));
                    if ($type === 'product' || (is_array($node['@type'] ?? null) && in_array('Product', $node['@type'], true))) {
                        $items[] = [
                            'name' => $node['name'] ?? '',
                            'sku'  => $node['sku'] ?? '',
                            'url'  => $node['url'] ?? '',
                        ];
                    }
                    if (count($items) >= 10) {
                        break 2;
                    }
                }
            }
        }

        return [
            'ok'    => !empty($items),
            'items' => $items,
        ];
    }

    protected function flattenJsonLd($json)
    {
        $nodes = [];
        if (isset($json['@graph']) && is_array($json['@graph'])) {
            foreach ($json['@graph'] as $entry) {
                if (is_array($entry)) {
                    $nodes[] = $entry;
                }
            }
        } elseif (array_keys($json) === range(0, count($json) - 1)) {
            foreach ($json as $entry) {
                if (is_array($entry)) {
                    $nodes[] = $entry;
                }
            }
        } else {
            $nodes[] = $json;
        }

        return $nodes;
    }

    protected function extractXmlLinks($xmlBody)
    {
        $links = [];
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($xmlBody);
        if (!$xml) {
            return $links;
        }

        $namespaces = $xml->getNamespaces(true);
        if (isset($namespaces[''])) {
            $xml->registerXPathNamespace('n', $namespaces['']);
            $nodes = $xml->xpath('//n:loc');
        } else {
            $nodes = $xml->xpath('//loc');
        }

        if (is_array($nodes)) {
            foreach ($nodes as $node) {
                $links[] = trim((string)$node);
            }
        }

        return array_values(array_unique(array_filter($links)));
    }

    protected function httpGet($url)
    {
        $headers = [
            'User-Agent: Mozilla/5.0 (compatible; WebSpiderProbe/1.0; +https://example.local)',
            'Accept: text/html,application/json,application/xml,text/plain,*/*;q=0.9',
        ];

        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL            => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_TIMEOUT        => 12,
                CURLOPT_CONNECTTIMEOUT => 6,
                CURLOPT_MAXREDIRS      => 3,
                CURLOPT_HTTPHEADER     => $headers,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
            ]);
            $body = curl_exec($ch);
            $error = curl_error($ch);
            $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($body === false) {
                return ['ok' => false, 'message' => $error ?: 'curl error', 'body' => ''];
            }
            if ($httpCode >= 400 || $httpCode === 0) {
                return ['ok' => false, 'message' => 'HTTP ' . $httpCode, 'body' => (string)$body];
            }
            return ['ok' => true, 'message' => 'ok', 'body' => (string)$body];
        }

        $context = stream_context_create([
            'http' => [
                'method'  => 'GET',
                'timeout' => 12,
                'header'  => implode("\r\n", $headers),
            ]
        ]);
        $body = @file_get_contents($url, false, $context);
        if ($body === false) {
            return ['ok' => false, 'message' => 'request failed', 'body' => ''];
        }

        return ['ok' => true, 'message' => 'ok', 'body' => (string)$body];
    }
}
