<?php
/*
 * aliexpress平台对接接口
 * add by: linzhengxiang @date 20140618
 */
class AliexpressButtAct extends CheckAct{
    
    private $account;
	private $appKey;
    private $appSecret;
    private $refresh_token;

	public function __construct(){
		parent::__construct();
	}

	public function setToken($account){
		######################以后扩展到接口获取 start ######################
		$appKey		    = "6149795";
		$appSecret		= "7xrXDiAZ8NwR";
		$refresh_token	= "1e2dccdb-d2b2-43ab-9e08-0643797de27f";
		######################以后扩展到接口获取  end  ######################
        $this->account        = $account;
		$this->appKey         = $appKey;
        $this->appSecret      = $appSecret;
        $this->refresh_token  = $refresh_token;
	}

	/**
	 * 抓取处于 'orderStatus'  => 'WAIT_SELLER_SEND_GOODS' 的订单
	 * @return array 订单数组
	 * @author zqt
	 */
	public function findOrderListQuery($createDateStart = '', $createDateEnd = ''){
		$OrderObject = F('aliexpress.package.AliexpressGetOrders');
        //var_dump($OrderObject);exit;
		$OrderObject->setConfig($this->account, $this->appKey, $this->appSecret, $this->refresh_token);
        $OrderObject->doInit();
        $orderList = $OrderObject->findOrderListQuery($createDateStart, $createDateEnd);
		return $orderList;
	}

    /**
	 * 标记发货 对对应订单标记发放， 支持全部发货， 部分发货
	 *  var serviceName	物流服务简称
	 *  var logisticsNo	物流追踪号
	 *	var	sendType	发送方式（all,part）
	 *	var	outRef		对应的订单号
	 */
	public function sellerShipment($serviceName, $logisticsNo, $sendType, $outRef, $description=''){
		$OrderObject = F('aliexpress.package.AliexpressSellerShipment');
		$OrderObject->setConfig($this->account, $this->appKey, $this->appSecret, $this->refresh_token);
        $OrderObject->doInit();
        $data = $OrderObject->sellerShipment($serviceName, $logisticsNo, $sendType, $outRef, $description="");
		return $data;
	}
    
    public function get_carrier_name($ebay_carrier){
		if(in_array($ebay_carrier, array('Hongkong Post Air Mail', 'HK Post Air Mail', 'HKPAM', 'Hongkong Post Airmail', 'HK Post Airmail','HongKong Post Air Mail'))){
			$ebay_carrier		= '香港小包挂号';
		}
		if(in_array($ebay_carrier, array('UPSS', 'UPS Express Saver'))){
			$ebay_carrier		= 'UPS';
		}
		
		if($ebay_carrier   == 'DHL'){
			$ebay_carrier		= 'DHL';
		}
		
		if($ebay_carrier   == 'EMS'){
			$ebay_carrier		= 'EMS';
		}
		
		if(in_array($ebay_carrier, array('ChinaPost Post Air Mail', 'China Post Air Mail', 'CPAM', 'China Post Airmail'))){
			$ebay_carrier		= '中国邮政挂号';
		}
		
		if($ebay_carrier=='ePacket'){
			$ebay_carrier = 'EUB';
		}

		if($ebay_carrier == "Fedex IE"){
			$ebay_carrier = 'FedEx';
		}
		return $ebay_carrier;
    }


    public function time_shift($origin_num) { //转换成时间戳
    	$time_offset	=	0;
    	$i	=	0;
    	$i	=	strpos($origin_num,"-");
    	
    	if($i > 0){
    		$temp	=	explode("-", $origin_num);
    		$utc	=	intval(preg_replace("/0/","",$temp[1]));
    		$time_offset	=	time() - 3600*(8+ $utc);	
    	}
    	$i	=	0;
    	$i	=	strpos($origin_num,"+");
    	if($i > 0){
    		$temp	=	explode("+", $origin_num);
    		$utc	=	intval(preg_replace("/0/","",$temp[1]));
    		if($utc > 8){
    			$time_offset	=	time() + 3600*($utc - 8);	
    		}else{
    			$time_offset	=	time() - 3600*(8 - $utc);	
    		}
    	}
    	$time	=	strtotime(substr($origin_num,0,14));
    	return array($time, $time_offset);
    }
    
    //根据平台返回的国家简码返回对应的国家全称，需完成
    public function get_country_name($code){
        return $code;
    }
}
?>