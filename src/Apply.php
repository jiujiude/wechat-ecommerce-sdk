<?php

namespace jiujiude\WechatEcommerce;

//商户进件 SDK
//分普通服务商 也就是特约 和 电商收付通的二级商户
class Apply
{

    /*****************************************************收付通二级商户进件* */
    /**[applyment 二级商户进件]
     *
     * 电商平台，可使用该接口，帮助其二级商户进件成为微信支付商户。
     * out_request_no string 业务申请编号
     * organization_type  int 主体类型
     * business_license_info obj 营业执照/登记证书信息 (小微/个人卖家不填，个体工商户/企业上传营业执照，党政、机关及事业单位/其他组织 登记证书)
     *     business_license_copy string 证件扫描件 先上传图片生成好的MediaID
     *     business_license_number string 证件注册号
     *     merchant_name string 商户名称
     *     legal_person  string 经营者/法定代表人姓名
     *     company_address string 注册地址（选填）
     *     business_time string 营业期限（选填）
     * organization_cert_info obj 组织机构代码证信息  主体为“企业/党政、机关及事业单位/其他组织”，且营业执照/登记证书号码不是18位时必填。
     *     organization_copy string 组织机构代码证照片 上传图片生成好的MediaID。
     *     organization_number string 组织机构代码
     *     organization_time string 组织机构代码有效期限
     * id_doc_type string 经营者/法人证件类型 （非必填）
     *      1、主体为“小微/个人卖家”，可选择：身份证。
     *      2、主体为“个体户/企业/党政、机关及事业单位/其他组织”，可选择：以下任一证件类型。
     *      3、若没有填写，系统默认选择：身份证。
     *      枚举值:
     *      IDENTIFICATION_TYPE_MAINLAND_IDCARD：中国大陆居民-身份证
     *      IDENTIFICATION_TYPE_OVERSEA_PASSPORT：其他国家或地区居民-护照
     *      IDENTIFICATION_TYPE_HONGKONG：中国香港居民–来往内地通行证
     *      IDENTIFICATION_TYPE_MACAO：中国澳门居民–来往内地通行证
     *      IDENTIFICATION_TYPE_TAIWAN：中国台湾居民–来往大陆通行证
     * id_card_info obj 经营者/法人身份证信息  请填写经营者/法人的身份证信息  证件类型为“身份证”时填写
     *      id_card_copy  string 身份证人像面照片 请上传经营者/法定代表人的身份证人像面照片 MediaID
     *      id_card_national  string 身份证国徽面照片     MediaID
     *      id_card_name string 身份证姓名 加密处理
     *      id_card_number string 身份证号码 加密处理
     *      id_card_valid_time string 身份证有效期限
     * id_doc_info obj 经营者/法人其他类型证件信息 证件类型为“来往内地通行证、来往大陆通行证、护照”时填写。
     *      id_doc_name  string 证件姓名
     *      id_doc_number  string 证件号码
     *      id_doc_copy string 证件照片     MediaID
     *      doc_period_end string证件结束日期
     * need_account_info bool  是否填写结算银行账户
     * account_info obj  结算银行账户 当 need_account_info为true时，这边必填
     *      bank_account_type  string 账户类型 （74,75）
     *      account_bank string 开户银行
     *      account_name string 开户名称
     *      bank_address_code string 开户银行省市编码    省区市编号
     *      bank_branch_id string 开户银行联行号 17家直连银行无需填写，如为其他银行，开户银行全称（含支行）和开户银行联行号二选一。
     *      bank_name string string 开户银行全称 （含支行)   17家直连银行无需填写，如为其他银行，开户银行全称（含支行）和开户银行联行号二选一。
     *      account_number string 银行帐号 加密处理
     * contact_info obj 超级管理员信息  超级管理员需在开户后进行签约
     *      contact_type string 超级管理员类型     65 66
     *      contact_name string 超级管理员姓名 加密处理
     *      contact_id_card_number  string 超级管理员身份证件号码 加密处理 超级管理员签约时，校验微信号绑定的银行卡实名信息，是否与该证件号码一致
     *      mobile_phone string 超级管理员手机    加密处理
     *      contact_email string 超级管理员邮箱 需要带@，遵循邮箱格式校验 。 加密处理
     * sales_scene_info obj 店铺信息 必填
     *      merchant_shortname string 商户简称 （48字符内）
     *      qualifications string  特殊资质 多张图片[\"jTpGmxUX3FBWVQ5NJInE4d2I6_H7I4\"] （1、若从事互联网售药，则需提供 《互联网药品交易服务证》；2、最多可上传5张照片，请填写通过图片上传接口预先上传图片生成好的MediaID 。）
     *      business_addition_pics string 补充材料  多张图片[\"jTpGmxUX3FBWVQ5NJInE4d2I6_H7I4\"]
     *      business_addition_desc  string 512字内 若主体为“个人卖家”，则需填写描述“ 该商户已持续从事电子商务经营活动满6个月，且期间经营收入累计超过20万元。”
     */
    public function applyment($param = [])
    {
        $url = 'https://api.mch.weixin.qq.com/v3/ecommerce/applyments/';
        if (isset($param['id_card_info']['id_card_name'])) {
            $param['id_card_info']['id_card_name'] = Signs::getEncrypt($param['id_card_info']['id_card_name']);
        }
        if (isset($param['id_card_info']['id_card_number'])) {
            $param['id_card_info']['id_card_number'] = Signs::getEncrypt($param['id_card_info']['id_card_number']);
        }
        if (isset($param['account_info']['account_number'])) {
            $param['account_info']['account_number'] = Signs::getEncrypt($param['account_info']['account_number']);
        }
        if (isset($param['account_info']['account_name'])) {
            $param['account_info']['account_name'] = Signs::getEncrypt($param['account_info']['account_name']);
        }
        if (isset($param['contact_info']['contact_name'])) {
            $param['contact_info']['contact_name'] = Signs::getEncrypt($param['contact_info']['contact_name']);
        }
        if (isset($param['contact_info']['contact_id_card_number'])) {
            $param['contact_info']['contact_id_card_number'] = Signs::getEncrypt($param['contact_info']['contact_id_card_number']);
        }
        if (isset($param['contact_info']['mobile_phone'])) {
            $param['contact_info']['mobile_phone'] = Signs::getEncrypt($param['contact_info']['mobile_phone']);
        }
        if (isset($param['contact_info']['contact_email'])) {
            $param['contact_info']['contact_email'] = Signs::getEncrypt($param['contact_info']['contact_email']);
        }

        $data = json_encode($param);
        return Signs::_Postresponse($url, $data);
    }

