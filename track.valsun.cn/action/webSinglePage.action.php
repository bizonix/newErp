<?php
/**
 * 类名：WebSinglePageAct
 * 功能：网站单页管理动作处理层
 * 版本：1.0
 * 日期：2014/07/16
 * 作者：管拥军
 */
  
class WebSinglePageAct {
    public static $errCode	= 0;
	public static $errMsg	= "";
	
	/**
	 * WebSinglePageAct::actIndex()
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
			if(!in_array($type,array('topic'))) redirect_to("index.php?mod=webSinglePage&act=index");
			$condition	.= ' AND '.$type." LIKE '%".$key."%'";
		}
		//获取符合条件的数据并分页
		$pagenum		= 20;
		$total			= WebSinglePageModel::modListCount($condition);
		$res			= WebSinglePageModel::modList($condition, $curpage, $pagenum);
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
		self::$errCode   = WebSinglePageModel::$errCode;
        self::$errMsg    = WebSinglePageModel::$errMsg;
		if(self::$errCode != 0) {
			show_message($this->smarty,self::$errMsg,"");	
			exit;
		}
        return $data;
    }

	/**
	 * WebSinglePageAct::actAdd()
	 * 添加某个网站单页
	 * @return array  
	 */
	public function actAdd(){
		$data	= array();
        return $data;
    }
	
	/**
	 * WebSinglePageAct::actModify()
	 * 返回某个网站单页
	 * @param int $id 查询ID
	 * @return array 
	 */
	public function actModify(){
		$data			= array();
		$id				= isset($_GET['id']) ? abs(intval(trim($_GET['id']))) : 0;
		if(empty($id)) {
			show_message($this->smarty,"ID参数非法","");
			exit;
		}
		$data['id']		= $id;
		$data['res']	= WebSinglePageModel::modModify($id);
		self::$errCode  = WebSinglePageModel::$errCode;
        self::$errMsg   = WebSinglePageModel::$errMsg;
		if(self::$errCode != 0) {
			show_message($this->smarty,self::$errMsg,"");	
			exit;
		}
        return $data;
    }
	
	/**
	 * WebSinglePageAct::act_addWebSinglePage()
	 * 添加网站单页
	 * @param string $topic 名称
	 * @param string $content 内容
	 * @param int $is_enable 是否启用
	 * @param int $layer 排序层级
	 * @return  bool
	 */
	public function act_addWebSinglePage(){
        $topic				= isset($_POST["topic"]) ? post_check($_POST["topic"]) : "";
        $content			= isset($_POST["content"]) ? $_POST["content"] : "";
        $is_enable			= isset($_POST["is_enable"]) ? abs(intval($_POST["is_enable"])) : 0;
        $layer				= isset($_POST["layer"]) ? abs(intval($_POST["layer"])) : 0;
		$act				= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod				= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 10000;
			self::$errMsg   = "对不起,您无数据添加权限！";
			return false;
		}
		if(empty($topic)) {
			self::$errCode  = 10001;
			self::$errMsg   = "名称参数有误！";
			return false;
		}
		if(empty($content)) {
			self::$errCode  = 10002;
			self::$errMsg   = "内容参数有误！";
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
								"topic"				=> $topic,
								"content"			=> $content,
								"is_enable"			=> $is_enable,
								"layer"				=> $layer,
								"addTime"			=> $addTime,
								"add_user_id"		=> $uid,
							);
        $res				= WebSinglePageModel::addWebSinglePage($data);
		self::$errCode  	= WebSinglePageModel::$errCode;
        self::$errMsg   	= WebSinglePageModel::$errMsg;
		return $res;
    }

	/**
	 * WebSinglePageAct::act_updateWebSinglePage()
	 * 修改网站单页
	 * @param string $topic 名称
	 * @param string $content 内容
	 * @param int $is_enable 是否启用
	 * @param int $layer 排序层级
	 * @param int $id ID
	 * @return  bool
	 */
	public function act_updateWebSinglePage(){
		$id					= isset($_POST["id"]) ? abs(intval(trim($_POST["id"]))) : 0;
        $topic				= isset($_POST["topic"]) ? post_check($_POST["topic"]) : "";
        $content			= isset($_POST["content"]) ? $_POST["content"] : "";
        $is_enable			= isset($_POST["is_enable"]) ? abs(intval($_POST["is_enable"])) : 0;
        $layer				= isset($_POST["layer"]) ? abs(intval($_POST["layer"])) : 0;
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
		if(empty($topic)) {
			self::$errCode  = 10001;
			self::$errMsg   = "名称参数有误！";
			return false;
		}
		if(empty($content)) {
			self::$errCode  = 10002;
			self::$errMsg   = "内容参数有误！";
			return false;
		}
		if(!in_array($is_enable,array(0,1))) {
			self::$errCode  = 10003;
			self::$errMsg   = "是否启用参数有误！";
			return false;
		}
		$uid				= $_SESSION[C('USER_AUTH_SYS_ID')];
		$data  				= array(
								"topic"				=> $topic,
								"content"			=> $content,
								"is_enable"			=> $is_enable,
								"layer"				=> $layer,
								"editTime"			=> time(),
								"edit_user_id"		=> $uid,
							);
        $res				= WebSinglePageModel::updateWebSinglePage($id, $data);
		self::$errCode  	= WebSinglePageModel::$errCode;
        self::$errMsg   	= WebSinglePageModel::$errMsg;
		return $res;
    }
	
	/**
	 * WebSinglePageAct::act_delWebSinglePage()
	 * 删除网站单页
	 * @param int $id ID
	 * @return  bool
	 */
	public function act_delWebSinglePage(){
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
        $res			= WebSinglePageModel::delWebSinglePage($id);
		self::$errCode  = WebSinglePageModel::$errCode;
        self::$errMsg   = WebSinglePageModel::$errMsg;
		return $res;
    }
}
?>