<?php
	//脚本参数检验
	if($argc<4){
		exit("Usage: /usr/bin/php	$argv[0] eBayAccount detailname siteid\n");
	}
	$nowtime = time();
	$account	= trim($argv[1]);
	$detail 	= trim($argv[2]);
	$siteid 	= intval($argv[3]);
	include_once dirname(__DIR__)."/common.php";
	$accounts = M('Account')->getAccountNameByPlatformId(1);
	if (!in_array($account, $accounts)){
		exit("{$argv[1]} is wrong!\n");
	}
	echo "\n<<<<=====[".date('Y-m-d H:i:s', $nowtime)."]系统【开始】同步账号【 $account 】订单 =====>>>>\n";
	$ebaybutt = A('EbayButt');
	$ebaybutt->setToken($account);
	$ebaybutt->setSiteId($siteid);
	var_dump($ebaybutt->geteBayDetails($detail));
	echo "\n\t------- [耗时:".ceil((time()-$nowtime)/60)."分钟] -------\n";
	echo "\n<<<<=====[".date('Y-m-d H:i:s')."]系统【结束】同步账号【 $account 】订单=====>>>>\n";
?>