<?php


/**
 * 类名：PackingMaterialsModel
 * 功能：对pc_packing_material表进行数据库操作
 * 版本：1.0
 * 日期：2013/07/25
 * 作者：朱清庭
 */

class PackingMaterialsModel {
	public static $dbConn;
	public static $errCode = 0;
	public static $errMsg = "";
	static $table = "pc_packing_material";

	public function initDB() {
		global $dbConn;
		self :: $dbConn = $dbConn;
	}

	public static function getPmList($select, $where) {
		self :: initDB();
		$sql = "select $select from " . self :: $table . " $where";
		//echo $sql;
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$ret = self :: $dbConn->fetch_array_all($query);
			return $ret;
		} else {
			self :: $errCode = "1201";
			self :: $errMsg = "getPmList";
			return false;
		}
	}

	public static function updatePm($set, $where) {
		self :: initDB();
		$sql = "update " . self :: $table . " $set $where";
		//echo $sql.'<br>';
		if (!self :: $dbConn->query($sql)) {
			self :: $errCode = "1301";
			self :: $errMsg = "updatePm";
			return false;
		}
		return self :: $dbConn->affected_rows();
	}

	public static function getPmCount($where) {
		self :: initDB();
		$sql = "select id from " . self :: $table . " $where";
		//echo $sql;
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$ret = self :: $dbConn->num_rows($query);
			return $ret;
		} else {
			self :: $errCode = "1401";
			self :: $errMsg = "getPmCount";
			return false;
		}
	}

	public static function addPm($set) {
		self :: initDB();
		$sql = "insert into " . self :: $table . " $set";
		//echo $sql;
		if (!self :: $dbConn->query($sql)) {
			self :: $errCode = "1501";
			self :: $errMsg = "addPm";
			return false;
		}
		return self :: $dbConn->insert_id();
	}

	public static function deletePm($where) {
		self :: initDB();
		$sql = "update " . self :: $table . " set is_delete=1 $where";
		//echo $sql.'<br>';
		if (!self :: $dbConn->query($sql)) {
			self :: $errCode = "1601";
			self :: $errMsg = "delete";
			return false;
		}
		return self :: $dbConn->affected_rows();
	}
    
    public static function getPmNameById($id) {
		self :: initDB();
		$sql = "select pmName from " . self :: $table . " where id=$id limit 1";
		//echo $sql;
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$ret = self :: $dbConn->fetch_array_all($query);
			return $ret[0]['pmName'];
		} else {
			self :: $errCode = "1201";
			self :: $errMsg = "getPmList";
			return false;
		}
	}

}
?>