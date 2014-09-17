<?php
/**
 * 类名：TrackWarnInfoAct
 * 功能：运输方式跟踪号预警管理动作处理层
 * 版本：1.0
 * 日期：2013/10/21
 * 作者：管拥军
 */
  
class TrackWarnInfoAct {
    public static $errCode	= 0;
	public static $errMsg	= "";

	/**
	 * TrackWarnInfoAct::actList()
	 * 列出符合条件的数据并分页显示
	 * @param string $where 查询条件
	 * @param integer $page 页码
	 * @param integer $pagenum 每页个数
	 * @return array 结果集数组
	 */
 	public function actList($where='1', $page=1, $pagenum=20){
		$res			= TrackWarnInfoModel::modList($where, $page, $pagenum);
		self::$errCode  = TrackWarnInfoModel::$errCode;
        self::$errMsg   = TrackWarnInfoModel::$errMsg;
        return $res;
    }

	/**
	 * TrackWarnInfoAct::actListCount()
	 * 返回某个条件结果统计的总数
	 * @param string $where 查询条件
	 * @return integer 总数量 
	 */
	public function actListCount($where='1'){
		$res			= TrackWarnInfoModel::modListCount($where);
		self::$errCode  = TrackWarnInfoModel::$errCode;
        self::$errMsg   = TrackWarnInfoModel::$errMsg;
        return $res;
    }	
	
	/**
	 * TrackWarnInfoAct::act_getTrackNumberInfo()
	 * 列出某个跟踪号的详细追踪信息
	 * @param integer $tid 分表ID
	 * @param string $trackNumber 跟踪号
	 * @return array
	 */
 	public function act_getTrackNumberInfo(){
		$tid				= isset($_POST["tid"]) ? intval($_POST["tid"]) : 0;
		$trackNumber		= isset($_POST["trackNumber"]) ? post_check($_POST["trackNumber"]) : "";
		$act				= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod				= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 10002;
			self::$errMsg   = "对不起,您无跟踪号详细数据查看权限！";
			return false;
		}
		if(empty($tid) || !is_numeric($tid)) {
			self::$errCode  = "ID有误";
			self::$errMsg   = 10000;
			return false;
		}
		if(empty($trackNumber)) {
			self::$errCode  = "跟踪号有误！";
			self::$errMsg   = 10001;
			return false;
		}
		$res['trackInfo']		= TrackWarnInfoModel::listTrackNumberInfo($tid, $trackNumber);
		self::$errCode  		= TrackWarnInfoModel::$errCode;
        self::$errMsg   		= TrackWarnInfoModel::$errMsg;
		if(in_array($tid,array(2,6,79,88,89))) { 
			$res['countryInfo']	= TrackWarnInfoModel::listTrackNumberInfoForCountry($tid, $trackNumber);
		} else {
			$res['countryInfo']	= array();
		}
        return $res;
    }
	
	/**
	 * TrackWarnInfoAct::act_trackNumberInfo()
	 * 实时获取某个跟踪号的跟踪信息
	 * @param integer $carrierId 运输方式ID
	 * @param integer $lan  跟踪语言
	 * @param string $trackNumber 跟踪号
	 * @return json string 
	 */
 	public function act_trackNumberInfo(){
		$carrierId		= isset($_POST["tid"]) ? intval($_POST["tid"]) : 0;
		$trackNumber	= isset($_POST["trackNumber"]) ? post_check($_POST["trackNumber"]) : "";
		$lan			= isset($_POST["lan"]) ? intval($_POST["lan"]) : 10000;
		$act			= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod			= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 10002;
			self::$errMsg   = "对不起,您无实时跟踪号详细数据查看权限！";
			return false;
		}
		if(empty($carrierId) || !is_numeric($carrierId)) {
			self::$errCode  = "运输方式ID有误";
			self::$errMsg   = 10000;
			return false;
		}
		if(empty($trackNumber)) {
			self::$errCode  = "跟踪号有误！";
			self::$errMsg   = 10001;
			return false;
		}
		$res['trackInfo']	= TrackWarnInfoModel::trackNumberInfo($carrierId, $trackNumber, $lan);
		$res['countryInfo']	= array();
		self::$errCode  	= TrackWarnInfoModel::$errCode;
        self::$errMsg   	= TrackWarnInfoModel::$errMsg;
        return $res;
    }
}
?>