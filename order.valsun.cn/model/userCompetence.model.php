<?php
/**
 * 类名：UserCompetenceModel
 * 功能：用户权限颗粒model层
 * 版本：1.0
 * 日期：2013/9/12
 * 作者：管拥军
 */
class UserCompetenceModel {
	public static $dbConn;
	//public static $prefix;
	public static $errCode	= 0;
	public static $errMsg	= "";
	public static $table_user		= "om_userCompetence";
	public static $table_account	= "om_account";
	public static $table_platform	= "om_platform";
	
	//初始化
	public static function	initDB(){
		global $dbConn;
		self::$dbConn = $dbConn;
		//self::$prefix  =  C("DB_PREFIX");
		//self::$table_user   =  self::$prefix."user_competence";;
	}
		
	/**
	 * UserCompetenceModel::Competence()
	 * 添加修改用户权限
	 * @param array $data 数组
	 * @return  bool
	 */
    public static function Competence($data){
		self::initDB();
		$sql	= array2sql($data);
		$sql 	= "REPLACE INTO ".self::$table_user." SET ".$sql;
		//echo $sql;
		$query	=	self::$dbConn->query($sql);
		if($query){
			$affectedrows = self::$dbConn->affected_rows();           
			if($affectedrows){
				return true;
			}else {
				self::$errCode	= "0002";
				self::$errMsg	= "用户权限操作失败";
				return false;
			}			
		}else {
			self::$errCode	= "0002";
			self::$errMsg	= "用户权限操作失败";
			return false;
		}
	}
	
	/**
	 * UserCompetenceModel::showCompetence($uid)
	 * 查看用户权限
	 * @param int $uid 用户id
	 * @return  array
	 */
    public static function showCompetence($uid){
		self::initDB();
		$sql = "SELECT * FROM ".self::$table_user." WHERE global_user_id = {$uid}";
		//echo $sql;
		$query	= self::$dbConn->query($sql);
		if($query){
			$affectedrows = self::$dbConn->affected_rows();           
			if($affectedrows){
				$ret	= self::$dbConn->fetch_array_all($query);
				return $ret;
			}else {
				self::$errCode	= "1059";
				self::$errMsg	= "尚未数据,暂不能修改";
				return false;
			}
		}else {
			self::$errCode	= "1060";
			self::$errMsg	= "获取数据失败";
			return false;
		}
	}
	
	public static function showCompetenceVisibleAccount($uid){
		self::initDB();
		$sql = "SELECT * FROM ".self::$table_user." WHERE global_user_id = {$uid}";
		//echo $sql;
		$query	= self::$dbConn->query($sql);
		if($query){
			$affectedrows = self::$dbConn->affected_rows();           
			if($affectedrows){
				$ret	= self::$dbConn->fetch_array($query);
				if($ret){
					$visible_platform_account = json_decode($ret['visible_platform_account'],true);
					if(is_array($visible_platform_account)){
						$visible_account = array();
						foreach($visible_platform_account as $value){
							foreach($value as $v){
								$visible_account[] = $v;
							}
						}
						return array_filter($visible_account);
					}else{
						return array();	
					}
					//return explode(',',$ret['visible_account']);
				}
				self::$errCode	= "1059";
				self::$errMsg	= "尚未数据,暂不能修改";
				return false;
			}else {
				self::$errCode	= "1059";
				self::$errMsg	= "尚未数据,暂不能修改";
				return false;
			}
		}else {
			self::$errCode	= "1060";
			self::$errMsg	= "获取数据失败";
			return false;
		}
	}
	
	public static function showCompetenceVisiblePlatform($uid){
		self::initDB();
		$sql = "SELECT * FROM ".self::$table_user." WHERE global_user_id = {$uid}";
		//echo $sql;
		$query	= self::$dbConn->query($sql);
		if($query){
			$affectedrows = self::$dbConn->affected_rows();           
			if($affectedrows){
				$ret	= self::$dbConn->fetch_array($query);
				if($ret){
					$visible_platform_account = json_decode($ret['visible_platform_account'],true);
					if(is_array($visible_platform_account)){
						return array_keys($visible_platform_account);
					}else{
						return array();	
					}
					//return explode(',',$ret['visible_platform']);
				}
				self::$errCode	= "1059";
				self::$errMsg	= "尚未数据,暂不能修改";
				return false;
			}else {
				self::$errCode	= "1059";
				self::$errMsg	= "尚未数据,暂不能修改";
				return false;
			}
		}else {
			self::$errCode	= "1060";
			self::$errMsg	= "获取数据失败";
			return false;
		}
	}
	
	/**
	 * UserCompetenceModel::listPlatform()
	 * 列出所有平台
	 * @return  array
	 */
    public static function listPlatform(){
		self::initDB();
		$sql = "SELECT id,platform FROM ".self::$table_platform." WHERE is_delete = 0";
		//echo $sql;
		$query	= self::$dbConn->query($sql);
		if($query){
			$ret	= self::$dbConn->fetch_array_all($query);
			return $ret;
		}else{
			self::$errCode	= "1060";
			self::$errMsg	= "获取数据失败";
			return false;
		}
	}
	
	/**
	 * UserCompetenceModel::listAccount()
	 * 列出某个或全部平台用户帐号
	 * @param int $pid 平台ID
	 * @return  array
	 */
    public static function listAccount($pfid){
		self::initDB();
		if($pfid==0){
			$sql = "SELECT id,account FROM ".self::$table_account." WHERE is_delete = 0";
		}else {
			$sql = "SELECT id,account FROM ".self::$table_account." WHERE platformId = {$pfid} AND is_delete = 0";
		}
		//echo $sql;
		$query	= self::$dbConn->query($sql);
		if($query){
			$ret	= self::$dbConn->fetch_array_all($query);
			return $ret;
		}else{
			self::$errCode	= "1060";
			self::$errMsg	= "获取数据失败";
			return false;
		}
	}
}
?>