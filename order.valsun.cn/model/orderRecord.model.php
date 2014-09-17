<?php
/*
 * 名称：OrderRecordModel
 * 功能：订单记录，包括各个操作记录和审核记录
 * 版本：v 1.0
 * 日期：2013/12/06
 * 作者：Herman.xi
 * */
class OrderRecordModel{
	public 	static $dbConn;
	public	static $errCode	=	0;
	public	static $errMsg	=	'';
	public	static $auitTable	=	'om_records_order_audit';
	public	static $spitTable	=	'om_records_splitOrder';
	public	static $combineTable=	'om_records_combinePackage';
	public	static $srTable		=	'om_sendReplacement_records';
	public	static $combineOrderTable		=	'om_records_combineOrder';
	
	public static function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
		mysql_query('SET NAMES UTF8');
	}
	
	/*
	 * 获取订单下的审核记录(最新版)
	 * last modified by Herman.Xi @20131205
	 */
	public static function getOrderAuditRecords($omOrderId){
		!self::$dbConn ? self::initDB() : null;
		$sql = "select * from ".self::$auitTable." WHERE omOrderId = ".$omOrderId;
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$ret = self :: $dbConn->fetch_array_all($query);
			return $ret; //成功， 返回列表数据
		} else {
			self :: $errCode = "001";
			self :: $errMsg = "获取数据失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}
	
	/*
	 * 获取订单下的审核记录(最新版)
	 * last modified by Herman.Xi @20131205
	 */
	public static function getOrderCombineRecords($omOrderId){
		!self::$dbConn ? self::initDB() : null;
		$sql = "select * from ".self::$om_sendReplacement_records." WHERE main_order_id = ".$omOrderId;
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$ret = self :: $dbConn->fetch_array_all($query);
			return $ret; //成功， 返回列表数据
		} else {
			self :: $errCode = "001";
			self :: $errMsg = "获取数据失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}
	
	/*
	 * 获取订单下sku的审核记录(最新版)
	 * last modified by Herman.Xi @20131205
	 */
	public static function getOrderAuditRecordsBySku($omOrderId, $sku){
		!self::$dbConn ? self::initDB() : null;
		$sql = "select * from ".self::$auitTable." WHERE omOrderId = ".$omOrderId." and sku = '".$sku."'";
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$ret = self :: $dbConn->fetch_array($query);
			return $ret; //成功， 返回列表数据
		} else {
			self :: $errCode = "001";
			self :: $errMsg = "获取数据失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}
	
	/*
	 * 判断sku是否有未审核通过的方法
	 * last modified by Herman.Xi @20131206
	 */
	public static function judgeAuditRecordsInSkus($omOrderId, $skuinfos){
		!self::$dbConn ? self::initDB() : null;
		$isend = false;
		foreach($skuinfos as $_sku => $_nums){
			$oneAuditRecord = self::getOrderAuditRecordsBySku($omOrderId, $_sku);
			if (!empty($oneAuditRecord) && $oneAuditRecord['auditStatus'] == 2){
				$isend = true;
				break;
			}
		}
		return $isend;
	}
	
	/*
	 * 插入超大订单拆分记录(最新版)
	 * last modified by Herman.Xi @20131205
	 */
	public static function insertSpitRecords($_mainId,$_spitId){
		!self::$dbConn ? self::initDB() : null;
		$data = array('main_order_id'=>$_mainId, 'split_order_id' => $_spitId, 'createdTime'=>time(), 'creator'=>$_SESSION['sysUserId']);
        $string = array2sql($data);
		//var_dump($string); exit;
		$sql = "INSERT INTO `".self::$spitTable."` SET ".$string;
		//echo $sql; exit;
		$query	=	self::$dbConn->query($sql);
		if($query){
			self :: $errCode = "200";
			self :: $errMsg = "插入成功";
			return true;
		}else{
			self :: $errCode = "001";
			self :: $errMsg = "插入失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}
	
	/*
	 * 补寄记录(最新版)
	 * last modified by Herman.Xi @20131205
	 */
	public static function insertSendRecords($_mainId,$_spitId){
		!self::$dbConn ? self::initDB() : null;
		$data = array('main_order_id'=>$_mainId, 'split_order_id' => $_spitId, 'createdTime'=>time(), 'creator'=>$_SESSION['sysUserId']);
        $string = array2sql($data);
		//var_dump($string); exit;
		$sql = "INSERT INTO `".self::$srTable."` SET ".$string;
		//echo $sql; exit;
		$query	=	self::$dbConn->query($sql);
		if($query){
			self :: $errCode = "200";
			self :: $errMsg = "插入成功";
			return true;
		}else{
			self :: $errCode = "001";
			self :: $errMsg = "插入失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}
	
	/*
	 * 插入合并包裹订单拆分记录(最新版)
	 * last modified by Herman.Xi @20131213
	 */
	public static function insertCombineRecord($mainOrderId,$sonOrderId){
		self::initDB();
		$userId = $_SESSION['sysUserId'];
		$sql = "INSERT INTO ".self::$combineTable." SET main_order_id={$mainOrderId},split_order_id={$sonOrderId},createdTime=".time().",creator={$userId}";
		$query = self::$dbConn->query($sql);
		if($query){
			self :: $errCode = "200";
			self :: $errMsg = "插入成功";
			return true;
		}else{
			self :: $errCode = "001";
			self :: $errMsg = "插入失败";
			return false;
		}
	}
	
	/*
	 * 获取订单下的审核记录(最新版)
	 * last modified by Herman.Xi @20131205
	 */
	public static function getCombinePackageRecords($omOrderId){
		!self::$dbConn ? self::initDB() : null;
		$sql = "select * from ".self::$combineTable." WHERE main_order_id = ".$omOrderId." and is_enable = 1";
		$query = self :: $dbConn->query($sql);
		$arr = array();
		if ($query) {
			$arrSon = array();
			$ret = self :: $dbConn->fetch_array_all($query);
			if(!empty($ret)){
				$arr['main'] = $omOrderId;
				foreach($ret as $value){
					$arrSon[] = $value['split_order_id'];
				}
				$arr['son'] = join(',', $arrSon);
				self :: $errCode = "200";
				self :: $errMsg = "获取数据成功";
				return $arr; //失败则设置错误码和错误信息， 返回false
			}else{
				$sql = "select * from ".self::$combineTable." WHERE split_order_id = ".$omOrderId." and is_enable = 1";
				$query = self :: $dbConn->query($sql);
				if ($query) {
					$ret = self :: $dbConn->fetch_array_all($query);
					if(!empty($ret)){
						$arr = self::getCombinePackageRecords($ret[0]['main_order_id']);
					}
					return $arr; //
				} else {
					self :: $errCode = "200";
					self :: $errMsg = "获取数据成功";
					return false; //失败则设置错误码和错误信息， 返回false
				}	
			}
		} else {
			self :: $errCode = "001";
			self :: $errMsg = "获取数据失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}
	/*
	 * 插入合订单跟踪号记录(最新版)
	 * last modified by Herman.Xi @20131213
	 */
	public static function insertOrderTrackRow($data){
		self::initDB();
		$string = array2sql($data);
		$sql = "INSERT INTO om_order_tracknumber SET ".$string;
		$query = self::$dbConn->query($sql);
		if($query){
			self :: $errCode = "200";
			self :: $errMsg = "插入成功";
			return true;
		}else{
			self :: $errCode = "001";
			self :: $errMsg = "插入失败";
			return false;
		}
	}
	
}
?>