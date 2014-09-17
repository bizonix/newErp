<?php
/*
*小包复核
*ADD BY hws
*/
class OrderReviewModel{	
	public 	static $dbConn;
	public	static $errCode	=	0;
	public	static $errMsg	=	"";
	static  $table			=	"wh_order_review_records";
	
	
	//db初始化
	public 	function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
		mysql_query('SET NAMES UTF8');
	}
	
	//获取复核信息
	public 	static function getReviewList($select,$where){
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
	
	/**
	 *修改指定表记录
	 */
	public static function updateRow($set,$where) {
		self :: initDB();
		$sql = "UPDATE `".self::$table."` $set $where";
		$query = self :: $dbConn->query($sql);
		if ($query) {
			return true;
		} else {
			self :: $errCode = "003";
			self :: $errMsg = "修改失败";
			return false; 
		}
	}
	
	/**
	 * 插入记录
	 * @para $string as string
	 */
	public static function insert($string){
		self::initDB();
		$sql   = "INSERT INTO `".self::$table."` (shipOrderId,sku,goodsName,amount,totalNums,scanUserId,scanTime,isScan) values{$string}";
		$query = self::$dbConn->query($sql);
		if($query){
			return true;
		}else{
			self::$errCode	=	"003";
			self::$errMsg	=	"error";
			return false;
		}
	}
	
	//获取sku信息
	public 	static function getSkuInfo($select,$where){
		self::initDB();
		$sql	 =	"select {$select} from `pc_goods` {$where} ";
		$query	 =	self::$dbConn->fetch_first($sql);		
		if($query){
			return $query;	//成功， 返回列表数据
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"error";
			return false;	
		}
	}
}
?>