<?php
/**
 * 类名：OrderPushAct
 * 功能：订单系统推送给仓库系统处理层
 * 版本：1.0
 * 日期：2013/9/20
 * 作者：管拥军
 */
class  OrderPushAct{
    public static $errCode = 0;
    public static $errMsg = '';
		
	/**
	 * OrderPushAct::act_pushMessage()
	 * 获取订单推送信息状态
	 * @return bool 
	 */
	public function act_pushMessage(){
		$orderid	= isset($_GET["orderid"]) ? intval($_GET["orderid"]) : 0;
		$flag		= isset($_GET["flag"]) ? intval($_GET["flag"]) : 2;
		if (!$orderid) {
			self::$errCode  = "1001";
			self::$errMsg   = "订单号参数非法";
			return false;
		}
		/*if ($flag > 1) {
			self::$errCode  = "1002";
			self::$errMsg   = "标记参数非法";
			return false;
		}*/
        $result			= OrderPushModel::listPushMessage($orderid,$flag);
		self::$errCode  = OrderPushModel::$errCode;
        self::$errMsg   = OrderPushModel::$errMsg;
		return $result;
    }
	
	/**
	 * OrderPushAct::act_getorderinfo()
	 * 
	 * @return bool 
	 */
	public function act_getOrderinfo(){
		$ostatus	= isset($_POST["ostatus"]) ? intval($_POST["ostatus"]) : '';
		$otype		= isset($_POST["otype"]) ? intval($_POST["otype"]) : '';
		$flag		= isset($_POST["flag"]) ? intval($_POST["flag"]) : 2;
		if (!$ostatus) {
			self::$errCode  = "1001";
			self::$errMsg   = "无一级状态";
			return false;
		}
        $result			= OrderPushModel::getOrderinfo($ostatus,$otype,$flag);
		self::$errCode  = OrderPushModel::$errCode;
        self::$errMsg   = OrderPushModel::$errMsg;
		return $result;
    }
	
	/**
	 * OrderPushAct::申请打印()
	 * 
	 * @return bool 
	 */
	public function act_applyAllPrint(){
		$ostatus	= isset($_POST["ostatus"]) ? intval($_POST["ostatus"]) : '';
		$otype		= isset($_POST["otype"]) ? intval($_POST["otype"]) : '';
		$flag		= isset($_POST["flag"]) ? intval($_POST["flag"]) : 2;
		if (!$ostatus) {
			self::$errCode  = "1001";
			self::$errMsg   = "无一级状态";
			return false;
		}
        $result			= OrderPushModel::applyAllPrint($ostatus,$otype,$flag);
		self::$errCode  = OrderPushModel::$errCode;
        self::$errMsg   = OrderPushModel::$errMsg;
		return $result;
    }
	
	/**
	 * OrderPushAct::申请部分打印()
	 * 
	 * @return bool 
	 */
	public function act_applyPartPrint(){
		$orderid_arr = isset($_POST["orderid_arr"]) ? $_POST["orderid_arr"] : '';
		$ostatus	= isset($_POST["ostatus"]) ? intval($_POST["ostatus"]) : '';
		$otype		= isset($_POST["otype"]) ? intval($_POST["otype"]) : '';
		$flag		= isset($_POST["flag"]) ? intval($_POST["flag"]) : 2;
		/*if (!$ostatus) {
			self::$errCode  = "1001";
			self::$errMsg   = "无一级状态";
			return false;
		}*/
        $result			= OrderPushModel::applyPartPrint($orderid_arr,$ostatus,$otype,$flag);
		self::$errCode  = OrderPushModel::$errCode;
        self::$errMsg   = OrderPushModel::$errMsg;
		return $result;
    }
		
}
?>