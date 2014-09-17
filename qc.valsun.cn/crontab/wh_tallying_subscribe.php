<?php
error_reporting(-1);
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
require "/data/web/qc.valsun.cn/framework.php";
Core::getInstance();

require_once WEB_PATH . 'lib/rabbitmq/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPConnection('112.124.41.121', 5672, 'valsun_warehouse','warehouse%123','valsun_warehouse');
//$connection = new AMQPConnection('192.168.200.222', 5672, 'xiaojinhua','jinhua','/');
$queue_name = 'wh_tallyingList';
$exchange_name='send_tallying_list';
$consumer_tag = 'consumer_qc';

$channel = $connection->channel();
//第三个参数 true 会检测交换器是否存在 ，第4个参数 true 表示 服务器重启时，交换器依然不会消失，第5个参数false 表示 如果交换器删掉，消息通道依然生效
//$channel->exchange_declare($exchange_name, 'fanout', false, true, false);
$channel->queue_declare($queue_name, false, true, false, false);
$channel->queue_bind($queue_name, $exchange_name);
//echo '======='.date('Y-m-d H:i:s', time()).'======接收到 '.$messageCount[1].' 条数据！'."\n";
echo ' [*] Waiting for logs. To exit press CTRL+C', "\n";

$callback = function($msg){
	global $dbConn;
	//echo "=============="; echo "\n";
  	//echo ' [x] ', json_decode($msg->body), "\n";
	$arr = json_decode($msg->body,true);
	//var_dump($arr);
    if(empty($arr)){
		
	}elseif($arr == 'quit'){
		echo "最后一条数据为退出命令！\n";
		$msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
		break;
	}else{
		$qc_arr = array();
		foreach($arr as $key=>$value){
			$qc_arr['sku'] 			= $value['sku'];
			$qc_arr['skuCode']  	= $value['googsCode'];
			$qc_arr['location'] 	= "无";
			$qc_arr['goodsName'] 	= $value['googsName'];
			$qc_arr['printBatch'] 	= $value['batchNum'];
			$qc_arr['num'] 			= $value['num'];
			$qc_arr['printerId'] 	= $value['printerId'];
			$qc_arr['printTime'] 	= $value['printTime'];
			$qc_arr['purchaseId'] 	= $value['purchaseId'];
			$qc_arr['sellerId'] 	= 1;//$value['companyId'];
			$qc_arr['addTime']      = time();
			//echo "##########################\n";
			if(strlen($value['batchNum']) < 11){
				echo $qc_arr['sku']."批次号".$value['batchNum']." 长度小于11"; echo "\n";
				continue;
			}
			//检测数据库中是否存在此批次号
			$sql = "SELECT * FROM qc_sample_info WHERE printBatch = '{$value['batchNum']}' and is_delete = 0 ";
			$query = $dbConn->query($sql);
			$infoNum = $dbConn->num_rows($query);
			if($infoNum > 0){
				echo "{$value['batchNum']} 重复\n";
				continue;
			}
			
			$sql = "INSERT INTO qc_sample_info set ".array2sql($qc_arr);
			//echo $sql; echo "\n";
			$con_stat = mysql_ping();
			$query	= $dbConn->query($sql);
			if (!$query) {
				echo ' [error] ', $con_stat,'---', $sql, "\n";
			} else {
				echo ' [ok] ', $con_stat,'---', $sql, "\n";
			}
		}
		$msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
	}
};

$channel->basic_consume($queue_name, $consumer_tag, false, false, false, false, $callback);

while(count($channel->callbacks)) {
    $channel->wait();
}



$channel->close();
$connection->close();
exit;