<?php
/*
 * 名称管理系统 nameSystem.model.php
 * ADD BY chenwei 2013.10.30
 */
class NameSystemModel{	
	public 	static $dbConn;
	public	static $errCode	=	0;
	public	static $errMsg	=	"";
	static  $table1			=	"nm_namesysteminfo";
	static  $table2 		=	"nm_systemname";
	static  $table3 		=	"nm_valnametype";
		
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
		$sql	 =	"select * from ".self::$table1." {$where}";
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
     * 名称系统管理页面数据列表显示、搜索、添加
     */
	public 	static function nameSysermList($where){
		self::initDB();
		$sql	 =	"select * from ".self::$table1." {$where} ";
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
     * 系统名称
     */
	public 	static function systemNameAllArr($where){
		self::initDB();
		$sql	 =	"select * from ".self::$table2." {$where} ";
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
     * 名称类型
     */
	public 	static function valTypeAllArr($where){
		self::initDB();
		$sql	 =	"select * from ".self::$table3." {$where} ";
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
     * 申请新名称验证
     */
	public 	static function nameSystemVerify($addNewName){
		self::initDB();
		$reInfo  = array();
		$sql	 =	"select * from ".self::$table1." where is_delete != 1 and name = '{$addNewName}'";
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);
			if(empty($ret)){
				$reInfo['reNum'] = "200";
				$reInfo['reStr'] = "此帐号名称可以使用!";
			}else{
				$sql2	 =	"select * from ".self::$table2." where is_delete != 1 and id = {$ret[0]['systemTypeId']}";
				$query2	 =	self::$dbConn->query($sql2);	
				$ret2    =  self::$dbConn->fetch_array_all($query2);
				
				$reInfo['reNum'] = "400";
				$reInfo['reStr'] = "此名称已被其他人使用在[{$ret2[0]['systemName']}]系统!";
			}
			return $reInfo;
		}else{
			self :: $errCode = "4444";
			self :: $errMsg = "mysql:".$sql." error";
			return false;	
		}
	}
	
	/*
     * 插入新名称
     */
	public 	static function addNameSubmit($submitStr){
		self::initDB();
		$reInfo      = array();
		$insertSql	 =	"INSERT INTO ".self::$table1." SET ".$submitStr;	
		if(self::$dbConn->query($insertSql)){
			$reInfo['reNum'] = "200";
			$reInfo['reStr'] = "添加成功！";
			return $reInfo;
		}else{
			$reInfo['reNum'] = "400";
			$reInfo['reStr'] = "mysql:".$sql." error";
			return $reInfo;	
		}
	}
	
	/*
     * 删除
     */
	public 	static function delName($whereStr){
		self::initDB();
		$reInfo      = array();
		$updateSql	 =	"UPDATE ".self::$table1." SET is_delete = 1 ".$whereStr;	
		if(self::$dbConn->query($updateSql)){
			$reInfo['reNum'] = "200";
			$reInfo['reStr'] = "删除成功！";
			return $reInfo;
		}else{
			$reInfo['reNum'] = "400";
			$reInfo['reStr'] = "mysql:".$sql." error";
			return $reInfo;	
		}
	}
		
}
?>
