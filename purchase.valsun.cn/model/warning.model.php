<?php
/**
 * 类名：WarningModel
 * 功能：催货+海外仓预警数据（CRUD）层
 * 版本：1.0
 * 日期：2013/8/5
 * 作者：管拥军
 */
 
class WarningModel{
	public static $dbConn;
	public static $errCode		= 0;
	public static $errMsg		= "";
	public static $prefix;
	private static $detailtab	= "sku_info_tmp";
	private static $showtab		= "goods";
	private static $patab		= "partner";
	private static $owtab		= "stock_invoice";
	private static $owdetailtab	= "stock_invoice_detail";
		
	/**
	 * WarningModel::initDB()
	 * 返回数据库连接
	 * @return 
	 */
	public static function	initDB(){
		global $dbConn;
		self::$dbConn	= $dbConn;
		self::$prefix	= C('DB_PREFIX');
	}
	
	/**
	 * WarningModel::modList()
	 * 列出符合条件的数据并分页显示
	 * @param string $where 查询条件
	 * @param integer $page 页码
	 * @param integer $pagenum 每页个数
	 * @return array 结果集数组
	 */
	public static function modList($where, $page, $pagenum){
		self::initDB();
		$start	= ($page-1)*$pagenum;
		$sql 	= "SELECT
				a.partnerid,
				a.isNew,
				a.warehouseid,
				a.id,
				a.sku,
				a.goodsName,
				a.goodsCost,		
				a.goodsNote,
				a.pmId,
				c.company_name,
				b.purchase_days,
				b.alert_days,
				b.everyday_sale,
				b.booknums,
				b.interceptnums,
				b.autointerceptnums,
				b.is_warning,
				b.stock_qty,
				b.adjust_num,
				b.waiting_send,
				b.aduit_num,
				b.first_sale,
				b.last_sale
				FROM
				".self::$prefix.self::$showtab." AS a
				LEFT JOIN ".self::$prefix.self::$patab." AS c ON c.id = a.partnerid
				LEFT JOIN ".self::$prefix.self::$detailtab." AS b ON b.sku = a.sku WHERE $where AND b.everyday_sale > 0 ORDER BY a.id ASC LIMIT $start,$pagenum";
		$query	= self::$dbConn->query($sql);
		if($query){
			$ret	= self::$dbConn->fetch_array_all($query);
			return $ret;
		}else{
			self::$errCode	= "1060";
			self::$errMsg	= "获取数据失败";
			return false;
		}
	}
	
	/**
	 * WarningModel::modListCount()
	 * 返回某个条件结果统计的总数
	 * @param string $where 查询条件
	 * @return integer 总数量 
	 */
	public static function modListCount($where){
		self::initDB();
		$sql = "SELECT count(*)	FROM ".self::$prefix.self::$showtab." AS a
				LEFT JOIN ".self::$prefix.self::$patab." AS c ON c.id = a.partnerid
				LEFT JOIN ".self::$prefix.self::$detailtab." AS b ON b.sku = a.sku WHERE $where AND b.everyday_sale > 0";
		$query	= self::$dbConn->query($sql);
		if($result=self::$dbConn->query($sql))
		{
			$data=self::$dbConn->fetch_row($result);
			return $data[0];
		}else{
			self::$errCode	= "1060";
			self::$errMsg	= "获取数据失败";
			return 0;
		}
	}
	
	/**
	 * WarningModel::addStock()
	 * 生成海外备货清单
	 * @param string $ids
	 * @param string $purchase
	 * @return  bool
	 */
    public static function addStock($ids, $purchase){
		self::initDB();
		require_once WEB_PATH."model/purchaseOrder.model.php";
		$sql 		= "SELECT sku,goodsCost FROM ".self::$prefix.self::$showtab." WHERE id IN ({$ids})";
		$query		= self::$dbConn->query($sql);
		if($query){
			$ret	= self::$dbConn->fetch_array_all($query);
			$nowtime	= time();
			$stocksn	= PurchaseOrderModel::autoCreateOrderSn(66,3);
			$sqlstock	= "INSERT INTO ".self::$prefix.self::$owtab."(ordersn,adduser,addtime,status) VALUES('{$stocksn}','{$purchase}','{$nowtime}','1')";
			$query		= self::$dbConn->query($sqlstock);
			foreach($ret as $v){
				$skusql	= "INSERT INTO ".self::$prefix.self::$owdetailtab."(ordersn,sku,cost) VALUES('{$stocksn}', '{$v['sku']}', '{$v['goodsCost']}')";
				$query	= self::$dbConn->query($skusql);
			}
			return true;
		}else{
			self::$errCode	= "1060";
			self::$errMsg	= "获取数据失败";
			return false;
		}
	}
}
?>