<?php

/**
 * 名称: 
 * 功能: 订单相关的跟踪号信息类
 * Added By zxh 2014/3/7
 * max $errCode = 3303
 */
 
class OrderTracknumberModel{
	public static $dbConn;
	public static $errCode = 0;
	public static $errMsg = "";
	public static $table = "om_order_refund";

	//db初始化
	public static function initDB() {
		global $dbConn;
		self :: $dbConn = $dbConn;
		mysql_query('SET NAMES UTF8');
	}

	public static function updateOrderTracknumber($data) {
		self::initDB();
		$orderIds = array();
		foreach($data as $k => $v) {	//去除相同的数据
			if($data[$k + 1] == $v) {
				continue;
			}
			$data[] = $v;
		}
		foreach($data as $k => $v) {
			$orderIds[] = $v['orderId'];
		}
		$sql	= 'SELECT `omOrderId`,`tracknumber` FROM om_order_tracknumber WHERE `omOrderId` IN (\''.implode('\',\'',$orderIds).'\')';
		$query	= self::$dbConn->query($sql);
		if(!$query) {
			self::$errCode	= '3301';
			self::$errMsg	= '查询系统的跟踪号失败, 请重试！';
			return false;
		}
		$orderTracknumberData = self::$dbConn->fetch_array_all($query);
		$orderSysIds = array();
		if(!empty($orderTracknumberData)) {
			foreach($orderTracknumberData as $k => $v) {
				$orderSysIds[] = $v['omOrderId'];
			}
		}
		$ids		= array_diff($orderIds,$orderSysIds);	//对比订单系统与仓库系统的差集
		$insertData = array();
		$sql		= array();
		$updateSql	= array();
		foreach($data as $k => $v) {
			if(in_array($v['orderId'], $ids)) {
				$sql[] = '(\''.$v['orderId'].'\',\''.$v['tracknumber'].'\')'
			} else {
				$updateSql[$v['orderId']] = 'UPDATE om_order_tracknumber SET `tracknumber` = \''.$v['tracknumber'].'\' WHERE `omOrderId` = \''.$v['orderId'].'\'';
			}
		}
		if(empty($sql)) {
			return true;
		}
		$sql	= 'INSERT INTO om_order_tracknumber (`'.implode('`,`',array_keys($insertData[0]).'`) VALUES '.implode(',',$sql);
		$query	= self::$dbConn->query($sql);
		if(!$query) {
			self::$errCode	= '3302';
			self::$errMsg	= '查询系统的跟踪号失败, 请重试！';
			return false;
		}
		$updateStatus = 1;
		$errStr = '';
		if(!empty($updateSql)) {
			foreach($updateSql as $k => $v) {
				$query	= self::$dbConn->query($sql);
				if(!$query) {
					$updateStatus = $updateStatus * 0;
					$errStr			.= $k.": 订单修改失败！\r\n";
				}
			}
		}
		if(strlen($errStr) > 0) {
			self::$errCode	= '3303';
			self::$errMsg	= $errStr;
			return false;
		}
		return true;
	}
}