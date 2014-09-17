<?php
/**
 * 状态处理标记
 * add by yxd
 */
class OrderOperatedModel extends CommonModel{
	public function __construct(){
		parent::__construct();
	}
	
	public function get_operateByAS($omOrderId,$statusId='0',$typeId){
		$statusId = intval($statusId);
		$typeId = intval($typeId);
		if(empty($statusId)){//statusId,typeId都是在statusmenu表中，是唯一的
			return $this->sql("SELECT * FROM ".$this->getTableName()." WHERE omOrderId=$omOrderId AND  typeId=$typeId ")->limit("*")->select();
		}else{
			return $this->sql("SELECT * FROM ".$this->getTableName()." WHERE omOrderId=$omOrderId AND statusId=$statusId AND typeId=$typeId ")->limit("*")->select();
		}
		
	}
	public function isOderOperated($omOrderId,$typeId='0',$statusId='0'){
		$statusId = intval($statusId);
		$typeId = intval($typeId);
		if(empty($statusId)){//statusId,typeId都是在statusmenu表中，是唯一的
			$result =  $this->sql("SELECT * FROM ".$this->getTableName()." WHERE omOrderId=$omOrderId AND  typeId=$typeId ")->limit("1")->select();
		}else{
			$result =  $this->sql("SELECT * FROM ".$this->getTableName()." WHERE omOrderId=$omOrderId AND statusId=$statusId AND typeId=$typeId ")->limit("1")->select();
		}
		
		if(!empty($result)){
			return true;
		}
		
		return false;
	}
}
?>