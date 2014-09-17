<?php
/**
 * 类名：TrackWarnStatModel
 * 功能：运输方式跟踪号统计管理数据（CRUD）层
 * 版本：1.0
 * 日期：2013/11/21
 * 作者：管拥军
 */
 
class TrackWarnStatModel{
	public static $dbConn;
	public static $errCode		= 0;
	public static $errMsg		= "";
	public static $prefix;
	private static $tab_track_num	= "track_number";
	private static $tab_track_warn	= "track_number_warn_info";
		
	/**
	 * TrackWarnStatModel::initDB()
	 * 返回数据库连接
	 * @return 
	 */
	public static function	initDB(){
		global $dbConn;
		self::$dbConn	= $dbConn;
		self::$prefix	= C('DB_PREFIX');
	}
	
	/**
	 * TrackWarnStatModel::getViewTable()
	 * 列出某个运输方式各节点的效率
	 * @param integer $carrierId 运输方式ID
	 * @param integer $channelId 渠道ID
	 * @param integer $is_warn 是否包含预警天数 0包含，1不包含
	 * @param string $statType 统计类型
	 * @param string $condition 条件
	 * @return json string
	 */
	public static function getViewTable($carrierId, $channelId, $statType, $condition, $is_warn, $countryId){
		self::initDB();
		$total		= 0;
		$realTotal	= 0;
		$table		= "<table>";
		$channelArr	= TransOpenApiModel::getCarrierChannel($carrierId, $channelId);
		foreach ($channelArr as $k=>$ch) {
			$nodeArr= TransOpenApiModel::getTrackNodeList($carrierId,$ch['id']);
			if ($k==0) {
				$table	.= "<tr><th>渠道</th>";
				if ($statType!='internalTime') {
					foreach ($nodeArr as $v) {
						$table	.= "<th>{$v['nodeName']}</th>";
					}
				} else {
					if ($carrierId == 46 || $carrierId == 47) {
						$priceUnit	= "$";
					} else {
						$priceUnit	= "￥";
					}
					$table	.= "<th>国内平均处理时间(天)</th><th>平均处理重量(KG)</th><th>平均处理运费({$priceUnit})</th>";
				}
				$table	.= "</tr>";
			}
			$table	.= "<tr><td>{$ch['channelName']}</td>";
			$key	= 0;
			$percent= 0;
			$nodeStr= "";
			$nodeEffStr = "";
			$nodeWarnStr="";
			if ($statType!='internalTime') {
				foreach ($nodeArr as $nd) {
					if ($statType=='nodeEff' || $statType=='nodeEffPer') {
						if($key==0) {
							$nodeEffStr	= " AND nodeEff like '1%'";
							if ($is_warn) $nodeWarnStr	= " AND warnLevel NOT like '1%'";
						} else {
							$nodeStr	= str_pad($nodeStr,$key,"_",STR_PAD_LEFT);
							$nodeEffStr	= " AND nodeEff like '{$nodeStr}1%'";
							if ($is_warn) $nodeWarnStr	= " AND warnLevel NOT like '{$nodeStr}1%'";
						}
					}
					if ($is_warn && $statType=='nodeTime') $nodeWarnStr	= " AND is_warn = 0";
					$callFunction 	= 'get'.$statType;
					if ($statType=='nodeEffPer' && $key==0) {
						$total		= self::getNodeEffTotal($carrierId, $ch['id'], $condition.$nodeWarnStr, $nd['id'], $countryId);
					}
					$realTotal		= self::$callFunction($carrierId, $ch['id'], $condition.$nodeEffStr.$nodeWarnStr, $nd['id'], $countryId);
					if ($statType=='nodeEffPer') {
						$percent = round($realTotal/$total,4)*100;
						$table	.= "<td>处理数量:{$realTotal}<br/>总数量:{$total}<br/>处理率:{$percent}%</td>";
					} else {
						$table	.= "<td>{$realTotal}</td>";
					}
					$key++;
				}
			} else {
				$nodeWarnStr	= $is_warn ? " AND warnLevel > 0" : '';
				$res	= self::getInternalTime($carrierId, $ch['id'], $condition.$nodeWarnStr, "", $countryId);
				$table	.= "<td>{$res[0]}</td><td>{$res[1]}</td><td>{$res[2]}</td>";
			}
			$table	.= "</tr>";
		}
		$table	.= "</table>";
		return $table;
	}
	
