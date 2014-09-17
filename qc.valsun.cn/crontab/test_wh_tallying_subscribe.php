<?php
define('SCRIPTS_PATH_CRONTAB', '/data/web/erpNew/wh.valsun.cn/crontab/');    
require_once SCRIPTS_PATH_CRONTAB."scripts.comm.php";

require_once  '/data/web/erpNew/wh.valsun.cn/lib/rabbitmq/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;

//$connection = new AMQPConnection('112.124.41.121', 5672, 'valsun_warehouse','warehouse%123','valsun_warehouse');
$connection = new AMQPConnection('192.168.200.222', 5672, 'xiaojinhua','jinhua','/');
$queue_name = 'wh_tallying_list_test';
$exchange_name='send_tallying_list';

$channel = $connection->channel();
//第三个参数 true 会检测交换器是否存在 ，第4个参数 true 表示 服务器重启时，交换器依然不会消失，第5个参数false 表示 如果交换器删掉，消息通道依然生效
$channel->exchange_declare($exchange_name, 'fanout', false, false, false);
$channel->queue_declare($queue_name, false, false, false, false);
$channel->queue_bind($queue_name, $exchange_name);

echo ' [*] Waiting for logs. To exit press CTRL+C', "\n";

$callback = function($msg){
	$res	= json_decode($msg->body);
	var_dump($res);exit;
	$dbConn->close();
};

$channel->basic_consume($queue_name, '', false, true, false, false, $callback);

while(count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();
?>