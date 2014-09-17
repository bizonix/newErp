<?php
/**
 * 功能：跟踪号批量作废脚本
 * 版本：1.0
 * 日期：2014/06/24
 * 作者：管拥军
 */

error_reporting(0);
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
require_once '/data/web/tran.valsun.cn/crontab/config.php';
require_once SCRIPT_PATH."framework.php";
Core::getInstance();
$carrierId		= isset($argv[1]) ? abs(intval($argv[1])) : 0; //运输方式ID
$where			= isset($argv[2]) ? $argv[2] : ''; //where条件
$limits			= isset($argv[3]) ? $argv[3] : 5000; //一次清理多少个
if(empty($carrierId)) {
	print_r($argv);
	echo date('Y-m-d H:i:s')."==="."运输方式ID有误！\n";
	exit;
}
if(empty($where)) {
	print_r($argv);
	echo date('Y-m-d H:i:s')."==="."条件有误！\n";
	exit;
}
######################### 跟踪号清理开始 ############################
$sql 	= "SELECT count(*) FROM `trans_track_numbers` WHERE {$where} AND `carrierId` = '{$carrierId}' AND `orderId` = 0 AND is_delete = 0";
echo date('Y-m-d H:i:s')."===".$sql,"\n";
$query	= $dbConn->query($sql);
$res	= $dbConn->fetch_row($query);
$total 	= isset($res[0]) ? $res[0] : 0;
$pages	= ceil($total/$limits);
if(empty($total)) {
	print_r($argv);
	echo date('Y-m-d H:i:s')."==="."当前运输方式没有需要作废的跟踪号!\n";
	exit;
}
echo date('Y-m-d H:i:s')."==="."共有{$total}条跟踪号，分{$pages}批次作废===开始时间===".date('Y-m-d H:i:s')."\n";
//循环作废跟踪号
for($i=0; $i<$pages; $i++) {
	$sql 	= "SELECT id FROM `trans_track_numbers` WHERE {$where} AND `carrierId` = '{$carrierId}' AND `orderId` = 0 AND is_delete = 0 LIMIT {$limits}";
	echo date('Y-m-d H:i:s')."===".$sql,"\n";
	$query	= $dbConn->query($sql);
	$res 	= $dbConn->fetch_array_all($query);
	foreach($res as $v) {
		$sql 	= "UPDATE `trans_track_numbers` SET is_delete = 1 WHERE id = '{$v['id']}'";
		echo date('Y-m-d H:i:s')."===".$sql,"===";
		$query	= $dbConn->query($sql);
		$rows 	= $dbConn->affected_rows();
		echo $rows,"\n";
	}
	//跑完一批休息下
	sleep(5);
}
echo "完成时间===".date('Y-m-d H:i:s')."\n";
exit;
?>