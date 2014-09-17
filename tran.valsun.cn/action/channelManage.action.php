<?php
/**
 * 类名：ChannelManageAct
 * 功能：渠道管理动作处理层
 * 版本：1.0
 * 日期：2013/10/21
 * 作者：管拥军
 */
  
class ChannelManageAct {
    public static $errCode	= 0;
	public static $errMsg	= "";

	/**
	 * ChannelManageAct::actIndex()
	 * 列出符合条件的数据并分页显示
	 * @param string $condition 查询条件
	 * @param integer $curpage 页码
	 * @param integer $pagenum 每页个数
	 * @return array 
	 */
 	public function actIndex(){
		$data			= array();
		$condition		= '';
		$channelManage	= new ChannelManageModel();
		//接收参数生成条件
		$curpage		= isset($_GET['page']) ? abs(intval($_GET['page'])) : 1;
		$type			= isset($_GET['type']) ? trim($_GET['type']) : '';
		$key			= isset($_GET['key']) ? post_check(trim($_GET['key'])) : '';
		$id				= isset($_GET['id']) ? intval($_GET['id']) : 0;//运输方式ID
		$condition		.= "1";
		$condition		.= " AND carrierId = {$id}";
		if ($type && $key) {
			if (!in_array($type,array('channelName','channelAlias'))) redirect_to("index.php?mod=channelManage&act=index");
			$condition	.= ' AND '.$type." = '".$key."'";
		}
		//获取符合条件的数据并分页
		$pagenum		= 20;
		$total			= $channelManage->modListCount($condition);
		$res			= $channelManage->modList($condition, $curpage, $pagenum);
		$page	 		= new Page($total, $pagenum, '', 'CN');
		$pageStr		= "";
		if ($res) {
			if ($total>$pagenum) {
				$pageStr = $page->fpage(array(0,1,2,3,4,5,6,7,8,9));
			} else {
				$pageStr = $page->fpage(array(0,1,2,3));
			}
		} else {
			$pageStr 	 = '暂无数据';
		}		
		//封装数据返回
		$data['id']		 = $id;
		$data['key']	 = $key;
		$data['type']	 = $type;
		$data['lists']	 = $res;
		$data['pages']	 = $pageStr;
		self::$errCode   = ChannelManageModel::$errCode;
        self::$errMsg    = ChannelManageModel::$errMsg;
		if (self::$errCode!=0) {
			show_message($this->smarty,self::$errMsg,"");	
			return false;
		}
        return $data;
    }

	/**
	 * ChannelManageAct::actAdd()
	 * 添加某个运输方式的渠道
	 * @param int $id 运输方式ID
	 * @return array  
	 */
	public function actAdd(){
		$data			= array();
		$id				= isset($_GET['id']) ? intval($_GET['id']) : 0;//运输方式ID
		if (empty($id)) {
			show_message($this->smarty,"运输方式ID不能为空？","");	
			return false;
		}
		$data['id']		= $id;
		$data['lists']	= TransOpenApiModel::getCarrier(2);
        return $data;
    }
	
	/**
	 * ChannelManageAct::actModify()
	 * 返回某个渠道的信息
	 * @param int $id 查询ID
	 * @return array 
	 */
	public function actModify(){
		$data			= array();
		$id				= isset($_GET['id']) ? intval(trim($_GET['id'])) : 0;
		if (empty($id)) {
			show_message($this->smarty,"运输方式ID不能为空？","");	
			return false;
		}
		$data['id']		= $id;
		$data['lists']	= TransOpenApiModel::getCarrier(2);
		$data['res']	= ChannelManageModel::modModify($id);
		self::$errCode  = ChannelManageModel::$errCode;
        self::$errMsg   = ChannelManageModel::$errMsg;
		if (self::$errCode!=0) {
			show_message($this->smarty,self::$errMsg,"");	
			return false;
		}
        return $data;
    }

