<?php

namespace Demo\client;

use Demo\common\Application;
use Demo\common\Base\MakesHttpRequests;
use GuzzleHttp\RequestOptions;

/**
 * 基础服务.
 */
class BaseClient
{
    use MakesHttpRequests;

    /**
     * 容器实例.
     */
    protected $app;

    protected $headers;

    protected $json;

    protected $multipart = [];

    protected $body;

    /**
     * 通用redis客户端过期时间.
     */
    protected $_rds_data_expire = 7200;

    /**
     * 容器初始化输入参数.
     */
    protected $_inputConfig;

    /**
     * 验证器.
     */
    protected $_validator;

    /**
     * 容器初始化redis客户端.
     */
    protected $_cache;

    public function __construct(Application $app)
    {
        //初始化使用到的必须依赖
        $this->app          = $app;
        $this->_inputConfig = $app['config'];
        $this->_validator   = $app['validator'];
        $this->_cache       = $app['cache'];
    }

    /**
     * 标准返回方法, 全部规范为数组返回.
     */
    public function formatReturn($data)
    {
        if (empty($data)) {
            return [];
        }

        if ('object' == gettype($data)) {
            //判断是否结果集, 进行结果集转化
            if (method_exists($data, 'isEmpty')) {
                if ($data->isEmpty()) {
                    return [];
                }
            }

            //判断是否结果集, 进行结果集转化
            if (method_exists($data, 'toArray')) {
                return $data->toArray();
            }
        }

        return $data;
    }

    /**
     * Set json params.
     *
     * @param array $json Json参数
     */
    public function setParams(array $json)
    {
        $this->json = $json;
    }

    /**
     * Set Form params.
     */
    public function setFormParams(array $param, $sign_key)
    {
        $string = '';

        $param['signature'] = $this->sign($sign_key);

        foreach ($param as $key => $value) {
            $string .= $key . '=' . $value . '&';
        }

        return substr($string, 0, -1);
    }

    /**
     * Make a get request.
     *
     * @throws ClientError
     */
    public function httpGet($uri, array $options = [])
    {
        $options = $this->_headers($options);

        return $this->request('GET', $uri, $options);
    }

    /**
     * Make a post request.
     *
     * @throws ClientError
     */
    public function httpPostJson($uri)
    {
        return $this->requestPost($uri, [RequestOptions::JSON => $this->json, RequestOptions::HEADERS => $this->headers]);
    }

    /**
     * Make a post request.
     *
     * @throws ClientError
     */
    public function httpPostFile($uri)
    {
        return $this->requestPost($uri, [RequestOptions::BODY => $this->body]);
    }

    /**
     * 获取unix的时间戳.
     */
    public function getUnixTimestamp()
    {
        [$s1, $s2] = explode(' ', microtime());
        return (float) sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000);
    }

    /**
     * set Headers.
     *
     * @return array
     */
    public function _headers(array $options = [])
    {
        $options[RequestOptions::HEADERS] = $this->headers;

        return $options;
    }

    /**
     * @throws ClientError
     */
    protected function requestPost($uri, array $options = [])
    {
        return $this->request('POST', $uri, $options);
    }

    /**
     * 生成签名.
     */
    protected function sign($sign_key)
    {
        $string = urldecode($sign_key) . $this->app['config']->get('appid') . $this->app['config']->get('secret');
        return strtoupper(md5($string));
    }
}
