<?php
/**
 * 名称：NoticeModel
 * 功能： 查询消息纪录
 * 版本：v1.0
 * 日期：2013/10/09
 * 作者：Ren da hai
 * */
class NoticeModel{
	public static $dbConn;
	static $errCode	=	0;
	static $errMsg	=	"";
	private static $tabemail	=	"nt_email";
	private static $tabsms		=	"nt_sms";

	public static function	initDB(){
		global $dbConn;
		self::$dbConn	=	$dbConn;
	}	
	
    public static function getNoticeList($where, $limit, $table="nt_email") {
		self::initDB();
        if($table == 'nt_email') {		
            $sql = "SELECT * FROM `".self::$tabemail."` WHERE 1 ".$where." AND `is_delete` = 0 ORDER BY id DESC ".$limit;
		} else {		
            $sql = "SELECT * FROM `".self::$tabsms."` WHERE 1 ".$where." AND `is_delete` = 0 ORDER BY id DESC ".$limit;
		} 
		$query	=	self::$dbConn->query($sql);
		if($query) {
			$ret	=	self::$dbConn->fetch_array_all($query);
			return $ret;
		} else {
            self::$errCode	=	"003";
            self::$errMsg	=	"Error occurred！Function=".__FUNCTION__." sql= ".$sql;
            return false;
		}
	}    
    
	public static function getData($where = "", $field = "*", $order = "", $limitStart = NULL, $limit = NULL, $table="nt_email") {
		self::initDB();
		if($limit > 0) {
			$limitStr = " LIMIT ".$limitStart.",".$limit;
		} else {
			$limitStr = "";
		}        
        if($table=='nt_email') {           
            $sql = "SELECT $field FROM `".self::$tabemail."` WHERE 1  AND `is_delete` = 0 ".$where.$order.$limitStr;            
		} else {           
            $sql = "SELECT $field FROM `".self::$tabsms."` WHERE 1  AND `is_delete` = 0 ".$where.$order.$limitStr;
		}        
		$query = self::$dbConn->query($sql);
		if($query) {
			$result = self::$dbConn->fetch_array_all($query);
			return $result;
		} else {
            self::$errCode	=	"004";
            self::$errMsg	=	"Error occurred！Function=".__FUNCTION__." sql= ".$sql;
            return false;
		}
	} 
    
   	public static function update($data, $where = "", $table="nt_email") {
		self::initDB(); 
        $sql = array2sql($data);        
        if($table == 'nt_email') {               
           	$sql = "UPDATE `".self::$tabemail."` SET ".$sql." WHERE 1 ".$where;           
		} else {              
           	$sql = "UPDATE `".self::$tabsms."` SET ".$sql." WHERE 1 ".$where;
		}       
		$query	=	self::$dbConn->query($sql);
		if($query){
            return true;				
		} else {
            self::$errCode	=	"005";
            self::$errMsg	=	"Error occurred！Function=".__FUNCTION__." sql= ".$sql;
            return false;
		}
	} 
}
?>