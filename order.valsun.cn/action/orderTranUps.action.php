<?php
/**
 * 类名：OrderTranUpsAct
 * 功能：UPS美国专线订单信息导入导出action
 * 版本：1.0
 * 日期：2014/03/01
 * 作者：管拥军
 */
class OrderTranUpsAct{
	public static $dbConn;
	public static $errCode		= 0;
	public static $errMsg		= "";
	
	//初始化db类
	public static function	initDB(){
		global $dbConn;
		self::$dbConn	= $dbConn;
	}
	
	/**
	 * OrderTranUpsAct::act_export_ups_xml_info()
	 * 导出选中的UPS美国专线订单信息(XML格式)
	 * @param string $ids 订单编号
	 * @return  array
	 */
	public static function act_export_ups_xml_info(){
		// $ids	= isset($_REQUEST["ids"]) ? $_REQUEST["ids"] : "";
		// $ids	= array(419835,419755);
		// if (empty($ids)) {
			// self::$errCode  = 10000;
			// self::$errMsg   = "订单参数有误";
			// return false;
		// }
		$act	= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod	= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if	(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 10002;
			self::$errMsg   = "对不起,您暂无权使用此功能！";
			return false;
		}
		$res			= OrderTranUpsModel::export_ups_xml_info($ids);
		self::$errCode  = OrderTranUpsModel::$errCode;
        self::$errMsg   = OrderTranUpsModel::$errMsg;
        return $res;		
	}			
}
?>