<?php


/**
 * 类名：PendingProductsModel
 * 功能：对qc_sample_defective_products表进行数据库操作
 * 版本：1.0
 * 日期：2013/08/05
 * 作者：朱清庭
 */

class PendingProductsModel {
	public static $dbConn;
	public static $errCode = 0;
	public static $errMsg = "";
	static $table = "qc_sample_pending_products";

	public function initDB() {
		global $dbConn;
		self :: $dbConn = $dbConn;
	}

	/**
	* 根据条件取得qc_sample_defective_products表的结果集
	* @param     $select	select的字段
	* @param     $where 	条件
	* @return    $ret		结果集
	*/
	public static function getPendingProductsList($select, $where) {
		self :: initDB();
		$sql = "SELECT $select FROM " . self :: $table . " $where";
		//echo $sql.'<br>';
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$ret = self :: $dbConn->fetch_array_all($query);
			return $ret;
		} else {
			self :: $errCode = "1101";
			self :: $errMsg = "getPendingProductsList";
			return false;
		}
	}

	/**
	* 根据条件更新数据
	* @param     $set		更新的字段
	* @param     $where 	条件
	* @return    返回更新的记录数
	*/
	public static function updatePendingProducts($set, $where) {
		self :: initDB();
		$sql = "UPDATE " . self :: $table . " $set $where";
		echo $sql . '<br>';
		if (!self :: $dbConn->query($sql)) {
			self :: $errCode = "1301";
			self :: $errMsg = "updatePendingProducts";
			return false;
		}
		return self :: $dbConn->affected_rows();
	}

	/**
	* 根据条件取得符合的记录数
	* @param     $where 	条件
	* @return    $ret		记录数
	*/
	public static function getPendingProductsCount($where) {
		self :: initDB();
		$sql = "SELECT id FROM " . self :: $table . " $where";
		//echo $sql.'<br>';
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$ret = self :: $dbConn->num_rows($query);
			return $ret;
		} else {
			self :: $errCode = "1301";
			self :: $errMsg = "getPendingProductsCount";
			return false;
		}
	}

	/**
	* 根据条件更新数据
	* @param     $set		插入字段的值
	* @return    返回插入的记录数
	*/
	public static function addPendingProducts($set) {
		self :: initDB();
		$sql = "INSERT INTO " . self :: $table . " $set";
		//echo $sql;
		if (!self :: $dbConn->query($sql)) {
			self :: $errCode = "1501";
			self :: $errMsg = "addPendingProducts";
			return false;
		} else {
			return self :: $dbConn->affected_rows();
		}
	}

	//从wh_sample_info表中取出数据
	public static function getWhInfo($select, $where) {
		self :: initDB();
		$sql = $sql = "SELECT $select FROM wh_sample_info $where ";
		echo $sql . '<br>';
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$ret = self :: $dbConn->fetch_array_all($query);
			return $ret;
		} else {
			self :: $errCode = "1601";
			self :: $errMsg = "getWhInfo";
			return false;
		}
	}

	//update wh表中数据
	public static function updateWhInfo($set, $where) {
		self :: initDB();
		$sql = "UPDATE wh_sample_info $set $where";
		echo $sql . '<br>';
		if (!self :: $dbConn->query($sql)) {
			self :: $errCode = "1701";
			self :: $errMsg = "updateWhInfo";
			return false;
		}
		return self :: $dbConn->affected_rows();
	}

	//添加一条记录到wh
	public static function addgetWhInfo($set) {
		self :: initDB();
		$sql = "INSERT INTO wh_sample_info $set";
		echo $sql . '<br>';
		if (!self :: $dbConn->query($sql)) {
			self :: $errCode = "1801";
			self :: $errMsg = "wh_sample_info";
			return false;
		} else {
			return self :: $dbConn->affected_rows();
		}
	}

}
?>