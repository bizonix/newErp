<?php
/*
*包装扫描功能
*@author heminghua
*/
class packingScanOrderModel{
	public 	static $dbConn;
	public	static $errCode	=	0;
	public	static $errMsg	=	"";

	//db初始化
	public 	function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
		mysql_query('SET NAMES UTF8');
	}
	public static function selectRecord($where){
		self::initDB();
		$sql = "SELECT id,orderStatus,transportId,channelId,countryName FROM wh_shipping_order {$where}";
		//echo $sql;
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			$ret = self::$dbConn->fetch_array_all($query);
			return $ret;	
		}else{
			return false;	
		}
	}
	public static function updateOrderRecord($orderid,$userId){
		self::initDB();
		$sql = "UPDATE wh_shipping_order_records SET packersId={$userId},packingTime=".time()." WHERE shipOrderId={$orderid}";
		//echo $sql;
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			
			return true;	
		}else{
			return false;	
		}
	}
	public static function insertPackingRecord($orderid,$userId){
		self::initDB();
		$sql = "INSERT INTO wh_order_package_records(shipOrderId,scanTime,scanUserId,isScan) VALUES ({$orderid},".time().",{$userId},1)";
		//echo $sql;
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			
			return true;	
		}else{
			return false;	
		}
	}
	public static function insertTrackRecord($tracknumber,$orderid){
		self::initDB();
		$sql = "INSERT INTO wh_order_tracknumber(tracknumber,shipOrderId,createdTime) VALUES ('{$tracknumber}',{$orderid},".time().")";
		//echo $sql;
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			
			return true;	
		}else{
			return false;	
		}
	}
}
?>