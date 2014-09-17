<?php

/*
 * 配货单对应颜色表Model
 * ADD BY cmf 2014.7.22
 * modify by czq 2014.7.22
 */
class WhWaveColorModel extends WhBaseModel {
	public static $tablename = 'wh_wave_color';
	
	/**
	 * 获取箱子颜色
	 * @param string $select
	 * @param string $where
	 * @return array
	 * @author czq
	 */
	public static function getWaveBoxColor(){
		return self::select();
	}
	
	/**
	 * 箱子颜色表插入一条记录
	 * @param array $data
	 * @return number $insertId
	 * @author czq
	 */
	public static function insertWaveBoxColorRow($data){
		self::initDB();
		$sql = "INSERT INTO ".self::$tablename." SET ".array2sql($data);
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
	
	/**
	 * 更新箱子颜色表一条或多条记录，暂只支持一维数组
	 * @param array $data
	 * @param string $where
	 * @return boolean
	 * @author czq
	 */
	public static function updateWaveBoxColor($data,$where = ""){
		self::initDB();
		$sql	= "UPDATE ".self::$tablename." SET ".array2sql($data)." WHERE 1 ".$where;
		$query	=	self::$dbConn->query($sql);
		if($query){
			return true;
		} else {
			return false;
		}
	}
}
?>
