<?php
	//脚本参数检验
	if($argc<4){
		exit("Usage: /usr/bin/php	$argv[0] eBayAccount \n");
	}
	$nowtime 	= time();
	$account	= trim($argv[1]);
	$site 		= trim($argv[2]);
	$minute		= intval($argv[3]);
	include_once dirname(__DIR__)."/common.php";
	error_reporting(E_ALL);
	
	$accounts = M('Account')->getAccountNameByPlatformId(11);
	if (!in_array($account, $accounts)){
		exit("{$argv[1]} is wrong!\n");
	}
	$starttime	= date(DATE_ATOM, get_ebay_timestamp($nowtime-(60*$minute)));
	$endtime	= date(DATE_ATOM, get_ebay_timestamp($nowtime));
	
	echo "\n<<<<=====[".date('Y-m-d H:i:s', $nowtime)."]系统【开始】同步账号【 $account 】订单 ====>>>>\n";
	echo "\n\t-------同步订单范围From: {$starttime} To {$endtime} -------\n";
	
	$amazonbutt = A('AmazonButt');
	$amazonbutt->setToken($account, $site);
	$spiderlists = $amazonbutt->spiderOrderLists($starttime, $endtime);
	var_dump($spiderlists);
	
	if(empty($spiderlists)){
		die('not found any order.');
	}
	//插入订单
	A('OrderAdd')->act_insertOrder($spiderlists);
	var_dump(A('OrderAdd')->act_getErrorMsg(), M('OrderAdd')->getAllRunSql());
	echo "\n\t------- [耗时:".ceil((time()-$nowtime)/60)."分钟] -------\n";
	echo "\n<<<<=====[".date('Y-m-d H:i:s')."]系统【结束】同步账号【 $account 】订单=====>>>>\n";
	exit;
?>