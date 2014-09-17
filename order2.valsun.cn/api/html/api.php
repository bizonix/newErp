<?php
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
include "../../framework.php";
Core::getInstance();
//初始化缓存，memcache类
$memc_obj = new Cache(C('CACHEGROUP'));

$act = isset($_GET['action']) ? $_GET['action']: "";
$v 	 = isset($_GET['v']) ? $_GET['v']: "1.0";
if(empty($act)){
	json_return(10111);
}

if (preg_match("/^[a-z0-9_]*$/i", $act)==0){
	json_return(10112, '', $act);
}

if (preg_match("/^[\.0-9_]*$/i", $v)==0){
	json_return(10116, '', $v);
}

$data = MC("SELECT * FROM ".C('DB_PREFIX')."interface_version WHERE requestname='{$act}' AND version='{$v}' AND is_delete=0", 0);
if (!isset($data[0]['is_disable'])){
	json_return(10114, '', $act, $v);
}
if ($data[0]['is_disable']==1){
	json_return(10115, '', $act, $v);
}
//对接口请求内容进行验证或转换
$transform = !empty($data[0]['extend_transform']) ? $data[0]['extend_transform'] : 'Transform:commonTransform';
list($vclass, $vfun) = explode(':', $transform);
$vmethod = ucfirst($vclass."Act");
$vfun   = 'act_'.$vfun;
if (!class_exists($vmethod)){
	json_return(10117);
}
if (!method_exists($vmethod, $vfun)){
	json_return(10117);
}
$vreturn = A($vclass)->$vfun();
if ($vreturn===false){
	json_return(A($vclass)->act_getErrorMsg());
}
//加载实际执行函数
list($class, $fun) = explode(':', $data[0]['rule']);
$method = ucfirst($class."Act");
$fun   = 'act_'.$fun;
if (!class_exists($method)){
	json_return(10117);
}
if (!method_exists($method, $fun)){
	json_return(10117);
}

if ($vreturn===true){
	$ret = A($class)->$fun();
}else if(is_string($vreturn)){
	$ret = A($class)->$fun($vreturn);
}else if (is_array($vreturn)){
	$vars = array();
	foreach ($vreturn AS $k=>$vr){
		$vars[] = "\$vreturn[{$k}]";
	}
	$varstr = implode(',', $vars);
	eval("\$ret = A('{$class}')->{$fun}({$varstr});");
}else{
	$ret = A($class)->$fun();
}
if ($_GET['debug']==1){
	echo "<!-- \n\t\t".implode("\n\t\t", M($class)->getAllRunSql())."\n\t -->\n";
}
if (empty($ret)){
	$errmsg = A($class)->act_getErrorMsg();
	if (!empty($errmsg)){
		json_return($errmsg);
	}
}
//对返回数据进行封装
$package = !empty($data[0]['extend_package']) ? $data[0]['extend_package'] : 'Package:commonPackage';
list($pclass, $pfun) = explode(':', $package);
$pmethod = ucfirst($pclass."Act");
$pfun   = 'act_'.$pfun;
if (!class_exists($pmethod)){
	json_return(10117);
}
if (!method_exists($pmethod, $pfun)){
	json_return(10117);
}
$ret = A($pclass)->$pfun($ret);

$callback	=	isset($_GET['callback']) ? $_GET['callback'] : "";
$jsonp		=	isset($_GET['jsonp']) ? $_GET['jsonp']: "";

$data = array("errCode"=>10113, "errMsg"=>A($class)->act_getErrorMsg(), "status"=>true, "data"=>$ret);
if(!empty($callback)){
	if(!empty($jsonp)){
		echo "try{ ".$callback."(".json_encode($data)."); }catch(){alert(e);}";
	}else{
		echo $callback."(".json_encode($data).");";
	}
}else{
	echo json_encode($data);
}
exit;
?>