<?php
/*
 *运输方式管理相关接口操作类(model)
 *@add by : linzhengxiang ,date : 20140528
 */
defined('WEB_PATH') ? '' : exit;
class InterfaceTranModel extends InterfaceModel {

	public function __construct(){
		parent::__construct();
	}

	/**
     * 获取所有渠道信息
	 * @return array
	 * @author lzx
     */
	public function getChannelList($carrierId='all'){
		$conf = $this->getRequestConf(__FUNCTION__);
		if (empty($conf)){
			return false;
		}
		$conf['carrierId'] = $carrierId;
		$result = callOpenSystem($conf);
		$data = json_decode($result,true);
		if ($data['errCode']>0) self::$errMsg[$data['errCode']] = "[{$data['errCode']}]{$data['errMsg']}";
		return $this->changeArrayKey($data['data']);
    }

    /**
     * 获取运输方式列表信息,填写正确的运输方式参数类型（0非快递，1快递，2全部）
	 * @return array
	 * @author lzx
     */
	public function getCarrierList($type){
		$conf = $this->getRequestConf(__FUNCTION__);
		if (empty($conf)){
			return false;
		}
		$conf['type'] = "$type";
		$result = callOpenSystem($conf);
		$data = json_decode($result,true);
		if ($data['errCode']>0) self::$errMsg[$data['errCode']] = "[{$data['errCode']}]{$data['errMsg']}";
		return $this->changeArrayKey($data['data']);
    }
	
	/**
     * 获取运输方式列表信息,填写正确的运输方式参数类型（0非快递，1快递，2全部）
	 * @return array
	 * @author lzx
     */
	public function getCarrierNameList($type, $flip = false){
		$CarrierList = $this->getCarrierList($type);
		$CarrierNameList = array();
		foreach($CarrierList as $value){
			$CarrierNameList[$value['id']] = $value['carrierNameCn'];
		}
		if($flip){
			$CarrierNameList = array_flip($CarrierNameList);
		}
		return $CarrierNameList;
    }
	
	/**
     * 获取运输方式列表信息,填写正确的运输方式参数类型（0非快递，1快递，2全部）
	 * @return array
	 * @author herman.xi 20140620
     */
	public function getCarrierNameById($transportId){
		$CarrierList = $this->getCarrierNameList(2);
		return $CarrierList[$transportId];
    }
	
	/**
     * 获取运输方式列表信息,填写正确的运输方式参数类型（0非快递，1快递，2全部）
	 * @return array
	 * @author herman.xi 20140701
     */
	public function getCarrierIdByName($CarrierName){
		$CarrierList = $this->getCarrierNameList(2, true);
		return $CarrierList[$CarrierName];
    }

    /**
     * 获取最优运输方式 (接口需要改造，必须传可选的运输方式数组，接口从该数组中选择最优的返回)
     * @param $countryName 国家
	 * @param $calcWeight 重量
     * @param $shipaddr 发货地址 默认为1，深圳
     * @param $zipCode 邮编，默认为空
     * @param $noShipId 中转地址ID，默认为空
     * @return array
	 * @author zqt
     */
	public function getBestShippingFee($countryName, $calcWeight, $shipaddr=1, $zipCode='', $noShipId=''){
		$conf = $this->getRequestConf(__FUNCTION__);
		if (empty($conf)){
			return false;
		}
		$conf['country'] = $countryName;
        $conf['weight'] = $calcWeight;
        $conf['shipAddId'] = $shipaddr;
        if(!empty($zipCode)){
            $conf['postCode'] = $zipCode;
        }
        if(!empty($noShipId)){
            $conf['noShipId'] = $noShipId;
        }
		$result = callOpenSystem($conf);
		$data = json_decode($result,true);
		if ($data['errCode']>0) self::$errMsg[$data['errCode']] = "[{$data['errCode']}]{$data['errMsg']}";
		return $this->changeArrayKey($data['data']);
    }

    /**
     * 获取固定运输方式
     * @param $transportId 运输方式Id
     * @param $countryName 国家
     * @param $calcWeight 重量
	 * @return array
	 * @author zqt
     */
	public function getFixShippingFee($transportId, $countryName, $calcWeight){
		$conf = $this->getRequestConf(__FUNCTION__);
		if (empty($conf)){
			return false;
		}
        $conf['carrierId'] = $transportId;
		$conf['country'] = $countryName;
        $conf['weight'] = $calcWeight;
		$result = callOpenSystem($conf);
		$data = json_decode($result,true);
		if ($data['errCode']>0) self::$errMsg[$data['errCode']] = "[{$data['errCode']}]{$data['errMsg']}";
		return $this->changeArrayKey($data['data']);
    }
	
