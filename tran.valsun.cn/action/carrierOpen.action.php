<?php
/**
 * 类名：CarrierOpenAct
 * 功能：运输方式开放管理动作处理层
 * 版本：1.0
 * 日期：2014/07/07
 * 作者：管拥军
 */
  
class CarrierOpenAct {
    public static $errCode	= 0;
	public static $errMsg	= "";

	/**
	 * CarrierOpenAct::actIndex()
	 * 列出符合条件的数据并分页显示
	 * @param string $condition 查询条件
	 * @param integer $curpage 页码
	 * @param integer $pagenum 每页个数
	 * @return array 
	 */
 	public function actIndex(){
		$data			= array();
		$carrierOpen	= new CarrierOpenModel();
		//接收参数生成条件
		$curpage		= isset($_GET['page']) ? abs(intval($_GET['page'])) : 1;
		$type			= isset($_GET['type']) ? trim($_GET['type']) : '';
		$key			= isset($_GET['key']) ? post_check(trim($_GET['key'])) : '';
		$carrierId		= isset($_GET['carrierId']) ? intval($_GET['carrierId']) : 0;
		$condition		= "1";
		if($type && $key) {
			if(!in_array($type,array('carrierAbb','carrierIndex'))) redirect_to("index.php?mod=carrierOpen&act=index");
			$condition	.= ' AND '.$type." = '".$key."'";
		}
		if(!empty($carrierId)) {
			$condition	.= " AND carrierId = '{$carrierId}'";
		}
		
		//获取符合条件的数据并分页
		$pagenum		= 20; //每页显示的个数
		$total			= $carrierOpen->modListCount($condition);
		$res			= $carrierOpen->modList($condition, $curpage, $pagenum);
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
		self::$errCode   	= CarrierOpenModel::$errCode;
        self::$errMsg    	= CarrierOpenModel::$errMsg;
		if(self::$errCode != 0) {
			show_message($this->smarty,self::$errMsg,"");	
			return false;
		}
        return $data;
    }
		
	/**
	 * CarrierOpenAct::actAdd()
	 * 添加运输方式开放信息
	 * @return array 
	 */
	public function actAdd(){
		$data				= array();
		$data['lists']		= TransOpenApiModel::getCarrierByAdd(1);
		self::$errCode  	= TransOpenApiModel::$errCode;
        self::$errMsg   	= TransOpenApiModel::$errMsg;
		if(self::$errCode != 0) {
			show_message($this->smarty,self::$errMsg,"");	
			return false;
		}
        return $data;
    }

	/**
	 * CarrierOpenAct::actModify()
	 * 返回某个运输方式开放的信息
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
		$data['lists']		= TransOpenApiModel::getCarrierByAdd(1);
		$data['res']		= CarrierOpenModel::modModify($id);
		self::$errCode  	= CarrierOpenModel::$errCode;
        self::$errMsg   	= CarrierOpenModel::$errMsg;
		if(self::$errCode != 0) {
			show_message($this->smarty,self::$errMsg,"");	
			return false;
		}
		return $data;
    }	
	
	/**
	 * CarrierOpenAct::act_addCarrierOpen()
	 * 添加开放运输方式
	 * @param string $carrierAbb 简称
	 * @param string $carrierEn 英文名称
	 * @param string $carrierIndex 字母索引
	 * @param string $carrierAging 时效
	 * @param string $carrierNote 备注
	 * @param float $carrierDis 原价后折扣
	 * @param string $carrierId 运输方式ID
	 * @return  bool
	 */
	public function act_addCarrierOpen(){
        $carrierAbb			= isset($_POST["carrierAbb"]) ? post_check($_POST["carrierAbb"]) : "";
        $carrierEn			= isset($_POST["carrierEn"]) ? post_check($_POST["carrierEn"]) : "";
        $carrierIndex		= isset($_POST["carrierIndex"]) ? post_check($_POST["carrierIndex"]) : "";
        $carrierId			= isset($_POST["carrierId"]) ? abs(intval(trim($_POST["carrierId"]))) : 0;
        $carrierDis			= isset($_POST["carrierDiscount"]) ? floatval(trim($_POST["carrierDiscount"])) : 0;
        $carrierAging		= isset($_POST["carrierAging"]) ? post_check($_POST["carrierAging"]) : "";
        $carrierNote		= isset($_POST["carrierNote"]) ? post_check($_POST["carrierNote"]) : "";
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
		if(empty($carrierAbb) || !(preg_match("/^[A-Z_]{1,20}$/",$carrierAbb))) {
			self::$errCode  = 10001;
			self::$errMsg   = "运输方式简称参数有误！";
			return false;
		}
		if(empty($carrierEn) || !(preg_match("/^[A-Za-z]{1,50}$/",$carrierEn))) {
			self::$errCode  = 10002;
			self::$errMsg   = "运输方式英文名称参数有误！";
			return false;
		}
		if(empty($carrierIndex) || !(preg_match("/^[A-Z]{1}$/",$carrierIndex))) {
			self::$errCode  = 10003;
			self::$errMsg   = "字母索引参数有误！";
			return false;
		}
		$carrierAdds		= TransOpenApiModel::getShipAddByCarrierId($carrierId);
		$carrierAdd			= !empty($carrierAdds) ? $carrierAdds['addressId'] : 0;
		$uid				= $_SESSION[C('USER_AUTH_SYS_ID')];
		$data  				= array(
								"carrierAbb"		=> $carrierAbb,
								"carrierEn"			=> $carrierEn,
								"carrierIndex"		=> $carrierIndex,
								"carrierAdd"		=> $carrierAdd,
								"carrierId"			=> $carrierId,
								"carrierDiscount"	=> $carrierDis,
								"carrierAging"		=> $carrierAging,
								"carrierNote"		=> $carrierNote,
								"addTime"			=> time(),
								"add_user_id"		=> $uid,
							);
        $res				= CarrierOpenModel::addCarrierOpen($data);
		self::$errCode  	= CarrierOpenModel::$errCode;
        self::$errMsg   	= CarrierOpenModel::$errMsg;
		return $res;
    }

