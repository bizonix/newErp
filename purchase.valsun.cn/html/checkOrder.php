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
	print_r($flag);
	if(!in_array(0,$flag)){
		$sql = "update ph_order set status=4, finishtime={$now} where recordnumber='{$recordnumber}'";
		echo $sql;
		/*
		if($dbconn->execute($sql)){
			echo "$sql\n";
		}
		 */
	}
}



$sql = "SELECT * FROM  `in_warehouse_history` WHERE  `in_time` >1392422400 AND in_time < 1392595199  ";
$sql = $dbConn->execute($sql);
$inhistory = $dbConn->getResultArray($sql);

foreach($inhistory as $item){
	checkOrderFinish($item['in_ordersn']);
}


?>
