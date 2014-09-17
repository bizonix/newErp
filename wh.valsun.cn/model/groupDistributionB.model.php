<?php
/*
*配货清单配货表
*ADD BY hws
*/
class GroupDistributionBModel{
	public 	static $dbConn;
	public	static $errCode	=	0;
	public	static $errMsg	=	"";
	static  $table			=	"wh_shipping_order_group_distribution_b";
		
	//db初始化
	public 	function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
		mysql_query('SET NAMES UTF8');
	}
	
	//获取清单配货列表
	public 	static function getGroupDistListB($select,$where){
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

	//插入清单配货表
	public static function insertGroupDistB($data){
		self::initDB();
		$sql    = "INSERT INTO ".self::$table."(shipOrderGroup,sku,groupId,skuAmount,shipOrderId,amount,status,pName,userID,scanTime) values{$data}";
		$query	= self::$dbConn->query($sql);
		if($query){
			return true;
		}else{
			self::$errCode	=	"003";
			self::$errMsg	=	"error";
			return false;
		}
	}
	
	//获取当前配货清单未配货sku信息
	public static function getGroupSkuInfoB($where){
		self::initDB();
		$sql   = "select a.shipOrderGroup,a.groupId,a.sku,a.skuAmount,a.pName from `wh_shipping_order_group_distribution_b` as a where 1 ".$where;		
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
	
	//获取配货单信息
	public 	static function getShipOrder($select,$where){
		self::initDB();
		$sql   = "select {$select} from `wh_shipping_order` {$where} ";
		$query = self::$dbConn->query($sql);	
		if($query){
			$ret = self::$dbConn->fetch_array_all($query);
			return $ret;	
		}else{
			self::$errCode = "003";
			self::$errMsg  = "error";
			return false;	
		}
	}

	//获取sku总库存
	public 	static function getSkuStock($select,$where){
		self::initDB();
		$sql	 =	"select {$select} from `wh_sku_location` {$where} ";
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
	
	//获取sku具体仓位库存
	public 	static function getSkuPositionStock($where){
		self::initDB();
		$sql     =  "select a.nums from `wh_product_position_relation` as a 
					left join `wh_position_distribution` as b on a.positionId=b.id 
					left join `pc_goods` as c on a.pId=c.id where a.type=1 and a.is_delete=0 {$where}";
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
	
	//获取配货单支付信息
	public 	static function getShipOrderPay($select,$where){
		self::initDB();
		$sql   = "select {$select} from `wh_shipping_order` as a left join 
				`wh_shipping_order_group_distribution` as b on a.id=b.shipOrderId {$where} ";
		$query = self::$dbConn->query($sql);	
		if($query){
			$ret = self::$dbConn->fetch_array_all($query);
			return $ret;	
		}else{
			self::$errCode = "003";
			self::$errMsg  = "error";
			return false;	
		}
	}
	
	/**
	 * 更新一条或多条记录，暂只支持一维数组
	 * @para $data as array
	 $ @where as String
	 */
	public static function updateShipOrder($data,$where = ""){
		self::initDB();
		$field = "";
		if(!is_array($field)){
			foreach($data as $k => $v){
				$field .= ",`".$k."` = '".$v."'";
			}
			$field	= ltrim($field,",");
			$sql	= "UPDATE `wh_shipping_order` SET ".$field." WHERE 1 ".$where;
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
}
?>