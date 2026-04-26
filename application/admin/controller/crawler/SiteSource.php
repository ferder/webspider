<?php

namespace app\admin\controller\crawler;

use app\admin\model\crawler\SiteSource as SiteSourceModel;

/**
 * 站点来源管理
 */
class SiteSource extends BaseCrud
{
    protected $model = null;
    protected $searchFields = 'id,site_name,base_url,domain,country,language,currency';
    protected $multiFields = 'status,platform_type';

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new SiteSourceModel();
        $this->view->assign('statusList', $this->model->getStatusList());
        $this->view->assign('platformTypeList', $this->model->getPlatformTypeList());
    }

    public function index()
    {
        return parent::index();
    }

    public function add()
    {
        if ($this->request->isPost()) {
            $params = $this->request->post('row/a');
            if ($params) {
                $params = $this->normalizeSiteParams($params);
                $this->request->post(['row' => $params]);
            }
        }
        return parent::add();
    }

    public function edit($ids = null)
    {
        if ($this->request->isPost()) {
            $params = $this->request->post('row/a');
            if ($params) {
                $params = $this->normalizeSiteParams($params);
                $this->request->post(['row' => $params]);
            }
        }
        return parent::edit($ids);
    }

    public function batchimport()
    {
        if (!$this->request->isPost()) {
            return $this->view->fetch();
        }
        $urls = (string)$this->request->post('urls', '');
        if (!$urls) {
            $this->error('请输入URL');
        }
        $lines = preg_split('/\r\n|\r|\n/', $urls);
        $success = 0;
        $skip = 0;
        foreach ($lines as $line) {
            $url = trim($line);
            if (!$url) {
                continue;
            }
            $normalized = $this->normalizeUrl($url);
            $domain = $this->extractDomain($normalized);
            if (!$domain) {
                $skip++;
                continue;
            }
            $exists = $this->model->where('base_url', $normalized)->whereOr('domain', $domain)->find();
            if ($exists) {
                $skip++;
                continue;
            }
            $this->model->save([
                'site_name' => $domain,
                'base_url' => $normalized,
                'homepage_url' => $normalized,
                'domain' => $domain,
                'sitemap_url' => rtrim($normalized, '/') . '/sitemap.xml',
                'robots_url' => rtrim($normalized, '/') . '/robots.txt',
                'platform_type' => 'unknown',
                'status' => 'pending_detect',
                'detect_score' => 0,
            ]);
            $success++;
        }
        $this->success("导入完成，成功{$success}条，跳过{$skip}条");
    }

    protected function normalizeSiteParams(array $params)
    {
        $params['base_url'] = $this->normalizeUrl($params['base_url'] ?? '');
        if (empty($params['homepage_url'])) {
            $params['homepage_url'] = $params['base_url'];
        }
        if (empty($params['domain'])) {
            $params['domain'] = $this->extractDomain($params['base_url']);
        }
        if (empty($params['sitemap_url'])) {
            $params['sitemap_url'] = rtrim($params['base_url'], '/') . '/sitemap.xml';
        }
        if (empty($params['robots_url'])) {
            $params['robots_url'] = rtrim($params['base_url'], '/') . '/robots.txt';
        }
        return $params;
    }

    protected function normalizeUrl($url)
    {
        $url = trim((string)$url);
        if ($url && !preg_match('/^https?:\/\//i', $url)) {
            $url = 'https://' . $url;
        }
        return rtrim($url, '/');
    }

    protected function extractDomain($url)
    {
        $parts = parse_url($url);
        return $parts['host'] ?? '';
    }
}
