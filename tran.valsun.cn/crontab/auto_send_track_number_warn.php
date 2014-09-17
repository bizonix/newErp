<?php
/**
 * 功能：跟踪号数量预警脚本
 * 版本：1.0
 * 日期：2014/06/05
 * 作者：管拥军
 */

error_reporting(0);
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
require_once '/data/web/tran.valsun.cn/crontab/config.php';
require_once SCRIPT_PATH."framework.php";
Core::getInstance();
$carrierId		= isset($argv[1]) ? abs(intval($argv[1])) : 0; //运输方式ID
$warnNum		= isset($argv[2]) ? abs(intval($argv[2])) : 5000; //预警数量，默认1000
##################### 预警开始 ########################
$css_height		= "line-heigh:180%";
$system_name	= "运输方式管理系统";
$system_url		= "http://tran.valsun.cn/";
$type			= "email";	//消息发送类型
$from			= "管拥军"; //发送人
$to				= "管拥军,夏良,陈前,王凤珠,陈晓兰B,于雅杰,魏凤玲,叶霄,王友芝,范雪琴,胡涛"; //接收者
$res			= TransOpenApiModel::getCarrierById($carrierId);
if (empty($carrierId) || empty($res)) {
	print_r($argv);
	echo "运输方式ID有误！\n";
	exit;
}

//各运输方式下渠道跟踪号使用统计
$chArr			= TransOpenApiModel::getCarrierChannel($carrierId);
$total			= TrackNumberModel::modListCount("carrierId = '{$carrierId}'");
$used			= TrackNumberModel::modListCount("carrierId = '{$carrierId}' AND orderId > 0");
$last			= TrackNumberModel::modListCount("carrierId = '{$carrierId}' AND orderId = 0");
$title			= "【跟踪号可用数量预警】".date('Y-m-d',time())." 运输方式{$res['carrierNameCn']}";
$table			= '<p style="'.$css_height.'"><b>大家好:</b><br/>以下为运输方式<b>'.$res['carrierNameCn'].'</b>跟踪号可用数量预警简报，请查阅</p>';
$table			.= '<table border="1" cellpadding="0" cellspacing="0" width="791"><tr><td height="25px"><b>运输方式</b></td><td><b>跟踪号总数</b></td><td><b>已用数量</b></td><td><b>剩余数量</b></td></tr>';
$table			.= '<tr><td height="20px">'.$res['carrierNameCn'].'</td><td>'.$total.'</td><td>'.$used.'</td><td>'.$last.'</td></tr>';
$flag			= false;
foreach($chArr as $v) {
	$total_ch	= TrackNumberModel::modListCount("carrierId = '{$carrierId}' AND channelId = '{$v['id']}'");
	$used_ch	= TrackNumberModel::modListCount("carrierId = '{$carrierId}' AND channelId = '{$v['id']}' AND orderId > 0");
	$last_ch	= TrackNumberModel::modListCount("carrierId = '{$carrierId}' AND channelId = '{$v['id']}' AND orderId = 0");
	if($carrierId == 88) {
		$total_cur	= TrackNumberModel::modListCount("carrierId = '{$carrierId}' AND countrys = 'Switzerland'");
		$used_cur	= TrackNumberModel::modListCount("carrierId = '{$carrierId}' AND countrys = 'Switzerland' AND orderId > 0");
		$last_cur	= TrackNumberModel::modListCount("carrierId = '{$carrierId}' AND countrys = 'Switzerland' AND orderId = 0");
		$table 		.= '<tr><td height="20px">'.$res['carrierNameCn'].'->非瑞士国家</td><td>'.($total-$total_cur).'</td><td>'.($used-$used_cur).'</td><td>'.($last-$last_cur).'</td></tr>';
		$table 		.= '<tr><td height="20px">'.$res['carrierNameCn'].'->瑞士国家</td><td>'.$total_cur.'</td><td>'.$used_cur.'</td><td>'.$last_cur.'</td></tr>';
	}
	if($last_ch <= $warnNum) $flag = true;
	if($carrierId == 2) $table .= '<tr><td height="20px">'.$res['carrierNameCn']."->".$v['channelName'].'渠道</td><td>'.$total_ch.'</td><td>'.$used_ch.'</td><td>'.$last_ch.'</td></tr>';
}
$table		.= '</table>';
$table		.= '<p style="'.$css_height.'">详情请登录：<a href="'.$system_url.'" target="_blank">'.$system_name.'</a><br/></br>'.date('Y-m-d').'<br/>'.$system_name.'</p>';
echo $table,"\n";

//跟踪号可用数低于多少发预警邮件
if($flag || $last <= $warnNum) {
	$message = TransOpenApiModel::sendMessage("{$type}","{$from}","{$to}",$table,"{$title}");
	echo $message,"\n";
}
echo "\n\n完成时间".date('Y-m-d H:i:s')."\n";
exit;
?>
