<?php
/**
 * 类名：ApiVisitStatModel
 * 功能：API调用统计数据（CRUD）层
 * 版本：1.0
 * 日期：2014/7/10
 * 作者：管拥军
 */
 
class ApiVisitStatModel{
	public static $dbConn;
	public static $errCode		= 0;
	public static $errMsg		= "";
	public static $prefix;
	private static $table		= "open_api_stat";
	private static $table_api	= "open_api";
	private static $table_user	= "power_global_user";
	
	/**
	 * ApiVisitStatModel::initDB()
	 * 返回数据库连接
	 * @return 
	 */
	public static function	initDB(){
		global $dbConn;
		self::$dbConn	= $dbConn;
		self::$prefix	= C('DB_PREFIX');
	}
	
	/**
	 * ApiVisitStatModel::modList()
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
						b.global_user_register_time,
						c.apiName
						FROM
						".self::$prefix.self::$table." AS a
						INNER JOIN ".self::$table_user." AS b ON a.apiUid = b.global_user_id
						LEFT JOIN ".self::$prefix.self::$table_api." AS c ON a.apiId = c.id
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
	 * ApiVisitStatModel::modListCount()
	 * 返回某个条件结果统计的总数
	 * @param string $where 查询条件
	 * @return integer 总数量 
	 */
	public static function modListCount($where){
		self::initDB();
		$sql 		= "SELECT count(*) FROM ".self::$prefix.self::$table." WHERE {$where} AND is_delete = 0";
		$query		= self::$dbConn->query($sql);
		if($result	= self::$dbConn->query($sql)) {
			$data	= self::$dbConn->fetch_row($result);
			return $data[0];
		} else {
			self::$errCode	= 90000;
			self::$errMsg	= "获取数据失败";
			return 0;
		}
	}

	/**
	 * ApiVisitStatModel::getStatByTime()
	 * 返回某个用户某个api接口某天的调用信息
	 * @param int $apiId 接口ID
	 * @param int $apiUid 接口调用人UID
	 * @param int $sTime 开始时间
	 * @param int $eTime 结束时间
	 * @return array
	 */
	public static function getStatByTime($apiId, $apiUid, $sTime, $eTime){
		self::initDB();
		$sql 		= "SELECT * FROM ".self::$prefix.self::$table." WHERE apiId = '{$apiId}' AND apiUid = '{$apiUid}' AND firstTime BETWEEN {$sTime} AND {$eTime} AND is_delete = 0 LIMIT 1";
		$query		= self::$dbConn->query($sql);
		if($query) {
			$res	= self::$dbConn->fetch_array($query);
			return $res;
		} else {
			self::$errCode	= 90000;
			self::$errMsg	= "获取数据失败";
			return 0;
		}
	}
	
	/**
	 * ApiVisitStatModel::updateApiVisitStat()
	 * 更新API访问统计
	 * @param integer $apiId api Id
	 * @param integer $apiUid api Uid
	 * @return array 结果集数组
	 */
	public static function updateApiVisitStat($apiId, $apiUid){
		self::initDB();
		$res		= 0;
		$data		= array();
		$times		= time();
		$firstTime	= strtotime(date('Y-m-d',$times)." 00:00:01");
		$where		= "apiId = '{$apiId}' AND apiUid = '{$apiUid}' AND firstTime >= '{$firstTime}'"; 
		$res		= self::modListCount($where);
		if($res > 0) {
			$sql 	= "UPDATE `".self::$prefix.self::$table."` SET apiCount = apiCount + 1,lastTime = '{$times}' WHERE apiId = '{$apiId}' AND apiUid = '{$apiUid}' AND firstTime >= '{$firstTime}'"; 
		} else {
			$data	= array(
						"apiId"		=> $apiId,
						"apiUid"	=> $apiUid,
						"apiCount"	=> 1,
						"firstTime"	=> $times,
						"lastTime"	=> $times,						
					);
			$sql 	= array2sql($data);
			$sql 	= "INSERT INTO `".self::$prefix.self::$table."` SET ".$sql; 
		}		
		$query	= self::$dbConn->query($sql);
		if($query) {
			return true;
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "执行SQL语句出错";
			return false;
		}
	}
}
?>