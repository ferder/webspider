<?php

namespace app\common\service;

class AiRuleAnalyzeService
{
    public function analyze($html, array $options = [])
    {
        return [
            'provider' => 'qwen-plus',
            'status' => 'placeholder',
            'message' => 'Phase1 占位实现，未调用真实模型',
            'suggestions' => [
                'title_selector' => '',
                'price_selector' => '',
                'image_selector' => '',
            ],
        ];
    }
}
