<?php
/**
 * 移动记录添加共用方法的编写
 * add by chenwei 2013.9.10
 **/
class OrderOperationLogAct extends Auth {
	static $errCode	=	0;
	static $errMsg	=	"";	

	/*
     * 移动记录添加共用方法的编写
     */
	function  act_orderOperationLogList($where = "",$table = ""){
		$listArr =	OrderOperationLogModel :: orderOperationLogList($where,$table);		
		if($listArr){
			return $listArr;
		}else{
			self :: $errCode = OrderOperationLogModel :: $errCode;
			self :: $errMsg = OrderOperationLogModel :: $errMsg;
			return false;
		}
	}
	
	/*
     * 求出数据库所有表明
     */
	function  act_orderTabelNameList($sqlStr = '',$strstrStr = ''){
		$listArr =	OrderOperationLogModel :: orderTabelNameList($sqlStr,$strstrStr);		
		if($listArr){
			return $listArr;
		}else{
			self :: $errCode = OrderOperationLogModel :: $errCode;
			self :: $errMsg = OrderOperationLogModel :: $errMsg;
			return false;
		}
	}

}
?>
