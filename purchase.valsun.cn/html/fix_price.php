<?php
include "config.php";
include "/data/web/purchase.valsun.cn/lib/functions.php";
include "/data/web/purchase.valsun.cn/lib/rabbitmq/config.php";
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;
function publish_msg($data){//广播信息到外面
	$exchange = 'check_cost_exchange';
	$conn = new AMQPConnection(HOST, PORT, USER, PASS, VHOST);
	$ch = $conn->channel();
	$ch->exchange_declare($exchange, 'fanout', false, true, false);
	/*
		name: $exchange
		type: fanout
		passive: false // don't check is an exchange with the same name exists
		durable: false // the exchange won't survive server restarts
		auto_delete: true //the exchange will be deleted once the channel is closed.
	*/
	//$ch->exchange_declare($exchange, 'fanout', false, true, false);
	$msg = new AMQPMessage(json_encode($data),array('content_type' => 'text/plain'));
	$ch->basic_publish($msg, $exchange);
	$ch->close();
	$conn->close();
}

global $dbConn,$dbconn;
//$sql = "select sku from pc_goods where id>40000 and  is_delete=0 ";
//$sql = "select sku from pc_goods where sku in ('3284_GR') and  is_delete=0 ";
$sql = "select sku from pc_goods where  is_delete=0 ";


$sql = $dbConn->execute($sql);
$skuArr = $dbConn->getResultArray($sql);
foreach($skuArr as $item){
	$sql = "SELECT sku,price FROM  `ph_order_detail` as a left join  ph_order as b on b.id = a.po_id where a.sku='{$item['sku']}'  order by b.addtime desc limit 1";
	//$sql = "SELECT sku,price FROM  `ph_order_detail` where sku='{$item['sku']}' order by po_id desc limit 1";

	$sql = $dbConn->execute($sql);
	$skuInfo = $dbConn->fetch_one($sql);
	$publish_data = array();
	$publish_data["type"] = "updateCheckCost";
	$publish_data["data"] = $skuInfo;
	publish_msg($publish_data);
	print_r($skuInfo);
}
?>
