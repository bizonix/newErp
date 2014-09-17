<?php
/**
 * 补寄订单
*作者：txl
*
*/
class ResendOrderAct extends Auth {
	public static $errCode = 0;
	public static $errMsg = '';
	
	/*
	 * 申请异常
	 */
	public function act_resendOrder() {
		$orderid = isset ( $_GET ['orderid'] ) ? intval ( $_GET ['orderid'] ) : 0;
		$type = isset ( $_GET ['type'] ) ? intval ( $_GET ['type'] ) : 1;
		
		$resendArr 		= isset ( $_POST ['resendArr'] ) ? $_POST ['type'] : '';
		$reason_noteb 	= isset ( $_POST ['reason_noteb'] ) ? $_POST ['reason_noteb'] : '';
		$extral_noteb 	= isset ( $_POST ['extral_noteb'] ) ? $_POST ['extral_noteb'] : '';
		
		$old_ostatus 	= isset ( $_POST ['old_ostatus'] ) ? $_POST ['old_ostatus'] : '';
		$old_otype 	= isset ( $_POST ['old_otype'] ) ? $_POST ['old_otype'] : '';
		
		$SendReplacementAct = new SendReplacementAct();
		$return = $SendReplacementAct ->act_getSendReplacement();
		$sendreplacement = $return['SendReplacementType'];
		$send_reason = $return['SendReplacementReason'];
		
		$note = " 补寄 订单(".$sendreplacement[$resendArr].")--{$extral_noteb},".$send_reason[$reason_noteb];
		
		//echo $orderid;
		if ($orderid < 1) { // 传入订单号不正确
			self::$errCode = 500;
			self::$errMsg = '订单号类型不正确！';
			return FALSE;
		}
		
		//$oi_obj = new OrderInfoModel ();
		/*$orderinfo = OrderInfoModel::getShipedOrderInfo($orderid);
		
		if ($orderinfo == FALSE) { // 未找到订单
			switch (OrderInfoModel::$errCode) {
				case '003' :
					self::$errCode = 501;
					break;
				case '004' :
					self::$errCode = 503;
					break;
			}
			self::$errMsg = OrderInfoModel::$errMsg;
			return FALSE;
		}*/
		
		/*if (C('STATEHASSHIPPED') != $orderinfo['orderStatus']) {	//不会已经发货订单 不能补寄
			self::$errCode = 504;
			self::$errMsg = '当前状态不可申请补寄！';
			return FALSE;
		}*/
	
		$updateresult = OrderInfoModel::resendOrder($orderid, $note, $type, $old_ostatus, $old_otype);
		if (FALSE == $updateresult) {	//更新状态失败
			self::$errCode = OrderInfoModel::$errCode;
			self::$errMsg = OrderInfoModel::$errMsg;
			return FALSE;
		}
		
		self::$errCode = OrderInfoModel::$errCode;
		self::$errMsg = OrderInfoModel::$errMsg;
		return TRUE;
	}
}
