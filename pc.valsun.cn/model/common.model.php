<?php
/**
 * 公共相关类
 * add by hws
 */

class CommonModel{
    public static $cateInfo      = NULL;
	public static $materInfo     = NULL;
	public static $shippingInfo  = NULL;
	public static $shippingInfo1 = NULL;
	public static $shippingInfo2 = NULL;
	public static $shippingInfo3 = NULL;
	public static $shippingInfo4 = NULL;
	public static $platformInfo  = NULL;
	public static $platformInfo2 = NULL;
	public static $positionInfo  = NULL;
	public static $locationInfo  = NULL;
	public static $locationInfo1 = NULL;
	public static $countrysInfo  = NULL;
	public static $countrysInfo2 = NULL;
	public static $countrysInfo3 = NULL;
	
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
	
	/*
     * 获取单个sku信息(兼容组合料号)
	 *@para $sku as string
     */
	public static function getSkuinfo($sku){
        global $memc_obj;
        $skuInfo = $memc_obj->get_extral('sku_info_'.$sku);
        return $skuInfo;
    }
	
	/*
     * 获取料号类别
     */
	public static function getCateinfo(){
        global $memc_obj;
        if (self::$cateInfo == NULL){
            self::$cateInfo = $memc_obj->get_extral('pc_goods_category');
        }
        return self::$cateInfo;
    }
	
	/*
     * 通过类别id获取类别名称
     */
    public static function getCateInfoById($cateId){
        $cateInfoList = self::getCateinfo();
        return  $cateInfoList[$cateId]['name'];
    }
	
	/*
     * 获取包材信息
     */
	public static function getMaterInfo(){
        global $memc_obj;
        if (self::$materInfo == NULL){
            self::$materInfo = $memc_obj->get_extral('pc_packing_material');
        }
        return self::$materInfo;
    }
	
	/*
     * 通过包材id获取包材名称
     */
    public static function geteMaterInfoById($MaterId){
        $materInfoList = self::getMaterInfo();
        return  $materInfoList[$MaterId];
    }
	
    /*
     * 获取全部的运输方式列表信息 通过api到运输方式管理系统获取
     */
    public static function getShipingTypeList(){
        global $memc_obj;
        if (self::$shippingInfo == NULL){
            self::$shippingInfo = $memc_obj->get_extral('trans_system_carrier');
        }
        return self::$shippingInfo;
    }
    
    /*
     * 运输方式id到运输方式名称映射函数 $id unsigned int 运输方式id
     */
    public static function getShipingNameById($id){
        $shippingInfoList = self::getShipingTypeList();
        return   $shippingInfoList[$id]['carrierNameCn'];
    }
    
	/*
     * 获取全部的分区信息
	 *return Array[运输方式ID][渠道ID] [分区ID]= 分区信息；
     */
    public static function getAreaList(){
        global $memc_obj;
        if (self::$shippingInfo1 == NULL){
            self::$shippingInfo1 = $memc_obj->get_extral('trans_system_carrierinfo');
        }
        return self::$shippingInfo1;
    }
	
	/*
     * 获取全部的渠道信息
	 *return Array[运输方式ID][渠道ID] = 渠道信息；
     */
    public static function getChannelist(){
        global $memc_obj;
        if (self::$shippingInfo2 == NULL){
            self::$shippingInfo2 = $memc_obj->get_extral('trans_system_channelinfo');
        }
        return self::$shippingInfo2;
    }

	/*
     * 获取平台信息
     */
	public static function getPlatformInfo(){
        global $memc_obj;
        if (self::$platformInfo == NULL){
            self::$platformInfo = $memc_obj->get_extral('trans_system_platform');
        }
        return self::$platformInfo;
    }
	
	/*
     * 通过平台id获取平台名称
     */
    public static function getPlatformInfoById($platformId){
        $platformInfoList = self::getPlatformInfo();
        return  $platformInfoList[$platformId];
    }
	
	/*
     * 获取平台信息(平台对应的运输方式)
	 *return Array[平台ID] []= 平台信息；
     */
	public static function getPlatCarrInfo(){
        global $memc_obj;
        if (self::$platformInfo1 == NULL){
            self::$platformInfo1 = $memc_obj->get_extral('trans_platform_carrierName');
        }
        return self::$platformInfo1;
    }
	
	/*
     * 获取平台信息
     */
	public static function getPositionInfo(){
        global $memc_obj;
        if (self::$positionInfo == NULL){
            self::$positionInfo = $memc_obj->get_extral('wh_position');
        }
        return self::$positionInfo;
    }
	
	/*
     * 通过仓位id获取仓位名称
     */
    public static function getPositionInfoById($positionId){
        $positionInfoList = self::getPositionInfo();
        return  $positionInfoList[$positionId]['pName'];
    }
	
	/*
     * 获取仓位信息
     */
	public static function getLocationInfo(){
        global $memc_obj;
        if (self::$locationInfo == NULL){
            self::$locationInfo = $memc_obj->get_extral('wh_sku_location');
        }
        return self::$locationInfo;
    }
	
	/*
     * 通过sku获取仓位信息
     */
    public static function getPositionInfoBySku($sku){
        $locationInfoList = self::getLocationInfo();
        return  $locationInfoList[$sku];
    }
	
