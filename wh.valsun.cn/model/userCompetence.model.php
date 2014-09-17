<?php
/*
*权限设置(model)
*
*/
class UserCompetenceModel{
	public 	static $dbConn;
	public	static $errCode	=	0;
	public	static $errMsg	=	"";

	//db初始化
	public 	function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
		mysql_query('SET NAMES UTF8');
	}
	
	public static function insertCarrierData($uid,$shippingids){
		self::initDB();
		$s_sql   = "select * from wh_userCompetence where globalUserId={$uid}";
		$s_query = self::$dbConn->query($s_sql);
		$s_ret 	 = self::$dbConn->fetch_array_all($s_query);
		
		if(empty($s_ret)){
			$u_sql = "insert into wh_userCompetence(globalUserId,visibleCarrier) values ({$uid},'{$shippingids}')";
		}else{
			$u_sql = "update wh_userCompetence set visibleCarrier='{$shippingids}' where globalUserId={$uid}";
		}
		$query = self::$dbConn->query($u_sql);		
		if($query){
			return true;
		}else{
			return false;	
		}
	}
	
	public static function insertPlatData($uid,$data){
		self::initDB();
		$s_sql   = "select * from wh_userCompetence where globalUserId={$uid}";
		$s_query = self::$dbConn->query($s_sql);
		$s_ret 	 = self::$dbConn->fetch_array_all($s_query);
		
		if(empty($s_ret)){
			$u_sql = "insert into wh_userCompetence(globalUserId,visiblePlatformAccount) values ({$uid},'{$data}')";
		}else{
			$u_sql = "update wh_userCompetence set visiblePlatformAccount='{$data}' where globalUserId={$uid}";
		}
		
		$query = self::$dbConn->query($u_sql);		
		if($query){
			return true;
		}else{
			return false;	
		}
	}
	
	public static function selectUserCompetenceInfo($uid){
		self::initDB();
		$sql	 =	"SELECT  * FROM wh_userCompetence where globalUserId={$uid}";
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			$ret =  self::$dbConn->fetch_array_all($query);
			return $ret;	
		}else{
			return false;	
		}
	}
	
	public static function showCompetenceVisibleShip($uid){
		self::initDB();
		$sql    = "SELECT  * FROM wh_userCompetence where globalUserId={$uid}";
		$query	= self::$dbConn->query($sql);
		if($query){
			$data = array();
			$ret  = self::$dbConn->fetch_array($query);
			if(!empty($ret)){
				if(!empty($ret['visibleCarrier'])){
					$data = explode(',',$ret['visibleCarrier']);
				}
			}
			return $data;
		}else {
			self::$errCode	= "401";
			self::$errMsg	= "获取数据失败";
			return false;
		}
	}
	
	//获取账号
	public static function showCompetenceVisibleAccount($uid){
		self::initDB();
		$sql    = "SELECT  * FROM wh_userCompetence where globalUserId={$uid}";
		$query	= self::$dbConn->query($sql);
		if($query){	
			$data = array();
			$ret = self::$dbConn->fetch_array($query);
			if(!empty($ret)){
				$data = array();
				if(!empty($ret['visiblePlatformAccount'])){
					$visible_platform_account = json_decode($ret['visiblePlatformAccount'],true);
					foreach($visible_platform_account as $value){
						foreach($value as $v){
							$visible_account[] = $v;
						}
					}
					$data = array_filter($visible_account);
				}
			}
			return $data;
		}else {
			self::$errCode	= "401";
			self::$errMsg	= "获取数据失败";
			return false;
		}
	}
	
	
	//获取平台
	public static function getCompetenceVisiblePlat($uid){
		self::initDB();
		$sql    = "SELECT  * FROM wh_userCompetence where globalUserId={$uid}";
		$query	= self::$dbConn->query($sql);
		if($query){	
			$data = array();
			$ret = self::$dbConn->fetch_array($query);
			if(!empty($ret)){
				$data = array();
				if(!empty($ret['visiblePlatformAccount'])){
					$visible_platform_account = json_decode($ret['visiblePlatformAccount'],true);
					foreach($visible_platform_account as $key=>$value){
							$visible_account[] = $key;
					}
					$data = array_filter($visible_account);
				}
			}
			return $data;
		}else {
			self::$errCode	= "401";
			self::$errMsg	= "获取数据失败";
			return false;
		}
	}
}
?>