	/**
	 * TrackWarnStatModel::getViewTodayTable()
	 * 列出某个运输方式各节点每天的效率
	 * @param integer $carrierId 运输方式ID
	 * @param integer $channelId 渠道ID
	 * @param integer $is_warn 是否包含预警天数 0包含，1不包含
	 * @param string $statType 统计类型
	 * @param string $condition 时间条件
	 * @return json string
	 */
	public static function getViewTodayTable($carrierId, $channelId, $statType, $condition, $is_warn, $countryId){
		self::initDB();
		$total		= 0;
		$realTotal	= 0;
		$days	= ceil(($condition[3]-$condition[2])/86400);
		$nTime	= $condition[1];
		$sTime	= $condition[2];
		$eTime	= $condition[3];		
		$nKey	= $condition[4];	
		$nTitle	= $condition[5];
		array_pop($condition);
		array_pop($condition);
		$table	= "<table border=\"1\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\"><tr><th colspan=".($days+1).">{$nTitle}</th></tr>";
		$channelArr	= TransOpenApiModel::getCarrierChannel($carrierId, $channelId);
		foreach ($channelArr as $k=>$ch) {
			$nodeArr= TransOpenApiModel::getTrackNodeList($carrierId,$ch['id']);
			if ($k==0) {
				$table	.= "<tr><th>渠道</th>";
				for ($i = 0; $i < $days; $i++) {
					$today	= date('m-d',strtotime("+$i day"." 00:00:01",$sTime));
					$table	.= "<th>".$today."</th>";
				}				
				$table	.= "</tr>";
			}
			$table	.= "<tr><td>{$ch['channelName']}</td>";
			$percent= 0;
			$nodeStr= "";
			$nodeEffStr = "";
			$nodeWarnStr="";
			if ($nKey==0) {
				//$nodeEffStr	= " AND nodeEff like '1%'";
				$nodeWarnStr	= " AND warnLevel like '1%'";
			} else {
				$nodeStr	= str_pad($nodeStr,$nKey,"_",STR_PAD_LEFT);
				//$nodeEffStr	= " AND nodeEff like '{$nodeStr}1%'";
				$nodeWarnStr	= " AND warnLevel like '{$nodeStr}1%'";
			}
			//if ($is_warn && $statType=='nodeTime') $nodeWarnStr	= " AND is_warn = 0";
			//$callFunction 	= 'get'.$statType;
			for ($i = 0; $i < $days; $i++) {
				$today			= date('Y-m-d',strtotime("+$i day"." 00:00:01",$sTime));
				$condition[2]	= strtotime($today." 00:00:01");
				$condition[3]	= strtotime($today." 23:59:59");
				$conditions		= implode(" AND ",$condition);
				$conditions		= str_replace("{$nTime} AND","{$nTime} BETWEEN",$conditions);
				$total			= self::getNodeEffTotal($carrierId, $ch['id'], $conditions, $nd['id'], $countryId);
				$realTotal		= self::getNodeEff($carrierId, $ch['id'], $conditions.$nodeWarnStr, $nd['id'], $countryId);
				if ($statType=='todayWarnPer') {
					$percent = round($realTotal/$total,4)*100;
					//$table	.= "<td>处理数量:{$realTotal}<br/>总数量:{$total}<br/>处理率:{$percent}%</td>";
					if ($percent>5) {
						$cssRed	= "style='color:red'";
					} else {
						$cssRed	= "";
					}
					$table	.= "<td {$cssRed}>{$percent}%</td>";
				} else {
					$table	.= "<td>{$realTotal}</td>";
				}
			}				
			$table	.= "</tr>";
		}
		$table	.= "</table>";
		return $table;
	}
	