	/*
     * 获取SKU对应仓位坐标信息列
	 *return Array[SKU][仓位ID]= 仓位坐标信息；
     */
	public static function getProPositionInfo(){
        global $memc_obj;
        if (self::$locationInfo1 == NULL){
            self::$locationInfo1 = $memc_obj->get_extral('wh_product_position');
        }
        return self::$locationInfo1;
    }
	
	/*
     * 获取国家信息列表
	 *return Array[ID] = 国家信息；
     */
	public static function getCountriesList(){
        global $memc_obj;
        if (self::$countrysInfo == NULL){
            self::$countrysInfo = $memc_obj->get_extral('trans_countries_standard');
        }
        return self::$countrysInfo;
    }
	
	/*
     * 根据国家简称获取国家信息列表
     */
	public static function getCountrieInfoBySn($countrySn){
        global $memc_obj;
        if (self::$countrysInfo2 == NULL){
            self::$countrysInfo2 = $memc_obj->get_extral('trans_countries_sn');
        }
        return self::$countrysInfo2[$countrySn];
    }
	
	/*
     * 根据国家英文全称获取国家信息列表
     */
	public static function getCountrieInfoByEn($countryNameEn){
        global $memc_obj;
        if (self::$countrysInfo1 == NULL){
            self::$countrysInfo1 = $memc_obj->get_extral('trans_countries_en');
        }
        return self::$countrysInfo1[$countryNameEn];
    }
	
	/*
     * 根据小语种国家名称改成英文国家名称
     */
	public static function getCountrieInfoBySmallCountries($smallCountry){
        global $memc_obj;
        $cacheName = md5('countries_small_comparison'.$smallCountry);
        self::$countrysInfo1 = $memc_obj->get_extral($cacheName);
        return self::$countrysInfo1;
    }
	
	/*
     *获取快递列表信息
	 *return Array[ID] = 快递信息；
     */
	public static function getCarrierExp(){
        global $memc_obj;
        if (self::$shippingInfo3 == NULL){
            self::$shippingInfo3 = $memc_obj->get_extral('trans_carrier_exp');
        }
        return self::$shippingInfo3;
    }
	
	/*
     *获取非快递列表信息
	 *return Array[ID] = 非快递信息；
     */
	public static function getCarrierNoExp(){
        global $memc_obj;
        if (self::$shippingInfo4 == NULL){
            self::$shippingInfo4 = $memc_obj->get_extral('trans_carrier_noexp');
        }
        return self::$shippingInfo4;
    }
	
	public static function checkIsIntercept($omOrderId){
		//订单自动拦截方法 支持虚拟料号
		self::initDB();
		$sql 				= "SELECT * FROM om_unshipped_order_detail WHERE omOrderId='{$omOrderId}'";
		$query				= self::$dbConn->query($sql);
		$orderdetaillist 	= self::$dbConn->fetch_array($query);
		
		foreach ($orderdetaillist AS $orderdetail){
			$sku_arr = self::getRealSkuInfo($orderdetail['sku']);
			foreach($sku_arr as $or_sku => $or_nums){
				$allnums = $or_nums*$orderdetail['ebay_amount'];
				if (!check_sku($or_sku, $allnums)){
					$sql = "UPDATE ebay_order SET ebay_status=640 WHERE ebay_ordersn='{$order_sn}'";
					echo "\n{$sql}\n";
					$dbcon->execute($sql);
					
					$sql = "select ebay_id,ebay_status,ebay_account from ebay_order where ebay_ordersn='{$order_sn}'";
					$dbcon->execute($sql);
					$order_info = $dbcon->getResultArray($sql);
					$datetime = date("Y-m-d H:i:s");
					$sql = "INSERT INTO ebay_mark_shipping SET ebay_id={$order_info[0]['ebay_id']}, ebay_status={$order_info[0]['ebay_status']}, type=1, ebay_account='{$order_info[0]['ebay_account']}', addtime='{$datetime}'";
					$dbcon->execute($sql);
					return true;
				}
			}
		}
		return false;
	}
	
	public static function getRealSkuInfo($sku){
		//获取料号下详细信息
		$combinelists = self::getSkuinfo($sku);
		if (empty($combinelists['sku'])){ //modified by Herman.Xi @ 2013-05-22
			$sku = self::getConversionSku($sku);
			return array($sku=>1);
		}
		$results = array();
		if (strpos($combinelists['goods_sncombine'], ',')!==false){
			$skulists = explode(',', $combinelists['goods_sncombine']);
			foreach ($skulists AS $skulist){
				list($_sku, $snum) = strpos($skulist, '*')!==false ? explode('*', $skulist) : array($skulist, 1);
				$_sku = self::getConversionSku($_sku);
				$results[trim($_sku)] = $snum;
			}
		}else if (strpos($combinelists['goods_sncombine'], '*')!==false){
			list($_sku, $snum) = explode('*', $combinelists['goods_sncombine']);
			$_sku = self::getConversionSku($_sku);
			$results[trim($_sku)] = $snum;
		}else{
			$sku = self::getConversionSku($sku);
			$results[trim($sku)] = 1;
		}
		return $results;
	}
	
	public static function getConversionSku($sku){
		/*add by Herman.Xi @ 2013-06-04
		新旧料号转换问题解决*/
		$paArr = array(
			/* API系统级参数 End */
			/* API应用级输入参数 Start*/
			'oldSku' => $sku
		);
		$conversion_sku = UserCacheModel::getOpenSysApi('ph.showNewSku', $paArr);
		return trim($conversion_sku['new_sku']);
	}
	
}

