<?php
class OpenapiModel{	
	public 	static $dbConn;
	public	static $errCode	=	0;
	public	static $errMsg	=	"";
	static  $table			=	"pc_goods";
	
	
	//db初始化
	public 	function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
		mysql_query('SET NAMES UTF8');
	}
	
	//获取指定的数据
	public 	static function getGoodsList($fields,$where,$groupBy,$orderBy){
		self::initDB();
		$sql	 =	"select {$fields} from ".self::$table." where {$where} {$groupBy} {$orderBy}";
		$query	 =	self::$dbConn->query($sql);
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);
			return $ret;	//成功， 返回列表数据
		}else{
			self::$errCode =	"001";
			self::$errMsg  =	"获取产品数据失败";
			return false;	//失败则设置错误码和错误信息， 返回false
		}
	}
	/**
	 * 插入一条记录
	 * @para $data as array
	 * return insert_id
	 */
	public static function insertRow($data){
		self::initDB();
        $sql = array2sql($data);
		$sql = "INSERT INTO `".self::$table."` SET ".$sql;
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
	

	
	/**
	 * 更新一条或多条记录，暂只支持一维数组
	 * @para $data as array
	 $ @where as String
	 */
	public static function update($data,$where = ""){
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

	public 	static function getGoodsListNum($where){
		self::initDB();
		$sql	 =	"select * from ".self::$table." $where";
		$query	 =	self::$dbConn->query($sql);
		if($query){
			$ret =self::$dbConn->num_rows($query);
			return $ret;	//成功， 返回列表数据
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"444444444";
			return false;	//失败则设置错误码和错误信息， 返回false
		}
	}
	
	//获取属性
	public 	static function getPropertyList(){
		self::initDB();
		$sql	 =	"select * from pc_goods_property";
		$query	 =	self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->fetch_array_all($query);
			return $ret;	//成功， 返回列表数据
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"444444444";
			return false;	//失败则设置错误码和错误信息， 返回false
		}
	}

	/**
	 * 插入物品与属性关联表
	 * @para $data as String  as:('2','34'),('2','43')
	 */
	public static function insertProRelationRow($data){
		self::initDB();
		$sql = "INSERT INTO `pc_goods_property_relation`(goodsId,propertyId) VALUES".$data;
		$query	=	self::$dbConn->query($sql);
		if($query){
			return true;
		}else{
			self::$errCode	=	"003";
			self::$errMsg	=	"444444444";
			return false;
		}
	}
	
	//根据商品id获取商品属性
	public static function delProRelation($id){
		self::initDB();
		$sql    = "DELETE FROM `pc_goods_property_relation` WHERE goodsId=".$id;
		$query	= self::$dbConn->query($sql);
		if($query){
			return true;
		}else{
			self::$errCode	=	"003";
			self::$errMsg	=	"444444444";
			return false;
		}
	}
	
	//根据商品id获取商品属性及属性名称
	public static function getPropertyRelationList($id){
		self::initDB();
		$sql    = "SELECT a.propertyId,b.propertyName FROM pc_goods_property_relation AS a 
				  LEFT JOIN pc_goods_property as b on a.propertyId=b.id WHERE a.goodsId='{$id}'";
		$query	= self::$dbConn->query($sql);
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);
			return $ret;
		}else{
			self::$errCode	=	"003";
			self::$errMsg	=	"444444444";
			return false;
		}
	}

}
?>