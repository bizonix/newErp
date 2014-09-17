<?php
/**
 * 类名：ShipfeeQueryModel
 * 功能：运输方式运费计算model层
 * 版本：1.0
 * 日期：2013/10/21
 * 作者：管拥军
 */

class ShipfeeQueryModel {
    public static $errCode = 0;
    public static $errMsg = 0;
    private $dbconn = null;
    
    //初始化
    public function __construct() {
        global $dbConn;
        $this->dbconn = $dbConn; 
    }
    
	/**
	 * ShipfeeQueryModel::getCarrierAllList()
	 * 获取所有运输方式列表
	 * @return array
	 */
    public function getCarrierAllList(){
        $sql 	= "SELECT id,carrierNameCn FROM trans_carrier WHERE is_delete = 0";
        $query	= $this->dbconn->query($sql);
		$res	= $this->dbconn->fetch_array_all($query);
		return $res;
	}
	
	/**
	 * ShipfeeQueryModel::getShipListByShipaddr()
	 * 获得某个发货地的发货方式列表
	 * @param string $shid 发货地址ID
	 * @param string $noShipId 排除的运输方式ID
	 * @return array
	 */
    public function getShipListByShipaddr($shid,$noShipId){
        $condition	= 1;
		if(!empty($noShipId)) $condition	.= " AND c.id NOT IN({$noShipId})";
		$sql    = "SELECT c.id,c.carrierNameCn,c.carrierNameEn,c.weightMin,c.weightMax FROM trans_address_carrier_relation as acr LEFT JOIN trans_carrier as c on acr.carrierId=c.id WHERE {$condition} AND acr.addressId=$shid and acr.is_delete=0 and c.is_delete=0";
        $query	= $this->dbconn->query($sql);
		$res	= $this->dbconn->fetch_array_all($query);
		return $res;
    }
    
    /**
	 * ShipfeeQueryModel::getAllShipAddrList()
	 * 获得所有发货地列表
	 * @return array
	 */
    public function getAllShipAddrList(){
        $sql 	= "SELECT * FROM trans_shipping_address WHERE is_delete=0";
        $query	= $this->dbconn->query($sql);
		$res	= $this->dbconn->fetch_array_all($query);
		return $res;
	}

	/**
	 * ShipfeeQueryModel::getStandardCountryName()
	 * 获得所有标准国家名称
	 * @return array
	 */
    public function getStandardCountryName(){
        $sql 	= "SELECT * FROM trans_countries_standard WHERE is_delete = 0 order by countryNameEn";
        $query	= $this->dbconn->query($sql);
		$res	= $this->dbconn->fetch_array_all($query);
		return $res;
	}
    
	/**
	 * ShipfeeQueryModel::getStdCountryNameById()
	 * 根据id获取标准国家名称
	 * @param int $id 标准国家ID
	 * @return array
	 */
    public function getStdCountryNameById($id){
        $sql 	= "SELECT * FROM trans_countries_standard WHERE id={$id} AND is_delete = 0";
        $query	= $this->dbconn->query($sql);
		$res	= $this->dbconn->fetch_array($query);
		return $res;
    }

	/**
	 * ShipfeeQueryModel::translateMinorityLangToStd()
	 * 小语种的国家名称转换为标准英文国家名称
	 * @param string $countryname 小语种国家
	 * @return array
	 */
    public function translateMinorityLangToStd($countryname){
        $countryname 	= mysql_real_escape_string($countryname);
        $sql 			= "SELECT * FROM trans_countries_small_comparison WHERE is_delete = 0 AND small_country = binary '$countryname'";
        return $row 	= $this->dbconn->fetch_first($sql);
    }
	
    /**
	 * ShipfeeQueryModel::translateStdCountryNameToShipCountryName()
	 * 标准国家名转换到运输方式的国家名称
	 * @param int $shipId 运输方式ID
	 * @param string $stdcountryname 标准国家名
	 * @return string
	 */
    public function translateStdCountryNameToShipCountryName($stdcountryname,$shipId){
        $stdcountryname = mysql_real_escape_string($stdcountryname);
        $sql 			= "SELECT carrier_country FROM trans_countries_carrier_comparison  WHERE is_delete = 0 AND countryName = binary '$stdcountryname' and carrierId = '{$shipId}' LIMIT 1";
        $query			= $this->dbconn->query($sql);
		$res			= $this->dbconn->fetch_array($query);
        if(empty($res)) {
            return $stdcountryname;
        } else {
            return $res['carrier_country'];
        }
    }
    
	/**
	 * ShipfeeQueryModel::getChannelInfo()
	 * 返回某个运输方式的所有渠道信息
	 * @param int $carrierid 运输方式ID
	 * @return array
	 */
    public function getChannelInfo($carrierid){ 
        $sql 	= "SELECT id,carrierId,channelName,channelAlias FROM trans_channels WHERE carrierId={$carrierid} AND is_enable = 1 AND is_delete = 0";
        $query	= $this->dbconn->query($sql);
		$res	= $this->dbconn->fetch_array_all($query);
		return $res;
    }	

	/**
	 * ShipfeeQueryModel::calculateShipfee()
	 * 根据指定运输方式和渠道别名信息来计算运费
	 * @param string $channelAlias 渠道别名
	 * @param float $weight 重量
	 * @param string $countryname 发往国家
	 * @param $data 额外的参数
	 * @return array
	 */
    public function calculateShipfee($channelAlias,$weight, $countryname ,$data=''){
		$channelAlias 	= trim($channelAlias);
        $function 		= 'cal_'.$channelAlias;
		return $this->$function($weight, $countryname, $data);
    }
    
	/**
	 * ShipfeeQueryModel::cal_yto_shenzhen()
	 * 圆通运费计算(中国)
	 * @param float $weight 重量
	 * @param string $countryname 发往地区
	 * @param $data 待定
	 * @return false or array
	 */
    public function cal_yto_shenzhen($weight, $countryname ,$data=''){
        $totalfee		= 0;
        $sql 			= "SELECT * FROM trans_freight_yto_shenzhen WHERE areaId = '{$countryname}' AND is_delete = 0 LIMIT 1";
		$query			= $this->dbconn->query($sql);
        $res			= $this->dbconn->fetch_array($query);
		if(!is_array($res)) return false;
		$firstWeight	= $res['firstWeight'];
		$price			= $res['price'];
		$nextPrice		= $res['nextPrice'];
		$noPrice		= $res['noPrice'];
        $rate			= $res['discount'];
		$handlefee		= $res['handlefee'];
		if($noPrice>$weight) {
			$shipfee	= $price + $handlefee;
		} else {
			$shipfee	= $price + ($weight-$firstWeight)*$nextPrice + $handlefee;
		}
		$totalfee		= $shipfee;
		if($rate > 0) $shipfee = $shipfee * $rate;
        $shipfee 		= round($shipfee,4);
		return array('discount'=>$rate, 'fee'=>$shipfee, 'totalfee'=>$totalfee);
    }
	
	/**
	 * ShipfeeQueryModel::cal_zto_shenzhen()
	 * 中通运费计算(中国)
	 * @param float $weight 重量
	 * @param string $countryname 发往地区
	 * @param $data 待定
	 * @return false or array
	 */
    public function cal_zto_shenzhen($weight, $countryname ,$data=''){
        $totalfee		= 0;
        $sql 			= "SELECT * FROM trans_freight_zto_shenzhen WHERE areaId = '{$countryname}' AND is_delete = 0 LIMIT 1";
		$query			= $this->dbconn->query($sql);
        $res			= $this->dbconn->fetch_array($query);
		if(!is_array($res)) return false;
		$firstWeight	= $res['firstWeight'];
		$price			= $res['price'];
		$nextPrice		= $res['nextPrice'];
		$noPrice		= $res['noPrice'];
        $rate			= $res['discount'];
		$handlefee		= $res['handlefee'];
		if($noPrice>$weight) {
			$shipfee	= $price + $handlefee;
		} else {
			$shipfee	= $price + ($weight-$firstWeight)*$nextPrice + $handlefee;
		}
		$totalfee		= $shipfee;
		if($rate > 0) $shipfee = $shipfee * $rate;
        $shipfee 		= round($shipfee,4);
		return array('discount'=>$rate, 'fee'=>$shipfee, 'totalfee'=>$totalfee);
    }
	
	/**
	 * ShipfeeQueryModel::cal_sto_shenzhen()
	 * 申通运费计算(中国)
	 * @param float $weight 重量
	 * @param string $countryname 发往地区
	 * @param $data 待定
	 * @return false or array
	 */
    public function cal_sto_shenzhen($weight, $countryname ,$data=''){
        $totalfee		= 0;
        $sql 			= "SELECT * FROM trans_freight_sto_shenzhen WHERE areaId = '{$countryname}' AND is_delete = 0 LIMIT 1";
		$query			= $this->dbconn->query($sql);
        $res			= $this->dbconn->fetch_array($query);
		if(!is_array($res)) return false;
		$firstWeight	= $res['firstWeight'];
		$price			= $res['price'];
		$nextPrice		= $res['nextPrice'];
		$noPrice		= $res['noPrice'];
        $rate			= $res['discount'];
		$handlefee		= $res['handlefee'];
		if($noPrice>$weight) {
			$shipfee	= $price + $handlefee;
		} else {
			$shipfee	= $price + ($weight-$firstWeight)*$nextPrice + $handlefee;
		}
		$totalfee		= $shipfee;
		if($rate > 0) $shipfee = $shipfee * $rate;
        $shipfee 		= round($shipfee,4);
		return array('discount'=>$rate, 'fee'=>$shipfee, 'totalfee'=>$totalfee);
    }
	
	/**
	 * ShipfeeQueryModel::cal_best_shenzhen()
	 * 汇通运费计算(中国)
	 * @param float $weight 重量
	 * @param string $countryname 发往地区
	 * @param $data 待定
	 * @return false or array
	 */
    public function cal_best_shenzhen($weight, $countryname ,$data=''){
        $totalfee		= 0;
        $sql 			= "SELECT * FROM trans_freight_best_shenzhen WHERE areaId = '{$countryname}' AND is_delete = 0 LIMIT 1";
		$query			= $this->dbconn->query($sql);
        $res			= $this->dbconn->fetch_array($query);
		if(!is_array($res)) return false;
		$firstWeight	= $res['firstWeight'];
		$price			= $res['price'];
		$nextPrice		= $res['nextPrice'];
		$noPrice		= $res['noPrice'];
        $rate			= $res['discount'];
		$handlefee		= $res['handlefee'];
		if($noPrice>$weight) {
			$shipfee	= $price + $handlefee;
		} else {
			$shipfee	= $price + ($weight-$firstWeight)*$nextPrice + $handlefee;
		}
		$totalfee		= $shipfee;
		if($rate > 0) $shipfee = $shipfee * $rate;
        $shipfee 		= round($shipfee,4);
		return array('discount'=>$rate, 'fee'=>$shipfee, 'totalfee'=>$totalfee);
    }
	
	/**
	 * ShipfeeQueryModel::cal_jym_shenzhen()
	 * 加运美运费计算(中国)
	 * @param float $weight 重量
	 * @param string $countryname 发往地区
	 * @param $data 待定
	 * @return false or array
	 */
    public function cal_jym_shenzhen($weight, $countryname ,$data=''){
        $totalfee		= 0;
        $sql 			= "SELECT * FROM trans_freight_jym_shenzhen WHERE areaId = '{$countryname}' AND is_delete = 0 LIMIT 1";
		$query			= $this->dbconn->query($sql);
        $res			= $this->dbconn->fetch_array($query);
		if(!is_array($res)) return false;
		$firstWeight	= $res['firstWeight'];
		$price			= $res['price'];
		$nextPrice		= $res['nextPrice'];
		$noPrice		= $res['noPrice'];
        $rate			= $res['discount'];
		$handlefee		= $res['handlefee'];
		if($noPrice>$weight) {
			$shipfee	= $price + $handlefee;
		} else {
			$shipfee	= $price + ($weight-$firstWeight)*$nextPrice + $handlefee;
		}
		$totalfee		= $shipfee;
		if($rate > 0) $shipfee = $shipfee * $rate;
        $shipfee 		= round($shipfee,4);
		return array('discount'=>$rate, 'fee'=>$shipfee, 'totalfee'=>$totalfee);
    }
	
