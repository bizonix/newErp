<?php
/**
 * 类名：CountriesUsazoneModel
 * 功能：美国邮政分区管理数据（CRUD）层
 * 版本：1.2
 * 日期：2013/12/16
 * 作者：管拥军
 */
 
class CountriesUsazoneModel{
	public static $dbConn;
	public static $errCode		= 0;
	public static $errMsg		= "";
	public static $prefix;
	private static $table		= "usa_zone_postcode";
	private static $tab_rel		= "transit_center";
		
	/**
	 * CountriesUsazoneModel::initDB()
	 * 返回数据库连接
	 * @return 
	 */
	public static function	initDB(){
		global $dbConn;
		self::$dbConn	= $dbConn;
		self::$prefix	= C('DB_PREFIX');
	}
	
	/**
	 * CountriesUsazoneModel::modList()
	 * 列出符合条件的数据并分页显示
	 * @param string $where 查询条件
	 * @param integer $page 页码
	 * @param integer $pagenum 每页个数
	 * @return array 结果集数组
	 */
	public static function modList($where, $page, $pagenum){
		self::initDB();
		$start		= ($page-1)*$pagenum;
		$sql 		= "SELECT a.*,b.cn_title,b.en_title FROM ".self::$prefix.self::$table." as a
						LEFT JOIN ".self::$prefix.self::$tab_rel." as b ON a.transitId = b.id
						WHERE $where AND a.is_delete = 0 ORDER BY a.transitId,a.zone,a.id DESC LIMIT $start,$pagenum";
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
	 * CountriesUsazoneModel::modListCount()
	 * 返回某个条件结果统计的总数
	 * @param string $where 查询条件
	 * @return integer 总数量 
	 */
	public static function modListCount($where){
		self::initDB();
		$sql 		= "SELECT count(*)	FROM ".self::$prefix.self::$table." WHERE $where AND is_delete = 0";
		$query		= self::$dbConn->query($sql);
		if ($result	= self::$dbConn->query($sql)) {
			$data	= self::$dbConn->fetch_row($result);
			return $data[0];
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "获取数据失败";
			return 0;
		}
	}
	
	/**
	 * CountriesUsazoneModel::modModify()
	 * 返回某个美国邮政分区的信息
	 * @param integer $id 美国邮政分区ID
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
	 * CountriesUsazoneModel::addCountriesUsazone()
	 * 添加美国邮政分区信息保存到数据库
	 * @param array $data 数据集
	 * @return array 结果集数组
	 */
	public static function addCountriesUsazone($data){
		self::initDB();
		$res	= 0;
		$where	= "(zone = '{$data['zone']}' AND transitId = '{$data['transitId']}')"; 
        $res	= self::modListCount($where);
		if ($res > 0) {
			self::$errCode	= 10002;
			self::$errMsg	= "添加失败：当前转运中心美国邮政分区已存在";
			return false;
		}
		$sql 		= array2sql($data);
		$sql 		= "INSERT INTO `".self::$prefix.self::$table."` SET ".$sql; 
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
	 * CountriesUsazoneModel::updateCountriesUsazone()
	 * 更新美国邮政分区信息
	 * @param integer $id 美国邮政分区ID
	 * @param array $data 数据集
	 * @return array 结果集数组
	 */
	public static function updateCountriesUsazone($id, $data){
		self::initDB();
		$res	= 0;
		$where	= "id <> {$id} AND ((zone = '{$data['zone']}' OR zip_code = '{$data['zip_code']}') AND transitId = '{$data['transitId']}')"; 
        $res	= self::modListCount($where);
		if ($res > 0) {
			self::$errCode	= 10002;
			self::$errMsg	= "更新失败：当前转运中心已存在相应的美国邮编或分区！";
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
	 * CountriesUsazoneModel::delCountriesUsazone()
	 * 美国邮政分区删除
	 * @param integer $id 美国邮政分区ID
	 * @return bool
	 */
	public static function delCountriesUsazone($id){
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
	
	/**
	 * CountriesUsazoneModel::listZone()
	 * 列出所有邮编分区
	 * @return array 
	 */
	public static function listZone($transitId=1){
		self::initDB();
		$sql 		= "SELECT zone FROM ".self::$prefix.self::$table." WHERE transitId = '{$transitId}' AND is_delete = 0 GROUP BY zone";
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
}
?>