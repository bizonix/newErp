<?php
/**
 * 订单异常申请处理
*作者：txl
*
*/
class ExceptionHandelAct extends Auth {
	public static $errCode = 0;
	public static $errMsg = '';
	
	/*
	 * 申请异常
	 */
	public function act_applyForException() {
		
		$orderid = isset ( $_GET ['orderid'] ) ? intval ( $_GET ['orderid'] ) : 0;
		if ($orderid < 1) { // 传入订单号不正确
			self::$errCode = 200;
			self::$errCode = '订单号类型不正确！';
			return FALSEl;
		}
		
		//$oi_obj = new OrderInfoModel ();
		$orderinfo = OrderInfoModel::getOrderInfo ( $orderid );
		if ($orderinfo == FALSE) { // 未找到订单
			switch (OrderInfoModel::$errCode) {
				case '003' :
					self::$errCode = 201;
					break;
				case '004' :
					self::$errCode = 203;
					break;
			}
			self::$errMsg = OrderInfoModel::$errMsg;
			return FALSE;
		}
		
		
		
		if (900 != $orderinfo['orderStatus']) {	//当前状态不可以申请
			self::$errCode = 204;
			self::$errMsg = '当前状态不可申请异常';
			return FALSE;
		}
		
		$ro_obj = new RequestOpenApiModel();
		
		$parameter = array(
			/* API系统级输入参数 Start */
			'method' => 'wh.discardShippingOrder',  //API名称
			'format' => 'json',  //返回格式
			'v' => '1.0',   //API版本号
			'username'	 => 'valsun.cn',
			/* API系统级参数 End */
			
			/* API应用级输入参数 Start*/
			'originOrderId'=>$orderid,
			'storeId'=>1
		);
		//print_r($parameter);exit;
		$sendresult = $ro_obj->sendRequest($parameter);
		//print_r( $sendresult); exit;
		$sendresult = json_decode($sendresult, TRUE);
		
		
		if (FALSE != $sendresult['errCode']) {	//推送消息到仓库系统失败
			self::$errCode = 205;
			self::$errMsg = $sendresult['errMsg'];
			return FALSE;
		}
		
		$updateresult = $oi_obj->changStatusToException($orderid);
		if (FALSE == $updateresult) {	//更新状态失败
			self::$errCode = 206;
			self::$errMsg = '申请失败！';
			return FALSE;
		}
		
		self::$errCode = 207;
		self::$errMsg = '申请成功！';
		return TRUE;
	}
}
