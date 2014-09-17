<?php
/**
 * 类名：UserCompetenceModel
 * 功能：用户开放授权管理数据（CRUD）层
 * 版本：1.0
 * 日期：2014/04/19
 * 作者：管拥军
 */
 
class UserCompetenceModel{
	public static $dbConn;
	public static $errCode		= 0;
	public static $errMsg		= "";
	public static $prefix;
	private static $table		= "user_competence";
	private static $table_user	= "power_global_user";
		
	/**
	 * UserCompetenceModel::initDB()
	 * 返回数据库连接
	 * @return 
	 */
	public static function	initDB(){
		global $dbConn;
		self::$dbConn	= $dbConn;
		self::$prefix	= C('DB_PREFIX');
	}
	
	/**
	 * UserCompetenceModel::modList()
	 * 列出符合条件的数据并分页显示
	 * @param string $where 查询条件
	 * @param integer $page 页码
	 * @param integer $pagenum 每页个数
	 * @return array 结果集数组
	 */
	public static function modList($where, $page, $pagenum){
		self::initDB();
		$start	= ($page-1)*$pagenum;
		$sql 	= "SELECT a.*,b.global_user_name,b.global_user_register_time FROM ".self::$prefix.self::$table." as a 
					LEFT JOIN `power_global_user` as b on a.gid = b.global_user_id
					WHERE $where AND a.is_delete = 0 LIMIT $start,$pagenum";
		$query	= self::$dbConn->query($sql);
		if ($query) {
			$res	= self::$dbConn->fetch_array_all($query);
			return $res;
		} else {
			self::$errCode	= 90000;
			self::$errMsg	= "获取数据失败";
			return false;
		}
	}
	
	/**
	 * UserCompetenceModel::modListCount()
	 * 返回某个条件结果统计的总数
	 * @param string $where 查询条件
	 * @return integer 总数量 
	 */
	public static function modListCount($where){
		self::initDB();
		$sql = "SELECT count(*)	FROM ".self::$prefix.self::$table." WHERE $where AND is_delete = 0";
		$query	= self::$dbConn->query($sql);
		if ($result=self::$dbConn->query($sql)) {
			$data=self::$dbConn->fetch_row($result);
			return $data[0];
		} else {
			self::$errCode	= 90000;
			self::$errMsg	= "获取数据失败";
			return 0;
		}
	}
	
	/**
	 * UserCompetenceModel::modModify()
	 * 返回某个渠道的信息
	 * @param integer $id 用户权限ID
	 * @return array 结果集数组
	 */
	public static function modModify($id){
		self::initDB();
		$sql 	= "SELECT * FROM ".self::$prefix.self::$table." WHERE gid = {$id} AND is_delete = 0 LIMIT 1";
		$query	= self::$dbConn->query($sql);
		if ($query) {
			$res	= self::$dbConn->fetch_array($query);
			return $res;
		} else {
			self::$errCode	= 90000;
			self::$errMsg	= "获取数据失败";
			return false;
		}
	}
	
	/**
	 * UserCompetenceModel::addUserCompetence()
	 * 添加用户开放权限信息保存到数据库
	 * @param array $data 数据集
	 * @return array 结果集数组
	 */
	public static function addUserCompetence($data){
		self::initDB();
		$sql 	= "";
		$values	= array();
		foreach ($data as $key=>$v) {
			array_push($values,"{$key} = '{$v}'");
		}
		$sql	= implode(",",$values);
		$sql 	= "INSERT INTO `".self::$prefix.self::$table."` SET ".$sql; 
		$query	= self::$dbConn->query($sql);
		if ($query) {
			$rows 	= self::$dbConn->affected_rows();           
			if ($rows) {
				return $rows;
			} else {
				self::$errCode	= 10001;
				self::$errMsg	= "插入数据失败";
				return false;
			}
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "执行SQL语句出错";
			return false;
		}
	}
	
	/**
	 * UserCompetenceModel::updateUserCompetence()
	 * 更新用户开放权限信息
	 * @param integer $gid 用户权限ID
	 * @param array $data 数据集
	 * @return array 结果集数组
	 */
	public static function updateUserCompetence($gid, $data){
		self::initDB();
		$sql 	= "";
		$values	= array();
		foreach ($data as $key=>$v) {
			array_push($values,"{$key} = '{$v}'");
		}
		$sql	= implode(",",$values);
		$sql 	= "UPDATE `".self::$prefix.self::$table."` SET ".$sql." WHERE gid = {$gid}"; 
		$query	= self::$dbConn->query($sql);
		if ($query) {
			return true;
		} else {
			self::$errCode	= 20000;
			self::$errMsg	= "执行SQL语句出错";
			return false;
		}
	}

	/**
	 * UserCompetenceModel::delUserCompetence()
	 * 用户开放权限删除
	 * @param integer $gid 权限ID
	 * @return bool
	 */
	public static function delUserCompetence($gid){
		self::initDB();
		$res 	= self::getCompetenceById($gid);
		if (!$res) {
			self::$errCode	= 10000;
			self::$errMsg	= "不存在的用户权限ID";
			return false;
		}
		$sql	= "DELETE FROM `".self::$prefix.self::$table."` WHERE gid = {$gid}";
		$query	= self::$dbConn->query($sql);
		if ($query) {
			$rows 	= self::$dbConn->affected_rows(); 
			if ($rows) {
				return $rows;
			} else {
				self::$errCode	= 10002;
				self::$errMsg	= "删除数据失败";
				return false;
			}
		} else {
            self::$errCode	= 10003;
			self::$errMsg	= "执行SQL语句失败！";
			return false;
		}
	}	

	/**
	 * UserCompetenceModel::getGlobalUser()
	 * 获取一个或多个统一用户信息
	 * @param integer $uid 用户ID
	 * @return array
	 */
	public static function getGlobalUser($uid=0){
		self::initDB();
		$condition	= "1";
		if (!empty($uid)) $condition .= " AND a.global_user_id = {$uid}";
		$sql 	= "SELECT a.global_user_id,a.global_user_name,b.company_name FROM `power_global_user` as a
					LEFT JOIN `power_company` as b ON a.global_user_company = b.company_id
					WHERE {$condition} AND a.global_user_is_delete = 0";
		$query	= self::$dbConn->query($sql);
		if ($query) {
			return self::$dbConn->fetch_array_all($query);
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "执行SQL语句出错";
			return false;
		}
	}
	
	/**
	 * UserCompetenceModel::getCompetenceById()
	 * 获取某个用户开放权限ID信息
	 * @param integer $gid 权限ID
	 * @return array
	 */
	public static function getCompetenceById($gid){
		self::initDB();
		$sql 	= "SELECT * FROM `".self::$prefix.self::$table."` WHERE gid = {$gid}"; 
		$query	= self::$dbConn->query($sql);
		if ($query) {
			return self::$dbConn->fetch_array($query);
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "执行SQL语句出错";
			return false;
		}
	}
}
?>