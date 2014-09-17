<?php
/*
*eub授权设置
*ADD BY heminghua 
*/
class eubAccountModel{	
	public 	static $dbConn;
	public	static $errCode	=	0;
	public	static $errMsg	=	"";

	//db初始化
	public 	function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
		mysql_query('SET NAMES UTF8');
	}
	public static function selectList($where=""){
		self::initDB();
		$sql	 =	"SELECT * FROM om_eub_account {$where}";
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);
			return $ret;	//成功， 返回列表数据
		}else{
			return false;	
		}
	}
	public static function updateRecord($sql,$id){
		self::initDB();
		$sql	 =	"UPDATE om_eub_account SET {$sql} WHERE accountId={$id}";
		//echo $sql;exit;
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			
			return true;	
		}else{
			return false;	
		}
	}
	public static function selectAccount($where){
		self::initDB();
		$sql	 =	"SELECT * FROM om_account {$where}";
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);
			return $ret;	//成功， 返回列表数据
		}else{
			return false;	
		}
	}
}
?>