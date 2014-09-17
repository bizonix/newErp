<?php

define('SCRIPTS_PATH_CRONTAB', '/data/web/purchase.valsun.cn/crontab/');    
require_once SCRIPTS_PATH_CRONTAB."scripts.comm.php";

require_once WEB_PATH."lib/rabbitmq/vendor/autoload.php";
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPConnection('192.168.200.222', 5672, 'xiaojinhua', 'jinhua','/');
$channel = $connection->channel();


$channel->exchange_declare('power', 'fanout', false, false, false);

list($queue_name, ,) = $channel->queue_declare("", false, false, false, false);

$channel->queue_bind($queue_name, 'power');

echo ' [*] Waiting for logs. To exit press CTRL+C', "\n";

//连接数据库执行接收到的SQL语句
// $sql 	= "SELECT * FROM ph_stock_invoice LIMIT 10";
// $query	= $dbConn->query($sql);
// $ret	= $dbConn->fetch_array_all($query);
// print_r($ret);


$callback = function($msg){
	global $dbConn;
	$con_stat = mysql_ping();
	$sql = $msg->body;
	$query	= $dbConn->query($sql);
	if (!$query) {
		Log::write($con_stat.'---'.$sql, Log::ERR);
		//Log::write($errorStr,Log::ERR)
	} else {
		echo ' [ok] ', $con_stat,'---', $sql, "\n";
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