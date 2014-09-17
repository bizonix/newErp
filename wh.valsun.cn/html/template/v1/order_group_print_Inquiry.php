<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>配货清单查询</title>
</head>

<body>

<?php
require_once "../../../framework.php";
Core::getInstance();
global $dbConn;
$type = $_GET['type'];
$order_groups = array();
$time = strtotime(date('Y-m-d'));
if($type==0){
	$sql = "select * from wh_shipping_order_group_print where groupPrintTime>'$time'";
}else{
	$sql = "select * from wh_shipping_order_group_print where groupPrintTime>'$time' and status=0";
}
$sql= $dbConn->query($sql);
$sql= $dbConn->fetch_array_all($sql);
$total_count = count($sql);
$info = ($type==0)? "今天打印的配货清单数量为" : "今天配货清单未打印地址条的数量为";
echo $info."<span style='color:red'>".$total_count."</span>笔,清单号分别是：<br/>";
if(!empty($sql)){
	foreach($sql as $g_info){
		echo $g_info['shipOrderGroup']."<br />";
	}
}
?>
</body>
</html>
