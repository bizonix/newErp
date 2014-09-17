<?php
/**
 * 类名：OrderPushModel
 * 功能：订单系统推送给仓库系统model层
 * 版本：1.0
 * 日期：2013/9/20
 * 作者：管拥军
 */
class OrderPushModel {
	public static $dbConn;
	public static $rabbitMQClass;
	//public static $prefix;
	public static $errCode	= 0;
	public static $errMsg	= "";
	public static $table_order				= "om_unshipped_order";
	public static $table_order_extension	= "om_unshipped_order_extension_";
	public static $table_detail				= "om_unshipped_order_detail";
	public static $table_detail_extension	= "om_unshipped_order_detail_extension_";
	public static $table_userinfo			= "om_unshipped_order_userInfo";
	public static $table_package			= "om_records_combinePackage";
	public static $table_account			= "om_account";
	public static $table_tracknumber		= "om_order_tracknumber";
	public static $table_notes				= "om_order_notes";
	public static $table_express_remark		= "om_express_remark";
	
	//初始化
	public static function	initDB(){
		global $dbConn;
		
		self::$dbConn = $dbConn;
		
		$rmq_config	=	C("RMQ_CONFIG");
		//echo "<pre>"; var_dump($rmq_config); exit;
		$rabbitMQClass= new RabbitMQClass($rmq_config['sendOrder'][1],$rmq_config['sendOrder'][2],$rmq_config['sendOrder'][4],$rmq_config['sendOrder'][0]);//队列对象
		self::$rabbitMQClass = $rabbitMQClass;
		//self::$prefix  =  C("DB_PREFIX");
		//self::$table_user   =  self::$prefix."unshipped_order";;
	}
		
