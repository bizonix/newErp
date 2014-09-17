<?php
/*
 * 名称：OrderRecordAct
 * 功能：订单记录表
 * 版本：v 1.0
 * 日期：2013/09/12
 * 作者：zyp
 * 修改：Herman.Xi @ 20131205
 * */
class OrderRecordAct{
	public static $errCode = 0;
	public static $errMsg = '';
	
	/*
	 * 根据条件获取对应订单详情(最新版)
	 * last modified by Herman.Xi @20131205
	 */
	public function act_getOrderAuditRecords($omOrderId){
		
		$data	=	OrderRecordModel::getOrderAuditRecords($omOrderId);
		
		self::$errCode = OrderRecordModel::$errCode;
		self::$errMsg  = OrderRecordModel::$errMsg;
		return $data;
	}
	
	/*
	 * 根据条件获取对应订单详情(最新版)
	 * last modified by Herman.Xi @20131205
	 */
	public function act_getOrderAuditRecordsBySku($omOrderId, $sku){
		
		$data	=	OrderRecordModel::getOrderAuditRecordsBySku($omOrderId, $sku);
		
		self::$errCode = OrderRecordModel::$errCode;
		self::$errMsg  = OrderRecordModel::$errMsg;
		return $data;
	}
	
	/*
	 * 根据条件获取对应订单详情(最新版)
	 * last modified by Herman.Xi @20131205
	 */
	public function act_judgeAuditRecordsInSkus($omOrderId, $skuinfos){
		
		$data	=	OrderRecordModel::judgeAuditRecordsInSkus($omOrderId, $skuinfos);
		
		self::$errCode = OrderRecordModel::$errCode;
		self::$errMsg  = OrderRecordModel::$errMsg;
		return $data;
	}
	
}

?>