<?php
/**
 * 过渡期切换公共相关类
 * add by Herman.Xi @20131110
 */

class OldsystemModel{
    public static $cateInfo      = NULL;
	public static $materInfo     = NULL;

	public static $dbConn;
	private static $_instance;
	private static $errCode;
	private static $errMsg;
	public static $tempsyncTable = 'temp_sync_order_records';

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

	/*
     * 获取sku库存信息
	 *@para $sku as string
     */
	public static function qccenterGetErpGoodscount($sku){
		require_once WEB_PATH."api/include/functions.php";

		$url	= 'http://gw.open.valsun.cn:88/router/rest?';
		$paramArr= array(
			/* API系统级输入参数 Start */
			'method'	=> 'qccenter.get.erp.goodscount',  //API名称
			'format'	=> 'json',  //返回格式
			'v'			=> '1.0',   //API版本号
			'username'	=> 'purchase',
			/* API系统级参数 End */

			/* API应用级输入参数 Start*/
			'goods_sn' => $sku
			/* API应用级输入参数 End*/
		);
		$result = callOpenSystem($paramArr , $url);

		$data = json_decode($result,true);
		if(empty($data['data'])){
			return false;
		}
		return $data['data'];

		/*$paArr = array(
			'goods_sn' => $sku
		);
		$conversion_sku = self::getOpenSysApi('qccenter.get.erp.goodscount', $paArr);
		return $conversion_sku['data'];*/
	}

	/*
     * 获取总待发货数量
	 *@para $sku as string
     */
	public static function	getpartsaleandnosendallSQL($sku){
		self::initDB();
		$sql             = "SELECT * FROM  `om_sku_statistics` WHERE sku = '{$sku}' ";
		$query	         =	self::$dbConn->query($sql);
		$goodsInfo       =	self::$dbConn->fetch_array($query);
        return $goodsInfo;
	}

	/*
     * 获取总待发货数量
	 *@para $sku as string
     */
	public static function	getpartsaleandnosendall($sku, $isCache = true){
		require_once WEB_PATH."api/include/functions.php";

		$url	= 'http://gw.open.valsun.cn:88/router/rest?';
		$paramArr= array(
			/* API系统级输入参数 Start */
			'method'	=> 'order.erp.getpartsaleandnosendall',  //API名称
			'format'	=> 'json',  //返回格式
			'v'			=> '1.0',   //API版本号
			'username'	=> 'purchase',
			/* API系统级参数 End */

			/* API应用级输入参数 Start*/
			'sku' => $sku
			/* API应用级输入参数 End*/
		);
		$result = callOpenSystem($paramArr, $url);

		$data = json_decode($result,true);
		if(empty($data['data'])){
			$data['data'] = CommonModel::getpartsaleandnosendall($sku); //添加新系统待发货数量
		}
		/*if(empty($data['data'])){
			return false;
		}*/
		if($isCache){//同步更新待发货缓存表
			$sendArr = array('salensend' => $data['data']);
			CommonModel::updateSkuStatistics($sku,$sendArr);
		}
		//var_dump($data['data']);
		return $data['data'];
	}

	/*
     * 获取待发货数量(不包含超大订单审核通过的)
	 *@para $sku as string
     */
	public static function	getsaleandnosendall($sku){
		require_once WEB_PATH."api/include/functions.php";

		$url	= 'http://gw.open.valsun.cn:88/router/rest?';
		$paramArr= array(
			/* API系统级输入参数 Start */
			'method'	=> 'order.erp.getsaleandnosendall',  //API名称
			'format'	=> 'json',  //返回格式
			'v'			=> '1.0',   //API版本号
			'username'	=> 'purchase',
			/* API系统级参数 End */

			/* API应用级输入参数 Start*/
			'sku' => $sku
			/* API应用级输入参数 End*/
		);
		$result = callOpenSystem($paramArr);

		$data = json_decode($result,true);
		if(empty($data['data'])){
			return false;
		}
		return $data['data'];
	}