	/**
	 * TrackWarnStatModel::getViewTodayPic()
	 * 列出某个运输方式每天各节点的效率
	 * @param integer $carrierId 运输方式ID
	 * @param integer $channelId 渠道ID
	 * @param integer $is_warn 是否包含预警天数 0包含，1不包含
	 * @param string $statType 统计类型
	 * @param string $condition 条件
	 * @param string $title 图形标题
	 * @return json string
	 */
	public static function getViewTodayPic($carrierId, $channelId, $statType, $condition, $title, $is_warn, $countryId){
		self::initDB();
		$data		= array();
		$channelArr	= array();
		$nodeArr	= array();
		$res		= array();
		$total		= 0;
		$realTotal	= 0;
		$days	= ceil(($condition[3]-$condition[2])/86400);
		$nTime	= $condition[1];
		$sTime	= $condition[2];
		$eTime	= $condition[3];		
		$nKey	= $condition[4];	
		$picId	= $condition[5];
		array_pop($condition);
		array_pop($condition);
		$channelArr	= TransOpenApiModel::getCarrierChannel($carrierId, $channelId);
		foreach ($channelArr as $k=>$ch) {
			$nodeArr= TransOpenApiModel::getTrackNodeList($carrierId,$ch['id']);
			$res[$k]['data'] = array();
			$res[$k]['name'] = $ch['channelName'];
			$key	= 0;
			$percent= 0;
			$nodeStr= "";
			$nodeEffStr = "";
			//X轴
			for ($i = 0; $i < $days; $i++) {
				$today	= date('Y-m-d',strtotime("+$i day"." 00:00:01",$sTime));
				array_push($data, $today);
			}
			//Y轴
			$percent= 0;
			$nodeStr= "";
			$nodeEffStr = "";
			$nodeWarnStr="";
			if ($nKey==0) {
				//$nodeEffStr	= " AND nodeEff like '1%'";
				$nodeWarnStr	= " AND warnLevel like '1%'";
			} else {
				$nodeStr	= str_pad($nodeStr,$nKey,"_",STR_PAD_LEFT);
				//$nodeEffStr	= " AND nodeEff like '{$nodeStr}1%'";
				$nodeWarnStr	= " AND warnLevel like '{$nodeStr}1%'";
			}
			//if ($is_warn && $statType=='nodeTime') $nodeWarnStr	= " AND is_warn = 0";
			//$callFunction 	= 'get'.$statType;
			for ($i = 0; $i < $days; $i++) {
				$today			= date('Y-m-d',strtotime("+$i day"." 00:00:01",$sTime));
				$condition[2]	= strtotime($today." 00:00:01");
				$condition[3]	= strtotime($today." 23:59:59");
				$conditions		= implode(" AND ",$condition);
				$conditions		= str_replace("{$nTime} AND","{$nTime} BETWEEN",$conditions);
				$total			= self::getNodeEffTotal($carrierId, $ch['id'], $conditions, $nd['id'], $countryId);
				$realTotal		= self::getNodeEff($carrierId, $ch['id'], $conditions.$nodeWarnStr, $nd['id'], $countryId);
				if ($statType=='todayWarnPer') {
					$percent = round($realTotal/$total,4)*100;
					array_push($res[$k]['data'],$percent);
				} else {
					$table	.= "<td>{$realTotal}</td>";
				}
			}			
		}
		switch ($statType) {
			case "todayWarnPer":
				$unit	= "%";
				$y_title= "百分比";
			break;						
		}
		$categories = json_encode($data);
		$series		= json_encode($res);
		$data		= "$('#{$picId}').highcharts({
						chart: {
							type: 'spline'
						},
						title: {
							text: '{$title}'
						},
						xAxis: {
							categories: {$categories},
						},
						yAxis: {
							title: {
								text: '{$y_title}'
							},
							labels: {
								formatter: function() {
									return this.value +'{$unit}'
								}
							}
						},
						tooltip: {
							crosshairs: true,
							shared: true
						},
						plotOptions: {
							spline: {
								marker: {
									radius: 4,
									lineColor: '#666666',
									lineWidth: 1
								}
							}
						},
						series: {$series}
					});";
		return $data;
	}
	
	/**
	 * TrackWarnStatModel::getViewPic()
	 * 列出某个运输方式各节点的效率
	 * @param integer $carrierId 运输方式ID
	 * @param integer $channelId 渠道ID
	 * @param integer $is_warn 是否包含预警天数 0包含，1不包含
	 * @param string $statType 统计类型
	 * @param string $condition 条件
	 * @param string $title 图形标题
	 * @return json string
	 */
	public static function getViewPic($carrierId, $channelId, $statType, $condition, $title, $is_warn, $countryId){
		self::initDB();
		$data		= array();
		$channelArr	= array();
		$nodeArr	= array();
		$res		= array();
		$total		= 0;
		$realTotal	= 0;
		$channelArr	= TransOpenApiModel::getCarrierChannel($carrierId, $channelId);
		foreach ($channelArr as $k=>$ch) {
			$nodeArr= TransOpenApiModel::getTrackNodeList($carrierId,$ch['id']);
			$res[$k]['data'] = array();
			$res[$k]['name'] = $ch['channelName'];
			$key	= 0;
			$percent= 0;
			$nodeStr= "";
			$nodeEffStr = "";
			//X轴
			if ($statType!='internalTime') {
				if ($k==0) { //
					foreach ($nodeArr as $v) {
						array_push($data, $v['nodeName']);
					}
				}
			} else {
				array_push($data, $ch['channelName']);
			}
			//Y轴
			if ($statType!='internalTime') {
				foreach ($nodeArr as $nd) {
					if ($statType=='nodeEff' || $statType=='nodeEffPer') {
						if($key==0) {
							$nodeEffStr	= " AND nodeEff like '1%'";
						} else {
							$nodeStr	= str_pad($nodeStr,$key,"_",STR_PAD_LEFT);
							$nodeEffStr	= " AND nodeEff like '{$nodeStr}1%'";
						}
					}
					$callFunction 	= 'get'.$statType;
					if ($statType=='nodeEffPer' && $key==0) {
						$total		= self::getNodeEffTotal($carrierId, $ch['id'], $condition, $nd['id'], $countryId);
					}
					$realTotal		= self::$callFunction($carrierId, $ch['id'], $condition.$nodeEffStr, $nd['id'], $countryId);
					if ($statType=='nodeEffPer') {
						$percent = round($realTotal/$total,4)*100;
						array_push($res[$k]['data'],$percent);
					} else {
						array_push($res[$k]['data'],$realTotal);
					}
					$key++;
				}
			} else {
				$nodeWarnStr	= $is_warn ? " AND warnLevel > 0" : '';
				$total			= self::getInternalTime($carrierId, $ch['id'], $condition.$nodeWarnStr, "", $countryId);
				array_push($res[$k]['data'],$total[0]);
				array_push($res[$k]['data'],$total[1]);
				array_push($res[$k]['data'],$total[2]);
			}
		}
		switch ($statType) {
			case "nodeEff":
				$unit	= "件";
				$y_title= "包裹数量";
			break;
			case "nodeTime":
				$unit	= "天";
				$y_title= "平均处理天数";
			break;
			case "nodeEffPer":
				$unit	= "%";
				$y_title= "百分比";
			break;
			case "internalTime":
				$unit	= "";
				$y_title= "";
				unset($data);
				if ($carrierId == 46 || $carrierId == 47) {
					$priceUnit	= "$";
				} else {
					$priceUnit	= "￥";
				}
				$data	= array('国内平均处理时间(天)', '平均处理重量(KG)', "平均处理运费({$priceUnit})");
			break;			
		}
		$categories = json_encode($data);
		$series		= json_encode($res);
		$data		= "$('#container').highcharts({
						chart: {
							type: 'spline'
						},
						title: {
							text: '{$title}'
						},
						xAxis: {
							categories: {$categories},
						},
						yAxis: {
							title: {
								text: '{$y_title}'
							},
							labels: {
								formatter: function() {
									return this.value +'{$unit}'
								}
							}
						},
						tooltip: {
							crosshairs: true,
							shared: true
						},
						plotOptions: {
							spline: {
								marker: {
									radius: 4,
									lineColor: '#666666',
									lineWidth: 1
								}
							}
						},
						series: {$series}
					});";
		if ($statType=='internalTime') exit($data);
		return $data;
	}
	
	/**
	 * TrackWarnStatModel::getCountryTrackNumber()
	 * 获取某个国家某个发货时间跟踪号列表
	 * @param integer $countryId 国家ID
	 * @param integer $carrierId 运输方式ID
	 * @param integer $channelId 渠道ID
	 * @param string $condition 条件
	 * @return json string  
	 */
	public static function getCountryTrackNumber($carrierId, $channelId, $condition, $countryId){
		self::initDB();
		$sql 	= "SELECT trackNumber FROM ".self::$prefix.self::$tab_track_num." WHERE $condition AND carrierId = {$carrierId} AND channelId = {$channelId} AND countryId = {$countryId} AND is_delete = 0";
		$query	= self::$dbConn->query($sql);
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
	 * TrackWarnStatModel::getNodeEffTotal()
	 * 返回某个运输方式某渠道节点处理总数量
	 * @param integer $carrierId 运输方式ID
	 * @param integer $channelId 渠道ID
	 * @param string $condition 条件
	 * @param string $nodeId 节点ID，待定
	 * @param integer $countryId 国家ID 如果有
	 * @return integer  
	 */
	public static function getNodeEffTotal($carrierId, $channelId, $condition, $nodeId="", $countryId=""){
		self::initDB();
		if (empty($countryId)) {
			$sql = "SELECT count(*)	FROM ".self::$prefix.self::$tab_track_num." WHERE $condition AND carrierId='{$carrierId}' AND channelId='{$channelId}' AND is_delete = 0";
		} else {
			$sql = "SELECT count(*)	FROM ".self::$prefix.self::$tab_track_num." WHERE $condition AND carrierId='{$carrierId}' AND channelId='{$channelId}' AND countryId = {$countryId} AND is_delete = 0";
		}
		$query	= self::$dbConn->query($sql);
		if ($query) {
			$data	= self::$dbConn->fetch_row($query);
			return intval($data[0]);
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "获取数据失败";
			return 0;
		}
	}
	
	/**
	 * TrackWarnStatModel::getNodeEff()
	 * 返回某个运输方式某渠道节点实际处理总数量
	 * @param integer $carrierId 运输方式ID
	 * @param integer $channelId 渠道ID
	 * @param string $condition 条件
	 * @param string $nodeId 节点ID，待定
	 * @param integer $countryId 国家ID 如果有
	 * @return integer 总数量 
	 */
	public static function getNodeEff($carrierId, $channelId, $condition, $nodeId="", $countryId=""){
		self::initDB();
		if (empty($countryId)) {
			$sql = "SELECT count(*)	FROM ".self::$prefix.self::$tab_track_num." WHERE $condition AND carrierId = {$carrierId} AND channelId = {$channelId} AND is_delete = 0";
		} else {
			$sql = "SELECT count(*)	FROM ".self::$prefix.self::$tab_track_num." WHERE $condition AND carrierId = {$carrierId} AND channelId = {$channelId} AND countryId = {$countryId} AND is_delete = 0";
		}
		$query	= self::$dbConn->query($sql);
		if ($query) {
			$data	= self::$dbConn->fetch_row($query);
			return intval($data[0]);
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "获取数据失败";
			return 0;
		}
	}
	
	/**
	 * TrackWarnStatModel::getNodeEffPer()
	 * 返回某个运输方式某渠道节点实际处理总数量（百分比）
	 * @param integer $carrierId 运输方式ID
	 * @param integer $channelId 渠道ID
	 * @param string $condition 条件
	 * @param string $nodeId 节点ID，待定
	 * @param integer $countryId 国家ID 如果有
	 * @return integer 总数量 
	 */
	public static function getNodeEffPer($carrierId, $channelId, $condition, $nodeId="", $countryId=""){
		self::initDB();
		if (empty($countryId)) {
			$sql 	= "SELECT count(*) FROM ".self::$prefix.self::$tab_track_num." WHERE $condition AND carrierId = {$carrierId} AND channelId = {$channelId} AND is_delete = 0";
		} else {
			$sql 	= "SELECT count(*) FROM ".self::$prefix.self::$tab_track_num." WHERE $condition AND carrierId = {$carrierId} AND channelId = {$channelId} AND countryId = {$countryId} AND is_delete = 0";
		}
		$query	= self::$dbConn->query($sql);
		if ($query) {
			$data	= self::$dbConn->fetch_row($query);
			return intval($data[0]);
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "获取数据失败";
			return 0;
		}
	}
	
	/**
	 * TrackWarnStatModel::getNodeTime()
	 * 返回某个运输方式某渠道节点处理时效
	 * @param integer $carrierId 运输方式ID
	 * @param integer $channelId 渠道ID
	 * @param string $condition 条件
	 * @param string $nodeId 节点ID
	 * @return integer 总数量 
	 */
	public static function getNodeTime($carrierId, $channelId, $condition, $nodeId, $countryId=""){
		self::initDB();
		$trackNums	= '';
		if (!empty($countryId)) {
			$trackNumArr= array();
			$res 	= TrackWarnStatModel::getCountryTrackNumber($carrierId, $channelId, $condition, $countryId);
			foreach ($res as $v) {
				array_push($trackNumArr, "'".$v['trackNumber']."'");
			}
			$trackNums	= implode(",",$trackNumArr);
			$condition  = count($res) ? "1 AND trackNumber IN($trackNums)" : "1 AND trackNumber IN('')";
			$sql 		= "SELECT AVG(processTime)	FROM ".self::$prefix.self::$tab_track_warn." WHERE $condition AND carrierId='{$carrierId}' AND channelId='{$channelId}' AND nodeId='{$nodeId}' AND processTime>0";
		} else {
			$condition 	= str_replace("scanTime","warnStartTime",$condition);
			$sql 	   	= "SELECT AVG(processTime)	FROM ".self::$prefix.self::$tab_track_warn." WHERE $condition AND carrierId='{$carrierId}' AND channelId='{$channelId}' AND nodeId='{$nodeId}' AND processTime>0";
		}
		$query			= self::$dbConn->query($sql);
		if ($query) {
			$data	= self::$dbConn->fetch_row($query);
			return round(intval($data[0])/86400,2);
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "获取数据失败";
			return 0;
		}
	}
	
	/**
	 * TrackWarnStatModel::getInternalTime()
	 * 返回某个运输方式某渠道国内处理时效
	 * @param integer $carrierId 运输方式ID
	 * @param integer $channelId 渠道ID
	 * @param integer $countryId 国家ID
	 * @param string $condition 条件
	 * @return array 
	 */
	public static function getInternalTime($carrierId, $channelId, $condition, $nodeId="", $countryId){
		self::initDB();
		if (!empty($countryId)) {
			$sql = "SELECT AVG(internalTime),AVG(weight),AVG(cost) FROM ".self::$prefix.self::$tab_track_num." WHERE $condition AND carrierId = {$carrierId} AND channelId = {$channelId} AND countryId = {$countryId} AND internalTime > 0";
		} else {
			$sql = "SELECT AVG(internalTime),AVG(weight),AVG(cost) FROM ".self::$prefix.self::$tab_track_num." WHERE $condition AND carrierId = {$carrierId} AND channelId = {$channelId} AND internalTime > 0";
		}
		$query	= self::$dbConn->query($sql);
		if ($query) {
			$data	= self::$dbConn->fetch_row($query);
			return array(round(intval($data[0])/86400,2), round(floatval($data[1]),4), round(floatval($data[2]),4));
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "获取数据失败";
			return 0;
		}
	}
}
?>