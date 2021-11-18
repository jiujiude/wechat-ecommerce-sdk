<?php
/**
 * Created by PhpStorm.
 * User: hgq <393210556@qq.com>
 * Date: 2021/10/27
 * Time: 11:41
 */

use jiujiude\WechatEcommerce\Apply;
use jiujiude\WechatEcommerce\Config;
use jiujiude\WechatEcommerce\WxPayv3Exception;

require_once '../vendor/autoload.php';

try {
    $config = require_once './config/ecommerce.php';
    //设置配置
    Config::setConfig($config);
    //配置获取
    print_r(Config::getConfig());
    die;

    $obj = new Apply();
    $obj->applyment([]);
} catch (WxPayv3Exception $th) {
    var_dump($th);
}
