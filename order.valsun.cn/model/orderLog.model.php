<?php
/*
 * 名称：OrderLog
 * 功能：订单状态日志，操作日志
 * 版本：v 1.0
 * 日期：2013/12/10
 * 作者：Herman.xi
 * */
class OrderLogModel{
	public 	static $dbConn;
	public	static $errCode	=	0;
	public	static $errMsg	=	'';
	public	static $orderLogTable	=	'om_order_log_';
	
	public static function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
		mysql_query('SET NAMES UTF8');
	}
	
	/*
	 * 插入超大订单拆分记录(最新版)
	 * last modified by Herman.Xi @20131205
	 */
	public static function insertOrderLog($omOrderId, $note){
		!self::$dbConn ? self::initDB() : null;
		//om_order_log
		$data = array('operatorId'=>$_SESSION['sysUserId'], 'omOrderId'=>$omOrderId, 'note' => $note, 'createdTime'=>time());
        $string = array2sql($data);
		//var_dump($string); exit;
		$strmctime = date('Y_m', time());
		$sql = "INSERT INTO `".self::$orderLogTable.$strmctime."` SET ".$string;
		//echo $sql; exit;
		$query	=	self::$dbConn->query($sql);
		if($query){
			self :: $errCode = "200";
			self :: $errMsg = "插入成功";
			return true;
		}else{
			self :: $errCode = "001";
			self :: $errMsg = "获取数据失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}
	
	public static function orderLog($orderid,$ss,$note,$orderType=""){
		!self::$dbConn ? self::initDB() : null;
		$where = "where id={$orderid}";
		$orderinfo = OmAvailableModel::getTNameList("om_unshipped_order","*",$where);
		$sql = array();
		$sql['operatorId'] = $_SESSION['sysUserId'];
		$sql['omOrderId'] = $orderid;
		//$sql['note'] = "编辑订单";
		$sql['note'] = $note;
		$sql['sql'] = mysql_real_escape_string($ss);
		$sql['createdTime'] = time();
		if(!empty($orderType) && $orderinfo[0]['orderType']!=$orderType){
			$sql['oldStatus'] = $orderinfo[0]['orderType'];
			$sql['newStatus'] = $orderType;
			$sql['note'] .= "修改订单状态";
		}
		$strmctime = date('Y_m', time());
		$sql = "INSERT INTO om_order_log_".$strmctime." set ".array2sql($sql);

		$sql		= self::$dbConn->query($sql);
		if($sql){
			return true;
		}else{
			return false;
			//echo $sql;
		}
			
	}
	
}
?>