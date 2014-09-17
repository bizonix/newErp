<?php
/* *****************************************************
 * 脚本回复message到ebay服务器
 * 该脚本会一直运行 当脚本将msg_reply表中的数据读空以后 会睡眠3分钟
 * 脚本工作过程:
 * 手续查找队列中trytime为0的记录 并处理该记录.如果trytimes为0的记录全部
 * 处理完成以后，则查找trytimes小于20次的记录 如果执行成功 则继续下一个处理，
 * 如果执行失败 ,则脚本睡眠10分钟，然后再执行. 若都没有 则睡眠3分钟
 * *****************************************************
 */
date_default_timezone_set('Asia/Shanghai');
// error_reporting(0);
include_once __DIR__.'/../framework.php';                               // 加载框架
Core::getInstance();                                                    // 初始化框架对象
include_once WEB_PATH . 'crontab/scriptcommon.php';                     //脚本公共文件
require_once WEB_PATH . 'lib/global_ebay_accounts.php';                 // 加载账号信息
require_once WEB_PATH . 'lib/ebaylibrary/GetMemberMessages.php';        //订单抓取脚本
require_once WEB_PATH . 'lib/xmlhandle.php';                            //xml处理脚本
require_once WEB_PATH . 'lib/ebay_order_cron_func.php';                 //公用处理函数

$remsgque_obj = new replyMessageQueueModel();
while (TRUE){                                                           //程序永久运行 Ctrl-C 退出
    $retype = 0;                //当前处理记录类型 0表示trytimes为0的记录 1 表示trytimes大于1的记录
    echo formatetime()."---processing... at code line--".__LINE__."\n";
    $dbConn->begin();                                                   //启动事务
    /* *************************************************
     * 实现方式:
     * 每次从msg_replyqueue中取出一条数据并在该行数据上加上`排他锁`<必须在
     * innodb引擎上实现>,以 防止其他进程读取该行数据。当消息回复成功以后会把该
     * 行数据删除掉,这样以保证每条数据只回复一次而不回复多次
     * *************************************************
     */
    
    /*----- 取trytimes为0的记录 -----*/
    $sql = "select * from msg_replyqueue where trytimes=0 order by id desc limit 1 for update";
    $row = $dbConn->fetch_first($sql);
    
    if (empty($row)) {
        /*----- 取trytimes小于20的记录 -----*/
        $sql = "select * from msg_replyqueue where trytimes<20 order by id limit 1 for update";   	
        $row = $dbConn->fetch_first($sql);
        
        if (empty($row)) {
            /*----- 亦没有trytimes大于20的记录 则  -----*/
        	commitQuery();                             //提交事务
        	echo formatetime()."---sleeping... 3 mins  at code line--".__LINE__."\n";    //提示正在睡眠中
        	sleep(10);                                //睡眠3分钟
        	continue;                                  //跳出当前循环
        } else {
            $retype = 1;
        }
    }//print_r($row);exit;
    $remsg_obj = new replyMessageQueueModel();      //队列处理对象
    $msg_obj = new messageModel();                  //message处理对象
    $rm_obj = new ReplyMessageModel();              //处理发送的model
    
    $infor = unserialize($row['parameter']);        // 反序列化其他扩展信息
    
    /*----- 根据账号来加载账号信息 -----*/
    $ebayaccount = $row['account'];                 //所属账号
    $token_file = WEB_PATH . "lib/ebaylibrary/keys/keys_" . $ebayaccount . ".php";
    if (! file_exists($token_file)) {
        echo  formatetime().'---'.$token_file . " does not exists!!! at code line--".__LINE__."\n"; // 密码文件不存在
        $remsgque_obj->delAQueueRecords($row['id']);                    //数据不对直接删除
        $msg_obj->updateMessageStatus(array($row['messageid']), 0);     //重置message为0的状态
        commitQuery();
        continue;                                                       // 跳出当前循环
    }
    include ''.$token_file;
    /*----- 根据账号来加载账号信息 -----*/
    $msgid  = $row['messageid'];                            //对应的message表的主键id
    
    if ($row['replytype'] == 1) {                           //带有回复内容的message回复
        $content        = $row['retext'];                   //回复内容
        $copytosender   = $infor['iscopy'];                 // 是否抄送 改值只能为0、1
        
        /*----- 发送请求 -----*/
        $result         = $rm_obj->replyMessage($msgid, $content, $copytosender);   //回复 并返回结果
        /*----- 发送请求 -----*/
        
        if ($result == TRUE) {                              //执行成功则标记为已经读取
        	$rm_obj->markAsRead($msgid, 'Read');
        }
    } else {                                                //指标及为回复状态 没有回复内容的
        /*----- 发送请求 -----*/
        $result = $rm_obj->markAsRead($msgid, 'Read');
        /*----- 发送请求 -----*/
    }
    
    if ($result == TRUE) {                                   // 发送成功 删除该回复记录
        $remsgque_obj->delAQueueRecords($row['id']);
        $status = 2;                                         // 默认为正常回复
        if ($row['replytype']==2) {                          //为标记回复
        	$status  = 3;
        }
        $msg_obj->updateMessageStatus(array($msgid), 2 );        //将message状态改为回复成功
        echo 'success!'."\n";
        commitQuery();
    } else { // 发送失败
        //echo 8;exit;
        echo formatetime().'---'.__LINE__.'---'.__FILE__.'---'.ReplyMessageModel::$errMsg."\n";
        $remsgque_obj->plusCountById($row['id']);               // 将失败次数加一
        if ($row['trytimes'] == 19) {
            $msg_obj->updateMessageStatus(array($msgid), 4);    //将message状态改为回复失败
        }
        
        commitQuery();
        if ($retype === 1) {                                    //表示当前处理的是$trytimes大于0的记录 则需睡眠10 分钟
            echo formatetime()."sleep 10 mins at code line--".__LINE__."\n";
            sleep(600);
        }
    }
}

/*
 * 提交查询
 */
function commitQuery(){
    global $dbConn;
    $dbConn->commit();                  //提交事务
    $dbConn->query('SET AUTOCOMMIT=1'); //改回自动提交sql
}

/*
 * 日志记录
 * $file 记录的日志
 * $msg  日志内容
 */
function writelog($file, $msg){
    $fp = fopen($file, 'a');
    fwrite($fp, $msg);
}

/*
 * 格式化当前时间
 */
function formatetime(){
    return date('Y-m-d H:i:s', time());
}