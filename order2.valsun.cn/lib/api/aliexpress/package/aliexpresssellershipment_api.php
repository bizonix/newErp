<?php
/**
 * 亚马逊抓单类-标记发货
 */
include_once WEB_PATH."lib/api/aliexpress/aliexpressSession.php";
class AliexpressSellerShipment extends AliexpressSession{
    public function __construct(){
		parent::__construct();
	}
    /********************************************************
	 *	对对应订单标记发放， 支持全部发货， 部分发货
	 *	var	serviceName	物流服务简称
	 *	var	logisticsNo	物流追踪号
	 *	var	sendType	发送方式（all,part）
	 *	var	outRef		对应的订单号
	 */
	public function sellerShipment($serviceName, $logisticsNo, $sendType, $outRef, $description=""){
		$data	=	array(
			'access_token'	=>	$this->access_token,
			'serviceName'	=>	$serviceName,
			'logisticsNo'	=>	$logisticsNo,
			'sendType'		=>	$sendType,
			'outRef'		=>	$outRef
		);
		
		if(!empty($description)){
			$data['description'] = $description;
		}
		$url = "{$this->server}/{$this->rootpath}/{$this->protocol}/{$this->version}/{$this->ns}/api.sellerShipment/{$this->appKey}";
		return json_decode($this->Curl($url,$data),true);	
	}
}
?>
