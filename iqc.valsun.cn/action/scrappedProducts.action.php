<?php
class ScrappedProductsAct extends Auth {
	static $errCode = 0;
	static $errMsg = "";

	function act_getScrappedProductsList($select, $where) {
		$list = ScrappedProductsModel :: getScrappedProductsList($select, $where);
		if ($list) {
			return $list;
		} else {
			self :: $errCode = ScrappedProductsModel :: $errCode;
			self :: $errMsg = ScrappedProductsModel :: $errMsg;
			return false;
		}
	}

	function act_updateScrappedProducts($set, $where) {
		return ScrappedProductsModel :: updateScrappedProducts($set, $where);
	}

	function act_getScrappedProductsCount($where) { //根据条件，取得记录总数
		$list = ScrappedProductsModel :: getScrappedProductsCount($where);
		if ($list) {
			return $list;
		} else {
			self :: $errCode = ScrappedProductsModel :: $errCode;
			self :: $errMsg = ScrappedProductsModel :: $errMsg;
			return false;
		}
	}

	function act_addScrappedProducts($set) {
		$list = ScrappedProductsModel :: updateScrappedProducts($set);
		if ($list) {
			return $list;
		} else {
			self :: $errCode = ScrappedProductsModel :: $errCode;
			self :: $errMsg = ScrappedProductsModel :: $errMsg;
			return false;
		}
	}
}
?>