<?php
/*
*邮局退回
*ADD BY hws
*/
class PostReturnModel{	
	public 	static $dbConn;
	public	static $errCode	=	0;
	public	static $errMsg	=	"";
	static  $table			=	"wh_order_postReturn";
	
	
	//db初始化
	public 	function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
		mysql_query('SET NAMES UTF8');
	}
	
	//获取邮局退回列表
	public 	static function getReturnList($select,$where){
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
			return true;
		}else{
			self::$errCode	=	"003";
			self::$errMsg	=	"error";
			return false;
		}
	}
	
	/**
	 *更新更新表良品数
	 */
	public static function updateIchibanNums($shipOrderId, $sku, $ichibanNums) {
		self :: initDB();
		$sql = "update `".self::$table."` set ichibanNums={$ichibanNums} where shipOrderId='$shipOrderId' and sku='$sku'";
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$affectRows = self :: $dbConn->affected_rows($query);
			return $affectRows; //成功， 返回列表数据
		} else {
			self :: $errCode = "003";
			self :: $errMsg = "修改失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}
	
	//更新上架状态
	public static function updateReturnStatus($id,$num){
		self::initDB();
		$sql	 =	"UPDATE `".self::$table."` SET status=1,shelvesNums=shelvesNums+{$num} WHERE id={$id}";
		//echo $sql;
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			return true;	
		}else{
			return false;	
		}
	
	}
	public static function updateReturnShelfNum($id,$num){
		self::initDB();
		$sql	 =	"UPDATE `".self::$table."` SET shelvesNums=shelvesNums+{$num} WHERE id={$id}";
		//echo $sql;
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			return true;	
		}else{
			return false;	
		}
	
	}

}
?>