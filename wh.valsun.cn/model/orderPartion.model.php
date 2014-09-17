<?php
/*
*分区操作(model)
*add by heminghua
*
*/
class orderPartionModel{
	public 	static $dbConn;
	public	static $errCode	=	0;
	public	static $errMsg	=	"";

	//db初始化
	public 	function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
		mysql_query('SET NAMES UTF8');
	}
	public static function insertPrintRecord($partion,$userId){
		self::initDB();
		$sql	 =	"INSERT INTO wh_order_partion_print(partion,printUserId,printTime) values ('{$partion}',{$userId},".time().")";
		//echo $sql;
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			
			return mysql_insert_id();	
		}else{
			return false;	
		}
	}
	
	public static function selectOrder($where){
		self::initDB();
		$sql	 =	"SELECT  * FROM wh_shipping_order {$where}";
		//echo $sql;
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			$ret =  self::$dbConn->fetch_array_all($query);
			return $ret;	
		}else{
			return false;	
		}
	}
	
	public static function selectPartionRecord($orderid){
		self::initDB();
		$sql	 =	"SELECT  * FROM wh_order_partion_records WHERE shipOrderId={$orderid}";
		//echo $sql;
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			$ret =  self::$dbConn->fetch_array_all($query);
			return $ret;	
		}else{
			return false;	
		}
	}
	
	public static function selectWeight($orderid){
		self::initDB();
		$sql	 =	"SELECT actualWeight FROM wh_shipping_order_records WHERE shipOrderId={$orderid}";
		//echo $sql;
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			$ret =  self::$dbConn->fetch_array_all($query);
			return $ret[0]['actualWeight'];	
		}else{
			return false;	
		}
		
		
	}
	
	public static function insertRecord($orderid,$partion,$weight,$userId){
		self::initDB();
		$sql	 =	"INSERT INTO wh_order_partion_records(shipOrderId,weight,partion,scanUserId,scanTime) values ({$orderid},{$weight},'{$partion}',{$userId},".time().")";
		//echo $sql;exit;
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			
			return mysql_insert_id();	
		}else{
			return false;	
		}
	}
	
	public static function updateOrderRecords($orderid,$userId){
		self::initDB();
		$sql	 =	"UPDATE wh_shipping_order_records SET districtStaffId={$userId},districtTime=".time()." where shipOrderId={$orderid} ";
		//echo $sql;
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			
			return true;;	
		}else{
			return false;	
		}
	
	}
	
	public static function updateOrderStatus($orderid){
		self::initDB();
		$sql	 =	"UPDATE wh_shipping_order SET orderStatus=501 where id={$orderid} ";
		//echo $sql;
		$query	 =	self::$dbConn->query($sql);		
		if($query){		
			return true;;	
		}else{
			return false;	
		}
	
	}
	
	public static function selectPartionPack($packageId){
		self::initDB();
		$sql	 =	"SELECT partion FROM wh_order_partion_print WHERE id='{$packageId}'";
		//echo $sql;exit;
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			$ret =  self::$dbConn->fetch_array_all($query);
			return $ret;	
		}else{
			return false;	
		}
	}
	
	public static function updatePartionRecord($partion,$userId,$packageId){
		self::initDB();
		$sql	 =	"UPDATE wh_order_partion_records SET packageId={$packageId},modifyTime=".time()." where partion='{$partion}' AND packageId is null AND scanUserId={$userId}";
		//echo $sql;
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			
			return true;;	
		}else{
			return false;	
		}
	
	}
	
	public static function updatePartionPack($packageId,$num,$weight,$userId){
		self::initDB();
		$sql	 =	"UPDATE wh_order_partion_print SET totalWeight={$weight},totalNum={$num},status=1,modifyTime=".time()." where id={$packageId}";
		//echo $sql;exit;
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			
			return true;;	
		}else{
			return false;	
		}
	
	}
	
	public static function selectData($where){
		self::initDB();
		$sql	 =	"SELECT  sum(weight) as totalWeight,count(*) as totalNum FROM wh_order_partion_records {$where}";
		//echo $sql;
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			$ret =  self::$dbConn->fetch_array_all($query);
			return $ret;	
		}else{
			return false;	
		}
	}
	
	public static function selectUserPartion($where){
		self::initDB();
		$sql	 =	"SELECT DISTINCT partion FROM wh_order_partion_records {$where}";
		//echo $sql;
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			$ret =  self::$dbConn->fetch_array_all($query);
			return $ret;	
		}else{
			return false;	
		}
	}
}
?>