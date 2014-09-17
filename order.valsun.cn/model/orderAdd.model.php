<?php
/*
*用户信息
*ADD BY hws
*/
class OrderAddModel{
	public 	static $dbConn;
	public	static $errCode	=	0;
	public	static $errMsg	=	"";
	static  $table			=	"om_buyerinfo";
	
	
	//db初始化
	public 	function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
		mysql_query('SET NAMES UTF8');
	}
	
	//获取客户信息列表
	public 	static function getBuyerInfo($select,$where){
		self::initDB();
		$sql	= "select {$select} from ".self::$table." {$where} ";
		//echo $sql;
		$query	= self::$dbConn->query($sql);		
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);
			return $ret;	//成功， 返回列表数据
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"error";
			return false;	
		}
	}
	
	/**
	 * 更新一条或多条记录，暂只支持一维数组
	 * @para $data as array
	 $ @where as String
	 */
	public static function update($data,$where = ""){
		self::initDB();
		$field = "";
		if(!is_array($field)){
			foreach($data as $k => $v){
				$field .= ",`".$k."` = '".$v."'";
			}
			$field	= ltrim($field,",");
			$sql	= "UPDATE `".self::$table."` SET ".$field." WHERE 1 ".$where;
			$query	=	self::$dbConn->query($sql);
			if($query){                             
				return true;
			} else {			
				return false;
			}
		}
		else {
			return false;
		}
	}

	/**
	 * 插入一条记录
	 * @para $data as array
	 * return insert_id
	 */
	public static function insertRow($data){
		self::initDB();
        $string = array2sql_extral($data);
		$sql = "INSERT INTO `".self::$table."` SET ".$string;
		$query	=	self::$dbConn->query($sql);
		if($query){
			$insertId = self::$dbConn->insert_id();
			return $insertId;
		}else{
			self::$errCode	=	"003";
			self::$errMsg	=	"error";
			return false;
		}
	}
	
	/**
	 * 先验证再插入一条用户记录记录
	 * @para $data as array
	 * return insert_id
	 */
	public static function insertBuyerInfoRow($data){
		self::initDB();
		$platformId = mysql_real_escape_string($data['platformId']);
		$username = mysql_real_escape_string($data['username']);
		$platformUsername = mysql_real_escape_string($data['platformUsername']);
		$where = " WHERE platformId = '".$platformId."' AND username = '".$username."' AND platformUsername = '".$platformUsername."' ";
		$ret = self::getBuyerInfo('*', $where);
		//var_dump($ret);
		if(!$ret){
			$string = array2sql_extral($data);
			$sql = "INSERT INTO `".self::$table."` SET ".$string;
			//echo $sql;
			$query	=	self::$dbConn->query($sql);
			if($query){
				$insertId = self::$dbConn->insert_id();
				self::$errCode	=	"200";
				self::$errMsg	=	"success";
				return $insertId;
			}else{
				self::$errCode	=	"003";
				self::$errMsg	=	"error";
				return false;
			}
		}else{
			self::$errCode	=	"500";
			self::$errMsg	=	"已经存在";
			return true;
		}
	}
	
	/**
	 * 插入一条订单记录
	 * @para $data as array
	 * return insert_id
	 */
	public static function insertOrderRow($data, $tName='om_unshipped_order'){
		self::initDB();
        $string = array2sql_extral($data);
		$sql = "INSERT INTO `{$tName}` SET ".$string;		
		$query	=	self::$dbConn->query($sql);
		if($query){
			if($tName=='om_shipped_order'){
				return true;
			}
			$insertId = self::$dbConn->insert_id();
			return $insertId;
		}else{
			self::$errCode	=	"003";
			self::$errMsg	=	"error";
			return false;
		}
	}
	
	/**
	 * 插入多条订单详情
	 * @para $data as string
	 * return insert_id
	 */
	public static function insertOrderdetail($data,$tName='om_unshipped_order'){
		self::initDB();
        $string = array2sql_extral($data);
		$sql = "INSERT INTO `{$tName}_detail` SET ".$string;
		//echo $sql; //exit;
		$query	=	self::$dbConn->query($sql);
		if($query){
			$orderid = $data['omOrderId'];
			//CommonModel::orderLog($orderid,$sql,"插入订单明细");
			if($tName=='om_shipped_order'){
				return true;
			}
			$insertId = self::$dbConn->insert_id();
			return $insertId;
		}else{
			self::$errCode	=	"003";
			self::$errMsg	=	"error";
			return false;
		}
	}
	
	/**
	 * 插入平台扩展详情
	 * @para $data as array
	 * return true
	 */
	public static function insertExtension($data, $extension = 'ebay', $tName='om_unshipped_order'){
		self::initDB();
        $string = array2sql_extral($data);
		$sql = "INSERT INTO `{$tName}_extension_".$extension."` SET ".$string;
		//echo $sql;
		$query	=	self::$dbConn->query($sql);
		if($query){
			return true;
		}else{
			self::$errCode	=	"003";
			self::$errMsg	=	"error";
			return false;
		}
	}
	
	/**
	 * 订单明细平台扩展表添加共用方法
	 * @para $data as array
	 * return true
	 */
	public static function insertDetailExtension($data, $extension = 'ebay', $tName='om_unshipped_order'){
		self::initDB();
        $string = array2sql_extral($data);
		$sql = "INSERT INTO `{$tName}_detail_extension_".$extension."` SET ".$string;
		//echo $sql; echo "\n";
		$query	=	self::$dbConn->query($sql);
		if($query){
			return true;
		}else{
			self::$errCode	=	"003";
			self::$errMsg	=	"error";
			return false;
		}
	}
	
	/**
	 * 订单明细平台明细表更新共用方法
	 * @para $data as array
	 * return true
	 */
	public static function updateDetail($data, $where){
		self::initDB();
        $string = array2sql_extral($data);
		$sql = "UPDATE `om_unshipped_order_detail` SET ".$string." ".$where;
		//echo $sql; echo "\n";
		$query	=	self::$dbConn->query($sql);
		if($query){
			return true;
		}else{
			self::$errCode	=	"003";
			self::$errMsg	=	"error";
			return false;
		}
	}
	
	/**
	 * 插入订单用户信息列表
	 * @para $data as array
	 * return true
	 */
	public static function insertUserinfoRow($data,$tName='om_unshipped_order'){
		self::initDB();
        $string = array2sql_extral($data);
		$sql = "INSERT INTO `{$tName}_userInfo` SET ".$string;
		$query	=	self::$dbConn->query($sql);
		if($query){
			return true;
		}else{
			self::$errCode	=	"003";
			self::$errMsg	=	"error";
			return false;
		}
	}
	
	/**
	 * 插入仓库信息列表
	 * @para $data as array
	 * return true
	 */
	public static function insertWhInfoRow($data,$tName='om_unshipped_order'){
		self::initDB();
        $string = array2sql_extral($data);
		$sql = "INSERT INTO `{$tName}_warehouse` SET ".$string;
		$query	=	self::$dbConn->query($sql);
		if($query){
			return true;
		}else{
			self::$errCode	=	"003";
			self::$errMsg	=	"error";
			return false;
		}
	}
	
	/**
	 * 插入订单对应仓库处理信息列表
	 * @para $data as array
	 * return true
	 */
	public static function insertWarehouseRow($data){
		self::initDB();
        $string = array2sql_extral($data);
		$sql = "INSERT INTO `om_unshipped_order_warehouse` SET ".$string;
		$query	=	self::$dbConn->query($sql);
		if($query){
			return true;
		}else{
			self::$errCode	=	"003";
			self::$errMsg	=	"error";
			return false;
		}
	}
	
	/**
	 * 插入订单备注信息
	 * @para $data as array
	 * return true
	 */
	public static function insertOrderNotesRow($data){
		self::initDB();
        $string = array2sql_extral($data);
		$sql = "INSERT INTO `om_order_notes` SET ".$string;
		$query	=	self::$dbConn->query($sql);
		if($query){
			$orderid = $data['omOrderId'];
			OrderLogModel::orderLog($orderid,$sql,"添加备注");
			return true;
		}else{
			self::$errCode	=	"003";
			self::$errMsg	=	"error";
			return false;
		}
	}
	
	/**
	 * 插入跟踪号信息
	 * @para $data as array
	 * return true
	 */
	public static function insertOrderTrackRow($data){
		self::initDB();
        $string = array2sql_extral($data);
		$sql = "INSERT INTO `om_order_tracknumber` SET ".$string;		
		$query	=	self::$dbConn->query($sql);
		if($query){
			$orderid = $data['omOrderId'];
			OrderLogModel::orderLog($orderid,$sql,"插入跟踪号");
			return true;
		}else{
			self::$errCode	=	"003";
			self::$errMsg	=	"error";
			return false;
		}
	}
	
	/**
	 * 插入订单对应下的总表信息
	 * @para $data as array
	 * @add by Herman.Xi
	 * @last modified 20131022
	 * $in 默认为进入订单模式
	 */
	public static function insertAllOrderRow($orderData, $extension = 'ebay', $in=true){
		self::initDB();
		//var_dump($orderData); exit;
		$obj_order_data = $orderData['orderData'];
		$orderExtenData = $orderData['orderExtenData'];
		$orderUserInfoData = $orderData['orderUserInfoData'];
		$orderDetailArr = $orderData['orderDetail'];

		if($obj_order_data['platformId'] == "2"){	//ebay
			$ebay_orderid	=	$orderExtenData['orderId'];
			$tName = 'om_unshipped_order_extension_ebay';
			$where = "WHERE orderId='$ebay_orderid'";
			$flagCountUnshipped = OmAvailableModel :: getTNameCount($tName, $where);
			$tName = 'om_shipped_order_extension_ebay';
			$flagCountshipped = OmAvailableModel :: getTNameCount($tName, $where);
		}else{
			$tName = 'om_unshipped_order';
			$where = "WHERE accountId='{$obj_order_data['accountId']}' AND recordNumber='{$obj_order_data['recordNumber']}' AND platformId={$obj_order_data['platformId']} and is_delete ='0'";
			$flagCountUnshipped = OmAvailableModel :: getTNameCount($tName, $where);
			$tName = 'om_shipped_order';
			$flagCountshipped = OmAvailableModel :: getTNameCount($tName, $where);
		}	
		//if (empty ($flagCountUnshipped) && empty ($flagCountshipped)) { //判断订单是否已经在系统2个订单表（未发货和已发货）中存在
		
		if(empty ($flagCountUnshipped) && empty ($flagCountshipped)){ 
			$platfrom = omAccountModel::getPlatformSuffixById($obj_order_data['platformId']);
			$extension = $platfrom['suffix'];//获取后缀名称
			BaseModel :: begin(); //开始事务
			$insertOrderDataRow = self :: insertOrderRow($obj_order_data); //插入到order表
			if (empty ($insertOrderDataRow)) {
				BaseModel :: rollback();
				//throw new Exception('insert orderData error');
			}
			$orderExtenData['omOrderId'] = $insertOrderDataRow;
			$orderUserInfoData['omOrderId'] = $insertOrderDataRow;
			$resultExten = self :: insertExtension($orderExtenData, $extension); //插入到order_extend表
			if (!$resultExten) {
				BaseModel :: rollback();
				//throw new Exception('insert orderExtenData error');
			}
			$resultUserInfo = self :: insertUserinfoRow($orderUserInfoData); //插入到order_userInfo表
			if (!$resultUserInfo) {
				BaseModel :: rollback();
				//throw new Exception('insert orderUserInfoData error');
			}
			
			foreach($orderDetailArr as $orderDetail){
				$orderDetailData = $orderDetail['orderDetailData'];
				$orderDetailExtenData = $orderDetail['orderDetailExtenData'];
				$orderDetailData['omOrderId'] = $insertOrderDataRow;
				$orderDetailData['createdTime'] = time();
				$insertOrderDatilRow = self :: insertOrderdetail($orderDetailData); //插入到detail表
				if (empty ($insertOrderDatilRow)) {
					BaseModel :: rollback();
					//throw new Exception('insert orderDetailData error');
				}
				$orderDetailExtenData['omOrderdetailId'] = $insertOrderDatilRow;
				if($orderDetailExtenData){
					$resultOrderDetailExten = self :: insertDetailExtension($orderDetailExtenData, $extension); //插入到detailExtend
					if (!$resultOrderDetailExten) {
						BaseModel :: rollback();
						//throw new Exception('insert orderDetailExtenData error');
					}
				}
			}
			if($obj_order_data['platformId'] == 1){
				$insertOrderidsDada = array('omOrderId'=>$insertOrderDataRow,'PayPalPaymentId'=>$orderData['orderExtenData']['PayPalPaymentId'],'orderid'=>$orderData['orderExtenData']['orderId'],'accountId'=>$orderData['orderData']['accountId'],'saletime'=>time());
				$insertOrderids = OrderidsModel::insertOrderidsList($insertOrderidsDada);
				if (!$insertOrderids) {
					BaseModel :: rollback();
					//throw new Exception('insert orderIds error');
				}
			}
			if(isset($orderData['orderNote']) && !empty($orderData['orderNote'])){
				$orderNote = $orderData['orderNote'];
				$insertOrderNoteDada = array('omOrderId'=>$insertOrderDataRow,'content'=>$orderNote['content'],'userId'=>$orderNote['userId'],'createdTime'=>$orderNote['createdTime']);
				$insertOrderNoteids = OrderAddModel::insertOrderNotesRow($insertOrderNoteDada);
				if (!$insertOrderNoteids) {
					BaseModel :: rollback();
					//throw new Exception('insert orderNote error');
				}
			}
			$buyerInfo = $orderUserInfoData;
			$buyerInfo['platformId'] = $obj_order_data['platformId'];
			unset($buyerInfo['omOrderId']);
			unset($buyerInfo['countrySn']);
			unset($buyerInfo['currency']);
			unset($buyerInfo['currency']);
			$insertBuyerInfo = self::insertBuyerInfoRow($buyerInfo);
			if (!$insertBuyerInfo) {
				BaseModel :: rollback();
				//throw new Exception('insert BuyerInfo error');
			}
			$ProductStatus = new ProductStatus();
			if(!$ProductStatus->updateSkuStatusByOrderStatus(array($insertOrderDataRow))){
				BaseModel :: rollback();
				//throw new Exception('update puchaseinfo error');
			}
			BaseModel :: commit();
			BaseModel :: autoCommit();
			return $insertOrderDataRow;
		}else{
			self::$errCode	=	"400";
			self::$errMsg	=	"已经包含订单信息，不能重复插入！";
			return false;	
		}
	}
	
	/**
	 * 插入订单对应下的总表信息,没有批量处理事件
	 * @para $data as array
	 * @add by Herman.Xi
	 * @last modified 20131022
	 * $in 默认为进入订单模式
	 */
	public static function insertAllOrderRowNoEvent($orderData,$tName = 'om_unshipped_order',$in=true){
		self::initDB();
		//var_dump($orderData); exit;
		$obj_order_data = $orderData['orderData'];
		$orderExtenData = $orderData['orderExtenData'];
		$orderUserInfoData = $orderData['orderUserInfoData'];
		$orderDetailArr = $orderData['orderDetail'];
		$orderTrackInfo = $orderData['orderTrack'];
		//var_dump($obj_order_data); exit;
		//$tName = 'om_unshipped_order';
		$where = "WHERE recordNumber='{$obj_order_data['recordNumber']}' AND platformId={$obj_order_data['platformId']}";
		$flagCountUnshipped = OmAvailableModel :: getTNameCount($tName, $where);
		//var_dump($flagCountUnshipped); exit;
		$_tName = 'om_shipped_order';
		$flagCountshipped = OmAvailableModel :: getTNameCount($_tName, $where);
		if ($obj_order_data && empty ($flagCountshipped)) { //判断订单是否已经在系统2个订单表（未发货和已发货）中存在

			$platfrom = omAccountModel::getPlatformSuffixById($obj_order_data['platformId']);
			$extension = $platfrom['suffix'];//获取后缀名称
			//echo $extension; echo "<br>"; exit;	
			$insertOrderDataRow = self :: insertOrderRow($obj_order_data,$tName); //插入到order表
			if (empty ($insertOrderDataRow)) {
				self::$errCode	=	"020";
				self::$errMsg	=	"插入订单失败！";
				return false;
			}
			$orderExtenData['omOrderId'] = $insertOrderDataRow;
			$orderUserInfoData['omOrderId'] = $insertOrderDataRow;
			$resultExten = self :: insertExtension($orderExtenData, $extension, $tName); //插入到order_extend表
			if (!$resultExten) {
				self::$errCode	=	"021";
				self::$errMsg	=	"插入订单附加表失败！";
				return false;
			}
			$resultUserInfo = self :: insertUserinfoRow($orderUserInfoData,$tName); //插入到order_userInfo表
			if (!$resultUserInfo) {
				self::$errCode	=	"022";
				self::$errMsg	=	"插入用户信息表失败！";
				return false;
			}
			
			foreach($orderDetailArr as $orderDetail){
				$orderDetailData = $orderDetail['orderDetailData'];
				$orderDetailExtenData = $orderDetail['orderDetailExtenData'];
				$orderDetailData['omOrderId'] = $insertOrderDataRow;
				$orderDetailData['createdTime'] = time();
				$insertOrderDatilRow = self :: insertOrderdetail($orderDetailData,$tName); //插入到detail表
				if (empty ($insertOrderDatilRow)) {
					self::$errCode	=	"023";
					self::$errMsg	=	"插入订明细单失败！";
					return false;
				}
				$orderDetailExtenData['omOrderdetailId'] = $insertOrderDatilRow;
				if($orderDetailExtenData){
				$resultOrderDetailExten = self :: insertDetailExtension($orderDetailExtenData, $extension, $tName); //插入到detailExtend
				if (!$resultOrderDetailExten) {
					self::$errCode	=	"024";
					self::$errMsg	=	"插入订单明细附带表失败！";
					return false;
				}
				}
			}
			if($obj_order_data['platformId'] == 1 && $in){
				$insertOrderidsDada = array('omOrderId'=>$insertOrderDataRow,'PayPalPaymentId'=>$orderData['orderExtenData']['PayPalPaymentId'],'orderid'=>$orderData['orderExtenData']['orderId'],'accountId'=>$orderData['orderData']['accountId'],'saletime'=>time());
				$insertOrderids = OrderidsModel::insertOrderidsList($insertOrderidsDada);
				if (!$insertOrderids) {
					self::$errCode	=	"025";
					self::$errMsg	=	"插入ebay订单IDS失败！";
					return false;
				}
			}
			if(isset($orderData['orderNote']) && !empty($orderData['orderNote'])){
				$orderNote = $orderData['orderNote'];
				$insertOrderNoteDada = array('omOrderId'=>$insertOrderDataRow,'content'=>$orderNote['content'],'userId'=>$orderNote['userId'],'createdTime'=>$orderNote['createdTime']);
				$insertOrderNoteids = self::insertOrderNotesRow($insertOrderNoteDada);
				if (!$insertOrderNoteids) {
					self::$errCode	=	"026";
					self::$errMsg	=	"插入订单Note失败！";
					return false;
				}
			}
			$buyerInfo = $orderUserInfoData;
			$buyerInfo['platformId'] = $obj_order_data['platformId'];
			unset($buyerInfo['omOrderId']);
			unset($buyerInfo['countrySn']);
			unset($buyerInfo['currency']);
			unset($buyerInfo['currency']);
			//var_dump($buyerInfo);
			$insertBuyerInfo = self::insertBuyerInfoRow($buyerInfo);
			//var_dump($insertBuyerInfo);
			if (!$insertBuyerInfo) {
				return false;
			}

			//判断为新插入订单时，需要插入跟踪号
			if ( ($orderTrackInfo['tracknumber'] != '') && $in) {			
				$orderTrackInfo['omOrderId'] = $insertOrderDataRow;			
				$insertTrack = self::insertOrderTrackRow($orderTrackInfo);
				if (!$insertTrack) {
					return false;
				}
			}

			$ProductStatus = new ProductStatus();			
			if(!$ProductStatus->updateSkuStatusByOrderStatus(array($insertOrderDataRow))){
				self::$errCode	=	"400";
				self::$errMsg	=	"已经包含订单信息，不能重复插入！";
				return false;
			}
			//var_dump($insertOrderDataRow); exit;
			return $insertOrderDataRow;
		}else{
			self::$errCode	=	"400";
			self::$errMsg	=	"已经包含订单信息，不能重复插入！";
			return false;
		}
	}
	
	/**
	 * 转移订单对应下的总表信息,没有批量处理事件
	 * @para $data as array
	 * @add by Herman.Xi
	 * @last modified 20131022
	 * $in 默认为进入订单模式
	 */
	public static function shiftAllOrderRowNoEvent($orderData, $tName = 'om_shipped_order'){
		self::initDB();
		//var_dump($orderData); exit;
		$obj_order_data = $orderData['orderData'];
		$orderExtenData = $orderData['orderExtenData'];
		$orderUserInfoData = $orderData['orderUserInfoData'];
		$orderDetailArr = $orderData['orderDetail'];
		$orderWhInfoData = $orderData['orderWhInfoData'];
		//var_dump($obj_order_data); exit;
		//$tName = 'om_unshipped_order';
		$where = "WHERE recordNumber='{$obj_order_data['recordNumber']}' AND platformId={$obj_order_data['platformId']}";
		$flagCountUnshipped = OmAvailableModel :: getTNameCount($tName, $where);
		//var_dump($flagCountUnshipped); exit;
		/*$tName = 'om_shipped_order';
		$flagCountshipped = OmAvailableModel :: getTNameCount($tName, $where);*/
		if ($obj_order_data /*&& empty ($flagCountshipped)*/) { //判断订单是否已经在系统2个订单表（未发货和已发货）中存在
		
			$platfrom = omAccountModel::getPlatformSuffixById($obj_order_data['platformId']);
			$extension = $platfrom['suffix'];//获取后缀名称
			//echo $extension; echo "<br>"; exit;	
			$insertOrderDataRow = self :: insertOrderRow($obj_order_data,$tName); //插入到order表
			if (empty ($insertOrderDataRow)) {
				self::$errCode	=	"020";
				self::$errMsg	=	"插入订单失败！";
				return false;
			}
			//$orderExtenData['omOrderId'] = $obj_order_data['id'];
			//$orderExtenData['omOrderId'] = $insertOrderDataRow;
			//$orderUserInfoData['omOrderId'] = $insertOrderDataRow;
			//$orderUserInfoData['omOrderId'] = $obj_order_data['id'];
			if ($orderExtenData) {
				$resultExten = self :: insertExtension($orderExtenData, $extension, $tName); //插入到order_extend表
				if (!$resultExten) {
					self::$errCode	=	"021";
					self::$errMsg	=	"插入订单附加表失败！";
					return false;
				}
			}
			if($orderUserInfoData){
				$resultUserInfo = self :: insertUserinfoRow($orderUserInfoData,$tName); //插入到order_userInfo表
				if (!$resultUserInfo) {
					self::$errCode	=	"022";
					self::$errMsg	=	"插入用户信息表失败！";
					return false;
				}
			}
			if($orderWhInfoData){
				$resultWhInfo = self :: insertWhInfoRow($orderWhInfoData,$tName); //插入到order_userInfo表
				if (!$resultWhInfo) {
					self::$errCode	=	"022";
					self::$errMsg	=	"插入仓库信息表失败！";
					return false;
				}
			}
			
			foreach($orderDetailArr as $orderDetail){
				$orderDetailData = $orderDetail['orderDetailData'];
				$orderDetailExtenData = $orderDetail['orderDetailExtenData'];
				//$orderDetailData['omOrderId'] = $insertOrderDataRow;
				//$orderDetailData['omOrderId'] = $obj_order_data['id'];
				$orderDetailData['createdTime'] = time();
				$insertOrderDatilRow = self :: insertOrderdetail($orderDetailData,$tName); //插入到detail表
				if (empty ($insertOrderDatilRow)) {
					self::$errCode	=	"023";
					self::$errMsg	=	"插入订明细单失败！";
					return false;
				}
				//$orderDetailExtenData['omOrderdetailId'] = $insertOrderDatilRow;
				//$orderDetailExtenData['omOrderdetailId'] = $orderDetailData['id'];
				if($orderDetailExtenData){
					$resultOrderDetailExten = self :: insertDetailExtension($orderDetailExtenData, $extension, $tName); //插入到detailExtend
					if (!$resultOrderDetailExten) {
						self::$errCode	=	"024";
						self::$errMsg	=	"插入订单明细附带表失败！";
						return false;
					}
				}
			}
			/*if($obj_order_data['platformId'] == 1 && $in){
				$insertOrderidsDada = array('omOrderId'=>$insertOrderDataRow,'PayPalPaymentId'=>$orderData['orderExtenData']['PayPalPaymentId'],'orderid'=>$orderData['orderExtenData']['orderId'],'accountId'=>$orderData['orderData']['accountId'],'saletime'=>time());
				$insertOrderids = OrderidsModel::insertOrderidsList($insertOrderidsDada);
				if (!$insertOrderids) {
					self::$errCode	=	"025";
					self::$errMsg	=	"插入ebay订单IDS失败！";
					return false;
				}
			}
			if(isset($orderData['orderNote']) && !empty($orderData['orderNote'])){
				$orderNote = $orderData['orderNote'];
				$insertOrderNoteDada = array('omOrderId'=>$insertOrderDataRow,'content'=>$orderNote['content'],'userId'=>$orderNote['userId'],'createdTime'=>$orderNote['createdTime']);
				$insertOrderNoteids = self::insertOrderNotesRow($insertOrderNoteDada);
				if (!$insertOrderNoteids) {
					self::$errCode	=	"026";
					self::$errMsg	=	"插入订单Note失败！";
					return false;
				}
			}*/
			/*$buyerInfo = $orderUserInfoData;
			$buyerInfo['platformId'] = $obj_order_data['platformId'];
			unset($buyerInfo['omOrderId']);
			unset($buyerInfo['countrySn']);
			unset($buyerInfo['currency']);
			unset($buyerInfo['currency']);
			//var_dump($buyerInfo);
			$insertBuyerInfo = self::insertBuyerInfoRow($buyerInfo);
			//var_dump($insertBuyerInfo);
			if (!$insertBuyerInfo) {
				return false;
			}
			$ProductStatus = new ProductStatus();			
			if(!$ProductStatus->updateSkuStatusByOrderStatus(array($insertOrderDataRow))){
				self::$errCode	=	"400";
				self::$errMsg	=	"已经包含订单信息，不能重复插入！";
				return false;
			}*/
			//var_dump($insertOrderDataRow); exit;
			//return $insertOrderDataRow;
			self::$errCode	=	"200";
			self::$errMsg	=	"转移成功！";
			return true;
		}else{
			self::$errCode	=	"400";
			self::$errMsg	=	"已经包含订单信息，不能重复插入！";
			return false;
		}
	}
	
	/**
	 * 临时插入订单数据对比关系
	 * @para $data as array
	 * return true
	 */
	public static function insertTempOrderRelation($data){
		self::initDB();
        $string = array2sql_extral($data);
		$sql = "INSERT INTO `om_temp_orderRelation` SET ".$string;
		$query	=	self::$dbConn->query($sql);
		if($query){
			//$orderid = $data['omOrderId'];
			return true;
		}else{
			self::$errCode	=	"003";
			self::$errMsg	=	"error";
			return false;
		}
	}
	
}
?>