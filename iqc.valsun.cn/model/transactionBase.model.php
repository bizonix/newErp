<?php
class TransactionBaseModel {

	public static $dbConn;
	public static $errCode = 0;
	public static $errMsg = "";

	//db初始化
	public function initDB() {
		global $dbConn;
		self :: $dbConn = $dbConn;
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