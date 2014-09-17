<?php
/*
 * 名称：SendReplacementModel
 * 功能：订单修改查看操作
 * 版本：v 1.0
 * 日期：2013/09/12
 * 作者：zyp
 * */
class SendReplacementModel{
	public 	static $dbConn;
	public	static $errCode	=	0;
	public	static $errMsg	=	"";
	public static $dbPrefix = "";
	
	public static function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
		self::$dbPrefix = "om_";
		mysql_query('SET NAMES UTF8');
	}
	
	/**
	 *修改指定表记录(只要sql运行成功就算成功)
	 */
	public static function getSendReplacementType() {
		self :: initDB();
		//$string = array2sql($data);
		$tName = 'om_sendReplacement_type';
		$sql = "SELECT * FROM $tName ";
		//echo $sql; exit;
		$query = self :: $dbConn->query($sql);
		$ret = array();
		$order_info = self::$dbConn->fetch_array_all($query);
		foreach($order_info as $value){
			$ret[$value['id']] = $value['typeName'];
		}
		//var_dump($ret);
		if ($ret) {
			self :: $errCode = "200";
			self :: $errMsg = "获取成功";
		} else {
			self :: $errCode = "003";
			self :: $errMsg = "获取失败";
		}
		return $ret; //成功， 返回真
	}
	
	/**
	 *修改指定表记录(只要sql运行成功就算成功)
	 */
	public static function getSendReplacementReason() {
		self :: initDB();
		//$string = array2sql($data);
		$tName = 'om_sendReplacement_reason';
		$sql = "SELECT * FROM $tName ";
		//echo $sql; exit;
		$query = self :: $dbConn->query($sql);
		$ret = array();
		$order_info = self::$dbConn->fetch_array_all($query);
		foreach($order_info as $value){
			$ret[$value['id']] = $value['reason'];
		}
		//var_dump($ret);
		if ($ret) {
			self :: $errCode = "200";
			self :: $errMsg = "获取成功";
		} else {
			self :: $errCode = "003";
			self :: $errMsg = "获取失败";
		}
		return $ret; //成功， 返回真
	}
	
}
?>