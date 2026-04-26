<?php

namespace app\common\model\spider;

use think\Model;

class ProductSnapshot extends Model
{
    protected $name = 'product_snapshots';
    protected $autoWriteTimestamp = 'int';
    protected $createTime = 'createtime';
    protected $updateTime = false;
}
