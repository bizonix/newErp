<?php
include('/data/web/purchase.valsun.cn/lib/rabbitmq/config.php');
require "/data/web/purchase.valsun.cn/framework.php";

use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;
Core::getInstance();
global $dbConn,$dbconn;
$consumer_tag 	= 'consumer';
$host 			= '198.23.70.51';
$port 			= '5672';
$user 			= 'oversea';
$pass 			= '123456';
$vhost 			= 'valsun_oversea';
$conn 			= new AMQPConnection($host, $port, $user, $pass, $vhost);
$ch = $conn->channel();
$queue = "oversea_queue";
$ch->queue_declare("oversea_queue", false, true, false, false);
$ch->queue_bind("oversea_queue", "inStock");
error_reporting(-1);


function publish($data){
	$conn = new AMQPConnection(HOST, PORT, USER, PASS, VHOST);
	$ch = $conn->channel();
	$ch->queue_declare("oversold_queue", false, true, false, false);
	$ch->queue_bind("oversold_queue", "oversold");
	$msg = new AMQPMessage(json_encode($data), array('content_type' => 'text/plain', 'delivery_mode' => 2));
	$rtn = $ch->basic_publish($msg, "oversold");
}

function process_message($msg){
	$dataStr = $msg->body;
	if(!empty($dataStr)){
		$dataArr = json_decode($dataStr,true);
		print_r($dataArr);
		if($dataArr['type'] == "oversea_instock"){
			$sku = $dataArr['sku'];
			$amount = $dataArr['inamount'] + $dataArr['beforeamount'];
			$rtn = trigger_list($sku,$amount);
			if($rtn == 1){
				$msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
			}
		}else{
			$msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
		}
	}

    // Send a message with the string "quit" to cancel the consumer.
    if ($msg->body === 'quit') {
        $msg->delivery_info['channel']->
            basic_cancel($msg->delivery_info['consumer_tag']);
    }
}


	//到货触发超卖系统
function trigger_list($sku,$amount){
	global $dbConn;
	$curl = new CURL();
	$sql = "select * from ow_stock where sku='{$sku}'";
	$sql = $dbConn->execute($sql);
	$item = $dbConn->fetch_one($sql);
	//var_dump($item['everyday_sale'],$item['out_alert']);
	if($item['out_mark'] == 0){
		//exit;
		return 1;
	}
	if(($amount + $item['count']) <= 0){
		return false;
	}
	$availableStock = $amount + $item['count'] - $item['salensend'];
	$availableInventoryDays = ceil($availableStock/$item['everyday_sale']);
	//$outOfStockDays = $availableInventoryDays - $item['reach_days'];
	$outOfStockDays = 0;
	if($availableInventoryDays > 5){
		$days = floor((time() - $item['addReachtime']) / (24*60*60));
		$arrivalGoodsDays = $item['reach_days'] - $days; 
		$sendData = array(
			"sku" => $item['sku'],
			"availableStock" => $availableStock,
			"location" => "US",
			"availableInventoryDays" => $availableInventoryDays,
			"outOfStockDays" => $outOfStockDays,
			"arrivalGoodsDays" => $arrivalGoodsDays
		);
		print_r($sendData);
	}
	return 1;
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
