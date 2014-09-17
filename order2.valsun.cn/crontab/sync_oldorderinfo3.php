<?php
error_reporting(-1);
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
require "/data/web/order.valsun.cn/framework.php";
Core::getInstance();
$sql = "select * from om_status_menu where groupId != 0 and is_delete = 0 ";
$query	= $dbConn->query($sql);
$om_status_menu =   $dbConn->fetch_array_all($query);
//var_dump($om_status_menu); echo "\n"; exit;
$array = array();
foreach($om_status_menu as $status_menu){
	$statusCode = $status_menu['statusCode'];
	if(!in_array($statusCode, array(C("STATESHIPPED"),C("STATESHIPPED_PRINTPEND"),C("STATESHIPPED_PRINTED"),C("STATESHIPPED_BEPICKING"),C("STATESHIPPED_PENDREVIEW"),C("STATESHIPPED_BEPACKAGED"),C("STATESHIPPED_BEWEIGHED"),C("STATEHASSHIPPED"),C("STATEHASSHIPPED_CONV")))){
		//$username = $userinfo['username'];
		foreach($om_status_menu as $value){
			if(!in_array($value['statusCode'], array(C("STATESHIPPED"),C("STATESHIPPED_PRINTPEND"),C("STATESHIPPED_PRINTED"),C("STATESHIPPED_BEPICKING"),C("STATESHIPPED_PENDREVIEW"),C("STATESHIPPED_BEPACKAGED"),C("STATESHIPPED_BEWEIGHED"),C("STATEHASSHIPPED"),C("STATEHASSHIPPED_CONV")))){
				$array[$statusCode][] = $value['statusCode'];
			}
		}
	}
}
//var_dump($array); exit;
//$json_array = json_encode($array);
if($array){
	$data = array();
	$data['visible_movefolder'] = json_encode($array);
	$string = array2sql_extral($data);
	$sql = "UPDATE om_userCompetence SET {$string} ";
	$dbConn->query($sql);
}
exit;
?>