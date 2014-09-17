<?php
/**
 * 类名：UserCompetencesAct
 * 功能：开放授权管理动作处理层
 * 版本：1.0
 * 日期：2014/04/19
 * 作者：管拥军
 */
  
class UserCompetencesAct {
    public static $errCode	= 0;
	public static $errMsg	= "";

	/**
	 * UserCompetencesAct::actIndex()
	 * 列出符合条件的数据并分页显示
	 * @param string $condition 查询条件
	 * @param integer $curpage 页码
	 * @param integer $pagenum 每页个数
	 * @return array 
	 */
 	public function actIndex(){
		$data			= array();
		$condition		= '';
		//接收参数生成条件
		$curpage		= isset($_GET['page']) ? abs(intval($_GET['page'])) : 1;
		$type			= isset($_GET['type']) ? trim($_GET['type']) : '';
		$key			= isset($_GET['key']) ? post_check(trim($_GET['key'])) : '';
		$condition		.= "1";
		if ($type && $key) {
			if (!in_array($type,array('title','content'))) redirect_to("index.php?mod=userCompetences&act=index");
			$condition	.= ' AND '.$type." = '".$key."'";
		}
		//获取符合条件的数据并分页
		$pagenum		= 20;
		$total			= UserCompetencesModel::modListCount($condition);
		$res			= UserCompetencesModel::modList($condition, $curpage, $pagenum);
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
		$data['key']	 = $key;
		$data['type']	 = $type;
		$data['lists']	 = $res;
		$data['pages']	 = $pageStr;
		self::$errCode   = UserCompetencesModel::$errCode;
        self::$errMsg    = UserCompetencesModel::$errMsg;
		if (self::$errCode!=0) {
			show_message($this->smarty,self::$errMsg,"");	
			return false;
		}
        return $data;
    }

	/**
	 * UserCompetencesAct::actAdd()
	 * 添加某个开放权限信息
	 * @return array  
	 */
	public function actAdd(){
		$data	= array();
		$data['lists']	= UserCompetencesModel::getCompetencesByCat();
        return $data;
    }
	
	/**
	 * UserCompetencesAct::actModify()
	 * 返回某个开放权限的信息
	 * @param int $id 查询ID
	 * @return array 
	 */
	public function actModify(){
		$data			= array();
		$id				= isset($_GET['id']) ? intval(trim($_GET['id'])) : 0;
		if (empty($id)) {
			show_message($this->smarty,"ID不能为空？","");	
			return false;
		}
		$data['id']		= $id;
		$data['lists']	= UserCompetencesModel::getCompetencesByCat();
		$data['res']	= UserCompetencesModel::modModify($id);
		self::$errCode  = UserCompetencesModel::$errCode;
        self::$errMsg   = UserCompetencesModel::$errMsg;
		if (self::$errCode!=0) {
			show_message($this->smarty,self::$errMsg,"");	
			return false;
		}
        return $data;
    }

	/**
	 * UserCompetencesAct::act_addUserCompetences()
	 * 添加开放权限信息
	 * @param string $ucp_title 开放权限名称
	 * @param string $ucp_item 开放权限键名
	 * @param string $ucp_content 键名内容
	 * @param int $ucp_pid 开放权限分类
	 * @return  bool
	 */
	public function act_addUserCompetences(){
        $title		= isset($_POST["ucp_title"]) ? post_check($_POST["ucp_title"]) : "";
        $item		= isset($_POST["ucp_item"]) ? post_check($_POST["ucp_item"]) : "";
        $content	= isset($_POST["ucp_content"]) ? post_check($_POST["ucp_content"]) : "";
        $pid		= isset($_POST["ucp_pid"]) ? post_check($_POST["ucp_pid"]) : "";
		$act		= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod		= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if	(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 10000;
			self::$errMsg   = "对不起,您无数据添加权限！";
			return false;
		}
		if (empty($title)) {
			self::$errCode  = 10001;
			self::$errMsg   = "开放权限名称有误！";
			return false;
		}
		if (empty($item)) {
			self::$errCode  = 10002;
			self::$errMsg   = "开放权限键名有误！";
			return false;
		}
		if (empty($content)) {
			self::$errCode  = 10003;
			self::$errMsg   = "开放权限内容有误！";
			return false;
		}
		if ($pid=="") {
			self::$errCode  = 10003;
			self::$errMsg   = "开放权限分类有误！";
			return false;
		}
		$uid				= $_SESSION[C('USER_AUTH_SYS_ID')];
		$pid				= explode("|",$pid);
		if ($pid[0]==0) {
			$level			= 1;
			$path			= 0;
		} else {
			$level			= count(explode("-",$pid[1]))+1;
			$path			= $pid[1].'-';
		}
		$data  = array(
			"title"			=> $title,
			"item"			=> $item,
			"content"		=> $content,
			"pid"			=> $pid[0],
			"level"			=> $level,
			"path"			=> $path,
			"addTime"		=> time(),
			"add_user_id"	=> $uid,
		);
        $res				= UserCompetencesModel::addUserCompetences($data);
		self::$errCode  	= UserCompetencesModel::$errCode;
        self::$errMsg   	= UserCompetencesModel::$errMsg;
		return $res;
    }

