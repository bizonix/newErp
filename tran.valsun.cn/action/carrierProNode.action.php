<?php
/**
 * 类名：CarrierProNodeAct
 * 功能：运输方式处理节点管理动作处理层
 * 版本：1.0
 * 日期：2014/07/08
 * 作者：管拥军
 */
  
class CarrierProNodeAct {
    public static $errCode	= 0;
	public static $errMsg	= "";

	/**
	 * CarrierProNodeAct::actIndex()
	 * 列出符合条件的数据并分页显示
	 * @param string $condition 查询条件
	 * @param integer $curpage 页码
	 * @param integer $pagenum 每页个数
	 * @return array 
	 */
 	public function actIndex(){
		$data			= array();
		$carrierProNode	= new CarrierProNodeModel();
		//接收参数生成条件
		$curpage		= isset($_GET['page']) ? abs(intval($_GET['page'])) : 1;
		$type			= isset($_GET['type']) ? trim($_GET['type']) : '';
		$key			= isset($_GET['key']) ? post_check(trim($_GET['key'])) : '';
		$carrierId		= isset($_GET['carrierId']) ? intval($_GET['carrierId']) : 0;
		$condition		= "1";
		if($type && $key) {
			if(!in_array($type,array('nodeTitle','nodeKey'))) redirect_to("index.php?mod=carrierProNode&act=index");
			$condition	.= ' AND '.$type." LIKE '%".$key."%'";
		}
		if(!empty($carrierId)) {
			$condition	.= " AND carrierId = '{$carrierId}'";
		}
		
		//获取符合条件的数据并分页
		$pagenum		= 20; //每页显示的个数
		$total			= $carrierProNode->modListCount($condition);
		$res			= $carrierProNode->modList($condition, $curpage, $pagenum);
		$page	 		= new Page($total, $pagenum, '', 'CN');
		$pageStr		= "";
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
		$data['carriers']	= TransOpenApiModel::getCarrier(2);
		$data['carrierId']	= $carrierId;
		self::$errCode   	= CarrierProNodeModel::$errCode;
        self::$errMsg    	= CarrierProNodeModel::$errMsg;
		if(self::$errCode != 0) {
			show_message($this->smarty,self::$errMsg,"");	
			return false;
		}
        return $data;
    }
		
	/**
	 * CarrierProNodeAct::actAdd()
	 * 添加运输方式处理节点信息
	 * @return array 
	 */
	public function actAdd(){
		$data				= array();
		$data['lists']		= TransOpenApiModel::getCarrier(2);
		self::$errCode  	= TransOpenApiModel::$errCode;
        self::$errMsg   	= TransOpenApiModel::$errMsg;
		if(self::$errCode != 0) {
			show_message($this->smarty,self::$errMsg,"");	
			return false;
		}
        return $data;
    }

	/**
	 * CarrierProNodeAct::actModify()
	 * 返回某个运输方式处理节点的信息
	 * @param int $id 查询ID
	 * @return array 
	 */
	public function actModify(){
		$data				= array();
		$id					= isset($_GET['id']) ? intval(trim($_GET['id'])) : 0;
		if(empty($id)) {
			show_message($this->smarty,"ID不能为空？","");	
			return false;
		}
		$data['id']			= $id;
		$data['lists']		= TransOpenApiModel::getCarrier(2);
		$data['res']		= CarrierProNodeModel::modModify($id);
		self::$errCode  	= CarrierProNodeModel::$errCode;
        self::$errMsg   	= CarrierProNodeModel::$errMsg;
		if(self::$errCode != 0) {
			show_message($this->smarty,self::$errMsg,"");	
			return false;
		}
		return $data;
    }	
	
