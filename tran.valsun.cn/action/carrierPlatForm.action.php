<?php
/**
 * 类名：CarrierPlatFormAct
 * 功能：运输方式对应平台管理动作处理层
 * 版本：1.0
 * 日期：2013/12/02
 * 作者：管拥军
 */
  
class CarrierPlatFormAct {
    public static $errCode	= 0;
	public static $errMsg	= "";

	/**
	 * CarrierPlatFormAct::actList()
	 * 列出符合条件的数据并分页显示
	 * @param string $where 查询条件
	 * @param integer $page 页码
	 * @param integer $pagenum 每页个数
	 * @return array 结果集数组
	 */
 	public function actList($where='1', $page=1, $pagenum=20){
		$res			= CarrierPlatFormModel::modList($where, $page, $pagenum);
		self::$errCode  = CarrierPlatFormModel::$errCode;
        self::$errMsg   = CarrierPlatFormModel::$errMsg;
        return $res;
    }

	/**
	 * CarrierPlatFormAct::actListCount()
	 * 返回某个条件结果统计的总数
	 * @param string $where 查询条件
	 * @return integer 总数量 
	 */
	public function actListCount($where='1'){
		$res			= CarrierPlatFormModel::modListCount($where);
		self::$errCode  = CarrierPlatFormModel::$errCode;
        self::$errMsg   = CarrierPlatFormModel::$errMsg;
        return $res;
    }
	
	/**
	 * CarrierPlatFormAct::actModify()
	 * 返回某个平台的信息
	 * @param int $id 查询ID
	 * @return array 
	 */
	public function actModify($id){
		$res			= CarrierPlatFormModel::modModify($id);
		self::$errCode  = CarrierPlatFormModel::$errCode;
        self::$errMsg   = CarrierPlatFormModel::$errMsg;
        return $res;
    }

	/**
	 * CarrierPlatFormAct::act_addCarrierPlatForm()
	 * 添加运输平台
	 * @param int $ship_id 运输方式ID
	 * @param int $plat_id 平台ID
	 * @param string $ship_name 运输名
	 * @param string $ship_service 服务名
	 * @return  bool
	 */
	public function act_addCarrierPlatForm(){
        $carrierId	= isset($_POST["ship_id"]) ? abs(intval($_POST["ship_id"])) : 0;
        $platId		= isset($_POST["plat_id"]) ? abs(intval($_POST["plat_id"])) : 0;
        $ship_name	= isset($_POST["ship_name"]) ? post_check($_POST["ship_name"]) : "";
        $ship_service	= isset($_POST["ship_service"]) ? post_check($_POST["ship_service"]) : "";
		$act		= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod		= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if	(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 10001;
			self::$errMsg   = "对不起,您无数据添加权限！";
			return false;
		}
		if (empty($carrierId)) {
			self::$errCode  = 10000;
			self::$errMsg   = "运输方式选择有误！";
			return false;
		}
		if (empty($platId)) {
			self::$errCode  = 10001;
			self::$errMsg   = "平台选择有误！";
			return false;
		}
		if (empty($ship_name)) {
			self::$errCode  = 10002;
			self::$errMsg   = "运输名有误！";
			return false;
		}
		if (empty($ship_service)) {
			self::$errCode  = 10003;
			self::$errMsg   = "服务名有误！";
			return false;
		}
		$uid   = $_SESSION[C('USER_AUTH_SYS_ID')];
		$data  = array(
			"carrierId"	=> $carrierId,
			"platId"	=> $platId,
			"shipName"	=> $ship_name,
			"shipService"	=> $ship_service,
			"createdTime"	=> time(),
			"createdUserId"	=> $uid,
		);
        $res			= CarrierPlatFormModel::addCarrierPlatForm($data);
		self::$errCode  = CarrierPlatFormModel::$errCode;
        self::$errMsg   = CarrierPlatFormModel::$errMsg;
		return $res;
    }

	/**
	 * CarrierPlatFormAct::act_updateCarrierPlatForm()
	 * 修改运输平台
	 * @param int $ship_id 运输方式ID
	 * @param int $plat_id 平台ID
	 * @param string $ship_name 运输名
	 * @param string $ship_service 服务名
	 * @return  bool
	 */
	public function act_updateCarrierPlatForm(){
		$id			= isset($_POST["id"]) ? intval(trim($_POST["id"])) : 0;
        $carrierId	= isset($_POST["ship_id"]) ? abs(intval($_POST["ship_id"])) : 0;
        $platId		= isset($_POST["plat_id"]) ? abs(intval($_POST["plat_id"])) : 0;
		$ship_name	= isset($_POST["ship_name"]) ? post_check($_POST["ship_name"]) : "";
        $ship_service	= isset($_POST["ship_service"]) ? post_check($_POST["ship_service"]) : "";
		$act		= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod		= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if	(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 10001;
			self::$errMsg   = "对不起,您无数据编辑权限！";
			return false;
		}
		if (empty($carrierId)) {
			self::$errCode  = 10000;
			self::$errMsg   = "运输方式选择有误！";
			return false;
		}
		if (empty($platId)) {
			self::$errCode  = 10001;
			self::$errMsg   = "平台选择有误！";
			return false;
		}
		if (empty($ship_name)) {
			self::$errCode  = 10002;
			self::$errMsg   = "运输名有误！";
			return false;
		}
		if (empty($ship_service)) {
			self::$errCode  = 10003;
			self::$errMsg   = "服务名有误！";
			return false;
		}
		$uid   = $_SESSION[C('USER_AUTH_SYS_ID')];
		$data  = array(
			"carrierId"	=> $carrierId,
			"platId"	=> $platId,
			"shipName"	=> $ship_name,
			"shipService"	=> $ship_service,
			"modifyTime"	=> time(),
			"modifyUserId"	=> $uid,
		);
        $res			= CarrierPlatFormModel::updateCarrierPlatForm($id, $data);
		self::$errCode  = CarrierPlatFormModel::$errCode;
        self::$errMsg   = CarrierPlatFormModel::$errMsg;
		return $res;
    }
	
	/**
	 * CarrierPlatFormAct::act_delCarrierPlatForm()
	 * 删除平台
	 * @param int $id 平台ID
	 * @return  bool
	 */
	public function act_delCarrierPlatForm(){
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
			self::$errMsg   = "平台ID有误！";
			return false;
		}
        $res			= CarrierPlatFormModel::delCarrierPlatForm($id);
		self::$errCode  = CarrierPlatFormModel::$errCode;
        self::$errMsg   = CarrierPlatFormModel::$errMsg;
		return $res;
    }
}
?>