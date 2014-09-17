<?php
error_reporting(0);
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
include "../framework.php";
Core::getInstance();
error_reporting(0);
$mod	=	isset($_REQUEST['mod']) ? $_REQUEST['mod']: "";
$act	=	isset($_REQUEST['act']) ? $_REQUEST['act']: "";
//$token	=	trim($_REQUEST['token']);

if(empty($mod)){
	echo "empty mod";
	exit;
}

if(empty($act)){
	echo "empty act";
	exit;
}
//初始化memcache类
$memc_obj = new Cache(C('CACHEGROUP'));
$modName	=	ucfirst($mod."Act");
$modClass	=	new $modName();

$actName	=	"act_".$act;
if(method_exists($modClass, $actName)){
	$ret	=	$modClass->$actName();
}else{
	echo "no this act!!";
	exit;
}

$callback	=	isset($_REQUEST['callback']) ? $_REQUEST['callback']: "";
$jsonp		=	isset($_REQUEST['jsonp']) ? $_REQUEST['jsonp']: "";

$dat	=	array();
if(empty($ret)){
	$dat	=	array("errCode"=>$modName::$errCode, "errMsg"=>$modName::$errMsg, "data"=>"");
}else{
	$dat	=	array("errCode"=>$modName::$errCode, "errMsg"=>$modName::$errMsg, "data"=>$ret);
}

if(!empty($callback)){
	if(!empty($jsonp)){
		echo "try{ ".$callback."(".json_encode($dat)."); }catch(){alert(e);}";
	}else{
		echo $callback."(".json_encode($dat).");";
	}

}else{
	if(!empty($jsonp)){
		echo json_encode($dat);
	}else{
		echo $dat;
	}
}
?>