<?php
/**
 * 类名：UserCompetenceAct
 * 功能：用户开放授权管理动作处理层
 * 版本：1.0
 * 日期：2014/04/19
 * 作者：管拥军
 */
  
class UserCompetenceAct {
    public static $errCode	= 0;
	public static $errMsg	= "";

	/**
	 * UserCompetenceAct::actIndex()
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
			if (!in_array($type,array('competence'))) redirect_to("index.php?mod=userCompetence&act=index");
			$condition	.= ' AND '.$type." = '".$key."'";
		}
		//获取符合条件的数据并分页
		$pagenum		= 20;
		$total			= UserCompetenceModel::modListCount($condition);
		$res			= UserCompetenceModel::modList($condition, $curpage, $pagenum);
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
		self::$errCode   = UserCompetenceModel::$errCode;
        self::$errMsg    = UserCompetenceModel::$errMsg;
		if (self::$errCode!=0) {
			show_message($this->smarty,self::$errMsg,"");	
			return false;
		}
        return $data;
    }

	/**
	 * UserCompetenceAct::actAdd()
	 * 添加某个用户开放授权
	 * @return array  
	 */
	public function actAdd(){
		$data	= array();
		$data['gids']	= UserCompetenceModel::getGlobalUser();
		$data['lists']	= UserCompetencesModel::getCompetencesByCat();
        return $data;
    }
	
	/**
	 * UserCompetenceAct::actModify()
	 * 返回某个用户开放授权
	 * @param int $gid 查询ID
	 * @return array 
	 */
	public function actModify(){
		$data		= array();
		$id			= isset($_GET['id']) ? intval(trim($_GET['id'])) : 0;
		if (empty($id)) {
			show_message($this->smarty,"用户开放授权ID不能为空？","");	
			return false;
		}
		$data['gid']		= $id;
		$data['gids']	= UserCompetenceModel::getGlobalUser();
		$data['lists']	= UserCompetencesModel::getCompetencesByCat();
		$data['res']	= UserCompetenceModel::modModify($id);
		self::$errCode  = UserCompetenceModel::$errCode;
        self::$errMsg   = UserCompetenceModel::$errMsg;
		if (self::$errCode!=0) {
			show_message($this->smarty,self::$errMsg,"");	
			return false;
		}
        return $data;
    }

	/**
	 * UserCompetenceAct::act_addUserCompetence()
	 * 添加用户开放授权
	 * @param string $competence 授权内容
	 * @param int $gid 开放权限ID
	 * @return  bool
	 */
	public function act_addUserCompetence(){
        $gid		= isset($_POST["gid"]) ? intval($_POST["gid"]) : 0;
        $competence	= isset($_POST["competence"]) ? post_check($_POST["competence"]) : "";
		$act		= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod		= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if	(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 10000;
			self::$errMsg   = "对不起,您无数据添加权限！";
			return false;
		}
		if (empty($gid)) {
			self::$errCode  = 10001;
			self::$errMsg   = "用户开放权限ID有误！";
			return false;
		}
		if (empty($competence)) {
			self::$errCode  = 10002;
			self::$errMsg   = "用户开放权限内容有误！";
			return false;
		}
		$competence			= explode(",",$competence);
		$competences		= array();
		foreach ($competence as $v) {
			$vals	= explode(":",$v);
			if(!is_array($competences[$vals[0]])) $competences[$vals[0]] = array();
			array_push($competences[$vals[0]], $vals[1]);
		}
		$competences		= json_encode($competences);
		$uid				= $_SESSION[C('USER_AUTH_SYS_ID')];
		$data  = array(
			"gid"			=> $gid,
			"competence"	=> $competences,
			"addTime"		=> time(),
			"add_user_id"	=> $uid,
		);
        $res				= UserCompetenceModel::addUserCompetence($data);
		self::$errCode  	= UserCompetenceModel::$errCode;
        self::$errMsg   	= UserCompetenceModel::$errMsg;
		return $res;
    }

	/**
	 * UserCompetenceAct::act_updateUserCompetence()
	 * 修改用户开放授权
	 * @param string $competence 授权内容
	 * @param int $gid 开放权限ID
	 * @return  bool
	 */
	public function act_updateUserCompetence(){
		$gid		= isset($_POST["gid"]) ? intval($_POST["gid"]) : 0;
        $competence	= isset($_POST["competence"]) ? post_check($_POST["competence"]) : "";
		$act		= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod		= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if	(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 20000;
			self::$errMsg   = "对不起,您无数据编辑权限！";
			return false;
		}
		if (empty($gid)) {
			self::$errCode  = 20001;
			self::$errMsg   = "用户开放权限ID有误！";
			return false;
		}
		if (empty($competence)) {
			self::$errCode  = 20002;
			self::$errMsg   = "用户开放权限内容有误！";
			return false;
		}
		$competence			= explode(",",$competence);
		$competences		= array();
		foreach ($competence as $v) {
			$vals	= explode(":",$v);
			if(!is_array($competences[$vals[0]])) $competences[$vals[0]] = array();
			array_push($competences[$vals[0]], $vals[1]);
		}
		$competences		= json_encode($competences);
		$uid				= $_SESSION[C('USER_AUTH_SYS_ID')];
		$data  = array(
			"gid"			=> $gid,
			"competence"	=> $competences,
			"editTime"		=> time(),
			"edit_user_id"	=> $uid,
		);
        $res			= UserCompetenceModel::updateUserCompetence($gid, $data);
		self::$errCode  = UserCompetenceModel::$errCode;
        self::$errMsg   = UserCompetenceModel::$errMsg;
		return $res;
    }
	
	/**
	 * UserCompetenceAct::act_delUserCompetence()
	 * 删除用户开放授权
	 * @param int $gid 开放权限ID
	 * @return  bool
	 */
	public function act_delUserCompetence(){
		$gid		= isset($_POST["id"]) ? intval(trim($_POST["id"])) : 0;
		$act		= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod		= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if	(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 30001;
			self::$errMsg   = "对不起,您无数据删除权限！";
			return false;
		}
		if (empty($gid) || !is_numeric($gid)) {
			self::$errCode  = 30000;
			self::$errMsg   = "开放用户权限ID有误！";
			return false;
		}
        $res			= UserCompetenceModel::delUserCompetence($gid);
		self::$errCode  = UserCompetenceModel::$errCode;
        self::$errMsg   = UserCompetenceModel::$errMsg;
		return $res;
    }
}
?>