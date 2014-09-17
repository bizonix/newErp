<?php
/**
 * 类名：ApiCompetenceAct
 * 功能：API开放授权管理动作处理层
 * 版本：1.0
 * 日期：2014/07/10
 * 作者：管拥军
 */
  
class ApiCompetenceAct {
    public static $errCode	= 0;
	public static $errMsg	= "";

	/**
	 * ApiCompetenceAct::actIndex()
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
			if(!in_array($type,array('apiName'))) redirect_to("index.php?mod=apiCompetence&act=index");
			$condition	.= ' AND '.$type." = '".$key."'";
		}
		//获取符合条件的数据并分页
		$pagenum		= 20;
		$total			= ApiCompetenceModel::modListCount($condition);
		$res			= ApiCompetenceModel::modList($condition, $curpage, $pagenum);
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
		self::$errCode   = ApiCompetenceModel::$errCode;
        self::$errMsg    = ApiCompetenceModel::$errMsg;
		if(self::$errCode!=0) {
			show_message($this->smarty,self::$errMsg,"");	
			return false;
		}
        return $data;
    }

	/**
	 * ApiCompetenceAct::actAdd()
	 * 添加某个API开放授权
	 * @return array  
	 */
	public function actAdd(){
		$data			= array();
		$data['gids']	= ApiCompetenceModel::getGlobalUser();
		$data['lists']	= TransOpenApiModel::getCarrierOpenList();
		if(self::$errCode != 0) {
			show_message($this->smarty,self::$errMsg,"");	
			return false;
		}
        return $data;
    }
	
	/**
	 * ApiCompetenceAct::actModify()
	 * 返回某个API开放授权
	 * @param int $id 查询ID
	 * @return array 
	 */
	public function actModify(){
		$data			= array();
		$id				= isset($_GET['id']) ? intval(trim($_GET['id'])) : 0;
		if(empty($id)) {
			show_message($this->smarty,"API开放授权ID不能为空？","");	
			return false;
		}
		$data['id']		= $id;
		$data['gids']	= ApiCompetenceModel::getGlobalUser();
		$data['lists']	= TransOpenApiModel::getCarrierOpenList();
		$data['res']	= ApiCompetenceModel::modModify($id);
		self::$errCode  = ApiCompetenceModel::$errCode;
        self::$errMsg   = ApiCompetenceModel::$errMsg;
		if(self::$errCode != 0) {
			show_message($this->smarty,self::$errMsg,"");	
			return false;
		}
        return $data;
    }

