<?php

namespace JF;

use GuzzleHttp\Client;
use Ramsey\Uuid\Uuid;

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
    protected $token;
    protected $timeout = 5;
    protected $encryptionAlgorithm;
    protected $policyAllowed = ['deadline', 'autoCompress', 'autoWatermark', 'timestamp', 'nonce'];
    /**
     * 上传策略。
     *
     * @var array
     */
    protected $policy;

    public function __construct($ak, $sk, $baseUrl = 'https://imgserver-api.deenet.cn/v1/', $encryptionAlgorithm = 'sha256')
    {
        $this->ak = $ak;
        $this->sk = $sk;
        $this->encryptionAlgorithm = $encryptionAlgorithm;
        $this->client = new Client(['base_uri' => rtrim($baseUrl, '/') . '/']);
        $curTime = time();
        $this->policy = [
            'deadline' => $curTime + 300,
            'autoCompress' => 1,
            'timestamp' => $curTime,
            'nonce' => $this->createNonce($curTime),
        ];
        $this->token = $this->getToken();
    }

    /**
     * 设置上传策略
     *
     * @param array $policy 上传策略
     * @return void
     */
    public function setPolicy(array $policy)
    {
        if (!empty($policy)) {
            foreach ($policy as $key => $value) {
                if (in_array($key, $this->policyAllowed)) {
                    $this->policy[$key] = $value;
                }
            }
            $this->token = $this->getToken();
        }
    }

    /**
     * 获取token
     *
     * @return string token
     */
    public function getToken()
    {
        $encodeStr = base64_encode(json_encode($this->policy));
        if ($this->encryptionAlgorithm == 'md5') {
            return base64_encode($this->ak) . ':' . $encodeStr . ':' .
            base64_encode(md5($encodeStr . $this->sk));
        } else {
            return base64_encode($this->ak) . ':' . $encodeStr . ':' .
            base64_encode(hash_hmac('sha256', $encodeStr, $this->sk));
        }
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
     * 生成随机串
     *
     */
    public function createNonce($curTime)
    {
        try {
            $uuid = Uuid::uuid4()->tostring();
        } catch (\Exception $e) {
            $uuid = md5(uniqid(mt_rand(), true));
        }
        return $uuid;
    }

    /**
     * 文件上传
     *
     * @param string $file 文件路径
     * @param string $token token
     * @return
     */
    public function upload($file)
    {
        if (!is_readable($file)) {
            throw new \InvalidArgumentException("文件不存在或无读取权限：" . $file);
        }

        $fileHandle = fopen($file, 'r');
        if ($fileHandle === false) {
            throw new \RuntimeException("读取文件失败：" . $file);
        }

        return $this->client->POST('images/upload', [
            'multipart' => [
                [
                    'name' =>'token',
                    'contents'=>$this->token,
                ],
                [
                    'name' =>'file',
                    'contents'=> $fileHandle,
                ]
            ],
            'timeout' => $this->timeout
        ]);
    }

    /**
     * 远程文件下载
     *
     * @param string $url 文件url
     * @param string $token token
     * @return
     */
    public function collect($url)
    {
        if (false === filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException("url无效：" . $url);
        }

        return $this->client->request('POST', 'images/collect', [
            'multipart' => [
                [
                    'name' =>'token',
                    'contents'=>$this->token,
                ],
                [
                    'name' =>'url',
                    'contents'=> $url,
                ]
            ],
            'timeout' => $this->timeout
        ]);
    }
}
