<?php
/*
* 淘宝刷单操作
* ADD BY chenwei 2013.9.17
*/
class TaoBaoRemoveOrderAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";	
	
	//暂时不寄
	public function act_taoBaoRemoveOrder(){
		$orderIdArr	= array();
		$postData   = trim($_POST['omData']);//订单编号队列
		$orderIdArr = explode(',',$postData);
		$returnList = TaoBaoRemoveOrderModel::taoBaoRemoveOrder($orderIdArr);		
		if($returnList){
			self :: $errCode = TaoBaoRemoveOrderModel :: $errCode;
			self :: $errMsg = TaoBaoRemoveOrderModel :: $errMsg;
			return true;
		}else{
			self :: $errCode = TaoBaoRemoveOrderModel :: $errCode;
			self :: $errMsg = TaoBaoRemoveOrderModel :: $errMsg;
			return false;
		}	
	}
}
?>