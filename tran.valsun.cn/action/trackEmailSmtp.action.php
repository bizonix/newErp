<?php
/**
 * 类名：TrackEmailSmtpAct
 * 功能：邮件服务配置动作处理层
 * 版本：1.0
 * 日期：2014/04/10
 * 作者：管拥军
 */
  
class TrackEmailSmtpAct {
    public static $errCode	= 0;
	public static $errMsg	= "";

	/**
	 * TrackEmailSmtpAct::actIndex()
	 * 列出符合条件的数据并分页显示
	 * @param string $condition 查询条件
	 * @param integer $curpage 页码
	 * @param integer $pagenum 每页个数
	 * @return array 
	 */
 	public function actIndex(){
		$data			= array();
		$condition		= '';
		$trackEmailSmtp		= new trackEmailSmtpModel();
		//接收参数生成条件
		$curpage		= isset($_GET['page']) ? abs(intval($_GET['page'])) : 1;
		$type			= isset($_GET['type']) ? trim($_GET['type']) : '';
		$key			= isset($_GET['key']) ? post_check(trim($_GET['key'])) : '';
		$condition		.= "1";
		if($type && $key) {
			if(!in_array($type,array('platForm'))) redirect_to("index.php?mod=trackEmailSmtp&act=index");
			$condition	.= ' AND '.$type." = '".$key."'";
		}
		//获取符合条件的数据并分页
		$pagenum		= 20;
		$total			= $trackEmailSmtp->modListCount($condition);
		$res			= $trackEmailSmtp->modList($condition, $curpage, $pagenum);
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
		self::$errCode   = TrackEmailSmtpModel::$errCode;
        self::$errMsg    = TrackEmailSmtpModel::$errMsg;
		if(self::$errCode!=0) {
			show_message($this->smarty,self::$errMsg,"");	
			return false;
		}
        return $data;
    }
	
