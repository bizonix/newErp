<?php
error_reporting(-1);
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
require "/data/web/order.valsun.cn/framework.php";
Core::getInstance();
$sql = "select * from ebay_paypal1 where 1=1 ";
$query	= $dbConn->query($sql);
$ebay_paypals =   $dbConn->fetch_array_all($query);
//var_dump($om_status_menu); echo "\n"; exit;
$array = array();
foreach($ebay_paypals as $ebay_paypal){
	$account = $ebay_paypal['account'];
	$name = $ebay_paypal['name'];
	$pass = $ebay_paypal['pass'];
	$signature = $ebay_paypal['signature'];
	$account2 = $ebay_paypal['account2'];
	$pass2 = $ebay_paypal['pass2'];
	$signature2 = $ebay_paypal['signature2'];
	$ebayaccount = $ebay_paypal['ebayaccount'];
	$fees = $ebay_paypal['fees'];
	$user = $ebay_paypal['user']; 	 	
	
	$array = array();
	$array['name'] = $name;
	$array['ebayaccount'] = $ebayaccount;
	$array['account1'] = $account;
	$array['pass1'] = $pass;
	$array['signature1'] = $signature;
	$array['account2'] = $account2;
	$array['pass2'] = $pass2;
	$array['signature2'] = $signature2;
	$array['fees'] = $fees;
	$array['user'] = $user;
	
	$sql = "select * from om_paypal where ebayaccount = '{$ebayaccount}' ";
	$query	= $dbConn->query($sql);
	$result =   $dbConn->fetch_array($query);
	$string = array2sql_extral($array);
	if($result){
		$sql = "update om_paypal set ".$string." where ebayaccount = '{$ebayaccount}' ";
		if($dbConn->query($sql)){
			echo $ebayaccount." update success!\n";
		}else{
			echo $ebayaccount." update error!\n";
		}
	}else{
		$sql = "insert into om_paypal set ".$string;
		if($dbConn->query($sql)){
			echo $ebayaccount." add success!\n";
		}else{
			echo $ebayaccount." add error!\n";
		}
	}
}
exit;
?>