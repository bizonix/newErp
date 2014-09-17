<?php
/**
 * 类名：UserCompetenceModel
 * 功能：用户权限颗粒model层
 * 版本：1.0
 * 日期：2013/11/13
 * 作者：管拥军
 */
 
class UserCompetenceModel {
	public static $dbConn;
	public static $prefix;
	public static $errCode		= 0;
	public static $errMsg		= "";
	public static $table_user	= "purchases_access";
	
	//初始化
	public static function	initDB(){
		global $dbConn;
		self::$dbConn = $dbConn;
		self::$prefix  =  C("DB_PREFIX");
	}
		
	/**
	 * UserCompetenceModel::Competence()
	 * 添加修改用户权限
	 * @param array $data 数组
	 * @return  bool
	 */
    public static function competence($data){
		self::initDB();
		$sql	= array2sql($data);
		$sql 	= "REPLACE INTO ".self::$prefix.self::$table_user." SET ".$sql;               
		$query	=	self::$dbConn->query($sql);
		if ($query) {
			$affectedrows = self::$dbConn->affected_rows();           
			if ($affectedrows) {
				return true;
			}else {
				self::$errCode	= "10000";
				self::$errMsg	= "用户权限操作失败";
				return false;
			}			
		}else {
			self::$errCode	= "10001";
			self::$errMsg	= "执行SQL语句失败";
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
		$sql = "SELECT * FROM ".self::$prefix.self::$table_user." WHERE user_id = {$uid}";
		$query	= self::$dbConn->query($sql);
		if ($query) {
			$affectedrows = self::$dbConn->affected_rows();           
			if ($affectedrows) {
				$ret	= self::$dbConn->fetch_array_all($query);
				return $ret;
			} else {
				self::$errCode	= "10000";
				self::$errMsg	= "尚未数据,暂不能修改";
				return false;
			}
		} else {
			self::$errCode	= "10001";
			self::$errMsg	= "获取数据失败";
			return false;
		}
	}	
}
?>