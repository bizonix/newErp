<?php
class ProductAct extends Auth {
	static $errCode = 0;
	static $errMsg = "";

	function act_getProductList($select = '*', $where) {
		$list = ProductModel :: getProductList($select, $where);
		if ($list) {
			return $list;
		} else {
			self :: $errCode = ProductModel :: $errCode;
			self :: $errMsg = ProductModel :: $errMsg;
			return false;
		}
	}

	static function act_getProductListNum() {
		$list = ProductModel :: getProductListNum();
		if ($list) {
			return $list;
		} else {
			self :: $errCode = ProductModel :: $errCode;
			self :: $errMsg = OrderTestModel :: $errMsg;
			return false;
		}
	}

	static function act_getNewGoodsListNum($where) {
		$list = ProductModel :: getNewGoodsListNum($where);
		if ($list) {
			return $list;
		} else {
			self :: $errCode = ProductModel :: $errCode;
			self :: $errMsg = OrderTestModel :: $errMsg;
			return false;
		}
	}

	function act_getNewGoodsList($select, $where) {
		$list = ProductModel :: getNewGoodsList($select, $where);
		if ($list) {
			return $list;
		} else {
			self :: $errCode = ProductModel :: $errCode;
			self :: $errMsg = OrderTestModel :: $errMsg;
			return false;
		}
	}

}
?>