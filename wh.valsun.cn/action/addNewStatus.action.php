<?php
class addNewStatusAct extends Auth{
	public static $errCode = 0;
	public static $errMsg = "";
	
	public function act_addNewStatus(){
		$statusName = isset($_POST['statusName'])?trim($_POST['statusName']):"";
		$statusCode = isset($_POST['statusCode'])?trim($_POST['statusCode']):"";
		$statusGroup = isset($_POST['statusGroup'])?trim($_POST['statusGroup']):"";
		$note = isset($_POST['note'])?trim($_POST['note']):"";
		$where = "where statusName='{$statusName}'"; 
		$list = addNewStatusModel::selectRecord($where);
		if($list){
			self::$errCode = 101;
			self::$errMsg = "状态名已存在！";
			return false;
		}
		$where = "where statusCode='{$statusCode}'"; 
		$list = addNewStatusModel::selectRecord($where);
		if($list){
			self::$errCode = 102;
			self::$errMsg = "状态码已存在！";
			return false;
		}
		$msg = addNewStatusModel::insertRecord($statusName,$statusCode,$statusGroup,$note);
		if(!$msg){
			self::$errCode = 103;
			self::$errMsg = "添加状态失败！";
			return false;
		}
		return true;
	}
}
?>