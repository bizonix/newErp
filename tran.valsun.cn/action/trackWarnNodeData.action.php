<?php
/**
 * 类名：TrackWarnNodeDataAct
 * 功能：运输方式节点预警数据管理动作处理层
 * 版本：1.0
 * 日期：2014/05/16
 * 作者：管拥军
 */
  
class TrackWarnNodeDataAct {
    public static $errCode	= 0;
	public static $errMsg	= "";
	
	/**
	 * TrackWarnNodeDataAct::actIndex()
	 * 列出符合条件的数据并分页显示
	 * @param string $condition 查询条件
	 * @param integer $curpage 页码
	 * @param integer $pagenum 每页个数
	 * @return array 
	 */
 	public function actIndex(){
		$data			= array();
		$condition		= '';
		$TrackWarnNodeData		= new TrackWarnNodeDataModel();
		//接收参数生成条件
		$curpage		= isset($_GET['page']) ? abs(intval($_GET['page'])) : 1;
		$type			= isset($_GET['type']) ? trim($_GET['type']) : '';
		$key			= isset($_GET['key']) ? post_check(trim($_GET['key'])) : '';
		$condition		.= "1";
		if($type && $key) {
			if(!in_array($type,array('country'))) redirect_to("index.php?mod=trackWarnNodeData&act=index");
			$condition	.= ' AND '.$type." = '".$key."'";
		}
		//获取符合条件的数据并分页
		$pagenum		= 20;
		$total			= $TrackWarnNodeData->modListCount($condition);
		$res			= $TrackWarnNodeData->modList($condition, $curpage, $pagenum);
		$page	 		= new Page($total, $pagenum, '', 'CN');
		$pageStr		= "";
		if($res) {
			if($total>$pagenum) {
				$pageStr = $page->fpage(array(0,1,2,3,4,5,6,7,8,9));
			} else {
				$pageStr = $page->fpage(array(0,1,2,3));
			}
		} else {
			$pageStr 	 = '暂无数据';
		}		
		//封装数据返回
		$data['key']	 = $key;
		$data['type']	 = $type;
		$data['lists']	 = $res;
		$data['pages']	 = $pageStr;
		self::$errCode   = TrackWarnNodeDataModel::$errCode;
        self::$errMsg    = TrackWarnNodeDataModel::$errMsg;
		if(self::$errCode!=0) {
			show_message($this->smarty,self::$errMsg,"");	
			return false;
		}
        return $data;
    }

	/**
	 * TrackWarnNodeDataAct::actModify()
	 * 返回某个国家预警数据信息
	 * @param int $id 查询ID
	 * @return array 
	 */
	public function actModify(){
		$data			= array();
		$id				= isset($_GET['id']) ? intval(trim($_GET['id'])) : 0;
		if(empty($id)) {
			show_message($this->smarty,"ID不能为空？","");	
			return false;
		}
		$data['id']		= $id;
		$data['lists']	= TransOpenApiModel::getTrackNodeList(61); //预警节点列表
		$data['res']	= TrackWarnNodeDataModel::modModify($id);
		if(empty($data['res'])) {
			show_message($this->smarty,"数据为空，请返回确认条件!","");	
			return false;
		}
		self::$errCode  = TrackWarnNodeDataModel::$errCode;
        self::$errMsg   = TrackWarnNodeDataModel::$errMsg;
		if(self::$errCode!=0) {
			show_message($this->smarty,self::$errMsg,"");	
			return false;
		}
        return $data;
    }	
		
	/**
	 * TrackWarnNodeDataAct::act_updateTrackWarnNodeData()
	 * 修改运输方式节点预警数据
	 * @param string $node_id 跟踪系统运输方式节点
	 * @param string $aging erp运输方式节点
	 * @param string $ship_id 运输方式ID
	 * @return  bool
	 */
	public function act_updateTrackWarnNodeData(){
		$id			= isset($_POST["id"]) ? abs(intval(trim($_POST["id"]))) : 0;
        $nodeId		= isset($_POST["nodeId"]) ? post_check($_POST["nodeId"]) : "";
        $aging		= isset($_POST["aging"]) ? abs(intval(trim($_POST["aging"]))) : 0;
        $country	= isset($_POST["country"]) ? post_check($_POST["country"]) : "";
        $is_auto	= isset($_POST["is_auto"]) ? abs(intval(trim($_POST["is_auto"]))) : "";
		$act		= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod		= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if	(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 10010;
			self::$errMsg   = "对不起,您无数据编辑权限！";
			return false;
		}
		if(empty($id) || !is_numeric($id)) {
			self::$errCode  = 10000;
			self::$errMsg   = "ID有误！";
			return false;
		}
		if(empty($nodeId)) {
			self::$errCode  = 10001;
			self::$errMsg   = "节点名称有误！";
			return false;
		}
		if(empty($aging)) {
			self::$errCode  = 10002;
			self::$errMsg   = "节点时效有误！";
			return false;
		}
		if(empty($country)) {
			self::$errCode  = 10003;
			self::$errMsg   = "节点国家参数有误！";
			return false;
		}
		if(!in_array($is_auto,array(0,1))) {
			self::$errCode  = 10004;
			self::$errMsg   = "是否自动更新参数有误！";
			return false;
		}		
		$uid				= $_SESSION[C('USER_AUTH_SYS_ID')];
		$data  = array(
			//"nodeId"		=> $nodeId,
			"aging"			=> $aging,
			"editTime"		=> time(),
			//"country"		=> $country,
			"is_auto"		=> $is_auto,
			"edit_user_id"	=> $uid,
		);
        $res			= TrackWarnNodeDataModel::updateTrackWarnNodeData($id, $data);
		self::$errCode  = TrackWarnNodeDataModel::$errCode;
        self::$errMsg   = TrackWarnNodeDataModel::$errMsg;
		return $res;
    }
	
	/**
	 * TrackWarnNodeDataAct::act_delTrackWarnNodeData()
	 * 删除运输方式节点预警数据
	 * @param int $id 运输方式节点预警数据ID
	 * @return  bool
	 */
	public function act_delTrackWarnNodeData(){
		$id			= isset($_POST["id"]) ? abs(intval(trim($_POST["id"]))) : 0;
		$act		= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod		= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if	(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 10001;
			self::$errMsg   = "对不起,您无数据删除权限！";
			return false;
		}
		if(empty($id) || !is_numeric($id)) {
			self::$errCode  = 10000;
			self::$errMsg   = "ID有误！";
			return false;
		}
        $res			= TrackWarnNodeDataModel::delTrackWarnNodeData($id);
		self::$errCode  = TrackWarnNodeDataModel::$errCode;
        self::$errMsg   = TrackWarnNodeDataModel::$errMsg;
		return $res;
    }
}
?>