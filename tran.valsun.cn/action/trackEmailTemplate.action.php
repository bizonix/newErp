<?php
/**
 * 类名：TrackEmailTemplateAct
 * 功能：跟踪邮件模版动作处理层
 * 版本：1.0
 * 日期：2013/10/21
 * 作者：管拥军
 */
  
class TrackEmailTemplateAct {
    public static $errCode	= 0;
	public static $errMsg	= "";

	/**
	 * TrackEmailTemplateAct::actIndex()
	 * 列出符合条件的数据并分页显示
	 * @param string $condition 查询条件
	 * @param integer $curpage 页码
	 * @param integer $pagenum 每页个数
	 * @return array 
	 */
 	public function actIndex(){
		$data			= array();
		$condition		= '';
		$trackEmailTemplate		= new TrackEmailTemplateModel();
		//接收参数生成条件
		$curpage		= isset($_GET['page']) ? abs(intval($_GET['page'])) : 1;
		$type			= isset($_GET['type']) ? trim($_GET['type']) : '';
		$key			= isset($_GET['key']) ? post_check(trim($_GET['key'])) : '';
		$condition		.= "1";
		if($type && $key) {
			if(!in_array($type,array('platForm'))) redirect_to("index.php?mod=trackEmailTemplate&act=index");
			$condition	.= ' AND '.$type." = '".$key."'";
		}
		//获取符合条件的数据并分页
		$pagenum		= 20;
		$total			= $trackEmailTemplate->modListCount($condition);
		$res			= $trackEmailTemplate->modList($condition, $curpage, $pagenum);
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
		self::$errCode   = trackEmailTemplateModel::$errCode;
        self::$errMsg    = trackEmailTemplateModel::$errMsg;
		if(self::$errCode!=0) {
			show_message($this->smarty,self::$errMsg,"");	
			return false;
		}
        return $data;
    }
	
	/**
	 * TrackEmailTemplateAct::actAdd()
	 * 添加某个平台跟踪邮件模版的信息
	 * @return array 
	 */
	public function actAdd(){
		$data			= array();
		$data['lists']	= PlatFormModel::modList(1, 1, 200); //平台列表
		self::$errCode  = PlatFormModel::$errCode;
        self::$errMsg   = PlatFormModel::$errMsg;
		if(self::$errCode!=0) {
			show_message($this->smarty,self::$errMsg,"");	
			return false;
		}
        return $data;
    }

	/**
	 * TrackEmailTemplateAct::actModify()
	 * 返回某个跟踪邮件模版的信息
	 * @param int $id 查询ID
	 * @return array 
	 */
	public function actModify(){
		$data			= array();
		$id				= isset($_GET['id']) ? intval(trim($_GET['id'])) : 0;
		if(empty($id)) {
			show_message($this->smarty,"跟踪邮件ID不能为空？","");	
			return false;
		}
		$data['id']		= $id;
		$data['lists']	= PlatFormModel::modList(1, 1, 200); //平台列表
		$data['res']	= TrackEmailTemplateModel::modModify($id);
		self::$errCode  = TrackEmailTemplateModel::$errCode;
        self::$errMsg   = TrackEmailTemplateModel::$errMsg;
		if(self::$errCode!=0) {
			show_message($this->smarty,self::$errMsg,"");	
			return false;
		}
        return $data;
    }

