<?php
$mctime = time();
echo "脚本开始时间".date('Y-m-d H:i:s',$mctime)."\n";
include "include/dbconnect.php";
$dbcon	= new DBClass();
include "include/function_general.php";
include "include/function_purchase.php";
$url = 'http://idc.gw.open.valsun.cn/router/rest?';  //idc开放系统入口地址
$token  = 'a6c94667ab1820b43c0b8a559b4bc909'; //用户finejo token
error_reporting(-1);
$carriersql 		= "SELECT * FROM ebay_carrier ";
$carriersql			= $dbcon->execute($carriersql);
$carrierList		= $dbcon->getResultArray($carriersql);
$carrierArr = array();
$transportList = getCarrierListById();
$flip_transportList = array_flip($transportList);
foreach($carrierList as $value){
	$carrier = $value['name'];
	if($carrier == '海运运输'){
		continue;	
	}
	//echo $carrier; echo "\n";
	if($carrier == "英国专线挂号"){
		$carrier = "英国专线挂号";
		$id = 63;
	}else
	if($carrier == "天天快递"){
		$carrier = "天天快递";
		$id = 64;
	}else
	if($carrier == "SurePost"){
		$carrier = "SurePost";
		$id = 65;
	}else
	if($carrier == "同城速递"){
		$carrier = "同城速递";
		$id = 66;
	}else
	if($carrier == "同城速递"){
		$carrier = "同城速递";
		$id = 66;
	}else
	if($carrier == "国内快递"){
		$carrier = "国内快递";
		$id = 67;
	}else
	if($carrier == "自提"){
		$carrier = "自提";
		$id = 68;
	}else
	if($carrier == "送货上门"){
		$carrier = "送货上门";
		$id = 69;
	}else
	if($carrier == "TNT"){
		$carrier = "TNT";
		$id = 70;
	}else
	if($carrier == "城市之星物流"){
		$carrier = "城市之星物流";
		$id = 71;
	}else
	if($carrier == "优速快递"){
		$carrier = "优速快递";
		$id = 72;
	}else
	if($carrier == "速尔快递"){
		$carrier = "速尔快递";
		$id = 73;
	}else
	if($carrier == "天地华宇物流"){
		$carrier = "天地华宇物流";
		$id = 74;
	}else
	if($carrier == "德邦物流"){
		$carrier = "德邦物流";
		$id = 75;
	}else
	if($carrier == "盛辉物流"){
		$carrier = "盛辉物流";
		$id = 76;
	}else
	if($carrier == "新加坡小包挂号"){
		$new_carrier = "新加坡邮政";
		$id = $flip_transportList[$new_carrier];
	}else
	if($carrier == "德国邮政挂号"){
		$new_carrier = "德国邮政";
		$id = $flip_transportList[$new_carrier];
	}else
	if($carrier == "顺丰快递"){
		$new_carrier = "顺丰速运";
		$id = $flip_transportList[$new_carrier];
	}else
	if($carrier == "汇通快递"){
		$new_carrier = "百世汇通快递";
		$id = $flip_transportList[$new_carrier];
	}else{
		$id = $flip_transportList[$carrier];
	}
	$carrierArr[$id] = $carrier;
}
//var_dump($carrierArr); echo "\n"; exit;
/*$arr1 = array_diff_assoc($carrierArr,$transportList);
var_dump($arr1); echo "\n";
$arr2 = array_diff_assoc($transportList,$carrierArr);
var_dump($arr2); echo "\n";
exit;*/
$flip_carrierArr = array_flip($carrierArr);
$omAccountList = getOmAccount();
$omAccount = array();
foreach($omAccountList as $value){
	$account = trim($value['account']);
	$omAccount[$account] = $value;
}
var_dump($omAccount); exit;
$user = 'vipchen';
$strtime = date('Y-m-d',$mctime);
$start = strtotime($strtime.' 00:00:00');
$end = strtotime($strtime.' 23:59:59');
$ebay_account = '';
$ebay_status = '';
$selectStr = " (scantime BETWEEN '{$start}' AND '{$end}') ";
if($ebay_account){
	$selectStr .= " AND ebay_account = '{$ebay_account}' ";
}
if($ebay_status){
	$selectStr .= " AND ebay_status = '{$ebay_status}' ";
}
$selectStr .= " AND ebay_combine != 0 AND ebay_user = '{$user}'  LIMIT 2 ";
$data = array();
$sql 	= "SELECT * FROM ebay_order WHERE ".$selectStr;
$sql	= $dbcon->execute($sql);
$orders	= $dbcon->getResultArray($sql);

