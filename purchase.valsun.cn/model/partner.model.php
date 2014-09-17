<?php


class PartnerModel{
	public static $dbConn;
	static $prefix	=	"";
	static $errCode	=	0;
	static $errMsg	=	"";
    static $table_partner         =	"partner";
    static $table_partner_type	  =	"partner_type";
    //static $table_user            =	"user";
    static $table_user            =	"power_global_user";
    static $table_company         =	"company";

    /**
    * 初始化数据库连接
    */
	public static function	initDB() {
		global $dbConn;
        self::$prefix = C('DB_PREFIX');
		self::$dbConn	=	$dbConn;
	}

    /**
    * 获取供应商列表
    * @param     $where    查询条件
    * @param     $limit    分页条件
    * @return    $result   查询到的记录集
    */
	public static function getPartnerList($where, $field, $limit) {
		self::initDB();
        $sql = "SELECT $field FROM `".C('DB_PREFIX').self::$table_partner."` pp,`".C('DB_PREFIX').self::$table_partner_type."` ppt,`".self::$table_user."` pu,`".C('DB_PREFIX').self::$table_company."` pc WHERE pp.is_delete = 0 ".$where."  AND pp.purchaseuser_id = pu.global_user_id  ORDER BY pp.id DESC ".$limit;
        //$sql = "SELECT $field FROM `".C('DB_PREFIX').self::$table_partner."` pp,`".self::$table_user."` pu,`".C('DB_PREFIX').self::$table_company."` pc WHERE pp.is_delete = 0 ".$where."  AND pp.purchaseuser_id = pu.id AND pp.company_id = pc.id ORDER BY pp.id DESC ".$limit;
        //echo $sql;
        $query	=	self::$dbConn->query($sql);
		if($query) {
			$ret	=	self::$dbConn->fetch_array_all($query);
			return $ret;
		} else {
			self::$errCode	=	"001";
			self::$errMsg	=	"出错！位置：".__FUNCTION__." sql= ".$sql;
			return false;
		}
	}

    /**
    * 获取记录集的通用函数
    * @param     $where    查询条件
    * @param     $field    返回的字段名
    * @param     $order    排序方式
    * @return    $limitStart 分页起始页
    * @abstract  $limit    分页结束页
    * @return    $result   $result > 0 成功，否则失败
    */
	public static function getData($where = "", $field = "*", $order = "", $limitStart = NULL, $limit = NULL){
		self::initDB();
		if($limit > 0) {
			$limitStr = " limit ".$limitStart.",".$limit;
		} else {
			$limitStr = "";
		}
		$sql   = "select $field from `".C('DB_PREFIX').self::$table_partner."` pp,`".C('DB_PREFIX').self::$table_partner_type."` ppt,`".self::$table_user."` pu,`".C('DB_PREFIX').self::$table_company."` pc WHERE pp.is_delete = 0 ".$where." AND pp.purchaseuser_id = pu.global_user_id ".$order.$limitStr;
        $query = self::$dbConn->query($sql);
		if($query) {
			$result = self::$dbConn->fetch_array_all($query);
			return $result;
		} else {
			self::$errCode	= "002";
			self::$errMsg	= "出错！位置：".__FUNCTION__." sql= ".$sql;
			return false;
		}
	}

    /**
    * 获取供应商公司列表
    * @param     $where    查询条件
    * @param     $limit    分页条件
    * @return    $result   查询到的记录集
    */
	public static function getCompanyList($where, $field, $limit = '') {
		self::initDB();
        $sql = "SELECT $field FROM `".C('DB_PREFIX').self::$table_company."` WHERE `is_delete` = 0 ".$where."  ORDER BY id ASC ".$limit;
        //echo $sql;
  		$query	=	self::$dbConn->query($sql);
		if($query) {
			$ret	=	self::$dbConn->fetch_array_all($query);
			return $ret;
		} else {
			self::$errCode	=	"003";
			self::$errMsg	=	"出错！位置：".__FUNCTION__." sql= ".$sql;
			return false;
		}
	}

