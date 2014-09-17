<?php
/*
 * 提供对接仓库管理系统のACTION
 * ADD BY Herman.Xi @20140120
 */
class WarehouseAPIAct{
	static $errCode = 0;
	static $errMsg = "";
	
	//获取所有的账号信息BY id
	function act_getAbOrderList() {
		$res = WarehouseAPIModel::getAbOrderList();
		//self::$errCode = WarehouseAPIModel::$errCode;
		//self::$errMsg = WarehouseAPIModel::$errMsg;
		return $res;
	}
	
	//获取所有的账号信息BY id
	function act_getAbOrderInfo($orderId) {
		$res = WarehouseAPIModel::getAbOrderInfo($orderId);
		//self::$errCode = WarehouseAPIModel::$errCode;
		//self::$errMsg = WarehouseAPIModel::$errMsg;
		return $res;
	}
	
	//获取所有的账号信息BY id
	function act_operateAbOrder($orderId,$calcWeight) {
		$res = WarehouseAPIModel::operateAbOrder($orderId,$calcWeight);
		//self::$errCode = WarehouseAPIModel::$errCode;
		//self::$errMsg = WarehouseAPIModel::$errMsg;
		return $res;
	}
	
}
?>	