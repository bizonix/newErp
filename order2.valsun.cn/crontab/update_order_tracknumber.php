<?php
error_reporting(-1);
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
require "/data/web/order.valsun.cn/framework.php";
Core::getInstance();

/*
require_once WEB_PATH . 'lib/rabbitmq/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPConnection('112.124.41.121', 5672, 'valsun_warehouse','warehouse%123','valsun_warehouse');*/

echo date("Y-m-d H:i:s").": 开始更新!\r\n";
$startTime	= strtotime("-3 days", time()); 
$endTime	= time();
$url = 'http://api.wh.valsun.cn/json.php?mod=orderTracknumber&act=getOrderTracknumberBydate&jsonp=1&startTime='.$startTime.'&endTime='.$endTime;
$data = file_get_contents($url);
$data = json_decode($data,true);
$ret = OrderTracknumberModel::updateOrderTracknumber($data['data']);
if(!$ret) {
	echo '更新失败!, 原因: errCode'.OrderTracknumberModel::$errCode.', errMsg: '.OrderTracknumberModel::$errMsg;
	exit;
}
echo date("Y-m-d H:i:s").": 更新成功!\r\n";exit;