	/**
	 * ChannelManageAct::act_addChannelManage()
	 * 添加渠道
	 * @param int $ship_id 运输方式ID
	 * @param string $ch_name 渠道名称
	 * @param string $ch_alias 渠道别名
	 * @param string $ch_post 渠道收寄局名称
	 * @param float $ch_discount 渠道折扣
	 * @param int $ch_enabel 是否启用
	 * @param int $ch_time 时差
	 * @return  bool
	 */
	public function act_addChannelManage(){
        $ship_id	= isset($_POST["ship_id"]) ? abs(intval(post_check($_POST["ship_id"]))) : 0;
        $ch_name	= isset($_POST["ch_name"]) ? post_check($_POST["ch_name"]) : "";
        $ch_alias	= isset($_POST["ch_alias"]) ? post_check($_POST["ch_alias"]) : "";
        $ch_post	= isset($_POST["ch_post"]) ? post_check($_POST["ch_post"]) : "";
        $ch_post1	= isset($_POST["ch_post1"]) ? post_check($_POST["ch_post1"]) : "";
        $ch_post2	= isset($_POST["ch_post2"]) ? post_check($_POST["ch_post2"]) : "";
        $ch_discount= isset($_POST["ch_discount"]) ? abs(floatval(post_check($_POST["ch_discount"]))) : 0;
        $ch_enabel	= isset($_POST["ch_enabel"]) ? intval($_POST["ch_enabel"]) : 0;
        $ch_time	= isset($_POST["ch_time"]) ? intval($_POST["ch_time"]) : 0;
		$act		= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod		= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if	(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 10010;
			self::$errMsg   = "对不起,您无数据添加权限！";
			return false;
		}
		if (empty($ship_id) || !is_numeric($ship_id)) {
			self::$errCode  = 10000;
			self::$errMsg   = "运输方式ID参数有误！";
			return false;
		}
		if (empty($ch_name) || empty($ch_alias)) {
			self::$errCode  = 10002;
			self::$errMsg   = "渠道中文名称或英文名称有误！";
			return false;
		}
		if (!is_numeric($ch_discount)) {
			self::$errCode  = 10003;
			self::$errMsg   = "渠道折扣参数有误！";
			return false;
		}
		if (!in_array($ch_enabel,array("0","1"))) {
			self::$errCode  = 10004;
			self::$errMsg   = "是否启用参数有误！";
			return false;
		}
		$data  = array(
			"carrierId"		=> $ship_id,
			"channelName"	=> $ch_name,
			"channelAlias"	=> $ch_alias,
			"postName"		=> $ch_post,
			"postName1"		=> $ch_post1,
			"postName2"		=> $ch_post2,
			"discount"		=> $ch_discount,
			"is_enable"		=> $ch_enabel,
			"createdTime"	=> time(),
			"timeDiff"		=> $ch_time,
		);
        $res			= ChannelManageModel::addChannelManage($data);
		self::$errCode  = ChannelManageModel::$errCode;
        self::$errMsg   = ChannelManageModel::$errMsg;
		return $res;
    }

	/**
	 * ChannelManageAct::act_updateChannelManage()
	 * 修改渠道
	 * @param int $id 渠道ID
	 * @param int $ship_id 运输方式ID
	 * @param string $ch_name 渠道名称
	 * @param string $ch_alias 渠道别名
	 * @param string $ch_post 渠道收寄局名称
	 * @param float $ch_discount 渠道折扣
	 * @param int $ch_enabel 是否启用
	 * @return  bool
	 */
	public function act_updateChannelManage(){
		$id			= isset($_POST["id"]) ? intval(trim($_POST["id"])) : 0;
		$ship_id	= isset($_POST["ship_id"]) ? abs(intval(post_check($_POST["ship_id"]))) : 0;
        $ch_name	= isset($_POST["ch_name"]) ? post_check($_POST["ch_name"]) : "";
        $ch_alias	= isset($_POST["ch_alias"]) ? post_check($_POST["ch_alias"]) : "";
        $ch_post	= isset($_POST["ch_post"]) ? post_check($_POST["ch_post"]) : "";
        $ch_post1	= isset($_POST["ch_post1"]) ? post_check($_POST["ch_post1"]) : "";
        $ch_post2	= isset($_POST["ch_post2"]) ? post_check($_POST["ch_post2"]) : "";
        $ch_discount= isset($_POST["ch_discount"]) ? abs(floatval(post_check($_POST["ch_discount"]))) : 0;
        $ch_enabel	= isset($_POST["ch_enabel"]) ? intval($_POST["ch_enabel"]) : 0;
        $ch_time	= isset($_POST["ch_time"]) ? intval($_POST["ch_time"]) : 0;
		$act		= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod		= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if	(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 20005;
			self::$errMsg   = "对不起,您无数据编辑权限！";
			return false;
		}
		if (empty($id) || !is_numeric($id)) {
			self::$errCode  = 20000;
			self::$errMsg   = "渠道ID参数有误！";
			return false;
		}
		if (empty($ship_id) || !is_numeric($ship_id)) {
			self::$errCode  = 20001;
			self::$errMsg   = "运输方式ID参数有误！";
			return false;
		}
		if (empty($ch_name) || empty($ch_alias)) {
			self::$errCode  = 20002;
			self::$errMsg   = "渠道中文名称或英文名称有误！";
			return false;
		}
		if (!is_numeric($ch_discount)) {
			self::$errCode  = 20003;
			self::$errMsg   = "渠道折扣参数有误！";
			return false;
		}
		if (!in_array($ch_enabel,array("0","1"))) {
			self::$errCode  = 20004;
			self::$errMsg   = "是否启用参数有误！";
			return false;
		}
		$data  = array(
			"carrierId"		=> $ship_id,
			"channelName"	=> $ch_name,
			"channelAlias"	=> $ch_alias,
			"postName"		=> $ch_post,
			"postName1"		=> $ch_post1,
			"postName2"		=> $ch_post2,
			"discount"		=> $ch_discount,
			"is_enable"		=> $ch_enabel,
			"timeDiff"		=> $ch_time,
		);
        $res			= ChannelManageModel::updateChannelManage($id, $data);
		self::$errCode  = ChannelManageModel::$errCode;
        self::$errMsg   = ChannelManageModel::$errMsg;
		return $res;
    }
	
	/**
	 * ChannelManageAct::act_delChannelManage()
	 * 删除渠道
	 * @param int $id 渠道ID
	 * @return  bool
	 */
	public function act_delChannelManage(){
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
			self::$errMsg   = "渠道ID有误！";
			return false;
		}
        $res			= ChannelManageModel::delChannelManage($id);
		self::$errCode  = ChannelManageModel::$errCode;
        self::$errMsg   = ChannelManageModel::$errMsg;
		return $res;
    }
}
?>