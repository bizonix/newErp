<?php
/**
 * 类名：UserCompetencesModel
 * 功能：开放授权管理数据（CRUD）层
 * 版本：1.0
 * 日期：2014/04/19
 * 作者：管拥军
 */
 
class UserCompetencesModel{
	public static $dbConn;
	public static $errCode		= 0;
	public static $errMsg		= "";
	public static $prefix;
	private static $table		= "user_competences";
		
	/**
	 * UserCompetencesModel::initDB()
	 * 返回数据库连接
	 * @return 
	 */
	public static function	initDB(){
		global $dbConn;
		self::$dbConn	= $dbConn;
		self::$prefix	= C('DB_PREFIX');
	}
	
	/**
	 * UserCompetencesModel::getCompetencesByCat()
	 * 获取某个分类的开放权限信息
	 * @param integer $cid 待定
	 * @return array 结果集数组
	 */
	public static function getCompetencesByCat($cid=0){
		self::initDB();
		$sql 	= "SELECT * FROM ".self::$prefix.self::$table." WHERE is_delete = 0 ORDER BY path,level ASC";
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
	 * UserCompetencesModel::modList()
	 * 列出符合条件的数据并分页显示
	 * @param string $where 查询条件
	 * @param integer $page 页码
	 * @param integer $pagenum 每页个数
	 * @return array 结果集数组
	 */
	public static function modList($where, $page, $pagenum){
		self::initDB();
		$start	= ($page-1)*$pagenum;
		$sql 	= "SELECT * FROM ".self::$prefix.self::$table." WHERE $where AND is_delete = 0 ORDER BY path,level ASC LIMIT $start,$pagenum";
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
	 * UserCompetencesModel::modListCount()
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
	 * UserCompetencesModel::modModify()
	 * 返回某个渠道的信息
	 * @param integer $id 权限ID
	 * @return array 结果集数组
	 */
	public static function modModify($id){
		self::initDB();
		$sql 	= "SELECT * FROM ".self::$prefix.self::$table." WHERE id = {$id} AND is_delete = 0 LIMIT 1";
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
	 * UserCompetencesModel::addUserCompetences()
	 * 添加开放权限信息保存到数据库
	 * @param array $data 数据集
	 * @return array 结果集数组
	 */
	public static function addUserCompetences($data){
		self::initDB();
		$res	= 0;
		$where	= "(title = '{$data['title']}' AND content = '{$data['content']}')"; 
        $res	= self::modListCount($where);
		if ($res > 0) {
			self::$errCode	= 10002;
			self::$errMsg	= "添加失败：开放权限信息已存在！";
			return false;
		}
		$sql 	= array2sql($data);
		$sql 	= "INSERT INTO `".self::$prefix.self::$table."` SET ".$sql; 
		$query	= self::$dbConn->query($sql);
		if ($query) {
			$rid 	= self::$dbConn->insert_id();           
			if ($rid) {
				return self::updatePath($rid, $data);
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
	 * UserCompetencesModel::updateUserCompetences()
	 * 更新开放权限信息
	 * @param integer $id 权限ID
	 * @param array $data 数据集
	 * @return array 结果集数组
	 */
	public static function updateUserCompetences($id, $data){
		self::initDB();
		$res	= 0;
		$where	= "id <> {$id} AND (title = '{$data['title']}' AND content = '{$data['content']}')"; 
        $res	= self::modListCount($where);
		if ($res > 0) {
			self::$errCode	= 20002;
			self::$errMsg	= "更新失败：开放权限已存在！";
			return false;
		}
		$sql 	= array2sql($data);
		$sql 	= "UPDATE `".self::$prefix.self::$table."` SET ".$sql." WHERE id = {$id}"; 
		$query	= self::$dbConn->query($sql);
		if ($query) {
			return self::updatePath($id, $data);
		} else {
			self::$errCode	= 20000;
			self::$errMsg	= "执行SQL语句出错";
			return false;
		}
	}

	/**
	 * UserCompetencesModel::delUserCompetences()
	 * 开放权限信息删除
	 * @param integer $id 权限ID
	 * @return bool
	 */
	public static function delUserCompetences($id){
		self::initDB();
		$res 	= self::getCompetencesById($id);
		if (!$res) {
			self::$errCode	= 10000;
			self::$errMsg	= "不存在的权限ID";
			return false;
		}
		$path 	= $res['path'];
		$res	= 0;
		$where	= "path like '{$path}-%'"; 
        $res	= self::modListCount($where);
		if ($res > 0) {
			self::$errCode	= 10001;
			self::$errMsg	= "删除失败：当前权限下还有数据！";
			return false;
		}
		$sql	= "UPDATE `".self::$prefix.self::$table."` SET is_delete = 1 WHERE id = {$id}";
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
	 * UserCompetencesModel::updatePath()
	 * 更新开放权限path
	 * @param integer $id 权限ID
	 * @param string $data 数据集
	 * @return bool
	 */
	public static function updatePath($id, $data){
		self::initDB();
		$pid	= $data['pid'];
		$path	= $data['path'];
		if ($pid==0) {
			$path	= $id;
		} else {
			$path	= $path.$id;
		}
		$sql 	= "UPDATE `".self::$prefix.self::$table."` SET path = '{$path}' WHERE id = {$id}"; 
		$query	= self::$dbConn->query($sql);
		if ($query) {
			return true;
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "执行SQL语句出错";
			return false;
		}
	}
	
	/**
	 * UserCompetencesModel::getCompetencesById()
	 * 获取某个开放权限ID信息
	 * @param integer $id 权限ID
	 * @return array
	 */
	public static function getCompetencesById($id){
		self::initDB();
		$sql 	= "SELECT * FROM `".self::$prefix.self::$table."` WHERE id = {$id}"; 
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