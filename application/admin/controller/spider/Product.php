<?php

namespace app\admin\controller\spider;

use app\common\controller\Backend;

class Product extends Backend
{
    protected $model = null;
    protected $searchFields = 'id,title,source_url,sku';

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('app\\common\\model\\spider\\Product');
    }

    public function index()
    {
        $this->relationSearch = false;
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $hasVariants = $this->request->request('has_variants', '');
            $hasImages = $this->request->request('has_images', '');
            $recentRange = $this->request->request('recent_crawled', '');

            $query = $this->model->where($where);
            if ($hasVariants !== '') {
                $query->where('variant_count', $hasVariants ? '>' : '=', 0);
            }
            if ($hasImages !== '') {
                $query->where('image_count', $hasImages ? '>' : '=', 0);
            }
            if ($recentRange !== '') {
                $arr = explode(' - ', $recentRange);
                if (count($arr) === 2) {
                    $query->whereTime('last_crawled_at', 'between', [$arr[0], $arr[1]]);
                }
            }

            $list = $query->order($sort, $order)->paginate($limit);
            return json(['total' => $list->total(), 'rows' => $list->items()]);
        }
        return $this->view->fetch();
    }

    public function detail($ids = null)
    {
        $row = $this->model->with(['images', 'variants', 'snapshots' => function ($q) {
            $q->limit(10);
        }])->where('id', $ids)->find();
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $this->view->assign('row', $row);
        return $this->view->fetch();
    }

    public function markStatus($ids = null)
    {
        $status = $this->request->post('status', '');
        if (!in_array($status, ['draft', 'active', 'inactive', 'deleted'])) {
            $this->error('invalid_status');
        }
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $row->save(['status' => $status]);
        $this->success();
    }
}
