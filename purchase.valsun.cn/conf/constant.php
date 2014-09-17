<?php
if (!defined('WEB_PATH')) exit();

//日志及debug信息记录配置

return  array(
    'LOG_RECORD'	=>true,													// 开启日志记录
    'LOG_EXCEPTION_RECORD'  => true,										// 是否记录异常信息日志
    'LOG_LEVEL'		=>   'EMERG,ALERT,CRIT,ERR,WARN,NOTIC,INFO,DEBUG,SQL'	// 允许记录的日志级别
);

//rabbitmq  配置
define('MQ_EXCHANGE', 'purchase_info_exchange');                     //消息队列交换机
//define('MQ_VHOST', 'process_message');                              //消息队列虚拟机
define('MQ_VHOST', 'valsun_purchase');                              //消息队列虚拟机
define('MQ_QUEUE', 'rabbitmq_purchase_info_queue');                  //消息队列队列名
define('MQ_USER', 'purchase');                                    //消息队列队列用户
define('MQ_PSW', 'purchase%123');                                         //消息队列队列密码
define('MQ_SERVER', '127.0.0.1');                                   //消息队列服务器地址
?>
