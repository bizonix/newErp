<?php
/*
 * 订单抓取脚本
 */

// error_reporting(0);
include_once __DIR__.'/../framework.php'; // 加载框架
Core::getInstance(); // 初始化框架对象
include_once WEB_PATH . 'crontab/scriptcommon.php';  //脚本公共文件
require_once WEB_PATH . 'lib/global_ebay_accounts.php'; // 加载账号信息
require_once WEB_PATH . 'lib/ebaylibrary/GetMemberMessages.php';//订单抓取脚本
require_once WEB_PATH . 'lib/xmlhandle.php';    //xml处理脚本
require_once WEB_PATH . 'lib/ebay_order_cron_func.php';    //公用处理函数

/*----- 传入参数处理  -------*/
if ($argc != 4) {   //参数个数不少于三个
    exit("Put eBayaccount $argv[0] eBayaccount startTime");
}

$ebayaccount = trim($argv[1]);
$startTime   = trim($argv[2]);

$limit       = $argv[3];
if (! preg_match('#^[\da-zA-Z]+$#i', $ebayaccount)) {
    exit("Invaild ebay account:$ebayaccount");
}
if (! preg_match('#^[\d]+$#i', $startTime)) {
    exit("Invaild Time:$startTime");
}
if (! in_array($ebayaccount, $GLOBAL_EBAY_ACCOUNT)) {
    exit("$ebayaccount is not support now !");
}
/*----- 传入参数处理  -------*/

$token_file = WEB_PATH . "lib/ebaylibrary/keys/keys_" . $ebayaccount . ".php";
if (! file_exists($token_file)) {
    exit($token_file . " does not exists!!!"); // 密码文件不存在
}
include_once ''.$token_file;

date_default_timezone_set("UTC");       //时区设置为标准时间

$currenttime = date("Y-m-d H:i:s");     //当前时间

/*----- 避开早上八点到九点的高峰期  -----*/
$startTime1 = date('Y-m-d 08:20:00');
$endTime1 = date('Y-m-d 09:10:00');
$nowTime1 = date('Y-m-d H:i:s', strtotime("$currenttime + 480 minutes"));
/* if ($nowTime1 > $startTime1 && $nowTime1 < $endTime1) {
    exit('此时间段不执行');
} */
/*----- 避开早上八点到九点的高峰期   -----*/

$start = date('Y-m-d H:i:s', strtotime("$currenttime - $startTime minutes"));
$end = date('Y-m-d', strtotime("$currenttime +0 days")) . 'T' . date('H:i:s', strtotime($currenttime));

echo " $start --- $end" . "\n";

$fe_obj = new FetchModel();
$fe_obj->GetMemberMessages($start, $end, $ebayaccount, $type = 1, $limit);      //抓取脚本
echo $ebayaccount . ' Success'."\n";
exit();
?>