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
     * 获取单个sku信息
	 *@para $sku as string
     */
	public static function getSkuinfo($sku){
        global $memc_obj;
        $skuInfo = $memc_obj->get_extral('pc_goods_'.$sku);
        return $skuInfo;
    }
	
	/*
     * 获取虚拟料号信息
	 *@para $sku as string
     */
	public static function getCombSkuinfo($sku){
        global $memc_obj;
        $skuInfo = $memc_obj->get_extral('pc_goods_combine_'.$sku);
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
     * 通过类别path获取类别名称
     */
    public static function getCateInfoByPath($path){
		$cate_name = '';
        $cateInfoList = self::getCateinfo();
		foreach($cateInfoList as $cate){
			if($path==$cate['path']){
				$cate_name = $cate['name'];
				break;
			}
		}
        return  $cate_name;
    }
	
	/*
     * 获取包材信息
     */
	 /*
	public static function getMaterInfo(){
        global $memc_obj;
        if (self::$materInfo == NULL){
            self::$materInfo = $memc_obj->get_extral('pc_packing_material');
        }
        return self::$materInfo;
    }
	*/
	/*
     * 通过包材id获取包材名称
     */
	 /*
    public static function geteMaterInfoById($MaterId){
        $materInfoList = self::getMaterInfo();
        return  $materInfoList[$MaterId];
    }
	*/
    
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
	
	/*
     *根据供应商名称获取id
     */
	public static function getPartnerByName($name){
        global $memc_obj;
        $Partner = $memc_obj->get_extral('purchase_partner_name_'.$name);
        return $Partner;
    }
	
	/*
     *根据供应商id获取名称
     */
	public static function getPartnerByID($id){
        global $memc_obj;
        $Partner = $memc_obj->get_extral('purchase_partner_'.$id);
        return $Partner;
    }
	
	/*
     *根据采购员名称获取id
     */
	public static function getPurchaserByName($name){
        global $memc_obj;
        $Purchaser = $memc_obj->get_extral('power_global_user'.$name);
        return $Purchaser;
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
	
	/*
	 * 插入打标队列记录
	 */
	public static function get($orderids){
		self::initDB();
		self::$dbConn->begin();
		//$sql = "select * from wh_order_printing_list where status = 0 and is_delete = 0 and storeId = 1 applicantId = '{$_SESSION['userId']}'";
		$sql	 =	"INSERT INTO wh_order_printing_list(orderIds,applicantId,applicantTime) VALUES({$orderids},{$_SESSION['userId']},".time().")";
		//echo $sql;
		$query	 =	self::$dbConn->query($sql);
		if($query){
			$sql = "update wh_shipping_order set orderStatus = 401 where id in ({$orderids}) and storeId = 1 ";
			if(self::$dbConn->query($sql)){
				self::$dbConn->commit();
				return true;
			}else{
				self::$dbConn->rollback();
				return false;
			}
		}else{
			self::$dbConn->rollback();
			return false;	
		}
		
	}
	
	/**
	 *获取memcache中用户信息
	 */
	public function getUsernameById($userId){
		$mem = new Memcache;
		$mem->connect("192.168.200.150",11211);	
		//$var = $mem->get('userId');
		//print_r($var);exit;
		//$userId = $argv[1];	
		
		$var = $mem->get('GlobalUser_'.$userId);		
		if(empty($var))
		{
			//$url = 'dev.power.valsun.cn/api/mem.php';//开发环境
			$url = 'http://power.valsun.cn/api/mem.php';//正式环境
			$urlPost = 'userId='.$userId;	
			$curl = curl_init();
			curl_setopt($curl,CURLOPT_URL,$url);//设置你要抓取的URL
			curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);//设置CURL参数，要求结果保存到字符串还是输出到屏幕上
			curl_setopt($curl,CURLOPT_POST,1);//设置为POST提交
			curl_setopt($curl,CURLOPT_POSTFIELDS,$urlPost);//提交的参数
			$data=curl_exec($curl);//运行CURL，请求网页
			curl_close($curl);
		}
		$var = $mem->get('GlobalUser_'.$userId);
		$var = json_decode($var,TRUE);
		$mem->close();
		return $var[0];
	}
	
	
	/*
     *根据英文国家获取中文国家名
     */
	public static function getCountryNameCn($countryEn){
        global $memc_obj;
		$paramArr = array(
				'method' 	=> 'trans.country.info.get',  //API名称
				'type'   	=> 'CN',  
				'country'   => $countryEn, 
		);
        $data = UserCacheModel::callOpenSystem($paramArr);
		return $data['data']['country'];
    }
	
	/*
     *销售账号列表
     */
	public static function getSalesaccountList(){
		//global $memc_obj;
		$cacheName = md5("wh_system_accountList");
        
        $salesaccountlist   =   WhBaseModel::cache($cacheName);
        if(!$salesaccountlist){
            $paramArr   =   array(
                				'method' 	=> 'om.omAccount',  //销售账号
            		      );
    		$salesaccountlist =   UserCacheModel::callOpenSystem($paramArr);
            if(isset($salesaccountlist['errCode']) && $salesaccountlist['errCode'] == 0){
                $salesaccountlist    =   json_decode($salesaccountlist['data'], true);
                WhBaseModel::cache($cacheName, $salesaccountlist, 86400);   
            }else{
                $salesaccountlist    =   '';
            }
        }
		return $salesaccountlist;
	}
	
	/*
     * 根据销售id获取账号
     */
    public static function getAccountNameById($id){
		global $memc_obj;
		$id = intval($id);
		if(empty($id)){
			return '';
		}
		
		$cacheName = md5("wh_system_account".$id);
		$list = $memc_obj->get_extral($cacheName);
		if(!empty($list)){
			return $list;
		}
        $salesaccountList = self::getSalesaccountList();
		if($salesaccountList){
			foreach($salesaccountList as $info){
				if($info['id']==$id){
					$list = array(
						'account' => $info['account'],
						'appname' => $info['appname'],
					);
					break;
				}
			}
			$memc_obj->set_extral($cacheName,$list);
			return  $list;
		}else{
			return '';
		} 
    }
	
	/*
     *ebay销售账号列表
     */
	public static function getEbayAccountList(){
		/*
		global $memc_obj;
		$cacheName = md5("wh_system_ebayAccountList");
		$list = $memc_obj->get_extral($cacheName);
		if(!empty($list)){
			return $list;
		}*/
        $paramArr['method'] = 'order.system.getAccountListEbay';  //API名称
		$salesaccount_info  = UserCacheModel::callOpenSystem($paramArr);
		$salesaccountlist   = json_decode($salesaccount_info['data'],TRUE);      //销售账号

		//$memc_obj->set_extral($cacheName,$salesaccountlist);
		return $salesaccountlist;
	}
	
	/*
     *根据销售id数组获取销售名字和简称
     */
	public static function getAccountInfo($accountIdArr){
        $salesaccountList = self::getSalesaccountList();
		$acc_info = array();
		if(empty($accountIdArr)){
			return $acc_info;
		}
		foreach($accountIdArr as $accountId){
			foreach($salesaccountList as $list){
				if($list['id']==$accountId){
					$acc_info[$list['id']] = array(
						'account' => $list['account'],
						'appname' => $list['appname'],
					);
					break;
				}
			}
		}
		return $acc_info;
	}
	
	/*
     *根据sku检测采购订单
     */
	public static function purchaseSkuIsExist($sku){
        $paramArr['method'] = 'purchase.checkPurchaseSkuIsExist';  //API名称
		$paramArr['sku'] 	= $sku;
		$purchase_info  	= UserCacheModel::callOpenSystem($paramArr);
		$purchaselist   	= json_decode($purchase_info['data'],TRUE); 
		return $purchaselist;
	}
	
	/*
     *更新采购订单
     */
	public static function updateSkuCountInStock($orderId,$sku_id,$stockqty,$status){
        $paramArr['method'] = 'purchase.updateSkuCountInStock';  //API名称
		if(!empty($orderId)){
			$paramArr['orderId'] = $orderId;
		}
		if(!empty($sku_id)){
			$paramArr['sku_id'] = $sku_id;
		}
		if(!empty($stockqty)){
			$paramArr['stockqty'] = $stockqty;
		}
		if(!empty($status)){
			$paramArr['status'] = $status;
		}
		$purchase_info  	= UserCacheModel::callOpenSystem($paramArr);
		$purchaselist   	= json_decode($purchase_info['data'],TRUE); 
		return $purchaselist;
	}
	
	/*
     *根据采购订单id获取详细
     */
	public static function getOrderDetailsById($id){
        $paramArr['method']  = 'purchase.getOrderDetailsById';  //API名称
		$paramArr['orderId'] = $id;
		$purchase_info  	 = UserCacheModel::callOpenSystem($paramArr);
		$purchaselist   	 = json_decode($purchase_info['data'],TRUE); 
		return $purchaselist;
	}
	
	/*
     *操作采购订单
     */
	public static function processPurchaseOrder($sku,$num){
        $paramArr['method'] = 'purchase.processPurchaseOrder';  //API名称
		$paramArr['sku'] 	= $sku;
		$paramArr['num'] 	= $num;
		$purchase_info  	= UserCacheModel::callOpenSystem($paramArr);
		//$purchaselist   	= json_decode($purchase_info['data'],TRUE); 
		return $purchase_info['num'];
	}
	
	/*
     * 获取全部的运输方式列表信息 通过api到运输方式管理系统获取
     */
    public static function getShipingTypeList($type=2){
		//global $memc_obj;
		$cacheName = "wh_system_shiping_type".$type;
        $transportlist   =   WhBaseModel::cache($cacheName);
        if(!$transportlist){
            $paramArr   =   array(
                				'method' 	=> 'trans.carrier.info.get',  //API名称
                				'type'   	=> $type,  //（0非快递，1快递，2全部）
            		      );
    		$ShipingTypeList = UserCacheModel::callOpenSystem($paramArr);
            if(isset($ShipingTypeList['errCode']) && $ShipingTypeList['errCode'] == 0){
                $ShipingTypeList    =   $ShipingTypeList['data'];
                $transportlist = array();
                //变换下标为渠道id
                foreach($ShipingTypeList as $val){
                	$transportlist[$val['id']] = $val;
                }
                WhBaseModel::cache($cacheName, $transportlist, 86400);   
            }else{
                $transportlist    =   '';
            }
        }
		return $transportlist;
    }
    
    /**
     * 获取运输渠道id
     * @return array $carrierChannellist
     * @author czq
     */
    public static function getCarrierChannelList(){
    	$cacheName = "wh_carrier_channel";
    	$carrierChannellist   =   WhBaseModel::cache($cacheName);
    	if(!$carrierChannellist){
    		$paramArr   =   array(
    				'method' 	=> 'trans.carrier.channel.info.get',  //API名称
    		);
    		$carrierChannellist = UserCacheModel::callOpenSystem($paramArr);
    		if(isset($carrierChannellist['errCode']) && $carrierChannellist['errCode'] == 0){
    			$carrierChannellist    =   $carrierChannellist['data'];
    			WhBaseModel::cache($cacheName, $carrierChannellist, 86400);
    		}else{
    			$carrierChannellist    =   '';
    		}
    	}
    	return $carrierChannellist;
    }
    
    /**
     * 获取国家列表
     * @return array $countrylist
     * @author czq
     */
    public static function getCountryList(){
	    //获取国家列表
	    $countrylist = WhBaseModel::cache('country_list');
	    if(!$countrylist){
			$paramArr = array(
				'method'	=> 'trans.country.info.get',
				'type'		=> 'ALL'	//CN中文，EN英文，ALL全部
			);
		   	$result = UserCacheModel::callOpenSystem($paramArr);
		   	$countrylist = $result['data'];
		   	if(!empty($countrylist)){
		   		WhBaseModel::cache('country_list', $countrylist, 864000);
		   	}else{
		   		$countrylist = '';
		   	}
		   	
		}    	
		return $countrylist;
    }
    
    /*
     * 运输方式id到运输方式名称映射函数 $id unsigned int 运输方式id
     */
    public static function getShipingNameById($id){
		global $memc_obj;
		$id = intval($id);
		if(empty($id)){
			return '';
		}
		
		$cacheName = md5("wh_system_shipingName".$id);
		
		$list = $memc_obj->get_extral($cacheName);
		if(!empty($list)){
			return $list;
		}
		
        $shippingInfoList = self::getShipingTypeList();
		if($shippingInfoList){
			foreach($shippingInfoList as $info){
				if($info['id']==$id){
					$list = $info['carrierNameCn'];
					break;
				}
			}
			$memc_obj->set_extral($cacheName,$list);
			return  $list;
		}else{
			return '';
		}
        
    }
	
	 /*
     * 运输方式id到运输方式名称简称
     */
    public static function getShipingAbbrNameById($id){
		global $memc_obj;
		$id = intval($id);
		if(empty($id)){
			return '';
		}
		
		$cacheName = md5("wh_system_shipingAbbrName".$id);
		
		$list = $memc_obj->get_extral($cacheName);
		if(!empty($list)){
			return $list;
		}
		
        $shippingInfoList = self::getShipingTypeList();
		if($shippingInfoList){
			foreach($shippingInfoList as $info){
				if($info['id']==$id){
					$list = $info['carrierAli'];
					break;
				}
			}
			$memc_obj->set_extral($cacheName,$list);
			return  $list;
		}else{
			return '';
		}
        
    }
	
	/*
     * 运输方式id到运输方式名称映射函数 $id unsigned int 运输方式id
     */
    public static function getShipingTypeListKeyId(){
		$shipinfo = array();
        $shippingInfoList = self::getShipingTypeList();
		if($shippingInfoList){
			foreach($shippingInfoList as $info){
				$shipinfo[$info['id']] = $info['carrierNameCn'];
			}
		}
        return   $shipinfo;
    }
	
	/*
     * 运输方式id、国家获取分区
     */
    public static function getChannelNameByIds($id,$countryName=''){
		$paramArr = array(
				'method'	  => 'trans.partition.info.get',  //API名称
				'carrierId'   => $id,  
				//'countryName' => $countryName, 
		);
		if(!empty($countryName)){
			$paramArr['countryName']=$countryName;
		}
		$ShipingTypeList = UserCacheModel::callOpenSystem($paramArr);
		if($ShipingTypeList['data']){
			if($id=='all'){
				return $ShipingTypeList['data'];
			}else{
				return $ShipingTypeList['data'][0]['partitionName'];
			}
		}else{
			return '';
		}
    }
	
	/*
     * 运输方式id、国家获取分区id
     */
    public static function getChannelIdByIds($id,$countryName){
		$paramArr = array(
				'method'	  => 'trans.partition.info.get',  //API名称
				'carrierId'   => $id,  
				'countryName' => $countryName, 
		);
		$ShipingTypeList = UserCacheModel::callOpenSystem($paramArr);
		if($ShipingTypeList['data']){
			return $ShipingTypeList['data'][0]['id'];
		}else{
			return '';
		}
    }
	
	/*
     * 运输方式id获取渠道
     */
    public static function getCarrierChannelByIds($id){
    	$cacheName = "transprotId-$id";
    	$channelList = WhBaseModel::cache($cacheName);
    	if(!$channelList){
			$paramArr = array(
					'method'	  => 'trans.carrier.channel.info.get',  //API名称
					'carrierId'   => $id,  
			);
			$channelList = UserCacheModel::callOpenSystem($paramArr);
			if(!empty($channelList['data'])){
				$channelList =  $channelList['data'];
				WhBaseModel::cache($cacheName,$channelList,86400);
			}else{
				$channelList =  '';
			}
    	}
		return $channelList;
    }
	
	/*
     * 运输方式id,渠道id获取渠道
     */
     public static function getChannelByIds($carrierId,$channelId){
		$paramArr = array(
				'method'	  => 'trans.carrier.channel.info.get',  //API名称
				'carrierId'   => $carrierId,  
				'channelId'   => $channelId, 
		);
		$ShipingTypeList = UserCacheModel::callOpenSystem($paramArr);
		if($ShipingTypeList['data']){
			return $ShipingTypeList['data'];
		}else{
			return '';
		}
		
    }
	
	/*
     * 获取包材信息
     */
	public static function getMaterInfoById($id){
		global $memc_obj;
		$list = '';
		$id = intval($id);
		if($id==0){
			return '';
		}
		
		$cacheName = md5("wh_system_mater".$id);
		$list = $memc_obj->get_extral($cacheName);
		if(!empty($list)){
			return $list;
		}
		$paramArr = array(
				'method' 	=> 'pc.getPmInfoById',  //API名称
				'id'        => $id
		);
		$MaterInfoTypeList = UserCacheModel::callOpenSystem($paramArr);	
		if(!empty($MaterInfoTypeList['data'])){
			$list			   = $MaterInfoTypeList['data']['pmName'];
			$memc_obj->set_extral($cacheName,$list);
		}
		return  $list;
    }
    
    /**
     * CommonModel::getMaterInfoAll()
     * 获取所有包材信息
     * @author Gary
     * @return void
     */
    public static function getMaterInfoAll(){
        $cacheName  =   md5('wh_system_mater');
        $materInfo  =   WhBaseModel::cache($cacheName);
        if($materInfo === FALSE){
            $paramArr['method'] =   'pc.getPmInfoAll';
            $materInfo          =   UserCacheModel::callOpenSystem($paramArr);
            if($materInfo['errCode'] == 200){
                $materInfo  =   $materInfo['data'];
                WhBaseModel::cache($cacheName, $materInfo);
            }else{
                $materInfo  =   '';
            }
        }
        return $materInfo;
    }
	
	/*
     * 获取平台列表
     */
    public static function getPlatformInfo($id=0){
		/*global $memc_obj;
		$id = intval($id);
		$cacheName = md5("wh_system_platform".$id);
		$list = $memc_obj->get_extral($cacheName);		
		if(!empty($list)){
			return $list;
		}
		if($id==0){
			$paramArr = array(
					'method' 	=> 'order.getPlatformList',  //API名称
			);
			$ShipingTypeList = UserCacheModel::callOpenSystem($paramArr);
			$memc_obj->set_extral($cacheName, json_decode($ShipingTypeList['data'],TRUE));
			return json_decode($ShipingTypeList['data'],TRUE);		
		}else{
			$paramArr = array(
					'method' 	=> 'order.getPlatformList',  //API名称
					'id' 		=> $id,  
			);
			$ShipingTypeList = UserCacheModel::callOpenSystem($paramArr);
			$info = json_decode($ShipingTypeList['data'],TRUE);
			$memc_obj->set_extral($cacheName, $info[0]['platform']);
			return $info[0]['platform'];
			
		}*/
        $id         =   intval($id);
        //$id         =   1;
        $cacheName  =   md5('wh_system_platform'.$id);  //缓存键名
        $Platforms  =   WhBaseModel::cache($cacheName); //判断是否有缓存
        if($Platforms === FALSE){
            $paramArr['method'] =   'order.getPlatformList';
            if($id){
                $paramArr['id'] =   $id; //获取单个平台数据
            }
            $Platforms  =   UserCacheModel::callOpenSystem($paramArr);
            //print_r($Platforms);exit;
            if(isset($Platforms['errCode']) && $Platforms['errcode'] == 0){
                $Platforms  =   json_decode($Platforms['data'], true);
                if($id){
                    $Platforms  =   $Platforms[0]['platform'];
                }
                WhBaseModel::cache($cacheName, $Platforms); //存进缓存中
            }else{
                $Platforms  =   '';
            }
        }
        return $Platforms;
        
    }
	
	/*
     * 获取平台列表
     */
    public static function getPlatformName($id){
		global $memc_obj;
		$cacheName = md5("om_system_platform".$id);
		$list = $memc_obj->get_extral($cacheName);
		if($list){
			return $list[0]['platform'];
		}else{
			$paramArr = array(
					'method' 	=> 'order.getPlatformList',  //API名称
			);
			$ShipingTypeList = UserCacheModel::callOpenSystem($paramArr);
			return json_decode($ShipingTypeList['data'],TRUE);
		}
    }
	
	/*
     * 根据账号id获取EUB回邮地址
     */
    public static function getEubAccounts($id){
		/*
		global $memc_obj;
		$id = intval($id);
		if(empty($id)){
			return array();
		}
		$cacheName = md5("om_system_EubAccount".$id);
		$list = $memc_obj->get_extral($cacheName);
		if($list){
			return $list;
		}else{*/
			$list = array();
			$paramArr = array(
					'method' 	=> 'order.system.eubAccounts.get',  //API名称
			);
			$EubLists = UserCacheModel::callOpenSystem($paramArr);
			$infos = json_decode($EubLists['data'],TRUE);
			foreach($infos as $info){
				if($info['accountId']==$id){
					$list = array(
						'pname' 	=> $info['pname'],
						'dstreet' 	=> $info['dstreet'],
						'dcity' 	=> $info['dcity'],
						'dprovince' => $info['dprovince'],
						'dzip' 		=> $info['dzip'],
						'dtel' 		=> $info['dtel'],
						'dcountry' 	=> $info['dcountry'],
					);
					break;
				}
			}
			//$memc_obj->set_extral($cacheName,$list);
			return  $list;
		//}
    }

	/*
     * 出入库单
	 *	$paraArr 为数组
     */
	 public static function addIoRecores($paraArr){
		self::initDB();
		$ordersn = isset ($paraArr['ordersn'])?$paraArr['ordersn']:''; //发货单号或者是单据的ordersn
		$sku = $paraArr['sku']; //sku
		$amount = $paraArr['amount']; //数量
		$positionId = isset ($paraArr['positionId']) ? $paraArr['positionId'] : 0; //仓位ID
		$purchaseId = $paraArr['purchaseId']; //采购员id
		$ioType = $paraArr['ioType']; //出/入库，1为出库，2为入库
		$ioTypeId = $paraArr['ioTypeId']; //出入库类型id，即出入库类型表中对应的id
		$userId = $paraArr['userId']; //添加人id
		$reason = isset ($paraArr['reason']) ? $paraArr['reason'] : ''; //原因
		$storeId = isset ($paraArr['storeId']) ? intval($paraArr['storeId']) : 1;//仓库，默认为1
		
		$createdTime = time();
		$tName = 'wh_iorecords';
		$set = "SET ordersn='$ordersn',sku='$sku',amount='$amount',positionId='$positionId',purchaseId='$purchaseId',ioType='$ioType',ioTypeId='$ioTypeId',userId='$userId',reason='$reason',createdTime='$createdTime',storeId='$storeId'";
		$sql = "INSERT INTO $tName $set";
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$insertId = self :: $dbConn->insert_id($query);
			return $insertId; //成功， 返回插入的id
		} else {
			self :: $errCode = "002";
			self :: $errMsg = "添加失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
    }
	
	/**
	 * CommonModel::getSkuImg()   已不用
	 * 获取sku图片
	 * @param string $spu 主料号
	 * @param string $picType 图片类型
	 * @param string $sku 待用
	 * @return string
	 */
	public static function getSkuImg($spu, $sku, $picType){
		$paramArr= array(
			/* API系统级输入参数 Start */
			'method'	=> 'datacenter.picture.getAllSizePic',  //API名称
			/* API系统级参数 End */
			/* API应用级输入参数 Start*/
			'spu'		=> $spu,  //主料号
			'picType'	=> $picType, //站点
			/* API应用级输入参数 End*/
		);
		$data 	= UserCacheModel::callOpenSystem($paramArr);
		//$data 	= json_decode($data, true);
		$imgUrl = isset($data['data']['artwork']) ? $data['data']['artwork'][$spu][0] : '';
        return $imgUrl;
	}
	
	/*
     * 邮局退回写入qc系统
     */
    public static function qcOrderBackDetect($data){
		$paramArr = array(
				'method'   => 'qc.wh.orderBackDetect',  //API名称
				'qc_arr'   => $data,  
		);
		$info = UserCacheModel::callOpenSystem($paramArr);
		if($info['data']){
			return true;
		}else{
			return false;
		}
		
    }
	
	/**
	 * CommonModel::getSkuImg()    现使用
	 * 获取sku图片
	 */
	public static function getImgBySku($sku, $size){
		$sku_arr = array($sku);
		$jsku = json_encode($sku_arr);
		$paramArr= array(
			'method' => 'datacenter.picture.getPicBySkuArr',  //API名称
			'sku'	 => $jsku,
			'size'	 => $size,
		);
		$data = UserCacheModel::callOpenSystem($paramArr);
	  //$data = json_decode($data, true);
        return isset($data['data'][0]['sourceUrl']) ? $data['data'][0]['sourceUrl'] : 0;
	}
	
	//获取快递备注
	public static function getExpressRemark($shipOrderId){
		self::initDB();
		$sql       = "select originOrderId from `wh_shipping_order_relation` where shipOrderId={$shipOrderId}";
		$orderInfo = self::$dbConn->fetch_first($sql);
		$orderid   = $orderInfo['originOrderId'];
		$paramArr= array(
			'method'  => 'order.system.expressRemarks.get',  //API名称
			'orderid' => $orderid,
		);
		$data = UserCacheModel::callOpenSystem($paramArr);
        return $data['data'];
	}
	
	//验证文员录入料号、数量是否存在
	//type: 1:为验证，2:为删除 3:为重置为推送数
	public static function checkOnWaySkuNum($sku,$num='',$type=''){
		$paramArr= array(
			'method'  => 'purchase.processPurchaseOrder',  //API名称
			'sku' 	  => $sku,
			'amount'  => $num,
			'type'    => $type,
		);

		$data = UserCacheModel::callOpenSystem($paramArr);
        return intval($data['num']);
	}
	
public static function  getczPicture($ordersn,$starttime,$endtime,$scanuser,$limit){
		$paramArr= array(
				'method'  		=> 	'wh.getczpicture',  //API名称
				'starttime' 	=> 	 $starttime,
				'endtime' 	    => 	 $endtime,
				'scanuser'      =>   $scanuser,
				'ordersn'		=>	$ordersn,
				'limit'			=>  $limit
		);
		$data = UserCacheModel::callOpenSystem2($paramArr);
		return $data;
	}
	
	public static function  getfhPicture($ordersn,$starttime,$endtime,$scanuser,$limit){
		$paramArr= array(
				'method'  		=> 	'wh.getfhpicture',  //API名称
				'starttime' 	=> 	 $starttime,
				'endtime' 	    => 	 $endtime,
				'scanuser'      =>   $scanuser,
				'ordersn'		=>	$ordersn,
				'limit'			=>	$limit
		);
		$data = UserCacheModel::callOpenSystem2($paramArr);
		return $data;
	}
	//异常录入推送采购系统
	public static function pushAbnormalPrint($idArr){
		self::initDB();
		if(!is_array($idArr)){
			return false;
		}
		$o_count = count($idArr);
		foreach($idArr as $id){
			$ent_list = packageCheckModel::selectList("where id={$id} and entryStatus=2");
			if(!empty($ent_list)){
				return false;
			}
		}
		$info = array();
		foreach($idArr as $id){
			$list = packageCheckModel::selectList("where id={$id}");
            $waitShelfNum   =   packageCheckModel::getSkuWaitShelfNum($list[0]['sku']); //等待上架数量
			$info[] = array(
				'sku'   	 => $list[0]['sku'],
				'amount' 	 => $list[0]['num'] + $waitShelfNum,
				'tallymanId' => $list[0]['tallyUserId'],
				'orderid'    => $list[0]['id'],
			);
		}

		$paramArr= array(
			'protocol' => 'param2',
			'method'   => 'purchase.addSkuReach',  //API名称
			'orderArr' => json_encode($info),
		);
        
		//$data = UserCacheModel::callOpenSystem2($paramArr,"post");
        //$url    =   'http://test.purchase.valsun.cn/json.php?mod=sku&act=addSkuReach';
        $url    =   'http://purchase.valsun.cn/json.php?mod=sku&act=addSkuReach';	
		$curl = curl_init();
		curl_setopt($curl,CURLOPT_URL,$url);//设置你要抓取的URL
		curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);//设置CURL参数，要求结果保存到字符串还是输出到屏幕上
		curl_setopt($curl,CURLOPT_POST,1);//设置为POST提交
		curl_setopt($curl,CURLOPT_POSTFIELDS,$paramArr);//提交的参数
		$data=curl_exec($curl);//运行CURL，请求网页
		curl_close($curl);
        
        /** 添加推送日志**/
        $log_file   =   'UnNormalPackageRecord/'.date('Ymd').'.txt';   //日志文件路径
        $date       =   date('Y-m-d H:i:s');
        $log_info   =   sprintf("推送信息：%s, 时间：%s,错误信息:%s \r\n", $paramArr['orderArr'], $date, is_array($data) ? json_encode($data) : $data);
        write_log($log_file, $log_info);
        
        $data   =   json_decode($data, true);
        
        if($data['errCode'] == 0 && isset($data['errCode'])){
			return true;
		}else{
			return false;
		}
	}
	
	/*
     *上架操作
     */
	public static function endPurchaseOrder($sku, $num, $time, $key){
        $paramArr['method'] = 'purchase.addStock';  //API名称
        //$paramArr['method'] = 'purchase.addStock.test';  //测试API名称        
		$paramArr['sku'] 	= $sku;
		$paramArr['amount'] = $num;
        $paramArr['intime'] = $time;
        $paramArr['key']    = $key;
		$purchase_info  	= UserCacheModel::callOpenSystem($paramArr);
        //$purchase_info  	= UserCacheModel::callOpenSystem2($paramArr);
		//$purchaselist   	= json_decode($purchase_info['data'],TRUE); 
		return $purchase_info;
	}
	
	/*
     *更新旧erp库存
	 * userCnName 中文名
     */
	public static function updateOnhand($sku,$num,$userCnName,$goods_location,$goods_store='', $time, $key){
        $paramArr['method'] = 'wh.updateOnhand';  //API名称
        //$paramArr['method'] = 'test.wh.updateOnhand';  //测试API名称
		$paramArr['sku'] 	= $sku;
		$paramArr['num'] 	= $num;
		$paramArr['goods_location'] 	= $goods_location;
		if($goods_store){
			$paramArr['goods_store'] = $goods_store;
		}
		$paramArr['truename'] 	= $userCnName;
        $paramArr['intime'] 	= $time;
        $paramArr['key'] 	= $key;
		$purchase_info  	= UserCacheModel::callOpenSystem2($paramArr);
		return $purchase_info;
	}
	
	/*
     *根据sku获取采购id
     */
	public static function getPurchaseId($sku){
		$skuInfo = OmAvailableModel::getTNameList("pc_goods","purchaseId","where sku='$sku'");
		return $skuInfo[0]['purchaseId'];
	}
	
	/*
     *旧系统修改sku仓位，更新新系统仓位地图
	 * add by Herman.Xi @20140219
     */
	public static function updateNewPostion($sku,$location){
		self::initDB();
		$goodsinfos = OmAvailableModel::getTNameList("pc_goods","sku,id","where sku='{$sku}'");
		$pId = $goodsinfos[0]['id'];
		//$locationinfos = OmAvailableModel::getTNameList("ebay_goods","goods_location","where goods_sn='{$sku}'");
		//$location      = $locationinfos[0]['goods_location'];
		if($location){
			$sql1   = "select * from `wh_position_distribution` where pName = '{$location}'";
			$query = self::$dbConn->query($sql1);
			$wh_position_distribution =self::$dbConn->fetch_array($query);
			if($wh_position_distribution){
				$positionId		= $wh_position_distribution['id'];
				//$postionId = OmAvailableModel::insertRow2("wh_position_distribution","set pName='$location',x_alixs=0,y_alixs=0,z_alixs=0,floor=0,is_enable=0,type=1,storeId=2");
				if($positionId){
					$positioninfos = OmAvailableModel::getTNameList("wh_product_position_relation","id","where pId='$pId'");	
					if(!empty($positioninfos)){
						if($data = OmAvailableModel::updateTNameRow("wh_product_position_relation","set positionId='$positionId', storeId={$wh_position_distribution['storeId']}", " where pId='$pId' ")){
							//echo "update <".$sku."> ===(".$positionId.") success\n";
							return true;
						}else{
							return false;
						}
					}else{
						$infos = OmAvailableModel::getTNameList("wh_sku_location","sku,actualStock","where sku='{$sku}'");
						$num = $info['actualStock'];
						$data = OmAvailableModel::insertRow("wh_product_position_relation","set pId='$pId',positionId='$positionId',nums='$num', storeId={$wh_position_distribution['storeId']}");
					}
				}else{
					return false;	
				}
			}else{
				return false;	
			}
		}	
	}
		
	/*
     *更新qc数量
     */
	public static function adjustPrintNum($batchNum,$num){
        $paramArr['method']     = 'qc.adjustPrintNum';  //API名称
		$paramArr['printBatch'] = $batchNum;
		$paramArr['num'] 	    = $num;
		$info  					= UserCacheModel::callOpenSystem($paramArr);
		return $info;
	}
	
	/*
     *获取采购人员
     */
	public static function getPurchaseList(){
        $usermodel 	  = UserModel::getInstance();
		$PurchaseList = $usermodel->getGlobalUserLists('global_user_id,global_user_name'," where a.global_user_job in(28,46,44,65,88,97,98,100,113,115,119,121,125,173,180,184,185,264,267,269)",'','');
		return $PurchaseList;
	}
	
	/*
     *根据工号获取用户信息
     */
	public static function getLoginUserInfo($jobNo){
        $usermodel = UserModel::getInstance();
		$userInfo  = $usermodel->getGlobalUserLists('global_user_id,global_user_name,global_user_login_name'," where a.global_user_job_no='{$jobNo}'",'','');
		return $userInfo;
	}
	
	/*
     *回调订单系统推送信息
     */
	public static function callbackOrderSys($orderId,$note){
        $paramArr['method']     = 'om.applicationException';  //API名称
		$paramArr['omOrderId']  = $orderId;
		$paramArr['content'] 	= $note;
		$info  					= UserCacheModel::callOpenSystemForRq($paramArr);
		return $info;
	}
	
	/*
     *获取订单系统订单的详细(sku及对应数量)
     */
	public static function getRealskulist($originOrderId){
        $paramArr['method']     = 'om.getRealskulist';  //API名称
		$paramArr['omOrderId']  = $originOrderId;
		$paramArr['type']  		= 1;
		$info  					= UserCacheModel::callOpenSystem($paramArr);
		return $info['data'];
	}
	
	/*
     *料号称重
     */
	public static function updateSkuWeight($sku,$skuweight,$userId){
        $paramArr['method']     = 'pc.setSkuWeightInWh';  //API名称
		$paramArr['sku']        = $sku;
		$paramArr['skuweight']  = $skuweight;
		$paramArr['userId']     = $userId;
		$info  					= UserCacheModel::callOpenSystem($paramArr);
		return $info['errCode'];
	}
    
    /**
     * CommonModel::updateOrderWeight()
     * 料号重新称重后更新订单重量
     * @author Gary
     * @param mixed $ebay_id
     * @return
     */
    public static function updateOrderWeight($ebay_id, $user){
        $paramArr['method']     =   'erp.recalcorderweight';
        $paramArr['ebay_id']    =   "$ebay_id";
        $paramArr['user']       =   $user;
        $info  					= UserCacheModel::callOpenSystem2($paramArr);
		return $info;
    }
	
	/*
     *获取平台及对应账号
     */
	public static function getPlatformAccountList(){
        $paramArr['method']     = 'order.getPlatformAccountListAPI';  //API名称
		$info  					= UserCacheModel::callOpenSystem($paramArr);
		return $info['data'];
	}
	
	/*
     *更新旧erp库存(点货调整)
	 * userCnName 中文名
     */
	public static function adjustOut($sku,$num,$userCnName){
        $paramArr['method']   = 'wh.adjustOut';  //API名称
		$paramArr['sku'] 	  = $sku;
		$paramArr['num'] 	  = $num;
		$paramArr['truename'] = $userCnName;
		$purchase_info  	  = UserCacheModel::callOpenSystem1($paramArr);
		return $purchase_info;
	}
	
	/*
     *获取旧erp料号库存
     */
	public static function getGoodsCount($sku){
        $paramArr['method']   = 'qccenter.get.erp.goodscount';  //API名称
		$paramArr['goods_sn'] = $sku;
		$info  	              = UserCacheModel::callOpenSystem2($paramArr);
		if($info){
            //根据仓位判断A仓库存还是B仓库存
            $field  =   preg_match("/WH|HW/U", $info['data']['goods_location']) ? 'second_count' : 'goods_count';
			return $info['data'][$field];
		}else{
			return false;
		}
	}
    
    /** 获取老ERP料号仓位库存信息 add BY Gary**/
    public static function getErpSkuInfo($sku){
        $paramArr['method']   = 'qccenter.get.erp.goodscount';  //API名称
		$paramArr['goods_sn'] = $sku;
		$info  	              = UserCacheModel::callOpenSystem2($paramArr);
		if($info){
			return $info;
		}else{
			return false;
		}
    }   
    /** end**/
	
	/*
     *更新旧erp库存(盘点调整)
	 * userCnName 中文名
     */
	public static function adjustInventory($sku,$num,$userCnName){
        $paramArr['method']   = 'wh.inventory';  //API名称
        //$paramArr['method']   = 'test.wh.inventory';  //测试
		$paramArr['sku'] 	  = $sku;
		$paramArr['num'] 	  = $num;
		$paramArr['truename'] = $userCnName;
		$info  	              = UserCacheModel::callOpenSystem2($paramArr);
		return $info;
	}
    
	/**
	 * CommonModel::getSkuIoRecord()
	 * 获取旧ERP系统入库记录
     * @param Gary
	 * @param mixed $sku
	 * @param mixed $num
	 * @param mixed $name
	 * @return
	 */
	public static function getSkuIoRecord($sku, $num, $name, $time){
        $paramArr['method']   = 'erp.getSkuIoRecord';  //API名称
		$paramArr['sku'] 	  = $sku;
		$paramArr['num'] 	  = $num;
		$paramArr['name']     = $name;
        $paramArr['time']     = $time;
		$info  	              = UserCacheModel::callOpenSystem2($paramArr);
		return $info;
	}
    
    /**
	 * TransOpenApiModel::sendMessage()
	 * 发送信息
     * @author Gary
	 * @param string $type ems手机短信，email 邮件
	 * @param string $from 发件人
	 * @param string $to 收件人
	 * @param string $content 内容
	 * @param string $title 标题
	 * @return  json string
	 */
	public static function sendMessage($type, $from, $to, $content, $title=''){
		$paramArr = array(
			/* API系统级输入参数 Start */
				'method'    => 'notice.send.message',  //API名称
				'format'    => 'json',  //返回格式
				'v'         => '1.0',   //API版本号
	            'username'  => C('OPEN_SYS_USER'),
			/* API系统级参数 End */				 
			/* API应用级输入参数 Start*/
				'type'      => $type,
				'from'      => $from,
				'to'        => $to,
				'content'	=> $content,
				'title'     => urlencode($title),
				'sysName'	=> urlencode(C('AUTH_SYSNAME')),
			/* API应用级输入参数 End*/
		);
		$messageInfo	    = UserCacheModel::callOpenSystem($paramArr);
		unset($paramArr);
		return $messageInfo;
	}
    
    /**
     * CommonModel::updateIoRecord()
     * 将出入库记录同步到老ERP
     * @return
     */
    public static function updateIoRecord($sku, $num, $type, $reason, $truename, $goods_location){
		$paramArr = array(
        				//'method'    => 'test.wh.iorecord',  //测试API名称
                        'method'    => 'wh.iorecord',  //正式API名称
                        'sku'       => $sku,			 
        				'type'      => $type,
        				'num'       => $num,
        				'reason'    => $reason,
        				'truename'	=> $truename,
                        'goods_location'=> $goods_location                        
                	 );
		$res	  = UserCacheModel::callOpenSystem2($paramArr);
		unset($paramArr);
		return $res;
	}
    
    /**
     * CommonModel::updateOrderStatus()
     * 更新老ERP订单状态
     * @return
     */
    public static function updateOrderStatus($ids, $status){
		$paramArr = array(
        				'method'    => 'test.wh.updateOrderStatus',  //API名称
                        'ids'       => $ids,			 
        				'status'    => $status
                	 );
		$res      = UserCacheModel::callOpenSystem2($paramArr);
		unset($paramArr);
		return $res;
	}
    
    /** 清空老ERP仓位信息**/
    public static function clearSkuLocation($sku){
        $paramArr = array(
        				'method'    => 'wh.clearSkuLocation',  //API名称
                        //'method'    => 'test.wh.clearSkuLocation',
                        'sku'       => $sku
                	 );
		$res      = UserCacheModel::callOpenSystem2($paramArr);
		unset($paramArr);
		return $res;
    }
    
    /** 获取老ERP B仓订单信息**/
    public static function getErpOrderInfoB($orderId, $user){
        $paramArr = array(
        				'method'    => 'wh.getErpOrderInfoB',  //API名称
                        //'method'    => 'test.wh.getErpOrderInfoB',
                        'orderId'   => $orderId,
                        'user'      => $user
                	 );
		$res      = UserCacheModel::callOpenSystem2($paramArr);
		unset($paramArr);
		return $res;
    }
    
    /**
     * CommonModel::updateOrderScanRecord()
     * 将B仓订单配货记录同步到老ERP
     * @return
     */
    public static function updateOrderScanRecord($orderId, $sku, $amount, $total_nums, $is_scan, $user){
		$paramArr = array(
        				//'method'    => 'test.wh.update_order_scan_record',  //测试API名称
                        'method'    => 'wh.update_order_scan_record',  //正式API名称
                        'orderId'   => $orderId,			 
        				'sku'       => $sku,
        				'amount'    => $amount,
        				'total_nums'=> $total_nums,
        				'is_scan'	=> (string)$is_scan,
                        'user'      => $user
                	 );
		$res	  = UserCacheModel::callOpenSystem2($paramArr);
		unset($paramArr);
		return $res;
	}
    
    /**
     * CommonModel::getSkuInevntory()
     * 获取料号盘点的库存天数每日均量，库存天数虚拟库存等信息
     * @param string $sku
     * @return
     */
    public static function getSkuInevntory($sku){
        $paramArr = array(
        				//'method'    => 'test.wh.update_order_scan_record',  //测试API名称
                        'method'    => 'wh.get_sku_inventory_info',  //正式API名称			 
        				'sku'       => $sku
                	 );
		$res	  = UserCacheModel::callOpenSystem2($paramArr);
		unset($paramArr);
		return $res;
    }
    
    /**
     * CommonModel::getSalerBySku()
     * 获取料号销售负责人 
     * @param mixed $sku
     * @return void
     */
    public static function getSalerBySpu($spu){
        $paramArr = array(
        				//'method'    => 'test.wh.update_order_scan_record',  //测试API名称
                        'method'    => 'pc.getSpuSalerIdsBySpu',  //正式API名称			 
        				'spu'       => $spu
                	 );
		$res	  = UserCacheModel::callOpenSystem($paramArr);
		unset($paramArr);
		return $res;
    }
    
    /**
     * CommonModel::updateSkuLocation()
     * 新仓库移仓更新老ERP仓位 
     * @param mixed $sku
     * @param mixed $location
     * @return void
     */
    public function updateSkuLocation($sku, $location){
        $paramArr = array(
                        'method'    => 'wh.updateSkuLocation',  //正式API名称
                        //'method'    => 'test.wh.updateSkuLocation',  //正式API名称
        				'sku'       => $sku,
                        'location'  => $location
                	 );
		$res	  = UserCacheModel::callOpenSystem2($paramArr);
		unset($paramArr);
		return $res;
    }
	
    /**
     * 获取发货单的最优渠道id获取运输方式和对应的运费
     * @param array $data
     * @return array $res
     * @author czq
     */
    public static function getTransportByApi($data){ 
    	$paramArr = array(
    			'method'    			=> 'trans.batch.order.channel.shipfee.post',  //正式API名称
    			'orders'      			=> json_encode($data),
    	);
    	$res	= UserCacheModel::callOpenSystem($paramArr,'post');
    	unset($paramArr);
    	return $res;
    }
    
    /**
     * 申请跟踪号
     * @param array $data
     * @return array $res
     * @author czq
     */
    public static function getTracknumberByApi($data){
    	$paramArr = array(
    			'method'	=> 'trans.batch.order.tracknum.post',  //正式API名称
    			'orders'    => json_encode($data),
    	);
    	$res	  = UserCacheModel::callOpenSystem($paramArr,'post');
    	unset($paramArr);
    	return $res;
    }
    /**
     * CommonModel::get_hsInfo()
     * 获取spu海关报关信息
     * @param mixed $spus
     * @author Gary
     * @return void
     */
    public static function get_hsInfo($spus){
        $paramArr = array(
                        'method'    => 'pc.getHscodeInfoBySpuArr',  //正式API名称
        				'spuArr'    => $spus //json_encode格式化后的spu数组
                	 );
		$res	  = UserCacheModel::callOpenSystem($paramArr);
		unset($paramArr);
		return $res;
    }
        /**
     * CommonModel::get_shipDetail()
     * 异常发货单的拆分
     * @author cxy
     * @param array $data
     * @return
     */
    public static function get_shipDetail($data){
         $paramArr = array(
                        'method'    => 'order.splitOrderWithOrderDetailBatch',  //API名称
        				'splitData'    => json_encode($data) //json_encode格式化后的数组
                        //	'splitData'    =>($data) //json_encode格式化后的数组
                	 );
		$res	  = UserCacheModel::callOpenSystem2($paramArr);
		unset($paramArr);
		return $res;
 
    }
    
	/**
	 * 获取eub绑定的账号设置
	 * @param number $accountId
	 * @return array $res
	 * @author czq
	 */
    public static function get_eub_account($accountId){
    	$paramArr = array(
    			'method'    	=> 'order.getEubAccountByAccountId',  //正式API名称
    			'accountId'    	=> $accountId  //账户id
    	);
    	$res	  = UserCacheModel::callOpenSystem2($paramArr);
    	unset($paramArr);
    	return $res;
    }
    
    /**
     * 通过订单id从订单系统获取订单信息
     * @param string $orderids => '10011,10012'
     * @return array $res
     * @author czq
     */
    public static function get_orderInfoFromOrderSys($orderids){
    	$paramArr = array(
    			'method'		=> 'order.getOrderInfoListByOmOrderIds',  //正式API名称
    			'omOrderIds'    => $orderids,
    	);
    	$res	  = UserCacheModel::callOpenSystem2($paramArr,'post');
    	unset($paramArr);
    	return $res;
    }
    
    /**
     * CommonModel::updateOrderStatusFromWhAfterMQ()
     * 接收队列接收订单时，推送订单号到订单系统
     * @author Gary
     * @param int $orderId
     * @return
     */
    public static function updateOrderStatusFromWhAfterMQ($orderId){
        $paramArr   =   array(
                            'method'    =>  'order.updateOrderStatusFromWhAfterMQ',
                            'omOrderIds'=>  $orderId
                        );
        $res        =   UserCacheModel::callOpenSystem2($paramArr);
        unset($paramArr);
        return $res;
    }
}