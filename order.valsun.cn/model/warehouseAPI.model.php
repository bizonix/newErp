<?php
/**
 * 对接仓库系统的API接口
 * add by 黄伟生 @20131125
 */

class WarehouseAPIModel{
	
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
	
	//取消交易,仓库废弃订单
	public static function discardShippingOrder($orderid, $storeId = 1){
		require_once WEB_PATH."api/include/functions.php";
		
		$url	= 'http://gw.open.valsun.cn:88/router/rest?';
		$paramArr= array(
			/* API系统级输入参数 Start */
			'method'	=> 'wh.ordersDiscard',  //API名称
			'format'	=> 'json',  //返回格式
			'v'			=> '1.0',   //API版本号
			'username'	=> C('OPEN_SYS_USER'),
			/* API系统级参数 End */				 

			/* API应用级输入参数 Start*/
			'oidStr'=>$orderid,
			'storeId'=>$storeId
			/* API应用级输入参数 End*/
		);
		$result = callOpenSystem($paramArr);
		
		$data = json_decode($result,true);
		//var_dump($data);
		if(empty($data['data'])){
			return array();
		}
		return $data['data'];
	}
	
	//获取新仓库系统实际库存
	public static function getSkuStock($sku, $storeId = 1){
		require_once WEB_PATH."api/include/functions.php";
		
		$url	= 'http://gw.open.valsun.cn:88/router/rest?';
		$paramArr= array(
			/* API系统级输入参数 Start */
			'method'	=> 'wh.getSkuStock',  //API名称
			'format'	=> 'json',  //返回格式
			'v'			=> '1.0',   //API版本号
			'username'	=> C('OPEN_SYS_USER'),
			/* API系统级参数 End */				 

			/* API应用级输入参数 Start*/
			'sku'       => $sku,
			'storeId'   => $storeId,
			/* API应用级输入参数 End*/
		);
		$result = callOpenSystem($paramArr);
		
		$data = json_decode($result,true);
		//var_dump($data);
		if(empty($data['data'])){
			return 0;
		}
		return $data['data'];
	}
	
	//接口测试
	public static function getText(){
		require_once WEB_PATH."api/include/functions.php";
		
		$url	= 'http://gw.open.valsun.cn:88/router/rest?';
		$paramArr= array(
			/* API系统级输入参数 Start */
			'method'	=> 'order.system.getfirstsale',  //API名称
			'format'	=> 'json',  //返回格式
			'v'			=> '1.0',   //API版本号
			'username'	=> C('OPEN_SYS_USER'),
			/* API系统级参数 End */
			/* API应用级输入参数 Start*/
			'sku'       => '8812'
			/*'purchaseId'		=> $purchaseId, */ //主料号
			/* API应用级输入参数 End*/
		);
		$result 	= callOpenSystem($paramArr, $url);
		$data 		= json_decode($result, true);
		var_dump($data);
	}
	
	//获取订单下仓库配货记录@20131126
	public static function getOrderPickingInfo($orderId){
		require_once WEB_PATH."api/include/functions.php";
		
		$url	= 'http://gw.open.valsun.cn:88/router/rest?';
		$paramArr= array(
			/* API系统级输入参数 Start */
			'method'	=> 'wh.getOrderPickingInfo',  //API名称
			'format'	=> 'json',  //返回格式
			'v'			=> '1.0',   //API版本号
			'username'	=> C('OPEN_SYS_USER'),
			/* API系统级参数 End */
			
			/* API应用级输入参数 Start*/
			'orderId'   => $orderId
			/* API应用级输入参数 End*/
		);
		$result = callOpenSystem($paramArr);
		
		$data = json_decode($result,true);
		if(empty($data['data'])){
			return array();	
		}
		return $data['data'];	
	}
	//获取仓库配货记录@20131126
	public static function getOrderSkuPickingRecords($orderId, $sku){
		require_once WEB_PATH."api/include/functions.php";
		
		$url	= 'http://gw.open.valsun.cn:88/router/rest?';
		$paramArr= array(
			/* API系统级输入参数 Start */
			'method'	=> 'wh.getOrderSkuPickingRecords',  //API名称
			'format'	=> 'json',  //返回格式
			'v'			=> '1.0',   //API版本号
			'username'	=> C('OPEN_SYS_USER'),
			/* API系统级参数 End */				 

			/* API应用级输入参数 Start*/
			'orderId'   => $orderId,
			'sku'       => $sku
			/* API应用级输入参数 End*/
		);
		$result = callOpenSystem($paramArr);
		
		$data = json_decode($result,true);
		if(empty($data['data'])){
			return array();	
		}
		return $data['data'];	
	}
	
