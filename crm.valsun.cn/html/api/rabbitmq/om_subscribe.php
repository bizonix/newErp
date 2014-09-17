<?php
error_reporting(0);
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
include "../../../framework.php";
Core::getInstance();

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPConnection('192.168.200.222', 5672, 'xiaojinhua', 'jinhua','mq_vhost1');
$channel = $connection->channel();


$channel->exchange_declare('power', 'topic', false, false, false);

list($queue_name, ,) = $channel->queue_declare("", false, false, true, false);

$channel->queue_bind($queue_name, 'power');

echo ' [*] Waiting for logs. To exit press CTRL+C', "\n";

//连接数据库执行接收到的SQL语句
// $sql 	= "SELECT * FROM ph_stock_invoice LIMIT 10";
// $query	= $dbConn->query($sql);
// $ret	= $dbConn->fetch_array_all($query);
// print_r($ret);


$callback = function($msg){
	global $dbConn;
	$sql = $msg->body;
	$query	= $dbConn->query($sql);
	if (!$query) {
		Log::write($sql, Log::ERR);
		//Log::write($errorStr,Log::ERR)
	}
	// $result	= $dbConn->fetch_array_all($query);
	// print_r($result);
	//echo ' [x] ', $sql, "\n";
};

$channel->basic_consume($queue_name, '', false, true, false, false, $callback);

while(count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();

?>