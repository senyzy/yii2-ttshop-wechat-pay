<?php
/**
 * Created by IntelliJ IDEA.
 * User: wdp
 * Date: 2017/5/26
 * Time: 10:18
 */

namespace senyzy\wechat;


class Pay extends Base
{

    /**
     * 统一下单
     * @param array $args [
     *
     * 'body' => '商品描述',
     *
     * 'detail' => '商品详情，选填',
     *
     * 'attach' => '附加数据，选填',
     *
     * 'out_trade_no' => '商户订单号，最大长度32',
     *
     * 'total_fee' => '订单总金额，单位为分',
     *
     * 'notify_url' => '异步接收微信支付结果通知的回调地址，通知url必须为外网可访问的url，不能携带参数',
     *
     * 'trade_type' => '交易类型，可选值：JSAPI，NATIVE，APP',
     *
     * 'product_id' => '商品ID，trade_type=NATIVE时，此参数必传',
     *
     * 'openid' => '用户标识，trade_type=JSAPI时，此参数必传',
     *
     * ]
     *
     * @return array|boolean
     *
     */
    public function unifiedOrder($args)
    {
        $args['appid'] = $args['trade_type'] === 'APP' ? $this->wechat->uni_app_id : $this->wechat->appId;
        $args['mch_id'] = $this->wechat->mchId;
        $args['nonce_str'] = md5(uniqid());
        $args['sign_type'] = 'MD5';
        $args['spbill_create_ip'] = $args['spbill_create_ip'] ? $args['spbill_create_ip'] : '127.0.0.1';
        $args['sign'] = $this->makeSign($args);
        $xml = DataTransform::arrayToXml($args);
        $api = "https://api.mch.weixin.qq.com/pay/unifiedorder";
        $this->wechat->curl->post($api, $xml);
        if (!$this->wechat->curl->response)
            return false;
        return DataTransform::xmlToArray($this->wechat->curl->response);
    }

    /**
     * 请求单次分账
     * @param $args [
     * 'transaction_id' => 微信订单号
     *
     * 'out_order_no' => 商户分账单号
     *
     * 'receivers' => [
     *
     *      'type' => 分账接收方类型 MERCHANT_ID：商户号（mch_id或者sub_mch_id）PERSONAL_OPENID：个人openid
     *
     *      'account' => 分账接收方帐号  类型是MERCHANT_ID时，是商户号（mch_id或者sub_mch_id）类型是PERSONAL_OPENID时，是个人openid
     *
     *      'amount' => 分账金额 分账金额，单位为分，只能为整数，不能超过原订单支付金额及最大分账比例金额
     *
     *      'description' => 分账描述 分账的原因描述，分账账单中需要体现
     *
     *      'name' =>  分账个人接收方姓名 可选项，在接收方类型为个人的时可选填，若有值，会检查与 name 是否实名匹配，不匹配会拒绝分账请求.
     *                 分账接收方类型是PERSONAL_OPENID时，是个人姓名（选传，传则校验）
     *
     * ]
     * @return array|bool
     */
    public function Profitsharing($args)
    {
        $args['appid'] = $args['trade_type'] === 'APP' ? $this->wechat->uni_app_id : $this->wechat->appId;
        $args['mch_id'] = $this->wechat->mchId;
        $args['nonce_str'] = md5(uniqid());
        $args['sign_type'] = 'HMAC-SHA256';
        $args['sign'] = $this->makeSign($args);
        $xml = DataTransform::arrayToXml($args);
        $api = "https://api.mch.weixin.qq.com/secapi/pay/profitsharing";
        $this->wechat->curl->post($api, $xml);
        if (!$this->wechat->curl->response)
            return false;
        return DataTransform::xmlToArray($this->wechat->curl->response);

    }

