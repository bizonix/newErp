<?php
/**
 * 类名：TrackWarnStatAct
 * 功能：运输方式跟踪号统计管理动作处理层
 * 版本：1.0
 * 日期：2013/11/21
 * 作者：管拥军
 */
  
class TrackWarnStatAct {
    public static $errCode	= 0;
	public static $errMsg	= "";

	/**
	 * TrackWarnStatAct::act_viewTable()
	 * 列出某个运输方式各渠道各节点的（处理、时效）效率
	 * @param integer $carrierId 运输方式ID
	 * @param integer $channelId 渠道ID
	 * @param string $timeNode 时间条件
	 * @param string $statType 统计类型
	 * @return json string 
	 */
 	public function act_viewTable(){
		$condition	= "1";
		$carrierId	= isset($_POST['carrierId']) ? abs(intval($_POST['carrierId'])) : 0;
		$channelId	= isset($_POST['channelId']) ? abs(intval($_POST['channelId'])) : 0;
		$countryId	= isset($_POST['countryId']) ? abs(intval($_POST['countryId'])) : 0;
		$timeNode	= isset($_GET['timeNode']) ? post_check(trim($_GET['timeNode'])) : '';
		$statType	= isset($_POST['statType']) ? post_check(trim($_POST['statType'])) : '';
		$is_warn	= isset($_POST['is_warn']) ? post_check(trim($_POST['is_warn'])) : 0;
		$act		= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod		= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if	(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 10000;
			self::$errMsg   = "对不起,您无跟踪号统计查看权限！";
			return false;
		}
		if (empty($carrierId)) {
			self::$errCode  = 10001;
			self::$errMsg   = "运输方式参数有误";
			return false;
		}
		if (empty($timeNode) || !in_array($timeNode,array('scanTime'))) {
			self::$errCode  = 10002;
			self::$errMsg   = "时间条件参数有误";
			return false;
		}
		if (empty($statType) || !in_array($statType,array('nodeEff','nodeEffPer','nodeTime','internalTime','todayWarnPer'))) {
			self::$errCode  = 10003;
			self::$errMsg   = "统计类型参数有误";
			return false;
		}
		if (!in_array($is_warn,array(0,1))) {
			self::$errCode  = 10004;
			self::$errMsg   = "预警天数参数有误";
			return false;
		}
		if (!empty($timeNode)) {
			$startTime		= isset($_GET['startTime']) ? strtotime(trim($_GET['startTime'])." 00:00:00") : strtotime(date("Y-m-d",time())." 00:00:00");
			$endTime		= isset($_GET['endTime']) ? strtotime(trim($_GET['endTime'])." 23:59:59") : strtotime(date("Y-m-d",time())." 23:59:59");
			if ($startTime && $endTime) {
				$condition	.= ' AND '.$timeNode." BETWEEN '".$startTime."' AND "."'".$endTime."'";
			}
		}
		if (in_array($statType,array('todayWarnPer'))) {
			$res			= "";
			$nodeArr		= TransOpenApiModel::getRandTrackNodeList($carrierId);
			foreach ($nodeArr as $key=>$nd) {
				$res		.= '<span class="stat_pic">';
				$condition	= array(1,$timeNode,$startTime,$endTime,$key,$nd['nodeName'].'节点--各渠道预警率信息一览表');
				$res		.= TrackWarnStatModel::getViewTodayTable($carrierId, $channelId, $statType, $condition , $is_warn, $countryId);
				$res		.= '</span>';
			}
		} else { 
			$res			= TrackWarnStatModel::getViewTable($carrierId, $channelId, $statType, $condition , $is_warn, $countryId);
		}
		self::$errCode  = TrackWarnStatModel::$errCode;
        self::$errMsg   = TrackWarnStatModel::$errMsg;
        return $res;
    }
	
