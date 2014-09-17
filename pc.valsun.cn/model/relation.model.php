<?php
class RelationModel{
	public static $dbConn;
	static $errCode	=	0;
	static $errMsg	=	"";
	static $table	=	"";
	
	public  function	initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
		
	}
	
	/**
	 * 读取仓库信息
	 * @para $where 
	 * return array
	 */
	public static function getWarehouse($where){
		self::initDB();
		if(!empty($where)){
			$sql   = "select * from wh_warehouse where 1 ".$where;
			$query = self::$dbConn->query($sql);
			if($query){
				$ret = self::$dbConn->fetch_array_all($query);
				return $ret;
			}else{
				self::$errCode =	"003";
				self::$errMsg  =	"444444444";
				return false;
			}
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"444444444";
			return false;
		}	
	}
	
	/**
	 * 插入一条物品与仓位关联表记录
	 * @para $data as array
	 * return insert_id
	 */
	public static function insertGoodsWhRow($data){
		self::initDB();
        $sql = array2sql($data);
		$sql = "INSERT INTO `pc_goods_location_relation` SET ".$sql;
		$query	=	self::$dbConn->query($sql);
		if($query){
			$insertId = self::$dbConn->insert_id();
			return $insertId;
		}else{
			self::$errCode	=	"003";
			self::$errMsg	=	"444444444";
			return false;
		}
	}
	

}
?>