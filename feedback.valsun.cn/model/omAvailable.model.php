<?php
/*
 * om通用Model
 * ADD BY zqt 2013.9.5
 */
class OmAvailableModel {
	public static $dbConn;
	public static $errCode = 0;
	public static $errMsg = "";

	//db初始化
	public function initDB() {
		global $dbConn;
		self :: $dbConn = $dbConn;
		mysql_query('SET NAMES UTF8');
	}
	/*
	 *取得指定表中的指定记录
	 */
	public static function getTNameList($tName, $select, $where) {
		self :: initDB();
		$sql = "select $select from $tName $where";
        //echo $sql.'<br>';
      //  global $memc_obj;
//        $result1 = $memc_obj->get_extral("sku_info_".'001');
//        var_dump($result1);
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
	
	/*
	 *取得指定表中的指定记录并且存入到单个数组中
	 */
	public static function getTNameList2arr($tName, $select, $where) {
		self :: initDB();
		$sql = "select $select from $tName $where";
        //echo $sql.'<br>';
      //  global $memc_obj;
//        $result1 = $memc_obj->get_extral("sku_info_".'001');
//        var_dump($result1);
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$ret = self :: $dbConn->fetch_array_all($query);
			$ret2 = array();
			foreach($ret as $val){
				$ret2[] = $val[$select];
			}
			return $ret2; //成功， 返回列表数据
		} else {
			self :: $errCode = "001";
			self :: $errMsg = "获取数据失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}

	/*
	 *取得指定表中的指定记录记录数
	 */
	public static function getTNameCount($tName, $where) {
		self :: initDB();
		$sql = "select count(*) count from $tName $where";
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$ret = self :: $dbConn->fetch_array_all($query);
			return $ret[0]['count']; //成功， 返回列表数据
		} else {
			self :: $errCode = "001";
			self :: $errMsg = "获取数据失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}

	/**
	 *添加指定表记录,返回insertId
	 */
	public static function addTNameRow($tName, $set) {
		self :: initDB();
		$sql = "INSERT INTO $tName $set";
        //echo $sql;
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$insertId = self :: $dbConn->insert_id($query);
			return $insertId; //成功， 返回插入的id
		} else {
			self :: $errCode = "002";
			self :: $errMsg = "添加失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}
    
   	/**
	 *添加指定表记录
	 */
	public static function insertRow($tName, $set) {
		self :: initDB();
		$sql = "INSERT INTO $tName $set";
        //echo $sql;
		$query = self :: $dbConn->query($sql);
		if ($query) {
			return TRUE; //成功，
		} else {
			self :: $errCode = "002";
			self :: $errMsg = "添加失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}
    

	/**
	 *修改指定表记录
	 */
	public static function updateTNameRow($tName, $set, $where) {
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
    
    /**
	 *根据平台id取得其名称
	 */
	public static function getPlatformById($id) {
		self :: initDB();
		$sql = "SELECT platform from om_platform WHERE id='$id'";
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$ret = self :: $dbConn->fetch_array_all($query);
			return $ret[0]['platform']; //成功， 返回列表数据
		} else {
			self :: $errCode = "003";
			self :: $errMsg = "修改失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
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
    
}
?>
