<?php
/*
*DresslinkinfoModel
*ADD BY herman.xi @20140604
*/
class DresslinkinfoModel{	
	public 	static $dbConn;
	public	static $errCode	=	0;
	public	static $errMsg	=	"";
	static  $table			=	"om_dresslink_info";
	
	//db初始化
	public 	function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
		mysql_query('SET NAMES UTF8');
	}
	
	//获取订单ids列表
	public 	static function getDresslinkinfoList($select,$where){
		self::initDB();
		$sql	 =	"select {$select} from ".self::$table." {$where} ";
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);
			self::$errCode =	"200";
			self::$errMsg  =	"success";
			return $ret;	//成功， 返回列表数据
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"error";
			return false;	
		}
	}
	
	//判断是否存在
	public 	static function judgeDresslinkinfoList($select,$where){
		self::initDB();
		$sql	 =	"select {$select} from ".self::$table." {$where} ";
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);
			if(count($ret) == 0){
				self::$errCode =	"002";
				self::$errMsg  =	"empty";
				return false;	
			}
			//return $ret;	//成功， 返回列表数据
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"error";
			return false;	
		}
		self::$errCode =	"200";
		self::$errMsg  =	"success";
		return true;
	}
	
	/**
	 * 更新一条或多条记录，暂只支持一维数组
	 * @para $data as array
	 $ @where as String
	 */
	public static function updateDresslinkinfoList($data,$where = ""){
		self::initDB();
		$field = array2sql($data);
		$sql	= "UPDATE `".self::$table."` SET ".$field." WHERE 1 ".$where;
		$query	=	self::$dbConn->query($sql);
		if($query){
			self::$errCode =	"200";
			self::$errMsg  =	"success";                          
			return true;
		} else {
			self::$errCode =	"004";
			self::$errMsg  =	"error";
			return false;
		}
	}

	/**
	 * 插入一条记录
	 * @para $data as array
	 * return insert_id
	 */
	public static function insertDresslinkinfoList($data){
		self::initDB();
        $sql = array2sql($data);
		$sql = "INSERT INTO `".self::$table."` SET ".$sql;
		//echo $sql;
		$query	=	self::$dbConn->query($sql);
		if($query){
			self::$errCode =	"200";
			self::$errMsg  =	"success";
			return true;
		}else{
			self::$errCode	=	"003";
			self::$errMsg	=	"error";
			return false;
		}
	}

}
?>