<?php
error_reporting(0);
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
require_once '/data/web/tran.valsun.cn/crontab/config.php';
require_once SCRIPT_PATH."framework.php";
Core::getInstance();
$carrierId	= isset($argv[1]) ? abs(intval($argv[1])) : 0;//运输方式ID
$item		= isset($argv[2]) ? $argv[2] : "todayPer";//运输方式ID
$day		= isset($argv[3]) ? $argv[3] : "30,60";//几天内
$css_height	= "line-heigh:180%";

$system_name= "运输方式管理系统";
$system_url	= "http://tran.valsun.cn/";
$type	= "email";	//消息发送类型
$from	= "管拥军"; //发送人
// $from	= "温小彬"; //发送人
/* 获取订阅人列表start */
$english_id	= 'daily_warning_rates'; //订阅系统的邮件ID
$jsonp		= 1;
$list		= array();
$userList	= TransOpenApiModel::getMailUser($english_id, $jsonp);
$userParam	= json_decode($userList, true);
foreach($userParam['data'] as $keyvar=>$users) {
	$list[]		= $users['global_user_name'];
}
$to			= implode(",", $list);
echo $to,"===\n";
/* 获取订阅人列表end */
//$to		= "陈文平,陈文辉,王绪成,陈前,王晓华,陈燕云,罗莉,陈小霞,陈月葵,郑凤娇,李美琴,包凤明,雷贤容,张振祥,林正祥,肖金华,钟衍台,周聪,李高飞,席慧超,张容,陈小兰,覃云云,管拥军,廖海英,韩庆新,仝召燕,陈智兴"; //接收者
//$to		= "管拥军,周聪,林正祥"; //接收者
$res	= TransOpenApiModel::getCarrierById($carrierId);
if (empty($carrierId) || empty($res)) {
	print_r($argv);
	echo "运输方式ID有误！\n";
	exit;
}
if ($item=="todayPer") {
	$dayArr		= explode(",",$day);//统计时间：30天，60天内
	$title		= "【各渠道处理效率简报】".date('Y-m-d',time())." 运输方式{$res['carrierNameCn']}";
	$table		= '<p style="'.$css_height.'"><b>大家好:</b><br/>以下为运输方式<b>'.$res['carrierNameCn'].'</b>各渠道各节点处理效率简报，请查阅</p>';
	foreach($dayArr as $days) {
		$startTime	= strtotime(date('Y-m-d',(time()-86400*$days))." 00:00:01");
		$endTime	= strtotime(date('Y-m-d',time())." 23:59:59");
		$condition	= "1";
		$condition	.= " AND scanTime BETWEEN {$startTime} AND {$endTime}";
		echo $condition,"\n";
		echo "开始时间".date('Y-m-d H:i:s',time())."\n\n";
		$channelArr	= TransOpenApiModel::getCarrierChannel($carrierId);
		$table		.= '<p style="'.$css_height.'">'.$days.'天以内的统计数据<br/></p><table border="1" cellpadding="0" cellspacing="0" width="791">';
		foreach ($channelArr as $k=>$ch) {
			$nodeArr= TransOpenApiModel::getTrackNodeList($carrierId,$ch['id']);
			if ($k==0) {
				$table	.= "<tr><th>渠道</th>";
				foreach ($nodeArr as $v) {
					$table	.= "<th>{$v['nodeName']}</th><th>已处理</th><th style=\"color:red\">预警率</th>";
				}
				$table	.= "</tr>";
			}
			$table	.= "<tr><td>{$ch['channelName']}</td>";
			$key	= 0;
			$percent= 0;
			$nodeStr= "";
			$nodeEffStr = "";
			$nodeWarnStr="";
			foreach ($nodeArr as $nd) {
				if($key==0) {
					$nodeEffStr	= " AND nodeEff like '1%'";
					$nodeWarnStr	= " AND warnLevel NOT like '1%'";
				} else {
					$nodeStr	= str_pad($nodeStr,$key,"_",STR_PAD_LEFT);
					$nodeEffStr	= " AND nodeEff like '{$nodeStr}1%'";
					$nodeWarnStr= " AND warnLevel NOT like '{$nodeStr}1%'";
				}		
				$total		    = TrackWarnStatModel::getNodeEffTotal($carrierId, $ch['id'], $condition, $nd['id']);
				$realTotal		= TrackWarnStatModel::getNodeEff($carrierId, $ch['id'], $condition.$nodeEffStr, $nd['id']);
				$warnTotal		= TrackWarnStatModel::getNodeEff($carrierId, $ch['id'], $condition.$nodeWarnStr.$nodeEffStr, $nd['id']);
				$realPercent 	= round($realTotal/$total,2)*100;
				$warnPercent 	= round(($realTotal-$warnTotal)/$total,2)*100;
				$table	.= "<td>{$total}</td><td>{$realPercent}%</td><td style=\"color:red\">{$warnPercent}%</td>";
				$key++;
			}
			$table	.= "</tr>";
		}
		$table	.= "</table>";
	}
	$table	.= '<p style="'.$css_height.'">详情请登录：<a href="'.$system_url.'" target="_blank">'.$system_name.'</a><br/></br>'.date('Y-m-d',$endTime).'<br/>'.$system_name.'</p>';
	echo $table,"\n";
} else {
	$startTime	= strtotime("-{$day} day"." 00:00:01");
	$endTime	= strtotime(date('Y-m-d')." 23:59:59");
	$table	= "";
	$title	= "【日预警率简报】".date('Y-m-d',time())." 运输方式{$res['carrierNameCn']}渠道各节点{$day}日内预警率数据一览表";
	$nodeArr= TransOpenApiModel::getRandTrackNodeList($carrierId);
	foreach ($nodeArr as $key=>$nd) {
		$channelId	= $ch['id'];
		$timeNode	= "scanTime";
		$condition	= array(1,$timeNode,$startTime,$endTime,$key,$nd['nodeName'].'节点--各渠道预警率信息一览表');
		$table	.= "<p style=\"{$css_height}\">".TrackWarnStatModel::getViewTodayTable($carrierId, $channelId, "todayWarnPer", $condition, 0, 0)."</p>";
	}
	$table	.= '<p style="'.$css_height.'">详情请登录：<a href="'.$system_url.'" target="_blank">'.$system_name.'</a><br/></br>'.date('Y-m-d',$endTime).'<br/>'.$system_name.'</p>';
	echo $table,"\n";
}
$message = TransOpenApiModel::sendMessage("{$type}","{$from}","{$to}",$table,"{$title}");
echo $message,"\n";
echo "\n\n完成时间".date('Y-m-d H:i:s',time())."\n";
$dbConn->close();
exit;
?>
