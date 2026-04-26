<?php

namespace app\common\model\spider;

use think\Model;

class ProductImage extends Model
{
    protected $name = 'product_images';
    protected $autoWriteTimestamp = 'int';
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
}
