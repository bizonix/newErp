<?php


/**
 * 类名：SampleCoefficientModel
 * 功能：对qc_sample_coefficient表进行数据库操作
 * 版本：1.0
 * 日期：2013/08/03
 * 作者：朱清庭
 */

class SampleCoefficientModel {
	public static $dbConn;
	public static $errCode = 0;
	public static $errMsg = "";
	static $table = "qc_sample_coefficient";

	public function initDB() {
		global $dbConn;
		self :: $dbConn = $dbConn;
	}

	/**
	* 根据条件取得qc_sample_coefficient表的结果集
	* @param     $select	select的字段
	* @param     $where 	条件
	* @return    $ret		结果集
	*/
	public static function getSampleCoefficientList($select, $where) {
		self :: initDB();
		$sql = "SELECT $select FROM " . self :: $table . " $where";
		//echo $sql.'<br>';
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$ret = self :: $dbConn->fetch_array_all($query);
			return $ret;
		} else {
			self :: $errCode = "1101";
			self :: $errMsg = "getCoefficient";
			return false;
		}
	}

	/**
	* 根据条件更新数据
	* @param     $set		更新的字段
	* @param     $where 	条件
	* @return    返回更新的记录数
	*/
	public static function updateSampleCoefficient($set, $where) {
		self :: initDB();
		$sql = "UPDATE " . self :: $table . " $set $where";
		//echo $sql.'<br>';
		if (!self :: $dbConn->query($sql)) {
			self :: $errCode = "1301";
			self :: $errMsg = "updateSampleCoefficient";
			return false;
		}
		return self :: $dbConn->affected_rows();
	}

	/**
	* 根据条件取得符合的记录数
	* @param     $where 	条件
	* @return    $ret		记录数
	*/
	public static function getSampleCoefficientCount($where) {
		self :: initDB();
		$sql = "SELECT id FROM " . self :: $table . " $where";
		//echo $sql.'<br>';
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$ret = self :: $dbConn->num_rows($query);
			return $ret;
		} else {
			self :: $errCode = "1301";
			self :: $errMsg = "getSampleCoefficientCount";
			return false;
		}
	}

	/**
	* 根据条件更新数据
	* @param     $set		插入字段的值
	* @return    返回插入的记录数
	*/
	public static function addSampleCoefficient($set) {
		self :: initDB();
		$sql = "INSERT INTO " . self :: $table . " $set";
		//echo $sql;
		if (!self :: $dbConn->query($sql)) {
			self :: $errCode = "1501";
			self :: $errMsg = "addSampleCoefficient";
			return false;
		} else {
			return self :: $dbConn->affected_rows();
		}
	}
    
    //根据id选出样本类型名称
    public static function getSampleTypeNameById($id) {
		self :: initDB();
		$sql = "SELECT typeName FROM " . 'qc_sample_type' . " WHERE id='$id'";
		//echo $sql.'<br>';
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$ret = self :: $dbConn->fetch_array_all($query);
			return $ret[0]['typeName'];
		} else {
			self :: $errCode = "2201";
			self :: $errMsg = "getSampleTypeNameById";
			return false;
		}
	}
    
    //根据id选出样本大小
    public static function getSizeCodeNumById($id) {
		self :: initDB();
		$sql = "SELECT sampleNum FROM " . 'qc_sample_size_code' . " WHERE id='$id'";
		//echo $sql.'<br>';
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$ret = self :: $dbConn->fetch_array_all($query);
			return $ret[0]['sampleNum'];
		} else {
			self :: $errCode = "2301";
			self :: $errMsg = "getSizeCodeNumById";
			return false;
		}
	}
    
    //取出所有的样本类型
    public static function getSampleType() {
		self :: initDB();
		$sql = "SELECT * FROM " . 'qc_sample_type';
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$ret = self :: $dbConn->fetch_array_all($query);
            return $ret;
		} else {
			self :: $errCode = "2301";
			self :: $errMsg = "getSampleType";
			return false;
		}
	}
    
    //取出所有的样本大小
    public static function getSizeCode() {
		self :: initDB();
		$sql = "SELECT * FROM " . 'qc_sample_size_code ORDER BY sampleNum';
        //echo $sql.'<br>';
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$ret = self :: $dbConn->fetch_array_all($query);
            return $ret;
		} else {
			self :: $errCode = "2401";
			self :: $errMsg = "getSizeCode";
			return false;
		}
	}
    
    //系数表中对应的sampleTypeId和sampleTypeName
    public static function getCoefficientSampleTypeName() {
		self :: initDB();
		$sql = "SELECT a.sampleTypeId,b.typeName FROM qc_sample_coefficient a,qc_sample_type b where a.sampleTypeId=b.id group by a.sampleTypeId";
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$ret = self :: $dbConn->fetch_array_all($query);
            return $ret;
		} else {
			self :: $errCode = "2501";
			self :: $errMsg = "getCoefficientSampleTypeName";
			return false;
		}
	}

}
?>