<?php
/**
 *类名: OrderTracknumberAct
 *功能: 获取订单的跟踪号等信息接口
 *作者: zxh
 *
 */
class OrderTracknumberAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";	

	/**
	 * 名称: act_getOrderTrucknumber
	 * 功能: 获取订单的跟踪号信息
	 * @para $orderIds String
	 * return Array
	 */
	public function act_getOrderTracknumber() {
		$orderIds			= $_REQUEST['orderIds'];
		$truckNumberData	= OrderTracknumberModel::getTracknumberInfo($orderIds);
		if(!$truckNumberData) {
			self::$errCode	= OrderTracknumberModel::$errCode;
			self::$errMsg	= OrderTracknumberModel::$errMsg;
			return false;
		}
		return $truckNumberData;
	}

	/**
	 * 名称: act_getOrderTracknumberBydate
	 * 功能: 获取订单的跟踪号信息
	 * @para $startTime int
	 * @para $endTime int
	 * return Array
	 */
	public function act_getOrderTracknumberBydate() {
		$startTime			= $_REQUEST['startTime'];
		$endTime			= $_REQUEST['endTime'];
		$truckNumberData	= OrderTracknumberModel::getTracknumberInfoByDate($startTime,$endTime);
		if(!$truckNumberData) {
			self::$errCode	= OrderTracknumberModel::$errCode;
			self::$errMsg	= OrderTracknumberModel::$errMsg;
			return false;
		}
		return $truckNumberData;
	}
}