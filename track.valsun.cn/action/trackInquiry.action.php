<?php
/**
 * 类名：TrackInquiryAct
 * 功能：运德物流系统查询动作处理层
 * 版本：1.0
 * 日期：2014/01/17
 * 作者：管拥军
 */
  
class TrackInquiryAct {
    public static $errCode	= 0;
	public static $errMsg	= "";
	public static $logFile	= "";

	//初始化
    public function __construct() {
        self::$logFile		= WEB_PATH."html/access/".date('Y')."/".date('Y-m-d').".track.log";
    }
	
	/**
	 * TrackInquiryAct::actIndex()
	 * 网站首页
	 * @return array 
	 */
 	public function actIndex(){
		$data				= array();
		$data['config']		= OpenApiAct::act_getWebConfig();
		$data['carriers']	= self::act_trackName();
		return $data;
	}
	
	/**
	 * TrackInquiryAct::actTrack()
	 * 跟踪号数据跟踪
	 * @return array 
	 */
 	public function actTrack(){
		$data				= array();
        $carrier			= isset($_REQUEST["carrier"]) ? post_check($_REQUEST["carrier"]) : '';
        $tracknum			= isset($_REQUEST["tracknum"]) ? post_check($_REQUEST["tracknum"]) : '';
		if($carrier=='wedo') $carrier = '运德物流';
		$data['carrier']	= $carrier;
		$data['carrierEn']	= C('TRACK_NAME')[$carrier];
		$data['tracknum']	= $tracknum;
		$data['carriers']	= self::act_trackName();
		return $data;
	}
	
	/**
	 * TrackInquiryAct::act_trackName()
	 * 获取跟踪运输方式列表
	 * @param bool $is_new 待定
	 * @return json string 
	 */
 	public function act_trackName(){
        $is_new				= isset($_REQUEST["is_new"]) ? $_REQUEST["is_new"] : 0;
        if(!in_array($is_new, array(0,1))) {
            self::$errCode  = 10000;
			self::$errMsg   = "更新参数非法!";
			return false;
        }
		$cacheName 			= md5("track_name_list");
		$memc_obj			= new Cache(C('CACHEGROUP'));
		$trackNameInfo 		= $memc_obj->get_extral($cacheName);
		if(!empty($trackNameInfo) && empty($is_new)) {
			return unserialize($trackNameInfo);
		} else {
			$trackNameInfo		= TrackInquiryModel::trackNameList();
			$isok 				= $memc_obj->set_extral($cacheName, serialize($trackNameInfo), 14400);
			if(!$isok) {
				self::$errCode 	= 0;
				self::$errMsg  	= 'memcache缓存出错!';
				//return false;
			}
			return $trackNameInfo;
		}
    }
	
