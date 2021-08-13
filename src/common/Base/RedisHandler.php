<?php

namespace Demo\common\Base;

use Demo\common\Application;

/**
 * Redis相关操作.
 */
class RedisHandler extends \Redis
{
    /**
     * @var \Redis
     */
    protected $redisClient;

    /**
     * 操作配置信息.
     */
    private $_config;

    /**
     * rds操作前缀.
     */
    private $_rds_data_prefix = 'api_7cDjeUI1k2_';

    /**
     * 使用容器注入的参数初始化redis.
     */
    public function __construct(Application $app)
    {
        parent::__construct();
        $this->_config = $config = $app['config'];

        $this->redisClient = new \Redis();
        $this->redisClient->connect($config['rds_host'], $config['rds_port'], $config['rds_time_out']);
        $this->redisClient->auth($config['rds_password']);
        $this->redisClient->select($config['rds_select']);
        $this->redisClient->setOption(\Redis::OPT_PREFIX, $this->_rds_data_prefix);
    }

    /**
     * 删除某个redis.
     *
     * @param string $key Redis的key
     */
    public function deleteRedis(string $key): int
    {
        $result = 0;

        if (!empty($key)) {
            $result = $this->redisClient->delete($key);
        }

        return $result;
    }

    /**
     * 获取redis类.
     */
    public function getRedis()
    {
        return $this->redisClient;
    }
}
