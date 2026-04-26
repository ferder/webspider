<?php

namespace app\admin\model\crawler;

class Product extends BaseModel
{
    protected $name = 'products';

    public function setRawDataJsonAttr($value)
    {
        return $this->encodeJson($value, 'raw_data_json');
    }
}