	/**
     * 获取指定某些运输方式ids的最优运输方式列表
     * @param $transportId 运输方式Id
     * @param $countryName 国家
     * @param $calcWeight 重量
	 * @return array
	 * @author zqt
     */
	public function getBatchBestShippingFee($countryName, $calcWeight, $shipAddId=1, $postCode = '', $transitId = '', $noShipId = ''){
		$conf = $this->getRequestConf(__FUNCTION__);
		if (empty($conf)){
			return false;
		}
		$conf['shipAddId'] = $shipAddId;
        //$conf['carrierId'] = $transportId;
		$conf['country'] = $countryName;
        $conf['weight'] = $calcWeight;
		if($postCode){
			$conf['postCode'] = $postCode;
		}
		if($transitId){
			$conf['transitId'] = $transitId;
		}
		if($noShipId){
			$conf['noShipId'] = $noShipId;
		}
		$result = callOpenSystem($conf);
		$data = json_decode($result,true);
		if ($data['errCode']>0) self::$errMsg[$data['errCode']] = "[{$data['errCode']}]{$data['errMsg']}";
		return $this->changeArrayKey($data['data']);
    }
	
	/**
     * 获取指定某些运输方式ids的固定运输方式列表
     * @param $transportId 运输方式Id
     * @param $countryName 国家
     * @param $calcWeight 重量
	 * @return array
	 * @author zqt
     */
	public function getBatchFixShippingFee($carrierId, $countryName, $calcWeight, $postCode = '', $transitId = ''){
		$conf = $this->getRequestConf(__FUNCTION__);
		if (empty($conf)){
			return false;
		}
		$conf['carrierId'] = $carrierId;
		$conf['country'] = $countryName;
        $conf['weight'] = $calcWeight;
		if($postCode){
			$conf['postCode'] = $postCode;
		}
		if($transitId){
			$conf['transitId'] = $transitId;
		}
		$result = callOpenSystem($conf);
		$data = json_decode($result,true);
		if ($data['errCode']>0) self::$errMsg[$data['errCode']] = "[{$data['errCode']}]{$data['errMsg']}";
		return $this->changeArrayKey($data['data']);
    }
    
    /**
     * 获取指定某些运输方式ids的固定运输方式列表
     * @param $transportId 运输方式Id
     * @param $countryName 国家
     * @param $calcWeight 重量
	 * @return array
	 * @author zqt
     */
	public function getAllCountryList($type='ALL'){
		$conf = $this->getRequestConf(__FUNCTION__);
		if (empty($conf)){
			return false;
		}
        $conf['type'] = $type;
		$result = callOpenSystem($conf);
		$data = json_decode($result,true);
		if ($data['errCode']>0) self::$errMsg[$data['errCode']] = "[{$data['errCode']}]{$data['errMsg']}";
		return $this->changeArrayKey($data['data']);
    }
    
    /**
	 * 查询多个transportId的运费信息
	 * @param string $transportIdStr 运输方式id字符串，每个id用英文,隔开
     * @param number $weight 重量
     * @param string $country 国家
     * @param string $postCode 邮编？
     * @param int $transitId 转运中心？
	 * @return array 
	 * @author lzx
	 */
	public function getBatchTransportShipFee($transportIdStr, $weight, $country, $postCode='', $transitId=''){
		$conf = $this->getRequestConf(__FUNCTION__);
		if (empty($conf)){
			return false;
		}
		$conf['carrierId'] = $transportIdStr;
        $conf['weight'] = $weight;
        $conf['country'] = rawurlencode($country);
        $conf['postCode'] = $postCode;
        $conf['transitId'] = $transitId;
		$result = callOpenSystem($conf);
		$data = json_decode($result,true);
		if ($data['errCode']>0) self::$errMsg[$data['errCode']] = "[{$data['errCode']}]{$data['errMsg']}";
		return $this->changeArrayKey($data['data']);
	}
    
    /**
	 * 查询多个channelId的运费信息
	 * @param string $transportIdStr 运输方式id字符串，每个id用英文,隔开
     * @param number $weight 重量
     * @param string $country 国家
     * @param string $postCode 邮编？
     * @param int $transitId 转运中心？
	 * @return array 
	 * @author lzx
	 */
	public function getBatchChannelIdShipFee($channelIdStr, $weight, $country, $postCode='', $transitId=''){
		$conf = $this->getRequestConf(__FUNCTION__);
		if (empty($conf)){
			return false;
		}
		$conf['channelId'] = $channelIdStr;
        $conf['weight'] = $weight;
        $conf['country'] = rawurlencode($country);
        $conf['postCode'] = $postCode;
        $conf['transitId'] = $transitId;
		$result = callOpenSystem($conf);
		$data = json_decode($result,true);
		if ($data['errCode']>0) self::$errMsg[$data['errCode']] = "[{$data['errCode']}]{$data['errMsg']}";
		return $this->changeArrayKey($data['data']);
	}
    
}
?>