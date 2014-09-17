<?php
/**
 * 类名：ApiCompetenceModel
 * 功能：API开放授权管理数据（CRUD）层
 * 版本：1.0
 * 日期：2014/07/10
 * 作者：管拥军
 */
 
class ApiCompetenceModel{
	public static $dbConn;
	public static $errCode		= 0;
	public static $errMsg		= "";
	public static $prefix;
	private static $table		= "open_api";
	private static $table_user	= "power_global_user";
		
	/**
	 * ApiCompetenceModel::initDB()
	 * 返回数据库连接
	 * @return 
	 */
	public static function	initDB(){
		global $dbConn;
		self::$dbConn	= $dbConn;
		self::$prefix	= C('DB_PREFIX');
	}
	
	/**
	 * ApiCompetenceModel::modList()
	 * 列出符合条件的数据并分页显示
	 * @param string $where 查询条件
	 * @param integer $page 页码
	 * @param integer $pagenum 每页个数
	 * @return array 结果集数组
	 */
	public static function modList($where, $page, $pagenum){
		self::initDB();
		$start		= ($page-1)*$pagenum;
		$sql		= "SELECT
						a.*,
						b.global_user_name,
						b.global_user_register_time
						FROM
						".self::$prefix.self::$table." AS a
						INNER JOIN ".self::$table_user." AS b ON a.apiUid = b.global_user_id
						WHERE {$where} AND a.is_delete = 0
						LIMIT {$start},{$pagenum}";
		$query		= self::$dbConn->query($sql);
		if($query) {
			$res	= self::$dbConn->fetch_array_all($query);
			return $res;
		} else {
			self::$errCode	= 90000;
			self::$errMsg	= "获取数据失败";
			return false;
		}
	}
	
	/**
	 * ApiCompetenceModel::modListCount()
	 * 返回某个条件结果统计的总数
	 * @param string $where 查询条件
	 * @return integer 总数量 
	 */
	public static function modListCount($where){
		self::initDB();
		$sql 		= "SELECT count(*) FROM ".self::$prefix.self::$table." WHERE {$where} AND is_delete = 0";
		$query		= self::$dbConn->query($sql);
		if($query) {
			$data	= self::$dbConn->fetch_row($query);
			return $data[0];
		} else {
			self::$errCode	= 90000;
			self::$errMsg	= "获取数据失败";
			return 0;
		}
	}
	
	/**
	 * ApiCompetenceModel::modModify()
	 * 返回某个API开放授权的信息
	 * @param integer $id 用户权限ID
	 * @return array 结果集数组
	 */
	public static function modModify($id){
		self::initDB();
		$sql 		= "SELECT * FROM ".self::$prefix.self::$table." WHERE id = '{$id}' AND is_delete = 0 LIMIT 1";
		$query		= self::$dbConn->query($sql);
		if($query) {
			$res	= self::$dbConn->fetch_array($query);
			return $res;
		} else {
			self::$errCode	= 90000;
			self::$errMsg	= "获取数据失败";
			return false;
		}
	}
	
	/**
	 * ApiCompetenceModel::getApiInfoByToken()
	 * 返回某个api token 的接口信息
	 * @param string $apiToken api token
	 * @return array 结果集数组
	 */
	public static function getApiInfoByToken($apiToken){
		self::initDB();
		$sql 		= "SELECT * FROM ".self::$prefix.self::$table." WHERE apiToken = '{$apiToken}' AND is_delete = 0 LIMIT 1";
		$query		= self::$dbConn->query($sql);
		if($query) {
			$res	= self::$dbConn->fetch_array($query);
			return $res;
		} else {
			self::$errCode	= 90000;
			self::$errMsg	= "获取数据失败";
			return false;
		}
	}
	