	/**
	 * CarrierOpenAct::act_updateCarrierOpen()
	 * 修改开放运输方式
	 * @param string $carrierAbb 简称
	 * @param string $carrierIndex 字母索引
	 * @param string $carrierAging 时效
	 * @param string $carrierNote 备注
	 * @param float $carrierDis 原价后折扣
	 * @param string $carrierId 运输方式ID
	 * @return  bool
	 */
	public function act_updateCarrierOpen(){
		$id					= isset($_POST["id"]) ? abs(intval(trim($_POST["id"]))) : 0;
		$carrierAbb			= isset($_POST["carrierAbb"]) ? post_check($_POST["carrierAbb"]) : "";
		$carrierEn			= isset($_POST["carrierEn"]) ? post_check($_POST["carrierEn"]) : "";
        $carrierIndex		= isset($_POST["carrierIndex"]) ? post_check($_POST["carrierIndex"]) : "";
        $carrierId			= isset($_POST["carrierId"]) ? abs(intval(trim($_POST["carrierId"]))) : 0;
        $carrierDis			= isset($_POST["carrierDiscount"]) ? floatval(trim($_POST["carrierDiscount"])) : 0;
        $carrierAging		= isset($_POST["carrierAging"]) ? post_check($_POST["carrierAging"]) : "";
        $carrierNote		= isset($_POST["carrierNote"]) ? post_check($_POST["carrierNote"]) : "";
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
			self::$errCode  = 10000;
			self::$errMsg   = "运输方式参数有误！";
			return false;
		}
		if(empty($carrierAbb) || !(preg_match("/^[A-Z_]{1,20}$/",$carrierAbb))) {
			self::$errCode  = 10001;
			self::$errMsg   = "运输方式简称参数有误！";
			return false;
		}
		if(empty($carrierEn) || !(preg_match("/^[A-Za-z]{1,50}$/",$carrierEn))) {
			self::$errCode  = 10002;
			self::$errMsg   = "运输方式英文名称参数有误！";
			return false;
		}
		if(empty($carrierIndex) || !(preg_match("/^[A-Z]{1}$/",$carrierIndex))) {
			self::$errCode  = 10003;
			self::$errMsg   = "字母索引参数有误！";
			return false;
		}
		$carrierAdds		= TransOpenApiModel::getShipAddByCarrierId($carrierId);
		$carrierAdd			= !empty($carrierAdds) ? $carrierAdds['addressId'] : 0;
		$uid				= $_SESSION[C('USER_AUTH_SYS_ID')];
		$data 				= array(
								"carrierAbb"		=> $carrierAbb,
								"carrierEn"			=> $carrierEn,
								"carrierIndex"		=> $carrierIndex,
								"carrierAdd"		=> $carrierAdd,
								"carrierId"			=> $carrierId,
								"carrierDiscount"	=> $carrierDis,
								"carrierAging"		=> $carrierAging,
								"carrierNote"		=> $carrierNote,
								"editTime"			=> time(),
								"edit_user_id"		=> $uid,
							);
        $res				= CarrierOpenModel::updateCarrierOpen($id, $data);
		self::$errCode  	= CarrierOpenModel::$errCode;
        self::$errMsg  		= CarrierOpenModel::$errMsg;
		return $res;
    }
	
	/**
	 * CarrierOpenAct::act_delCarrierOpen()
	 * 删除开放运输方式
	 * @param int $id 开放运输方式ID
	 * @return  bool
	 */
	public function act_delCarrierOpen(){
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
        $res				= CarrierOpenModel::delCarrierOpen($id);
		self::$errCode  	= CarrierOpenModel::$errCode;
        self::$errMsg   	= CarrierOpenModel::$errMsg;
		return $res;
    }
}
?>