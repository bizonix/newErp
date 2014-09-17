<?php
/**
 * 类名：OrderTranModel
 * 功能：订单提供给物流系统的对外接口model
 * 版本：1.0
 * 日期：2014/03/01
 * 作者：管拥军
 */
 
class OrderTranModel{
	public static $dbConn;
	public static $errCode		= 0;
	public static $errMsg		= "";
	
	//初始化db类
	public static function	initDB(){
		global $dbConn;
		self::$dbConn	= $dbConn;
	}
			
	/**
	 * OrderTranModel::get_order_tran_info()
	 * 获取一个或多个订单摘要信息
	 * @param string $ids 订单编号
	 * @return  array
	 */
	public static function get_order_tran_info($ids){
		self::initDB();
		$sql 	= "SELECT 
					a.omOrderId as ebay_id,a.actualWeight as orderweight,a.actualShipping as ordershipfee,weighTime as scantime,
					b.accountId,b.recordNumber,b.transportId as ebay_carrier,b.channelId,b.orderStatus as ebay_status,
					c.countryName as ebay_countryname,c.countrySn as ebay_couny,
					d.tracknumber as ebay_tracknumber,
					e.account as ebay_account
					FROM om_shipped_order_warehouse AS a 
					LEFT JOIN om_shipped_order AS b ON a.omOrderId = b.id
					LEFT JOIN om_shipped_order_userInfo AS c ON a.omOrderId = c.omOrderId
					LEFT JOIN om_order_tracknumber AS d ON a.omOrderId = d.omOrderId
					LEFT JOIN om_account AS e ON b.accountId = e.id
					WHERE a.omOrderId IN({$ids})
					";
		$query	= self::$dbConn->query($sql);
		if ($query) {
			$res	= self::$dbConn->fetch_array_all($query);
			return $res;
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "获取数据失败";
			return false;
		}
	}
	
	/**
	 * OrderTranModel::get_tracknum_tran_info()
	 * 获取订单系统某个几天内跟踪号列表
	 * @param int $days 时间
	 * @param int $carrierId 运输方式ID
	 * @return  array
	 */
	public static function get_tracknum_tran_info($days,$carrierId){
		self::initDB();
		$condition	= "";
		if (!empty($carrierId)) $condition	.= " AND b.transportId = {$carrierId}";
		$track_tab	= in_array($carrierId,array(46,47)) ? "ow_order_tracknumber" : "om_order_tracknumber";
		$sql 		= "SELECT 
						a.omOrderId as ebay_id,a.actualWeight as orderweight,a.actualShipping as ordershipfee,weighTime as scantime,
						b.accountId,b.recordNumber,b.transportId as ebay_carrier,b.channelId,b.orderStatus as ebay_status,
						c.countryName as ebay_countryname,c.countrySn as ebay_couny,
						d.tracknumber as ebay_tracknumber,
						e.account as ebay_account,
						f.platform as PlatForm
						FROM om_shipped_order_warehouse AS a 
						LEFT JOIN om_shipped_order AS b ON a.omOrderId = b.id
						LEFT JOIN om_shipped_order_userInfo AS c ON a.omOrderId = c.omOrderId
						LEFT JOIN {$track_tab} AS d ON a.omOrderId = d.omOrderId
						LEFT JOIN om_account AS e ON b.accountId = e.id
						LEFT JOIN om_platform AS f ON b.platformId = f.id
						WHERE a.weighTime >= {$days} {$condition}
						";
		//echo $sql;
		$query	= self::$dbConn->query($sql);
		if ($query) {
			$res	= self::$dbConn->fetch_array_all($query);
			return $res;
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "获取数据失败";
			return false;
		}
	}		
}
?>
