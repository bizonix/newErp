<?php
/*
*仓位相关信息
*add by :hws
*/
class WhAreaModel{
	public 	static $dbConn;
	public	static $errCode	=	0;
	public	static $errMsg	=	"";
	static  $table			=	"wh_wave_area_info";
	
	
	//db初始化
	public 	function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
		mysql_query('SET NAMES UTF8');
	}
	
	//获取条件区域列表
	public function getAreaList($select="*",$where=""){
		self::initDB();
		$sql	 =	"select {$select} from ".self::$table." {$where} ";
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);
			self::$errCode =	"200";
			self::$errMsg  =	"获取区域列表成功";
			return $ret;	//成功， 返回列表数据
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"获取区域列表失败";
			return false;	
		}
	}
	
	//获取条件区域列表
	public function getAreaListToArray($select="*",$where=""){
		$ret = $this->getAreaList($select,$where);
		$arr = array();
		foreach($ret as $val){
			$arr[] = $val['areaName'];
		}
		//var_dump($arr);
		self::$errCode =	"200";
		self::$errMsg  =	"获取区域成功";
		return $arr;
	}
	
	//获取条件区域列表
	public function addArea($data){
		self::initDB();
		$extral_sql = array2sql($data);
		$sql	 =	"insert into ".self::$table." set {$extral_sql} ";
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			self::$errCode =	"200";
			self::$errMsg  =	"新增成功";
			return $query;	//插入成功
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"新增失败";
			return false;	//插入失败
		}
	}
	
	//更新仓位列表
	public 	static function updateAreaList($data,$where){
		self::initDB();
		$extral_sql = array2sql($data);
		$sql	 =	"update ".self::$table." set {$extral_sql} {$where} ";
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			self::$errCode =	"200";
			self::$errMsg  =	"更新成功";
			return $query;	//插入成功
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"更新失败";
			return false;	//插入失败
		}
	}
	
}