<?php
/**
 * 名称类型管理 nameTypeManage.action.php
 * @author chenwei 2013.11.1
 */
class NameTypeManageAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";	
	
	/*
     * 分页总数
     */
	function act_getPageNum($where = ""){
		//调用model层获取数据
		$list =	NameTypeManageModel::getPageNum($where);
		if($list){
			return $list;
		}else{
			self::$errCode = NameTypeManageModel::$errCode;
			self::$errMsg  = NameTypeManageModel::$errMsg;
			return false;
		}
	}

	
	/*
     * 名称类型显示、搜索、添加
     */
	function  act_nameTypeManageList($where = ""){
		$listArr =	NameTypeManageModel::nameTypeManageList($where);		
		if($listArr){
			return $listArr;
		}else{
			self :: $errCode = NameTypeManageModel :: $errCode;
			self :: $errMsg = NameTypeManageModel :: $errMsg;
			return false;
		}
	}
	
	/*
     * 填写系统名称重复验证
     */
	function  act_nameTypeVerify(){
		$addNewNameType = trim($_POST['addNewNameType']);
		$listArr        =	NameTypeManageModel::nameTypeVerify($addNewNameType);	
		if(empty($listArr)){
			self :: $errCode = NameTypeManageModel :: $errCode;
			self :: $errMsg  = NameTypeManageModel :: $errMsg;
			return false;
		}else{
			self :: $errCode = $listArr['reNum'];
			self :: $errMsg = $listArr['reStr'];
			return true;
		}
	}
	
	/*
     * 提交类型
     */
	function  act_addNameTypeSubmit(){	
		$addNewNameType    = trim($_POST['addNewNameType']);//名称类型
		$condition         = array();
		$submitStr         = "";
		$condition[]       = "typeName = '{$addNewNameType}'";	
		$condition[]       = "addUsernameId = {$_SESSION['sysUserId']}";//添加用户ID
		$condition[]       = "addTime = ".time();	
		$submitStr         = implode(",",$condition);
		$listArr           = NameTypeManageModel::addNameTypeSubmit($submitStr);		
		if($listArr['reNum'] == '200'){
		     echo "<script>location.href='index.php?mod=nameTypeManage&act=nameTypeManageList';</script>";
			 exit;
		}else{
			 return false;
		}
	}
	
	/*
     * 逻辑废弃类型
     */
	 function act_delNameType(){
		 $whereStr = "";
		 $whereStr = " where id = ".trim($_POST['delId']); 
		 $listArr  =	NameTypeManageModel::delNameType($whereStr);	
		 if($listArr['reNum'] == '200'){
			self :: $errCode = $listArr['reNum'];
			self :: $errMsg  = $listArr['reStr'];
			return true;
		 }else{	
			self :: $errCode = NameTypeManageModel :: $errCode;
			self :: $errMsg  = NameTypeManageModel :: $errMsg;
			return false;
		}
	 }
	 
	 /*
     * 启用类型
     */
	 function act_enabledNameType(){
		 $whereStr = "";
		 $whereStr = " where id = ".trim($_POST['enabledId']); 
		 $listArr  =	NameTypeManageModel::enabledNameType($whereStr);	
		 if($listArr['reNum'] == '200'){
			self :: $errCode = $listArr['reNum'];
			self :: $errMsg  = $listArr['reStr'];
			return true;
		 }else{	
			self :: $errCode = NameTypeManageModel :: $errCode;
			self :: $errMsg  = NameTypeManageModel :: $errMsg;
			return false;
		}
	 }
	
}
?>
