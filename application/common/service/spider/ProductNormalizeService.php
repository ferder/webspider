<?php

namespace app\common\service\spider;

use app\common\model\spider\Product;
use app\common\model\spider\ProductImage;
use app\common\model\spider\ProductSnapshot;
use app\common\model\spider\ProductVariant;
use think\Db;

class ProductNormalizeService
{
    public function normalizeAndSave(int $siteId, array $rawProduct, array $context = []): array
    {
        $normalized = $this->normalize($siteId, $rawProduct, $context);
        if (empty($normalized['title'])) {
            return ['status' => 'failed', 'message' => 'missing_title', 'warnings' => $normalized['warnings']];
        }

        return Db::transaction(function () use ($siteId, $rawProduct, $normalized, $context) {
            $existing = Product::where('site_id', $siteId)->where('source_url', $normalized['source_url'])->find();
            if ($existing) {
                if ($existing['data_hash'] === $normalized['data_hash']) {
                    $existing->save(['last_crawled_at' => time(), 'last_compare_status' => 'unchanged']);
                    return ['status' => 'unchanged', 'product_id' => (int)$existing['id'], 'warnings' => $normalized['warnings']];
                }

                ProductSnapshot::create([
                    'product_id' => $existing['id'],
                    'site_id' => $siteId,
                    'source_url' => $existing['source_url'],
                    'data_hash' => $existing['data_hash'],
                    'snapshot_json' => json_encode($existing->toArray(), JSON_UNESCAPED_UNICODE),
                    'change_type' => 'update',
                ]);

                $existing->allowField(true)->save(array_merge($normalized['product'], [
                    'data_hash' => $normalized['data_hash'],
                    'last_crawled_at' => time(),
                    'last_compare_status' => 'updated',
                ]));

                $this->replaceImages((int)$existing['id'], $normalized['images']);
                $this->replaceVariants((int)$existing['id'], $normalized['variants']);
                $existing->save(['image_count' => count($normalized['images']), 'variant_count' => count($normalized['variants'])]);

                return ['status' => 'updated', 'product_id' => (int)$existing['id'], 'warnings' => $normalized['warnings']];
            }

            $product = Product::create(array_merge($normalized['product'], [
                'site_id' => $siteId,
                'data_hash' => $normalized['data_hash'],
                'raw_data_json' => json_encode($rawProduct, JSON_UNESCAPED_UNICODE),
                'status' => 'draft',
                'last_crawled_at' => time(),
                'last_compare_status' => 'new',
                'crawl_task_id' => $context['crawl_task_id'] ?? 0,
                'image_count' => count($normalized['images']),
                'variant_count' => count($normalized['variants']),
            ]));

            $this->replaceImages((int)$product['id'], $normalized['images']);
            $this->replaceVariants((int)$product['id'], $normalized['variants']);
            ProductSnapshot::create([
                'product_id' => $product['id'],
                'site_id' => $siteId,
                'source_url' => $normalized['source_url'],
                'data_hash' => $normalized['data_hash'],
                'snapshot_json' => json_encode($rawProduct, JSON_UNESCAPED_UNICODE),
                'change_type' => 'new',
            ]);

            return ['status' => 'new', 'product_id' => (int)$product['id'], 'warnings' => $normalized['warnings']];
        });
    }

    public function normalize(int $siteId, array $raw, array $context = []): array
    {
        $title = trim((string)($raw['title'] ?? $raw['name'] ?? ''));
        $description = (string)($raw['description'] ?? $raw['body_html'] ?? $raw['content'] ?? '');
        $shortDescription = trim((string)($raw['short_description'] ?? ''));
        if ($shortDescription === '') {
            $plain = trim(strip_tags($description));
            $shortDescription = mb_substr($plain, 0, 150);
        }

        $images = $this->normalizeImages($raw['images'] ?? $raw['image'] ?? []);
        $variants = $this->normalizeVariants($raw['variants'] ?? [], $raw);
        $productType = trim((string)($raw['product_type'] ?? $raw['category'] ?? $raw['breadcrumb'] ?? ($context['site_name'] ?? '')));

        $normalized = [
            'source_url' => (string)($raw['source_url'] ?? $raw['url'] ?? ''),
            'source_product_id' => (string)($raw['source_product_id'] ?? $raw['id'] ?? ''),
            'title' => $title,
            'product_type' => $productType,
            'description' => $description,
            'short_description' => $shortDescription,
            'seo_title' => trim((string)($raw['seo_title'] ?? $raw['meta_title'] ?? $title)),
            'seo_description' => trim((string)($raw['seo_description'] ?? $raw['meta_description'] ?? $shortDescription)),
            'handle' => $this->generateUniqueHandle($siteId, (string)($raw['handle'] ?? $raw['slug'] ?? ''), $title, 0),
            'price' => $this->num($raw['price'] ?? null),
            'compare_price' => $this->num($raw['compare_price'] ?? $raw['original_price'] ?? null),
            'currency' => strtoupper((string)($raw['currency'] ?? $context['currency'] ?? 'USD')),
            'sku' => trim((string)($raw['sku'] ?? '')),
            'stock' => intval($raw['stock'] ?? $raw['inventory'] ?? 0),
            'weight' => (string)($raw['weight'] ?? ''),
        ];

        $warnings = [];
        if ($normalized['price'] === null) {
            $warnings[] = 'missing_price';
        }
        if (empty($images)) {
            $warnings[] = 'missing_images';
        }

        return [
            'product' => array_merge($normalized, ['raw_data_json' => json_encode($raw, JSON_UNESCAPED_UNICODE)]),
            'images' => $images,
            'variants' => $variants,
            'data_hash' => $this->buildDataHash($normalized, $images, $variants),
            'source_url' => $normalized['source_url'],
            'title' => $normalized['title'],
            'warnings' => $warnings,
        ];
    }

