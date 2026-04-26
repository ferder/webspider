<?php

namespace app\common\service;

use app\admin\model\crawler\Product;

class ProductNormalizeService
{
    public function normalize(array $raw)
    {
        $title = $raw['title'] ?? '';
        $sourceUrl = $raw['source_url'] ?? '';
        return [
            'site_id' => $raw['site_id'] ?? 0,
            'source_url' => $sourceUrl,
            'title' => $title,
            'handle' => $this->generateHandle($title),
            'price' => $raw['price'] ?? 0,
            'compare_price' => $raw['compare_price'] ?? 0,
            'currency' => $raw['currency'] ?? 'EUR',
            'sku' => $raw['sku'] ?? '',
            'stock' => $raw['stock'] ?? 0,
            'status' => $raw['status'] ?? 'draft',
            'data_hash' => $this->generateDataHash($raw),
            'raw_data_json' => json_encode($raw, JSON_UNESCAPED_UNICODE),
            'last_crawled_at' => time(),
        ];
    }

    public function upsertProduct(array $raw)
    {
        $data = $this->normalize($raw);
        $existing = Product::where('source_url', $data['source_url'])->find();
        if (!$existing) {
            Product::create($data);
            return 'new';
        }
        if ($existing['data_hash'] !== $data['data_hash']) {
            $existing->allowField(true)->save($data);
            return 'updated';
        }
        return 'same';
    }

    public function generateHandle($title)
    {
        $value = strtolower(trim($title));
        $value = preg_replace('/[^a-z0-9\s-]/', '', $value);
        return trim(preg_replace('/[\s-]+/', '-', $value), '-');
    }

    public function generateDataHash(array $data)
    {
        return md5(json_encode($data, JSON_UNESCAPED_UNICODE));
    }
}
