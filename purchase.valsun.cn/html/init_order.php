<?php
include "config.php";
include "/data/web/purchase.valsun.cn/lib/functions.php";

global $dbConn,$dbconn;
//$sql = "select a.*,b.companyname from ph_sku_statistics as a left join ph_user_partner_relation as b on a.sku=b.sku ";
$sql = "select a.*,b.global_user_name from ph_user_partner_relation as a left join  power_global_user as b on a.purchaseId = b.global_user_id";
$sql = $dbConn->execute($sql);
$info = $dbConn->getResultArray($sql);
foreach($info as $item){
	$sql = "update om_sku_daily_status set purchaseUser='{$item['global_user_name']}', supplier='{$item['companyname']}' where sku='{$item['sku']}'";
	if($dbConn->execute($sql)){
		echo "$sql\n";
	}
}
/*
foreach($info as $item){
	$sku = $item['sku'];
	$spu = $item['spu'];
	$purchaseuser = $item['purchaseuser'];
	$everyday_sale = $item['everyday_sale'];
	$first_sale = $item['first_sale'];
	$sql = "INSERT INTO `om_sku_daily_status`(`sku`, `spu`, `purchaseUser`,`last_everyday_sale`,firstSaleTime) VALUES ('{$sku}','{$spu}','{$purchaseuser}','{$everyday_sale}','{$first_sale}')";
	if($dbConn->execute($sql)){
		echo "$sql\n";
	}

}
 */
?>
