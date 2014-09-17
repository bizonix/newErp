<?php
/*error_reporting(-1);
//header('location:json.php?mod=setToCache&act=setCarrier');
file_get_contents('/data/web/erpNew/trans.valsun.cn/html/json.php?mod=setToCache&act=setCarrier');
exit;*/
set_time_limit(0);
error_reporting(E_ALL);
$link_listing	=	mysql_connect('192.168.200.222','cerp','123456') or die("Could not connect: " . mysql_error());
$db_listing		=	mysql_select_db('valsun_trans',$link_listing) or die('数据库连接错误');
mysql_query('set names utf8',$link_listing);

$memc_obj = new Memcache;
$memc_obj->connect('192.168.200.222', 11211);

var_dump(setCarrier());

function setCarrier(){
	global $memc_obj;
	/*$return_arr = array();
	$rowArray1 = array();
	$rowArray2 = array();
	$rowArray3 = array();
	$sql = "select * from trans_carrier where storeId=1 ";
	$query_car = mysql_query($sql);
	while($row = mysql_fetch_assoc($query_car))
	{
		$rowArray1[$row['id']] = $row;
		$sql = "select * from trans_channels where carrierId = '{$row['id']}' and storeId=1 ";
		$query_chan = mysql_query($sql);
		while($line = mysql_fetch_assoc($query_chan))
		{
			$rowArray3[$row['id']][$line['id']] = $line;
			$sql = "select * from trans_partition where channelId = '{$line['id']}' and storeId=1 ";
			$query_par = mysql_query($sql);
			while($val = mysql_fetch_assoc($query_par))
			{
				$rowArray2[$row['id']][$line['id']][$val['id']] = $val;
			}
		}
	}
	$result1 = $memc_obj->get('trans_system_carrier');
	if(!$result1){
		$isok = $memc_obj->set('trans_system_carrier', $rowArray1, MEMCACHE_COMPRESSED, 0);
		if(!$isok){
			$return_arr['errCode'] = 2;
			$return_arr['errMsg'] = 'memcache缓存出错!';
		}else{
			$return_arr['errCode'] = 200;
			$return_arr['errMsg'] = 'memcache缓存成功!';
		}
	}else{
		return $result1;	
	}
	$result2 = $memc_obj->get('trans_system_carrierinfo');
	if(!$result2){
		$isok = $memc_obj->set('trans_system_carrierinfo', $rowArray2, MEMCACHE_COMPRESSED, 0);
		if(!$isok){
			$return_arr['errCode'] = 2;
			$return_arr['errMsg'] = 'memcache缓存出错!';
		}else{
			$return_arr['errCode'] = 200;
			$return_arr['errMsg'] = 'memcache缓存成功!';
		}
	}else{
		return $result2;
	}
	$result3 = $memc_obj->get('trans_system_channelinfo');
	if(!$result3){
		$isok = $memc_obj->set('trans_system_channelinfo', $rowArray3, MEMCACHE_COMPRESSED, 0);
		if(!$isok){
			$return_arr['errCode'] = 2;
			$return_arr['errMsg'] = 'memcache缓存出错!';
		}else{
			$return_arr['errCode'] = 200;
			$return_arr['errMsg'] = 'memcache缓存成功!';
		}
	}else{
		return $result3;
	}*/
	
	$sql = "select * from trans_countries_small_comparison ";
	$query_car = mysql_query($sql);
	while($row = mysql_fetch_assoc($query_car))
	{
		$cacheName = md5('countries_small_comparison'.$row['small_country']);
		echo $cacheName; echo "<br>";
		$result1 = $memc_obj->get($cacheName);
		if(!$result1){
			$isok = $memc_obj->set($cacheName, $row, MEMCACHE_COMPRESSED, 0);
			if(!$isok){
				$return_arr['errCode'] = 2;
				$return_arr['errMsg'] = 'memcache缓存出错!';
			}else{
				$return_arr['errCode'] = 200;
				$return_arr['errMsg'] = 'memcache缓存成功!';
			}
		}else{
			var_dump($result1);
		}
	}
	
	$sql = "select * from trans_countries_standard ";
	$query_car = mysql_query($sql);
	while($row = mysql_fetch_assoc($query_car))
	{
		$cacheName = md5('countries_standard'.$row['countrySn']);
		$result1 = $memc_obj->get($cacheName);
		if(!$result1){
			$isok = $memc_obj->set($cacheName, $row['countryNameEn'], MEMCACHE_COMPRESSED, 0);
			if(!$isok){
				$return_arr['errCode'] = 2;
				$return_arr['errMsg'] = 'memcache缓存出错!';
			}else{
				$return_arr['errCode'] = 200;
				$return_arr['errMsg'] = 'memcache缓存成功!';
			}
		}else{
			var_dump($result1);
		}
	}
	//return $return_arr;
}