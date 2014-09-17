<?php

/*
 * 配货单配置表Model
 * ADD BY cmf 2014.7.22
 * modify by czq 2014.7.22
 */
class WhWaveConfigModel extends WhBaseModel {
	public static $tablename = 'wh_wave_config';
	
	/**
	 * 获取波次配置列表
	 * @param string $select
	 * @param string $where
	 * @return array
	 * @author czq
	 */
	public static function getWaveConfig(){
		return self::select();
	}
	
	/**
	 * 更新波次配置表一条或多条记录，暂只支持一维数组
	 * @param array $data
	 * @param string $where
	 * @return boolean
	 * @author czq
	 */
	public static function updateWaveConfig($data,$where = ""){
		self::initDB();
		$sql	= "UPDATE ".self::$tablename." SET ".array2sql($data)." WHERE 1 ".$where;
		$query	=	self::$dbConn->query($sql);
		if($query){
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * 波次配置表插入一条记录
	 * @param array $data
	 * @return number $insertId
	 * @author czq
	 */
	public static function insertWaveConfigRow($data){
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
}
?>
