<?php
/*
* 合并订单
* ADD BY chenwei 2013.9.11
*/
class CombineOrderAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";	

	public function act_combineOrder(){
		$orderIdArr	= array();
		$postData   = trim($_POST['omData']);//订单编号队列
		$orderIdArr = explode(',',$postData);
		$returnList =	CombineOrderModel::combineOrder($orderIdArr);		
		if($returnList){
			self :: $errCode = CombineOrderModel :: $errCode;
			self :: $errMsg = CombineOrderModel :: $errMsg;
			return true;
		}else{
			self :: $errCode = CombineOrderModel :: $errCode;
			self :: $errMsg = CombineOrderModel :: $errMsg;
			return false;
		}	
	}
}
?>