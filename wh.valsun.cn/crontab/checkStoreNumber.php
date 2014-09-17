<?php
/**
 * 每2小时检测新系统入库记录与老ERP系统是否同步，不同步则发送邮件
 */
error_reporting(1);
session_start();
set_time_limit(0);
ini_set('memory_limit','256M');
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
//$path   =   __DIR__;
//$path   =   str_replace(array("\\", 'crontab'), array('/', ''), $path);
//include $path.'framework.php';

if(!class_exists('Core')){  //Core类不存在，重新载入文件
    $web_path   =   str_replace(array("\\", 'crontab'), array('/', ''), __DIR__); //获取framework.php所在路径
    include_once $web_path.'framework.php';
}

Core::getInstance();
global $dbConn;
$log_file       =   'checkStoreNumber/'.date('YmdHis').'.txt';
$to     =   '闫应门,席慧超';
$from   =   '任达海';
$type   =   'email';
$date   =   date('Y-m-d H:i:s');
//$time           =   strtotime('-2 hours');  //2小时之内的入库记录

//$where          =   "where ioType = 2 and createdTime >= $time";
$flag   =   FALSE;
$i      =   0;
do{
    $old_res     =   CommonModel::getSkuIoRecord();  //获取老ERP入库记录
    if($old_res['res_code'] == 200){
        $flag   =   TRUE;
    }
    $i++;
}while($flag == FALSE && $i<20);

if($flag == FALSE){  //获取老ERP入库记录失败则发送邮件通知
    /**发送邮件通知**/
    $title  =   '未获取老ERP入库信息';
    $content=   '时间:'.date('Y-m-d H:i:s');
    CommonModel::sendMessage($type, $from, $to, $content, $title);
    $log_info      = sprintf("时间：%s,错误信息:%s \r\n", $date, $title);
    write_log($log_file, $log_info);
    exit;
}
$old_res    =   $old_res['data'];
if(empty($old_res)){
    echo '老ERP无入库记录!';
    $log_info      = sprintf("时间：%s,错误信息:%s \r\n", $date, '老ERP无入库记录');
    write_log($log_file, $log_info);
    exit;
}

mysql_query('SET NAMES UTF8');
//$where          =   "where ioType = 2 order by id desc limit 100 ";
//$query          =   'select sku, amount, userId from wh_iorecords '.$where;
$time           =   strtotime('-2 hours');  //2小时之内的入库记录
$where          =   "where ioType = 2 and createdTime >= $time";
$query          =   'select sku from wh_iorecords '.$where;
$query          =   $dbConn->query($query);
$new_res        =   $dbConn->fetch_array_all($query); //新系统入库记录

$sku_arr        =   array();  //新旧库存所有sku集合

foreach($new_res as $key=>$val){
    $sku_arr[]      =   $val['sku'];
    $new_res[$key]  =   $val['sku'];
}

foreach($old_res as $key=>$val){
    $sku_arr[]      =   $val['in_sku'];
    $old_res[$key]  =   $val['in_sku'];
}

$sku_arr        =   array_unique($sku_arr);

//分别获取sku在新旧系统中的入库记录数
$new_key_count  =   array_count_values($new_res);  
$old_key_count  =   array_count_values($old_res);

$diff_sku       =   array();

foreach($sku_arr as $sku){      //循环总料号集合并判断该料号在新旧系统入库记录中记录数是否一致 并记录不一致的sku
    $new_counts =   $new_key_count[$sku];
    $old_counts =   $old_key_count[$sku];
    $msg        =   '记录一致!';
    if($new_counts != $old_counts){
        $diff_sku[] = $sku;
        $msg        =   '记录不一致!';
    }
    $log_info      = sprintf("时间：%s,错误信息:%s \r\n", $date, $msg);
    write_log($log_file, $log_info);
}
if(!empty($diff_sku)){
    /**发送邮件通知**/
    $title  =   '新旧系统入库记录不一致';
    $content=   '料号信息：'.implode(',', $diff_sku);
    CommonModel::sendMessage($type, $from, $to, $content, $title);
}
echo '处理完毕!';
//print_r($old_sku);exit;
//var_dump(array_diff($new_sku, $old_sku));exit;



//print_r($new_res);
//print_r($old_res);
//if(!empty($res)){
//    foreach($res as $val){
//        $userName   =   getUserNameById($val['userId']);
//        
//        $info       =   CommonModel::getSkuIoRecord($val['sku'], $val['amount'], $userName, $val['createdTime']);
//        $date       =   date('Y-m-d H:i:s', $val['createdTime']);
//        if($info['res_code'] != 200){
//            $content=   "料号：{$val['sku']}, 数量：{$val['amount']}, 时间:{$date} ,旧ERP未同步";
//            /**发送邮件通知**/
//            $to     =   '闫应门,席慧超';
//            $from   =   '任达海';
//            $type   =   'email';
//            $title  =   '料号未同步测试';
//            $info   =   CommonModel::sendMessage($type, $from, $to, $content, $title);
//
//        }else{
//            $content=   "料号：{$val['sku']}, 数量：{$val['amount']}, 时间:{$date} ,已同步";
//        }
//        write_log($log_file, $content."\r\n");
//    }
//}
?>