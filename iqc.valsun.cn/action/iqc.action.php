<?php
/**
*类名：IQC领取、待测
*功能：处理产品检测信息
*作者：hws
*
*/
class IqcAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";	
	
	//获取当前待测列表
	function  act_getNowWhList($select = '*',$where){
		$list =	WhStandardModel::getNowWhList($select,$where);
		if($list){
			return $list;
		}else{
			self::$errCode = WhStandardModel::$errCode;
			self::$errMsg  = WhStandardModel::$errMsg;
			return false;
		}
	}
	
	//获取当前待测数量
	function act_getNowWhNum($where){
		//调用model层获取数据
		$list =	WhStandardModel::getNowWhNum($where);
		if($list){
			return $list;
		}else{
			self::$errCode = WhStandardModel::$errCode;
			self::$errMsg  = WhStandardModel::$errMsg;
			return false;
		}
	}
	
	//领取料号
	function  act_getSku(){
		$data   = array();
		$id_arr = $_POST['id'];
		$id     = implode(',',$id_arr);
		$where  = " and id in(".$id.")";
		$data   = array(
			'getUserId'		=> $_SESSION['userId'],
			'getTime'   	=> time(),
			'detectStatus' 	=> 1
		);
		$list =	WhStandardModel::update($data,$where);
		if($list){
			return $list;
		}else{
			self::$errCode = "003";
			self::$errMsg  = "领取失败，请重试！";
			return false;
		}
	}

	//退回料号
	function  act_returnSku(){
		$data   = array();
		$id_arr = $_POST['id'];
		$id     = implode(',',$id_arr);
		$where  = " and id in(".$id.")";
		$data   = array(
			'getUserId'		=> '',
			'getTime'   	=> '',
			'detectStatus' 	=> 0
		);
		$list =	WhStandardModel::update($data,$where);
		if($list){
			return $list;
		}else{
			self::$errCode = "003";
			self::$errMsg  = "退回失败，请重试！";
			return false;
		}
	}
	
	//删掉料号
	function  act_delSku(){
		$data   = array();
		$id_arr = $_POST['id'];
		$id     = implode(',',$id_arr);
		$where  = "where id in(".$id.")";
		$list =	WhStandardModel::delete($where);
		if($list){
			return $list;
		}else{
			self::$errCode = "003";
			self::$errMsg  = "退回失败，请重试！";
			return false;
		}
	}
	
	//料号查询
	function  act_getSkuInfo(){
		$sku   = $_POST['sku'];
		$where = "where sku='$sku' and sellerId=0 and detectStatus=0 order by id desc";
		$list  = WhStandardModel::getNowWhList("*",$where);
		if($list){
			return $list;
		}else{
			self::$errCode = "003";
			self::$errMsg  = "没有找到对应的料号！";
			return false;
		}
	}
	
}


?>