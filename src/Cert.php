<?php

namespace jiujiude\WechatEcommerce;

class Cert
{
    /**
     * [certificates 保存获取的平台证书，且返回证书的序列号serial_no] 缓存一天时间
     * @return string serial_no证书的序列号
     */
    public static function certificates()
    {
        if (file_exists(Config::$config['PLATFORM_SERIAL_NO_PATH'])) {
            if ($serial_nos = file_get_contents(Config::$config['PLATFORM_SERIAL_NO_PATH'])) {
                if ($serial_nos) {
                    $serial_nos = json_decode($serial_nos, true);
                    if (time() < $serial_nos['time']) { //如果 1天缓存未过期，直接使用。
                        return $serial_nos['serial_no'];
                    }
                }
            }
        }

        $url = 'https://api.mch.weixin.qq.com/v3/certificates';
        $r = Signs::_Getresponse($url);
        $r = json_decode($r, true);
        if (isset($r['code'])) {
            return $r;
        }
        $r = $r['data'];
        if (is_array($r)) {
            $associatedData = $r[0]['encrypt_certificate']['associated_data'];
            $nonceStr = $r[0]['encrypt_certificate']['nonce'];
            $ciphertext = $r[0]['encrypt_certificate']['ciphertext'];
            try {
                $data1 = Signs::decryptToString($associatedData, $nonceStr, $ciphertext);
                $serial_no = $r[0]['serial_no'];
                file_put_contents(Config::$config['PLATFORM_CERT_PATH'], $data1);
                unset($serial_nos);
                $serial_nos = [
                    'time' => time() + 24 * 3600 * 365,
                    'serial_no' => $serial_no
                ];
                file_put_contents(Config::$config['PLATFORM_SERIAL_NO_PATH'], json_encode($serial_nos));
            } catch (WxPayv3Exception $th) {
                throw $th;
            }
        }
        return $serial_no;
    }

}