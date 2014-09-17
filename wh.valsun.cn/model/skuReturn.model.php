<?php

/*
 * pda料号入库
 * @author heminghua
 */
class SkuReturnModel {
	public static $dbConn;
	public static $errCode = 0;
	public static $errMsg = "";

	//db初始化
	public function initDB() {
		global $dbConn;
		self :: $dbConn = $dbConn;
		mysql_query('SET NAMES UTF8');
	}

	/*
	 * 查询料号信息
	 */
	public static function selectSkuRecord($sku) {
		self :: initDB();
		$sql = "SELECT * FROM pc_goods WHERE sku='{$sku}'";
		$query = self::$dbConn->query($sql);
		//$goodsinfo = $dbconn->fetch_array($result);
		if ($query) {
			$ret = self :: $dbConn->fetch_array_all($query);
			return $ret;
		} else {
			self :: $errCode = 0100;
			self :: $errMsg = 'getSkuStockList';
			return false;
		}
	}
			
	public static function selectStockRecord($sku) {
		self :: initDB();
		$sql = "SELECT * FROM wh_sku_location WHERE sku ='{$sku}'";
		$query = self::$dbConn->query($sql);
		//$goodsinfo = $dbconn->fetch_array($result);
		if ($query) {
			$ret = self :: $dbConn->fetch_array_all($query);
			return $ret;
		} else {
			self :: $errCode = 0100;
			self :: $errMsg = 'getSkuStockList';
			return false;
		}
	}
	public static function updateStock($sku) {
		self :: initDB();
		$sql = "UPDATE wh_sku_location SET actualStock=actualStock+1 WHERE sku ='{$sku}'";
		$query = self::$dbConn->query($sql);
		//$goodsinfo = $dbconn->fetch_array($result);
		if ($query) {
			return true;
		} else {
			self :: $errCode = 0100;
			self :: $errMsg = 'getSkuStockList';
			return false;
		}
	}
	
	public static function selectRecordById($ebay_id) {
		self :: initDB();
		$sql = "SELECT * FROM wh_shipping_order WHERE id ={$ebay_id}";
		$query = self::$dbConn->query($sql);
		//$goodsinfo = $dbconn->fetch_array($result);
		if ($query) {
			$ret = self :: $dbConn->fetch_array_all($query);
			return $ret;
		} else {
			self :: $errCode = 0100;
			self :: $errMsg = 'getSkuStockList';
			return false;
		}
	}
	public static function selectRecordBytrack($ebay_id) {
		self :: initDB();
		$sql = "SELECT * FROM wh_order_tracknumber WHERE tracknumber ={$ebay_id}";
		$query = self::$dbConn->query($sql);
		//$goodsinfo = $dbconn->fetch_array($result);
		if ($query) {
			$ret = self :: $dbConn->fetch_array_all($query);
			return $ret;
		} else {
			self :: $errCode = 0100;
			self :: $errMsg = 'getSkuStockList';
			return false;
		}
	}
	public static function selectscanRecord($sku,$ebay_id) {
		self :: initDB();
		$sql = "SELECT * FROM wh_order_picking_records WHERE shipOrderId ={$ebay_id} and sku='{$sku}' and is_delete=0";
		$query = self::$dbConn->query($sql);
		//$goodsinfo = $dbconn->fetch_array($result);
		if ($query) {
			$ret = self :: $dbConn->fetch_array($query);
			return $ret;
		} else {
			self :: $errCode = 0100;
			self :: $errMsg = 'getSkuStockList';
			return false;
		}
	}
	public static function updatescanRecord($num,$ebay_id,$sku){
		self :: initDB();
		if($num === "all"){
			$sql = "UPDATE wh_order_picking_records SET amount=0 WHERE sku ='{$sku}' and is_delete=0";
		}else{
			$sql = "UPDATE wh_order_picking_records SET amount=amount-{$num} WHERE sku ='{$sku}' and is_delete=0";
		}
		$query = self::$dbConn->query($sql);
		//$goodsinfo = $dbconn->fetch_array($result);
		if ($query) {

			return true;
		} else {
			self :: $errCode = 0100;
			self :: $errMsg = 'getSkuStockList';
			return false;
		}
	}
}	