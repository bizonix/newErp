<?php
/*
 *
 */
error_reporting(-1);
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
require "/data/web/purchase.valsun.cn/framework.php";
Core::getInstance();

//require_once __DIR__ . '/data/web/wh.valsun.cn/lib/rabbitmq/vendor/autoload.php';
require_once __DIR__ . '/../lib/rabbitmq/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPConnection('115.29.188.246', 5672, 'valsun_pc','pc%123','valsun_pc');
$exchange_name = 'rabbitmq_skuConversion_exchange';
$queue_name = 'purchase_rabbitmq_comversion_queue';

$channel = $connection->channel();
//第三个参数 true 会检测交换器是否存在 ，第4个参数 true 表示 服务器重启时，交换器依然不会消失，第5个参数false 表示 如果交换器删掉，消息通道依然生效
$channel->exchange_declare($exchange_name, 'fanout', false, false, false);
$channel->queue_declare($queue_name, false, false, false, false);
$channel->queue_bind($queue_name, $exchange_name);

echo ' [*] Waiting for logs. To exit press CTRL+C', "\n";

$callback = function($msg){
	// global $dbConn;
	//var_dump($dbConn);
	//常连接MYSQL
	$db_config	=	C("DB_CONFIG");
	$dbConn		=	new mysql();
	$dbConn->connect($db_config["master1"][0],$db_config["master1"][1],$db_config["master1"][2],'');
	$dbConn->select_db($db_config["master1"][4]);
	//$con_stat = $dbConn->ping();
	$sql = $msg->body;
	//echo $sql;echo "\n\n";
	$query	= $dbConn->query(json_decode($sql));
	if (!$query) {
		//Log::write($con_stat.'---'.$sql, Log::ERR);
		Log::write($sql, Log::ERR);
	} else {
		//echo ' [ok] ', $con_stat,'---', $sql, "\n";
		echo ' [ok] ', '---', $sql, "\n";
	}
	$dbConn->close();
};

$channel->basic_consume($queue_name, '', false, true, false, false, $callback);

while(count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();
?>
