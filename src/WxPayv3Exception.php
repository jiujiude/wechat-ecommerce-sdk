<?php

namespace WxPayEcommerce;

/**
 * 
 * 微信支付API异常类
 * @author widyhu
 *
 */
class WxPayv3Exception extends \Exception {
	public function errorMessage()
	{
		return $this->getMessage();
	}
}
