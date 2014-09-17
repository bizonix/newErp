<?php
/**
 * 类名：TrackShipFeeAct
 * 功能：运德物流运费查询动作处理层
 * 版本：1.0
 * 日期：2014/07/26
 * 作者：管拥军
 */
  
class TrackShipFeeAct {
    public static $errCode	= 0;
	public static $errMsg	= "";
	public static $logFile	= "";

	//初始化
    public function __construct() {
        self::$logFile		= WEB_PATH."html/access/".date('Y')."/".date('Y-m-d').".shipFee.log";
    }
	
	/**
	 * TrackShipFeeAct::actIndex()
	 * 运费计算网站首页
	 * @return array 
	 */
 	public function actIndex(){
		$data				= array();
		$countrys			= json_decode(OpenApiAct::act_getCountriesStandard(),true);
		$data['countrys']	= $countrys['data'];
		return $data;
	}
	
	/**
	 * TrackShipFeeAct::actShipFee()
	 * 运费计算结果页面
	 * @return array 
	 */
 	public function actShipFee(){
		$data		= array();
        $addId		= isset($_REQUEST["addId"]) ? abs(intval($_REQUEST["addId"])) : 0;
        $country	= isset($_REQUEST["country"]) ? post_check($_REQUEST["country"]) : '';
        $unit		= isset($_REQUEST["unit"]) ? post_check($_REQUEST["unit"]) : '';
        $longs		= isset($_REQUEST["longs"]) ? post_check($_REQUEST["longs"]) : '';
        $widths		= isset($_REQUEST["widths"]) ? post_check($_REQUEST["widths"]) : '';
        $heights	= isset($_REQUEST["heights"]) ? post_check($_REQUEST["heights"]) : '';
        $unitW		= isset($_REQUEST["unitW"]) ? post_check($_REQUEST["unitW"]) : '';
        $weight		= isset($_REQUEST["weights"]) ? post_check($_REQUEST["weights"]) : '';
		$maxItem	= 4;
		$moreItem	= 0;
		$topNum		= 3;
		if(!in_array($addId,array(1),true)) {
			show_message($this->smarty,"The delivery address parameter error!","");	
			exit;
		}
		if(empty($country)) {
			show_message($this->smarty,"Recipient countries parameter error!","");	
			exit;
		}
		if(!in_array($unit,array('CM','IN','M'),true)) {
			show_message($this->smarty,"A unit of volume parameters error!","");	
			exit;
		}
		if(!in_array($unitW,array('KG','LB','OZ'),true)) {
			show_message($this->smarty,"A unit of weight parameters error!","");	
			exit;
		}
		if(is_numeric($longs) && is_numeric($widths) && is_numeric($heights)) {
			if($longs <= 0 || $widths <= 0 || $heights <= 0) {
				show_message($this->smarty,"Size (L/W/H) parameter error!","");	
				exit;
			} else {
				if($unit == 'CM') $volWeight	= round((($longs * $widths * $heights)/6000),4);	
				if($unit == 'IN') $volWeight	= round((($longs * $widths * $heights)*30.48/6000),4);	
				if($unit == 'M') $volWeight	= round((($longs * $widths * $heights)*100/6000),4);	
			}
		}
		if(is_numeric($weight)) {
			if($weight <= 0) {
				show_message($this->smarty,"The weight parameter error!","");	
				exit;
			} else {
				if($unitW == 'KG') $weight	= round($weight,4);
				if($unitW == 'LB') $weight	= round(($weight*0.4535924),4);
				if($unitW == 'OZ') $weight	= round(($weight*0.0283495),4);
			}
		}
		if($volWeight > $weight) {
			$realWeight		= $volWeight;
		} else {
			$realWeight		= $weight;
		}
		if(empty($realWeight)) {
			show_message($this->smarty,"Weight is 0, please check!","");	
			exit;
		}		
		$countrys			= json_decode(OpenApiAct::act_getCountriesStandard(),true);
		$data['countrys']	= $countrys['data'];
		$data['addId']		= $addId;
		$data['country']	= $country;
		$data['unit']		= $unit;
		$data['unitW']		= $unitW;
		$data['longs']		= $longs;
		$data['widths']		= $widths;
		$data['heights']	= $heights;
		$data['weights']	= $weight;
		$data['openFees']	= TrackShipFeeAct::actGetShipFee($addId,$country,$realWeight);
		$data['maxItem']	= $maxItem;
		$data['topNum']		= $topNum;
		$data['moreItem']	= count($data['openFees']) - ($maxItem+1);
		return $data;
	}

