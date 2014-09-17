<?php
/**
 * 类名：WebSinglePageModel
 * 功能：网站单页管理数据（CRUD）层
 * 版本：1.0
 * 日期：2014/07/16
 * 作者：管拥军
 */
 
class WebSinglePageModel{
	public static $dbConn;
	public static $errCode		= 0;
	public static $errMsg		= "";
	public static $prefix;
	private static $table		= "single_page";
		
	/**
	 * WebSinglePageModel::initDB()
	 * 返回数据库连接
	 * @return 
	 */
	public static function	initDB(){
		global $dbConn;
		self::$dbConn	= $dbConn;
		self::$prefix	= C('DB_PREFIX');
	}
	
	/**
	 * WebSinglePageModel::modList()
	 * 列出符合条件的数据并分页显示
	 * @param string $where 查询条件
	 * @param integer $page 页码
	 * @param integer $pagenum 每页个数
	 * @return array 结果集数组
	 */
	public static function modList($where, $page, $pagenum){
		self::initDB();
		$start		= ($page-1)*$pagenum;
		$sql		= "SELECT *	FROM ".self::$prefix.self::$table."
						WHERE {$where} AND is_delete = 0
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
	 * WebSinglePageModel::modListCount()
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
	 * WebSinglePageModel::modModify()
	 * 返回某个网站单页的信息
	 * @param integer $id ID
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
	 * WebSinglePageModel::addWebSinglePage()
	 * 添加网站单页信息保存到数据库
	 * @param array $data 数据集
	 * @return array 结果集数组
	 */
	public static function addWebSinglePage($data){
		self::initDB();
		$res	= 0;
		$where	= "(topic = '{$data['topic']}' AND is_delete = 0)"; 
        $res	= self::modListCount($where);
		if($res > 0) {
			self::$errCode	= 10002;
			self::$errMsg	= "添加失败：当前网站单页名称已存在！";
			return false;
		}
		$sql 		= arrayTsql($data);
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
	 * WebSinglePageModel::updateWebSinglePage()
	 * 更新网站单页信息
	 * @param integer $id ID
	 * @param array $data 数据集
	 * @return array 结果集数组
	 */
	public static function updateWebSinglePage($id, $data){
		self::initDB();
		$res	= 0;
		$where	= "id <> $id AND (topic = '{$data['topic']}' AND is_delete = 0)"; 
        $res	= self::modListCount($where);
		if($res > 0) {
			self::$errCode	= 20000;
			self::$errMsg	= "更新失败：当前网站单页名称已存在！";
			return false;
		}
		$sql 	= arrayTsql($data);
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
	 * WebSinglePageModel::delWebSinglePage()
	 * 网站单页删除
	 * @param integer $id ID
	 * @return bool
	 */
	public static function delWebSinglePage($id){
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
}
?>