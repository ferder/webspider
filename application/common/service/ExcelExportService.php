<?php

namespace app\common\service;

use app\admin\model\crawler\ExcelTemplateField;

class ExcelExportService
{
    public function getTemplateFields()
    {
        return ExcelTemplateField::order('field_order asc,id asc')->select();
    }

    public function buildRowExample(array $product)
    {
        return [
            '商品标题' => $product['title'] ?? '',
            '商品属性' => $product['property_type'] ?? 'S',
            '商品类型' => $product['product_type'] ?? '',
            '商品售价' => $product['price'] ?? 0,
            '商品图片' => $product['image'] ?? '',
            '商品上架' => 'N',
            '商品收税' => 'Y',
            '库存规则' => '1',
        ];
    }
}
