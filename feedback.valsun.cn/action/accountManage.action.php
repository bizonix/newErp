<?php
class AccountManageAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";
	public function act_userList(){
		$select      = " id,user_name ";
		$where		 = " where is_delete=0 and type=1 and user_name!='姚晓东'";
		$userList    = OmAvailableModel::getTNameList("fb_account_power", $select, $where);
		return $userList;
	}
	public function act_accountList(){
		$select           = " id ,account ";
		$where     		  = " where is_delete = 0 and token <> '' order by account ";
		$accountList	  = OmAvailableModel::getTNameList("fb_account", $select, $where);
		return $accountList;
	}
	public function act_userPowerSearch($username){
		$select           = " power ";
		$where            = " where user_name='$username'";
		$res       		  = AccountManageModel::getAccoutPower($select,$where);
		self::$errCode    = AccountManageModel::$errCode;
		self::$errMsg     = AccountManageModel::$errMsg;
		return     $res[0]['power'];
	}
	public function act_userPowerSave(){
		$username		  = $_POST['username'];
		$power			  = $_POST['power'];
		if(empty($username)){
			self::$errCode    = "002";
			self::$errMsg     = "请选择用户";
			return  false;
		} 
		$select    		  = " set power='$power' ";
		$where     		  = "  user_name='$username' ";
		$res       		  = AccountManageModel::saveAccoutPower($select,$where);
		self::$errCode    = AccountManageModel::$errCode;
		self::$errMsg     = AccountManageModel::$errMsg;
		return  $res;
	}
	
	public function act_addUserNmae(){
		$username     = isset($_POST['addusername']) ? trim($_POST['addusername']) : "";
	    if(empty($username)){
			self::$errCode    = "002";
			self::$errMsg     = "请选择用户";
			return  false;
		} 
		$res       		  = AccountManageModel::addUserName($username);
		self::$errCode    = AccountManageModel::$errCode;
		self::$errMsg     = AccountManageModel::$errMsg;
		return  $res;
	}
}
?>