	/**
	 * ShipfeeQueryModel::cal_yundaex_shenzhen()
	 * 韵达运费计算(中国)
	 * @param float $weight 重量
	 * @param string $countryname 发往地区
	 * @param $data 待定
	 * @return false or array
	 */
    public function cal_yundaex_shenzhen($weight, $countryname ,$data=''){
        $totalfee		= 0;
        $sql 			= "SELECT * FROM trans_freight_yundaex_shenzhen WHERE areaId = '{$countryname}' AND is_delete = 0 LIMIT 1";
		$query			= $this->dbconn->query($sql);
        $res			= $this->dbconn->fetch_array($query);
		if(!is_array($res)) return false;
		$firstWeight	= $res['firstWeight'];
		$price			= $res['price'];
		$nextPrice		= $res['nextPrice'];
		$noPrice		= $res['noPrice'];
        $rate			= $res['discount'];
		$handlefee		= $res['handlefee'];
		if($noPrice>$weight) {
			$shipfee	= $price + $handlefee;
		} else {
			$shipfee	= $price + ($weight-$firstWeight)*$nextPrice + $handlefee;
		}
		$totalfee		= $shipfee;
		if($rate > 0) $shipfee = $shipfee * $rate;
        $shipfee 		= round($shipfee,4);
		return array('discount'=>$rate, 'fee'=>$shipfee, 'totalfee'=>$totalfee);
    }
	
	/**
	 * ShipfeeQueryModel::cal_gto_shenzhen()
	 * 国通运费计算(中国)
	 * @param float $weight 重量
	 * @param string $countryname 发往地区
	 * @param $data 待定
	 * @return false or array
	 */
    public function cal_gto_shenzhen($weight, $countryname ,$data=''){
        $totalfee		= 0;
        $sql 			= "SELECT * FROM trans_freight_gto_shenzhen WHERE areaId = '{$countryname}' AND is_delete = 0 LIMIT 1";
		$query			= $this->dbconn->query($sql);
        $res			= $this->dbconn->fetch_array($query);
		if(!is_array($res)) return false;
		$firstWeight	= $res['firstWeight'];
		$price			= $res['price'];
		$nextPrice		= $res['nextPrice'];
		$noPrice		= $res['noPrice'];
        $rate			= $res['discount'];
		$handlefee		= $res['handlefee'];
		if($noPrice>$weight) {
			$shipfee	= $price + $handlefee;
		} else {
			$shipfee	= $price + ($weight-$firstWeight)*$nextPrice + $handlefee;
		}
		$totalfee		= $shipfee;
		if($rate > 0) $shipfee = $shipfee * $rate;
        $shipfee 		= round($shipfee,4);
		return array('discount'=>$rate, 'fee'=>$shipfee, 'totalfee'=>$totalfee);
    }
	
	/**
	 * ShipfeeQueryModel::cal_ups_calcfree()
	 * 海外仓UPS运费计算
	 * @param float $weight 重量
	 * @param string $countryname 待定
	 * @param array $data 邮编
	 * @return array
	 */
	public function cal_ups_calcfree($weight, $countryname ,$data=''){
        $shipfee     = 0;
		$rate	     = 0;
		$totalfee	 = 0;
		$zipCode     = substr($data['postCode'], 0, 3);//只需取邮编前3位数字
		$getZone     = "SELECT zone FROM trans_usa_zone_postcode WHERE is_delete = 0 AND zip_code like '%{$zipCode}%'";
		$getZone     = $this->dbconn->query($getZone);
		$getZone     = $this->dbconn->fetch_array($getZone);
		$zone      	 = $getZone['zone'];//邮编所属分区
		$weight_lbs  = ceil($weight / 0.4536);//kg转换成磅
		$getUpsCost  = "SELECT cost FROM trans_freight_ups_calcfree WHERE is_delete = 0 AND weight = '{$weight_lbs}' AND zone = '{$zone}' AND unit = 'lbs'";
		$getUpsCost  = $this->dbconn->query($getUpsCost);
		$getUpsCost  = $this->dbconn->fetch_array($getUpsCost);
		$shipfee     = $getUpsCost['cost'];//UPS运费
		$shipfee	 = empty($shipfee) ? 0 : $shipfee;
		$shipfee     = $shipfee*(1+0.07)+2.8; //添加 燃油附加费
        $totalfee	 = $shipfee;
		return array('discount'=>$rate, 'fee'=>$shipfee, 'totalfee'=>$totalfee);
    }
	
	/**
	 * ShipfeeQueryModel::cal_usps_calcfree()
	 * 海外仓USPS运费计算
	 * @param float $weight 重量
	 * @param string $countryname 待定
	 * @param array $data 邮编
	 * @return array
	 */
    public function cal_usps_calcfree($weight, $countryname='' ,$data=''){
        $shipfee 	= 0;
		$rate 		= 0;
		$totalfee	= 0;
		$zipCode  	= substr($data['postCode'], 0, 3);//只需取邮编前3位数字
		$getZone  	= "SELECT zone FROM trans_usa_zone_postcode WHERE is_delete = 0 AND zip_code like '%{$zipCode}%'";
		$getZone	= $this->dbconn->query($getZone);
		$getZone    = $this->dbconn->fetch_array($getZone);
		$zone       = $getZone['zone'];//邮编所属分区
		$weight_g  	= $weight * 1000;//kg转换成g;
		$weight_oz 	= ceil($weight_g / 28.35);//g转换成盎司
		$weight_lbs = ceil($weight / 0.4536);//kg转换成磅
		if($weight_oz <= 13) {//重量小于13盎司的运费
			$getUspsCost = "SELECT cost FROM trans_freight_usps_calcfree WHERE is_delete = 0 AND weight = '{$weight_oz}' AND unit = 'oz'";
			$getUspsCost = $this->dbconn->query($getUspsCost);
			$getUspsCost = $this->dbconn->fetch_array($getUspsCost);
			$shipfee     = $getUspsCost['cost'];
		} else {
			$getUspsCost = "SELECT cost FROM trans_freight_usps_calcfree WHERE is_delete = 0 AND weight = '{$weight_lbs}' AND zone = '{$zone}' AND unit = 'lbs'";
			$getUspsCost = $this->dbconn->query($getUspsCost);
			$getUspsCost = $this->dbconn->fetch_array($getUspsCost);
			$shipfee     = $getUspsCost['cost'];//USPS运费
		}
		$shipfee		 = empty($shipfee) ? 0 : $shipfee;
		$totalfee		 = $shipfee;
		return array('discount'=>$rate, 'fee'=>$shipfee, 'totalfee'=>$totalfee);
    }
	
	/**
	 * ShipfeeQueryModel::cal_ups_us()
	 * UPS美国专线运费计算
	 * @param float $weight 重量
	 * @param string $countryname 待定
	 * @param int $type 1经济型，2优先
	 * @param $data 待定
	 * @return array
	 */
	public function cal_ups_us($weight, $countryname, $data='', $type=1){
		$shipfee		= 0;
		$totalfee		= 0;
		$rate			= 0;
		$realWeight		= 0;
		if($weight > 20) {
			$realWeight	= $weight;
			$weight		= ceil(floatval($weight));
		}
		if(!in_array($countryname,array('United States','US','USA'))) return false;
		if($weight < 21) {
			$sql		= "SELECT * FROM trans_freight_ups_us WHERE is_delete = 0 AND type = {$type} AND {$weight} > min_weight AND {$weight} <= max_weight LIMIT 1";
		} else {
			$sql		= "SELECT * FROM trans_freight_ups_us WHERE is_delete = 0 AND type = {$type} AND {$weight} >= min_weight AND {$weight} <= max_weight LIMIT 1";
		}
		$query			= $this->dbconn->query($sql);
		$res			= $this->dbconn->fetch_array($query);
		if(count($res) > 0) {
			$shipfee	= floatval($res['price']);
			if($weight > 20) $shipfee	= $shipfee * $realWeight;
			$fuelcosts	= floatval($res['fuelcosts']);
			$vat		= floatval($res['vat']);
			if($fuelcosts > 0) $shipfee	= $shipfee*(1+$fuelcosts);
			if($vat > 0) $shipfee		= $shipfee*(1+$vat);
			$shipfee	= round($shipfee, 4);
			$totalfee	= $shipfee;
		}
		return array('discount'=>$rate, 'fee'=>$shipfee, 'totalfee'=>$totalfee);
	}
	
	/**
	 * ShipfeeQueryModel::cal_ups_uk()
	 * UPS英国专线运费计算
	 * @param float $weight 重量
	 * @param string $countryname 待定
	 * @param int $type 1经济型，2优先
	 * @param $data 待定
	 * @return array
	 */
	public function cal_ups_uk($weight, $countryname, $data='', $type=1){
		$shipfee		= 0;
		$totalfee		= 0;
		$rate			= 0;
		$realWeight		= 0;
		if($weight > 20) {
			$realWeight	= $weight;
			$weight		= ceil(floatval($weight));
		}
		if(!in_array($countryname,array('United Kingdom','UK'))) return false;
		if($weight < 21) {
			$sql		= "SELECT * FROM trans_freight_ups_uk WHERE is_delete = 0 AND type = {$type} AND {$weight} > min_weight AND {$weight} <= max_weight LIMIT 1";
		} else {
			$sql		= "SELECT * FROM trans_freight_ups_uk WHERE is_delete = 0 AND type = {$type} AND {$weight} >= min_weight AND {$weight} <= max_weight LIMIT 1";
		}
		$query			= $this->dbconn->query($sql);
		$res			= $this->dbconn->fetch_array($query);
		if(count($res) > 0) {
			$shipfee	= floatval($res['price']);
			if($weight > 20) $shipfee	= $shipfee * $realWeight;
			$fuelcosts	= floatval($res['fuelcosts']);
			$vat		= floatval($res['vat']);
			if($fuelcosts > 0) $shipfee	= $shipfee*(1+$fuelcosts);
			if($vat > 0) $shipfee		= $shipfee*(1+$vat);
			$shipfee	= round($shipfee, 4);
			$totalfee	= $shipfee;
		}
		return array('discount'=>$rate, 'fee'=>$shipfee, 'totalfee'=>$totalfee);
	}
	
	/**
	 * ShipfeeQueryModel::cal_ups_fr()
	 * UPS法国专线运费计算
	 * @param float $weight 重量
	 * @param string $countryname 待定
	 * @param int $type 1经济型，2优先
	 * @param $data 待定
	 * @return array
	 */
	public function cal_ups_fr($weight, $countryname, $data='', $type=1){
		$shipfee		= 0;
		$totalfee		= 0;
		$rate			= 0;
		$realWeight		= 0;
		if($weight > 20) {
			$realWeight	= $weight;
			$weight		= ceil(floatval($weight));
		}
		if(!in_array($countryname,array('France','FR'))) return false;
		if($weight < 21) {
			$sql		= "SELECT * FROM trans_freight_ups_fr WHERE is_delete = 0 AND type = {$type} AND {$weight} > min_weight AND {$weight} <= max_weight LIMIT 1";
		} else {
			$sql		= "SELECT * FROM trans_freight_ups_fr WHERE is_delete = 0 AND type = {$type} AND {$weight} >= min_weight AND {$weight} <= max_weight LIMIT 1";
		}
		$query			= $this->dbconn->query($sql);
		$res			= $this->dbconn->fetch_array($query);
		if(count($res) > 0) {
			$shipfee	= floatval($res['price']);
			if($weight > 20) $shipfee	= $shipfee * $realWeight;
			$fuelcosts	= floatval($res['fuelcosts']);
			$vat		= floatval($res['vat']);
			if($fuelcosts > 0) $shipfee	= $shipfee*(1+$fuelcosts);
			if($vat > 0) $shipfee		= $shipfee*(1+$vat);
			$shipfee	= round($shipfee, 4);
			$totalfee	= $shipfee;
		}
		return array('discount'=>$rate, 'fee'=>$shipfee, 'totalfee'=>$totalfee);
	}
	
