<?php

namespace jiujiude\WechatEcommerce;

//回调数据验证 验证签名 返回数据
class Notify
{

    /**
     * 支付成功回调
     * @param string jsonData 微信支付返回的数据 json字符串
     * @param array headers 头部信息
     */
    public function notifyPay($jsonData, $headers = [])
    {
        if (!$jsonData) {
            throw new WxPayv3Exception('数据为空');
        }
        //先写入日志
        $data = json_decode($jsonData, true);
        if ($data['event_type'] != 'TRANSACTION.SUCCESS') {
            throw new WxPayv3Exception('通知的类型错误');
        }
        if ($data['resource_type'] != 'encrypt-resource') {
            throw new WxPayv3Exception('通知的数据类型错误');
        }
        if (!$data['resource']) {
            throw new WxPayv3Exception('数据错误');
        }

        try {
            $ret = $this->Verify($jsonData, $headers);
        } catch (WxPayv3Exception $th) {
            throw $th;
        }
        if (!$ret) {
            throw new WxPayv3Exception('验证签名失败');
        }
        //然后对回报结果部分数据进行解密
        $resourceData = Signs::decryptToString($data['resource']['associated_data'], $data['resource']['nonce'], $data['resource']['ciphertext']);
        return $resourceData; //返回回调数据 格式参考订单查询格式 类似
    }

    /**
     * 验签
     * 3 构造验签名串 （http头部的Wechatpay-Timestamp，HTTP头Wechatpay-Nonce，返回的body）
     * 4 3个都已 \n 结尾，
     * 获取应答签名 HTTP头Wechatpay-Signature 然后使用base64_decode 解码得到应答签名
     * @param string body 签名主体
     * @param array headers 头部信息
     */
    public function Verify($body, $headers)
    {
        $serial_no = Cert::certificates();
        $mch_public_key = Signs::getCertificate(Config::$config['PLATFORM_CERT_PATH']); //获取平台的公钥
        $head_serial_no = $headers['HTTP_WECHATPAY_SERIAL'];
        if ($serial_no != $head_serial_no) {
            throw new WxPayv3Exception("回调请求证书序列化不一致");
        }
        $timestamp = $headers['HTTP_WECHATPAY_TIMESTAMP'];
        $nonce = $headers['HTTP_WECHATPAY_NONCE'];
        $signature = $headers['HTTP_WECHATPAY_SIGNATURE'];
        $str = $timestamp . "\n" . $nonce . "\n" . ($body ? $body . "\n" : ''); //验签名串
        $signature = base64_decode($signature); //应答签名
        $value = openssl_verify($str, $signature, $mch_public_key, 'sha256WithRSAEncryption');
        if ($value == 1) {
            return true;
        }
        return false;
    }

    /**
     * 退款通知回调
     * @param string jsonData 退款通知返回的信息 json字符串
     * @param array headers 头部信息
     */
    public function notifyRefund($jsonData, $headers = [])
    {
        if (!$jsonData) {
            throw new WxPayv3Exception('数据为空');
        }
        //先写入日志
        $data = json_decode($jsonData, true);
        if ($data['event_type'] != 'REFUND.SUCCESS') {
            throw new WxPayv3Exception('通知的类型错误');
        }
        if ($data['resource_type'] != 'encrypt-resource') {
            throw new WxPayv3Exception('通知的数据类型错误');
        }
        if (!$data['resource']) {
            throw new WxPayv3Exception('数据错误');
        }

        try {
            $ret = $this->Verify($jsonData, $headers);
        } catch (WxPayv3Exception $th) {
            throw $th;
        }
        if (!$ret) {
            throw new WxPayv3Exception('验证签名失败');
        }
        //然后对回报结果部分数据进行解密
        $resourceData = Signs::decryptToString($data['resource']['associated_data'], $data['resource']['nonce'], $data['resource']['ciphertext']);
        return $resourceData; //返回回调数据 格式参考订单查询格式 类似
    }

    /**
     * 获取请求头部headers信息 返回数组
     */
    function em_getallheaders()
    {
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }
}