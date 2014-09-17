<?php
/**
 * 功能：跟踪号最后更新时间检测脚本
 * 版本：1.0
 * 日期：2014/04/30
 * 作者：管拥军
 */
 
error_reporting(-1);
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
include "/data/web/tran.valsun.cn/framework.php";
Core::getInstance();
// global $dbConn;
$carrierId	= isset($argv[1]) ? $argv[1] : 0; //运输方式ID，默认全部
$days		= isset($argv[2]) ? $argv[2] : 10; //几天内没有更新追踪信息
$upStatus	= isset($argv[3]) ? $argv[3] : 11; //更新状态，默认11，更新异常
$status		= isset($argv[4]) ? $argv[4] : 2; //当前状态，默认转运中
$lastTime	= strtotime("-{$days} days"." 00:00:00");
if(empty($carrierId)) {
	$sql		= "UPDATE trans_track_number SET `status` = '{$upStatus}' WHERE lastTime <= {$lastTime} AND trackTime>= {$lastTime} AND `status` IN({$status})";
} else {
	$sql		= "UPDATE trans_track_number SET `status` = '{$upStatus}' WHERE carrierId = '{$carrierId}' AND lastTime <= {$lastTime} AND trackTime>= {$lastTime} AND `status` IN({$status})";
}
echo date('Y-m-d H:i:s')."===执行语句\n".$sql."\n";
$query	= $dbConn->query($sql);
if($query) {
	$rows 	= $dbConn->affected_rows();           
	echo date('Y-m-d H:i:s')."===共更新{$rows}条数据\n";
} else {
	echo date('Y-m-d H:i:s')."===无法执行语句\n";
}
exit;
?>