    public function generateUniqueHandle(int $siteId, string $rawHandle, string $title, int $excludeId = 0): string
    {
        $handle = trim($rawHandle) !== '' ? trim($rawHandle) : $title;
        $handle = strtolower($handle);
        $handle = preg_replace('/\s+/', '-', $handle);
        $handle = preg_replace('/[^a-z0-9\-]/', '', $handle);
        $handle = trim(preg_replace('/-+/', '-', $handle), '-');
        if ($handle === '') {
            $handle = 'product';
        }

        $base = $handle;
        $suffix = 0;
        while (true) {
            $query = Product::where('site_id', $siteId)->where('handle', $handle);
            if ($excludeId > 0) {
                $query->where('id', '<>', $excludeId);
            }
            if (!$query->find()) {
                return $handle;
            }
            $suffix++;
            $handle = $base . '-' . $suffix;
        }
    }

    protected function normalizeImages($rawImages): array
    {
        if (is_string($rawImages)) {
            $rawImages = [$rawImages];
        }
        if (!is_array($rawImages)) {
            return [];
        }

        $result = [];
        $seen = [];
        $pos = 1;
        foreach ($rawImages as $image) {
            $url = is_array($image) ? (string)($image['url'] ?? $image['src'] ?? '') : (string)$image;
            $url = trim($url);
            if ($url === '' || isset($seen[$url])) {
                continue;
            }
            $seen[$url] = true;
            $result[] = [
                'image_url' => $url,
                'image_position' => $pos,
                'is_main' => $pos === 1 ? 1 : 0,
                'image_hash' => md5($url),
            ];
            $pos++;
        }
        return $result;
    }

    protected function normalizeVariants(array $rawVariants, array $rawProduct): array
    {
        $variants = [];
        if (empty($rawVariants)) {
            $variants[] = [
                'option1_name' => 'Default',
                'option1_value' => 'Default',
                'option2_name' => '',
                'option2_value' => '',
                'option3_name' => '',
                'option3_value' => '',
                'price' => $this->num($rawProduct['price'] ?? null),
                'compare_price' => $this->num($rawProduct['compare_price'] ?? null),
                'sku' => (string)($rawProduct['sku'] ?? ''),
                'stock' => intval($rawProduct['stock'] ?? 0),
                'image_url' => (string)($rawProduct['image'] ?? ''),
            ];
            return $variants;
        }

        foreach ($rawVariants as $variant) {
            $variants[] = [
                'option1_name' => (string)($variant['option1_name'] ?? $variant['name1'] ?? 'Option1'),
                'option1_value' => (string)($variant['option1_value'] ?? $variant['option1'] ?? $variant['value1'] ?? ''),
                'option2_name' => (string)($variant['option2_name'] ?? $variant['name2'] ?? ''),
                'option2_value' => (string)($variant['option2_value'] ?? $variant['option2'] ?? $variant['value2'] ?? ''),
                'option3_name' => (string)($variant['option3_name'] ?? $variant['name3'] ?? ''),
                'option3_value' => (string)($variant['option3_value'] ?? $variant['option3'] ?? $variant['value3'] ?? ''),
                'price' => $this->num($variant['price'] ?? null),
                'compare_price' => $this->num($variant['compare_price'] ?? null),
                'sku' => (string)($variant['sku'] ?? ''),
                'stock' => intval($variant['stock'] ?? 0),
                'image_url' => (string)($variant['image_url'] ?? $variant['image'] ?? ''),
            ];
        }

        return $variants;
    }

    protected function replaceImages(int $productId, array $images): void
    {
        ProductImage::where('product_id', $productId)->delete();
        foreach ($images as $image) {
            ProductImage::create(array_merge($image, ['product_id' => $productId]));
        }
    }

    protected function replaceVariants(int $productId, array $variants): void
    {
        ProductVariant::where('product_id', $productId)->delete();
        foreach ($variants as $variant) {
            ProductVariant::create(array_merge($variant, ['product_id' => $productId]));
        }
    }

    protected function buildDataHash(array $product, array $images, array $variants): string
    {
        $payload = [
            'title' => $product['title'] ?? '',
            'price' => $product['price'] ?? null,
            'compare_price' => $product['compare_price'] ?? null,
            'sku' => $product['sku'] ?? '',
            'stock' => $product['stock'] ?? 0,
            'images' => array_column($images, 'image_url'),
            'variants' => $variants,
        ];
        return md5(json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    protected function num($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }
        return (float)$value;
    }
}
