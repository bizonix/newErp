<?php
/*
 * 类名：OfferPurchaseOrderModel
* 功能：对外提供采购订单列表的数据，并支持请求过来进行条件搜索
* 版本：1.0
* 日期：2013/08/16
*作者：温小彬
* */
class OfferPurchaseOrderModel{
	public static $dbConn;
	public static $prefix;
	public static $errCode = 0;
	public static $errMsg = "";
	public static $table;
	public static function	initDB(){
		global $dbConn;
		self::$dbConn = $dbConn;
		self::$prefix  =  C("DB_PREFIX");
		self::$table   =  self::$prefix."order";;
	}
	public static function error($errCode,$errMsg){
		self::$errCode=$errCode;
		self::$errMsg=$errMsg;
		return false;
	}
	public static function	getOrderList($where="",$limit=""){
		self::initDB();
		if($where!=''){
			$where=" and ".$where;
		}
		$where=" where  po.is_delete=0 ".$where;
		$sql="SELECT DISTINCT
 					po.warehouse_id,
					po.note,
					po.aduituser_id,
					po.order_type,
					po.id,
					po.recordnumber,
					po.status,
					po.addtime,
					po.finishtime,
					po.paymethod,
					po.paystatus,
					po.purchaseuser_id,
					po.partner_id,
 				    po.aduittime,
 				    po.deliverytime
				FROM
					".self::$prefix."order AS po
				LEFT JOIN `".self::$prefix."order_detail` AS pd ON po.id = pd.po_id
				LEFT JOIN `".self::$prefix."goods` as pg ON pg.id = pd.sku_id
				LEFT JOIN  `".self::$prefix."sku_info_tmp` AS ps ON ps.sku = pg.sku
 				".$where."  ".$limit;
		$query=self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->fetch_array_all($query);
			// 			var_dump($sql,$ret);
			return $ret;
		}
		self::error("01","获取采购订单列表失败！");
		return false;
	}
	public  function getOneDetail($po_id){
			$sql = "
					SELECT
			ps.thirtydays,
			ps.fifteendays,
			ps.sevendays,
			pd.id,
			ps.sku,
			sku_id,
			count,
			price,
			stockqty,
			waiting_send,
			booknums,
			interceptnums,
			stock_qty,
			aduit_num
		FROM
			".C('DB_PREFIX')."order_detail AS pd
		LEFT JOIN `".C('DB_PREFIX')."goods` AS pg ON pd.sku_id = pg.id
		LEFT JOIN `".C('DB_PREFIX')."sku_info_tmp` AS ps ON pg.sku = ps.sku
		WHERE
			ps.is_delete = '0'
		AND pd.is_delete = '0'
		AND po_id = ".$po_id;
       
			$ret=queryResult($sql);	
			if($ret){
				return $ret;
			}
			return false;
		}
}
?>