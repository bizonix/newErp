<?php
/**
 * 取消交易
*作者：txl
*
*/
class CancelDealAct extends Auth {
	public static $errCode = 0;
	public static $errMsg = '';
	
	/*
	 * 申请异常
	 */
	public function act_cancelDeal() {
		
		$orderid = isset ( $_GET ['orderid'] ) ? intval ( $_GET ['orderid'] ) : 0;
		$type = isset ( $_GET ['type'] ) ? intval ( $_GET ['type'] ) : 2;
		if ($orderid < 1) { // 传入订单号不正确
			self::$errCode = 300;
			self::$errMsg = '订单号类型不正确！';
			return FALSE;
		}
		//var_dump($orderid); exit;
		//$oi_obj = new OrderInfoModel ();
		$orderinfo = OrderInfoModel::getOrderInfo($orderid);
		//var_dump($orderinfo);
		if (!$orderinfo) { // 未找到订单
			self::$errCode = OrderInfoModel::$errCode;
			self::$errMsg = OrderInfoModel::$errMsg;
			return FALSE;
		}
		
		if ((C('STATESHIPPED') == $orderinfo['orderStatus']) && ( C('STATEHASSHIPPED_CONV') == $orderinfo['orderType']) ) {	//订单已发货不能取消交易
			self::$errCode = 304;
			self::$errMsg = '订单已完成不能取消交易!';
			return FALSE;
		}else if((C('STATESHIPPED') == $orderinfo['orderStatus']) && (C('STATESHIPPED_APPLYPRINT') != $orderinfo['orderType'])) {
			/*$ro_obj = new RequestOpenApiModel();
			
			$parameter = array(
				'method' => 'wh.discardShippingOrder',  //API名称
				'format' => 'json',  //返回格式
				'v' => '1.0',   //API版本号
				'username'	 => 'valsun.cn',
				'originOrderId'=>$orderid,
				'storeId'=>1
			);
			
			$sendresult = $ro_obj->sendRequest($parameter);
			$sendresult = json_decode($sendresult, TRUE);*/
			//$orderids = array(0=>$orderid);
			$sendresult = WarehouseAPIModel::discardShippingOrder($orderid);
			//var_dump($sendresult);
			
			if (!$sendresult) {	//推送消息到仓库系统失败
				self::$errCode = 305;
				self::$errMsg = "发货单仓库废弃失败";
				return FALSE;
			}
		}
		
		$rtn = OrderInfoModel::cancelDeal($orderid,$type);
		self::$errCode = OrderInfoModel::$errCode;
		self::$errMsg = OrderInfoModel::$errMsg;
		return $rtn;
	}
}