	/**
	 * OrderPushModel::listPushMessage()
	 * 推送信息给仓库系统
	 * @return  array
	 */
    public static function listPushMessage($orderid,$flag=1){
		self::initDB();
		$storeId = 1;
		//$rmq_config	=	C("RMQ_CONFIG");
		//echo "<pre>"; var_dump($rmq_config); exit;
		//$rabbitMQClass= new RabbitMQClass($rmq_config['sendOrder'][1],$rmq_config['sendOrder'][2],$rmq_config['sendOrder'][4],$rmq_config['sendOrder'][0]);//队列对象
		$rabbitMQClass = self::$rabbitMQClass;
		//$ret = array();
		$ids = array();
		$omid= $orderid;
		$affectedrows = 0;
		
		$orderDataInfo = array();
		//基础信息
		$sql = "SELECT * FROM ".self::$table_order." WHERE id = '{$orderid}' AND is_delete = 0 AND storeId = {$storeId} ";
		$query	= self::$dbConn->query($sql);
		$orderData = self::$dbConn->fetch_array($query);
		
		$platfrom = omAccountModel::getPlatformSuffixById($orderData['platformId']);
		$extension = $platfrom['suffix'];//获取后缀名称
		
		$ids[] = $orderid;
		if($orderData['combinePackage']==2){
			self::$errCode	= "1041";
			self::$errMsg	= "此订单为合并包裹子订单，不能单独申请打印！";
			return false;
		}else if ($orderData['combinePackage']==1) { //如果是合并包裹
			$sql = "SELECT split_order_id FROM ".self::$table_package." WHERE main_order_id = '{$orderid}' AND is_enable = 1";
			$query	= self::$dbConn->query($sql);
			$affectedrows = self::$dbConn->affected_rows();           
			if ($affectedrows) {
				$result	= self::$dbConn->fetch_array_all($query);
				for($i=0; $i < count($result); $i++){
					$ids[] = $result[$i]['split_order_id'];
				}
			}
		}
		$actualTotal = 0;
		$actualShipping = 0;
		$calcWeight = 0;
		$calcShipping = 0;
		if(count($ids) > 1){
			foreach($ids as $vid){
				$sql = "SELECT * FROM ".self::$table_order." WHERE id = '{$vid}' ";
				$query	= self::$dbConn->query($sql);
				$orderOneInfo = self::$dbConn->fetch_array($query);
				$actualTotal += $orderOneInfo['actualTotal'];
				$actualShipping += $orderOneInfo['actualShipping'];
				$calcWeight += $orderOneInfo['calcWeight'];
				$calcShipping += $orderOneInfo['calcShipping'];
			}
			$orderData['actualTotal'] = $actualTotal;
			$orderData['actualShipping'] = $actualShipping;
			$orderData['calcWeight'] = $calcWeight;
			$orderData['calcShipping'] = $calcShipping;
		}
		$sql = "SELECT * FROM ".self::$table_order_extension.$extension." WHERE omOrderId = '{$orderid}' ";
		$query	= self::$dbConn->query($sql);
		$orderExtenData = self::$dbConn->fetch_array($query);
		
		$sql = "SELECT * FROM ".self::$table_userinfo." WHERE omOrderId = '{$orderid}' ";
		$query	= self::$dbConn->query($sql);
		$orderUserInfoData = self::$dbConn->fetch_array($query);
		
		$sql = "SELECT * FROM ".self::$table_tracknumber." WHERE omOrderId = '{$orderid}' AND is_delete = 0 ";
		$query	= self::$dbConn->query($sql);
		$orderTrackInfoData = self::$dbConn->fetch_array($query);
		
		if($orderData['transportId'] == ''){
			self::$errCode = "0021";
			self::$errMsg = "订单无运输方式不能申请打印！";
			return false;
		}
		if($orderData['channelId'] == ''){
			self::$errCode = "0021";
			self::$errMsg = "订单无渠道不能申请打印！";
			return false;
		}
		if($orderData['transportId'] == 6 && empty($orderTrackInfoData)){
			self::$errCode = "0021";
			self::$errMsg = "EUB订单号无跟踪号拒绝申请打印！";
			return false;
		}
		
		$sql = "SELECT * FROM ".self::$table_express_remark." WHERE omOrderId = '{$orderid}' ";
		$query	= self::$dbConn->query($sql);
		$orderExpressRemark = self::$dbConn->fetch_array($query);
		
		if(in_array($orderData['transportId'], array(5,8,9)) && empty($orderExpressRemark)){
			self::$errCode = "0025";
			self::$errMsg = "EMS\FEDEX\DHL无快递描述不允许推送到仓库系统！";
			return false;
		}
		
		$sql = "SELECT * FROM ".self::$table_notes." WHERE omOrderId = '{$orderid}' AND is_delete = 0 ";
		$query	= self::$dbConn->query($sql);
		$orderNotesInfoData = self::$dbConn->fetch_array_all($query);
		
		$sql = "SELECT * FROM ".self::$table_detail." WHERE omOrderId IN (".join(',', $ids).") AND is_delete = 0 AND storeId = {$storeId} ";
		$query	= self::$dbConn->query($sql);
		$orderDetailData = self::$dbConn->fetch_array_all($query);
		
		$detailids = array();
		foreach($orderDetailData as $dv){
			$detailids[] = $dv['id'];	
		}
		if($detailids){
			$sql = "SELECT * FROM ".self::$table_detail_extension.$extension." WHERE omOrderdetailId IN (".join(',', $detailids).") ";
			$query	= self::$dbConn->query($sql);
			$orderDetailExtenData = self::$dbConn->fetch_array_all($query);
		}else{
			$orderDetailExtenData = array();	
		}
		
		$obj_order_detail_data = array();	 
		foreach($orderDetailData as $detailValue){
			$obj_order_detail_data[] = array('orderDetailData' => $detailValue,			
											 'orderDetailExtenData' => $orderDetailExtenData);	
		}
		
		$orderDataInfo = array('orderData' => $orderData,
							   'orderExtenData' => $orderExtenData,
							   'orderUserInfoData' => $orderUserInfoData,
							   'orderDetail' => $obj_order_detail_data,
							   'tracknumbers' => $orderTrackInfoData,
							   'notes' => $orderNotesInfoData,
							   'flag'  => $flag,//1发货单，0配货单
							  );
		$exchange='send_order_exchange';
		if($rabbitMQClass->queue_publish($exchange,$orderDataInfo)){
			$sql = "UPDATE ".self::$table_order." SET orderStatus = ".C("STATESHIPPED").", orderType = ".C("STATESHIPPED_PRINTPEND")." WHERE id IN (".join(',', $ids).") AND is_delete = 0 AND storeId = {$storeId} ";
			if(!self::$dbConn->query($sql)){
				self::$errCode = "0023";
				self::$errMsg = "订单号申请打印之后，更新状态失败！";
				return false;	
			}
			foreach($ids as $id){
				MarkShippingModel::insert_mark_shipping($id);
			}
		}
		self::$errCode = "200";
		self::$errMsg = "订单号：{$omid} 申请打印成功！";
		return true;
	}
	
	
	/**
	 * OrderPushModel::limitApplyPrintInfo($orderid)
	 * 推送信息给仓库系统
	 * @return  array
	 */
    public static function limitApplyPrintInfo($orderid){
		self::initDB();
		$storeId = 1;
		
		//基础信息
		$sql = "SELECT * FROM ".self::$table_order." WHERE id = '{$orderid}' AND is_delete = 0 AND storeId = {$storeId} ";
		$query	= self::$dbConn->query($sql);
		$orderData = self::$dbConn->fetch_array($query);
		
		if($orderData['transportId'] == ''){
			//echo $sql;
			//var_dump($orderData);
			self::$errCode = "0021";
			self::$errMsg = "订单无运输方式不能申请打印！";
			return false;
		}
		$platfrom = omAccountModel::getPlatformSuffixById($orderData['platformId']);
		$extension = $platfrom['suffix'];//获取后缀名称
		
		$ids[] = $orderid;
		if($orderData['combinePackage']==2){
			self::$errCode	= "1041";
			self::$errMsg	= "此订单为合并包裹子订单，不能单独申请打印！";
			return false;
		}else if ($orderData['combinePackage']==1) { //如果是合并包裹
			$sql = "SELECT split_order_id FROM ".self::$table_package." WHERE main_order_id = '{$orderid}' AND is_enable = 1";
			$query	= self::$dbConn->query($sql);
			$affectedrows = self::$dbConn->affected_rows();           
			if ($affectedrows) {
				$result	= self::$dbConn->fetch_array_all($query);
				for($i=0; $i < count($result); $i++){
					$ids[] = $result[$i]['split_order_id'];
				}
			}
		}
		
		/*$actualTotal = 0;
		$actualShipping = 0;
		$calcWeight = 0;
		$calcShipping = 0;
		if(count($ids) > 1){
			foreach($ids as $vid){
				$sql = "SELECT * FROM ".self::$table_order." WHERE id = '{$vid}' ";
				$query	= self::$dbConn->query($sql);
				$orderOneInfo = self::$dbConn->fetch_array($query);
				$actualTotal += $orderOneInfo['actualTotal'];
				$actualShipping += $orderOneInfo['actualShipping'];
				$calcWeight += $orderOneInfo['calcWeight'];
				$calcShipping += $orderOneInfo['calcShipping'];
			}
			$orderData['actualTotal'] = $actualTotal;
			$orderData['actualShipping'] = $actualShipping;
			$orderData['calcWeight'] = $calcWeight;
			$orderData['calcShipping'] = $calcShipping;
		}*/
		/*$sql = "SELECT * FROM ".self::$table_order_extension.$extension." WHERE omOrderId = '{$orderid}' ";
		$query	= self::$dbConn->query($sql);
		$orderExtenData = self::$dbConn->fetch_array($query);
		
		$sql = "SELECT * FROM ".self::$table_userinfo." WHERE omOrderId = '{$orderid}' ";
		$query	= self::$dbConn->query($sql);
		$orderUserInfoData = self::$dbConn->fetch_array($query);*/
		
		$sql = "SELECT * FROM ".self::$table_tracknumber." WHERE omOrderId = '{$orderid}' AND is_delete = 0 ";
		$query	= self::$dbConn->query($sql);
		$orderTrackInfoData = self::$dbConn->fetch_array($query);
		
		if($orderData['transportId'] == 6 && empty($orderTrackInfoData)){
			self::$errCode = "0021";
			self::$errMsg = "EUB订单号无跟踪号拒绝申请打印！";
			return false;
		}
		
		$sql = "SELECT * FROM ".self::$table_express_remark." WHERE omOrderId = '{$orderid}' ";
		$query	= self::$dbConn->query($sql);
		$orderExpressRemark = self::$dbConn->fetch_array($query);
		
		if(in_array($orderData['transportId'], array(5,8,9)) && empty($orderExpressRemark)){
			self::$errCode = "0025";
			self::$errMsg = "EMS\FEDEX\DHL无快递描述不允许推送到仓库系统！";
			return false;
		}
		
		/*$sql = "SELECT * FROM ".self::$table_notes." WHERE omOrderId = '{$orderid}' AND is_delete = 0 ";
		$query	= self::$dbConn->query($sql);
		$orderNotesInfoData = self::$dbConn->fetch_array_all($query);
		
		$sql = "SELECT * FROM ".self::$table_detail." WHERE omOrderId IN (".join(',', $ids).") AND is_delete = 0 AND storeId = {$storeId} ";
		$query	= self::$dbConn->query($sql);
		$orderDetailData = self::$dbConn->fetch_array_all($query);
		
		$detailids = array();
		foreach($orderDetailData as $dv){
			$detailids[] = $dv['id'];	
		}
		if($detailids){
			$sql = "SELECT * FROM ".self::$table_detail_extension.$extension." WHERE omOrderdetailId IN (".join(',', $detailids).") ";
			$query	= self::$dbConn->query($sql);
			$orderDetailExtenData = self::$dbConn->fetch_array_all($query);
		}else{
			$orderDetailExtenData = array();	
		}
		
		$obj_order_detail_data = array();	 
		foreach($orderDetailData as $detailValue){
			$obj_order_detail_data[] = array('orderDetailData' => $detailValue,			
											 'orderDetailExtenData' => $orderDetailExtenData);	
		}
		
		$orderDataInfo = array('orderData' => $orderData,
							   'orderExtenData' => $orderExtenData,
							   'orderUserInfoData' => $orderUserInfoData,
							   'orderDetail' => $obj_order_detail_data,
							   'tracknumbers' => $orderTrackInfoData,
							   'notes' => $orderNotesInfoData,
							   'flag'  => $flag,//1发货单，0配货单
							  );
		$exchange='send_order_exchange';
		if($rabbitMQClass->queue_publish($exchange,$orderDataInfo)){
			$sql = "UPDATE ".self::$table_order." SET orderStatus = ".C("STATESHIPPED").", orderType = ".C("STATESHIPPED_PRINTPEND")." WHERE id IN (".join(',', $ids).") AND is_delete = 0 AND storeId = {$storeId} ";
			if(!self::$dbConn->query($sql)){
				self::$errCode = "0023";
				self::$errMsg = "订单号申请打印之后，更新状态失败！";
				return false;	
			}
			foreach($ids as $id){
				MarkShippingModel::insert_mark_shipping($id);
			}
		}*/
		$where = " WHERE id = ".$orderid;
		$returnStatus0 = array('orderStatus'=>C("STATESHIPPED"),'orderType'=>C('STATESHIPPED_APPLYPRINT'));
		if(OrderindexModel::updateOrder(self::$table_order,$returnStatus0,$where)){
			self::$errCode	= "200";
			self::$errMsg	= "申请打印成功！";
			return true;
		}else{
			self::$errCode	= "002";
			self::$errMsg	= "申请打印失败！";
			return false;	
		}
	}
	
