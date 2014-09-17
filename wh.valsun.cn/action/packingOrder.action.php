<?php
/*
*包装扫描功能
*/
class packingOrderAct extends Auth{
	public static $errCode = 0;
    public static $errMsg = '';
    
    /*
     * 构造函数
     */
    public function __construct() {
    }
	
	/*
	*扫描订单号
	*/
	public function act_packingOrder(){
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
			self::$errCode = "001";
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
			self::$errCode = 404;
			self::$errMsg = "发货单号不存在！";
			return false;
			exit;
		}
		
		if($record[0]['orderStatus'] != 404){			
			self::$errCode = 405;
			self::$errMsg = "此发货单不在待包装！";
			if($record[0]['orderStatus'] == 900){
				self::$errMsg = "此发货单已废弃！";
			}
			if($record[0]['orderStatus'] == 405){
				self::$errMsg = "该订单[{$record[0]['id']}]已经经过包装扫描了!";
			}
			if($record[0]['orderStatus'] == 403){
				self::$errMsg = "该订单[{$record[0]['id']}]还在待复核!";
			}
			return false;
			
		}
		//挂号的单需要再输入挂号条码
		$carrier = CommonModel::getShipingNameById($record[0]['transportId']);
		$partion = printLabelModel::showPartionScan($record[0]['id'],$record[0]['accountId'],$carrier,$record[0]['countryName']);
		
		$total_num = 0;
		$skuinfos = get_realskunum($record[0]['id']);	
		foreach($skuinfos as $or_sku => $or_nums){
			$total_num += $or_nums;
		}
		
		if(in_array($carrier,array("中国邮政挂号","香港小包挂号","德国邮政"))){
			self::$errCode = 222;
			self::$errMsg = "请录入挂号条码！";
			$arr['partion'] = $partion;
			$arr['carrier'] = $carrier;
			return json_encode($arr);
			exit;
		}
		
		
		TransactionBaseModel :: begin();
		
		//更新订单状态
		$msg = OmAvailableModel::updateTNameRow("wh_shipping_order", "set orderStatus=405", "where id={$record[0]['id']}");
		if(!$msg){
			self::$errCode = 409;
			self::$errMsg = "更新发货单状态记录失败！";
			$arr['partion'] = $partion;
			$arr['carrier'] = $carrier;
			TransactionBaseModel :: rollback();
			return json_encode($arr);
			exit;
		}
	
		//更新操作记录
		$msg = packingScanOrderModel::updateOrderRecord($record[0]['id'],$userId);
		if(!$msg){
			self::$errCode = 406;
			self::$errMsg = "更新发货单操作记录失败！";
			TransactionBaseModel :: rollback();
			return false;
		}
		
		//插入包装记录
		$msg = packingScanOrderModel::insertPackingRecord($record[0]['id'],$userId);
		if(!$msg){
			self::$errCode = 407;
			self::$errMsg = "插入包装记录失败！";
			TransactionBaseModel :: rollback();
			return false;
		}
		WhPushModel::pushOrderStatus($record[0]['id'],'STATESHIPPED_BEWEIGHED',$userId,time());        //状态推送
		$arr['partion']  = "<font color='#FF0000'>该订单属于".$partion.";含SKU总数：".$total_num."个</font><br>";
		$arr['carrier']  = $carrier;
		$arr['userName'] = $_SESSION['userName'];
		$arr['res']   	 = $arr['partion']."包装人员："."<font color='green'>".$_SESSION['userCnName']."</font><br>"."运输方式："."<font color='#FF0000'>".$carrier."</font> <br>扫描结果：<font color='#33CC33'>包装扫描成功!</font>";
		
