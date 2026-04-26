<?php

namespace app\admin\controller\crawler;

use app\admin\model\crawler\CrawlTask as CrawlTaskModel;

/**
 * CrawlTask管理
 */
class CrawlTask extends BaseCrud
{
    protected $model = null;
    protected $searchFields = 'id,task_no,site_id,status,task_type,error_message';
    protected $multiFields = 'status,task_type';

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new CrawlTaskModel();
    }

    public function index()
    {
        return parent::index();
    }

    public function add()
    {
        if ($this->request->isPost()) {
            $params = $this->request->post('row/a');
            $this->validateJsonFields($params, ['task_config_json']);
            if (empty($params['task_no'])) {
                $params['task_no'] = 'TASK' . date('YmdHis') . mt_rand(1000, 9999);
            }
            $this->request->post(['row' => $params]);
        }
        return parent::add();
    }

    public function pause($ids = null)
    {
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $row->save(['status' => 'paused']);
        $this->success();
    }

    public function markfailed($ids = null)
    {
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $row->save(['status' => 'failed']);
        $this->success();
    }

    public function workerpull()
    {
        $task = $this->model->where('status', 'pending')->order('id asc')->find();
        if (!$task) {
            $this->success('no task', null, ['task' => null]);
        }
        $task->save(['status' => 'running', 'start_time' => time()]);
        $this->success('ok', null, ['task' => $task]);
    }

    public function workerreport()
    {
        $id = (int)$this->request->post('task_id', 0);
        $status = (string)$this->request->post('status', 'completed');
        $message = (string)$this->request->post('message', '');
        $task = $this->model->get($id);
        if (!$task) {
            $this->error('任务不存在');
        }
        $task->save(['status' => $status, 'end_time' => time(), 'error_message' => $message]);
        $this->success();
    }
}