	/**
	 * OrderPushModel::listPushMessage()
	 * 推送信息给仓库系统
	 * @return  array
	 */
    public static function listPushOneMessage($orderDataInfo,$flag=1,$exchange='send_order_exchange'){
		self::initDB();
		
		$orderDataInfo['flag'] = $flag; //1发货单，0配货单
		if(!$rabbitMQClass->queue_publish($exchange,$orderDataInfo)){
			/*$sql = "UPDATE ".self::$table_order." SET orderStatus = ".C("STATESHIPPED").", orderType = ".C("STATESHIPPED_PRINTPEND")." WHERE id = {$orderDataInfo['orderData']['id']} ";
			if(!self::$dbConn->query($sql)){*/
				self::$errCode = "0023";
				self::$errMsg = "订单号推送失败！";
				return false;
			//}
		}
		MarkShippingModel::insert_mark_shipping($orderDataInfo['orderData']['id']);
		self::$errCode = "200";
		self::$errMsg = "订单号：{$omid} 申请打印成功！";
		return true;
	}
	
	public static function getOrderinfo($ostatus, $otype, $flag, $storeId = 1){
		self::initDB();
		if(!isset($_SESSION['sysUserId'])){
			self::$errCode	= "400";
			self::$errMsg	= "登陆过期！";
			return false;
		}
		if($ostatus){
			$where .= ' and orderStatus = '.$ostatus;		
		}
		if($otype){
			$where .= ' and orderType = '.$otype;
		}
		$accountList = $_SESSION['accountList'];
		$platformList = $_SESSION['platformList'];
		//echo "<pre>"; print_r($accountList); exit;
		$platformsee = array();
		for($i=0;$i<count($platformList);$i++){
			$platformsee[]	= "platformId='".$platformList[$i]."'";
		}
		if($platformsee){
			$where .= ' AND ('.join(" or ", $platformsee).') ';
		}
		$accountsee = array();
		for($i=0;$i<count($accountList);$i++){
			$accountsee[]	= "accountId='".$accountList[$i]."'";
		}
		if($accountsee){
			$where .= ' AND ('.join(" or ", $accountsee).') ';
		}
		//基础信息
		$sql = "SELECT id FROM ".self::$table_order." WHERE is_delete = 0 AND storeId = {$storeId} {$where}";
		//echo $sql; echo "<br>";
		$query	= self::$dbConn->query($sql);
		$orderids = self::$dbConn->fetch_array_all($query);
		foreach($orderids as $value){
			//echo $value['id']."---".$flag; echo "<br>";
			self::listPushMessage($value['id'],$flag);
		}
		self::$errCode	= "200";
		self::$errMsg	= "整个文件夹申请打印成功！";
		return true;
	}
	
