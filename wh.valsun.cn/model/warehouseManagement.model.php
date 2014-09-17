<?php
/*
 * 仓库基础信息管理(model)
 * ADD BY chenwei 2013.8.13
 */
class WarehouseManagementModel{	
	public 	static $dbConn;
	public	static $errCode	=	0;
	public	static $errMsg	=	"";
	static  $table			=	"wh_store"; //仓库管理列表
	static  $table2			=	"wh_iotype"; //出入库类型管理表
	static  $table3			=	"wh_invoice_type"; //出入库单据类型管理表
		
	//db初始化
	public 	function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
		mysql_query('SET NAMES UTF8');
	}
	
	/*
     * 分页总数
     */
	public 	static function getPageNum($where){
		self::initDB();
		$sql	 =	"select * from ".self::$table2." {$where}";
		//echo $sql;exit;
		$query	 =	self::$dbConn->query($sql);
		if($query){
			$ret =self::$dbConn->num_rows($query);
			return $ret;	//成功， 返回列表数据
		}else{
			self::$errCode =	"4444";
			self::$errMsg  =	"mysql:".$sql." error";
			return false;	
		}
	}
	
	/*
     * 仓库名称管理数据查询
     */
	public 	static function warehouseManagementModelList($where=''){
		self::initDB();
		$sql	 =	"select * from ".self::$table." {$where}";
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);
			return $ret;	
		}else{
			self :: $errCode = "4444";
			self :: $errMsg = "mysql:".$sql." error";
			return false;	
		}
	}
	
	/*
     * 验证模块 $table			=	"wh_store";
     */
	public 	static function existModel($where){
		self::initDB();
		$sql	 =	"select * from ".self::$table." {$where}";
		$query	 =	self::$dbConn->query($sql);		
		if ($query) {
			$ret = self :: $dbConn->fetch_array_all($query);
			return $ret;
		} else {
			self :: $errCode = "4444";
			self :: $errMsg = "mysql:".$sql." error";
			return false;
		}
	}
	
	
	/*
     * 插入、修改
     */
	public static function warehouseSubmit($where,$type) {
		self :: initDB();
		if($type == "add"){
			$sql = "INSERT INTO " . self :: $table . " $where";
		}else if($type == "edit"){
			$sql = "UPDATE " . self :: $table . " $where";
		}		
		//echo $sql;exit;
		$query	 =	self::$dbConn->query($sql);	
		if ($query) {
			return true;
		} else {
			self :: $errCode = "4444";
			self :: $errMsg = "mysql:".$sql." error";
			return false;
		}
	}
	
	/*
     * 出入库类型管理数据查询
     */
	public 	static function whIoTypeModelList($where){
		self::initDB();
		$sql	 =	"select * from ".self::$table2." {$where}";
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);
			return $ret;	
		}else{
			self :: $errCode = "4444";
			self :: $errMsg = "mysql:".$sql." error";
			return false;	
		}
	}
	
	/*
     * 验证模块 $table2			=	"wh_iotype"
     */
	public 	static function whIoTypeExistModel($where){
		self::initDB();
		$sql	 =	"select * from ".self::$table2." {$where}";
		$query	 =	self::$dbConn->query($sql);		
		if ($query) {
			$ret = self :: $dbConn->fetch_array_all($query);
			return $ret;
		} else {
			self :: $errCode = "4444";
			self :: $errMsg = "mysql:".$sql." error";
			return false;
		}
	}
	
	/*
     * 出入库类型 添加、编辑
     */
	public static function whIoTypeSubmit($where,$type) {
		self :: initDB();
		if($type == "add"){
			$sql = "INSERT INTO " . self :: $table2 . " $where";
		}else if($type == "edit"){
			$sql = "UPDATE " . self :: $table2 . " $where";
		}		
		//echo $sql;exit;
		$query	 =	self::$dbConn->query($sql);	
		if ($query) {
			return true;
		} else {
			self :: $errCode = "4444";
			self :: $errMsg = "mysql:".$sql." error";
			return false;
		}
	}
	
	/*
     * 出入库类型删除
     */
	public 	static function whIoTypeDel($where){
		self::initDB();
		$sql	 =	"DELETE FROM ".self::$table2." {$where}";
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			return true;	
		}else{
			self :: $errCode = "4444";
			self :: $errMsg = "mysql:".$sql." error";
			return false;	
		}
	}
	
	/*
     * 出入库单据类型管理数据查询
     */
	public 	static function whIoInvoicesTypeModelList($where){
		self::initDB();
		$sql	 =	"select * from ".self::$table3." {$where}";
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);
			return $ret;	
		}else{
			self :: $errCode = "4444";
			self :: $errMsg = "mysql:".$sql." error";
			return false;	
		}
	}
	
	/*
     * 出入库单据类型 添加、编辑
     */
	public static function whIoInvoicesTypeSubmit($where,$type) {
		self :: initDB();
		if($type == "add"){
			$sql = "INSERT INTO " . self :: $table3 . " $where";
		}else if($type == "edit"){
			$sql = "UPDATE " . self :: $table3 . " $where";
		}		
		$query	 =	self::$dbConn->query($sql);	
		if ($query) {
			return true;
		} else {
			self :: $errCode = "4444";
			self :: $errMsg = "mysql:".$sql." error";
			return false;
		}
	}
	
	/*
     * 单据验证 
     */
	public 	static function whIoInvoicesTypeExistModel($where){
		self::initDB();
		$sql	 =	"select * from ".self::$table3." {$where}";
		$query	 =	self::$dbConn->query($sql);		
		if ($query) {
			$ret = self :: $dbConn->fetch_array_all($query);
			return $ret;
		} else {
			self :: $errCode = "4444";
			self :: $errMsg = "mysql:".$sql." error";
			return false;
		}
	}
	
	/*
     * 单据类型删除
     */
	public 	static function whIoInvoicesTypeDel($where){
		self::initDB();
		$sql	 =	"DELETE FROM ".self::$table3." {$where}";
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			return true;	
		}else{
			self :: $errCode = "4444";
			self :: $errMsg = "mysql:".$sql." error";
			return false;	
		}
	}
	
}
?>
