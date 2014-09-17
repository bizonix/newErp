<?php
/**
 * 名称：订阅系统鉴权队列接收处理脚本
 * 版本：1.0
 * 日期：2014/08/18
 */

error_reporting(-1);
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
include "/data/web/rss.valsun.cn/framework.php";
Core::getInstance();

################# 消息队列初始化 ###################
include(__DIR__ . '/config_mq.php');
use PhpAmqpLib\Connection\AMQPConnection;
$exchange 		= 'power';
$queue 			= 'rabbitmq_rss_power_queue';
$consumer_tag 	= 'consumer_rss';
$conn = new AMQPConnection(HOST, PORT, USER, PASS, VHOST);
$ch = $conn->channel();

/*
    The following code is the same both in the consumer and the producer.
    In this way we are sure we always have a queue to consume from and an
        exchange where to publish messages.
*/

/*
    name: $queue
    passive: false
    durable: true // the queue will survive server restarts
    exclusive: false // the queue can be accessed in other channels
    auto_delete: false //the queue won't be deleted once the channel is closed.
*/
$ch->queue_declare($queue, false, true, false, false);

/*
    name: $exchange
    type: direct
    passive: false
    durable: true // the exchange will survive server restarts
    auto_delete: false //the exchange won't be deleted once the channel is closed.
*/

$ch->exchange_declare($exchange, 'fanout', false, false, false);
$ch->queue_bind($queue, $exchange);
echo ' [*] Waiting for logs. To exit press CTRL+C', "\n";

//数据处理
function process_message($msg) {
    echo "\n############ 信息日志开始 ###############\n";
    echo date('Y-m-d H:i:s')."=====".$msg->body;
    echo "\n############ 信息日志结束 ###############\n";

	//执行SQL语句
	$db_config	=	C("DB_CONFIG");
	$dbConn		=	new mysql();
	$dbConn->connect($db_config["master1"][0],$db_config["master1"][1],$db_config["master1"][2],'');
	$dbConn->select_db($db_config["master1"][4]);
	$sql 		= $msg->body;
	$query		= $dbConn->query($sql);
	if (!$query) {
		Log::write($sql, Log::ERR);
	} else {
		echo date('Y-m-d H:i:s').'===== [ok] ', '=====', $sql, "\n";
		//确认消费OK
		$msg->delivery_info['channel']->
			basic_ack($msg->delivery_info['delivery_tag']);
	}
	$dbConn->close();

    // Send a message with the string "quit" to cancel the consumer.
    if ($msg->body === 'quit') {
        $msg->delivery_info['channel']->
            basic_cancel($msg->delivery_info['consumer_tag']);
    }
}

/*
    queue: Queue from where to get the messages
    consumer_tag: Consumer identifier
    no_local: Don't receive messages published by this consumer.
    no_ack: Tells the server if the consumer will acknowledge the messages.
    exclusive: Request exclusive consumer access, meaning only this consumer can access the queue
    nowait:
    callback: A PHP Callback
*/

$ch->basic_consume($queue, $consumer_tag, false, false, false, false, 'process_message');

function shutdown($ch, $conn)
{
    $ch->close();
    $conn->close();
}
register_shutdown_function('shutdown', $ch, $conn);

// Loop as long as the channel has callbacks registered
while (count($ch->callbacks)) {
    $read   = array($conn->getSocket()); // add here other sockets that you need to attend
    $write  = null;
    $except = null;
    if(false === ($num_changed_streams = stream_select($read, $write, $except, 60))) {
        /* Error handling */
		echo date('Y-m-d H:i:s')."===ERROR TCP link time out!\n";
		exit;
    } elseif ($num_changed_streams > 0) {
        $ch->wait();
    }
}
?>