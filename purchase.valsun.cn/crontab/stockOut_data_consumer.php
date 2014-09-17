<?php
include('/data/web/purchase.valsun.cn/lib/rabbitmq/config.php');
require "/data/web/purchase.valsun.cn/framework.php";
use PhpAmqpLib\Connection\AMQPConnection;
Core::getInstance();
error_reporting(-1);

global $dbConn,$dbconn;
$exchange = 'alert_exchange';
$queue = 'alert_queue';
$consumer_tag = 'consumer';

$conn = new AMQPConnection(HOST, PORT, USER, PASS, VHOST);
$ch = $conn->channel();

$ch->queue_declare($queue, false, true, false, false);
$ch->exchange_declare($exchange, 'fanout', false, true, false);
$ch->queue_bind($queue, $exchange);

function process_message($msg){
	$dataStr = $msg->body;
	if(!empty($dataStr)){
		$dataArr = json_decode($dataStr,true);
		print_r($dataArr);
		if($dataArr['type'] == "updateData"){
			$data = $dataArr['data'];
			$rtn = updateSkuInfo($data);
			if($rtn == 1){
				$msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
			}
		}
	}

    // Send a message with the string "quit" to cancel the consumer.
    if ($msg->body === 'quit') {
        $msg->delivery_info['channel']->
            basic_cancel($msg->delivery_info['consumer_tag']);
    }
}

function updateSkuInfo($data){
	global $dbConn;
	$setContent = array2sql($data);
	$sql = "select count(*) as totalnum from ph_sku_statistics where sku='{$data['sku']}'";
	$sql = $dbConn->execute($sql);
	$num = $dbConn->fetch_one($sql);
	if($num['totalnum'] > 0){
		$sql = "update ph_sku_statistics set {$setContent} where sku='{$data['sku']}'";
	}else{
		$sql = "insert into  ph_sku_statistics set {$setContent} ";
	}
	if($dbConn->execute($sql)){
		return 1;
	}else{
		return 0;
	}
	//echo $sql;
}


$ch->basic_consume($queue, $consumer_tag, false, false, false, false, 'process_message');

function shutdown($ch, $conn)
{
    $ch->close();
    $conn->close();
}
register_shutdown_function('shutdown', $ch, $conn);

// Loop as long as the channel has callbacks registered
while (count($ch->callbacks)) {
    $ch->wait();
}
