<?php
/**
 * 类名：TrackWarnExportAct
 * 功能：运输方式跟踪号报表导出动作处理层
 * 版本：1.0
 * 日期：2014/01/02
 * 作者：管拥军
 */
  
class TrackWarnExportAct {
    public static $errCode	= 0;
	public static $errMsg	= "";

	/**
	 * TrackWarnExportAct::act_exportTrackInfo()
	 * 导出跟踪信息
	 * @param integer $carrierId 运输方式ID
	 * @param string $status 跟踪号状态
	 * @return json string 
	 */
 	public function act_exportTrackInfo(){
		$countryId		= isset($_GET['countryId']) ? intval($_GET['countryId']) : 0;
		$carrierId		= isset($_GET['carrierId']) ? intval($_GET['carrierId']) : 0;
		$channelId		= isset($_GET['channelId']) ? intval($_GET['channelId']) : 0;
		$type			= isset($_GET['type']) ? trim($_GET['type']) : '';
		$key			= isset($_GET['key']) ? post_check(trim($_GET['key'])) : '';
		$timeNode		= isset($_GET['timeNode']) ? post_check(trim($_GET['timeNode'])) : '';
		$warnLevel		= isset($_GET['warnLevel']) ? intval($_GET['warnLevel']) : '';
		$is_warn		= isset($_GET['is_warn']) ? intval($_GET['is_warn']) : 1;
		$status			= isset($_GET['status']) ? intval($_GET['status']) : -1;
		$condition		= "1";
		if (!empty($countryId)) {
			$condition	.= " AND a.countryId = '{$countryId}'";
		}
		if ($status>=0) {
			$condition	.= " AND a.status = '{$status}'";
		}
		if (!empty($carrierId)) {
			$condition	.= " AND a.carrierId = '{$carrierId}'";
		}
		if (!empty($channelId)) {
			$condition	.= " AND a.channelId = '{$channelId}'";
		}
		if (!empty($timeNode)) {
			if(!in_array($timeNode,array('scanTime','lastTime','trackTime'))) redirect_to("index.php?mod=trackWarnInfo&act=index");
			$startTime		= isset($_GET['startTime']) ? strtotime(trim($_GET['startTime'])." 00:00:00") : strtotime(date("Y-m-d",time())." 00:00:00");
			$endTime		= isset($_GET['endTime']) ? strtotime(trim($_GET['endTime'])." 23:59:59") : strtotime(date("Y-m-d",time())." 23:59:59");
			if ($startTime && $endTime) {
				$condition	.= ' AND a.'.$timeNode." BETWEEN '".$startTime."' AND "."'".$endTime."'";
			}
		}		
		if ($type && $key) {
			if (!in_array($type,array('orderSn','trackNumber','recordId'))) redirect_to("index.php?mod=trackWarnInfo&act=index");
			$condition	.= ' AND a.'.$type." = '".$key."'";
		}
		if ($warnLevel === 0) {//全部节点预警
			$condition	.= " AND a.warnLevel > 0";
		} elseif ($warnLevel === -1) { //没预警节点
			$condition	.= " AND a.warnLevel = 0";
		} elseif (!empty($warnLevel)) { //某个预警节点
			$warnStr	= str_pad($warnStr,($warnLevel-1),"_",STR_PAD_LEFT);
			switch ($is_warn) {
				case 1:
					$condition	.= " AND a.warnLevel like '{$warnStr}1%'";
				break;
				case 2:
					$condition	.= " AND a.warnLevel like '{$warnStr}0%' AND a.nodeEff like '{$warnStr}1%'";
				break;
				case 3:
					$condition	.= " AND a.nodeEff like '{$warnStr}1%'";
				break;
				default:
					$condition	.= " AND a.warnLevel like '{$warnStr}1%'";
			}
		}
		$act			= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod			= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if (!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 10000;
			self::$errMsg   = "对不起,您无跟踪号数据导出权限！";
			return "fail";
		}
		$res			= TrackWarnExportModel::exportTrackNumberInfo($condition);
		self::$errCode  = TrackWarnExportModel::$errCode;
        self::$errMsg   = TrackWarnExportModel::$errMsg;
        return $res;
    }	
}
?>