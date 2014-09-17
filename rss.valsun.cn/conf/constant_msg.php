<?php
/*
 * message系统的常量
 */
define('MSGBODYSAVEPATH', '/msgbodyhome/');                         //message消息内容存放目录
// define('MSGREALPREFIX', WEB_PATH.'crontab');                        //当前系统的真实存放路径
define('MSGREALPREFIX', 'c:/mytest');                        //当前系统的真实存放路径


define('OPENURL', 'http://gw.open.valsun.cn:88/router/rest?');      //开发系统message
define('OPENTOKEN', '12aead0936276c4d8bbe32947b9e94b3');            //token
define('OPENGETORDER', 'order.getOrderInfoByUserId');               //从订单系统获取买家购买记录
define('OPENGETCARRIER', 'trans.track.info.get');                   //从订单系统获取买家购买记录

define('PLATFORM_EBAY', 1);                                         //EBAY平台id
define('PLATFORM_ALIBABA', 2);                                      //速卖通平台id

define('MQ_EXCHANGE', 'message_info_exchange');                     //消息队列交换机
//define('MQ_VHOST', 'process_message');                            //消息队列虚拟机
define('MQ_VHOST', '/');                                            //消息队列虚拟机
define('MQ_QUEUE', 'rabbitmq_message_info_queue');                  //消息队列队列名
//define('MQ_USER', 'valsun_msg');                                    //消息队列队列用户
define('MQ_USER', 'xiaojinhua');                                    //消息队列队列用户
//define('MQ_PSW', 'msg$1028');                                         //消息队列队列密码
define('MQ_PSW', 'jinhua');                                         //消息队列队列密码
//define('MQ_SERVER', '127.0.0.1');                                   //消息队列服务器地址
define('MQ_SERVER', '192.168.200.222');                                   //消息队列服务器地址

define('MQ_EXCHANGEALI', 'message_ali_info_exchange');              //速卖通交换机
