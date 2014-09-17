<?php
/**
 *类名：FinejoFactoryModel
 *功能：芬哲服装厂ERP数据同步信息
 *日期: 2014-02-27
 *版本：1.0
 *作者：王民伟
 */
class FinejoFactoryModel{
	public static $dbConn;
	public static $errCode = 0;
	public static $errMsg  = "";
	public static function	initDB(){
		global $dbConn;
		self::$dbConn 	= $dbConn;
	}

	//返回需要同步的数据行数,用于分页请求
	public static function getItemInfoCount(){
		self::initDB();
		$sql 	= "SELECT count(*) AS total FROM om_sku_daily_status AS a ";
		$sql   .= "JOIN ph_goods_calc AS b ON a.sku = b.sku ";
		$sql   .= "JOIN pc_goods AS c ON b.sku = c.sku";
		$query  = self::$dbConn->query($sql);
		$num 	= self::$dbConn->fetch_array($query);
		return $num["total"];
	}

	//按分页请求返回料号信息
	public function getItemInfo($page, $pagenum){
		self::initDB();
		$start	= ($page - 1) * $pagenum;
		$sql 	= "SELECT a.sku, a.averageDailyCount, a.availableStockCount, a.actualStockCount, a.is_warning, ";
		$sql   .= "a.waitingSendCount, a.interceptSendCount, a.shortageSendCount, a.waitingAuditCount, b.purchasedays, ";
		$sql   .= "b.goodsdays, c.goodsStatus FROM om_sku_daily_status AS a ";
		$sql   .= "JOIN ph_goods_calc AS b ON a.sku = b.sku ";
		$sql   .= "JOIN pc_goods AS c ON b.sku = c.sku ";
		$sql   .= "ORDER BY a.sku ASC LIMIT $start, $pagenum";
		$query  = self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->fetch_array_all($query);
			return $ret;
		}else{
			return false;
		}
	}	
}
?>