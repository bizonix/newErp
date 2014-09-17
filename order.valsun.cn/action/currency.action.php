<?php
/**
*类名：订单属性管理
*功能：订单属性信息
*作者：hws
*
*/
class CurrencyAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";	
	
	//获取订单属性列表
	function act_getCurrencyList($select = '*',$where=''){
		$list =	CurrencyModel::getCurrencyList($select,$where);
		if($list){
			return $list;
		}else{
			self::$errCode = CurrencyModel::$errCode;
			self::$errMsg  = CurrencyModel::$errMsg;
			return false;
		}
	}
	
	//获取订单属性列表通过ID
	function act_getCurrencyListById($select = '*',$where=''){
		$list =	CurrencyModel::getCurrencyList($select,$where);
		$rtn  = array();
		foreach($list as $value){
			$rtn[$value['id']] = $value['currency'];	
		}
		if($rtn){
			return $rtn;
		}else{
			self::$errCode = CurrencyModel::$errCode;
			self::$errMsg  = CurrencyModel::$errMsg;
			return false;
		}
	}
	
	//增加/修改属性
	function  act_sureAddCurr(){
		$bool			 	= array();
		$data 	    	 	= array();
		$id 	    	  	= trim($_POST['currId']);
		$data['currency']	= post_check(trim($_POST['currency']));
		$data['rates']    	= post_check(trim($_POST['rates']));
		$data['userId']     = $_SESSION['sysUserId'];
		$data['modefyTime'] = time();
		if(empty($id)){
			$bool = CurrencyModel::getCurrencyList("*","where currency='{$data['currency']}'");
			if($bool){
				return 2;
			}
			$insertid = CurrencyModel::insertRow($data);
			if($insertid){
				return 1;
			}else{
				return false;
			}
		}else{
			$bool = CurrencyModel::getCurrencyList("*","where id!=$id and currency='{$data['currency']}'");
			if($bool){
				return 2;
			}
			$updatedata = CurrencyModel::update($data,"and id='$id'");
			if($updatedata){
				return 1;
			}else{
				return false;
			}
		}		
	}
	
	
}


?>