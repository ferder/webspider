<?php

namespace app\admin\controller\crawler;

use app\admin\model\crawler\ExportJob as ExportJobModel;

/**
 * ExportJob管理
 */
class ExportJob extends BaseCrud
{
    protected $model = null;
    protected $searchFields = 'id,job_no,site_id,task_id,status,export_type,file_path';
    protected $multiFields = 'status,export_type';

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new ExportJobModel();
    }

    public function index()
    {
        return parent::index();
    }
}
