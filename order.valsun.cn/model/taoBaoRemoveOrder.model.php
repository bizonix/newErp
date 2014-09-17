<?php
/*
* 淘宝刷单操作
* ADD BY chenwei 2013.9.12
*/
class TaoBaoRemoveOrderModel{	
	public 	static $dbConn;
	public	static $errCode	=	0;
	public	static $errMsg	=	"";

	//db初始化
	public static function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
		mysql_query('SET NAMES UTF8');
	}
	
	//标记为"淘宝刷单"
	public static function taoBaoRemoveOrder($orderIdArr){
		self::initDB();
		self::$dbConn->begin();//事物
		
		$serchSql 		  = "SELECT * FROM om_unshipped_order WHERE id in ('".join("','",$orderIdArr)."') and is_delete = 0 ";
		$querySql 		  =  self::$dbConn->query($serchSql);
		$serchSqlArr	  =  self::$dbConn->fetch_array_all($querySql);
		
		//判断一：判断订单是否被删除
		if(count($serchSqlArr) < count($orderIdArr)){
			self :: $errCode = "1111";
			self :: $errMsg  = "包含已经被删除的订单！请确认。";
			return false;
		}
		
		foreach($serchSqlArr as $selectArr){
			
			//判断二：订单被其他人 <锁定> 订单判断
			if($selectArr['isLock'] == 1 && $selectArr['lockUser'] != $_SESSION['sysUserId']){
				self :: $errCode = "1111";
				self :: $errMsg  = "订单：[".$selectArr['id']."]已经被 [".$selectArr['lockUser']."] 锁定，不能操作。";
				return false;
			}
			
			//判断三：已经是‘淘宝刷单’的订单跳过
			if($selectArr['orderStatus'] == 100 && $selectArr['orderType'] == 108){
				continue;
			}

			//移动操作
			$updateSql = "UPDATE om_unshipped_order SET orderStatus = 100,orderType = 108 WHERE id = {$selectArr['id']}";
			if(!self::$dbConn->query($updateSql)){
				self::$dbConn->rollback();//事物回滚
				self :: $errCode = "0000";
				self :: $errMsg  = "mysq: ".$updateSql." error!";
				return false;
			}
			
			//移动日志添加   后期 会有公共方法插入  现在是写死了
			/*$insertSql = "INSERT INTO `om_order_log_2013-09_2013-12` (`operatorId`,`omOrderId`,`oldStatus`,`newStatus`,`sql`,`createdTime`)VALUES('".$_SESSION['sysUserId']."','".$selectArr['id']."','".$selectArr['orderType']."','108','".addslashes($updateSql)."','".time()."')";
			if(!self::$dbConn->query($insertSql)){
				self::$dbConn->rollback();//事物回滚
				self :: $errCode = "0000";
				self :: $errMsg  = "mysq: ".$insertSql." error!";
				return false;
			}*/
	
		}
		
		self::$dbConn->commit();//事物提交
		self :: $errCode = "200";
		self :: $errMsg  = "<淘宝刷单>标记成功！";
		return true;
				
	}
	
}
?>