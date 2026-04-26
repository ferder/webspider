<?php

namespace app\admin\model\crawler;

class SiteRule extends BaseModel
{
    protected $name = 'site_rules';

    protected static function init()
    {
        self::beforeWrite(function ($row) {
            if (isset($row['status']) && $row['status'] === 'active' && isset($row['site_id'])) {
                self::where('site_id', $row['site_id'])
                    ->where('id', '<>', $row['id'] ?? 0)
                    ->where('status', 'active')
                    ->update(['status' => 'disabled']);
            }
        });
    }

    public function site()
    {
        return $this->belongsTo(SiteSource::class, 'site_id', 'id')->setEagerlyType(0);
    }

    public function setRuleConfigJsonAttr($value)
    {
        return $this->encodeJson($value, 'rule_config_json');
    }
}
