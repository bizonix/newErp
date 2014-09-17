<?php
/**
 * 对接仓库系统的API接口
 * add by 黄伟生 @20131125
 */

class PurchaseAPIModel{
	
	public static $dbConn;
	private static $_instance;
	
	public static function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
	}
	
	//单实例
    public static function getInstance(){
        if(!(self::$_instance instanceof self)){
            self::$_instance = new self();
        }
        return self::$_instance;
    }
	
	public static function discardShippingOrder($orderid, $storeId = 1){
		require_once WEB_PATH."api/include/functions.php";
		
		$url	= 'http://gw.open.valsun.cn:88/router/rest?';
		$paramArr= array(
			/* API系统级输入参数 Start */
			'method'	=> 'wh.discardShippingOrder',  //API名称
			'format'	=> 'json',  //返回格式
			'v'			=> '1.0',   //API版本号
			'username'	=> 'purchase',
			/* API系统级参数 End */				 

			/* API应用级输入参数 Start*/
			'originOrderId'=>$orderid,
			'storeId'=>$storeId
			/* API应用级输入参数 End*/
		);
		$result = callOpenSystem($paramArr, $url);
		
		$data = json_decode($result,true);
		if(empty($data['data'])){
			return array();
		}
		return $data['data'];
	}
	
	public static function getSkuDailyStatus($sku){
		self::initDB();
		$sql             = "SELECT * FROM  `om_sku_daily_status` WHERE sku = '{$sku}' ";
		$query	         =	self::$dbConn->query($sql);	
		$goodsInfo     =	self::$dbConn->fetch_array($query);
		if(!$goodsInfo){
			return false;
		}
		$goodsInfo['purchaseName'] = UserModel::getUsernameById($goodsInfo['purchaseId']);
		$goodsInfo['enableCount'] = 0;//OldsystemModel::getEnableGoodscount($sku);
        return $goodsInfo;
	}
	
	public static function getAdjustransportFromPurchase($get=1){
		require_once WEB_PATH."api/include/functions.php";
		
		$url	= 'http://gw.open.valsun.cn:88/router/rest?';
		$paramArr= array(
			/* API系统级输入参数 Start */
			'method'	=> 'purchase.getAdjustransport',  //API名称
			'format'	=> 'json',  //返回格式
			'v'			=> '1.0',   //API版本号
			'username'	=> 'purchase',
			/* API系统级参数 End */				 

			/* API应用级输入参数 Start*/
			'get'		=> $get
			/* API应用级输入参数 End*/
		);
		$result = callOpenSystem($paramArr);
		//var_dump($result);
		$data = json_decode($result,true);
		/*var_dump($data);
		if(!isset($data['data'])){
			return array();
		}*/
		$__liquid_items_array = array();
		foreach($data as $dataValue){
			$__liquid_items_array[$dataValue['category']] = $dataValue['skulist'];
		}
		/*foreach($data['data'] as $dataValue){
			$__liquid_items_array[$dataValue['category']] = $dataValue['skulist'];
		}*/
		return $__liquid_items_array;
	}
	
	//接口测试
	public static function getText(){
		require_once WEB_PATH."api/include/functions.php";
		$start = time();
		$url	= 'http://gw.open.valsun.cn:88/router/rest?';
		$paramArr= array(
			/* API系统级输入参数 Start */
			'method'	=> 'om.showSuperOrder',  //API名称
			'format'	=> 'json',  //返回格式
			'v'			=> '1.0',   //API版本号
			'username'	=> C('OPEN_SYS_USER'),
			/* API系统级参数 End */
			/* API应用级输入参数 Start*/
			'purchaseId'       => '149'
			/*'purchaseId'		=> $purchaseId, */ //主料号
			/* API应用级输入参数 End*/
		);
		$result 	= callOpenSystem($paramArr);
		$data 		= json_decode($result, true);
		$end = time();
		echo "<pre>";
		var_dump($end-$start);
		var_dump($data);
	}
	
}