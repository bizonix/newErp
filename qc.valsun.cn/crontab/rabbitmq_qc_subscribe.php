<?php
error_reporting(-1);
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
require "/data/web/qc.valsun.cn/framework.php";
Core::getInstance();

require_once WEB_PATH.'lib/rabbitmq/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;

$exchange = 'qc_detect_info';
$queue_name = 'rabbitmq_qc_direct_info_queue';

$connection = new AMQPConnection('115.29.188.246', 5672, 'qc', 'qc%123','qc');

$channel = $connection->channel();
//第三个参数 true 会检测交换器是否存在 ，第4个参数 true 表示 服务器重启时，交换器依然不会消失，第5个参数false 表示 如果交换器删掉，消息通道依然生效
$channel->exchange_declare($exchange, 'fanout', false, false, false);
/*$messageCount = */$channel->queue_declare($queue_name, false, false, false, false);
$channel->queue_bind($queue_name, $exchange);
//echo '======='.date('Y-m-d H:i:s', time()).'======接收到 '.$messageCount[1].' 条数据！'."\n";
echo ' ['.date('Y-m-d H:i:s', time()).'] Waiting for logs. To exit press CTRL+C', "\n";

$callback = function($msg){
	// global $dbConn;
	//var_dump($dbConn);
	//常连接MYSQL
	$usermodel = new UserModel();
	$db_config	=	C("DB_CONFIG");
	$dbConn		=	new mysql();
	$dbConn->connect($db_config["master1"][0],$db_config["master1"][1],$db_config["master1"][2],'');
	$dbConn->select_db($db_config["master1"][4]);
	//$con_stat = $dbConn->ping();
	$msg_array = json_decode($msg->body,true);
	if(empty($msg_array)){
		
	}else if($msg_array == 'quit'){
		echo "最后一条数据为退出命令！\n";
		$channel->basic_ack($msg->delivery_info['delivery_tag']);
		break;
	}else{
		//var_dump($msg_array);
		/*unset($msg_array['count']);
		unset($msg_array['printuser']);
		unset($msg_array['reachtime']);*/
		$global_user_info = $usermodel->getGlobalUserId($msg_array['printerId']);
		//var_dump($global_user_info);
		$msg_array['printerId'] = !empty($global_user_info[0]['global_user_id']) ? $global_user_info[0]['global_user_id'] : 0;
		$global_user_info = $usermodel->getGlobalUserId($msg_array['purchaseId']);
		$msg_array['purchaseId'] = !empty($global_user_info[0]['global_user_id']) ? $global_user_info[0]['global_user_id'] : 0;
		//var_dump($global_user_info);
		$sql = "insert into qc_sample_info set ".array2sql($msg_array);
		echo $sql; echo "\n";
		$query	= $dbConn->query($sql);
		//$channel->basic_ack($msg->delivery_info['delivery_tag']);
		if (!$query) {
			Log::write($con_stat.'---'.$sql, Log::ERR);
			//Log::write($errorStr,Log::ERR)
		} else {
			$msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
			echo ' [ok] ', $con_stat,'---', $sql, "\n";
			//$channel->basic_ack($msg->delivery_info['delivery_tag']);
		}
		//$dbConn->close();
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