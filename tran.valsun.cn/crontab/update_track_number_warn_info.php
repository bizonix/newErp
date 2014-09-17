<?php
error_reporting(0);
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
require_once '/data/web/tran.valsun.cn/crontab/config.php';
require_once SCRIPT_PATH."framework.php";
Core::getInstance();
$numberList 	= array();//跟踪号列表
$nodeList		= array();//预警节点列表
$page 			= isset($argv[1]) ? abs(intval($argv[1])) : 1;//页码
$pagenum		= isset($argv[2]) ? abs(intval($argv[2])) : 2000;//每页多少条
$carrierId		= !empty($argv[3]) ? abs(intval($argv[3])) : "";//运输方式ID
$where			= isset($argv[4]) ? $argv[4] : "";//跟踪号状态
$numberList 	= TransOpenApiModel::getTrackNumberList($page, $pagenum, $carrierId, $where);//获取符合条件的跟踪号列表
$total			= count($numberList);
$chidArr		= array('9'=>23,'8'=>43,'46'=>65,'47'=>66,'53'=>72);
echo $total."条数据抓取开始,时间".date('Y-m-d H:i:s',time())."\n\n";
foreach ($numberList as $v) {
	$data			= array();
	$timestr= date('Y-m-d h:i:s', time());
	$detailInfo	= TransOpenApiModel::getTrackInfoLocal($v['trackNumber'], $v['carrierId']);
	echo $res,"======",$v['trackNumber'],"===[$timestr]\n";
	//print_r($detailInfo);
	$nodeEff 		= "";
	$nodePlaceEff 	= "";
	if (is_array($detailInfo)) {
		$detailCount	= count($detailInfo);
		if ($detailCount > 0) //有跟踪数据插入
		{	
			$detailInfo['trackingEventList'] = $detailInfo;
			$detailInfo['numberInfo']	= $v;
			foreach ($detailInfo['trackingEventList'] as $key=>$val) {
				unset($detailInfo['trackingEventList'][$key]);
				if ($key==0) { //得到跟踪号渠道信息
					$channelId	= TransOpenApiModel::getCarrierChannelByPostName($detailInfo['numberInfo']['carrierId'], $val['postion']);
					if (!empty($channelId)) {
						$detailInfo['numberInfo']['channelId'] = $channelId;
					} else {
						$detailInfo['numberInfo']['channelId'] = $chidArr[$v['carrierId']];
					}
					if (eregi("^(R.+SG)",$v['trackNumber'])) $detailInfo['numberInfo']['channelId'] = 70;
					if (eregi("^(E.+SG)",$v['trackNumber'])) $detailInfo['numberInfo']['channelId'] = 71;
				}
				$detailInfo['trackingEventList'][$key]['place']		= $val['postion'];
				$detailInfo['trackingEventList'][$key]['details']	= $val['event'];
				$detailInfo['trackingEventList'][$key]['date']		= date('Y-m-d H:i:s',$val['trackTime']);
			}
			print_r($detailInfo['numberInfo']);			
			$res_warn	= TransOpenApiModel::autoWarnInfo($detailInfo);//自动预警
			echo $res_warn,"=====",date('Y-m-d h:i:s', time()),"\n";
		}	
	}
}
if (!count($numberList)) {
	print_r($argv);
	echo "无数据，请确认你输入的条件！\n";
}
echo "\n\n全部数据抓取执行完毕,完成时间".date('Y-m-d H:i:s',time())."\n";
$dbConn->close();
exit;
?>
