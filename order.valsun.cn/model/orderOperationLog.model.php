<?php
/*
 * 订单操作日志查询  orderOperationLog.model.php
 * ADD BY chenwei 2013.9.9
 */
class OrderOperationLogModel {
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
	 *取得指定表中的指定记录
	 */
	public static function orderOperationLogList($where,$table) {
		self :: initDB();
		$sql	 =	"SELECT * FROM `".$table."` {$where}";
		$query	 =	self::$dbConn->query($sql);	
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);
			return $ret;	
		}else{
			self :: $errCode = "4444";
			self :: $errMsg = "mysql:".$sql." error";
			return false;	
		}
	}

	/*
	 * 获取数据库表明
	 * $sqlStr 数据库名称
	 * $strstrStr 找表名称条件
	 */
	public static function orderTabelNameList($sqlStr,$strstrStr) {
		self :: initDB();
		$retArr  = array();	
		$sql	 =	"{$sqlStr}";
		$query	 =	self::$dbConn->query($sql);	
			
		while ($row = mysql_fetch_row($query)) {
			if(strstr($row[0],$strstrStr)!=false){		
				$retArr[] = $row[0];
			}
		}
			
		if(!empty($retArr)){
			return $retArr;	
		}else{
			self :: $errCode = "4444";
			self :: $errMsg = "data:null";
			return false;	
		}
	}
}
?>
