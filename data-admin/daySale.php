<?php
include "dbconnect.php";
include "function_purchase.php";
$m = new MongoClient('mongodb://localhost:20000/');
$db = $m->selectDB("bigdata");
/*
$m->bigdata->collection->insert(
        [ 'client' => 'awesome' ], // ← document
        [ 'w' => 0 ]  // ← don't acknowledge writes for this insert
	);
 */

$dbcon = new DBClass();
$MemcacheObj = new Memcache;
/*
$sql = "SELECT * FROM `ebay_ordergrossrate` WHERE `is_effectiveorder` = 1 AND `is_delete` = 0 AND `order_scantime` BETWEEN '1404748800' AND '1404835199' ORDER BY `order_scantime` DESC limit 50,50000";
$sql = $dbcon->execute($sql);
$skuinfo = $dbcon->getResultArray($sql);
foreach($skuinfo as $item){
	$m->bigdata->ebay->insert($item);
}
 */


$sql = "SELECT ebay_account,ebay_platform FROM ebay_account WHERE ebay_platform!='' ORDER BY ebay_platform ASC";
$sql  = $dbcon->execute($sql);
$eaccounts = $dbcon->getResultArray($sql);
$accounts = array();

$cn2enarr = array( //中文转英文
	'ebay平台' => 'ebay',
	'亚马逊' => 'amazon',
	'出口通' => 'chukoutong',
	'国内销售部' => 'guonei',
	'天猫哲果' => 'zegoo',
	'天猫芬哲' => 'fenjo',
	'海外仓' => 'oversea',
	'线下结算客户' => 'offline'
);
foreach ($eaccounts AS $eaccount){
	$enAccount = $cn2enarr[$eaccount['ebay_platform']];
	if(isset($enAccount)){
		$accounts[$enAccount][] = $eaccount['ebay_account'];
	}else{
		$accounts[$eaccount['ebay_platform']][] = $eaccount['ebay_account'];
	}
}

$sql = "SELECT count(*) as total FROM `ebay_goods` ";
$sql = $dbcon->execute($sql);
$number = $dbcon->fetch_one($sql);
$length =  ceil($number['total']/1000);

for($i=0;$i<$length;$i++){
	$begin = $i*1000;
	$sql = "SELECT * FROM `ebay_goods` limit {$begin},1000 ";
	$sql = $dbcon->execute($sql);
	$skuinfo = $dbcon->getResultArray($sql);

	$now = time();
	$beforenow = $now - 24*60*60;
	$dateformat = date("Y-m-d",$beforenow);
	$startime = strtotime($dateformat." 00:00:00");
	$endtime = strtotime($dateformat." 23:59:59");
	$between = array($startime,$endtime);
	foreach($skuinfo as $skuItem){
		$platformsale = getallsale($skuItem['goods_sn'],$accounts,$between);
		$platformsale['sku'] = $skuItem['goods_sn'];
		$platformsale['cguser'] = $skuItem['cguser'];
		//print_r($platformsale);
		$m->daysale->$dateformat->insert($platformsale);
	}
}



/*
$collection = $m->daysale->$dateformat;
$cursor = $m->daysale->$dateformat->find();
$newdata = array('$set' => array("platformnums.ebay"=>99));
$collection->update(array("sku" => "017"), $newdata);
$cursor = $collection->find(array("sku" => "017"))->count();
var_dump($cursor);

foreach ($cursor as $doc) {
    // do something to each document
	print_r($doc);
}
 */

//var_dump($daysale);



?>
