<?php
error_reporting(E_ALL);
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
require "/data/web/qc.valsun.cn/framework.php";
Core::getInstance();
$now = time();
$weekago = $now-7*24*3600;
$where = " where printTime < {$weekago}  and (getUserId is null OR getUserId = 0) AND is_delete=0";
$records = OmAvailableModel::getTNameList("qc_sample_info","*",$where);
if($records){
	echo date('Y-m-d H:i:s', $now)."共有".count($records)."条记录需要删除！\n";
	$info = OmAvailableModel::updateTNameRow("qc_sample_info","set is_delete=1",$where);
	if($info){
		echo "删除成功!\n";
	}else{
		echo "删除失败!\n";
	}
}else{
	echo date('Y-m-d H:i:s', $now)."没有超过一周的无用记录!\n";
}

?>