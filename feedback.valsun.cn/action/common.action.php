<?php
/**
*类名：commonAct
*功能：共用的Action
*作者：rdh
*
*/
class CommonAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";	
	
	//获取当前记录列表
	function  act_getGoodsInfo($select ='*',$where){
		$list =	CommonModel::getGoodsInfo($select,$where);	
		if($list){
			return $list;
		}else{
			self::$errCode = CommonModel::$errCode;
			self::$errMsg  = CommonModel::$errMsg;
			return array();
		}
	}

	//获取当前记录列表
	function  act_getGoodsCombineInfo($select ='*',$where){
		$list =	CommonModel::getGoodsCombineInfo($select,$where);	
		if($list){
			return $list;
		}else{
			self::$errCode = CommonModel::$errCode;
			self::$errMsg  = CommonModel::$errMsg;
			return array();
		}
	}
	
	//获取当前记录列表
	function  act_getCombineSkuInfo($select ='*',$where){
		$list =	CommonModel::getCombineSkuInfo($select,$where);
		if($list){
			return $list;
		}else{
			self::$errCode = CommonModel::$errCode;
			self::$errMsg  = CommonModel::$errMsg;
			return array();
		}
	}

	//获取当前记录列表
	function  act_getPurchaserInfo($select ='*',$where){
		$list =	CommonModel::getPurchaserInfo($select,$where);	
		if($list){
			return $list;
		}else{
			self::$errCode = CommonModel::$errCode;
			self::$errMsg  = CommonModel::$errMsg;
			return array();
		}
	}
	
	//获取当前记录列表
	function  act_judgeCombineSku($sku){
		$list =	CommonModel::judgeCombineSku($sku);		
		if($list){
			return $list;
		}else{
			self::$errCode = CommonModel::$errCode;
			self::$errMsg  = CommonModel::$errMsg;
			return 0;
		}
	}
	
	//获取当前记录数量
	function act_getGoodsNum($where){
		//调用model层获取数据		
		$list =	contentModel::getContentNum($where);	
		if($list){
			return $list;
		}else{
			self::$errCode = contentModel::$errCode;
			self::$errMsg  = contentModel::$errMsg;
			return false;
		}
	}
	

	
	

		
}


?>