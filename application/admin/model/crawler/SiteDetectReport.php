<?php

namespace app\admin\model\crawler;

class SiteDetectReport extends BaseModel
{
    protected $name = 'site_detect_reports';

    public function site()
    {
        return $this->belongsTo(SiteSource::class, 'site_id', 'id')->setEagerlyType(0);
    }

    public function setDetectResultJsonAttr($value)
    {
        return $this->encodeJson($value, 'detect_result_json');
    }
}
