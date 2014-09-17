<?php
error_reporting(-1);
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
include "/data/web/tran.valsun.cn/framework.php";
Core::getInstance();
require_once WEB_PATH . '/lib/rabbitmq/autoload.php';

########################## 初始化配置 ##################
define('AMQP_DEBUG', false);
define('MQ_HOST', '115.29.188.246');
define('MQ_PORT', 5672);
define('MQ_USER', 'valsun_tran');
define('MQ_PASS', 'tranabc');
define('MQ_VHOST', 'valsun_tran');
########################################################
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;
$conn 			= new AMQPConnection(MQ_HOST, MQ_PORT, MQ_USER, MQ_PASS, MQ_VHOST);
$number  		= isset($argv[1]) ? $argv[1] : ''; //默认监听队列
$no_ack  		= isset($argv[2]) ? $argv[2] : true; //ack 默认false
$queue_name 	= 'tran_tracknum_queue'.$number;
$ch_name 		= 'tran_tracknum_exchange'.$number;
$consumer_tag 	= 'consumer_tracknum'.$number;
echo $queue_name,"=====",$ch_name,"=====",$consumer_tag,"\n";
$ch 			= $conn->channel();
$ch->exchange_declare($ch_name, 'direct', false, true, false); //声明交换机
$ch->queue_declare($queue_name, false, true, false, false); //声明队列名
$ch->queue_bind($queue_name, $ch_name);
echo ' [*] Waiting for logs. To exit press CTRL+C', "\n";

//处理信息
function process_message($msg){
	$res		= true;
	$data 		= $msg->body;
	$data 		= json_decode($data, true);
	if(!empty($data['ebay_tracknumber'])) {
		$res 	= consume_tracknum($data);
		// if($res) {
			// 成功发送ack信息
			// $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
		// }
	}	
    // Send a message with the string "quit" to cancel the consumer.
    if($msg->body === 'quit') {
        $msg->delivery_info['channel']->basic_cancel($msg->delivery_info['consumer_tag']);
    }
}

//确认消费
$ch->basic_consume($queue_name, $consumer_tag, false, $no_ack, false, false, 'process_message');

//关闭连接
function shutdown($ch, $conn){
    $ch->close();
    $conn->close();
}
register_shutdown_function('shutdown', $ch, $conn);

// Loop as long as the channel has callbacks registered
while (count($ch->callbacks)) {
    $read   = array($conn->getSocket()); // add here other sockets that you need to attend
    $write  = null;
    $except = null;
    if (false === ($num_changed_streams = stream_select($read, $write, $except, 60))) {
        /* Error handling */
		echo "超时了";
    } elseif ($num_changed_streams > 0) {
        $ch->wait();
    }
}

//处理跟踪号队列信息
function consume_tracknum($v){
	$type	= strtolower($v['ebay_carrier']);
	switch($type) {
		case "中国邮政挂号":
			$carrierId	= 2;
		break;
		case "香港小包挂号":
			$carrierId	= 4;
		break;
		case "ems":
			$carrierId	= 5;
		break;
		case "eub":
			$carrierId	= 6;
		break;
		case "dhl":
			$carrierId	= 8;
		break;
		case "fedex":
			$carrierId	= 9;
		break;
		case "global mail":
			$carrierId	= 10;
		break;
		case "ups ground":
			$carrierId	= 46;
		break;
		case "usps":
			$carrierId	= 47;
		break;
		case "顺丰快递":
			$carrierId	= 48;
		break;
		case "圆通快递":
			$carrierId	= 49;
		break;
		case "申通快递":
			$carrierId	= 50;
		break;
		case "韵达快递":
			$carrierId	= 51;
		break;
		case "新加坡小包挂号":
			$carrierId	= 52;
		break;
		case "德国邮政挂号":
			$carrierId	= 53;
		break;
		case "ups美国专线":
			$carrierId	= 62;
		break;
		case "ups英国专线":
			$carrierId	= 96;
		break;
		case "ups法国专线":
			$carrierId	= 97;
		break;
		case "ups德国专线":
			$carrierId	= 98;
		break;
		case "俄速通挂号":
			$carrierId	= 79;
		break;
		case "俄速通大包":
			$carrierId	= 81;
		break;
		case "飞腾dhl":
			$carrierId	= 59;
		break;
		case "自提":
			$carrierId	= 68;
		break;
		case "surepost":
			$carrierId	= 65;
		break;
		case "ups surepost":
			$carrierId	= 95;
		break;
		case "usps firstclass":
			$carrierId	= 91;
		break;
		case "ups ground commercia":
			$carrierId	= 92;
		break;
		case "俄速通平邮":
		case "香港小包平邮":
		case "新加坡dhl gm平邮":
		case "瑞士小包平邮":
		case "中国邮政平邮":
			$carrierId	= 61;
		break;
		case "新加坡dhl gm挂号":
			$carrierId	= 83;
		break;
		case "郑州小包挂号":
			$carrierId	= 86;
		break;
		case "瑞士小包挂号":
			$carrierId	= 88;
		break;
		case "比利时小包eu":
			$carrierId	= 89;
		break;
		case "澳邮宝挂号":
			$carrierId	= 93;
		break;
		default:
			$carrierId 	= 0;			
			print_r($v);
			return true;
	}
	if(empty($carrierId)) return false;
	$trackNumber	= $v['ebay_tracknumber'];
	if(empty($trackNumber)) $trackNumer	= 'WD'.str_pad($v['ebay_id'],9,"0",STR_PAD_LEFT).'CN';
	$timestr		= date('Y-m-d H:i:s');
	$trackNumber	= str_replace(array('CNEE','CNRB','SGEM'),array('CN,EE','CN,RB','SG,EM'), $v['ebay_tracknumber']);
	$numArr			= preg_split("/[和,\s]+/", $trackNumber);
	foreach($numArr as $val) {
		$flag			= TransOpenApiModel::checkTrackNumber($val, $carrierId);
		if(!$flag) {
			$res		= TransOpenApiModel::getCountriesStandardByName($v['ebay_countryname']);
			$countryId	= isset($res['id']) ? $res['id'] : 0;
			$data		= array(
							'trackNumber'	=> $val,
							'orderSn'		=> $v['ebay_id'],
							'weight'		=> $v['realWeight'],
							'cost'			=> $v['ordershipfee'],
							'carrierId'		=> $carrierId,
							'toCountry'		=> $v['ebay_countryname'],
							'countryId'		=> $countryId,
							'scanTime'		=> $v['scantime'],
							'recordId'		=> $v['recordnumber'],
							'platAccount'	=> $v['ebay_account'],
							'platForm'		=> $v['PlatForm'],
							'toCity'		=> $v['ebay_city'],
							'toUserId'		=> $v['ebay_userid'],
							'toUserEmail'	=> $v['ebay_usermail'],
							'toMarkTime'	=> $v['ShippedTime'],
							'fhTime'		=> $v['fhTime'],
						);
			$res		= TransOpenApiModel::addTrackNumber($data);
			if($res) {
				echo $res,"===",$v['ebay_id'],"===",$val,"===添加成功==={$timestr}\n";
			} else {
				echo $res,"===",$v['ebay_id'],"===",$val,"===添加失败==={$timestr}\n";
				echo "原因:[",TransOpenApiModel::$errMsg,"]\n";
			}
		} else {
			echo $v['ebay_id'],"===",$val,"===已添加==={$timestr}\n";
		}
	}
	return true;
}
exit;
?>