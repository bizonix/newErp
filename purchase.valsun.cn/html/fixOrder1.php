<?php
include "config.php";
include "/data/web/purchase.valsun.cn/lib/functions.php";

global $dbConn,$dbconn;

function checkOrderFinish($recordnumber){
	global $dbconn;
	$sql = "select count, stockqty from ph_order_detail where recordnumber='{$recordnumber}'";
	$sql = $dbconn->execute($sql);
	$numInfo = $dbconn->getResultArray($sql);
	$flag = array();
	$now = time();
	foreach($numInfo as $item){
		if($item["stockqty"] >= $item["count"]){//到货数量和订购数量比较
			$flag[] = 1;
		}else{
			$flag[] = 0;
		}
	}
	if(!in_array(0,$flag)){
		$sql = "update ph_order set status=4, finishtime={$now} where recordnumber='{$recordnumber}'";
		//echo $sql;
		if($dbconn->execute($sql)){
			echo "$sql\n";
		}
	}
}



$sql = "SELECT * FROM  `in_warehouse_history` WHERE  `in_time` >1392422400 AND in_time < 1392595199  ";
$sql = $dbConn->execute($sql);
$inhistory = $dbConn->getResultArray($sql);
/*
foreach($inhistory as $item){
	checkOrderFinish($item['in_ordersn']);
}

exit;
 */
foreach($inhistory as $item){
	if(empty($item['in_ordersn']) ){
		continue;
	}
	$sql = "SELECT * from ph_order_detail where recordnumber='{$item['in_ordersn']}' and sku='{$item['in_sku']}'";
	$sql = $dbConn->execute($sql);
	$orderInfo = $dbConn->fetch_one($sql);
	/*
	if($item['in_amount'] == $orderInfo['count']){
	}else{
		var_dump($item['in_amount'],$orderInfo['count']);
	}
	 */

	$inNumber = $orderInfo['stockqty'] + $item['in_amount'];
	if($inNumber >= $orderInfo['count']){
		$setCondition = $orderInfo['count'];
	}else{
		$setCondition = " stockqty + {$item['in_amount']}";
	}
	if(isset($orderInfo['id'])){

		$sql = "update ph_order_detail set stockqty= {$setCondition} where id={$orderInfo['id']}";

		if($dbConn->execute($sql)){
			echo "sku:{$orderInfo['sku']},入库数量：{$item['in_amount']},订单数量：{$orderInfo['count']}\n";
		}
	}


}

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

 */
exit;
/*
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
