<?php

namespace app\common\service\spider;

use app\common\model\spider\CrawlTask;
use app\common\model\spider\CrawlTaskLog;
use app\common\model\spider\Product;

class FullCrawlService
{
    protected $normalizer;

    public function __construct()
    {
        $this->normalizer = new ProductNormalizeService();
    }

    public function run(int $taskId, array $rawProducts, bool $resume = false): array
    {
        $task = CrawlTask::get($taskId);
        if (!$task) {
            throw new \RuntimeException('crawl_task_not_found');
        }

        $task->save(['status' => 'running']);
        $this->log($taskId, 'task_started', ['resume' => $resume]);

        $seenSourceUrls = [];
        $stats = ['success' => 0, 'fail' => 0, 'new' => 0, 'updated' => 0, 'unchanged' => 0];
        foreach ($rawProducts as $raw) {
            try {
                $result = $this->normalizer->normalizeAndSave((int)$task['site_id'], $raw, ['crawl_task_id' => $taskId]);
                $sourceUrl = (string)($raw['source_url'] ?? $raw['url'] ?? '');
                if ($sourceUrl !== '') {
                    $seenSourceUrls[] = $sourceUrl;
                }
                if ($result['status'] === 'failed') {
                    $stats['fail']++;
                    $this->log($taskId, 'product_failed', ['url' => $sourceUrl, 'reason' => $result['message']]);
                } else {
                    $stats['success']++;
                    $stats[$result['status']] = ($stats[$result['status']] ?? 0) + 1;
                    if (!empty($result['warnings'])) {
                        $this->log($taskId, 'product_warning', ['url' => $sourceUrl, 'warnings' => $result['warnings']]);
                    }
                }
            } catch (\Throwable $e) {
                $stats['fail']++;
                $snippet = json_encode($raw, JSON_UNESCAPED_UNICODE);
                $this->log($taskId, 'product_exception', [
                    'url' => (string)($raw['source_url'] ?? $raw['url'] ?? ''),
                    'reason' => $e->getMessage(),
                    'raw_snippet' => mb_substr($snippet, 0, 500),
                ]);
            }

            $task->save([
                'total_products' => $stats['success'] + $stats['fail'],
                'success_count' => $stats['success'],
                'fail_count' => $stats['fail'],
                'new_count' => $stats['new'],
                'update_count' => $stats['updated'],
            ]);
        }

        if (!empty($seenSourceUrls)) {
            Product::where('site_id', $task['site_id'])
                ->whereNotIn('source_url', array_values(array_unique($seenSourceUrls)))
                ->where('status', '<>', 'deleted')
                ->update(['last_compare_status' => 'inactive_candidate']);
        }

        $task->save(['status' => 'completed']);
        $this->log($taskId, 'task_completed', $stats);
        return $stats;
    }

    protected function log(int $taskId, string $stage, array $payload = []): void
    {
        CrawlTaskLog::create([
            'task_id' => $taskId,
            'stage' => $stage,
            'message' => json_encode($payload, JSON_UNESCAPED_UNICODE),
        ]);
    }
}
