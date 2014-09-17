<?php
/**
 * 类名：CountriesShipAct
 * 功能：运输方式国家列表管理动作处理层
 * 版本：1.0
 * 日期：2013/10/21
 * 作者：管拥军
 */
  
class CountriesShipAct {
    public static $errCode	= 0;
	public static $errMsg	= "";

	/**
	 * CountriesShipAct::actList()
	 * 列出符合条件的数据并分页显示
	 * @param string $where 查询条件
	 * @param integer $page 页码
	 * @param integer $pagenum 每页个数
	 * @return array 结果集数组
	 */
 	public function actList($where='1', $page=1, $pagenum=20){
		$res			= CountriesShipModel::modList($where, $page, $pagenum);
		self::$errCode  = CountriesShipModel::$errCode;
        self::$errMsg   = CountriesShipModel::$errMsg;
        return $res;
    }

	/**
	 * CountriesShipAct::actListCount()
	 * 返回某个条件结果统计的总数
	 * @param string $where 查询条件
	 * @return integer 总数量 
	 */
	public function actListCount($where='1'){
		$res			= CountriesShipModel::modListCount($where);
		self::$errCode  = CountriesShipModel::$errCode;
        self::$errMsg   = CountriesShipModel::$errMsg;
        return $res;
    }
	
	/**
	 * CountriesShipAct::actModify()
	 * 返回某个运输方式国家的信息
	 * @param int $id 查询ID
	 * @return array 
	 */
	public function actModify($id){
		$res			= CountriesShipModel::modModify($id);
		self::$errCode  = CountriesShipModel::$errCode;
        self::$errMsg   = CountriesShipModel::$errMsg;
        return $res;
    }
	
	/**
	 * CountriesShipAct::act_addCountriesShip()
	 * 添加运输方式国家
	 * @param string $carrier_name 运输方式名称
	 * @param string $en_name 标准国家英文名称
	 * @param string $ship_id 运输方式ID
	 * @return  bool
	 */
	public function act_addCountriesShip(){
        $carrier_name	= isset($_POST["carrier_name"]) ? post_check($_POST["carrier_name"]) : "";
        $en_name		= isset($_POST["en_name"]) ? post_check($_POST["en_name"]) : "";
        $ship_id		= isset($_POST["ship_id"]) ? post_check($_POST["ship_id"]) : 0;
		$act		= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod		= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if	(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 10001;
			self::$errMsg   = "对不起,您无数据添加权限！";
			return false;
		}
		if (empty($carrier_name) || empty($en_name)) {
			self::$errCode  = 10000;
			self::$errMsg   = "运输方式国家名称或标准英文名称有误！";
			return false;
		}
		$data  = array(
			"carrier_country"	=> $carrier_name,
			"countryName"		=> $en_name,
			"carrierId"			=> $ship_id,
			"createdTime"		=> time(),
		);
        $res			= CountriesShipModel::addCountriesShip($data);
		self::$errCode  = CountriesShipModel::$errCode;
        self::$errMsg   = CountriesShipModel::$errMsg;
		return $res;
    }

	/**
	 * CountriesShipAct::act_updateCountriesShip()
	 * 修改运输方式国家
	 * @param string $carrier_name 运输方式名称
	 * @param string $en_name 标准国家英文名称
	 * @param string $ship_id 运输方式ID
	 * @return  bool
	 */
	public function act_updateCountriesShip(){
		$id				= isset($_POST["id"]) ? intval(trim($_POST["id"])) : 0;
		$carrier_name	= isset($_POST["carrier_name"]) ? post_check($_POST["carrier_name"]) : "";
        $en_name		= isset($_POST["en_name"]) ? post_check($_POST["en_name"]) : "";
        $ship_id		= isset($_POST["ship_id"]) ? post_check($_POST["ship_id"]) : "";
		$act		= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod		= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if	(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 10002;
			self::$errMsg   = "对不起,您无数据编辑权限！";
			return false;
		}
		if (empty($id) || !is_numeric($id)) {
			self::$errCode  = 10000;
			self::$errMsg   = "运输方式国家ID有误！";
			return false;
		}
		if (empty($carrier_name) || empty($en_name)) {
			self::$errCode  = 10001;
			self::$errMsg   = "运输方式国家名称或标准英文名称有误！";
			return false;
		}
		$data  = array(
			"carrier_country"	=> $carrier_name,
			"countryName"	=> $en_name,
			"carrierId"		=> $ship_id,
		);
        $res			= CountriesShipModel::updateCountriesShip($id, $data);
		self::$errCode  = CountriesShipModel::$errCode;
        self::$errMsg   = CountriesShipModel::$errMsg;
		return $res;
    }
	
	/**
	 * CountriesShipAct::act_delCountriesShip()
	 * 删除运输方式国家
	 * @param int $id 运输方式国家ID
	 * @return  bool
	 */
	public function act_delCountriesShip(){
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
			self::$errMsg   = "运输方式国家ID有误！";
			return false;
		}
        $res			= CountriesShipModel::delCountriesShip($id);
		self::$errCode  = CountriesShipModel::$errCode;
        self::$errMsg   = CountriesShipModel::$errMsg;
		return $res;
    }
}
?>