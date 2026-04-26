<?php

namespace app\common\service;

use app\admin\model\crawler\SiteDetectReport;

class SiteDetectorService
{
    public function detect(array $site)
    {
        $base = rtrim($site['base_url'], '/');
        $result = [
            'robots_found' => $this->urlExists($base . '/robots.txt'),
            'sitemap_found' => $this->urlExists($base . '/sitemap.xml'),
            'product_sitemap_found' => $this->urlExists($base . '/product-sitemap.xml'),
            'jsonld_found' => false,
            'platform_guess' => 'unknown',
            'need_js_render' => 0,
            'has_product_links' => 0,
            'has_price' => 0,
            'has_images' => 0,
            'anti_crawl_level' => 'low',
            'detect_status' => 'success',
            'error_message' => '',
        ];
        $result['detect_score'] = (int)($result['robots_found'] * 20 + $result['sitemap_found'] * 20 + $result['product_sitemap_found'] * 20 + 40);
        $result['detect_result_json'] = json_encode($result, JSON_UNESCAPED_UNICODE);
        return $result;
    }

    public function saveReport($siteId, array $result)
    {
        return SiteDetectReport::create(array_merge($result, ['site_id' => $siteId]));
    }

    protected function urlExists($url)
    {
        $headers = @get_headers($url);
        return is_array($headers) && stripos($headers[0], '200') !== false;
    }
}