	//申请全部文件夹打印
	public static function applyAllPrint($ostatus, $otype, $flag, $storeId = 1){
		self::initDB();
		if(!isset($_SESSION['sysUserId'])){
			self::$errCode	= "400";
			self::$errMsg	= "登陆过期！";
			return false;
		}
		$tableName = "om_unshipped_order";
		$where = " WHERE is_delete = 0 AND storeId = {$storeId} ";
		if($ostatus){
			$where .= ' and orderStatus = '.$ostatus;
		}
		if($otype){
			$where .= ' and orderType = '.$otype;
		}
		$accountList = $_SESSION['accountList'];
		$platformList = $_SESSION['platformList'];
		//echo "<pre>"; print_r($accountList); exit;
		$platformsee = array();
		for($i=0;$i<count($platformList);$i++){
			$platformsee[] = $platformList[$i];
		}
		if($platformsee){
			$where .= ' AND platformId IN ('.join(",", $platformsee).') ';
		}else{
			self::$errCode	= "500";
			self::$errMsg	= "无申请权限！";
			return false;
		}
		$accountsee = array();
		for($i=0;$i<count($accountList);$i++){
			$accountsee[] = $accountList[$i];
		}
		if($accountsee){
			$where .= ' AND accountId IN ('.join(",", $accountsee).') ';
		}else{
			self::$errCode	= "600";
			self::$errMsg	= "无申请权限！";
			return false;
		}
		//基础信息
		/*$sql = "SELECT id,platformId FROM ".self::$table_order.$where;
		//echo $sql; echo "<br>";
		$query	= self::$dbConn->query($sql);
		$orderids = self::$dbConn->fetch_array_all($query);
		foreach($orderids as $value){
			$orderid = $value['id'];
			$platformId = $value['id'];
		}*/
		//$where = " WHERE id in (".join(',',$orderids).") AND orderStatus = ".C('STATESHIPPED');
		$returnStatus0 = array('orderStatus'=>C("STATESHIPPED"),'orderType'=>C('STATESHIPPED_APPLYPRINT'));
		if(OrderindexModel::updateOrder($tableName,$returnStatus0,$where)){
			self::$errCode	= "200";
			self::$errMsg	= "整个文件夹申请打印成功！";
			return true;
		}else{
			self::$errCode	= "002";
			self::$errMsg	= "整个文件夹申请打印失败！";
			return false;	
		}
		
		/*foreach($orderids as $value){
			//echo $value['id']."---".$flag; echo "<br>";
			self::listPushMessage($value['id'],$flag);
		}*/
	}
	
