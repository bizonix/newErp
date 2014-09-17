<?php
set_time_limit(0);
error_reporting(E_ALL);
$link_listing1	=	mysql_connect('192.168.200.222','cerp','123456') or die("Could not connect: " . mysql_error());
$db_listing		=	mysql_select_db('valsun_warehouse',$link_listing1) or die('数据库连接错误');
$link_listing2  =   mysql_connect('192.168.200.188','cerp','123456', true)or die("Could not connect: " . mysql_error());
$db_listing2    =	mysql_select_db('cerp',$link_listing2) or die('数据库连接错误');
mysql_query('set names utf8',$link_listing1);
mysql_query('set names utf8',$link_listing2);

$memc_obj = new Memcache;
$memc_obj->connect('192.168.200.222', 11211);

$trans_system_carrier = $memc_obj->get('trans_system_carrier');
$carrier_arr = array();
foreach($trans_system_carrier as $value){
	$carrier_arr[$value['id']] = $value['carrierNameCn'];
}
$flip_carrier_arr = array_flip($carrier_arr);

$n = 10; //随机显示的记录数
$results = array(); //记录结果的数组
//$rowNum = mysql_result(mysql_query("SELECT COUNT(*) AS cnt FROM ebay_order", $link_listing2), 0, 0);
/*$sql = "SELECT * FROM ebay_order WHERE ebay_id >= (((SELECT MAX(ebay_id) FROM ebay_order where ebay_carrier in ('EUB','Global Mail','FedEx','EMS','UPS','DHL','顺丰快递','韵达快递','申通快递','中通快递','圆通快递')) -(SELECT MIN(ebay_id) FROM ebay_order where ebay_carrier in ('EUB','Global Mail','FedEx','EMS','UPS','DHL','顺丰快递','韵达快递','申通快递','中通快递','圆通快递'))) * RAND() + (SELECT MIN(ebay_id) FROM ebay_order where ebay_carrier in ('EUB','Global Mail','FedEx','EMS','UPS','DHL','顺丰快递','韵达快递','申通快递','中通快递','圆通快递'))) LIMIT $n  ";//随机读取order表数据同步到发货单中*/
$sql = "SELECT * FROM ebay_order WHERE ebay_carrier in ('中国邮政平邮','中国邮政挂号','香港小包挂号','香港小包平邮','EUB','Global Mail','FedEx','EMS','UPS','DHL','顺丰快递','韵达快递','申通快递','中通快递','圆通快递') order by RAND() desc LIMIT $n  ";
//$sql = "SELECT * FROM ebay_order WHERE 1=1 order by RAND() desc LIMIT $n  ";
$query = mysql_query($sql, $link_listing2);
while($result = mysql_fetch_assoc($query)) {
	$mctime = time();
	$sql = "SELECT * FROM ebay_orderdetail WHERE ebay_ordersn = '{$result['ebay_ordersn']}' ";
	$query_detal = mysql_query($sql, $link_listing2);
	if(mysql_num_rows($query_detal) == 0){
		continue;
	}
	$insert_arr = array();
	$detailArrs = array();
	$allDetails = array();
	while($line = mysql_fetch_assoc($query_detal)) {
		$sku = $line['sku'];
		$ebay_amount = $line['ebay_amount'];
		$allDetails[$sku] = $ebay_amount;
		$sql = "SELECT goods_sncombine FROM ebay_productscombine WHERE goods_sn ='{$sku}'";
		$query_ep = mysql_query($sql, $link_listing2);
		if(mysql_num_rows($query_ep) == 0){
			$detailArrs[$sku] = $ebay_amount;
		}else{
			$combinelists =  mysql_fetch_assoc($query_ep);
			if (strpos($combinelists['goods_sncombine'], ',')!==false){
				$skulists = explode(',', $combinelists['goods_sncombine']);
				foreach ($skulists AS $skulist){
					list($_sku, $snum) = strpos($skulist, '*')!==false ? explode('*', $skulist) : array($skulist, 1);
					$detailArrs[trim($sku)][trim($_sku)] = $snum * $ebay_amount;
				}
			}else if (strpos($combinelists['goods_sncombine'], '*')!==false){
				list($_sku, $snum) = explode('*', $combinelists['goods_sncombine']);
				$detailArrs[trim($sku)][trim($_sku)] = $snum * $ebay_amount;
			}else{
				$detailArrs[trim($sku)] = $ebay_amount;
			}
		}
	}
	if(count($detailArrs) == 1){
		$orderAttributes = 1;
	}else{
		$orderAttributes = 2;
	}
	//$results[$result['ebay_id']] = $result;
	$insert_arr[] = "username='{$result['ebay_username']}'";
	$insert_arr[] = "platformUsername='{$result['ebay_userid']}'";
	$insert_arr[] = "email='{$result['ebay_usermail']}'";
	$insert_arr[] = "countryName='{$result['ebay_countryname']}'";
	$insert_arr[] = "countrySn='{$result['ebay_couny']}'";
	$insert_arr[] = "state='{$result['ebay_state']}'";
	$insert_arr[] = "city='{$result['ebay_city']}'";
	$insert_arr[] = "street='{$result['ebay_street']}'";
	$insert_arr[] = "address2='{$result['ebay_street1']}'";
	$insert_arr[] = "currency='{$result['ebay_currency']}'";
	$insert_arr[] = "landline='{$result['ebay_phone']}'";
	$insert_arr[] = "phone='{$result['ebay_phone1']}'";
	$insert_arr[] = "zipCode='{$result['ebay_postcode']}'";
	$insert_arr[] = "transportId='{$flip_carrier_arr[$result['ebay_carrier']]}'";
	$insert_arr[] = "account='{$result['ebay_account']}'";
	$insert_arr[] = "orderStatus='400'";
	$insert_arr[] = "orderAttributes={$orderAttributes}";
	$insert_arr[] = "isFixed=1";
	$insert_arr[] = "channelId='{1}'";
	$insert_arr[] = "total='{$result['ebay_total']}'";
	$insert_arr[] = "calcWeight={$result['orderweight']}";
	$insert_arr[] = "calcShipping={$result['ordershipfee']}";
	$insert_arr[] = "createdTime='{$mctime}'";
	
	$insert_sql = " INSERT INTO wh_shipping_order SET ".implode(",", $insert_arr);
	echo $insert_sql; echo "\n";
	if(!mysql_query($insert_sql, $link_listing1)){
		continue;
	}
	$originOrderId = mysql_insert_id($link_listing1);
	//var_dump($results); exit;
	$insert_relation_arr = array();
	$insert_relation_arr[] = "originOrderId = '{$result['ebay_id']}'";
	$insert_relation_arr[] = "shipOrderId = '{$originOrderId}'";
	$insert_relation_arr[] = "recordNumber = '{$result['recordnumber']}'";
	$insert_relation_sql = "INSERT INTO wh_shipping_order_relation SET ".implode(",", $insert_relation_arr);
	echo $insert_relation_sql; echo "\n";
	mysql_query($insert_relation_sql, $link_listing1);
	if(!empty($result['ebay_tracknumber'])){
		$insert_tracknumber_arr = array();
		$insert_tracknumber_arr[] = "tracknumber = '{$result['ebay_tracknumber']}'";
		$insert_tracknumber_arr[] = "shipOrderId = '{$originOrderId}'";
		$insert_tracknumber_arr[] = "createdTime = '{$mctime}'";
		$insert_tracknumber_sql = "INSERT INTO wh_order_tracknumber SET ".implode(",", $insert_tracknumber_arr);
		echo $insert_tracknumber_sql; echo "\n";
		mysql_query($insert_tracknumber_sql, $link_listing1);
	}
	foreach($detailArrs as $dsku => $dvalue){
		if(is_array($dvalue)){
			foreach($dvalue as $sku0 => $num0){
				$insert_detail_sql = "INSERT INTO wh_shipping_orderdetail(`shipOrderId`,`combineSku`,`combineNum`,`sku`,`amount`) VALUES ('{$originOrderId}','{$dsku}','{$allDetails[$dsku]}','{$sku0}','{$num0}')";
				echo $insert_detail_sql; echo "\n";
				mysql_query($insert_detail_sql, $link_listing1);
			}
		}else{
			$insert_detail_sql = "INSERT INTO wh_shipping_orderdetail(`shipOrderId`,`sku`,`amount`) VALUES ('{$originOrderId}','{$dsku}','{$dvalue}')";
			echo $insert_detail_sql; echo "\n";
			mysql_query($insert_detail_sql, $link_listing1);
		}
	}
}