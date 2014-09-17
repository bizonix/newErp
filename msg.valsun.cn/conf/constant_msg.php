<?php
/*
 * message系统的常量
 */
define('MSGBODYSAVEPATH', '/msgbodyhome/');                         //message消息内容存放目录
//define('MSGREALPREFIX', WEB_PATH.'crontab');                        //当前系统的真实存放路径
define('MSGREALPREFIX', '/data/web/ebay_html_files');                        //当前系统的真实存放路径


define('OPENURL', 'http://gw.open.valsun.cn:88/router/rest?');      //开发系统message
define('OPENTOKEN', '12aead0936276c4d8bbe32947b9e94b3');            //token
define('OPENGETORDER', 'order.getOrderInfoByUserId');               //从订单系统获取买家购买记录
define('OPENGETCARRIER', 'trans.track.info.get');                   //从订单系统获取买家购买记录
define('OPENGETAMAZONORDER', 'order.getBuyerAndSellerByOrderNumberAndEmail');    //从订单系统获取买家和卖家
define('OPENGETOVERSEAORDER','msg.getOverseaOrderInfo');            //获取海外仓订单部分信息
define('PLATFORM_EBAY', 1);                                         //EBAY平台id
define('PLATFORM_ALIBABA', 2);                                      //速卖通平台id

define('MQ_EXCHANGE', 'message_info_exchange');                     //消息队列交换机
//define('MQ_VHOST', 'process_message');                              //消息队列虚拟机
define('MQ_VHOST', 'valsun_message');                              //消息队列虚拟机
define('MQ_QUEUE', 'rabbitmq_message_info_queue');                  //消息队列队列名
define('MQ_USER', 'valsun_msg');                                    //消息队列队列用户
define('MQ_PSW', '125963');                                         //消息队列队列密码
define('MQ_SERVER', '127.0.0.1');                                   //消息队列服务器地址


define('MQ_EXCHANGE_AMAZON', 'message_amazon_exchange');            //消息队列Amazon交换机
define('MQ_QUEUE_AMAZON', 'rabbitmq_message_amazon_queue');         //消息队列Amazon队列名