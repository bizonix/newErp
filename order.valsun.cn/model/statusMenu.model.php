<?php
/*
*状态值
*ADD BY hws
*/
class StatusMenuModel{	
	public 	static $dbConn;
	public	static $errCode	=	0;
	public	static $errMsg	=	"";
	static  $table			=	"om_status_menu";
	
	//db初始化
	public 	function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
		mysql_query('SET NAMES UTF8');
	}
	
	//获取流程状态列表
	public 	static function getStatusMenuList($select,$where){
		self::initDB();
		$sql	 =	"select {$select} from ".self::$table." {$where} ";
		//echo $sql;
		$query	 =	self::$dbConn->query($sql);
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);
			self::$errCode =	"200";
			self::$errMsg  =	"success";
			return $ret;	//成功， 返回列表数据
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"error";
			return false;	
		}
	}
	
	//获取流程状态列表
	public 	static function getStatusMenuListById($select,$where){
		self::initDB();
		$sql	 =	"select {$select} from ".self::$table." {$where} ";
		//echo $sql;
		$query	 =	self::$dbConn->query($sql);
		$arr	 =	array();
		while($row = mysql_fetch_array($query, MYSQL_ASSOC)){
			$arr[$row['statusCode']] = $row['statusName'];
		}
		self::$errCode =	"200";
		self::$errMsg  =	"success";
		return $arr;
	}
	
	//获取待发货订单数据
	public 	static function getWaitingsend(){
		self::initDB();
		$select = "statusCode";
		$where = " WHERE groupId = 0 AND dStatus = 1";
		$ret	 =	self::getStatusMenuList($select,$where);
		//echo $sql;
		//$query	 =	self::$dbConn->query($sql);		
		if($ret){
			$arr = array();
			foreach($ret as $value){
				$arr[] = $value['statusCode'];
			}
			self::$errCode =	"200";
			self::$errMsg  =	"success";
			return $arr;	//成功， 返回列表数据
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"error";
			return false;	
		}
	}
	
	//获取超大订单数据
	public 	static function getBigOrder(){
		self::initDB();
		$select = "statusCode";
		$where = " WHERE groupId = 0 AND dStatus = 2";
		$ret	 =	self::getStatusMenuList($select,$where);
		//echo $sql;
		//$query	 =	self::$dbConn->query($sql);
		if($ret){
			$arr = array();
			foreach($ret as $value){
				$arr[] = $value['statusCode'];
			}
			self::$errCode =	"200";
			self::$errMsg  =	"success";
			return $arr;	//成功， 返回列表数据
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"error";
			return false;	
		}
	}
	
	//获取待审核状态码
	public 	static function getPendingAuditOrder(){
		$arr[] = (string)C('STATEOVERSIZEDORDERS_PEND');
		self::$errCode =	"200";
		self::$errMsg  =	"success";
		return $arr;	//成功， 返回列表数据
	}
	
	//获超大拦截状态码
	public 	static function getLargeInterceptOrder(){
		$arr[] = (string)C('STATEOVERSIZEDORDERS_WB');
		self::$errCode =	"200";
		self::$errMsg  =	"success";
		return $arr;	//成功， 返回列表数据
	}
	
	//获取拦截订单数据
	public 	static function getInterceptsend(){
		self::initDB();
		$select = "statusCode";
		$where = " WHERE groupId = 0 AND dStatus = 5";
		$ret	 =	self::getStatusMenuList($select,$where);
		//echo $sql;
		//$query	 =	self::$dbConn->query($sql);		
		if($ret){
			$arr = array();
			foreach($ret as $value){
				$arr[] = $value['statusCode'];
			}
			self::$errCode =	"200";
			self::$errMsg  =	"success";
			return $arr;	//成功， 返回列表数据
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"error";
			return false;	
		}
	}
	
	/**
	 * 更新一条或多条记录，暂只支持一维数组
	 * @para $data as array
	 * @where as String
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
			$insertId = self::$dbConn->insert_id();
			return $insertId;
		}else{
			self::$errCode	=	"003";
			self::$errMsg	=	"error";
			return false;
		}
	}
	
	/**
	 * 根据状态获取读取状态列表(最新版)
	 * last modified by Herman.Xi @20131205
	 $ @where as String
	 */
	public 	static function getOrderNameByStatus($ostatus, $otype, $storeId = 1){
		self::initDB();
		if(!$ostatus && !$otype){//预防状态不选择，如何选择分表搜索的情况@20140226
			return 'om_unshipped_order';
		}
		$select = 'oType';
		//echo $ostatus.'--'.$otype;
		if($otype){
			$where = ' where statusCode = '.$otype;
		}else{
			$where = ' where statusCode = '.$ostatus;
		}
		$where .= ' and storeId = '.$storeId.' and is_delete = 0 ';
		$sql	 =	"select {$select} from ".self::$table." {$where} ";
		$query	 =	self::$dbConn->query($sql);
		if($query){
			$ret =self::$dbConn->fetch_array($query);
			if($ret['oType'] == 1){
				$orderTable = 'om_unshipped_order';
			}else{
				$orderTable = 'om_shipped_order';
			}
			return $orderTable;	//成功， 返回列表数据
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"error";
			return false;	
		}
	}
	
	/**
	 * 根据老系统状态获取读取新系统状态
	 * last modified by Herman.Xi @20140509
	 $ @where as String
	 */
	public 	static function getStatusMenuByOldStatus($oldStatus){
		self::initDB();
		$sql	=	"select statusCode,groupId from ".self::$table." where oldStatusCode = {$oldStatus} ";
		//echo $sql;
		$query	=	self::$dbConn->query($sql);
		$ret 	= 	self::$dbConn->fetch_array($query);
		return $ret ? array($ret['groupId'],$ret['statusCode']) : array();
	}
	
}
?>