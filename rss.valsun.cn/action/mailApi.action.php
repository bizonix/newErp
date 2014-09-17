<?php
/*
 * 功能：对外提供对应邮件收件人Api接口,带有memcache缓存
 * 时间：2014/08/23
 * 作者：张志强
 */
class MailApiAct {
	public static $errCode	=	0;
	public static $errMsg	=	"";
	static $debug	  		= 	false;

	public function act_getUserList() {
		$list_english_id	= addslashes($_GET['englishId']);
		$list_english_id	= trim($list_english_id);
		if($list_english_id === '') {
			self::$errCode 	= '5506';
			self::$errMsg  	= 'Mail englishId is null,please input again!';
			return array();
		} else {
			$cacheName 			= md5("rss_name_list");
			$memc_obj			= new Cache(C('CACHEGROUP'));
			$rssNameInfo 		= $memc_obj->get_extral($cacheName);
			if(!empty($rssNameInfo)) {
				return unserialize($rssNameInfo);
			} else {
				$getData			= new MailApiModel();
				$getUserList		= $getData->checkPower($list_english_id);
				$rssNameInfo	 	= $this->checkReturnData($getUserList, array());
				$isok 				= $memc_obj->set_extral($cacheName, serialize($rssNameInfo), 14400);
				if(!$isok) {
					self::$errCode 	= 0;
					self::$errMsg  	= 'memcache缓存出错!';
				}
				self::$errCode 		= mailApiModel::$errCode;
				self::$errMsg 		= mailApiModel::$errMsg;
				return 	$rssNameInfo;
			}
		}
	}

	private function checkReturnData($data, $errreturn) {
		if($data === false) {
			self::$errCode = UserModel::$errCode;
			self::$errMsg  = UserModel::$errMsg;
			return $errreturn;
		} elseif(empty($data)) {
			self::$errCode 	= '5506';
			self::$errMsg  	= 'The english_id is not exists or there is no person subscribe to this mail right now,please check out.';
			if(self::$debug === true) {
				self::$errMsg .= 'The SQL is '.UserModel::$errMsg;
			}
			return $errreturn;
		} else {
			self::$errCode = 1;
			self::$errMsg  = 'success';
			return $data;
		}
	}
}