	/**
	 * UserCompetencesAct::act_updateUserCompetences()
	 * 修改开放权限信息
	 * @param string $ucp_title 开放权限名称
	 * @param string $ucp_item 开放权限键名
	 * @param string $ucp_content 键名内容
	 * @param int $ucp_pid 开放权限分类
	 * @return  bool
	 */
	public function act_updateUserCompetences(){
		$id			= isset($_POST["id"]) ? intval(trim($_POST["id"])) : 0;
		$title		= isset($_POST["ucp_title"]) ? post_check($_POST["ucp_title"]) : "";
        $item		= isset($_POST["ucp_item"]) ? post_check($_POST["ucp_item"]) : "";
        $content	= isset($_POST["ucp_content"]) ? post_check($_POST["ucp_content"]) : "";
        $pid		= isset($_POST["ucp_pid"]) ? post_check($_POST["ucp_pid"]) : "";
		$act		= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod		= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if	(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 20005;
			self::$errMsg   = "对不起,您无数据编辑权限！";
			return false;
		}
		if (empty($title)) {
			self::$errCode  = 10001;
			self::$errMsg   = "开放权限名称有误！";
			return false;
		}
		if (empty($item)) {
			self::$errCode  = 10002;
			self::$errMsg   = "开放权限键名有误！";
			return false;
		}
		if (empty($content)) {
			self::$errCode  = 10003;
			self::$errMsg   = "开放权限内容有误！";
			return false;
		}
		if ($pid=="") {
			self::$errCode  = 10004;
			self::$errMsg   = "开放权限分类有误！";
			return false;
		}
		$uid				= $_SESSION[C('USER_AUTH_SYS_ID')];
		$pid				= explode("|",$pid);
		if ($pid[0]==0) {
			$level			= 1;
			$path			= 0;
		} else {
			$level			= count(explode("-",$pid[1]))+1;
			$path			= $pid[1].'-';
		}
		$data  = array(
			"title"			=> $title,
			"item"			=> $item,
			"content"		=> $content,
			"pid"			=> $pid[0],
			"level"			=> $level,
			"path"			=> $path,
			"editTime"		=> time(),
			"edit_user_id"	=> $uid,
		);
        $res			= UserCompetencesModel::updateUserCompetences($id, $data);
		self::$errCode  = UserCompetencesModel::$errCode;
        self::$errMsg   = UserCompetencesModel::$errMsg;
		return $res;
    }
	
	/**
	 * UserCompetencesAct::act_delUserCompetences()
	 * 删除开放权限信息
	 * @param int $id 开放权限ID
	 * @return  bool
	 */
	public function act_delUserCompetences(){
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
			self::$errMsg   = "开放权限ID有误！";
			return false;
		}
        $res			= UserCompetencesModel::delUserCompetences($id);
		self::$errCode  = UserCompetencesModel::$errCode;
        self::$errMsg   = UserCompetencesModel::$errMsg;
		return $res;
    }
}
?>