<?php
error_reporting(-1);
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
require "/data/web/order.valsun.cn/framework.php";
Core::getInstance();

require_once WEB_PATH . 'lib/rabbitmq/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPConnection('112.124.41.121', 5672, 'valsun_warehouse','warehouse%123','valsun_warehouse');

$exchange_name = 'wh_status_exchange';
$queue_name = 'dev_rabbitmq_getwhstatus_queue';
//$connection = new AMQPConnection('192.168.128.129', 5672, 'peter', 'peter','mq_vhost1');//5672是端口号，两个peter分别是用户名和密码

$channel = $connection->channel();
//$channel->exchange_declare('power', 'topic', false, false, false);
//第三个参数 true 会检测交换器是否存在 ，第4个参数 true 表示 服务器重启时，交换器依然不会消失，第5个参数false 表示 如果交换器删掉，消息通道依然生效
$channel->exchange_declare($exchange_name, 'fanout', false, false, false);

//list($queue_name, ,) = 
$channel->queue_declare($queue_name, false, false, false, false);

$channel->queue_bind($queue_name, $exchange_name);

//echo ' [*] Waiting for logs. To exit press CTRL+C', "\n";

$callback = function($msg){
	$tableName = "om_unshipped_order";
	$db_config	=	C("DB_CONFIG");
	$dbConn		=	new mysql();
	$dbConn->connect($db_config["master1"][0],$db_config["master1"][1],$db_config["master1"][2],'');
	$dbConn->select_db($db_config["master1"][4]);
  	//echo ' [x] ', json_decode($msg->body), "\n";
	//var_dump($msg->body); echo "\n";
	$data = json_decode($msg->body, true);
	//var_dump($data); echo "\n";
	$orderid = $data['originOrderId'];
	$orderStatus = $data['orderStatus'];
	$operateUserId = $data['operateUserId'];
	$operateTime = $data['operateTime'];
	$actualWeight = $data['actualWeight'];
	$tracknumber = trim($data['tracknumber']);
	$orderids = CombinePackageModel::selectAllRecordByOrderId($orderid);
	var_dump($orderid."-------".$orderStatus); echo "\n";
	if(C($orderStatus) == 2){//已发货状态
		$where = " WHERE id in (".join(',',$orderids).") AND orderStatus = ".C('STATESHIPPED');
		$returnStatus0 = array('orderStatus'=>C("STATESHIPPED"),'orderType'=>C('STATEHASSHIPPED_CONV'));
			if(OrderindexModel::updateOrder($tableName,$returnStatus0,$where)){
				echo "\n=====同步的订单{$orderid}状态成功======\n";
			}
			$msg = CommonModel::updateWarehouseInfo($orderid,2,$operateUserId,$operateTime,$actualWeight);
			if($msg){
				$where = ' WHERE id in ('.join(",",$orderids).') AND orderStatus= '.C('STATESHIPPED').' AND orderType= '.C('STATEHASSHIPPED_CONV').' AND is_delete = 0 ';
				
				if(OrderindexModel::shiftOrderList($where)){
					echo "转移成功\n";
				}else{
					echo "转移失败\n";
				}
			}
	}else{
		$where = " WHERE id in (".join(',',$orderids).") AND orderStatus = ".C('STATESHIPPED');
		$returnStatus0 = array('orderType'=>C($orderStatus));
		if(OrderindexModel::updateOrder($tableName,$returnStatus0,$where)){
			echo "\n=====同步的订单{$orderid}状态成功======\n";
		}
		$msg = commonModel::updateWarehouseInfo($orderid,C($orderStatus),$operateUserId,$operateTime);
		if($tracknumber){
			$updateArr = array('omOrderId'=>$orderid,'tracknumber'=>$tracknumber,'createdTime'=>time());
			$msg = OrderAddModel::insertOrderTrackRow($updateArr);
		}
	}
};
$channel->basic_consume($queue_name, '', false, true, false, false, $callback);

while(count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();
//$dbConn->close();
?>
