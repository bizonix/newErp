<?php
/**
 * 类名：CountriesChinaAct
 * 功能：中国地区列表管理动作处理层
 * 版本：1.0
 * 日期：2013/10/21
 * 作者：管拥军
 */
  
class CountriesChinaAct {
    public static $errCode	= 0;
	public static $errMsg	= "";

	/**
	 * CountriesChinaAct::actList()
	 * 列出符合条件的数据并分页显示
	 * @param string $where 查询条件
	 * @param integer $page 页码
	 * @param integer $pagenum 每页个数
	 * @return array 结果集数组
	 */
 	public function actList($where='1', $page=1, $pagenum=20){
		$res			= CountriesChinaModel::modList($where, $page, $pagenum);
		self::$errCode  = CountriesChinaModel::$errCode;
        self::$errMsg   = CountriesChinaModel::$errMsg;
        return $res;
    }

	/**
	 * CountriesChinaAct::actListCount()
	 * 返回某个条件结果统计的总数
	 * @param string $where 查询条件
	 * @return integer 总数量 
	 */
	public function actListCount($where='1'){
		$res			= CountriesChinaModel::modListCount($where);
		self::$errCode  = CountriesChinaModel::$errCode;
        self::$errMsg   = CountriesChinaModel::$errMsg;
        return $res;
    }
	
	/**
	 * CountriesChinaAct::actModify()
	 * 返回某个中国地区的信息
	 * @param int $id 查询ID
	 * @return array 
	 */
	public function actModify($id){
		$res			= CountriesChinaModel::modModify($id);
		self::$errCode  = CountriesChinaModel::$errCode;
        self::$errMsg   = CountriesChinaModel::$errMsg;
        return $res;
    }

	/**
	 * CountriesChinaAct::act_addCountriesChina()
	 * 添加中国地区
	 * @param string $cn_name 地区名称
	 * @return  bool
	 */
	public function act_addCountriesChina(){
        $cn_name	= isset($_POST["cn_name"]) ? post_check($_POST["cn_name"]) : "";
		$act		= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod		= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if	(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 10001;
			self::$errMsg   = "对不起,您无数据添加权限！";
			return false;
		}
		if (empty($cn_name)) {
			self::$errCode  = 10000;
			self::$errMsg   = "地区名称有误！";
			return false;
		}
		$uid				= $_SESSION[C('USER_AUTH_SYS_ID')];
		$data  = array(
			"countryName"	=> $cn_name,
			"add_user_id"	=> $uid,
			"addTime"	=> time(),
		);
        $res			= CountriesChinaModel::addCountriesChina($data);
		self::$errCode  = CountriesChinaModel::$errCode;
        self::$errMsg   = CountriesChinaModel::$errMsg;
		return $res;
    }

	/**
	 * CountriesChinaAct::act_updateCountriesChina()
	 * 修改中国地区
	 * @param string $cn_name 地区名称
	 * @return  bool
	 */
	public function act_updateCountriesChina(){
		$id			= isset($_POST["id"]) ? intval(trim($_POST["id"])) : 0;
		$cn_name	= isset($_POST["cn_name"]) ? post_check($_POST["cn_name"]) : "";
		$act		= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod		= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if	(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 10002;
			self::$errMsg   = "对不起,您无数据编辑权限！";
			return false;
		}
		if (empty($id) || !is_numeric($id)) {
			self::$errCode  = 10000;
			self::$errMsg   = "地区ID有误！";
			return false;
		}
		if (empty($cn_name) || empty($cn_name)) {
			self::$errCode  = 10001;
			self::$errMsg   = "地区名称有误！";
			return false;
		}
		$uid				= $_SESSION[C('USER_AUTH_SYS_ID')];
		$data  = array(
			"countryName"	=> $cn_name,
			"edit_user_id"	=> $uid,
			"editTime"		=> time(),
		);
        $res			= CountriesChinaModel::updateCountriesChina($id, $data);
		self::$errCode  = CountriesChinaModel::$errCode;
        self::$errMsg   = CountriesChinaModel::$errMsg;
		return $res;
    }
	
	/**
	 * CountriesChinaAct::act_delCountriesChina()
	 * 删除中国地区
	 * @param int $id 中国地区ID
	 * @return  bool
	 */
	public function act_delCountriesChina(){
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
			self::$errMsg   = "地区ID有误！";
			return false;
		}
        $res			= CountriesChinaModel::delCountriesChina($id);
		self::$errCode  = CountriesChinaModel::$errCode;
        self::$errMsg   = CountriesChinaModel::$errMsg;
		return $res;
    }
}
?>