	/**
	 * TrackInquiryAct::act_trackInfo()
	 * 查询跟踪信息
	 * @param string $carrier 运输方式名称
	 * @param string $tracknum 跟踪号
	 * @param string $tracklan 语言
	 * @return json string 
	 */
 	public function act_trackInfo(){
		$carrier	= isset($_REQUEST['carrier']) ? post_check($_REQUEST['carrier']) : '';
		$tracknum	= isset($_REQUEST['tracknum']) ? post_check($_REQUEST['tracknum']) : '';
		$tracklan	= isset($_REQUEST['tracklan']) ? abs(intval($_REQUEST['tracklan'])) : 10000;
		$ip			= getClientIP();
		$ipNum		= sprintf('%u',ip2long($ip));
		if(in_array($carrier, array('美国邮政'))) $tracklan = 10000;
		if(empty($carrier)) {
			self::$errCode  = 10000;
			self::$errMsg   = "运输方式参数非法！";
			@write_a_file(self::$logFile, date('Y-m-d H:i:s')."=====".$ip."=====".$carrier."=====".$tracknum."=====".$tracklan."=====event:".self::$errMsg."\n");
			return false;
		}
		if(empty($tracknum)) {
			self::$errCode  = 10001;
			self::$errMsg   = "跟踪号参数非法！";
			@write_a_file(self::$logFile, date('Y-m-d H:i:s')."=====".$ip."=====".$carrier."=====".$tracknum."=====".$tracklan."=====event:".self::$errMsg."\n");
			return false;
		}
		//访问统计逻辑
		$data		= array();
		$times		= time();
		$maxcount	= C("USER_MAX_COUNT");
		$exptime	= C("USER_EXPIRES_TIME");
		$res 		= TrackInquiryModel::showIpStat($ipNum);
		$stats		= isset($res['count']) ? $res['count'] : 0;
		$exptimes	= isset($res['expires']) ? $res['expires'] : 0;
		$data['extInfo']	= array();
		$data['trackInfo']	= array();
		if($stats > $maxcount && $exptimes > $times && !in_array($ip, array('183.233.230.2'), true)) {
			array_push($data['extInfo'],array("fromCounty"=>'', "toCounty"=>'', "toCity"=>'', "realWeight"=>'', "file_cz"=>'', "file_fh"=>''));
			array_push($data['trackInfo'],array("postion"=>"server","event"=>"{$ip}:Visits over","trackTime"=>date('Y-m-d H:i:s',time()),"stat"=>0));
			@write_a_file(self::$logFile, date('Y-m-d H:i:s')."=====".$ip."=====".$carrier."=====".$tracknum."=====".$tracklan."=====event:Visits over\n");
			return $data;
			exit;
		}

		if(!$stats) {
			$res		= TrackInquiryModel::updateStatInfo($ipNum, array("ip"=>$ip, "count"=>1, "expires"=>$times+$exptime, "ipNum"=>$ipNum));
		} else {
			if($exptimes < $times) {
				$res	= TrackInquiryModel::updateStatInfo($ipNum, array("ip"=>$ip, "count"=>1, "expires"=>$times+$exptime, "ipNum"=>$ipNum));
			} else {
				$res	= TrackInquiryModel::updateStatInfo($ipNum, array("ip"=>$ip, "count"=>$stats+1, "ipNum"=>$ipNum));
			}
		}		
		//查询跟踪信息并memcache
		$cacheName 		= md5("track_number_info".$carrier."_".$tracknum."_".$tracklan);
		$memc_obj		= new Cache(C('CACHEGROUP'));
		$trackInfo		= $memc_obj->get_extral($cacheName);
		$trackInfo		= @unserialize($trackInfo);
		if(!empty($trackInfo['trackInfo'])) {
			@write_a_file(self::$logFile,date('Y-m-d H:i:s')."=====".$ip."=====".$carrier."=====".$tracknum."=====".$tracklan."=====event:memcache success\n");
			return $trackInfo;
		} else {
			if($carrier != '运德物流') {
				$trackInfo			= TrackInquiryModel::trackInfo($carrier, $tracknum, $tracklan);
			} else {
				if(!preg_match("/^((300\d{9})|(WD\d{9}CN)|(WD[A-Z]{1}\w{8}CN))$/",$tracknum)) {
					self::$errCode  = 10002;
					self::$errMsg   = "运德物流跟踪号参数非法！";
					@write_a_file(self::$logFile, date('Y-m-d H:i:s')."=====".$ip."=====".$carrier."=====".$tracknum."=====".$tracklan."=====event:".self::$errMsg."\n");
					return false;
				} else {
					if(!preg_match("/^((WD[A-Z]{1}\w{8}CN))$/",$tracknum)) {
						if(substr($tracknum,0,2) == 'WD') {
						   $tracknum 	= intval(substr($tracknum,2,9)); 				        
						} else {
						   $tracknum 	= intval(substr($tracknum,3));
						}
					}
				}
				$trackInfo				= TrackInquiryModel::trackWedoInfo($tracknum, $tracklan);
			}
			if(!in_array($trackInfo['trackInfo'][0]['event'],array('Time out','time out','No data','System Interface exceptions,Please try again!'))) {
				$isok 					= $memc_obj->set_extral($cacheName, serialize($trackInfo), 7200);
				if(!$isok) {
					self::$errCode 		= 0;
					self::$errMsg  		= 'memcache缓存出错!';
					@write_a_file(self::$logFile,date('Y-m-d H:i:s')."=====".$ip."=====".$carrier."=====".$tracknum."=====".$tracklan."=====event:".self::$errMsg."\n");
					//return false;
				}
			}
			if(in_array($trackInfo['trackInfo'][0]['event'],array('Time out','time out'))) {
				@write_a_file(self::$logFile,date('Y-m-d H:i:s')."=====".$ip."=====".$carrier."=====".$tracknum."=====".$tracklan."=====event:".$trackInfo['trackInfo'][0]['event']."\n");
			}
			if($trackInfo['trackInfo'][0]['postion'] == 'No data') {
				@write_a_file(self::$logFile,date('Y-m-d H:i:s')."=====".$ip."=====".$carrier."=====".$tracknum."=====".$tracklan."=====event:".$trackInfo['trackInfo'][0]['event']."\n");
			}
			if(empty($trackInfo['trackInfo'])) {
				@write_a_file(self::$logFile,date('Y-m-d H:i:s')."=====".$ip."=====".$carrier."=====".$tracknum."=====".$tracklan."=====event:接口获取数据异常\n");
			}
			@write_a_file(self::$logFile,date('Y-m-d H:i:s')."=====".$ip."=====".$carrier."=====".$tracknum."=====".$tracklan."=====event:api interface success\n");
			return $trackInfo;
		}		
    }

