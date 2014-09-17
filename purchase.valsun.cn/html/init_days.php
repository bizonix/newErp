<?php
include "config.php";
include "/data/web/purchase.valsun.cn/lib/functions.php";

global $dbConn,$dbconn;
//$sql = "select a.*,b.companyname from ph_sku_statistics as a left join ph_user_partner_relation as b on a.sku=b.sku ";
$sql = "select * from ph_sku_statistics ";
$sql = $dbConn->execute($sql);
$info = $dbConn->getResultArray($sql);
foreach($info as $item){
	$sql = "INSERT INTO `ph_goods_calc`(`sku`, `purchasedays`, `goodsdays`) VALUES ('{$item['sku']}','{$item['purchaseDays']}','{$item['alertDays']}')";
	//echo $sql."\n";
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
