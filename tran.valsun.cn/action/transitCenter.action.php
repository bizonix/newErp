<?php
/**
 * 类名：TransitCenterAct
 * 功能：转运中心管理动作处理层
 * 版本：1.0
 * 日期：2014/05/28
 * 作者：管拥军
 */
  
class TransitCenterAct {
    public static $errCode	= 0;
	public static $errMsg	= "";

	/**
	 * TransitCenterAct::actIndex()
	 * 列出符合条件的数据并分页显示
	 * @param string $condition 查询条件
	 * @param integer $curpage 页码
	 * @param integer $pagenum 每页个数
	 * @return array 
	 */
 	public function actIndex(){
		$data				= array();
		$condition			= '';
		$transitCenter		= new TransitCenterModel();
		//接收参数生成条件
		$curpage			= isset($_GET['page']) ? abs(intval($_GET['page'])) : 1;
		$type				= isset($_GET['type']) ? trim($_GET['type']) : '';
		$key				= isset($_GET['key']) ? post_check(trim($_GET['key'])) : '';
		$condition			.= "1";
		if($type && $key) {
			if(!in_array($type,array('cn_title','en_title'))) redirect_to("index.php?mod=transitCenter&act=index");
			$condition		.= ' AND '.$type." = '".$key."'";
		}
		//获取符合条件的数据并分页
		$pagenum			= 20;
		$total				= $transitCenter->modListCount($condition);
		$res				= $transitCenter->modList($condition, $curpage, $pagenum);
		$page	 			= new Page($total, $pagenum, '', 'CN');
		$pageStr			= "";
		if($res) {
			if($total>$pagenum) {
				$pageStr 	= $page->fpage(array(0,1,2,3,4,5,6,7,8,9));
			} else {
				$pageStr 	= $page->fpage(array(0,1,2,3));
			}
		} else {
			$pageStr 	 	= '暂无数据';
		}		
		//封装数据返回
		$data['key']	 	= $key;
		$data['type']	 	= $type;
		$data['lists']	 	= $res;
		$data['pages']	 	= $pageStr;
		self::$errCode   	= TransitCenterModel::$errCode;
        self::$errMsg    	= TransitCenterModel::$errMsg;
		if(self::$errCode!=0) {
			show_message($this->smarty,self::$errMsg,"");	
			return false;
		}
        return $data;
    }

	/**
	 * TransitCenterAct::actModify()
	 * 返回某个转运中心
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
		$data['res']	= TransitCenterModel::modModify($id);
		if(empty($data['res'])) {
			show_message($this->smarty,"数据为空，请返回确认条件!","");	
			return false;
		}
		self::$errCode  = TransitCenterModel::$errCode;
        self::$errMsg   = TransitCenterModel::$errMsg;
		if(self::$errCode!=0) {
			show_message($this->smarty,self::$errMsg,"");	
			return false;
		}
        return $data;
    }	

	/**
	 * TransitCenterAct::act_addTransitCenter()
	 * 添加转运中心
	 * @param string $cn_name 转运中心中文名
	 * @param string $en_name 转运中心英文名
	 * @return  bool
	 */
	public function act_addTransitCenter(){
        $cn_name	= isset($_POST["cn_name"]) ? post_check($_POST["cn_name"]) : "";
        $en_name	= isset($_POST["en_name"]) ? post_check($_POST["en_name"]) : "";
		$act		= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod		= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 10000;
			self::$errMsg   = "对不起,您无数据添加权限！";
			return false;
		}
		if(empty($cn_name)) {
			self::$errCode  = 10001;
			self::$errMsg   = "中文名称有误！";
			return false;
		}
		if(empty($en_name)) {
			self::$errCode  = 10002;
			self::$errMsg   = "英文名称有误！";
			return false;
		}
		$uid				= $_SESSION[C('USER_AUTH_SYS_ID')];
		$data  				= array(
								"cn_title"			=> $cn_name,
								"en_title"			=> $en_name,
								"add_user_id"	=> $uid,
								"addTime"		=> time(),
							);
        $res				= TransitCenterModel::addTransitCenter($data);
		self::$errCode  	= TransitCenterModel::$errCode;
        self::$errMsg   	= TransitCenterModel::$errMsg;
		return $res;
    }

	/**
	 * TransitCenterAct::act_updateTransitCenter()
	 * 修改转运中心
	 * @param string $cn_name 转运中心中文名
	 * @param string $en_name 转运中心英文名
	 * @return  bool
	 */
	public function act_updateTransitCenter(){
		$id			= isset($_POST["id"]) ? intval(trim($_POST["id"])) : 0;
		$cn_name	= isset($_POST["cn_name"]) ? post_check($_POST["cn_name"]) : "";
		$en_name	= isset($_POST["en_name"]) ? post_check($_POST["en_name"]) : "";
		$act		= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod		= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 10000;
			self::$errMsg   = "对不起,您无数据编辑权限！";
			return false;
		}
		if(empty($id) || !is_numeric($id)) {
			self::$errCode  = 10001;
			self::$errMsg   = "转运中心ID有误！";
			return false;
		}
		if(empty($cn_name)) {
			self::$errCode  = 10002;
			self::$errMsg   = "中文名称有误！";
			return false;
		}
		if(empty($en_name)) {
			self::$errCode  = 10003;
			self::$errMsg   = "英文名称有误！";
			return false;
		}
		$uid				= $_SESSION[C('USER_AUTH_SYS_ID')];
		$data  				= array(
								"cn_title"		=> $cn_name,
								"en_title"		=> $en_name,
								"edit_user_id"	=> $uid,
								"editTime"		=> time(),
							);
        $res				= TransitCenterModel::updateTransitCenter($id, $data);
		self::$errCode 	 	= TransitCenterModel::$errCode;
        self::$errMsg  	 	= TransitCenterModel::$errMsg;
		return $res;
    }
	
	/**
	 * TransitCenterAct::act_delTransitCenter()
	 * 删除转运中心
	 * @param int $id 转运中心ID
	 * @return  bool
	 */
	public function act_delTransitCenter(){
		$id			= isset($_POST["id"]) ? intval(trim($_POST["id"])) : 0;
		$act		= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod		= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 10001;
			self::$errMsg   = "对不起,您无数据删除权限！";
			return false;
		}
		if(empty($id) || !is_numeric($id)) {
			self::$errCode  = 10000;
			self::$errMsg   = "转运中心ID有误！";
			return false;
		}
        $res				= TransitCenterModel::delTransitCenter($id);
		self::$errCode  	= TransitCenterModel::$errCode;
        self::$errMsg   	= TransitCenterModel::$errMsg;
		return $res;
    }
}
?>