	/**
	 * TrackEmailTemplateAct::act_addTrackEmailTemplate()
	 * 添加跟踪邮件模版
	 * @param int $temp_plat 平台名称
	 * @param string $temp_name 跟踪邮件模版名称
	 * @param string $temp_title 跟踪邮件模版抬头
	 * @param string $temp_content 跟踪邮件模版内容
	 * @return  bool
	 */
	public function act_addTrackEmailTemplate(){
        $temp_plat		= isset($_POST["temp_plat"]) ? post_check($_POST["temp_plat"]) : "";
        $temp_name		= isset($_POST["temp_name"]) ? post_check($_POST["temp_name"]) : "";
        $temp_title		= isset($_POST["temp_title"]) ? post_check_no($_POST["temp_title"]) : "";
        $temp_content	= isset($_POST["temp_content"]) ? post_check_no($_POST["temp_content"]) : "";
		$act			= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod			= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 10010;
			self::$errMsg   = "对不起,您无数据添加权限！";
			return false;
		}
		if(empty($temp_plat)) {
			self::$errCode  = 10000;
			self::$errMsg   = "平台名称参数有误！";
			return false;
		}
		if(empty($temp_name)) {
			self::$errCode  = 10002;
			self::$errMsg   = "邮件模版名称有误！";
			return false;
		}
		if(empty($temp_title)) {
			self::$errCode  = 10003;
			self::$errMsg   = "邮件模版抬头有误！";
			return false;
		}
		if(empty($temp_content)) {
			self::$errCode  = 10004;
			self::$errMsg   = "邮件模版内容有误！";
			return false;
		}
		$uid				= $_SESSION[C('USER_AUTH_SYS_ID')];
		$data  = array(
			"platForm"		=> $temp_plat,
			"tempName"		=> $temp_name,
			"title"			=> $temp_title,
			"content"		=> $temp_content,
			"add_user_id"	=> $uid,
			"addTime"		=> time(),
		);
        $res			= TrackEmailTemplateModel::addTrackEmailTemplate($data);
		self::$errCode  = TrackEmailTemplateModel::$errCode;
        self::$errMsg   = TrackEmailTemplateModel::$errMsg;
		return $res;
    }

	/**
	 * TrackEmailTemplateAct::act_updateTrackEmailTemplate()
	 * 修改跟踪邮件模版
	 * @param int $id 跟踪邮件模版ID
	 * @param int $temp_plat 平台名称
	 * @param string $temp_name 跟踪邮件模版名称
	 * @param string $temp_title 跟踪邮件模版抬头
	 * @param string $temp_content 跟踪邮件模版内容
	 * @return  bool
	 */
	public function act_updateTrackEmailTemplate(){
		$id				= isset($_POST["id"]) ? intval(trim($_POST["id"])) : 0;
		$temp_plat		= isset($_POST["temp_plat"]) ? post_check($_POST["temp_plat"]) : "";
        $temp_name		= isset($_POST["temp_name"]) ? post_check($_POST["temp_name"]) : "";
        $temp_title		= isset($_POST["temp_title"]) ? post_check_no($_POST["temp_title"]) : "";
        $temp_content	= isset($_POST["temp_content"]) ? post_check_no($_POST["temp_content"]) : "";
		$act			= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod			= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 10010;
			self::$errMsg   = "对不起,您无数据添加权限！";
			return false;
		}
		if(empty($id)) {
			self::$errCode  = 10000;
			self::$errMsg   = "邮件模版ID参数有误！";
			return false;
		}
		if(empty($temp_plat)) {
			self::$errCode  = 10000;
			self::$errMsg   = "平台名称参数有误！";
			return false;
		}
		if(empty($temp_name)) {
			self::$errCode  = 10002;
			self::$errMsg   = "邮件模版名称有误！";
			return false;
		}
		if(empty($temp_title)) {
			self::$errCode  = 10003;
			self::$errMsg   = "邮件模版抬头有误！";
			return false;
		}
		if(empty($temp_content)) {
			self::$errCode  = 10004;
			self::$errMsg   = "邮件模版内容有误！";
			return false;
		}
		$uid				= $_SESSION[C('USER_AUTH_SYS_ID')];
		$data  = array(
			"platForm"		=> $temp_plat,
			"tempName"		=> $temp_name,
			"title"			=> $temp_title,
			"content"		=> $temp_content,
			"edit_user_id"	=> $uid,
			"editTime"		=> time(),
		);
        $res			= TrackEmailTemplateModel::updateTrackEmailTemplate($id, $data);
		self::$errCode  = TrackEmailTemplateModel::$errCode;
        self::$errMsg   = TrackEmailTemplateModel::$errMsg;
		return $res;
    }
	
	/**
	 * TrackEmailTemplateAct::act_delTrackEmailTemplate()
	 * 删除跟踪邮件模版
	 * @param int $id 跟踪邮件模版ID
	 * @return  bool
	 */
	public function act_delTrackEmailTemplate(){
		$id			= isset($_POST["id"]) ? intval(trim($_POST["id"])) : 0;
		$act		= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod		= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 30001;
			self::$errMsg   = "对不起,您无数据删除权限！";
			return false;
		}
		if(empty($id) || !is_numeric($id)) {
			self::$errCode  = 30000;
			self::$errMsg   = "跟踪邮件模版ID有误！";
			return false;
		}
        $res			= TrackEmailTemplateModel::delTrackEmailTemplate($id);
		self::$errCode  = TrackEmailTemplateModel::$errCode;
        self::$errMsg   = TrackEmailTemplateModel::$errMsg;
		return $res;
    }	
}
?>