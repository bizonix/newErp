<?php
/**
 * 类名：CountriesStandardAct
 * 功能：标准国家列表管理动作处理层
 * 版本：1.0
 * 日期：2013/10/21
 * 作者：管拥军
 */
  
class CountriesStandardAct {
    public static $errCode	= 0;
	public static $errMsg	= "";

	/**
	 * CountriesStandardAct::actList()
	 * 列出符合条件的数据并分页显示
	 * @param string $where 查询条件
	 * @param integer $page 页码
	 * @param integer $pagenum 每页个数
	 * @return array 结果集数组
	 */
 	public function actList($where='1', $page=1, $pagenum=20){
		$res			= CountriesStandardModel::modList($where, $page, $pagenum);
		self::$errCode  = CountriesStandardModel::$errCode;
        self::$errMsg   = CountriesStandardModel::$errMsg;
        return $res;
    }

	/**
	 * CountriesStandardAct::actListCount()
	 * 返回某个条件结果统计的总数
	 * @param string $where 查询条件
	 * @return integer 总数量 
	 */
	public function actListCount($where='1'){
		$res			= CountriesStandardModel::modListCount($where);
		self::$errCode  = CountriesStandardModel::$errCode;
        self::$errMsg   = CountriesStandardModel::$errMsg;
        return $res;
    }
	
	/**
	 * CountriesStandardAct::actModify()
	 * 返回某个标准国家的信息
	 * @param int $id 查询ID
	 * @return array 
	 */
	public function actModify($id){
		$res			= CountriesStandardModel::modModify($id);
		self::$errCode  = CountriesStandardModel::$errCode;
        self::$errMsg   = CountriesStandardModel::$errMsg;
        return $res;
    }

	/**
	 * CountriesStandardAct::act_addCountriesStandard()
	 * 添加标准国家
	 * @param string $cn_name 中文名称
	 * @param string $en_name 英文名称
	 * @param string $short_name 简称
	 * @return  bool
	 */
	public function act_addCountriesStandard(){
        $cn_name	= isset($_POST["cn_name"]) ? post_check($_POST["cn_name"]) : "";
        $en_name	= isset($_POST["en_name"]) ? post_check($_POST["en_name"]) : "";
        $short_name	= isset($_POST["short_name"]) ? post_check($_POST["short_name"]) : "";
		$act		= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod		= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if	(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 10001;
			self::$errMsg   = "对不起,您无数据添加权限！";
			return false;
		}
		if (empty($cn_name) || empty($cn_name)) {
			self::$errCode  = 10000;
			self::$errMsg   = "标准国家中文名称或英文名称有误！";
			return false;
		}
		$data  = array(
			"countryNameCn"	=> $cn_name,
			"countryNameEn"	=> $en_name,
			"countrySn"		=> $short_name,
			"createdTime"	=> time(),
		);
        $res			= CountriesStandardModel::addCountriesStandard($data);
		self::$errCode  = CountriesStandardModel::$errCode;
        self::$errMsg   = CountriesStandardModel::$errMsg;
		return $res;
    }

	/**
	 * CountriesStandardAct::act_updateCountriesStandard()
	 * 修改标准国家
	 * @param string $cn_name 中文名称
	 * @param string $en_name 英文名称
	 * @param string $short_name 简称
	 * @return  bool
	 */
	public function act_updateCountriesStandard(){
		$id			= isset($_POST["id"]) ? intval(trim($_POST["id"])) : 0;
		$cn_name	= isset($_POST["cn_name"]) ? post_check($_POST["cn_name"]) : "";
        $en_name	= isset($_POST["en_name"]) ? post_check($_POST["en_name"]) : "";
        $short_name	= isset($_POST["short_name"]) ? post_check($_POST["short_name"]) : "";
		$act		= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod		= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if	(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 10002;
			self::$errMsg   = "对不起,您无数据编辑权限！";
			return false;
		}
		if (empty($id) || !is_numeric($id)) {
			self::$errCode  = 10000;
			self::$errMsg   = "标准国家ID有误！";
			return false;
		}
		if (empty($cn_name) || empty($cn_name)) {
			self::$errCode  = 10001;
			self::$errMsg   = "标准国家中文名称或英文名称有误！";
			return false;
		}
		$data  = array(
			"countryNameCn"	=> $cn_name,
			"countryNameEn"	=> $en_name,
			"countrySn"		=> $short_name,
		);
        $res			= CountriesStandardModel::updateCountriesStandard($id, $data);
		self::$errCode  = CountriesStandardModel::$errCode;
        self::$errMsg   = CountriesStandardModel::$errMsg;
		return $res;
    }
	
	/**
	 * CountriesStandardAct::act_delCountriesStandard()
	 * 删除标准国家
	 * @param int $id 标准国家ID
	 * @return  bool
	 */
	public function act_delCountriesStandard(){
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
			self::$errMsg   = "标准国家ID有误！";
			return false;
		}
        $res			= CountriesStandardModel::delCountriesStandard($id);
		self::$errCode  = CountriesStandardModel::$errCode;
        self::$errMsg   = CountriesStandardModel::$errMsg;
		return $res;
    }
}
?>