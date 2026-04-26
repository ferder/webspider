<?php

namespace app\admin\controller\crawler;

use app\admin\model\crawler\ProductSnapshot as ProductSnapshotModel;

/**
 * ProductSnapshot管理
 */
class ProductSnapshot extends BaseCrud
{
    protected $model = null;
    protected $searchFields = 'id,product_id,site_id,source_url,title,sku,data_hash';
    protected $multiFields = '';

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new ProductSnapshotModel();
    }

    public function index()
    {
        return parent::index();
    }

    public function add()
    {
        if ($this->request->isPost()) {
            $params = $this->request->post('row/a');
            $this->validateJsonFields($params, ['snapshot_json']);
            $this->request->post(['row' => $params]);
        }
        return parent::add();
    }
}
