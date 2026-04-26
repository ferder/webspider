<?php

namespace app\common\model\spider;

use think\Model;

class CrawlTask extends Model
{
    protected $name = 'crawl_tasks';
    protected $autoWriteTimestamp = 'int';
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
}
