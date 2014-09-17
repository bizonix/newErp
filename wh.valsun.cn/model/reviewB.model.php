<?php
/*
*配货清单配货表
*ADD BY hws
*/
class ReviewBModel{
	public 	static $dbConn;
	public	static $errCode	=	0;
	public	static $errMsg	=	"";
	static  $table			=	"wh_order_review_records_b";
		
	//db初始化
	public 	function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
		mysql_query('SET NAMES UTF8');
	}
	
	//获取清单配货列表
	public 	static function getReviewListB($select,$where){
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
	public static function insertReviewB($data){
		self::initDB();
		$sql    = "INSERT INTO ".self::$table."(shipOrderGroup,shipOrderId,sku,totalNums) values{$data}";
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
	public static function updateSnapStock($num,$id){
		self::initDB();
		$sql   = "update ".self::$table." set snapStock=snapStock-'{$num}' where id='{$id}'";		
		$query = self::$dbConn->query($sql);	
		if($query){
			return true;	
		}else{
			self::$errCode = "003";
			self::$errMsg  = "error";
			return false;	
		}
	}
	

}
?>