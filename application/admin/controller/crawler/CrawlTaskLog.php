<?php

namespace app\admin\controller\crawler;

use app\admin\model\crawler\CrawlTaskLog as CrawlTaskLogModel;

/**
 * CrawlTaskLog管理
 */
class CrawlTaskLog extends BaseCrud
{
    protected $model = null;
    protected $searchFields = 'id,task_id,site_id,log_level,stage,message';
    protected $multiFields = 'log_level,stage';

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new CrawlTaskLogModel();
    }

    public function index()
    {
        return parent::index();
    }

    public function add()
    {
        if ($this->request->isPost()) {
            $params = $this->request->post('row/a');
            $this->validateJsonFields($params, ['context_json']);
            $this->request->post(['row' => $params]);
        }
        return parent::add();
    }
}