foreach($orders as $order){
	$ebay_ordersn = $order['ebay_ordersn'];
	$platformId = $order['recordnumber'];
	$oRecordNumber = $order['recordnumber'];
	$ebay_note = $order['ebay_note'];
	
	$ebay_account	= $order['ebay_account'];
	$accountId 		= $omAccount[$ebay_account]['id'];
	$platformId 	= $omAccount[$ebay_account]['platformId'];
	
	$ebay_orderqk     = mysql_real_escape_string($order['ebay_orderqk']);
	$ebay_paystatus   = mysql_real_escape_string($order['ebay_paystatus']);
	$ebay_tid         = mysql_real_escape_string($order['ebay_tid']);
	$ebay_ptid 		  = mysql_real_escape_string($order['ebay_ptid']);
	$ebay_orderid     = mysql_real_escape_string($order['ebay_orderid']);
	$ebay_createdtime = mysql_real_escape_string($order['ebay_createdtime']);
	$ebay_paidtime    = mysql_real_escape_string($order['ebay_paidtime']);
	$ebay_userid 	  = mysql_real_escape_string($order['ebay_userid']);
	$ebay_username    = mysql_real_escape_string($order['ebay_username']);
	$ebay_usermail    = mysql_real_escape_string($order['ebay_usermail']);
	$ebay_street 	  = mysql_real_escape_string($order['ebay_street']);
	$ebay_street1 	  = mysql_real_escape_string($order['ebay_street1']);
	$ebay_city 		  = mysql_real_escape_string($order['ebay_city']);
	$ebay_state       = mysql_real_escape_string($order['ebay_state']);
	$ebay_couny 	  = mysql_real_escape_string($order['ebay_couny']);
	$ebay_countryname = mysql_real_escape_string($order['ebay_countryname']);
	$ebay_postcode    = mysql_real_escape_string($order['ebay_postcode']);
	$ebay_phone 	  = mysql_real_escape_string($order['ebay_phone']);
	$ebay_phone1 	  = mysql_real_escape_string($order['ebay_phone1']);
	$ebay_currency    = mysql_real_escape_string($order['ebay_currency']);
	$ebay_total 	  = mysql_real_escape_string($order['ebay_total']);
	$ebay_status      = mysql_real_escape_string($order['ebay_status']);
	$ebay_addtime     = mysql_real_escape_string($order['ebay_addtime']);
	$ebay_shipfee     = mysql_real_escape_string($order['ebay_shipfee']);
	$ebay_noteb       = mysql_real_escape_string($order['ebay_noteb']);
	$ebay_carrier     = mysql_real_escape_string($order['ebay_carrier']);
	$ebay_markettime  = mysql_real_escape_string($order['ebay_markettime']);
	$ebay_tracknumber = mysql_real_escape_string($order['ebay_tracknumber']);
	$ebay_site        = mysql_real_escape_string($order['ebay_site']);
	$eBayPaymentStatus = mysql_real_escape_string($order['eBayPaymentStatus']);
	$PayPalEmailAddress = mysql_real_escape_string($order['PayPalEmailAddress']);
	$ShippedTime        = mysql_real_escape_string($order['ShippedTime']);
	$canceltime         = mysql_real_escape_string($order['canceltime']);
	$cancelreason       = mysql_real_escape_string($order['cancelreason']);
	$ebay_feedback       = mysql_real_escape_string($order['ebay_feedback']);
	$orderweight         = mysql_real_escape_string($order['orderweight']);
	$orderweight2       = mysql_real_escape_string($order['orderweight2']);
	$ordershipfee        = mysql_real_escape_string($order['ordershipfee']);
	$scantime            = mysql_real_escape_string($order['scantime']);
	$packingtype         = mysql_real_escape_string($order['packingtype']);
	$packagingstaff      = mysql_real_escape_string($order['packagingstaff']);
	
	$isNote = 0;
	if(!empty($ebay_note)){
		$isNote = 1;
	}
	$orderData = array();
	$orderData = array('orderData' => array('recordNumber'=>$oRecordNumber,
											  'platformId'=>$platformId,
											  'accountId'=>$accountId,
											  'ordersTime'=>$ebay_createdtime,
											  'paymentTime'=>$ebay_paidtime,
											  'onlineTotal'=>$ebay_total,
											  'actualTotal'=>$ebay_total,
											  'transportId'=>$flip_carrierArr[$ebay_carrier],
											  'actualShipping'=>$ebay_shipfee,
											  'orderStatus'=>100,
											  'orderType'=>101,
											  'orderAttribute'=>1,
											  'marketTime'=>$ebay_markettime,
											  'ShippedTime'=>$ShippedTime,
											  //'pmId'=>'',
											  //'isFixed'=>1,
											  'channelId'=>'',
											  'calcWeight'=>$orderweight,
											  'calcShipping'=>$ordershipfee,
											  'orderAddTime'=>$ebay_addtime,
											  'isNote'=>$isNote,
											  'storeId'=>1
									  ),
						'orderExtenData' => array('declaredPrice'=>$ebay_orderqk,
												  'paymentStatus'=>$ebay_paystatus,
												  'transId'=>$ebay_tid,
												  'PayPalPaymentId'=>$ebay_ptid,
												  'site'=>$ebay_site,
												  'orderId'=>$ebay_orderid,
												  'platformUsername'=>$ebay_userid,
												  'currency'=>$ebay_currency,
												  'feedback'=>$ebay_noteb,
												  'PayPalEmailAddress'=>$PayPalEmailAddress,
												  'eBayPaymentStatus'=>$eBayPaymentStatus,
										  ),					  
						'orderUserInfoData' => array('username'=>$ebay_username,
												  'platformUsername'=>$ebay_userid,
												  'email'=>$ebay_usermail,
												  'countryName'=>$ebay_countryname,
												  'countrySn'=>$ebay_couny,
												  'currency'=>$ebay_currency,
												  'state' =>$ebay_state,
												  'city' =>$ebay_city,
												  'street' =>$ebay_street,
												  'address2' =>$ebay_street1,
												  'address3' =>'',
												  'landline' =>$ebay_phone,
												  'phone' =>$ebay_phone1,
												  'zipCode' =>$ebay_postcode,
												  )
										  );
	$detailsql 		= "SELECT * FROM ebay_orderdetail WHERE ebay_ordersn = '{$ebay_ordersn}' ";
	$detailsql		= $dbcon->execute($detailsql);
	$orderdetails	= $dbcon->getResultArray($detailsql);
	foreach($orderdetails as $orderdetail){
		$ebay_itemid    	= mysql_real_escape_string($orderdetail['ebay_itemid']);
		$ebay_itemtitle 	= mysql_real_escape_string($orderdetail['ebay_itemtitle']);
		$ebay_itemurl   	= mysql_real_escape_string($orderdetail['ebay_itemurl']);
		$sku            	= mysql_real_escape_string($orderdetail['sku']);
		$ebay_itemprice 	= mysql_real_escape_string($orderdetail['ebay_itemprice']);
		$ebay_amount    	= mysql_real_escape_string($orderdetail['ebay_amount']);
		$ebay_createdtime 	= mysql_real_escape_string($orderdetail['ebay_createdtime']);
		$ebay_shiptype 		= mysql_real_escape_string($orderdetail['ebay_shiptype']);
		$shipingfee 		= mysql_real_escape_string($orderdetail['shipingfee']);
		$ebay_account 		= mysql_real_escape_string($orderdetail['ebay_account']);
		$ebay_site 			= mysql_real_escape_string($orderdetail['ebay_site']);
		$addtime 			= mysql_real_escape_string($orderdetail['addtime']);
		$FinalValueFee 		= mysql_real_escape_string($orderdetail['FinalValueFee']);
		$FeeOrCreditAmount 	= mysql_real_escape_string($orderdetail['FeeOrCreditAmount']);
		$ebay_tid 			= mysql_real_escape_string($orderdetail['ebay_tid']);
		$is_suffix 			= mysql_real_escape_string($orderdetail['is_suffix']);
		$ebay_feedback 		= mysql_real_escape_string($orderdetail['ebay_feedback']);
		
		$obj_order_detail_data[] = array('orderDetailData' => array('recordNumber'=>$oRecordNumber,
																	'itemPrice'=>$ebay_itemprice,
																	'sku'=>strtoupper($sku),
																	'amount'=>$ebay_amount,
																	'shippingFee'=>$shipingfee,
																	'createdTime'=>$ebay_createdtime,
																	),			
										 'orderDetailExtenData' => array('itemId'=>$ebay_itemid,
																		 'transId'=>$ebay_tid,
																		 'itemTitle'=>$ebay_itemtitle,
																		 'itemURL'=>$ebay_itemurl,
																		 'shippingType'=>$ebay_shiptype,
																		 'FinalValueFee'=>$FinalValueFee,
																		 'FeeOrCreditAmount'=>$FeeOrCreditAmount,
																		 'ListingType'=>$ListingType,
																		 'note'=>$note,
																		 //'attribute'=>$attribute,
																		 //'is_suffix'=>$is_suffix
																		 )
								   );
								
	}
							
	$orderData['orderDetail'] = $obj_order_detail_data;
	
}

write_log('move_order_'.date("Ymd").'/'.date("H").'.sql', $log_data."\n\n");

$endtime = time();
echo "脚本结束时间".date('Y-m-d H:i:s',$endtime)."\n";