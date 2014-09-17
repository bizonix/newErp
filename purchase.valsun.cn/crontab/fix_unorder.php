<?php
include('/data/web/purchase.valsun.cn/lib/rabbitmq/config.php');
require "/data/web/purchase.valsun.cn/framework.php";
use PhpAmqpLib\Connection\AMQPConnection;
Core::getInstance();
error_reporting(-1);

global $dbConn,$dbconn,$rmqObj;
$now = time();
$beforetime = $now - 24*60*60;
$sql = "SELECT * FROM  `ph_sku_reach_record` where  note like '%sku%' ";
$sql = $dbConn->execute($sql);
$skuInfo = $dbConn->getResultArray($sql);
$idArr = array();
foreach($skuInfo as $item){
	$id = $item['id'];
	$note = $item['note'];
	preg_match_all("/^sku.*?(\d*+)$/", $note, $matches, PREG_SET_ORDER);
	$amount = $matches[0][1];
	$sql = "update ph_sku_reach_record set amount={$amount} where id={$id}";
	if ($dbConn->execute($sql)) echo "$sql\n";
}




?>