	/**
	 * ApiCompetenceModel::addApiCompetence()
	 * 添加API开放授权信息保存到数据库
	 * @param array $data 数据集
	 * @return array 结果集数组
	 */
	public static function addApiCompetence($data){
		self::initDB();
		$res	= 0;
		$where	= "(apiName = '{$data['apiName']}' AND apiUid = '{$data['apiUid']}')"; 
        $res	= self::modListCount($where);
		if($res > 0) {
			self::$errCode	= 10002;
			self::$errMsg	= "添加失败：当前用户当前API开放授权接口已存在！";
			return false;
		}
		$sql 		= array2sql($data);
		$sql 		= "INSERT INTO `".self::$prefix.self::$table."` SET ".$sql; 
		$query		= self::$dbConn->query($sql);
		if($query) {
			$rows 	= self::$dbConn->affected_rows();           
			if($rows) {
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
	 * ApiCompetenceModel::updateApiCompetence()
	 * 更新API开放授权信息
	 * @param integer $id 用户权限ID
	 * @param array $data 数据集
	 * @return array 结果集数组
	 */
	public static function updateApiCompetence($id, $data){
		self::initDB();
		$res	= 0;
		$where	= "id <> $id AND (apiName = '{$data['apiName']}' AND apiUid = '{$data['apiUid']}')"; 
        $res	= self::modListCount($where);
		if($res > 0) {
			self::$errCode	= 20000;
			self::$errMsg	= "更新失败：当前用户当前API开放授权接口已存在！";
			return false;
		}
		$sql 	= array2sql($data);
		$sql 	= "UPDATE `".self::$prefix.self::$table."` SET ".$sql." WHERE id = {$id}"; 
		$query	= self::$dbConn->query($sql);
		if($query) {
			return true;
		} else {
			self::$errCode	= 20001;
			self::$errMsg	= "执行SQL语句出错";
			return false;
		}
	}

	/**
	 * ApiCompetenceModel::delApiCompetence()
	 * API开放授权删除
	 * @param integer $id 权限ID
	 * @return bool
	 */
	public static function delApiCompetence($id){
		self::initDB();
		$sql		= "UPDATE `".self::$prefix.self::$table."` SET is_delete = 1 WHERE id = {$id}";
		$query		= self::$dbConn->query($sql);
		if($query) {
			$rows 	= self::$dbConn->affected_rows(); 
			if($rows) {
				return $res;
			} else {
				self::$errCode	= 10001;
				self::$errMsg	= "删除数据失败";
				return false;
			}
		} else {
            self::$errCode		= 10000;
			self::$errMsg		= "执行SQL语句失败！";
			return false;
		}
	}	

	/**
	 * ApiCompetenceModel::getGlobalUser()
	 * 获取一个或多个统一用户信息
	 * @param integer $uid 用户ID
	 * @return array
	 */
	public static function getGlobalUser($uid=0){
		self::initDB();
		$condition	= "1";
		if(!empty($uid)) $condition .= " AND a.global_user_id = {$uid}";
		$sql 		= "SELECT a.global_user_id,a.global_user_name,b.company_name FROM `power_global_user` as a
						LEFT JOIN `power_company` as b ON a.global_user_company = b.company_id
						WHERE {$condition} AND a.global_user_is_delete = 0";
		$query		= self::$dbConn->query($sql);
		if($query) {
			return self::$dbConn->fetch_array_all($query);
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "执行SQL语句出错";
			return false;
		}
	}
	
	/**
	 * ApiCompetenceModel::getApiCompetenceById()
	 * 获取某个用户API开放授权信息
	 * @param integer $gid 用户GID
	 * @return array
	 */
	public static function getApiCompetenceById($gid){
		self::initDB();
		$sql 	= "SELECT * FROM `".self::$prefix.self::$table."` WHERE apiUid = {$gid}"; 
		$query	= self::$dbConn->query($sql);
		if($query) {
			return self::$dbConn->fetch_array_array($query);
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "执行SQL语句出错";
			return false;
		}
	}
}
?>