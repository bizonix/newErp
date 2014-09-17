<?php
/**
*类名：feedback管理类
*功能：处理feedback信息
*作者：yxd
*
*/
class FeedbackManageAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";	
	
	//获取当前记录列表
	function  act_getOrderList($select ='*',$where){
		$list =	FeedbackManageModel::getOrderList($select,$where);	
		if($list){
			return $list;
		}else{
			self::$errCode = FeedbackManageModel::$errCode;
			self::$errMsg  = FeedbackManageModel::$errMsg;
			return array();
		}
	}
	
	//获取当前记录数量
	function act_getOrderNum($where){
		//调用model层获取数据		
		$list =	FeedbackManageModel::getOrderNum($where);	
		if($list){
			return $list;
		}else{
			self::$errCode = FeedbackManageModel::$errCode;
			self::$errMsg  = FeedbackManageModel::$errMsg;
			return false;
		}
	}
	
	//评价
	function act_setEvaluation(){
		//调用model层获取数据
		$score 		= trim($_POST['valuatescore']);
		$content	= trim($_POST['valuatecontent']);
		$billArr	= $_POST['bill'];	
		$orderArr	= array();
		foreach ($billArr as  $item) {
			$orderArr[$item['account']][] = $item['recordnumber'];
		}
		//print_r($orderArr);
		foreach ($orderArr as $account => $recordnumberArr) {
			$ret = aliexpress_setEvaluation($account,$recordnumberArr,$score,$content);	
			if ($ret['errCode'] != '0') {
				self::$errCode = $ret['errCode'];
				self::$errMsg  = $ret['errMsg'];
				return false;
			}
		}
		return true;
	}
		
}


?>