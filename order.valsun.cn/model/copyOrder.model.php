<?php
/*
*复制订单功能
*ADD BY heminghua 
*/
class copyOrderModel{	
	public 	static $dbConn;
	public	static $errCode	=	0;
	public	static $errMsg	=	"";

	//db初始化
	public static function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
		mysql_query('SET NAMES UTF8');
	}
	public static function selectOrder($orderid){
		self::initDB();
		$sql = "SELECT * FROM om_unshipped_order WHERE id={$orderid}";
		//echo $sql;
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->fetch_array($query);
			return $ret;
		}else{
			return false;
		}
	}
	public static function selectUser($orderid){
		self::initDB();
		$sql = "SELECT * FROM om_unshipped_order_userInfo WHERE omOrderId={$orderid}";
		//echo $sql;
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->fetch_array($query);
			return $ret;
		}else{
			return false;
		}
	}

	public static function selectDetail($orderid){
		self::initDB();
		$sql = "SELECT * FROM om_unshipped_order_detail WHERE omOrderId={$orderid}";
		//echo $sql;
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->fetch_array_all($query);
			return $ret;
		}else{
			return false;
		}
	}
	public static function selectplatform($platformId){
		self::initDB();
		$sql = "SELECT platform FROM om_platform WHERE id={$platformId}";
		//echo $sql;
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->fetch_array($query);
			return $ret['platform'];
		}else{
			return false;
		}
	}
	public static function selectAccount($accountId){
		self::initDB();
		$sql = "SELECT * FROM om_account WHERE id={$accountId}";
		//echo $sql;
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->fetch_array($query);
			return $ret;
		}else{
			return false;
		}
	}
	
	public static function selectExtension($table,$orderid){
		self::initDB();
		$sql = "SELECT * FROM {$table} WHERE omOrderId={$orderid}";
		//echo $sql;
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->fetch_array($query);
			return $ret;
		}else{
			return false;
		}
	}
	public static function selectWarehouse($orderid){
		self::initDB();
		$sql = "SELECT * FROM om_unshipped_order_warehouse WHERE omOrderId={$orderid}";
		//echo $sql;
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->fetch_array($query);
			return $ret;
		}else{
			return false;
		}
	}
	public static function selectNote($orderid){
		self::initDB();
		$sql = "SELECT * FROM om_order_notes WHERE omOrderId={$orderid}";
		//echo $sql;
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->fetch_array($query);
			return $ret;
		}else{
			return false;
		}
	}
	public static function selectStatus($status){
		self::initDB();
		$sql = "SELECT * FROM om_status_menu WHERE id={$status}";
		//echo $sql;
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->fetch_array($query);
			return $ret;
		}else{
			return false;
		}
	}
	public static function selectStatusList(){
		self::initDB();
		$sql = "SELECT * FROM om_status_menu WHERE groupId !=0 and is_delete=0";
		//echo $sql;
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->fetch_array_all($query);
			return $ret;
		}else{
			return false;
		}
	}
	public static function insertOrder($sql,$userId){
		self::initDB();
		$sql = "INSERT INTO om_unshipped_order SET {$sql}";
		//echo $sql;
		$query = self::$dbConn->query($sql);
		if($query){
			$id = mysql_insert_id();
			$ret = self::insertOperationLog($sql,$userId,2);
			if(!$ret){
				return false;
			}
			return $id;
		}else{
			return false;
		}
	}
	public static function insertUser($sql,$userId){
		self::initDB();
		$sql = "INSERT INTO om_unshipped_order_userInfo SET {$sql}";
		//echo $sql;
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self::insertOperationLog($sql,$userId,2);
			if(!$ret){
				return false;
			}
			return true;
		}else{
			return false;
		}
	}
	public static function insertDetail($sql,$userId){
		self::initDB();
		$sql = "INSERT INTO om_unshipped_order_detail SET {$sql}";
		//echo $sql;
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self::insertOperationLog($sql,$userId,2);
			if(!$ret){
				return false;
			}
			return true;
		}else{
			return false;
		}
	}
	public static function insertExtension($table,$sql,$userId){
		self::initDB();
		$sql = "INSERT INTO {$table} SET {$sql}";
		//echo $sql;
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self::insertOperationLog($sql,$userId,2);
			if(!$ret){
				return false;
			}
			return true;
		}else{
			return false;
		}
	}
	public static function insertWarehouse($sql,$userId){
		self::initDB();
		$sql = "INSERT INTO om_unshipped_order_warehouse SET {$sql}";
		//echo $sql;
		$query = self::$dbConn->query($sql);
		if($query){
			/*$ret = OrderLogModel::insertOperationLog($sql,$userId,2);
			if(!$ret){
				return false;
			}*/
			return true;
		}else{
			return false;
		}
	}
	public static function insertCopyRecord($orderid,$id,$userId){
		self::initDB();
		$sql = "INSERT INTO om_records_copyOrder SET main_order_id={$orderid},split_order_id={$id},creator={$userId},createdTime=".time()." ";
		//echo $sql;
		$query = self::$dbConn->query($sql);
		if($query){

			return true;
		}else{
			return false;
		}
	}
	public static function insertOperationLog($sql,$userId,$type){
		self::initDB();
		$sql = addslashes($sql);
		$sql = "INSERT INTO `om_operation_log_2013-09_2013-12` (operatorId,`sql`,type,createdTime) VALUES ({$userId},'{$sql}',{$type},".time().")";
		
		$query = self::$dbConn->query($sql);
		if($query){

			return true;
		}else{
			return false;
		}
	}
	public static function updateOrder($orderid){
		self::initDB();
		$sql = "UPDATE om_unshipped_order SET isCopy=1 WHERE id={$orderid}";
		//echo $sql;
		$query = self::$dbConn->query($sql);
		if($query){

			return true;
		}else{
			return false;
		}
	}
	public static function insertNote($sql,$userId){
		self::initDB();
		$sql = "INSERT INTO om_order_notes SET {$sql}";
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