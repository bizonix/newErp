<?php
/**
 *类名：UnusualOrderModel
 *功能：对外提供API数据
 *版本：2013-09-17
 *作者：温小彬
 */
class UnusualOrderModel{
	public static $dbConn;
	public static $prefix;
	public static $errCode = 0;
	public static $errMsg = "";
	public static function	initDB(){
		global $dbConn;
		self::$dbConn = $dbConn;
		self::$prefix  =  C("DB_PREFIX");
	}
	
	/**
	 *通过id 获取中文名
	 *@param str $id
	 *@author wxb
	 */
	public static function getNameById($id){
		self::initDB();
		$sql = "SELECT global_user_name  FROM `power_global_user`  WHERE global_user_id = {$id} LIMIT 1";
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->fetch_array_all($query);
			if($ret[0]['global_user_name']){
				return  $ret[0]['global_user_name'];
			}else{
				self::$errMsg = "result empty ".$sql;
				return false;
			}
		}else{
			self::$errMsg = "query wrong ".$sql;
			return false;
		}		
	}
	/**
	 *通过sku 获取供应商名
	 *@param str $sku
	 *@author wxb
	 */
	public static function getComNameBySku($sku){
		self::initDB();
		$sql = "SELECT p.username  FROM `".self::$prefix."partner`  AS p ";
		$sql .= " LEFT JOIN ".self::$prefix."goods_partner_relation AS gp ";
		$sql .= " ON p.id = gp.partnerId ";
		$sql .= " WHERE gp.sku = '{$sku}' LIMIT 1";
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->fetch_array_all($query);
			if($ret[0]['username']){
				return  $ret[0]['username'];
			}else{
				self::$errMsg = "result empty ".$sql;
				return false;
			}
		}else{
			self::$errMsg = "query wrong ".$sql;
			return false;
		}
	}
	
}






?>
