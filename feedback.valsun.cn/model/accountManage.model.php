<?php
class AccountManageModel{
	public 	static $dbConn;
	public	static $errCode		=	0;
	public	static $errMsg		=	"";
	static  $table				=	"fb_account_power";
	//db初始化
	public 	function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
		mysql_query('SET NAMES UTF8');
	}
	public static function getAccoutPower($select,$where){
		$table    = self::$table;
		self::initDB();
		$sql      = " select $select from $table $where ";
		$query    = self::$dbConn->query($sql);
		if($query){
			$res    = self::$dbConn->fetch_array_all($query);
			return $res;
		}else{
			self::$errCode    = "002";
			self::$errMsg     = "sql error";
			return false;
		}
	}
	public static function saveAccoutPower($set,$where){
		$table    = self::$table;
		self::initDB();
		$sql      = " select id from $table where $where ";
		$query    = self::$dbConn->query($sql);
		if($query){
			$res    = self::$dbConn->fetch_array_all($query);
			if(count($res)>0){
				$update    = "update $table $set where $where";
				$query     = self::$dbConn->query($update);
				if(!$query){
					self::$errCode    = "002";
					self::$errMsg     = "sql error";
					return false;
				}
			}else{
				$set       .= ",$where ";
				$insert    = "insert into $table $set";
				$query     = self::$dbConn->query($insert);
				if(!$query){
					self::$errCode    = "002";
					self::$errMsg     = "sql error";
					return false;
				}
			}
			self::$errCode    = "200";
			self::$errMsg     = "success";
			return true;
		}else{
			self::$errCode    = "002";
			self::$errMsg     = "sql error";
			return false;
		}
	}
}
?>