<?php
/**
 * Created by PhpStorm.
 * User: hgq <393210556@qq.com>
 * Date: 2021/10/27
 * Time: 11:41
 */

use WxPayEcommerce\Apply;
use WxPayEcommerce\Config;
use WxPayEcommerce\WxPayv3Exception;

require_once '../vendor/autoload.php';

try {
    $config = [
        'MCHID' => '18999999', //合单发起方商户号。示例值：1900000109
        'SERIAL_NO' => 'SERIAL_NOSERIAL_NOSERIAL_NOSERIAL_NOSERIAL_NO', //网站证书的序列号，非实时获取的序列号
        'SERIAL_NO_PATH' => '/../src/cert/serial_no.txt', // 网站证书的序列号txt
        'COMBINE_APPID' => 'COMBINE_APPID', // 合单发起方的appid  示例值：wxd678efh567hg6787
        //'COMBINE_MCHID' => 'COMBINE_MCHID', // 合单发起方商户号。示例值：1900000109
        //'XCX_APPID' => 'XCX_APPIDXCX_APPID', // 合单发起方小程序商户号。示例值：1900000109

        //'APPKEY' => 'APPKEYAPPKEYAPPKEYAPPKEYAPPKEY',    // 合单发起方 key
        'APPKERV3' => 'APPKERV3APPKERV3APPKERV3APPKERV3',// 合单发起方v3版本key
        'SSLCERT_PATH' => '/../src/cert/apiclient_cert.pem',// 合单发起方证书(公钥)地址
        'SSLKEY_PATH' => '/../src/cert/apiclient_key.pem',// 合单发起方私钥地址
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
