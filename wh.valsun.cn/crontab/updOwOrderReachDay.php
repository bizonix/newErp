<?php
/**
 * 每天更新一次海外备货清单离美国仓库上架天数，每天减一天，直到为0天
 */
//date_default_timezone_set('Asia/Shanghai');
include "../framework.php";
if(!class_exists('Core')){  //Core类不存在，重新载入文件
    $web_path   =   str_replace('crontab', '', __DIR__); //获取framework.php所在路径
    include_once $web_path.'framework.php';
}
Core::getInstance();
global $dbConn;
echo date('Y-m-d H:i:s')."\n";
mysql_query('SET NAMES UTF8');
$sql        = 'SELECT id, arriveday FROM wh_preplenshOrder WHERE arriveday != 0';
$query      = $dbConn->query($sql);
$rtn        = $dbConn->fetch_array_all($query);
if(!empty($rtn)){
    foreach($rtn as $k => $v){
        $id         = $v['id'];
        $arriveday  = $v['arriveday'];
        if($arriveday > 0){
            $newArriveDay   = $arriveday - 1;
            $upd            = "UPDATE wh_preplenshOrder SET arriveday = '{$newArriveDay}' WHERE id = '{$id}'";
            echo $upd."\n";
            $dbConn->query($upd);
        }
    }
}
?>