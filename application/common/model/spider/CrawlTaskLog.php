<?php

namespace app\common\model\spider;

use think\Model;

class CrawlTaskLog extends Model
{
    protected $name = 'crawl_task_logs';
    protected $autoWriteTimestamp = 'int';
    protected $createTime = 'createtime';
    protected $updateTime = false;
}
