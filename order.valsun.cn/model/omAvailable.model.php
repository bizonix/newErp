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
	 *取得指定表中的指定记录并且存入到单个数组中
	 */
	public static function getTNameList2arrById($tName, $tId, $select, $where) {
		self :: initDB();
		$sql = "select * from $tName $where";
        //echo $sql.'<br>';
      //  global $memc_obj;
//        $result1 = $memc_obj->get_extral("sku_info_".'001');
//        var_dump($result1);
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$ret = self :: $dbConn->fetch_array_all($query);
			$ret2 = array();
			foreach($ret as $val){
				$ret2[$val[$tId]] = $val[$select];
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
		//echo $sql; echo "<br>";
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
	 *添加指定表记录
	 */
	public static function insertRowUseValue($tName, $key ,$value) {
		self :: initDB();
		$sql = "INSERT INTO $tName($key) values $value";
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
		//echo $sql; echo "\n";
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

	public static function replaceTNameRow2arr($tName, $data){
		self::initDB();
        $sql = array2sql($data);
		$sql = "REPLACE INTO `".$tName."` SET ".$sql;
		$query	=	self::$dbConn->query($sql);
		if($query){
			$insertId = self::$dbConn->insert_id();
			return $insertId;
		}else{
			self::$errCode	=	"003";
			self::$errMsg	=	"444444444";
			return false;
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

	/**
	 *获取每个平台对应的账号列表
	 */
	public static function getPlatformAccount() {
		self :: initDB();
		$sql = "SELECT id,account,platformId from om_account WHERE is_delete=0";
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$data = array();
			$ret = self :: $dbConn->fetch_array_all($query);
			foreach($ret as $v){
				$data[self::getPlatformById($v['platformId'])][$v['id']] = $v['account'];
			}
			return $data; //成功， 返回列表数据
		} else {
			self :: $errCode = "003";
			self :: $errMsg = "修改失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}

	//通过开放系统调用指定接口
	//type=="idc"或type==""
	public static function callOpenSystemByMethod($method,$dataArr,$type=""){
		require_once WEB_PATH."api/include/functions.php";

		$url	= 'http://gw.open.valsun.cn:88/router/rest?';
		$paramArr= array(
			/* API系统级输入参数 Start */
			'method'	=> $method,  //API名称
			'format'	=> 'json',  //返回格式
			'v'			=> '1.0',   //API版本号
			'username'	=> C('OPEN_SYS_USER')
		);
		$paramArr = array_merge($paramArr,$dataArr);
		if($type=="idc"){
			$result 	= callOpenSystem($paramArr);
		}else{
			$result 	= callOpenSystem($paramArr, $url);
		}
		//$result 	= callOpenSystem($paramArr, $url);
		$data 		= json_decode($result, true);
		return $data['data'];
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
