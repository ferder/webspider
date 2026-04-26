<?php

namespace app\admin\controller\crawler;

use app\common\controller\Backend;

class BaseCrud extends Backend
{
    protected function validateJsonFields(&$params, array $fields)
    {
        foreach ($fields as $field) {
            if (!isset($params[$field]) || $params[$field] === '') {
                continue;
            }
            json_decode($params[$field], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->error("字段 {$field} JSON格式不合法");
            }
        }
    }
}
