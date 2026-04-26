<?php

namespace app\admin\model\crawler;

class ProductVariant extends BaseModel
{
    protected $name = 'product_variants';

    public function setRawDataJsonAttr($value)
    {
        return $this->encodeJson($value, 'raw_data_json');
    }
}
