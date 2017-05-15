<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/FileManager.php';

class FileManager extends PHPUnit_Framework_TestCase
{
    protected $client;

    public function setUp()
    {
        $this->client = new JF\FileManager(
            'test',
            '+A0v4jhw6A67PCXQ7bYvt0UD9D7pXiqduSAnEMlFe3A=',
            'http://img-api.ll.com/v1'
        );
    }

    public function testCollect()
    {
        $token = $this->client->getToken();
        $response = $this->client->collect('http://9.pic.pc6.com/thumb/up/2015-4/14301352575151701_600_0.jpg', $token);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
