<?php
/**
 * 类名：PlatFormAct
 * 功能：平台管理动作处理层
 * 版本：1.0
 * 日期：2013/10/21
 * 作者：管拥军
 */
  
class PlatFormAct {
    public static $errCode	= 0;
	public static $errMsg	= "";

	/**
	 * PlatFormAct::actList()
	 * 列出符合条件的数据并分页显示
	 * @param string $where 查询条件
	 * @param integer $page 页码
	 * @param integer $pagenum 每页个数
	 * @return array 结果集数组
	 */
 	public function actList($where='1', $page=1, $pagenum=20){
		$res			= PlatFormModel::modList($where, $page, $pagenum);
		self::$errCode  = PlatFormModel::$errCode;
        self::$errMsg   = PlatFormModel::$errMsg;
        return $res;
    }

	/**
	 * PlatFormAct::actListCount()
	 * 返回某个条件结果统计的总数
	 * @param string $where 查询条件
	 * @return integer 总数量 
	 */
	public function actListCount($where='1'){
		$res			= PlatFormModel::modListCount($where);
		self::$errCode  = PlatFormModel::$errCode;
        self::$errMsg   = PlatFormModel::$errMsg;
        return $res;
    }
	
	/**
	 * PlatFormAct::actModify()
	 * 返回某个平台的信息
	 * @param int $id 查询ID
	 * @return array 
	 */
	public function actModify($id){
		$res			= PlatFormModel::modModify($id);
		self::$errCode  = PlatFormModel::$errCode;
        self::$errMsg   = PlatFormModel::$errMsg;
        return $res;
    }

	/**
	 * PlatFormAct::act_addPlatForm()
	 * 添加平台
	 * @param string $cn_name 中文名称
	 * @param string $en_name 英文名称
	 * @return  bool
	 */
	public function act_addPlatForm(){
        $cn_name	= isset($_POST["cn_name"]) ? post_check($_POST["cn_name"]) : "";
        $en_name	= isset($_POST["en_name"]) ? post_check($_POST["en_name"]) : "";
		$act		= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod		= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if	(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 10001;
			self::$errMsg   = "对不起,您无数据添加权限！";
			return false;
		}
		if (empty($cn_name) || empty($cn_name)) {
			self::$errCode  = 10000;
			self::$errMsg   = "平台中文名称或英文名称有误！";
			return false;
		}
		$data  = array(
			"platformNameCn"	=> $cn_name,
			"platformNameEn"	=> $en_name,
			"createdTime"		=> time(),
		);
        $res			= PlatFormModel::addPlatForm($data);
		self::$errCode  = PlatFormModel::$errCode;
        self::$errMsg   = PlatFormModel::$errMsg;
		return $res;
    }

	/**
	 * PlatFormAct::act_updatePlatForm()
	 * 修改平台
	 * @param string $cn_name 中文名称
	 * @param string $en_name 英文名称
	 * @return  bool
	 */
	public function act_updatePlatForm(){
		$id			= isset($_POST["id"]) ? intval(trim($_POST["id"])) : 0;
		$cn_name	= isset($_POST["cn_name"]) ? post_check($_POST["cn_name"]) : "";
        $en_name	= isset($_POST["en_name"]) ? post_check($_POST["en_name"]) : "";
		$act		= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod		= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if	(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 10001;
			self::$errMsg   = "对不起,您无数据编辑权限！";
			return false;
		}
		if (empty($id) || !is_numeric($id)) {
			self::$errCode  = 10000;
			self::$errMsg   = "平台ID有误！";
			return false;
		}
		if (empty($cn_name) || empty($cn_name)) {
			self::$errCode  = 10001;
			self::$errMsg   = "平台中文名称或英文名称有误！";
			return false;
		}
		$data  = array(
			"platformNameCn"	=> $cn_name,
			"platformNameEn"	=> $en_name,
		);
        $res			= PlatFormModel::updatePlatForm($id, $data);
		self::$errCode  = PlatFormModel::$errCode;
        self::$errMsg   = PlatFormModel::$errMsg;
		return $res;
    }
	
	/**
	 * PlatFormAct::act_delPlatForm()
	 * 删除平台
	 * @param int $id 平台ID
	 * @return  bool
	 */
	public function act_delPlatForm(){
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
        $res			= PlatFormModel::delPlatForm($id);
		self::$errCode  = PlatFormModel::$errCode;
        self::$errMsg   = PlatFormModel::$errMsg;
		return $res;
    }
}
?>