	/**
	 * TrackEmailSmtpAct::actAdd()
	 * 添加某个平台邮件服务配置的信息
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
	 * TrackEmailSmtpAct::actModify()
	 * 返回某个邮件服务配置的信息
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
		$data['res']	= TrackEmailSmtpModel::modModify($id);
		self::$errCode  = TrackEmailSmtpModel::$errCode;
        self::$errMsg   = TrackEmailSmtpModel::$errMsg;
		if(self::$errCode!=0) {
			show_message($this->smarty,self::$errMsg,"");	
			return false;
		}
        return $data;
    }

	/**
	 * TrackEmailSmtpAct::act_addTrackEmailSmtp()
	 * 添加邮件服务配置
	 * @param int $smtp_plat 平台名称
	 * @param string $smtp_count 平台帐号
	 * @param string $smtp_user_name 服务帐号
	 * @param string $smtp_user_pwd 服务密码
	 * @param string $smtp_host 服务地址
	 * @param string $smtp_port 服务端口
	 * @return  bool
	 */
	public function act_addTrackEmailSmtp(){
        $smtp_plat		= isset($_POST["smtp_plat"]) ? post_check($_POST["smtp_plat"]) : "";
        $smtp_count		= isset($_POST["smtp_count"]) ? post_check($_POST["smtp_count"]) : "";
        $smtp_user_name	= isset($_POST["smtp_user_name"]) ? post_check($_POST["smtp_user_name"]) : "";
        $smtp_user_pwd	= isset($_POST["smtp_user_pwd"]) ? post_check($_POST["smtp_user_pwd"]) : "";
        $smtp_host		= isset($_POST["smtp_host"]) ? post_check($_POST["smtp_host"]) : "";
        $smtp_port		= isset($_POST["smtp_port"]) ? post_check($_POST["smtp_port"]) : "";
		$act			= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod			= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 10010;
			self::$errMsg   = "对不起,您无数据添加权限！";
			return false;
		}
		if(empty($smtp_plat)) {
			self::$errCode  = 10000;
			self::$errMsg   = "平台名称参数有误！";
			return false;
		}
		if(empty($smtp_count)) {
			self::$errCode  = 10001;
			self::$errMsg   = "平台帐号有误！";
			return false;
		}
		if(empty($smtp_host)) {
			self::$errCode  = 10002;
			self::$errMsg   = "服务地址参数有误！";
			return false;
		}
		if(empty($smtp_port)) {
			self::$errCode  = 10003;
			self::$errMsg   = "服务端口有误！";
			return false;
		}
		if(empty($smtp_user_name)) {
			self::$errCode  = 10004;
			self::$errMsg   = "服务帐号有误！";
			return false;
		}
		if(empty($smtp_user_pwd)) {
			self::$errCode  = 10005;
			self::$errMsg   = "服务密码有误！";
			return false;
		}
		$uid				= $_SESSION[C('USER_AUTH_SYS_ID')];
		$data  = array(
			"platForm"		=> $smtp_plat,
			"platAccount"	=> $smtp_count,
			"smtpUser"		=> $smtp_user_name,
			"smtpPwd"		=> $smtp_user_pwd,
			"smtpHost"		=> $smtp_host,
			"smtpPort"		=> $smtp_port,
			"add_user_id"	=> $uid,
			"addTime"		=> time(),
		);
        $res			= TrackEmailSmtpModel::addTrackEmailSmtp($data);
		self::$errCode  = TrackEmailSmtpModel::$errCode;
        self::$errMsg   = TrackEmailSmtpModel::$errMsg;
		return $res;
    }

	/**
	 * TrackEmailSmtpAct::act_updateTrackEmailSmtp()
	 * 修改邮件服务配置
	 * @param int $id 邮件服务配置ID
	 * @param int $smtp_plat 平台名称
	 * @param string $smtp_count 平台帐号
	 * @param string $smtp_user_name 服务帐号
	 * @param string $smtp_user_pwd 服务密码
	 * @param string $smtp_host 服务地址
	 * @param string $smtp_port 服务端口
	 * @return  bool
	 */
	public function act_updateTrackEmailSmtp(){
		$id				= isset($_POST["id"]) ? intval(trim($_POST["id"])) : 0;
        $smtp_plat		= isset($_POST["smtp_plat"]) ? post_check($_POST["smtp_plat"]) : "";
        $smtp_count		= isset($_POST["smtp_count"]) ? post_check($_POST["smtp_count"]) : "";
        $smtp_user_name	= isset($_POST["smtp_user_name"]) ? post_check($_POST["smtp_user_name"]) : "";
        $smtp_user_pwd	= isset($_POST["smtp_user_pwd"]) ? post_check($_POST["smtp_user_pwd"]) : "";
        $smtp_host		= isset($_POST["smtp_host"]) ? post_check($_POST["smtp_host"]) : "";
        $smtp_port		= isset($_POST["smtp_port"]) ? post_check($_POST["smtp_port"]) : "";
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
		if(empty($smtp_plat)) {
			self::$errCode  = 10001;
			self::$errMsg   = "平台名称参数有误！";
			return false;
		}
		if(empty($smtp_count)) {
			self::$errCode  = 10002;
			self::$errMsg   = "平台帐号有误！";
			return false;
		}
		if(empty($smtp_host)) {
			self::$errCode  = 10003;
			self::$errMsg   = "服务地址参数有误！";
			return false;
		}
		if(empty($smtp_port)) {
			self::$errCode  = 10004;
			self::$errMsg   = "服务端口有误！";
			return false;
		}
		if(empty($smtp_user_name)) {
			self::$errCode  = 10005;
			self::$errMsg   = "服务帐号有误！";
			return false;
		}
		if(empty($smtp_user_pwd)) {
			self::$errCode  = 10006;
			self::$errMsg   = "服务密码有误！";
			return false;
		}
		$uid				= $_SESSION[C('USER_AUTH_SYS_ID')];
		$data  = array(
			"platForm"		=> $smtp_plat,
			"platAccount"	=> $smtp_count,
			"smtpUser"		=> $smtp_user_name,
			"smtpPwd"		=> $smtp_user_pwd,
			"smtpHost"		=> $smtp_host,
			"smtpPort"		=> $smtp_port,
			"edit_user_id"	=> $uid,
			"editTime"		=> time(),
		);
        $res			= TrackEmailSmtpModel::updateTrackEmailSmtp($id, $data);
		self::$errCode  = TrackEmailSmtpModel::$errCode;
        self::$errMsg   = TrackEmailSmtpModel::$errMsg;
		return $res;
    }
	
	/**
	 * TrackEmailSmtpAct::act_delTrackEmailSmtp()
	 * 删除邮件服务配置
	 * @param int $id 邮件服务配置ID
	 * @return  bool
	 */
	public function act_delTrackEmailSmtp(){
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
			self::$errMsg   = "邮件服务配置ID有误！";
			return false;
		}
        $res			= TrackEmailSmtpModel::delTrackEmailSmtp($id);
		self::$errCode  = TrackEmailSmtpModel::$errCode;
        self::$errMsg   = TrackEmailSmtpModel::$errMsg;
		return $res;
    }	
}
?>