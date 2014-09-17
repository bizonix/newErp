<?php
/**
 * 类名：TransOpenApiAct
 * 功能：物流系统开放动作处理层
 * 版本：1.0
 * 日期：2013/10/21
 * 作者：管拥军
 */

class TransOpenApiAct {
    public static $errCode	= 0;
	public static $errMsg	= "";

	/**
	 * TransOpenApiAct::act_getLogZip()
	 * 打包日志文件
	 * @return  json string
	 */
	public static function act_getLogZip(){
        $res			= TransOpenApiModel::getLogZip();
		self::$errCode  = TransOpenApiModel::$errCode;
        self::$errMsg   = TransOpenApiModel::$errMsg;
		return $res;
	}
	
	/**
	 * TransOpenApiAct::act_getCurrencyExchange()
	 * 物流系统获取汇率
	 * @param string $fromCode 转换前的币种
	 * @param string $toCode 转换后的币种
	 * @return array
	 */	 
	public function act_getCurrencyExchange($fromCodes='', $toCodes=''){
		$fromCodes			= isset($_POST["fromCode"]) ? $_POST["fromCode"] : $fromCodes;
		$toCodes			= isset($_POST["toCode"]) ? $_POST["toCode"] : $toCodes;
		$res				= array();
		if(empty($fromCodes) || !is_array($fromCodes)) {
			self::$errCode 	= 10000;
			self::$errMsg  	= "转换前的币种参数非法！";
			return false;
		}
		if(empty($toCodes) || !is_array($toCodes)) {
			self::$errCode 	= 10001;
			self::$errMsg  	= "转换后的币种参数非法！";
			return false;
		}
		//循环数组获取对应的汇率
		foreach($fromCodes as $fCode) {
			foreach($toCodes as $tCode) {
				$from_Currency 		= urlencode($fCode);
				$to_Currency 		= urlencode($tCode);
				$url 				= "download.finance.yahoo.com/d/quotes.html?s=".$from_Currency.$to_Currency."=X&f=sl1d1t1ba&e=.html";
				$ch 				= curl_init();
				$timeout 			= 30;
				curl_setopt ($ch, CURLOPT_URL, $url);
				curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch,  CURLOPT_USERAGENT , "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)");
				curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
				$response 			= curl_exec($ch);
				$statusCode			= curl_getinfo($ch,CURLINFO_HTTP_CODE);
				if(in_array($statusCode,array(200))) {
					self::$errCode 	= 10002;
					self::$errMsg  	= date('Y-m-d H:i:s',time())."===雅虎财经汇率接口地址已经被变更,请联系相关负责人处理！";
					return false;
				}
				if(curl_errno($ch)) {
					self::$errCode 	= 10002;
					self::$errMsg  	= date('Y-m-d H:i:s',time())."===雅虎财经汇率接口拉取数据失败,原因：".curl_error($ch);
					return false;
				}
				curl_close($ch);
				$data 				= explode(',', $response);
				if(empty($data[1])) {
					$res["{$fCode}/{$tCode}"]	= 0;
				} else {
					$res["{$fCode}/{$tCode}"]	= $data[1];
				}
			}
		}
		return $res;
	}
	
	/**
	 * TransOpenApiAct::act_invalidTrackNumber()
	 * 作废订单跟踪号接口
	 * @param int $orderId 订单编号ID
	 * @return array
	 */
	public static function act_invalidTrackNumber(){
		$orderId			= isset($_REQUEST["orderId"]) ? abs(intval($_REQUEST["orderId"])) : 0;
		if(empty($orderId)) {
			self::$errCode  = "订单ID参数非法";
			self::$errMsg   = 10001;
			return false;
		}
        $res				= TransOpenApiModel::invalidTrackNumbers($orderId);
		self::$errCode  	= TransOpenApiModel::$errCode;
        self::$errMsg   	= TransOpenApiModel::$errMsg;
		return $res;
	}
	
	/**
	 * TransOpenApiAct::act_assignTrackNumber()
	 * 分配跟踪号接口
	 * @param int $carrierId 运输方式ID
	 * @param int $orderId 订单编号ID
	 * @param int $channelId 渠道ID（可选）
	 * @param string $country 所属国家（可选）
	 * @return array
	 */
	public static function act_assignTrackNumber(){
		$carrierId	= isset($_REQUEST["carrierId"]) ? intval($_REQUEST["carrierId"]) : 0;
		$orderId	= isset($_REQUEST["orderId"]) ? intval($_REQUEST["orderId"]) : 0;
		$channelId	= isset($_REQUEST["channelId"]) ? intval($_REQUEST["channelId"]) : 0;
		$country	= isset($_REQUEST["country"]) ? post_check($_REQUEST["country"]) : "";
		if(!in_array($carrierId, C('TRACK_NUMBER_CARRIER'))) {
			self::$errCode  = "运输方式参数有误";
			self::$errMsg   = 10000;
			return false;
		}
		if(empty($orderId)) {
			self::$errCode  = "订单ID不能为空";
			self::$errMsg   = 10001;
			return false;
		}
        $res				= TransOpenApiModel::assignTrackNumbers($carrierId, $orderId, $country, $channelId);
		self::$errCode  	= TransOpenApiModel::$errCode;
        self::$errMsg   	= TransOpenApiModel::$errMsg;
		return $res;
	}
	
	/**
	 * TransOpenApiAct::act_updateFedexFee()
	 * 批量更新同步老ERP联邦运费价目表
	 * @param int $type 1经济型，2优先型
	 * @return  json string
	 */
	public static function act_updateFedexFee(){
		$type	= isset($_REQUEST["type"]) ? post_check($_REQUEST["type"]) : "";
		$baf	= isset($_REQUEST["baf"]) ? floatval($_REQUEST["baf"]) : "";
		if(!in_array($type,array(1,2))) {
			self::$errCode  = "类型参数有误";
			self::$errMsg   = 10000;
			return false;
		}
		if(empty($baf)) {
			self::$errCode  = "燃油附加费不能为空";
			self::$errMsg   = 10001;
			return false;
		}
        $res				= TransOpenApiModel::updateFedexFee($type, $baf);
		self::$errCode  	= TransOpenApiModel::$errCode;
        self::$errMsg   	= TransOpenApiModel::$errMsg;
		return $res;
	}
	
	/**
	 * TransOpenApiAct::act_getTracknumSimpleInfo()
	 * 获取跟踪号简易信息
	 * @param string $tracknum 跟踪号
	 * @return array;
	 */
	public function act_getTracknumSimpleInfo(){
		$tracknum	= isset($_REQUEST["tracknum"]) ? post_check($_REQUEST["tracknum"]) : "";
		$is_wedo	= isset($_REQUEST["is_wedo"]) ? post_check($_REQUEST["is_wedo"]) : 0;
		if(empty($tracknum)) {
			self::$errCode  = "跟踪号有误！";
			self::$errMsg   = 10000;
			return false;
		}
		$cacheName 			= md5("trans_tracknum_simple_".$tracknum);
		$memc_obj			= new Cache(C('CACHEGROUP'));
		$tracknumSimpleInfo = $memc_obj->get_extral($cacheName);
		if(!empty($tracknumSimpleInfo)) {
			return unserialize($tracknumSimpleInfo);
		} else {
			$tracknumSimpleInfo	= TransOpenApiModel::getTracknumSimpleInfo($tracknum, $is_wedo);
			self::$errCode  	= TransOpenApiModel::$errCode;
			self::$errMsg   	= TransOpenApiModel::$errMsg;
			$isok 				= $memc_obj->set_extral($cacheName, serialize($tracknumSimpleInfo), 86400);
			if(!$isok) {
				self::$errCode 	= 308;
				self::$errMsg  	= 'memcache缓存出错!';
				//return false;
			}
			return $tracknumSimpleInfo;
		}
    }
	
	/**
	 * TransOpenApiAct::act_getTrackInfo()
	 * 获取跟踪号追踪信息
	 * @param string $tid 跟踪号
	 * @param string $type 运输方式（如中国邮政,ems）
	 * @return  json string;
	 */
	public function act_getTrackInfo(){
		$tid	= isset($_REQUEST["tid"]) ? post_check($_REQUEST["tid"]) : "";
		$type	= isset($_REQUEST["type"]) ? urldecode($_REQUEST["type"]) : "";
		$lan	= isset($_REQUEST["lan"]) ? intval($_REQUEST["lan"]) : 10000;
		if(empty($tid)) {
			self::$errCode  = "跟踪号有误！";
			self::$errMsg   = 10000;
			return false;
		}
		if(empty($type)) {
			self::$errCode  = "运输方式有误！";
			self::$errMsg   = 10001;
			return false;
		}
		if(!in_array($lan,array(0,1,10000))) {
			self::$errCode  = "跟踪语言参数有误！";
			self::$errMsg   = 10002;
			return false;
		}
        $res			= TransOpenApiModel::getTrackInfo($tid, $type, $lan);
		self::$errCode  = TransOpenApiModel::$errCode;
        self::$errMsg   = TransOpenApiModel::$errMsg;
		return $res;
    }
	
	/**
	 * TransOpenApiAct::act_getTrackNodeList()
	 * 根据运输方式ID获取预警节点
	 * @param string $carrierId 
	 * @param string $channelId 
	 * @return  string ;
	 */
	public function act_getTrackNodeList(){
		$carrierId	= isset($_REQUEST['carrierId']) ? abs(intval($_REQUEST['carrierId'])) : 0;
		$channelId	= isset($_REQUEST['channelId']) ? abs(intval($_REQUEST['channelId'])) : 0;
		if(empty($carrierId)){
			self::$errCode 	= 10000;
			self::$errMsg 	= '运输方式ID有误!';
			return false;
		}
		if(empty($channelId)){
			self::$errCode	= 10001;
			self::$errMsg 	= '渠道ID有误!';
			return false;
		}
		$nodeInfo 			= TransOpenApiModel::getTrackNodeList($carrierId, $channelId);
		return $nodeInfo;
	}
	
	/**
	 * TransOpenApiAct::act_getConfigInfo()
	 * 获取配置文件信息
	 * @return array 
	 */
	public function act_getConfigInfo(){
		$res 	= C();
		return ($res);		
	}
	
	/**
	 * TransOpenApiAct::act_getRandTrackNodeCount()
	 * 随机获取某个运输方式某个渠道节点总数
	 * @param int $id 运输方式ID
	 * @return int 
	 */
	public function act_getRandTrackNodeCount(){
		$carrierId			= isset($_REQUEST['carrierId']) ? abs(intval($_REQUEST['carrierId'])) : 0;
		if(empty($carrierId)){
			self::$errCode 	= 10000;
			self::$errMsg 	= '运输方式ID有误!';
			return false;
		}
		$nodeInfo			= TransOpenApiModel::getRandTrackNodeList($carrierId);
		return count($nodeInfo);
	}
	
	/**
	 * TransOpenApiAct::act_getErpCarrierList()
	 * 获取ERP运输方式列表并cache
	 * @return  array
	 */
	public function act_getErpCarrierList(){
		$cacheName 		= md5("trans_erp_carrier_list");
		$memc_obj		= new Cache(C('CACHEGROUP'));
		$erpCarrierInfo = $memc_obj->get_extral($cacheName);
		if(!empty($erpCarrierInfo)) {
			return unserialize($erpCarrierInfo);
		} else {
			$erpCarrierInfo 	= TransOpenApiModel::getErpCarrierList();
			$isok 		 		= $memc_obj->set_extral($cacheName, serialize($erpCarrierInfo), 86400);
			if(!$isok) {
				self::$errCode 	= 308;
				self::$errMsg  	= 'memcache缓存出错!';
				//return false;
			}
			return $erpCarrierInfo;
		}
    }
	
	/**
	 * TransOpenApiAct::act_getTrackCarriers()
	 * 获取运输方式跟踪列表并cache
	 * @return  array
	 */
	public function act_getTrackCarriers(){
		$cacheName 		= md5("trans_track_carriers");
		$memc_obj		= new Cache(C('CACHEGROUP'));
		$trackCarriers 	= $memc_obj->get_extral($cacheName);
		if(!empty($trackCarriers)) {
			return unserialize($trackCarriers);
		} else {
			$trackCarriers 		= TransOpenApiModel::getTrackCarriers();
			$isok 				= $memc_obj->set_extral($cacheName, serialize($trackCarriers), 86400);
			if(!$isok) {
				self::$errCode 	= 308;
				self::$errMsg 	= 'memcache缓存出错!';
				//return false;
			}
			return $trackCarriers;
		}
    }
	
	/**
	 * TransOpenApiAct::act_getAuthCompanyList()
	 * 获取鉴权公司列表memcache
	 * @return  array
	 */
	public function act_getAuthCompanyList(){
		$cacheName 		= md5("trans_auth_company_list");
		$memc_obj		= new Cache(C('CACHEGROUP'));
		$companyInfo 	= $memc_obj->get_extral($cacheName);
		if(!empty($companyInfo)) {
			return unserialize($companyInfo);
		} else {
			$companyInfo 		= TransOpenApiModel::getAuthCompanyList();
			$isok 		   		= $memc_obj->set_extral($cacheName, serialize($companyInfo), 86400);
			if(!$isok) {
				self::$errCode 	= 308;
				self::$errMsg 	= 'memcache缓存出错!';
				//return false;
			}
			return $companyInfo;
		}
    }
	
	/**
	 * TransOpenApiModel::usCalcShipCost()
	 * 海外仓运输方式+运费计算
	 * @param float $weight 重量
	 * @param string $postcode 邮编
	 * @param string $other 其它待定（暂不用）
	 * @return  array;
	 */
	public static function act_usCalcShipCost(){
        $weight   = isset($_REQUEST['weight']) ? abs(floatval($_REQUEST['weight'])) : 0;
        $other	  = isset($_REQUEST['other']) ? trim($_REQUEST['other']) : '';
		$postcode = isset($_REQUEST['postcode']) ? trim($_REQUEST['postcode']) : '';
		if(empty($weight) || !is_numeric($weight)) {
			self::$errCode  = "重量有误！";
			self::$errMsg   = 10000;
			return false;
		}
		if(empty($postcode)) {
			self::$errCode  = "邮政编码有误！";
			self::$errMsg   = 10001;
			return false;
		}
		$res			= TransOpenApiModel::usCalcShipCost($weight, $postcode, $other);
		self::$errCode  = TransOpenApiModel::$errCode;
        self::$errMsg   = TransOpenApiModel::$errMsg;
		return $res;		
	}
	
	/**
	 * TransOpenApiAct::fetch_channel()
	 * 返回某种运输方式最便宜的渠道运费
	 * @param int $carrierId 运输方式ID
	 * @param int $channelId 渠道ID(备用)
	 * @return  array
	 */
	public function fetch_channel($carrierId, $channelId=''){
		$channel 		= $shipfeeobj->getChannelInfo($carrierId);
		$minship 		= null;
		$res			= array();
		foreach ($channel as $v) {
			$shipfee 				= $shipfeeobj->calculateShipfee($v['channelAlias'], $weight, $shcountryname, $data);
			if(empty($minship)) {
				$minship 			= $shipfee['fee'];
				$res['discount']	= $shipfee['discount'];
				$res['fee']			= $minship;
				$res['channelId']	= $v['id'];
			}
			if($shipfee['fee'] < $minship) {
				$minship = $shipfee['fee'];
				$res['discount']	= $shipfee['discount'];
				$res['fee']			= $minship;
				$res['channelId']	= $v['id'];
			}					
		}
		return $res;
	}
	
	/**
	 * TransOpenApiAct::act_batchCarrierShipFeeByCode()
	 * 根据多个运输方式简码计算运费
	 * @param string $carrierAbb 简码名称
	 * @param string $country 国家
	 * @param float $weight 重量
	 * @param string $postCode 邮政编码（预留）
	 * @param string $transitId 转运中心ID（预留）
	 * @param int $shipAddId 发货地ID
	 * @return array;
	 */
    public function act_batchCarrierShipFeeByCode(){
        $carrierAbb = isset($_REQUEST['carrierAbb']) ? post_check($_REQUEST['carrierAbb']) : '';
        $country  	= isset($_REQUEST['country']) ? rawurldecode(trim($_REQUEST['country'])) : '';
        $weight   	= isset($_REQUEST['weight']) ? abs(floatval($_REQUEST['weight'])) : 0;
        $postCode 	= isset($_REQUEST['postCode']) ? trim($_REQUEST['postCode']) : '';    
        $transitId 	= isset($_REQUEST['transitId']) ? abs(intval($_REQUEST['transitId'])) : 0;
        $shipAddId	= isset($_REQUEST['shipAddId']) ? abs(intval($_REQUEST['shipAddId'])) : 0;
		if(empty($carrierAbb) || !(preg_match("/^([A-Z]+,)*[A-Z]+$/", $carrierAbb))) {
			self::$errCode 	= 10000;
            self::$errMsg 	= '运输方式简码参数有误！';
            return false;
		}
		if(empty($shipAddId)) {
			self::$errCode 	= 10001;
            self::$errMsg 	= '发货地址ID参数有误！';
            return false;
		}
		if(empty($country)) { 
            self::$errCode 	= 10002;
            self::$errMsg 	= '国家参数有误！';
            return false;
        }
		if(empty($weight)) { 
            self::$errCode 	= 10003;
            self::$errMsg 	= '重量参数有误！';
            return false;
        }
		$abbs				= explode(",",$carrierAbb);
		$carriers			= self::act_getCarrierAbb();
		if(empty($carriers)) {
			self::$errCode 	= 10004;
            self::$errMsg 	= '无可用的运输方式简码参与运费计算！';
            return false;
		}
		$fee				= array();
		//循环验证简码合法性及计算运费
		foreach($abbs as $abb) {
			//验证简码和发货地址是否匹配
			$flag			= false;
			foreach($carriers as $v) {
				if($v['carrierAbb'] == $abb && $v['addressId'] == $shipAddId) {
					$flag 	= true;
					break;
				}
			}
			if($flag === false) {
				self::$errCode 	= 10005;
				self::$errMsg 	= '运输方式简码和发货地址存在不匹配的情况，请检查后再提交！';
				return false;
			} else {
				$fee[$abb]		= self::act_fixCarrierShipFeeByCode($abb,$country,$weight,$postCode,$transitId);
			}
		}		
		$rtn		= $fee;
		//增加接口调用日志
		$logFile	= WEB_PATH."log/batchCarrierAbbShipFee/".date('Y')."/".date('m')."/".date('Y-m-d').".abb.log";
		$log		= date('Y-m-d H:i:s')."==={$carrierAbb}==={$carrierId}==={$country}==={$weight}==={$postCode}==={$transitId}===".json_encode($rtn)."\n";
		if(function_exists('write_a_file')) {
			@write_a_file($logFile, $log);
		}
        return $rtn;
    }
	
	/**
	 * TransOpenApiAct::act_fixCarrierShipFeeByCode()
	 * 根据运输方式简码计算运费
	 * @param string $carrierAbb 简码名称
	 * @param string $country 国家
	 * @param float $weight 重量
	 * @param string $postCode 邮政编码（预留）
	 * @param string $transitId 转运中心ID（预留）
	 * @return array;
	 */
    public function act_fixCarrierShipFeeByCode($carrierAbb='',$country='',$weight=0,$postCode='',$transitId=0){
        $carrierAbb = isset($_REQUEST['carrierAbb']) ? post_check($_REQUEST['carrierAbb']) : $carrierAbb;
        $country  	= isset($_REQUEST['country']) ? rawurldecode(trim($_REQUEST['country'])) : $country;
        $weight   	= isset($_REQUEST['weight']) ? abs(floatval($_REQUEST['weight'])) : $weight;
        $postCode 	= isset($_REQUEST['postCode']) ? trim($_REQUEST['postCode']) : $postCode;    
        $transitId 	= isset($_REQUEST['transitId']) ? abs(intval($_REQUEST['transitId'])) : $transitId;
		if(empty($carrierAbb)) {
			self::$errCode 	= 10000;
            self::$errMsg 	= '运输方式简码参数有误！';
            return false;
		}
		$res 		= TransOpenApiModel::getCarrierByAbb($carrierAbb);
		$carrierId	= isset($res['id']) ? intval($res['id']) : 0;
		if(empty($carrierId)) { 
            self::$errCode 	= 10001;
            self::$errMsg 	= '运输方式简码不存在！';
            return false;
        }
		if(empty($country)) { 
            self::$errCode 	= 10002;
            self::$errMsg 	= '国家参数有误！';
            return false;
        }
		if(empty($weight)) { 
            self::$errCode 	= 10003;
            self::$errMsg 	= '重量参数有误！';
            return false;
        }
		$shipfee 			= 0;
		$data	 			= array();
        $data['postCode'] 	= $postCode; //获取邮编如果有
        $data['transitId'] 	= $transitId; //获取转运中心ID如果有
		if(!in_array($carrierId,array(1,2,3,4,5,6,8,9,10,62,79,80,81,83,84,85,86,87,88,89,91,92,93,95,96,97,98))) {
            self::$errCode 	= 10001;
            self::$errMsg 	= '不支持运费计算的运输方式简码！';
            return false;
		}
        $shipfeeobj 		= new ShipfeeQueryModel();
		//小语种国家转标准国家
        $stdcountry 		= $shipfeeobj->translateMinorityLangToStd($country);
        if(empty($stdcountry)) { 
            $stdcountry 	= $country;
        } else {
            $stdcountry 	= $stdcountry['countryName'];
        }
        //标准国家转运输方式国家	
		$shcountryname 	= $shipfeeobj->translateStdCountryNameToShipCountryName($stdcountry, $carrierId);
		//某个运输方式下所有渠道信息
		$channel 		= $shipfeeobj->getChannelInfo($carrierId);
		$minship 		= 0;
		$res			= array();
		//返回最优的（运输方式ID、渠道ID、折后价、折扣、原价）
		foreach ($channel as $v) {
			$shipfee 				= $shipfeeobj->calculateShipfee($v['channelAlias'], $weight, $shcountryname, $data);
			if(($shipfee['fee'] < $minship && !empty($shipfee['fee'])) || empty($minship)) {
				$minship 			= empty($shipfee['fee']) ? 0 : $shipfee['fee'];
				$res['discount']	= empty($shipfee['discount']) ? 0 : $shipfee['discount'];
				$res['fee']			= $minship;
				$res['channelId']	= $v['id'];
				$res['totalFee']	= empty($shipfee['totalfee']) ? 0 : $shipfee['totalfee'];
				$res['level']		= empty($shipfee['level']) ? '' : $shipfee['level'];
				$res['exRate']		= empty($shipfee['exRate']) ? '' : $shipfee['exRate'];
				$res['country']		= $shcountryname;
			}
		}
		$rtn		= array('fee'=>$res['fee'],'channelId'=>$res['channelId'],'discount'=>$res['discount'],'carrierId'=>$carrierId,'totalFee'=>$res['totalFee'],'country'=>$res['country'],'level'=>$res['level'],'exRate'=>$res['exRate']);
		//增加接口调用日志
		$logFile	= WEB_PATH."log/fixShipFee/".date('Y')."/".date('m')."/".date('Y-m-d').".abb.log";
		$log		= date('Y-m-d H:i:s')."==={$carrierAbb}==={$carrierId}==={$country}==={$weight}==={$postCode}==={$transitId}===".json_encode($rtn)."\n";
		if(function_exists('write_a_file')) {
			@write_a_file($logFile, $log);
		}
        return $rtn;
    }
	
	/**
	 * TransOpenApiAct::act_fixCarrierQueryNew()
	 * 固定运输方式费用新接口
	 * @param int $carrierId 运输方式ID
	 * @param string $country 国家
	 * @param float $weight 重量
	 * @param string $postCode 邮政编码（预留）
	 * @return array;
	 */
    public function act_fixCarrierQueryNew($carrierId=0,$country='',$weight=0,$postCode='',$transitId=0){
        $carrierId  		= isset($_REQUEST['carrierId']) ? abs(intval($_REQUEST['carrierId'])) : $carrierId;
        $country  			= isset($_REQUEST['country']) ? rawurldecode(trim($_REQUEST['country'])) : $country;
        $weight   			= isset($_REQUEST['weight']) ? abs(floatval($_REQUEST['weight'])) : $weight;
        $postCode 			= isset($_REQUEST['postCode']) ? trim($_REQUEST['postCode']) : $postCode;    
        $transitId 			= isset($_REQUEST['transitId']) ? intval($_REQUEST['transitId']) : $transitId;
        if(empty($carrierId)) { 
            self::$errCode 	= 10000;
            self::$errMsg 	= '运输方式ID参数有误！';
            return false;
        }
		if(empty($country)) { 
            self::$errCode 	= 10001;
            self::$errMsg 	= '国家参数有误！';
            return false;
        }
		if(empty($weight)) { 
            self::$errCode 	= 10002;
            self::$errMsg 	= '重量参数有误！';
            return false;
        }
		$shipfee 			= 0;
		$data	 			= array();
        $data['postCode'] 	= $postCode; //获取邮编如果有
        $data['transitId'] 	= $transitId; //获取转运中心ID如果有
		if(!in_array($carrierId,array(1,2,3,4,5,6,8,9,10,62,79,80,81,83,84,85,86,87,88,89,91,92,93,95,96,97,98))) {
            self::$errCode 	= 10001;
            self::$errMsg 	= '不支持的运输方式ID';
            return false;
		}
        $shipfeeobj 		= new ShipfeeQueryModel();
		//小语种国家转标准国家
        $stdcountry 		= $shipfeeobj->translateMinorityLangToStd($country);
        if(empty($stdcountry)) { 
            $stdcountry 	= $country;
        } else {
            $stdcountry 	= $stdcountry['countryName'];
        }
        //标准国家转运输方式国家	
		$shcountryname 		= $shipfeeobj->translateStdCountryNameToShipCountryName($stdcountry, $carrierId);
		//某个运输方式下所有渠道信息
		$channel 			= $shipfeeobj->getChannelInfo($carrierId);
		$minship 			= 0;
		$res				= array();
		//返回最优的（运输方式ID、渠道ID、折后价、折扣、原价）
		foreach ($channel as $v) {
			$shipfee 				= $shipfeeobj->calculateShipfee($v['channelAlias'], $weight, $shcountryname, $data);
			if(($shipfee['fee'] < $minship && !empty($shipfee['fee'])) || empty($minship)) {
				$minship 			= empty($shipfee['fee']) ? 0 : $shipfee['fee'];
				$res['discount']	= empty($shipfee['discount']) ? 0 : $shipfee['discount'];
				$res['fee']			= $minship;
				$res['channelId']	= $v['id'];
				$res['totalFee']	= empty($shipfee['totalfee']) ? 0 : $shipfee['totalfee'];
				$res['level']		= empty($shipfee['level']) ? '' : $shipfee['level'];
				$res['exRate']		= empty($shipfee['exRate']) ? '' : $shipfee['exRate'];
				$res['country']		= $shcountryname;
			}
		}
		$rtn		= array('fee'=>$res['fee'],'channelId'=>$res['channelId'],'discount'=>$res['discount'],'carrierId'=>$carrierId,'totalFee'=>$res['totalFee'],'country'=>$res['country'],'level'=>$res['level'],'exRate'=>$res['exRate']);
		//增加接口调用日志
		$logFile	= WEB_PATH."log/fixShipFee/".date('Y')."/".date('m')."/".date('Y-m-d').".log";
		$log		= date('Y-m-d H:i:s')."==={$carrierId}==={$country}==={$weight}==={$postCode}==={$transitId}===".json_encode($rtn)."\n";
		if(function_exists('write_a_file')) {
			@write_a_file($logFile, $log);
		}
        return $rtn;
    }
	
	/**
	 * TransOpenApiAct::act_batchOrderChannelFeeQuery()
	 * 批量订单多个渠道运费计算
	 * @param array $orders 订单信息
	 * @return array;
	 */
    public function act_batchOrderChannelFeeQuery(){
		$orderMax 			= C('ORDER_SHIPFEE_TRACKNUM_MAX');
		$resMore			= 2;
		$orders				= array();
		$res 				= array();
        $orders				= isset($_POST['orders']) ? $_POST['orders'] : $orders;
		$orders				= json_decode($orders,true);
		if(!is_array($orders)) {
			self::$errCode 	= 10000;
            self::$errMsg 	= '订单信息参数格式有误！';
            return false;
		}
		if(empty($orders)) {
			self::$errCode 	= 10001;
            self::$errMsg 	= '订单信息内容有误！';
            return false;
		}
		if(count($orders) > $orderMax) {
			self::$errCode 	= 10002;
            self::$errMsg 	= "一次批处理订单数据不能呢个超过 {$orderMax}单,请返回修改下！";
            return false;
		}		
		//循环获取运费
		foreach($orders as $key=>$v) {
			$shipFee		= array();
			$shipFee		= self::act_batchFixChannelIdQuery($v['channelId'],rawurldecode($v['country']),$v['weight'],$v['postCode'],$v['transitId'],$resMore);
			if(empty($shipFee)) continue;
			$res[$key]		= array("shipFee"=>$shipFee);
			//增加接口调用日志
			$logFile		= WEB_PATH."log/batchOrderChannelShipFee/".date('Y')."/".date('m')."/".date('Y-m-d').".log";
			$log			= date('Y-m-d H:i:s')."==={$key}==={$v['channelId']}==={$v['country']}==={$v['weight']}==={$v['postCode']}==={$v['transitId']}==={$resMore}===".json_encode($res[$key])."\n";
			if(function_exists('write_a_file')) {
				@write_a_file($logFile, $log);
			}
		}
		return $res;
    }
	
	/**
	 * TransOpenApiAct::act_batchOrderFeeQuery()
	 * 批量订单固定运输方式运费计算
	 * @param array $orders 订单信息
	 * @return array;
	 */
    public function act_batchOrderFeeQuery(){
		$orderMax 			= C('ORDER_SHIPFEE_TRACKNUM_MAX');
		$resMore			= 2;
		$orders				= array();
		$res 				= array();
        $orders				= isset($_POST['orders']) ? $_POST['orders'] : $orders;

		$orders				= json_decode($orders,true);
		if(!is_array($orders)) {
			self::$errCode 	= 10000;
            self::$errMsg 	= '订单信息参数格式有误！';
            return false;
		}
		if(empty($orders)) {
			self::$errCode 	= 10001;
            self::$errMsg 	= '订单信息内容有误！';
            return false;
		}
		if(count($orders) > $orderMax) {
			self::$errCode 	= 10002;
            self::$errMsg 	= "一次批处理订单数据不能呢个超过 {$orderMax}单,请返回修改下！";
            return false;
		}		
		//循环获取最优运费
		foreach($orders as $key=>$v) {
			$shipFee		= array();
			$shipFee		= self::act_batchFixCarrierQuery($v['carrierId'],rawurldecode($v['country']),$v['weight'],$v['postCode'],$v['transitId'],$resMore);
			if(empty($shipFee)) continue;
			$res[$key]		= array("shipFee"=>$shipFee);
			//增加接口调用日志
			$logFile		= WEB_PATH."log/batchOrderShipFee/".date('Y')."/".date('m')."/".date('Y-m-d').".log";
			$log			= date('Y-m-d H:i:s')."==={$key}==={$v['carrierId']}==={$v['country']}==={$v['weight']}==={$v['postCode']}==={$v['transitId']}==={$resMore}===".json_encode($res[$key])."\n";
			if(function_exists('write_a_file')) {
				@write_a_file($logFile, $log);
			}
		}
		return $res;
    }
	
	/**
	 * TransOpenApiAct::act_batchOrderTracknumQuery()
	 * 批量订单固定运输方式运费计算及跟踪号申请接口
	 * @param array $orders 订单信息
	 * @return array;
	 */
    public function act_batchOrderTracknumQuery(){
		$orderMax 			= C('ORDER_SHIPFEE_TRACKNUM_MAX');
		$orders				= array();
		$res 				= array();
        $orders				= isset($_POST['orders']) ? $_POST['orders'] : $orders;
		$orders				= json_decode($orders,true);
		if(!is_array($orders)) {
			self::$errCode 	= 10000;
            self::$errMsg 	= '订单信息参数格式有误！';
            return false;
		}
		if(empty($orders)) {
			self::$errCode 	= 10001;
            self::$errMsg 	= '订单信息内容有误！';
            return false;
		}
		if(count($orders) > $orderMax) {
			self::$errCode 	= 10002;
            self::$errMsg 	= "一次批处理订单数据不能呢个超过 {$orderMax}单,请返回修改下！";
            return false;
		}		
		//循环获取最优运费及跟踪号
		foreach($orders as $key=>$v) {
			$channelId		= 0;
			$carrierId		= 0;
			$country		= "";
			$trackNums		= array();
			$carrierId		= $v['carrierId'];
			$channelId		= $v['channelId'];
			$country		= $v['country'];
			//如果运输方式是瑞士小包挂号
			if($carrierId == 88 && $country == "Switzerland") {
				$country 	= "Switzerland"; 
			} else {
				$country	= "";
			}
			$trackNums		= TransOpenApiModel::assignTrackNumbers($carrierId, $key, $country, $channelId);
			if(empty($trackNums)) $trackNums = array("trackNumber"=>"");
			$res[$key]		= array("trackNums"=>$trackNums);
			//增加接口调用日志
			$logFile		= WEB_PATH."log/batchOrderTrackNum/".date('Y')."/".date('m')."/".date('Y-m-d').".log";
			$log			= date('Y-m-d H:i:s')."==={$key}==={$v['carrierId']}==={$v['country']}==={$v['channelId']}==={$resMore}===".json_encode($res[$key])."\n";
			if(function_exists('write_a_file')) {
				@write_a_file($logFile, $log);
			}
		}
		return $res;
    }
	
	/**
	 * TransOpenApiAct::act_batchOrderFeeTracknumQuery()
	 * 批量订单固定运输方式运费计算及跟踪号申请接口
	 * @param array $orders 订单信息
	 * @return array;
	 */
    public function act_batchOrderFeeTracknumQuery(){
		$trackNumArr	= C('TRACK_NUMBER_CARRIER');
		$orderMax 		= C('ORDER_SHIPFEE_TRACKNUM_MAX');
		$resMore		= 1;
		$orders			= array();
		$res 			= array();
        $orders			= isset($_POST['orders']) ? $_POST['orders'] : $orders;
		$orders			= json_decode($orders,true);
		if(!is_array($orders)) {
			self::$errCode 	= 10000;
            self::$errMsg 	= '订单信息参数格式有误！';
            return false;
		}
		if(empty($orders)) {
			self::$errCode 	= 10001;
            self::$errMsg 	= '订单信息内容有误！';
            return false;
		}
		if(count($orders) > $orderMax) {
			self::$errCode 	= 10002;
            self::$errMsg 	= "一次批处理订单数据不能呢个超过 {$orderMax}单,请返回修改下！";
            return false;
		}		
		//循环获取最优运费及跟踪号
		foreach($orders as $key=>$v) {
			$channelId		= 0;
			$carrierId		= 0;
			$country		= "";
			$shipFee		= array();
			$trackNums		= array();
			$shipFee		= self::act_batchFixCarrierQuery($v['carrierId'],rawurldecode($v['country']),$v['weight'],$v['postCode'],$v['transitId'],$resMore);
			if(empty($shipFee)) continue;
			$carrierId		= $shipFee['carrierId'];
			$channelId		= $v['channelId'];
			$country		= $shipFee['country'];
			//如果运输方式是瑞士小包挂号
			if($carrierId == 88 && $country == "Switzerland") {
				$country 	= "Switzerland"; 
			} else {
				$country	= "";
			}
			$trackNums		= TransOpenApiModel::assignTrackNumbers($carrierId, $key, $country, $channelId);
			if(empty($trackNums)) $trackNums = array("trackNumber"=>"");
			$res[$key]		= array("shipFee"=>$shipFee,"trackNums"=>$trackNums);
			//增加接口调用日志
			$logFile		= WEB_PATH."log/batchShipFeeTrackNum/".date('Y')."/".date('m')."/".date('Y-m-d').".log";
			$log			= date('Y-m-d H:i:s')."==={$key}==={$v['carrierId']}==={$v['country']}==={$v['weight']}==={$v['postCode']}==={$v['transitId']}==={$resMore}===".json_encode($res[$key])."\n";
			if(function_exists('write_a_file')) {
				@write_a_file($logFile, $log);
			}
		}		
		return $res;
    }

	/**
	 * TransOpenApiAct::act_batchFixChannelIdQuery()
	 * 批量固定运输方式渠道费用计算
	 * @param string $channelId 运输方式ID
	 * @param string $country 国家
	 * @param float $weight 重量
	 * @param string $postCode 邮政编码（预留）
	 * @param int $transitId 转运中心ID（预留）
	 * @param int $resMore 运费结果多条还是一条,默认1条
	 * @return array;
	 */
    public function act_batchFixChannelIdQuery($channelId=0,$country='',$weight=0,$postCode='',$transitId=0,$resMore=1){
        $channels   = isset($_REQUEST['channelId']) ? post_check($_REQUEST['channelId']) : $channelId;
        $country  	= isset($_REQUEST['country']) ? rawurldecode(trim($_REQUEST['country'])) : $country;
        $weight   	= isset($_REQUEST['weight']) ? abs(floatval($_REQUEST['weight'])) : $weight;
        $postCode 	= isset($_REQUEST['postCode']) ? trim($_REQUEST['postCode']) : $postCode;    
        $transitId 	= isset($_REQUEST['transitId']) ? abs(intval($_REQUEST['transitId'])) : $transitId;
        $resMore 	= isset($_REQUEST['resMore']) ? abs(intval($_REQUEST['resMore'])) : $resMore;
        if(empty($channels) || !(preg_match("/^([\d]+,)*[\d]+$/", $channels))) { 
            self::$errCode 	= 10000;
            self::$errMsg 	= '渠道参数有误！';
            return false;
        }
		if(empty($country)) { 
            self::$errCode 	= 10001;
            self::$errMsg 	= '国家参数有误！';
            return false;
        }
		if(empty($weight)) { 
            self::$errCode 	= 10002;
            self::$errMsg 	= '重量参数有误！';
            return false;
        }
		if(!in_array($resMore,array(1,2))) { 
            self::$errCode 	= 10003;
            self::$errMsg 	= '运费返回格式有误！';
            return false;
        }		
		$data	 			= array();
        $data['postCode'] 	= $postCode; //获取邮编如果有
        $data['transitId'] 	= $transitId; //获取转运中心ID如果有
		$shipFeeArr			= array();
        $shipfeeobj 		= new ShipfeeQueryModel();
		//小语种国家转标准国家
        $stdcountry 		= $shipfeeobj->translateMinorityLangToStd($country);
        if(empty($stdcountry)) { 
            $stdcountry 	= $country;
        } else {
            $stdcountry 	= $stdcountry['countryName'];
        }
		$channel 			= TransOpenApiModel::getCarrierChannel("",$channels);
		//返回最优的（运输方式ID、渠道ID、折后价、折扣、原价）
		foreach($channel as $v) {
			//标准国家转运输方式国家	
			$shcountryname 			= $shipfeeobj->translateStdCountryNameToShipCountryName($stdcountry, $carrierId);
			//运费计算
			$res					= array();
			if(!method_exists($shipfeeobj,"cal_{$v['channelAlias']}")) continue; //检查运费计算函数是不是存在，不存在跳过
			$shipfee 				= $shipfeeobj->calculateShipfee($v['channelAlias'], $weight, $shcountryname, $data);
			$res['discount']		= empty($shipfee['discount']) ? 0 : $shipfee['discount'];
			$res['fee']				= empty($shipfee['fee']) ? 0 : $shipfee['fee'];
			$res['channelId']		= $v['id'];
			$res['carrierId']		= $v['carrierId'];
			$res['totalFee']		= empty($shipfee['totalfee']) ? 0 : $shipfee['totalfee'];
			$res['level']			= empty($shipfee['level']) ? '' : $shipfee['level'];
			$res['exRate']			= empty($shipfee['exRate']) ? '' : $shipfee['exRate'];
			$res['country']			= $shcountryname;
			$shipFeeArr[$v['id']]	= array('fee'=>$res['fee'],'channelId'=>$res['channelId'],'discount'=>$res['discount'],'carrierId'=>$res['carrierId'],'totalFee'=>$res['totalFee'],'country'=>$res['country'],'level'=>$res['level'],'exRate'=>$res['exRate']);
		}
		//只返回一条最优运费计算结果
		if($resMore==1) {
			//过滤价格为0的运输方式运费
			foreach ($shipFeeArr as $key => $row) {
				if($row['fee'] <= 0) unset($shipFeeArr[$key]);
			}
			//对计算价格默认按照价格升序排列
			foreach ($shipFeeArr as $key => $row) {
				$fee[$key]  = $row['fee'];
			}
			array_multisort($fee, SORT_ASC, $shipFeeArr);
			$rtn	= $shipFeeArr[0];
		} else {
			$rtn	= $shipFeeArr;
		}
		//增加接口调用日志
		$logFile	= WEB_PATH."log/batchChannelShipFee/".date('Y')."/".date('m')."/".date('Y-m-d').".log";
		$log		= date('Y-m-d H:i:s')."==={$channels}==={$country}==={$weight}==={$postCode}==={$transitId}==={$resMore}===".json_encode($rtn)."\n";
		if(function_exists('write_a_file')) {
			@write_a_file($logFile, $log);
		}		
        return $rtn;
    }
	
	/**
	 * TransOpenApiAct::act_batchFixCarrierQuery()
	 * 批量固定运输方式费用计算
	 * @param int $carrierId 运输方式ID
	 * @param string $country 国家
	 * @param float $weight 重量
	 * @param string $postCode 邮政编码（预留）
	 * @param int $resMore 运费结果多条还是一条,默认多条
	 * @return array;
	 */
    public function act_batchFixCarrierQuery($carrierId=0,$country='',$weight=0,$postCode='',$transitId=0,$resMore=2){
        $carriers   = isset($_REQUEST['carrierId']) ? post_check($_REQUEST['carrierId']) : $carrierId;
        $country  	= isset($_REQUEST['country']) ? rawurldecode(trim($_REQUEST['country'])) : $country;
        $weight   	= isset($_REQUEST['weight']) ? abs(floatval($_REQUEST['weight'])) : $weight;
        $postCode 	= isset($_REQUEST['postCode']) ? trim($_REQUEST['postCode']) : $postCode;    
        $transitId 	= isset($_REQUEST['transitId']) ? abs(intval($_REQUEST['transitId'])) : $transitId;
        $resMore 	= isset($_REQUEST['resMore']) ? abs(intval($_REQUEST['resMore'])) : $resMore;
        if(empty($carriers) || !(preg_match("/^([\d]+,)*[\d]+$/", $carriers))) { 
            self::$errCode 	= 10000;
            self::$errMsg 	= '运输方式参数有误！';
            return false;
        }
		if(empty($country)) { 
            self::$errCode 	= 10001;
            self::$errMsg 	= '国家参数有误！';
            return false;
        }
		if(empty($weight)) { 
            self::$errCode 	= 10002;
            self::$errMsg 	= '重量参数有误！';
            return false;
        }
		if(!in_array($resMore,array(1,2))) { 
            self::$errCode 	= 10003;
            self::$errMsg 	= '运费返回格式有误！';
            return false;
        }		
		$data	 			= array();
        $data['postCode'] 	= $postCode; //获取邮编如果有
        $data['transitId'] 	= $transitId; //获取转运中心ID如果有
		$carrierArr			= explode(",", $carriers);
		$shipFeeArr			= array();
        $shipfeeobj 		= new ShipfeeQueryModel();
		//小语种国家转标准国家
        $stdcountry 		= $shipfeeobj->translateMinorityLangToStd($country);
        if(empty($stdcountry)) { 
            $stdcountry 	= $country;
        } else {
            $stdcountry 	= $stdcountry['countryName'];
        }
		//运输方式运费循环计算开始
		foreach($carrierArr as $carrierId) {
			if(!in_array($carrierId,array(1,2,3,4,5,6,8,9,10,62,79,80,81,83,84,85,86,87,88,89,91,92,93,95,96,97,98))) {
				$shipFeeArr[$carrierId]	= array('fee'=>0,'channelId'=>0,'discount'=>0,'carrierId'=>$carrierId,'totalFee'=>0,'country'=>'','level'=>'','exRate'=>'');
				continue;
			}
			//标准国家转运输方式国家	
			$shcountryname 	= $shipfeeobj->translateStdCountryNameToShipCountryName($stdcountry, $carrierId);
			//某个运输方式下所有渠道信息
			$channel 		= $shipfeeobj->getChannelInfo($carrierId);
			$minship 		= 0;
			$shipfee 		= 0;
			$res			= array();
			//返回最优的（运输方式ID、渠道ID、折后价、折扣、原价）
			foreach($channel as $v) {
				$shipfee 				= $shipfeeobj->calculateShipfee($v['channelAlias'], $weight, $shcountryname, $data);
				if(($shipfee['fee'] < $minship && !empty($shipfee['fee'])) || empty($minship)) {
					$minship 			= empty($shipfee['fee']) ? 0 : $shipfee['fee'];
					$res['discount']	= empty($shipfee['discount']) ? 0 : $shipfee['discount'];
					$res['fee']			= $minship;
					$res['channelId']	= $v['id'];
					$res['totalFee']	= empty($shipfee['totalfee']) ? 0 : $shipfee['totalfee'];
					$res['level']		= empty($shipfee['level']) ? '' : $shipfee['level'];
					$res['exRate']		= empty($shipfee['exRate']) ? '' : $shipfee['exRate'];
					$res['country']		= $shcountryname;
				}
			}
			$shipFeeArr[$carrierId]		= array('fee'=>$res['fee'],'channelId'=>$res['channelId'],'discount'=>$res['discount'],'carrierId'=>$carrierId,'totalFee'=>$res['totalFee'],'country'=>$res['country'],'level'=>$res['level'],'exRate'=>$res['exRate']);
		}
		//只返回一条最优运费计算结果
		if($resMore==1) {
			//过滤价格为0的运输方式运费
			foreach ($shipFeeArr as $key => $row) {
				if($row['fee'] <= 0) unset($shipFeeArr[$key]);
			}
			//对计算价格默认按照价格升序排列
			foreach ($shipFeeArr as $key => $row) {
				$fee[$key]  = $row['fee'];
			}
			array_multisort($fee, SORT_ASC, $shipFeeArr);
			$rtn	= $shipFeeArr[0];
		} else {
			$rtn	= $shipFeeArr;
		}
		//增加接口调用日志
		$logFile	= WEB_PATH."log/batchShipFee/".date('Y')."/".date('m')."/".date('Y-m-d').".log";
		$log		= date('Y-m-d H:i:s')."==={$carriers}==={$country}==={$weight}==={$postCode}==={$transitId}==={$resMore}===".json_encode($rtn)."\n";
		if(function_exists('write_a_file')) {
			@write_a_file($logFile, $log);
		}		
        return $rtn;
    }	
	    
	/**
	 * TransOpenApiAct::act_getBestCarrierNew()
	 * 最优运输方式费用接口
	 * @param string $country 国家
	 * @param float $weight 重量
	 * @param int $shipAddId 发货地址ID
	 * @param string $postCode 邮政编码
	 * @param string $noShipId 不参与计算的运输方式ID
	 * @return array;
	 */
    public function act_getBestCarrierNew($shipAddId=0,$country='',$weight=0,$postCode='',$transitId=0,$noShipId=''){
        $country 	= isset($_REQUEST['country']) ? rawurldecode(trim($_REQUEST['country'])) : $country;
        $weight 	= isset($_REQUEST['weight']) ? abs(floatval($_REQUEST['weight'])) : $weight;
        $shipAddId	= isset($_REQUEST['shipAddId']) ? abs(intval($_REQUEST['shipAddId'])) : $shipAddId;
        $postCode 	= isset($_REQUEST['postCode']) ? post_check($_REQUEST['postCode']) : $postCode;
        $transitId 	= isset($_REQUEST['transitId']) ? intval($_REQUEST['transitId']) : $transitId;
        $noShipId 	= isset($_REQUEST['noShipId']) ? post_check($_REQUEST['noShipId']) : $noShipId;
        if(empty($shipAddId)) { 
            self::$errCode 	= 10000;
            self::$errMsg 	= '发货地址ID参数有误！';
            return false;
        }
		if(empty($country)) { 
            self::$errCode 	= 10001;
            self::$errMsg 	= '国家参数有误！';
            return false;
        }
		if(empty($weight)) { 
            self::$errCode 	= 10002;
            self::$errMsg 	= '重量参数有误！';
            return false;
        }
		$data	 			= array();
        $data['postCode'] 	= $postCode; //获取邮编如果有
        $data['transitId'] 	= $transitId; //获取转运中心ID如果有
        $queryobj 			= new ShipfeeQueryModel();
		//小语种转标准国家
        $stdc 				= $queryobj->translateMinorityLangToStd($country);
        $countrystd 		= '';
        if(empty($stdc)) {
            $countrystd 	= $country;
        } else {
            $countrystd 	= $stdc['countryName'];
        }        
        //根据发货地获取运输方式列表并排除要排除的运输方式ID
        $shiplist = $queryobj->getShipListByShipaddr($shipAddId,$noShipId);
        $shipcalculateresult = array();
		//将发货地址下所有的运输方式所有渠道的运费都计算一遍
        foreach ($shiplist as $shipval) {
            $result 	= array();
			$shipfee	= array();
			//获取某个运输方式的所有渠道信息并保存计算相关渠道运费
            $channel 	= $queryobj->getChannelInfo($shipval['id']);
			if(empty($channel)) continue;
			foreach ($channel as $ch) {
				$result['chname'] 		= $ch['channelName']; //渠道名
				$result['channelId']	= $ch['id']; //渠道ID
				$result['carrierId'] 	= $ch['carrierId']; //运输方式ID
				$result['carriername'] 	= $shipval['carrierNameCn']; //运输方式名
				$result['paname'] 		= ''; //分区名称
				//标准国家转运输方式国家
				$carriercountryname 	= $queryobj->translateStdCountryNameToShipCountryName($countrystd, $shipval['id']);
				if(empty($carriercountryname)) $carriercountryname = $countrystd;
				//计算某个运输方式某个渠道运费
				if(!method_exists($queryobj,"cal_{$ch['channelAlias']}")) continue;
				$shipfee = $queryobj->calculateShipfee($ch['channelAlias'], $weight, $carriercountryname, $data);
				if(!$shipfee) continue;
				$result['shipfee'] 		= $shipfee['fee']; //折扣价
				$result['rate'] 		= empty($shipfee['discount']) ? 0 : $shipfee['discount']; //折扣
				$result['totalFee'] 	= empty($shipfee['totalfee']) ? 0 : $shipfee['totalfee']; //原价
				$result['level']		= empty($shipfee['level']) ? '' : $shipfee['level'];
				$result['exRate']		= empty($shipfee['exRate']) ? '' : $shipfee['exRate'];
				$result['country']		= $carriercountryname;
				$shipcalculateresult[] 	= $result;
			}
        }
		//返回最优的（运输方式ID、渠道ID、折后价、折扣、原价）
        $minship 	= array();
		$eubFee	 	= array();
		$mineubship = array();
        foreach ($shipcalculateresult as $val) {
            if(empty($minship)) $minship = $val;
            if($val['shipfee'] < $minship['shipfee']) $minship = $val;
			if($val['carrierId']=='6' && ($val['shipfee'] < $mineubship['shipfee'] || empty($mineubship))) $mineubship = $val;
		}
        if(empty($minship)) { //没有找到最优运输方式
            self::$errCode 	= 303;
            self::$errMsg 	= '没有找到最优运输方式';
            return;
        }
		// 如果运输方式为中国邮政平邮或挂号且总重量少于2KG
		if(in_array($minship['carrierId'],array(1,2)) && $weight <=2) {
			if(!empty($mineubship['shipfee']) && !empty($minship['shipfee']) && (($mineubship['shipfee']-$minship['shipfee']) <= 2 || ($mineubship['shipfee']/$minship['shipfee'] - 1) <= 0.09)) {
				$rtn		= array('fee'=>$mineubship['shipfee'], 'carrierId'=>$mineubship['carrierId'], 'channelId'=>$mineubship['channelId'], 'discount'=>$mineubship['rate'], 'totalFee'=>$mineubship['totalFee'], 'country'=>$mineubship['country'], 'level'=>$mineubship['level'], 'exRate'=>$mineubship['exRate']);
				//增加接口调用日志
				$logFile	= WEB_PATH."log/bestShipFee/".date('Y')."/".date('m')."/".date('Y-m-d').".log";
				$log		= date('Y-m-d H:i:s')."==={$shipAddId}==={$country}==={$weight}==={$postCode}==={$transitId}==={$noShipId}===".json_encode($rtn)."\n";
				if(function_exists('write_a_file')) {
					@write_a_file($logFile, $log);
				}
				return $rtn;
			}
		}
		$rtn		= array('fee'=>$minship['shipfee'], 'carrierId'=>$minship['carrierId'], 'channelId'=>$minship['channelId'], 'discount'=>$minship['rate'], 'totalFee'=>$minship['totalFee'], 'country'=>$minship['country'], 'level'=>$minship['level'], 'exRate'=>$minship['exRate']);
		//增加接口调用日志
		$logFile	= WEB_PATH."log/bestShipFee/".date('Y')."/".date('m')."/".date('Y-m-d').".log";
		$log		= date('Y-m-d H:i:s')."==={$shipAddId}==={$country}==={$weight}==={$postCode}==={$transitId}==={$noShipId}===".json_encode($rtn)."\n";
		if(function_exists('write_a_file')) {
			@write_a_file($logFile, $log);
		}
        return $rtn;
    }
	
	/**
	 * TransOpenApiAct::act_batchBestCarrier()
	 * 批量最优运输方式费用接口
	 * @param string $country 国家
	 * @param float $weight 重量
	 * @param int $shipAddId 发货地址ID
	 * @param string $postCode 邮政编码
	 * @param string $noShipId 不参与计算的运输方式ID
	 * @return array;
	 */
    public function act_batchBestCarrier(){
        $country 	= isset($_REQUEST['country']) ? rawurldecode(trim($_REQUEST['country'])) : '';
        $weight 	= isset($_REQUEST['weight']) ? abs(floatval($_REQUEST['weight'])) : 0;
        $shipAddId	= isset($_REQUEST['shipAddId']) ? abs(intval($_REQUEST['shipAddId'])) : 0;
        $postCode 	= isset($_REQUEST['postCode']) ? post_check($_REQUEST['postCode']) : '';
        $transitId 	= isset($_REQUEST['transitId']) ? abs(intval($_REQUEST['transitId'])) : 0;
        $noShipId 	= isset($_REQUEST['noShipId']) ? post_check($_REQUEST['noShipId']) : '';
        if(empty($shipAddId)) { 
            self::$errCode 	= 10000;
            self::$errMsg 	= '发货地址ID参数有误！';
            return false;
        }
		if(empty($country)) { 
            self::$errCode 	= 10001;
            self::$errMsg 	= '国家参数有误！';
            return false;
        }
		if(empty($weight)) { 
            self::$errCode 	= 10002;
            self::$errMsg 	= '重量参数有误！';
            return false;
        }
		$data	 			= array();
        $data['postCode'] 	= $postCode; //获取邮编如果有
        $data['transitId'] 	= $transitId; //获取转运中心ID如果有
        $queryobj 			= new ShipfeeQueryModel();
		//小语种转标准国家
        $stdc 				= $queryobj->translateMinorityLangToStd($country);
        $countrystd 		= '';
        if(empty($stdc)) {
            $countrystd 	= $country;
        } else {
            $countrystd 	= $stdc['countryName'];
        }        
        //根据发货地获取运输方式列表并排除要排除的运输方式ID
        $shiplist 				= $queryobj->getShipListByShipaddr($shipAddId, $noShipId);
		$shipFeeArr				= array();
		//将发货地址下所有的运输方式所有渠道的运费都计算一遍
        foreach($shiplist as $shipval) {
            $result 	= array();
			$shipfee	= array();
			//获取某个运输方式的所有渠道信息并保存计算相关渠道运费
            $channel 	= $queryobj->getChannelInfo($shipval['id']);
			if(empty($channel)) continue;
			foreach ($channel as $ch) {
				$result['channelId']	= $ch['id']; //渠道ID
				$result['carrierId'] 	= $ch['carrierId']; //运输方式ID
				//标准国家转运输方式国家
				$carriercountryname 	= $queryobj->translateStdCountryNameToShipCountryName($countrystd, $shipval['id']);
				if(empty($carriercountryname)) $carriercountryname = $countrystd;
				//计算某个运输方式某个渠道运费
				if(!method_exists($queryobj,"cal_{$ch['channelAlias']}")) continue;
				$shipfee = $queryobj->calculateShipfee($ch['channelAlias'], $weight, $carriercountryname, $data);
				if(!$shipfee) continue;
				$result['fee'] 			= $shipfee['fee']; //折扣价
				$result['rate'] 		= empty($shipfee['discount']) ? 0 : $shipfee['discount']; //折扣
				$result['totalFee'] 	= empty($shipfee['totalfee']) ? 0 : $shipfee['totalfee']; //原价
				$result['level']		= empty($shipfee['level']) ? '' : $shipfee['level'];
				$result['exRate']		= empty($shipfee['exRate']) ? '' : $shipfee['exRate'];
				$result['country']		= $carriercountryname;
				$shipFeeArr[] 			= $result;
			}
        }
		//增加接口调用日志
		$logFile	= WEB_PATH."log/batchBestShipFee/".date('Y')."/".date('m')."/".date('Y-m-d').".log";
		$log		= date('Y-m-d H:i:s')."==={$shipAddId}==={$country}==={$weight}==={$postCode}==={$transitId}==={$noShipId}===".json_encode($shipFeeArr)."\n";
		if(function_exists('write_a_file')) {
			@write_a_file($logFile, $log);
		}
        return $shipFeeArr;
    }
	
	/**
	 * TransOpenApiAct::openFixCarrierQueryNew()
	 * 开放固定运输方式费用新接口
	 * @param int $carrierId 运输方式ID
	 * @param string $country 国家
	 * @param float $weight 重量
	 * @param string $postCode 邮政编码（预留）
	 * @return array;
	 */
    public function openFixCarrierQueryNew($carrierId,$weightFlag=''){
        $country  	= isset($_REQUEST['country']) ? rawurldecode(trim($_REQUEST['country'])) : '';
        $weight   	= isset($_REQUEST['weight']) ? abs(floatval($_REQUEST['weight'])) : 0;
        $postCode 	= isset($_REQUEST['postCode']) ? trim($_REQUEST['postCode']) : '';    
        $transitId 	= isset($_REQUEST['transitId']) ? intval($_REQUEST['transitId']) : 0;
        if(empty($carrierId)) { 
            self::$errCode 	= 10000;
            self::$errMsg 	= '运输方式ID参数有误！';
            return false;
        }
		if(empty($country)) { 
            self::$errCode 	= 10001;
            self::$errMsg 	= '国家参数有误！';
            return false;
        }
		if(empty($weight)) { 
            self::$errCode 	= 10002;
            self::$errMsg 	= '重量参数有误！';
            return false;
        }
		$shipfee 			= 0;
		$data	 			= array();
        $data['postCode'] 	= $postCode; //获取邮编如果有
        $data['transitId'] 	= $transitId; //获取转运中心ID如果有
		if(!in_array($carrierId,array(1,2,3,4,5,6,8,9,10,62,79,80,81,83,84,85,86,87,88,89,91,92,93,95,96,97,98))) {
            self::$errCode 	= 10001;
            self::$errMsg 	= '不支持的运输方式ID';
            return;
		}
		//中国邮政（平邮、挂号）体积没有超过110时，但体积重超过2KG，按实重1KG算
		// if(in_array($carrierId,array(1,2)) && $weight>2 && $weightFlag == 'volWeight') {
            // $weight			= 1;
		// }
        $shipfeeobj 		= new ShipfeeQueryModel();
		//小语种国家转标准国家
        $stdcountry 		= $shipfeeobj->translateMinorityLangToStd($country);
        if(empty($stdcountry)) { 
            $stdcountry 	= $country;
        } else {
            $stdcountry 	= $stdcountry['countryName'];
        }
        //标准国家转运输方式国家	
		$shcountryname 	= $shipfeeobj->translateStdCountryNameToShipCountryName($stdcountry, $carrierId);
		//某个运输方式下所有渠道信息
		$channel 		= $shipfeeobj->getChannelInfo($carrierId);
		$minship 		= 0;
		$res			= array();
		//返回最优的（运输方式ID、渠道ID、折后价、折扣、原价）
		foreach ($channel as $v) {
			$shipfee 				= $shipfeeobj->calculateShipfee($v['channelAlias'], $weight, $shcountryname, $data);
			if(($shipfee['fee'] < $minship && !empty($shipfee['fee'])) || empty($minship)) {
				$minship 			= empty($shipfee['fee']) ? 0 : $shipfee['fee'];
				$res['discount']	= empty($shipfee['discount']) ? 0 : $shipfee['discount'];
				$res['fee']			= $minship;
				$res['channelId']	= $v['id'];
				$res['totalFee']	= empty($shipfee['totalfee']) ? 0 : $shipfee['totalfee'];
				$res['level']		= empty($shipfee['level']) ? '' : $shipfee['level'];
				$res['exRate']		= empty($shipfee['exRate']) ? '' : $shipfee['exRate'];
				$res['country']		= $shcountryname;
			}
		}
		$rtn		= array('fee'=>$res['fee'],'channelId'=>$res['channelId'],'discount'=>$res['discount'],'carrierId'=>$carrierId,'totalFee'=>$res['totalFee'],'country'=>$res['country'],'level'=>$res['level'],'exRate'=>$res['exRate']);
		//增加接口调用日志
		$logFile	= WEB_PATH."log/fixShipFee/".date('Y')."/".date('m')."/".date('Y-m-d').".log";
		$log		= date('Y-m-d H:i:s')."==={$carrierId}==={$country}==={$weight}==={$postCode}==={$transitId}===".json_encode($rtn)."\n";
		if(function_exists('write_a_file')) {
			@write_a_file($logFile, $log);
		}
        return $rtn;
    }
	
	/**
	 * TransOpenApiAct::act_openBestCarrierShipFee()
	 * 开放批量最优运输方式费用接口
	 * @param string $country 国家
	 * @param float $weight 重量
	 * @param int $shipAddId 发货地址ID
	 * @param string $postCode 邮政编码
	 * @param string $apiToken api调用token
	 * @param string $noShipId 不参与计算的运输方式ID
	 * @return array;
	 */
    public function act_openBestCarrierShipFee(){
		$res 				= array();
		$data				= array();
		$openFee			= array();
		$noShipArr			= array();
		$times				= time();
		$usRate				= 0;
		$exRates			= array();
		$apiToken 			= isset($_REQUEST['apiToken']) ? post_check($_REQUEST['apiToken']) : '';
		$noShipId 			= isset($_REQUEST['noShipId']) ? post_check($_REQUEST['noShipId']) : '';
		$weightFlag			= isset($_REQUEST['weightFlag']) ? post_check($_REQUEST['weightFlag']) : '';
		if(empty($apiToken)) {
            self::$errCode 	= 20001;
            self::$errMsg 	= 'API TOKEN 参数有误！';
            return false;
		}
		if(!empty($noShipId)) {
			if(!(preg_match("/^([\d]+,)*[\d]+$/", $noShipId))) {
				self::$errCode 	= 20002;
				self::$errMsg 	= '要排除的运输方式ID参数有误！';
				return false;
			} else {
				$noShipArr	= explode(",",$noShipId);
			}
		}
		//检查API token 合法性
		$res 				= ApiCompetenceModel::getApiInfoByToken($apiToken);
		if(empty($res)) {
			self::$errCode 	= 20004;
            self::$errMsg 	= '当前API TOKEN数据不存在,请检查相关token数据！';
            return false;
		}
		$apiTokenExpire		= isset($res['apiTokenExpire']) ? intval($res['apiTokenExpire']) : 0;
		$apiMaxCount		= isset($res['apiMaxCount']) ? intval($res['apiMaxCount']) : 0;
		$apiName			= isset($res['apiName']) ? $res['apiName'] : '';
		$apiValue			= isset($res['apiValue']) ? $res['apiValue'] : '';
		$apiUid				= isset($res['apiUid']) ? $res['apiUid'] : 0;
		$apiId				= isset($res['id']) ? $res['id'] : 0;		
		$maxCount			= 0;
		if($apiTokenExpire <= $times) {
			self::$errCode 	= 20003;
            self::$errMsg 	= '当前API TOKEN已过期,请更新API TOKEN！';
            return false;
		}
		if($apiName !== 'openBestCarrierShipFee') {
			self::$errCode 	= 20004;
            self::$errMsg 	= '当前API调用接口名称有误,请检查！';
            return false;
		}		
		if($apiMaxCount > 0) {
			$s_time			= strtotime(date('Y-m-d',$times)." 00:00:01");
			$e_time			= strtotime(date('Y-m-d',$times)." 23:59:59");
			$res 			= ApiVisitStatModel::getStatByTime($apiId, $apiUid, $s_time, $e_time);
			$maxCount		= isset($res['apiCount']) ? intval($res['apiCount']) : 0;
			if($maxCount > $apiMaxCount) {
				self::$errCode 	= 20005;
				self::$errMsg 	= "当日当前API接口调用次数:{$maxCount},已超过最大次:{$apiMaxCount}！";
				return false;
			}
		}
		//API 接口调用统计
		$res 				= ApiVisitStatModel::updateApiVisitStat($apiId, $apiUid);
		if(!$res) {
			self::$errCode 	= 20006;
			self::$errMsg 	= "API 接口调用统计出错，请联系相关负责人处理！";
			return false;	
		}		
		//开放API运费计算
		$res 				= self::act_batchBestCarrier();
		$carriers			= explode(",",$apiValue);
		foreach($carriers as $v) {
			if(!empty($noShipArr)) {
				if(in_array($v,$noShipArr)) continue;
			}
			$res 			= self::openFixCarrierQueryNew($v,$weightFlag);
			$totalFee		= 0;
			// 开放价格 = 原价 + 开放折扣价
			$result			= CarrierOpenModel::getCarrierOpenByCid($v);
			if(empty($res['totalFee'])) continue;
			$totalFee		= ceil($res['totalFee'] + $res['totalFee'] * $result['carrierDiscount']);
			//针对运德物流的开放运费查询做美元转换
			if($apiToken == 'e19d2feabc0eb1705f69c6ea2d9d0e1d') {
				$exRates	= TransOpenApiModel::cacheExRateInfo(array('USD'), array('CNY'), 'usRate', 7200, 0);
				$usRate		= round(floatval($exRates['USD/CNY']),4);
				if($usRate <= 0) continue;
				$totalFee	= ceil($totalFee/$usRate);
			}		
			$openFee[]		= array(
								"carrierId" 	=> $res['carrierId'],
								"channelId" 	=> $res['channelId'],
								"totalFee" 		=> $totalFee,
								"abb"			=> $result['carrierAbb'],
								"enName"		=> $result['carrierEn'],
								"aging"			=> $result['carrierAging'],
								"note"			=> $result['carrierNote']
							);			
		}
		//对计算价格默认按照价格升序排列
		foreach ($openFee as $key => $row) {
			$fee[$key]  = $row['totalFee'];
		}
		array_multisort($fee, SORT_ASC, $openFee);
		return $openFee;
    }
	
	/**
	 * TransOpenApiAct::act_fixCarrierQuery()
	 * 固定运输方式费用接口(已废弃)
	 * @param int $carrier 运输方式ID
	 * @param string $country 国家
	 * @param float $weight 重量
	 * @param int $shaddr 发货地ID（预留）
	 * @param string $postcode 邮政编码
	 * @return  json string;
	 */
    public function act_fixCarrierQuery(){
		$endTime  			= strtotime("2014-03-31 00:00:01");
		if($endTime<time()) {
			self::$errCode 	= 90000;
            self::$errMsg 	= '此接口服务已过期,请联系物流系统负责人更换!';
            return;
		}
		$carrier  = isset($_REQUEST['carrier']) ? abs(intval($_REQUEST['carrier'])) : 0;
        $country  = isset($_REQUEST['country']) ? rawurldecode(trim($_REQUEST['country'])) : '';
        $weight   = isset($_REQUEST['weight']) ? abs(floatval($_REQUEST['weight'])) : 0;
        $shipaddr = isset($_REQUEST['shaddr']) ? trim($_REQUEST['shaddr']) : '';
        $postcode = isset($_REQUEST['postcode']) ? trim($_REQUEST['postcode']) : '';    
        
        if(empty($carrier) || empty($country) || empty($weight) || empty($shipaddr)) {   //参数不完整
            self::$errCode 	= 10000;
            self::$errMsg 	= '参数信息不完整';
            return;
        }
        $shipfee 	= 0;
        $shipfeeobj = new ShipfeeQueryModel();
        $stdcountry = $shipfeeobj->translateMinorityLangToStd($country);
        if(empty($stdcountry)) { //没找到对应记录 则默认就是英文标准名称
            $stdcountry = $country;
        } else {
            $stdcountry = $stdcountry['countryName'];
        }
        $data 		= array();
        $data['postcode'] 	= $postcode;
		if(!in_array($carrier,array(1,2,3,4,5,6,8,9,10,61,62,79,80,81))) {
            self::$errCode 	= 10001;
            self::$errMsg 	= '不支持的运输方式ID';
            return;
		}		
		$shcountryname 	= $shipfeeobj->translateStdCountryNameToShipCountryName($stdcountry, $carrier);
		$channel 		= $shipfeeobj->getChannelInfo($carrier);
		$minship 		= 0;
		$res			= array();
		foreach ($channel as $v) {
			$shipfee 	= $shipfeeobj->calculateShipfee($v['channelAlias'], $weight, $shcountryname, $data);
			if(empty($minship)) {
				$minship = empty($shipfee['fee']) ? 0 : $shipfee['fee'];
				$res['discount']	= empty($shipfee['discount']) ? 0 : $shipfee['discount'];
				$res['fee']			= $minship;
				$res['channelId']	= $v['id'];
			}
			if($shipfee['fee'] < $minship) {
				$minship = empty($shipfee['fee']) ? 0 : $shipfee['fee'];
				$res['discount']	= empty($shipfee['discount']) ? 0 : $shipfee['discount'];
				$res['fee']			= $minship;
				$res['channelId']	= $v['id'];
			}					
		}
        return array('fee'=>$res);
    }
    
	/**
	 * TransOpenApiAct::act_getBestCarrier()
	 * 固定运输方式费用接口(已废弃)
	 * @param string $country 国家
	 * @param float $weight 重量
	 * @param int $shaddr 发货地ID（预留）
	 * @param string $postcode 邮政编码
	 * @param string $noShipId 邮政编码
	 * @return  json string;
	 */
    public function act_getBestCarrier(){
		$endTime  			= strtotime("2014-03-31 00:00:01");
		if($endTime<time()) {
			self::$errCode 	= 90000;
            self::$errMsg 	= '此接口服务已过期,请联系物流系统负责人更换!';
            return;
		}
        $country 	= isset($_REQUEST['country']) ? rawurldecode(trim($_REQUEST['country'])) : '';
        $weight 	= isset($_REQUEST['weight']) ? abs(floatval($_REQUEST['weight'])) : 0;
        $shipaddr	= isset($_REQUEST['shaddr']) ? post_check($_REQUEST['shaddr']) : '';
        $postcode 	= isset($_REQUEST['postcode']) ? post_check($_REQUEST['postcode']) : '';
        $noShipId 	= isset($_REQUEST['noshipid']) ? post_check($_REQUEST['noshipid']) : '';
        //print_r($_REQUEST);exit;
        if(empty($country) || empty($weight) || empty($shipaddr)) {   //参数不完整
            self::$errCode 	= 301;
            self::$errMsg 	= '参数信息不完整';
            return;
        }
        $queryobj 	= new ShipfeeQueryModel();
        $stdc 		= $queryobj->translateMinorityLangToStd($country);   //将小语种转换为标准英文
        $countrystd = '';
        if(empty($stdc)){   //没找到 则默认为标准的英文名
            $countrystd = $country;
        }else{
            $countrystd = $stdc['countryName'];
        }
        
        $data 			= array('postcode'=>$postcode);
        
        /*根据发货地获取相应的发货方式列表*/
        $shiplist 		= $queryobj->getShipListByShipaddr($shipaddr,$noShipId);
		/* 计算每一种发货方式的运费 */
        $shipcalculateresult 	= array();     //运输方式的计算结果集
        foreach ($shiplist as $shipval){
            $result 			= array();
            $channel 			= $queryobj->getChannelInfo($shipval['id']);
			if(empty($channel)) continue;
			foreach($channel as $ch) {
				$result['chname'] 		= $ch['channelName'];        //渠道名
				$result['channelId']	= $ch['id'];        //渠道ID
				$result['carrierId'] 	= $ch['carrierId'];        //运输方式ID
				$result['carriername'] 	= $shipval['carrierNameCn']; //运输方式名
				$result['paname'] 		= '';      //分区名称
				$carriercountryname 	= $queryobj->translateStdCountryNameToShipCountryName($countrystd, $shipval['id']);
				if(empty($carriercountryname)) {    //对照表中没有找到对应的信息 则默认为标准国家名称
					$carriercountryname = $countrystd;
				}
				$re 					= $queryobj->calculateShipfee($ch['channelAlias'], $weight, $carriercountryname, $data);
				if(!$re) continue;
				$result['shipfee'] 		= $re['fee'];
				$result['rate'] 		= $re['discount'];
				$shipcalculateresult[] 	= $result;
			}
        }
        $minship 	= array();
		$eubFee	 	= array();
		$mineubship = array();
        foreach($shipcalculateresult as $val) {
            if(empty($minship)) {
                $minship = $val;
            }
            if($val['shipfee'] < $minship['shipfee']) {
                $minship = $val;
            }
			if($val['carrierId']=='6' && ($val['shipfee'] < $mineubship['shipfee'] || empty($mineubship))) {
				$mineubship = $val;
			}
        }
        if(empty($minship)) {    
            self::$errCode 	= 303;
            self::$errMsg 	= '没有找到最优运输方式';
            return;
        }
        self::$errCode 	= 300;
        self::$errMsg 	= 'ok';
		
		// 如果运输方式为中国邮政平邮或挂号且总重量少于2KG
		if(in_array($minship['carrierId'],array(1,2)) && $weight <=2) {
			if(!empty($mineubship['shipfee']) && !empty($minship['shipfee']) && (($mineubship['shipfee']-$minship['shipfee']) <= 2 || ($mineubship['shipfee']/$minship['shipfee'] - 1) <= 0.09)){
				return array('fee'=>$mineubship['shipfee'], 'carrierId'=>$mineubship['carrierId'], 'channelId'=>$mineubship['channelId']);
			}
		}
        return array('fee'=>$minship['shipfee'], 'carrierId'=>$minship['carrierId'], 'channelId'=>$minship['channelId']);
    }
	
	/**
	 * TransOpenApiAct::act_getCountryBySmall()
	 * 用小语种国家名取得英文国家名并存入mencache
	 * @param string $country 小语种国家
	 * @param int $is_new 是否强制更新,默认0不强制
	 * @return  string 英文国家名;
	 */
	public function act_getCountryBySmall(){
		$smallCountry		= isset($_REQUEST['country']) ? post_check($_REQUEST['country']) : "";
		$is_new				= isset($_REQUEST['is_new']) ? $_REQUEST['is_new'] : 0;
		if(!in_array($is_new,array(0,1))) {
			self::$errCode 	= 10001;
			self::$errMsg 	= '强制更新参数有误！';
			return false;
		}
		if(empty($smallCountry)) {
			self::$errCode 	= 10002;
			self::$errMsg 	= '小语种国家参数传递错误!';
			return false;
		}
		$memc_obj			= new Cache(C('CACHEGROUP'));
		$cacheName			= md5("countries_small_comparson".$smallCountry);
		$countryinfo 		= $memc_obj->get_extral($cacheName);
		if(!empty($countryinfo) && empty($is_new)) {
			return unserialize($countryinfo);
		} else {
			$countryinfo = TransOpenApiModel::getCountryBySmall($smallCountry);
			$isok  = $memc_obj->set_extral($cacheName, serialize($countryinfo));
			if(!$isok) {
				self::$errCode 	= 10003;
				self::$errMsg 	= 'memcache缓存出错!';
				//return false;
			}
			return $countryinfo;
		}
	}
	
	/**
	 * TransOpenApiAct::act_getCountryBySmall()
	 * 根据标准英文国家名获取全部小语种国家并存入memcache
	 * @param string $country 标准英文国家
	 * @param int $is_new 是否强制更新,默认0不强制
	 * @return  array;
	 */
	public function act_getSmallCountryByEn(){
		$country			= isset($_REQUEST['country']) ? post_check($_REQUEST['country']) : "";
		$is_new				= isset($_REQUEST['is_new']) ? $_REQUEST['is_new'] : 0;
		if(!in_array($is_new,array(0,1))) {
			self::$errCode 	= 10001;
			self::$errMsg 	= '强制更新参数有误！';
			return false;
		}
		if(empty($country)) {
			self::$errCode 	= 10002;
			self::$errMsg 	= '标准英文国家参数错误!';
			return false;
		}
		$memc_obj			= new Cache(C('CACHEGROUP'));
		$cacheName			= md5("countries_en_small".$country);
		$countryinfo 		= $memc_obj->get_extral($cacheName);
		if(!empty($countryinfo) && empty($is_new)) {
			return unserialize($countryinfo);
		} else {
			$countryinfo 	= TransOpenApiModel::getSmallCountryByEn($country);
			$isok  			= $memc_obj->set_extral($cacheName, serialize($countryinfo));
			if(!$isok) {
				self::$errCode 	= 10003;
				self::$errMsg 	= 'memcache缓存出错!';
				//return false;
			}
			return $countryinfo;
		}
	}
	
	/**
	 * TransOpenApiAct::act_getCountryBySn()
	 * 用国家名简称取得英文国家名并存入mencache
	 * @param string $country 国家简称
	 * @param int $is_new 是否强制更新,默认0不强制
	 * @return  string 英文国家名;
	 */
	public function act_getCountryBySn(){
		$countrySn			= isset($_REQUEST['country']) ? post_check(trim($_REQUEST['country'])) : "";
		$is_new				= isset($_REQUEST['is_new']) ? $_REQUEST['is_new'] : 0;
		if(!in_array($is_new,array(0,1))) {
			self::$errCode 	= 10001;
			self::$errMsg 	= '强制更新参数有误！';
			return false;
		}
		if(empty($countrySn)) {
			self::$errCode	= 304;
			self::$errMsg 	= '国家简称参数传递错误!';
			return false;
		}
		$memc_obj			= new Cache(C('CACHEGROUP'));
		$cacheName 			= md5("trans_countries_sn".$countrySn);
		$countryInfo 		= $memc_obj->get_extral($cacheName);
		if(!empty($countryInfo) && empty($is_new)) {
			return unserialize($countryInfo);
		} else {
			$countryInfo 	= TransOpenApiModel::getCountryBySn($countrySn);
			$isok  			= $memc_obj->set_extral($cacheName, serialize($countryInfo));
			if(!$isok) {
				self::$errCode 	= 305;
				self::$errMsg 	= 'memcache缓存出错!';
				//return false;
			}
			return $countryInfo;
		}
	}

	/**
	 * TransOpenApiAct::act_getPlatForm()
	 * 获取运输方式平台列表并存入memcache
	 * @param string $type ALL 获取全部，其它待定
	 * @param int $is_new 是否强制更新,默认0不强制
	 * @return  json string
	 */
	public function act_getPlatForm(){
		$type				= isset($_REQUEST['type']) ? post_check($_REQUEST['type']) : "";
		if(!in_array($type, array("ALL"))) {
			self::$errCode 	= 307;
			self::$errMsg 	= 'type 参数传递错误!';
			return false;
		}
		$cacheName 			= md5("trans_plat_form_list_".$type);
		$memc_obj			= new Cache(C('CACHEGROUP'));
		$platFormInfo 		= $memc_obj->get_extral($cacheName);
		if(!empty($platFormInfo) && empty($is_new)) {
			return unserialize($platFormInfo);
		} else {
			$platFormInfo	= TransOpenApiModel::getPlatForm($type);
			$isok 		 	= $memc_obj->set_extral($cacheName, serialize($platFormInfo));
			if(!$isok) {
				self::$errCode	= 306;
				self::$errMsg	= 'memcache缓存出错!';
				//return false;
			}
			return $platFormInfo;
		}
	}
	
	/**
	 * TransOpenApiAct::act_getCarrierByPlatFormId()
	 * 根据平台ID获取运输方式列表并存入memcache
	 * @param int $id 平台ID 
	 * @param int $is_new 是否强制更新(默认0不强制) 
	 * @return  json string 
	 */
	public function act_getCarrierByPlatFormId(){
		$id		= isset($_REQUEST['id']) ? abs(intval($_REQUEST['id'])) : 0;
		$is_new	= isset($_REQUEST['is_new']) ? $_REQUEST['is_new'] : 0;
		if(!in_array($is_new,array(0,1))) {
			self::$errCode 	= 10001;
			self::$errMsg 	= '强制更新参数有误！';
			return false;
		}
		if(empty($id)) {
			self::$errCode 	= 307;
			self::$errMsg 	= '平台ID参数传递错误!';
			return false;
		}
		$cacheName 		= md5("trans_plat_form_carrier_".$id);
		$memc_obj		= new Cache(C('CACHEGROUP'));
		$platFormInfo 	= $memc_obj->get_extral($cacheName);
		if(!empty($platFormInfo) && empty($is_new)) {
			return unserialize($platFormInfo);
		} else {
			$platFormInfo	= TransOpenApiModel::getCarrierByPlatFormId($id);
			$isok 		 	= $memc_obj->set_extral($cacheName, serialize($platFormInfo));
			if(!$isok) {
				self::$errCode	= 306;
				self::$errMsg	= 'memcache缓存出错!';
				//return false;
			}
			return $platFormInfo;
		}
	}
	
	/**
	 * TransOpenApiAct::act_getCarrierPlatShip()
	 * 根据运输方式ID和平台ID获取平台运输方式信息
	 * @param int $carrierId 运输方式ID
	 * @param int $platId 平台ID
	 * @param int $is_new 是否强制更新,默认0不强制
	 * @return  json sting 
	 */
	public function act_getCarrierPlatShip(){
		$carrierId	= isset($_REQUEST['carrierId']) ? abs(intval($_REQUEST['carrierId'])) : 0;
		$platId		= isset($_REQUEST['platId']) ? abs(intval($_REQUEST['platId'])) : 0;
		$is_new		= isset($_REQUEST['is_new']) ? $_REQUEST['is_new'] : 0;
		if(!in_array($is_new,array(0,1))) {
			self::$errCode 	= 10001;
			self::$errMsg 	= '强制更新参数有误！';
			return false;
		}
		if(empty($carrierId)) {
			self::$errCode 	= 10000;
			self::$errMsg 	= '运输方式ID参数传递错误!';
			return false;
		}
		if(empty($platId)) {
			self::$errCode 	= 10001;
			self::$errMsg 	= '平台ID参数传递错误!';
			return false;
		}
		$cacheName 		= md5("trans_carrier_plat_".$carrierId.$platId);
		$memc_obj		= new Cache(C('CACHEGROUP'));
		$carrierInfo 	= $memc_obj->get_extral($cacheName);
		if(!empty($carrierInfo) && empty($is_new)) {
			return unserialize($carrierInfo);
		} else {
			$carrierInfo = TransOpenApiModel::getCarrierPlatShip($carrierId, $platId);
			$isok 		 = $memc_obj->set_extral($cacheName, serialize($carrierInfo));
			if(!$isok) {
				self::$errCode	= 306;
				self::$errMsg	= 'memcache缓存出错!';
				//return false;
			}
			return $carrierInfo;
		}
	}
	
	/**
	 * TransOpenApiAct::act_getCountriesChina()
	 * 获取全部或某个地区ID的区域信息
	 * @param int $areaId 区域ID
	 * @param int $is_new 是否强制更新,默认0不强制
	 * @return  array
	 */
	public function act_getCountriesChina(){
		$areaId		= isset($_REQUEST['areaId']) ? post_check($_REQUEST['areaId']) : "";
		$flag		= preg_match("/^(all|\d+)$/",$areaId);
		$is_new		= isset($_REQUEST['is_new']) ? $_REQUEST['is_new'] : 0;
		if(!in_array($is_new,array(0,1))) {
			self::$errCode 	= 10001;
			self::$errMsg 	= '强制更新参数有误！';
			return false;
		}
		if(empty($areaId) || !$flag) {
			self::$errCode 	= 307;
			self::$errMsg 	= '区域ID参数传递错误!';
			return false;
		}
		if($areaId == "all") $areaId = "";
		$cacheName 		= md5("trans_countries_china_".$areaId);
		$memc_obj		= new Cache(C('CACHEGROUP'));
		$countryInfo 	= $memc_obj->get_extral($cacheName);
		if(!empty($countryInfo) && empty($is_new)) {
			return unserialize($countryInfo);
		} else {
			$countryInfo = TransOpenApiModel::getCountriesChina($areaId);
			$isok 		 = $memc_obj->set_extral($cacheName, serialize($countryInfo), 3600);
			if(!$isok) {
				self::$errCode	= 306;
				self::$errMsg	= 'memcache缓存出错!';
				//return false;
			}
			return $countryInfo;
		}
	}
	
	/**
	 * TransOpenApiAct::act_getCarrierByAdd()
	 * 根据发货地址ID获取运输方式信息
	 * @param int $addId 发货地址ID
	 * @param int $is_new 是否强制更新(默认0不强制)
	 * @return  array
	 */
	public function act_getCarrierByAdd(){
		$addId		= isset($_REQUEST['addId']) ? abs(intval($_REQUEST['addId'])) : 0;
		$is_new		= isset($_REQUEST['is_new']) ? $_REQUEST['is_new'] : 0;
		if(!in_array($is_new,array(0,1))) {
			self::$errCode 	= 10001;
			self::$errMsg 	= '强制更新参数有误！';
			return false;
		}
		if(empty($addId)) {
			self::$errCode 	= 307;
			self::$errMsg 	= '发货地址ID参数传递错误!';
			return false;
		}
		$cacheName 		= md5("trans_carrier_add_".$addId);
		$memc_obj		= new Cache(C('CACHEGROUP'));
		$carrierInfo 	= $memc_obj->get_extral($cacheName);
		if(!empty($carrierInfo) && empty($is_new)) {
			return unserialize($carrierInfo);
		} else {
			$carrierInfo = TransOpenApiModel::getCarrierByAdd($addId);
			$isok 		 = $memc_obj->set_extral($cacheName, serialize($carrierInfo), 3600);
			if(!$isok) {
				self::$errCode	= 306;
				self::$errMsg	= 'memcache缓存出错!';
				//return false;
			}
			return $carrierInfo;
		}
	}
	
	/**
	 * TransOpenApiAct::act_getCarrierById()
	 * 根据运输方式ID获取运输方式信息
	 * @param int $id 运输方式ID
	 * @param int $is_new 是否强制更新(默认0不强制)
	 * @return  json sting 
	 */
	public function act_getCarrierById(){
		$id		= isset($_REQUEST['id']) ? abs(intval($_REQUEST['id'])) : 0;
		$is_new	= isset($_REQUEST['is_new']) ? $_REQUEST['is_new'] : 0;
		if(!in_array($is_new,array(0,1))) {
			self::$errCode 	= 10001;
			self::$errMsg 	= '强制更新参数有误！';
			return false;
		}
		if(empty($id)) {
			self::$errCode 	= 307;
			self::$errMsg 	= '运输方式ID参数传递错误!';
			return false;
		}
		$cacheName 		= md5("trans_carrier_id_".$id);
		$memc_obj		= new Cache(C('CACHEGROUP'));
		$carrierInfo 	= $memc_obj->get_extral($cacheName);
		if(!empty($carrierInfo) && empty($is_new)) {
			return unserialize($carrierInfo);
		} else {
			$carrierInfo = TransOpenApiModel::getCarrierById($id);
			$isok 		 = $memc_obj->set_extral($cacheName, serialize($carrierInfo));
			if(!$isok) {
				self::$errCode	= 306;
				self::$errMsg	= 'memcache缓存出错!';
				//return false;
			}
			return $carrierInfo;
		}
	}
	
	/**
	 * TransOpenApiAct::act_getCarrierAbb()
	 * 获取运输方式列表并存入memcache
	 * @param int $is_new 是否强制更新(默认0不强制)
	 * @return  json string
	 */
	public function act_getCarrierAbb(){
		$is_new				= isset($_REQUEST['is_new']) ? $_REQUEST['is_new'] : 0;
		if(!in_array($is_new,array(0,1))) {
			self::$errCode 	= 10001;
			self::$errMsg 	= '强制更新参数有误！';
			return false;
		}		
		$cacheName 		= md5("trans_carrierAbb_list_".$type);
		$memc_obj		= new Cache(C('CACHEGROUP'));
		$carrierInfo 	= $memc_obj->get_extral($cacheName);
		if(!empty($carrierInfo) && empty($is_new)) {
			return unserialize($carrierInfo);
		} else {
			$carrierInfo = TransOpenApiModel::getCarrierAbb();
			$isok 		 = $memc_obj->set_extral($cacheName, serialize($carrierInfo));
			if(!$isok) {
				self::$errCode	= 306;
				self::$errMsg	= 'memcache缓存出错!';
				//return false;
			}
			return $carrierInfo;
		}
	}
	
	/**
	 * TransOpenApiAct::act_getCarrier()
	 * 获取运输方式列表并存入memcache
	 * @param int $type 0非快递，1快递，2全部默认
	 * @param int $is_new 是否强制更新(默认0不强制)
	 * @return  json string
	 */
	public function act_getCarrier(){
		$type	= isset($_REQUEST['type']) ? post_check($_REQUEST['type']) : 2;
		$is_new	= isset($_REQUEST['is_new']) ? $_REQUEST['is_new'] : 0;
		if(!in_array($is_new,array(0,1))) {
			self::$errCode 	= 10001;
			self::$errMsg 	= '强制更新参数有误！';
			return false;
		}
		if(!in_array($type, array('0','1','2'))) {
			self::$errCode 	= 307;
			self::$errMsg 	= 'type 参数传递错误!';
			return false;
		}
		$cacheName 		= md5("trans_carrier_list_".$type);
		$memc_obj		= new Cache(C('CACHEGROUP'));
		$carrierInfo 	= $memc_obj->get_extral($cacheName);
		if(!empty($carrierInfo) && empty($is_new)) {
			return unserialize($carrierInfo);
		} else {
			$carrierInfo = TransOpenApiModel::getCarrier($type);
			$isok 		 = $memc_obj->set_extral($cacheName, serialize($carrierInfo));
			if(!$isok) {
				self::$errCode	= 306;
				self::$errMsg	= 'memcache缓存出错!';
				//return false;
			}
			return $carrierInfo;
		}
	}
	
	/**
	 * TransOpenApiAct::act_getCarrierChannel()
	 * 获取某个运输方式的渠道信息并存入memcache
	 * @param int $carrierId
	 * @param int $chId
	 * @param int $is_new 是否强制更新（默认0不强制）
	 * @return  array 
	 */
	public function act_getCarrierChannel(){
		$carrierId	= isset($_REQUEST['carrierId']) ? post_check($_REQUEST['carrierId']) : 0;
		$channelId	= isset($_REQUEST['channelId']) ? post_check($_REQUEST['channelId']) : 0;
		$is_new		= isset($_REQUEST['is_new']) ? $_REQUEST['is_new'] : 0;
		if(!in_array($is_new,array(0,1))) {
			self::$errCode 	= 10001;
			self::$errMsg 	= '强制更新参数有误！';
			return false;
		}
		if($carrierId=="all") {
			$carrierId 	= 0;
		} else {
			$carrierId 	= abs(intval($_REQUEST['carrierId']));
		}
		$cacheName 		= md5("trans_channel_list_".$carrierId.$channelId);
		$memc_obj		= new Cache(C('CACHEGROUP'));
		$channelInfo 	= $memc_obj->get_extral($cacheName);
		if(!empty($channelInfo) && empty($is_new)) {
			return unserialize($channelInfo);
		} else {
			if(empty($carrierId)) {
				if($channelId) { 
					$channelInfo 	= TransOpenApiModel::getCarrierChannel(0, $channelId);
				} else {
					$channelInfo 	= TransOpenApiModel::getCarrierChannel();
				}
			} else {
				$channelInfo 		= TransOpenApiModel::getCarrierChannel($carrierId);
			}
			$isok 		 		= $memc_obj->set_extral($cacheName, serialize($channelInfo));
			if(!$isok) {
				self::$errCode	= 306;
				self::$errMsg	= 'memcache缓存出错!';
				//return false;
			}
			return $channelInfo;
		}
	}
	
	/**
	 * TransOpenApiAct::act_getPartition()
	 * 获取运输方式渠道分区列表并存入memcache
	 * @param int $carrierId 运输方式ID(选填)
	 * @param int $is_new 是否强制更新(默认0不强制)
	 * @param string $countryName 国家名(选填)
	 * @return  array
	 */
	public function act_getPartition(){
		$carrierId	= isset($_REQUEST['carrierId']) ? $_REQUEST['carrierId'] : 0;
		$is_new		= isset($_REQUEST['is_new']) ? $_REQUEST['is_new'] : 0;
		if(!in_array($is_new,array(0,1))) {
			self::$errCode 	= 10001;
			self::$errMsg 	= '强制更新参数有误！';
			return false;
		}
		if($carrierId == "all") {
			$carrierId 	= 0;
		} else {
			$carrierId 	= abs(intval($carrierId));
		}
		if(!is_numeric($carrierId)) {
			self::$errCode 	= 10000;
			self::$errMsg 	= '运输方式参数ID错误';
			return false;
		}
		$countryName	= isset($_REQUEST['countryName']) ? post_check($_REQUEST['countryName']) : '';
		$cacheName 		= md5("trans_partition_list_".$carrierId.$countryName);
		$memc_obj		= new Cache(C('CACHEGROUP'));
		$partitionInfo 	= $memc_obj->get_extral($cacheName);
		if(!empty($partitionInfo) && empty($is_new)) {
			return unserialize($partitionInfo);
		} else {
			$partitionInfo 		= TransOpenApiModel::getPartition($carrierId, $countryName);
			$isok 		 		= $memc_obj->set_extral($cacheName, serialize($partitionInfo));
			if(!$isok) {
				self::$errCode 	= 10001;
				self::$errMsg 	= 'memcache缓存出错!';
				//return false;
			}
			return $partitionInfo;
		}
	}
	
	/**
	 * TransOpenApiAct::act_getCountriesStandard()
	 * 获取全部或部分标准国家并存入mencache
	 * @param string $type ALL全部，CN中文，EN英文
	 * @param string $country 国家，默认空
	 * @param int $is_new 是否强制更新(默认0不强制)
	 * @return  array 
	 */
	public function act_getCountriesStandard(){
		$type		= isset($_REQUEST['type']) ? post_check($_REQUEST['type']) : "ALL";
		$country	= isset($_REQUEST['country']) ? post_check($_REQUEST['country']) : "";
		$is_new		= isset($_REQUEST['is_new']) ? $_REQUEST['is_new'] : 0;
		if(!in_array($is_new,array(0,1))) {
			self::$errCode	= 10001;
			self::$errMsg 	= '强制更新参数有误！';
			return false;
		}
		if(!in_array($type,array("ALL","EN","CN"))){
			self::$errCode 	= 309;
			self::$errMsg  	= '参数TYPE类型错误!';
			return false;
		}
		if($type == "ALL") $country = "";//防止重复CACHE
		$cacheName 		= md5("trans_countries_standard_{$type}{$country}");
		$memc_obj		= new Cache(C('CACHEGROUP'));
		$countriesInfo 	= $memc_obj->get_extral($cacheName);
		if(!empty($countriesInfo) && empty($is_new)) {
			return unserialize($countriesInfo);
		} else {
			if($type == "ALL") {
				$countriesInfo 	= TransOpenApiModel::getCountriesStandard();
			} else {
				$countriesInfo 	= TransOpenApiModel::getCountriesStandard($type, $country);
			}
			$isok				= $memc_obj->set_extral($cacheName, serialize($countriesInfo));
			if(!$isok) {
				self::$errCode	= 308;
				self::$errMsg 	= 'memcache缓存出错!';
				//return false;
			}
			return $countriesInfo;
		}
	}	
}
?>