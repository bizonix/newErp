<?php
/**
 * 类名：TrackEmailAccountAct
 * 功能：客服邮件帐号动作处理层
 * 版本：1.0
 * 日期：2014/04/10
 * 作者：管拥军
 */
  
class TrackEmailAccountAct {
    public static $errCode	= 0;
	public static $errMsg	= "";

	/**
	 * TrackEmailAccountAct::actIndex()
	 * 列出符合条件的数据并分页显示
	 * @param string $condition 查询条件
	 * @param integer $curpage 页码
	 * @param integer $pagenum 每页个数
	 * @return array 
	 */
 	public function actIndex(){
		$data			= array();
		$condition		= '';
		$TrackEmailAccount		= new TrackEmailAccountModel();
		//接收参数生成条件
		$curpage		= isset($_GET['page']) ? abs(intval($_GET['page'])) : 1;
		$type			= isset($_GET['type']) ? trim($_GET['type']) : '';
		$key			= isset($_GET['key']) ? post_check(trim($_GET['key'])) : '';
		$condition		.= "1";
		if($type && $key) {
			if(!in_array($type,array('platForm','platAccount'))) redirect_to("index.php?mod=TrackEmailAccount&act=index");
			$condition	.= ' AND '.$type." = '".$key."'";
		}
		//获取符合条件的数据并分页
		$pagenum		= 20;
		$total			= $TrackEmailAccount->modListCount($condition);
		$res			= $TrackEmailAccount->modList($condition, $curpage, $pagenum);
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
		self::$errCode   = TrackEmailAccountModel::$errCode;
        self::$errMsg    = TrackEmailAccountModel::$errMsg;
		if(self::$errCode!=0) {
			show_message($this->smarty,self::$errMsg,"");	
			return false;
		}
        return $data;
    }
	
	/**
	 * TrackEmailAccountAct::actAdd()
	 * 添加某个平台客服邮件帐号的信息
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
	 * TrackEmailAccountAct::actModify()
	 * 返回某个客服邮件帐号的信息
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
		$data['res']	= TrackEmailAccountModel::modModify($id);
		self::$errCode  = TrackEmailAccountModel::$errCode;
        self::$errMsg   = TrackEmailAccountModel::$errMsg;
		if(self::$errCode!=0) {
			show_message($this->smarty,self::$errMsg,"");	
			return false;
		}
        return $data;
    }

	/**
	 * TrackEmailAccountAct::act_addTrackEmailAccount()
	 * 添加客服邮件帐号
	 * @param int $acc_plat 平台名称
	 * @param string $acc_count 平台帐号
	 * @param string $acc_user_name 客服姓名
	 * @param string $acc_user_email 客服邮箱
	 * @return  bool
	 */
	public function act_addTrackEmailAccount(){
        $acc_plat		= isset($_POST["acc_plat"]) ? post_check($_POST["acc_plat"]) : "";
        $acc_count		= isset($_POST["acc_count"]) ? post_check($_POST["acc_count"]) : "";
        $acc_user_name	= isset($_POST["acc_user_name"]) ? post_check($_POST["acc_user_name"]) : "";
        $acc_user_email	= isset($_POST["acc_user_email"]) ? post_check($_POST["acc_user_email"]) : "";
		$act			= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod			= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 10010;
			self::$errMsg   = "对不起,您无数据添加权限！";
			return false;
		}
		if(empty($acc_plat)) {
			self::$errCode  = 10000;
			self::$errMsg   = "平台名称参数有误！";
			return false;
		}
		if(empty($acc_count)) {
			self::$errCode  = 10002;
			self::$errMsg   = "平台帐号有误！";
			return false;
		}
		if(empty($acc_user_name)) {
			self::$errCode  = 10003;
			self::$errMsg   = "客服姓名有误！";
			return false;
		}
		if(empty($acc_user_email)) {
			self::$errCode  = 10004;
			self::$errMsg   = "客服邮箱有误！";
			return false;
		}
		$uid				= $_SESSION[C('USER_AUTH_SYS_ID')];
		$data  = array(
			"platForm"		=> $acc_plat,
			"platAccount"	=> $acc_count,
			"userName"		=> $acc_user_name,
			"userEmail"		=> $acc_user_email,
			"add_user_id"	=> $uid,
			"addTime"		=> time(),
		);
        $res			= TrackEmailAccountModel::addTrackEmailAccount($data);
		self::$errCode  = TrackEmailAccountModel::$errCode;
        self::$errMsg   = TrackEmailAccountModel::$errMsg;
		return $res;
    }

	/**
	 * TrackEmailAccountAct::act_updateTrackEmailAccount()
	 * 修改客服邮件帐号
	 * @param int $id 客服邮件帐号ID
	 * @param int $acc_plat 平台名称
	 * @param string $acc_count 平台帐号
	 * @param string $acc_user_name 客服姓名
	 * @param string $acc_user_email 客服邮箱
	 * @return  bool
	 */
	public function act_updateTrackEmailAccount(){
		$id				= isset($_POST["id"]) ? intval(trim($_POST["id"])) : 0;
		$acc_plat		= isset($_POST["acc_plat"]) ? post_check($_POST["acc_plat"]) : "";
        $acc_count		= isset($_POST["acc_count"]) ? post_check($_POST["acc_count"]) : "";
        $acc_user_name	= isset($_POST["acc_user_name"]) ? post_check($_POST["acc_user_name"]) : "";
        $acc_user_email	= isset($_POST["acc_user_email"]) ? post_check($_POST["acc_user_email"]) : "";
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
		if(empty($acc_plat)) {
			self::$errCode  = 10000;
			self::$errMsg   = "平台名称参数有误！";
			return false;
		}
		if(empty($acc_count)) {
			self::$errCode  = 10002;
			self::$errMsg   = "平台帐号有误！";
			return false;
		}
		if(empty($acc_user_name)) {
			self::$errCode  = 10003;
			self::$errMsg   = "客服姓名有误！";
			return false;
		}
		if(empty($acc_user_email)) {
			self::$errCode  = 10004;
			self::$errMsg   = "客服邮箱有误！";
			return false;
		}
		$uid				= $_SESSION[C('USER_AUTH_SYS_ID')];
		$data  = array(
			"platForm"		=> $acc_plat,
			"platAccount"	=> $acc_count,
			"userName"		=> $acc_user_name,
			"userEmail"		=> $acc_user_email,
			"edit_user_id"	=> $uid,
			"editTime"		=> time(),
		);
        $res			= TrackEmailAccountModel::updateTrackEmailAccount($id, $data);
		self::$errCode  = TrackEmailAccountModel::$errCode;
        self::$errMsg   = TrackEmailAccountModel::$errMsg;
		return $res;
    }
	
	/**
	 * TrackEmailAccountAct::act_delTrackEmailAccount()
	 * 删除客服邮件帐号
	 * @param int $id 客服邮件帐号ID
	 * @return  bool
	 */
	public function act_delTrackEmailAccount(){
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
			self::$errMsg   = "客服邮件帐号ID有误！";
			return false;
		}
        $res			= TrackEmailAccountModel::delTrackEmailAccount($id);
		self::$errCode  = TrackEmailAccountModel::$errCode;
        self::$errMsg   = TrackEmailAccountModel::$errMsg;
		return $res;
    }	
}
?>