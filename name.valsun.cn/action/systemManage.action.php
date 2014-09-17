<?php
/**
 * 系统管理 systemManage.action.php
 * @author chenwei 2013.11.1
 */
class SystemManageAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";	
	
	/*
     * 分页总数
     */
	function act_getPageNum($where = ""){
		//调用model层获取数据
		$list =	SystemManageModel::getPageNum($where);
		if($list){
			return $list;
		}else{
			self::$errCode = SystemManageModel::$errCode;
			self::$errMsg  = SystemManageModel::$errMsg;
			return false;
		}
	}

	
	/*
     * 系统名称显示、搜索、添加
     */
	function  act_systemManageList($where = ""){
		$listArr =	SystemManageModel::systemManageList($where);		
		if($listArr){
			return $listArr;
		}else{
			self :: $errCode = SystemManageModel :: $errCode;
			self :: $errMsg = SystemManageModel :: $errMsg;
			return false;
		}
	}
	
	/*
     * 填写系统名称重复验证
     */
	function  act_systemVerify(){
		$addNewSystem = trim($_POST['addNewSystem']);
		$listArr      =	SystemManageModel::systemVerify($addNewSystem);		
		if(empty($listArr)){
			self :: $errCode = SystemManageModel :: $errCode;
			self :: $errMsg = SystemManageModel :: $errMsg;
			return false;
		}else{
			self :: $errCode = $listArr['reNum'];
			self :: $errMsg = $listArr['reStr'];
			return true;
		}
	}
	
	/*
     * 提交系统名称数据
     */
	function  act_addSystemSubmit(){	
		$addNewSystem      = trim($_POST['addNewSystem']);//系统名称
		$condition         = array();
		$submitStr         = "";
		$condition[]       = "systemName = '{$addNewSystem}'";	
		$condition[]       = "addUsernameId = {$_SESSION['sysUserId']}";//添加用户ID
		$condition[]       = "addTime = ".time();	
		$submitStr         = implode(",",$condition);
		$strTip			   = "";
		$listArr =	SystemManageModel::addSystemSubmit($submitStr);		
		if($listArr['reNum'] == '200'){
		     echo "<script>location.href='index.php?mod=systemManage&act=systemManageList';</script>";
			 exit;
		}else{
			 return false;
		}
	}
	
	/*
     * 逻辑废弃系统
     */
	 function act_delSystem(){
		 $whereStr = "";
		 $whereStr = " where id = ".trim($_POST['delId']); 
		 $listArr  =	SystemManageModel::delSystem($whereStr);	
		 if($listArr['reNum'] == '200'){
			self :: $errCode = $listArr['reNum'];
			self :: $errMsg  = $listArr['reStr'];
			return true;
		 }else{	
			self :: $errCode = SystemManageModel :: $errCode;
			self :: $errMsg  = SystemManageModel :: $errMsg;
			return false;
		}
	 }
	 
	 /*
     * 启用系统
     */
	 function act_enabledSystem(){
		 $whereStr = "";
		 $whereStr = " where id = ".trim($_POST['enabledId']); 
		 $listArr  =	SystemManageModel::enabledSystem($whereStr);	
		 if($listArr['reNum'] == '200'){
			self :: $errCode = $listArr['reNum'];
			self :: $errMsg  = $listArr['reStr'];
			return true;
		 }else{	
			self :: $errCode = SystemManageModel :: $errCode;
			self :: $errMsg  = SystemManageModel :: $errMsg;
			return false;
		}
	 }
	
}
?>