    /**
     * getApplymentByid 查询申请状态 电商收付通可用
     *  方式1：业务申请编号查询申请状态；
     * @param string applyment_id 申请单ID
     */
    public function getApplymentByid($applyment_id)
    {
        $url = 'https://api.mch.weixin.qq.com/v3/ecommerce/applyments/' . $applyment_id;
        $ret = Signs::_Getresponse($url);
        $data = json_decode($ret, true);
        //银行账户和名称需解密
        if (isset($data['account_validation']['account_name'])) {
            $name = $data['account_validation']['account_name'];
            $names = Signs::getPrivateEncrypt($name);
            $data['account_validation']['account_name'] = $names;
        }
        if (isset($data['account_validation']['account_no'])) {
            $account_no = $data['account_validation']['account_no'];
            $account_no = Signs::getPrivateEncrypt($account_no);
            $data['account_validation']['account_no'] = $account_no;
        }
        $ret = json_encode($data);
        return $ret;
    }

    /**
     * getApplymentByno 查询申请状态 收付通
     * 方式2：申请单号查询申请状态。
     * @param string out_request_no 业务申请编号
     */
    public function getApplymentByno($out_request_no)
    {
        $url = 'https://api.mch.weixin.qq.com/v3/ecommerce/applyments/out-request-no' . $out_request_no;

        $data = Signs::_Getresponse($url);
        $data = json_decode($data, true);
        //银行账户和名称需解密
        if (isset($data['account_validation']['account_name'])) {
            $name = $data['account_validation']['account_name'];
            $names = Signs::getPrivateEncrypt($name);
            $data['account_validation']['account_name'] = $names;
        }
        if (isset($data['account_validation']['account_no'])) {
            $account_no = $data['account_validation']['account_no'];
            $account_no = Signs::getPrivateEncrypt($account_no);
            $data['account_validation']['account_no'] = $account_no;
        }
        $ret = json_encode($data);
        return $ret;
    }

