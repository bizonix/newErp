<?php
/**
 * 类名：TrackWarnCarrierAct
 * 功能：运输方式名预警管理动作处理层
 * 版本：1.0
 * 日期：2013/10/21
 * 作者：管拥军
 */
  
class TrackWarnCarrierAct {
    public static $errCode	= 0;
	public static $errMsg	= "";

	/**
	 * TrackWarnCarrierAct::actList()
	 * 列出符合条件的数据并分页显示
	 * @param string $where 查询条件
	 * @param integer $page 页码
	 * @param integer $pagenum 每页个数
	 * @return array 结果集数组
	 */
 	public function actList($where='1', $page=1, $pagenum=20){
		$res			= TrackWarnCarrierModel::modList($where, $page, $pagenum);
		self::$errCode  = TrackWarnCarrierModel::$errCode;
        self::$errMsg   = TrackWarnCarrierModel::$errMsg;
        return $res;
    }

	/**
	 * TrackWarnCarrierAct::actListCount()
	 * 返回某个条件结果统计的总数
	 * @param string $where 查询条件
	 * @return integer 总数量 
	 */
	public function actListCount($where='1'){
		$res			= TrackWarnCarrierModel::modListCount($where);
		self::$errCode  = TrackWarnCarrierModel::$errCode;
        self::$errMsg   = TrackWarnCarrierModel::$errMsg;
        return $res;
    }
	
	/**
	 * TrackWarnCarrierAct::actModify()
	 * 返回某个运输方式名预警的信息
	 * @param int $id 查询ID
	 * @return array 
	 */
	public function actModify($id){
		$res			= TrackWarnCarrierModel::modModify($id);
		self::$errCode  = TrackWarnCarrierModel::$errCode;
        self::$errMsg   = TrackWarnCarrierModel::$errMsg;
        return $res;
    }
	
	/**
	 * TrackWarnCarrierAct::act_addTrackWarnCarrier()
	 * 添加运输方式名预警
	 * @param string $carrier_name 跟踪系统运输方式名
	 * @param string $ship_erp erp运输方式名
	 * @param string $ship_id 运输方式ID
	 * @return  bool
	 */
	public function act_addTrackWarnCarrier(){
        $carrier_name	= isset($_POST["carrier_name"]) ? post_check($_POST["carrier_name"]) : "";
        $ship_erp		= isset($_POST["ship_erp"]) ? post_check($_POST["ship_erp"]) : "";
        $ship_id		= isset($_POST["ship_id"]) ? post_check($_POST["ship_id"]) : 0;
		$act			= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod			= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if	(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 10003;
			self::$errMsg   = "对不起,您无数据添加权限！";
			return false;
		}
		if (empty($ship_id)) {
			self::$errCode  = 10000;
			self::$errMsg   = "运输方式有误！";
			return false;
		}
		if (empty($ship_erp)) {
			self::$errCode  = 10001;
			self::$errMsg   = "ERP运输方式名参数有误！";
			return false;
		}
		if (empty($carrier_name)) {
			self::$errCode  = 10002;
			self::$errMsg   = "跟踪运输方式名有误！";
			return false;
		}
		$data  = array(
			"trackName"		=> $carrier_name,
			"erpName"		=> $ship_erp,
			"carrierId"		=> $ship_id,
		);
        $res			= TrackWarnCarrierModel::addTrackWarnCarrier($data);
		self::$errCode  = TrackWarnCarrierModel::$errCode;
        self::$errMsg   = TrackWarnCarrierModel::$errMsg;
		return $res;
    }

	/**
	 * TrackWarnCarrierAct::act_updateTrackWarnCarrier()
	 * 修改运输方式名预警
	 * @param string $carrier_name 跟踪系统运输方式名
	 * @param string $ship_erp erp运输方式名
	 * @param string $ship_id 运输方式ID
	 * @return  bool
	 */
	public function act_updateTrackWarnCarrier(){
		$id				= isset($_POST["id"]) ? intval(trim($_POST["id"])) : 0;
		$carrier_name	= isset($_POST["carrier_name"]) ? post_check($_POST["carrier_name"]) : "";
        $ship_erp		= isset($_POST["ship_erp"]) ? post_check($_POST["ship_erp"]) : "";
        $ship_id		= isset($_POST["ship_id"]) ? post_check($_POST["ship_id"]) : "";
		$act			= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod			= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if	(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 10003;
			self::$errMsg   = "对不起,您无数据编辑权限！";
			return false;
		}
		if (empty($id) || !is_numeric($id)) {
			self::$errCode  = 10000;
			self::$errMsg   = "运输方式ID有误！";
			return false;
		}
		if (empty($ship_erp)) {
			self::$errCode  = 10001;
			self::$errMsg   = "ERP运输方式名参数有误！";
			return false;
		}
		if (empty($carrier_name)) {
			self::$errCode  = 10002;
			self::$errMsg   = "跟踪运输方式名有误！";
			return false;
		}
		$data  = array(
			"trackName"	=> $carrier_name,
			"erpName"	=> $ship_erp,
			"carrierId"	=> $ship_id,
		);
        $res			= TrackWarnCarrierModel::updateTrackWarnCarrier($id, $data);
		self::$errCode  = TrackWarnCarrierModel::$errCode;
        self::$errMsg   = TrackWarnCarrierModel::$errMsg;
		return $res;
    }
	
	/**
	 * TrackWarnCarrierAct::act_delTrackWarnCarrier()
	 * 删除运输方式名预警
	 * @param int $id 运输方式名预警ID
	 * @return  bool
	 */
	public function act_delTrackWarnCarrier(){
		$id			= isset($_POST["id"]) ? intval(trim($_POST["id"])) : 0;
		$act		= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod		= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if	(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 10001;
			self::$errMsg   = "对不起,您无数据删除权限！";
			return false;
		}
		if (empty($id) || !is_numeric($id)) {
			self::$errCode  = 10000;
			self::$errMsg   = "运输方式名预警ID有误！";
			return false;
		}
        $res			= TrackWarnCarrierModel::delTrackWarnCarrier($id);
		self::$errCode  = TrackWarnCarrierModel::$errCode;
        self::$errMsg   = TrackWarnCarrierModel::$errMsg;
		return $res;
    }
}
?>