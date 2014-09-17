<?php
/**
 * 类名：TrackWarnNodeAct
 * 功能：运输方式节点预警管理动作处理层
 * 版本：1.0
 * 日期：2013/10/21
 * 作者：管拥军
 */
  
class TrackWarnNodeAct {
    public static $errCode	= 0;
	public static $errMsg	= "";

	/**
	 * TrackWarnNodeAct::actList()
	 * 列出符合条件的数据并分页显示
	 * @param string $where 查询条件
	 * @param integer $page 页码
	 * @param integer $pagenum 每页个数
	 * @return array 结果集数组
	 */
 	public function actList($where='1', $page=1, $pagenum=20){
		$res			= TrackWarnNodeModel::modList($where, $page, $pagenum);
		self::$errCode  = TrackWarnNodeModel::$errCode;
        self::$errMsg   = TrackWarnNodeModel::$errMsg;
        return $res;
    }

	/**
	 * TrackWarnNodeAct::actListCount()
	 * 返回某个条件结果统计的总数
	 * @param string $where 查询条件
	 * @return integer 总数量 
	 */
	public function actListCount($where='1'){
		$res			= TrackWarnNodeModel::modListCount($where);
		self::$errCode  = TrackWarnNodeModel::$errCode;
        self::$errMsg   = TrackWarnNodeModel::$errMsg;
        return $res;
    }
	
	/**
	 * TrackWarnNodeAct::actModify()
	 * 返回某个运输方式节点预警的信息
	 * @param int $id 查询ID
	 * @return array 
	 */
	public function actModify($id){
		$res			= TrackWarnNodeModel::modModify($id);
		self::$errCode  = TrackWarnNodeModel::$errCode;
        self::$errMsg   = TrackWarnNodeModel::$errMsg;
        return $res;
    }
	
	/**
	 * TrackWarnNodeAct::act_addTrackWarnNode()
	 * 添加运输方式节点预警
	 * @param string $node_name 跟踪系统运输方式节点
	 * @param string $node_days erp运输方式节点
	 * @param string $ship_id 运输方式ID
	 * @return  bool
	 */
	public function act_addTrackWarnNode(){
        $node_name		= isset($_POST["node_name"]) ? post_check($_POST["node_name"]) : "";
        $node_days		= isset($_POST["node_days"]) ? abs(intval(trim($_POST["node_days"]))) : 0;
        $node_key		= isset($_POST["node_key"]) ? post_check($_POST["node_key"]) : "";
        $node_place		= isset($_POST["node_place"]) ? post_check($_POST["node_place"]) : "";
        $node_chid		= isset($_POST["node_chid"]) ? abs(intval($_POST["node_chid"])) : 0;
        $ship_id		= isset($_POST["ship_id"]) ? abs(intval(trim($_POST["ship_id"]))) : 0;
		$act			= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod			= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if	(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 10010;
			self::$errMsg   = "对不起,您无数据添加权限！";
			return false;
		}
		if (empty($ship_id)) {
			self::$errCode  = 10000;
			self::$errMsg   = "运输方式有误！";
			return false;
		}
		if (empty($node_name)) {
			self::$errCode  = 10001;
			self::$errMsg   = "节点名称有误！";
			return false;
		}
		if (empty($node_days)) {
			self::$errCode  = 10002;
			self::$errMsg   = "节点预警天数有误！";
			return false;
		}
		if (empty($node_key)) {
			self::$errCode  = 10003;
			self::$errMsg   = "节点关键词有误！";
			return false;
		}
		$uid				= $_SESSION[C('USER_AUTH_SYS_ID')];
		$data  = array(
			"nodeName"		=> $node_name,
			"nodeDays"		=> $node_days,
			"nodeKey"		=> $node_key,
			"createTime"	=> time(),
			"carrierId"		=> $ship_id,
			"channelId"		=> $node_chid,
			"nodePlace"		=> $node_place,
			"createUserId"	=> $uid,
		);
        $res			= TrackWarnNodeModel::addTrackWarnNode($data);
		self::$errCode  = TrackWarnNodeModel::$errCode;
        self::$errMsg   = TrackWarnNodeModel::$errMsg;
		return $res;
    }

	/**
	 * TrackWarnNodeAct::act_updateTrackWarnNode()
	 * 修改运输方式节点预警
	 * @param string $node_name 跟踪系统运输方式节点
	 * @param string $node_days erp运输方式节点
	 * @param string $ship_id 运输方式ID
	 * @return  bool
	 */
	public function act_updateTrackWarnNode(){
		$id				= isset($_POST["id"]) ? abs(intval(trim($_POST["id"]))) : 0;
        $node_name		= isset($_POST["node_name"]) ? post_check($_POST["node_name"]) : "";
        $node_days		= isset($_POST["node_days"]) ? abs(intval(trim($_POST["node_days"]))) : 0;
        $node_key		= isset($_POST["node_key"]) ? post_check($_POST["node_key"]) : "";
        $node_place		= isset($_POST["node_place"]) ? post_check($_POST["node_place"]) : "";
        $node_chid		= isset($_POST["node_chid"]) ? abs(intval($_POST["node_chid"])) : 0;
        $ship_id		= isset($_POST["ship_id"]) ? abs(intval(trim($_POST["ship_id"]))) : 0;
		$act			= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod			= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if	(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 10010;
			self::$errMsg   = "对不起,您无数据编辑权限！";
			return false;
		}
		if (empty($id) || !is_numeric($id)) {
			self::$errCode  = 10000;
			self::$errMsg   = "ID有误！";
			return false;
		}
		if (empty($ship_id)) {
			self::$errCode  = 10001;
			self::$errMsg   = "运输方式有误！";
			return false;
		}
		if (empty($node_name)) {
			self::$errCode  = 10002;
			self::$errMsg   = "节点名称有误！";
			return false;
		}
		if (empty($node_days)) {
			self::$errCode  = 10003;
			self::$errMsg   = "节点预警天数有误！";
			return false;
		}
		if (empty($node_key)) {
			self::$errCode  = 10004;
			self::$errMsg   = "节点关键词有误！";
			return false;
		}
		$uid				= $_SESSION[C('USER_AUTH_SYS_ID')];
		$data  = array(
			"nodeName"		=> $node_name,
			"nodeDays"		=> $node_days,
			"nodeKey"		=> $node_key,
			"modifyTime"	=> time(),
			"carrierId"		=> $ship_id,
			"channelId"		=> $node_chid,
			"nodePlace"		=> $node_place,
			"modifyUserId"	=> $uid,
		);
        $res			= TrackWarnNodeModel::updateTrackWarnNode($id, $data);
		self::$errCode  = TrackWarnNodeModel::$errCode;
        self::$errMsg   = TrackWarnNodeModel::$errMsg;
		return $res;
    }
	
	/**
	 * TrackWarnNodeAct::act_delTrackWarnNode()
	 * 删除运输方式节点预警
	 * @param int $id 运输方式节点预警ID
	 * @return  bool
	 */
	public function act_delTrackWarnNode(){
		$id			= isset($_POST["id"]) ? abs(intval(trim($_POST["id"]))) : 0;
		$act		= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod		= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if	(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 10001;
			self::$errMsg   = "对不起,您无数据删除权限！";
			return false;
		}
		if (empty($id) || !is_numeric($id)) {
			self::$errCode  = 10000;
			self::$errMsg   = "ID有误！";
			return false;
		}
        $res			= TrackWarnNodeModel::delTrackWarnNode($id);
		self::$errCode  = TrackWarnNodeModel::$errCode;
        self::$errMsg   = TrackWarnNodeModel::$errMsg;
		return $res;
    }
}
?>