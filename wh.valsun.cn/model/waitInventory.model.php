<?php
/*
*申请盘点记录
*ADD BY hws
*/
class WaitInventoryModel{	
	public 	static $dbConn;
	public	static $errCode	=	0;
	public	static $errMsg	=	"";
	static  $table			=	"wh_waitforInventory_list";
	
	
	//db初始化
	public 	function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
		mysql_query('SET NAMES UTF8');
	}
	
	//获取申请盘点记录列表
	public 	static function getWaitInvList($select,$where){
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
	
	//获取数量
	public 	static function getWaitInvNum($where){
		self::initDB();
		$sql	 =	"select * from ".self::$table." $where";
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
			$insertId = self::$dbConn->insert_id();
			return $insertId;
		}else{
			self::$errCode	=	"003";
			self::$errMsg	=	"error";
			return false;
		}
	}
	
	//完结申请审核订单
	public static function updateInv($sku,$invPeopleId,$storeId=1){
		self::initDB();
		$time 		  =	time();
        $sql   		  = "select actualStock from wh_sku_location where sku='{$sku}' and storeId={$storeId} ";
		$sku_location = self::$dbConn->fetch_first($sql);
		if(empty($sku_location)){
			$invNums = 0;
		}else{
			$invNums = $sku_location['actualStock'];
		}
		$update_sql   = "update wh_waitforInventory_list set invStatus=1,invNums={$invNums},invPeopleId={$invPeopleId},invTime={$time} 
						  where sku='{$sku}' and invStatus=0 and storeId={$storeId}";
		$query		  =	self::$dbConn->query($update_sql);
		if($query){
			return true;
		}else{
			self::$errCode	=	"003";
			self::$errMsg	=	"error";
			return false;
		}
	}

}
?>