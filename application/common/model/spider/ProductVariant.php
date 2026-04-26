<?php

namespace app\common\model\spider;

use think\Model;

class ProductVariant extends Model
{
    protected $name = 'product_variants';
    protected $autoWriteTimestamp = 'int';
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
}