	/**
	 * TrackShipFeeAct::act_getShipFee()
	 * 获取运费信息
	 * @param string $addId 发货地址ID
	 * @param string $country 国家
	 * @param string $realWeight 重量
	 * @return array;
	 */
	public function act_getShipFee(){
		$realWeight			= 0;
		$volWeight			= 0;
		$noShip				= array();
		$noShipId			= "";
		$weightFlag			= "";
		$addId				= isset($_REQUEST["addId"]) ? abs(intval($_REQUEST["addId"])) : 0;
		$country			= isset($_REQUEST["country"]) ? post_check(rawurldecode($_REQUEST["country"])) : 0;
		$weight				= isset($_REQUEST["weight"]) ? post_check($_REQUEST["weight"]) : 0;
		$longs				= isset($_REQUEST["longs"]) ? post_check($_REQUEST["longs"]) : '';
		$widths				= isset($_REQUEST["widths"]) ? post_check($_REQUEST["widths"]) : '';
		$heights			= isset($_REQUEST["heights"]) ? post_check($_REQUEST["heights"]) : '';
		$unit				= isset($_REQUEST["unit"]) ? post_check($_REQUEST["unit"]) : '';
		$unitW				= isset($_REQUEST["unitW"]) ? post_check($_REQUEST["unitW"]) : '';
		$apiToken			= isset($_REQUEST["apiToken"]) ? post_check($_REQUEST["apiToken"]) : 'e19d2feabc0eb1705f69c6ea2d9d0e1d';
		$is_new				= isset($_REQUEST["is_new"]) ? abs(intval(($_REQUEST["is_new"]))) : 0;
		$ip					= getClientIP();
		if(!in_array($addId,array(1),true)) {
			self::$errCode  = 10000;
			self::$errMsg   = "The delivery address parameter error!";
			@write_a_file(self::$logFile, date('Y-m-d H:i:s')."====={$ip}=={$addId}=={$country}=={$unit}=={$longs}=={$widths}=={$heights}=={$unitW}=={$weight}=====event:".self::$errMsg."\n");
			return false;
		}
		if(empty($country)) {
			self::$errCode  = 10001;
			self::$errMsg   = "Recipient countries parameter error!";
			@write_a_file(self::$logFile, date('Y-m-d H:i:s')."====={$ip}=={$addId}=={$country}=={$unit}=={$longs}=={$widths}=={$heights}=={$unitW}=={$weight}=====event:".self::$errMsg."\n");
			return false;
		}
		if(!in_array($unit,array('CM','IN','M'),true)) {
			self::$errCode  = 10002;
			self::$errMsg   = "A unit of volume parameters error!";
			@write_a_file(self::$logFile, date('Y-m-d H:i:s')."====={$ip}=={$addId}=={$country}=={$unit}=={$longs}=={$widths}=={$heights}=={$unitW}=={$weight}=====event:".self::$errMsg."\n");
			return false;
		}
		if(!in_array($unitW,array('KG','LB','OZ'),true)) {
			self::$errCode  = 10003;
			self::$errMsg   = "A unit of weight parameters error!";
			@write_a_file(self::$logFile, date('Y-m-d H:i:s')."====={$ip}=={$addId}=={$country}=={$unit}=={$longs}=={$widths}=={$heights}=={$unitW}=={$weight}=====event:".self::$errMsg."\n");
			return false;
		}
		if(is_numeric($longs) && is_numeric($widths) && is_numeric($heights)) {
			if($longs <= 0 || $widths <= 0 || $heights <= 0) {
				self::$errCode  = 10004;
				self::$errMsg   = "Size (L/W/H) parameter error!";
				@write_a_file(self::$logFile, date('Y-m-d H:i:s')."====={$ip}=={$addId}=={$country}=={$unit}=={$longs}=={$widths}=={$heights}=={$unitW}=={$weight}=====event:".self::$errMsg."\n");
				return false;
			} else {
				if($unit=='CM') $volWeight	= round((($longs * $widths * $heights)/6000),4);	
				if($unit=='IN') $volWeight	= round(((($longs*2.54) * ($widths*2.54) * ($heights*2.54))/6000),4);	
				if($unit=='M') $volWeight	= round(((($longs*100) * ($widths*100) * ($heights*100))/6000),4);	
			}
			//加入中国邮政（平邮、挂号）小包体积限制
			if($unit=='CM') {
				if(($longs+$widths+$heights) > 110) {
					$noShip[]	= 1;
					$noShip[]	= 2;
				}
			}
			if($unit=='IN') {
				if(($longs+$widths+$heights)*2.54 > 110) {
					$noShip[]	= 1;
					$noShip[]	= 2;
				}
			}
			if($unit=='M') {
				if(($longs+$widths+$heights)*100 > 110) {
					$noShip[]	= 1;
					$noShip[]	= 2;
				}
			}
			if(!empty($noShip)) $noShipId = implode(",",$noShip);
		}
		if(is_numeric($weight)) {
			if($weight<=0) {
				self::$errCode  = 10005;
				self::$errMsg   = "The weight parameter error!";
				@write_a_file(self::$logFile, date('Y-m-d H:i:s')."====={$ip}=={$addId}=={$country}=={$unit}=={$longs}=={$widths}=={$heights}=={$unitW}=={$weight}=====event:".self::$errMsg."\n");
				return false;
			} else {
				if($unitW == 'KG') $weight	= round($weight,4);
				if($unitW == 'LB') $weight	= round(($weight*0.4535924),4);
				if($unitW == 'OZ') $weight	= round(($weight*0.0283495),4);
			}
		}
		if($volWeight > $weight) {
			$realWeight			= $volWeight;
			$weightFlag			= "volWeight";
		} else {
			$realWeight			= $weight;
			$weightFlag			= "realWeight";
		}
		if(empty($realWeight)) {
			self::$errCode  = 10006;
			self::$errMsg   = "Weight is 0, please check!";
			@write_a_file(self::$logFile, date('Y-m-d H:i:s')."====={$ip}=={$addId}=={$country}=={$unit}=={$longs}=={$widths}=={$heights}=={$unitW}=={$weight}=====event:".self::$errMsg."\n");
			return false;
		}
		@write_a_file(self::$logFile, date('Y-m-d H:i:s')."====={$ip}=={$addId}=={$country}=={$unit}=={$longs}=={$widths}=={$heights}=={$unitW}=={$weight}==体积重：{$volWeight}==实重：{$realWeight}\n");
		$key 				= md5($addId.$country.$realWeight);
		$cacheName 			= md5("track_ship_fee_".$key);
		$memc_obj			= new Cache(C('CACHEGROUP'));
		$shipFeeInfo 		= $memc_obj->get_extral($cacheName);
		if(!empty($shipFeeInfo) && empty($is_new)) {
			@write_a_file(self::$logFile, date('Y-m-d H:i:s')."====={$ip}=={$addId}=={$country}=={$unit}=={$longs}=={$widths}=={$heights}=={$unitW}=={$weight}==体积重：{$volWeight}==实重：{$realWeight}===event:memcache ok\n");
			return unserialize($shipFeeInfo);
		} else {
			$shipFeeInfo		= TrackShipFeeModel::calcOpenShipFee($addId, $country, $realWeight, '', '', $apiToken, $noShipId, $weightFlag);
			self::$errCode 		= TrackShipFeeModel::$errCode;
			self::$errMsg  		= TrackShipFeeModel::$errMsg;
			$isok 				= $memc_obj->set_extral($cacheName, serialize($shipFeeInfo), 7200);
			if(!$isok) {
				self::$errCode 	= 308;
				self::$errMsg  	= 'memcache缓存出错!';
				@write_a_file(self::$logFile, date('Y-m-d H:i:s')."====={$ip}=={$addId}=={$country}=={$unit}=={$longs}=={$widths}=={$heights}=={$unitW}=={$weight}==体积重：{$volWeight}==实重：{$realWeight}===event:".self::$errMsg."\n");
				//return false;
			}
			@write_a_file(self::$logFile, date('Y-m-d H:i:s')."====={$ip}=={$addId}=={$country}=={$unit}=={$longs}=={$widths}=={$heights}=={$unitW}=={$weight}==体积重：{$volWeight}==实重：{$realWeight}===event:api interface ok\n");
			return $shipFeeInfo;
		}
    }
	
