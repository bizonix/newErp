<?php
	//脚本参数检验
	if($argc<2){
		exit("Usage: /usr/bin/php	$argv[0] eBayAccount \n");
	}
	$nowtime 	= time();
	$account	= trim($argv[1]);
	include_once dirname(__DIR__)."/common.php";
	$accounts = M('Account')->getAccountNameByPlatformId(1);
	if (!in_array($account, $accounts)){
		exit("{$argv[1]} is wrong!\n");
	}
	//实例化消息队列
	$rabbitMQ = E('RabbitMQ');
	$rabbitMQ->connection('fetchorder');
	$exchange = "ORDER_spiderOrderId_{$account}";
	$queue = 'queue_orderid';
	$orderids = $rabbitMQ->queueSubscribeLimit($exchange, $queue, 30, false);
	//$orderids = array('131177444249-972658505003', "141347519608-999214136004"); //for test

	echo "\n<<<<=====[".date('Y-m-d H:i:s', $nowtime)."]系统【开始】同步账号【 $account 】订单 =====>>>>\n";
	if (count($orderids)>0){
		echo "\n\t------- 同步订单为【".implode(',', $orderids)."】 -------\n";
		$ebaybutt = A('EbayButt');
		$ebaybutt->setToken($account);
		$spiderlists = $ebaybutt->spiderOrderLists($orderids);
		A('OrderAdd')->act_insertOrder($spiderlists);
	}else {
		echo "\n\t------- 同步订单为空，消息队列暂无消息  -------\n";
	}
	var_dump(A('OrderAdd')->act_getErrorMsg());
	var_dump(M('OrderAdd')->getAllRunSql());
	echo "\n\t------- [耗时:".ceil((time()-$nowtime)/60)."分钟] -------\n";
	echo "\n<<<<=====[".date('Y-m-d H:i:s')."]系统【结束】同步账号【 $account 】订单=====>>>>\n";
	exit;
	
?>