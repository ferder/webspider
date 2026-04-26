<?php

namespace app\common\model\spider;

use think\Model;

class ExportJob extends Model
{
    protected $name = 'export_jobs';
    protected $autoWriteTimestamp = 'int';
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
}
