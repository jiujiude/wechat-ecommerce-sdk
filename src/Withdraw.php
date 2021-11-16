<?php

namespace jiujiude\WechatEcommerce;

//商户提现
class Withdraw
{
    /**
     * 二级商户余额提现
     * @param $sub_mchid
     * @param $out_request_no
     * @param $amount
     * @param string $remark
     * @param string $bank_memo
     * @param string $account_type
     * @return bool|string
     */
    public function fundWithdraw(
        $sub_mchid,
        $out_request_no,
        $amount,
        $remark = '交易提现',
        $bank_memo = '',
        $account_type = ''
    ) {
        $url = 'https://api.mch.weixin.qq.com/v3/ecommerce/fund/withdraw';
        $data = [
            'sub_mchid' => strval($sub_mchid), //二级商户号
            'out_request_no' => $out_request_no,
            'amount' => $amount,
            'remark' => $remark,
            'bank_memo' => $bank_memo,
            'account_type' => $account_type,
        ];
        if (empty($data['bank_memo'])) {
            unset($data['bank_memo']);
        }
        if (empty($data['account_type'])) {
            unset($data['account_type']);
        }
        $data = json_encode($data);
        return Signs::_Postresponse($url, $data);
    }

    /**
     * 查询提现状态 微信支付提现单号查询
     * @param $sub_mchid
     * @param $withdraw_id
     * @return bool|string
     */
    public function getWithdrawByid($sub_mchid, $withdraw_id)
    {
        $url = 'https://api.mch.weixin.qq.com/v3/ecommerce/fund/withdraw/' . $withdraw_id . '?sub_mchid=' . $sub_mchid;
        $body = '';
        $ret = Signs::_Getresponse($url, $body);
        return $ret;
    }

    /**
     * 查询提现状态 商户提现单号查询
     * @param $sub_mchid
     * @param $out_request_no
     * @return bool|string
     */
    public function getWithdrawByno($sub_mchid, $out_request_no)
    {
        $url = 'https://api.mch.weixin.qq.com/v3/ecommerce/fund/withdraw/out-request-no/' . $out_request_no . '?sub_mchid=' . $sub_mchid;
        $body = '';
        $ret = Signs::_Getresponse($url, $body);
        return $ret;
    }
}