	/*
     * 获取包含超大订单审核通过占用待发货数量，拦截数量
	 *@para $sku as string
     */
	public static function	get_partsalenosend($sku){
		require_once WEB_PATH."api/include/functions.php";

		$url	= 'http://gw.open.valsun.cn:88/router/rest?';
		$paramArr= array(
			/* API系统级输入参数 Start */
			'method'	=> 'order.erp.get_partsalenosend',  //API名称
			'format'	=> 'json',  //返回格式
			'v'			=> '1.0',   //API版本号
			'username'	=> 'purchase',
			/* API系统级参数 End */

			/* API应用级输入参数 Start*/
			'sku' => $sku
			/* API应用级输入参数 End*/
		);
		$result = callOpenSystem($paramArr);

		$data = json_decode($result,true);
		if(empty($data['data'])){
			return false;
		}
		return $data['data'];
	}

	/*
     * 更新 ebay_sku_statistics
	 *@para $sku as string
     */
	public static function	updateSkuStatistics($sku,$data){
		require_once WEB_PATH."api/include/functions.php";

		$url	= 'http://gw.open.valsun.cn:88/router/rest?';
		$paramArr= array(
			/* API系统级输入参数 Start */
			'method'	=> 'order.erp.updateSkuStatistics',  //API名称
			'format'	=> 'json',  //返回格式
			'v'			=> '1.0',   //API版本号
			'username'	=> 'purchase',
			/* API系统级参数 End */

			/* API应用级输入参数 Start*/
			'sku' => $sku,
			'data'=> $data
			/* API应用级输入参数 End*/
		);
		$result = callOpenSystem($paramArr);

		$data = json_decode($result,true);

		if($data['res_code'] == 200){
			return true;
		}
		return false;
	}

	/*
     * 获取可用库存
	 *@para $sku as string
     */
	public static function	getEnableGoodscount($sku, $storeId = 1){
		$skuInfo = self::qccenterGetErpGoodscount($sku);
		$saleand = self::getpartsaleandnosendallSQL($sku);
		return $skuInfo['goods_count']-$saleand['salensend'];
	}

	/*
     * 获取旧系统订单信息
	 *@para $sku as string
     */
	public static function	getERPorderinfo($scantime,$ebay_account,$ebay_status=''){
		require_once WEB_PATH."api/include/functions.php";

		$url	= 'http://gw.open.valsun.cn:88/router/rest?';
		$paramArr= array(
			/* API系统级输入参数 Start */
			'method'	=> 'erp.get.orderinfo',  //API名称
			'format'	=> 'json',  //返回格式
			'v'			=> '1.0',   //API版本号
			'username'	=> C('OPEN_SYS_USER'),
			/* API系统级参数 End */

			/* API应用级输入参数 Start*/
			'scantime' => $scantime,
			'ebay_account' => $ebay_account
			/* API应用级输入参数 End*/
		);
		if($ebay_status){
			$paramArr['ebay_status'] = $ebay_status;
		}
		$result = callOpenSystem($paramArr,$url);

		$data = json_decode($result,true);
		if(empty($data['data'])){
			return array();
		}
		return $data['data'];
	}

	/*
     * 将订单信息插入到老系统
	 *@para $sku as string
     */
	public static function	orderErpInsertorder($orderData){
		require_once WEB_PATH."api/include/functions.php";

		$url	= 'http://gw.open.valsun.cn:88/router/rest?';
		$method = '';
		if(C('IS_ONLINE') == 'YES'){//线上环境
			$method = 'order.erp.insertorder';
		}elseif(C('IS_ONLINE') == 'NO'){//测试环境
			$method = 'order.198erp.insertorder';
		}

		$paramArr= array(
			/* API系统级输入参数 Start */
			'method'	=> $method,  //API名称
			'format'	=> 'json',  //返回格式
			'v'			=> '1.0',   //API版本号
			'app_key'	=> C('OPEN_SYS_USER'),
			'protocol'	=> 'param2',
			'timestamp'	=> date('Y-m-d H:i:s'),
			/* API系统级参数 End */

			/* API应用级输入参数 Start*/
			'orderArr' => json_encode($orderData)
		);
		$result = callOpenSystemPost($paramArr,$url);
		$data = json_decode($result,true);
		return $data;
		/*if(empty($data['data'])){
			return $data;
		}
		return $data['data'];*/
	}

