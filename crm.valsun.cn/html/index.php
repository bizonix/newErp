<?php
error_reporting(E_ALL);
session_start();
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
include "../framework.php";
Core::getInstance();

$mod	=	isset($_REQUEST['mod']) ? $_REQUEST['mod']: "login";
$act	=	isset($_REQUEST['act']) ? $_REQUEST['act']: "login";

// if(empty($mod)){
	// echo "empty mod";
	// exit;
// }

// if(empty($act)){
	// echo "empty act";
	// exit;
// }


if(empty($mod)){
	//echo "empty mod";
	redirect_to(WEB_URL."index.php?mod=login&act=login"); // 跳转到登陆页
	exit;
}
if(empty($act)){
	redirect_to(WEB_URL."index.php?mod=login&act=login");
	exit;
}

//未登陆拦截
// if(empty($_SESSION['userId'])&&$mod!=='login'){
    // header('location:index.php?mod=login&act=login');exit;
// }
//初始化memcache类
$memc_obj = new Cache(C('CACHEGROUP'));

$modName	=	ucfirst($mod."View");

$modClass	=	new $modName();

$actName	=	"view_".$act;
if(method_exists($modClass, $actName)){
	$ret	=	$modClass->$actName();
}else{
	echo "no this act!!";
}
?>