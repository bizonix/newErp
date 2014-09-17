<?php
include "config.php";
include "/data/web/purchase.valsun.cn/lib/functions.php";

global $dbConn;

$sql = "select id from ph_order where status=3 and purchaseuser_id=0";
$sql = $dbConn->execute($sql);
$idArr = $dbConn->getResultArray($sql);
foreach()
exit;
$sql = "select `global_user_name`,`global_user_id` FROM `power_global_user` ";
$sql = $dbConn->execute($sql);
$name = $dbConn->getResultArray($sql);
foreach($name as $item){
	//print_r($item);
	$sql = "UPDATE `ph_order` SET purchaseUser='{$item['global_user_name']}' where purchaseuser_id='{$item['global_user_id']}'";
	$dbConn->execute($sql);
}
exit;


/*
$sql = "SELECT * FROM  `ph_user_partner_relation` WHERE  `partnerId` =0 AND companyname !=  ''";
$sql = $dbConn->execute($sql);
$name = $dbConn->getResultArray($sql);
foreach($name as $item){
	print_r($item);
	$name = trim($item['companyname']);
	$sql = "select id FROM ph_partner where company_name='{$name}'";
	echo $sql;
	$sql = $dbConn->execute($sql);
	$id =$dbConn->fetch_one($sql);
	if(isset($id['id'])){
		$sql = "update ph_user_partner_relation set partnerId={$id['id']} where id={$item['id']}";
		$dbConn->execute($sql);
	}
}

exit;
 */
$sql = "SELECT id FROM  `ph_order`  where partner_id=0 and status=3 ";
$sql = $dbConn->execute($sql);
$idArr = $dbConn->getResultArray($sql);
foreach($idArr as $item){
	$sql = "select sku from ph_order_detail where po_id={$item['id']} ";
	$sql = $dbConn->execute($sql);
	$skuInfo = $dbConn->fetch_one($sql);
	print_r($skuInfo);
	$sql = "SELECT partnerId from ph_user_partner_relation where sku='{$skuInfo['sku']}' ";
	$sql = $dbConn->execute($sql);
	$partnerInfo = $dbConn->fetch_one($sql);
	if(isset($partnerInfo['partnerId'])){
		$sql="update ph_order set partner_id={$partnerInfo['partnerId']} where id={$item['id']}";
		if($dbConn->execute($sql)){
			echo "$sql\n";
		}
	}
}


/*
$sql = "SELECT id,partner_name FROM ph_order where status=3";
$sql = $dbConn->execute($sql);
$partnerInfo = $dbConn->getResultArray($sql);

foreach($partnerInfo as $item){
	$sql = "select id from  ph_partner where  company_name='{$item['partner_name']}'";
	$sql = $dbConn->execute($sql);
	$nameInfo = $dbConn->fetch_one($sql);
	$sql = "update ph_order set partner_id='{$nameInfo['id']}' where id={$item['id']}";
	if($dbConn->execute($sql)){
		echo "$sql\n";
	}
	
}
exit;
$sql = "SELECT id,partner_id FROM ph_order where status=3";
$sql = $dbConn->execute($sql);
$partnerInfo = $dbConn->getResultArray($sql);

foreach($partnerInfo as $item){
	$sql = "select company_name from  ph_partner_bk where id={$item['partner_id']}";
	$sql = $dbConn->execute($sql);
	$nameInfo = $dbConn->fetch_one($sql);
	$sql = "update ph_order set partner_name='{$nameInfo['company_name']}' where id={$item['id']}";
	if($dbConn->execute($sql)){
		echo "$sql\n";
	}
	
}
 */

?>
