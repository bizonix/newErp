<?php
@session_start();
error_reporting(-1);
set_time_limit(0);

$orderid 	= isset($_POST['ids']) ? trim($_POST['ids']) : '';
$po_obj 	= new PackingOrderModel();
$ordersinfo = $po_obj->getaSetOfOrderInfo($orderid);

$text = '';
$lt = "\t";
$ln = "\r\n";
foreach($ordersinfo as $order){
	$all_amount = 0;
	$all_price  = 0;

	$text .= '|'.$order['username'].$lt;
	$text .= '|'.$order['username'].$lt;
	$text .= '|'.str_replace(array("\n","\r\n","\r"),"",$order['street']).$lt;
	$text .= '|'.str_replace(array("\n","\r\n","\r"),"",$order['address2']).$lt;
	$text .= '|'.$lt;
	$text .= '|'.$order['city'].$lt;
	$text .= '|'.$order['zipCode'].$lt;
	$text .= '|'.$order['countryName'].$lt;
	/*
	if(empty($order['ebay_couny'])){
		$country_name = $order['countryName'];
		$c_sql   = "select * from ebay_countrys where countryen='{$country_name}'";
		$c_sql	 = $dbcon->execute($c_sql);
		$country = $dbcon->fetch_one($c_sql);
		$text .= '|'.$country['countrysn'].$lt;
	}else{
		$text .= '|'.$order['ebay_couny'].$lt;
	}*/
	$text .= '|'.$order['countrySn'].$lt;
	$text .= !empty($order['landline']) ? '|'.$order['landline'].$lt : '|'.$order['phone'].$lt;
	$text .= '|'.$order['recordNumber'].$lt;
	
	$order_details = CommonModel::getExpressRemark($order['id']);
	$count = count($order_details);
	if(!empty($order_details)){
		foreach ($order_details AS $key=>$order_detail){
			$text .= '|'.str_replace('´','\'',stripslashes($order_detail['description'])).$lt;
			$price = $order_detail['price']*$order_detail['amount'];
			$all_amount += $order_detail['amount'];      //总申数量
			$all_price += $price;        //总申报价
		}
	}
	if($count<3){
		for($i=0;$i<(3-$count);$i++){
			$text .= '|'.$lt;
		}
	}
	//$text .= '|'.$all_amount.$lt;
	$text .= '|1'.$lt;
	$text .= (round($order['calcWeight'], 1))>=0.5 ? '|'.(round($order['calcWeight'], 1)).$lt:'|0.5'.$lt;
	$text .= '|'.$all_price.$lt;
	$text .= '|P'.$lt;
	$text .= '|'.$lt;
	$text .= '|'.$count.$lt;
	$text .= '|Y'.$lt;
	$text .= '|'.$lt.'|'.$lt.'|'.$lt.'|'.$lt.'|'.$lt;
	$text .= '|DDU'.$lt;
	$text .= '|'.$lt.'|'.$lt.'|'.$lt.'|'.$lt;
	if(!empty($order_details)){
		foreach ($order_details AS $key=>$order_detail){
			$text .= '|'.$order_detail['amount'].$lt;
			$text .= '|'.$lt.'|'.$lt;
			$text .= '|KGS'.$lt;
			$text .= '|1'.$lt;
			$text .= '|'.$lt.'|'.$lt.'|'.$lt.'|'.$lt;
			$text .= '|USD'.$lt;
			$text .= '|1'.$lt;
			$text .= '|'.round($order_detail['price'],1).$lt;
			$text .= '|'.$lt;
			$text .= '|'.str_replace('´','\'',stripslashes($order_detail['description'])).$lt;
			$text .= '|'.$lt.'|'.$lt.'|'.$lt.'|'.$lt.'|'.$lt.'|'.$lt.'|'.$lt.'|'.$lt.'|'.$lt.'|'.$lt.'|'.$lt.'|'.$lt.'|'.$lt.'|'.$lt;
		}
	}

	$text .= $ln;
}

$titlename = date("YmdHis").'.txt';

header("Content-Type: application/octet-stream");    
if (preg_match("/MSIE/", $_SERVER['HTTP_USER_AGENT']) ) {    
	header('Content-Disposition:  attachment; filename="' .  $titlename . '"');    
} elseif (preg_match("/Firefox/", $_SERVER['HTTP_USER_AGENT'])) {    
    header('Content-Disposition: attachment; filename*="' .  $titlename . '"');    
} else {    
    header('Content-Disposition: attachment; filename="' .  $titlename . '"');    
}   
echo $text;
exit;