    /**
     * modifySettlement 修改结算帐号 收付通
     * 通服务商（支付机构、银行不可用），可使用本接口修改其进件、已签约的特约商户-结算账户信息。
     */
    public function modifySettlement($param = [])
    {
        if (strlen($param['sub_mchid']) < 8) {
            throw new WxPayv3Exception('特约商户号:长度最小8个字节');
        }
        $url = 'https://api.mch.weixin.qq.com/v3/apply4sub/sub_merchants/' . $param['sub_mchid'] . '/modify-settlement';
        if ($param['account_number']) {
            $param['account_number'] = Signs::getEncrypt($param['account_number']);
        }

        $data = [
            //'sub_mchid' => strval($param['sub_mchid']),
            'account_type' => $param['account_type'],
            'account_bank' => $param['account_bank'],
            'bank_address_code' => $param['bank_address_code'],
            'bank_name' => $param['bank_name'] ?? '',
            'bank_branch_id' => $param['bank_branch_id'] ?? '',
            'account_number' => $param['account_number'],
        ];

        if (empty($param['bank_name']) || !isset($param['bank_name'])) {
            unset($data['bank_name']);
        }
        if (empty($param['bank_branch_id']) || !isset($param['bank_name'])) {
            unset($data['bank_branch_id']);
        }

        $data = json_encode($data);
        return Signs::_Postresponse($url, $data);
    }

    /**
     * getSettlement 查询结算账户 收付通
     * 普通服务商（支付机构、银行不可用），可使用本接口查询其进件、已签约的特约商户-结算账户信息（敏感信息掩码）。 该接口可用于核实是否成功修改结算账户信息、及查询系统汇款验证结果。
     * @param string sub_mchid 特约商户号
     */
    public function getSettlement($sub_mchid)
    {
        if (strlen($sub_mchid) < 8) {
            throw new WxPayv3Exception('特约商户号:长度最小8个字节');
        }
        $url = 'https://api.mch.weixin.qq.com/v3/apply4sub/sub_merchants/' . $sub_mchid . '/settlement';
        return Signs::_Getresponse($url);
    }

