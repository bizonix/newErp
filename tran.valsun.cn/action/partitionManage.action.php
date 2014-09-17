<?php
/**
 * 类名：PartitionManageAct
 * 功能：分区管理动作处理层
 * 版本：1.0
 * 日期：2013/10/21
 * 作者：管拥军
 */
  
class PartitionManageAct {
    public static $errCode	= 0;
	public static $errMsg	= "";

	/**
	 * PartitionManageAct::actList()
	 * 列出符合条件的数据并分页显示
	 * @param string $where 查询条件
	 * @param integer $page 页码
	 * @param integer $pagenum 每页个数
	 * @return array 结果集数组
	 */
 	public function actList($where='1', $page=1, $pagenum=20){
		$res			= PartitionManageModel::modList($where, $page, $pagenum);
		self::$errCode  = PartitionManageModel::$errCode;
        self::$errMsg   = PartitionManageModel::$errMsg;
        return $res;
    }

	/**
	 * PartitionManageAct::actListCount()
	 * 返回某个条件结果统计的总数
	 * @param string $where 查询条件
	 * @return integer 总数量 
	 */
	public function actListCount($where='1'){
		$res			= PartitionManageModel::modListCount($where);
		self::$errCode  = PartitionManageModel::$errCode;
        self::$errMsg   = PartitionManageModel::$errMsg;
        return $res;
    }
	
	/**
	 * PartitionManageAct::actModify()
	 * 返回某个分区的信息
	 * @param int $id 查询ID
	 * @return array 
	 */
	public function actModify($id){
		$res			= PartitionManageModel::modModify($id);
		self::$errCode  = PartitionManageModel::$errCode;
        self::$errMsg   = PartitionManageModel::$errMsg;
        return $res;
    }

	/**
	 * PartitionManageAct::act_addPartitionManage()
	 * 添加分区
	 * @param int $ch_id 渠道ID
	 * @param string $pt_code 分区代码
	 * @param string $pt_name 分区名称
	 * @param string $pt_add 回邮地址
	 * @param int $pt_enable 是否启用
	 * @param string $pt_country 分区国家
	 * @return  bool
	 */
	public function act_addPartitionManage(){
        $ch_id		= isset($_POST["ch_id"]) ? abs(intval($_POST["ch_id"])) : 0;
        $pt_code	= isset($_POST["pt_code"]) ? post_check($_POST["pt_code"]) : "";
        $pt_name	= isset($_POST["pt_name"]) ? post_check($_POST["pt_name"]) : "";
        $pt_ali		= isset($_POST["pt_ali"]) ? post_check($_POST["pt_ali"]) : "";
        $pt_country	= isset($_POST["pt_country"]) ? htmlspecialchars($_POST["pt_country"]) : "";
        $pt_add_html= isset($_POST["pt_add_html"]) ? htmlspecialchars($_POST["pt_add_html"]) : "";
        $pt_add		= isset($_POST["pt_add"]) ? post_check($_POST["pt_add"]) : "";
        $pt_enable	= isset($_POST["pt_enable"]) ? post_check($_POST["pt_enable"]) : "";
		$act		= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod		= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if	(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 10010;
			self::$errMsg   = "对不起,您无数据添加权限！";
			return false;
		}
		if (empty($ch_id) || !is_numeric($ch_id)) {
			self::$errCode  = 10000;
			self::$errMsg   = "渠道ID参数有误！";
			return false;
		}
		if (empty($pt_code) || empty($pt_name)) {
			self::$errCode  = 10002;
			self::$errMsg   = "分区代码或分区名称参数有误！";
			return false;
		}
		if (empty($pt_ali)) {
			self::$errCode  = 10006;
			self::$errMsg   = "分区简称参数有误！";
			return false;
		}
		if (empty($pt_country)) {
			self::$errCode  = 10005;
			self::$errMsg   = "分区国家参数有误！";
			return false;
		}
		if (!in_array($pt_enable,array("0","1"))) {
			self::$errCode  = 10004;
			self::$errMsg   = "是否启用参数有误！";
			return false;
		}
		$data  = array(
			"channelId"		=> $ch_id,
			"partitionCode"	=> $pt_code,
			"partitionName"	=> $pt_name,
			"partitionAli"	=> $pt_ali,
			"countries"		=> $pt_country,
			"returnAddress"	=> $pt_add,
			"enable"		=> $pt_enable,
			"createdTime"		=> time(),
		);
		if (!empty($pt_add_html)) $data["returnAddHtml"] = $pt_add_html;
        $res			= PartitionManageModel::addPartitionManage($data);
		self::$errCode  = PartitionManageModel::$errCode;
        self::$errMsg   = PartitionManageModel::$errMsg;
		return $res;
    }

