<?php
include('/data/web/purchase.valsun.cn/lib/rabbitmq/config.php');
require "/data/web/purchase.valsun.cn/framework.php";
use PhpAmqpLib\Connection\AMQPConnection;
Core::getInstance();
error_reporting(-1);

global $dbConn,$dbconn;
$exchange = 'ow_exchange';
$queue = 'ow_queue';
$consumer_tag = 'consumer';

$conn = new AMQPConnection(HOST, PORT, USER, PASS, VHOST);
$ch = $conn->channel();

$ch->queue_declare($queue, false, true, false, false);
$ch->exchange_declare($exchange, 'topic', false, true, false);
$ch->queue_bind($queue, $exchange);

function process_message($msg){
	$dataStr = $msg->body;
	if(!empty($dataStr)){
		$dataArr = json_decode($dataStr,true);
		//print_r($dataArr);
		if($dataArr['type'] == "updateOwData"){
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

$sql = "select settingJson from ow_setting where id=1";
$sql = $dbConn->execute($sql);
$setting = $dbConn->fetch_one($sql);
$configArgument = json_decode($setting['settingJson'],true);


function updateSkuInfo($data){
	global $dbConn,$configArgument;
	$setContent = array2sql($data);
	$sql = "select count(*) as totalnum,purchasedays,safeStockDays,cycle_days from ow_stock where sku='{$data['sku']}'";
	$sql = $dbConn->execute($sql);
	$num = $dbConn->fetch_one($sql);
	$comObj = new CommonAct();
	$booknums = $comObj->getOrderSkuNum($data['sku']); //已订购库存
	if($num['totalnum'] > 0){
		$alertdays = $configArgument['onseadays'] + max($configArgument['stockreaddays'],$configArgument['shipredaydays']) + $configArgument['reshelfdays'] + $num['safeStockDays'] + $num['cycle_days']; //预警天数计算
		if($data['everyday_sale'] != 0){
			$days = ($data['virtual_stock'] + $data['onWayCount'] + $data['b_stock_cout'] + $booknums) / $data['everyday_sale'];
			$out_alert_days = $data['virtual_stock'] / $data['everyday_sale']; //可用天数
			if($days < $alertdays){
				$is_alert = 1;
			}else{
				$is_alert = 0;
			}
			if($out_alert_days <= 5){
				$out_alert = 1;  //超卖控制
			}else{
				$out_alert = 0;
			}
		}else{
			$is_alert = 0;
			$out_alert = 0;
		}
		$sql = "update ow_stock set {$setContent}, is_alert='{$is_alert}',out_alert='{$out_alert}',booknums='{$booknums}' where sku='{$data['sku']}'";
	}else{
		$is_alert = 0;
		$out_alert = 0;
		$sql = "INSERT INTO `ow_stock`(`sku`,`everyday_sale`, `count`, `onWayCount`, `salensend`, `booknums`, `virtual_stock`, `b_stock_cout`,`is_alert`,out_alert) VALUES ('{$data['sku']}','{$data['everyday_sale']}','{$data['count']}','{$data['onWayCount']}','{$data['salensend']}','{$booknums}','{$data['virtual_stock']}','{$data['b_stock_cout']}','{$is_alert}','{$out_alert}')";
	}

	echo $sql."\n";
	if($dbConn->execute($sql)){
		return 1;
	}else{
		echo $sql."\n";
		return 0;
	}
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