	/**
	 * TrackWarnStatAct::act_viewPic()
	 * 列出某个运输方式各渠道各节点的（处理、时效）效率
	 * @param integer $carrierId 运输方式ID
	 * @param integer $channelId 渠道ID
	 * @param string $timeNode 时间条件
	 * @param string $statType 统计类型
	 * @return json string 
	 */
 	public function act_viewPic(){
		$condition	= "1";
		$title		= "";
		$countryStr	= "";
		$carrierId	= isset($_POST['carrierId']) ? abs(intval($_POST['carrierId'])) : 0;
		$channelId	= isset($_POST['channelId']) ? abs(intval($_POST['channelId'])) : 0;
		$countryId	= isset($_POST['countryId']) ? abs(intval($_POST['countryId'])) : 0;
		$timeNode	= isset($_GET['timeNode']) ? post_check(trim($_GET['timeNode'])) : '';
		$statType	= isset($_POST['statType']) ? post_check(trim($_POST['statType'])) : '';
		$is_warn	= isset($_POST['is_warn']) ? post_check(trim($_POST['is_warn'])) : 0;
		$act		= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod		= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if	(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 10000;
			self::$errMsg   = "对不起,您无跟踪号统计查看权限！";
			return false;
		}
		if (empty($carrierId)) {
			self::$errCode  = 10001;
			self::$errMsg   = "运输方式参数有误";
			return false;
		}
		if (empty($timeNode) || !in_array($timeNode,array('scanTime'))) {
			self::$errCode  = 10002;
			self::$errMsg   = "时间条件参数有误";
			return false;
		}
		if (empty($statType) || !in_array($statType,array('nodeEff','nodeEffPer','nodeTime','internalTime','todayWarnPer'))) {
			self::$errCode  = 10003;
			self::$errMsg   = "统计类型参数有误";
			return false;
		}
		if (!in_array($is_warn,array(0,1))) {
			self::$errCode  = 10004;
			self::$errMsg   = "预警天数参数有误";
			return false;
		}
		if (!empty($timeNode)) {
			$startTime		= isset($_GET['startTime']) ? strtotime(trim($_GET['startTime'])." 00:00:00") : strtotime(date("Y-m-d",time())." 00:00:00");
			$endTime		= isset($_GET['endTime']) ? strtotime(trim($_GET['endTime'])." 23:59:59") : strtotime(date("Y-m-d",time())." 23:59:59");
			if ($startTime && $endTime) {
				$condition	.= ' AND '.$timeNode." BETWEEN '".$startTime."' AND "."'".$endTime."'";
			}
		}
		if (empty($countryId)) {
			$countryStr	= " 国家";
		} else {
			$res	= TransOpenApiModel::getCountriesStandardById($countryId);
			$countryStr = " ({$res['countryNameCn']})";
		}
		
		
		switch ($statType) {
			case "nodeEff":
				$title	= $_GET['startTime'] == $_GET['endTime'] ? "{$_GET['startTime']}{$countryStr}各运输渠道节点处理效率统计" : "{$_GET['startTime']}——{$_GET['endTime']}{$countryStr}各运输渠道节点处理效率统计";
			break;
			case "nodeTime":
				$title	= $_GET['startTime'] == $_GET['endTime'] ? "{$_GET['startTime']}{$countryStr}各运输渠道节点处理时效统计" : "{$_GET['startTime']}——{$_GET['endTime']}{$countryStr}各运输渠道节点处理时效统计";
			break;
			case "nodeEffPer":
				$title	= $_GET['startTime'] == $_GET['endTime'] ? "{$_GET['startTime']}{$countryStr}各运输渠道节点处理效率百分比" : "{$_GET['startTime']}——{$_GET['endTime']}{$countryStr}各运输渠道节点处理效率百分比";
			break;
			case "internalTime":
				$title	= $_GET['startTime'] == $_GET['endTime'] ? "{$_GET['startTime']}{$countryStr}各运输渠道处理时效" : "{$_GET['startTime']}——{$_GET['endTime']}{$countryStr}各运输渠道处理时效";
			break;
		}
		if (in_array($statType,array('todayWarnPer'))) {
			$res			= "";
			$nodeArr		= TransOpenApiModel::getRandTrackNodeList($carrierId);
			foreach ($nodeArr as $key=>$nd) {
				$condition	= array(1,$timeNode,$startTime,$endTime,$key,"container".$key);
				$res		.= TrackWarnStatModel::getViewTodayPic($carrierId, $channelId, $statType, $condition , "{$nd['nodeName']}节点--各渠道预警率信息一览表", $is_warn, $countryId);
			}				
		} else {
			$res			= TrackWarnStatModel::getViewPic($carrierId, $channelId, $statType, $condition, $title, $is_warn, $countryId);
		}
		self::$errCode  = TrackWarnStatModel::$errCode;
        self::$errMsg   = TrackWarnStatModel::$errMsg;
        return $res;
    }
}
?>