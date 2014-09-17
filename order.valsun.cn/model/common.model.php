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
     * 获取料号类别
     */
	/*public static function getCateinfo(){
        global $memc_obj;
        if (self::$cateInfo == NULL){
            self::$cateInfo = $memc_obj->get_extral('pc_goods_category');
        }
        return self::$cateInfo;
    }*/
	
	/*
     * 通过类别id获取类别名称
     */
   /*public static function getCateInfoById($cateId){
        $cateInfoList = self::getCateinfo();
        return  $cateInfoList[$cateId]['name'];
    }*/
	
    /*
     * 获取全部的运输方式列表信息 通过api到运输方式管理系统获取
     */
   /* public static function getShipingTypeList(){
        global $memc_obj;
        if (self::$shippingInfo == NULL){
            self::$shippingInfo = $memc_obj->get_extral('trans_system_carrier');
        }
        return self::$shippingInfo;
    }*/
    
    /*
     * 运输方式id到运输方式名称映射函数 $id unsigned int 运输方式id
     */
   /* public static function getShipingNameById($id){
        $shippingInfoList = self::getShipingTypeList();
        return   $shippingInfoList[$id]['carrierNameCn'];
    }*/
    
	/*
     * 获取全部的分区信息
	 *return Array[运输方式ID][渠道ID] [分区ID]= 分区信息；
     */
    /*public static function getAreaList(){
        global $memc_obj;
        if (self::$shippingInfo1 == NULL){
            self::$shippingInfo1 = $memc_obj->get_extral('trans_system_carrierinfo');
        }
        return self::$shippingInfo1;
    }*/
	
	/*
     * 获取全部的渠道信息
	 *return Array[运输方式ID][渠道ID] = 渠道信息；
     */
    /*public static function getChannelist(){
        global $memc_obj;
        if (self::$shippingInfo2 == NULL){
            self::$shippingInfo2 = $memc_obj->get_extral('trans_system_channelinfo');
        }
        return self::$shippingInfo2;
    }*/

	/*
     * 获取平台信息
     */
	/*public static function getPlatformInfo(){
        global $memc_obj;
        if (self::$platformInfo == NULL){
            self::$platformInfo = $memc_obj->get_extral('trans_system_platform');
        }
        return self::$platformInfo;
    }*/
	
	/*
     * 通过平台id获取平台名称
     */
   /* public static function getPlatformInfoById($platformId){
        $platformInfoList = self::getPlatformInfo();
        return  $platformInfoList[$platformId];
    }*/
	
	/*
     * 获取平台信息(平台对应的运输方式)
	 *return Array[平台ID] []= 平台信息；
     */
	/*public static function getPlatCarrInfo(){
        global $memc_obj;
        if (self::$platformInfo1 == NULL){
            self::$platformInfo1 = $memc_obj->get_extral('trans_platform_carrierName');
        }
        return self::$platformInfo1;
    }*/
	
	/*
     * 获取平台信息
     */
	/*public static function getPositionInfo(){
        global $memc_obj;
        if (self::$positionInfo == NULL){
            self::$positionInfo = $memc_obj->get_extral('wh_position');
        }
        return self::$positionInfo;
    }*/
	
	/*
     * 通过仓位id获取仓位名称
     */
    /*public static function getPositionInfoById($positionId){
        $positionInfoList = self::getPositionInfo();
        return  $positionInfoList[$positionId]['pName'];
    }*/
	
	/*
     * 获取仓位信息
     */
	/*public static function getLocationInfo(){
        global $memc_obj;
        if (self::$locationInfo == NULL){
            self::$locationInfo = $memc_obj->get_extral('wh_sku_location');
        }
        return self::$locationInfo;
    }*/
	
	/*
     * 通过sku获取仓位信息
     */
    /*public static function getPositionInfoBySku($sku){
        $locationInfoList = self::getLocationInfo();
        return  $locationInfoList[$sku];
    }*/
	
	/*
     * 获取SKU对应仓位坐标信息列
	 *return Array[SKU][仓位ID]= 仓位坐标信息；
     */
	/*public static function getProPositionInfo(){
        global $memc_obj;
        if (self::$locationInfo1 == NULL){
            self::$locationInfo1 = $memc_obj->get_extral('wh_product_position');
        }
        return self::$locationInfo1;
    }*/
	
	/*
     * 获取国家信息列表
	 *return Array[ID] = 国家信息；
     */
	/*public static function getCountriesList(){
        global $memc_obj;
        if (self::$countrysInfo == NULL){
            self::$countrysInfo = $memc_obj->get_extral('trans_countries_standard');
        }
        return self::$countrysInfo;
    }*/
	
	/*
     * 根据国家简称获取国家信息列表
     */
	public static function getCountrieInfoBySn($countrySn){
        /*global $memc_obj;
        if (self::$countrysInfo2 == NULL){
            self::$countrysInfo2 = $memc_obj->get_extral('trans_countries_sn');
        }*/
		$countrySn = trim($countrySn);
		self::initDB();
		$sql = "select * from om_country_list where regions_jc='{$countrySn}'";
		$query = self::$dbConn->query($sql);
		$countrysInfo = self::$dbConn->fetch_array($query);
        return $countrysInfo;
    }
	
	/*
     * 根据国家英文全称获取国家信息列表
     */
	/*public static function getCountrieInfoByEn($countryNameEn){
        global $memc_obj;
        if (self::$countrysInfo1 == NULL){
            self::$countrysInfo1 = $memc_obj->get_extral('trans_countries_en');
        }
        return self::$countrysInfo1[$countryNameEn];
    }*/
	
	/*
     * 根据小语种国家名称改成英文国家名称
     */
	/*public static function getCountrieInfoBySmallCountries($smallCountry){
        global $memc_obj;
        $cacheName = md5('countries_small_comparison'.$smallCountry);
        self::$countrysInfo1 = $memc_obj->get_extral($cacheName);
        return self::$countrysInfo1;
    }*/
	
    /*
     *获取所有渠道信息
	 *
     */
	public static function getAllChannelList(){
		/*global $memc_obj;
		$memc_name = md5('trans_system_carrier'.$type);
		$shippingInfo = $memc_obj->get_extral($memc_name);*/
		
		require_once WEB_PATH."api/include/functions.php";
		
		$url	= 'http://idc.gw.open.valsun.cn/router/rest?';
		$paramArr= array(
			/* API系统级输入参数 Start */
			'method'	=> 'trans.carrier.channel.info.get',  //API名称
			'format'	=> 'json',  //返回格式
			'v'			=> '1.0',   //API版本号
			'username'	=> 'purchase',
			/* API系统级参数 End */				 
        
			/* API应用级输入参数 Start*/
			'carrierId'      => "all"
			/* API应用级输入参数 End*/
		);
		$result = callOpenSystem($paramArr);
		$data = json_decode($result,true);
        //print_r($data);
//        exit;
		return $data['data'];
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
     *获取运输方式列表信息
	 *填写正确的运输方式参数类型（0非快递，1快递，2全部）
     */
	public static function getCarrierInfoById($id = 2){
		$data = self::getCarrierList($id);
		//var_dump($data);
		$ret = array();
		foreach($data as $value){
			$ret[] = $value['id'];
		}
		return $ret;
    }
	
	/*
     *获取运输方式列表信息
	 *填写正确的运输方式参数类型（0非快递，1快递，2全部）
     */
	public static function getCarrierListById($id = 2){
		$data = self::getCarrierList($id);
		//var_dump($data);
		$ret = array();
		foreach($data as $value){
			$ret[$value['id']] = trim($value['carrierNameCn']);
		}
		return $ret;
    }

	
	/*
     *获取快递列表信息
	 *return Array[ID] = 快递信息；
     */
	public static function getCarrierExp(){
        /*global $memc_obj;
        if (self::$shippingInfo3 == NULL){
            self::$shippingInfo3 = $memc_obj->get_extral('trans_carrier_exp');
        }*/
		//$rtn = array();
		$data = self::getCarrierList(1);
		//var_dump($data);
		$rtn = array();
		foreach($data as $value){
			$rtn[] = $value['id'];
		}
        return $rtn;
    }
	
	/*
     *获取非快递列表信息
	 *return Array[ID] = 非快递信息；
     */
	public static function getCarrierNoExp(){
       /* global $memc_obj;
        if (self::$shippingInfo4 == NULL){
            self::$shippingInfo4 = $memc_obj->get_extral('trans_carrier_noexp');
        }*/
        //return self::$shippingInfo4;
		$data = self::getCarrierList(0);
		$rtn = array();
		foreach($data as $value){
			$rtn[] = $value['id'];
		}
        return $rtn;
    }
	
	public static function checkIsIntercept($omOrderId){
		//订单自动拦截方法 支持虚拟料号
		self::initDB();
		$sql 				= "SELECT * FROM om_unshipped_order_detail WHERE omOrderId='{$omOrderId}'";
		$query				= self::$dbConn->query($sql);
		$orderdetaillist 	= self::$dbConn->fetch_array($query);
		
		foreach ($orderdetaillist AS $orderdetail){
			$sku_arr = GoodsModel::get_realskuinfo($orderdetail['sku']);
			foreach($sku_arr as $or_sku => $or_nums){
				$allnums = $or_nums*$orderdetail['ebay_amount'];
				if (!self::check_sku($or_sku, $allnums)){
					$sql = "UPDATE ebay_order SET ebay_status=640 WHERE ebay_ordersn='{$order_sn}'";
					echo "\n{$sql}\n";
					self::$dbConn->query($sql);
					
					$sql = "select ebay_id,ebay_status,ebay_account from ebay_order where ebay_ordersn='{$order_sn}'";
					$query = self::$dbConn->query($sql);
					$order_info = self::$dbConn->fetch_array($query);
					$datetime = date("Y-m-d H:i:s");
					$sql = "INSERT INTO ebay_mark_shipping SET ebay_id={$order_info[0]['ebay_id']}, ebay_status={$order_info[0]['ebay_status']}, type=1, ebay_account='{$order_info[0]['ebay_account']}', addtime='{$datetime}'";
					self::$dbConn->query($sql);
					return true;
				}
			}
		}
		return false;
	}
	
	/*
	 * 计算正准备添加进系统的订单的重量
	 * 计算订单重量的方法,方法中加入了订单对应包材重量，未加入sku对应的包材重量
	 * add by Herman.Xi @20131024
		***add by Herman.Xi
		***create date 20121012
		***计算订单总重量***
		一、单料号,数量为1:
			重量=料号重量+包材重量。
		二、单料号,数量为多个:
			当总数小于包材容量时,重量=料号重量*总数+包材重量;
			当总数大于包材容量时,重量=料号重量*总数+1个包材重量+(总数-包材容量)/包材容量*0.6*包材重量。
		三、单料号组合:
			当总数小于包材容量时,重量=料号重量*总数+包材重量;
			当总数大于包材容量时,重量=料号重量*总数+1个包材重量+(总数-包材容量)/包材容量*0.6*包材重量。
		四、多料号组合:
			重量=(料号1总数/包材1容量)*0.6*包材1重量 + (料号1重量 * 料号1总数)
				+(料号2总数/包材2容量)*0.6*包材2重量 + (料号2重量 * 料号2总数) 
				+ ....
		
		注:'/'是除,'*'是乘,'%'是求余。
	*/
	public static function calcAddOrderWeight($obj_order_detail_data) {
		global $memc_obj; //调用memcache对象
		//var_dump($obj_order_detail_data); exit;
		$orderWeight = 0; //初始化要返回的订单重量变量
		$pweight = 0; //初始化包材重量
		$orderCosts = 0;
		$orderPrices = 0;
		//var_dump($obj_order_detail_data); exit;
		if(empty($obj_order_detail_data)){
			return 0;	
		}
		foreach($obj_order_detail_data as $detailValue){
			$sku = $detailValue['orderDetailData']['sku'];
			$amount = $detailValue['orderDetailData']['amount'];
			$itemPrice = $detailValue['orderDetailData']['itemPrice'];
			$skuinfo = GoodsModel::getCombineSkuinfo($sku);
			//var_dump($skuinfo);
			if($skuinfo){
				//组合料号
				$skuinfoDetail = $skuinfo['detail'];
				if(count($skuinfoDetail) == 1){
					$ssku = $skuinfoDetail[0]['sku'];
					$scount = $skuinfoDetail[0]['count'];
					$goodsinfo = GoodsModel::getSkuinfo($ssku);//获取单料号信息
					if($goodsinfo){
						$pmId = $goodsinfo['pmId'];
						$goodsWeight = $goodsinfo['goodsWeight'];
						$pmCapacity = $goodsinfo['pmCapacity'];
						$goodsCost = $goodsinfo['goodsCost'];
					}
					$pmInfo = GoodsModel::getMaterInfoById($pmId);//获取包材信息
					if($pmInfo){
						$pweight = $pmInfo['pmWeight'];
					}
					if($scount <= $pmCapacity){
						$orderWeight += $pweight + ($goodsWeight * $scount);
					}else{
						$orderWeight += (1 + ($scount-$pmCapacity)/$pmCapacity*0.6)*$pweight + ($goodsWeight * $scount);
					}
					$orderCosts += $goodsCost;
				}else if(count($skuinfoDetail) > 1){
					foreach($skuinfoDetail as $skuinfoDetailValue){
						$ssku = $skuinfoDetailValue['sku'];
						$scount = $skuinfoDetailValue['count'];
						$goodsinfo = GoodsModel::getSkuinfo($ssku);//获取单料号信息
						if($goodsinfo){
							$pmId = $goodsinfo['pmId'];
							$goodsWeight = $goodsinfo['goodsWeight'];
							$pmCapacity = $goodsinfo['pmCapacity'];
							$goodsCost = $goodsinfo['goodsCost'];
						}
						$pmInfo = GoodsModel::getMaterInfoById($pmId);//获取包材信息
						if($pmInfo){
							$pweight = $pmInfo['pmWeight'];
						}
						$orderWeight += ($scount/$pmCapacity)*0.6*$pweight + ($goodsWeight * $scount);
						$orderCosts += $goodsCost;
					}
				}
			}else{
				//单料号
				$goodsinfo = GoodsModel::getSkuinfo($sku);//获取单料号信息
				//var_dump($goodsinfo);
				if($goodsinfo){
					$pmId = $goodsinfo['pmId'];
					$goodsWeight = $goodsinfo['goodsWeight'];
					$pmCapacity = $goodsinfo['pmCapacity'];
					$goodsCost = $goodsinfo['goodsCost'];
				}
				//var_dump($pmId);
				$pmInfo = GoodsModel::getMaterInfoById($pmId);//获取包材信息
				if($pmInfo){
					$pweight = $pmInfo['pmWeight'];
				}
				if($amount <= $pmCapacity){
					$orderWeight += $pweight + ($goodsWeight * $amount);
				}else{
					if(!empty($pmCapacity)){
						$orderWeight += (1 + ($amount-$pmCapacity)/$pmCapacity*0.6)*$pweight + ($goodsWeight * $amount);
					}else{
						$orderWeight += $pweight + ($goodsWeight * $amount);	
					}
				}
				$orderCosts += $goodsCost;
			}
			$orderPrices += $itemPrice * $amount;
		}
		return array($orderWeight,$pmId,$orderCosts,$orderPrices);
	}
	
	public static function calcOrderWeight($obj_order_detail_data) {
		global $memc_obj; //调用memcache对象
		//var_dump($obj_order_detail_data); exit;
		$orderWeight = 0; //初始化要返回的订单重量变量
		$pweight = 0; //初始化包材重量
		$orderCosts = 0;
		$orderPrices = 0;
		//var_dump($obj_order_detail_data); exit;
		foreach($obj_order_detail_data as $detailValue){
			$sku = $detailValue['sku'];
			$amount = $detailValue['amount'];
			$itemPrice = $detailValue['itemPrice'];
			$skuinfo = GoodsModel::getCombineSkuinfo($sku);
			//var_dump($skuinfo);
			if($skuinfo){
				//组合料号
				$skuinfoDetail = $skuinfo['detail'];
				if(count($skuinfoDetail) == 1){
					$ssku = $skuinfoDetail[0]['sku'];
					$scount = $skuinfoDetail[0]['count'];
					$goodsinfo = GoodsModel::getSkuinfo($ssku);//获取单料号信息
					if($goodsinfo){
						$pmId = $goodsinfo['pmId'];
						$goodsWeight = $goodsinfo['goodsWeight'];
						$pmCapacity = $goodsinfo['pmCapacity'];
						$goodsCost = $goodsinfo['goodsCost'];
					}
					$pmInfo = GoodsModel::getMaterInfoById($pmId);//获取包材信息
					if($pmInfo){
						$pweight = $pmInfo['pmWeight'];
					}
					if($scount <= $pmCapacity){
						$orderWeight += $pweight + ($goodsWeight * $scount);
					}else{
						$orderWeight += (1 + ($scount-$pmCapacity)/$pmCapacity*0.6)*$pweight + ($goodsWeight * $scount);
					}
					$orderCosts += $goodsCost;
				}else if(count($skuinfoDetail) > 1){
					foreach($skuinfoDetail as $skuinfoDetailValue){
						$ssku = $skuinfoDetailValue['sku'];
						$scount = $skuinfoDetailValue['count'];
						$goodsinfo = GoodsModel::getSkuinfo($ssku);//获取单料号信息
						if($goodsinfo){
							$pmId = $goodsinfo['pmId'];
							$goodsWeight = $goodsinfo['goodsWeight'];
							$pmCapacity = $goodsinfo['pmCapacity'];
							$goodsCost = $goodsinfo['goodsCost'];
						}
						$pmInfo = GoodsModel::getMaterInfoById($pmId);//获取包材信息
						if($pmInfo){
							$pweight = $pmInfo['pmWeight'];
						}
						$orderWeight += ($scount/$pmCapacity)*0.6*$pweight + ($goodsWeight * $scount);
						$orderCosts += $goodsCost;
					}
				}
			}else{
				//单料号				
				$goodsinfo = GoodsModel::getSkuinfo($sku);//获取单料号信息
				//echo "<pre>";var_dump($goodsinfo);
				if($goodsinfo){
					$pmId = $goodsinfo['pmId'];
					$goodsWeight = $goodsinfo['goodsWeight'];
					$pmCapacity = $goodsinfo['pmCapacity'];
					$goodsCost = $goodsinfo['goodsCost'];
				} else {
					echo "===sku-$sku=========<br>";
				}
				//var_dump($pmId);
				$pmInfo = GoodsModel::getMaterInfoById($pmId);//获取包材信息
				//echo "<pre>";var_dump($pmInfo);
				if($pmInfo){
					$pweight = $pmInfo['pmWeight'];
				}
				if($amount <= $pmCapacity){
					$orderWeight += $pweight + ($goodsWeight * $amount);
				}else{
					if (!empty($pmCapacity)) {
						$orderWeight += (1 + ($amount-$pmCapacity)/$pmCapacity*0.6)*$pweight + ($goodsWeight * $amount);
					} else {
						$orderWeight += $pweight + ($goodsWeight * $amount);	
					}					
				}
				
				$orderCosts += $goodsCost;

				//echo "===amount-$amount=====pmCapacity-$pmCapacity=======pweight-$pweight===goodsWeight-$goodsWeight=========";
				//exit;
			}
			$orderPrices += $itemPrice * $amount;
		}
		return array($orderWeight,$pmId,$orderCosts,$orderPrices);
	}
	
	public static function calcOnlySkuWeight($sku, $amount) {
		global $memc_obj; //调用memcache对象
		//var_dump($obj_order_detail_data); exit;
		$skuWeight = 0; //初始化要返回的仅仅SKU重量
		$pweight = 0; //初始化包材重量
		//var_dump($obj_order_detail_data); exit;
		//foreach($obj_order_detail_data as $detailValue){
			//$sku = $detailValue['orderDetailData']['sku'];
			//$amount = $detailValue['orderDetailData']['amount'];
			$skuinfo = GoodsModel::getCombineSkuinfo($sku);
			//var_dump($skuinfo);
			if($skuinfo){
				//组合料号
				$skuinfoDetail = $skuinfo['detail'];
				if(count($skuinfoDetail) == 1){
					$ssku = $skuinfoDetail[0]['sku'];
					$scount = $skuinfoDetail[0]['count'];
					$goodsinfo = GoodsModel::getSkuinfo($ssku);//获取单料号信息
					if($goodsinfo){
						$pmId = $goodsinfo['pmId'];
						$goodsWeight = $goodsinfo['goodsWeight'];
						$pmCapacity = $goodsinfo['pmCapacity'];
					}
					$pmInfo = GoodsModel::getMaterInfoById($pmId);//获取包材信息
					if($pmInfo){
						$pweight = $pmInfo['pmWeight'];
					}
					if($scount <= $pmCapacity){
						$skuWeight += $pweight + ($goodsWeight * $scount);
					}else{
						$skuWeight += (1 + ($scount-$pmCapacity)/$pmCapacity*0.6)*$pweight + ($goodsWeight * $scount);
					}
				}else if(count($skuinfoDetail) > 1){
					foreach($skuinfoDetail as $skuinfoDetailValue){
						$ssku = $skuinfoDetailValue['sku'];
						$scount = $skuinfoDetailValue['count'];
						$goodsinfo = GoodsModel::getSkuinfo($ssku);//获取单料号信息
						if($goodsinfo){
							$pmId = $goodsinfo['pmId'];
							$goodsWeight = $goodsinfo['goodsWeight'];
							$pmCapacity = $goodsinfo['pmCapacity'];
						}
						$pmInfo = GoodsModel::getMaterInfoById($pmId);//获取包材信息
						if($pmInfo){
							$pweight = $pmInfo['pmWeight'];
						}
						$skuWeight += ($scount/$pmCapacity)*0.6*$pweight + ($goodsWeight * $scount);
					}
				}
			}else{
				//单料号
				$goodsinfo = GoodsModel::getSkuinfo($sku);//获取单料号信息
				//var_dump($goodsinfo);
				if($goodsinfo){
					$pmId = $goodsinfo['pmId'];
					$goodsWeight = $goodsinfo['goodsWeight'];
					$pmCapacity = $goodsinfo['pmCapacity'];
				}
				//var_dump($pmId);
				$pmInfo = GoodsModel::getMaterInfoById($pmId);//获取包材信息
				if($pmInfo){
					$pweight = $pmInfo['pmWeight'];
				}
				if($amount <= $pmCapacity){
					$skuWeight += $pweight + ($goodsWeight * $amount);
				}else{
					$skuWeight += (1 + ($amount-$pmCapacity)/$pmCapacity*0.6)*$pweight + ($goodsWeight * $amount);
				}
			}
		//}
		return array($skuWeight,$pmId);
	}
	
	/*
	 * 重新计算已经存在于系统的订单重量的方法,方法中加入了订单对应包材重量，未加入sku对应的包材重量
	 * lastModified by Herman.Xi @20131024
	*/
	public static function calcNowOrderWeight($omOrderId) {
		global $memc_obj; //调用memcache对象
		/*$pmList = GoodsModel::getMaterInfo(); //memcache中取得包材信息
		$orderWeight = 0; //初始化要返回的订单重量变量
		if (intval($omOrderId) == 0) { //订单号不合法
			return false;
		}
		$tName = 'om_unshipped_order';
		$select = '*';
		$where = "WHERE is_delete=0 AND id='$omOrderId'";
		$omOrderList = OmAvailableModel :: getTNameList($tName, $select, $where);
		if (empty ($omOrderList)) { //没有对应订单号
			return false;
		}
		$omOrderPmId = $omOrderList[0]['pmId']; //订单的包材号
		$pmWeight = $pmList[$omOrderPmId]['pmWeight']; //该订单对应的包材重量
		$orderWeight += $pmWeight; //累加
	
		$tName = 'om_unshipped_order_detail';
		$select = '*';
		$where = "WHERE omOrderId='$omOrderId'";
		$omOrderDetailList = OmAvailableModel :: getTNameList($tName, $select, $where);
		if (empty ($omOrderId)) { //没有对应订单号的订单明细
			return false;
		}
		foreach ($omOrderDetailList as $omOrderDetail) {
			$sku = $omOrderDetail['sku'];
			$amount = $omOrderDetail['amount'];
			$skuInfo = GoodsModel::getSkuList($sku);
			$skuWeight = $skuInfo['goods_weight'] * $amount;
			$orderWeight += $skuWeight; //累加sku重量
		}
		return $orderWeight;*/
		$tableName = 'om_unshipped_order';
		$where = ' WHERE id = '.$omOrderId.' and is_delete = 0 and storeId = 1';
		//$orderData = OrderindexModel::showOnlyOrderList($tableName, $where);
		$orderDetailData = OrderindexModel::showOnlyOrderDetailList($tableName, ' WHERE omOrderId = '.$omOrderId.' and is_delete = 0 and storeId = 1');
		//var_dump($orderDetailData); exit;
		return self::calcOrderWeight($orderDetailData);
	}
	
	/*
	 * 计算同步订单入系统时候的订单运费的方法,方法中要调用运输方式管理的api进行运费计算
	 * add by Herman.Xi @20131024
	 * $isFixed //是最优运输方式还是固定运输方式，默认为2
	 * $calcWeight //订单估算重量
	 * $transportId //运输方式id
	 * $shipaddr = 1; //发货地址id,用来获取最优运输方式
	 * $countryName //国家名称
	 * $zipCode //邮编
	*/
	public static function calcAddOrderShippingFee($orderData,$isFixed=2,$shipaddr=1) {
		//var_dump($orderData);
		require_once WEB_PATH."api/include/functions.php";
		$url	= 'http://gw.open.valsun.cn:88/router/rest?';
		$shippingFee = 0; //初始化要返回的订单运费变量
		$calcWeight = $orderData['orderData']['calcWeight']; //订单估算重量
		$transportId = $orderData['orderData']['transportId'];  //运输方式id
		$countryName = $orderData['orderUserInfoData']['countryName']; //国家名称
		$zipCode = $orderData['orderUserInfoData']['zipCode']; //邮编
	
		$method = ''; //访问开放系统接口名称,最优还是固定,调用开放系统必填参数
		$paramArr= array(
			/* API系统级输入参数 Start */
			'format'	=> 'json',  //返回格式
			'v'			=> '2.0',   //API版本号
			'username'	=> C('OPEN_SYS_USER'),
			/* API系统级参数 End */		
		);
		if ($isFixed == 2) {
			$method = 'trans.carriers.best.get';
			$paramArr['method'] = $method;
			$paramArr['country'] = $countryName;
			$paramArr['weight'] = $calcWeight;
			$paramArr['shipAddId'] = $shipaddr;
			if(isset($zipCode)){
				$paramArr['postCode'] = $zipCode;
			}
			if(isset($noShipId)){
				$paramArr['noShipId'] = $noShipId;
			}
		} else {
			$method = 'trans.carriers.fix.get';
			$paramArr['method'] = $method;
			$paramArr['carrierId'] = $transportId;
			$paramArr['country'] = $countryName;
			$paramArr['weight']  = $calcWeight;
		}
		
		/*$paramArr['carrier'] = $transportId;
		$paramArr['country'] = $countryName;
		$paramArr['weight'] = $calcWeight;
		$paramArr['shaddr'] = $shipaddr;
		$paramArr['postcode'] = $zipCode;*/
		//var_dump($paramArr); echo "<br>";
		$result = callOpenSystem($paramArr);
		
		$data = json_decode($result,true);
		//var_dump($data);
		if(empty($data['data'])){
			return false;	
		}
		return $data['data'];
		
		/*$data = UserCacheModel :: getOpenSysApi($method, $paramArr);
		if ($isFixed == 2) {
			return $data['data'];
		} else {
			return $data['fee']['fee'];
		}*/
	}
	
	/*
	 * 获取运输方式管理系统，运输方式列表信息
	 * add by Herman.Xi @20131025
	 * $type //默认1快递,2非快递
	*/
	public static function getTransCarrierInfo($type=2) {
		$method = 'trans.carrier.info.get';
		$paramArr = array(); //传递应用级参数数组
		$paramArr['type'] = $type;
		$data = UserCacheModel :: getOpenSysApi($method, $paramArr);
		return $data;
	}
	
	/*
	 * 获取采购系统下特殊料号列表
	 * add by Herman.Xi @20131025
	 * $get //1
	*/
	public static function getAdjustransportFromPurchase($get=1) {
		$method = 'purchase.getAdjustransport';
		$paramArr = array(); //传递应用级参数数组
		$paramArr['get'] = $get;
		$data = UserCacheModel :: getOpenSysApi($method, $paramArr);
		//var_dump($data);
		if(!isset($data['data'])){
			return array();
		}
		$__liquid_items_array = array();
		foreach($data['data'] as $dataValue){
			$__liquid_items_array[$dataValue['category']] = $dataValue['skulist'];
		}
		return $__liquid_items_array;
	}
	
	/*
	 * 计算已经存在系统的订单运费的方法,方法中要调用运输方式管理的api进行运费计算
	*/
	public static function calcNowOrderShippingFee($omOrderId) {
		require_once WEB_PATH."api/include/functions.php";
		$shippingFee = 0; //初始化要返回的订单运费变量
		if (intval($omOrderId) == 0) { //订单号不合法
			return false;
		}
		$tName = 'om_unshipped_order';
		$select = '*';
		$where = "WHERE is_delete=0 AND id='$omOrderId'";
		$omOrderList = OmAvailableModel :: getTNameList($tName, $select, $where);
		if (empty ($omOrderList)) { //没有对应订单号
			return false;
		}
		$isFixed = $omOrderList[0]['isFixed']; //是最优运输方式还是固定运输方式，默认为2
		$calcWeight = $omOrderList[0]['calcWeight']; //订单估算重量
		$transportId = $omOrderList[0]['transportId']; //运输方式id
	
		$tName = 'om_unshipped_order_userInfo';
		$select = '*';
		$where = "WHERE omOrderId='$omOrderId'";
		$omOrderUserInfoList = OmAvailableModel :: getTNameList($tName, $select, $where);
		if (empty ($omOrderUserInfoList)) { //没有对应订单号的用户信息
			return false;
		}
		$countryName = $omOrderUserInfoList[0]['countryName']; //国家名称
		$shipaddr = 1; //发货地址id,用来获取最优运输方式
		$zipCode = $omOrderUserInfoList[0]['zipCode']; //邮编
	
		$method = ''; //访问开放系统接口名称,最优还是固定,调用开放系统必填参数
		if ($isFixed == 2) {
			$method = 'trans.carrier.best.get';
		} else {
			$method = 'trans.carrier.fix.get';
		}
		$paramArr= array(
			/* API系统级输入参数 Start */
			'method'	=> $method,  //API名称
			'format'	=> 'json',  //返回格式
			'v'			=> '1.0',   //API版本号
			'username'	=> C('OPEN_SYS_USER'),
			/* API系统级参数 End */		
		);
		$paramArr['carrier'] = $transportId;
		$paramArr['country'] = $countryName;
		$paramArr['weight'] = $calcWeight;
		$paramArr['shaddr'] = $shipaddr;
		$paramArr['postcode'] = $zipCode;
		
		$result = callOpenSystem($paramArr);
		
		$data = json_decode($result,true);
		//var_dump($data);
		if(empty($data['data'])){
			return false;	
		}
		return $data['data'];
		/*$data = UserCacheModel :: getOpenSysApi($method, $paramArr);
		if ($isFixed == 2) {
			return $data['data'];
		} else {
			return $data['data'];
		}*/
	}
	
	/*
	 * 组合订单计算
	*/
	public static function calcshippingfee($calcWeight, $countryName, $totalmoney, $transportId){
		require_once WEB_PATH."api/include/functions.php";
		$isFixed = 1; //是最优运输方式还是固定运输方式，默认为2
		$shipaddr = 1; //发货地址id,用来获取最优运输方式
		$zipCode = ''; //邮编
		//var_dump($calcWeight, $countryName, $totalmoney, $transportId);
		$method = ''; //访问开放系统接口名称,最优还是固定,调用开放系统必填参数
		if ($isFixed == 2) {
			$method = 'trans.carrier.best.get';
		} else {
			$method = 'trans.carrier.fix.get';
		}
		$paramArr= array(
			/* API系统级输入参数 Start */
			'method'	=> $method,  //API名称
			'format'	=> 'json',  //返回格式
			'v'			=> '1.0',   //API版本号
			'username'	=> C('OPEN_SYS_USER'),
			/* API系统级参数 End */		
		);
		if($totalmoney > 40){
			//$transportId = 2;	
		}
		$paramArr['carrier'] = $transportId;
		$paramArr['country'] = $countryName;
		$paramArr['weight'] = $calcWeight;
		$paramArr['shaddr'] = $shipaddr;
		$paramArr['postcode'] = $zipCode;
		
		$result = callOpenSystem($paramArr);
		
		$data = json_decode($result,true);
		//var_dump($data);
		if(empty($data['data'])){
			return false;	
		}
		//var_dump($data);
		return $data['data'];
		/*$data = UserCacheModel :: getOpenSysApi($method, $paramArr);
		if ($isFixed == 2) {
			return $data['data'];
		} else {
			return $data['data'];
		}*/
	}
	
	/*
	* 订单自动拦截完整版 支持虚拟料号
	* add by Herman.Xi 2012-12-20
	* 订单进入系统首先判断 是否为超大订单,如果为超大订单,文件夹为640;
	* 判断订单下,料号是否全部有货,部分有货,全部没货:
		 如果部分有货,判断其运输方式,如果为快递,文件夹为659;非快递则为660;(订单自动部分包货)
		 如果全部没货,判断其运输方式,如果为快递,文件夹为658;非快递则为661;(订单自动拦截)
		 如果全部有货:
			先判断如果为组合订单,文件夹为606;
			如果超重订单,文件夹为608;
			如果快递订单,文件夹为639;
			全部不满足则为导入状态
	  自动拦截时,判断自动拦截快递,非快递,自动部分包货快递,非快递里面的订单,自动每隔十五分钟执行一次
	  添加缺货和合并包裹缺货处理
	*/
	public static function auto_contrast_intercept($orderData){
		
		global $SYSTEM_ACCOUNTS,$__liquid_items_fenmocsku,$__liquid_items_BuiltinBattery,$__liquid_items_SuperSpecific,$__liquid_items_Paste;
		global $GLOBAL_EBAY_ACCOUNT;
		global $express_delivery,$no_express_delivery;
		//var_dump($GLOBAL_EBAY_ACCOUNT); exit;
		global $definedArr; if(!empty($definedArr)) extract($definedArr);
		//var_dump($GLOBAL_EBAY_ACCOUNT);
		self::initDB();
		
		//$start = time();
		
		//var_dump($__liquid_items_fenmocsku); echo "\n";
		$log_data = "";
		$actualTotal0 = 0; //该订单实际总数
		$omOrderId = $orderData['orderData']['id'];
		$orderStatus = empty($orderData['orderData']['orderStatus']) ? C('STATEPENDING') : $orderData['orderData']['orderStatus'];
		$orderType = empty($orderData['orderData']['orderType']) ? C('STATEPENDING_CONV') : $orderData['orderData']['orderType'];
		$returnStatus = array('orderStatus'=>$orderStatus, 'orderType'=>$orderType);
		$isNote = $orderData['orderData']['isNote'];
		$calcWeight = $orderData['orderData']['calcWeight'];
		$pmId = $orderData['orderData']['pmId'];
		//var_dump($calcWeight); echo "\n";
		if(empty($calcWeight)){
			$calcInfo = self::calcAddOrderWeight($orderData['orderDetail']);//计算重量和包材
			$calcWeight = $calcInfo[0];
			$pmId = $calcInfo[1];
		}
		//var_dump($calcWeight); echo "\n";
		$transportId = @$orderData['orderData']['transportId'];
		$countryName = $orderData['orderUserInfoData']['countryName'];
		$accountId = $orderData['orderData']['accountId'];
		//echo $accountId; echo "<br>";
		$actualTotal	= $orderData['orderData']['actualTotal'];
		$ebay_username = $orderData['orderUserInfoData']['username'];
		$orderDataid = $orderData['orderExtenData']['orderId'];
		$ebay_usermail = $orderData['orderUserInfoData']['email'];
		$PayPalEmailAddress = @$orderData['orderExtenData']['PayPalEmailAddress'];
		echo "订单编号：{$omOrderId}--国家：{$countryName}--计算重量：{$calcWeight}\n";
		//echo "订单计算重量:$calcWeight\t\n";
		
		$orderdetaillist = $orderData['orderDetail'];
		
		$contain_special_item = false;
		$contain_os_item = false;
		$ow_status = array();
		foreach ($orderdetaillist AS $orderdetail){
			$sku = $orderdetail['orderDetailData']['sku'];
			$itemPrice = $orderdetail['orderDetailData']['itemPrice'];
			$amount = $orderdetail['orderDetailData']['amount'];
			$shippingFee = $orderdetail['orderDetailData']['shippingFee'];
			//var_dump($sku);
			$sku_arr = GoodsModel::get_realskuinfo($sku);
			//var_dump($sku_arr); exit;
			$actualTotal0 += $itemPrice*$amount + $shippingFee;
			foreach($sku_arr as $or_sku => $or_nums){
				if(in_array($or_sku,$__liquid_items_fenmocsku) || in_array($or_sku,$__liquid_items_SuperSpecific) || in_array($or_sku,$__liquid_items_BuiltinBattery)){ //粉末状,超规格产品 走福建邮局
					$contain_special_item = true;
				} 
				//if(preg_match("/^US01\+.*/", $or_sku, $matchArr) || preg_match("/^US1\+.*/", $or_sku, $matchArr) ){
//					$log_data .= "[".date("Y-m-d H:i:s")."]\t包含海外仓料号订单---{$ebay_id}-----料号：{$or_sku}--!\n\n";
//					$contain_os_item = true;
//					if(strpos($or_sku,"US01+") !== false){
//						$matchStr=substr($matchArr[0],5);//去除前面
//						//$matchStr = str_replace("US1+", "", $or_sku);
//					}else{
//						//$matchStr=substr($matchArr[0],5);//去除前面
//						$matchStr = str_replace("US1+", "", $or_sku);
//					}
//					$n=strpos($matchStr,':');//寻找位置
//					if($n){$matchStr=substr($matchStr,0,$n);}//删除后面
//
//					if(preg_match("/^0+(\w+)/",$matchStr,$matchArr)){
//						$matchStr = $matchArr[1];
//					}
//					
//					$orderdetail['orderDetailData']['sku'] = $matchStr;
//					//OrderAddModel::updateDetailExtension(array('sku'=>$matchStr), " id = {$orderdetail['ebay_id']} ");
//
//					$virtualnum = check_oversea_stock($matchStr); //检查海外仓虚拟库存
//					if($virtualnum >= 0){
//						$ow_status[] = 705;
//					}else{
//						$ow_status[] = 714; //海外仓缺货
//					}
//				}
			}
		}
		
		/*$end = time();
		echo $end-$start; echo "<br>";
		$start = $end;*/
		
		if($contain_special_item){
			/*$sql = "update ebay_order set ebay_carrierstyle ='1' where ebay_id ={$ebay_id}"; //add by Herman.Xi 记录该订单含有特殊料号
			$dbConnn->query($sql);
			$log_data .= "[".date("Y-m-d H:i:s")."]\t包含粉末状超规格产品---{$ebay_id}---!\n\n";*/
		}
		/*if($contain_os_item){
			if(in_array(714,$ow_status)){
				$final_status = 714;
			}else{
				$final_status = 705;
			}

			$orderData['orderData']['orderStatus'] = '';
			$orderData['orderData']['orderType'] = '';
			$log_data .= "[".date("Y-m-d H:i:s")."]\t更新海外仓料号订单状态为{$final_status}---{$ebay_id}--{$sql}-!\n\n";
			
			if($final_status == 705){

				$calcWeight = calcWeight($ebay_id);
				$skunums	 = checkSkuNum($ebay_id);
				if($skunums === true){
					continue;
				}else if ($calcWeight>20) {
					if($skunums==1){
						usCalcShipCost($ebay_id);
					}
				} else {
					 usCalcShipCost($ebay_id);
				}
			}

			$log_data .= "[".date("Y-m-d H:i:s")."]\t包含海外仓料号---自动跳转---{$ebay_id}---的状态为---$final_status!\n\n";
			write_scripts_log('auto_contrast_intercept', $GLOBAL_EBAY_ACCOUNT[$accountId], $log_data);
			$calcWeight = recalcorderweight($order_sn, $ebay_packingmaterial); //modified by Herman.Xi 2012-10-17
			continue;
		}*/
		
		$interceptrtn = self::intercept_exception_orders($orderData);
		if($interceptrtn){
			return $interceptrtn;	
		}
		
		/*$end = time();
		echo $end-$start; echo "<br>";
		$start = $end;*/
		
		$record_details = array();
		$is_640 = false;
		//var_dump($orderdetaillist); exit;
		foreach ($orderdetaillist AS $orderdetail){
			//var_dump($orderdetail['sku']);
			$sku = $orderdetail['orderDetailData']['sku'];
			//$itemPrice = $orderdetail['orderDetailData']['itemPrice'];
			$amount = $orderdetail['orderDetailData']['amount'];
			//$shippingFee = $orderdetail['orderDetailData']['shippingFee'];
			
			$sku_arr = GoodsModel::get_realskuinfo($sku);
			//var_dump($sku_arr); exit;
			$hava_goodscount = true;
			foreach($sku_arr as $or_sku => $or_nums){
				$allnums = $or_nums*$amount;
				if (!self::check_sku($or_sku, $allnums)){
					//超大订单状态
					$orderStatus = C('STATEOVERSIZEDORDERS');
					//$sql = "UPDATE ebay_order SET ebay_status='640' WHERE ebay_id ='$ebay_id' and ebay_status = '{$orderStatus}' ";
					if(in_array($GLOBAL_EBAY_ACCOUNT[$accountId], $SYSTEM_ACCOUNTS['aliexpress']) /*|| in_array($GLOBAL_EBAY_ACCOUNT[$accountId], $SYSTEM_ACCOUNTS['B2B外单'])*/ || in_array($GLOBAL_EBAY_ACCOUNT[$accountId], $SYSTEM_ACCOUNTS['DHgate']) || in_array($GLOBAL_EBAY_ACCOUNT[$accountId], $SYSTEM_ACCOUNTS['出口通']) || in_array($GLOBAL_EBAY_ACCOUNT[$accountId], $SYSTEM_ACCOUNTS['线下结算客户'])){
						$orderType = C('STATEOVERSIZEDORDERS_CONFIRM');
						//$returnStatus = array('orderStatus'=>C('STATEOVERSIZEDORDERS'), 'orderType'=>C('STATEOVERSIZEDORDERS_CONFIRM'));
						//$sql = "UPDATE ebay_order SET ebay_status='698' WHERE ebay_id ='$ebay_id' and ebay_status = '{$ebay_status}' ";
						//$log_data .= "[".date("Y-m-d H:i:s")."]\t自动跳转---{$ebay_id}---的状态为---".C('STATEOVERSIZEDORDERS_PEND')."!\n\n";
					}else{
						$orderType = C('STATEOVERSIZEDORDERS_PEND');
						//$returnStatus = array('orderStatus'=>C('STATEOVERSIZEDORDERS'), 'orderType'=>C('STATEOVERSIZEDORDERS_PEND'));
						//$log_data .= "[".date("Y-m-d H:i:s")."]\t自动跳转的状态为---".C('STATEOVERSIZEDORDERS_PEND')."!\n\n";
					}
					//self::$dbConn->query($sql) or die("Fail : $sql");
					//$order_statistics->replaceData($order_sn, array('mask'=>1), array('mask'=>1));
					//self::insert_mark_shipping($ebay_id);
					$is_640 = true;
					break;
				}else{
					$skuinfo = self::get_sku_info($or_sku);
					$salensend = self::getpartsaleandnosendall($or_sku);
					
					//$log_data .= "[".date("Y-m-d H:i:s")."]\t---{$sql}\n\n";
					$log_data .= "订单===$ebay_id===料号==$or_sku===实际库存为{$skuinfo['realnums']}===需求量为{$allnums}===待发货数量为{$salensend}===\n";
					if(!isset($skuinfo['realnums']) || empty($skuinfo['realnums']) || ($skuinfo['realnums'] - $salensend - $allnums) < 0){
						$hava_goodscount = false;
						break;
					}
				}
			}
			if($hava_goodscount){$record_details[] = $orderdetail;}
		}
		
		if($is_640){
			$returnStatus = array('orderStatus'=>$orderStatus, 'orderType'=>$orderType);
			$log_data .= "[".date("Y-m-d H:i:s")."]\t自动跳转的状态为--".$orderStatus."--".$orderType."!\n\n";
			self::write_scripts_log('auto_contrast_intercept', $GLOBAL_EBAY_ACCOUNT[$accountId], $log_data);
			return $returnStatus;
		}
		
		$count_record_details = count($record_details);
		$count_orderdetaillist = count($orderdetaillist);
		$final_status = $orderStatus; //原始状态
		if($count_record_details == 0){
			//更新至自动拦截发货状态
			/*if (!in_array($ebay_carrier, $no_express_delivery)){
				$final_status = 658;
			}else {
				$final_status = 661;
			}*/
			//$sql = "UPDATE ebay_order SET ebay_status='$final_status' WHERE ebay_id ='$ebay_id' and ebay_status = '{$orderStatus}' ";
			$orderStatus = C('STATEOUTOFSTOCK');
			$orderType = C('STATEOUTOFSTOCK_AO');
			//$log_data .= "[".date("Y-m-d H:i:s")."]\t自动跳转的状态为---".C('STATEOUTOFSTOCK_AO')."!\n\n";
			//self::$dbConn->query($sql) or die("Fail : $sql");
			//$order_statistics->replaceData($order_sn, array('mask'=>1), array('mask'=>1));
			//self::insert_mark_shipping($ebay_id);
			//$returnStatus = array('orderStatus'=>C('STATEOUTOFSTOCK'), 'orderType'=>C('STATEOUTOFSTOCK_AO'));
			//self::write_scripts_log('auto_contrast_intercept', $GLOBAL_EBAY_ACCOUNT[$accountId], $log_data);
			$returnStatus = array('orderStatus'=>$orderStatus, 'orderType'=>$orderType);
			$log_data .= "[".date("Y-m-d H:i:s")."]\t自动跳转的状态为--".$orderStatus."--".$orderType."!\n\n";
			self::write_scripts_log('auto_contrast_intercept', $GLOBAL_EBAY_ACCOUNT[$accountId], $log_data);
			return $returnStatus;
		}else if($count_record_details < $count_orderdetaillist){
			//更新至自动部分发货状态
			/*if (!in_array($ebay_carrier, $no_express_delivery)){
				//$final_status = 659;
				$final_status = 640;
			}else {
				$final_status = 660;
			}*/
			$orderStatus = C('STATEOUTOFSTOCK');
			$orderType = C('STATEOUTOFSTOCK_PO');
			//$sql = "UPDATE ebay_order SET ebay_status='$final_status' WHERE ebay_id ='$ebay_id' and ebay_status = '{$orderStatus}' ";
			//$log_data .= "[".date("Y-m-d H:i:s")."]\t自动跳转的状态为---".C('STATEOUTOFSTOCK_PO')."!\n\n";
			//self::$dbConn->query($sql) or die("Fail : $sql");
			//$order_statistics->replaceData($order_sn, array('mask'=>1), array('mask'=>1));
			//self::insert_mark_shipping($ebay_id);
			//$returnStatus = array('orderStatus'=>C('STATEOUTOFSTOCK'), 'orderType'=>C('STATEOUTOFSTOCK_PO'));
			//self::write_scripts_log('auto_contrast_intercept', $GLOBAL_EBAY_ACCOUNT[$accountId], $log_data);
			//return array('orderStatus'=>$final_status,'orderType'=>641);
			$returnStatus = array('orderStatus'=>$orderStatus, 'orderType'=>$orderType);
			$log_data .= "[".date("Y-m-d H:i:s")."]\t自动跳转的状态为--".$orderStatus."--".$orderType."!\n\n";
			self::write_scripts_log('auto_contrast_intercept', $GLOBAL_EBAY_ACCOUNT[$accountId], $log_data);
			return $returnStatus;
		}else if($count_record_details == $count_orderdetaillist){
			//正常发货状态
			if(in_array($GLOBAL_EBAY_ACCOUNT[$accountId],$SYSTEM_ACCOUNTS['ebay'])){
				if(in_array($orderStatus, array(C('STATEOUTOFSTOCK_PO'),C('STATEOUTOFSTOCK_PO')))){
					//$final_status = 618;//ebay订单自动拦截有货不能移动到待处理和有留言 modified by Herman.Xi @ 20130325(移动到缺货需打印中)
					$orderStatus = C('STATEPENDING');
					if($isNote == 1){
						echo "有留言\t";
						$orderType = C('STATEPENDING_MSG');
					}else{
						$orderType = C('STATEPENDING_HASARRIVED');
					}
				}else{
					$orderStatus = C('STATEPENDING');
					if($isNote == 1){
						echo "有留言\t";
						$orderType = C('STATEPENDING_MSG');
					}else{
						$orderType = C('STATEPENDING_CONV');
					}
				}
			}/*else if(in_array($GLOBAL_EBAY_ACCOUNT[$accountId],$SYSTEM_ACCOUNTS['海外销售平台'])){
				if(in_array($orderStatus, array(C('STATEOUTOFSTOCK_PO'),C('STATEOUTOFSTOCK_PO')))){
					//$final_status = 629; //德国订单区别于正常订单
					//$final_status = 618; //modified by Herman.Xi @20130823 雷贤容需要修改成缺货需打印中
					//$sql = "UPDATE ebay_order SET ebay_status='$final_status' WHERE ebay_id ='$ebay_id' and ebay_status = '{$orderStatus}' ";
					//$log_data .= "[".date("Y-m-d H:i:s")."]\t自动跳转的状态为---".C('STATEPENDING_HASARRIVED')."!\n\n";
					//self::$dbConn->query($sql) or die("Fail : $sql");
					//$order_statistics->replaceData($order_sn, array('mask'=>1), array('mask'=>1));
					//self::write_scripts_log('auto_contrast_intercept', $GLOBAL_EBAY_ACCOUNT[$accountId], $log_data);
					//$returnStatus = array('orderStatus'=>C('STATEPENDING'), 'orderType'=>C('STATEPENDING_HASARRIVED'));
					$orderStatus = C('STATEPENDING');
					$orderType = C('STATEPENDING_HASARRIVED');
					$returnStatus = array('orderStatus'=>$orderStatus, 'orderType'=>$orderType);
					$log_data .= "[".date("Y-m-d H:i:s")."]\t自动跳转的状态为--".$orderStatus."--".$orderType."!\n\n";
					self::write_scripts_log('auto_contrast_intercept', $GLOBAL_EBAY_ACCOUNT[$accountId], $log_data);
					return $returnStatus;
				}else{
					$orderStatus = C('STATEPENDING');
					if($isNote == 1){
						echo "德国订单有留言\t";
						$orderType = C('STATEPENDING_MSG');
					}else{
						$orderType = C('STATEPENDING_CONV');
					} 
					//德国订单进入正常订单流程
				}
			}*/else if(in_array($GLOBAL_EBAY_ACCOUNT[$accountId], $SYSTEM_ACCOUNTS['aliexpress']) /*|| in_array($GLOBAL_EBAY_ACCOUNT[$accountId], $SYSTEM_ACCOUNTS['B2B外单'])*/){
				//$orderStatus = C('STATEPENDING');
				//$orderType = C('STATEPENDING_CONV');
				if(in_array($countryName, array('Russian Federation', 'Russia')) && strpos($ebay_carrier, '中国邮政')!==false && str_word_count($ebay_username) < 2){
					$orderStatus = C('STATESYNCINTERCEPT');
					$orderType = C('STATESYNCINTERCEPT_AB');
					//$sql = "UPDATE ebay_order SET ebay_status='$final_status' WHERE ebay_id ='$ebay_id' and ebay_status = '{$orderStatus}' ";
					//$log_data .= "[".date("Y-m-d H:i:s")."]\t自动跳转---{$ebay_id}---的状态为---$final_status!\n\n";
					//self::$dbConn->query($sql) or die("Fail : $sql");
					//$order_statistics->replaceData($order_sn, array('mask'=>1), array('mask'=>1));
					//self::write_scripts_log('auto_contrast_intercept', $GLOBAL_EBAY_ACCOUNT[$accountId], $log_data);
					$returnStatus = array('orderStatus'=>$orderStatus, 'orderType'=>$orderType);
					$log_data .= "[".date("Y-m-d H:i:s")."]\t自动跳转的状态为--".$orderStatus."--".$orderType."!\n\n";
					self::write_scripts_log('auto_contrast_intercept', $GLOBAL_EBAY_ACCOUNT[$accountId], $log_data);
					return $returnStatus;
				}
			}else if(in_array($GLOBAL_EBAY_ACCOUNT[$accountId], $SYSTEM_ACCOUNTS['DHgate'])){
				$orderStatus = C('STATEPENDING');
				$orderType = C('STATEPENDING_CONV');
			}else if(in_array($GLOBAL_EBAY_ACCOUNT[$accountId], $SYSTEM_ACCOUNTS['dresslink.com'])){
				$orderStatus = C('STATEPENDING');
				$orderType = C('STATEPENDING_CONV');
			}else if(in_array($GLOBAL_EBAY_ACCOUNT[$accountId], $SYSTEM_ACCOUNTS['cndirect.com'])){
				$orderStatus = C('STATEPENDING');
				$orderType = C('STATEPENDING_CONV');
			}else if(in_array($GLOBAL_EBAY_ACCOUNT[$accountId], $SYSTEM_ACCOUNTS['Amazon'])){
				$orderStatus = C('STATEPENDING');
				$orderType = C('STATEPENDING_CONV');
			}else{
				$orderStatus = C('STATEPENDING');
				$orderType = C('STATEPENDING_CONV');
			}
			
			/*if(self::judge_contain_combinesku_new($orderdetaillist)){
				$final_status = 606;
			}*/
			if($calcWeight > 2){
				echo "\t 超重订单";
				$orderStatus = C('STATEPENDING');
				$orderType = C('STATEPENDING_OW');
				//$returnStatus = array('orderStatus'=>C('STATEPENDING'), 'orderType'=>C('STATEPENDING_OW'));
			}
			
			//$expressArr = self::getCarrierInfoById(1);
			if(in_array($transportId, $express_delivery)){
				$orderType = C('STATEPENDING_HASARRIVED');
			}
			/*if (!in_array($ebay_carrier, $no_express_delivery) && !empty($ebay_carrier)){
				if(in_array($GLOBAL_EBAY_ACCOUNT[$accountId],$SYSTEM_ACCOUNTS['ebay']) || in_array($GLOBAL_EBAY_ACCOUNT[$accountId],$SYSTEM_ACCOUNTS['海外销售平台'])){
					$final_status = 641;//ebay和海外都跳转到 待打印线下和异常订单
				}else{
					$final_status = 639;
				}
			}*/
			//$sql = "UPDATE ebay_order SET ebay_status='$final_status' WHERE ebay_id ='$ebay_id' and ebay_status = '{$orderStatus}' ";
			//self::$dbConn->query($sql) or die("Fail : $sql");
			//$order_statistics->replaceData($order_sn, array('mask'=>1), array('mask'=>1));
			//$log_data .= "\n-------------------end ----------------------\n";
		}else{
			$log_data .= "[".date("Y-m-d H:i:s")."]\t订单同步状态有误,请联系IT解决!";
		}
		$returnStatus = array('orderStatus'=>$orderStatus, 'orderType'=>$orderType);
		$log_data .= "[".date("Y-m-d H:i:s")."]\t自动跳转的状态为--".$orderStatus."--".$orderType."!\n\n";
		self::write_scripts_log('auto_contrast_intercept', $GLOBAL_EBAY_ACCOUNT[$accountId], $log_data);
		return $returnStatus;
	}
	
	//异常订单拦截方法@20131111
	function intercept_exception_orders($orderData){
		global $SYSTEM_ACCOUNTS,$__liquid_items_fenmocsku,$__liquid_items_BuiltinBattery,$__liquid_items_SuperSpecific,$__liquid_items_Paste;
		global $GLOBAL_EBAY_ACCOUNT;
		global $express_delivery,$no_express_delivery;
		global $actualTotal0;
		self::initDB();
		$log_data = '';
		$orderStatus = empty($orderData['orderData']['orderStatus']) ? C('STATEPENDING') : $orderData['orderData']['orderStatus'];
		$transportId = $orderData['orderData']['transportId'];
		$countryName = $orderData['orderUserInfoData']['countryName'];
		$accountId = $orderData['orderData']['accountId'];
		$paymentTime = $orderData['orderData']['paymentTime'];
		$actualTotal	= $orderData['orderData']['actualTotal'];
		$ebay_username = $orderData['orderUserInfoData']['username'];
		$orderDataid = $orderData['orderExtenData']['orderId'];
		$ebay_usermail = $orderData['orderUserInfoData']['email'];
		$PayPalEmailAddress = $orderData['orderExtenData']['PayPalEmailAddress'];
		
		if(in_array($GLOBAL_EBAY_ACCOUNT[$accountId],$SYSTEM_ACCOUNTS['Amazon'])){//非线下amazon账号订单
			//ebay 平台可以重新计算运输方式 @ 20130301
			if (empty($countryName)){
				$orderStatus = C('STATESYNCINTERCEPT');
				$orderType = C('STATESYNCINTERCEPT_AB');
				
				$log_data .= "[".date("Y-m-d H:i:s")."]\t自动跳转的状态为--".$orderStatus."--".$orderType."!\n\n";
				self::write_scripts_log('auto_contrast_intercept', $GLOBAL_EBAY_ACCOUNT[$accountId], $log_data);
				return array('orderStatus'=>$orderStatus,'orderType'=>$orderType);
			}
		}
		
		if((in_array($GLOBAL_EBAY_ACCOUNT[$accountId], $SYSTEM_ACCOUNTS['ebay']) /*|| in_array($GLOBAL_EBAY_ACCOUNT[$accountId],$SYSTEM_ACCOUNTS['海外销售平台'])*/) && !empty($orderDataid)){//非线下ebay账号订单
			//ebay 平台可以重新计算运输方式 @ 20130301
			if (empty($countryName)){
				$orderStatus = C('STATESYNCINTERCEPT');
				$orderType = C('STATESYNCINTERCEPT_AB');
			}
			if($actualTotal != $actualTotal0){
				$actualTotal0 = (string) $actualTotal0;
			}
			echo "[".date("Y-m-d H:i:s")."]\t总价记录---{$ebay_id}---系统总价{$actualTotal}---计算总价{$actualTotal0}\n";
			if(in_array($ebay_usermail, array("", "Invalid Request")) && $ebay_carrier=='EUB'){
				$orderStatus = C('STATESYNCINTERCEPT');
				$orderType = C('STATESYNCINTERCEPT_AB');
			}else if($actualTotal != $actualTotal0 && $orderStatus == 1){
				$orderStatus = C('STATESYNCINTERCEPT');
				$orderType = C('STATESYNCINTERCEPT_AB');
			}else if(!empty($PayPalEmailAddress) && !in_array(strtolower($PayPalEmailAddress),get_account_paypalemails($accountId)) && $orderStatus == 1){
				$orderStatus = C('STATESYNCINTERCEPT');
				$orderType = C('STATEPENDING_EXCPAY');
			}
			
			$log_data .= "[".date("Y-m-d H:i:s")."]\t自动跳转的状态为--".$orderStatus."--".$orderType."!\n\n";
			self::write_scripts_log('auto_contrast_intercept', $GLOBAL_EBAY_ACCOUNT[$accountId], $log_data);
			return array('orderStatus'=>$orderStatus,'orderType'=>$orderType);
		}
		
		if($orderStatus == C('STATEOUTOFSTOCK')){//缺货和自动拦截判断
			//ebay 线上订单EUB大于5天,平邮和挂号大于7天不发货,不包括快递
			//海外销售十天
			$timeout = false;
			//$orderDataid = isset($orderData['ebay_orderid']) ? $orderData['ebay_orderid'] : '';
			//$ebay_paidtime = isset($orderData['ebay_paidtime']) ? $orderData['ebay_paidtime'] : '';
			if(!empty($paymentTime)){//线上订单,付款时间不能为空
				$diff_time = ceil((time()-$paymentTime)/(3600*24));
				if(in_array($GLOBAL_EBAY_ACCOUNT[$accountId], $SYSTEM_ACCOUNTS['ebay'])){
					if($ebay_carrier == 'EUB' && $diff_time > 5){
						$timeout = true;
					}else if((strpos($ebay_carrier, '平邮')!==false || strpos($ebay_carrier, '挂号')!==false) && $diff_time > 7){
						$timeout = true;
					}
				}/*else if(in_array($GLOBAL_EBAY_ACCOUNT[$accountId],$SYSTEM_ACCOUNTS['海外销售平台'])){
					if((strpos($ebay_carrier, '中国邮政平邮')!==false && $diff_time > 5) || $diff_time > 10){
						$timeout = true;
					}
				}*/
			}
			if($timeout){
				//$log_data .= "\n缺货订单={$ebay_id}======移动到缺货需退款中======\n";
				$orderStatus = C('STATEREFUND');
				$orderType = C('STATEREFUND_OUTSTOCK');
				$log_data .= "[".date("Y-m-d H:i:s")."]\t自动跳转的状态为--".$orderStatus."--".$orderType."!\n\n";
				self::write_scripts_log('auto_contrast_intercept', $GLOBAL_EBAY_ACCOUNT[$accountId], $log_data);
				return array('orderStatus'=>$orderStatus,'orderType'=>$orderType);
				//continue;
			}
		}
		
		if(in_array($orderStatus, array(C('STATESTOCKEXCEPTION')))){//缺货处理\合并包裹处理
			$have_goodscount = true;
			foreach ($orderdetaillist AS $orderdetail){
				$sku_arr = GoodsModel::get_realskuinfo($orderdetail['sku']);
				foreach($sku_arr as $or_sku => $or_nums){
					$allnums = $or_nums*$orderdetail['ebay_amount'];
					$skuinfo = self::get_sku_info($or_sku);
					$salensend = self::getpartsaleandnosendall($or_sku);
					//$sql = "UPDATE ebay_sku_statistics SET salensend = $salensend WHERE sku = '$or_sku' ";
					//self::$dbConn->query($sql);
					//$log_data .= "[".date("Y-m-d H:i:s")."]\t---{$sql}\n\n";
					$log_data .= "订单===$ebay_id===料号==$or_sku===实际库存为{$skuinfo['realnums']}===需求量为{$allnums}===待发货数量为{$salensend}===\n";
					if(!isset($skuinfo['realnums']) || empty($skuinfo['realnums']) || ($skuinfo['realnums'] - $salensend) < 0){//缺货本身算待发货，不能重复扣除
						$have_goodscount = false;
						break;
					}
				}
			}
			if($have_goodscount){
				$orderStatus = C('STATEPENDING');
				$orderType = C('STATEPENDING_CONV');
				$log_data .= "\n缺货订单={$ebay_id}======有货至待打印======\n";
				//$final_status = 618;
				$log_data .= "[".date("Y-m-d H:i:s")."]\t自动跳转的状态为--".$orderStatus."--".$orderType."!\n\n";
				self::write_scripts_log('auto_contrast_intercept', $GLOBAL_EBAY_ACCOUNT[$accountId], $log_data);
				return array('orderStatus'=>$orderStatus,'orderType'=>$orderType);
				//continue;
			}
		}
		return false;
	}
	
	public static function write_scripts_log($action, $ebay_account, $data){
		//add by Herman.Xi @ 20130306
		// /data/ebay_order_cronjob_logs/auto_contrast_intercept/${ebay_account}/${year_month}/${today}/
		$dirPath = "/home/ebay_order_cronjob_logs/{$action}/{$ebay_account}/".date("Y-m")."/".date("d");
		if (!is_dir($dirPath)){
			mkdirs($dirPath);
		}
		$filename = date("H").".txt";
		$readpath = $dirPath.'/'.$filename;
		//chmod($readpath, 0777);
		if(!file_exists($readpath)){
			return false;
		}
		if (!$handle=@fopen($readpath, 'a+')) {
			 return false;
		}
		if(flock($handle, LOCK_EX)) { 
			if (fwrite($handle, $data) === FALSE) {
				return false;
			}
			flock($handle, LOCK_UN);
		}
		fclose($handle);
		return true;
	}
	
	/* 
	 * 获取虚拟代发货库存+部分包货占用代发货库存
	 * add by Herman.Xi @20131021
	*/
	public static function getpartsaleandnosendall($sku, $storeId = 1){
		list($packagingnums, $inums) = json_decode(self::get_partsalenosend($sku, $storeId));
		$salensend = $packagingnums;
		$salensend2 = json_decode(self::getsaleandnosendall($sku, $storeId));
		if($salensend2){
			$salensend += $salensend2;
		}
		return $salensend;
	}
	
	//add by xiaojinhua end
	//获取在部分包货中的数量
	public static function get_partsalenosend($sku, $storeId = 1){
		self::initDB();
		//$start = time();
		
		$packagingnums = 0;
		$interceptnums = 0;
		//echo $sku; echo "<br>";
		$combineskus = GoodsModel::getCombineBySku($sku);
		//var_dump($combineskus); echo "<br>";
		$skus = array($sku=>1);
		if($combineskus){
			$skus += $combineskus;
		}
		//var_dump($skus);
		/*$combineskus = get_combinesku($sku);
		$skus = empty($combineskus) ? array() : array_keys($combineskus);
		array_push($skus, $sku);*/
		
		$StatusMenuAct = new StatusMenuAct();
		$menuList      = $StatusMenuAct->act_getStatusMenuList("*", " where dStatus = 1 and groupId = 0 and is_delete=0 and storeId= {$storeId} ");
		
		$dStatusCodes = array();
		foreach($menuList as $value){
			$dStatusCodes[] = $value['statusCode'];
		}
		
		//待发货二级状态已经暂不寄超过十天时间
		/*$ordersql 	 =  'SELECT a.id FROM om_unshipped_order AS a where a.orderType in ('.join(',', $dStatusCodes).') and a.is_delete=0 and a.storeId=  '.$storeId;
		$query	     =	self::$dbConn->query($ordersql);
		$orders      =	self::$dbConn->fetch_array_all($query);
		
		foreach($orders as $value){*/
		foreach($skus as $_sku => $_num){
			$detailsql 	 =  'SELECT        a.id as omOrderId, b.id as omOrderdetailId, b.sku, b.amount 
							 FROM          om_unshipped_order AS a 
							 LEFT JOIN     om_unshipped_order_detail AS b 
							 ON            a.id = b.omOrderId 
							 WHERE         a.orderStatus in ('.join(',', $dStatusCodes).') 
							 AND           a.is_delete=0 
							 AND           a.storeId=  '.$storeId.' 
							 AND           b.sku = "'.$_sku.'" 
							 AND           b.is_delete=0 
							 AND           b.storeId= '.$storeId;
			//echo $detailsql; echo "<br>";
			$query	     =	self::$dbConn->query($detailsql);
			$detail      =	self::$dbConn->fetch_array_all($query);
			
			/*$end = time();
			echo $end-$start; echo "<br>";
			$start = $end;*/
		
			//var_dump($detail);
			if(empty($detail)){
				continue;
			}
			foreach($detail as $val){
				//$_sku    = $val['sku'];
				$amount = $val['amount'];
				/*$checksql = "SELECT * FROM om_records_order_audit WHERE omOrderId={$value['id']} AND omOrderdetailId = '{$val['id']}' AND sku='{$sku}'";
				$checksql = self::$dbConn->query($checksql);
				$checksql = $dbConn->fetch_one($checksql);*/
				
				$checksql = self::getRecordsOrderAudit($val['omOrderId'], $_sku);
				if (empty($checksql)||$checksql['auditStatus']==2){
					$interceptnums += $_num*$amount;
					continue;
				}
				//var_dump($val['omOrderId'], $_sku);
				$schecksql = WarehouseAPIModel::getOrderSkuPickingRecords($val['omOrderId'], $sku);
				if(!empty($schecksql)){
					$schecksql = json_decode($schecksql,true);
				}
				//var_dump($schecksql);
				/*$schecksql = "SELECT realnum,totalnum FROM ebay_packing_status WHERE ebaydetail_id='{$checksql['ebaydetail_id']}' AND sku='{$sku}' ";
				$schecksql = $dbcon->execute($schecksql);
				$schecksql = $dbcon->fetch_array($schecksql);*/
				
				if (!empty($schecksql)&&$schecksql['amount']==$schecksql['totalNums']){
					continue;
				}else if(!empty($schecksql)&&$schecksql['amount']<$schecksql['totalNums']){
					$packagingnums += $realtimes*($schecksql['totalNums']-$schecksql['amount']);
				}else if(!empty($schecksql)){
					//$packagingnums += $realtimes*$list['ebay_amount'];
					$packagingnums += $realtimes*($schecksql['totalNums']-$schecksql['amount']);
				}else{ //没有包货记录的情况下 add by xiaojinhua 2013-05-24
					/*$sql = "select  ebay_amount from ebay_orderdetail where ebay_id='{$checksql['ebaydetail_id']}'";
					$sql = $dbcon->execute($sql);
					$rtn = $dbcon->fetch_one($sql);*/
					$packagingnums += $_num * $amount;
				}
				
				/*if($sku == $_sku){
					$totalnums += ($amount*$realtimes);
				}else{
					$skuinfo = GoodsModel::getCombineSkuinfo($_sku);
					if($skuinfo){
						$skuinfoDetail = $skuinfo['detail'];
						foreach($skuinfoDetail as $skuinfoDetailValue){
							$ssku = $skuinfoDetailValue['sku'];
							$scount = $skuinfoDetailValue['count'];
							if($sku == $ssku){
								$totalnums += ($scount*$amount*$realtimes);
							}
						}
					}
				}*/
			}
		}
		//}
		
		/*foreach ($skus AS $_sku){
			$realtimes = 1;
			if ($_sku!=$sku&&$combineskus[$_sku]){
				$skulist = explode(',', $combineskus[$_sku]);
				foreach ($skulist AS $sku_info){
					list($_s,$times) = explode('*', $sku_info);
					if ($_s==$sku){
						$realtimes = $times;
					}
				}
				
			}else{
				$realtimes = 1;
			}
			$sql = "SELECT a.ebay_id,b.ebay_id as detail_id,b.ebay_amount FROM ebay_order AS a 
						LEFT JOIN ebay_orderdetail AS b ON a.ebay_ordersn=b.ebay_ordersn 
						WHERE a.ebay_userid !='' 
						AND a.ebay_status IN (652,653, 654) 
						AND a.ebay_combine!='1' 
						AND b.sku='{$_sku}'";
			$sql = self::$dbConn->query($sql);
			$lists = self::$dbConn->fetch_array_all($sql);
			foreach ($lists AS $list){
				
				$checksql = "SELECT check_status ,ebaydetail_id FROM ebay_unusual_order_check WHERE ebay_id={$list['ebay_id']} AND ebaydetail_id = '{$list['detail_id']}' AND sku='{$sku}'";
				$checksql = self::$dbConn->query($checksql);
				$checksql = $dbConn->fetch_one($checksql);
				
				if (empty($checksql)||$checksql['check_status']==2){
					$interceptnums += $realtimes*$list['ebay_amount'];
					continue;
				}
				$schecksql = "SELECT realnum,totalnum FROM ebay_packing_status WHERE ebaydetail_id='{$checksql['ebaydetail_id']}' AND sku='{$sku}'";
				$schecksql = self::$dbConn->query($schecksql);
				$schecksql = self::$dbConn->fetch_array_all($schecksql);
				if (!empty($schecksql)&&$schecksql[0]['realnum']==$schecksql[0]['totalnum']){
					continue;
				}else if(!empty($schecksql)&&$schecksql[0]['realnum']<$schecksql[0]['totalnum']){
					$packagingnums += $realtimes*($schecksql[0]['totalnum']-$schecksql[0]['realnum']);
				}else if(!empty($schecksql)){
					//$packagingnums += $realtimes*$list['ebay_amount'];
					$packagingnums += $realtimes*($schecksql[0]['totalnum']-$schecksql[0]['realnum']);
				}else{ //没有包货记录的情况下 add by xiaojinhua 2013-05-24
					$sql = "select  ebay_amount from ebay_orderdetail where ebay_id='{$checksql['ebaydetail_id']}'";
					$sql = self::$dbConn->query($sql);
					$rtn = $dbConn->fetch_one($sql);
					$packagingnums += $realtimes * $rtn["ebay_amount"];
				}
			}
		}*/
		return json_encode(array($packagingnums,$interceptnums));
	}
	
	public static function getRecordsOrderAudit($omOrderId, $sku){
		self::initDB();
		
		//$checksql = "SELECT * FROM om_records_order_audit WHERE omOrderId={$omOrderId} AND omOrderdetailId = '{$omOrderdetailId}' AND sku='{$sku}'";
		$checksql = "SELECT * FROM om_records_order_audit WHERE omOrderId={$omOrderId} AND sku='{$sku}'";
		//echo $checksql; echo "<br>";
		$checksql = self::$dbConn->query($checksql);
		$checksql = self::$dbConn->fetch_array($checksql);
		//var_dump($checksql);
		return $checksql;
	}
	
	public static function getsaleandnosendall($sku, $storeId = 1){
		//获取虚拟/待发货库存（除超大订单进来的待发货）
		self::initDB();
		
		$totalnums = 0;
		$combineskus = GoodsModel::getCombineBySku($sku);
		//var_dump($combineskus);
		$skus = empty($combineskus) ? array() : array_keys($combineskus);
		array_push($skus, $sku);
		//var_dump($skus); echo "<br>";
		$skus_str = implode("','",$skus);
		$skus_str = "'".$skus_str."'";
		
		//echo "<pre>";
		//$skus = GoodsModel::getCombineANDSKU($sku); //$combineskus
		//var_dump($skus); echo "<br>";
		//exit;
		//return 0;
		$menuList	=	StatusMenuModel::getStatusMenuList("*", " where dStatus = 1 and groupId = 0 and is_delete=0 and storeId= {$storeId} ");
		//var_dump($menuList); exit;
		$dStatusCodes = array();
		foreach($menuList as $value){
			if($value['statusCode']){
			$dStatusCodes[] = $value['statusCode'];
			}
		}
		
		$tendaytime = time() -10*24*60*60;
		//待发货二级状态已经暂不寄超过十天时间
		$ordersql 	 =  'SELECT b.sku, b.amount FROM om_unshipped_order AS a JOIN om_unshipped_order_detail AS b ON a.id = b.omOrderId where a.orderStatus in ('.join(',', $dStatusCodes).') and a.is_delete=0 and a.storeId= '.$storeId.' AND b.sku in ('.$skus_str.') and b.is_delete=0 and b.storeId= '.$storeId;
		$query	     =	self::$dbConn->query($ordersql);	
		$skunums     =	self::$dbConn->fetch_array_all($query);
		
		foreach($skunums as $sku_info){
			$realtimes = get_realtime($sku_info["sku"]);
			$totalnums += ($sku_info["amount"]*$realtimes);
		}
		
		$ordersql 	 =  'SELECT b.sku, b.amount FROM om_unshipped_order AS a JOIN om_unshipped_order_detail AS b ON a.id = b.omOrderId where a.orderStatus = '.C('STATESENDTEMP').' and a.paymentTime > '.$tendaytime.' and a.is_delete=0 and a.storeId= '.$storeId.' AND b.sku in ('.$skus_str.') and b.is_delete=0 and b.storeId= '.$storeId;
		$query	     =	self::$dbConn->query($ordersql);
		$skunums     =	self::$dbConn->fetch_array_all($query);
		
		foreach($skunums as $sku_info){
			$realtimes = get_realtime($sku_info["sku"]);
			$totalnums += ($sku_info["amount"]*$realtimes);
		}
		
		/*$ordersql 	 =  'SELECT a.id FROM om_unshipped_order AS a where a.orderStatus in ('.join(',', $dStatusCodes).') and a.is_delete=0 and a.storeId= '.$storeId;
		$ordersql    .= ' UNION ';
		$ordersql    .= 'SELECT a.id FROM om_unshipped_order AS a where a.orderStatus = '.C('STATESENDTEMP').' and a.paymentTime > '.$tendaytime.' and a.is_delete=0 and a.storeId= '.$storeId;
		//echo $ordersql; echo "<br>"; exit;
		$query	     =	self::$dbConn->query($ordersql);	
		$orders      =	self::$dbConn->fetch_array_all($query);*/
		//var_dump($orders); exit;
		//return 10;
		/*foreach($orders as $value){
			//foreach($skus as $_sku => $_num){
				$detailsql 	 =  'SELECT b.sku, b.amount FROM om_unshipped_order_detail AS b where b.omOrderId = '.$value['id'].' AND b.sku in ('.$skus_str.') and b.is_delete=0 and b.storeId= '.$storeId;
				echo $detailsql; echo "<br>";
				exit;
				$query	     =	self::$dbConn->query($detailsql);
				$detail      =	self::$dbConn->fetch_array_all($query);
				if(empty($detail)){
					continue;
				}
				//var_dump($detail);
				foreach($detail as $val){
					//$_sku    = $val['sku'];
					$amount = intval($val['amount']);
					$totalnums += ($amount*$_num);
				}
			//}
		}*/
		//var_dump($totalnums);
		//新的待发货计算没有数据，并且缺货配货记录，等待配货的信息@2013-11-10
		
		return json_encode($totalnums);
	}
	
	public static function getinterceptall($sku, $storeId = 1){
		//获取已拦截数量
		self::initDB();
		
		$totalnums = 0;
		/*$combineskus = get_combinesku($sku);
		$skus = empty($combineskus) ? array() : array_keys($combineskus);
		array_push($skus, $sku);*/
		
		$combineskus = GoodsModel::getCombineBySku($sku);
		$skus = array($sku=>1) + $combineskus;
	
		foreach ($skus AS $_sku => $_num){
			/*$realtimes = 1;
			if ($_sku!=$sku&&$combineskus[$_sku]){
				$skulist = explode(',', $combineskus[$_sku]);
				foreach ($skulist AS $sku_info){
					list($_s,$times) = explode('*', $sku_info);
					if ($_s==$sku){
						$realtimes = $times;
					}
				}
				
			}else{
				$realtimes = 1;
			}*/
			$ordersql 	 =  'SELECT         sum(b.amount) AS qty 
							FROM 			om_unshipped_order AS a 
							LEFT JOIN       om_unshipped_order_detail AS b
							ON 			    b.omOrderId = a.id
							WHERE			a.orderStatus = '.C('STATEOVERSIZEDORDERS').'
							AND				a.orderType = '.C('STATEOVERSIZEDORDERS_WB').'
							AND				b.sku = "'.$_sku.'"
							AND 			a.is_delete=0
							AND 			a.storeId= '.$storeId
							.' LIMIT 1 ';
			//echo $ordersql; echo "<br>";
			$query	     =	self::$dbConn->query($ordersql);	
			$orders      =	self::$dbConn->fetch_array($query);
		/*	$sql = "SELECT sum(b.ebay_amount) AS qty 
						FROM ebay_order AS a 
						LEFT JOIN ebay_orderdetail AS b ON a.ebay_ordersn = b.ebay_ordersn 
						WHERE a.ebay_status=642
						AND b.sku='{$_sku}'
						AND a.ebay_combine!='1'
						LIMIT 1";
			$sql = $dbcon->execute($sql);
			$skunums = $dbcon->getResultArray($sql);*/
			if (!empty($orders)){
				$totalnums += $orders['qty']*$_num;
			}
		}
		return json_encode($totalnums);
	}
	
	public static function get_autointercept($sku, $storeId = 1){
		//add by xiaojinhua  获取自动拦截占有数量
		self::initDB();
		
		$totalnums = 0;
		/*$combineskus = get_combinesku($sku);
		$skus = empty($combineskus) ? array() : array_keys($combineskus);
		array_push($skus, $sku);*/
		
		$combineskus = GoodsModel::getCombineBySku($sku);
		$skus = array($sku=>1) + $combineskus;
		
		foreach ($skus AS $_sku => $_num){
			/*$realtimes = 1;
			if ($_sku!=$sku&&$combineskus[$_sku]){
				$skulist = explode(',', $combineskus[$_sku]);
				foreach ($skulist AS $sku_info){
					list($_s,$times) = explode('*', $sku_info);
					if ($_s==$sku){
						$realtimes = $times;
					}
				}
				
			}else{
				$realtimes = 1;
			}*/
			/*$sql = "SELECT sum(b.ebay_amount) AS qty 
						FROM ebay_order AS a 
						LEFT JOIN ebay_orderdetail AS b ON a.ebay_ordersn = b.ebay_ordersn 
						WHERE a.ebay_status in (658,661)
						AND b.sku='{$_sku}'
						AND a.ebay_combine!='1'
						LIMIT 1";
			$sql = $dbcon->execute($sql);
			$skunums = $dbcon->getResultArray($sql);*/
			$ordersql 	 =  'SELECT         sum(b.amount) AS qty 
							FROM 			om_unshipped_order AS a 
							LEFT JOIN       om_unshipped_order_detail AS b
							ON 			    b.omOrderId = a.id
							WHERE			a.orderStatus = '.C('STATEOUTOFSTOCK').'
							AND				a.orderType IN ('.C('STATEOUTOFSTOCK_PO').','.C('STATEOUTOFSTOCK_AO').') 
							AND				b.sku = "'.$_sku.'"
							AND 			a.is_delete=0
							AND 			a.storeId= '.$storeId
							.' LIMIT 1 ';
			//echo $ordersql; echo "<br>";
			$query	     =	self::$dbConn->query($ordersql);	
			$orders      =	self::$dbConn->fetch_array($query);
			if (!empty($orders)){
				$totalnums += $orders['qty']*$_num;
			}
		}
		return json_encode($totalnums);
	}
	
	public static function getauditingall($sku, $storeId = 1){
		//获取待审核数量
		self::initDB();
		
		$totalnums = 0;
		/*$combineskus = get_combinesku($sku);
		$skus = empty($combineskus) ? array() : array_keys($combineskus);
		array_push($skus, $sku);*/
		
		$combineskus = GoodsModel::getCombineBySku($sku);
		$skus = array($sku=>1) + $combineskus;
	
		foreach ($skus AS $_sku => $_num){
			/*$realtimes = 1;
			if ($_sku!=$sku&&$combineskus[$_sku]){
				$skulist = explode(',', $combineskus[$_sku]);
				foreach ($skulist AS $sku_info){
					list($_s,$times) = explode('*', $sku_info);
					if ($_s==$sku){
						$realtimes = $times;
					}
				}
				
			}else{
				$realtimes = 1;
			}*/
			$ordersql 	 =  'SELECT         sum(b.amount) AS qty 
							FROM 			om_unshipped_order AS a 
							LEFT JOIN       om_unshipped_order_detail AS b
							ON 			    b.omOrderId = a.id
							WHERE			a.orderStatus = '.C('STATEOVERSIZEDORDERS').'
							AND				a.orderType = '.C('STATEOVERSIZEDORDERS_PEND').'
							AND				b.sku = "'.$_sku.'"
							AND 			a.is_delete=0
							AND 			a.storeId= '.$storeId
							.' LIMIT 1 ';
			//echo $ordersql; echo "<br>";
			$query	     =	self::$dbConn->query($ordersql);	
			$orders      =	self::$dbConn->fetch_array($query);
		/*	$sql = "SELECT sum(b.ebay_amount) AS qty 
						FROM ebay_order AS a 
						LEFT JOIN ebay_orderdetail AS b ON a.ebay_ordersn = b.ebay_ordersn 
						WHERE a.ebay_status=642
						AND b.sku='{$_sku}'
						AND a.ebay_combine!='1'
						LIMIT 1";
			$sql = $dbcon->execute($sql);
			$skunums = $dbcon->getResultArray($sql);*/
			if (!empty($orders)){
				$totalnums += $orders['qty']*$_num;
			}
		}
		return json_encode($totalnums);
	}
	
	public static function get_firstsale($sku, $storeId = 1){
		self::initDB();
		//获取第一次销售时间
		/*$combineskus = get_combinesku($sku);
		$skus = empty($combineskus) ? array() : array_keys($combineskus);
		array_push($skus, $sku);
		$skus = array2strarray($skus);*/
		
		$combineskus = GoodsModel::getCombineBySku($sku);
		$skus = array($sku=>1) + $combineskus;
		
		$key_skus = array_keys($skus);
		
		$ordersql = "SELECT			a.paymentTime,a.orderAddTime 
					FROM 			om_unshipped_order AS a 
					LEFT JOIN       om_unshipped_order_detail AS b 
					ON 				b.omOrderId = a.id 
					WHERE 			a.is_delete = 0 
					AND 			b.sku IN ('".implode("','", $key_skus)."')
					AND				a.storeId = ".$storeId."
					ORDER BY 		a.id ASC
					LIMIT 1";
		//echo $ordersql; echo "<br>";
		$query	     =	self::$dbConn->query($ordersql);
		$order       =	self::$dbConn->fetch_array($query);
		//var_dump($order);
		$result 	 = '0';
		if(!empty($order)){
			$result  =  $order['paymentTime'] != '' ? json_encode($order['paymentTime']) : json_encode($order['orderAddTime']);
		}
		//var_dump($result);
		return json_encode($result);
	}
	
	public static function get_lastsale($sku, $storeId = 1){
		//获取最后一次销售时间
		self::initDB();
		
		/*$combineskus = get_combinesku($sku);*/
		$combineskus = GoodsModel::getCombineBySku($sku);
		$skus = array($sku=>1) + $combineskus;
		$key_skus = array_keys($skus);
		//array_push($skus, $sku);
		//$skus = array2strarray($skus);
		//$where = '';
		//if(!empty($skus)){
			$where = " b.sku IN ('".join("','", $key_skus)."') ";
		//}
		
		$ordersql = "SELECT         a.paymentTime,a.orderAddTime
					 FROM 			om_unshipped_order AS a 
					 LEFT JOIN      om_unshipped_order_detail AS b
					 ON 			b.omOrderId = a.id 
					 WHERE 			{$where}
					 AND 			a.is_delete=0
					 AND 			a.storeId= ".$storeId ." 
					 ORDER BY 		a.id DESC LIMIT 1";
		//echo $ordersql;
		$query	     =	self::$dbConn->query($ordersql);
		$order       =	self::$dbConn->fetch_array($query);
		
		$result 	 = '0';
		if(!empty($order)){
			//return $order[0]['ebay_paidtime'] != '' ? $order[0]['ebay_paidtime'] : $order[0]['ebay_addtime'];
			$result = $order['orderAddTime'] != '' ? json_encode($order['orderAddTime']) : json_encode($order['paymentTime']);
		}
		return json_encode($result);
	}
	
	public static function getSaleProducts($start, $end, $sku, $everyday_sale = 5, $storeId = 1){
		//获取一段时间内的销售数量
		self::initDB();
		
		$totalnums = '0';
		/*$combineskus = get_combinesku($sku);
		$skus = empty($combineskus) ? array() : array_keys($combineskus);
		array_push($skus, $sku);
		foreach ($skus AS $_sku){*/
		$combineskus = GoodsModel::getCombineBySku($sku);
		//echo "<pre>"; var_dump($combineskus);
		//var_dump(array($sku=>1));
		$skus = array($sku=>1) + $combineskus;
		
		$StatusMenuAct = new StatusMenuAct();
		$menuList      = $StatusMenuAct->act_getStatusMenuList("*", " where sStatus = 2 and groupId != 0 and is_delete=0 and storeId= {$storeId} ");
		//var_dump($menuList); exit;
		$sStatusCodes = array();
		foreach($menuList as $value){
			$sStatusCodes[] = $value['statusCode'];
		}
		//var_dump($skus);
		foreach ($skus AS $_sku => $_num){
			/*$realtimes = 1;
			if ($_sku!=$sku&&$combineskus[$_sku]){
				$skulist = explode(',', $combineskus[$_sku]);
				foreach ($skulist AS $sku_info){
					list($_s,$times) = explode('*', $sku_info);
					if ($_s==$sku){
						$realtimes = $times;
					}
				}
			}else{
				$realtimes = 1;
			}*/
			$maxnums = $everyday_sale>=5 ? ceil(10*$everyday_sale/$_num) : 50;
			$ordersql = "SELECT 		sum(b.amount) as qty 
					 	FROM 			om_unshipped_order AS a 
						LEFT JOIN       om_unshipped_order_detail AS b
					 	ON 				b.omOrderId = a.id 
						WHERE 			a.orderType NOT IN (".join(',', $sStatusCodes).") 
						AND 			a.is_delete = 0
						AND 			(a.paymentTime>{$start} OR a.orderAddTime>{$start})
						AND 			(a.paymentTime<{$end} OR a.orderAddTime<{$end})
						AND 			b.sku='{$_sku}'
						AND 			b.amount<{$maxnums}
						AND				a.storeId = ".$storeId."
						LIMIT 1";
			//var_dump($ordersql);
			// 去掉淘宝刷单状态的订单
			//修复因付款时间为空导致每日均量数据异常的BUG add by guanyongjun 2013-10-08
			//if ($truename=='vipchen') echo "<br/>".$sql."<br/>";
			$query	     =	self::$dbConn->query($ordersql);
			$orders      =	self::$dbConn->fetch_array($query);
			//var_dump($orders);
			//$orders['qty'] = empty($orders['qty']) ? 0 : $orders['qty'];
			//var_dump($orders['qty']);
			if (!empty($orders)){
				$totalnums += $orders['qty']*$_num;
			}
			//var_dump($totalnums);
		}
		//var_dump($totalnums);
		return json_encode($totalnums);
	}
	
	public static function getorderbysku($sku, $storeId = 1){
		//通过sku获取待发货中的订单
		self::initDB();
		
		$results = array();
		
		/*$combineskus = get_combinesku($sku);
		$skus = empty($combineskus) ? array() : array_keys($combineskus);
		array_push($skus, $sku);*/
		
		$combineskus = GoodsModel::getCombineBySku($sku);
		$skus = array($sku=>1) + $combineskus;
		
		$StatusMenuAct = new StatusMenuAct();
		$menuList      = $StatusMenuAct->act_getStatusMenuList("*", " where dStatus = 1 and groupId = 0 and is_delete=0 and storeId= {$storeId} ");
		//var_dump($menuList); exit;
		$dStatusCodes = array();
		foreach($menuList as $value){
			$dStatusCodes[] = $value['statusCode'];
		}
		//var_dump($dStatusCodes);
		foreach ($skus AS $_sku => $_num){
			$ordersql = "SELECT 		a.id 
						FROM 			om_unshipped_order AS a 
						LEFT JOIN       om_unshipped_order_detail AS b 
						ON 				b.omOrderId = a.id 
						WHERE 			a.orderStatus NOT IN (".join(',', $dStatusCodes).") 
						AND 			a.is_delete = 0 
						AND 			b.sku='{$_sku}' 
						AND				a.storeId = ".$storeId;
			//echo $ordersql;
			$query	     =	self::$dbConn->query($ordersql);
			$ebay_ids    =	self::$dbConn->fetch_array_all($query);
			if (!empty($ebay_ids)){
				foreach ($ebay_ids AS $ebay_id){
					$results[] = $ebay_id['id'];
				}
			}
		}
		return json_encode($results);
	}
	
	public static function check_sku($sku, $num){
	
		self::initDB();
	
		$sku = trim($sku);
		
		/*$sql = "SELECT o.goods_count,g.cguser FROM ebay_goods AS g LEFT JOIN ebay_onhandle AS o ON o.goods_sn=g.goods_sn WHERE o.goods_sn='{$sku}'";
		$sql		= self::$dbConn->query($sql);
		$goodsinfo  = self::$dbConn->fetch_array_all($sql);*/
		
		$sql = "SELECT g.* FROM pc_goods AS g WHERE g.sku='{$sku}'";
		$sql		= self::$dbConn->query($sql);
		$goodsinfo  = self::$dbConn->fetch_array_all($sql);
		//var_dump($sku,$goodsinfo);
		if (empty($goodsinfo)||empty($goodsinfo[0]['purchaseId'])){
			echo "\n该料号{$sku}没有添加采购人员!\n";
			return true;
		}
		
		//$goodsCountInfo = OldsystemModel::qccenterGetErpGoodscount($sku);
		$goodsCountInfo = WarehouseAPIModel::getSkuStock($sku);
		if (is_null($goodsCountInfo)){
			echo "\n该料号{$sku}没有库存信息!\n";
			return true;
		}
		
		$sql = "SELECT * FROM om_sku_daily_status WHERE sku='{$sku}'";
		$sql = self::$dbConn->query($sql);
		$sku_info = self::$dbConn->fetch_array($sql);
		
		if(empty($sku_info)){
			echo "\n该料号{$sku}没有统计信息!\n";
			return true;
		}
		
		$goods_count = $goodsCountInfo;
		//$goods_location = $goodsCountInfo['goods_location'];
		//$cguser = $goodsCountInfo['cguser'];
		
		//var_dump($goodsCount);
		
		/*if ($num>9&&$num>$goodsinfo[0]['goods_count']){
			echo "\n该料号数量{$num},而实际库存是{$goodsinfo[0]['goods_count']}!\n";
			return false;
		}
		
		if ($sku_info['everyday_sale']=='0.00'&&$num>9){
			echo "\n该料号第一次卖出,数量{$num},实际库存{$goodsinfo[0]['goods_count']}!\n";
			return false;
		}*/
		$everyday_sale = $sku_info['AverageDailyCount'];
		$takenum = ceil($everyday_sale*10);
		
		$actuallaygoods = $goods_count;
		if($actuallaygoods == 0){
			$goods_bili = 0;
		}else{
			$goods_bili = $num / $actuallaygoods;
		}
		if ($num>9 && $num>$takenum){
			echo "\n该料号{$sku}超出10天的销售量,数量{$num},实际库存{$goods_count},每天销售量{$everyday_sale}!\n";
			return false;
		}else if($goods_bili>0.5 && $num>$takenum && $actuallaygoods > 0 && $takenum > 0){
			echo "\n该料号{$sku}超出10天的销售量,且发货数量大于库存数量一半,数量{$num},实际库存{$goods_count},每天销售量{$everyday_sale}!\n";
			return false;
		}else{
			//echo "\n通过料号检测,数量{$num},实际库存{$goods_count}!\n";
			return true;
		}
	}
	
	public static function get_sku_info($sku){
	
		self::initDB();
		
		//$sql = "SELECT cguser FROM ebay_goods WHERE goods_sn='{$sku}'";
		/*$sql = "SELECT o.goods_count,g.cguser FROM ebay_goods AS g LEFT JOIN ebay_onhandle AS o ON o.goods_sn=g.goods_sn WHERE o.goods_sn='{$sku}'";
		$sql		= self::$dbConn->query($sql);
		$goodsinfo  = self::$dbConn->fetch_array_all($sql);
		
		if (empty($goodsinfo)||empty($goodsinfo[0]['cguser'])){
			return array();
		}*/
		
		$sql = "SELECT g.* FROM pc_goods AS g WHERE g.sku='{$sku}'";
		$sql		= self::$dbConn->query($sql);
		$goodsinfo  = self::$dbConn->fetch_array_all($sql);
		//var_dump($sku,$goodsinfo);
		if (empty($goodsinfo)||empty($goodsinfo[0]['purchaseId'])){
			echo "\n该料号{$sku}没有添加采购人员!\n";
			return true;
		}
		
		//$goodsCountInfo = OldsystemModel::qccenterGetErpGoodscount($sku);
		$goodsCountInfo = WarehouseAPIModel::getSkuStock($sku);
		/*if (!$goodsCountInfo){
			echo "\n该料号没有库存信息!\n";
			return true;
		}*/
		
		if (is_null($goodsCountInfo)){
			echo "\n该料号{$sku}没有库存信息!\n";
			return true;
		}
		
		/*$sql = "SELECT * FROM om_sku_daily_status WHERE sku='{$sku}'";
		$sql = self::$dbConn->query($sql);
		$sku_info = self::$dbConn->fetch_array($sql);*/
		
		$purchaseinfo = !empty($sku_info) ? $sku_info : array();
		//$purchaseinfo['realnums'] = $goodsCountInfo['goods_count'];
		$purchaseinfo['realnums'] = $goodsCountInfo;
		
		return $purchaseinfo;
	}
	
	public static function get_waiting_sale($sku){
		//获取海外仓待发货数据 Herman.Xi
		$totalnums = 0;
		$combineskus = GoodsModel::getCombineBySku($sku);
		$skus = array($sku=>1) + $combineskus;
		/*$combineskus = get_combinesku($sku);
		$skus = empty($combineskus) ? array() : array_keys($combineskus);
		array_push($skus, $sku);*/
	
		$StatusMenuAct = new StatusMenuAct();
		$menuList      = $StatusMenuAct->act_getStatusMenuList("*", " where dStatus = 1 and groupId = 0 and is_delete=0 and storeId= {$storeId} ");
		//var_dump($menuList); exit;
		$dStatusCodes = array();
		foreach($menuList as $value){
			$dStatusCodes[] = $value['statusCode'];
		}
		//var_dump($dStatusCodes);
		foreach ($skus AS $_sku => $_num){
			$ordersql = "SELECT 		a.id,b.sku,b.amount
						FROM 			om_unshipped_order AS a 
						LEFT JOIN       om_unshipped_order_detail AS b 
						ON 				b.omOrderId = a.id 
						WHERE 			a.orderStatus NOT IN (".join(',', $dStatusCodes).") 
						AND 			a.is_delete = 0 
						AND 			b.sku='{$_sku}' 
						AND				a.storeId = ".$storeId;
			//echo $ordersql;
			$query	     =	self::$dbConn->query($ordersql);
			$ebay_ids    =	self::$dbConn->fetch_array_all($query);
			if (!empty($ebay_ids)){
				foreach ($ebay_ids AS $ebay_id){
					$realtimes = self::get_realtime($sku_info["sku"]);
					$totalnums += ($sku_info["amount"]*$realtimes);
				}
			}
		}
		return $totalnums;
	
		/*$sql = "SELECT b.ebay_amount ,b.sku
						FROM ebay_order AS a 
						LEFT JOIN ebay_orderdetail AS b ON a.ebay_ordersn = b.ebay_ordersn 
						WHERE a.ebay_status  IN (705,706,707,710)
						AND b.sku in ({$skus_str})
						AND a.ebay_combine!='1'
						"; 
		$sql = $dbcon->execute($sql);
		$skunums = $dbcon->getResultArray($sql);
		foreach($skunums as $sku_info){
			$realtimes = get_realtime($sku_info["sku"]);
			$totalnums += ($sku_info["ebay_amount"]*$realtimes);
		}
		return $totalnums;*/
	}
	
	public static function get_realtime($sku){
		$realtimes = 1;
		if(strpos($sku,"*") === false){
			return 1;
		}else{
			$sku_arr = explode('*', $sku);
			$realtimes = $sku_arr[0];
			return $realtimes;
		}
	}
	
	public static function judge_contain_combinesku_new($orderdetaillist){
		//判断订单是否包含组合料号
		self::initDB();
		$iszuhe	= false;		
		foreach ($orderdetaillist AS $orderdetail){
			//var_dump($orderdetail['sku']);
			$sku = $orderdetail['orderDetailData']['sku'];
			if(GoodsModel::getCombineSkuinfo($sku)){
				$iszuhe	= true;
				break;
			}
		}
		return $iszuhe;
	}
	
	public static function judge_contain_combinesku($omOrderId, $storeId=1){
		//判断订单是否包含组合料号
		self::initDB();
		
		$sql = "SELECT sku,amount FROM om_unshipped_order_detail WHERE omOrderId = '{$omOrderId}' and storeId = {$storeId} and is_delete = 0 ";
		$sql = self::$dbConn->query($sql);
		$orderdetaillist = self::$dbConn->fetch_array_all($sql);
		$iszuhe	= false;		
		foreach ($orderdetaillist AS $orderdetail){
			$sku = $orderdetail['sku'];
			if(GoodsModel::getCombineSkuinfo($sku)){
				$iszuhe	= true;
				break;
			}
		}
		return $iszuhe;
	}
	
	public static function updateSkuStatistics($sku,$data){
		self::initDB();
		//BaseModel :: begin(); //开始事务
		$string = array2sql($data);//salensend = $salensend
		$sql = "UPDATE om_sku_statistics SET {$string} WHERE sku = '$sku' ";
		if(self::$dbConn->query($sql)){
			/*if(OldsystemModel::updateSkuStatistics($sku,json_encode($sendArr))){
				BaseModel :: commit();
				BaseModel :: autoCommit();*/
				return true;
			//}
		}
		return false;
	}
	
	/**
	 * CommonModel::getSkuImg()
	 * 获取sku图片
	 * @param string $spu 主料号
	 * @param string $picType 图片类型
	 * @param string $sku 待用
	 * @return string
	 */
	public static function getSkuImg($spu, $sku, $picType){
		require_once WEB_PATH."api/include/functions.php";
		$url	= 'http://gw.open.valsun.cn:88/router/rest?';
		$paramArr= array(
			/* API系统级输入参数 Start */
			'method'	=> 'datacenter.picture.getAllSizePic',  //API名称
			'format'	=> 'json',  //返回格式
			'v'			=> '1.0',   //API版本号
			'username'	=> C('OPEN_SYS_USER'),
			/* API系统级参数 End */
			/* API应用级输入参数 Start*/
			'spu'		=> $spu,  //主料号
			'picType'	=> $picType, //站点
			/* API应用级输入参数 End*/
		);
		$data 	= callOpenSystem($paramArr);
		$data 	= json_decode($data, true);
		//var_dump($data);
		$imgUrl = isset($data['data']['artwork']) ? $data['data']['artwork'][$spu][0] : '';
        return $imgUrl;
	}

	/**
	 * CommonModel::getSpuAllPic()
	 * 获取sku图片(取得对应spu的所有图片,spu是一个数组)
	 * @param string $spu 主料号
	 * @param string $picType 图片类型
	 * @param string $sku 待用
	 * @return string
	 * @addTime 20131202
	 */
	public static function getSpuAllPic($spu, $picType){
		require_once WEB_PATH."api/include/functions.php";
		$url	= 'http://gw.open.valsun.cn:88/router/rest?';

		$paramArr= array(
			/* API系统级输入参数 Start */
			'method'	=> 'datacenter.picture.getSpuAllSizePic',  //API名称
			'format'	=> 'json',  //返回格式
			'v'			=> '1.0',   //API版本号
			'username'	=> C('OPEN_SYS_USER'),
			/* API系统级参数 End */				 

			/* API应用级输入参数 Start*/
			'spu'		  => json_encode($spu),
			'picType'	  => $picType

			/* API应用级输入参数 End*/
		);
		$result = callOpenSystem($paramArr);
		//return $result;
		
		$data = json_decode($result,true);
		//return $data;
		//var_dump($data);
		if(empty($data)){
			return array();	
		}
		return $data;
		//return $data;
    }
	
    public static function getStaffInfo($staffId){
    	//global $dbConn;	
		self::initDB();		
		$sql = "SELECT * FROM power_global_user WHERE global_user_id = '$staffId'";
		//echo $sql;
		$sql		= self::$dbConn->query($sql);
		$purchaseInfo  = self::$dbConn->fetch_array_all($sql);		
		return $purchaseInfo;
	}
	
	public static function checkOrder($id){
    	//global $dbConn;
		self::initDB();
		$sql = "SELECT * FROM om_unshipped_order WHERE recordNumber = '$id'";
		//echo $sql;
		$sql		= self::$dbConn->query($sql);
		$info  = self::$dbConn->fetch_array_all($sql);
		return $info;
	}

	//判断不同平台，分销商，平台订单号是否重复的情况
	public static function checkRecordNumber($recordNumber,$platformId,$accountId){
    	//global $dbConn;
		self::initDB();
		$sql = "SELECT * FROM om_unshipped_order WHERE recordNumber = '$recordNumber' and platformId = '$platformId' and accountId = '$accountId'";
		$sql		= self::$dbConn->query($sql);
		$info  = self::$dbConn->fetch_array_all($sql);
		if (count($info) == 0) {
			return false;
		} else {
			return true;
		}	
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
	/*function orderLog($orderid,$ss,$note,$orderType=""){
		self::initDB();	
		$where = "where id={$orderid}";
		$orderinfo = OmAvailableModel::getTNameList("om_unshipped_order","*",$where);
		$sql = array();
		$sql['operatorId'] = $_SESSION['sysUserId'];
		$sql['omOrderId'] = $orderid;
		//$sql['note'] = "编辑订单";
		$sql['note'] = $note;
		$sql['sql'] = $ss;
		$sql['createdTime'] = time();
		if(!empty($orderType) && $orderinfo[0]['orderType']!=$orderType){
			$sql['oldStatus'] = $orderinfo[0]['orderType'];
			$sql['newStatus'] = $orderType;
			$sql['note'] .= "修改订单状态";
		}
		$strmctime = date('Y_m', time());
		$sql = "INSERT INTO om_order_log_".$strmctime." set ".array2sql($sql);

		$sql		= self::$dbConn->query($sql);
		if($sql){
			return true;
		}else{
			return false;
			echo $sql;
		}
			
	}*/
	public function updateWarehouseInfo($orderid,$status,$user,$time,$weight=""){
		self::initDB();
		//$user = UserModel::getUsernameById($user);
		$data = array();
		

		
		if($status==903){
			$data['printerId'] = $user;
			$data['omOrderId'] = $orderid;
			$data['printTime'] = $time;		
			$sql = "INSERT INTO om_unshipped_order_warehouse set ".array2sql($data);
		}elseif($status==2){
			$data['weighStaffId'] = $user;
			$data['weighTime'] = $time;
			$data['actualWeight'] = $weight;
			$sql = "UPDATE om_unshipped_order_warehouse set ".array2sql($data)." where omOrderId={$orderid}";
		}elseif($status==906){
			$data['packersId'] = $user;
			$data['packingTime'] = $time;
			$sql = "UPDATE om_unshipped_order_warehouse set ".array2sql($data)." where omOrderId={$orderid}";
		}elseif($status==905){
			$data['reviewerId'] = $user;
			$data['reviewTime'] = $time;
			$sql = "UPDATE om_unshipped_order_warehouse set ".array2sql($data)." where omOrderId={$orderid}";
		}else{
			return false;
		}
		$sql		= self::$dbConn->query($sql);
		if($sql){
			return true;
		}else{
			return false;
		}
	}
	
	public static function getStaffInfoList(){
    	//global $dbConn;	
		self::initDB();		
		$sql = "SELECT global_user_id,global_user_name FROM power_global_user WHERE global_user_is_delete = 0";
		//echo $sql;
		$sql			= self::$dbConn->query($sql);
		$purchaseInfo 	= self::$dbConn->fetch_array_all($sql);		
		return $purchaseInfo;
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
	
}