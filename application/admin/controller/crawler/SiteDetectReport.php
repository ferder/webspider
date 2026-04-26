<?php

namespace app\admin\controller\crawler;

use app\admin\model\crawler\SiteDetectReport as SiteDetectReportModel;

/**
 * SiteDetectReport管理
 */
class SiteDetectReport extends BaseCrud
{
    protected $model = null;
    protected $searchFields = 'id,site_id,platform_guess,detect_status,error_message';
    protected $multiFields = 'detect_status,anti_crawl_level';

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new SiteDetectReportModel();
    }

    public function index()
    {
        return parent::index();
    }
}
