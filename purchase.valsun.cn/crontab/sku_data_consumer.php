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

$exchange1 = 'outStock_exchange';
$queue1 = 'outStock_queue';
$consumer_tag1 = 'consumer1';

$conn = new AMQPConnection(HOST, PORT, USER, PASS, VHOST);
$ch = $conn->channel();

$ch->queue_declare($queue, false, true, false, false);
$ch->exchange_declare($exchange, 'fanout', false, true, false);
$ch->queue_bind($queue, $exchange);

$ch->queue_declare($queue1, false, true, false, false);
$ch->queue_bind($queue1, $exchange1);

function process_message($msg){
	$dataStr = $msg->body;
	if(!empty($dataStr)){
		$dataArr = json_decode($dataStr,true);
		if($dataArr['type'] == "updateData"){
			$data = $dataArr['data'];
			$rtn = updateSkuInfo($data);
			if($rtn == 1){
				$msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
			}
		}else if($dataArr['type'] == "outStockData"){
			$data = $dataArr['data'];
			//print_r($data);
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
	$comm = new CommonAct();
	$bookNum = $comm->getOrderSkuNum($data['sku']);
	$item = $data;
	if(isset($item['everyday_sale'])){
		$alertNum = $item['stock_qty'] + $item['ow_stock'] + $bookNum - $item['salensend'] - $item['interceptnums'] - $item['autointerceptnums'] - $item['auditingnums'];
		$outalertNum = $item['stock_qty'] + $item['ow_stock'] - $item['salensend'] - $item['autointerceptnums'] ;
		if($item['everyday_sale'] != 0){
			$canUseDay = ($alertNum / $item['everyday_sale']);
			if($canUseDay < $item['alertDays']){
				$isAlert = 1;
			}else{
				$isAlert = 0;
			}

			$outuseDay = ($outalertNum / $item['everyday_sale']);
			if(isset($item['stockoutDays']) && $item['stockoutDays']!=0 ){
				$stockoutDays = $item['stockoutDays'];
			}else{
				$stockoutDays = 10;
			}
			if($outuseDay < $stockoutDays){
				$outAlert = 1;
			}else{
				$outAlert = 0;
			}
		}else{
				$outAlert = 0;
				$isAlert = 0;
		}
		$data['is_alert'] = $isAlert;
		$data['out_alert'] = $outAlert;
		$data['newBookNum'] = $bookNum;
	}else{
		$sql = "select * from ph_sku_statistics where sku='{$data['sku']}'";
		$sql = $dbConn->execute($sql);
		$skuitem = $dbConn->fetch_one($sql);

		$alertNum = $item['stock_qty'] + $item['ow_stock'] + $bookNum - $skuitem['salensend'] - $skuitem['interceptnums'] - $skuitem['autointerceptnums'] - $skuitem['auditingnums'];

		$outalertNum = $item['stock_qty'] + $item['ow_stock'] - $item['salensend'] - $item['autointerceptnums'] ;
		if($skuitem['everyday_sale'] != 0){
			$canUseDay = ($alertNum / $skuitem['everyday_sale']);
			if($canUseDay < $skuitem['alertDays']){
				$isAlert = 1;
			}else{
				$isAlert = 0;
			}

			$outuseDay = ($outalertNum / $skuitem['everyday_sale']);
			if(isset($skuitem['stockoutDays']) && $skuitem['stockoutDays']!=0 ){
				$stockoutDays = $item['stockoutDays'];
			}else{
				$stockoutDays = 10;
			}
			echo "可用天数".$outuseDay."超卖预警控制天数".$stockoutDays."\n";
			if($outuseDay < $stockoutDays){
				$outAlert = 1;
			}else{
				$outAlert = 0;
			}
		}else{
				$isAlert = 0;
				$outAlert = 0;
		}
		$data['is_alert'] = $isAlert;
		$data['out_alert'] = $outAlert;
		$data['newBookNum'] = $bookNum;
	}
	$setContent = array2sql($data);

	$sql = "select count(*) as totalnum from ph_sku_statistics where sku='{$data['sku']}'";
	$sql = $dbConn->execute($sql);
	$num = $dbConn->fetch_one($sql);
	if($num['totalnum'] > 0){
		$sql = "update ph_sku_statistics set {$setContent} where sku='{$data['sku']}'";
	}else{
		$sql = "insert into  ph_sku_statistics set {$setContent} ";
	}

	echo $sql."\n";

	if($dbConn->execute($sql)){
		//$comm->calcAlert($data['sku'],"auto"); //自动更新判断预警
		return 1;
	}else{
		return 0;
	}
	//echo $sql;
}


$ch->basic_consume($queue, $consumer_tag, false, false, false, false, 'process_message');
$ch->basic_consume($queue1, $consumer_tag1, false, false, false, false, 'process_message');

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
    if (false === ($num_changed_streams = stream_select($read, $write, $except, 60))) {
		exit;
        /* Error handling */
    } elseif ($num_changed_streams > 0) {
        $ch->wait();
    }
}