    /**
    * 获取采购员列表
    * @param     $where    查询条件
    * @param     $limit    分页条件
    * @return    $result   查询到的记录集
    */
	public static function getPurchaserList($where, $field, $limit = '') {
		self::initDB();
        $sql = "SELECT * FROM `".self::$table_user."` WHERE `global_user_is_delete` = 0 ".$where."  ORDER BY global_user_id ASC ".$limit;
  		$query	=	self::$dbConn->query($sql);
		if($query) {
			$ret	=	self::$dbConn->fetch_array_all($query);
			return $ret;
		} else {
			self::$errCode	=	"004";
			self::$errMsg	=	"出错！位置：".__FUNCTION__." sql= ".$sql;
			return false;
		}
	}

    /**
    * 判断是否存在记录
    * @para    $data 插入的数组
    * @return  if existed, renturn > 0 else return others
    */
   	public static function IsDataExist($data) {
		self::initDB();
        $sql	=	"select count(*) from `".C('DB_PREFIX').self::$table_partner."` where `company_name` = '$data[company_name]' and `is_delete` = '0' ";
        $query	=	self::$dbConn->query($sql);
		if($query) {
            $ret	=	self::$dbConn->fetch_array_all($query);
            $num = intval($ret[0]['count(*)']);
            if($num > 0) {
                return $num;//username exists
            }
		}
		return false;
	}

    /**
     * 插入一条记录
     * @para    $data 插入的数组
     * @return  if success renturn > 0 else return others
     */
	public static function insertRow($data) {
		self::initDB();
        $ret = self::IsDataExist($data);
        if($ret > 0) {
       	    self::$errCode	=	"005";
			self::$errMsg	=	"单位名称已存在";
            return false;//username exists
        }
        $sql = array2sql($data);
		$sql = "INSERT INTO `".C('DB_PREFIX').self::$table_partner."` SET ".$sql;
		echo $sql;
		$query	=	self::$dbConn->query($sql);
		if($query){
			$affectedrows = self::$dbConn->affected_rows();
			return $affectedrows;
		} else {
			self::$errCode	=	"006";
			self::$errMsg	=	"出错！位置：".__FUNCTION__." sql= ".$sql;
			return false;
		}
	}

	/**
	 * 更新一条记录，只支持一维数组
	 * @para    $data 更新记录的数组
	 * @return  if success renturn > 0 else return others
	 */
	public static function update($data,$where = "") {
		self::initDB();
        $sql    = array2sql($data);
		$sql	= "UPDATE `".C('DB_PREFIX').self::$table_partner."` SET ".$sql." WHERE 1 ".$where;
		$query	=	self::$dbConn->query($sql);
		if($query) {
            return true;
		} else {
            self::$errCode	= "007";
            self::$errMsg	= "出错！位置：".__FUNCTION__." sql= ".$sql;
            return false;
		}
	}
	/**
	 * 名称:change_sign
	 * 功能:更改签约状态
	 * @param str $status
	 * @param arr $idArr
	 * @return void
	 */
	public static function change_sign($status,$idArr){
		self::initDB();
		$sql = "UPDATE ".self::$prefix."partner SET is_sign = {$status} WHERE is_delete = 0 AND   ";
		$errArr = array();
		foreach($idArr as $idVal){
			$sql .= " id = {$idVal} ";
			$query = self::$dbConn->query($sql);
			if($query){
				$num = self::$dbConn->affected_rows();
				if($num !== 1){
					$errArr[] = $idVal;
				}
			}
		}
		if(count($errArr)>0){
			self::$errMsg = "id 为".implode(',',$errArr)." 审核失败";
			return false;
		}
		self::$errCode = '111';
		self::$errMsg = 'success';
		return true;
	}

	/**
	 * 名称：getOne
	 * 功能:根据公司名称获取一条记录
	 * @param string $company_name
	 * @return array
	 */
	public static function getOne($company_name) {
		self::initDB();
		$sql = "select * from ph_partner where company_name='$company_name' ";
		$sql = self::$dbConn->query($sql);
		return self::$dbConn->fetch_one($sql);
	}

}

?>
