<?php
include('/data/web/data/php-amqplib/demo/config.php');
include_once "sync_data.php";
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;
error_reporting(0);


global $dbcon;

//获取所有平台账号信息
$sql = "SELECT ebay_account,ebay_platform FROM ebay_account WHERE ebay_platform!='' ORDER BY ebay_platform ASC";
$sql  = $dbcon->execute($sql);
$eaccounts = $dbcon->getResultArray($sql);
$accounts = array();
$cn2enarr = array( //中文转英文
	'ebay平台' => 'ebay',
	'亚马逊' => 'amazon',
	'出口通' => 'chukoutong',
	'国内销售部' => 'guonei',
	'天猫哲果' => 'zegoo',
	'天猫芬哲' => 'fenjo',
	'海外仓' => 'oversea',
	'线下结算客户' => 'offline'
);
foreach ($eaccounts AS $eaccount){
	$enAccount = $cn2enarr[$eaccount['ebay_platform']];
	if(isset($enAccount)){
		$accounts[$enAccount][] = $eaccount['ebay_account'];
	}else{
		$accounts[$eaccount['ebay_platform']][] = $eaccount['ebay_account'];
	}
}

$platform = array_keys($accounts);
$date = "2014-07-29";
$platformNum = array();
foreach($platform as $item){
	$platformNum[$item] = 0;
}
$insertData = array("date"=>$date,"platform" =>$platformNum);
//$db->send_statistics->insert($insertData);
//exit;
//$sql = "SELECT * FROM  `ebay_ordergrossrate_queue` datetime between '2014-07-29 00:00:00' and '2014-07-29 23:59:59'";
$sql = "SELECT order_id FROM  `ebay_ordergrossrate` where order_scantime between 1406563200 and 1406649599 ";
/*
$sql = "SELECT ebay_id, ebay_account FROM  `ebay_order` WHERE scantime BETWEEN 1406563200  AND 1406649599 ";
$sql = $dbcon->execute($sql);
$orderid_arr = $dbcon->getResultArray($sql);

foreach($orderid_arr as $orderlist){
	print_r($orderlist);
	foreach($accounts as $platform => $accountarr){
		if(in_array($orderlist['ebay_account'],$accountarr)){
			$order_platform = $platform;
			break;
		}
	}
	if($order_platform == "ebay"){
		$insertData = array("ebay_id"=>$orderlist['ebay_id'],"status"=>0);
		$table_name = "ebay_id_2014-07-29";
		$db->$table_name->insert($insertData);
	}
	$contion = array("date" => "2014-07-29");
	echo "{$order_platform}\n";
	$incData = array('$inc' => array("platform.{$order_platform}"=>1));
	$db->send_statistics->update(
		$contion,$incData
	);
}
	 */


// 修复显示异常详细信息

$where = array("status" => 0);
$table_name = "ebay_id_2014-07-29";
$orderid_arr = $db->$table_name->find($where,array('ebay_id'));
foreach($orderid_arr as $doc){
	//$rtn = calc_order($doc['order_id'],"ebay_test");
	echo $doc['ebay_id']."\n";
	$rtn = $db->ebay->find(array('order_id'=>$doc['ebay_id']))->count();
	if($rtn == 0){
		echo "漏传的\n";
		calc_order($doc['ebay_id'],"ebay");
	}else{
		echo "已经计算了的\n";
		$contion = array('ebay_id'=>$doc['ebay_id']);
		$setcontion = array('$set',array("status" => 1));
		$db->$table_name->update($contion,$setcontion);
	}
}
//calc_order("14421361","ebay_test");




$where = array(
	'order_sendZone' => null,
    'send_carrier' => array('$in' => array('中国邮政平邮','中国邮政挂号'))
    //'send_carrier' => '中国邮政平邮'
);

/*
$orderid_arr = $db->ebay->find($where,array('order_id','order_sendZone','send_carrier'));
foreach($orderid_arr as $doc){
	$order_sendZone = _getSendZone($doc['order_id']);
	var_dump($doc['order_id'],$order_sendZone);
	$contion = array("order_id" => $doc['order_id']);
	//$contion = array("order_id" => "14176294");
	$newdata = array("order_sendZone" => $order_sendZone);
	//$newdata = array("order_sendZone" => '福建');
	$rtnMsg = $db->ebay->update($contion,array('$set'=>$newdata),array("multiple" => true));
	if($rtnMsg['updatedExisting'] === true){
		echo "订单{$doc['order_id']},更新分区：{$order_sendZone}成功\n";
	}else{
		echo "订单{$doc['order_id']},更新分区：{$order_sendZone}失败\n";
	}
	//var_dump($db->ebay->update($contion,array('$set'=>$newdata),array("multiple" => true)));
}
 */






?>
