<?php
/*
* 暂时不寄操作
* ADD BY chenwei 2013.9.12
*/
class TemporarilyUnsendAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";	
	
	//暂时不寄
	public function act_temporarilyUnsend(){
		$orderIdArr	= array();
		$postData   = trim($_POST['omData']);//订单编号队列
		$orderIdArr = explode(',',$postData);
		$returnList = TemporarilyUnsendModel::temporarilyUnsend($orderIdArr);		
		if($returnList){
			self :: $errCode = TemporarilyUnsendModel :: $errCode;
			self :: $errMsg = TemporarilyUnsendModel :: $errMsg;
			return true;
		}else{
			self :: $errCode = TemporarilyUnsendModel :: $errCode;
			self :: $errMsg = TemporarilyUnsendModel :: $errMsg;
			return false;
		}	
	}
}
?>