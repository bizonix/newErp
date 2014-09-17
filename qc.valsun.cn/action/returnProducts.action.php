<?php
class ReturnProductsAct extends Auth {
	static $errCode = 0;
	static $errMsg = "";

	function act_getReturnProductsList($select, $where) {
		$list = ReturnProductsModel :: getReturnProductsList($select, $where);
		if ($list) {
			return $list;
		} else {
			self :: $errCode = ReturnProductsModel :: $errCode;
			self :: $errMsg = ReturnProductsModel :: $errMsg;
			return false;
		}
	}

	function act_updateReturnProducts($set, $where) {
		return ReturnProductsModel :: updateReturnProducts($set, $where);
	}

	function act_getReturnProductsCount($where) { //根据条件，取得记录总数
		$list = ReturnProductsModel :: getReturnProductsCount($where);
		if ($list) {
			return $list;
		} else {
			self :: $errCode = ReturnProductsModel :: $errCode;
			self :: $errMsg = ReturnProductsModel :: $errMsg;
			return false;
		}
	}

	function act_addReturnProducts($set) {
		$list = ReturnProductsModel :: updateReturnProducts($set);
		if ($list) {
			return $list;
		} else {
			self :: $errCode = ReturnProductsModel :: $errCode;
			self :: $errMsg = ReturnProductsModel :: $errMsg;
			return false;
		}
	}
}
?>