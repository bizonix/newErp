<?php
class partitionManageModel{
	public 	static $dbConn;
	public	static $errCode	=	0;
	public	static $errMsg	=	"";
	//static  $table			=	"trans_platform";

	//db初始化
	public 	function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
	}

	public static function partitionShowByWhere($where){
		self::initDB();

		$sql	 =	"SELECT * FROM trans_partition {$where}";
		
		$query	 =	self::$dbConn->query($sql);
	
		if($query){
			$ret = self::$dbConn->fetch_array_all($query);
			return $ret;	
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"die";
			return false;
		}	
	}	
	public static function insertPartition($where){
		self::initDB();

		$sql	 =	"INSERT INTO trans_partition {$where}";
		
		$query	 =	self::$dbConn->query($sql);
	
		if($query){
			
			return mysql_insert_id();	
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"die";
			return false;
		}	
	}
	public static function updatePartition($channelId,$partitionName,$countries,$returnAddress,$enable,$id){
		self::initDB();

		$sql	 =	"UPDATE trans_partition SET channelId={$channelId},partitionName='{$partitionName}',countries='{$countries}',returnAddress='{$returnAddress}',enable={$enable},lastmodified=".time()." where id={$id}";
		$query	 =	self::$dbConn->query($sql);
	
		if($query){
			
			return mysql_insert_id();	
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"die";
			return false;
		}	
	}
}

?>