	/**
	 * PartitionManageAct::act_updatePartitionManage()
	 * 修改分区
	 * @param int $id 分区ID
	 * @param string $pt_code 分区代码
	 * @param string $pt_name 分区名称
	 * @param string $pt_add 回邮地址
	 * @param int $pt_enable 是否启用
	 * @param string $pt_country 分区国家
	 * @return  bool
	 */
	public function act_updatePartitionManage(){
		$id			= isset($_POST["id"]) ? intval(trim($_POST["id"])) : 0;
        $pt_code	= isset($_POST["pt_code"]) ? post_check($_POST["pt_code"]) : "";
        $pt_name	= isset($_POST["pt_name"]) ? post_check($_POST["pt_name"]) : "";
        $pt_ali		= isset($_POST["pt_ali"]) ? post_check($_POST["pt_ali"]) : "";
        $pt_country	= isset($_POST["pt_country"]) ? htmlspecialchars($_POST["pt_country"]) : "";
        $pt_add_html= isset($_POST["pt_add_html"]) ? htmlspecialchars($_POST["pt_add_html"]) : "";
        $pt_add		= isset($_POST["pt_add"]) ? post_check($_POST["pt_add"]) : "";
        $pt_enable	= isset($_POST["pt_enable"]) ? post_check($_POST["pt_enable"]) : "";
		$act		= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod		= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if	(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 20005;
			self::$errMsg   = "对不起,您无数据编辑权限！";
			return false;
		}
		if (empty($id) || !is_numeric($id)) {
			self::$errCode  = 20000;
			self::$errMsg   = "分区ID参数有误！";
			return false;
		}
		if (empty($pt_code) || empty($pt_name)) {
			self::$errCode  = 20001;
			self::$errMsg   = "分区代码或分区名称参数有误！";
			return false;
		}
		if (empty($pt_ali)) {
			self::$errCode  = 20005;
			self::$errMsg   = "分区简称参数有误！";
			return false;
		}
		if (empty($pt_country)) {
			self::$errCode  = 20002;
			self::$errMsg   = "分区国家参数有误！";
			return false;
		}
		if (!in_array($pt_enable,array("0","1"))) {
			self::$errCode  = 20004;
			self::$errMsg   = "是否启用参数有误！";
			return false;
		}
		$data  = array(
			"partitionCode"	=> $pt_code,
			"partitionName"	=> $pt_name,
			"partitionAli"	=> $pt_ali,
			"countries"		=> $pt_country,
			"returnAddress"	=> $pt_add,
			"enable"		=> $pt_enable,
			"lastmodified"	=> time(),
		);
		if (!empty($pt_add_html)) $data["returnAddHtml"] = $pt_add_html;
		$res			= PartitionManageModel::updatePartitionManage($id, $data);
		self::$errCode  = PartitionManageModel::$errCode;
        self::$errMsg   = PartitionManageModel::$errMsg;
		return $res;
    }
	
	/**
	 * PartitionManageAct::act_delPartitionManage()
	 * 删除分区
	 * @param int $id 分区ID
	 * @return  bool
	 */
	public function act_delPartitionManage(){
		$id			= isset($_POST["id"]) ? intval(trim($_POST["id"])) : 0;
		$act		= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod		= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if	(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 30001;
			self::$errMsg   = "对不起,您无数据删除权限！";
			return false;
		}
		if (empty($id) || !is_numeric($id)) {
			self::$errCode  = 30000;
			self::$errMsg   = "分区ID有误！";
			return false;
		}
        $res			= PartitionManageModel::delPartitionManage($id);
		self::$errCode  = PartitionManageModel::$errCode;
        self::$errMsg   = PartitionManageModel::$errMsg;
		return $res;
    }
}
?>