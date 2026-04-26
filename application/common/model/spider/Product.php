<?php

namespace app\common\model\spider;

use think\Model;

class Product extends Model
{
    protected $name = 'products';
    protected $autoWriteTimestamp = 'int';
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'id')->order('image_position asc,id asc');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'product_id', 'id')->order('id asc');
    }

    public function snapshots()
    {
        return $this->hasMany(ProductSnapshot::class, 'product_id', 'id')->order('id desc');
    }
}
