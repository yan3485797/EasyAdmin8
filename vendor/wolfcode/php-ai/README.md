# php-ai

## Require

- PHP 8.1+
- GuzzleHttp 7.9.0+

## Installation

```bash
composer require wolfcode/php-ai
```

## Demo

```php
<?php

namespace App\Demo;

use Wolfcode\Ai\Enum\AiType;
use Wolfcode\Ai\Service\AiChatService;

class Demo{

    public function test()
    {
    
      $single = AiChatService::instance();
      $result = $single
            // 当使用推理模型时，可能存在超时的情况，所以需要设置超时时间为 0
            // ->setTimeLimit(0)
            // 请替换为您需要的模型类型
            ->setAiType(AiType::QWEN)
            // 如果需要指定模型的 API 地址，可自行设置
            // ->setAiUrl('https://xxx.com')
            // 请替换为您的模型
            ->setAiModel('qwen-plus')
            // 请替换为您的 API KEY
            ->setAiKey('sk-1234567890')
            // 此内容会作为系统提示，会影响到回答的内容 当前仅作为测试使用
            ->setSystemContent('你现在是一位资深的海外电商产品经理')
            ->chat('who are you ?');
            
        $result2 = $single
            // 当使用推理模型时，可能存在超时的情况，所以需要设置超时时间为 0
            // ->setTimeLimit(0)
            // 请替换为您需要的模型类型
            ->setAiType(AiType::DOUBAO)
            // 如果需要指定模型的 API 地址，可自行设置
            // ->setAiUrl('https://xxx.com')
            // 请替换为您的模型
            ->setAiModel('doubao-1-5-pro-32k-250115')
            // 请替换为您的 API KEY
            ->setAiKey('sk-1234567890')
            // 此内容会作为系统提示，会影响到回答的内容 当前仅作为测试使用
            ->setSystemContent('你现在是一位资深的海外电商产品经理')
            ->chat('who are you ?');    
            
        $result3 = $single
            // 当使用推理模型时，可能存在超时的情况，所以需要设置超时时间为 0
            ->setTimeLimit(0)
            // 请替换为您需要的模型类型
            ->setAiType(AiType::DEEPSEEK)
            // 如果需要指定模型的 API 地址，可自行设置
            // ->setAiUrl('https://xxx.com')
            // 请替换为您的模型
            ->setAiModel('deepseek-reasoner')
            // 请替换为您的 API KEY
            ->setAiKey('sk-1234567890')
            // 此内容会作为系统提示，会影响到回答的内容 当前仅作为测试使用
            ->setSystemContent('你现在是一位资深的海外电商产品经理')
            ->chat('who are you ?'); 
            
        // 当 AiType 不在枚举范围内时，可自行设置
        $resultCustom = $single->customChat([
                'url'      => 'https://api.siliconflow.cn/v1/chat/completions',
                'key'      => 'sk-1234567890',
                'model'    => 'deepseek-ai/DeepSeek-R1',
                'messages' => [
                    ['role' => 'system', 'content' => '你是一个AI助手'],
                    ['role' => 'user', 'content' => '高斯的主要成就有哪些？'],
                ]
            ]
        );
            
    }
}
```