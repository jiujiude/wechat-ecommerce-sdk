<?php

namespace jiujiude\WechatEcommerce;

//查询余额 账单下载
class Bill
{
    /**
     * 查询平台电商的实时余额 fund_type=1 https://api.mch.weixin.qq.com/v3/merchant/fund/balance/{account_type}
     * 查询平台电商的日终余额 fund_type=2 https://api.mch.weixin.qq.com/v3/merchant/fund/dayendbalance/{account_type}
     * @param int fund_type 1是实时余额 2是日终余额（可选90天内的）
     * @param string account_type 账户类型 (BASIC：基本账户 OPERATION：运营账户 FEES：手续费账户)
     * @param string date 指定查询商户日终余额的日期 实时无需传递
     */
    public function getComBalance($fund_type = 1, $account_type = 'BASIC', $date = '')
    {
        $http_url = '';
        $body = '';
        if (!in_array($account_type, ['BASIC', 'OPERATION', 'FEES'])) {
            throw new WxPayv3Exception('查询的账户类型不存在');
        }
        switch ($fund_type) {
            case 1:
                $http_url = 'https://api.mch.weixin.qq.com/v3/merchant/fund/balance/' . $account_type;
                break;
            case 2:
                $date = $date ?: date('Y-m-d');
                $http_url = 'https://api.mch.weixin.qq.com/v3/merchant/fund/dayendbalance/' . $account_type . '?date=' . $date;
                $body = json_encode(['date' => $date]);
                break;
            default:
                break;
        }
        if ($http_url == '') {
            throw new WxPayv3Exception('请求路径错误');
        }
        $ret = Signs::_Getresponse($http_url, $body);
        return $ret;
    }

    /**
     * 查询二级商会的实时余额 fund_type=1 https://api.mch.weixin.qq.com/v3/ecommerce/fund/balance/{sub_mchid}
     * 查询二级商户的日终余额 fund_type=2 https://api.mch.weixin.qq.com/v3/ecommerce/fund/enddaybalance/{sub_mchid}
     * @param int fund_type 1是实时余额 2是日终余额（可选90天内的）
     * @param string sub_mchid 电商特约商户商户号sub_mchid
     * @param string date 指定查询商户日终余额的日期
     */
    public function getShopBalance($sub_mchid, $fund_type = 1, $date = '')
    {
        if (empty($sub_mchid)) {
            throw new WxPayv3Exception('查询的二级商户不能为空');
        }
        $http_url = '';
        $body = '';
        switch ($fund_type) {
            case 1:
                $http_url = 'https://api.mch.weixin.qq.com/v3/ecommerce/fund/balance/' . $sub_mchid;
                break;
            case 2:
                $date = $date ?: date('Y-m-d');
                $http_url = 'https://api.mch.weixin.qq.com/v3/ecommerce/fund/enddaybalance/' . $sub_mchid . '?date=' . $date;
                $body = json_encode(["date" => $date]);
                break;
            default:
                break;
        }
        if ($http_url == '') {
            throw new WxPayv3Exception('请求路径错误');
        }
        $ret = Signs::_Getresponse($http_url, $body);
        return $ret;
    }


    /**
     * 申请交易账单 默认显示的账单下载地址 和hash对比
     * @param string filepath 账单的下载路径
     * @param string bill_date 账单日期90天内且非当日
     * @param string sub_mchid 二级商户号（直连商户：空，存在则是某一个二级商户的账单，空是全部）
     * @param string bill_type 账单类型 (ALL 所有，SUCCESS 成功，REFUND 退款)
     * @param string tar_type 压缩类型 （默认数据流，GZIP 压缩包）
     * @return string 返回的是下载地址，需要前面加上路径
     */
    public function tradeBill($filepath, $bill_date, $sub_mchid = '', $bill_type = 'ALL', $tar_type = '')
    {
        $url = 'https://api.mch.weixin.qq.com/v3/bill/tradebill?bill_date=' . $bill_date;
        if (!empty($sub_mchid)) {
            $url .= '&sub_mchid=' . $sub_mchid;
        }
        $url .= '&bill_type=' . $bill_type;
        if (!empty($tar_type)) {
            $url .= '&tar_type=' . $tar_type;
        }
        $ret = Signs::_Getresponse($url);
        // return $ret;
        $ret = json_decode($ret, true);
        if (isset($ret['code'])) {
            return $ret;
        }
        $down = $this->saveBillFile($ret, '交易账单_' . $bill_type, $filepath);
        if ($down) {
            return $down;
        } else {
            throw new WxPayv3Exception('下载出错');
        }
    }

    /**
     * 申请资金账单
     * @param array data 申请账单的数组包括
     * @param string name 文件名称
     * @param string filepath 保存的文件路径
     * hash_type  hash_value  download_url
     */
    private function saveBillFile($data, $name, $filepath)
    {
        $url = $data['download_url'];
        $ret = Signs::_Getresponse($url);
        $file = $name . '_' . date('YmdHIs') . '.xlsx';
        if (file_put_contents($filepath . $file, $ret)) {
            return $file;
        } else {
            return false;
        }
    }

    /**
     * 申请资金账单 默认显示的账单下载地址 和hash对比
     * @param string filepath 账单的下载路径
     * @param string bill_date 账单日期90天内且非当日
     * @param string account_type 资金账户类型 (BASIC 基本账户, OPERATION 运营账户 ，FEES 手续费账户)
     * @param string tar_type 压缩类型 （默认数据流，GZIP 压缩包）
     * @return string 返回的是下载地址，需要前面加上路径
     */
    public function fundBill($filepath, $bill_date, $account_type = 'BASIC', $tar_type = '')
    {
        $url = 'https://api.mch.weixin.qq.com/v3/bill/fundflowbill?bill_date=' . $bill_date;
        if (empty($account_type)) {
            $url .= '&account_type=' . $account_type;
        }
        if (!empty($tar_type)) {
            $url .= '&tar_type=' . $tar_type;
        }
        $ret = Signs::_Getresponse($url);
        $ret = json_decode($ret, true);
        if (isset($ret['code'])) {
            return $ret;
        }
        $down = $this->saveBillFile($ret, '资金账单_' . $account_type, $filepath);
        if ($down) {
            return $down;
        } else {
            throw new WxPayv3Exception('下载出错');
        }
    }
}