	/**
	 * ShipfeeQueryModel::cal_ups_ger()
	 * UPS德国专线运费计算
	 * @param float $weight 重量
	 * @param string $countryname 待定
	 * @param int $type 1经济型，2优先
	 * @param $data 待定
	 * @return array
	 */
	public function cal_ups_ger($weight, $countryname, $data='', $type=1){
		$shipfee		= 0;
		$totalfee		= 0;
		$rate			= 0;
		$realWeight		= 0;
		if($weight > 20) {
			$realWeight	= $weight;
			$weight		= ceil(floatval($weight));
		}
		if(!in_array($countryname,array('Germany','GER'))) return false;
		if($weight < 21) {
			$sql		= "SELECT * FROM trans_freight_ups_ger WHERE is_delete = 0 AND type = {$type} AND {$weight} > min_weight AND {$weight} <= max_weight LIMIT 1";
		} else {
			$sql		= "SELECT * FROM trans_freight_ups_ger WHERE is_delete = 0 AND type = {$type} AND {$weight} >= min_weight AND {$weight} <= max_weight LIMIT 1";
		}
		$query			= $this->dbconn->query($sql);
		$res			= $this->dbconn->fetch_array($query);
		if(count($res) > 0) {
			$shipfee	= floatval($res['price']);
			if($weight > 20) $shipfee	= $shipfee * $realWeight;
			$fuelcosts	= floatval($res['fuelcosts']);
			$vat		= floatval($res['vat']);
			if($fuelcosts > 0) $shipfee	= $shipfee*(1+$fuelcosts);
			if($vat > 0) $shipfee		= $shipfee*(1+$vat);
			$shipfee	= round($shipfee, 4);
			$totalfee	= $shipfee;
		}
		return array('discount'=>$rate, 'fee'=>$shipfee, 'totalfee'=>$totalfee);
	}
	
	/**
	 * ShipfeeQueryModel::cal_hkpostsf_hk()
	 * 香港小包平邮运费计算
	 * @param float $weight 重量
	 * @param string $countryname 发往国家
	 * @param $data 待定
	 * @return false or array
	 */
    public function cal_hkpostsf_hk($weight, $countryname ,$data=''){
        $totalfee	= 0;
		$arealist 	= array();
		$maxWeight	= 0;
		$chAlias	= 'hkpostsf_hk';
		$res		= array();
		$res		= TransOpenApiModel::cacheCarrierInfoByChannelAli("trans_channels", "channelAlias='{$chAlias}'", "{$chAlias}_carrier", 86400, 0);
        $maxWeight	= !empty($res['weightMax']) ? floatval($res['weightMax']) : 0;
		if(!empty($maxWeight)) {
			if($weight>$maxWeight) return false;
		}
		$arealist	= TransOpenApiModel::cacheTableInfo('trans_freight_hkpostsf_hk', '1', 'cal_hkpostsf_hk', 86400, 0);
        if(empty($arealist)) return false;
		$arearow 	= array();
        foreach($arealist as $value) {
            $countrys 		= explode(',', $value['countrys']);
            $countrys 		= array_map(array($this, 'callback_trimspace'), $countrys); //去除空格
            if(in_array($countryname, $countrys)) { //找到国家
                $arearow 	= $value;
                break;
            }
        }
        if(empty($arearow)){
            return false;
        }
        //计算运费
        $rate			= $arearow['discount'] ? $arearow['discount'] : 0;
        $exrate			= $arearow['exchange_rate'] ? $arearow['exchange_rate'] : 0;
		$kg				= $arearow['firstweight'];
		$handlefee		= $arearow['handlefee'];
		$zgTranFee		= $arearow['zgTranFee'] * $weight;
		$shipfee		= $kg * $weight + $handlefee;
		if($exrate > 0) $shipfee = $shipfee * $exrate;
		$shipfee 		+= $zgTranFee;
		$totalfee		= $shipfee;
		if($rate > 0) $shipfee = $shipfee * $rate;
        $shipfee 		= round($shipfee,4);
		return array('discount'=>$rate, 'fee'=>$shipfee, 'totalfee'=>$totalfee);
    }
	
	/**
	 * ShipfeeQueryModel::cal_hkpostrg_hk()
	 * 香港小包挂号运费计算
	 * @param float $weight 重量
	 * @param string $countryname 发往国家
	 * @param $data 待定
	 * @return false or array
	 */
	public function cal_hkpostrg_hk($weight, $countryname ,$data=''){
        $totalfee	= 0;
		$arealist 	= array();
		$maxWeight	= 0;
		$chAlias	= 'hkpostrg_hk';
		$res		= array();
		$res		= TransOpenApiModel::cacheCarrierInfoByChannelAli("trans_channels", "channelAlias='{$chAlias}'", "{$chAlias}_carrier", 86400, 0);
        $maxWeight	= !empty($res['weightMax']) ? floatval($res['weightMax']) : 0;
		if(!empty($maxWeight)) {
			if($weight>$maxWeight) return false;
		}
		$arealist	= TransOpenApiModel::cacheTableInfo('trans_freight_hkpostrg_hk', '1', 'cal_hkpostrg_hk', 86400, 0);
        if(empty($arealist)) return false;
        $arearow 	= array();
        foreach($arealist as $value) {
            $countrys 		= explode(',', $value['countrys']);
            $countrys 		= array_map(array($this, 'callback_trimspace'), $countrys);//去除空格
            if(in_array($countryname, $countrys)) {  //找到国家
                $arearow 	= $value;
                break;
            }
        }
        if(empty($arearow)) return false;
        //计算运费
        $rate			= $arearow['discount']?$arearow['discount']:1;
        $exrate			= $arearow['exchange_rate']?$arearow['exchange_rate']:1;
		$kg				= $arearow['firstweight'];
		$handlefee		= $arearow['handlefee'];
		$zgTranFee		= $arearow['zgTranFee'] * $weight;
		$shipfee		= $kg * $weight + $handlefee;
		if($exrate > 0) $shipfee = $shipfee * $exrate;
		$shipfee 		+= $zgTranFee;
		$totalfee		= $shipfee;
		if($rate > 0) $shipfee = $shipfee * $rate;
        $shipfee 		= round($shipfee, 4);
		return array('discount'=>$rate, 'fee'=>$shipfee, 'totalfee'=>$totalfee);
    }
	
	/**
	 * ShipfeeQueryModel::cal_sg_dhl_gm_py()
	 * 新加坡DHL GM(平邮)运费计算
	 * @param float $weight 重量
	 * @param string $countryname 发往国家
	 * @param $data 待定
	 * @return false or array
	 */
    public function  cal_sg_dhl_gm_py($weight, $countryname, $data){
        $totalfee	= 0;
		$arealist 	= array();
		$maxWeight	= 0;
		$chAlias	= 'sg_dhl_gm_py';
		$res		= array();
		$res		= TransOpenApiModel::cacheCarrierInfoByChannelAli("trans_channels", "channelAlias='{$chAlias}'", "{$chAlias}_carrier", 600, 0);
        $maxWeight	= !empty($res['weightMax']) ? floatval($res['weightMax']) : 0;
		if(!empty($maxWeight)) {
			if($weight>$maxWeight) return false;
		}
		$arealist	= TransOpenApiModel::cacheTableInfo('trans_freight_sg_dhl_gm_py', '1', 'cal_sg_dhl_gm_py', 86400, 0);
        if(empty($arealist)) return false;
        $arearow 	= array();
        foreach($arealist as $value) {
            $countrys		= explode(',', $value['countrys']);
            $countrys 		= array_map(array($this, 'callback_trimspace'), $countrys); //去空格
            if(in_array($countryname, $countrys)) { //找到国家
                $arearow 	= $value;
                break;
            }
        }
        if(empty($arearow)) return false;
        //计算运费
		$paNum			= isset($data['paNum']) ? $data['paNum'] : 1; //默认包裹数量1
		$exRates		= TransOpenApiModel::cacheExRateInfo(array('HKD','SGD'), array('CNY'), 'hsRate', 7200, 0);
		$hkRate			= round(floatval($exRates['HKD/CNY']),4);
		$sgRate			= round(floatval($exRates['SGD/CNY']),4);
		if($hkRate<=0) return array('discount'=>0, 'fee'=>0, 'totalfee'=>0, 'exRate'=>array("sgRate"=>0,"hkRate"=>0));				
        $rate			= $arearow['discount'] ? $arearow['discount'] : 1; //折扣 默认不打折
		$paTranFee		= $arearow['paTranFee'] * $sgRate ; //包裹运输费
		$paFee			= $arearow['paFee'] * $sgRate; //包裹处理费
		$delFee			= $arearow['delFee'] * $sgRate; //目的地派送费
		//$clsFee			= $arearow['clsFee'] * $sgRate; //清关费屏蔽掉
		$clsFee			= 0; //清关费
		$zgTranFee		= $arearow['zgTranFee']; //中港运输费
		$airFee			= $arearow['airFee'] * $hkRate; //空运费
		$otherFee		= $arearow['otherFee']; //其它费用
		$shipfee		= $paTranFee * $weight + $paFee * $paNum + $delFee * $weight + $clsFee * $paNum + $zgTranFee * $weight + $airFee * $weight + $otherFee;
		$totalfee		= $shipfee;
		if($rate > 0) $shipfee = $shipfee * $rate;
        $shipfee 		= round($shipfee, 4);
		return array('discount'=>$rate, 'fee'=>$shipfee, 'totalfee'=>$totalfee, 'exRate'=>array("sgRate"=>$sgRate,"hkRate"=>$hkRate));
    }
	
	/**
	 * ShipfeeQueryModel::cal_sg_dhl_gm_gh()
	 * 新加坡DHL GM(挂号)运费计算
	 * @param float $weight 重量
	 * @param string $countryname 发往国家
	 * @param $data 待定
	 * @return false or array
	 */
    public function  cal_sg_dhl_gm_gh($weight, $countryname, $data){
        $totalfee	= 0;
		$arealist 	= array();
		$maxWeight	= 0;
		$chAlias	= 'sg_dhl_gm_gh';
		$res		= array();
		$res		= TransOpenApiModel::cacheCarrierInfoByChannelAli("trans_channels", "channelAlias='{$chAlias}'", "{$chAlias}_carrier", 600, 0);
        $maxWeight	= !empty($res['weightMax']) ? floatval($res['weightMax']) : 0;
		if(!empty($maxWeight)) {
			if($weight>$maxWeight) return false;
		}
		$arealist	= TransOpenApiModel::cacheTableInfo('trans_freight_sg_dhl_gm_gh', '1', 'cal_sg_dhl_gm_gh', 86400, 0);
        if(empty($arealist)) return false;
        $arearow 	= array();
        foreach($arealist as $value) {
            $countrys 		= explode(',', $value['countrys']);
            $countrys 		= array_map(array($this, 'callback_trimspace'), $countrys); //去空格
            if(in_array($countryname, $countrys)) { //找到国家
                $arearow 	= $value;
                break;
            }
        }
        if(empty($arearow)) return false;
        //计算运费
		$paNum			= isset($data['paNum']) ? $data['paNum'] : 1; //默认包裹数量1
		$exRates		= TransOpenApiModel::cacheExRateInfo(array('HKD','SGD'), array('CNY'), 'hsRate', 7200, 0);
		$hkRate			= round(floatval($exRates['HKD/CNY']),4);
		$sgRate			= round(floatval($exRates['SGD/CNY']),4);
		if($hkRate<=0) return array('discount'=>0, 'fee'=>0, 'totalfee'=>0, 'exRate'=>array("sgRate"=>0,"hkRate"=>0));
        $rate			= $arearow['discount'] ? $arearow['discount'] : 1; //折扣 默认不打折
		$paTranFee		= $arearow['paTranFee'] * $sgRate ; //包裹运输费
		$paFee			= $arearow['paFee'] * $sgRate; //包裹处理费
		$delFee			= $arearow['delFee'] * $sgRate; //目的地派送费
		//$clsFee			= $arearow['clsFee'] * $sgRate; //清关费屏蔽掉
		$clsFee			= 0; //清关费
		$zgTranFee		= $arearow['zgTranFee']; //中港运输费
		$airFee			= $arearow['airFee'] * $hkRate; //空运费
		$otherFee		= $arearow['otherFee']; //其它费用
		$shipfee		= $paTranFee * $weight + $paFee * $paNum + $delFee * $weight + $clsFee * $paNum + $zgTranFee * $weight + $airFee * $weight + $otherFee;
		$totalfee		= $shipfee;
		if($rate > 0) $shipfee = $shipfee * $rate;
        $shipfee 		= round($shipfee, 4);
		return array('discount'=>$rate, 'fee'=>$shipfee, 'totalfee'=>$totalfee, 'exRate'=>array("sgRate"=>$sgRate,"hkRate"=>$hkRate));
    }
	