    /**
     * 添加分账接收方
     * @param $args
     * @return array|bool
     */
    public function Profitsharingaddreceiver($args)
    {
        $args['appid'] = $args['trade_type'] === 'APP' ? $this->wechat->uni_app_id : $this->wechat->appId;
        $args['mch_id'] = $this->wechat->mchId;
        $args['nonce_str'] = md5(uniqid());
        $args['sign_type'] = 'HMAC-SHA256';
        $args['sign'] = $this->makeSign($args);
        $xml = DataTransform::arrayToXml($args);
        $api = "https://api.mch.weixin.qq.com/pay/profitsharingaddreceiver";
        $this->wechat->curl->post($api, $xml);
        if (!$this->wechat->curl->response)
            return false;
        return DataTransform::xmlToArray($this->wechat->curl->response);
    }

    /**
     * 查询订单待分账金额
     * @return array|bool
     */
    public function Profitsharingorderamountquery($args)
    {
        $args['mch_id'] = $this->wechat->mchId;
        $args['nonce_str'] = md5(uniqid());
        $args['sign_type'] = 'HMAC-SHA256';
        $args['sign'] = $this->makeSign($args);
        $xml = DataTransform::arrayToXml($args);
        $api = "https://api.mch.weixin.qq.com/pay/profitsharingorderamountquery";
        $this->wechat->curl->post($api, $xml);
        if (!$this->wechat->curl->response)
            return false;
        return DataTransform::xmlToArray($this->wechat->curl->response);

    }

    public function orderQuery($order_no)
    {
        $data = [
            'appid' => $this->wechat->appId,
            'mch_id' => $this->wechat->mchId,
            'out_trade_no' => $order_no,
            'nonce_str' => md5(uniqid()),
        ];
        $data['sign'] = $this->makeSign($data);
        $xml = DataTransform::arrayToXml($data);
        $api = "https://api.mch.weixin.qq.com/pay/orderquery";
        $this->wechat->curl->post($api, $xml);
        if (!$this->wechat->curl->response)
            return false;
        return DataTransform::xmlToArray($this->wechat->curl->response);
    }

    /**
     * 获取H5支付签名数据包
     * @param array $args [
     *
     * 'body' => '商品描述',
     *
     * 'detail' => '商品详情，选填',
     *
     * 'attach' => '附加数据，选填',
     *
     * 'out_trade_no' => '商户订单号，最大长度32',
     *
     * 'total_fee' => '订单总金额，单位为分',
     *
     * 'notify_url' => '异步接收微信支付结果通知的回调地址，通知url必须为外网可访问的url，不能携带参数',
     *
     * 'openid' => '用户标识',
     *
     * ]
     *
     * @return array|null
     */
    public function getJsSignPackage($args)
    {
    }

    /**
     * 获取APP支付签名数据包
     * @param array $args [
     *
     * 'body' => '商品描述',
     *
     * 'detail' => '商品详情，选填',
     *
     * 'attach' => '附加数据，选填',
     *
     * 'out_trade_no' => '商户订单号，最大长度32',
     *
     * 'total_fee' => '订单总金额，单位为分',
     *
     * 'notify_url' => '异步接收微信支付结果通知的回调地址，通知url必须为外网可访问的url，不能携带参数',
     *
     * ]
     *
     * @return array|null
     */
    public function getAppSignPackage($args)
    {
    }

    /**
     * 退款申请
     * @param array $args [
     *
     *
     * 'out_trade_no' => '商户订单号，最大长度32',
     *
     * 'out_refund_no' => '商户退款单号，最大长度32',
     *
     * 'total_fee' => '订单总金额，单位为分',
     *
     * 'refund_fee' => '退款总金额，单位为分',
     *
     * ]
     *
     * @return array|null
     */
    public function refund($args)
    {
        $args['appid'] = $this->wechat->appId;
        $args['mch_id'] = $this->wechat->mchId;
        $args['nonce_str'] = md5(uniqid());
        $args['op_user_id'] = $this->wechat->mchId;
        $args['sign'] = $this->makeSign($args);
        $xml = DataTransform::arrayToXml($args);
        $api = 'https://api.mch.weixin.qq.com/secapi/pay/refund';
        $this->wechat->curl->post($api, $xml);
        if (!$this->wechat->curl->response)
            return false;
        return DataTransform::xmlToArray($this->wechat->curl->response);
    }


