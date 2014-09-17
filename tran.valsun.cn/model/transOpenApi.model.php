<?php
/**
 * 类名：TransOpenApiModel
 * 功能：运输方式对方接口调用model层
 * 版本：1.0
 * 日期：2013/10/21
 * 作者：管拥军
 */
 
class TransOpenApiModel{
	public static $dbConn;
	public static $errCode		=	0;
	public static $errMsg		=	"";
	public static $prefix;
	public static $prefixfee;
	public static $chnameArr;
	
	//初始化db类
	public static function	initDB(){
		global $dbConn;
		self::$dbConn		= $dbConn;
		self::$prefix		= C('DB_PREFIX');
		self::$prefixfee	= C('DB_PREFIX').'freight_';
		self::$chnameArr	= array('cpsf_fujian_quanzhou','cpsf_fujian_zhangpu','cpsf_shenzhen','cprg_fujian','cprg_fujian_zhangpu','cprg_fujian_quanzhou','cprg_shenzhen','hkpostsf_hk','hkpostrg_hk','ems_shenzhen','eub_shenzhen','eub_fujian','dhl_shenzhen','fedex_shenzhen','globalmail_shenzhen','ups_calcfree','usps_calcfree','ups_us','sto_shenzhen','zto_shenzhen','yto_shenzhen','yundaex_shenzhen','best_shenzhen','jym_shenzhen','gto_shenzhen','ruston_packet_py','ruston_packet_gh','ruston_large_package','sg_dhl_gm_gh','sg_dhl_gm_py','zhengzhou_xb_py','zhengzhou_xb_gh','ruishi_xb_py','ruishi_xb_gh','bilishi_xb_py','bilishi_xb_gh','usps_first_class','ups_ground_commercia','aoyoubao_gh','aoyoubao_py');
	}	
	
	/**
	 * TransOpenApiModel::getMailUser()
	 * 外接邮件订阅系统获取收件人
	 * @param string $english_id 邮件英文ID
	 * @param string $jsonp 判断状态
	 * @return  json string
	 */
	public static function getMailUser($english_id, $jsonp) {
		$paramList = array(
			'method' 		=> 'subscription.user.query',  //API名称
			'format'		=> 'json',  //返回格式
			'v' 			=> '1.0',   //API版本号
			'username'		=> C('OPEN_SYS_USER'),
			'english_id'	=> $english_id,
			'jsonp'			=> $jsonp,
		);
		$mailUserList		= callOpenSystem($paramList);
		unset($paramList);
		return $mailUserList;
	}
	
	/**
	 * TransOpenApiModel::updateFedexFee()
	 * 批量更新同步老ERP联邦运费价目表
	 * @param int $type 1经济型，2优先型
	 * @param float $baf 燃油费率
	 * @return  json string
	 */
	public static function updateFedexFee($type, $baf){
		self::initDB();
		$types	= array('economica','prior');
		$times	= time();
		$data	= array();
		$uid	= 71;
		$sql	= "DELETE FROM trans_freight_fedex_shenzhen WHERE type = '{$types[($type-1)]}'";
		$query	= self::$dbConn->query($sql);
		if(!$query) {
			self::$errCode	= 10000;
			self::$errMsg	= "执行SQL失败";
			return false;
		}
		include_once WEB_PATH."html/temp/fedex_{$type}.php";
		//经济型
		if($type==1) {
			foreach ($FEDEX_WEIGHT_LIST_1 as $key=>$v) {
				if(empty($v)) continue;
				foreach ($FEDEX_CONTRY_LIST_1 as $cty=>$c) {
					$data	= array(
									'weightinterval'	=> $FEDEX_WEIGHT_LIST_1[$key],
									'unitprice'			=> $FEDEX_PRICE_LIST_1[$key][$cty],
									'countrylist'		=> $c,
									'type'				=> $types[($type-1)],
									'baf'				=> $baf,
									'addTime'			=> $times,
									'add_user_id'		=> $uid,
								);
					$sql	= array2sql($data);
					$sql	= "INSERT INTO trans_freight_fedex_shenzhen SET {$sql}";
					$query	= self::$dbConn->query($sql);
					if(!$query) {
						print_r($data);
						return false;
					}
				}
			}
			return "经济型运费批量更新完成!";
		}
		//优先型
		if($type==2) {
			foreach ($FEDEX_WEIGHT_LIST_2 as $key=>$v) {
				if(empty($v)) continue;
				foreach ($FEDEX_CONTRY_LIST_2 as $cty=>$c) {
					$data	= array(
									'weightinterval'	=> $FEDEX_WEIGHT_LIST_2[$key],
									'unitprice'			=> $FEDEX_PRICE_LIST_2[$key][$cty],
									'countrylist'		=> $c,
									'type'				=> $types[($type-1)],
									'baf'				=> $baf,
									'addTime'			=> $times,
									'add_user_id'		=> $uid,
								);
					$sql	= array2sql($data);
					$sql	= "INSERT INTO trans_freight_fedex_shenzhen SET {$sql}";
					$query	= self::$dbConn->query($sql);
					if(!$query) {
						print_r($data);
						return false;
					}
				}
			}
			return "优先型运费批量更新完成!";
		}
	}
	
	/**
	 * TransOpenApiModel::getLogZip()
	 * 打包日志文件
	 * @return  json string
	 */
	public static function getLogZip(){
		$zipname 		= date('Ymd',time());
		$zipfile 		= WEB_PATH."html/temp/".$zipname.".zip";
		// if(file_exists($zipfile)) {
			// return WEB_URL.'temp/'.$zipname.'.zip';
		// } else {
			require_once(WEB_PATH.'lib/pclzip.lib.php');
			$obj		= new PclZip($zipfile);
			$files		= array(WEB_PATH.'log/');
			$curtime 	= date('Y-m-d H:i:s',time()); 
			//创建压缩文件
			if($obj->create($files, PCLZIP_OPT_REMOVE_PATH, WEB_PATH.'log/', PCLZIP_OPT_COMMENT, "Today's tran.valsun.cn log packaged!\n\npackaged time:{$curtime}")) {
				return WEB_URL.'temp/'.$zipname.'.zip';
			} else {
				self::$errCode	= 10000;
				self::$errMsg	= "打包失败！请检查相关权限";
				return false;
			}
		// }
	}
	
	/**
	 * TransOpenApiModel::getErpOrderInfo()
	 * 获取ERP订单摘要信息
	 * @param string $ids 一个或多个订单号
	 * @return  json string
	 */
	public static function getErpOrderInfo($ids){
		$paramArr = array(
			/* API系统级输入参数 Start */
				'method' 	=> 'trans.erp.orderinfo.get',  //API名称
				'format' 	=> 'json',  //返回格式
					 'v' 	=> '1.0',   //API版本号
			'username'	 	=> C('OPEN_SYS_USER'),
			/* API系统级参数 End */				 
			/* API应用级输入参数 Start*/
				'ids'		=> $ids,
			/* API应用级输入参数 End*/
		);
		$trackOrderInfo		= callOpenSystem($paramArr,'local');
		unset($paramArr);
		return $trackOrderInfo;
	}
	
	/**
	 * TransOpenApiModel::getOrderInfo()
	 * 获取新订单系统订单摘要信息
	 * @param string $ids 一个或多个订单号
	 * @return  json string
	 */
	public static function getOrderInfo($ids){
		$paramArr = array(
			/* API系统级输入参数 Start */
				'method' 	=> 'trans.order.info.get',  //API名称
				'format' 	=> 'json',  //返回格式
					 'v' 	=> '1.0',   //API版本号
			'username'	 	=> C('OPEN_SYS_USER'),
			/* API系统级参数 End */				 
			/* API应用级输入参数 Start*/
				'ids'		=> $ids,
			/* API应用级输入参数 End*/
		);
		$trackOrderInfo		= callOpenSystem($paramArr);
		unset($paramArr);
		return $trackOrderInfo;
	}
	
	/**
	 * TransOpenApiModel::getErpTrackNumList()
	 * 获取ERP跟踪号列表
	 * @param string $type 运输方式名称
	 * @param string $days N天内的跟踪号
	 * @param string $cat 日期类型
	 * @return  json string
	 */
	public static function getErpTrackNumList($type,$days,$cat){
		$paramArr = array(
			/* API系统级输入参数 Start */
				'method' 	=> 'trans.erp.tracknum.get',  //API名称
				'format' 	=> 'json',  //返回格式
					 'v' 	=> '1.0',   //API版本号
			'username'	 	=> C('OPEN_SYS_USER'),
			/* API系统级参数 End */				 
			/* API应用级输入参数 Start*/
				'carrier'	=> $type,
				'days'		=> $days,
				'cat'		=> $cat,
			/* API应用级输入参数 End*/
		);
		$trackNumberInfo	= callOpenSystem($paramArr,'local');
		//$trackNumberInfo	= json_decode(trackNumberInfo, true);
		//$trackNumberInfo	= is_array($trackNumberInfo) ? $trackNumberInfo : array();
		unset($paramArr);
		return $trackNumberInfo;
	}
	
