<?php
/*
 * 名称管理系统 nameSystem.action.php
 * ADD BY chenwei 2013.9.26
 */
class NameSystemAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";	
	
	/*
     * 分页总数
     */
	function act_getPageNum($where = ""){
		//调用model层获取数据
		$list =	NameSystemModel::getPageNum($where);
		if($list){
			return $list;
		}else{
			self::$errCode = NameSystemModel::$errCode;
			self::$errMsg  = NameSystemModel::$errMsg;
			return false;
		}
	}

	
	/*
     * 名称管理页面数据列表显示、搜索、添加
     */
	function  act_nameSysermList($where = ""){
		$listArr =	NameSystemModel::nameSysermList($where);		
		if($listArr){
			return $listArr;
		}else{
			self :: $errCode = NameSystemModel :: $errCode;
			self :: $errMsg = NameSystemModel :: $errMsg;
			return false;
		}
	}
	
	/*
     * 系统名称
     */
	function  act_systemNameAllArr($where = ""){
		$listArr =	NameSystemModel::systemNameAllArr($where);		
		if($listArr){
			return $listArr;
		}else{
			self :: $errCode = NameSystemModel :: $errCode;
			self :: $errMsg = NameSystemModel :: $errMsg;
			return false;
		}
	}
	
	/*
     * 名称类型
     */
	function  act_valTypeAllArr($where = ""){
		$listArr =	NameSystemModel::valTypeAllArr($where);		
		if($listArr){
			return $listArr;
		}else{
			self :: $errCode = NameSystemModel :: $errCode;
			self :: $errMsg = NameSystemModel :: $errMsg;
			return false;
		}
	}
	
	/*
     * 申请新名称验证
     */
	function  act_nameSystemVerify(){
		$addNewName = trim($_POST['addNewName']);
		//echo "aaaaaaaaa".$addNewName;exit;
		$listArr =	NameSystemModel::nameSystemVerify($addNewName);		
		if(empty($listArr)){
			self :: $errCode = NameSystemModel :: $errCode;
			self :: $errMsg = NameSystemModel :: $errMsg;
			return false;
		}else{
			//var_dump($listArr);
			self :: $errCode = $listArr['reNum'];
			self :: $errMsg = $listArr['reStr'];
			return true;
		}
	}
	
	/*
     * 插入新名称
     */
	function  act_addNameSubmit(){	
		$addNewName      = trim($_POST['addNewName']);//名称
		$chooseSystem    = trim($_POST['chooseSystem']);//系统名称ID
		$chooseNameType  = trim($_POST['chooseNameType']);//名称类型ID
		$addFunctionNote = trim($_POST['addFunctionNote']);//功能备注
		$condition       = array();
		$submitStr       = "";
		$condition[]     = "name         = '{$addNewName}'";	
		$condition[]     = "systemId     = {$chooseSystem}";
		$condition[]     = "systemTypeId = {$chooseNameType}";
		$condition[]     = "functionNote = '{$addFunctionNote}'";
		$condition[]     = "addUsernameId = {$_SESSION['sysUserId']}";//添加用户ID
		$condition[]     = "addTime = ".time();	
		$submitStr       = implode(",",$condition);
		$strTip			 = "";
		$listArr =	NameSystemModel::addNameSubmit($submitStr);		
		$strTip  = $listArr['reStr'];
		if($listArr['reNum'] == '200'){
		     echo "<script>location.href='index.php?mod=nameSystem&act=nameSystemList';</script>";
			 exit;
		}else{
			 return false;
		}
	}
	
	/*
     * 逻辑废弃名称
     */
	 function act_delName(){
		 $whereStr = "";
		 $billArr  = array_filter(explode(",",trim($_POST['bill'])));
		 $whereStr = " where id in (".implode(',',$billArr).")";
		 $listArr  =	NameSystemModel::delName($whereStr);	
		 if($listArr['reNum'] == '200'){
			self :: $errCode = $listArr['reNum'];
			self :: $errMsg  = $listArr['reStr'];
			return true;
		 }else{	
			self :: $errCode = NameSystemModel :: $errCode;
			self :: $errMsg  = NameSystemModel :: $errMsg;
			return false;
		}
	 }
	
}
?>
