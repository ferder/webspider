<?php

namespace app\admin\controller\spider;

use app\common\controller\Backend;
use app\common\model\spider\ExportJob as ExportJobModel;
use app\common\service\spider\ExcelExportService;

class Exportjob extends Backend
{
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('app\\common\\model\\spider\\ExportJob');
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

    public function create()
    {
        if (!$this->request->isPost()) {
            return $this->view->fetch();
        }

        $params = $this->request->post('row/a', []);
        $job = ExportJobModel::create([
            'site_id' => (int)($params['site_id'] ?? 0),
            'crawl_task_id' => (int)($params['crawl_task_id'] ?? 0),
            'filter_type' => (string)($params['filter_type'] ?? 'all'),
            'product_ids_json' => json_encode($params['product_ids'] ?? [], JSON_UNESCAPED_UNICODE),
            'status' => 'pending',
            'error_message' => '',
        ]);

        $this->runExport((int)$job['id']);
        $this->success();
    }

    protected function runExport(int $jobId): void
    {
        $job = ExportJobModel::get($jobId);
        $job->save(['status' => 'running', 'error_message' => '']);
        try {
            $service = new ExcelExportService();
            $filters = [
                'site_id' => (int)$job['site_id'],
                'crawl_task_id' => (int)$job['crawl_task_id'],
                'only_new' => $job['filter_type'] === 'new',
                'only_updated' => $job['filter_type'] === 'updated',
                'product_ids' => $job['filter_type'] === 'selected' ? (json_decode((string)$job['product_ids_json'], true) ?: []) : [],
            ];
            $products = $service->queryProducts($filters);
            $filename = $service->buildFilename((string)($job['site_id'] ?: 'all'));
            $result = $service->export($products, $filename);
            $relativePath = str_replace(ROOT_PATH, '/', $result['file_path']);
            $job->save([
                'status' => 'completed',
                'file_path' => $relativePath,
                'warning_message' => json_encode($result['warnings'] ?? [], JSON_UNESCAPED_UNICODE),
                'error_message' => '',
            ]);
        } catch (\Throwable $e) {
            $job->save(['status' => 'failed', 'error_message' => $e->getMessage()]);
        }
    }
}
