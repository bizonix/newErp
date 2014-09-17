<?php
error_reporting(E_ALL);
include_once __DIR__.'/../framework.php'; // 加载框架                  //加载框架信息
Core::getInstance();
date_default_timezone_set('Asia/Shanghai');         //设置为中国时区
define('KEY_PATH', 'lib/ali_keys/');                 //token文件目录
ini_set('max_execution_time', 1800);                //最大执行时间
include_once WEB_PATH."lib/AliMessage.class.php";   //抓取类
//include_once "conf\common.php";                     //公用配置文件
define('ALI_LOGPATH', '/home/weblog/msg.valsun.cn/');        //速卖通日志目录
//define('ALI_LOGPATH', 'c:/php/');        //速卖通日志目录
define('ALI_PAGESIZE', 50);                         //页面大小
$_SERVER['REQUEST_URI']    = 'fetch_ali_message.php';

$time   = intval($argv[2]);
if (empty($time)) {
	echo 'need time ! an integer should be given !', "\n";
	exit;
}
$endtime    = time();
$starttime      = $endtime  - 60*$time;             //每小时抓一次
//$starttime      = $endtime  - 3600;             //每小时抓一次

$ali_user	=	trim($argv[1]);                     //账号名称
if (empty($ali_user) ) {
	echo '参数不正确! 需要账号名称!', "\n";
	exit;
}
exit();
//加载token信息
$configFile = WEB_PATH.KEY_PATH."config_{$ali_user}.php";   //echo $configFile;exit;
if (file_exists($configFile)){
	include $configFile;
}else{
    echo "cannot found token file !\n";
	writeLog(ALI_LOGPATH, '找不到token文件'.$configFile.__FILE__.'--'.__LINE__);
	exit;
}

$aliexpress = new AliMessage();
$aliexpress->setConfig($appKey,$appSecret,$refresh_token);
$aliexpress->doInit();      

echo "开始同步订单留言\n";
date_default_timezone_set('America/Los_Angeles');		//洛杉矶时间
$starttime  = date('m/d/Y H:i:s', $starttime);
$endtime    = date('m/d/Y H:i:s',$endtime);
echo $starttime.' start -----'. $endtime.' end -----', "\n";
date_default_timezone_set('Asia/Shanghai');			//恢复为正常时间
$result = $aliexpress->getOrderMessage($starttime, $endtime);   //抓取订单留言
if ($result === FALSE) {
	writelog(ALI_LOGPATH, AliMessage::$errMsg);
	echo '抓取订单留言出错! ';
} else {
    echo 'success! ', date('Y-m-d H:i:s', time()), "\n";
}

echo "开始同步站内信\n";
$result = $aliexpress->getSiteMessage($starttime, $endtime);   //抓取站内信
if ($result === FALSE) {
    writelog(ALI_LOGPATH, AliMessage::$errMsg);
    echo '抓取站内信出错! ';
} else {
    echo 'site message success! ', date('Y-m-d H:i:s', time()), "\n";
}

echo 'done work ', date('Y-m-d H:i:s', time()), "\n";
