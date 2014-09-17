<?php
/*
*配货清单组等相关操作
*add by :hws
*/
class GroupRouteModel{	
	public 	static $dbConn;
	public	static $errCode	=	0;
	public	static $errMsg	=	"";
	static  $table			=	"wh_shipping_order_group";
	
	
	//db初始化
	public 	function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
		mysql_query('SET NAMES UTF8');
	}
	
	//获取条件配货清单
	public 	static function getOrderGroup($select,$where){
		self::initDB();
		$sql	 =	"select {$select} from ".self::$table." {$where} ";
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);
			return $ret;	
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

	//插入清单分组表
	public static function insertOrderGroup($data){
		self::initDB();
		$sql    = "INSERT INTO ".self::$table."(sku,skuAmount,shipOrderId,shipOrderGroup,carNumber,todaySequence,user,createdTime,pName) values{$data}";
		$query	=	self::$dbConn->query($sql);
		if($query){
			return true;
		}else{
			self::$errCode	=	"003";
			self::$errMsg	=	"error";
			return false;
		}
	}
	
	//获取索引临时表
	public 	static function getRouteIndex($select,$where){
		self::initDB();
		$sql	 =	"select {$select} from `wh_group_route_index` {$where} ";
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);
			return $ret;	
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"error";
			return false;	
		}
	}
	
	//获取索引临时表数量
	public 	static function getRouteIndexNum($where){
		self::initDB();
		$sql	 =	"select * from `wh_group_route_index` $where";
		$query	 =	self::$dbConn->query($sql);
		if($query){
			$ret =self::$dbConn->num_rows($query);
			return $ret;	
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"error";
			return false;	
		}
	}
	
	//插入临时表
	public static function insertRouteIndex($data){
		self::initDB();
		$sql    = "INSERT INTO `wh_group_route_index`(shipOrderId,level,user) values{$data}";
		$query	=	self::$dbConn->query($sql);
		if($query){
			return true;
		}else{
			self::$errCode	=	"003";
			self::$errMsg	=	"error";
			return false;
		}
	}
	
	//删除临时表
	public static function delRouteIndex($where){
		self::initDB();
		$sql   = "DElETE FROM `wh_group_route_index` ".$where;
		$query = self::$dbConn->query($sql);
		if($query){                        
			return true;
		}else{		
			return false;
		}
	}
	
	//获取指定sku仓位ID
	public static function getSkuPositionID($sku){
		self::initDB();
		$sql   = "select b.positionId from `wh_sku_location` as a left join `wh_product_position_relation` as b
				  on a.id=b.pId where a.sku='$sku' limit 0,1";
		$query = self::$dbConn->query($sql);	
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);
			return $ret;	
		}else{
			self::$errCode = "003";
			self::$errMsg  = "error";
			return false;	
		}
	}
	
	//获取指定sku仓位
	public static function getSkuPosition($where){
		self::initDB();
		$sql   = "select a.sku,b.nums,c.pName from `pc_goods` as a left join `wh_product_position_relation` as b on 
				a.id=b.pId left join `wh_position_distribution` as c on b.positionId=c.id ".$where;
		$query = self::$dbConn->query($sql);	
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);
			return $ret;	
		}else{
			self::$errCode = "003";
			self::$errMsg  = "error";
			return false;	
		}
	}
	
	//获取指定订单料号信息(仓位ID)
	public static function getOrderPositionID($order){
		self::initDB();
		//$sql   = "select a.positionId,a.pName,a.sku,a.amount from `wh_shipping_orderdetail` as a left join `wh_shipping_order` as b
		//		  on a.shipOrderId=b.id where b.id='$order'";
		$sql   = "select id,positionId,pName,sku,amount,storeId,shipOrderId from `wh_shipping_orderdetail` where shipOrderId='$order'";
		$query = self::$dbConn->query($sql);	
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);
			return $ret;	
		}else{
			self::$errCode = "003";
			self::$errMsg  = "error";
			return false;	
		}
	}
	
	//获取指定订单料号信息(仓位ID)
	public static function getOrderPositionIDGroup($order){
		self::initDB();
		//$sql   = "select a.positionId,a.pName,a.sku,a.amount from `wh_shipping_orderdetail` as a left join `wh_shipping_order` as b
		//		  on a.shipOrderId=b.id where b.id='$order'";
		$sql   = "select positionId,pName,sku,sum(amount) as total from `wh_shipping_orderdetail` where shipOrderId='$order' group by pName,sku";
		$query = self::$dbConn->query($sql);	
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);
			return $ret;	
		}else{
			self::$errCode = "003";
			self::$errMsg  = "error";
			return false;	
		}
	}
	
}
?>