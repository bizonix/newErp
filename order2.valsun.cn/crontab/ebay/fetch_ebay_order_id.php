<?php
//脚本参数检验
if($argc!=3){
	exit("Usage: /usr/bin/php	{$argv[0]} eBayAccount minute_range \n");
}

$loop		= true;
$page		= 1;
$nowtime 	= time();
$account	= trim($argv[1]);
$minute		= intval($argv[2]);

include_once dirname(__DIR__)."/common.php";
$accounts = M('Account')->getAccountNameByPlatformId(1);
if (!in_array($account, $accounts)){
	exit("{$argv[1]} is wrong!\n");
}
if ($minute==0){
	exit("{$argv[2]} is minute, Must be greater than 0!\n");
}

################################## start 这里可以扩展时间分页  ##################################
$ebay_start	= date('Y-m-d\TH:i:s', get_ebay_timestamp($nowtime-(60*$minute)));
$ebay_end	= date('Y-m-d\TH:i:s', get_ebay_timestamp($nowtime));

echo "\n<<<<=====[".date('Y-m-d H:i:s', $nowtime)."]系统【开始】同步账号【 $account 】订单 ====>>>>\n";
echo "\n\t-------同步订单范围From: {$ebay_start} To {$ebay_end} -------\n";

$ebaybutt = A('EbayButt');
$ebaybutt->setToken($account);
$spiderlists = $ebaybutt->spiderOrderId($ebay_start, $ebay_end);

//实例化队列
$rabbitMQ = E('RabbitMQ');
$rabbitMQ->connection('fetchorder');
$exchange = "ORDER_spiderOrderId_{$account}";
foreach ($spiderlists AS $spiderlist){
	if (M('Order')->checkEbayOrderidExists($spiderlist['ebay_orderid'])){
		echo "\n\t-------订单抓取号【{$spiderlist['ebay_orderid']}】已经存在 -------\n"; 
		continue;
	}
	echo "\n\t-------订单抓取号【{$spiderlist['ebay_orderid']}】抓取成功,并推送队列 -------\n"; 
	$rabbitMQ->basicPublish($exchange, $spiderlist['ebay_orderid']);
	M('OrderAdd')->insertSpiderOrderId($spiderlist);
}
echo "\n<====[".date('Y-m-d H:i:s')."]系统【结束】同步账号【 $account 】订单, 本次共处理".count($spiderlists)."条数据\n";
################################## end 这里可以扩展时间分页  ##################################
?>