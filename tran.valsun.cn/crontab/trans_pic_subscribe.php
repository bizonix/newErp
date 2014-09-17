<?php
error_reporting(0);
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
include "/data/web/tran.valsun.cn/framework.php";
Core::getInstance();
require_once WEB_PATH . '/lib/rabbitmq/autoload.php';

########################## 初始化配置 ##################
define('AMQP_DEBUG', false);
define('MQ_HOST', '115.29.188.246');
define('MQ_PORT', 5672);
define('MQ_USER', 'valsun_tran');
define('MQ_PASS', 'tranabc');
define('MQ_VHOST', 'valsun_tran');
########################################################
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;
$connection = new AMQPConnection(MQ_HOST, MQ_PORT, MQ_USER, MQ_PASS, MQ_VHOST);
$number  	= isset($argv[1]) ? $argv[1] : ''; //默认监听队列
$queue_name 	= 'tran_pic_queue'.$number;
$channel_name 	= 'tran_pic_exchange'.$number;
echo $queue_name,"=====",$channel_name,"\n";
$channel = $connection->channel();
$channel->exchange_declare($channel_name, 'direct', false, true, false); //声明交换机
$channel->queue_declare($queue_name, false, false, false, false); //声明队列名
$channel->queue_bind($queue_name, $channel_name);
echo ' [*] Waiting for logs. To exit press CTRL+C', "\n";
$callback = function($msg){	
	$res = $msg->body;
	echo ' [ok] ', '---', $res, "\n";
};
$channel->basic_consume($queue_name, '', false, true, false, false, $callback);
while(count($channel->callbacks)) {
    $channel->wait();
}
$channel->close();
$connection->close();
?>