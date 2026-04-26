<?php

namespace app\admin\controller\crawler;

use app\admin\model\crawler\ProductImage as ProductImageModel;

/**
 * ProductImage管理
 */
class ProductImage extends BaseCrud
{
    protected $model = null;
    protected $searchFields = 'id,product_id,variant_id,image_url,image_hash';
    protected $multiFields = 'is_main';

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new ProductImageModel();
    }

    public function index()
    {
        return parent::index();
    }
}
