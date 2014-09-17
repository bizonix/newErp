<?php
/**
*类名：IQC完成检测数据处理(action层)
*功能：生成检测数据IQC KPI考核
*作者：陈伟
*
*/
class IqcCompleteInfoAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";	
	
	//获取当前待测列表
	function  act_iqcCompleteInfo($where,$combine){
		if($combine){
			$list =	IqcCompleteInfoModel::iqcCompleteInfoCombine($where);
		}else{
			$list =	IqcCompleteInfoModel::iqcCompleteInfo($where);
		}
		if($list){
			return $list;
		}else{
			self::$errCode = WhStandardModel::$errCode;
			self::$errMsg  = WhStandardModel::$errMsg;
			return false;
		}
	}
	
	//获取当前完成信息总条数
	function act_getPageNum($where){
		//调用model层获取数据
		$list =	IqcCompleteInfoModel::getPageNum($where);
		if($list){
			return $list;
		}else{
			self::$errCode = WhStandardModel::$errCode;
			self::$errMsg  = WhStandardModel::$errMsg;
			return false;
		}
	}
	
}


?>