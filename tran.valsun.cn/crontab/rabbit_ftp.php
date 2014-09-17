<?php
/*************************************
* PHP amqp(RabbitMQ) Demo - consumer
* Author: Linvo
* Date: 2012/7/30
*************************************/
//配置信息
require_once 'ftpclass.php';
$FTP_HOST='115.29.188.246';//服务器
$FTP_USER='guanyongjun';//用户名
$FTP_PASS='guanyongjunlkp[p23';//密码
$FTP_PORT='21';//端口
$conn_args = array(
    'host' => '192.168.200.246',
    'port' => '5672',
    'login' => 'valsun_tran',
    'password' => 'tranabc',
    'vhost'=>'valsun_tran'
);
$e_name = 'tran_pic_exchange'; //交换机名
$q_name = 'tran_pic_queue'; //队列名
//$k_route = 'key_1'; //路由key

//创建连接和channel
$conn = new AMQPConnection($conn_args);
if (!$conn->connect()) {
    die("Cannot connect to the broker!\n");
}
$channel = new AMQPChannel($conn);

//创建交换机
$ex = new AMQPExchange($channel);
$ex->setName($e_name);
$ex->setType(AMQP_EX_TYPE_DIRECT); //direct类型
$ex->setFlags(AMQP_DURABLE); //持久化
echo "Exchange Status:".$ex->declare()."\n";

//创建队列
$q = new AMQPQueue($channel);
$q->setName($q_name);
$q->setFlags(AMQP_DURABLE); //持久化
echo "Message Total:".$q->declare()."\n";

//绑定交换机与队列，并指定路由键
echo 'Queue Bind: '.$q->bind($e_name, $k_route)."\n";

//阻塞模式接收消息
echo "Message:\n";
while(True){
    $q->consume('processMessage');
    //$q->consume('processMessage', AMQP_AUTOACK); //自动ACK应答
}
$conn->disconnect();

/**
* 消费回调函数
* 处理消息
*/
function processMessage($envelope, $queue) {
    
    $pic_url = $envelope->getBody();
    echo $pic_url."\n"; //处理消息
    $queue->ack($envelope->getDeliveryTag()); //手动发送ACK应答
    $ftpup = new ftp();
    $ftpup->__construct();
    $ftpup->up_file();       
}

