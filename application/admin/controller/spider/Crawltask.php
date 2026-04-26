<?php

namespace app\admin\controller\spider;

use app\common\controller\Backend;
use app\common\service\spider\FullCrawlService;

class Crawltask extends Backend
{
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('app\\common\\model\\spider\\CrawlTask');
    }

    public function index()
    {
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $list = $this->model->where($where)->order($sort, $order)->paginate($limit);
            return json(['total' => $list->total(), 'rows' => $list->items()]);
        }
        return $this->view->fetch();
    }

    public function createFullCrawl()
    {
        if (!$this->request->isPost()) {
            return $this->view->fetch();
        }
        $row = $this->request->post('row/a', []);
        $task = $this->model->create([
            'task_type' => 'full_crawl',
            'site_id' => (int)($row['site_id'] ?? 0),
            'rule_id' => (int)($row['rule_id'] ?? 0),
            'max_products' => (int)($row['max_products'] ?? 0),
            'max_pages' => (int)($row['max_pages'] ?? 0),
            'crawl_interval' => (int)($row['crawl_interval'] ?? 0),
            'only_new' => !empty($row['only_new']) ? 1 : 0,
            'collect_image_url' => !empty($row['collect_image_url']) ? 1 : 0,
            'status' => 'pending',
        ]);

        $rawProducts = json_decode((string)$this->request->post('raw_products_json', '[]'), true);
        if (!is_array($rawProducts)) {
            $rawProducts = [];
        }
        $resume = (bool)$this->request->post('resume', false);
        (new FullCrawlService())->run((int)$task['id'], $rawProducts, $resume);
        $this->success();
    }
}
