<?php
define('SCRIPTS_PATH_CRONTAB', '/data/web/erpNew/order.valsun.cn/crontab/');    
require_once SCRIPTS_PATH_CRONTAB."scripts.comm.php";

$rabbitMQClass = new RabbitMQClass('xiaojinhua','jinhua','/');//队列对象

$exchange = 'power';
$queue_name = 'rabbitmq_order_power_queue';

$sqls = $rabbitMQClass->queue_subscribe($exchange,$queue_name,false);
$count_sqls = count($sqls);
if($count_sqls == 0){
	exit;	
}
echo "已接收队列中 ".$count_sqls." 条数据\n";

foreach($sqls as $sql){
	//if(get_magic_quotes_gpc())//如果get_magic_quotes_gpc()是打开的
	//{
		//$sql=addslashes($sql);//将字符串进行处理
		echo $sql; echo "\n";
	//}
	$con_stat = mysql_ping();
	$query	= $dbConn->query($sql);
	if (!$query) {
		Log::write($con_stat.'---'.$sql, Log::ERR);
		//Log::write($errorStr,Log::ERR)
	} else {
		echo ' [ok] ', $con_stat,'---', $sql, "\n";
	}
	$dbConn->close();
}
?>