<?php

namespace app\admin\model\crawler;

class CrawlTask extends BaseModel
{
    protected $name = 'crawl_tasks';

    public function site()
    {
        return $this->belongsTo(SiteSource::class, 'site_id', 'id')->setEagerlyType(0);
    }

    public function rule()
    {
        return $this->belongsTo(SiteRule::class, 'rule_id', 'id')->setEagerlyType(0);
    }

    public function setTaskConfigJsonAttr($value)
    {
        return $this->encodeJson($value, 'task_config_json');
    }
}