	/**
	 * ShipfeeQueryModel::cal_ruishi_xb_py()
	 * 瑞士小包(平邮)运费计算
	 * @param float $weight 重量
	 * @param string $countryname 发往国家
	 * @param $data 待定
	 * @return false or array
	 */
    public function  cal_ruishi_xb_py($weight, $countryname, $data){
        $totalfee	= 0;
		$arealist 	= array();
		$maxWeight	= 0;
		$chAlias	= 'ruishi_xb_py';
		$res		= array();
		$res		= TransOpenApiModel::cacheCarrierInfoByChannelAli("trans_channels", "channelAlias='{$chAlias}'", "{$chAlias}_carrier", 600, 0);
        $maxWeight	= !empty($res['weightMax']) ? floatval($res['weightMax']) : 0;
		if(!empty($maxWeight)) {
			if($weight>$maxWeight) return false;
		}
		$arealist	= TransOpenApiModel::cacheTableInfo('trans_freight_ruishi_xb_py', '1', 'cal_ruishi_xb_py', 86400, 0);
        if(empty($arealist)) return false;
        $arearow 	= array();
        foreach($arealist as $value) {
            $countrys 		= explode(',', $value['countries']);
            $countrys 		= array_map(array($this, 'callback_trimspace'), $countrys); //去空格
            if(in_array($countryname, $countrys)) { //找到国家
                $arearow 	= $value;
                break;
            }
        }
        if(empty($arearow)) return false;
        //计算运费
		$exRates		= TransOpenApiModel::cacheExRateInfo(array('HKD'), array('CNY'), 'hkRate', 7200, 0);
		$hkRate			= round(floatval($exRates['HKD/CNY']),4);
		if($hkRate <= 0) return array('discount'=>0, 'fee'=>0, 'totalfee'=>0, 'level'=>"", 'exRate'=>array("hkRate"=>0));				
        $rate			= $arearow['discount'] ? $arearow['discount'] : 1; //折扣 默认不打折
		$paTranFee		= $arearow['unitPrice'] * $hkRate ; //包裹运输费
		//$hdFee			= $arearow['handlefee'] * $hkRate; //挂号费
		$zgTranFee		= $arearow['zgTranFee'] * $hkRate; //中港运输费
		$level			= $arearow['level']; //级别
		$shipfee		= $paTranFee * $weight + $zgTranFee * $weight;
		$totalfee		= $shipfee;
		if($rate > 0) $shipfee = $shipfee * $rate;
        $shipfee 		= round($shipfee, 4);
		return array('discount'=>$rate, 'fee'=>$shipfee, 'totalfee'=>$totalfee, 'level'=>$level, 'exRate'=>array("hkRate"=>$hkRate));
    }
	
	/**
	 * ShipfeeQueryModel::cal_ruishi_xb_gh()
	 * 瑞士小包(挂号)运费计算
	 * @param float $weight 重量
	 * @param string $countryname 发往国家
	 * @param $data 待定
	 * @return false or array
	 */
    public function  cal_ruishi_xb_gh($weight, $countryname, $data){
        $totalfee	= 0;
		$arealist 	= array();
		$maxWeight	= 0;
		$chAlias	= 'ruishi_xb_gh';
		$res		= array();
		$res		= TransOpenApiModel::cacheCarrierInfoByChannelAli("trans_channels", "channelAlias='{$chAlias}'", "{$chAlias}_carrier", 600, 0);
        $maxWeight	= !empty($res['weightMax']) ? floatval($res['weightMax']) : 0;
		if(!empty($maxWeight)) {
			if($weight>$maxWeight) return false;
		}
		$arealist	= TransOpenApiModel::cacheTableInfo('trans_freight_ruishi_xb_gh', '1', 'cal_ruishi_xb_gh', 86400, 0);
        if(empty($arealist)) return false;
        $arearow 	= array();
        foreach($arealist as $value) {
            $countrys 		= explode(',', $value['countries']);
            $countrys 		= array_map(array($this, 'callback_trimspace'), $countrys); //去空格
            if(in_array($countryname, $countrys)) { //找到国家
                $arearow 	= $value;
                break;
            }
        }
        if(empty($arearow)) return false;
        //计算运费
		$exRates		= TransOpenApiModel::cacheExRateInfo(array('HKD'), array('CNY'), 'hkRate', 7200, 0);
		$hkRate			= round(floatval($exRates['HKD/CNY']),4);
		if($hkRate <= 0) return array('discount'=>0, 'fee'=>0, 'totalfee'=>0, 'level'=>"", 'exRate'=>array("hkRate"=>0));				
        $rate			= $arearow['discount'] ? $arearow['discount'] : 1; //折扣 默认不打折
		$paTranFee		= $arearow['unitPrice'] * $hkRate ; //包裹运输费
		$hdFee			= $arearow['handlefee'] * $hkRate; //挂号费
		$zgTranFee		= $arearow['zgTranFee'] * $hkRate; //中港运输费
		$level			= $arearow['level']; //级别
		$shipfee		= $paTranFee * $weight + $zgTranFee * $weight + $hdFee;
		$totalfee		= $shipfee;
		if($rate > 0) $shipfee = $shipfee * $rate;
        $shipfee 		= round($shipfee, 4);
		return array('discount'=>$rate, 'fee'=>$shipfee, 'totalfee'=>$totalfee, 'level'=>$level, 'exRate'=>array("hkRate"=>$hkRate));
    }
	
	/**
	 * ShipfeeQueryModel::cal_bilishi_xb_py()
	 * 比利时小包平邮运费计算
	 * @param float $weight 重量
	 * @param string $countryname 发往国家
	 * @param $data 待定
	 * @return false or array
	 */
    public function  cal_bilishi_xb_py($weight, $countryname, $data){
        $totalfee	= 0;
		$arealist 	= array();
		$maxWeight	= 0;
		$chAlias	= 'bilishi_xb_py';
		$res		= array();
		$res		= TransOpenApiModel::cacheCarrierInfoByChannelAli("trans_channels", "channelAlias='{$chAlias}'", "{$chAlias}_carrier", 600, 0);
        $maxWeight	= !empty($res['weightMax']) ? floatval($res['weightMax']) : 0;
		if(!empty($maxWeight)) {
			if($weight>$maxWeight) return false;
		}
		$arealist	= TransOpenApiModel::cacheTableInfo('trans_freight_bilishi_xb_py', '1', 'cal_bilishi_xb_py', 86400, 0);
        if(empty($arealist)) return false;
        $arearow 	= array();
        foreach($arealist as $value) {
            $countrys 		= explode(',', $value['countries']);
            if(in_array($countryname, $countrys)) {
                $arearow 	= $value;
                break;
            }
        }
        if(empty($arearow)) return false;
        //计算运费
		$exRates		= TransOpenApiModel::cacheExRateInfo(array('EUR'), array('CNY'), 'eurRate', 7200, 0);
		$eurRate		= round(floatval($exRates['EUR/CNY']),4);
		if($eurRate <= 0) return array('discount'=>0, 'fee'=>0, 'totalfee'=>0, 'exRate'=>array("eurRate"=>0));	
        $rate			= $arearow['discount']?$arearow['discount']:1;
		$kg				= $arearow['unitPrice'] * $eurRate;
		$handlefee		= $arearow['handlefee'] * $eurRate;
		$shipfee		= $kg * $weight + $handlefee;
		$totalfee		= $shipfee;
		if($rate > 0) $shipfee = $shipfee * $rate;
        $shipfee 		= round($shipfee, 4);
		return array('discount'=>$rate, 'fee'=>$shipfee, 'totalfee'=>$totalfee, 'exRate'=>array("eurRate"=>$eurRate));
    }
    
	/**
	 * ShipfeeQueryModel::cal_bilishi_xb_gh()
	 * 比利时小包挂号运费计算
	 * @param float $weight 重量
	 * @param string $countryname 发往国家
	 * @param $data 待定
	 * @return false or array
	 */
    public function  cal_bilishi_xb_gh($weight, $countryname, $data){
        $totalfee	= 0;
		$arealist 	= array();
		$maxWeight	= 0;
		$chAlias	= 'bilishi_xb_gh';
		$res		= array();
		$res		= TransOpenApiModel::cacheCarrierInfoByChannelAli("trans_channels", "channelAlias='{$chAlias}'", "{$chAlias}_carrier", 600, 0);
        $maxWeight	= !empty($res['weightMax']) ? floatval($res['weightMax']) : 0;
		if(!empty($maxWeight)) {
			if($weight>$maxWeight) return false;
		}
		$arealist	= TransOpenApiModel::cacheTableInfo('trans_freight_bilishi_xb_gh', '1', 'cal_bilishi_xb_gh', 86400, 0);
		if(empty($arealist)) return false;
        $arearow 	= array();
        foreach($arealist as $value) {
            $countrys 		= explode(',', $value['countries']);
            $countrys 		= array_map(array($this, 'callback_trimspace'), $countrys);
            if(in_array($countryname, $countrys)) {
                $arearow 	= $value;
                break;
            }
        }
        if(empty($arearow)) return false;
        //计算运费
		$exRates		= TransOpenApiModel::cacheExRateInfo(array('EUR'), array('CNY'), 'eurRate', 7200, 0);
		$eurRate		= round(floatval($exRates['EUR/CNY']),4);
		if($eurRate <= 0) return array('discount'=>0, 'fee'=>0, 'totalfee'=>0, 'exRate'=>array("eurRate"=>0));	
        $rate			= $arearow['discount'] ? $arearow['discount'] : 0;
		$kg				= $arearow['unitPrice'] * $eurRate;
		$handlefee		= $arearow['handlefee'] * $eurRate;
		$shipfee		= $kg * $weight + $handlefee;
		$totalfee		= $shipfee;
		if($rate > 0) $shipfee = $shipfee * $rate;
        $shipfee 		= round($shipfee, 4);
		return array('discount'=>$rate, 'fee'=>$shipfee, 'totalfee'=>$totalfee, 'exRate'=>array("eurRate"=>$eurRate));
	}
	
	/**
	 * ShipfeeQueryModel::cal_usps_first_class()
	 * 赛维USPS运费计算
	 * @param float $weight 重量
	 * @param string $countryname 发往国家
	 * @param $data 待定
	 * @return false or array
	 */
    public function  cal_usps_first_class($weight, $countryname, $data){
        $totalfee	= 0;
		$arealist 	= array();
		$postCode	= $data['postCode'];
		$transitId	= $data['transitId'];
		if(!in_array(strtolower($countryname),array('united states','usa'))) return false;
		// if(empty($postCode) || strlen($postCode)!=3) return false;
		if(empty($transitId) || !is_numeric($transitId)) return false;
        $sql		= "SELECT zone FROM trans_usa_zone_postcode WHERE transitId = '{$transitId}' AND is_delete = 0 LIMIT 1";
		$query		= $this->dbconn->query($sql);
		$res		= $this->dbconn->fetch_array($query);
		if(empty($res)) return false;
		$zone		= $res['zone'];
        $sql		= "SELECT * FROM trans_freight_usps_first_class WHERE minWeight <= '{$weight}' AND maxWeight >= '{$weight}' AND is_delete = 0";
		$query		= $this->dbconn->query($sql);
        $arearow	= $this->dbconn->fetch_array($query);
		if(empty($arearow)) return false;
        //计算运费
		$exRates		= TransOpenApiModel::cacheExRateInfo(array('USD','HKD'), array('CNY'), 'uhRate', 7200, 0);
		$usdRate		= round(floatval($exRates['USD/CNY']),4);
		$hkdRate		= round(floatval($exRates['HKD/CNY']),4);
		if($usdRate <= 0 || $hkdRate <= 0) return array('discount'=>0, 'fee'=>0, 'totalfee'=>0, 'exRate'=>array("usdRate"=>0,"hkdRate"=>0));	
        $rate			= $arearow['discount'] ? $arearow['discount'] : 0;
		$cost			= $arearow['cost'] * $usdRate;
		// $handlefee		= $arearow['handlefee'] * $usdRate; //待定
		// $fuelCost		= $arearow['fuelCost']; //待定
		$zgTranFee		= $arearow['zgTranFee'] * $weight;
		$airFee			= ($arearow['airFee'] * $weight) * $hkdRate;
		$clsFee			= $arearow['clsFee'] * $usdRate;
		$shipfee		= $cost + $zgTranFee + $airFee + $clsFee;
		$totalfee		= $shipfee;
		if($rate > 0) $shipfee = $shipfee * $rate;
        $shipfee 		= round($shipfee, 4);
		return array('discount'=>$rate, 'fee'=>$shipfee, 'totalfee'=>$totalfee, 'exRate'=>array("usdRate"=>$usdRate,"hkdRate"=>$hkdRate));
	}
	
