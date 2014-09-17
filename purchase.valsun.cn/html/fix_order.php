<?php
include "/data/web/purchase.valsun.cn/html/config.php";
include "/data/web/purchase.valsun.cn/lib/functions.php";

error_reporting(-1);
global $dbConn,$dbconn;
$sql = "select id,recordnumber from ph_order  where  status=3 ";
$sql = $dbConn->execute($sql);
$idArr = $dbConn->getResultArray($sql);
$pidArr = array();
foreach($idArr as $item){
	checkOrderFinish($item['id'],$item['recordnumber']);
}



//checkOrderFinish(174696,"SWB140520578685");

function checkOrderFinish($orderid,$recordnumber){
	global $dbconn;
	$sql = "select * from ph_order_detail where po_id={$orderid} and is_delete=0";
	$sql = $dbconn->execute($sql);
	$numInfo = $dbconn->getResultArray($sql);
	$flag = array();
	$now = time();
	foreach($numInfo as $item){
		if($item["stockqty"] < $item["count"]){//到货数量和订购数量比较
			return;
		}
	}
	if(count($numInfo) > 0){
		$sql = "update ph_order set status=4, finishtime={$now} where id={$orderid}";
		echo $sql.",{$recordnumber}\n";
		if($dbconn->execute($sql)){
			return 1;
		}
	}
}
?>
