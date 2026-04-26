<?php

namespace app\admin\model\crawler;

use think\model\concern\SoftDelete;

class SiteSource extends BaseModel
{
    protected $name = 'site_sources';

    public function getPlatformTypeList()
    {
        return [
            'unknown' => 'unknown',
            'shopify' => 'shopify',
            'woocommerce' => 'woocommerce',
            'magento' => 'magento',
            'prestashop' => 'prestashop',
            'shopware' => 'shopware',
            'custom' => 'custom',
        ];
    }

    public function getStatusList()
    {
        return [
            'pending_detect' => 'pending_detect',
            'detecting' => 'detecting',
            'detected' => 'detected',
            'ready' => 'ready',
            'manual_required' => 'manual_required',
            'disabled' => 'disabled',
        ];
    }
}
