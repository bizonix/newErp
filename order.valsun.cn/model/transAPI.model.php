<?php
/**
 * 对接运输方式管理系统
 * add by Herman.Xi
 * last modified by 20131227
 */

class TransAPIModel {
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
	
	//通过接口获取渠道列表
	public static function getChannelistByApi(){
		require_once WEB_PATH."api/include/functions.php";
		
		$url	= 'http://gw.open.valsun.cn:88/router/rest?';
		$paramArr= array(
			/* API系统级输入参数 Start */
			'method'	=> 'trans.carrier.channel.info.get',  //API名称
			'format'	=> 'json',  //返回格式
			'v'			=> '1.0',   //API版本号
			'username'	=> C('OPEN_SYS_USER'),
			/* API系统级参数 End */
			/* API应用级输入参数 Start*/			
			'carrierId' => 'all'

			/*'purchaseId'		=> $purchaseId, */ //主料号
			/* API应用级输入参数 End*/
		);
		$result 	= callOpenSystem($paramArr);
		$data 		= json_decode($result, true);
		return $data['data'];		
	}

	//通过接口获取渠道列表
	public static function getPositionBySku($sku){
		require_once WEB_PATH."api/include/functions.php";
		
		$url	= 'http://gw.open.valsun.cn:88/router/rest?';
		$paramArr= array(
			/* API系统级输入参数 Start */
			'method'	=> 'wh.getSkuPositions',  //API名称
			'format'	=> 'json',  //返回格式
			'v'			=> '1.0',   //API版本号
			'username'	=> C('OPEN_SYS_USER'),
			/* API系统级参数 End */
			/* API应用级输入参数 Start*/			
			'sku' 		=> $sku,
			'storeId'	=> '1'

			/*'purchaseId'		=> $purchaseId, */ //主料号
			/* API应用级输入参数 End*/
		);		

		$result 	= callOpenSystem($paramArr);
		$data 		= json_decode($result, true);	
		$data 		= json_decode($data['data'], true);
		return $data[0]['pName'];				
	}
	
	/*
     *获取运输方式列表信息
	 *填写正确的运输方式参数类型（0非快递，1快递，2全部）
     */
	public static function getCarrierList($type = 2){
		/*global $memc_obj;
		$memc_name = md5('trans_system_carrier'.$type);
		$shippingInfo = $memc_obj->get_extral($memc_name);*/
		
		require_once WEB_PATH."api/include/functions.php";
		
		$url	= 'http://gw.open.valsun.cn:88/router/rest?';
		$paramArr= array(
			/* API系统级输入参数 Start */
			'method'	=> 'trans.carrier.info.get',  //API名称
			'format'	=> 'json',  //返回格式
			'v'			=> '1.0',   //API版本号
			'username'	=> 'purchase',
			/* API系统级参数 End */				 

			/* API应用级输入参数 Start*/
			'type'      => "{$type}"
			/* API应用级输入参数 End*/
		);
		$result = callOpenSystem($paramArr);
		$data = json_decode($result,true);
		return $data['data'];
    }
	
	/*
	 *新系统最优运费计算方法
	 *version 2.0
	 *add by herman.xi @20140411
	 */
	public static function trans_carriers_best_get($totalweight,$ebay_countryname,$ebay_account,$ebay_total,$zipCode="",$noShipId=""){
		require_once WEB_PATH."api/include/functions.php";
		
		$url	= 'http://gw.open.valsun.cn:88/router/rest?';
		$paramArr   = array(
			'method' 		=> 'trans.carriers.best.get',  //API名称
			'format' 		=> 'json',  //返回格式
			'v' 			=> '2.0',   //API版本号
			'username'	 	=> C('OPEN_SYS_USER'),
		);
		$paramArr['country'] = $ebay_countryname;
		$paramArr['weight'] = $totalweight;
		$paramArr['shipAddId'] = 1;
		if(isset($zipCode)){
			$paramArr['postCode'] = $zipCode;
		}
		if(isset($noShipId)){
			$paramArr['noShipId'] = $noShipId;
		}
		$rtn = callOpenSystem($paramArr);
		$rtn = json_decode($rtn, true);
		if(!isset($rtn['data'])){
			return $rtn;
		}
		return $rtn['data'];
	}
	
	/*
	 *新系统固定运费计算方法
	 *version 2.0
	 *add by herman.xi @20140411
	 */
	public static function trans_carriers_fix_get($carrierId, $totalweight, $ebay_countryname){
		require_once WEB_PATH."api/include/functions.php";
		
		$url	= 'http://gw.open.valsun.cn:88/router/rest?';
		$paramArr   = array(
			'method' 		=> 'trans.carriers.fix.get',  //API名称
			'format' 		=> 'json',  //返回格式
			'v' 			=> '2.0',   //API版本号
			'username'	 	=> C('OPEN_SYS_USER'),
		);
		/*if($ebay_carrier == "新加坡小包挂号"){
			$ebay_carrier = "新加坡邮政";
		}*/
		/*//$CarrierLists = getCarrierListById();
		$CarrierLists = array(1=> "中国邮政平邮", 2=> "中国邮政挂号", 3=> "香港小包平邮", 4=> "香港小包挂号", 5=> "EMS", 6=> "EUB", 8=> "DHL", 9=> "FedEx", 10=> "Global Mail", 46=> "UPS Ground", 47=> "USPS", 48=> "顺丰快递", 49=> "圆通快递", 50=> "申通快递", 51=> "韵达快递" , 52=> "新加坡邮政" , 53=> "德国邮政挂号" , 54=> "中通快递" , 55=> "汇通快递" , 56=> "国通快递" , 57=> "加运美快递" , 58=> "UPS" , 59=> "飞腾DHL" , 60=> "上门提货" , 61=> "运德物流" , 62=> "UPS美国专线" , 63=> "英国专线挂号" , 64=> "天天快递" , 65=> "SurePost" , 66=> "同城速递" , 67=> "国内快递" , 68=> "自提" , 69=> "送货上门" , 70=> "TNT" , 71=> "城市之星物流" , 72=> "优速快递" , 73=> "速尔快递" , 74=> "天地华宇物流" , 75=> "德邦物流" , 76=> "盛辉物流" , 77=> "vietnam" , 78=> "快捷快递" , 79=> "俄速通挂号" , 80=> "俄速通平邮" , 81=> "俄速通大包" , 82=> "海运运输");
		$flip_CarrierLists = array_flip($CarrierLists);
		if(!isset($flip_CarrierLists[$ebay_carrier])){
			return false;
		}else{
			$carrierId = $flip_CarrierLists[$ebay_carrier];
		}*/
		$paramArr['carrierId'] = $carrierId;
		$paramArr['country'] = $ebay_countryname;
		$paramArr['weight']  = $totalweight;
		$rtn = callOpenSystem($paramArr);
		$rtn = json_decode($rtn, true);
		if(!isset($rtn['data'])){
			return false;	
		}
		return $rtn['data'];
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
}