	/**
	 * ShipfeeQueryModel::cal_ups_ground_commercia()
	 * 赛维UPS运费计算
	 * @param float $weight 重量
	 * @param string $countryname 发往国家
	 * @param $data 待定
	 * @return false or array
	 */
    public function  cal_ups_ground_commercia($weight, $countryname, $data){
		$totalfee	= 0;
		$arealist 	= array();
		$postCode	= $data['postCode'];
		$transitId	= $data['transitId'];
		if(!in_array(strtolower($countryname),array('united states','usa'))) return false;
		if(empty($postCode) || strlen($postCode)!=3) return false;
		if(empty($transitId) || !is_numeric($transitId)) return false;
        $sql		= "SELECT zone FROM trans_usa_zone_postcode WHERE transitId = '{$transitId}' AND zip_code LIKE '%{$postCode}%' AND is_delete = 0 LIMIT 1";
		$query		= $this->dbconn->query($sql);
		$res		= $this->dbconn->fetch_array($query);
		if(empty($res)) return false;
		$zone		= $res['zone'];
        $sql		= "SELECT * FROM trans_freight_ups_ground_commercia WHERE zone = '{$zone}' AND minWeight <= '{$weight}' AND maxWeight >= '{$weight}' AND is_delete = 0";
		$query		= $this->dbconn->query($sql);
        $arearow	= $this->dbconn->fetch_array($query);
		if(empty($arearow)) return false;
        //计算运费
		$exRates		= TransOpenApiModel::cacheExRateInfo(array('USD','HKD'), array('CNY'), 'uhRate', 7200, 0);
		$usdRate		= round(floatval($exRates['USD/CNY']),4);
		$hkdRate		= round(floatval($exRates['HKD/CNY']),4);
		if($usdRate <= 0 || $hkdRate <= 0) return array('discount'=>0, 'fee'=>0, 'totalfee'=>0, 'exRate'=>array("usdRate"=>0,"hkdRate"=>0));	
        $rate			= $arearow['discount'] ? $arearow['discount'] : 0;
		$fuelCost		= $arearow['fuelCost'];
		$cost			= ($arearow['cost'] * (1 + $fuelCost)) * $usdRate;
		$handlefee		= $arearow['handlefee'] * $usdRate;
		$zgTranFee		= $arearow['zgTranFee'] * $weight;
		$airFee			= ($arearow['airFee'] * $weight) * $hkdRate;
		$clsFee			= $arearow['clsFee'] * $usdRate;
		$shipfee		= $cost + $handlefee + $zgTranFee + $airFee + $clsFee;
		$totalfee		= $shipfee;
		if($rate > 0) $shipfee = $shipfee * $rate;
        $shipfee 		= round($shipfee, 4);
		return array('discount'=>$rate, 'fee'=>$shipfee, 'totalfee'=>$totalfee, 'exRate'=>array("usdRate"=>$usdRate,"hkdRate"=>$hkdRate));
	}
	
	/**
	 * ShipfeeQueryModel::cal_sv_sure_post()
	 * 赛维SurePost运费计算
	 * @param float $weight 重量
	 * @param string $countryname 发往国家
	 * @param $data 待定
	 * @return false or array
	 */
    public function  cal_sv_sure_post($weight, $countryname, $data){
        $totalfee	= 0;
		$arealist 	= array();
		$postCode	= $data['postCode'];
		$transitId	= $data['transitId'];
		if(!in_array(strtolower($countryname),array('united states','usa'))) return false;
		if(empty($postCode) || strlen($postCode)!=3) return false;
		if(empty($transitId) || !is_numeric($transitId)) return false;
        $sql		= "SELECT zone FROM trans_usa_zone_postcode WHERE transitId = '{$transitId}' AND zip_code LIKE '%{$postCode}%' AND is_delete = 0 LIMIT 1";
		$query		= $this->dbconn->query($sql);
		$res		= $this->dbconn->fetch_array($query);
		if(empty($res)) return false;
		$zone		= $res['zone'];
        $sql		= "SELECT * FROM trans_freight_sv_sure_post WHERE zone = '{$zone}' AND minWeight <= '{$weight}' AND maxWeight >= '{$weight}' AND is_delete = 0";
		$query		= $this->dbconn->query($sql);
        $arearow	= $this->dbconn->fetch_array($query);
		if(empty($arearow)) return false;
        //计算运费
		$exRates		= TransOpenApiModel::cacheExRateInfo(array('USD','HKD'), array('CNY'), 'uhRate', 7200, 0);
		$usdRate		= round(floatval($exRates['USD/CNY']),4);
		$hkdRate		= round(floatval($exRates['HKD/CNY']),4);
		if($usdRate <= 0 || $hkdRate <= 0) return array('discount'=>0, 'fee'=>0, 'totalfee'=>0, 'exRate'=>array("usdRate"=>0,"hkdRate"=>0));	
        $rate			= $arearow['discount'] ? $arearow['discount'] : 0;
		$fuelCost		= $arearow['fuelCost'];
		$cost			= ($arearow['cost'] * (1 + $fuelCost)) * $usdRate;
		$handlefee		= $arearow['handlefee'] * $usdRate;
		$zgTranFee		= $arearow['zgTranFee'] * $weight;
		$airFee			= ($arearow['airFee'] * $weight) * $hkdRate;
		$clsFee			= $arearow['clsFee'] * $usdRate;
		$shipfee		= $cost + $handlefee + $zgTranFee + $airFee + $clsFee;
		$totalfee		= $shipfee;
		if($rate > 0) $shipfee = $shipfee * $rate;
        $shipfee 		= round($shipfee, 4);
		return array('discount'=>$rate, 'fee'=>$shipfee, 'totalfee'=>$totalfee, 'exRate'=>array("usdRate"=>$usdRate,"hkdRate"=>$hkdRate));
	}
	
	/**
	 * ShipfeeQueryModel::cal_ruston_packet_py()
	 * 俄速通小包(平邮)运费计算
	 * @param float $weight 重量
	 * @param string $countryname 发往国家
	 * @param $data 待定
	 * @return false or array
	 */
    public function  cal_ruston_packet_py($weight, $countryname, $data){
        $totalfee	= 0;
		$arealist 	= array();
		$arealist	= TransOpenApiModel::cacheTableInfo('trans_freight_ruston_packet_py', '1', 'cal_ruston_packet_py', 86400, 0);
        if(empty($arealist)) return false;
        $arearow 	= array();
        foreach($arealist as $value) {
            $countrys 		= explode(',', $value['countrys']);
            $countrys 		= array_map(array($this, 'callback_trimspace'), $countrys); //去空格
            if(in_array($countryname, $countrys)) { //找到国家
                $arearow 	= $value;
                break;
            }
        }
        if(empty($arearow)) return false;
        //计算运费
        $rate			= $arearow['discount'] ? $arearow['discount'] : 1;
		$price			= $arearow['price'];
		$maxWeight		= $arearow['maxWeight'];
		$handlefee		= $arearow['handlefee'];
		if($weight >= $maxWeight) return false;
		$shipfee		= $price * $weight + $handlefee;
		$totalfee		= $shipfee;
		if($rate > 0) $shipfee = $shipfee * $rate;
        $shipfee 		= round($shipfee, 4);
		return array('discount'=>$rate, 'fee'=>$shipfee, 'totalfee'=>$totalfee);
    }
	
	/**
	 * ShipfeeQueryModel::cal_ruston_packet_gh()
	 * 俄速通小包(挂号)运费计算
	 * @param float $weight 重量
	 * @param string $countryname 发往国家
	 * @param $data 待定
	 * @return false or array
	 */
    public function  cal_ruston_packet_gh($weight, $countryname, $data){
        $totalfee	= 0;
		$arealist 	= array();
		$arealist	= TransOpenApiModel::cacheTableInfo('trans_freight_ruston_packet_gh', '1', 'cal_ruston_packet_gh', 86400, 0);
        if(empty($arealist)) return false;
        $arearow 	= array();
        foreach($arealist as $value) {
            $countrys 		= explode(',', $value['countrys']);
            $countrys 		= array_map(array($this, 'callback_trimspace'), $countrys); //去空格
            if(in_array($countryname, $countrys)) { //找到国家
                $arearow 	= $value;
                break;
            }
        }
        if(empty($arearow)) return false;
        //计算运费
        $rate			= $arearow['discount'] ? $arearow['discount'] : 1;
		$price			= $arearow['price'];
		$maxWeight		= $arearow['maxWeight'];
		$handlefee		= $arearow['handlefee'];
		if($weight >= $maxWeight) return false;
		$shipfee		= $price * $weight + $handlefee;
		$totalfee		= $shipfee;
		if($rate > 0) $shipfee = $shipfee * $rate;
        $shipfee 		= round($shipfee, 4);
		return array('discount'=>$rate, 'fee'=>$shipfee, 'totalfee'=>$totalfee);
    }
	
	/**
	 * ShipfeeQueryModel::cal_ruston_large_package()
	 * 俄速通大包运费计算
	 * @param float $weight 重量
	 * @param string $countryname 发往国家
	 * @param $data 待定
	 * @return false or array
	 */
    public function  cal_ruston_large_package($weight, $countryname, $data){
        $totalfee	= 0;
		$arealist 	= array();
		$arealist	= TransOpenApiModel::cacheTableInfo('trans_freight_ruston_large_package', '1', 'cal_ruston_large_package', 86400, 0);
        if(empty($arealist)) return false;
        $arearow 	= array();
        foreach($arealist as $value) {
            $countrys 		= explode(',', $value['countrys']);
            $countrys 		= array_map(array($this, 'callback_trimspace'), $countrys); //去空格
            if(in_array($countryname, $countrys)) { //找到国家
                $arearow 	= $value;
                break;
            }
        }
        if(empty($arearow)) return false;
        //计算运费
        $rate			= $arearow['discount'] ? $arearow['discount'] : 1;
		$price			= $arearow['price'];
		$nextPrice		= $arearow['nextPrice'];
		$minWeight		= $arearow['minWeight'];
		$handlefee		= $arearow['handlefee'];
		if($weight < $minWeight) return false;
		$shipfee		= $price + (ceil($weight-1)*$nextPrice) + $handlefee;
		$totalfee		= $shipfee;
		if($rate > 0) $shipfee = $shipfee * $rate;
        $shipfee 		= round($shipfee, 4);
		return array('discount'=>$rate, 'fee'=>$shipfee, 'totalfee'=>$totalfee);
    }
	
	/**
	 * ShipfeeQueryModel::cal_aoyoubao_py()
	 * 澳邮宝小包(平邮)运费计算
	 * @param float $weight 重量
	 * @param string $countryname 发往国家
	 * @param $data 待定
	 * @return false or array
	 */
    public function  cal_aoyoubao_py($weight, $countryname, $data){
        $totalfee	= 0;
		$arealist 	= array();
		$maxWeight	= 0;
		$chAlias	= 'aoyoubao_py';
		$res		= array();
		$res		= TransOpenApiModel::cacheCarrierInfoByChannelAli("trans_channels", "channelAlias='{$chAlias}'", "{$chAlias}_carrier", 600, 0);
        $maxWeight	= !empty($res['weightMax']) ? floatval($res['weightMax']) : 0;
		if(!empty($maxWeight)) {
			if($weight>$maxWeight) return false;
		}
        $arealist	= TransOpenApiModel::cacheTableInfo('trans_freight_aoyoubao_py', '1', 'cal_aoyoubao_py', 86400, 0);
        if(empty($arealist)) return false;
        $arearow 	= array();
        foreach($arealist as $value) {
            $countrys 		= explode(',', $value['countrys']);
            $countrys 		= array_map(array($this, 'callback_trimspace'), $countrys); //去空格
            if(in_array($countryname, $countrys)) { //找到国家
                $arearow 	= $value;
                break;
            }
        }
        if(empty($arearow)) return false;
        //计算运费
        $rate			= $arearow['discount'] ? $arearow['discount'] : 1;
		$price			= $arearow['price'];
		$handlefee		= $arearow['handlefee'];
		$shipfee		= $price * $weight;
		$totalfee		= $shipfee;
		if($rate > 0) $shipfee = $shipfee * $rate;
        $shipfee 		= round($shipfee, 4);
		return array('discount'=>$rate, 'fee'=>$shipfee, 'totalfee'=>$totalfee);
    }
	
