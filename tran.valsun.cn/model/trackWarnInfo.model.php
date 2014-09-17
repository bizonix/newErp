<?php
/**
 * 类名：TrackWarnInfoModel
 * 功能：运输方式跟踪号预警管理数据（CRUD）层
 * 版本：1.0
 * 日期：2013/10/21
 * 作者：管拥军
 */
 
class TrackWarnInfoModel{
	public static $dbConn;
	public static $errCode				= 0;
	public static $errMsg				= "";
	public static $prefix;
	private static $table				= "track_number";
	private static $tab_track_warn		= "track_number_warn_info";
	private static $tab_track_node		= "track_node";
	private static $tab_track_info		= "track_number_detail_";
	private static $tab_track_carrier	= "track_carrier";
	private static $rel_table			= "carrier";
		
	/**
	 * TrackWarnInfoModel::initDB()
	 * 返回数据库连接
	 * @return 
	 */
	public static function	initDB(){
		global $dbConn;
		self::$dbConn	= $dbConn;
		self::$prefix	= C('DB_PREFIX');
	}
	
	/**
	 * TrackWarnInfoModel::modList()
	 * 列出符合条件的数据并分页显示
	 * @param string $where 查询条件
	 * @param integer $page 页码
	 * @param integer $pagenum 每页个数
	 * @return array 结果集数组
	 */
	public static function modList($where, $page, $pagenum){
		self::initDB();
		$start	= ($page-1)*$pagenum;
		$idArr	= array();
		$ids	= '';
		$sql	= "SELECT
					a.id
					FROM
					".self::$prefix.self::$table." AS a
					WHERE {$where} AND a.is_delete = 0
					ORDER BY scanTime DESC
					LIMIT {$start},{$pagenum}";
		$query	= self::$dbConn->query($sql);
		$res	= self::$dbConn->fetch_array_all($query);
		foreach($res as $v) {
			array_push($idArr, $v['id']);
		}
		if(empty($idArr)) {
			self::$errCode	= 10001;
			self::$errMsg	= "获取数据失败";
			return false;
		} else {
			$ids	= implode(",",$idArr);
		}		
		$sql		= "SELECT
						a.*,
						b.carrierNameCn,
						b.carrierNameEn
						FROM
						".self::$prefix.self::$table." AS a
						LEFT JOIN ".self::$prefix.self::$rel_table." AS b ON a.carrierId = b.id
						WHERE a.id IN({$ids})
						ORDER BY scanTime DESC";
		$query		= self::$dbConn->query($sql);
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
	 * TrackWarnInfoModel::modListCount()
	 * 返回某个条件结果统计的总数
	 * @param string $where 查询条件
	 * @return integer 总数量 
	 */
	public static function modListCount($where){
		self::initDB();
		$sql 		= "SELECT count(*)	FROM ".self::$prefix.self::$table." WHERE {$where} AND is_delete = 0";
		$query		= self::$dbConn->query($sql);
		if($query) {
			$data	= self::$dbConn->fetch_row($query);
			return $data[0];
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "获取数据失败";
			return 0;
		}
	}
	
	/**
	 * TrackWarnInfoModel::listTrackNumberWarnInfo()
	 * 列出某个跟踪号的预警信息
	 * @param integer $tid 跟踪号
	 * @return array 结果集数组
	 */
	public static function listTrackNumberWarnInfo($tid){
		self::initDB();
		$start		= ($page-1)*$pagenum;
		$sql		= "SELECT
						a.id,
						a.trackNumber,
						a.carrierId,
						a.nodeId,
						a.is_warn,
						a.warnDays,
						a.warnStartTime,
						a.warnEndTime,
						b.nodeName
						FROM
						".self::$prefix.self::$tab_track_warn." AS a
						INNER JOIN ".self::$prefix.self::$tab_track_node." AS b ON a.nodeId = b.id
						WHERE trackNumber = '{$tid}' ORDER BY a.nodeId ASC";
		$query		= self::$dbConn->query($sql);
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
	 * TrackWarnInfoModel::listTrackNumberInfoForCountry()
	 * 列出某个跟踪号的目的地国家详细追踪信息
	 * @param integer $tid 分表ID
	 * @param string $trackNumber 跟踪号
	 * @return array 
	 */
	public static function listTrackNumberInfoForCountry($tid, $trackNumber){
		self::initDB();
		$data		= array();
		$sql		= "SELECT postion,event,trackTime FROM ".self::$prefix.self::$tab_track_info.$tid."_country WHERE trackNumber = '{$trackNumber}' GROUP BY `event` ORDER BY id ASC";
		$query		= self::$dbConn->query($sql);
		if($query) {
			$res	= self::$dbConn->fetch_array_all($query);
			foreach($res as $v) {
				array_push($data,array("postion"=>$v['postion'],"event"=>$v['event'],"trackTime"=>date('Y-m-d H:i:s',$v['trackTime'])));
			}	
			return $data;
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "获取数据失败";
			return false;
		}
	}
	
	/**
	 * TrackWarnInfoModel::listTrackNumberInfo()
	 * 列出某个跟踪号的详细追踪信息
	 * @param integer $tid 分表ID
	 * @param string $trackNumber 跟踪号
	 * @return json string 
	 */
	public static function listTrackNumberInfo($tid, $trackNumber){
		self::initDB();
		$data		= array();
		$sql		= "SELECT postion,event,trackTime FROM ".self::$prefix.self::$tab_track_info.$tid." WHERE trackNumber = '{$trackNumber}' GROUP BY `event` ORDER BY id ASC";
		$query		= self::$dbConn->query($sql);
		if($query) {
			$res	= self::$dbConn->fetch_array_all($query);
			foreach($res as $v) {
				array_push($data,array("postion"=>$v['postion'],"event"=>$v['event'],"trackTime"=>date('Y-m-d H:i:s',$v['trackTime'])));
			}	
			return $data;
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "获取数据失败";
			return false;
		}
	}
	
	/**
	 * TrackWarnInfoModel::trackNumberInfo()
	 * 实时获取某个跟踪号的跟踪信息
	 * @param integer $carrierId 运输方式ID
	 * @param integer $lan 跟踪语言
	 * @param string $trackNumber 跟踪号
	 * @return json string 
	 */
	public static function trackNumberInfo($carrierId, $trackNumber, $lan){
		self::initDB();
		$data			= array();
		$trackName		= "";
		$sql			= "SELECT trackName FROM ".self::$prefix.self::$tab_track_carrier." WHERE carrierId = '{$carrierId}'";
		$query			= self::$dbConn->query($sql);
		if($query) {
			$res		= self::$dbConn->fetch_array($query);
			$trackName	= isset($res['trackName']) ? trim($res['trackName']) : "";
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "获取数据失败";
			return false;
		}
		if(empty($trackName)) {
			self::$errCode	= 10001;
			self::$errMsg	= "跟踪名称没有获取到，请选择正确的运输方式！";
			return false;
		}
		$res	= TransOpenApiModel::getTrackInfo($trackNumber, $trackName, $lan);
		$res	= json_decode($res,true);
		if(isset($res[trackingEventList])) {
			foreach ($res[trackingEventList] as $v) {
				array_push($data,array("postion"=>$v['place'],"event"=>$v['details'],"trackTime"=>$v['date']));
			}
		} else {
			if($res['ReturnValue']=='-1') {
				array_push($data,array("postion"=>"暂无","event"=>$res['ReturnValue']."(跟踪号或运输方式有误或暂无跟踪信息)","trackTime"=>"暂无"));
			} else {
				array_push($data,array("postion"=>"物流系统服务器","event"=>$res['ReturnValue'],"trackTime"=>date('Y-m-d H:i:s')));
			}
		}	
		return $data;
	}
}
?>