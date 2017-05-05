<?php

namespace JF;

use GuzzleHttp\Client;

class FileManager
{
    /**
     * http client
     *
     * @var GuzzleHttp\Client
     */
    protected $client;
    protected $ak;
    protected $sk;
    protected $timeout = 10;
    /**
     * 上传策略。
     *
     * @var array
     */
    protected $policy;

    public function __construct($ak, $sk, $baseUrl = 'http://img-upload.20hn.cn/v1/')
    {
        $this->ak = $ak;
        $this->sk = $sk;
        $this->client = new Client(['base_url' => rtrim($baseUrl, '/') . '/']);
        $this->policy = [
            'deadline' => time() + 3600,
            'autoCompress' => 1,
        ];
    }

    /**
     * 设置上传策略
     *
     * @param array $policy 上传策略
     * @return void
     */
    public function setPolicy(array $policy)
    {
        $this->policy = $policy;
    }

    /**
     * 获取token
     *
     * @return string token
     */
    public function getToken()
    {
        $encodeStr = base64_encode(json_encode($this->policy));
        return base64_encode($this->ak) . ':' . $encodeStr . ':' .
            base64_encode(hash_hmac('sha256', $encodeStr, $this->sk));
    }

    /**
     * 设置超时
     *
     * @param integer $timeout curl超时，秒
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }

    /**
     * 文件上传
     *
     * @param string $file 文件路径
     * @return
     */
    public function upload($file, $token)
    {
        if (!is_readable($file)) {
            throw new \InvalidArgumentException("文件不存在或无读取权限：" . $file);
        }

        $fileHandle = fopen($file, 'r');
        if ($fileHandle === false) {
            throw new \RuntimeException("读取文件失败：" . $file);
        }

        return $this->client->post('upload', [
                'body' => [
                    'token' => $token,
                    'file' => $fileHandle,
                ],
                'timeout' => $this->timeout,
            ]);
    }
}
