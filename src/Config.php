<?php

namespace WxPayEcommerce;

/**
 *    配置账号信息
 */
class Config
{
    const MCHID = '18999999'; //合单发起方商户号。示例值：1900000109
    const SERIAL_NO = 'SERIAL_NOSERIAL_NOSERIAL_NOSERIAL_NOSERIAL_NO'; //网站证书的序列号，非实时获取的序列号
    const COMBINE_APPID = 'COMBINE_APPID'; // 合单发起方的appid  示例值：wxd678efh567hg6787
    const COMBINE_MCHID = 'COMBINE_MCHID'; // 合单发起方商户号。示例值：1900000109
    const XCX_APPID = 'XCX_APPIDXCX_APPID'; // 合单发起方小程序商户号。示例值：1900000109

    const APPKEY = 'APPKEYAPPKEYAPPKEYAPPKEYAPPKEY';    // 合单发起方 key
    const APPKERV3 = 'APPKERV3APPKERV3APPKERV3APPKERV3';// 合单发起方v3版本key
    const SSLCERT_PATH = '/../src/cert/apiclient_cert.pem';// 合单发起方证书(公钥)地址
    const SSLKEY_PATH = '/../src/cert/apiclient_key.pem';// 合单发起方私钥地址

}
