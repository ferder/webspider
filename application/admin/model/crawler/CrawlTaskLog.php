<?php

namespace app\admin\model\crawler;

class CrawlTaskLog extends BaseModel
{
    protected $name = 'crawl_task_logs';
    protected $updateTime = false;

    public function setContextJsonAttr($value)
    {
        return $this->encodeJson($value, 'context_json');
    }
}
