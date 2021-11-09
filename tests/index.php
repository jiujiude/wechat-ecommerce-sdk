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
    $config = [
        //合单商户信息APP
        'APP' => [
            'COMBINE_APPID' => 'xxx', // 合单发起方的appid
        ],
        //合单商户信息公众号
        'PUB' => [
            'COMBINE_APPID' => 'xxx', // 合单发起方的appid
        ],
        //合单商户信息小程序
        'XCX' => [
            'COMBINE_APPID' => 'xxx', // 合单发起方的appid
        ],
        //分账APPid
        'ACCOUNT' => [
            'COMBINE_APPID' => 'xxx', // 分账APPID
        ],

        'COMBINE_APPID' => 'xxx', // 默认合单APPID，示例值：1900000109
        'MCHID' => 'xxx', //合单发起方商户号。示例值：1900000109
        'COMBINE_MCHID' => 'xxx', // 合单发起方商户号。服务商商户号，示例值：1900000109
        'SERIAL_NO' => 'xxx', //网站证书的序列号，非实时获取的序列号

        'APPKEY' => 'xxx',    // 合单发起方 key
        'APPKERV3' => 'xxx',  // 合单发起方v3版本key

        'SSLCERT_PATH' => getcwd() . '/cert/apiclient_cert.pem',// 合单发起方证书(公钥)地址，暂时没用
        'SSLKEY_PATH' => getcwd() . '/cert/apiclient_key.pem',// 合单发起方私钥地址

        'PLATFORM_SERIAL_NO_PATH' => getcwd() . '/cert/platform_serial_no.txt', // 平台证书的序列号txt
        'PLATFORM_CERT_PATH' => getcwd() . '/cert/platform_cert.pem', // 平台证书
    ];
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
