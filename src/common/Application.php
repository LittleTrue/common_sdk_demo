<?php

namespace Demo\common;

use Demo\common\Base\Config;
use Pimple\Container;

/**
 * Class Application.
 */
class Application extends Container
{
    /**
     * @var array
     */
    protected $providers = [
        Base\ServiceProvider::class,
    ];

    /**
     * 容器初始化并处理初始化注入。
     * Application constructor.
     */
    public function __construct(array $config = [])
    {
        parent::__construct();

        //容器初始化参数的对象依赖注入
        $this['config'] = function () use ($config) {
            return new Config($config);
        };

        $this->registerProviders();
    }

    /**
     * @param $id
     */
    public function __get($id)
    {
        return $this->offsetGet($id);
    }

    /**
     * Register providers.
     */
    protected function registerProviders()
    {
        foreach ($this->providers as $provider) {
            $this->register(new $provider());
        }
    }
}