	/**
	 * ShipfeeQueryModel::cal_aoyoubao_gh()
	 * 澳邮宝小包(挂号)运费计算
	 * @param float $weight 重量
	 * @param string $countryname 发往国家
	 * @param $data 待定
	 * @return false or array
	 */
    public function  cal_aoyoubao_gh($weight, $countryname, $data){
        $totalfee	= 0;
		$arealist 	= array();
		$maxWeight	= 0;
		$chAlias	= 'aoyoubao_gh';
		$res		= array();
		$res		= TransOpenApiModel::cacheCarrierInfoByChannelAli("trans_channels", "channelAlias='{$chAlias}'", "{$chAlias}_carrier", 600, 0);
        $maxWeight	= !empty($res['weightMax']) ? floatval($res['weightMax']) : 0;
		if(!empty($maxWeight)) {
			if($weight>$maxWeight) return false;
		}
        $arealist	= TransOpenApiModel::cacheTableInfo('trans_freight_aoyoubao_gh', '1', 'cal_aoyoubao_gh', 86400, 0);
        if(empty($arealist)) return false;
        $arearow 	= array();
        foreach($arealist as $value) {
            $countrys 		= explode(',', $value['countrys']);
            $countrys 		= array_map(array($this, 'callback_trimspace'), $countrys); //去空格
            if(in_array($countryname, $countrys)) { //找到国家
                $arearow 	= $value;
                break;
            }
        }
        if(empty($arearow)) return false;
        //计算运费
        $rate			= $arearow['discount'] ? $arearow['discount'] : 1;
		$price			= $arearow['price'];
		$handlefee		= $arearow['handlefee'];
		$shipfee		= $price * $weight + $handlefee;
		$totalfee		= $shipfee;
		if($rate > 0) $shipfee = $shipfee * $rate;
        $shipfee 		= round($shipfee, 4);
		return array('discount'=>$rate, 'fee'=>$shipfee, 'totalfee'=>$totalfee);
    }
	
	/**
	 * ShipfeeQueryModel::cal_zhengzhou_xb_py()
	 * 郑州小包平邮运费计算
	 * @param float $weight 重量
	 * @param string $countryname 发往国家
	 * @param $data 待定
	 * @return false or array
	 */
    public function  cal_zhengzhou_xb_py($weight, $countryname, $data){
        $totalfee	= 0;
		$arealist 	= array();
		$maxWeight	= 0;
		$chAlias	= 'zhengzhou_xb_py';
		$res		= array();
		$res		= TransOpenApiModel::cacheCarrierInfoByChannelAli("trans_channels", "channelAlias='{$chAlias}'", "{$chAlias}_carrier", 600, 0);
        $maxWeight	= !empty($res['weightMax']) ? floatval($res['weightMax']) : 0;
		if(!empty($maxWeight)) {
			if($weight>$maxWeight) return false;
		}
        $arealist	= TransOpenApiModel::cacheTableInfo('trans_freight_zhengzhou_xb_py', '1', 'cal_zhengzhou_xb_py', 86400, 0);
        if(empty($arealist)) return false;
        $arearow 	= array();
        foreach($arealist as $value) {
            $countrys 		= explode(',', $value['countries']);
            if(in_array($countryname, $countrys)) {
                $arearow 	= $value;
                break;
            }
        }
        if(empty($arearow)) return false;
        //计算运费
        $rate			= $arearow['discount']?$arearow['discount']:1;
		$kg				= $arearow['unitPrice'];
		$handlefee		= $arearow['handlefee'];
		$shipfee		= $kg * $weight + $handlefee;
		$totalfee		= $shipfee;
		if($rate > 0) $shipfee = $shipfee * $rate;
        $shipfee 		= round($shipfee, 4);
		return array('discount'=>$rate, 'fee'=>$shipfee, 'totalfee'=>$totalfee);
    }
    
	/**
	 * ShipfeeQueryModel::cal_zhengzhou_xb_gh()
	 * 郑州小包挂号运费计算
	 * @param float $weight 重量
	 * @param string $countryname 发往国家
	 * @param $data 待定
	 * @return false or array
	 */
    public function  cal_zhengzhou_xb_gh($weight, $countryname, $data){
        $totalfee	= 0;
		$arealist 	= array();
		$maxWeight	= 0;
		$chAlias	= 'zhengzhou_xb_gh';
		$res		= array();
		$res		= TransOpenApiModel::cacheCarrierInfoByChannelAli("trans_channels", "channelAlias='{$chAlias}'", "{$chAlias}_carrier", 600, 0);
        $maxWeight	= !empty($res['weightMax']) ? floatval($res['weightMax']) : 0;
		if(!empty($maxWeight)) {
			if($weight>$maxWeight) return false;
		}
        $arealist	= TransOpenApiModel::cacheTableInfo('trans_freight_zhengzhou_xb_gh', '1', 'cal_zhengzhou_xb_gh', 86400, 0);
		if(empty($arealist)) return false;
        $arearow 	= array();
        foreach($arealist as $value) {
            $countrys 		= explode(',', $value['countries']);
            $countrys 		= array_map(array($this, 'callback_trimspace'), $countrys);
            if(in_array($countryname, $countrys)) {
                $arearow 	= $value;
                break;
            }
        }
        if(empty($arearow)) return false;
        //计算运费
        $rate			= $arearow['discount'] ? $arearow['discount'] : 0;
		$kg				= $arearow['unitPrice'];
		$handlefee		= $arearow['handlefee'];
		$shipfee		= $kg * $weight + $handlefee;
		$totalfee		= $shipfee;
		if($rate > 0) $shipfee = $shipfee * $rate;
        $shipfee 		= round($shipfee, 4);
		return array('discount'=>$rate, 'fee'=>$shipfee, 'totalfee'=>$totalfee);
	}
    
	/**
	 * ShipfeeQueryModel::cal_cpsf_shenzhen()
	 * 中国邮政平邮深圳运费计算
	 * @param float $weight 重量
	 * @param string $countryname 发往国家
	 * @param $data 待定
	 * @return false or array
	 */
    public function  cal_cpsf_shenzhen($weight, $countryname, $data){
        $totalfee	= 0;
		$arealist 	= array();
		$maxWeight	= 0;
		$chAlias	= 'cpsf_shenzhen';
		$res		= array();
		$res		= TransOpenApiModel::cacheCarrierInfoByChannelAli("trans_channels", "channelAlias='{$chAlias}'", "{$chAlias}_carrier", 600, 0);
        $maxWeight	= !empty($res['weightMax']) ? floatval($res['weightMax']) : 0;
		if(!empty($maxWeight)) {
			if($weight>$maxWeight) return false;
		}
		$arealist	= TransOpenApiModel::cacheTableInfo('trans_freight_cpsf_shenzhen', '1', 'cal_cpsf_shenzhen', 86400, 0);
        if(empty($arealist)) return false;
        $arearow 	= array();
        foreach($arealist as $value) {
            $countrys 		= explode(',', $value['countries']);
            $countrys 		= array_map(array($this, 'callback_trimspace'), $countrys); //去空格
            if(in_array($countryname, $countrys)) { //找到国家
                $arearow 	= $value;
                break;
            }
        }
        if(empty($arearow)) return false;
        //计算运费
        $rate			= $arearow['discount']?$arearow['discount']:1;
		$kg				= $arearow['firstweight'];
		$handlefee		= $arearow['handlefee'];
		$shipfee		= $kg * $weight + $handlefee;
		$totalfee		= $shipfee;
		if($rate > 0) $shipfee = $shipfee * $rate;
        $shipfee 		= round($shipfee, 4);
		return array('discount'=>$rate, 'fee'=>$shipfee, 'totalfee'=>$totalfee);
    }
    
	/**
	 * ShipfeeQueryModel::cal_cpsf_fujian_quanzhou()
	 * 中国邮政平邮福建泉州运费计算
	 * @param float $weight 重量
	 * @param string $countryname 发往国家
	 * @param $data 待定
	 * @return false or array
	 */
    public function  cal_cpsf_fujian_quanzhou($weight, $countryname, $data){
        $totalfee	= 0;
		$arealist 	= array();
		$maxWeight	= 0;
		$chAlias	= 'cpsf_fujian_quanzhou';
		$res		= array();
		$res		= TransOpenApiModel::cacheCarrierInfoByChannelAli("trans_channels", "channelAlias='{$chAlias}'", "{$chAlias}_carrier", 600, 0);
        $maxWeight	= !empty($res['weightMax']) ? floatval($res['weightMax']) : 0;
		if(!empty($maxWeight)) {
			if($weight>$maxWeight) return false;
		}
        $arealist	= TransOpenApiModel::cacheTableInfo('trans_freight_cpsf_fujian_quanzhou', '1', 'cal_cpsf_fujian_quanzhou', 86400, 0);
        if(empty($arealist)) return false;
        $arearow 	= array();
        foreach($arealist as $value) {
            $countrys 		= explode(',', $value['countries']);
            if(in_array($countryname, $countrys)) {
                $arearow 	= $value;
                break;
            }
        }
        if(empty($arearow)) return false;
        //计算运费
        $rate			= $arearow['discount']?$arearow['discount']:1;
		$kg				= $arearow['unitPrice'];
		$handlefee		= $arearow['handlefee'];
		$shipfee		= $kg * $weight + $handlefee;
		$totalfee		= $shipfee;
		if($rate > 0) $shipfee = $shipfee * $rate;
        $shipfee 		= round($shipfee, 4);
		return array('discount'=>$rate, 'fee'=>$shipfee, 'totalfee'=>$totalfee);
    }
	
	/**
	 * ShipfeeQueryModel::cal_cpsf_fujian_zhangpu()
	 * 中国邮政平邮福建漳浦运费计算
	 * @param float $weight 重量
	 * @param string $countryname 发往国家
	 * @param $data 待定
	 * @return false or array
	 */
    public function  cal_cpsf_fujian_zhangpu($weight, $countryname, $data){
        $totalfee	= 0;
		$arealist 	= array();
		$maxWeight	= 0;
		$chAlias	= 'cpsf_fujian_zhangpu';
		$res		= array();
		$res		= TransOpenApiModel::cacheCarrierInfoByChannelAli("trans_channels", "channelAlias='{$chAlias}'", "{$chAlias}_carrier", 600, 0);
        $maxWeight	= !empty($res['weightMax']) ? floatval($res['weightMax']) : 0;
		if(!empty($maxWeight)) {
			if($weight>$maxWeight) return false;
		}
        $arealist	= TransOpenApiModel::cacheTableInfo('trans_freight_cpsf_fujian_zhangpu', '1', 'cal_cpsf_fujian_zhangpu', 86400, 0);
        if(empty($arealist)) return false;
        $arearow 	= array();
        foreach($arealist as $value) {
            $countrys 		= explode(',', $value['countries']);
            if(in_array($countryname, $countrys)) {
                $arearow 	= $value;
                break;
            }
        }
        if(empty($arearow)) return false;
        //计算运费
        $rate			= $arearow['discount']?$arearow['discount']:1;
		$kg				= $arearow['unitPrice'];
		$handlefee		= $arearow['handlefee'];
		$shipfee		= $kg * $weight + $handlefee;
		$totalfee		= $shipfee;
		if($rate > 0) $shipfee = $shipfee * $rate;
        $shipfee 		= round($shipfee, 4);
		return array('discount'=>$rate, 'fee'=>$shipfee, 'totalfee'=>$totalfee);
    }
    
