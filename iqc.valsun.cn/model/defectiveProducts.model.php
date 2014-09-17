<?php


/**
 * 类名：DefectiveProductsModel
 * 功能：对qc_sample_defective_products表进行数据库操作
 * 版本：1.0
 * 日期：2013/08/05
 * 作者：朱清庭
 */

class DefectiveProductsModel {
	public static $dbConn;
	public static $errCode = 0;
	public static $errMsg = "";
	static $table = "qc_sample_defective_products";

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
	public static function getDefectiveProductsList($select, $where) {
		self :: initDB();
		$sql = "SELECT $select FROM " . self :: $table . " $where";
		//echo $sql.'<br>';
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$ret = self :: $dbConn->fetch_array_all($query);
			return $ret;
		} else {
			self :: $errCode = "1101";
			self :: $errMsg = "getDefectiveProductsList";
			return false;
		}
	}

	/**
	* 根据条件更新数据
	* @param     $set		更新的字段
	* @param     $where 	条件
	* @return    返回更新的记录数
	*/
	public static function updateDefectiveProducts($set, $where) {
		self :: initDB();
		$sql = "UPDATE " . self :: $table . " $set $where";
		//echo $sql.'<br>';
		if (!self :: $dbConn->query($sql)) {
			self :: $errCode = "1301";
			self :: $errMsg = "updateDefectiveProducts";
			return false;
		}
		return self :: $dbConn->affected_rows();
	}

	/**
	* 根据条件取得符合的记录数
	* @param     $where 	条件
	* @return    $ret		记录数
	*/
	public static function getDefectiveProductsCount($where) {
		self :: initDB();
		$sql = "SELECT id FROM " . self :: $table . " $where";
		//echo $sql.'<br>';
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$ret = self :: $dbConn->num_rows($query);
			return $ret;
		} else {
			self :: $errCode = "1301";
			self :: $errMsg = "getDefectiveProductsCount";
			return false;
		}
	}

	/**
	* 根据条件更新数据
	* @param     $set		插入字段的值
	* @return    返回插入的记录数
	*/
	public static function addDefectiveProducts($set) {
		self :: initDB();
		$sql = "INSERT INTO " . self :: $table . " $set";
		//echo $sql;
		if (!self :: $dbConn->query($sql)) {
			self :: $errCode = "1501";
			self :: $errMsg = "addDefectiveProducts";
			return false;
		} else {
			return self :: $dbConn->affected_rows();
		}
	}

}
?>