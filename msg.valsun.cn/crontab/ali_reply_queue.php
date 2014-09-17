<?php
set_time_limit(0);

/*消息队列相关*/
// define('AMQP_DEBUG',  true);
define('__DIR__', '/');
define('ALI_PAGESIZE', 50);                                                             //速卖通抓取页面大小
define('P_CONNECT', TRUE);                                                             //长连接

include_once __DIR__.'/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;
/*消息队列相关*/
define('ALIREPLYERR', '/home/weblog/msg.valsun.cn/alilog/');                    //速卖通错误处日志存放路径
define('KEY_PATH', 'lib/ali_keys/');                 //token文件目录
/*----- 框架相关 -----*/
date_default_timezone_set('Asia/Shanghai');
include_once __DIR__.'/../framework.php';                                               // 加载框架
Core::getInstance();                                                                    // 初始化框架对象
/*----- 框架相关 -----*/
include_once WEB_PATH.'lib/AliMessage.class.php';

echo "-------------- start ----------------\n";

$exchange = 'message_ali_info_exchange';                                                //交换机名
$queue = 'rabbitmq_ali_message_info_queue';                                             //队列名
$consumer_tag = 'consumer'. getmypid ();

$conn = new AMQPConnection('127.0.0.1', 5672, 'valsun_msg', '125963', 'valsun_message');
//$conn = new AMQPConnection('192.168.200.222', 5672, 'xiaojinhua', 'jinhua', '/');
$ch = $conn->channel();
/*
    name: $queue    // should be unique in fanout exchange.
    passive: false  // don't check if a queue with the same name exists
    durable: false // the queue will not survive server restarts
    exclusive: false // the queue might be accessed by other channels
    auto_delete: true //the queue will be deleted once the channel is closed.
*/
$messageCount = $ch->queue_declare($queue, false, false, false, false);
/*
    name: $queue    // should be unique in fanout exchange.
    passive: false  // don't check if a queue with the same name exists
    durable: false // the queue will not survive server restarts
    exclusive: false // the queue might be accessed by other channels
    auto_delete: true //the queue will be deleted once the channel is closed.
*/
$ch->queue_declare($queue, false, false, false, false);
$ch->queue_bind($queue, $exchange);
$ch->basic_consume($queue, $consumer_tag, false, false, false, false, 'process_message');

$aliexpress     = new AliMessage();
$alrequeue      = new AliReplyQueueModel();
$aliorder_obj   = new AliOderMessageModel();
$msg_obj        = new messageModel();

register_shutdown_function('shutdown', $ch, $conn);

while (count($ch->callbacks)) {
    $ch->wait();
}

/*
 * 处理消息
 * 消息结构 array('id'=?) id为队列表里面的主键id
 */
function process_message($msg){
    $dbalive = mysql_ping();
    if($dbalive !== TRUE){      //数据库连接失效 
        echo "mysql time out ! \n";exit;
        return ;
    }
    global $dbConn, $aliexpress, $alrequeue, $aliorder_obj, $msg_obj;
    $message_body = json_decode($msg->body, TRUE);
    if ($message_body == FALSE || !is_array($message_body) || !isset($message_body['id'])) {
    	echo 'invalid message ! --- '.$msg->body."\n";                                                          //错误的消息结果 
    	$msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);                         //确认收到
    	return ;
    }
    $id         = $message_body['id'];                                                                          //队列主键id
    $queueinfo  = $alrequeue->getQueueRow($id);
    if (empty($queueinfo)){                                                                                     //队列id错误
        writeLog(ALIREPLYERR, '队列id不存在!--- ID:'.$id);
        //确认收到
        $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);                         //确认收到
    	return ;
    }
    if ($queueinfo['retype'] == 1) {                                                                            //处理速卖通订单留言
        $rowinfo    = $aliorder_obj->getMessageInfoByMessageId($queueinfo['msgid']);
        if (empty($rowinfo)) {                                                                                  //没找到对应的message信息
            writeLog(ALIREPLYERR, '没找到message信息 订单留言---MSGID:'.$queueinfo['msgid']);
            //确认收到
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);                      //确认收到
            echo date('Y-m-d H:i:s', time()).'---'.__LINE__." message not found ! \n";
            return ;
        }
        //加载token信息
        $configFile = WEB_PATH.KEY_PATH."config_{$rowinfo[receiverid]}.php";
