<?php
error_reporting(-1);
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
include('/data/web/purchase.valsun.cn/lib/rabbitmq/config.php');
require "/data/web/purchase.valsun.cn/framework.php";
use PhpAmqpLib\Connection\AMQPConnection;
Core::getInstance();

$exchange = "SEND_BIG_ORDER_2_PH";//交换器名称
$queue = 'CONSUMER_BIG_ORDER_2_PH';
$consumer_tag = 'consumer';

$conn = new AMQPConnection(HOST, PORT, USER, PASS, VHOST);
$ch = $conn->channel();

$ch->queue_declare($queue, false, true, false, false);
$ch->exchange_declare($exchange, 'direct', false, false, false);
$ch->queue_bind($queue, $exchange);

function process_message($msg){
	$dataStr = $msg->body;
	print_r($dataStr);//print

// 	$filename = '/data/web/purchase.valsun.cn/log/superorder.txt';
// 	file_put_contents($filename, date("Y-m-d H:i:s")."\t".$dataStr."\r\n", FILE_APPEND);

	if (empty($dataStr)) {
		return;
	}

	$dataArr = json_decode($dataStr,true);
	if (!isset($dataArr['omOrderId'], $dataArr['omOrderdetailId'], $dataArr['sku'], $dataArr['amount'])) {
		return;
	}

	reconnect();//mysql 重新连接

	$where = "omOrderdetailId='{$dataArr['omOrderdetailId']}' and omOrderId='{$dataArr['omOrderId']}' and sku='{$dataArr['sku']}' and is_delete=0 ";

	try {
		$row = SuperorderAuditModel::getOne('id, amount',$where);
	} catch (Exception $e) {
		print_r(array($e->getMessage(),$e->getTraceAsString()));
		return;
	}

	$data_array = array();
	$data_array['omOrderId'] = $dataArr['omOrderId'];
	$data_array['omOrderdetailId'] = $dataArr['omOrderdetailId'];
	$data_array['sku'] = $dataArr['sku'];
	$data_array['amount'] = $dataArr['amount'];

	if (!empty($row)) {
		if ( $row['amount'] != $dataArr['amount'] && $dataArr['amount'] != 0) {
			$where = " id='{$row['id']}'";
			$isOk = SuperorderAuditModel::update(array('is_delete'=>1), $where);
			if ($isOk) {
				$data_array['addTime'] = time();
				$isOk = SuperorderAuditModel::add($data_array);
			}
		}
// 		echo '111'."\n";
// 		var_dump($isOk);
	} else {
		$data_array['addTime'] = time();
		$isOk = SuperorderAuditModel::add($data_array);
// 		echo '222'."\n";
// 		var_dump($isOk);
	}

	if ($isOk) {
		$msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
	}
}

function reconnect() {
	global $dbConn;
	if (mysql_ping()) {
		return ;
	}
	$db_config = C("DB_CONFIG");
	$dbConn	= new mysql();
	$dbConn->connect($db_config["master1"][0],$db_config["master1"][1],$db_config["master1"][2]);
	$dbConn->select_db($db_config["master1"][4]);
}

echo ' [*] Waiting for data. To exit press CTRL+C', "\n";
$ch->basic_consume($queue, $consumer_tag, false, false, false, false, 'process_message');

// Loop as long as the channel has callbacks registered
while (count($ch->callbacks)) {
    $ch->wait();
}

$ch->close();
$conn->close();


