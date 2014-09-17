<?php
//脚本参数检验
$nowtime 	= time();
include_once dirname(__DIR__)."/common.php";

$rabbitMQ = E('RabbitMQ');
$rabbitMQ->connection('fetchorder');
$exchange = "SEND_BIG_ORDER_2_PH";//推送超大订单给采购系统

echo "\n<<<<=====[".date('Y-m-d H:i:s', $nowtime)."]系统【开始】推送超大订单 ====>>>>\n";

$orderObj = M('order');
$omOrderId = 1265971;
$orderAuditList = $orderObj->getOrderAuditListById($omOrderId);
$count = count($orderAuditList);
echo "\n<====[".date('Y-m-d H:i:s')."]系统共有 $count 个订单信息要发送\n";

$orderData = array();
$orderData['omOrderId'] = $orderAuditList[0]['omOrderId'];
$orderData['omOrderdetailId'] = $orderAuditList[0]['omOrderdetailId'];
$orderData['sku'] = $orderAuditList[0]['sku'];
$orderData['amount'] = $orderAuditList[0]['amount'];
$message = json_encode($orderData);
$rabbitMQ->basicPublish($exchange, $message);
echo "[send]--$message \n\n";
echo "\n<====[".date('Y-m-d H:i:s')."]系统【结束】推送订单, 本次共发送 $count 条数据\n";
################################## end 这里可以扩展时间分页  ##################################
?>