	//拉取异常订单接口，不用传参考，返回订单系统订单数组@20140117
	public static function getAbOrderList(){
		require_once WEB_PATH."api/include/functions.php";
		
		$url	= 'http://gw.open.valsun.cn:88/router/rest?';
		$paramArr= array(
			/* API系统级输入参数 Start */
			'method'	=> 'wh.getAbOrderList',  //API名称
			'format'	=> 'json',  //返回格式
			'v'			=> '1.0',   //API版本号
			'username'	=> C('OPEN_SYS_USER'),
			/* API系统级参数 End */				 

			/* API应用级输入参数 Start*/
			/*'orderId'   => $orderId,
			'sku'       => $sku*/
			/* API应用级输入参数 End*/
		);
		$result = callOpenSystem($paramArr);
		
		$data = json_decode($result,true);
		//var_dump($data); echo "<br>";
		if(empty($data['data'])){
			return array();	
		}
		return $data['data'];
		$rtn = array();
		foreach($data['data'] as $value){
			$rtn[] = $value['originOrderId'];
		}
		return $rtn;
	}
	
	//获取订单料号配货信息，参数orderId（订单系统订单号）@20140117
	//返回料号配货value值为1,没有配货返回为0
	public static function getAbOrderInfo($orderId){
		require_once WEB_PATH."api/include/functions.php";
		
		$url	= 'http://gw.open.valsun.cn:88/router/rest?';
		$paramArr= array(
			/* API系统级输入参数 Start */
			'method'	=> 'wh.getAbOrderInfo',  //API名称
			'format'	=> 'json',  //返回格式
			'v'			=> '1.0',   //API版本号
			'username'	=> C('OPEN_SYS_USER'),
			/* API系统级参数 End */				 

			/* API应用级输入参数 Start*/
			'orderId'   => $orderId
			/* API应用级输入参数 End*/
		);
		$result = callOpenSystem($paramArr);
		
		$data = json_decode($result,true);
		if(empty($data['data'])){
			return array();	
		}
		return $data['data'];
	}
	
	//拆分订单接口，参数orderId（订单系统订单号）@20140117
	public static function operateAbOrder($orderId,$calcWeight){
		require_once WEB_PATH."api/include/functions.php";
		
		$url	= 'http://gw.open.valsun.cn:88/router/rest?';
		$paramArr= array(
			/* API系统级输入参数 Start */
			'method'	=> 'wh.operateAbOrder',  //API名称
			'format'	=> 'json',  //返回格式
			'v'			=> '1.0',   //API版本号
			'username'	=> C('OPEN_SYS_USER'),
			/* API系统级参数 End */				 

			/* API应用级输入参数 Start*/
			'orderId'   => $orderId,
			'calcWeight' => $calcWeight
			/* API应用级输入参数 End*/
		);
		$result = callOpenSystem($paramArr);
		
		$data = json_decode($result,true);
		//var_dump($data);
		/*if(empty($data['data'])){
			return array();	
		}*/
		return $data['data'];	
	}
	
	//获取入库接口：wh.getInRecords@20140225
	public static function getInRecords(){
		require_once WEB_PATH."api/include/functions.php";
		
		$url	= 'http://gw.open.valsun.cn:88/router/rest?';
		$paramArr= array(
			/* API系统级输入参数 Start */
			'method'	=> 'wh.getInRecords',  //API名称
			'format'	=> 'json',  //返回格式
			'v'			=> '1.0',   //API版本号
			'username'	=> C('OPEN_SYS_USER'),
			/* API系统级参数 End */				 

			/* API应用级输入参数 Start*/
			/* API应用级输入参数 End*/
		);
		$result = callOpenSystem($paramArr);
		
		$data = json_decode($result,true);
		//var_dump($data);
		if(empty($data['data'])){
			return array();
		}
		return $data['data'];	
	}
	
	//获取出库接口：wh.getOutRecords@20140225
	public static function getOutRecords(){
		require_once WEB_PATH."api/include/functions.php";
		
		$url	= 'http://gw.open.valsun.cn:88/router/rest?';
		$paramArr= array(
			/* API系统级输入参数 Start */
			'method'	=> 'wh.getOutRecords',  //API名称
			'format'	=> 'json',  //返回格式
			'v'			=> '1.0',   //API版本号
			'username'	=> C('OPEN_SYS_USER'),
			/* API系统级参数 End */				 

			/* API应用级输入参数 Start*/
			/* API应用级输入参数 End*/
		);
		$result = callOpenSystem($paramArr);
		
		$data = json_decode($result,true);
		//var_dump($data);
		if(empty($data['data'])){
			return array();
		}
		return $data['data'];	
	}
	
}