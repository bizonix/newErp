<?php
error_reporting(-1);
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
require "/data/web/order.valsun.cn/framework.php";
Core::getInstance();
$tableName = "om_unshipped_order";
/*$db_config	=	C("DB_CONFIG");
$dbConn		=	new mysql();
$dbConn->connect($db_config["master1"][0],$db_config["master1"][1],$db_config["master1"][2],'');
$dbConn->select_db($db_config["master1"][4]);*/
//echo ' [x] ', json_decode($msg->body), "\n";
//var_dump($msg->body); echo "\n";
//$where = ' WHERE orderStatus = 100 and orderType in(101,110) and is_delete = 0 and storeId = 1 ';
$where = ' WHERE orderAddTime <= 1393171200 and is_delete = 0 and storeId = 1 ';
$orderNum = OrderindexModel::showOrderNum($tableName, $where);
echo $orderNum; echo "\n";

$page = 1;
$perpage = 100;
$totalpage = ceil($orderNum/$perpage);

while ($page<=$totalpage){
	echo "总共{$totalpage}页---现在是第{$page}页\n";
	//$start_num  = ($page-1)*$perpage;
	$start_num  = 0;
	$limit = " ORDER BY id LIMIT {$start_num}, {$perpage}";
	if(OrderindexModel::shiftOrderList($where.$limit)){
		echo "转移成功\n";
	}else{
		echo "转移失败\n";
	}
	$page++;
}
?>