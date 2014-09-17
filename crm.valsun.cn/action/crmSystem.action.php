<?php
/*
 * 客户关系管理系统 crmSystem.action.php
 * ADD BY chenwei 2013.9.26
 */
class CrmSystemAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";	
	
	/*
     * 分页总数
     */
	function act_getPageNum($where = ""){
		//调用model层获取数据
		$list =	CrmSystemModel::getPageNum($where);
		if($list){
			return $list;
		}else{
			self::$errCode = CrmSystemModel::$errCode;
			self::$errMsg  = CrmSystemModel::$errMsg;
			return false;
		}
	}

	
	/*
     * 客户管理页面数据列表显示、搜索
     */
	function  act_crmSysermList($where = ""){
		$listArr =	CrmSystemModel::crmSysermList($where);		
		if($listArr){
			return $listArr;
		}else{
			self :: $errCode = CrmSystemModel :: $errCode;
			self :: $errMsg = CrmSystemModel :: $errMsg;
			return false;
		}
	}
	
}
?>
