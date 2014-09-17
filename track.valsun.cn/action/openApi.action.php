<?php
/**
 * 类名：OpenApiAct
 * 功能：API对外接口调用动作处理层
 * 版本：1.0
 * 日期：2014/7/21
 * 作者：管拥军
 */

class OpenApiAct {
    public static $errCode	= 0;
	public static $errMsg	= "";

	/**
	 * OpenApiAct::act_getLogZip()
	 * 打包日志文件
	 * @return  json string
	 */
	public static function act_getLogZip(){
        $res			= OpenApiModel::getLogZip();
		self::$errCode  = OpenApiModel::$errCode;
        self::$errMsg   = OpenApiModel::$errMsg;
		return $res;
	}	
	
	/**
	 * OpenApiAct::act_getTracknumSimpleInfo()
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
		$trackAdInfo = $memc_obj->get_extral($cacheName);
		if(!empty($trackAdInfo)) {
			return unserialize($trackAdInfo);
		} else {
			$trackAdInfo	= OpenApiModel::getTracknumSimpleInfo($tracknum, $is_wedo);
			self::$errCode  	= OpenApiModel::$errCode;
			self::$errMsg   	= OpenApiModel::$errMsg;
			$isok 				= $memc_obj->set_extral($cacheName, serialize($trackAdInfo), 86400);
			if(!$isok) {
				self::$errCode 	= 308;
				self::$errMsg  	= 'memcache缓存出错!';
				//return false;
			}
			return $trackAdInfo;
		}
    }
	
	/**
	 * OpenApiAct::act_getWebAdInfoById()
	 * 获取一个或多个网站广告内容
	 * @param string $ids 广告ID
	 * @return array;
	 */
	public function act_getWebAdInfoById(){
		$ids				= isset($_REQUEST["ids"]) ? post_check($_REQUEST["ids"]) : "";
		$is_new				= isset($_REQUEST["is_new"]) ? abs(intval(($_REQUEST["is_new"]))) : 0;
		if(empty($ids) || !(preg_match("/^([\d]+,)*[\d]+$/",$ids))){
			self::$errCode  = "广告ID参数有误！";
			self::$errMsg   = 10000;
			return false;
		}
		if(!in_array($is_new,array(0,1))) {
			self::$errCode  = "强制更新参数有误！";
			self::$errMsg   = 10001;
			return false;
		}
		$cacheName 			= md5("track_ads_info_".$ids);
		$memc_obj			= new Cache(C('CACHEGROUP'));
		$trackAdInfo 		= $memc_obj->get_extral($cacheName);
		if(!empty($trackAdInfo) && empty($is_new)) {
			return unserialize($trackAdInfo);
		} else {
			$trackAdInfo		= OpenApiModel::getWebAdInfoById($ids);
			self::$errCode  	= OpenApiModel::$errCode;
			self::$errMsg   	= OpenApiModel::$errMsg;
			$isok 				= $memc_obj->set_extral($cacheName, serialize($trackAdInfo), 86400);
			if(!$isok) {
				self::$errCode 	= 308;
				self::$errMsg  	= 'memcache缓存出错!';
				//return false;
			}
			return $trackAdInfo;
		}
    }
	
	/**
	 * OpenApiAct::act_getWebConfig()
	 * 获取网站所有配置信息
	 * @param string $is_new 强制更新参数
	 * @return array;
	 */
	public function act_getWebConfig(){
		$is_new				= isset($_REQUEST["is_new"]) ? abs(intval(($_REQUEST["is_new"]))) : 0;
		if(!in_array($is_new,array(0,1))) {
			self::$errCode  = "强制更新参数有误！";
			self::$errMsg   = 10000;
			return false;
		}
		$cacheName 			= md5("track_webconfig_info");
		$memc_obj			= new Cache(C('CACHEGROUP'));
		$configInfo 		= $memc_obj->get_extral($cacheName);
		if(!empty($configInfo) && empty($is_new)) {
			return unserialize($configInfo);
		} else {
			$configInfo			= OpenApiModel::getWebConfigAll();
			self::$errCode  	= OpenApiModel::$errCode;
			self::$errMsg   	= OpenApiModel::$errMsg;
			$isok 				= $memc_obj->set_extral($cacheName, serialize($configInfo), 86400);
			if(!$isok) {
				self::$errCode 	= 308;
				self::$errMsg  	= 'memcache缓存出错!';
				//return false;
			}
			return $configInfo;
		}
    }
	
	/**
	 * OpenApiAct::act_getCountriesStandard()
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
			$countriesInfo 		= OpenApiModel::getCountriesStandard($type, $country, $is_new);
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