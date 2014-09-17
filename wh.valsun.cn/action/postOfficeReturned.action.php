<?php
/*
*邮局退回
*/
class PostOfficeReturnedAct extends Auth{
	public static $errCode = 0;
    public static $errMsg = '';
    
    /*
     * 构造函数
     */
    public function __construct() {
    }
	
	
    public function act_returnToerp(){
    	$ebay_id		=	isset($_REQUEST['orderid'])?$_REQUEST['orderid']:"";
    	$p_real_ebayid='#^\d+$#';
    	$p_trackno_eub='#^(LK|RA|RB|RC|RR|RF|LN)\d+(CN|HK|DE200)$#';
    	if(	!preg_match($p_real_ebayid,$ebay_id) && !preg_match($p_trackno_eub,$ebay_id)){
    		self::$errCode = 401;
    		self::$errMsg  = "订单号[".$ebay_id."]格式有误";
    		return false;
    	}
    	$paramArr= array(
    			'method'  		=> 	'erp.process_scan_return_order_ajax.php',  //API名称
    			'ebay_id' 	=> 	 $ebay_id
    	);
    	$data = UserCacheModel::callOpenSystem2($paramArr);
    	return  $data;
    }
    /*
     *扫描订单号
    */
    public function act_search($buyer_userid="",$recordnumber=""){
    	if(!empty($buyer_userid) || !empty($recordnumber)){
    		$paramArr= array(
    				'method'  			=> 	'erp.scan_returnSearch.php',  //API名称
    				'buyer_userid' 		=> 	 $buyer_userid,
    				'recordnumber' 	    => 	 $$recordnumber
    		);
    		$data = UserCacheModel::callOpenSystem2($paramArr);
    		return $data;
    	}else{
    		self::$errCode = "301";
    		self::$errMsg  = "非法搜索条件不";
    		return false;
    	}
    }
	public function act_return(){
		$orderid = isset($_POST['orderid'])?$_POST['orderid']:"";
		$userId = $_SESSION['userId'];
		
		//先核对订单
		//兼容 EUB或者 包裹 扫描的是 trackno 而非ebayid
		$p_real_ebayid='#^\d+$#';
		$p_trackno_eub='#^(LK|RA|RB|RC|RR|RF|LN)\d+(CN|HK|DE200)$#';
		$is_eub_package_type=false;
		if(	preg_match($p_real_ebayid,$orderid) ){
		}else if( preg_match($p_trackno_eub,$orderid) ){
			$is_eub_package_type=true;
		}else{
			self::$errCode = 401;
			self::$errMsg  = "订单号[".$orderid."]格式有误";
			return false;			
		}

		if($is_eub_package_type===true){
			$record = ShippingOrderModel::getShippingOrderInfo("b.*","where a.tracknumber='$orderid' and a.is_delete=0");
		}else{
			$record = ShippingOrderModel::getShippingOrder("*","where id='$orderid'");
		}

		//验证发货单号 以及所属状态
		if(!$record){
			self::$errCode = 402;
			self::$errMsg = "发货单号不存在！";
			return false;
		}
		
		if($record[0]['orderStatus'] != 501){
			self::$errCode = 403;
			self::$errMsg = "此发货单不是已发货！";
			return false;
		}
		
		$order_detail = get_realskunum($record[0]['id']);
		$return_info  = PostReturnModel::getReturnList("*","where shipOrderId={$record[0]['id']}");
		
		if(empty($return_info)){
			$data 	= array();
			$qc_arr = array();
			$time 	= time();
			OmAvailableModel::begin();
			foreach($order_detail as $sku=>$num){
				$data['shipOrderId'] = $record[0]['id'];
				$data['sku']         = $sku;
				$data['amount']      = $num;
				$data['returnTime']  = $time;
				$insert_info = PostReturnModel::insertRow($data);
				if(!$insert_info){
					self::$errCode = 404;
					self::$errMsg  = "订单录入失败";
					OmAvailableModel::rollback();
					return false;
				}
				
				$qc_arr[$record[0]['id']][$sku] = $num;
			}
			$qcinfo = CommonModel::qcOrderBackDetect(json_encode($qc_arr));
			if(!$qcinfo){
				self::$errCode = 405;
				self::$errMsg  = "订单录入失败";
				OmAvailableModel::rollback();
				return false;
			}
			OmAvailableModel::commit();
			self::$errMsg  = "订单录入成功";
		}else{
			self::$errCode = 406;
			self::$errMsg  = "该订单已录入系统，请不要重复录入";
			return false;
		}
		
	}
}	
?>
