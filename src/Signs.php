<?php

namespace jiujiude\WechatEcommerce;

//加签，延签，加密，解密，get提交，post提交
class Signs
{
    /**
     * 微信电商收付通API V3
     * 不同的商户，对应的微信支付平台证书是不一样的，平台证书会周期性更换。建议商户定时通过API下载新的证书，不要依赖人工更换证书。
     * 微信支付的平台证书序列号位于HTTP头Wechatpay-Serial。验证签名前，请商户先检查序列号是否跟商户当前所持有的微信支付平台证书的序列号一致。
     * 生成签名的serial_no是网站上面的证书的序列号。而HTTP头Wechatpay-Serial以post方式带上的serial_no是实时获取的serial_no。
     * 后期可以做缓存serial_no，并且缓存新证书内容，不需要每次都获取且保存文件。
     * 如果不一致，请重新获取证书。否则，签名的私钥和证书不匹配，将无法成功验证签名。
     */
    // get方式 提交 body不参与签名
    public static function _Getresponse($url, $body = '')
    {
        $methed = 'GET';
        $date = time();
        $nonce = self::createNoncestr();
        $sign = self::sign($url, $methed, $date, $nonce, $body); //$http_method要大写
        $header[] = 'User-Agent:Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.108 Safari/537.36';
        $header[] = 'Accept:application/json';
        $header[] = 'Authorization:WECHATPAY2-SHA256-RSA2048 ' . $sign;

        $serial_nos = Cert::certificates();
        $header[] = 'Wechatpay-Serial:' . $serial_nos;
        $r = self::_requestGet($url, $body, $header);
        return $r;
    }

    public static function _Postresponse($url, $body)
    {
        $methed = 'POST';
        $date = time();
        if (is_array($body)) {
            $body = json_encode($body);
        }
        $nonce = self::createNoncestr();
        $sign = self::sign($url, $methed, $date, $nonce, $body);
        $serial_nos = Cert::certificates();
        $header[] = 'User-Agent:233';
        $header[] = 'Accept:application/json';
        $header[] = 'Content-Type:application/json';
        $header[] = 'Authorization:WECHATPAY2-SHA256-RSA2048 ' . $sign;
        $header[] = 'Wechatpay-Serial:' . $serial_nos;
        $r = self::_requestPost($url, $body, $header);
        return $r;
    }
    /**
     * [_requestGet CURL GET请求]
     * @param  [type] $url    [请求目标]
     * @param  [type] $meta   [请求参数]
     * @param  [type] $header [头部参数]
     * @return [type]         [结果返回]
     */
    private static function _requestGet($url, $meta, $header = array(), $referer = '',  $timeout = 30)
    {
        $ch = curl_init();
        //设置抓取的url
        curl_setopt($ch, CURLOPT_URL, $url);
        //设置头文件的信息作为数据流输出
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        //执行命令
        $response = curl_exec($ch);
        if ($error = curl_error($ch)) {
            curl_close($ch);
            throw new WxPayv3Exception($error);
        }
        curl_close($ch);
        return $response;
    }

