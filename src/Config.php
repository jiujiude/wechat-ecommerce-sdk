<?php

namespace jiujiude\WechatEcommerce;

/**
 * 配置信息
 */
class Config
{
    public static $config = [];

    /**
     * 获取配置
     * @return array
     * @author hgq <393210556@qq.com>.
     * @date: 2021/10/28 13:48
     */
    public static function getConfig()
    {
        return self::$config;
    }

    /**
     * 外部设置配置
     * @param array $config
     * @author hgq <393210556@qq.com>.
     * @date: 2021/10/28 13:44
     */
    public static function setConfig($config = [])
    {
        self::$config = $config;
    }

    public function __get($name)
    {
        return self::$config[$name] ?? '';
    }
}
