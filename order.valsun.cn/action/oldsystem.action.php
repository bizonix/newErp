<?php
/**
 * 类名：CommAct
 * 功能：管理Action公用的方法类
 * 版本：2013-09-26
 * 作者：Herman.Xi
 */
class OldsystemAct{

	static $errCode	  = 0;
	static $errMsg	  = '';
	
	//构造函数
	public function __construct(){
		
	}
	
	//获取控制器方法信息
	public function act_getpartsaleandnosendall(){
		$sku = $_GET['sku'];
		$isCache = true;
		if(isset($_GET['cache'])){
			$isCache = $_GET['cache'];
		}
		if (empty($sku)){
			self::$errCode = '5806';
			self::$errMsg  = 'sku is error';
			return array();
		}
		
		$data = OldsystemModel::getpartsaleandnosendall($sku, $isCache);
		
		return $data;
	}
	
	//同步订单状态接口
	public function act_ordererpupdateStatus(){
		$orderid_arr = $_POST['orderid_arr'];
		$ostatus = $_POST['ostatus'];
		$otype = $_POST['otype'];
		if(!$ostatus){
			self::$errCode = "001";
			self::$errMsg = "ostatus is null";
			return false;
		}
		if(!$otype){
			self::$errCode = "002";
			self::$errMsg = "otype is null";
			return false;
		}
		$idArr = $_POST['idArr'];
		$idStr = "";
		if(is_array($idArr)){
			$idStr = " AND id in (".join(',',$idArr).") ";	
		}
		$table = 'om_unshipped_order';
		$fields = '*';
		$storeId = 1;
		$where = " WHERE is_delete = '0' {$idStr} AND orderStatus = {$ostatus} AND orderType = {$otype} ";
		$rtn = OrderPushModel::listPushOneMessage($orderValue);
		
		//$data = OldsystemModel::ordererpupdateStatus($orderId,$ebay_status,$final_status);
		
		return $data;
	}
	
}
?>