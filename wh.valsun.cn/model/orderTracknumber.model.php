<?php
/**
 * 类名: OrderTracknumberModel
 * 功能: 查询与订单跟踪号有关的数据
 * 
 * max errCode : 3302
 */
class OrderTracknumberModel{	
	public 	static $dbConn;
	public	static $errCode	=	0;
	public	static $errMsg	=	"";
	static  $table			=	"";
	
	//db初始化
	public static function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
		mysql_query('SET NAMES UTF8');
	}

	/**
	 * 名称: getTracknumberInfo
	 * 功能: 根据订单ID获取跟踪号信息
	 * @para $orderIds Array
	 * return Array
	 */
	public function getTracknumberInfo($orderIds) {
		self::initDB();
		$orderIds	= @is_array($orderIds) ? $orderIds : explode(',',$orderIds);
		$errStr		= '';
		if(empty($orderIds)) {
			$errStr .= "未输入有效的订单ID<br />";
		}
		foreach($orderIds as $k => $v) {
			if(preg_match('/^\d+$/',$v) < 1) {
				$errStr .= $v."的格式不正确!";
			}
		}
		if(strlen($errStr) > 0) {
			self::$errCode = '3301';
			self::$errMsg = $errStr;
			return false;
		}
		$sql = 'SELECT os.`id`,os.`originOrderId`,os.`recordNumber`,os.`shipOrderId`,os.`storeId`,ot.`tracknumber` 
				FROM wh_shipping_order_relation AS os
				LEFT JOIN wh_order_tracknumber AS ot ON os.`shipOrderId` = ot.`shipOrderId`
				WHERE originOrderId IN (\''.implode('\',\'',$orderIds).'\')';
		$query	 =	self::$dbConn->query($sql);	
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"error";
			return false;	
		}
		$data = array();
		if(!empty($ret)) {
			$tempData = array();
			foreach($ret as $k => $v) {
				$tempData[] = $v['originOrderId'];
			}
			$tempData = array_flip(array_flip($tempData));	//清除多余的订单号
			foreach($ret as $k => $v) {
				if(in_array($v['originOrderId'],$tempData)) {
					$data[] = array('orderId' => $v['originOrderId'],'tracknumber' => $v['tracknumber']);
				}
			}
		}
		return $data;	//成功， 返回列表数据
	}

	/**
	 * 名称: getTracknumberInfoByDate
	 * 功能: 根据开始时间与结束时间获取跟踪号信息
	 * @para $startTime int
	 * @para $endTime int
	 * return Array
	 */
	public function getTracknumberInfoByDate($startTime,$endTime) {
		self::initDB();
		if(preg_match('/^\d+$/',$startTime) < 1 || preg_match('/^\d+$/',$endTime) < 1) {
			self::$errCode	= '3302';
			self::$errMsg	= '开始时间或结束时间输入不正确, 请重试! ';
			return false;
		}

		$sql = 'SELECT os.`id`,os.`originOrderId`,os.`recordNumber`,os.`shipOrderId`,os.`storeId`,ot.`tracknumber` 
				FROM wh_shipping_order_relation AS os
				LEFT JOIN wh_order_tracknumber AS ot ON os.`shipOrderId` = ot.`shipOrderId`
				WHERE ot.createdTime >= '.$startTime.' AND ot.createdTime <= '.$endTime;
		$query	 =	self::$dbConn->query($sql);	
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"error";
			return false;	
		}
		$data = array();
		if(!empty($ret)) {
			$tempData = array();
			foreach($ret as $k => $v) {
				$tempData[] = $v['originOrderId'];
			}
			$tempData = array_flip(array_flip($tempData));	//清除多余的订单号
			foreach($ret as $k => $v) {
				if(in_array($v['originOrderId'],$tempData)) {
					$data[] = array('orderId' => $v['originOrderId'],'tracknumber' => $v['tracknumber']);
				}
			}
		}
		return $data;	//成功， 返回列表数据
	}
}