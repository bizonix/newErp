<?php
/**
类名： RtnPurchaseErpApiAct
*功能:返回采购系统所需数据API
*日期:2013/08/11
*作者:王民伟
*/
class RtnPurchaseErpApiAct {
	public static $errCode = '';
	public static $errMsg  = '未初始化';

	//不良品信息列表
	public function act_getBadGoodList() {
		$purid 	  = isset($_GET['purid']) ? $_GET['purid'] : '1';
		$where 	  = isset($_GET['where']) ? base64_decode($_GET['where']) : '';
		$page     = isset($_GET['page']) ? $_GET['page'] : '1';
		$rtn_data = RtnPurchaseErpApiModel::getBadGoodList($purid, $where, $page);
		return $rtn_data;
	}
	
	//待定信息列表
	public function act_getPendGoodList() {
		$purid 	  = isset($_GET['purid']) ? $_GET['purid'] : '1';
		$where 	  = isset($_GET['where']) ? base64_decode($_GET['where']) : '';
		$page     = isset($_GET['page']) ? $_GET['page'] : '1';
		$rtn_data = RtnPurchaseErpApiModel::getPendGoodList($purid, $where, $page);
		return $rtn_data;
	}

    //待退回信息列表
	public function act_getReturnGoodList() {
		$purid 	  = isset($_GET['purid']) ? $_GET['purid'] : '1';
		$where 	  = isset($_GET['where']) ? base64_decode($_GET['where']) : '';
		$page     = isset($_GET['page']) ? $_GET['page'] : '1';
		$rtn_data = RtnPurchaseErpApiModel::getReturnGoodList($purid, $where, $page);
		return $rtn_data;
	}
}