<?php
include_once WEB_PATH."model/noticeApi.model.php";
/**
 * 名称：NoticeApiAct
 * 功能：对外提供的发送短信和邮件API
 * */
class NoticeApiCurdAct extends Auth{
    public static $errCode	=	0;
	public static $errMsg	=	"";

	/*
	 * 功能：消息插入到数据库方法
	*/
	public function actInsert($data, $table) {
		return NoticeApiModel::modInsert($data, $table);
	}

	/*
	 * 功能：获取某个用户最近n条发送消息方法
	*/
	public function actDetailList($from, $table, $page) {
		return NoticeApiModel::modDetailList($from, $table, $page);
	}

	/*
	 * 功能：获取用户可发送短信数量方法
	 */
	public function actSmsSurNum($from) {
		return NoticeApiModel::modSmsSurNum($from);
	}

    /*
     *功能：获取所有可发送人名字列表，具有缓存功能
    */
	public function actSendList() {
		$is_new				= isset($_REQUEST["is_new"]) ? $_REQUEST["is_new"] : 0;
		if(!in_array($is_new, array(0,1))) {
			self::$errCode  = 10000;
			self::$errMsg   = "更新参数非法!";
			return false;
		}
		$cacheName 			= md5("notice_name_list");
		$memc_obj			= new Cache(C('CACHEGROUP'));
		$noticeNameInfo 	= $memc_obj->get_extral($cacheName);
		if(!empty($noticeNameInfo) && empty($is_new)) {
			return unserialize($noticeNameInfo);
		} else {
			$noticeNameInfo		= NoticeApiModel::showNameList();
			self::$errCode 		= NoticeApiModel::$errCode;
			self::$errMsg 		= NoticeApiModel::$errMsg;
			$isok 				= $memc_obj->set_extral($cacheName, serialize($noticeNameInfo), 14400);
			if(!$isok) {
				self::$errCode 	= 0;
				self::$errMsg  	= 'memcache缓存出错!';
				//return false;
			}
			return $noticeNameInfo;
		}
	}

    /*
     *功能：根据登入名从鉴权获取用户的中文名字
    */
    public function actgetUserCName() {
    	if(empty($_GET['loginName'])) {
    		self::$errCode 	= '027';
    		self::$errMsg  	= 'param error';
    		return null ;
    	}
    	$loginName 			= $_GET['loginName'];
    	$queryConditions 	= array(										//查询条件
    							'loginName' =>$loginName,					//登录名，类型int(8)，可选项
    						  );
    	$getApiGlobalUser 	= Auth::getApiGlobalUser($queryConditions);
		if($getApiGlobalUser) {
			self::$errCode 	= '001';
			self::$errMsg  	= 'Get api global userName success';
			$arrRes			= json_decode($getApiGlobalUser,true);
			return $arrRes;
			//array('userName'=>$arrRes['0']['userName']);
		};
    }

	/*
	 *功能：查询一张表中的数据
	*/
    public function selectOneTable($table, $filed, $where){
    	return NoticeApiModel::selectOneTable($table, $filed, $where) ;
    }
}
?>
