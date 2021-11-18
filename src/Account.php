<?php

namespace jiujiude\WechatEcommerce;

/**
 * 分账功能
 * Class Account
 * @package WechatEcommerce
 */
class Account
{
    /**-----------------------分账接口
     * 包括：请求分账，查询分账结果，请求分账回退，查询分账回退，完结分账，添加分账接收方，删除分账接收方，分账动账通知
     */
    /**
     * 添加分账接收方 post
     * @param string type  接收方类型 MERCHANT_ID 商户  PERSONAL_OPENID 个人
     * @param string account 分账接收方的账号 类型为商户时是商户号，个人时是openid
     * @param string name 接收方名称 假如是商户时 是商户全程，假如是个人则是个人姓名(需加密换encrypted_name字段)
     * @param string relation_type 分账方的关系类型  SERVICE_PROVIDER服务商，PLATFORM 平台，SUPPLIER 供应商，DISTRIBUTOR 分销商 ，OTHERS其他
     * @return object
     */
    public function addReceivers($type, $account, $name, $relation_type = 'SERVICE_PROVIDER')
    {
        $url = 'https://api.mch.weixin.qq.com/v3/ecommerce/profitsharing/receivers/add';
        $post = [
            'appid' => Config::$config['COMBINE_APPID'],
            'type' => $type,
            'account' => $account,
            'relation_type' => $relation_type  //SUPPLIER：供应商 DISTRIBUTOR：分销商 SERVICE_PROVIDER：服务商 PLATFORM：平台 OTHERS：其他
        ];
        if ($type == 'MERCHANT_ID') {
            $post['name'] = $name;
        }
        if ($type == 'PERSONAL_OPENID') {
            $post['encrypted_name'] = Signs::getEncrypt($name);
        }
        $post = json_encode($post, JSON_UNESCAPED_UNICODE);
        $ret = Signs::_Postresponse($url, $post);
        if ($type == 'PERSONAL_OPENID') {
        }
        return $ret;
    }

    /**
     * 删除分账接收方 post
     * @param string type 接收方类型 MERCHANT_ID 商户  PERSONAL_OPENID 个人
     * @param string account 分账接收方的账号 类型为商户时是商户号，个人时是openid
     * @return object
     */
    public function delReceivers($type, $account)
    {
        $url = 'https://api.mch.weixin.qq.com/v3/ecommerce/profitsharing/receivers/delete';
        $post = [
            'appid' => Config::$config['COMBINE_APPID'],
            'type' => $type,
            'account' => $account
        ];
        $ret = Signs::_Postresponse($url, $post);
        return $ret;
    }

    /**
     * 发起分账请求 post 最大30%
     * 注意：对同一笔订单最多能发起20次分账请求，每次请求最多分给5个接收方
     * @param string out_order_no   商户分账单号 自己生成的唯一的单号 64位内
     * @param string transaction_id 微信订单号 订单支付流水号
     * @param string sub_mchid 分账出资的二级商户号，微信支付分配的
     * @param string receivers 分账接收方列表。最多5个，包括(类型，账号，金额，描述)
     * @param bool finish 是否结束分账，true是解冻二级商户金额，false可继续分账
     */
    public function reqAccount($out_order_no, $transaction_id, $sub_mchid, $receivers, $finish = true)
    {
        $url = 'https://api.mch.weixin.qq.com/v3/ecommerce/profitsharing/orders';
        $post = [
            'appid' => Config::$config['COMBINE_APPID'],
            'out_order_no' => $out_order_no,
            'transaction_id' => $transaction_id,
            'sub_mchid' => strval($sub_mchid),
            'receivers' => $receivers,
            'finish' => $finish
        ];
        $post = json_encode($post);
        $ret = Signs::_Postresponse($url, $post);
        return $ret;
    }

    /**
     * 查询分账结果 get
     * @param string out_order_no   商户分账单号 自己生成的唯一的单号 64位内
     * @param string transaction_id 微信订单号 订单支付流水号
     * @param string sub_mchid 分账出资的二级商户号，微信支付分配的
     */
    public function findAccount($out_order_no, $transaction_id, $sub_mchid)
    {
        $url = 'https://api.mch.weixin.qq.com/v3/ecommerce/profitsharing/orders';
        $url .= '?sub_mchid=' . $sub_mchid . '&transaction_id=' . $transaction_id . '&out_order_no=' . $out_order_no;
        $ret = Signs::_Getresponse($url);
        return $ret;
    }

    /**
     * 完结分账 post  收付通
     * 不需要分账的账单直接把二级商户里面的金额直接解冻给二级商户
     * @param string out_order_no  商户分帐单号 唯一 自己生成
     * @param string transaction_id 微信支付子单号的流水号
     * @param string sub_mchid 电商平台二级商户
     * @param string description 描述
     */
    public function finishAccount($out_order_no, $transaction_id, $sub_mchid, $description)
    {
        $url = 'https://api.mch.weixin.qq.com/v3/ecommerce/profitsharing/finish-order';
        $post = [
            'sub_mchid' => strval($sub_mchid),
            'transaction_id' => $transaction_id,
            'out_order_no' => $out_order_no,
            'description' => $description ?: '分账完结'
        ];
        $ret = Signs::_Postresponse($url, $post);
        return $ret;
    }

    /**
     * 请求分账回退 post
     * 注意：订单已经分账，在退款时，可以先调此接口，将已分账的资金从分账接收方的账户回退给分账方，再发起退款。
     * 对同一笔分账单最多能发起20次分账回退请求
     * @param string sub_mchid 分账出资的电商平台二级商户
     * @param string order_id 微信分账单号
     * @param string out_order_no  分账单号 和 order_id 二选一
     * @param string out_return_no 回退单号 自己生成
     * @param string return_mchid 回退商户号 只能对原分账请求中成功分给商户接收方进行回退
     * @param int  amount 回退金额
     * @param string description 回退描述
     */
    public function returnAccount(
        $sub_mchid,
        $order_id,
        $out_order_no,
        $out_return_no,
        $return_mchid,
        $amount,
        $description
    ) {
        $url = 'https://api.mch.weixin.qq.com/v3/ecommerce/profitsharing/returnorders';
        $post = [
            'sub_mchid' => $sub_mchid,
            'order_id' => $order_id,
            'out_order_no' => $out_order_no,
            'out_return_no' => $out_return_no,
            'return_mchid' => $return_mchid,
            'amount' => $amount * 1,
            'description' => $description
        ];
        $post = json_encode($post);
        $ret = Signs::_Postresponse($url, $post);
        return $ret;
    }

    /**
     * 查询分账回退结果 get
     * @param string sub_mchid 分账出资的电商平台二级商户
     * @param string order_id 微信分账单号
     * @param string out_order_no  分账单号 和 order_id 二选一
     * @param string out_return_no 回退单号  请求分账回退生成的 out_return_no
     */
    public function findreturnAccount($sub_mchid, $order_id = '', $out_order_no = '', $out_return_no)
    {
        $url = 'https://api.mch.weixin.qq.com/v3/ecommerce/profitsharing/returnorders';
        if ($order_id) {
            $url .= '?sub_mchid=' . $sub_mchid . '&order_id=' . $order_id . '&out_return_no=' . $out_return_no;
        } else {
            $url .= '?sub_mchid=' . $sub_mchid . '&out_order_no=' . $out_order_no . '&out_return_no=' . $out_return_no;
        }
        $ret = Signs::_Getresponse($url);
        return $ret;
    }

}