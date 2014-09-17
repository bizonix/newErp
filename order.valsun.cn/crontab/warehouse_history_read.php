<?php
error_reporting(E_ALL);
ini_set('max_execution_time', 1800);
if(!defined('WEB_PATH')){
	define("WEB_PATH","/data/web/order.valsun.cn/");
}
require_once WEB_PATH."crontab/scripts.comm.php";
require_once WEB_PATH_CONF_SCRIPTS."script.ebay.config.php";
/*include_once "/data/scripts/ebay_order_cron_job/ebay_order_cron_config.php";
include_once "/data/scripts/ebay_order_cron_job/function_purchase.php";*/

$mctime = time();
$starttime = $mctime-60*60;

/*$sql = "SELECT * FROM in_warehouse_history where in_time>{$starttime}";
$sql = $dbConn->query($sql);
$in_warehouse_history = $dbConn->fetch_array_all($sql);*/
$in_warehouse_history = WarehouseAPIModel::getInRecords();
//var_dump($in_warehouse_history); exit;
/*$sql0 = "SELECT * FROM out_warehouse_history where out_time>{$starttime}";
$sql0 = $dbConn->query($sql0);
$out_warehouse_history = $dbConn->fetch_array_all($sql0);*/
$out_warehouse_history = WarehouseAPIModel::getOutRecords();
//var_dump($out_warehouse_history); exit;
//$ss0 = "SELECT *FROM  warehouse_history_read SET starttime={$starttime},endtime={$mctime}";
//$ss0 = $dbcon->execute($ss0);
if(empty($in_warehouse_history)&&empty($in_warehouse_history)){
	echo "无出入库记录！";
	exit;
}
$stock = array();
if($in_warehouse_history){
	foreach($in_warehouse_history as $key=>$value){
		//$tt = "INSERT INTO adjust_sku_info_new set sku='{$value['in_sku']}',initvalue='' ";
		//$stock[$value['in_sku']] =  $value['in_amount'];
		if(array_key_exists($value['sku'],$stock)){
			$stock[$value['sku']] = $stock[$value['sku']]+$value['amount'];
		}else{
			$stock[$value['sku']] =  $value['amount'];
		}
	}
}
if($out_warehouse_history){
	foreach($out_warehouse_history as $key=>$value){
		if(array_key_exists($value['sku'],$stock)){
			$stock[$value['sku']] = $stock[$value['sku']]+$value['amount'];
		}else{
			$stock[$value['sku']] = $value['amount'];
		}
	}
}
/*foreach($stock as $key=>$value){
	if($value==0){
		continue;
	}
	$ss = "SELECT * FROM ebay_onhandle WHERE goods_sn='$key'";
	$ss = $dbcon->execute($ss);
	$ss = $dbcon->fetch_one($ss);
	if($ss['goods_count']>0){
		$now_status = '1';
	}else{
		$now_status = '0';
	}
	$last_value = $ss['goods_count']-$value;
	if($last_value>0){
		$last_status = '1';
	}else{
		$last_status = '0';
	}
	if($now_status != $last_status){
		$st = "INSERT INTO adjust_sku_info_new set sku='$key',initvalue='$last_status',sku_stock={$ss['goods_count']},adjustvalue='$now_status',type=1,creator='vipchen'";
		$st = $dbcon->execute($st);
		if(!$st){
			echo "料号'$key'执行失败！\n";
		}
	}
}*/
foreach($stock as $key=>$value){
	if($value==0){
		continue;
	}
	
	$sf = "SELECT * FROM pc_goods WHERE sku='{$key}' and goodsStatus in (2,3,4,5,6) and is_delete = 0 ";   //停售状态不管库存变化均不更新
	$sf = $dbConn->query($sf);
	$sf = $dbConn->fetch_array($sf);
	$enable = 1;
	if($sf){
		$enable = 0;
	}
	/*$str_limit  = array('停产','停售','缺货','侵权','违反','政策','SPY','spy','ebay平台不销售','EBAY平台不销售','商标权','违规','ebay不在线','EBAY不在线');
	foreach($str_limit as $run) {
		if (strpos($sf['goods_name'],$run) !== false){
			$enable = 0;
			break;
		}
	}*/
	if($sf['isuse']=='1'){ 
		$enable = 0;
	}
	if($enable == 0){
		continue;
	}
	if($sf['goods_location']=="停售"){
		continue;
	}
	/*if(in_array($key,$__liquid_items_postbyhkpost)||in_array($key,$__liquid_items_cptohkpost)||in_array($key,$__liquid_items_BuiltinBattery)||in_array($key,$__liquid_items_Paste)){
		continue;
	}*/
	
	/*$ss = "SELECT goods_count FROM ebay_onhandle WHERE goods_sn='$key'";
	$ss = $dbConn->query($ss);
	$ss = $dbConn->fetch_array($ss);*/
	$goods_count = WarehouseAPIModel::getSkuStock($key);
	$salensend = CommonModel::getpartsaleandnosendall($key);
	$useable_stock = $goods_count-$salensend;
	if($useable_stock>0){
		$now_status = '1';
	}else{
		$now_status = '0';
	}
	$sql = "SELECT * FROM om_adjust_sku WHERE sku='{$key}' and type=1 order by createdtime desc";
	$sql = $dbConn->query($sql);
	$sql = $dbConn->fetch_array($sql);
	if($sql){
		if($now_status == $sql['adjustvalue']){
			continue;
		}
	}else{
		$sql['adjustvalue'] = "";
	}
	$mm = "SELECT * FROM om_adjust_sku WHERE sku='{$key}' and type=4";
	$mm = $dbConn->query($mm);
	$mm = $dbConn->fetch_array($mm);
	if(!empty($mm)){			//属于限制料号
		if($mm['CN_status']==0&&$mm['DL_status']==0){
			continue;
		}elseif($mm['CN_status']==0&&$mm['DL_status']==1){
			$st = "INSERT INTO om_adjust_sku set sku='$key',initvalue='{$sql['adjustvalue']}',sku_stock={$useable_stock},adjustvalue='$now_status',type=1,CN_status=1,createdtime=".time().",creator='vipchen'";
		}elseif($mm['CN_status']==1&&$mm['DL_status']==0){
			$st = "INSERT INTO om_adjust_sku set sku='$key',initvalue='{$sql['adjustvalue']}',sku_stock={$useable_stock},adjustvalue='$now_status',type=1,DL_status=1,createdtime=".time().",creator='vipchen'";
		}
	}else{
		$st = "INSERT INTO om_adjust_sku set sku='$key',initvalue='{$sql['adjustvalue']}',sku_stock={$useable_stock},adjustvalue='$now_status',type=1,createdtime=".time().",creator='vipchen'";
	}
	$st = $dbConn->query($st);
	if(!$st){
		echo "料号'$key'执行失败！\n";
	}
}
?>