<?php
/*
*合并包裹功能
*ADD BY heminghua 
*/
class cancelCombineModel{	
	public 	static $dbConn;
	public	static $errCode	=	0;
	public	static $errMsg	=	"";

	//db初始化
	public static function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
		mysql_query('SET NAMES UTF8');
	}
	public static function selectCombineOrder($str){
		self::initDB();
		$sql = "SELECT id,combinePackage FROM om_unshipped_order WHERE combinePackage !=0 AND id in ({$str})";
		//echo $sql;
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->fetch_array_all($query);
			return $ret;
		}else{
			return false;
		}
	}
	public static function selectSonOrder($mainorder){
		self::initDB();
		$sql = "SELECT split_order_id FROM om_records_combinePackage WHERE  main_order_id={$mainorder} AND is_enable=1";
		//echo $sql;
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->fetch_array_all($query);
			return $ret;
		}else{
			return false;
		}
	}
	public static function selectMainOrder($sonorder){
		self::initDB();
		$sql = "SELECT main_order_id FROM om_records_combinePackage WHERE  split_order_id={$sonorder}";
		//echo $sql;
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->fetch_array_all($query);
			return $ret[0]['main_order_id'];
		}else{
			return false;
		}
	}
	public static function selectRecord($orderid){
		self::initDB();
		$sql = "SELECT id,combinePackage FROM om_unshipped_order WHERE id={$orderid}";
		//echo $sql;
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->fetch_array_all($query);
			return $ret;
		}else{
			return false;
		}
	}
	public static function updateOrder($orderid){
		self::initDB();
		$sql = "UPDATE om_unshipped_order SET orderStatus=".C('STATEPENDING').", orderType=".C('STATEPENDING_CONV').", combinePackage=0 WHERE id={$orderid}";
		//echo $sql; echo "<br>";
		$query = self::$dbConn->query($sql);
		if($query){
			OrderLogModel::orderLog($orderid,$sql,"更新订单");
			return true;
		}else{
			return false;
		}
	}
	public static function updateRecords($orderid,$userId,$info=""){
		self::initDB();
		if($info=="son"){
			$sql = "UPDATE om_records_combinePackage SET is_enable=0,cancelTime=".time().",cancelUser={$userId} WHERE split_order_id={$orderid}";
		}else{
			$sql = "UPDATE om_records_combinePackage SET is_enable=0,cancelTime=".time().",cancelUser={$userId} WHERE main_order_id={$orderid}";
		}

		//echo $sql;
		$query = self::$dbConn->query($sql);
		if($query){
			
			return true;
		}else{
			return false;
		}
	}
}
?>