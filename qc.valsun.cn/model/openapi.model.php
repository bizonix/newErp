<?php
/**
 * OpenapiModel
 * 
 * @package qc.valsun.cn
 * @author blog.anchen8.net
 * @copyright 2013
 * @version $Id$
 * @access public
 */
class OpenapiModel {
	public static $dbConn;
	public static $errCode = 0;
	public static $errMsg = "";

	//db初始化
	public function initDB() {
		global $dbConn;
		self :: $dbConn = $dbConn;
		mysql_query('SET NAMES UTF8');
	}

	/**
	 *取得指定表中的指定记录
	 */
	public static function getTNameProductsList($tName, $select, $where) {
		self :: initDB();
		$sql = "select $select from $tName $where";
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$ret = self :: $dbConn->fetch_array_all($query);
			return $ret; //成功， 返回列表数据
		} else {
			self :: $errCode = "001";
			self :: $errMsg = "获取数据失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}


	/**
	 *添加指定表记录
	 */
	public static function addScrappedProducts($tName, $set) {
		self :: initDB();
		$sql = "INSERT INTO $tName $set";
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$affectRows = self :: $dbConn->affected_rows($query);
			return $affectRows; //成功， 返回列表数据
		} else {
			self :: $errCode = "002";
			self :: $errMsg = "添加失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}
    
    
    /**
	 *修改指定表记录
	 */
	public static function updateTnameProducts($tName, $set, $where) {
		self :: initDB();
		$sql = "UPDATE $tName $set $where";
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$affectRows = self :: $dbConn->affected_rows($query);
			return $affectRows; //成功， 返回列表数据
		} else {
			self :: $errCode = "003";
			self :: $errMsg = "修改失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}
	
}
?>