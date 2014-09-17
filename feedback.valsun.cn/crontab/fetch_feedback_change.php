<?php 
	@session_start();
	error_reporting(0);
	require_once 'global_ebay_accounts.php';
	require_once "script_root_path.php";
	if($argc!=2)
	{
		exit("Usage: /usr/bin/php $argv[0] eBayAccount1 ");
		
	}
	$ebayaccount  = trim($argv[1]);
	
	if(!preg_match('#^[\da-zA-Z]+$#i',$ebayaccount)){
		exit("Invaild ebay account:$ebayaccount");
	}
	if(!in_array($ebayaccount,$GLOBAL_EBAY_ACCOUNT)){
		exit("$ebayaccount is not support now !");
	}
	
	$_SESSION['user']='vipchen';
	require_once 'ebay_order_cron_config.php';
	require_once 'ebaylibrary/GetFeedbackAPI.php';
	require_once 'ebaylibrary/keys/keys_'.$ebayaccount.'.php';
	$api_feedback = new GetFeedbackAPI($ebayaccount);
	$start = date('Y-m-d H:i:s');
	GetFeedback_change($ebayaccount);
	echo $ebayaccount.' Success----'.'start:'.$start.'----end:'.date('Y-m-d H:i:s')."\n";
	exit();
?>