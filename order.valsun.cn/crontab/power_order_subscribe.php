<?php
error_reporting(-1);
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
require "../framework.php";
Core::getInstance();

require_once __DIR__ . '/../lib/rabbitmq/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPConnection('115.29.188.246', 5672, 'valsun_power', 'power%123','power');

$queue_name = 'test_rabbitmq_order_power_queue';
//$connection = new AMQPConnection('192.168.128.129', 5672, 'peter', 'peter','mq_vhost1');//5672是端口号，两个peter分别是用户名和密码

$channel = $connection->channel();
//$channel->exchange_declare('power', 'topic', false, false, false);
//第三个参数 true 会检测交换器是否存在 ，第4个参数 true 表示 服务器重启时，交换器依然不会消失，第5个参数false 表示 如果交换器删掉，消息通道依然生效
$channel->exchange_declare('power', 'fanout', false, false, false);

//list($queue_name, ,) = 
$channel->queue_declare($queue_name, false, false, false, false);

$channel->queue_bind($queue_name, 'power');

echo ' [*] Waiting for logs. To exit press CTRL+C', "\n";

$callback = function($msg){
	$db_config	=	C("DB_CONFIG");
	$dbConn		=	new mysql();
	$dbConn->connect($db_config["master1"][0],$db_config["master1"][1],$db_config["master1"][2],'');
	$dbConn->select_db($db_config["master1"][4]);
  	echo ' [x] ', $msg->body, "\n";
	$sql = $msg->body;
	$con_stat = mysql_ping();
	$query	= $dbConn->query($sql);
	if (!$query) {
		Log::write($con_stat.'---'.$sql, Log::ERR);
		//Log::write($errorStr,Log::ERR)
	} else {
		echo ' [ok] ', $con_stat,'---', $sql, "\n";
	}
};
$channel->basic_consume($queue_name, '', false, true, false, false, $callback);

while(count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();
//$dbConn->close();
?>