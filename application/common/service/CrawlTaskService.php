<?php

namespace app\common\service;

use app\admin\model\crawler\CrawlTask;
use app\admin\model\crawler\CrawlTaskLog;

class CrawlTaskService
{
    public function createTask(array $data)
    {
        if (empty($data['task_no'])) {
            $data['task_no'] = 'TASK' . date('YmdHis') . mt_rand(1000, 9999);
        }
        return CrawlTask::create($data);
    }

    public function updateStatus($taskId, $status, $message = '')
    {
        $update = ['status' => $status];
        if ($message) {
            $update['error_message'] = $message;
        }
        if (in_array($status, ['running'])) {
            $update['start_time'] = time();
        }
        if (in_array($status, ['failed', 'completed', 'manual_required'])) {
            $update['end_time'] = time();
        }
        return CrawlTask::update($update, ['id' => $taskId]);
    }

    public function writeLog($taskId, $siteId, $level, $stage, $message, array $context = [])
    {
        return CrawlTaskLog::create([
            'task_id' => $taskId,
            'site_id' => $siteId,
            'log_level' => $level,
            'stage' => $stage,
            'message' => $message,
            'context_json' => json_encode($context, JSON_UNESCAPED_UNICODE),
        ]);
    }

    public function pauseTask($taskId)
    {
        return $this->updateStatus($taskId, 'paused');
    }

    public function failTask($taskId, $message)
    {
        return $this->updateStatus($taskId, 'failed', $message);
    }

    public function completeTask($taskId)
    {
        return $this->updateStatus($taskId, 'completed');
    }
}
