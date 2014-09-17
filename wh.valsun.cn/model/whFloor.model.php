<?php
/*
*楼层相关信息
*add by :herman.xi
*/
class WhFloorModel{
	public 	static $dbConn;
	public	static $errCode	=	0;
	public	static $errMsg	=	"";
	static  $table			=	"wh_floor";
	
	
	//db初始化
	public 	function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
		mysql_query('SET NAMES UTF8');
	}
	
	//获取条件楼层列表
	public function getFloorList($select,$where){
		self::initDB();
		$sql	 =	"select {$select} from ".self::$table." {$where} ";
		$query	 =	self::$dbConn->query($sql);	
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);
			return $ret;	//成功， 返回列表数据
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"error";
			return false;	
		}
	}
	
	//获取条件楼层Code
	public function getFloorWhCode($id){
		self::initDB();
		$sql	 =	"select whCode from ".self::$table." WHERE id = {$id} ";
		$query	 =	self::$dbConn->query($sql);	
		if($query){
			$ret =self::$dbConn->fetch_array($query);
			return $ret['whCode'];	//成功， 返回列表数据
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"error";
			return false;	
		}
	}
	
	//获取条件楼层Code
	public function addFloor($data){
		self::initDB();
		$extralsql 	= 	array2sql($data);
		$sql	 	=	"INSERT ".self::$table." SET {$extralsql} ";
		$query	 	=	self::$dbConn->query($sql);	
		if($query){
			self::$errCode =	"200";
			self::$errMsg  =	"success";
			return true;
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"error";
			return false;	
		}
	}
}