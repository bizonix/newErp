<?php
/**
 * 类名：TmpReturnProsModel
 * 功能：对pc_tmp_products_return表进行数据库操作
 * 版本：1.0
 * 日期：2013/07/25
 * 作者：朱清庭
 */
class TmpReturnProsModel {
	public static $dbConn;
	public static $errCode = 0;
	public static $errMsg = "";
	static $table = "pc_tmp_products_return";

	public function initDB() {
		global $dbConn;
		self :: $dbConn = $dbConn;
	}

	public static function getTmpReturnPros($select, $where) {
		self :: initDB();
		$sql = "select $select from " . self :: $table . " $where";
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$ret = self :: $dbConn->fetch_array_all($query);
			return $ret;
		} else {
			self :: $errCode = "2101";
			self :: $errMsg = "getTmpReturnPros";
			return false;
		}
	}

	public static function addTmpReturnPros($set) {
		self :: initDB();
		$sql = "insert into " . self :: $table . " $set";
		if (!self :: $dbConn->query($sql)) {
			self :: $errCode = "2201";
			self :: $errMsg = "addTmpReturnPros";
			return false;
		}
	}

	public static function updateTmpReturnPros($set, $where) {
		self :: initDB();
		$sql = "update " . self :: $table . " $set $where";
		//echo $sql.'<br>';
		if (!self :: $dbConn->query($sql)) {
			self :: $errCode = "2301";
			self :: $errMsg = "updateTmpReturnPros";
			return false;
		}
	}

	public static function getTmpReturnProsCount($where) {
		self :: initDB();
		$sql = "select id from " . self :: $table . " $where";
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$ret = self :: $dbConn->num_rows($query);
			return $ret;
		} else {
			self :: $errCode = "2401";
			self :: $errMsg = "getTmpReturnProsCount";
			return false;
		}
	}

	public static function deleteTmpReturnPros($where) {
		self :: initDB();
		$sql = "delete from " . self :: $table . " $where";
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$ret = self :: $dbConn->affected_rows($query);
			return $ret;
		} else {
			self :: $errCode = "2501";
			self :: $errMsg = "deleteNoRows";
		}
	}

}
?>