<?php
include "config.php";
include "/data/web/purchase.valsun.cn/lib/functions.php";
$sql = "select sku,purchaseId from pc_goods  order by rand() limit 500 ";
$sql = $dbconn->execute($sql);
$skuInfo = $dbconn->getResultArray($sql);
foreach($skuInfo as $item){
	$sku = $item["sku"];
	$amount = rand(1,200);
	$purchaseId = $item["purchaseId"];
	$partnerName = getPartnerBySku($sku);
	$tallymanId = rand(200,300);
	$now = time();
	$note = "异常到货数量{$amount}个";
	$sql = "INSERT INTO `ph_sku_reach_record`(`sku`, `purchaseId`, `amount`,tallymanId,note,addtime,partnerName) VALUES ('{$sku}',{$purchaseId},{$amount},{$tallymanId},'{$note}',{$now},'{$partnerName}')";
	if($dbconn->execute($sql)){
		echo "添加数据成功。。。。\n";
	}
}
?>
