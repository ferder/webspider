<?php

namespace app\common\service\spider;

use app\common\model\spider\Product;
use app\common\model\spider\ProductImage;
use app\common\model\spider\ProductVariant;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExcelExportService
{
    public const HEADERS = [
        '商品标题*', '商品属性*', '商品类型', '商品描述', '简短描述', 'SEO 标题', 'SEO 描述', 'SEO URL Handle',
        '商品上架', '商品收税', '库存规则*', '款式1', '款式2', '款式3', '商品售价*', '商品原价',
        '商品 SKU', '商品重量', '商品库存', '商品图片*'
    ];

    public function export(array $products, string $filename): array
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray(self::HEADERS, null, 'A1');

        $rowIndex = 2;
        $warnings = [];
        foreach ($products as $product) {
            $rows = $this->buildRows($product, $warnings);
            foreach ($rows as $row) {
                $sheet->fromArray(array_values($row), null, 'A' . $rowIndex);
                $rowIndex++;
            }
        }

        $dir = ROOT_PATH . 'runtime/export/';
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        $path = $dir . $filename;
        (new Xlsx($spreadsheet))->save($path);

        return ['file_path' => $path, 'warnings' => $warnings];
    }

    public function buildRows(array $product, array &$warnings = []): array
    {
        $images = $product['images'] ?? [];
        if (empty($images) && !empty($product['id'])) {
            $images = ProductImage::where('product_id', $product['id'])->order('image_position asc,id asc')->select()->toArray();
        }
        $imageUrls = array_values(array_filter(array_unique(array_column($images, 'image_url'))));
        $mainImage = $imageUrls[0] ?? '';

        $variants = $product['variants'] ?? [];
        if (empty($variants) && !empty($product['id'])) {
            $variants = ProductVariant::where('product_id', $product['id'])->select()->toArray();
        }

        $title = trim((string)($product['title'] ?? ''));
        if ($title === '') {
            $warnings[] = 'missing_required: 商品标题*';
        }

        $shortDescription = trim((string)($product['short_description'] ?? ''));
        if ($shortDescription === '') {
            $shortDescription = mb_substr(trim(strip_tags((string)($product['description'] ?? ''))), 0, 150);
        }

        $base = [
            '商品标题*' => $title,
            '商品属性*' => 'S',
            '商品类型' => (string)($product['product_type'] ?? ''),
            '商品描述' => (string)($product['description'] ?? ''),
            '简短描述' => $shortDescription,
            'SEO 标题' => (string)($product['seo_title'] ?? $title),
            'SEO 描述' => (string)($product['seo_description'] ?? $shortDescription),
            'SEO URL Handle' => (string)($product['handle'] ?? ''),
            '商品上架' => 'N',
            '商品收税' => 'Y',
            '库存规则*' => '1',
            '款式1' => '',
            '款式2' => '',
            '款式3' => '',
            '商品售价*' => $product['price'] ?? '',
            '商品原价' => $product['compare_price'] ?? '',
            '商品 SKU' => (string)($product['sku'] ?? ''),
            '商品重量' => (string)($product['weight'] ?? ''),
            '商品库存' => (string)($product['stock'] ?? ''),
            '商品图片*' => implode(',', $imageUrls),
        ];

        if (empty($variants) || count($variants) <= 1) {
            if ($base['商品售价*'] === '') {
                $warnings[] = 'missing_required: 商品售价*';
            }
            if ($base['商品图片*'] === '') {
                $warnings[] = 'missing_required: 商品图片*';
            }
            return [$base];
        }

        $rows = [];
        $master = $base;
        $master['商品属性*'] = 'M';
        $master['商品售价*'] = '';
        $master['商品原价'] = '';
        $master['商品 SKU'] = '';
        $master['商品库存'] = '';
        $rows[] = $master;

        foreach ($variants as $variant) {
            $variantRow = $base;
            $variantRow['商品属性*'] = 'P';
            $variantRow['款式1'] = (string)($variant['option1_value'] ?? '');
            $variantRow['款式2'] = (string)($variant['option2_value'] ?? '');
            $variantRow['款式3'] = (string)($variant['option3_value'] ?? '');
            $variantRow['商品售价*'] = $variant['price'] ?? '';
            $variantRow['商品原价'] = $variant['compare_price'] ?? '';
            $variantRow['商品 SKU'] = (string)($variant['sku'] ?? '');
            $variantRow['商品库存'] = (string)($variant['stock'] ?? '');
            $variantRow['商品图片*'] = (string)($variant['image_url'] ?? '') ?: $mainImage;
            $rows[] = $variantRow;
        }

        return $rows;
    }

    public function buildFilename(string $siteDomain = 'all'): string
    {
        $domain = preg_replace('/[^a-zA-Z0-9\-_.\x{4e00}-\x{9fa5}]/u', '_', $siteDomain);
        return sprintf('products_export_%s_%s.xlsx', $domain, date('YmdHis'));
    }

    public function queryProducts(array $filters): array
    {
        $query = Product::with(['images', 'variants']);

        if (!empty($filters['site_id'])) {
            $query->where('site_id', (int)$filters['site_id']);
        }
        if (!empty($filters['crawl_task_id'])) {
            $query->where('crawl_task_id', (int)$filters['crawl_task_id']);
        }
        if (!empty($filters['only_new'])) {
            $query->where('last_compare_status', 'new');
        }
        if (!empty($filters['only_updated'])) {
            $query->where('last_compare_status', 'updated');
        }
        if (!empty($filters['product_ids']) && is_array($filters['product_ids'])) {
            $query->whereIn('id', $filters['product_ids']);
        }

        return $query->select()->toArray();
    }
}
