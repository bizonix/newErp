<?php
/**
*类名：订单属性管理
*功能：订单属性信息
*作者：hws
*
*/
class OrderSettingAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";	
	
	//获取订单属性列表
	function act_getPropertyList($select = '*',$where){
		$list =	OrderAttrModel::getOrderAttrList($select,$where);
		if($list){
			return $list;
		}else{
			self::$errCode = OrderAttrModel::$errCode;
			self::$errMsg  = OrderAttrModel::$errMsg;
			return false;
		}
	}
	
	//增加/修改属性
	function  act_sureAddAttr(){
		$bool					= array();
		$data 	    			= array();
		$id 	    			= trim($_POST['attrId']);
		$data['attributesName'] = post_check(trim($_POST['attributesName']));
		if(empty($id)){
			$bool = OrderAttrModel::getOrderAttrList("*","where attributesName='{$data['attributesName']}'");
			if($bool){
				return 2;
			}
			$insertid = OrderAttrModel::insertRow($data);
			if($insertid){
				return 1;
			}else{
				return false;
			}
		}else{
			$bool = OrderAttrModel::getOrderAttrList("*","where id!=$id and attributesName='{$data['attributesName']}'");
			if($bool){
				return 2;
			}
			$updatedata = OrderAttrModel::update($data,"and id='$id'");
			if($updatedata){
				return 1;
			}else{
				return false;
			}
		}		
	}
	
}


?>