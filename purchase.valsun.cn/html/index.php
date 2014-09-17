<?php
error_reporting(0);
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
include "../framework.php";

Core::getInstance();

$mod	=	isset($_REQUEST['mod']) ? $_REQUEST['mod']: "";
$act	=	isset($_REQUEST['act']) ? $_REQUEST['act']: "";
if(empty($mod)){
	//echo "empty mod";
	//redirect_to(WEB_URL."index.php?mod=public&act=login"); // 跳转到登陆页
	redirect_to("index.php?mod=public&act=login"); // 跳转到登陆页
	exit;
}
if(empty($act)){
	//redirect_to(WEB_URL."index.php?mod=public&act=login");
	redirect_to("index.php?mod=public&act=login");
	exit;
}

//初始化memcache类
//$memc_obj = new Cache(C('CACHEGROUP'));

$modName	=	ucfirst($mod."View");
$modClass	=	new $modName();
$actName	=	"view_".$act;
if(method_exists($modClass, $actName)){
	$ret	=	$modClass->$actName();
}else{
	echo "no this act!!";
}
echo 'test123';
?>