	/**
	 * TrackShipFeeAct::actGetShipFee()
	 * 获取接口运费信息
	 * @param string $addId 发货地址ID
	 * @param string $country 国家
	 * @param string $realWeight 重量
	 * @return array;
	 */
	public function actGetShipFee($addId,$country,$realWeight,$is_new=0){
		$key 				= md5($addId.$country.$realWeight);
		$cacheName 			= md5("track_ship_fee_".$key);
		$memc_obj			= new Cache(C('CACHEGROUP'));
		$shipFeeInfo 		= $memc_obj->get_extral($cacheName);
		if(!empty($shipFeeInfo) && empty($is_new)) {
			return unserialize($shipFeeInfo);
		} else {
			$shipFeeInfo		= TrackShipFeeModel::calcOpenShipFee($addId, $country, $realWeight, '', '', $apiToken, $noShipId, $weightFlag);
			self::$errCode 		= TrackShipFeeModel::$errCode;
			self::$errMsg  		= TrackShipFeeModel::$errMsg;
			$isok 				= $memc_obj->set_extral($cacheName, serialize($shipFeeInfo), 86400);
			if(!$isok) {
				self::$errCode 	= 308;
				self::$errMsg  	= 'memcache缓存出错!';
				//return false;
			}
			return $shipFeeInfo;
		}
    }
}
?>