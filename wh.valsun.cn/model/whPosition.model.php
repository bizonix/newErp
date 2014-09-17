<?php
/*
*类名：仓位管理
*功能：处理仓位信息
*作者：Herman.Xi @ 20140817
*参考黄伟生仓位管理mode
*/
class WhPositionModel{	
	public 	static $dbConn;
	public	static $errCode	=	0;
	public	static $errMsg	=	"";
	static  $table			=	"wh_position_distribution";
	
	//db初始化
	public function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
		mysql_query('SET NAMES UTF8');
	}
	
	//获取条件仓位列表
	public function getPositionList($select,$where){
		self::initDB();
		$sql	 =	"select {$select} from ".self::$table." {$where} ";
		$query	 =	self::$dbConn->query($sql);
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);
			return $ret;	//成功， 返回列表数据
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"error";
			return false;	
		}
	}
	
	/**
	 * 更新一条或多条记录，暂只支持一维数组
	 * @para $data as array
	 $ @where as String
	 */
	public function update($data,$where = ""){
		self::initDB();
		$field = "";
		if(!is_array($field)){
			foreach($data as $k => $v){
				$field .= ",`".$k."` = '".$v."'";
			}
			$field	= ltrim($field,",");
			$sql	= "UPDATE `".self::$table."` SET ".$field." WHERE 1 ".$where;
			$query	=	self::$dbConn->query($sql);
			if($query){                             
				return true;
			} else {			
				return false;
			}
		}
		else {
			return false;
		}
	}

	/**
	 * 插入一条记录
	 * @para $data as array
	 * return insert_id
	 */
	public function insertRow($data){
		self::initDB();
        $sql = array2sql($data);
		$sql = "INSERT INTO `".self::$table."` SET ".$sql;
		$query	=	self::$dbConn->query($sql);
		if($query){
			$insertId = self::$dbConn->insert_id();
			return $insertId;
		}else{
			self::$errCode	=	"003";
			self::$errMsg	=	"error";
			return false;
		}
	}
	
	//删除仓位索引表
	public function delPositonIndex($where){
		self::initDB();
		$sql   = "DElETE FROM `wh_position_index` ".$where;
		$query = self::$dbConn->query($sql);
		if($query){                        
			return true;
		}else{		
			return false;
		}
	}
	
	/**
	 * 插入仓位索引表
	 * @para $data as array
	 * return insert_id
	 */
	public function insertPositonIndex($data){
		self::initDB();
        $sql = array2sql($data);
		$sql = "INSERT INTO `wh_position_index` SET ".$sql;
		$query	=	self::$dbConn->query($sql);
		if($query){
			$insertId = self::$dbConn->insert_id();
			return $insertId;
		}else{
			self::$errCode	=	"003";
			self::$errMsg	=	"error";
			return false;
		}
	}
	
	//获取仓位索引表
	public function getPositonIndexList($select,$where){
		self::initDB();
		$sql	 =	"select {$select} from `wh_position_index` {$where} ";
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);
			return $ret;	//成功， 返回列表数据
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"error";
			return false;	
		}
	}
	
	/**
	*类名：仓位管理
	*功能：更新分区下对应仓位之间的关系
	*作者：Herman.Xi
	*时间：2014-08-17
	*修改：Herman.Xi
	*时间: 2014-08-17
	*/
	public function InitPartition($end_x_alixs,$end_y_alixs){
			
	}

}
?>