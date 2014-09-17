<?php
error_reporting(E_ALL);
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
include "../framework.php";
Core::getInstance();

$mod	=	isset($_REQUEST['mod']) ? $_REQUEST['mod']: "";
$act	=	isset($_REQUEST['act']) ? $_REQUEST['act']: "";
$mod    =   trim($mod);
$act    =   trim($act);

if(!isset($_SESSION)){
	@session_start();
}

$username 	=	$_SESSION['username'];
$userid 	=	$_SESSION['userId'];

if($username || $userid) {
	if($mod == "" || $act == "") {
		if(empty($_SERVER['HTTP_REFERER'])) {
			$url = 'index.php?mod=public&act=login';
		} else {
			$url = $_SERVER['HTTP_REFERER'];
		}
		header("location:{$url}");
		exit;
	}
} else {
	//无参访问且没有登入的情况下
    if($mod == "" || $act == "") {
        header("location:index.php?mod=public&act=login");
        exit;
    }
}

if(empty($mod)) {
	echo "empty mod";
	exit;
}
if(empty($act)) {
	echo "empty act";
	exit;
}

$modName	=	ucfirst($mod."View");
$modClass	=	new $modName();
$actName	=	"view_".$act;
if(method_exists($modClass, $actName)) {
	$ret	=	$modClass->$actName();
} else {
	echo "no this act!!";
}
?>