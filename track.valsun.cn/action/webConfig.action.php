<?php
/**
 * 类名：WebConfigAct
 * 功能：网站后台配置管理动作处理层
 * 版本：1.0
 * 日期：2014/07/16
 * 作者：管拥军
 */
  
class WebConfigAct {
    public static $errCode	= 0;
	public static $errMsg	= "";

	/**
	 * WebConfigAct::actIndex()
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
		if($type && $key) {
			if(!in_array($type,array('cKey'))) redirect_to("index.php?mod=webConfig&act=index");
			$condition	.= ' AND '.$type." = '".$key."'";
		}
		//获取符合条件的数据并分页
		$pagenum		= 20;
		$total			= WebConfigModel::modListCount($condition);
		$res			= WebConfigModel::modList($condition, $curpage, $pagenum);
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
		self::$errCode   = WebConfigModel::$errCode;
        self::$errMsg    = WebConfigModel::$errMsg;
		if(self::$errCode != 0) {
			show_message($this->smarty,self::$errMsg,"");	
			exit;
		}
        return $data;
    }

	/**
	 * WebConfigAct::actAdd()
	 * 添加某个网站后台配置
	 * @return array  
	 */
	public function actAdd(){
		$data	= array();
        return $data;
    }
	
	/**
	 * WebConfigAct::actModify()
	 * 返回某个网站后台配置
	 * @param int $id 查询ID
	 * @return array 
	 */
	public function actModify(){
		$data			= array();
		$id				= isset($_GET['id']) ? intval(trim($_GET['id'])) : 0;
		if(empty($id)) {
			show_message($this->smarty,"ID不能为空？","");	
			exit;
		}
		$data['id']		= $id;
		$data['res']	= WebConfigModel::modModify($id);
		self::$errCode  = WebConfigModel::$errCode;
        self::$errMsg   = WebConfigModel::$errMsg;
		if(self::$errCode != 0) {
			show_message($this->smarty,self::$errMsg,"");	
			exit;
		}
        return $data;
    }

	/**
	 * WebConfigAct::act_addWebConfig()
	 * 添加网站后台配置
	 * @param string $cKey 名称
	 * @param string $cValue 内容
	 * @param string $is_enable 是否启用
	 * @return  bool
	 */
	public function act_addWebConfig(){
        $cKey				= isset($_POST["cKey"]) ? post_check($_POST["cKey"]) : "";
        $cValue				= isset($_POST["cValue"]) ? post_check($_POST["cValue"]) : "";
        $is_enable			= isset($_POST["is_enable"]) ? abs(intval($_POST["is_enable"])) : 0;
		$act				= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod				= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 10000;
			self::$errMsg   = "对不起,您无数据添加权限！";
			return false;
		}
		if(empty($cKey) || !(preg_match("/^([A-Z]+_?)*[A-Z]$/",$cKey))) {
			self::$errCode  = 10001;
			self::$errMsg   = "配置名称参数有误！";
			return false;
		}
		if(empty($cValue)) {
			self::$errCode  = 10002;
			self::$errMsg   = "配置内容参数有误！";
			return false;
		}
		if(!in_array($is_enable,array(0,1))) {
			self::$errCode  = 10003;
			self::$errMsg   = "是否启用参数有误！";
			return false;
		}
		$addTime			= time();
		$uid				= $_SESSION[C('USER_AUTH_SYS_ID')];
		$data  				= array(
								"cKey"				=> $cKey,
								"cValue"			=> $cValue,
								"is_enable"			=> $is_enable,
								"addTime"			=> $addTime,
								"add_user_id"		=> $uid,
							);
        $res				= WebConfigModel::addWebConfig($data);
		self::$errCode  	= WebConfigModel::$errCode;
        self::$errMsg   	= WebConfigModel::$errMsg;
		return $res;
    }

	/**
	 * WebConfigAct::act_updateWebConfig()
	 * 修改网站后台配置
	 * @param string $cKey 名称
	 * @param string $cValue 内容
	 * @param string $is_enable 是否启用
	 * @param int $id ID
	 * @return  bool
	 */
	public function act_updateWebConfig(){
		$id					= isset($_POST["id"]) ? abs(intval(trim($_POST["id"]))) : 0;
        $cKey				= isset($_POST["cKey"]) ? post_check($_POST["cKey"]) : "";
        $cValue				= isset($_POST["cValue"]) ? post_check($_POST["cValue"]) : "";
        $is_enable			= isset($_POST["is_enable"]) ? abs(intval($_POST["is_enable"])) : 0;
		$act				= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod				= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 20000;
			self::$errMsg   = "对不起,您无数据编辑权限！";
			return false;
		}
		if(empty($id) || !is_numeric($id)) {
			self::$errCode  = 10000;
			self::$errMsg   = "ID有误！";
			return false;
		}
		if(empty($cKey) || !(preg_match("/^([A-Z]+_?)*[A-Z]$/",$cKey))) {
			self::$errCode  = 10001;
			self::$errMsg   = "配置名称参数有误！";
			return false;
		}
		if(empty($cValue)) {
			self::$errCode  = 10002;
			self::$errMsg   = "配置内容参数有误！";
			return false;
		}
		if(!in_array($is_enable,array(0,1))) {
			self::$errCode  = 10003;
			self::$errMsg   = "是否启用参数有误！";
			return false;
		}
		$uid				= $_SESSION[C('USER_AUTH_SYS_ID')];
		$data  				= array(
								"cKey"				=> $cKey,
								"cValue"			=> $cValue,
								"is_enable"			=> $is_enable,
								"editTime"			=> time(),
								"edit_user_id"		=> $uid,
							);
        $res				= WebConfigModel::updateWebConfig($id, $data);
		self::$errCode  	= WebConfigModel::$errCode;
        self::$errMsg   	= WebConfigModel::$errMsg;
		return $res;
    }
	
	/**
	 * WebConfigAct::act_delWebConfig()
	 * 删除网站后台配置
	 * @param int $id ID
	 * @return  bool
	 */
	public function act_delWebConfig(){
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
			self::$errMsg   = "ID有误！";
			return false;
		}
        $res			= WebConfigModel::delWebConfig($id);
		self::$errCode  = WebConfigModel::$errCode;
        self::$errMsg   = WebConfigModel::$errMsg;
		return $res;
    }
}
?>