	//申请全部文件夹打印
	public static function applyPartPrint($orderid_arr,$ostatus, $otype, $flag, $storeId = 1){
		self::initDB();
		if(!isset($_SESSION['sysUserId'])){
			self::$errCode	= "400";
			self::$errMsg	= "登陆过期！";
			return false;
		}
		$tableName = "om_unshipped_order";
		//var_dump($orderid_arr); exit;
		if(!$orderid_arr){
			self::$errCode	= "300";
			self::$errMsg	= "传值失败！";
			return false;
		}
		$where = " WHERE is_delete = 0 AND storeId = {$storeId} ";
		if($ostatus){
			$where .= ' and orderStatus = '.$ostatus;
		}
		if($otype){
			$where .= ' and orderType = '.$otype;
		}
		$accountList = $_SESSION['accountList'];
		$platformList = $_SESSION['platformList'];
		//echo "<pre>"; print_r($accountList); exit;
		$platformsee = array();
		for($i=0;$i<count($platformList);$i++){
			$platformsee[]	= $platformList[$i];
		}
		if($platformsee){
			$where .= ' AND platformId IN ('.join(",", $platformsee).') ';
		}else{
			self::$errCode	= "500";
			self::$errMsg	= "无申请权限！";
			return false;
		}
		$accountsee = array();
		for($i=0;$i<count($accountList);$i++){
			$accountsee[] = $accountList[$i];
		}
		if($accountsee){
			$where .= ' AND accountId IN ('.join(",", $accountsee).') ';
		}else{
			self::$errCode	= "600";
			self::$errMsg	= "无申请权限！";
			return false;
		}
		$where .= ' AND id in ('.join(',', $orderid_arr).') ';
		//基础信息
		/*$sql = "SELECT id FROM ".self::$table_order.$where;
		//echo $sql; echo "<br>";
		$query	= self::$dbConn->query($sql);
		$orderids = self::$dbConn->fetch_array_all($query);
		
		foreach($orderids as $value){
			$orderid = $value['id'];
			self::limitApplyPrintInfo($orderid);
		}*/
		
		//$where = " WHERE id in (".join(',',$orderids).") AND orderStatus = ".C('STATESHIPPED');
		$returnStatus0 = array('orderStatus'=>C("STATESHIPPED"),'orderType'=>C('STATESHIPPED_APPLYPRINT'));
		if(OrderindexModel::updateOrder($tableName,$returnStatus0,$where)){
			self::$errCode	= "200";
			self::$errMsg	= "申请打印成功！";
			return true;
		}else{
			self::$errCode	= "002";
			self::$errMsg	= "申请打印失败！";
			return false;	
		}
		
		/*foreach($orderids as $value){
			//echo $value['id']."---".$flag; echo "<br>";
			self::listPushMessage($value['id'],$flag);
		}*/
	}
	
}
?>