//          echo $configFile;"\n";
        if (file_exists($configFile)){
            include $configFile;
        }else{
            echo date('Y-m-d H:i:s', time()).'---'.__LINE__."key file was not found !\n";
            writeLog(ALIREPLYERR, '找不到token文件'.$configFile.__FILE__.'--'.__LINE__);
            //确认收到
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);                      //确认收到
            return ;
        }

        $aliexpress->setConfig($appKey,$appSecret,$refresh_token);
        $aliexpress->doInit(); 
        // print_r($rowinfo);exit;
        $replyresult    = $aliexpress->replyOrderMessage($rowinfo['orderid'], $rowinfo['responsecontent']);
        if($replyresult === FALSE){                                                                              //返回失败
            updateQueue(AliMessage::$errMsg, $queueinfo['id']);                                                  //记录错误信息到表中
            $msg_obj->updateMessageStatus_aliorder(array($rowinfo['id']), 3);                                    //标记为失败
            //确认收到
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);                      //确认收到
            echo date('Y-m-d H:i:s', time()).'---'."failure !\n";
            return ;
        } else {
            deleteQueue($queueinfo['id']);                                                                       //删除队列行
            $msg_obj->updateMessageStatus_aliorder(array($rowinfo['id']), 2);                                    //标记为成功
            //确认收到
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);                      //确认收到
            echo date('Y-m-d H:i:s', time()).'---'."success !\n";
            return ;
        }
    } else {                                                                                                     //处理速卖通站内信
        $rowinfo    = $aliorder_obj->getMessageInfoByMessageId_site($queueinfo['msgid']);
        if (empty($rowinfo)) {                                                                                  //没找到对应的message信息
            writeLog(ALIREPLYERR, '没找到message信息 站内信---MSGID:'.$queueinfo['msgid']);
            //确认收到
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);                      //确认收到
            echo date('Y-m-d H:i:s', time()).'---'.'message not found!',"\n";
            return ;
        }
        // print_r($rowinfo);exit;
        //加载token信息
        $configFile = WEB_PATH.KEY_PATH."config_{$rowinfo[receiverid]}.php";
        if (file_exists($configFile)){
            include $configFile;
        }else{
            echo __LINE__."key file was not found ! --- $configFile \n";
            writeLog(ALIREPLYERR, '找不到token文件'.$configFile.__FILE__.'--'.__LINE__);
            //确认收到
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);                      //确认收到
            echo date('Y-m-d H:i:s', time()).'---'.__LINE__." message not found ! \n";
            return ;
        }
        $aliexpress->setConfig($appKey,$appSecret,$refresh_token);
        $aliexpress->doInit(); 
        $replyresult    = $aliexpress->replySiteMessage($rowinfo['senderid'], $rowinfo['replyconten']);
        if($replyresult === FALSE){                                                                              //返回失败
            updateQueue(AliMessage::$errMsg, $queueinfo['id']);                                                  //记录错误信息到表中
            $msg_obj->updateMessageStatus_alisite(array($rowinfo['id']), 3);                                    //标记为失败
            //确认收到
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);                      //确认收到
            echo "failure!\n";
            return ;
        } else {
            deleteQueue($queueinfo['id']);                                                                       //删除队列行
            $msg_obj->updateMessageStatus_alisite(array($rowinfo['id']), 2);                                    //标记为成功
            //确认收到
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);                      //确认收到
            echo date('Y-m-d H:i:s', time())."success!\n";
            return ;
        }
    }

    // Send a message with the string "quit" to cancel the consumer.
    if ($msg->body === 'quit') {
        $msg->delivery_info['channel']->basic_cancel($msg->delivery_info['consumer_tag']);
    }
}

function shutdown($ch, $conn){
//    $ch->close();
 //   $conn->close();
}

/*
 * 将错误信息记录到表中
 */
function updateQueue($content, $id){
    global $dbConn;
    $content    = mysql_real_escape_string($content);
    $sql        = "update msg_alimsgqueue set failurecontent='$content', trytimes=trytimes+1 where id=$id";
    $dbConn->query($sql);
}

/*
 * 删除一条队列行
 */
function deleteQueue($id) {
    global $dbConn;
	$sql   = 'delete from msg_alimsgqueue where id='.$id;
    return $dbConn->query($sql);
}
