<?php
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
require "/data/web/purchase.valsun.cn/framework.php";
Core::getInstance();

require_once __DIR__ . '/../lib/rabbitmq/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;

$rmq_config	= array(
				"ip"=> "112.124.41.121",
				"user"=>"valsun_sendOrder",
				"passwd"=>"sendOrder%123",
				"port" => "5672",
				"vhost"=>"valsun_datas"
			 );
//$rabbitMQClass= new RabbitMQClass($rmq_config['user'],$rmq_config['passwd'],$rmq_config['vhost'],$rmq_config['ip']);//队列对象
$connection = new AMQPConnection($rmq_config['ip'], $rmq_config['port'], $rmq_config['user'],$rmq_config['passwd'],$rmq_config['vhost']);
$exchange_name = 'data_analyze';
$queue_name = 'data_queue';

$channel = $connection->channel();
//第三个参数 true 会检测交换器是否存在 ，第4个参数 true 表示 服务器重启时，交换器依然不会消失，第5个参数false 表示 如果交换器删掉，消息通道依然生效
$channel->exchange_declare($exchange_name, 'fanout', false, false, false);
$channel->queue_declare($queue_name, false, false, false, false);
$channel->queue_bind($queue_name, $exchange_name);

echo ' [*] Waiting for logs. To exit press CTRL+C', "\n";

$callback = function($msg){
	global $dbconn;
	$sql = $msg->body;
	$sql = json_decode($sql);

	$query = $dbconn->execute($sql);
	if ($query) {
		echo ' [ok] ', '---', $sql, "\n";
	}else{
		Log::write($sql, Log::ERR);
	}
	//$dbconn->close();
};

$channel->basic_consume($queue_name, '', false, true, false, false, $callback);

while(count($channel->callbacks)){
    $channel->wait();
}



$channel->close();
$connection->close();
?>
