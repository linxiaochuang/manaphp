<?php
defined('UNIT_TESTS_ROOT') || require __DIR__ . '/bootstrap.php';

class DummyClient extends \ManaPHP\Http\Client
{
    public $type;
    public $url;
    public $data;
    public $headers;
    public $options;
    public $httpCode = 200;

    public function _request($type, $url, $data, $headers, $options)
    {
        $this->type = $type;
        $this->url = $url;
        $this->data = $data;
        $this->headers = $headers;
        $this->options = $options;
        return $this->httpCode;
    }
}

class HttpClientTest extends TestCase
{

    public function test_buildUrl()
    {
        $httpClient = new DummyClient();
        $httpClient->get('http://www.example.com/');
        $this->assertEquals('http://www.example.com/', $httpClient->url);

        $httpClient->get('http://www.example.com/?page=1');
        $this->assertEquals('http://www.example.com/?page=1', $httpClient->url);

        $httpClient->get(['http://www.example.com/', ['page' => 1]]);
        $this->assertEquals('http://www.example.com/?page=1', $httpClient->url);

        $httpClient->get('http://www.example.com/?page=1&size=10');
        $this->assertEquals('http://www.example.com/?page=1&size=10', $httpClient->url);

        $httpClient->get(['http://www.example.com/', ['page' => 1, 'size' => 10]]);
        $this->assertEquals('http://www.example.com/?page=1&size=10', $httpClient->url);

        $httpClient->get(['http://www.example.com/?page=1', ['size' => 10]]);
        $this->assertEquals('http://www.example.com/?page=1&size=10', $httpClient->url);

        $httpClient->get(['http://www.example.com/', ['keyword' => '中国']]);
        $this->assertEquals('http://www.example.com/?keyword=%E4%B8%AD%E5%9B%BD', $httpClient->url);
    }

    public function test_get()
    {
        $httpClient = new \ManaPHP\Http\Client\Adapter\Curl();

        $statusCode = $httpClient->get(['http://apis.juhe.cn/ip/ip2addr', ['ip' => 'www.baidu.com', 'key' => 'appkey']]);
        $this->assertEquals(200, $statusCode);
        $json = json_decode($httpClient->getResponseBody(), true);
        $this->assertEquals(101, $json['resultcode']);
    }

    public function test_post()
    {
        $httpClient = new \ManaPHP\Http\Client\Adapter\Curl();

        $statusCode = $httpClient->post(['http://lxb.baidu.com/', ['uid' => 0, 'f' => 4]], ['r' => 'www.xxx.com']);
        $this->assertEquals(200, $statusCode);
    }

    public function test_delete()
    {
        $httpClient = new \ManaPHP\Http\Client\Adapter\Curl();
        $httpClient->delete('http://www.baidu.com/', []);
    }

    public function test_put()
    {
        $httpClient = new \ManaPHP\Http\Client\Adapter\Curl();
        $httpClient->put('http://www.baidu.com/', [], []/*,['proxy'=>'127.0.0.1:8888']*/);
    }

    public function test_patch()
    {
        $httpClient = new \ManaPHP\Http\Client\Adapter\Curl();
        $httpClient->patch('http://www.baidu.com/', [], []/*,['proxy'=>'127.0.0.1:8888']*/);
    }
}