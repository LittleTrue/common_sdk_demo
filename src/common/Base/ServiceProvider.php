<?php

namespace Demo\common\Base;

use GuzzleHttp\Client as GuzzleHttp;
use GuzzleHttp\RequestOptions;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\Cache\Simple\RedisCache;

/**
 * 注册基础工具依赖.
 * Class ServiceProvider.
 */
class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        //注册基于guzzle的http请求客户端.
        $app['http_client'] = function ($app) {
            return new GuzzleHttp([
                RequestOptions::TIMEOUT => 60,
            ]);
        };

        //注册参数验证器.
        $app['validator'] = function ($app) {
            return new Validator($app);
        };

        //容器redis操作对象依赖注入
        $app['cache'] = function ($app) {
            $redis_client = new RedisHandler($app);
            return new RedisCache($redis_client->getRedis());
        };
    }
}
