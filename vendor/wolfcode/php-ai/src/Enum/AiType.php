<?php

declare(strict_types = 1);

namespace Wolfcode\Ai\Enum;

enum AiType
{

    /**
     * @url https://api-docs.deepseek.com
     */
    case DEEPSEEK;

    /**
     * @url https://www.aliyun.com/product/tongyi
     */
    case QWEN;

    /**
     * @url https://www.volcengine.com/product/doubao
     */
    case DOUBAO;

}