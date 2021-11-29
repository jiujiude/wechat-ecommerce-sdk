<?php

namespace jiujiude\WechatEcommerce;

//支付操作：jsapi native，app 小程序 h5  ，以及对于的支付拉起，查询订单，退款
//目前只有，jsapi 支付和小程序支付，查询订单，退款
class Pay
{
    /**[closingorder 合单下单-JSAPI支付]
     *
     * @param  [type] $combine_appid        [合单发起方的appid]
     * @param  [type] $openid               [使用合单appid获取的对应用户openid]
     * @param  [type] $combine_mchid        [合单发起方商户号]
     * @param  [type] $combine_out_trade_no [合单支付总订单号]
     * @param  [type] $sub_orders           [子单信息]
     * @param  [type] $time_start           [订单生成时间]
     * @param  [type] $notify_url           [回调通知地址]
     * @param  [type] $limit_pay            [指定支付方式 目前为：no_debit]
     * @return [type]                       [返回参数预支付交易会话标识：prepay_id。示例值：wx201410272009395522657a690389285100]
     */
    public function closingOrderJsapi($combine_out_trade_no, $sub_orders, $openid)
    {
        $url = 'https://api.mch.weixin.qq.com/v3/combine-transactions/jsapi';
        $orders = [];
        foreach ($sub_orders as $order) {
            $orders[] = [
                'mchid' => Config::$config['MCHID'],
                'attach' => $order['attach'] ?? '',
                //附加数据，在查询API和支付通知中原样返回，可作为自定义参数使用。  示例值：深圳分店
                'out_trade_no' => $order['out_trade_no'] ?? '',
                //商户系统内部订单号，要求32个字符内，只能是数字、大小写字母_-|*@ ，且在同一个商户号下唯一。特殊规则：最小字符长度为6 示例值：20150806125346
                'sub_mchid' => strval($order['sub_mchid']),
                //二级商户商户号，由微信支付生成并下发。 示例值：1900000109
                //'detail' => $order['detail'],
                //商品详情描述
                //'profit_sharing' => $order['profit_sharing'],
                //是否指定分账
                'description' => $order['description'],
                //商品描述商品简单描述。需传入应用市场上的APP名字-实际商品名称，例如：天天爱消除-游戏充值。示例值：腾讯充值中心-QQ会员充值
                'amount' => [ //子单金额，单位为分。
                    'total_amount' => $order['total_amount'],
                    'currency' => 'CNY'
                ],
                'settle_info' => [
                    'profit_sharing' => $order['profit_sharing_settle'], //是否分账，与外层profit_sharing同时存在时，以本字段为准。 示例值：true
                    //'subsidy_amount' => $order['subsidy_amount'] //SettleInfo.profit_sharing为true时，该金额才生效。示例值：10
                ]
            ];
        }
        $paramData = [
            //合单商户appid
            'combine_appid' => Config::$config['PUB']['COMBINE_APPID'],
            //合单发起方的appid  示例值：wxd678efh567hg6787
            //合单发起方商户号
            'combine_mchid' => Config::$config['COMBINE_MCHID'],
            //合单发起方商户号。示例值：1900000109
            //合单商户订单号
            'combine_out_trade_no' => $combine_out_trade_no,
            //合单支付总订单号，要求32个字符内，只能是数字、大小写字母_-|*@ ，且在同一个商户号下唯一。示例值：P20150806125346
            'sub_orders' => $orders,
            //支付者 支付者信息
            'combine_payer_info' => [
                //子单商户号
                'openid' => $openid //使用合单appid获取的对应用户openid。是用户在商户appid下的唯一标识。 示例值：oUpF8uMuAJO_M2pxb1Q9zNjWeS6o
            ],
            //交易起始时间
            //'time_start' => $time_start,
            //订单生成时间，遵循rfc3339标准格式，格式为YYYY-MM-DDTHH:mm:ss+TIMEZONE，YYYY-MM-DD表示年月日，T出现在字符串中，表示time元素的开头，HH:mm:ss表示时分秒，TIMEZONE表示时区（+08:00表示东八区时间，领先UTC 8小时，即北京时间）。例如：2015-05-20T13:29:35+08:00表示，北京时间2015年5月20日 13点29分35秒。示例值：2019-12-31T15:59:60+08:00
            //交易结束时间
            //'time_expire'    => $time_start,//订单失效时间，遵循rfc3339标准格式，格式为YYYY-MM-DDTHH:mm:ss+TIMEZONE，YYYY-MM-DD表示年月日，T出现在字符串中，表示time元素的开头，HH:mm:ss表示时分秒，TIMEZONE表示时区（+08:00表示东八区时间，领先UTC 8小时，即北京时间）。例如：2015-05-20T13:29:35+08:00表示，北京时间2015年5月20日 13点29分35秒。示例值：2019-12-31T15:59:60+08:00
            //通知地址
            'notify_url' => Config::$config['PAY_NOTIFY_URL'],
            //接收微信支付异步通知回调地址，通知url必须为直接可访问的URL，不能携带参数。格式: URL 示例值：https://yourapp.com/notify
            //指定支付方式
            //'limit_pay' => array($limit_pay)
            //指定支付方式 示例值：no_debit
        ];
        if (empty($openid)) { //兼容H5 电商收付通暂不支持
            unset($paramData['combine_payer_info']);
        }
        $parameters = json_encode($paramData);
        return Signs::_Postresponse($url, $parameters);
    }