    /**
     * [_requestPost CURL POST请求]
     * @param  [type]  $url     [请求目标]
     * @param  [type]  $data    [请求参数]
     * @param  array   $header  [头部参数]
     * @param  string  $referer [referer]
     * @param  integer $timeout [超时时间：单位秒]
     * @return [type]           [结果返回]
     */
    public static function _requestPost($url, $data, $header = array(), $referer = '', $timeout = 30)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        //避免https 的ssl验证
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSLVERSION, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        // 模拟来源
        curl_setopt($ch, CURLOPT_REFERER, $referer);
        $response = curl_exec($ch);
        if ($error = curl_error($ch)) {
            curl_close($ch);
            throw new WxPayv3Exception($error);
        }
        curl_close($ch);
        return $response;
    }

    /**[sign 签名]
     * [sign 签名]
     * @param  [type] $url             [请求目标]
     * @param  [type] $http_method     [请求方式 GET POST PUT]
     * @param  [type] $timestamp       [时间戳]
     * @param  [type] $nonce           [随机串]
     * @param  [type] $body            [报文 GET请求时可以为空]
     * @param  [type] $mch_private_key [api 密钥]
     * @param  [type] $merchant_id     [发起请求的商户（包括直连商户、服务商或渠道商）的商户号mchid]
     * @param  [type] $serial_no       [证书序列号]
     * @return [type]                  [返回为签名串]
     */
    public static function sign($url, $http_method, $timestamp, $nonce, $body = '')
    {

        if (!in_array('sha256WithRSAEncryption', \openssl_get_md_methods(true))) {
            throw new WxPayv3Exception("当前PHP环境不支持SHA256withRSA");
        }

        //发起请求的商户（包括直连商户、服务商或渠道商）的商户号mchid
        $merchant_id = Config::$config['MCHID'];
        //商户API证书序列号
        $serial_no = Config::$config['SERIAL_NO'];
        //获取私钥
        $mch_private_key = self::getPrivateKey(Config::$config['SSLKEY_PATH']);       //商户私钥

        $url_parts = parse_url($url);

        $canonical_url = ($url_parts['path'] . (!empty($url_parts['query']) ? "?${url_parts['query']}" : ""));
        if ($http_method == 'GET') {
            $body = '';
        }
        $message = $http_method . "\n" . $canonical_url . "\n" . $timestamp . "\n" . $nonce . "\n" . $body . "\n";
        openssl_sign($message, $raw_sign, $mch_private_key, 'sha256WithRSAEncryption');
        $sign = base64_encode($raw_sign);
        $token = sprintf('mchid="%s",nonce_str="%s",timestamp="%d",serial_no="%s",signature="%s"', $merchant_id, $nonce, $timestamp, $serial_no, $sign);
        return $token;
    }


    /**
     * 吊起支付 封裝
     * @param string appid 微信公众号的APPID或者小程序的appid
     * @param string prepay_id 合单支付返回的prepay_id
     */
    public static function _PayJson($appid, $prepay_id)
    {
        $timestamp = time();
        $nonceStr = self::createNoncestr();
        $package = 'prepay_id=' . $prepay_id;
        $paySign = self::paySign($appid, $timestamp, $nonceStr, $package);
        $data = [
            'appId' => $appid,
            'timeStamp' => $timestamp,
            'nonceStr' => $nonceStr,
            'package' => $package,
            'signType' => 'RSA',
            'paySign' => $paySign
        ];
        return json_encode($data);
    }

    /**
     * 吊起支付 封裝
     * @param string appid App的APPID或者小程序的appid
     * @param string prepay_id 合单支付返回的prepay_id
     */
    public static function _PayAppJson($appid, $prepay_id)
    {
        $timestamp = time();
        $nonceStr = self::createNoncestr();
        $paySign = self::paySign($appid, $timestamp, $nonceStr, $prepay_id);
        $data = [
            'appid' => $appid,
            'partnerid' => Config::$config['MCHID'],
            'prepayid' => $prepay_id,
            'package' => 'Sign=WXPay',
            'noncestr' => $nonceStr,
            'timestamp' => $timestamp,
            'sign' => $paySign
        ];
        return json_encode($data);
    }

    /**
     * 支付pay计算签名
     * @param string appid 微信公众号的APPID或者小程序的appid
     * @param string timestamp 时间戳
     * @param string nonceStr 随机字符串
     * @param string body 签名主体内容 prepay_id=wx_12121
     */
    private static function paySign($appid, $timestamp, $nonceStr, $body)
    {
        if (!in_array('sha256WithRSAEncryption', \openssl_get_md_methods(true))) {
            throw new WxPayv3Exception("当前PHP环境不支持SHA256withRSA");
        }
        $mch_private_key = self::getPrivateKey(Config::$config['SSLKEY_PATH']);       //商户私钥
        $message = $appid . "\n" . $timestamp . "\n" . $nonceStr . "\n" . $body . "\n";
        openssl_sign($message, $raw_sign, $mch_private_key, 'sha256WithRSAEncryption');
        $paySign = base64_encode($raw_sign);
        return $paySign;
    }

    //获取私钥
    private static function getPrivateKey($filepath)
    {
        return openssl_get_privatekey(file_get_contents($filepath));
    }

    //加载证书 公钥
    public static function getCertificate($filepath)
    {
        return openssl_x509_read(file_get_contents($filepath));
    }
    //作用：产生随机字符串，不长于32位
    private static function createNoncestr($length = 32)
    {
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    /**
     *  [getEncrypt V3敏感信息进行加密]
     *  使用的是公钥 ok
     * @param string str要加密的内容
     */
    public static function getEncrypt($str)
    {
        //$public_key_path = '证书地址'; //看情况使用证书， 个别接口证书 使用的是 平台证书而不是 api证书
        $serial_no = Cert::certificates();
        $mch_public_key = self::getCertificate(Config::$config['PLATFORM_CERT_PATH']);

        $encrypted = '';

        if (openssl_public_encrypt($str, $encrypted, $mch_public_key, OPENSSL_PKCS1_OAEP_PADDING)) {
            //base64编码
            $sign = base64_encode($encrypted);
        } else {
            throw new WxPayv3Exception('加密encrypt failed');
        }
        return $sign;
    }
    /**
     * [getPrivateEncrypt V3敏感信息进行解密]
     * @param string str 需要解密的秘闻 ok
     */
    public static function getPrivateEncrypt($str)
    {
        $result = false;
        $str = base64_decode($str);
        $mch_private_key = self::getPrivateKey(Config::$config['SSLKEY_PATH']);
        if (openssl_private_decrypt($str, $result, $mch_private_key, OPENSSL_PKCS1_OAEP_PADDING)) {
            return $result;
        } else {
            throw new WxPayv3Exception('解密Encrypt failed');
        }
    }

    /**
     * [decryptToString V3 证书和回调报文解密]
     * @param stingr $aesKey V3签名
     * @param string $associatedData 附加数据包
     * @param string $nonceStr 加密使用的随机串初始化向量）
     * @param string $ciphertext Base64编码后的密文
     *
     * @return string|bool      Decrypted string on success or FALSE on failure
     */
    public static function decryptToString($associatedData, $nonceStr, $ciphertext)
    {
        $aesKey = Config::$config['APPKERV3'];
        if (strlen($aesKey) != 32) {
            throw new WxPayv3Exception('无效的ApiV3Key，长度应为32个字节');
        }

        $ciphertext = \base64_decode($ciphertext);
        if (strlen($ciphertext) <= 16) {
            throw new WxPayv3Exception('无效的数据密文，长度应为16个字节以上');
        }

        // ext-sodium (default installed on >= PHP 7.2)
        if (
            function_exists('\sodium_crypto_aead_aes256gcm_is_available') &&
            \sodium_crypto_aead_aes256gcm_is_available()
        ) {
            return \sodium_crypto_aead_aes256gcm_decrypt($ciphertext, $associatedData, $nonceStr, $aesKey);
        }

        // ext-libsodium (need install libsodium-php 1.x via pecl)
        if (
            function_exists('\Sodium\crypto_aead_aes256gcm_is_available') &&
            \Sodium\crypto_aead_aes256gcm_is_available()
        ) {
            return \Sodium\crypto_aead_aes256gcm_decrypt($ciphertext, $associatedData, $nonceStr, $aesKey);
        }

        // openssl (PHP >= 7.1 support AEAD)
        if (PHP_VERSION_ID >= 70100 && in_array('aes-256-gcm', \openssl_get_cipher_methods())) {
            $ctext = substr($ciphertext, 0, -16);
            $authTag = substr($ciphertext, -16);

            return \openssl_decrypt(
                $ctext,
                'aes-256-gcm',
                $aesKey,
                \OPENSSL_RAW_DATA,
                $nonceStr,
                $authTag,
                $associatedData
            );
        }

        throw new WxPayv3Exception('AEAD_AES_256_GCM需要PHP 7.1以上或者安装libsodium-php');
    }

    /**
     * 获取毫秒级别的时间戳
     */
    private static function getMillisecond()
    {
        $time = explode(" ", microtime());
        $time = $time[1] . ($time[0] * 1000);
        $time2 = explode(".", $time);
        $time = $time2[0];
        return $time;
    }
}
