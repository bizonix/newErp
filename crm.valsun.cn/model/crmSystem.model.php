<?php
/*
 * 客户关系管理系统 crmSystem.model.php
 * ADD BY chenwei 2013.9.26
 */
class CrmSystemModel{	
	public 	static $dbConn;
	public	static $errCode	=	0;
	public	static $errMsg	=	"";
	static  $table			=	"crm_clientmanage";

		
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
		$sql	 =	"select * from ".self::$table." {$where}";
		$query	 =	self::$dbConn->query($sql);
		if($query){
			$ret =self::$dbConn->num_rows($query);
			return $ret;	
		}else{
			self::$errCode =	"4444";
			self::$errMsg  =	"mysql:".$sql." error";
			return false;	
		}
	}
		
	
	/*
     * 客户管理页面数据列表显示、搜索
     */
	public 	static function crmSysermList($where){
		self::initDB();
		$sql	 =	"select * from ".self::$table." {$where} ";
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
		
}
?>
