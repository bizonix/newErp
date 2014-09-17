<?php
/**
 * 类名：PartnerTypeModel
 * 功能：封装供应商管理模块相关的Model
 * 版本：1.0
 * 日期：2013/7/31
 * 作者：任达海
 */
 
class PartnerTypeModel{
	public static $dbConn;
	static $errCode	=	0;
	static $errMsg	=	"";
	static $table	=	"partner_type";
    
    /**
    * 初始化数据库连接
    */
	public static function	initDB() {
		global $dbConn;
		self::$dbConn	=	$dbConn;
	}
	
    /**
    * 获取供应商列表
    * @param     $where    查询条件
    * @param     $limit    分页条件
    * @return    $result   查询到的记录集 
    */
	public static function getPartnerTypeList($where, $field, $limit) {
		self::initDB();
        $sql = "SELECT ".$field." FROM `".C('DB_PREFIX').self::$table."` where `is_delete` = 0 ".$where." ORDER BY id DESC ".$limit;
  		//echo $sql;//
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
		$sql   = "select $field from `".C('DB_PREFIX').self::$table."` where 1 ".$where.$order.$limitStr;
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
    * 判断是否存在记录
    * @para    $data 插入的数组
    * @return  if existed, renturn > 0 else return others
    */
   	public static function IsDataExist($data) {
		self::initDB();
        $sql	=	"select count(*) from `".C('DB_PREFIX').self::$table."` where `category_name` = '$data[category_name]' and `is_delete` = '0' ";            
		$query	=	self::$dbConn->query($sql);
		if($query) {
            $ret	=	self::$dbConn->fetch_array_all($query);
            $num = intval($ret[0]['count(*)']);           
            if($num > 0) {                
                return $num;// exists
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
   	        self::$errCode	=	"003";
			self::$errMsg	=	"类型名称已存在";
            return -1;//exists
        }        
        $sql = array2sql($data);
		$sql = "INSERT INTO `".C('DB_PREFIX').self::$table."` SET ".$sql;          
		$query	=	self::$dbConn->query($sql);
		if($query){
			$affectedrows = self::$dbConn->affected_rows();           
			return $affectedrows;
		} else {
			self::$errCode	=	"004";
			self::$errMsg	=	"出错！位置：".__FUNCTION__." sql= ".$sql;
			return false;
		}
	} 

	/**
	 * 更新一条记录，只支持一维数组
	 * @para    $data 更新记录的数组
	 * @return  if success renturn > 0 else return others
	 */
	public static function update($data, $where = "") {
		self::initDB(); 
        $sql    = array2sql($data);	
		$sql	= "UPDATE `".C('DB_PREFIX').self::$table."` SET ".$sql." where 1 ".$where;        
		$query	=	self::$dbConn->query($sql);
		if($query) {
            return true;				
		} else {
            self::$errCode	= "005";
            self::$errMsg	= "出错！位置：".__FUNCTION__." sql= ".$sql;
            return false;
		}	
	}
    
}

?>