<?php

namespace jiujiude\WechatEcommerce;

//上传图片，上传物理地址返回 微信的media_id
class Uploads
{

    /** [upload 商户收付通图片上传]
     * @param  [type] $imgpath        [文件物理地址]
     * @return [type] [返回参数媒体文件标识 media_id 示例值：6uqyGjGrCf2GtyXP8bxrbuH9-aAoTjH-rKeSl3Lf4_So6kdkQu4w8BYVP3bzLtvR38lxt4PjtCDXsQpzqge_hQEovHzOhsLleGFQVRF-U_0]
     */
    public function upload($imgpath)
    {
        $url = 'https://api.mch.weixin.qq.com/v3/merchant/media/upload';
        $filename = $imgpath;

        $fi = new \finfo(FILEINFO_MIME_TYPE);
        $mime_type = $fi->file($filename);

        $basename = basename($filename);
        $data['filename'] = $basename;
        $meta['filename'] = $basename;
        $meta['sha256'] = hash_file('sha256', $filename);
        $boundary = uniqid(); //分割符号
        $date = time();
        $nonce = self::createNoncestr();
        $sign = Signs::sign($url, 'POST', $date, $nonce, json_encode($meta)); //$http_method要大写
        $header[] = 'User-Agent:Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.108 Safari/537.36';
        $header[] = 'Accept:application/json';
        $header[] = 'Authorization:WECHATPAY2-SHA256-RSA2048 ' . $sign;
        $header[] = 'Content-Type:multipart/form-data;boundary=' . $boundary;

        $boundaryStr = "--{$boundary}\r\n";
        $out = $boundaryStr;
        $out .= 'Content-Disposition: form-data; name="meta"' . "\r\n";
        $out .= 'Content-Type: application/json' . "\r\n";
        $out .= "\r\n";
        $out .= json_encode($meta) . "\r\n";
        $out .= $boundaryStr;
        $out .= 'Content-Disposition: form-data; name="file"; filename="' . $data['filename'] . '"' . "\r\n";
        $out .= 'Content-Type: ' . $mime_type . ';' . "\r\n";
        $out .= "\r\n";
        $out .= file_get_contents($filename) . "\r\n";
        $out .= "--{$boundary}--\r\n";
        $r = Signs::_requestPost($url, $out, $header);

        return $r;
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
}