	/**
	 * ShipfeeQueryModel::cal_cprg_fujian_zhangpu()
	 * 中国邮政挂号福建漳浦运费计算
	 * @param float $weight 重量
	 * @param string $countryname 发往国家
	 * @param $data 待定
	 * @return false or array
	 */
    public function  cal_cprg_fujian_zhangpu($weight, $countryname, $data){
        $totalfee	= 0;
		$arealist 	= array();
		$maxWeight	= 0;
		$chAlias	= 'cprg_fujian_zhangpu';
		$res		= array();
		$res		= TransOpenApiModel::cacheCarrierInfoByChannelAli("trans_channels", "channelAlias='{$chAlias}'", "{$chAlias}_carrier", 600, 0);
        $maxWeight	= !empty($res['weightMax']) ? floatval($res['weightMax']) : 0;
		if(!empty($maxWeight)) {
			if($weight>$maxWeight) return false;
		}
        $arealist	= TransOpenApiModel::cacheTableInfo('trans_freight_cprg_fujian_zhangpu', '1', 'cal_cprg_fujian_zhangpu', 86400, 0);
		if(empty($arealist)) return false;
        $arearow 	= array();
        foreach($arealist as $value) {
            $countrys 		= explode(',', $value['countries']);
            $countrys 		= array_map(array($this, 'callback_trimspace'), $countrys);
            if(in_array($countryname, $countrys)) {
                $arearow 	= $value;
                break;
            }
        }
        if(empty($arearow)) return false;
        //计算运费
        $rate			= $arearow['discount'] ? $arearow['discount'] : 0;
		$kg				= $arearow['unitPrice'];
		$handlefee		= $arearow['handlefee'];
		$shipfee		= $kg * $weight + $handlefee;
		$totalfee		= $shipfee;
		if($rate > 0) $shipfee = $shipfee * $rate;
        $shipfee 		= round($shipfee, 4);
		return array('discount'=>$rate, 'fee'=>$shipfee, 'totalfee'=>$totalfee);
	}
	
	/**
	 * ShipfeeQueryModel::cal_cprg_fujian_quanzhou()
	 * 中国邮政挂号福建泉州运费计算
	 * @param float $weight 重量
	 * @param string $countryname 发往国家
	 * @param $data 待定
	 * @return false or array
	 */
    public function  cal_cprg_fujian_quanzhou($weight, $countryname, $data){
        $totalfee	= 0;
		$arealist 	= array();
		$maxWeight	= 0;
		$chAlias	= 'cprg_fujian_quanzhou';
		$res		= array();
		$res		= TransOpenApiModel::cacheCarrierInfoByChannelAli("trans_channels", "channelAlias='{$chAlias}'", "{$chAlias}_carrier", 600, 0);
        $maxWeight	= !empty($res['weightMax']) ? floatval($res['weightMax']) : 0;
		if(!empty($maxWeight)) {
			if($weight>$maxWeight) return false;
		}
        $arealist	= TransOpenApiModel::cacheTableInfo('trans_freight_cprg_fujian_quanzhou', '1', 'cal_cprg_fujian_quanzhou', 86400, 0);
		if(empty($arealist)) return false;
        $arearow 	= array();
        foreach($arealist as $value) {
            $countrys 		= explode(',', $value['countries']);
            $countrys 		= array_map(array($this, 'callback_trimspace'), $countrys);
            if(in_array($countryname, $countrys)) {
                $arearow 	= $value;
                break;
            }
        }
        if(empty($arearow)) return false;
        //计算运费
        $rate			= $arearow['discount'] ? $arearow['discount'] : 0;
		$kg				= $arearow['unitPrice'];
		$handlefee		= $arearow['handlefee'];
		$shipfee		= $kg * $weight + $handlefee;
		$totalfee		= $shipfee;
		if($rate > 0) $shipfee = $shipfee * $rate;
        $shipfee 		= round($shipfee, 4);
		return array('discount'=>$rate, 'fee'=>$shipfee, 'totalfee'=>$totalfee);
	}
	
	/**
	 * ShipfeeQueryModel::cal_cprg_shenzhen()
	 * 中国邮政挂号深圳运费计算
	 * @param float $weight 重量
	 * @param string $countryname 发往国家
	 * @param $data 待定
	 * @return false or array
	 */
    public function  cal_cprg_shenzhen($weight, $countryname, $data){
        $totalfee	= 0;
		$arealist 	= array();
		$maxWeight	= 0;
		$chAlias	= 'cprg_shenzhen';
		$res		= array();
		$res		= TransOpenApiModel::cacheCarrierInfoByChannelAli("trans_channels", "channelAlias='{$chAlias}'", "{$chAlias}_carrier", 600, 0);
        $maxWeight	= !empty($res['weightMax']) ? floatval($res['weightMax']) : 0;
		if(!empty($maxWeight)) {
			if($weight>$maxWeight) return false;
		}
        $arealist	= TransOpenApiModel::cacheTableInfo('trans_freight_cprg_shenzhen', '1', 'cal_cprg_shenzhen', 86400, 0);
		if(empty($arealist)) return false;
        $arearow 	= array();
        foreach($arealist as $value) {
            $countrys 		= explode(',', $value['countries']);
            $countrys 		= array_map(array($this, 'callback_trimspace'), $countrys);
            if(in_array($countryname, $countrys)) {
                $arearow 	= $value;
                break;
            }
        }
        if(empty($arearow)) return false;
        //计算运费
        $rate			= $arearow['discount'] ? $arearow['discount'] : 0;
		$kg				= $arearow['unitPrice'];
		$handlefee		= $arearow['handlefee'];
		$shipfee		= $kg * $weight + $handlefee;
		$totalfee		= $shipfee;
		if($rate > 0) $shipfee = $shipfee * $rate;
        $shipfee 		= round($shipfee, 4);
		return array('discount'=>$rate, 'fee'=>$shipfee, 'totalfee'=>$totalfee);
	}
    
	/**
	 * ShipfeeQueryModel::cal_ems_shenzhen()
	 * 中国邮政EMS深圳运费计算
	 * @param float $weight 重量
	 * @param string $countryname 发往国家
	 * @param $data 待定
	 * @return false or array
	 */
    public function  cal_ems_shenzhen($weight, $countryname, $data){
        $totalfee	= 0;
		$arealist 	= array();
		$maxWeight	= 0;
		$chAlias	= 'ems_shenzhen';
		$res		= array();
		$res		= TransOpenApiModel::cacheCarrierInfoByChannelAli("trans_channels", "channelAlias='{$chAlias}'", "{$chAlias}_carrier", 600, 0);
        $maxWeight	= !empty($res['weightMax']) ? floatval($res['weightMax']) : 0;
		if(!empty($maxWeight)) {
			if($weight>$maxWeight) return false;
		}
        $arealist	= TransOpenApiModel::cacheTableInfo('trans_freight_ems_shenzhen', '1', 'cal_ems_shenzhen', 86400, 0);
        if(empty($arealist)) return false;
        $arearow	= array();
        foreach($arealist as $value) {
            $countrys 		= explode(',', $value['countrys']);
            $countrys 		= array_map(array($this, 'callback_trimspace'), $countrys);
            if(in_array($countryname, $countrys)) {
                $arearow 	= $value;
                break;
            }
        }
        if(empty($arearow)) return false;
		//计算运费
        $firstweight	= $arearow['firstweight'];
		$nextweight		= $arearow['nextweight'];
		$rate			= $arearow['discount'];
		$firstweight0	= $arearow['firstweight0'];
		$files			= $arearow['files'];
		$declared_value = $arearow['declared_value'];
		if($files == '1' && $weight <= 0.5) $firstweight = $firstweight0;//如果是文件
		if($weight <= 0.5){						
			$shipfee	= $firstweight;
		} else {				
			$shipfee	= ceil((($weight*1000-500)/500))*$nextweight + $firstweight;
		}
		$totalfee		= $shipfee+$declared_value;
		$shipfee 		= $shipfee*$rate+$declared_value;
        $shipfee 		= round($shipfee, 4);
		return array('discount'=>$rate, 'fee'=>$shipfee, 'totalfee'=>$totalfee);
    }
    
	/**
	 * ShipfeeQueryModel::cal_eub_shenzhen()
	 * 中国邮政EUB深圳运费计算
	 * @param float $weight 重量
	 * @param string $countryname 发往国家
	 * @param $data 待定
	 * @param $discount 是否有折扣
	 * @return false or array
	 */
    public function cal_eub_shenzhen($weight, $countryname, $data, $discount=true){
		$totalfee	= 0;
		$arealist 	= array();
		$maxWeight	= 0;
		$chAlias	= 'eub_shenzhen';
		$res		= array();
		$res		= TransOpenApiModel::cacheCarrierInfoByChannelAli("trans_channels", "channelAlias='{$chAlias}'", "{$chAlias}_carrier", 600, 0);
        $maxWeight	= !empty($res['weightMax']) ? floatval($res['weightMax']) : 0;
		if(!empty($maxWeight)) {
			if($weight>$maxWeight) return false;
		}
        $arealist	= TransOpenApiModel::cacheTableInfo('trans_freight_eub_shenzhen', '1', 'cal_eub_shenzhen', 86400, 0);
        if(empty($arealist)) return false;
        $arearow 	= array();
        foreach($arealist as $value) {
            $countrys 		= explode(',', $value['countrys']);
            $countrys 		= array_map(array($this, 'callback_trimspace'), $countrys);
            if(in_array($countryname, $countrys)) {
                $arearow 	= $value;
                break;
            }
        }
        if(empty($arearow)) return false;
        $rate1 		= empty($arearow['discount']) ? 0 : floatval($arearow['discount']);
        $rate2 		= empty($arearow['discount1']) ? 0 : floatval($arearow['discount1']);
        $rate		= 0;
		$rateweight = empty($arearow['nextweight']) ? 0 : floatval($arearow['nextweight']);
		$noweight   = empty($arearow['noWeight']) ? 0 : intval($arearow['noWeight'])/1000;
		if($weight <= $rateweight) {
			if($weight <= $noweight && $noweight > 0) {
				$shipfee	= $arearow['unitprice']*$noweight+$arearow['handlefee'];
			} else {
				$shipfee	= $arearow['unitprice']*$weight+$arearow['handlefee'];
			}
			$totalfee		= $shipfee;
			if($rate1 > 0) {
				$shipfee	= $shipfee*$rate1;
				$rate		= $rate1;
			}
		} else {
			$shipfee		= $arearow['unitprice']*$weight+$arearow['handlefee'];
			$totalfee		= $shipfee;
			if($rate2 > 0) {
				$shipfee	= $shipfee*$rate2;
				$rate		= $rate2;
			}
		}
		return array('discount'=>$rate, 'fee'=>$shipfee, 'totalfee'=>$totalfee);
    }
	
	/**
	 * ShipfeeQueryModel::cal_eub_fujian()
	 * 中国邮政EUB福建运费计算
	 * @param float $weight 重量
	 * @param string $countryname 发往国家
	 * @param $data 待定
	 * @param $discount 是否有折扣
	 * @return false or array
	 */
    public function cal_eub_fujian($weight, $countryname, $data, $discount=true){
		$totalfee	= 0;
		$arealist 	= array();
		$maxWeight	= 0;
		$chAlias	= 'eub_fujian';
		$res		= array();
		$res		= TransOpenApiModel::cacheCarrierInfoByChannelAli("trans_channels", "channelAlias='{$chAlias}'", "{$chAlias}_carrier", 600, 0);
        $maxWeight	= !empty($res['weightMax']) ? floatval($res['weightMax']) : 0;
		if(!empty($maxWeight)) {
			if($weight>$maxWeight) return false;
		}
        $arealist	= TransOpenApiModel::cacheTableInfo('trans_freight_eub_fujian', '1', 'cal_eub_fujian', 86400, 0);
        if(empty($arealist)) return false;
        $arearow 	= array();
        foreach($arealist as $value) {
            $countrys 		= explode(',', $value['countrys']);
            $countrys 		= array_map(array($this, 'callback_trimspace'), $countrys);
            if(in_array($countryname, $countrys)) {
                $arearow 	= $value;
                break;
            }
        }
        if(empty($arearow)) return false;
        $rate1 		= empty($arearow['discount']) ? 0 : floatval($arearow['discount']);
        $rate2 		= empty($arearow['discount1']) ? 0 : floatval($arearow['discount1']);
        $rate		= 0;
		$rateweight = empty($arearow['nextweight']) ? 0 : floatval($arearow['nextweight']);
		$noweight   = empty($arearow['noWeight']) ? 0 : intval($arearow['noWeight'])/1000;
		if($weight <= $rateweight) {
			if($weight <= $noweight && $noweight > 0) {
				$shipfee	= $arearow['unitprice']*$noweight+$arearow['handlefee'];
			} else {
				$shipfee	= $arearow['unitprice']*$weight+$arearow['handlefee'];
			}
			$totalfee	= $shipfee;
			if($rate1 > 0) {
				$shipfee	= $shipfee*$rate1;
				$rate		= $rate1;
			}
		} else {
			$shipfee		= $arearow['unitprice']*$weight+$arearow['handlefee'];
			$totalfee		= $shipfee;
			if($rate2 > 0) {
				$shipfee	= $shipfee*$rate2;
				$rate		= $rate2;
			}
		}
		return array('discount'=>$rate, 'fee'=>$shipfee, 'totalfee'=>$totalfee);
    }
	
