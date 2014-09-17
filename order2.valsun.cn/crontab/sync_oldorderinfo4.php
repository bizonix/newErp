<?php
error_reporting(-1);
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
require "/data/web/order.valsun.cn/framework.php";
Core::getInstance();
$sql = "select * from eub_account where 1=1 ";
$query	= $dbConn->query($sql);
$eub_accounts =   $dbConn->fetch_array_all($query);
//var_dump($om_status_menu); echo "\n"; exit;
$array = array();
foreach($eub_accounts as $eub_account){
	unset($eub_account['id']);
	$account = $eub_account['ebay_account'];
	unset($eub_account['ebay_account']);
	$eub_account['account'] = $account;
	$eub_account['accountId'] = $eub_account['pid'];
	unset($eub_account['pid']);
	$sql = "select * from om_eub_account where account = '{$account}' ";
	$query	= $dbConn->query($sql);
	$result =   $dbConn->fetch_array($query);
	$string = array2sql_extral($eub_account);
	if($result){
		$sql = "update om_eub_account set ".$string." where account='{$account}'";
		if($dbConn->query($sql)){
			echo $account." update success!\n";
		}else{
			echo $account." update error!\n";
		}
	}else{
		$string = array2sql_extral($eub_account);
		$sql = "insert into om_eub_account set ".$string;
		if($dbConn->query($sql)){
			echo $account." add success!\n";
		}else{
			echo $account." add error!\n";
		}
	}
}
exit;
?>