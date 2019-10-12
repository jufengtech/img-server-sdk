<?php

class FileManagerTest extends PHPUnit_Framework_TestCase
{
    protected $client;

    public function setUp()
    {
        $this->client = new JF\FileManager(
            'test',
            '+A0v4jhw6A67PCXQ7bYvt0UD9D7pXiqduSAnEMlFe3A=',
            'https://test-imgserver-api.deenet.cn/v1'
        );
    }

    public function testCollect()
    {
        $response = $this->client->collect('http://9.pic.pc6.com/thumb/up/2015-4/14301352575151701_600_0.jpg');
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testUpload()
    {
        $response = $this->client->upload('tests/fixture/upload.png');
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testMultiCollect()
    {
        $response = $this->client->collect('http://9.pic.pc6.com/thumb/up/2015-4/14301352575151701_600_0.jpg');
        $this->assertEquals(200, $response->getStatusCode());
        $response = $this->client->collect('http://9.pic.pc6.com/thumb/up/2015-4/14301352575151701_600_0.jpg');
        $this->assertEquals(200, $response->getStatusCode());
    }
}
