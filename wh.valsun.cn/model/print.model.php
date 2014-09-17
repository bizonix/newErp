<?php
/*
*打印地址条操作(model)
*add by Herman.Xi
*2013-08-27
*/
class printModel{
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
	 * 插入打标队列记录
	 */
	public static function insertPrintGroup($orderids,$storeId=1){
		self::initDB();
		self::$dbConn->begin();
		$orderidsArr = explode(",",$orderids);
		$sql = "select * from wh_shipping_order where orderStatus = 400 and id in (".join(",",$orderidsArr).") and is_delete = 0 ";
		$query	 =	self::$dbConn->query($sql);
		if(self::$dbConn->num_rows($query) < count($orderidsArr)){
			return false;
		}
		$sql	 =	"INSERT INTO wh_order_printing_list(orderIds,applicantId,applicantTime,status,storeId) VALUES('".join(",",$orderidsArr)."',{$_SESSION['userId']},".time().", 1001,{$storeId})";
		//echo $sql;
		$query	 =	self::$dbConn->query($sql);
		if($query){
			$sql = "update wh_shipping_order set orderStatus = 401 where id in ({$orderids}) and storeId = {$storeId} ";
			/*
			foreach($orderidsArr as $oid){
				WhPushModel::pushOrderStatus($oid,'STATESHIPPED_PRINTPEND',$_SESSION['userId'],time());
			}*/
			if(self::$dbConn->query($sql)){
				self::$dbConn->commit();
				return true;
			}else{
				self::$dbConn->rollback();
				return false;
			}
		}else{
			self::$dbConn->rollback();
			return false;	
		}
		
	} 
}
?>