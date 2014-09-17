<?php
include_once __DIR__.'/../framework.php';                               // 加载框架
Core::getInstance();                                                    // 初始化框架对象
include_once __DIR__.'/../lib/productstatus.class.php';                               // 加载框架

error_reporting(-1);
$product = new ProductStatus();
global $dbConn;

$updateSkuArr = array();
if(isset($argv[2])){// 更新单个采购的料号
	$sql = "select a.sku from pc_goods as a left join power_global_user as b on a.purchaseId=b.global_user_id where b.global_user_name='{$argv[2]}' ";
	echo $sql;
	$sql = $dbConn->execute($sql);
	$skuArr = $dbConn->getResultArray($sql);
	foreach($skuArr as $item){
		$updateSkuArr[] = $item['sku'];
	}

}
//$updateSkuArr = array("1724");

foreach($updateSkuArr as $sku){
	//$product->resetSkuStock($sku,1);
	//$product->calcAverageDailyCount($item['sku']);
	$product->resetSkuAverage($sku);
}

if(isset($argv[2])){
	$sku = $argv[2];
	$product->resetSkuStock($sku,1);
	$product->calcAverageDailyCount($item['sku']);
}else{
	$sql = "SELECT sku,spu FROM `pc_goods` where 1";
	$sql = $dbConn->execute($sql);
	$skuInfo = $dbConn->getResultArray($sql);
	if($argv[1] == "resetSkuAverage"){
		foreach($skuInfo as $item){
			$sku = $item['sku'];
			$product->resetSkuAverage($item['sku']);
		}
	}else if($argv[1] == "resetSkuStock"){
		foreach($skuInfo as $item){
			$sku = $item['sku'];
			$product->resetSkuStock($item['sku'],1);
		}
}

}

?>
