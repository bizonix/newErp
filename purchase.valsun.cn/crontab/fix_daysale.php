<?php
include('/data/web/purchase.valsun.cn/lib/rabbitmq/config.php');
require "/data/web/purchase.valsun.cn/framework.php";
use PhpAmqpLib\Connection\AMQPConnection;
Core::getInstance();
error_reporting(-1);

global $dbConn,$dbconn,$rmqObj;
$now = time();
//$beforetime = $now - 60*60;
//$sql = "SELECT *  FROM `ph_sku_statistics` WHERE  lastupdate<{$beforetime} limit 500";
$sql = "SELECT *  FROM `pc_goods` WHERE  goodsStatus!=2";
$sql = $dbConn->execute($sql);
$skuInfo = $dbConn->getResultArray($sql);
foreach($skuInfo as $item){
	$publish_data = array();
	$publish_data['type'] = "updateSku";
	$publish_data['sku'] = $item['sku'];
	print_r($publish_data);
	$rmqObj->single_queue_publish("purchase_info_exchange",json_encode($publish_data));
}
?>
