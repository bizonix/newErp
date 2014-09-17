<?php
 
class PackageAct{
	public static $dbConn;
	public static $errCode		= 0;
	public static $errMsg		= "";
	
	//初始化db类
	public static function	initDB(){
		global $dbConn;
		self::$dbConn	= $dbConn;
	}
	
	/**
	 * PackageAct::fixPartnerId()
	 * 修复某些状态订单的供应商ID
	 * @param string $status 订单状态
	 * @return  array
	 */
	public static function fixPartnerId(){
		$status	= isset($_REQUEST["status"]) ? $_REQUEST["status"] : "";
		if (empty($status)) {
			self::$errCode  = 10000;
			self::$errMsg   = "订单状态参数有误";
			return false;
		}
		$res			= PackageModel::fixPartnerId($status);
		self::$errCode  = PackageModel::$errCode;
        self::$errMsg   = PackageModel::$errMsg;
        return $res;		
	}
	
	/**
	 * PackageAct::getPackageOrder()
	 * 获取采购下给供应商的在途订单
	 * @param date $addTime 下单日期
	 * @param bool $debug 是否显示SQL语句
	 * @return  array
	 */
	public static function getPackageOrder(){
		$addTime	= isset($_REQUEST["add_time"]) ? intval($_REQUEST["add_time"]) : 0;
		$debug		= isset($_REQUEST["debug"]) ? $_REQUEST["debug"] : 0;
		if (empty($addTime)) {
			self::$errCode  = 10000;
			self::$errMsg   = "下单时间参数有误";
			return false;
		}
		$res			= PackageModel::getPackageOrders($addTime, $debug);
		self::$errCode  = PackageModel::$errCode;
        self::$errMsg   = PackageModel::$errMsg;
        return $res;		
	}
	
	/**
	 * PackageAct::getPackageOrderBySkuTime()
	 * 获取SKU到货时间更新的订单
	 * @param date $addTime 到货日期
	 * @return  array
	 */
	public static function getPackageOrderBySkuTime(){
		$addTime	= isset($_REQUEST["add_time"]) ? intval($_REQUEST["add_time"]) : 0;
		if (empty($addTime)) {
			self::$errCode  = 10000;
			self::$errMsg   = "到货时间参数有误";
			return false;
		}
		$res			= PackageModel::getPackageOrdersBySkuTime($addTime);
		self::$errCode  = PackageModel::$errCode;
        self::$errMsg   = PackageModel::$errMsg;
        return $res;		
	}

	/**
	 * PackageAct::getPackageOrderDetail()
	 * 获取一个或多个订单详情
	 * @param string $po_id 订单号
	 * @return  array
	 */
	public static function getPackageOrderDetail(){
		$poid	= isset($_REQUEST["po_id"]) ? post_check($_REQUEST["po_id"]) : "";
		if (empty($poid)) {
			self::$errCode  = 10000;
			self::$errMsg   = "订单编号参数有误";
			return false;
		}
		$res			= PackageModel::getPackageOrderDetails($poid);
		self::$errCode  = PackageModel::$errCode;
        self::$errMsg   = PackageModel::$errMsg;
        return $res;		
	}
}
?>
