<?php
/*
*基础操作类(model)
*add by Herman.Xi
*2013-09-01
*/
defined('WEB_PATH') ? '' : exit;
class BaseModel{
	
	public static $dbConn;
	public static $errCode = 0;
	public static $errMsg = "";

	//db初始化
	public static function initDB() {
		global $dbConn;
		self :: $dbConn = $dbConn;
		mysql_query('SET NAMES UTF8');
	}

	public static function begin() {
		self :: initDB();
		self :: $dbConn->begin();
	}

	public static function commit() {
		self :: initDB();
		self :: $dbConn->commit();
	}

	public static function rollback() {
		self :: initDB();
		self :: $dbConn->rollback();
	}

	public static function autoCommit() {
		self :: initDB();
		mysql_query('SET autocommit=1');
	}
}
?>