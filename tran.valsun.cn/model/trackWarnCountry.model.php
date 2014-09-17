<?php
/**
 * 类名：TrackWarnCountryModel
 * 功能：目的地国家预警管理数据（CRUD）层
 * 版本：1.0
 * 日期：2014/05/23
 * 作者：管拥军
 */
 
class TrackWarnCountryModel{
	public static $dbConn;
	public static $errCode		= 0;
	public static $errMsg		= "";
	public static $prefix;
	private static $table		= "track_carrier_country";
	private static $rel_table	= "carrier";
		
	/**
	 * TrackWarnCountryModel::initDB()
	 * 返回数据库连接
	 * @return 
	 */
	public static function	initDB(){
		global $dbConn;
		self::$dbConn	= $dbConn;
		self::$prefix	= C('DB_PREFIX');
	}
	
	/**
	 * TrackWarnCountryModel::modList()
	 * 列出符合条件的数据并分页显示
	 * @param string $where 查询条件
	 * @param integer $page 页码
	 * @param integer $pagenum 每页个数
	 * @return array 结果集数组
	 */
	public static function modList($where, $page, $pagenum){
		self::initDB();
		$start	= ($page-1)*$pagenum;
		$sql	= "SELECT
					a.id,
					a.carrierId,
					a.trackName,
					a.countryName,
					a.addTime,
					b.carrierNameCn,
					b.carrierNameEn
					FROM
					".self::$prefix.self::$table." AS a
					INNER JOIN ".self::$prefix.self::$rel_table." AS b ON a.carrierId = b.id
					WHERE $where AND a.is_delete = 0
					ORDER BY id DESC LIMIT $start,$pagenum";
		$query		= self::$dbConn->query($sql);
		if ($query) {
			$res	= self::$dbConn->fetch_array_all($query);
			return $res;
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "获取数据失败";
			return false;
		}
	}
	
	/**
	 * TrackWarnCountryModel::modListCount()
	 * 返回某个条件结果统计的总数
	 * @param string $where 查询条件
	 * @return integer 总数量 
	 */
	public static function modListCount($where){
		self::initDB();
		$sql 		= "SELECT count(*)	FROM ".self::$prefix.self::$table." WHERE $where AND is_delete = 0";
		$query		= self::$dbConn->query($sql);
		if ($result = self::$dbConn->query($sql)) {
			$data 	= self::$dbConn->fetch_row($result);
			return $data[0];
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "获取数据失败";
			return 0;
		}
	}
	
	/**
	 * TrackWarnCountryModel::modModify()
	 * 返回某个目的地国家预警的信息
	 * @param integer $id 目的地国家预警ID
	 * @return array 结果集数组
	 */
	public static function modModify($id){
		self::initDB();
		$sql 		= "SELECT * FROM ".self::$prefix.self::$table." WHERE id = {$id} AND is_delete = 0 LIMIT 1";
		$query		= self::$dbConn->query($sql);
		if ($query) {
			$res	= self::$dbConn->fetch_array($query);
			return $res;
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "获取数据失败";
			return false;
		}
	}
	
	/**
	 * TrackWarnCountryModel::addTrackWarnCountry()
	 * 添加目的地国家预警信息保存到数据库
	 * @param array $data 数据集
	 * @return array 结果集数组
	 */
	public static function addTrackWarnCountry($data){
		self::initDB();
		$res	= 0;
		$where	= "(countryName = '{$data['countryName']}' AND carrierId = '{$data['carrierId']}')"; 
        $res	= self::modListCount($where);
		if ($res > 0) {
			self::$errCode	= 10002;
			self::$errMsg	= "添加失败：已存在相关的运输方式预警名！";
			return false;
		}
		$sql 		= array2sql($data);
		$sql 		= "REPLACE INTO `".self::$prefix.self::$table."` SET ".$sql; 
		$query		= self::$dbConn->query($sql);
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
			self::$errCode		= 10000;
			self::$errMsg		= "执行SQL语句出错";
			return false;
		}
	}
	
	/**
	 * TrackWarnCountryModel::updateTrackWarnCountry()
	 * 更新目的地国家预警信息
	 * @param integer $id 目的地国家预警ID
	 * @param array $data 数据集
	 * @return array 结果集数组
	 */
	public static function updateTrackWarnCountry($id, $data){
		self::initDB();
		$res	= 0;
		$where	= "(id <> {$id} AND countryName = '{$data['countryName']}' AND carrierId = '{$data['carrierId']}')"; 
        $res	= self::modListCount($where);
		if ($res > 0) {
			self::$errCode	= 10002;
			self::$errMsg	= "更新失败：目的地国家预警已存在！";
			return false;
		}
		$sql 	= array2sql($data);
		$sql 	= "UPDATE `".self::$prefix.self::$table."` SET ".$sql." WHERE id = {$id}"; 
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
	 * TrackWarnCountryModel::delTrackWarnCountry()
	 * 目的地国家预警删除
	 * @param integer $id 目的地国家预警ID
	 * @return bool
	 */
	public static function delTrackWarnCountry($id){
		self::initDB();
		$sql		= "UPDATE `".self::$prefix.self::$table."` SET is_delete = 1 WHERE id = {$id}";
		$query		= self::$dbConn->query($sql);
		if ($query) {
			$rows 	= self::$dbConn->affected_rows(); 
			if ($rows) {
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