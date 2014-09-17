<?php
include "config.php";
include "/data/web/purchase.valsun.cn/lib/functions.php";

global $dbConn;
//$sql = "select a.id,a.sku ,a.totalAmount,a.amount,b.tallyAmout from ph_sku_reach_record as a left join ph_tallySku_record as b on a.sku=b.sku  where a.totalAmount=b.tallyAmout ";

function deal_partner(){
	global $dbConn;
	$sql = "select * from ph_partner ";
	$sql = $dbConn->execute($sql);
	$numberInfo = $dbConn->getResultArray($sql);
	foreach($numberInfo as $item){
		$sql = "select company_name from ph_partner_unique where company_name='{$item['company_name']}'";
		$sql = $dbConn->execute($sql);
		$skuInfo = $dbConn->fetch_one($sql);
		
		if(empty($skuInfo['company_name'])){
			$dataSet = array2sql($item);
			$sql = "insert into  ph_partner_unique set {$dataSet} ";
			if($dbConn->execute($sql)){
				echo $sql."\n";
			}
		}
	}
}

//deal_partner();

$sql = "SELECT id,companyname FROM  `ph_user_partner_relation` ";
$sql = $dbConn->execute($sql);
$partnerInfo = $dbConn->getResultArray($sql);
foreach($partnerInfo as $item){
	$sql = "SELECT id FROM  `ph_partner_unique` where company_name='{$item['companyname']}' ";
	$sql = $dbConn->execute($sql);
	$partnerId = $dbConn->fetch_one($sql);

	if(isset($partnerId['id'])){
		$sql = "UPDATE `ph_user_partner_relation` SET `partnerId`={$partnerId['id']} WHERE id={$item['id']} ";
		if($dbConn->execute($sql)){
			echo $sql."\n";
		}
	}

	
}


/*
$sql = "select a.sku ,purchaseId,c.company_name from pc_goods as a left join pc_goods_partner_relation as b on a.sku=b.sku 
		left join ph_partner as c on b.partnerId = c.id 
  	 ";
$sql = $dbConn->execute($sql);
$partnerInfo = $dbConn->getResultArray($sql);
foreach($partnerInfo as $item){
	$sql = "INSERT INTO `ph_user_partner_relation`(`sku`, `purchaseId`, `companyname`) VALUES ('{$item['sku']}','{$item['purchaseId']}','{$item['company_name']}')";
	if($dbConn->execute($sql)){
		echo "$sql \n";
	}

}
 */
//print_r($partnerInfo);

?>
