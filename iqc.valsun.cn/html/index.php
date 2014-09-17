<?php
error_reporting(E_ALL);
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
include "../framework.php";
Core::getInstance();
session_start();
$mod	=	isset($_REQUEST['mod']) ? $_REQUEST['mod']: "login";
$act	=	isset($_REQUEST['act']) ? $_REQUEST['act']: "index";

if(empty($mod)){
	echo "empty mod";
	exit;
}

if(empty($act)){
	echo "empty act";
	exit;
}

//未登陆拦截
if(empty($_SESSION['userId'])&&$mod!=='login'){
    header('location:index.php?mod=login&act=index');exit;
}

//初始化memcache类
$memc_obj = new Cache(C('CACHEGROUP'));

//模板目录常量
define('TEMPLATEPATH', WEB_PATH.'html/v1/');

$modName	=	ucfirst($mod."View");

$modClass	=	new $modName();


$actName	=	"view_".$act;
if(method_exists($modClass, $actName)){
	$ret	=	$modClass->$actName();
}else{
	echo "no this act!!";
}
?>