    /****************************************************特约商户进件* */
    /**
     * 特约商户进件 post
     * 注意：不是电商收付通的商户进件哦
     */
    public function applymentTY($param = [])
    {
        $url = 'https://api.mch.weixin.qq.com/v3/applyment4sub/applyment/';

        //法人身份账号和姓名加密
        if ($param['subject_info']['identity_info']['id_card_info']['id_card_name']) {
            $param['subject_info']['identity_info']['id_card_info']['id_card_name'] = Signs::getEncrypt($param['subject_info']['identity_info']['id_card_info']['id_card_name']);
        }
        if ($param['subject_info']['identity_info']['id_card_info']['id_card_number']) {
            $param['subject_info']['identity_info']['id_card_info']['id_card_number'] = Signs::getEncrypt($param['subject_info']['identity_info']['id_card_info']['id_card_number']);
        }

        //结算银行账户 加密
        if ($param['bank_account_info']['account_name']) {
            $param['bank_account_info']['account_name'] = Signs::getEncrypt($param['bank_account_info']['account_name']);
        }
        if ($param['bank_account_info']['account_number']) {
            $param['bank_account_info']['account_number'] = Signs::getEncrypt($param['bank_account_info']['account_number']);
        }

        //超级管理员信息加密
        if ($param['contact_info']['contact_name']) {
            $param['contact_info']['contact_name'] = Signs::getEncrypt($param['contact_info']['contact_name']);
        }
        if ($param['contact_info']['contact_id_number']) {
            $param['contact_info']['contact_id_number'] = Signs::getEncrypt($param['contact_info']['contact_id_number']);
        }
        if ($param['contact_info']['openid']) {
            $param['contact_info']['openid'] = Signs::getEncrypt($param['contact_info']['openid']);
        }
        if ($param['contact_info']['mobile_phone']) {
            $param['contact_info']['mobile_phone'] = Signs::getEncrypt($param['contact_info']['mobile_phone']);
        }
        if ($param['contact_info']['contact_email']) {
            $param['contact_info']['contact_email'] = Signs::getEncrypt($param['contact_info']['contact_email']);
        }
        //

        $data = json_encode($param);
        return Signs::_Postresponse($url, $data);
    }

    /**
     * 根据申请单号 查询特约商户的申请情况
     * @param string applyment_id 申请单号
     */
    public function getApplymentByidTY($applyment_id)
    {
        $url = 'https://api.mch.weixin.qq.com/v3/applyment4sub/applyment/applyment_id/' . $applyment_id;
        $ret = Signs::_Getresponse($url);
        return $ret;
    }

    /**
     * 根据业务申请编号 查询特约商户的申请情况
     * @param string business_code 业务申请编号
     */
    public function getApplymentBynoTY($business_code)
    {
        $url = 'https://api.mch.weixin.qq.com/v3/ecommerce/applyments/business_code' . $business_code;
        $ret = Signs::_Getresponse($url);
        return $ret;
    }

    /**
     * 修改结算帐号 post
     * 注意：普通服务商 修改已签约的特约商户的结算账户信息
     * @param array post 提交参数
     * @param string sub_mchid 特约商户号
     * @param string account_type 账户类型 ACCOUNT_TYPE_BUSINESS：对公银行账户 ACCOUNT_TYPE_PRIVATE：经营者个人银行卡
     * @param string account_bank 开户银行（17个银行账户名称）或 其他银行
     * @param string bank_address_code 开户银行省市编码 （省市编码）
     * @param string bank_name 开户银行全称（含支行）
     * @param string bank_branch_id 开户银行联行号
     * @param string account_number 银行账号 需公钥加密
     */
    public function modifySettlementTY($post = [])
    {
        $url = 'https://api.mch.weixin.qq.com/v3/apply4sub/sub_merchants/' . $post['sub_mchid'] . '/modify-settlement';
        $data = [
            'account_type' => $post['account_type'],
            'account_bank' => $post['account_bank'],
            'bank_address_code' => $post['bank_address_code'],
            'bank_name' => $post['bank_name'],
            'account_number' => Signs::getEncrypt($post['account_number'])
        ];
        if ($post['bank_branch_id']) {
            $data['bank_branch_id'] = $post['bank_branch_id']; //假如存在 就显示
        }
        $data = json_encode($data);
        $ret = Signs::_Postresponse($url, $data); //head状态码是204 假如有误 返回存在code
        return $ret;
    }

    /**
     * 查询结算账户
     * 银行账户或掩码显示
     * @param string sub_mchid 已签约的特约商户号
     */
    public function findSettlementTY($sub_mchid)
    {
        $url = 'https://api.mch.weixin.qq.com/v3/apply4sub/sub_merchants/' . $sub_mchid . '/settlement';
        $ret = Signs::_Getresponse($url); //head状态码是204 假如有误 返回存在code
        return $ret;
    }


}