<?php
/**
*类名：IQC领取、待测
*功能：处理产品检测信息
*作者：hws
*
*/
class AccountAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";	
	
	//获取当前记录列表
	function  act_getAccountList($select = '*',$where){
		$list =	AccountModel::getAccountList($select,"$where  order by account ASC");
		if($list){
			return $list;
		}else{
			self::$errCode = AccountModel::$errCode;
			self::$errMsg  = AccountModel::$errMsg;
			return array();
		}
	}
	
	//获取当前记录列表
	function  act_getAccountById($accountId){
		
		$where = " where id = '$accountId'";
		$list =	AccountModel::getAccountList('account',$where);
		if($list){
			return $list[0]['account'];
		}else{
			self::$errCode = AccountModel::$errCode;
			self::$errMsg  = AccountModel::$errMsg;
			return array();
		}
	}
	
	//获取当前记录列表
	function  act_getAccountId($account){
		$where = " where account = '$account'";
		$list =	AccountModel::getAccountList('id',$where);
		if($list){
			return $list[0]['id'];
		}else{
			self::$errCode = AccountModel::$errCode;
			self::$errMsg  = AccountModel::$errMsg;
			return array();
		}
	}
	
	
	
	//获取当前记录数量
	function act_getAccountNum($where){
		//调用model层获取数据
		$list =	AccountModel::getAccountNum($where);
		if($list){
			return $list;
		}else{
			self::$errCode = AccountModel::$errCode;
			self::$errMsg  = AccountModel::$errMsg;
			return false;
		}
	}
		
}


?>