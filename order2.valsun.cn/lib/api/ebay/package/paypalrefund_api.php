<?php
/**
 * ebay paypal退款
 * add by yxd 2014/08/18
 */
include_once WEB_PATH."lib/api/ebay/eBaySession.php";
class PaypalRefund extends eBaySession{
	
	public function __construct(){
		parent::__construct();
	}
	
	/**
	 * curl发送退款信息
	 * @param array
	 * @return array
	 * @author yxd
	 */
	public function curlRefund($dataArr){
		$paypal_account     = trim($dataArr['paypalAccount']);
		$paypal_passwd      = trim($dataArr['pass']);
		$signature          = trim($dataArr['signature']);
		$account			= trim($dataArr['totalSum']);
		$transactionID		= urlencode(trim($dataArr['PayPalPaymentId']));
		//$transactionID	= urlencode('8UB78354D73053524'); //for test.........
		$refundType			= urlencode(trim($dataArr['refundType']));
		$currencyID			= urlencode($dataArr['currency']);
		$amount				= trim($dataArr['refundSum']);
		$memo               = $dataArr['note'];
		$nvpStr             = "&TRANSACTIONID=$transactionID&REFUNDTYPE=$refundType&CURRENCYCODE=$currencyID&NOTE=$memo";
		
		if($refundType == 'Partial'){
			$nvpStr=$nvpStr."&AMT=$amount";
		}
		//var_dump($paypal_account,$paypal_passwd,$signature,'RefundTransaction', $nvpStr , $account);
		$ppRtnInfo = $this->PPHttpPost($paypal_account,$paypal_passwd,$signature,'RefundTransaction', $nvpStr , $account);
		return $ppRtnInfo;
	}
	
}
?>
