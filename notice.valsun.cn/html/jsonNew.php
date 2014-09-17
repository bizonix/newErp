<?php
	error_reporting(E_ALL);
	header("Content-type: text/html; charset=utf-8");
	date_default_timezone_set('Asia/Shanghai');
	include "../framework.php";
	Core::getInstance();

	$mod	=	isset($_REQUEST['mod']) ? $_REQUEST['mod']: "";
	$act	=	isset($_REQUEST['act']) ? $_REQUEST['act']: "";

	if(empty($mod)) {
		echo "empty mod";
		exit;
	}
	if(empty($act)) {
		echo "empty act";
		exit;
	}

	$modName	=	ucfirst($mod."Act");
	$modClass	=	new $modName();

	$actName	=	"act_".$act;
	if(method_exists($modClass, $actName)) {
		$ret	=	$modClass->$actName();
	} else {
		echo "no this act!!";
		exit;
	}

	$callback	=	isset($_REQUEST['callback']) ? $_REQUEST['callback']: "";
	$jsonp		=	isset($_REQUEST['jsonp']) ? $_REQUEST['jsonp']: "";

	echo "try{ ".$callback."(".json_encode($ret)."); }catch(e){alert(e);}";
?>