    /**[closingorder 合单下单-APP支付]
     *
     * @param  [type] $combine_out_trade_no [合单支付总订单号]
     * @param  [type] $sub_orders           [子单信息]
     * @return [type]                       [返回参数预支付交易会话标识：prepay_id。示例值：wx201410272009395522657a690389285100]
     */
    public function closingOrderApp($combine_out_trade_no, $sub_orders)
    {
        $url = 'https://api.mch.weixin.qq.com/v3/combine-transactions/app';
        $orders = [];
        foreach ($sub_orders as $order) {
            $orders[] = [
                'mchid' => Config::$config['MCHID'],
                'attach' => $order['attach'] ?? '',
                //附加数据，在查询API和支付通知中原样返回，可作为自定义参数使用。  示例值：深圳分店
                'out_trade_no' => $order['out_trade_no'] ?? '',
                //商户系统内部订单号，要求32个字符内，只能是数字、大小写字母_-|*@ ，且在同一个商户号下唯一。特殊规则：最小字符长度为6 示例值：20150806125346
                'sub_mchid' => strval($order['sub_mchid']),
                //二级商户商户号，由微信支付生成并下发。 示例值：1900000109
                //'detail' => $order['detail'],
                //商品详情描述
                //'profit_sharing' => $order['profit_sharing'],
                //是否指定分账
                'description' => $order['description'],
                //商品描述商品简单描述。需传入应用市场上的APP名字-实际商品名称，例如：天天爱消除-游戏充值。示例值：腾讯充值中心-QQ会员充值
                'amount' => [ //子单金额，单位为分。
                    'total_amount' => $order['total_amount'],
                    'currency' => 'CNY'
                ],
                'settle_info' => [
                    'profit_sharing' => $order['profit_sharing_settle'], //是否分账，与外层profit_sharing同时存在时，以本字段为准。 示例值：true
                    //'subsidy_amount' => $order['subsidy_amount'] //SettleInfo.profit_sharing为true时，该金额才生效。示例值：10
                ]
            ];
        }
        $paramData = [
            //合单商户appid
            'combine_appid' => Config::$config['APP']['COMBINE_APPID'],
            //合单发起方的appid  示例值：wxd678efh567hg6787
            //合单发起方商户号
            'combine_mchid' => Config::$config['COMBINE_MCHID'],
            //合单发起方商户号。示例值：1900000109
            //合单商户订单号
            'combine_out_trade_no' => $combine_out_trade_no,
            //合单支付总订单号，要求32个字符内，只能是数字、大小写字母_-|*@ ，且在同一个商户号下唯一。示例值：P20150806125346
            'sub_orders' => $orders,
            //支付者 支付者信息
            //'combine_payer_info' => [
            //    //子单商户号
            //    'openid' => $openid //使用合单appid获取的对应用户openid。是用户在商户appid下的唯一标识。 示例值：oUpF8uMuAJO_M2pxb1Q9zNjWeS6o
            //],
            //交易起始时间
            //'time_start' => $time_start,
            //订单生成时间，遵循rfc3339标准格式，格式为YYYY-MM-DDTHH:mm:ss+TIMEZONE，YYYY-MM-DD表示年月日，T出现在字符串中，表示time元素的开头，HH:mm:ss表示时分秒，TIMEZONE表示时区（+08:00表示东八区时间，领先UTC 8小时，即北京时间）。例如：2015-05-20T13:29:35+08:00表示，北京时间2015年5月20日 13点29分35秒。示例值：2019-12-31T15:59:60+08:00
            //交易结束时间
            //'time_expire'    => $time_start,//订单失效时间，遵循rfc3339标准格式，格式为YYYY-MM-DDTHH:mm:ss+TIMEZONE，YYYY-MM-DD表示年月日，T出现在字符串中，表示time元素的开头，HH:mm:ss表示时分秒，TIMEZONE表示时区（+08:00表示东八区时间，领先UTC 8小时，即北京时间）。例如：2015-05-20T13:29:35+08:00表示，北京时间2015年5月20日 13点29分35秒。示例值：2019-12-31T15:59:60+08:00
            //通知地址
            'notify_url' => Config::$config['PAY_NOTIFY_URL'],
            //接收微信支付异步通知回调地址，通知url必须为直接可访问的URL，不能携带参数。格式: URL 示例值：https://yourapp.com/notify
            //指定支付方式
            //'limit_pay' => array($limit_pay)
            //指定支付方式 示例值：no_debit
        ];
        $parameters = json_encode($paramData);
        return Signs::_Postresponse($url, $parameters);
    }

