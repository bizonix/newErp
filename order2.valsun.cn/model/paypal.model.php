<?php 
/**
 * 提供paypal付款账户接口
 * @author yxd
 *
 */
class PaypalModel extends CommonModel{
	public function __construct(){
		parent::__construct();
	}	
	
	/**
	 * 根据ebay账号，获取账号信息
	 */
	public function get_paypalByEbayAccount($accountId){
		return $this->sql("SELECT b.account1, b.pass1, b.signature1, b.account2, b.pass2, b.signature2
				FROM `om_account` a JOIN `om_paypal` b 
				ON a.account = b.ebayaccount
				WHERE a.id = '$accountId'")->limit("*")->select();
	}
}

?>