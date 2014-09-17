<?php
class addGroupAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";
	public function act_addGroup(){
		TransactionBaseModel :: begin();
		$group_name = isset($_POST['group_name'])?$_POST['group_name']:"";
		$group_num = isset($_POST['group_num'])?$_POST['group_num']:"";
		$userId = $_SESSION['userId'];
		$ret = addGroupModel::insertRecord($group_name,$group_num,$userId);
		if(!$ret){
			self::$errCode = 101;
			self::$errMsg  = "²åÈëÊ§°Ü";
			TransactionBaseModel :: rollback();
			return false;
		}
		TransactionBaseModel :: commit();
		return true;
	}
}	
?>	