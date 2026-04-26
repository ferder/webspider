<?php

namespace app\admin\model\crawler;

use think\Exception;
use think\Model;

class BaseModel extends Model
{
    protected $autoWriteTimestamp = 'int';
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    protected function encodeJson($value, $field)
    {
        if ($value === null || $value === '') {
            return '';
        }
        if (is_array($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE);
        }
        json_decode($value, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("{$field} JSON格式不合法");
        }
        return $value;
    }
}
