<?php
/*
 * 名称：OrderindexModel
 * 功能：订单修改查看操作
 * 版本：v 1.0
 * 日期：2013/09/12
 * 作者：zyp
 * */
class OrderindexModel{
	public 	static $dbConn;
	public	static $errCode	=	0;
	public	static $errMsg	=	"";
	public static $dbPrefix = "";
	
	public static function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
		self::$dbPrefix = "om_";
		mysql_query('SET NAMES UTF8');
	}
	
	/*
	 * 根据条件获取对应订单详情页面数量(最新版)
	 * last modified by Herman.Xi @20131205
	 */
	public static function showOrderNum($tableName, $where){
		!self::$dbConn ? self::initDB() : null;
		$sql = "select * from $tableName $where";
        //echo $sql.'<br>';
      	//global $memc_obj;
		//$result1 = $memc_obj->get_extral("sku_info_".'001');
		//var_dump($result1);
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$ret = self :: $dbConn->num_rows($query);
			return $ret; //成功， 返回列表数据
		} else {
			self :: $errCode = "001";
			self :: $errMsg = "获取数据失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}
	
	/*
	 * 根据条件获取对应订单详情(最新版)
	 * last modified by Herman.Xi @20131205
	 */
	public static function showOrderList($tableName, $where){
		!self::$dbConn ? self::initDB() : null;
		$sql = "select * from $tableName $where";
        //echo $sql.'<br>';
      	//global $memc_obj;
		//$result1 = $memc_obj->get_extral("sku_info_".'001');
		//var_dump($result1);
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$orderList = array();
			while($orderData = self :: $dbConn->fetch_array($query)){
				$omOrderId = $orderData['id'];
				//echo $omOrderId; echo "<br>";
				$orderList[$omOrderId]['orderData'] = $orderData;
				$platfrom = omAccountModel::getPlatformSuffixById($orderData['platformId']);
				$extension = $platfrom['suffix'];//获取后缀名称
				$orderExtenData = self::selectOrderExtenData($tableName, 'where omOrderId = '.$omOrderId, $extension);
				//var_dump($orderExtenData);
				$orderList[$omOrderId]['orderExtenData'] = $orderExtenData;
				$orderUserInfoData = self::selectOrderUserInfoData($tableName, 'where omOrderId = '.$omOrderId, $extension);
				$orderList[$omOrderId]['orderUserInfoData'] = $orderUserInfoData;
                $orderWhInfoData = self::selectOrderWhInfoData($tableName, 'where omOrderId = '.$omOrderId, $extension);
				$orderList[$omOrderId]['orderWhInfoData'] = $orderWhInfoData;
				
				$orderNote = self::selectOrderNote('where omOrderId = '.$omOrderId);
				$orderList[$omOrderId]['orderNote'] = $orderNote;
				
				$orderTracknumber = self::selectOrderTracknumber('where is_delete=0 and omOrderId = '.$omOrderId);
				$orderList[$omOrderId]['orderTracknumber'] = $orderTracknumber;
				
				$orderAudit = SuperOrderModel::selectResult("om_records_order_audit", "omOrderdetailId,sku,auditStatus,appStatus", ' omOrderId = '.$omOrderId);
				$orderList[$omOrderId]['orderAudit'] = $orderAudit;
				
				$orderDetail = self::showOrderDetailList($tableName, 'where omOrderId = '.$omOrderId, $extension);
				$orderList[$omOrderId]['orderDetail'] = $orderDetail;
				
				//echo "<pre>";
				//var_dump($orderList);exit;
				//var_dump($orderDetail); echo "<br>";
				//echo $extension; echo "<br>";
			}
			return $orderList;
			//$ret = self :: $dbConn->fetch_array_all($query);
			//return $ret; //成功，返回列表数据
		} else {
			self :: $errCode = "001";
			self :: $errMsg = "获取数据失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}
	
	/*
	 * 根据条件获取对应订单详情(最新版)
	 * last modified by Herman.Xi @20131205
	 */
	public static function showOnlyOrderList($tableName, $where){
		!self::$dbConn ? self::initDB() : null;
		$sql = "select * from $tableName $where";
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$orderList = self :: $dbConn->fetch_array_all($query);
			return $orderList;
		} else {
			self :: $errCode = "001";
			self :: $errMsg = "获取数据失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}
	
	/*
	 * 根据条件获取对应订单详情(最新版)
	 * last modified by Herman.Xi @20131205
	 */
	public static function showOrderDetailList($tableName, $where, $extension = 'ebay'){
		!self::$dbConn ? self::initDB() : null;
		$sql = "SELECT *  FROM `".$tableName."_detail` {$where}";
		//echo $sql; echo "<br>";
		$query	=	self::$dbConn->query($sql);
		if ($query) {
			$orderDetailList = array();
			while($orderDetailData = self :: $dbConn->fetch_array($query)){
				$omOrderdetailId = $orderDetailData['id'];
				$orderDetailList[$omOrderdetailId]['orderDetailData'] = $orderDetailData;
				$orderDetailExtenData = self::selectOrderDetailExtenData($tableName, 'where omOrderdetailId = '.$omOrderdetailId, $extension);
				$orderDetailList[$omOrderdetailId]['orderDetailExtenData'] = $orderDetailExtenData;
			}
			//var_dump($orderUserInfoData); exit;
			//echo $extension; echo "<br>";
			return $orderDetailList;
		} else {
			self :: $errCode = "001";
			self :: $errMsg = "获取数据失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}
	
	/*
	 * 根据条件获取对应订单明细详情，单独获取明细表(最新版)
	 * last modified by Herman.Xi @20131206
	 */
	public static function showOnlyOrderDetailList($tableName, $where){
		!self::$dbConn ? self::initDB() : null;
		$sql = "SELECT *  FROM `".$tableName."_detail` {$where}";
		//echo $sql; echo "<br>";
		$query	=	self::$dbConn->query($sql);
		if ($query) {
			$orderDetailList = self::$dbConn->fetch_array_all($query);
			return $orderDetailList;
		} else {
			self :: $errCode = "001";
			self :: $errMsg = "获取数据失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}
	
	/**
	 * 搜索平台扩展详情
	 * @para $data as array
	 * return true
	 */
	public static function selectOrderExtenData($tableName, $where, $extension = 'ebay'){
		!self::$dbConn ? self::initDB() : null;
		$sql = "SELECT *  FROM `".$tableName."_extension_".$extension."` {$where}";
		$query	=	self::$dbConn->query($sql);
		if($query){
			$ret = self :: $dbConn->fetch_array($query);
			return $ret;
		}else{
			self::$errCode	=	"003";
			self::$errMsg	=	"error";
			return false;
		}
	}
	
	/**
	 * 搜索用户扩展详情
	 * @para $data as array
	 * return true
	 */
	public static function selectOrderUserInfoData($tableName, $where, $extension = 'ebay'){
		!self::$dbConn ? self::initDB() : null;
		$sql = "SELECT *  FROM `".$tableName."_userInfo"."` {$where}";
		$query	=	self::$dbConn->query($sql);
		if($query){
			$ret = self :: $dbConn->fetch_array($query);
			return $ret;
		}else{
			self::$errCode	=	"003";
			self::$errMsg	=	"error";
			return false;
		}
	}
	
	/**
	 * 搜索平台订单明细详情
	 * @para $data as array
	 * return true
	 */
	public static function selectOrderDetailExtenData($tableName, $where, $extension = 'ebay'){
		!self::$dbConn ? self::initDB() : null;
		$sql = "SELECT *  FROM `".$tableName."_detail_extension_".$extension."` {$where}";
		$query	=	self::$dbConn->query($sql);
		if($query){
			$ret = self :: $dbConn->fetch_array($query);
			return $ret;
		}else{
			self::$errCode	=	"003";
			self::$errMsg	=	"error";
			return false;
		}
	}
	
    /**
	 * 搜索物料对订单操作信息详情
	 * @para $data as array
	 * return true
	 */
	public static function selectOrderWhInfoData($tableName, $where, $extension = 'ebay'){
		!self::$dbConn ? self::initDB() : null;
		$sql = "SELECT *  FROM `".$tableName."_warehouse"."` {$where}";
		$query	=	self::$dbConn->query($sql);
		if($query){
			$ret = self :: $dbConn->fetch_array($query);
			return $ret;
		}else{
			self::$errCode	=	"003";
			self::$errMsg	=	"error";
			return false;
		}
	}
    
	/**
	 * 搜索平台订单备注信息详情
	 * @para $data as array
	 * return true
	 */
	public static function selectOrderNote($where){
		!self::$dbConn ? self::initDB() : null;
		$sql = "SELECT *  FROM `om_order_notes` {$where}";
		$query	=	self::$dbConn->query($sql);
		if($query){
			$ret = self :: $dbConn->fetch_array_all($query);
			return $ret;
		}else{
			self::$errCode	=	"003";
			self::$errMsg	=	"error";
			return false;
		}
	}
	
	/**
	 * 搜索平台订单跟踪号详情
	 * @para $data as array
	 * return true
	 */
	public static function selectOrderTracknumber($where){
		!self::$dbConn ? self::initDB() : null;
		$sql = "SELECT *  FROM `om_order_tracknumber` {$where}";
		$query	=	self::$dbConn->query($sql);
		if($query){
			$ret = self :: $dbConn->fetch_array_all($query);
			return $ret;
		}else{
			self::$errCode	=	"003";
			self::$errMsg	=	"error";
			return false;
		}
	}
	
	/**
	 * 搜索平台对应仓库配货信息跟踪详情
	 * @para $data as array
	 * return true
	 */
	public static function selectOrderWarehouse($where,$tableName= "om_unshipped_order"){
		!self::$dbConn ? self::initDB() : null;
		$sql = "SELECT *  FROM `".$tableName."_warehouse` {$where}";
		$query	=	self::$dbConn->query($sql);
		if($query){
			$ret = self :: $dbConn->fetch_array($query);
			return $ret;
		}else{
			self::$errCode	=	"003";
			self::$errMsg	=	"error";
			return false;
		}
	}
	
	/**
	 * 搜索真实料号信息列表
	 * @para $data as array
	 * return true
	 */
	public static function getRealskulist($omOrderId, $tableName, $where, $storeId=1){
		!self::$dbConn ? self::initDB() : null;
		
		$sql = "SELECT id,combinePackage FROM $tableName WHERE is_delete = 0 AND storeId = ".$storeId." AND id = ".$omOrderId." ".$where;
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$skuinfos = array();
			while($orderData = self :: $dbConn->fetch_array($query)){
				//var_dump($orderData); exit;
				$omOrderId 		= $orderData['id'];
				$combinePackage = $orderData['combinePackage'];
				$orderdetails = self::showOnlyOrderDetailList($tableName, 'where omOrderId = '.$omOrderId);
				//var_dump($orderdetails); echo "<br>";
				foreach ($orderdetails AS $_k => $odlist){
					$sku = trim($odlist['sku']);
					$amount = $odlist['amount'];
					$sku_arr = GoodsModel::get_realskuinfo($sku);
					//var_dump($sku_arr); exit;
					foreach($sku_arr as $or_sku => $or_nums){
						if(isset($skuinfos[$or_sku])){
							$skuinfos[$or_sku]+=$or_nums * $amount;
						}else{
							$skuinfos[$or_sku]=$or_nums * $amount;
						}
					}
				}
					
				if($combinePackage == 1){
					$_omOrderids = combinePackageModel::selectRecordByOrderId($omOrderId);
					if($_omOrderids){
						foreach($_omOrderids as $_omOrderId){
							$_skuinfos = self::getRealskulist($_omOrderId,$tableName, $where, $storeId);
							foreach($_skuinfos as $_sku => $_nums){
								if(isset($_skuinfos[$_sku])){
									$skuinfos[$_sku]+=$_nums;
								}else{
									$skuinfos[$_sku]=$_nums;
								}
							}
						}
					}
				}
			}
			return $skuinfos;
		}else{
			self::$errCode	=	"003";
			self::$errMsg	=	"error";
			return false;
		}
	}
	
	/**
	 * 删除订单表信息，附带的信息也将删除
	 * @para $data as array
	 * return true
	 */
	public static function deleteOrderData($tableName, $where, $extension = 'ebay'){
		!self::$dbConn ? self::initDB() : null;
		$sql = "UPDATE `".$tableName."` SET is_delete = 1 {$where}";
		$query	=	self::$dbConn->query($sql);
		if($query){
			self::$errCode	=	"200";
			self::$errMsg	=	"delete success";
			return true;
		}else{
			self::$errCode	=	"003";
			self::$errMsg	=	"delete error";
			return false;
		}
	}
	
	/**
	 * 删除订单表明细表信息，附带的信息也将删除
	 * @para $data as array
	 * return true
	 */
	public static function deleteOrderDetailData($tableName, $where, $extension = 'ebay'){
		!self::$dbConn ? self::initDB() : null;
		$sql = "UPDATE `".$tableName."` SET is_delete = 1 {$where}";
		//echo $sql; echo "<br>";
		$query	=	self::$dbConn->query($sql);
		if($query){
			self::$errCode	=	"200";
			self::$errMsg	=	"delete success";
			return true;
		}else{
			self::$errCode	=	"003";
			self::$errMsg	=	"delete error";
			return false;
		}
	}
	
	public static function showSearchOrderList($tableName, $where, $extenwhere=''){//数据查找

		!self::$dbConn ? self::initDB() : null;
		$orderDetailForm = $tableName.'_detail';
		$accountList = $_SESSION['accountList'];
		$platformList = $_SESSION['platformList'];
		//echo "<pre>"; print_r($accountList); exit;
		$platformsee = array();
		for($i=0;$i<count($platformList);$i++){
			$platformsee[]	= $platformList[$i];
		}
		if($platformsee){
			$where .= ' AND da.platformId IN ( '.join(',', $platformsee).' ) ';
		}
		$accountsee = array();
		for($i=0;$i<count($accountList);$i++){
			$accountsee[]	= $accountList[$i];
		}
		if($accountsee){
			$where .= ' AND da.accountId IN ( '.join(",", $accountsee).' ) ';
		}
		
		$sql = "SELECT
					da.*
				FROM
					".$tableName." AS da
				LEFT JOIN ".$orderDetailForm." AS db ON db.omOrderId = da.id 
				".$where.$extenwhere;
				
		//echo $sql; echo "<br>";
		$query	 =	self::$dbConn->query($sql);
		if($query){
			//$ret	=	self::$dbConn->fetch_array_all($query);
			$orderList = array();
			while($orderData = self :: $dbConn->fetch_array($query)){
				$omOrderId = $orderData['id'];
				//echo $omOrderId; echo "<br>";
				$orderList[$omOrderId]['orderData'] = $orderData;
				$platfrom = omAccountModel::getPlatformSuffixById($orderData['platformId']);
				$extension = $platfrom['suffix'];//获取后缀名称
				$orderExtenData = self::selectOrderExtenData($tableName, 'where omOrderId = '.$omOrderId, $extension);
				//var_dump($orderExtenData);
				$orderList[$omOrderId]['orderExtenData'] = $orderExtenData;
				$orderUserInfoData = self::selectOrderUserInfoData($tableName, 'where omOrderId = '.$omOrderId, $extension);
				$orderList[$omOrderId]['orderUserInfoData'] = $orderUserInfoData;
				
				$orderNote = self::selectOrderNote('where omOrderId = '.$omOrderId);
				$orderList[$omOrderId]['orderNote'] = $orderNote;
				
				$orderTracknumber = self::selectOrderTracknumber('where omOrderId = '.$omOrderId);
				$orderList[$omOrderId]['orderTracknumber'] = $orderTracknumber;
				
				$orderWarehouse = self::selectOrderWarehouse('where omOrderId = '.$omOrderId, $tableName);
				$orderList[$omOrderId]['orderWarehouse'] = $orderWarehouse;
				
				$orderAudit = SuperOrderModel::selectResult("om_records_order_audit", "omOrderdetailId,sku,auditStatus,appStatus", ' omOrderId = '.$omOrderId);
				$orderList[$omOrderId]['orderAudit'] = $orderAudit;
				
				$orderCombinePackage = OrderRecordModel::getCombinePackageRecords($omOrderId);
				$orderList[$omOrderId]['combinePackage'] = $orderCombinePackage;
				
				$orderDetail = self::showOrderDetailList($tableName, 'where is_delete = 0 and  omOrderId = '.$omOrderId, $extension);
				$orderList[$omOrderId]['orderDetail'] = $orderDetail;
				
				//echo "<pre>";
				//var_dump($orderList);exit;
				//var_dump($orderDetail); echo "<br>";
				//echo $extension; echo "<br>";
			}
			self::$errCode	=	"200";
			self::$errMsg	=	"get success";
			return $orderList;
		}else{
			self::$errCode	=	"004";
			self::$errMsg	=	"get error";
			return false;
		}
	}
	
	public static function showSearchOrderNum($tableName, $where, $extenwhere=''){//数据查找

		!self::$dbConn ? self::initDB() : null;
		$orderDetailForm = $tableName.'_detail';
		$accountList = $_SESSION['accountList'];
		$accountsee = array();
		for($i=0;$i<count($accountList);$i++){
			$accountsee[]	= "da.accountId='".$accountList[$i]."'";
		}
		if($accountsee){
			$where .= ' AND ('.join(" or ", $accountsee).') ';
		}
		
		/*$sql = "SELECT
					DISTINCT da.*
				FROM
					".$tableName." AS da
				LEFT JOIN ".$orderDetailForm." AS db ON db.omOrderId = da.id 
				".$where.$extenwhere;*/
		$sql = "SELECT
					count(*) AS num
				FROM
					".$tableName." AS da
				".$where.$extenwhere;
		//echo $sql; echo "<br>";	
		//  JOIN ".$orderDetailForm." AS db ON db.omOrderId = da.id 
		$query	 =	self::$dbConn->query($sql);
		if($query){
			//$ret	=	self::$dbConn->fetch_array($query);
			$ret	=	self::$dbConn->fetch_array($query);
			
			self::$errCode	=	"200";
			self::$errMsg	=	"get success";
			/*if($ret){
				$ret = count($ret);
			}else{
				$ret = 0;	
			}*/
			return $ret['num'];
		}else{
			self::$errCode	=	"004";
			self::$errMsg	=	"get error";
			return 0;
		}
	}
	
	public static function updateOrder($tableName,$data,$where){
		self::initDB();
		$string = array2sql($data);
		$sql = "UPDATE ".$tableName." SET ".$string." ".$where;
		if(isset($data['orderType'])){
			$info = explode("=",$where);
			$orderid = $info[1];
			OrderLogModel::orderLog($orderid,$sql,"更新".$tableName,$data['orderType']);
		}else{
			$info = explode("=",$where);
			$orderid = $info[1];
			OrderLogModel::orderLog($orderid,$sql,"更新".$tableName);
		}
		$query = self::$dbConn->query($sql);
		if($query){
			self::$errCode	=	"200";
			self::$errMsg	=	"update success";
			return true;
		}else{
			self::$errCode	=	"004";
			self::$errMsg	=	"update error";
			return false;
		}
	}
	
	public static function deleteOrderDetail($data,$where){
		$tableName = 'om_unshipped_order_detail';
		$rtn = self::updateOrder($tableName,$data,$where);
		//self::deleteOrderDetailExtenData();
		return $rtn;
	}
	
	//将订单从unShipped 转移到 shipped 里面
	//add by Herman.Xi @ 20140215
	public static function shiftOrderList($where){
		BaseModel :: begin(); //开始事务
		$unshipped_tableName	= 'om_unshipped_order';
		$shipped_tableName		= 'om_shipped_order';
		$orderList = self::showOrderList($unshipped_tableName,$where);
		//echo "<pre>"; print_r($orderList); exit;
		if(empty($orderList)){
			self :: $errCode = '000';
			self :: $errMsg = "无操作数据!";
			return false;
		}
		foreach($orderList as $omOrderId => $orderData){
			$obj_order_data = $orderData['orderData'];
			$orderDetail = $orderData['orderDetail'];
			$insert_orderDetail = array ();
			foreach ($orderDetail as $detail) {
				$insert_orderDetailData = $detail['orderDetailData'];
				//unset ($insert_orderDetailData['id']);
				$insert_orderDetailExtenData = $detail['orderDetailExtenData'];
				//unset ($insert_orderDetailExtenData['omOrderdetailId']);
				$insert_orderDetail[] = array (
					'orderDetailData' => $insert_orderDetailData,
					'orderDetailExtenData' => $insert_orderDetailExtenData
				);
			}
			//BaseModel :: rollback();\
			//self :: $dbConn->query('SET AUTOCOMMIT=1');
			//return FALSE;
			//unset ($obj_order_data['id']);
			$orderExtenData = $orderData['orderExtenData'];
			//unset ($orderExtenData['omOrderId']);
			$orderUserInfoData = $orderData['orderUserInfoData'];
			//unset ($orderExtenData['omOrderId']);
			$orderWhInfoData = $orderData['orderWhInfoData'];
			//unset ($orderWhInfoData['omOrderId']);
			//$obj_order_data['orderStatus'] = C('STATEBUJI');
			//$obj_order_data['orderType'] = C('STATEBUJI_DONE');
			
			$insert_orderData = array ();
			$insert_orderData = array (
				'orderData' => $obj_order_data,
				'orderExtenData' => $orderExtenData,
				'orderUserInfoData' => $orderUserInfoData,
				'orderDetail' => $insert_orderDetail,
				'orderWhInfoData'=> $orderWhInfoData,
			);
			//var_dump($insert_orderData); exit;
			if ($insertId = OrderAddModel :: shiftAllOrderRowNoEvent($insert_orderData)) {
				//echo $split_log .= 'insert success!' . "\n"; exit;
				//var_dump($_mainId,$_spitId); exit;
				if (!OrderLogModel :: insertOrderLog($insertId, '从unshipped表转移数据到shipped表中，第一步：添加shipped表数据')) {
					BaseModel :: rollback();
					self :: $errCode = '001';
					self :: $errMsg = "转移数据添加日志失败，第一步：添加shipped表数据失败!";
					return false;
				}
			} else {
				BaseModel :: rollback();
				self :: $errCode = '002';
				self :: $errMsg = "INSERT数据失败!";
				return false;
			}
			if (self :: killAllOrderRowNoEvent($omOrderId,$obj_order_data['platformId'])) {
				//echo $split_log .= 'insert success!' . "\n"; exit;
				//var_dump($_mainId,$_spitId); exit;
				if (!OrderLogModel :: insertOrderLog($omOrderId, '从unshipped表转移数据到shipped表中，第二步：删除unshipped表中数据')) {
					BaseModel :: rollback();
					self :: $errCode = '003';
					self :: $errMsg = "转移数据添加日志失败，第二步：删除unshipped表中数据失败!";
					return false;
				}
			} else {
				BaseModel :: rollback();
				self :: $errCode = '004';
				self :: $errMsg = "KILL数据失败!";
				return false;
			}
		}
		BaseModel :: commit();
		BaseModel :: autoCommit();
		self :: $errCode = '200';
		self :: $errMsg = "转移数据成功！";
		return TRUE;
	}
	
	/**
	 * KILL订单信息
	 * @para $data as array
	 * return true
	 */
	public static function killOrder($omOrderId){
		self::initDB();
		//var_dump(self::$dbConn);
		$tableName = 'om_unshipped_order';
		$where = ' WHERE id = ' . $omOrderId . ' and is_delete = 0 and storeId = 1';
		$sql = "DELETE FROM ".$tableName.$where;
		//OrderLogModel::orderLog($omOrderId,$sql,"物理删除".$tableName);
		$query = self::$dbConn->query($sql);
		if($query){
			self::$errCode	=	"200";
			self::$errMsg	=	"update success";
			return true;
		}else{
			self::$errCode	=	"004";
			self::$errMsg	=	"update error";
			return false;
		}
	}
	
	/**
	 * KILL订单扩展详情
	 * @para $data as array
	 * return true
	 */
	public static function killOrderExtenData($tableName, $where, $extension = 'ebay'){
		self::initDB();
		$sql 	= "DELETE FROM `".$tableName."_extension_".$extension."` {$where}";
		//echo $sql; echo "\n";
		$query	= self::$dbConn->query($sql);
		if($query){
			self::$errCode	=	"200";
			self::$errMsg	=	"success";
			return true;
		}else{
			self::$errCode	=	"003";
			self::$errMsg	=	"error";
			return false;
		}
	}
	
	/**
	 * KILL用户扩展详情
	 * @para $data as array
	 * return true
	 */
	public static function killOrderUserInfoData($tableName, $where, $extension = 'ebay'){
		!self::$dbConn ? self::initDB() : null;
		$sql = "DELETE FROM `".$tableName."_userInfo"."` {$where}";
		$query	=	self::$dbConn->query($sql);
		if($query){
			self::$errCode	=	"200";
			self::$errMsg	=	"success";
			return true;
		}else{
			self::$errCode	=	"003";
			self::$errMsg	=	"error";
			return false;
		}
	}
	
	/**
	 * KILL物料对订单操作信息详情
	 * @para $data as array
	 * return true
	 */
	public static function killOrderWhInfoData($tableName, $where, $extension = 'ebay'){
		!self::$dbConn ? self::initDB() : null;
		$sql = "DELETE FROM `".$tableName."_warehouse"."` {$where}";
		$query	=	self::$dbConn->query($sql);
		if($query){
			self::$errCode	=	"200";
			self::$errMsg	=	"success";
			return true;
		}else{
			self::$errCode	=	"003";
			self::$errMsg	=	"error";
			return false;
		}
	}
	
	/*
	 * 删除根据条件获取对应订单详情(最新版)
	 * last modified by Herman.Xi @20140215
	 */
	public static function killOrderDetailList($tableName, $where, $extension = 'ebay'){
		!self::$dbConn ? self::initDB() : null;
		$sql = "SELECT *  FROM `".$tableName."_detail` {$where}";
		//echo $sql; echo "<br>";
		$query	=	self::$dbConn->query($sql);
		if ($query) {
			$orderDetailList = array();
			while($orderDetailData = self :: $dbConn->fetch_array($query)){
				$omOrderdetailId = $orderDetailData['id'];
				$orderDetailExtenData = self::killOrderDetailExtenData($tableName, 'where omOrderdetailId = '.$omOrderdetailId, $extension);
			}
			$sql = "DELETE FROM `".$tableName."_detail` {$where}";
			//echo $sql; echo "<br>";
			$query	=	self::$dbConn->query($sql);
			if($query){
				self::$errCode	=	"200";
				self::$errMsg	=	"success";
				return true;
			}else{
				self::$errCode	=	"003";
				self::$errMsg	=	"error";
				return false;
			}
		}
	}
	
	/**
	 * 搜索平台订单明细详情
	 * @para $data as array
	 * return true
	 */
	public static function killOrderDetailExtenData($tableName, $where, $extension = 'ebay'){
		!self::$dbConn ? self::initDB() : null;
		$sql = "DELETE FROM `".$tableName."_detail_extension_".$extension."` {$where}";
		$query	=	self::$dbConn->query($sql);
		if($query){
			self::$errCode	=	"200";
			self::$errMsg	=	"success";
			return true;
		}else{
			self::$errCode	=	"003";
			self::$errMsg	=	"error";
			return false;
		}
	}
	
	/*
	 * 将订单数据kill掉，单个物理删除(最新版)
	 * last modified by Herman.Xi @20141205
	 */
	public static function killAllOrderRowNoEvent($omOrderId,$platformId,$tableName = 'om_unshipped_order'){
		!self::$dbConn ? self::initDB() : null;
		/*$where = ' WHERE id = {$omOrderId} AND storeId = 1 AND is_delete = 0 ';
		$sql = "SELECT * FROM $tableName $where";
        //echo $sql.'<br>';
		
		$query = self :: $dbConn->query($sql);
		$orderList = self :: $dbConn->fetch_array($query);
		$orderData = $orderList[$omOrderId];
		$omOrderId = $orderData['id'];
		//echo $omOrderId; echo "<br>";
		$orderList[$omOrderId]['orderData'] = $orderData;*/
		if(!self::killOrder($omOrderId)){
			self :: $errCode = "010";
			self :: $errMsg = "删除订单表失败";
			return false; //失败则设置错误码和错误信息， 返回false	
		}
		$platfrom = omAccountModel::getPlatformSuffixById($platformId);
		$extension = $platfrom['suffix'];//获取后缀名称
		if(!self::killOrderExtenData($tableName, 'WHERE omOrderId = '.$omOrderId, $extension)){
			self :: $errCode = "011";
			self :: $errMsg = "删除订单附带表失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
		//var_dump($orderExtenData);
		if(!self::killOrderUserInfoData($tableName, 'WHERE omOrderId = '.$omOrderId, $extension)){
			self :: $errCode = "012";
			self :: $errMsg = "删除订单用户信息表失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
		if(!self::killOrderWhInfoData($tableName, 'WHERE omOrderId = '.$omOrderId, $extension)){
			self :: $errCode = "013";
			self :: $errMsg = "删除订单仓库信息表失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
		if(!self::killOrderDetailList($tableName, 'WHERE omOrderId = '.$omOrderId, $extension)){
			self :: $errCode = "014";
			self :: $errMsg = "删除订单明细表失败";
			return false; //失败则设置错误码和错误信息， 返回false	
		}
		/*if(!self::killOrderDetailList($tableName, 'WHERE omOrderId = '.$omOrderId, $extension)){
			self :: $errCode = "015";
			self :: $errMsg = "删除订单明细表失败";
			return false; //失败则设置错误码和错误信息， 返回false	
		}*/
		return true;
	}
	
}
?>