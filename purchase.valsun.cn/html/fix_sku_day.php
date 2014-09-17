<?php
include "config.php";
include "/data/web/purchase.valsun.cn/lib/functions.php";

global $dbConn;

$sql = "select * from ph_sku_statistics  where reach_days<0";
$sql = $dbConn->execute($sql);
$skuInfo = $dbConn->getResultArray($sql);
foreach($skuInfo as $item){
	$reach_days = (-1)*$item['reach_days'];
	$sql = "update ph_sku_statistics  set reach_days={$reach_days} where sku='{$item['sku']}'";
	if($dbConn->execute($sql)){
		echo $sql."\n";
	}
}




?>