    /**
     * 微信js和jsapi 和h5  支付吊起
     * @param string prepay_id 合单支付返回的prepay_id
     */
    public function appPay($prepay_id)
    {
        $appid = Config::$config['APP']['COMBINE_APPID'];
        return Signs::_PayAppJson($appid, $prepay_id);
    }

    /**
     * 微信js和jsapi 和h5  支付吊起
     * @param string prepay_id 合单支付返回的prepay_id
     */
    public function jsPay($prepay_id)
    {
        $appid = Config::$config['PUB']['COMBINE_APPID'];
        return Signs::_PayJson($appid, $prepay_id);
    }

    /**
     * 小程序 吊起支付
     * @param string prepay_id 合单支付返回的prepay_id
     */
    public function xcxPay($prepay_id)
    {
        $appid = Config::$config['XCX']['COMBINE_APPID'];
        return Signs::_PayJson($appid, $prepay_id);
    }

    /**
     * [findorder 合单查询订单]
     * 电商平台通过合单查询订单API 查询订单状态
     * @param string combine_out_trade_no 合单商户订单号,自己生成的父订单号
     */
    public function findorder($combine_out_trade_no)
    {
        $url = 'https://api.mch.weixin.qq.com/v3/combine-transactions/out-trade-no/' . $combine_out_trade_no;
        $re = Signs::_Getresponse($url);
        return $re;
    }

    /**
     * [closeorder 合单关闭订单]
     * 合单支付订单只能使用此合单关单api完成关单。
     * @param string combine_out_trade_no 合单商户订单号,自己生成的父订单号
     * @param array orders 子单信息
     *  [ mchid 子单商户号  子单发起方商户号，必须与发起方appid有绑定关系
     *  out_trade_no 子单商户订单号  商户系统内部订单号，
     *  sub_mchid  二级商户号  由微信支付生成并下发]
     */
    public function closeorder($combine_out_trade_no, $orders)
    {
        $url = 'https://api.mch.weixin.qq.com/v3/combine-transactions/out-trade-no/' . $combine_out_trade_no . '/close';
        $data = [
            'combine_appid' => Config::$config['MCHID'],
            'sub_orders' => $orders
        ];
        $data = json_encode($data);
        $re = Signs::_Postresponse($url, $data);
        return $re;
    }


    /**
     * 退款申请API 收付通
     * 备注：交易时间超过一年的订单无法提交退款；每个支付订单的部分退款次数不能超过50次
     * @param string sub_mchid 微信支付分配二级商户的商户号
     * @param string out_refund_no 原支付交易对应的商户订单号 子订单号
     * @param string transaction_id 原支付交易对应的微信订单号  子流水号
     * @param string out_trade_no 子退款单号 自己编写
     * @param string refund_fee 子单的申请的退款金额 分
     * @param string total_fee 子单的全部金额  分
     * @param string notify_url 退款回调地址
     * @param string reason 退款原因
     * @param string sign 退款单来源 weixin xcx h5 等，后期会涉及到使用
     */
    public function applyRefund(
        $sub_mchid,
        $out_refund_no,
        $transaction_id,
        $refund_fee,
        $total_fee,
        $reason = '',
        $out_trade_no = ''
    ) {
        $post = [
            'sub_mchid' => strval($sub_mchid), //二级商户号
            // 'sub_appid' => $sub_appid, //二级商户APPID 可空
            'sp_appid' => Config::$config['COMBINE_APPID'], //电商平台APPID 可空
            'transaction_id' => $transaction_id,
            'out_trade_no' => $out_trade_no,
            'out_refund_no' => strval($out_refund_no),
            'reason' => $reason,
            'amount' => [
                'refund' => $refund_fee,
                'total' => $total_fee,
                'currency' => 'CNY'
            ],
            'notify_url' => Config::$config['REFUND_NOTIFY_URL']
        ];
        if (empty($out_trade_no)) {
            unset($post['out_trade_no']);
        }
        if (empty($reason)) {
            unset($post['reason']);
        }
        $url = 'https://api.mch.weixin.qq.com/v3/ecommerce/refunds/apply';
        $ret = Signs::_Postresponse($url, json_encode($post));
        return $ret;
    }

    /**
     * 查询退款状态 情况
     * @param string sub_mchid 二级商户号
     * @param string refund_id 微信退款单号 微信返回的退款单号 以微信退款单号为主
     * @param string out_refund_no 退款申请编号 平台自己生产的退款编号
     */
    public function findRefund($sub_mchid, $refund_id = '', $out_refund_no = '')
    {
        $url = 'https://api.mch.weixin.qq.com/v3/ecommerce/refunds/';
        if ($refund_id) {
            $url .= 'id/' . $refund_id;
        } else {
            $url .= 'out-refund-no/' . $out_refund_no;
        }
        $url .= '?sub_mchid=' . $sub_mchid;
        $ret = Signs::_Getresponse($url);
        return $ret;
    }
}