	/*
	 * 插入临时信息(最新版)
	 * last modified by Herman.Xi @20131205
	 */
	public static function insertTempSyncRecords($data){
		!self::$dbConn ? self::initDB() : null;
		$string = array2sql($data);
		$sql = "INSERT INTO ".self::$tempsyncTable." SET {$string} ";
		//echo $sql; echo "\n";
		if(!self::$dbConn->query($sql)){
			self :: $errCode = "002";
			self :: $errMsg =  " 插入临时信息失败！";
			return false; //失败则设置错误码和错误信息， 返回false
		}
		self :: $errCode = "200";
		self :: $errMsg =  " 插入临时信息成功！";
		return true; //失败则设置错误码和错误信息， 返回false
	}

	/*
	 * 同步老系统订单状态接口
	 * last modified by Herman.Xi @20140516
	 */
	public static function ordererpupdateStatus($orderId,$ebay_status,$final_status){
		require_once WEB_PATH."api/include/functions.php";
		
		$url	= 'http://gw.open.valsun.cn:88/router/rest?';
		$paramArr= array(
			/* API系统级输入参数 Start */
			'method'	=> 'order.erp.updateStatus',  //API名称
			'format'	=> 'json',  //返回格式
			'v'			=> '1.0',   //API版本号
			'app_key'	=> C('OPEN_SYS_USER'),
			'protocol'	=> 'param2',
			'timestamp'	=> date('Y-m-d H:i:s'),
			/* API系统级参数 End */				 

			/* API应用级输入参数 Start*/
			'ebay_id'     => $orderId,
			'ebay_status'  => $ebay_status,
			'final_status' => $final_status,
			'truename'     => $_SESSION['userCnName'],
		);
		$result = callOpenSystemPost($paramArr,$url);
		
		$data = json_decode($result,true);
		if(empty($data['data'])){
			return $data;
		}
		return $data['data'];
	}
	
	/*
	 * 同步老系统文件夹权限
	 * @$movefolders array
	 * @$nameCn string
	 */
	public static function erpSyncMovefolders($moveFolders,$nameCn){
		require_once WEB_PATH."api/include/functions.php";
		
		$url	= 'http://gw.open.valsun.cn:88/router/rest?';
		//$method = '';
		//if(C('IS_ONLINE') == 'YES'){//线上环境
		//	$method = 'order.erp.movefolders';
		//}elseif(C('IS_ONLINE') == 'NO'){//测试环境
		//$method = 'order.198erp.moveFolders';
		$method = 'order.erp.movefolders';
		//}
		
		$paramArr= array(
				/* API系统级输入参数 Start */
				'method'	=> $method,  //API名称
				'format'	=> 'json',  //返回格式
				'v'			=> '1.0',   //API版本号
				'app_key'	=> C('OPEN_SYS_USER'),
				'protocol'	=> 'param2',
				'timestamp'	=> date('Y-m-d H:i:s'),
				/* API系统级参数 End */
		
				/* API应用级输入参数 Start*/
				'movefolders' => $moveFolders,
				'cnName' => $nameCn
		);
		$result = callOpenSystemPost($paramArr,$url);
		$data = json_decode($result,true);
		return $data;
	}
	
	/*
	 * 同步老系统平台增删改
	* @$platform string
	* @$handle string 说明是什么操作,传递三个值：insert,update,delete 
	*/
	public static function erpSyncPlatform($new_platform,$handle,$old_platform=''){
		require_once WEB_PATH."api/include/functions.php";
	
		$url	= 'http://gw.open.valsun.cn:88/router/rest?';
		$method = '';
		if(C('IS_ONLINE') == 'YES'){//线上环境
			$method = 'order.erp.updatePlatform';
		}elseif(C('IS_ONLINE') == 'NO'){//测试环境
			$method = 'order.198erp.updatePlatform';
		}
		$paramArr= array(
				/* API系统级输入参数 Start */
				'method'	=> $method,  //API名称
				'format'	=> 'json',  //返回格式
				'v'			=> '1.0',   //API版本号
				'app_key'	=> C('OPEN_SYS_USER'),
				'protocol'	=> 'param2',
				'timestamp'	=> date('Y-m-d H:i:s'),
				/* API系统级参数 End */
	
				/* API应用级输入参数 Start*/
				'new_platform' => $new_platform,
				'old_platform' => $old_platform,
				'handle' => $handle
		);
		$result = callOpenSystemPost($paramArr,$url);
		$data = json_decode($result,true);
		return $data;
	}
}