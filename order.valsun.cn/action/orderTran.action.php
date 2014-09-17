<?php
/**
 * 类名：OrderTranAct
 * 功能：订单提供给物流系统的对外接口action
 * 版本：1.0
 * 日期：2014/03/01
 * 作者：管拥军
 */
class OrderTranAct{
	public static $dbConn;
	public static $errCode		= 0;
	public static $errMsg		= "";
	
	//初始化db类
	public static function	initDB(){
		global $dbConn;
		self::$dbConn	= $dbConn;
	}
	
	/**
	 * OrderTranAct::act_get_order_tran_info()
	 * 获取一个或多个订单摘要信息
	 * @param string $ids 订单编号
	 * @return  array
	 */
	public static function act_get_order_tran_info(){
		$ids	= isset($_REQUEST["ids"]) ? $_REQUEST["ids"] : "";
		if (empty($ids)) {
			self::$errCode  = 10000;
			self::$errMsg   = "订单ID参数有误";
			return false;
		}
		$res			= OrderTranModel::get_order_tran_info($ids);
		self::$errCode  = OrderTranModel::$errCode;
        self::$errMsg   = OrderTranModel::$errMsg;
        return $res;		
	}
	
	/**
	 * OrderTranAct::act_get_tracknum_tran_info()
	 * 获取订单系统某个几天内跟踪号列表
	 * @param int $days 几天内
	 * @param int $carrierId 运输方式ID
	 * @return  array
	 */
	public static function act_get_tracknum_tran_info(){
		$days		= isset($_REQUEST["days"]) ? intval($_REQUEST["days"]) : 0;
		$carrierId	= isset($_REQUEST["carrierId"]) ? intval($_REQUEST["carrierId"]) : 0;
		if (empty($days)) {
			self::$errCode  = 10000;
			self::$errMsg   = "时间参数有误";
			return false;
		}
		// if (empty($carrierId)) {
			// self::$errCode  = 10001;
			// self::$errMsg   = "运输方式ID参数有误";
			// return false;
		// }
		$times			= strtotime("-{$days} day"." 00:00:01");
		$res			= OrderTranModel::get_tracknum_tran_info($times, $carrierId);
		self::$errCode  = OrderTranModel::$errCode;
        self::$errMsg   = OrderTranModel::$errMsg;
        return $res;		
	}		
}
?>