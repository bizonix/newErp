<?php
/**
 * 补寄订单
*作者：txl
*
*/
class SendReplacementAct extends Auth {
	public static $errCode = 0;
	public static $errMsg = '';
	
	/*
	 * 获取补寄和列表信息
	 */
	public function act_getSendReplacement() {
		//$oi_obj = new OrderInfoModel ();
		$return = array();
		
		$SendReplacementType = SendReplacementModel::getSendReplacementType();
		$SendReplacementReason = SendReplacementModel::getSendReplacementReason();
		if ($SendReplacementType) { // 未找到订单
			$return['SendReplacementType'] = $SendReplacementType;
			//self::$errCode = SendReplacementModel::$errCode;
			//self::$errMsg = SendReplacementModel::$errMsg;
		}else {
			self::$errCode = 506;
			self::$errMsg = '获取失败！';
			return FALSE;
		}
		if ($SendReplacementType) { // 未找到订单
			$return['SendReplacementReason'] = $SendReplacementReason;
			//self::$errCode = SendReplacementModel::$errCode;
			//self::$errMsg = SendReplacementModel::$errMsg;
		}else {
			self::$errCode = 506;
			self::$errMsg = '获取失败！';
			return FALSE;
		}
		//var_dump($return); exit;
		self::$errCode = 200;
		self::$errMsg = '申请成功！';
		return $return;
	}
}
