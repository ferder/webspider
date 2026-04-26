<?php

namespace app\admin\controller\crawler;

use app\admin\model\crawler\ProductVariant as ProductVariantModel;

/**
 * ProductVariant管理
 */
class ProductVariant extends BaseCrud
{
    protected $model = null;
    protected $searchFields = 'id,product_id,sku,option1_value,option2_value,option3_value';
    protected $multiFields = '';

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new ProductVariantModel();
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
