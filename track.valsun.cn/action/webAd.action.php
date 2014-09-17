<?php
/**
 * 类名：WebAdAct
 * 功能：网站广告管理动作处理层
 * 版本：1.0
 * 日期：2014/07/18
 * 作者：管拥军
 */
  
class WebAdAct {
    public static $errCode	= 0;
	public static $errMsg	= "";
	
	/**
	 * WebAdAct::actIndex()
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
		$typeId			= isset($_GET['typeId']) ? abs(intval($_GET['typeId'])) : 0;
		$condition		.= "1";
		if($type && $key) {
			if(!in_array($type,array('topic'))) redirect_to("index.php?mod=webAd&act=index");
			$condition	.= ' AND '.$type." LIKE '%".$key."%'";
		}
		if(!empty($typeId)) {
			$condition	.= " AND typeId = '{$typeId}'";
		}
		
		//获取符合条件的数据并分页
		$pagenum		= 20;
		$total			= WebAdModel::modListCount($condition);
		$res			= WebAdModel::modList($condition, $curpage, $pagenum);
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
		$data['typeId']	 = $typeId;
		$data['lists']	 = $res;
		$data['pages']	 = $pageStr;
		self::$errCode   = WebAdModel::$errCode;
        self::$errMsg    = WebAdModel::$errMsg;
		if(self::$errCode != 0) {
			show_message($this->smarty,self::$errMsg,"");	
			exit;
		}
        return $data;
    }

	/**
	 * WebAdAct::actAdd()
	 * 添加某个网站广告
	 * @return array  
	 */
	public function actAdd(){
		$data	= array();
        return $data;
    }
	
	/**
	 * WebAdAct::actModify()
	 * 返回某个网站广告
	 * @param int $id 查询ID
	 * @return array 
	 */
	public function actModify(){
		$data			= array();
		$id				= isset($_GET['id']) ? intval(trim($_GET['id'])) : 0;
		if(empty($id)) {
			show_message($this->smarty,"网站广告ID不能为空？","");	
			exit;
		}
		$data['id']		= $id;
		$data['res']	= WebAdModel::modModify($id);
		self::$errCode  = WebAdModel::$errCode;
        self::$errMsg   = WebAdModel::$errMsg;
		if(self::$errCode != 0) {
			show_message($this->smarty,self::$errMsg,"");	
			exit;
		}
        return $data;
    }

	/**
	 * WebAdAct::act_addWebAd()
	 * 添加网站广告
	 * @param string $topic 名称
	 * @param string $content 内容
	 * @param int $is_enable 是否启用
	 * @param int $layer 排序层级
	 * @param int $typeId 类型
	 * @return  bool
	 */
	public function act_addWebAd(){
        $topic				= isset($_POST["topic"]) ? post_check($_POST["topic"]) : "";
        $content			= isset($_POST["content"]) ? $_POST["content"] : "";
        $is_enable			= isset($_POST["is_enable"]) ? abs(intval($_POST["is_enable"])) : 0;
        $layer				= isset($_POST["layer"]) ? abs(intval($_POST["layer"])) : 0;
        $typeId				= isset($_POST["typeId"]) ? abs(intval($_POST["typeId"])) : 0;
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
		if(!in_array($typeId,array(1,2,3))) {
			self::$errCode  = 10004;
			self::$errMsg   = "类型参数有误！";
			return false;
		}
		$addTime			= time();
		$uid				= $_SESSION[C('USER_AUTH_SYS_ID')];
		$data  				= array(
								"topic"				=> $topic,
								"content"			=> $content,
								"is_enable"			=> $is_enable,
								"layer"				=> $layer,
								"typeId"			=> $typeId,
								"addTime"			=> $addTime,
								"add_user_id"		=> $uid,
							);
        $res				= WebAdModel::addWebAd($data);
		self::$errCode  	= WebAdModel::$errCode;
        self::$errMsg   	= WebAdModel::$errMsg;
		return $res;
    }

	/**
	 * WebAdAct::act_updateWebAd()
	 * 修改网站广告
	 * @param string $topic 名称
	 * @param string $content 内容
	 * @param int $is_enable 是否启用
	 * @param int $layer 排序层级
	 * @param int $id ID
	 * @return  bool
	 */
	public function act_updateWebAd(){
		$id					= isset($_POST["id"]) ? abs(intval(trim($_POST["id"]))) : 0;
        $topic				= isset($_POST["topic"]) ? post_check($_POST["topic"]) : "";
        $content			= isset($_POST["content"]) ? $_POST["content"] : "";
        $is_enable			= isset($_POST["is_enable"]) ? abs(intval($_POST["is_enable"])) : 0;
        $layer				= isset($_POST["layer"]) ? abs(intval($_POST["layer"])) : 0;
        $typeId				= isset($_POST["typeId"]) ? abs(intval($_POST["typeId"])) : 0;
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
		if(!in_array($typeId,array(1,2,3))) {
			self::$errCode  = 10004;
			self::$errMsg   = "类型参数有误！";
			return false;
		}
		$uid				= $_SESSION[C('USER_AUTH_SYS_ID')];
		$data  				= array(
								"topic"				=> $topic,
								"content"			=> $content,
								"is_enable"			=> $is_enable,
								"layer"				=> $layer,
								"typeId"			=> $typeId,
								"editTime"			=> time(),
								"edit_user_id"		=> $uid,
							);
        $res				= WebAdModel::updateWebAd($id, $data);
		self::$errCode  	= WebAdModel::$errCode;
        self::$errMsg   	= WebAdModel::$errMsg;
		return $res;
    }
	
	/**
	 * WebAdAct::act_delWebAd()
	 * 删除网站广告
	 * @param int $id ID
	 * @return  bool
	 */
	public function act_delWebAd(){
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
        $res			= WebAdModel::delWebAd($id);
		self::$errCode  = WebAdModel::$errCode;
        self::$errMsg   = WebAdModel::$errMsg;
		return $res;
    }	
}
?>