<?php
set_time_limit(0);
error_reporting(E_ALL);
$link_listing1	=	mysql_connect('192.168.200.222','cerp','123456') or die("Could not connect: " . mysql_error());
$db_listing		=	mysql_select_db('valsun_ordermanage',$link_listing1) or die('数据库连接错误');
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

$sql = "select * from om_platform";
$query = mysql_query($sql, $link_listing1);
$om_platform = array();
while($row = mysql_fetch_assoc($query)){
	$om_platform[$row['id']] = $row['platform'];
}
$flip_om_platform = array_flip($om_platform);

$sql = "select * from om_account";
$query = mysql_query($sql, $link_listing1);
$om_account = array();
while($row = mysql_fetch_assoc($query)){
	$om_account[$row['id']] = $row['account'];
}
$flip_om_account = array_flip($om_account);

$n = 10; //随机显示的记录数
$results = array(); //记录结果的数组
$val = mt_rand(1766059, 5766059);
//$rowNum = mysql_result(mysql_query("SELECT COUNT(*) AS cnt FROM ebay_order", $link_listing2), 0, 0);
//$sql = "SELECT * FROM ebay_order WHERE ebay_id >= (((SELECT MAX(ebay_id) FROM ebay_order where ebay_carrier in ('EUB','Global Mail','FedEx','EMS','UPS','DHL','顺丰快递','韵达快递','申通快递','中通快递','圆通快递')) -(SELECT MIN(ebay_id) FROM ebay_order where ebay_carrier in ('EUB','Global Mail','FedEx','EMS','UPS','DHL','顺丰快递','韵达快递','申通快递','中通快递','圆通快递'))) * RAND() + (SELECT MIN(ebay_id) FROM ebay_order where ebay_carrier in ('EUB','Global Mail','FedEx','EMS','UPS','DHL','顺丰快递','韵达快递','申通快递','中通快递','圆通快递'))) LIMIT $n  ";//随机读取order表数据同步到发货单中
//$sql = "SELECT * FROM ebay_order WHERE ebay_carrier in ('中国邮政平邮','中国邮政挂号','香港小包挂号','香港小包平邮','EUB','Global Mail','FedEx','EMS','UPS','DHL','顺丰快递','韵达快递','申通快递','中通快递','圆通快递') order by RAND() desc LIMIT $n  ";
//$sql = "SELECT * FROM ebay_order WHERE 1=1 order by RAND() desc LIMIT $n  ";
while(true){
	$sql = "SELECT * FROM ebay_order where ebay_id = '{$val}' and ebay_combine !=1 and ebay_user = 'vipchen' ";
	$query = mysql_query($sql, $link_listing2);
	$si = mysql_num_rows($query);
	if($si!=0){
		break;	
	}
	$val = mt_rand(1766059, 5766059);
}
echo $sql; echo "\n";
//$query = mysql_query($sql, $link_listing2);
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
		$allDetails[$sku] = $line;
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
	if(empty($result['ebay_note'])){
		$isNote = 0;
	}else{
		$isNote = 1;
	}
	//$results[$result['ebay_id']] = $result;
	$insert_arr[] = "recordNumber='{$result['recordnumber']}'";
	$insert_arr[] = "platformId='1'";
	$insert_arr[] = "accountId='{$flip_om_account[$result['ebay_account']]}'";
	$insert_arr[] = "ordersTime='{$result['ebay_createdtime']}'";
	$insert_arr[] = "paymentTime='{$result['ebay_paidtime']}'";
	$insert_arr[] = "onlineTotal='{$result['ebay_total']}'";
	$insert_arr[] = "actualTotal='{$result['ebay_total']}'";
	$insert_arr[] = "transportId='{$flip_carrier_arr[$result['ebay_carrier']]}'";
	$insert_arr[] = "orderStatus='100'";
	$insert_arr[] = "orderType='101'";
	$insert_arr[] = "pmId='2'";
	$insert_arr[] = "channelId='{1}'";
	$insert_arr[] = "calcWeight='{$result['orderweight']}'";
	$insert_arr[] = "calcShipping='{$result['ordershipfee']}'";
	$insert_arr[] = "orderAddTime='{$mctime}'";
	$insert_arr[] = "isNote='{$isNote}'";
	/*$insert_arr[] = "transportId='{$flip_carrier_arr[$result['ebay_carrier']]}'";
	$insert_arr[] = "account='{$result['ebay_account']}'";
	$insert_arr[] = "orderStatus='400'";
	$insert_arr[] = "orderAttributes={$orderAttributes}";
	$insert_arr[] = "isFixed=1";
	$insert_arr[] = "channelId='{1}'";
	$insert_arr[] = "total='{$result['ebay_total']}'";
	$insert_arr[] = "calcWeight={$result['orderweight']}";
	$insert_arr[] = "calcShipping={$result['ordershipfee']}";*/
	
	$insert_sql = "INSERT INTO om_unshipped_order SET ".implode(",", $insert_arr);
	echo $insert_sql; echo "\n";
	if(!mysql_query($insert_sql, $link_listing1)){
		continue;
	}
	$originOrderId = mysql_insert_id($link_listing1);
	//var_dump($results); exit;
	$insert_relation_arr = array();
	$insert_relation_arr[] = "omOrderId = '{$originOrderId}'";
	$insert_relation_arr[] = "username = '{$result['ebay_username']}'";
	$insert_relation_arr[] = "platformUsername = '{$result['ebay_userid']}'";
	$insert_relation_arr[] = "email = '{$result['ebay_usermail']}'";
	$insert_relation_arr[] = "countryName = '{$result['ebay_countryname']}'";
	$insert_relation_arr[] = "countrySn = '{$result['ebay_couny']}'";
	$insert_relation_arr[] = "state = '{$result['ebay_state']}'";
	$insert_relation_arr[] = "city = '{$result['ebay_city']}'";
	$insert_relation_arr[] = "street = '{$result['ebay_street']}'";
	$insert_relation_arr[] = "address2 = '{$result['ebay_street1']}'";
	$insert_relation_arr[] = "address3 = '{$result['ebay_street1']}'";
	$insert_relation_arr[] = "landline = '{$result['ebay_phone']}'";
	$insert_relation_arr[] = "phone = '{$result['ebay_phone1']}'";
	$insert_relation_arr[] = "zipCode = '{$result['postive']}'";
	$insert_relation_sql = "INSERT INTO om_unshipped_order_userInfo SET ".implode(",", $insert_relation_arr);
	echo $insert_relation_sql; echo "\n";
	mysql_query($insert_relation_sql, $link_listing1);
	
	$insert_relation_arr2 = array();
	$insert_relation_arr2[] = "omOrderId = '{$originOrderId}'";
	$insert_relation_arr2[] = "declaredPrice = '{$result['ebay_orderqk']}'";
	$insert_relation_arr2[] = "paymentStatus = '{$result['ebay_paystatus']}'";
	$insert_relation_arr2[] = "transId = '{$result['ebay_tid']}'";
	$insert_relation_arr2[] = "PayPalPaymentId = '{$result['ebay_ptid']}'";
	$insert_relation_arr2[] = "site = 'US'";
	$insert_relation_arr2[] = "orderId = '{$result['ebay_orderid']}'";
	$insert_relation_arr2[] = "platformUsername = '{$result['ebay_userid']}'";
	$insert_relation_arr2[] = "currency = '{$result['ebay_currency']}'";
	$insert_relation_arr2[] = "feedback = '{$result['ebay_note']}'";
	$insert_relation_arr2[] = "PayPalEmailAddress = '{$result['PayPalEmailAddress']}'";
	$insert_relation_arr2[] = "eBayPaymentStatus = '{$result['eBayPaymentStatus']}'";
	$insert_relation_sql2 = "INSERT INTO om_unshipped_order_extension_ebay SET ".implode(",", $insert_relation_arr2);
	echo $insert_relation_sql2; echo "\n";
	mysql_query($insert_relation_sql2, $link_listing1);
	
	/*if(!empty($result['ebay_tracknumber'])){
		$insert_tracknumber_arr = array();
		$insert_tracknumber_arr[] = "tracknumber = '{$result['ebay_tracknumber']}'";
		$insert_tracknumber_arr[] = "shipOrderId = '{$originOrderId}'";
		$insert_tracknumber_arr[] = "createdTime = '{$mctime}'";
		$insert_tracknumber_sql = "INSERT INTO wh_order_tracknumber SET ".implode(",", $insert_tracknumber_arr);
		echo $insert_tracknumber_sql; echo "\n";
		mysql_query($insert_tracknumber_sql, $link_listing1);
	}*/
	foreach($allDetails as $dsku => $dvalue){
		$insert_detail_sql = "INSERT INTO om_unshipped_order_detail(`omOrderId`,`recordNumber`,`itemPrice`,`sku`,`amount`,`shippingFee`,`reviews`,`createdTime`) VALUES ('{$originOrderId}','{$dvalue['recordnumber']}','{$dvalue['ebay_itemprice']}','{$dsku}','{$dvalue['ebay_amount']}','{$dvalue['shipingfee']}','1','{$mctime}')";
		echo $insert_detail_sql; echo "\n";
		mysql_query($insert_detail_sql, $link_listing1);
		$originOrderDetailId = mysql_insert_id($link_listing1);
		
		$insert_relation_detail_arr = array();
		$insert_relation_detail_arr[] = "omOrderdetailId = '{$originOrderDetailId}'";
		$insert_relation_detail_arr[] = "itemId = '{$dvalue['ebay_itemid']}'";
		$insert_relation_detail_arr[] = "transId = '{$dvalue['ebay_tid']}'";
		$insert_relation_detail_arr[] = "itemTitle = '{$dvalue['ebay_itemtitle']}'";
		$insert_relation_detail_arr[] = "itemURL = '{$dvalue['ebay_itemurl']}'";
		$insert_relation_detail_arr[] = "shippingType = '{$dvalue['ebay_shiptype']}'";
		$insert_relation_detail_arr[] = "FinalValueFee = '{$dvalue['FinalValueFee']}'";
		$insert_relation_detail_arr[] = "FeeOrCreditAmount = '{$dvalue['FeeOrCreditAmount']}'";
		$insert_relation_detail_arr[] = "ListingType = '{$dvalue['ListingType']}'";
		$insert_relation_detail_sql = "INSERT INTO om_unshipped_order_detail_extension_ebay SET ".implode(",", $insert_relation_detail_arr);
		echo $insert_relation_detail_sql; echo "\n";
		mysql_query($insert_relation_detail_sql, $link_listing1);
	}
}