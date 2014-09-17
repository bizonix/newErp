<?php
/**
 * 功能：跟踪邮件推送脚本
 * 版本：2.0
 * 日期：2014/07/15
 * 作者：管拥军
 */

error_reporting(-1);
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
require_once '/data/web/tran.valsun.cn/crontab/config.php';
include_once SCRIPT_PATH."framework.php";
Core::getInstance();

############### 脚本运行参数 ########################
$carrierId	= isset($argv[1]) ? $argv[1] : 61; //运输方式ID
$markTime	= isset($argv[2]) ? $argv[2] : 3; //几天内标记发货，默认3天内
$platForm	= isset($argv[3]) ? $argv[3] : 'aliexpress'; //跟踪邮件平台,默认速卖通
$retryCount	= isset($argv[4]) ? $argv[4] : 3; //邮件失败重发次数
$sleepTime	= isset($argv[5]) ? $argv[5] : 3; //间隔多少秒请求一次
$page		= isset($argv[6]) ? $argv[6] : 1; //页码
$pagesize	= isset($argv[7]) ? $argv[7] : 1000; //每页多少条
$setLimit	= isset($argv[8]) ? $argv[8] : 1800; //默认最大执行时间1800秒
set_time_limit($setLimit);
$markTime	= strtotime("-{$markTime} day".' 00:00:01');

//获取符合条件的跟踪号列表
$res	= TrackEmailStatModel::trackEmailInfo($carrierId, $platForm, $markTime, $page, $pagesize);
if(count($res)==0) exit(date('Y-m-d H:i:s')."==={$platForm}==={$markTime},暂无跟踪邮件可推！\n");

//获取各平台跟踪邮件模版
$tp				= TrackEmailStatModel::trackEmailTemplat($platForm);
if(count($tp)==0) exit(date('Y-m-d H:i:s')."==={$platForm},暂无跟踪邮件模版可用！\n");
$email_title	= $tp['title'];
$email_body		= $tp['content'];

############### 初始化变量 ########################
$sendTime		= '';
$markTime		= '';
$recordId		= '';
$trackNum		= '';
$trackUrl		= '';
$userId			= '';
$title			= '';
$content		= '';
$userEmail		= '';
$userName		= '';
$smtpHost		= '';
$smtpPort		= '';
$smtpUser		= '';
$smtpPwd		= '';
$platAccount	= '';
$log			= '';
$totals			= count($res);
$i				= 1;
$logFile		= WEB_PATH."log/track_email/".date('Y')."/".date('m')."/".date('Y-m-d')."_".$platForm.".log";
$log			.= date('Y-m-d H:i:s')."==={$carrierId}==={$platForm}==={$markTime}===跟踪邮件推送开始！\n";
echo date('Y-m-d H:i:s')."==={$page}==={$carrierId}==={$platForm}==={$markTime}===跟踪邮件推送开始！\n";
//循环推送邮件
foreach($res as $v) {
	$sendTime		= date('Y-m-d H:i:s');
	$markTime		= date('Y-m-d H:i:s',$v['toMarkTime']);
	$recordId		= $v['recordId'];
	$trackNum		= $v['trackNumber'];
	$trackUrl		= '<a href="http://www.wedoexpress.com/rest?us=email&carrier=wedo&tracknum='.$trackNum.'" target="_blank">http://www.wedoexpress.com/rest?carrier=wedo&tracknum='.$trackNum.'</a>';
	$toUserId		= $v['toUserId'];
	$toEmailSend	= $v['toEmailSend'];
	$toUserEmail	= $v['toUserEmail'];
	$userEmail		= $v['userEmail'];
	$userName		= $v['userName'];
	$smtpHost		= $v['smtpHost'];
	$smtpPort		= $v['smtpPort'];
	$smtpUser		= $v['smtpUser'];
	$smtpPwd		= $v['smtpPwd'];
	$platAccount	= $v['platAccount'];
	if(!empty($toEmailSend)) continue;
	//格式化邮件内容
	$title			= str_replace("<wedo:recordId>", $recordId, $email_title);
	$content		= str_replace(array('<wedo:recordId>', '<wedo:userId>', '<wedo:markTime>', '<wedo:trackNum>', '<wedo:trackUrl>', '<wedo:userEmail>', '<wedo:userName>', '<wedo:sendTime>'),array($recordId, $toUserId, $markTime, $trackNum, $trackUrl, $userEmail, $userName, $sendTime), $email_body);
	$log			.= date('Y-m-d H:i:s')."==={$recordId}==={$toUserId}==={$toUserEmail}===";
	echo date('Y-m-d H:i:s')."==={$recordId}==={$toUserId}==={$toUserEmail}===\n";
	//发邮件
	$emailData		= array(
						'smtpHost'		=> $smtpHost,
						'smtpPort'		=> $smtpPort,
						'smtpUser'		=> $smtpUser,
						'smtpPwd'		=> $smtpPwd,
						'title'			=> $title,
						'content'		=> $content,
						'toUserEmail'	=> $toUserEmail,
						'toUserId'		=> $toUserId,
						'userEmail'		=> $userEmail,
						'userName'		=> $userName,
						'trackNumber'	=> $trackNum,
						'retryCount'	=> $retryCount,
					);
	$result			= TrackEmailStatModel::sendTrackEmail($emailData);
	$log			.= "SES状态数据:".json_encode($result)."\n";
	print_r($result);
	$email_flag		= $result['sendFlag'];
	$MessageId		= $result['MessageId'];
	$RequestId		= $result['RequestId'];
	//保存邮件发送记录
	$emailStat		= array(
						'trackNumber'	=> $trackNum,
						'content'		=> $content,
						'platAccount'	=> $platAccount,
						'MessageId'		=> $MessageId,
						'RequestId'		=> $RequestId,
						'is_success'	=> $email_flag,
						'addTime'		=> time(),
					);
	$stat_flag		= TrackEmailStatModel::saveTrackEmail($emailStat);
	$log			.= date('Y-m-d H:i:s')."==={$i}/{$totals}==={$trackNum}===邮件发送状态:{$email_flag}===邮件记录状态:{$stat_flag}\n";
	echo date('Y-m-d H:i:s')."===={$i}/{$totals}==={$trackNum}===邮件发送状态:{$email_flag}===邮件记录状态:{$stat_flag}\n";
	sleep($sleepTime);
	$i++;
}
$log				.= date('Y-m-d H:i:s')."==={$totals}封邮件已推送完毕!\n\n";
echo "\n\n".date('Y-m-d H:i:s')."==={$totals}封邮件已推送完毕!\n\n";
if(function_exists('write_a_file')) {
	write_a_file($logFile, $log);
}
exit;
?>
