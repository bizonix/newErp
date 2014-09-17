<?php 
/*
 * 包材出库
 * @author heminghua
 */
class wrapperSkuOutModel{
	public 	static $dbConn;
	public	static $errCode	=	0;
	public	static $errMsg	=	"";

	//db初始化
	public 	function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
		mysql_query('SET NAMES UTF8');
	}
	/*
	 * 查找料号信息
	 */
	public static function skuinfo($sku){
		self::initDB();
		$sql	 =	"SELECT * FROM pc_goods WHERE sku='$sku'";
		
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			$res = self::$dbConn->fetch_array($query);
			return $res;	
		}else{
			return false;	
		}
	
	}
	public static function selectstock($sku){
		self::initDB();
		$sql	 =	"SELECT * FROM wh_sku_location WHERE sku='$sku'";
		
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			$res = self::$dbConn->fetch_array($query);
			return $res;	
		}else{
			return false;	
		}
	
	}
	public static function updateStock($sku,$num){
		self::initDB();
		$sql 	 =  "UPDATE wh_sku_location SET actualStock=actualStock-{$num} WHERE sku='{$sku}'";
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			
			return true;	
		}else{
			return false;	
		}
	
	}
}
?>	