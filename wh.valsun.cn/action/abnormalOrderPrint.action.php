<?php
/*
 * 异常订单打印 action
 * ADD BY chenwei 2013.11.28
 */
class abnormalOrderPrintAct extends Auth {
	
	static $errCode = 0;
	static $errMsg = "";

	/**
	 * 异常订单打印数据组装
	 */
	public function act_abnormalOrderInfo($oids){
		//获取订单信息
		$now_position_info = PackingOrderModel::getaSetOfOrderInfo($oids);
		echo "<pre>";print_r($now_position_info);exit;
		return $now_position_info;
	}
}
?>
