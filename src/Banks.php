<?php
/**
 * Created by PhpStorm.
 * User: hgq <393210556@qq.com>
 * Date: 2022/07/07
 * Time: 下午 12:22
 */

namespace jiujiude\WechatEcommerce;

//银行组件
class Banks
{
    /**
     * 获取对私银行卡号开户银行API
     * @author hgq <393210556@qq.com>.
     * @date: 2022/07/07 12:24
     */
    public function searchBanks($account_number)
    {
        $url = 'https://api.mch.weixin.qq.com/v3/capital/capitallhh/banks/search-banks-by-bank-account';
        $url .= '?account_number=' . urlencode(Signs::getEncrypt($account_number));
        $ret = Signs::_Getresponse($url);
        $ret = json_decode($ret, true);
        return $ret;
    }

    /**
     * 省列表
     * @return mixed
     * @author hgq <393210556@qq.com>.
     * @date: 2022/07/07 15:02
     */
    public function provinces()
    {
        $url = 'https://api.mch.weixin.qq.com/v3/capital/capitallhh/areas/provinces';
        $ret = Signs::_Getresponse($url);
        $ret = json_decode($ret, true);
        return $ret;
    }

    /**
     * 市列表
     * @param $province_code
     * @return mixed
     * @author hgq <393210556@qq.com>.
     * @date: 2022/07/07 15:02
     */
    public function cities($province_code)
    {
        $url = 'https://api.mch.weixin.qq.com/v3/capital/capitallhh/areas/provinces/' . $province_code . '/cities';
        $ret = Signs::_Getresponse($url);
        $ret = json_decode($ret, true);
        return $ret;
    }

    /**
     * 查询支行列表API
     * @param $bank_alias_code
     * @return mixed
     * @author hgq <393210556@qq.com>.
     * @date: 2022/07/07 15:02
     */
    public function branches($bank_alias_code, $city_code)
    {
        $url = "https://api.mch.weixin.qq.com/v3/capital/capitallhh/banks/{$bank_alias_code}/branches?city_code={$city_code}&offset=0&limit=200";
        $ret = Signs::_Getresponse($url);
        $ret = json_decode($ret, true);
        return $ret;
    }

    /**
     * 查询支持个人业务的银行列表API
     * @author hgq <393210556@qq.com>.
     * @date: 2022/07/07 12:24
     */
    public function personalBank()
    {
        $bank_list = [];
        $url = 'https://api.mch.weixin.qq.com/v3/capital/capitallhh/banks/personal-banking';
        $url .= '?offset=0&limit=200';
        while (true) {
            $data = Signs::_Getresponse($url);
            $data = json_decode($data, true);
            $bank_list = array_merge($bank_list, $data['data']);
            $next_url = $data['links']['next'];
            $url = 'https://api.mch.weixin.qq.com' . $next_url;
            if (empty($next_url)) {
                break;
            }
        }
        return $bank_list;
    }

    /**
     * 查询支持对公业务的银行列表API
     * @author hgq <393210556@qq.com>.
     * @date: 2022/07/07 12:24
     */
    public function corporateBank()
    {
        $bank_list = [];
        $url = 'https://api.mch.weixin.qq.com/v3/capital/capitallhh/banks/corporate-banking';
        $url .= '?offset=0&limit=200';
        while (true) {
            $data = Signs::_Getresponse($url);
            $data = json_decode($data, true);
            $bank_list = array_merge($bank_list, $data['data']);
            $next_url = $data['links']['next'];
            $url = 'https://api.mch.weixin.qq.com' . $next_url;
            if (empty($next_url)) {
                break;
            }
        }
        return $bank_list;
    }
}