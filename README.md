# JF图片服务 PHP SDK

## 说明
http 请求使用 Guzzle 三方库（v5.3），具体文档可参考：https://github.com/guzzle/guzzle

## 安装
```
composer require jufeng/phpsdk
```

## 使用方法
```
<?php

require 'vendor/autoload.php';

// 实例化客户端
$client = new JF\FileManager('ak', 'sk', 'http://img-upload.20hn.cn/v1');
// 设置上传策略，无特殊需求可省略。参考：参考：http://git.20hn.cn/developer/img-server/wikis/api-des
// $client->setPolicy(['deadline' => time() + 3600, 'autoCompress' => 1]);
// 获取token
$token = $client->getToken();

// 文件上传
try {
    $response = $client->upload($filePath = '/tmp/test.jpeg', $token);
    // http 请求返回内容
    echo $response->getBody();
    // http 响应状态码
    echo $response->getStatusCode();
} catch (GuzzleHttp\Exception\BadResponseException $e) {
    // 4xx 或 5xx 错误
    $response = $e->getResponse();
    echo $response->getStatusCode();
} catch (Exception $e) {
    echo $e->getMessage();
}
```