	/**
	 * CarrierProNodeAct::act_addCarrierProNode()
	 * 添加运输方式处理节点
	 * @param string $nodeTitle 节点名
	 * @param string $nodeKey 处理关键词
	 * @param string $carrierId 运输方式ID
	 * @return  bool
	 */
	public function act_addCarrierProNode(){
        $nodeTitle			= isset($_POST["nodeTitle"]) ? post_check($_POST["nodeTitle"]) : "";
        $nodeKey			= isset($_POST["nodeKey"]) ? post_check($_POST["nodeKey"]) : "";
        $carrierId			= isset($_POST["carrierId"]) ? abs(intval(trim($_POST["carrierId"]))) : 0;
		$act				= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod				= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 10010;
			self::$errMsg   = "对不起,您无数据添加权限！";
			return false;
		}
		if(empty($carrierId)) {
			self::$errCode  = 10000;
			self::$errMsg   = "运输方式参数有误！";
			return false;
		}
		if(empty($nodeTitle)) {
			self::$errCode  = 10001;
			self::$errMsg   = "节点名参数有误！";
			return false;
		}
		if(empty($nodeKey) || !(preg_match("/^([\S]+\s?)*[\S]$/",$nodeKey))) {
			self::$errCode  = 10002;
			self::$errMsg   = "处理关键词参数有误！";
			return false;
		}
		$uid				= $_SESSION[C('USER_AUTH_SYS_ID')];
		$data  				= array(
								"nodeName"		=> $nodeTitle,
								"nodeKey"		=> $nodeKey,
								"carrierId"		=> $carrierId,
								"addTime"		=> time(),
								"add_user_id"	=> $uid,
							);
        $res				= CarrierProNodeModel::addCarrierProNode($data);
		self::$errCode  	= CarrierProNodeModel::$errCode;
        self::$errMsg   	= CarrierProNodeModel::$errMsg;
		return $res;
    }

	/**
	 * CarrierProNodeAct::act_updateCarrierProNode()
	 * 修改运输方式处理节点
	 * @param string $nodeTitle 节点名称
	 * @param string $nodeKey 处理关键词
	 * @param string $carrierId 运输方式ID
	 * @return  bool
	 */
	public function act_updateCarrierProNode(){
		$id					= isset($_POST["id"]) ? abs(intval(trim($_POST["id"]))) : 0;
		$nodeTitle			= isset($_POST["nodeTitle"]) ? post_check($_POST["nodeTitle"]) : "";
        $nodeKey			= isset($_POST["nodeKey"]) ? post_check($_POST["nodeKey"]) : "";
        $carrierId			= isset($_POST["carrierId"]) ? abs(intval(trim($_POST["carrierId"]))) : 0;
		$act				= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod				= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 10010;
			self::$errMsg   = "对不起,您无数据编辑权限！";
			return false;
		}
		if(empty($id) || !is_numeric($id)) {
			self::$errCode  = 10000;
			self::$errMsg   = "ID有误！";
			return false;
		}
		if(empty($carrierId)) {
			self::$errCode  = 10001;
			self::$errMsg   = "运输方式参数有误！";
			return false;
		}
		if(empty($nodeTitle)) {
			self::$errCode  = 10002;
			self::$errMsg   = "节点名参数有误！";
			return false;
		}
		if(empty($nodeKey) || !(preg_match("/^([\S]+\s?)*[\S]$/",$nodeKey))) {
			self::$errCode  = 10003;
			self::$errMsg   = "处理关键词参数有误！";
			return false;
		}
		$uid				= $_SESSION[C('USER_AUTH_SYS_ID')];
		$data 				= array(
								"nodeName"		=> $nodeTitle,
								"nodeKey"		=> $nodeKey,
								"carrierId"		=> $carrierId,
								"editTime"		=> time(),
								"edit_user_id"	=> $uid,
							);
        $res				= CarrierProNodeModel::updateCarrierProNode($id, $data);
		self::$errCode  	= CarrierProNodeModel::$errCode;
        self::$errMsg  		= CarrierProNodeModel::$errMsg;
		return $res;
    }
	
	/**
	 * CarrierProNodeAct::act_delCarrierProNode()
	 * 删除运输方式处理节点
	 * @param int $id 运输方式处理节点ID
	 * @return  bool
	 */
	public function act_delCarrierProNode(){
		$id		= isset($_POST["id"]) ? abs(intval(trim($_POST["id"]))) : 0;
		$act	= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod	= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 10001;
			self::$errMsg   = "对不起,您无数据删除权限！";
			return false;
		}
		if(empty($id) || !is_numeric($id)) {
			self::$errCode  = 10000;
			self::$errMsg   = "ID有误！";
			return false;
		}
        $res				= CarrierProNodeModel::delCarrierProNode($id);
		self::$errCode  	= CarrierProNodeModel::$errCode;
        self::$errMsg   	= CarrierProNodeModel::$errMsg;
		return $res;
    }
}
?>