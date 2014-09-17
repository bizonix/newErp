<?php
/**
 * 更新采购系统待上架数量
 */
error_reporting(1);
session_start();
set_time_limit(0);
ini_set('memory_limit','256M');
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
//include "../framework.php";

if(!class_exists('Core')){  //Core类不存在，重新载入文件
    $web_path   =   str_replace('crontab', '', __DIR__); //获取framework.php所在路径
    include_once $web_path.'framework.php';
}

Core::getInstance();

$where          =   "where tallyStatus=0 and entryStatus=0 and is_delete = 0 group by sku order by id desc";
$query          =   'select sku, sum(num) ichibanNums, sum(shelvesNums) shelvesNums from wh_tallying_list '.$where;

mysql_query('SET NAMES UTF8');
$query          =   $dbConn->query($query);
$res            =   $dbConn->fetch_array_all($query); //点货记录中的sku集合


$log_file       =   'updatePurchaseWaitNum/'.date('Y-m-d').'.txt';
$date           =   date('Y-m-d H:i:s');
foreach($res as $val){
    $ichibanNums=   intval($val['ichibanNums']);
    $shelvesNums=   intval($val['shelvesNums']);
    $wait_shelf =   intval($ichibanNums-$shelvesNums);
    $sku        =   $val['sku'];
    //var_dump($val);exit;
    /** 释放采购订单更新等待上架数量**/
    $info       =   CommonModel::checkOnWaySkuNum($sku, $wait_shelf, 3);
    $msg        =   $info == 0 ? '更新等待上架数量成功!' : '更新等待上架数量失败!';
    
    $log_info         =   sprintf("料号：%s, 时间：%s,信息:%s,返回值：%s, 参数：%s, %s \r\n", $sku, $date, $msg,
                                        is_array($info) ? json_decode($info) : $info, $ichibanNums, $shelvesNums);
    write_log($log_file, $log_info);
}
?>