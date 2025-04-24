<?php

declare(strict_types = 1);

namespace Wolfcode\Ai\Service;

use Wolfcode\Ai\Enum\AiType;
use Wolfcode\Ai\Exception\AiException;
use GuzzleHttp\Client;

class AiChatService
{

    /**
     * @var AiChatService|null
     */
    public static ?AiChatService $instance = null;

    /**
     * @var AiType
     */
    protected AiType $type = AiType::QWEN;

    /**
     * @var string
     */
    protected string $url = '';

    /**
     * @var string
     */
    protected string $model = '';

    /**
     * @var string
     */
    protected string $key = '';

    /**
     * @var int|string
     */
    protected int|string $time_limit = 30;

    /**
     * @var string
     */
    protected string $systemContent = '';

    /**
     * @param array $options
     * @return AiChatService|null
     */
    public static function instance(array $options = []): ?AiChatService
    {
        if (!static::$instance) static::$instance = new static();
        return static::$instance;
    }

    private function __construct() {}

    private function __clone() {}

    /**
     * @return mixed
     * @throws AiException
     */
    public function __wakeup()
    {
        throw new AiException("Cannot unserialize singleton");
    }

    public function hello(): string
    {
        return 'hello ' . date('Y-m-d H:i:s');
    }

    /**
     * @param int|string $limit
     * @return $this
     */
    public function setTimeLimit(int|string $limit): static
    {
        $this->time_limit = $limit;
        return $this;
    }

    /**
     * @param AiType $type
     * @return $this
     */
    public function setAiType(AiType $type): static
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @param string $url
     * @return $this
     */
    public function setAiUrl(string $url): static
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @param string $model
     * @return $this
     */
    public function setAiModel(string $model): static
    {
        $this->model = $model;
        return $this;
    }

    /**
     * @param string $key
     * @return $this
     */
    public function setAiKey(string $key): static
    {
        $this->key = $key;
        return $this;
    }

    /**
     * @param string $systemContent
     * @return $this
     */
    public function setSystemContent(string $systemContent): static
    {
        $this->systemContent = $systemContent;
        return $this;
    }

    /**
     * @param string $message
     * @return mixed
     * @throws AiException
     */
    public function chat(string $message): mixed
    {
        if (empty($message)) throw new AiException('Message cannot be empty...');
        $type = match ($this->type) {
            AiType::DOUBAO   => 'douBao',
            AiType::DEEPSEEK => 'deepSeek',
            default          => 'qwen',
        };
        return $this->$type($message);
    }

    /**
     * @param array $options
     * @return array
     * @throws AiException
     */
    public function customChat(array $options = []): array
    {
        $url      = $options['url'] ?? '';
        $messages = $options['messages'] ?? [];
        $key      = $options['key'] ?? '';
        $model    = $options['model'] ?? '';
        if ($key) $this->setAiKey($key);

        $params = $options['params'] ?? compact('model', 'messages');
        return $this->httpPost($url, $params, $this->key, $options['headers'] ?? []);
    }

    /**
     * @param string $message
     * @return array
     * @throws AiException
     */
    protected function deepSeek(string $message): array
    {
        $url    = $this->url ?: 'https://api.deepseek.com/chat/completions';
        $params = [
            'model'    => $this->model,
            'messages' => [
                ['role' => 'system', 'content' => $this->systemContent],
                ['role' => 'user', 'content' => $message],
            ],
        ];
        return $this->httpPost($url, $params, $this->key);
    }

    /**
     * @param string $message
     * @return array
     * @throws AiException
     */
    protected function qwen(string $message): array
    {
        $url    = $this->url ?: 'https://dashscope.aliyuncs.com/compatible-mode/v1/chat/completions';
        $params = [
            'model'    => $this->model,
            'messages' => [
                ['role' => 'system', 'content' => $this->systemContent],
                ['role' => 'user', 'content' => $message],
            ],
        ];
        return $this->httpPost($url, $params, $this->key);
    }

    /**
     * @param string $message
     * @return array
     * @throws AiException
     */
    protected function douBao(string $message): array
    {
        $url    = $this->url ?: 'https://ark.cn-beijing.volces.com/api/v3/chat/completions';
        $params = [
            'model'    => $this->model,
            'messages' => [
                ['role' => 'system', 'content' => $this->systemContent],
                ['role' => 'user', 'content' => $message],
            ],
        ];
        return $this->httpPost($url, $params, $this->key);
    }

    /**
     * @param string $url
     * @param array $params
     * @param string $key
     * @param array $headers
     * @return array
     * @throws AiException
     */
    protected function httpPost(string $url, array $params = [], string $key = '', array $headers = []): array
    {
        set_time_limit($this->time_limit);
        $client = new Client(['timeout' => 300.0]);
        try {
            $promise = $client->requestAsync('POST', $url, [
                'headers'         => $headers ?: ['Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $key],
                'json'            => $params,
                'connect_timeout' => 300.0,
            ])->then(function($response) {
                return json_decode($response->getBody()->getContents(), true);
            });
            return $promise->wait();
        }catch (\Throwable $exception) {
            throw new AiException($exception->getMessage());
        }
    }

}