    /**
     * 企业付款，企业向用户支付
     * @param array $args [
     *
     *
     * 'partner_trade_no' => '商户订单号，最大长度32',
     *
     * 'openid' => '用户openid',
     *
     * 'amount' => '提现金额，单位为分',
     *
     * 'desc' => '企业付款操作说明，例如：提现',
     *
     * ]
     */
    public function transfers($args)
    {
        $args['mch_appid'] = $this->wechat->appId;
        $args['mchid'] = $this->wechat->mchId;
        $args['nonce_str'] = md5(uniqid());
        $args['check_name'] = 'NO_CHECK';
        $args['spbill_create_ip'] = '127.0.0.1';
        $args['sign'] = $this->makeSign($args);
        $xml = DataTransform::arrayToXml($args);
        $api = "https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers";
        $this->wechat->curl->post($api, $xml);
        if (!$this->wechat->curl->response)
            return false;
        return DataTransform::xmlToArray($this->wechat->curl->response);
    }

    /**
     * 发放普通红包
     */
    public function sendRedPack($args)
    {
    }

    /**
     * 发放裂变红包
     */
    public function sendGroupRedPack($args)
    {
    }

    /**
     * 签名
     * @param $args
     * @return string
     */
    public function makeSign($args)
    {
        if (isset($args['sign']))
            unset($args['sign']);
        ksort($args);
        foreach ($args as $i => $arg) {
            if ($args === null || $arg === '')
                unset($args[$i]);
        }
        $string = DataTransform::arrayToUrlParam($args, false);
        $string = $string . "&key={$this->wechat->apiKey}";
        if ($args['sign_type'] == 'HMAC-SHA256') {
            $string = hash_hmac("sha256", $string, $this->wechat->apiKey);
        } else {
            $string = md5($string);
        }
        $result = strtoupper($string);
        return $result;
    }

    public function GetmakeSign($args)
    {
        if (isset($args['sign']))
            unset($args['sign']);
        ksort($args);
        foreach ($args as $i => $arg) {
            if ($args === null || $arg === '')
                unset($args[$i]);
        }
        $string = DataTransform::arrayToUrlParam($args, false);

        $string = $string . "&key=" . $this->wechat->apiKey;
        $string = md5($string);
        $result = strtoupper($string);
        return $result;

    }

    /**
     * @param $order_no
     * @return array|bool
     * 退款查询
     */
    public function refundQuery($order_no)
    {
        $data = [
            'appid' => $this->wechat->appId,
            'mch_id' => $this->wechat->mchId,
            'out_trade_no' => $order_no,
            'nonce_str' => md5(uniqid()),
        ];
        $data['sign'] = $this->makeSign($data);
        $xml = DataTransform::arrayToXml($data);
        $api = "https://api.mch.weixin.qq.com/pay/refundquery";
        $this->wechat->curl->post($api, $xml);
        if (!$this->wechat->curl->response)
            return false;
        return DataTransform::xmlToArray($this->wechat->curl->response);
    }

    /**
     * @param  $args
     * @return array|bool
     * uniapp 二次签名
     */
    public function uniSign(array $args)
    {
        $res = [
            'appid' => $args['appid'],
            'noncestr' => substr(md5(uniqid()), mt_rand(10, 20)),
            'package' => 'Sign=WXPay',
            'partnerid' => $args['mch_id'],
            'prepayid' => $args['prepay_id'],
            'timestamp' => (int)time(),
        ];
        $res['sign'] = $this->makeSign($res);
        return $res;
    }


}