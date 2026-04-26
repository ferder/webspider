<?php

namespace app\admin\controller\crawler;

use app\admin\model\crawler\Product as ProductModel;

/**
 * Product管理
 */
class Product extends BaseCrud
{
    protected $model = null;
    protected $searchFields = 'id,site_id,title,source_url,sku,status';
    protected $multiFields = 'status,currency';

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new ProductModel();
    }

    public function index()
    {
        return parent::index();
    }

    public function add()
    {
        if ($this->request->isPost()) {
            $params = $this->request->post('row/a');
            $this->validateJsonFields($params, ['raw_data_json']);
            $this->request->post(['row' => $params]);
        }
        return parent::add();
    }

    public function edit($ids = null)
    {
        if ($this->request->isPost()) {
            $params = $this->request->post('row/a');
            $this->validateJsonFields($params, ['raw_data_json']);
            $this->request->post(['row' => $params]);
        }
        return parent::edit($ids);
    }
}
