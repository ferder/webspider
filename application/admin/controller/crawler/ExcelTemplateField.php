<?php

namespace app\admin\controller\crawler;

use app\admin\model\crawler\ExcelTemplateField as ExcelTemplateFieldModel;

/**
 * ExcelTemplateField管理
 */
class ExcelTemplateField extends BaseCrud
{
    protected $model = null;
    protected $searchFields = 'id,field_name,field_key,is_required,field_order';
    protected $multiFields = 'is_required';

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new ExcelTemplateFieldModel();
    }

    public function index()
    {
        return parent::index();
    }
}
