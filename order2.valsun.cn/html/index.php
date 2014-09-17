<?php
session_start();
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
include_once dirname(__DIR__)."/conf/define.php";
include_once WEB_PATH."framework.php";
Core::getInstance();

$mod	=	isset($_REQUEST['mod']) ? $_REQUEST['mod']: "";
$act	=	isset($_REQUEST['act']) ? $_REQUEST['act']: "";

error_reporting(-1);

if(empty($mod)){
	redirect_to(WEB_URL."index.php?mod=public&act=login"); // 跳转到登陆页
	exit;
}
if(empty($act)){
	redirect_to(WEB_URL."index.php?mod=public&act=login");
	exit;
}

//初始化memcache类
$memc_obj 	= new Cache(C('CACHEGROUP'));
$modName	= ucfirst($mod."View");
$modClass	= new $modName();
$actName	= "view_".$act;
if(method_exists($modClass, $actName)){
	$ret	=	$modClass->$actName();
}else{
	echo "no this act!!";
}
?>