	/**
	 * ShipfeeQueryModel::cal_eub_jiete()
	 * 中国邮政EUB捷特运费计算
	 * @param float $weight 重量
	 * @param string $countryname 发往国家
	 * @param $data 待定
	 * @param $discount 是否有折扣
	 * @return false or array
	 */
    public function cal_eub_jiete($weight, $countryname, $data, $discount=true){
		$totalfee	= 0;
		$arealist 	= array();
		$maxWeight	= 0;
		$chAlias	= 'eub_fujian';
		$res		= array();
		$res		= TransOpenApiModel::cacheCarrierInfoByChannelAli("trans_channels", "channelAlias='{$chAlias}'", "{$chAlias}_carrier", 600, 0);
        $maxWeight	= !empty($res['weightMax']) ? floatval($res['weightMax']) : 0;
		if(!empty($maxWeight)) {
			if($weight>$maxWeight) return false;
		}
        $arealist	= TransOpenApiModel::cacheTableInfo('trans_freight_eub_jiete', '1', 'cal_eub_jiete', 86400, 0);
        if(empty($arealist)) return false;
        $arearow 	= array();
        foreach($arealist as $value) {
            $countrys 		= explode(',', $value['countrys']);
            $countrys 		= array_map(array($this, 'callback_trimspace'), $countrys);
            if(in_array($countryname, $countrys)) {
                $arearow 	= $value;
                break;
            }
        }
        if(empty($arearow)) return false;
        $rate1 		= empty($arearow['discount']) ? 0 : floatval($arearow['discount']);
        $rate2 		= empty($arearow['discount1']) ? 0 : floatval($arearow['discount1']);
        $rate		= 0;
		$rateweight = empty($arearow['nextweight']) ? 0 : floatval($arearow['nextweight']);
		$noweight   = empty($arearow['noWeight']) ? 0 : intval($arearow['noWeight'])/1000;
		if($weight <= $rateweight) {
			if($weight <= $noweight && $noweight > 0) {
				$shipfee	= $arearow['unitprice']*$noweight+$arearow['handlefee'];
			} else {
				$shipfee	= $arearow['unitprice']*$weight+$arearow['handlefee'];
			}
			$totalfee	= $shipfee;
			if($rate1 > 0) {
				$shipfee	= $shipfee*$rate1;
				$rate		= $rate1;
			}
		} else {
			$shipfee		= $arearow['unitprice']*$weight+$arearow['handlefee'];
			$totalfee		= $shipfee;
			if($rate2 > 0) {
				$shipfee	= $shipfee*$rate2;
				$rate		= $rate2;
			}
		}
		return array('discount'=>$rate, 'fee'=>$shipfee, 'totalfee'=>$totalfee);
    }
    
	/**
	 * ShipfeeQueryModel::cal_dhl_shenzhen()
	 * DHL深圳运费计算
	 * @param float $weight 重量
	 * @param string $countryname 发往国家
	 * @param $data 待定
	 * @return false or array
	 */
    public function cal_dhl_shenzhen($weight, $countryname , $data){
        $totalfee	= 0;
		$rate		= 0;
		if($weight <= 20) {
			$mode 	= 1;
		} else {
			$mode 	= 2;
		}
        $arealist 	= array();
        $arealist	= TransOpenApiModel::cacheTableInfo('trans_freight_dhl_shenzhen', "mode = '{$mode}'", 'cal_dhl_shenzhen_'.$mode, 86400, 0);
		if(empty($arealist)) return false;
        $arearow 	= array();
        foreach($arealist as $value) {
            $countrys 		= explode(',', $value['country']);
            if(in_array('['.$countryname.']', $countrys)) {
                $arearow 	= $value;
                break;
            }
        }
        if(empty($arearow)) return false;
		//计算运费
		$weight_freight 	= $arearow['weight_freight']; 
		$weight_freight_arr = explode(',', $weight_freight);
		foreach($weight_freight_arr as $wf_value) {
			$wf_value_arr 	= explode(':', $wf_value);
			$w_range = explode('-', $wf_value_arr[0]);
			if($mode == 1) {
				if($weight > $w_range[0] && $weight <= $w_range[1]) {
					$shipfee = $wf_value_arr[1];
					break;
				}
			} else if($mode == 2) {
				if(empty($w_range[1])) {
					if($weight > $w_range[0]) $shipfee = $weight * $wf_value_arr[1];
				} else {
					if($weight >= $w_range[0] && $weight <= $w_range[1]) {
						$shipfee = $weight * $wf_value_arr[1];
					}
				}
			}
		}
		
		$shipfee 	= $shipfee * (1 + $arearow['fuelcosts']);
		$totalfee	= $shipfee;
		$shipfee 	= round($shipfee, 4);
        return array('discount'=>$rate, 'fee'=>$shipfee, 'totalfee'=>$totalfee);
    }
    
	/**
	 * ShipfeeQueryModel::cal_fedex_shenzhen()
	 * 联邦深圳运费计算
	 * @param float $weight 重量
	 * @param string $countryname 发往国家
	 * @param $data 待定
	 * @return false or array
	 */
    function cal_fedex_shenzhen($weight, $countryname, $data){
		$totalfee	= 0;
		$rate		= 0;
		$postcode 	= isset($data['postCode']) ? $data['postCode'] : 0;
        $arealist	= TransOpenApiModel::cacheTableInfo('trans_freight_fedex_shenzhen', "type = 'economica'", 'cal_fedex_shenzhen_economica', 86400, 0);
        $rowinfo 	= array();
        foreach($arealist as $val) {
            $country 	= explode(',', $val['countrylist']);
            if(in_array($countryname, $country)) { //找到价目表信息
                $w 		= explode('-', $val['weightinterval']);
                if($weight<=$w[1] && $weight>$w[0]){
                    $rowinfo[] = $val;
                }
            }
        }
        if(empty($rowinfo)) { //没找到经济型的运输信息 则超找优先性
			$arealist		= TransOpenApiModel::cacheTableInfo('trans_freight_fedex_shenzhen', "type = 'prior'", 'cal_fedex_shenzhen_prior', 86400, 0);
			foreach($arealist as $val) {
                $country 	= explode(',', $val['countrylist']);
                if(in_array($countryname, $country)) {
                    $w 		= explode('-', $val['weightinterval']);
                    if($weight<=$w[1] && $weight>$w[0]) {
                        $rowinfo[] = $val;
                    }
                }
            }
        }
        if(empty($rowinfo)) return false;
        if($countryname == 'United States'){ //如果是美国
            $cls 			= explode(',', $rowinfo[0]['countrylist']);
            $postcodelist 	= explode('#', $cls[1]);
            $isthis 		= false;
            foreach($postcodelist as $pval) {
                $pl 		= explode('-', $pval);
                if($postcode>$pl[0] && $postcode<$pl[1]) { //找到
                    $isthis = true;
                    break;
                }
            }
            if(!$isthis) { //没找 这查找United States#other
				$arealist	= TransOpenApiModel::cacheTableInfo('trans_freight_fedex_shenzhen', "countrylist = 'United States#other' and type='economica'", 'cal_fedex_shenzhen_economica_other', 86400, 0);
				foreach($arealist as $val){
                   $w 			= explode('-', $val['weightinterval']);
                   if($weight<=$w[1] && $weight>$w[0]) {
                       $rowinfo = array($val);
                   }
                }
            }
        }
		//计算运费
        $shipfee 	= $weight > 20.5 ? floatval($rowinfo[0]['unitprice'])*$weight*(1+$rowinfo[0]['baf']) : $rowinfo[0]['unitprice']*(1+$rowinfo[0]['baf']);
        $totalfee	= $shipfee;
		$shipfee 	= round($shipfee, 4);
        return array('discount'=>$rate, 'fee'=>$shipfee, 'totalfee'=>$totalfee);
    }
    
	/**
	 * ShipfeeQueryModel::cal_globalmail_shenzhen()
	 * 香港globalmail运费计算
	 * @param float $weight 重量
	 * @param string $countryname 发往国家
	 * @param $data 待定
	 * @return false or array
	 */
    public function cal_globalmail_shenzhen($weight, $countryname, $data){
        $totalfee	= 0;
		$rate		= 0;
		$arealist 	= array();
        $arealist	= TransOpenApiModel::cacheTableInfo('trans_freight_globalmail_shenzhen', '1', 'cal_globalmail_shenzhen', 86400, 0);
        if(empty($arealist)) return false;
        $arearow 	= array();
        foreach($arealist as $value) {
            $countrys 		= explode(',', $value['country']);
            $countrys 		= array_map(array($this, 'callback_trimspace'), $countrys);
            if(in_array($countryname, $countrys)) {
                $arearow 	= $value;
                break;
            }
        }
        if(empty($arearow)) return false;
        //运费计算
		$weight_freight 	= $arearow['weight_freight'];
		$weight_freight_arr	= explode(',',$weight_freight);
		foreach($weight_freight_arr as $key1 => $value1)
		{
			$value1_arr		= explode(':',$value1);
			$weight_range	= explode('-',$value1_arr[0]);
			if($weight>$weight_range[0] && $weight<=$weight_range[1])
			{
				$shipfee	= $value1_arr[1];
				break;
			}
		}
		if(empty($shipfee)) return false;
		$shipfee *= $weight;
		//油费计算
		$fuelcosts 			= $arearow['fuelcosts'];
		$zgTranFee			= $arearow['zgTranFee'] * $weight;
		$fuelcosts_arr 		= explode(',',$fuelcosts);
		foreach($fuelcosts_arr as $key2 => $value2)
		{
			$value2_arr 	= explode(':',$value2);
			$weight_range 	= explode('-',$value2_arr[0]);
			if($weight>$weight_range[0] && $weight<=$weight_range[1])
			{
				$fuelfee 	= $value2_arr[1];
				break;
			}
		}
		$exRates	= TransOpenApiModel::cacheExRateInfo(array('HKD'), array('CNY'), 'hkRate', 7200, 0);
		$hkRate		= round(floatval($exRates['HKD/CNY']),4);
		if($hkRate <= 0) return array('discount'=>0, 'fee'=>0, 'totalfee'=>0, 'level'=>"", 'exRate'=>array("hkRate"=>0));
		$shipfee 	+= $fuelfee;
		$shipfee	= $shipfee * $hkRate;
		$shipfee 	+= $zgTranFee;
		$totalfee	= $shipfee;
		$shipfee 	= round($shipfee, 4);
        return array('discount'=>$rate, 'fee'=>$shipfee, 'totalfee'=>$totalfee, 'level'=>"", 'exRate'=>array("hkRate"=>$hkRate));
    }
	
	/**
	 * ShipfeeQueryModel::trimSingleQuotes()
	 * 去掉国家前名字前后的单引号
	 * @param string $val 字符串
	 * @return string
	 */
    private function trimSingleQuotes($val){
        return trim($val, "'");
    }
    
	/**
	 * ShipfeeQueryModel::callback_trimspace()
	 * 去掉字符串的前后空格
	 * @param string $val 字符串
	 * @return string
	 */
    private function callback_trimspace($val){
        return trim($val);
    }

    //待定
	public function getAreaInfo($countryname, $carrierid){ //echo $carrierid;exit;
        $sql = "SELECT ch.carrierId,ch.channelName , ch.id, pa.id as paid, pa.countries, pa.partitionName, ch.channelAlias, ch.id FROM trans_channels as ch join trans_partition as pa on ch.id=pa.channelId WHERE  ch.carrierId=$carrierid and 
                ch.is_enable=1 and ch.is_delete=0 and pa.is_enable=1 and pa.is_delete=0";
        //echo $sql, "\n";
		$arealist = $this->dbconn->fetch_array_all($this->dbconn->query($sql)); //运输方式下的所有
        //print_r($arealist);
        $result = array();
        foreach($arealist as $areaval) {   //过滤分区列表
            $countryar 	= explode(',', $areaval['countries']);   
            $countryar 	= array_map(array($this, 'trimSingleQuotes'), $countryar); //去除两边的单引号
            $countryar 	= array_map(array($this, 'callback_trimspace'), $countryar); //去除两边的空格
			if(in_array($countryname, $countryar)) {
				$result = $areaval;
                break;
            }
        }
        return $result;
    }    
}