<?php

class addNewStatusModel{	
	public 	static $dbConn;
	public	static $errCode	=	0;
	public	static $errMsg	=	"";
	
	
	
	//db初始化
	public 	function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
		mysql_query('SET NAMES UTF8');
	}
	
	public 	static function insertRecord($statusName,$statusCode,$statusGroup,$note){
		self::initDB();
		$sql	 =	"insert into wh_storage_status(statusName,statusCode,groupId,note) values('{$statusName}',{$statusCode},{$statusGroup},'{$note}')";
		//echo $sql;
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			
			return true;	
		}else{
			return false;	
		}
	}
	public 	static function selectGroup(){
		self::initDB();
		$sql	 =	"SELECT * FROM wh_storage_status_group";
		//echo $sql;
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			$ret = self::$dbConn->fetch_array_all($query);
			return $ret;	
		}else{
			return false;	
		}
	}
	public 	static function selectRecord($where){
		self::initDB();
		$sql	 =	"SELECT * FROM wh_storage_status {$where}";
		//echo $sql;
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			$ret = self::$dbConn->fetch_array_all($query);
			return $ret;	
		}else{
			return false;	
		}
	}
}
?>	
	