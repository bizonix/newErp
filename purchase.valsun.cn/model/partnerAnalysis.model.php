<?php
/**
 * 类名：PartnerModel
 * 功能：封装供应商管理模块相关的Model
 * 版本：1.0
 * 日期：2013/7/31
 * 作者：任达海
 */
 
class PartnerAnalysisModel {
	public static $dbConn;
	static $errCode	            =	0;
	static $errMsg	            =	"";
	static $table_goods	        =	"goods";
    static $table_order_detail	=	"order_detail";
    static $table_order     	=	"order";
    
    /**
    * 初始化数据库连接
    */
	public static function	initDB() {
		global $dbConn;
		self::$dbConn	=	$dbConn;
	}
	
    /**
    * 获取SKU列表
    * @param     $where    查询条件
    * @param     $limit    查询字段
    * @param     $limit    分页条件
    * @return    $result   查询到的记录集 
    */
	public static function getSKUList($where, $field, $limit = '') {
		self::initDB();
        $sql = "SELECT ".$field." FROM `".C('DB_PREFIX').self::$table_goods."` pg,`".C('DB_PREFIX').self::$table_order_detail."` pod WHERE pg.is_delete = '0' ".$where." AND pg.id = pod.sku_id ORDER BY pod.id ASC ".$limit;
        $query	=	self::$dbConn->query($sql);
		if($query) {
			$ret	=	self::$dbConn->fetch_array_all($query);
			return $ret;
		} else {
			self::$errCode	=	"001";
			self::$errMsg	=	"出错！位置：".__FUNCTION__." sql= ".$sql;
			return false;
		}
	} 
    
    /**
    * 获取订单列表
    * @param     $where    查询条件
    * @param     $limit    查询字段
    * @param     $limit    分页条件
    * @return    $result   查询到的记录集 
    */
   	public static function getOrderList($where, $field, $limit = '') {
		self::initDB();
        $sql = "SELECT ".$field." FROM `".C('DB_PREFIX').self::$table_order."` WHERE `is_delete` = '0' ".$where." ORDER BY id ASC ".$limit;
        $query	=	self::$dbConn->query($sql);
		if($query) {
			$ret	=	self::$dbConn->fetch_array_all($query);
			return $ret;
		} else {
			self::$errCode	=	"002";
			self::$errMsg	=	"出错！位置：".__FUNCTION__." sql= ".$sql;
			return false;
		}
	} 
 
}

?>