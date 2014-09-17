<?php
/**
 * 类名：TrackWarnNodeDataModel
 * 功能：运输方式节点预警数据管理数据（CRUD）层
 * 版本：1.0
 * 日期：2014/05/16
 * 作者：管拥军
 */
 
class TrackWarnNodeDataModel{
	public static $dbConn;
	public static $errCode		= 0;
	public static $errMsg		= "";
	public static $prefix;
	private static $table		= "carrier_process_aging";
	private static $tab_node	= "track_node";
		
	/**
	 * TrackWarnNodeDataModel::initDB()
	 * 返回数据库连接
	 * @return 
	 */
	public static function	initDB(){
		global $dbConn;
		self::$dbConn	= $dbConn;
		self::$prefix	= C('DB_PREFIX');
	}
	
	/**
	 * TrackWarnNodeDataModel::modList()
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
					a.*,
					b.nodeName
					FROM
					".self::$prefix.self::$table." AS a
					INNER JOIN ".self::$prefix.self::$tab_node." AS b ON a.nodeId = b.id
					WHERE $where AND a.is_delete = 0
					ORDER BY country ASC,id ASC LIMIT $start,$pagenum";
		$query	= self::$dbConn->query($sql);
		if($query) {
			$res	= self::$dbConn->fetch_array_all($query);
			return $res;
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "获取数据失败";
			return false;
		}
	}
	
	/**
	 * TrackWarnNodeDataModel::modListCount()
	 * 返回某个条件结果统计的总数
	 * @param string $where 查询条件
	 * @return integer 总数量 
	 */
	public static function modListCount($where){
		self::initDB();
		$sql = "SELECT count(*)	FROM ".self::$prefix.self::$table." WHERE $where AND is_delete = 0";
		$query	= self::$dbConn->query($sql);
		if($result=self::$dbConn->query($sql)) {
			$data=self::$dbConn->fetch_row($result);
			return $data[0];
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "获取数据失败";
			return 0;
		}
	}
	
	/**
	 * TrackWarnNodeDataModel::modModify()
	 * 返回某个运输方式节点预警数据的信息
	 * @param integer $id 运输方式节点预警数据ID
	 * @return array 结果集数组
	 */
	public static function modModify($id){
		self::initDB();
		$sql 	= "SELECT * FROM ".self::$prefix.self::$table." WHERE id = {$id} AND is_delete = 0 LIMIT 1";
		$query	= self::$dbConn->query($sql);
		if($query) {
			$res	= self::$dbConn->fetch_array($query);
			return $res;
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "获取数据失败";
			return false;
		}
	}	
	
	/**
	 * TrackWarnNodeDataModel::addTrackWarnNodeData()
	 * 添加运输方式节点预警数据信息
	 * @param array $data 数据集
	 * @return array 结果集数组
	 */
	public static function addTrackWarnNodeData($data){
		self::initDB();
		$res		= 0;
		$nodeId		= $data['nodeId'];
		$country	= post_check($data['country']);
		$where		= "(nodeId = '{$nodeId}' AND country = '{$country}')"; 
        $res		= self::modListCount($where);
		if($res > 0) {
			unset($data['addTime']);
			unset($data['add_user_id']);
			$data['editTime']		= time();
			$data['edit_user_id']	= 71;
			return self::updateTrackWarnNodeDataByCountry($nodeId, $country, $data);
		}
		$sql 	= array2sql($data);
		$sql 	= "INSERT INTO `".self::$prefix.self::$table."` SET ".$sql; 
		$query	= self::$dbConn->query($sql);
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
	 * TrackWarnNodeDataModel::updateTrackWarnNodeDataByCountry()
	 * 根据国家和节点ID更新运输方式节点预警数据信息
	 * @param integer $nid 运输方式节点预警数据ID
	 * @param string $country 运输方式国家
	 * @param array $data 数据集
	 * @return array 结果集数组
	 */
	public static function updateTrackWarnNodeDataByCountry($nid, $country, $data){
		self::initDB();
		$sql 	= array2sql($data);
		$sql 	= "UPDATE `".self::$prefix.self::$table."` SET ".$sql." WHERE nodeId = {$nid} AND country = '{$country}'"; 
		$query	= self::$dbConn->query($sql);
		if($query) {
			return true;
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "执行SQL语句出错";
			return false;
		}
	}
	
	/**
	 * TrackWarnNodeDataModel::updateTrackWarnNodeData()
	 * 更新运输方式节点预警数据信息
	 * @param integer $id 运输方式节点预警数据ID
	 * @param array $data 数据集
	 * @return array 结果集数组
	 */
	public static function updateTrackWarnNodeData($id, $data){
		self::initDB();
		$sql 	= array2sql($data);
		$sql 	= "UPDATE `".self::$prefix.self::$table."` SET ".$sql." WHERE id = {$id}"; 
		$query	= self::$dbConn->query($sql);
		if($query) {
			return true;
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "执行SQL语句出错";
			return false;
		}
	}

	/**
	 * TrackWarnNodeDataModel::delTrackWarnNodeData()
	 * 运输方式节点预警数据删除
	 * @param integer $id 运输方式节点预警数据ID
	 * @return bool
	 */
	public static function delTrackWarnNodeData($id){
		self::initDB();
		$sql	= "UPDATE `".self::$prefix.self::$table."` SET is_delete = 1 WHERE id = {$id}";
		$query	= self::$dbConn->query($sql);
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
            self::$errCode	= 10000;
			self::$errMsg	= "执行SQL语句失败！";
			return false;
		}
	}
}
?>