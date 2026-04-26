<?php

namespace app\admin\model\crawler;

class ProductSnapshot extends BaseModel
{
    protected $name = 'product_snapshots';
    protected $updateTime = false;

    public function setSnapshotJsonAttr($value)
    {
        return $this->encodeJson($value, 'snapshot_json');
    }
}
