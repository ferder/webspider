<?php

namespace app\admin\controller\crawler;

use app\admin\model\crawler\SiteRule as SiteRuleModel;

/**
 * SiteRule管理
 */
class SiteRule extends BaseCrud
{
    protected $model = null;
    protected $searchFields = 'id,site_id,rule_name,rule_type,product_url_pattern';
    protected $multiFields = 'status,rule_type';

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new SiteRuleModel();
    }

    public function index()
    {
        return parent::index();
    }

    public function add()
    {
        if ($this->request->isPost()) {
            $params = $this->request->post('row/a');
            $this->validateJsonFields($params, ['rule_config_json']);
            $this->request->post(['row' => $params]);
        }
        return parent::add();
    }

    public function edit($ids = null)
    {
        if ($this->request->isPost()) {
            $params = $this->request->post('row/a');
            $this->validateJsonFields($params, ['rule_config_json']);
            $this->request->post(['row' => $params]);
        }
        return parent::edit($ids);
    }
}
