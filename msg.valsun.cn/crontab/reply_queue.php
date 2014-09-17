<?php
set_time_limit(0);

/*消息队列相关*/
//define('AMQP_DEBUG',  true);
define('__DIR__', '/');
include_once __DIR__.'/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;
define('P_CONNECT', TRUE);						//长连接
/*消息队列相关*/

/*----- 框架相关 -----*/
date_default_timezone_set('Asia/Shanghai');
include_once __DIR__.'/../framework.php';                               // 加载框架
Core::getInstance();                                                    // 初始化框架对象
include_once WEB_PATH . 'crontab/scriptcommon.php';                     // 脚本公共文件
require_once WEB_PATH . 'lib/global_ebay_accounts.php';                 // 加载账号信息
require_once WEB_PATH . 'lib/ebaylibrary/GetMemberMessages.php';        // 订单抓取脚本
require_once WEB_PATH . 'lib/xmlhandle.php';                            // xml处理脚本
require_once WEB_PATH . 'lib/opensys_functions.php';
require_once WEB_PATH . 'lib/ebay_order_cron_func.php';                 // 公用处理函数
/*----- 框架相关 -----*/

$remsg_obj      = new replyMessageQueueModel();         //队列处理对象
$msg_obj        = new messageModel();                   //message处理对象
$rm_obj         = new ReplyMessageModel();              //处理发送的model
$remsgque_obj   = new replyMessageQueueModel();

echo "-------------- start ".date('Y-m-d H:i:s', time())."----------------\n";

$exchange = 'message_info_exchange';                    //交换机名
$queue = 'rabbitmq_message_info_queue';                 //队列名
$consumer_tag = 'consumer'. getmypid ();

$conn = new AMQPConnection('127.0.0.1', 5672, 'valsun_msg', '125963', 'valsun_message');
// $conn = new AMQPConnection('192.168.200.222', 5672, 'xiaojinhua', 'jinhua', '/');
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
    if($dbalive !== TRUE){      //数据库连接失效 重连
        echo "reconnecting DB ! \n";
    }
    global $remsg_obj, $msg_obj, $rm_obj, $remsgque_obj, $dbConn; 
    $message_body = json_decode($msg->body, TRUE);
    if ($message_body == FALSE || !is_array($message_body) || !isset($message_body['id'])) {
    	echo 'invalid message ! --- '.$msg->body."\n";
    	return ;
    }
    $id           = $message_body['id'];
    $sql = "select * from msg_replyqueue where id=$id  limit 1";
    $row = $dbConn->fetch_first($sql);
    
    if (empty($row)) {                              //没找到信息 直接return
        $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);             //删除队列信息
        return;
    }
    
    $infor = unserialize($row['parameter']);        // 反序列化其他扩展信息
    
    /*----- 根据账号来加载账号信息 -----*/
    $ebayaccount = $row['account'];                 //所属账号
    $token_file = WEB_PATH . "lib/ebaylibrary/keys/keys_" . $ebayaccount . ".php";
    if (! file_exists($token_file)) {
        echo  formatetime().'---'.$token_file . " does not exists!!! at code line--".__LINE__."\n"; // 密码文件不存在
        $remsgque_obj->delAQueueRecords($row['id']);                                                //数据不对直接删除
        $msg_obj->updateMessageStatus(array($row['messageid']), 0);                                 //重置message为0的状态
        $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);             //删除队列信息
        return;                                                                                     // 退出
    }
    include ''.$token_file;
    /*----- 导出为全局变量 ugly code -----*/
    $GLOBALS['siteID']              = $siteID;
    $GLOBALS['production']          = $production;
    $GLOBALS['compatabilityLevel']  = $compatabilityLevel;
    $GLOBALS['devID']               = $devID;
    $GLOBALS['appID']               = $appID;
    $GLOBALS['certID']              = $certID;
    $GLOBALS['serverUrl']           = $serverUrl;
    $GLOBALS['userToken']           = $userToken;
    /*----- 根据账号来加载账号信息 -----*/
    $msgid  = $row['messageid'];                                                            //对应的message表的主键id
    
    if ($row['replytype'] == 1) {                                                           //带有回复内容的message回复
        $content        = $row['retext'];                                                   //回复内容
        $copytosender   = $infor['iscopy'];                                                 // 是否抄送 改值只能为0、1
    
        /*----- 推送回复到线上 -----*/
        $result         = $rm_obj->replyMessage($msgid, $content, $copytosender);           //回复 并返回结果
    
        if ($result == TRUE) {                                                              //执行成功则标记为已经读取
            $result = $rm_obj->markAsRead($msgid, 'Read');
            if (!$result) {  
            	echo ReplyMessageModel::$errMsg." --- 回邮件发送成功, 标记已读失败!\n";
            }
        }
    } else {                                                                                //只标及为回复状态 没有回复内容的
        /*----- 发送请求 -----*/
        $result = $rm_obj->markAsRead($msgid, 'Read');
    }
    
    if ($result == TRUE) {                                                                  // 发送成功 删除该回复队列记录
        $remsgque_obj->delAQueueRecords($row['id']);
        $status = 2;                                                                        // 默认为正常回复
        if ($row['replytype']==2) {                                                         //为标记回复
            $status  = 3;
        }
        $msg_obj->updateMessageStatus(array($msgid), $status );                             //将message状态改为回复成功
        echo 'success!  '.date('Y:m:d H:i:s', time())."\n";
    } else {                                                                                // 发送失败
        echo date('Y-m-d H:i:s', time()).'---'.__LINE__.'---'.__FILE__.'---'.ReplyMessageModel::$errMsg."\n";
        $errcontent    = ReplyMessageModel::$sendMsg;
        if (ReplyMessageModel::$sender != NULL) {
            $usrmod_obj = new GetLoacalUserModel();
            $userinfo   = $usrmod_obj->getUserInfoBySysId(ReplyMessageModel::$sender);
            if (!empty($userinfo)) {
                /*发送邮件*/
                $result_sys = getOpenSysApi('notice.send.message', array('content'=>$errcontent, 'from'=>'tuxinglong',
                        'to'=>$userinfo['global_user_login_name'], 'type'=>'email'));
            }
        }
        
        $remsgque_obj->plusCountById($row['id']);                      // 将失败次数加一
    }
    
    //确认收到
    $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']); //确认收到

    // Send a message with the string "quit" to cancel the consumer.
    if ($msg->body === 'quit') {
        $msg->delivery_info['channel']->basic_cancel($msg->delivery_info['consumer_tag']);
    }
}

function shutdown($ch, $conn){
//    $ch->close();
 //   $conn->close();
}