	/**
	 * ApiCompetenceAct::act_addApiCompetence()
	 * 添加API开放授权
	 * @param string $apiName API名称
	 * @param string $apiValue 授权内容
	 * @param string $apiMaxCount 当天调用次数
	 * @param string $apiEnable 是否启用
	 * @param int $apiUid 用户GID
	 * @return  bool
	 */
	public function act_addApiCompetence(){
        $apiUid				= isset($_POST["apiUid"]) ? abs(intval($_POST["apiUid"])) : 0;
        $apiName			= isset($_POST["apiName"]) ? post_check($_POST["apiName"]) : "";
        $apiArr				= isset($_POST["apiValue"]) ? $_POST["apiValue"] : "";
        $apiMaxCount		= isset($_POST["apiMaxCount"]) ? abs(intval($_POST["apiMaxCount"])) : 0;
        $apiEnable			= isset($_POST["apiEnable"]) ? abs(intval($_POST["apiEnable"])) : 0;
		$apiValue			= "";
		$act				= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod				= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 10000;
			self::$errMsg   = "对不起,您无数据添加权限！";
			return false;
		}
		if(empty($apiUid)) {
			self::$errCode  = 10001;
			self::$errMsg   = "API开放授权UID有误！";
			return false;
		}
		if(empty($apiName) || !(preg_match("/^([A-Za-z]+_?)*[A-Za-z]$/",$apiName))) {
			self::$errCode  = 10002;
			self::$errMsg   = "API开放授权接口名有误！";
			return false;
		}
		if(empty($apiArr)) {
			self::$errCode  = 10003;
			self::$errMsg   = "API开放授权内容参数有误！";
			return false;
		} else {
			$apiValue		= implode(",", $apiArr);
		}
		if(empty($apiValue) || !(preg_match("/^([\d]\,?)*[\d]$/",$apiValue))) {
			self::$errCode  = 10003;
			self::$errMsg   = "API开放授权内容格式有误！";
			return false;
		}
		if(!is_numeric($apiMaxCount)) {
			self::$errCode  = 10004;
			self::$errMsg   = "调用次数参数有误！";
			return false;
		}
		if(!in_array($apiEnable,array(0,1))) {
			self::$errCode  = 10005;
			self::$errMsg   = "是否启用参数有误！";
			return false;
		}
		$addTime			= time();
		$apiTokenExpire		= $addTime + 86400 * 365;
		$apiToken			= md5($apiUid.$apiName.$apiUid.'_trans');
		$uid				= $_SESSION[C('USER_AUTH_SYS_ID')];
		$data  				= array(
								"apiUid"			=> $apiUid,
								"apiName"			=> $apiName,
								"apiValue"			=> $apiValue,
								"apiMaxCount"		=> $apiMaxCount,
								"apiToken"			=> $apiToken,
								"apiTokenExpire"	=> $apiTokenExpire,
								"is_enable"			=> $apiEnable,
								"addTime"			=> $addTime,
								"add_user_id"		=> $uid,
							);
        $res				= ApiCompetenceModel::addApiCompetence($data);
		self::$errCode  	= ApiCompetenceModel::$errCode;
        self::$errMsg   	= ApiCompetenceModel::$errMsg;
		return $res;
    }

	/**
	 * ApiCompetenceAct::act_updateApiCompetence()
	 * 修改API开放授权
	 * @param string $apiName API名称
	 * @param string $apiValue 授权内容
	 * @param string $apiMaxCount 当天调用次数
	 * @param date $apiTokenExpire API token有效期
	 * @param string $apiEnable 是否启用
	 * @param int $apiUid 用户GID
	 * @param int $id 开放权限ID
	 * @return  bool
	 */
	public function act_updateApiCompetence(){
		$id					= isset($_POST["id"]) ? abs(intval(trim($_POST["id"]))) : 0;
        $apiUid				= isset($_POST["apiUid"]) ? abs(intval($_POST["apiUid"])) : 0;
        $apiName			= isset($_POST["apiName"]) ? post_check($_POST["apiName"]) : "";
        $apiArr				= isset($_POST["apiValue"]) ? $_POST["apiValue"] : "";
        $apiMaxCount		= isset($_POST["apiMaxCount"]) ? abs(intval($_POST["apiMaxCount"])) : 0;
        $apiEnable			= isset($_POST["apiEnable"]) ? abs(intval($_POST["apiEnable"])) : 0;
        $apiTokenExpire		= isset($_POST["apiTokenExpire"]) ? post_check($_POST["apiTokenExpire"]) : 0;
		$apiValue			= "";
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
		if(empty($apiUid)) {
			self::$errCode  = 10001;
			self::$errMsg   = "API开放授权UID有误！";
			return false;
		}
		if(empty($apiName) || !(preg_match("/^([A-Za-z]+_?)*[A-Za-z]$/",$apiName))) {
			self::$errCode  = 10002;
			self::$errMsg   = "API开放授权接口名有误！";
			return false;
		}
		if(empty($apiArr)) {
			self::$errCode  = 10003;
			self::$errMsg   = "API开放授权内容参数有误！";
			return false;
		} else {
			$apiValue		= implode(",", $apiArr);
		}
		if(empty($apiValue) || !(preg_match("/^([\d]\,?)*[\d]$/",$apiValue))) {
			self::$errCode  = 10003;
			self::$errMsg   = "API开放授权内容格式有误！";
			return false;
		}
		if(!is_numeric($apiMaxCount)) {
			self::$errCode  = 10004;
			self::$errMsg   = "调用次数参数有误！";
			return false;
		}
		if(!in_array($apiEnable,array(0,1))) {
			self::$errCode  = 10005;
			self::$errMsg   = "是否启用参数有误！";
			return false;
		}
		$apiTokenExpire		= strtotime($apiTokenExpire);
		if($apiTokenExpire!==false) {
			if($apiTokenExpire <= time()) {
				self::$errCode  = 10006;
				self::$errMsg   = "API TOKEN有效期不能低于当前日期！";
				return false;
			}			
		} else {
			self::$errCode  = 10007;
			self::$errMsg   = "API TOKEN有效期参数有误！";
			return false;
		}		
		$data  				= array(
								"apiUid"			=> $apiUid,
								"apiName"			=> $apiName,
								"apiValue"			=> $apiValue,
								"apiMaxCount"		=> $apiMaxCount,
								"apiTokenExpire"	=> $apiTokenExpire,
								"is_enable"			=> $apiEnable,
								"editTime"			=> time(),
								"edit_user_id"		=> $uid,
							);
        $res				= ApiCompetenceModel::updateApiCompetence($id, $data);
		self::$errCode  	= ApiCompetenceModel::$errCode;
        self::$errMsg   	= ApiCompetenceModel::$errMsg;
		return $res;
    }
	
	/**
	 * ApiCompetenceAct::act_delApiCompetence()
	 * 删除API开放授权
	 * @param int $id 开放权限ID
	 * @return  bool
	 */
	public function act_delApiCompetence(){
		$gid		= isset($_POST["id"]) ? intval(trim($_POST["id"])) : 0;
		$act		= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod		= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 30001;
			self::$errMsg   = "对不起,您无数据删除权限！";
			return false;
		}
		if(empty($gid) || !is_numeric($gid)) {
			self::$errCode  = 30000;
			self::$errMsg   = "开放用户权限ID有误！";
			return false;
		}
        $res			= ApiCompetenceModel::delApiCompetence($gid);
		self::$errCode  = ApiCompetenceModel::$errCode;
        self::$errMsg   = ApiCompetenceModel::$errMsg;
		return $res;
    }
}
?>