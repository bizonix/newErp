<?php
/**
 * 系统管理 systemManage.model.php
 * @author chenwei 2013.11.1
 */
class SystemManageModel{	
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
		$sql	 =	"select * from ".self::$table2." {$where}";
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
     * 系统管理数据列表显示、搜索、添加
     */
	public 	static function systemManageList($where){
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
     * 系统验证
     */
	public 	static function systemVerify($addNewSystem){
		self::initDB();
		$reInfo  = array();
		$sql	 =	"select * from ".self::$table2." where systemName = '{$addNewSystem}'";
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			$ret =  self::$dbConn->fetch_array_all($query);
			if(empty($ret)){
				$reInfo['reNum'] = "200";
				$reInfo['reStr'] = "此系统名称可以使用!";
			}else{
				if($ret[0]['is_delete'] == 1){
					$reInfo['reNum'] = "400";
					$reInfo['reStr'] = "此系统名称已存在，并已经被废弃，请重新启用！";
				}else{
					$reInfo['reNum'] = "400";
					$reInfo['reStr'] = "此系统名称已存在！请确认！";
				}
			}
			return $reInfo;
		}else{
			self :: $errCode = "4444";
			self :: $errMsg = "mysql:".$sql." error";
			return false;	
		}
	}
	
	/*
     * 插入系统
     */
	public 	static function addSystemSubmit($submitStr){
		self::initDB();
		$reInfo      = array();
		$insertSql	 =	"INSERT INTO ".self::$table2." SET ".$submitStr;	
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
     * 废弃
     */
	public 	static function delSystem($whereStr){
		self::initDB();
		$reInfo      = array();
		$updateSql	 =	"UPDATE ".self::$table2." SET is_delete = 1 ".$whereStr;	
		if(self::$dbConn->query($updateSql)){
			$reInfo['reNum'] = "200";
			$reInfo['reStr'] = "废弃成功！将变成灰色，欲再用，请启用！";
			return $reInfo;
		}else{
			$reInfo['reNum'] = "400";
			$reInfo['reStr'] = "mysql:".$sql." error";
			return $reInfo;	
		}
	}
	
	/*
     * 启用
     */
	public 	static function enabledSystem($whereStr){
		self::initDB();
		$reInfo      = array();
		$updateSql	 =	"UPDATE ".self::$table2." SET is_delete = 0 ".$whereStr;	
		if(self::$dbConn->query($updateSql)){
			$reInfo['reNum'] = "200";
			$reInfo['reStr'] = "启用成功！";
			return $reInfo;
		}else{
			$reInfo['reNum'] = "400";
			$reInfo['reStr'] = "mysql:".$sql." error";
			return $reInfo;	
		}
	}
		
}
?>
