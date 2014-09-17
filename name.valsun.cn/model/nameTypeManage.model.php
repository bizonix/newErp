<?php
/**
 * 名称类型管理 nameTypeManage.model.php
 * @author chenwei 2013.11.1
 */
class NameTypeManageModel{	
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
		$sql	 =	"select * from ".self::$table3." {$where}";
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
     * 名称类型管理数据列表显示、搜索、添加
     */
	public 	static function nameTypeManageList($where){
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
     * 系统验证
     */
	public 	static function nameTypeVerify($addNewNameType){
		self::initDB();
		$reInfo  = array();
		$sql	 =	"select * from ".self::$table3." where typeName = '{$addNewNameType}'";
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			$ret =  self::$dbConn->fetch_array_all($query);
			if(empty($ret)){
				$reInfo['reNum'] = "200";
				$reInfo['reStr'] = "此类型名称可以使用!";
			}else{
				if($ret[0]['is_delete'] == 1){
					$reInfo['reNum'] = "400";
					$reInfo['reStr'] = "此类型名称已存在，并已经被废弃，请重新启用！";
				}else{
					$reInfo['reNum'] = "400";
					$reInfo['reStr'] = "此类型名称已存在！请确认！";
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
	public 	static function addNameTypeSubmit($submitStr){
		self::initDB();
		$reInfo      = array();
		$insertSql	 =	"INSERT INTO ".self::$table3." SET ".$submitStr;	
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
	public 	static function delNameType($whereStr){
		self::initDB();
		$reInfo      = array();
		$updateSql	 =	"UPDATE ".self::$table3." SET is_delete = 1 ".$whereStr;	
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
	public 	static function enabledNameType($whereStr){
		self::initDB();
		$reInfo      = array();
		$updateSql	 =	"UPDATE ".self::$table3." SET is_delete = 0 ".$whereStr;	
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
