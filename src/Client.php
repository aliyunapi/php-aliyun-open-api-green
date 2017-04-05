<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace aliyun\green;

use GuzzleHttp\HandlerStack;
use GuzzleHttp\Client as HttpClient;
use aliyun\guzzle\subscriber\Rpc;

/**
 * Class Client
 * @package aliyun\search
 */
class Client
{
    /**
     * @var string
     */
    public $accessKeyId;

    /**
     * @var string
     */
    public $accessSecret;

    /**
     * @var string 应用名称
     */
    public $appName;

    /**
     * @var string API版本
     */
    public $version = '2016-12-16';

    /**
     * @var string 网关地址
     */
    public $baseUri = 'http://green.cn-hangzhou.aliyuncs.com';

    /**
     * @var HttpClient
     */
    private $_httpClient;

    /**
     * Client constructor.
     * @param array $config
     * @throws \Exception
     */
    public function __construct($config = [])
    {
        foreach ($config as $name => $value) {
            $this->{$name} = $value;
        }
        if (empty ($this->accessKeyId)) {
            throw new \Exception ('The "accessKeyId" property must be set.');
        }
        if (empty ($this->accessSecret)) {
            throw new \Exception ('The "accessSecret" property must be set.');
        }
        if (empty ($this->baseUri)) {
            throw new \Exception ('The "baseUri" property must be set.');
        }
        if (empty ($this->appName)) {
            throw new \Exception ('The "appName" property must be set.');
        }
    }

    /**
     * 获取Http Client
     * @return HttpClient
     */
    public function getHttpClient()
    {
        if (!is_object($this->_httpClient)) {
            $stack = HandlerStack::create();
            $middleware = new Rpc([
                'accessKeyId' => $this->accessKeyId,
                'accessSecret' => $this->accessSecret,
                'Version' => $this->version
            ]);
            $stack->push($middleware);

            $this->_httpClient = new HttpClient([
                'base_uri' => $this->baseUri,
                'handler' => $stack,
                'verify' => false,
                'http_errors' => false,
                'connect_timeout' => 3,
                'read_timeout' => 10,
                'debug' => false,
            ]);
        }
        return $this->_httpClient;
    }

    /**
     * 获取应用列表
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function imageDetection()
    {
        return $this->getHttpClient()->get('/index');
    }

    /**
     * 查看应用信息
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function imageResults()
    {
        return $this->getHttpClient()->get('/' . $this->appName);
    }

    /**
     * 搜索
     * @param array $params
     * @return \Psr\Http\Message\ResponseInterface
     * @see https://help.aliyun.com/document_detail/29150.html
     */
    public function textAntispamDetection(array $params)
    {
        return $this->getHttpClient()->get('/search', ['query' => $params]);
    }

    /**
     * 下拉提示
     * @param array $params
     * @return \Psr\Http\Message\ResponseInterface
     * @see https://help.aliyun.com/document_detail/29151.html
     */
    public function textKeywordFilter(array $params)
    {
        return $this->getHttpClient()->get('/suggest', ['query' => $params]);
    }
}