<?php
include "config.php";
include "/data/web/purchase.valsun.cn/lib/functions.php";
$sql = "SELECT * FROM  `ebay_iostore`  order by id desc";
$sql = $dbconn->execute($sql);
$orderInfo = $dbconn->getResultArray($sql);
foreach($orderInfo as $item){
	$partnerId = getPartnerId($item['partner']);
	$purchaseuse_id = getUserId($item['io_purchaseuser']);
	$aduituser_id = getUserId($item['audituser']);
	switch($item['io_status']){
		case "0":
			$status = 1;
			break;
		case "1":
			$status = 2;
			break;
		case "2":
			$status = 4;
			break;
		case "3":
			$status = 3;
			break;
	}
	if(empty($userId)){
		$userId = 2;
	}

	$sql = "select count(*) as num from ph_order where recordnumber='{$item['io_ordersn']}' ";
	$sql = $dbconn->execute($sql);
	$num = $dbconn->fetch_one($sql);
	if($num['num'] > 0){
		$sql = "update ph_order set is_delete=0 where recordnumber='{$item['io_ordersn']}'";
		$dbconn->execute($sql);
		echo "\n";
		echo $item['io_ordersn']."\n";
	}else{

   $sql = "INSERT INTO `ph_order`( `recordnumber`, `addtime`, `aduittime`, `status`, `order_type`, `purchaseuser_id`, `aduituser_id`, `partner_id`, `paymethod`, `paystatus`) VALUES ('{$item['io_ordersn']}','{$item['io_addtime']}','{$item['io_audittime']}','{$status}','{$item['io_type']}','{$purchaseuse_id}','{$aduituser_id}','{$partnerId}','{$item['io_paymentmethod']}','{$item['paystatus']}')";
   echo "#############\n";


	if($dbconn->execute($sql)){
		$sql = "select * from ebay_iostoredetail where io_ordersn='{$item['io_ordersn']}'";
		$sql = $dbconn->execute($sql);
		$orderInfodetail = $dbconn->getResultArray($sql);
		foreach($orderInfodetail as $item){
			$po_id = getOrderId($item['io_ordersn']);
			if(isset($po_id)){
				$sql = "INSERT INTO `ph_order_detail`(`po_id`, `sku`, `price`, `count`, `stockqty`, `ungoodqty`, `goods_recommend_count`) VALUES ({$po_id},'{$item['goods_sn']}','{$item['goods_cost']}','{$item['goods_count']}','{$item['stockqty']}','0','{$item['goods_recommend_count']}')";
				if($dbconn->execute($sql)){
					echo "{$sql}\n";
					echo "添加数据成功。。。。\n";
				}
			}
		}
	}
	}

}

/*
$sql = "SELECT count(*) as totalNum FROM `ebay_iostoredetail` WHERE 1";

$sql = $dbconn->execute($sql);
$number = $dbconn->fetch_one($sql);

$length = ceil($number['totalNum']/10000);
for($i=0;$i < $length; $i++){
	$sql = "select * from ebay_iostoredetail limit {$i},10000";
	$sql = $dbconn->execute($sql);
	$orderInfodetail = $dbconn->getResultArray($sql);
	foreach($orderInfodetail as $item){
		$po_id = getOrderId($item['io_ordersn']);
		if(isset($po_id)){
			$sql = "INSERT INTO `ph_order_detail`(`po_id`, `sku`, `price`, `count`, `stockqty`, `ungoodqty`, `goods_recommend_count`) VALUES ({$po_id},'{$item['goods_sn']}','{$item['goods_cost']}','{$item['goods_count']}','{$item['qty_01']}','0','{$item['goods_recommend_count']}')";
			if($dbconn->execute($sql)){
				echo "{$sql}\n";
				echo "添加数据成功。。。。\n";
			}
		}
	}
}
 */



function getOrderId($ordersn){
	global $dbconn;
	$sql = "select id from ph_order where recordnumber='{$ordersn}'";
	$sql = $dbconn->execute($sql);
	$idInfo = $dbconn->fetch_one($sql);
	return $idInfo['id'];
}

function getUserId($user){
	global $dbconn;
	$sql = "select global_user_id from power_global_user where global_user_name='{$user}'";
	$sql = $dbconn->execute($sql);
	$userInfo = $dbconn->fetch_one($sql);
	return $userInfo['global_user_id'];
}

function getPartnerId($companyName){
	global $dbconn;
	$companyName = trim($companyName);
	$sql = "select id from ph_partner where company_name='{$companyName}' ";
	$sql = $dbconn->execute($sql);
	$partnerInfo = $dbconn->fetch_one($sql);
	return $partnerInfo['id'];
}
?>
