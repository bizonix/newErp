<?php
/*
*点货操作(model)
*add by heminghua
*
*/
class orderWeighingModel{
	public 	static $dbConn;
	public	static $errCode	=	0;
	public	static $errMsg	=	"";

	//db初始化
	public 	function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
		mysql_query('SET NAMES UTF8');
	}
	public static function selectOrderId($tracknumber){
		self::initDB();
		$sql	 =	"SELECT shipOrderId FROM wh_order_tracknumber WHERE tracknumber='{$tracknumber}'";
		//echo $sql;
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			$res = self::$dbConn->fetch_array_all($query);
			return $res;	
		}else{
			return false;	
		}
	}
	public static function selectRecord($where){
		self::initDB();
		$sql	 =	"SELECT id,countryName,transportId,channelId,orderStatus,calcWeight,orderAttributes FROM wh_shipping_order {$where}";
		//echo $sql;
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			$res = self::$dbConn->fetch_array_all($query);
			return $res;	
		}else{
			return false;	
		}
	}
	public static function selectOrderDetail($orderid){
		self::initDB();
		$sql     = "SELECT id,shipOrderId,sku,amount FROM `wh_shipping_orderdetail` WHERE shipOrderId ={$orderid}";
		$query   = self::$dbConn->query($sql);
		if($query){
			$res = self::$dbConn->fetch_array_all($query);
			return $res;
		}else{
			return false;
		}
	}
	public static function insertRecord($orderid,$userId){
		self::initDB();
		$sql     = "INSERT INTO wh_order_weigh_records(shipOrderId,scanTime,scanUserId,isScan) values({$orderid},".time().",{$userId},1)";
		$query   = self::$dbConn->query($sql);
		if($query){
			return true;
		}else{
			return false;
		}
	}
	public static function updateRecord($orderid,$orderweight,$userId){
		self::initDB();
		$sql     = "UPDATE wh_shipping_order_records SET actualWeight={$orderweight},weighStaffId={$userId},weighTime=".time()." where shipOrderId={$orderid}";
		$query   = self::$dbConn->query($sql);
		if($query){
			
			return true;
		}else{
			return false;
		}
	}
	public static function updateStatus($orderid){
		self::initDB();
		$sql     = "UPDATE wh_shipping_order SET orderStatus=406 where id={$orderid}";
		$query   = self::$dbConn->query($sql);
		if($query){
			
			return true;
		}else{
			return false;
		}
	}
}
?>