	/**
	 * TransOpenApiModel::getOrderTrackNumList()
	 * 获取新订单系统跟踪号列表
	 * @param string $carrierId 运输方式ID
	 * @param int $days N天内的跟踪号
	 * @param string $cat 日期类型
	 * @return  json string
	 */
	public static function getOrderTrackNumList($carrierId,$days,$cat){
		$paramArr = array(
			/* API系统级输入参数 Start */
				'method' 	=> 'trans.order.tracknum.info.get',  //API名称
				'format' 	=> 'json',  //返回格式
					 'v' 	=> '1.0',   //API版本号
			'username'	 	=> C('OPEN_SYS_USER'),
			/* API系统级参数 End */				 
			/* API应用级输入参数 Start*/
				'carrierId'	=> $carrierId,
				'days'		=> $days,
				'cat'		=> $cat,
			/* API应用级输入参数 End*/
		);
		$trackNumberInfo	= callOpenSystem($paramArr);
		//$trackNumberInfo	= json_decode(trackNumberInfo, true);
		//$trackNumberInfo	= is_array($trackNumberInfo) ? $trackNumberInfo : array();
		unset($paramArr);
		return $trackNumberInfo;
	}
	
	/**
	 * TransOpenApiModel::sendMessage()
	 * 发送信息
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
				'method' 	=> 'notice.send.message',  //API名称
				'format' 	=> 'json',  //返回格式
					 'v' 	=> '1.0',   //API版本号
			'username'	 	=> C('OPEN_SYS_USER'),
			/* API系统级参数 End */				 
			/* API应用级输入参数 Start*/
				'type'		=> $type,
				'from'		=> $from,
				'to'		=> $to,
				'content'	=> $content,
				'title'	=> urlencode($title),
				'sysName'	=> urlencode(C('AUTH_SYSNAME')),
			/* API应用级输入参数 End*/
		);
		$messageInfo		= callOpenSystem($paramArr);
		unset($paramArr);
		return $messageInfo;
	}
	
	/**
	 * TransOpenApiModel::getErpCarrierList()
	 * 获取ERP运输方式列表
	 * @return  array
	 */
	public static function getErpCarrierList(){
		$paramArr = array(
			/* API系统级输入参数 Start */
				'method' 	=> 'trans.erp.carrier.get',  //API名称
				'format' 	=> 'json',  //返回格式
					 'v' 	=> '1.0',   //API版本号
			'username'	 	=> C('OPEN_SYS_USER'),
			/* API系统级参数 End */				 
			/* API应用级输入参数 Start*/
                'action' 	=> 'listCarrier'
			/* API应用级输入参数 End*/
		);
		$erpCarrierInfo		= callOpenSystem($paramArr,'local');
		$erpCarrierInfo		= json_decode($erpCarrierInfo, true);
		$erpCarrierInfo		= is_array($erpCarrierInfo) ? $erpCarrierInfo : array();
		unset($paramArr);
		return $erpCarrierInfo;
	}
	
	/**
	 * TransOpenApiModel::getAuthCompanyList()
	 * 获取鉴权公司列表
	 * @return  array
	 */
	public static function getAuthCompanyList(){
		$paramArr = array(
			/* API系统级输入参数 Start */
				'method' 	=> 'power.user.getApiCompany.get',  //API名称
				'format' 	=> 'json',  //返回格式
					 'v' 	=> '1.0',   //API版本号
			'username'	 	=> C('OPEN_SYS_USER'),
			/* API系统级参数 End */				 
			/* API应用级输入参数 Start*/
                'sysName' 	=> C('AUTH_SYSNAME'),
                'sysToken' 	=> C('AUTH_SYSTOKEN')

			/* API应用级输入参数 End*/
		);
		$companyInfo		= callOpenSystem($paramArr);
		$companyInfo		= json_decode($companyInfo, true);
		$companyInfo		= is_array($companyInfo) ? $companyInfo : array();
		unset($paramArr);
		return $companyInfo;
	}
	
	/**
	 * TransOpenApiModel::getTrackInfo()
	 * 获取跟踪号追踪信息
	 * @param string $tid 跟踪号
	 * @param string $type 运输方式（如中国邮政,ems）
	 * @return  json string;
	 */
	public static function getTrackInfo($tid, $type, $lan=10000){
		$type		= mb_convert_encoding($type, "GBK", "UTF-8");
		// $url 		= "http://202.103.191.212:8888/cgi-bin/GInfo.dll?EmmisTrackGenData&cemskind={$type}&cno={$tid}&lan={$lan}";
		$url 		= "http://121.40.69.217/cgi-bin/GInfo.dll?EmmisTrackGenData&cemskind={$type}&cno={$tid}&lan={$lan}";
		//$res		= file_get_contents($url);
		$header[] 	= "Content-type: text/html";
		$newdata  	= array();
		$ch 		= curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_TIMEOUT, 120);
		$res= curl_exec($ch);
		if(curl_errno($ch)) {
			$newdata['trackingEventList'] = array();
			$res	= array('ReturnValue' =>'错误信息:'.curl_error($ch).',请联系管理员谢谢！','errCode'=>curl_errno($ch));
			return json_encode($res);
			exit;
		}
		curl_close($ch);
		if($lan==10000) {
			$res 	= str_replace('",","','","',$res);
			$res 	= mb_convert_encoding($res, "GBK", "UTF-8");
		} else { //跟踪英文信息
			$newdata['trackingEventList'] = array();
			$data 	= substr($res,strpos($res,"<EMS_INFO>"));
			if($data!='-1') {
				$data	= mb_convert_encoding($data, "GBK", "UTF-8");
				$data	= '<?xml version="1.0" encoding="UTF-8"?>
							<RESPONSE_INFO>
							'.$data.'
							</RESPONSE_INFO>';
				$data 	= json_encode(simplexml_load_string($data));
				$data	= json_decode($data, true);
				$key	= 0;
				foreach ($data['TRACK_DATA']['DATETIME'] as $v) {
					array_push($newdata['trackingEventList'], array('date'=>$data['TRACK_DATA']['DATETIME'][$key], 'place'=>$data['TRACK_DATA']['PLACE'][$key], 'details'=>$data['TRACK_DATA']['INFO'][$key]));
					$key++;
				}
				$res	= json_encode($newdata);
			}
		}
		//增加接口调用日志
		$logFile	= WEB_PATH."log/track_number_info/".date('Y')."/".date('m')."/".date('Y-m-d').".log";
		$log		= date('Y-m-d H:i:s')."==={$url}==={$tid}==={$type}==={$lan}==={$res}\n";
		if(function_exists('write_a_file')) {
			write_a_file($logFile, $log);
		}		
		return $res;
	}
	
	/**
	 * TransOpenApiModel::usCalcShipCost()
	 * 海外仓运输方式+运费计算
	 * @param float $weight 重量
	 * @param string $postcode 邮编
	 * @param string $other 其它待定（暂不用）
	 * @return  array;
	 */
	public static function usCalcShipCost($weight, $postcode , $other=""){
		self::initDB();
		$shipCost  			= '';
		$carrier   			= '';
		$zipCode   			= substr($postcode, 0, 3);//只需取邮编前3位数字
		$getZone   			= "SELECT zone FROM trans_usa_zone_postcode WHERE zip_code like '%$zipCode%'";
		$getZone   			= self::$dbConn->query($getZone);
		$getZone   			= self::$dbConn->fetch_array($getZone);
		$zone      			= $getZone['zone'];//邮编所属分区
		$weight_g  	 		= $weight * 1000;//kg转换成g;
		$weight_oz 	 		= ceil($weight_g / 28.35);//g转换成盎司
		$weight_lbs 		= ceil($weight / 0.4536);//kg转换成磅
		if($weight_oz <= 13) {//重量小于13盎司直接选USPS运输方式
			$getUspsCost 	= "SELECT cost FROM trans_freight_usps_calcfree WHERE weight = '{$weight_oz}' AND unit = 'oz'";
			$getUspsCost 	= self::$dbConn->query($getUspsCost);
			$getUspsCost 	= self::$dbConn->fetch_array($getUspsCost);
			$shipCost    	= $getUspsCost[0]['cost'];
			$carrier     	= 'USPS';
		} else {
			$getUspsCost 	= "SELECT cost FROM trans_freight_usps_calcfree WHERE weight = '{$weight_lbs}' AND zone = '{$zone}' AND unit = 'lbs'";
			$getUspsCost 	= self::$dbConn->query($getUspsCost);
			$getUspsCost 	= self::$dbConn->fetch_array($getUspsCost);
			$uspsCost    	= $getUspsCost['cost'];//USPS运费
			$getUpsCost  	= "SELECT cost FROM trans_freight_ups_calcfree WHERE weight = '{$weight_lbs}' AND zone = '{$zone}' AND unit = 'lbs'";
			$getUpsCost  	= self::$dbConn->query($getUpsCost);
			$getUpsCost  	= self::$dbConn->fetch_array($getUpsCost);
			$upsCost     	= $getUpsCost['cost'];//UPS运费
			$upsCost     	= $upsCost*(1+0.07)+2.8; //添加 燃油附加费
			if($uspsCost <= $upsCost) {//运费对比
				$shipCost 	= $uspsCost;
				$carrier  	= 'USPS';
			} else {
				$shipCost 	= $upsCost;
				$carrier  	= 'UPS Ground';
			}
		}
		return array('carrier'=>$carrier, 'fee'=>$shipCost);
	}	
	
	/**
	 * TransOpenApiModel::getTracknumSimpleInfo()
	 * 获取跟踪号简明信息
	 * @param string $tracknum 跟踪号
	 * @param string $is_wedo 是否为运德物流跟踪号
	 * @return  array
	 */
	public static function getTracknumSimpleInfo($tracknum,$is_wedo=0){
		self::initDB();
		if($is_wedo) {
			$sql	= "SELECT orderSn,scanTime,toCountry,platAccount,platForm FROM trans_track_wedo_number where trackNumber = '{$tracknum}' AND is_delete = 0";
		} else {
			$sql 	= "SELECT orderSn,scanTime,toCountry,weight,toCity,platAccount,fhTime,recordId,carrierId FROM trans_track_number where trackNumber = '{$tracknum}' AND is_delete = 0";
		}
		$query	 	= self::$dbConn->query($sql);
		if($query) {
			$res 	= self::$dbConn->fetch_array_all($query);
			return $res;	
		} else {
			self::$errCode = 10000;
			self::$errMsg  = "获取数据失败！";
			return false;
		}
	}
	
	/**
	 * TransOpenApiModel::getShipAddByCarrierId()
	 * @param int $carrierId 运输方式ID
	 * 根据运输方式ID获取发货地址信息
	 * @return  array
	 */
	public static function getShipAddByCarrierId($carrierId){
		self::initDB();
		$sql	 =	"SELECT addressId FROM trans_address_carrier_relation WHERE carrierId = '{$carrierId}' AND is_delete = 0 LIMIT 1";
		$query	 =	self::$dbConn->query($sql);
		if($query) {
			$res = self::$dbConn->fetch_array($query);
			return $res;	
		} else {
			self::$errCode = 10000;
			self::$errMsg  = "获取数据失败！";
			return false;
		}	
	}
	
	/**
	 * TransOpenApiModel::getShipAddress()
	 * 获取发货地址
	 * @return  array
	 */
	public static function getShipAddress(){
		self::initDB();
		$sql	 =	"SELECT id,addressNameCn,addressNameEn FROM trans_shipping_address where is_delete = 0";
		$query	 =	self::$dbConn->query($sql);
		if($query) {
			$res = self::$dbConn->fetch_array_all($query);
			return $res;	
		} else {
			self::$errCode = 10000;
			self::$errMsg  = "获取数据失败！";
			return false;
		}	
	}
	
	/**
	 * TransOpenApiModel::getCountryBySmall()
	 * 用小语种国家名取得英文国家名
	 * @param string $smallCountry 小语种国家
	 * @return  string 英文国家名;
	 */
	public static function getCountryBySmall($smallCountry){
		self::initDB();
		$sql	 =	"SELECT countryName FROM trans_countries_small_comparison where small_country='{$smallCountry}' AND is_delete = 0";
		$query	 =	self::$dbConn->query($sql);
		if($query) {
			$res = self::$dbConn->fetch_array($query);
			return $res['countryName'];	
		} else {
			self::$errCode = 10000;
			self::$errMsg  = "获取数据失败！";
			return false;
		}	
	}
	
	/**
	 * TransOpenApiModel::getSmallCountryByEn()
	 * 用标准英文国家名取得全部小语种国家名
	 * @param string $country 标准英文国家
	 * @return  array;
	 */
	public static function getSmallCountryByEn($country){
		self::initDB();
		$sql	 =	"SELECT small_country FROM trans_countries_small_comparison where countryName = '{$country}' AND is_delete = 0";
		$query	 =	self::$dbConn->query($sql);
		if($query) {
			return self::$dbConn->fetch_array_all($query);
		} else {
			self::$errCode = 10000;
			self::$errMsg  = "获取数据失败！";
			return false;
		}	
	}
	
	/**
	 * TransOpenApiModel::getCountryBySn()
	 * 用国家简称取得英文国家名
	 * @param string $countrySn 国家简称
	 * @return  array 英文国家名;
	 */
	public static function getCountryBySn($countrySn){
		self::initDB();
		$sql	 =	"SELECT countryNameEn FROM trans_countries_standard where countrySn='{$countrySn}' AND is_delete = 0";
		$query	 =	self::$dbConn->query($sql);
		if($query) {
			$res = self::$dbConn->fetch_array($query);
			return $res['countryNameEn'];	
		} else {
			self::$errCode = 10000;
			self::$errMsg  = "获取数据失败！";
			return false;
		}	
	}
	
	/**
	 * TransOpenApiModel::getTrackCarriers()
	 * 获取运输方式跟踪平台列表
	 * @param string $where 预留
	 * @return  array 
	 */
	public static function getTrackCarriers($where){
		self::initDB();
		$sql 	 = "SELECT trackName FROM trans_track_carrier WHERE is_delete = 0 GROUP BY trackName ORDER BY id ASC";
		$query	 = self::$dbConn->query($sql);
		if($query) {
			$res = self::$dbConn->fetch_array_all($query);
			return $res;	
		} else {
			self::$errCode = 10000;
			self::$errMsg  = "获取数据失败！";
			return false;
		}	
	}
	
	/**
	 * TransOpenApiModel::getPlatForm()
	 * 获取运输方式平台列表
	 * @param string $type 预留
	 * @return  array 
	 */
	public static function getPlatForm($type){
		self::initDB();
		$sql 	 = "SELECT id,platformNameEn,platformNameCn FROM trans_platform WHERE is_delete = 0";
		$query	 = self::$dbConn->query($sql);
		if($query) {
			$res = self::$dbConn->fetch_array_all($query);
			return $res;	
		} else {
			self::$errCode = 10000;
			self::$errMsg  = "获取数据失败！";
			return false;
		}	
	}
	
	/**
	 * TransOpenApiModel::getCarrierByPlatFormId()
	 * 根据平台ID获取运输方式列表
	 * @param int $id 平台ID 
	 * @return  json string 
	 */
	public static function getCarrierByPlatFormId($id){
		self::initDB();
		$sql 	= "SELECT
					b.id,
					b.carrierNameCn,
					b.carrierNameEn
					FROM
					trans_carrierName AS a
					INNER JOIN trans_carrier AS b ON a.carrierId = b.id
					WHERE platformId = {$id} AND b.is_delete = 0";
		$query	= self::$dbConn->query($sql);
		if($query) {
			$res = self::$dbConn->fetch_array_all($query);
			return $res;	
		} else {
			self::$errCode = 10000;
			self::$errMsg  = "获取数据失败！";
			return false;
		}
	}
	
	/**
	 * TransOpenApiModel::getApiCompetenceList()
	 * 获取API运输方式列表
	 * @return  array 
	 */
	public static function getApiCompetenceList(){
		self::initDB();
		$sql		= "SELECT id,apiName FROM `trans_open_api` WHERE is_delete = 0";		
		$query	 	=	self::$dbConn->query($sql);
		if($query) {
			$res 	= self::$dbConn->fetch_array_all($query);
			return $res;	
		} else {
			self::$errCode = 10000;
			self::$errMsg  = "获取数据失败！";
			return false;
		}	
	}
	
	/**
	 * TransOpenApiModel::getCarrierOpenList()
	 * 获取开放运输方式列表
	 * @return  array 
	 */
	public static function getCarrierOpenList(){
		self::initDB();
		$sql		= "SELECT
						a.carrierId,
						a.carrierAdd,
						a.carrierAbb,
						a.carrierEn,
						a.carrierIndex,
						a.carrierDiscount,
						a.carrierAging,
						a.carrierNote,
						b.carrierNameCn,
						b.carrierNameEn,
						c.addressNameCn
						FROM `trans_carrier_open` AS a
						INNER JOIN `trans_carrier` AS b ON a.carrierId = b.id
						INNER JOIN `trans_shipping_address` AS c ON a.carrierAdd = c.id
						WHERE a.is_delete = 0";		
		$query	 	= self::$dbConn->query($sql);
		if($query) {
			$res 	= self::$dbConn->fetch_array_all($query);
			return $res;	
		} else {
			self::$errCode = 10000;
			self::$errMsg  = "获取数据失败！";
			return false;
		}	
	}
	
	/**
	 * TransOpenApiModel::getCarrier()
	 * 获取运输方式列表
	 * @param int $type 0非快递，1快递，2全部默认
	 * @return  array 
	 */
	public static function getCarrier($type){
		self::initDB();
		if($type == 2) {
			$sql	 	=	"SELECT id,type,carrierNameCn,carrierNameEn,is_track,carrierAli,carrierAbb,carrierIndex,carrierLogo FROM trans_carrier WHERE is_delete = 0";
		} else {
			$sql	 	=	"SELECT id,type,carrierNameCn,carrierNameEn,is_track,carrierAli,carrierAbb,carrierIndex,carrierLogo FROM trans_carrier where type = {$type} AND is_delete = 0";
		}		
		$query	 		=	self::$dbConn->query($sql);
		if($query) {
			$res = self::$dbConn->fetch_array_all($query);
			return $res;	
		} else {
			self::$errCode = 10000;
			self::$errMsg  = "获取数据失败！";
			return false;
		}	
	}

	/**
	 * TransOpenApiModel::getCarrierAbb()
	 * 获取运输方式简码列表
	 * @return  array 
	 */
	public static function getCarrierAbb(){
		self::initDB();
		$sql	 			= "SELECT a.carrierNameCn,a.carrierNameEn,a.carrierAbb,a.carrierIndex,a.carrierLogo,b.addressId,c.addressNameCn FROM trans_carrier AS a 
								LEFT JOIN trans_address_carrier_relation AS b ON a.id = b.carrierId
								LEFT JOIN trans_shipping_address AS c ON b.addressId = c.id
								WHERE a.is_delete = 0 AND b.is_delete = 0 AND c.is_delete = 0";
		$query	 			= self::$dbConn->query($sql);
		if($query) {
			$res = self::$dbConn->fetch_array_all($query);
			return $res;	
		} else {
			self::$errCode 	= 10000;
			self::$errMsg  	= "获取数据失败！";
			return false;
		}	
	}	
	
	/**
	 * TransOpenApiModel::getCarrierPlatShip()
	 * 根据运输方式ID和平台ID获取平台运输方式信息
	 * @param int $carrierId 运输方式ID
	 * @param int $platId 平台ID
	 * @return  json sting 
	 */
	public static function getCarrierPlatShip($carrierId, $platId){
		self::initDB();
		$sql	 =	"SELECT id,shipName,shipService FROM trans_carrier_platform_relation where carrierId = {$carrierId} AND platId = {$platId} AND is_delete = 0 LIMIT 1";
		$query	 =	self::$dbConn->query($sql);
		if($query) {
			$res = self::$dbConn->fetch_array($query);
			return $res;	
		} else {
			self::$errCode = 10000;
			self::$errMsg  = "获取数据失败！";
			return false;
		}	
	}
	
	/**
	 * TransOpenApiModel::getCountriesChina()
	 * 根据发货地址ID获取运输方式信息
	 * @param int $areaId 区域ID
	 * @return  array 
	 */
	public static function getCountriesChina($areaId){
		self::initDB();
		$condition	= 1;
		$condition	.= !empty($areaId) ? " AND id = {$areaId}" : "";
		$sql	 	=	"SELECT id,countryName FROM trans_countries_china WHERE {$condition} AND is_delete = 0";
		$query	 	=	self::$dbConn->query($sql);
		if($query) {
			$res 	= self::$dbConn->fetch_array_all($query);
			return $res;	
		} else {
			self::$errCode = 10000;
			self::$errMsg  = "获取数据失败！";
			return false;
		}	
	}
	
	/**
	 * TransOpenApiModel::getCarrierByAdd()
	 * 根据发货地址ID获取运输方式信息
	 * @param int $addId 发货地址ID
	 * @return  array 
	 */
	public static function getCarrierByAdd($addId){
		self::initDB();
		$sql	 =	"SELECT a.id,a.carrierNameCn,a.carrierNameEn,a.carrierAli FROM trans_carrier as a 
					INNER JOIN trans_address_carrier_relation as b ON a.id = b.carrierId
					WHERE b.addressId = {$addId} AND a.is_delete = 0";
		$query	 =	self::$dbConn->query($sql);
		if($query) {
			$res = self::$dbConn->fetch_array_all($query);
			return $res;	
		} else {
			self::$errCode = 10000;
			self::$errMsg  = "获取数据失败！";
			return false;
		}	
	}
	
	/**
	 * TransOpenApiModel::getCarrierById()
	 * 根据运输方式ID获取运输方式信息
	 * @param int $id 运输方式ID
	 * @return  array
	 */
	public static function getCarrierById($id){
		self::initDB();
		$sql	 =	"SELECT id,carrierNameCn,carrierNameEn,carrierAli FROM trans_carrier where id={$id} AND is_delete = 0";
		$query	 =	self::$dbConn->query($sql);
		if($query) {
			$res = self::$dbConn->fetch_array($query);
			return $res;	
		} else {
			self::$errCode = 10000;
			self::$errMsg  = "获取数据失败！";
			return false;
		}	
	}
	
	/**
	 * TransOpenApiModel::getCarrierByAbb()
	 * 根据运输方式简码获取运输方式信息
	 * @param string $abb 运输方式简码
	 * @return  array
	 */
	public static function getCarrierByAbb($abb){
		self::initDB();
		$sql	 =	"SELECT id FROM trans_carrier where carrierAbb = '{$abb}' AND is_delete = 0 LIMIT 1";
		$query	 =	self::$dbConn->query($sql);
		if($query) {
			$res = self::$dbConn->fetch_array($query);
			return $res;	
		} else {
			self::$errCode = 10000;
			self::$errMsg  = "获取数据失败！";
			return false;
		}	
	}
	
	/**
	 * TransOpenApiModel::getCarrierChannel()
	 * 获取某个运输方式的渠道信息
	 * @param int $carrierId 运输方式ID
	 * @param int $chId 渠道ID
	 * @return  array ;
	 */
	public static function getCarrierChannel($carrierId="", $chId=""){
		self::initDB();
		$condition = "1";
		if(!empty($carrierId)) {
			$condition .= " AND a.carrierId IN({$carrierId}) ";
		}
		if(!empty($chId)) {
			$condition .= " AND a.id IN({$chId}) ";
		}
		$sql	 =	"SELECT a.id,a.channelName,a.timeDiff,a.channelAlias,b.id as carrierId FROM trans_channels AS a 
					LEFT JOIN trans_carrier	AS b ON a.carrierId = b.id
					WHERE {$condition} AND a.is_delete = 0";
		$query	 =	self::$dbConn->query($sql);
		if($query) {
			$res = self::$dbConn->fetch_array_all($query);
			return $res;	
		} else {
			self::$errCode = 10000;
			self::$errMsg  = "获取数据失败！";
			return false;
		}	
	}
	
	/**
	 * TransOpenApiModel::getCarrierChannelByPostName()
	 * 根据收寄局名获取渠道ID
	 * @param string $postName 收寄局名
	 * @param int $carrierId 运输方式ID
	 * @return int ;
	 */
	public static function getCarrierChannelByPostName($carrierId, $postName){
		self::initDB();
		$sql	 =	"SELECT id FROM trans_channels where carrierId = {$carrierId} AND (postName = '{$postName}' OR postName1 = '{$postName}' OR postName2 = '{$postName}') AND is_delete = 0";
		$query	 =	self::$dbConn->query($sql);
		if($query) {
			$res = self::$dbConn->fetch_array($query);
			return is_array($res) ? $res['id'] : 0;	
		} else {
			self::$errCode = 10000;
			self::$errMsg  = "获取数据失败！";
			return false;
		}	
	}
	
	/**
	 * TransOpenApiModel::getPartition()
	 * 获取运输方式渠道分区列表
	 * @param int $carrierId 运输方式ID(选填)
	 * @param string $countryName 国家名(选填)
	 * @return  array
	 */
	public static function getPartition($carrierId, $countryName){
		self::initDB();
		$condition	= '';
		if($carrierId) $condition	.= " AND a.carrierId = {$carrierId}";
		if($countryName) $condition	.= " AND b.countries like '%{$countryName}%'";
		$sql	 =	"SELECT b.id,b.channelId,b.partitionCode,b.partitionAli,b.countries,b.returnAddress,b.partitionName,b.returnAddHtml FROM trans_channels AS a
					INNER JOIN trans_partition AS b ON a.id = b.channelId
					WHERE a.is_delete = 0 AND a.is_enable = 1 AND b.enable = 1 $condition";
		$query	 =	self::$dbConn->query($sql);
		if($query) {
			$res = self::$dbConn->fetch_array_all($query);
			return $res;	
		} else {
			self::$errCode = 10000;
			self::$errMsg  = "获取数据失败！";
			return false;
		}	
	}
	
	/**
	 * TransOpenApiModel::getCountriesStandardByName()
	 * 根据国家名获取标准国家信息
	 * @param string $countryName 国家名
	 * @return  array 
	 */
	public static function getCountriesStandardByName($countryName){
		self::initDB();
		$sql	 = "SELECT id,countryNameCn,countryNameEn FROM trans_countries_standard WHERE is_delete = 0 AND countryNameEn = '{$countryName}' LIMIT 1";
		$query	 = self::$dbConn->query($sql);
		if($query) {
			$res = self::$dbConn->fetch_array($query);
			return $res;	
		} else {
			self::$errCode = 10000;
			self::$errMsg  = "获取数据失败！";
			return 0;
		}	
	}
	
	/**
	 * TransOpenApiModel::getCountriesStandardById()
	 * 根据国家ID获取标准国家信息
	 * @param string $countryId 国家ID
	 * @return  array 
	 */
	public static function getCountriesStandardById($countryId){
		self::initDB();
		$sql	 = "SELECT id,countryNameCn,countryNameEn FROM trans_countries_standard WHERE is_delete = 0 AND id = '{$countryId}' LIMIT 1";
		$query	 = self::$dbConn->query($sql);
		if($query) {
			$res = self::$dbConn->fetch_array($query);
			return $res;	
		} else {
			self::$errCode = 10000;
			self::$errMsg  = "获取数据失败！";
			return 0;
		}	
	}
	
	/**
	 * TransOpenApiModel::getCountriesStandard()
	 * 获取获取全部或部分标准国家
	 * @param string $type ALL全部，CN中文，EN英文
	 * @param string $country 国家，默认空
	 * @return  array 
	 */
	public static function getCountriesStandard($type="ALL", $country=""){
		self::initDB();
		switch ($type) {
			case "ALL":
				$sql	=	"SELECT id,countryNameEn,countryNameCn,countrySn FROM trans_countries_standard WHERE is_delete = 0 ORDER BY countryNameEn ASC, id DESC";
			break;
			case "EN":
				$sql	=	"SELECT countryNameEn FROM trans_countries_standard WHERE is_delete = 0 AND countryNameCn = '{$country}' LIMIT 1";
			break;
			case "CN":
				$sql 	=	"SELECT countryNameCn FROM trans_countries_standard WHERE is_delete = 0 AND countryNameEn = '{$country}' LIMIT 1";
			break;
		}
		$query 			=	self::$dbConn->query($sql);
		if($query) {
			if($type == "ALL") {
				$res 	= self::$dbConn->fetch_array_all($query);
			} else {
				$res 	= self::$dbConn->fetch_row($query);
				$res 	= array("country"	=> $res[0]);
			}
			return $res;	
		} else {
			self::$errCode = 10000;
			self::$errMsg  = "获取数据失败！";
			return false;
		}	
	}
	
	/**
	 * TransOpenApiModel::checkTrackNumber()
	 * 检查跟踪号是否存在
	 * @param string $tid 跟踪号
	 * @param int $carrierId 运输方式ID
	 * @return bool;
	 */
	public static function checkTrackNumber($tid, $carrierId){
		self::initDB();
		$sql	 =	"SELECT count(*) as total FROM trans_track_number where carrierId = {$carrierId} AND trackNumber='{$tid}' AND is_delete = 0";
		$query	 =	self::$dbConn->query($sql);
		if($query) {
			$res = self::$dbConn->fetch_array($query);
			return $res['total'];	
		} else {
			self::$errCode = 10000;
			self::$errMsg  = "执行SQL语句失败！";
			return false;
		}	
	}
	
	/**
	 * TransOpenApiModel::addTrackNumber()
	 * 添加跟踪号信息到数据库
	 * @param array $data 数据集
	 * @return bool
	 */
	public static function addTrackNumber($data){
		self::initDB();
		$sql 		= array2sql($data);
		$sql 		= "INSERT INTO `trans_track_number` SET ".$sql; 
		$query		= self::$dbConn->query($sql);
		if($query) {
			$rows 	= self::$dbConn->affected_rows();           
			if($rows) {
				return $rows;
			} else {
				self::$errCode	= 10001;
				self::$errMsg	= "插入数据失败";
				return false;
			}	
		} else {
			self::$errCode		= 10000;
			self::$errMsg		= "执行SQL语句出错";
			return false;
		}
	}
	
	/**
	 * TransOpenApiModel::updateTrackNumber()
	 * 更新跟踪号信息到数据库
	 * @param string $tid 跟踪号
	 * @param array $data 数据集
	 * @return bool
	 */
	public static function updateTrackNumber($tid, $data){
		self::initDB();
		$sql 	= array2sql($data);
		$sql 	= "UPDATE `trans_track_number` SET ".$sql." WHERE trackNumber = '{$tid}'";; 
		$query	= self::$dbConn->query($sql);
		if($query) {
			return true;
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "执行SQL语句出错";
			return false;
		}
	}
	
	/**
	 * TransOpenApiModel::updateTrackOrderInfo()
	 * 更新跟踪号信息到数据库
	 * @param string $oid 订单号
	 * @param array $data 数据集
	 * @return bool
	 */
	public static function updateTrackOrderInfo($oid, $data){
		self::initDB();
		$sql 	= array2sql($data);
		$sql 	= "UPDATE `trans_track_number` SET ".$sql." WHERE orderSn = '{$oid}'";; 
		$query	= self::$dbConn->query($sql);
		if($query) {
			return true;
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "执行SQL语句出错";
			return false;
		}
	}
	
	/**
	 * TransOpenApiModel::addTrackNumberWarnInfo()
	 * 添加跟踪号预警信息到数据库
	 * @param array $data 数据集
	 * @return bool
	 */
	public static function addTrackNumberWarnInfo($data){
		self::initDB();
		$sql 		= array2sql($data);
		$sql 		= "INSERT INTO `trans_track_number_warn_info` SET ".$sql; 
		$query		= self::$dbConn->query($sql);
		if($query) {
			$rows 	= self::$dbConn->affected_rows();           
			if($rows) {
				return $rows;
			} else {
				self::$errCode	= 10001;
				self::$errMsg	= "插入数据失败";
				return false;
			}
		} else {
			self::$errCode		= 10000;
			self::$errMsg		= "执行SQL语句出错";
			return false;
		}
	}
	
	/**
	 * TransOpenApiModel::getTrackNumberDetailTotal()
	 * 返回某个跟踪号详细信息条数
	 * @param string $tid 表ID
	 * @param string $trackNumber 跟踪号
	 * @return integer 总数量 
	 */
	public static function getTrackNumberDetailTotal($tid, $trackNumber){
		self::initDB();
		$sql 	= "SELECT count(*)	FROM trans_track_number_detail_{$tid} WHERE trackNumber = '{$trackNumber}'";
		$query	= self::$dbConn->query($sql);
		if($result=self::$dbConn->query($sql)) {
			$data = self::$dbConn->fetch_row($result);
			return $data[0];
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "获取数据失败";
			return 0;
		}
	}
	
	/**
	 * TransOpenApiModel::getTrackNumberDetailTotalByCountry()
	 * 返回某个跟踪号详细信息条数
	 * @param string $tid 表ID
	 * @param string $trackNumber 跟踪号
	 * @return integer 总数量 
	 */
	public static function getTrackNumberDetailTotalByCountry($tid, $trackNumber){
		self::initDB();
		$sql 	= "SELECT count(*)	FROM trans_track_number_detail_{$tid}_country WHERE trackNumber = '{$trackNumber}'";
		$query	= self::$dbConn->query($sql);
		if($result=self::$dbConn->query($sql)) {
			$data = self::$dbConn->fetch_row($result);
			return $data[0];
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "获取数据失败";
			return 0;
		}
	}
	
	/**
	 * TransOpenApiModel::addTrackNumberDetail()
	 * 添加跟踪号详细信息到数据库
	 * @param int $tid 表ID
	 * @param string $data 插入内容，支持批量插入逗号分隔
	 * @return bool
	 */
	public static function addTrackNumberDetail($tid, $data){
		self::initDB();
		$trackNumber	= $data[count($data)-1];
		$total			= self::getTrackNumberDetailTotal($tid, $trackNumber);
		array_pop($data);
		if($total == count($data)) return true;
		if($total < count($data) && $total > 0) {
			for ($i=0; $i < $total; $i++) {
				unset($data[$i]);
			}
		}
		$data		= implode(",",$data);
		$sql 		= "INSERT INTO `trans_track_number_detail_{$tid}`(`trackNumber`,`postion`,`event`,`trackTime`,`addTime`) VALUES ".$data; 
		$query		= self::$dbConn->query($sql);
		if($query) {
			$rows 	= self::$dbConn->affected_rows();           
			if($rows) {
				return $rows;
			} else {
				self::$errCode	= 10001;
				self::$errMsg	= "插入数据失败";
				return false;
			}
		} else {
			self::$errCode		= 10000;
			self::$errMsg		= "执行SQL语句出错";
			return false;
		}
	}
	
	/**
	 * TransOpenApiModel::addTrackNumberDetailByCountry()
	 * 添加跟踪号详细英文信息到数据库
	 * @param int $tid 表ID
	 * @param string $data 插入内容，支持批量插入逗号分隔
	 * @return bool
	 */
	public static function addTrackNumberDetailByCountry($tid, $data){
		self::initDB();
		$trackNumber	= $data[count($data)-1];
		$total			= self::getTrackNumberDetailTotalByCountry($tid, $trackNumber);
		array_pop($data);
		if($total == count($data)) return true;
		if($total < count($data) && $total > 0) {
			for ($i=0; $i < $total; $i++) {
				unset($data[$i]);
			}
		}
		$data		= implode(",",$data);
		$sql 		= "INSERT INTO `trans_track_number_detail_{$tid}_country`(`trackNumber`,`postion`,`event`,`trackTime`,`addTime`) VALUES ".$data; 
		$query		= self::$dbConn->query($sql);
		if($query) {
			$rows 	= self::$dbConn->affected_rows();           
			if($rows) {
				return $rows;
			} else {
				self::$errCode	= 10001;
				self::$errMsg	= "插入数据失败";
				return false;
			}
		} else {
			self::$errCode		= 10000;
			self::$errMsg		= "执行SQL语句出错";
			return false;
		}
	}
	
	/**
	 * TransOpenApiModel::delTrackNumberWarnInfo()
	 * 删除某个跟踪号预警信息
	 * @param string $tid 跟踪号
	 * @return bool 
	 */
	public static function delTrackNumberWarnInfo($tid){
		self::initDB();
		$sql 	= "DELETE FROM trans_track_number_warn_info WHERE trackNumber = '{$tid}'";
		$query	= self::$dbConn->query($sql);
		if($query) {
			return true;
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "执行SQL语句失败";
			return false;
		}
	}
	
	/**
	 * TransOpenApiModel::addTrackWarnInfo()
	 * 添加跟踪号预警信息到数据库
	 * @param string $data 插入内容，支持批量插入逗号分隔
	 * @return bool
	 */
	public static function addTrackWarnInfo($data){
		self::initDB();
		$trackNumber	= $data[count($data)-1];
		$res			= self::delTrackNumberWarnInfo($trackNumber);
		array_pop($data);
		$data			= implode(",",$data);
		$sql 			= "INSERT INTO `trans_track_number_warn_info`(`trackNumber`,`carrierId`,`channelId`,`nodeId`,`is_warn`,`warnDays`,`warnStartTime`,`warnEndTime`,`processTime`) VALUES ".$data; 
		$query			= self::$dbConn->query($sql);
		if($query) {
			$rows 		= self::$dbConn->affected_rows();           
			if($rows) {
				return $rows;
			} else {
				self::$errCode	= 10001;
				self::$errMsg	= "插入数据失败";
				return false;
			}
		} else {
			self::$errCode		= 10000;
			self::$errMsg		= "执行SQL语句出错";
			return false;
		}
	}
	
	/**
	 * TransOpenApiModel::getTrackNumberList()
	 * 分页获取跟踪号列表
	 * @param int $page 页码
	 * @param int $pagenum 每页条数
	 * @param int $carrierId 运输方式ID
	 * @param string $where 条件
	 * @param int $hours 小时
	 * @return  array 
	 */
	public static function getTrackNumberList($page, $pagenum, $carrierId, $where, $hours=0 , $order="ASC"){
		self::initDB();
		$condition	= "";
		$start		= ($page-1)*$pagenum;
		if(!empty($carrierId)) $condition .= "a.carrierId = {$carrierId} AND ";
		$condition .= empty($where) ? "status <> 3" : $where;
		$condition .= empty($hours) ? "" : " AND trackTime<=".(time() - $hours*3600);		
		$sql		= "SELECT
						a.trackNumber,
						a.carrierId,
						a.`status`,
						a.scanTime,
						a.orderSn,
						a.toCountry,
						b.trackName
						FROM trans_track_number AS a
						LEFT JOIN trans_track_carrier AS b ON a.carrierId = b.carrierId
						WHERE {$condition} AND a.is_delete = 0 AND b.is_delete = 0
						ORDER BY a.id {$order} LIMIT $start,$pagenum";
		echo $sql,"\n";
		$query		= self::$dbConn->query($sql);
		if($query) {
			$res	= self::$dbConn->fetch_array_all($query);
			return $res;
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "获取数据失败";
			return false;
		}	
	}
	
	/**
	 * TransOpenApiModel::getTrackNameByCountry()
	 * 获取某个运输方式目的地国家跟踪名称
	 * @param int $carrierId 运输方式ID
	 * @param string $country 国家名
	 * @return  array 
	 */
	public static function getTrackNameByCountry($carrierId, $country){
		self::initDB();
		$sql 		= "SELECT * FROM trans_track_carrier_country WHERE carrierId = '{$carrierId}' AND countryName = '{$country}' AND is_delete = 0";
		$query		= self::$dbConn->query($sql);
		if($query) {
			$res	= self::$dbConn->fetch_array($query);
			return $res;
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "获取数据失败";
			return false;
		}	
	}
	
	/**
	 * TransOpenApiModel::getTrackInfoLocal()
	 * 获取某个跟踪号的本地跟踪信息
	 * @param string $trackNumber 跟踪号
	 * @param int $tid 表ID
	 * @return  array 
	 */
	public static function getTrackInfoLocal($trackNumber, $tid){
		self::initDB();
		$sql		= "SELECT * FROM trans_track_number_detail_{$tid} WHERE trackNumber = '{$trackNumber}' ORDER BY id ASC";
		$query		= self::$dbConn->query($sql);
		if($query) {
			$res	= self::$dbConn->fetch_array_all($query);
			return $res;
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "获取数据失败";
			return false;
		}
	}
	
	/**
	 * TransOpenApiModel::getTrackNodeList()
	 * 获取运输方式预警节点
	 * @param int $id 运输方式ID
	 * @param int $chid 渠道ID
	 * @return  array 
	 */
	public static function getTrackNodeList($id, $chid=0){
		self::initDB();
		$condition 			= "";
		if(!empty($chid)) {
			$condition		= " AND channelId = {$chid} "; 
		}
		$sql				= "SELECT
								id,carrierId,nodeName,nodeDays,nodeKey,nodePlace FROM trans_track_node 
								WHERE carrierId = {$id} {$condition} AND is_delete = 0 ORDER BY id ASC";
		$query				= self::$dbConn->query($sql);
		if($query) {
			$res			= self::$dbConn->fetch_array_all($query);
			return $res;
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "获取数据失败";
			return false;
		}	
	}
	
	/**
	 * TransOpenApiModel::getRandTrackNodeList()
	 * 随机获取某个运输方式某个渠道节点
	 * @param int $id 运输方式ID
	 * @return array 
	 */
	public static function getRandTrackNodeList($id){
		self::initDB();
		$sql		= "SELECT * FROM trans_track_node WHERE carrierId = {$id} AND is_delete = 0 LIMIT 1";
		$query		= self::$dbConn->query($sql);
		if($query) {
			$res	= self::$dbConn->fetch_array($query);
			return self::getTrackNodeList($id, $res['channelId']);
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "获取数据失败";
			return false;
		}	
	}
	
	/**
	 * TransOpenApiModel::getTrackCarrierList()
	 * 获取跟踪号运输方式列表
	 * @return  array 
	 */
	public static function getTrackCarrierList(){
		self::initDB();
		$sql		= "SELECT id,carrierName FROM trans_track_carrier_name WHERE is_delete = 0 ORDER BY id ASC";
		$query		= self::$dbConn->query($sql);
		if($query) {
			$res	= self::$dbConn->fetch_array_all($query);
			return $res;
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "获取数据失败";
			return false;
		}	
	}
	
	/**
	 * TransOpenApiModel::invalidTrackNumbers()
	 * 作废订单跟踪号
	 * @param int $orderId 订单编号ID
	 * @return  array 
	 */
	public static function invalidTrackNumbers($orderId){
		self::initDB();
		$times		= time();
		$sql		= "UPDATE trans_track_numbers SET is_delete = 1,editTime = '{$times}',edit_user_id = 0 WHERE orderId = '{$orderId}'";
		$query		= self::$dbConn->query($sql);
		if($query) {
			$rows 	= self::$dbConn->affected_rows(); 
			if($rows) {
				return array("orderId" => "订单编号:{$orderId}-{$rows}跟踪号作废成功!");
			} else {
				self::$errCode	= 10001;
				self::$errMsg	= "订单ID:{$orderId}不存在或删除数据失败!";
				return false;
			}
		} else {
            self::$errCode		= 10000;
			self::$errMsg		= "执行SQL语句失败！";
			return false;
		}
	}
	
	/**
	 * TransOpenApiModel::assignTrackNumbers()
	 * 分配跟踪号
	 * @param int $carrierId 运输方式ID
	 * @param int $orderId 订单编号ID
	 * @param int $channelId 渠道ID（可选）
	 * @param string $country 所属国家（可选）
	 * @return  array 
	 */
	public static function assignTrackNumbers($carrierId, $orderId, $country='', $channelId=0){
		self::initDB();
		$where			= '';
		if(empty($country)) {
			$where 		= "carrierId = '{$carrierId}' AND orderId = '{$orderId}' AND (countrys = '' OR countrys IS NULL)";
		} else {
			$where 		= "carrierId = '{$carrierId}' AND orderId = '{$orderId}' AND countrys = '{$country}'";
		}
		//已经分配的订单跟踪号再次分配时直接返回原来的跟踪号
		$res			= TrackNumberModel::assignTrackNumber($where);
		if(!empty($res['trackNumber'])) {
			return array("trackNumber"=>$res['trackNumber']);
		}
		//兼容渠道
		if(empty($channelId) && $carrierId != 2) {
			$where		= '';
		} else {
			$where		= " AND channelId = '{$channelId}'";
		}		
		//新订单跟踪号分配
		if(empty($country)) {
			$where 		= "carrierId = '{$carrierId}' AND orderId = 0 AND is_lock = 0 AND (countrys = '' OR countrys IS NULL)".$where;
		} else {
			$where 		= "carrierId = '{$carrierId}' AND orderId = 0 AND is_lock = 0 AND countrys = '{$country}'".$where;
		}
		$res			= TrackNumberModel::assignTrackNumber($where);
		if(empty($res['trackNumber'])) {
			self::$errCode	= 10001;
			self::$errMsg	= "当前运输方式已无跟踪号可以分配";
			return false;
		}
		$tracknum			= $res['trackNumber'];
		$id					= $res['id'];
		//增加锁机制防止并发异常
		$res				= TrackNumberModel::updateTrackNumber($id,array("is_lock"=>1),true);
		if(!$res) {
			self::$errCode	= 10002;
			self::$errMsg	= "订单ID{$orderId}=锁定{$id}=跟踪号:{$tracknum}失败,请不要过度频繁申请跟踪号！";
			return false;
		}
		//更新分配的跟踪号信息
		$data				= array (
								"orderId" 		=> $orderId,
								"assignTime" 	=> time(),
							);
		$res				= TrackNumberModel::updateTrackNumber($id,$data,true);
		if(!$res) {
			self::$errCode	= TrackNumberModel::$errCode;
			self::$errMsg	= TrackNumberModel::$errMsg;
			return false;
		} else {
			return array("trackNumber" 	=> $tracknum);
		}
	}
	
	/**
	 * TransOpenApiModel::autoWarnInfo()
	 * 跟踪号自采集回来自动预警
	 * @param array $data array
	 * @return  bool 
	 */
	public static function autoWarnInfo($data){
		self::initDB();
		$res			= array();
		$ret			= 0;
		$detail			= $data['trackingEventList'];
		$carrierId		= $data['numberInfo']['carrierId'];
		$channelId		= $data['numberInfo']['channelId'];
		$trackNumber	= $data['numberInfo']['trackNumber'];
		$nodeList		= self::getTrackNodeList($data['numberInfo']['carrierId'], $data['numberInfo']['channelId']);
		$warnLevel		= "";
		$nodeEff		= "";
		$nodePlaceEff	= "";
		$internalTime	= 0;
		//$carrierInfo= self::getCarrierChannel($carrierId, $channelId);
		//$timeDiff	= isset($carrierInfo[0]['timeDiff']) ? $carrierInfo[0]['timeDiff'] : 0;//增加时差
		$uptime			= $data['numberInfo']['scanTime'];//跟踪号首次出库时间
		if(in_array($carrierId,array('46','47'))) $uptime	= $uptime - 16*3600;//UPS ground、USPS发货时间采用洛杉矶时间-16小时
		//echo $timeDiff,"==时差\n";
		if(count($nodeList) > 0) {
			foreach ($nodeList as $v) {
				$is_warn	= 0;
				$node_warn	= 0;
				$place_warn	= 0;
				$startTime	= 0;
				$endTime	= 0;
				$realTime	= 0;
				foreach ($detail as $val) {
					$keys	= explode(" ",$v['nodeKey']);
					$hits	= 0;
					foreach ($keys as $key) {
						if(strpos($val['details'],$key) !== false && !in_array($val['details'],array('未妥投'))) {
							$node_warn	= 1;
							$hits		= 1;
							$warnTime	= $uptime + $v['nodeDays']*3600;//预警时间
							$trackTime	= strtotime($val['date']) ? strtotime($val['date']) : strtotime(trim(substr($val['date'],0,strpos($val['date'],' '))));//跟踪时间
							if($trackTime > $warnTime) {
								$is_warn	= 1;
								$startTime	= $uptime;//预警开始时间
								$endTime	= $trackTime;//预警结束时间
								echo "{$v['id']}节点预警开始时间:[",$startTime,"]===结束时间:[",$endTime,"]\n";
								break;
							} else {
								$startTime	= $uptime;//预警开始时间
								$endTime	= $trackTime;//预警结束时间
							}
						}
					}
					if($hits) $uptime 		= $trackTime;
				}
				$nodeEff			.= $node_warn;
				$warnLevel			.= $is_warn;//预警级别
				$nodeName			= $v['nodeName'];
				$nodeId				= $v['id'];
				$warnDays			= $v['nodeDays'];
				if($startTime>0 && $endTime>0) $realTime	= $endTime - $startTime;
				array_push($res, "('{$trackNumber}','{$carrierId}','{$channelId}','{$nodeId}','{$is_warn}','{$warnDays}','{$startTime}','{$endTime}','{$realTime}')");
				$internalTime		+= $realTime;
			}
			$warnLevel	= str_pad($warnLevel,count($nodeList),"0");//补全
			$nodeEff	= str_pad($nodeEff,count($nodeList),"0");//补全
			echo "预警级别:[",$warnLevel,"]=====节点处理效率:[",$nodeEff,"]\n";
			if($warnLevel) { //更新预警级别
				$internalTime	= count($nodeList)>1 ? $internalTime - $realTime : $internalTime;//国内处理时间
				$ret	= self::updateTrackNumber($trackNumber,array("channelId"=>$channelId,"warnLevel"=>$warnLevel,"nodeEff"=>$nodeEff,"internalTime"=>$internalTime));
				print_r($ret);
			}
			array_push($res, $trackNumber);
			print_r($res);//打印预警信息
			return self::addTrackWarnInfo($res);
		} else {
			return "尚未设置预警节点";
		}
	}
	
	/**
	 * TransOpenApiModel::cacheCarrierInfoByChannelAli()
	 * memcache根据渠道别名缓存运输方式方法
	 * @param string $tab 表名
	 * @param string $cacheName 键名
	 * @param string $where 条件
	 * @param int $cacheTime 缓存时间
	 * @param int $is_new 是否强制更新，0否
	 * @return  array 
	 */
	public static function cacheCarrierInfoByChannelAli($tab, $where=1, $cacheName, $cacheTime=86400, $is_new=0){
		self::initDB();
		$memc_obj		= new Cache(C('CACHEGROUP'));
		$cacheName 		= md5("{$cacheName}");
		$cacheInfo		= $memc_obj->get_extral($cacheName);
		if(!empty($cacheInfo) && empty($is_new)) {
			$cacheInfo	= unserialize($cacheInfo);
		} else {
			$sql 		= "SELECT carrierId FROM {$tab} WHERE {$where} AND is_enable = 1 AND is_delete = 0 LIMIT 1";
			$query		= self::$dbConn->query($sql);
			$res		= self::$dbConn->fetch_array($query);
			$carrierId	= isset($res['carrierId']) ? intval($res['carrierId']) : 0;
			$sql 		= "SELECT * FROM trans_carrier WHERE id = '{$res['carrierId']}' AND is_delete = 0 LIMIT 1";
			$query		= self::$dbConn->query($sql);
			$cacheInfo	= self::$dbConn->fetch_array($query);
			$isok 		= $memc_obj->set_extral($cacheName, serialize($cacheInfo), $cacheTime);
		}
		return $cacheInfo;
	}
	
	/**
	 * TransOpenApiModel::cacheTableInfo()
	 * memcache缓存表方法
	 * @param string $tab 表名
	 * @param string $cacheName 键名
	 * @param string $where 条件
	 * @param int $cacheTime 缓存时间
	 * @param int $is_new 是否强制更新，0否
	 * @return  array 
	 */
	public static function cacheTableInfo($tab, $where=1, $cacheName, $cacheTime=86400, $is_new=0){
		self::initDB();
		$memc_obj		= new Cache(C('CACHEGROUP'));
		$cacheName 		= md5("{$cacheName}");
		$cacheInfo		= $memc_obj->get_extral($cacheName);
		if(!empty($cacheInfo) && empty($is_new)) {
			$cacheInfo	= unserialize($cacheInfo);
		} else {
			$sql 		= "SELECT * FROM {$tab} WHERE {$where} AND is_delete = 0";
			$query		= self::$dbConn->query($sql);
			$cacheInfo	= self::$dbConn->fetch_array_all($query);
			$isok 		= $memc_obj->set_extral($cacheName, serialize($cacheInfo), $cacheTime);
		}
		return $cacheInfo;
	}
	
	/**
	 * TransOpenApiModel::cacheExRateInfo()
	 * memcache缓存汇率方法
	 * @param string $fromRate 需要转换的币种
	 * @param string $toRate 目标币种
	 * @param string $cacheName 键名
	 * @param int $cacheTime 缓存时间
	 * @param int $is_new 是否强制更新，0否
	 * @return  array 
	 */
	public static function cacheExRateInfo($fromRate, $toRate, $cacheName, $cacheTime=86400, $is_new=0){
		$memc_obj			= new Cache(C('CACHEGROUP'));
		$cacheName 			= md5("{$cacheName}");
		$cacheInfo			= $memc_obj->get_extral($cacheName);
		if(!empty($cacheInfo) && empty($is_new)) {
			$cacheInfo		= unserialize($cacheInfo);
		} else {
			// 取消暂时屏蔽走刊登系统汇率接口
			$paramList 		= array(
				'method' 		=> 'pa.getCurrencyRateByArr',  //API名称
				'format'		=> 'json',  //返回格式
				'v' 			=> '1.0',   //API版本号
				'username'		=> C('OPEN_SYS_USER'),
				'fromCodeArr'	=> json_encode($fromRate),
				'toCodeArr'		=> json_encode($toRate),
			);
			for($i=0; $i<3; $i++) {
				$cacheInfo	= callOpenSystem($paramList);
				if(!empty($cacheInfo)) break;
			}
			$cacheInfo		= json_decode($cacheInfo, true);
			$cacheInfo		= json_decode($cacheInfo['data'], true);
			// $cacheInfo		= TransOpenApiAct::act_getCurrencyExchange($fromRate,$toRate);
			if(!empty($cacheInfo)) {
				$isok 		= $memc_obj->set_extral($cacheName, serialize($cacheInfo), $cacheTime);
			}
		}
		return $cacheInfo;
	}
	
	/**
	 * TransOpenApiModel::updateCacheTableFee()
	 * 更新价目表memcache缓存方法
	 * @param string $tab 价目表名
	 * @param array $data 参考数据
	 * @return  array 
	 */
	public static function updateCacheTableFee($tab, $data){
		self::initDB();
		$cacheName	= 'cal_';
		switch($tab) {
			case "cpsf_fujian_zhangpu": //平邮福建漳浦
			case "cpsf_fujian_quanzhou": //平邮福建泉州
			case "cpsf_shenzhen": //平邮深圳
			case "cprg_fujian_zhangpu": //福建漳浦挂号
			case "cprg_fujian_quanzhou": //福建泉州挂号
			case "cprg_shenzhen": //深圳挂号
			case "zhengzhou_xb_py": //郑州小包平邮
			case "zhengzhou_xb_gh": //郑州小包挂号
			case "hkpostsf_hk": //香港小包平邮
			case "hkpostrg_hk": //香港小包挂号
			case "ems_shenzhen": //EMS深圳
			case "eub_fujian": //EUB福建
			case "eub_jiete": //EUB捷特
			case "globalmail_shenzhen": //德国小包深圳
			case "ruston_packet_py": //俄速通平邮
			case "ruston_packet_gh": //俄速通挂号
			case "ruston_large_package": //俄速通大包
			case "sg_dhl_gm_gh": //新加坡DHL GM挂号
			case "sg_dhl_gm_py": //新加坡DHL GM平邮
			case "ruishi_xb_py": //瑞士小包平邮
			case "ruishi_xb_gh": //瑞士小包挂号
			case "bilishi_xb_py": //比利时小包平邮
			case "bilishi_xb_gh": //比利时小包挂号
			case "aoyoubao_py": //澳邮宝平邮
			case "aoyoubao_gh": //澳邮宝挂号
				$cacheName .= $tab;
				$res		= self::cacheTableInfo(self::$prefixfee.$tab, '1', $cacheName, 86400, 1);
			break;
			case "dhl_shenzhen": //DHL深圳
				$cacheName .= $tab;
				$res		= self::cacheTableInfo(self::$prefixfee.$tab, "mode = '{$data['mode']}'", $cacheName.'_'.$data['mode'], 86400, 1);
			break;
			case "fedex_shenzhen": //联邦深圳
				$cacheName .= $tab;
				$res		= self::cacheTableInfo(self::$prefixfee.$tab, "type = '{$data['type']}'", $cacheName.'_'.$data['type'], 86400, 1);
				$res		= self::cacheTableInfo('trans_freight_fedex_shenzhen', "countrylist = 'United States#other' and type='economica'", 'cal_fedex_shenzhen_economica_other', 86400, 1);
			break;
			case "usps_first_class": //赛维USPS
			case "ups_ground_commercia": //赛维UPS
			case "ups_us": //UPS美国专线
			case "ups_calcfree": //ups ground 海外仓UPS运费价目表
			case "usps_calcfree": //USPS 海外仓USPS运费价目表
			case "sto_shenzhen": //申通
			case "yto_shenzhen": //圆通
			case "yundaex_shenzhen": //韵达
			case "best_shenzhen": //汇通
			case "jym_shenzhen": //加运美
			case "gto_shenzhen": //国通
				$res 		= 1; //待定
			break;			
			default:
				$res 		= 0;
		}
		return $res;
	}
}
?>