	/**
	 * TrackInquiryAct::act_trackInfoEn()
	 * 查询目的地跟踪信息
	 * @param string $carrier 运输方式名称
	 * @param string $tracknum 跟踪号
	 * @param string $tracklan 语言
	 * @return json string 
	 */
 	public function act_trackInfoEn(){
		$carrier	= isset($_REQUEST['carrier']) ? post_check($_REQUEST['carrier']) : '';
		$tracknum	= isset($_REQUEST['tracknum']) ? post_check($_REQUEST['tracknum']) : '';
		$tracklan	= isset($_REQUEST['tracklan']) ? abs(intval($_REQUEST['tracklan'])) : 10000;
		$ip			= getClientIP();
		$ipNum		= sprintf('%u',ip2long($ip));
		if(in_array($carrier, array('美国邮政'))) $tracklan = 10000;
		if(empty($carrier)) {
			self::$errCode  = 10000;
			self::$errMsg   = "运输方式参数非法！";
			@write_a_file(self::$logFile, date('Y-m-d H:i:s')."=====".$ip."=====".$carrier."=====".$tracknum."=====".$tracklan."=====event:".self::$errMsg."\n");
			return false;
		}
		if(empty($tracknum)) {
			self::$errCode  = 10001;
			self::$errMsg   = "跟踪号参数非法！";
			@write_a_file(self::$logFile, date('Y-m-d H:i:s')."=====".$ip."=====".$carrier."=====".$tracknum."=====".$tracklan."=====event:".self::$errMsg."\n");
			return false;
		}
		//访问统计逻辑
		$data		= array();
		$times		= time();
		$maxcount	= C("USER_MAX_COUNT");
		$exptime	= C("USER_EXPIRES_TIME");
		$res 		= TrackInquiryModel::showIpStat($ipNum);
		$stats		= isset($res['count']) ? $res['count'] : 0;
		$exptimes	= isset($res['expires']) ? $res['expires'] : 0;
		$data['trackInfoEn']	= array();
		if($stats > $maxcount && $exptimes > $times && !in_array($ip, array('183.233.230.2'), true)) {
			array_push($data['trackInfoEn'],array("postion"=>"server","event"=>"{$ip}:Visits over","trackTime"=>date('Y-m-d H:i:s',time()),"stat"=>0));
			@write_a_file(self::$logFile, date('Y-m-d H:i:s')."=====".$ip."=====".$carrier."=====".$tracknum."=====".$tracklan."=====event:Visits over\n");
			return $data;
			exit;
		}
		if(!$stats) {
			$res		= TrackInquiryModel::updateStatInfo($ipNum, array("ip"=>$ip, "count"=>1, "expires"=>$times+$exptime, "ipNum"=>$ipNum));
		} else {
			if($exptimes < $times) {
				$res	= TrackInquiryModel::updateStatInfo($ipNum, array("ip"=>$ip, "count"=>1, "expires"=>$times+$exptime, "ipNum"=>$ipNum));
			} else {
				$res	= TrackInquiryModel::updateStatInfo($ipNum, array("ip"=>$ip, "count"=>$stats+1, "ipNum"=>$ipNum));
			}
		}		
		//查询跟踪信息并memcache
		$cacheName 		= md5("track_number_info".$carrier."_".$tracknum."_".$tracklan);
		$memc_obj		= new Cache(C('CACHEGROUP'));
		$trackInfo		= $memc_obj->get_extral($cacheName);
		$trackInfo		= @unserialize($trackInfo);
		if(!empty($trackInfo['trackInfo'])) {
			@write_a_file(self::$logFile,date('Y-m-d H:i:s')."=====".$ip."=====".$carrier."=====".$tracknum."=====".$tracklan."=====event:memcache success\n");
			return $trackInfo;
		} else {
			$trackInfo			= TrackInquiryModel::trackInfoEn($carrier, $tracknum, $tracklan);
			if(!in_array($trackInfo['trackInfoEn'][0]['event'],array('Time out','time out','No data','System Interface exceptions,Please try again!'))) {
				$isok 					= $memc_obj->set_extral($cacheName, serialize($trackInfo), 7200);
				if(!$isok) {
					self::$errCode 		= 0;
					self::$errMsg  		= 'memcache缓存出错!';
					@write_a_file(self::$logFile,date('Y-m-d H:i:s')."=====".$ip."=====".$carrier."=====".$tracknum."=====".$tracklan."=====event:".self::$errMsg."\n");
					//return false;
				}
			}
			if(in_array($trackInfo['trackInfoEn'][0]['event'],array('Time out','time out'))) {
				@write_a_file(self::$logFile,date('Y-m-d H:i:s')."=====".$ip."=====".$carrier."=====".$tracknum."=====".$tracklan."=====event:".$trackInfo['trackInfo'][0]['event']."\n");
			}
			if($trackInfo['trackInfoEn'][0]['postion'] == 'No data') {
				@write_a_file(self::$logFile,date('Y-m-d H:i:s')."=====".$ip."=====".$carrier."=====".$tracknum."=====".$tracklan."=====event:".$trackInfo['trackInfo'][0]['event']."\n");
			}
			if(empty($trackInfo['trackInfoEn'])) {
				@write_a_file(self::$logFile,date('Y-m-d H:i:s')."=====".$ip."=====".$carrier."=====".$tracknum."=====".$tracklan."=====event:接口获取数据异常\n");
			}
			@write_a_file(self::$logFile,date('Y-m-d H:i:s')."=====".$ip."=====".$carrier."=====".$tracknum."=====".$tracklan."=====event:api interface success\n");
			return $trackInfo;
		}		
    }
}
?>