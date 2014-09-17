<?php
/**
 * 类名：CarrierManageAct
 * 功能：运输方式管理动作处理层
 * 版本：1.0
 * 日期：2013/10/21
 * 作者：管拥军
 */
  
class CarrierManageAct {
    public static $errCode	= 0;
	public static $errMsg	= "";

	/**
	 * CarrierManageAct::actList()
	 * 列出符合条件的数据并分页显示
	 * @param string $where 查询条件
	 * @param integer $page 页码
	 * @param integer $pagenum 每页个数
	 * @return array 结果集数组
	 */
 	public function actList($where='1', $page=1, $pagenum=20){
		$res			= CarrierManageModel::modList($where, $page, $pagenum);
		self::$errCode  = CarrierManageModel::$errCode;
        self::$errMsg   = CarrierManageModel::$errMsg;
        return $res;
    }

	/**
	 * CarrierManageAct::actListCount()
	 * 返回某个条件结果统计的总数
	 * @param string $where 查询条件
	 * @return integer 总数量 
	 */
	public function actListCount($where='1'){
		$res			= CarrierManageModel::modListCount($where);
		self::$errCode  = CarrierManageModel::$errCode;
        self::$errMsg   = CarrierManageModel::$errMsg;
        return $res;
    }
	
	/**
	 * CarrierManageAct::actModify()
	 * 返回某个运输方式的信息
	 * @param int $id 查询ID
	 * @return array 
	 */
	public function actModify($id){
		$res			= CarrierManageModel::modModify($id);
		self::$errCode  = CarrierManageModel::$errCode;
        self::$errMsg   = CarrierManageModel::$errMsg;
        return $res;
    }

	/**
	 * CarrierManageAct::actAddCarrierManage()
	 * 添加运输方式
	 * @param array $data 
	 * @return  bool
	 */
	public function actAddCarrierManage($data){
        $res			= CarrierManageModel::ModaddCarrierManage($data);
		self::$errCode  = CarrierManageModel::$errCode;
        self::$errMsg   = CarrierManageModel::$errMsg;
		return $res;
    }

	/**
	 * CarrierManageAct::actUpdateCarrierManage()
	 * 修改运输方式
	 * @param array $data 
	 * @return  bool
	 */
	public function actUpdateCarrierManage($id, $data){
		$res			= CarrierManageModel::ModUpdateCarrierManage($id, $data);
		self::$errCode  = CarrierManageModel::$errCode;
        self::$errMsg   = CarrierManageModel::$errMsg;
		return $res;
    }
	
	/**
	 * CarrierManageAct::act_delCarrierManage()
	 * 删除运输方式
	 * @param int $id 运输方式ID
	 * @return  bool
	 */
	public function act_delCarrierManage(){
		$id			= isset($_POST["id"]) ? intval(trim($_POST["id"])) : 0;
		$status		= isset($_POST["status"]) ? trim($_POST["status"]) : "";
		$act		= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod		= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if	(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 10002;
			self::$errMsg   = "对不起,您无数据(禁用或启用)权限！";
			return false;
		}
		if (empty($id) || !is_numeric($id)) {
			self::$errCode  = 10000;
			self::$errMsg   = "运输方式ID有误！";
			return false;
		}
		if (!in_array($status,array(0,1))) {
			self::$errCode  = 10001;
			self::$errMsg   = "状态参数有误！";
			return false;
		}
        $res			= CarrierManageModel::delCarrierManage($id, $status);
		self::$errCode  = CarrierManageModel::$errCode;
        self::$errMsg   = CarrierManageModel::$errMsg;
		return $res;
    }
}
?>