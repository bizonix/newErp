<?php
/**
 * 类名：WebAdStatModel
 * 功能：网站广告统计数据（CRUD）层
 * 版本：1.0
 * 日期：2014/07/21
 * 作者：管拥军
 */
 
class WebAdStatModel{
	public static $dbConn;
	public static $errCode		= 0;
	public static $errMsg		= "";
	public static $prefix;
	private static $table		= "web_ad_stat";
	private static $table_ad	= "website_ad";
	
	/**
	 * WebAdStatModel::initDB()
	 * 返回数据库连接
	 * @return 
	 */
	public static function	initDB(){
		global $dbConn;
		self::$dbConn	= $dbConn;
		self::$prefix	= C('DB_PREFIX');
	}
	
	/**
	 * WebAdStatModel::modList()
	 * 列出符合条件的数据并分页显示
	 * @param string $where 查询条件
	 * @param integer $page 页码
	 * @param integer $pagenum 每页个数
	 * @return array 结果集数组
	 */
	public static function modList($where, $page, $pagenum){
		self::initDB();
		$start		= ($page-1)*$pagenum;
		$sql		= "SELECT a.*,b.id,b.topic FROM ".self::$prefix.self::$table." AS a
						LEFT JOIN ".self::$prefix.self::$table_ad." AS b
						ON a.adId = b.id
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
	 * WebAdStatModel::modListCount()
	 * 返回某个条件结果统计的总数
	 * @param string $where 查询条件
	 * @return integer 总数量 
	 */
	public static function modListCount($where){
		self::initDB();
		$sql 				= "SELECT count(*) FROM ".self::$prefix.self::$table." AS a WHERE {$where} AND a.is_delete = 0";
		$query				= self::$dbConn->query($sql);
		if($query) {
			$data			= self::$dbConn->fetch_row($query);
			return $data[0];
		} else {
			self::$errCode	= 90000;
			self::$errMsg	= "获取数据失败";
			return 0;
		}
	}
	
	/**
	 * WebAdStatModel::updateStatInfo()
	 * 更新广告访问统计信息
	 * @param int $ipNum ip数值
	 * @param int $ids 广告ID
	 * @param array $data 数据集
	 * @return array 结果集数组
	 */
	public static function updateStatInfo($ipNum,$ids,$data){
		self::initDB();
		$res		= self::showIpAdStat($ipNum,$ids);
		$sql 		= array2sql($data);
		if($res) {
			$id		= $res['id'];
			$sql 	= "UPDATE `".self::$prefix.self::$table."` SET ".$sql." WHERE id = '{$id}'"; 
		} else {
			$sql 	= "INSERT INTO `".self::$prefix.self::$table."` SET ".$sql; 
		}
		$query		= self::$dbConn->query($sql);
		if($query) {
			$rows 	= self::$dbConn->affected_rows();           
			if($rows) {
				return $rows;
			} else {
				self::$errCode	= 10001;
				self::$errMsg	= "更新数据失败";
				return false;
			}
		} else {
			self::$errCode		= 10000;
			self::$errMsg		= "执行SQL语句出错";
			return false;
		}
	}

	/**
	 * WebAdStatModel::showIpAdStat()
	 * 返回某个IP AD访问详情
	 * @param int $ipNum ip整数型
	 * @param int $ids 广告ID
	 * @return array  
	 */
	public static function showIpAdStat($ipNum,$ids){
		self::initDB();
		$addTime	= strtotime(date('Y-m-d')." 00:00:00");
		$lastTime	= strtotime(date('Y-m-d')." 23:59:59");
		$sql 		= "SELECT * FROM ".self::$prefix.self::$table." WHERE adId = '{$ids}' AND ipNum = '{$ipNum}' AND addTime >= {$addTime} AND lastTime <= {$lastTime} LIMIT 1";
		$query		= self::$dbConn->query($sql);
		$res		= self::$dbConn->fetch_array($query);
		if($res) {
			return $res;
		} else {
			return array();
		}
	}
}
?>