		TransactionBaseModel :: commit();
		return $arr;
	}
	/*
	*挂号条码录入
	*/
	public function act_packingTracknumber(){
		$orderid = isset($_POST['orderid'])?$_POST['orderid']:"";
		$tracknumber = isset($_POST['tracknumber'])?$_POST['tracknumber']:"";
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
			self::$errCode = "001";
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
			self::$errCode = 404;
			self::$errMsg = "发货单号不存在！";
			return false;
		}		
		if($record[0]['orderStatus'] != 404){
			self::$errCode = 405;
			self::$errMsg = "此发货单不在待包装！";
			if($record[0]['orderStatus'] == 900){
				self::$errMsg = "此发货单已废弃！";
			}
			return false;
		}
		//挂号的单需要再输入挂号条码
		$carrier = CommonModel::getShipingNameById($record[0]['transportId']);
		$partion = CommonModel::getChannelNameByIds($record[0]['transportId'],$record[0]['countryName']);
		
		
		$total_num = 0;
		$skuinfos = get_realskunum($record[0]['id']);	
		foreach($skuinfos as $or_sku => $or_nums){
			$total_num += $or_nums;
		}
		
		if(carrier=='中国邮政挂号' && !preg_match("/^(RA|RB|RC|RR)\d+(CN)$/", $tracknumber)){
			self::$errCode = 111;
			self::$errMsg = "录入失败,中国邮政挂号跟踪码不符合规范";
			return false;
		}
		if($carrier=='香港小包挂号' && !preg_match("/^(RA|RB|RC|RR)\d+(HK)$/", $tracknumber)){
			self::$errCode = 111;
			self::$errMsg = "录入失败,香港小包挂号跟踪码不符合规范";
			return false;
		}
		$p_str = "挂号条码："."<font color='#FF0000'>".$tracknumber."</font> <br>";		
				
		TransactionBaseModel :: begin();
		
		//更新订单状态
		$msg = OmAvailableModel::updateTNameRow("wh_shipping_order", "set orderStatus=405", "where id={$record[0]['id']}");
		if(!$msg){
			self::$errCode = 409;
			self::$errMsg = "更新发货单状态记录失败！";
			$arr['partion'] = $partion;
			$arr['carrier'] = $carrier;
			TransactionBaseModel :: rollback();
			return json_encode($arr);
			exit;
		}
		
		//更新操作记录
		$msg = packingScanOrderModel::updateOrderRecord($record[0]['id'],$userId);
		if(!$msg){
			self::$errCode = 406;
			self::$errMsg = "更新发货单操作记录失败！";
			TransactionBaseModel :: rollback();
			return false;
		}
		
		//插入包装记录
		$msg = packingScanOrderModel::insertPackingRecord($record[0]['id'],$userId);
		if(!$msg){
			self::$errCode = 407;
			self::$errMsg = "插入包装记录失败！";
			TransactionBaseModel :: rollback();
			return false;
		}
		
		$msg = packingScanOrderModel::deleteTrackRecord($record[0]['id']);
		if(!$msg){
			self::$errCode = 409;
			self::$errMsg = "删除跟踪记录失败！";
			TransactionBaseModel :: rollback();
			return false;
		}
		//录入挂号条码
		$msg = packingScanOrderModel::insertTrackRecord($tracknumber,$record[0]['id']);
		if(!$msg){
			self::$errCode = 408;
			self::$errMsg = "插入包装记录失败！";
			TransactionBaseModel :: rollback();
			return false;
		}
		WhPushModel::pushOrderStatus($record[0]['id'],'STATESHIPPED_BEWEIGHED',$userId,time(),'',$tracknumber);        //状态推送
		$arr['partion'] = "<font color='#FF0000'>该订单属于".$partion.";含SKU总数：".$total_num."个</font><br>";
		$arr['carrier'] = $carrier;
		$arr['res']   	= $arr['partion']."包装人员："."<font color='green'>".$_SESSION['userCnName']."</font><br>"."运输方式："."<font color='#FF0000'>".$carrier."</font> <br>".$p_str."扫描结果：<font color='#33CC33'>包装扫描成功!</font>";
		TransactionBaseModel :: commit();
		return $arr;
	}
}	
?>
