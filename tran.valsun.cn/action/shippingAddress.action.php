<?php
/**
 * 类名：ShippingAddressAct
 * 功能：发货地址管理动作处理层
 * 版本：1.0
 * 日期：2013/10/21
 * 作者：管拥军
 */
  
class ShippingAddressAct {
    public static $errCode	= 0;
	public static $errMsg	= "";

	/**
	 * ShippingAddressAct::actList()
	 * 列出符合条件的数据并分页显示
	 * @param string $where 查询条件
	 * @param integer $page 页码
	 * @param integer $pagenum 每页个数
	 * @return array 结果集数组
	 */
 	public function actList($where='1', $page=1, $pagenum=20){
		$res			= ShippingAddressModel::modList($where, $page, $pagenum);
		self::$errCode  = ShippingAddressModel::$errCode;
        self::$errMsg   = ShippingAddressModel::$errMsg;
        return $res;
    }

	/**
	 * ShippingAddressAct::actListCount()
	 * 返回某个条件结果统计的总数
	 * @param string $where 查询条件
	 * @return integer 总数量 
	 */
	public function actListCount($where='1'){
		$res			= ShippingAddressModel::modListCount($where);
		self::$errCode  = ShippingAddressModel::$errCode;
        self::$errMsg   = ShippingAddressModel::$errMsg;
        return $res;
    }
	
	/**
	 * ShippingAddressAct::actModify()
	 * 返回某个发货地址的信息
	 * @param int $id 查询ID
	 * @return array 
	 */
	public function actModify($id){
		$res			= ShippingAddressModel::modModify($id);
		self::$errCode  = ShippingAddressModel::$errCode;
        self::$errMsg   = ShippingAddressModel::$errMsg;
        return $res;
    }

	/**
	 * ShippingAddressAct::act_addShippingAddress()
	 * 添加发货地址
	 * @param string $cn_name 中文名称
	 * @param string $en_name 英文名称
	 * @return  bool
	 */
	public function act_addShippingAddress(){
        $cn_name	= isset($_POST["cn_name"]) ? post_check($_POST["cn_name"]) : "";
        $en_name	= isset($_POST["en_name"]) ? post_check($_POST["en_name"]) : "";
        $addres_code= isset($_POST["addres_code"]) ? post_check($_POST["addres_code"]) : "";
        $seller		= isset($_POST["seller"]) ? post_check($_POST["seller"]) : "";
		$act		= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod		= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if	(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 10001;
			self::$errMsg   = "对不起,您无数据添加权限！";
			return false;
		}
		if (empty($cn_name) || empty($cn_name)) {
			self::$errCode  = 10000;
			self::$errMsg   = "发货地址中文名称或英文名称有误！";
			return false;
		}
		$data  = array(
			"addressNameCn"	=> $cn_name,
			"addressNameEn"	=> $en_name,
			"addressCode"	=> $addres_code,
			"createdTime"	=> time(),
			"sellerName"	=> $seller,
		);
        $res			= ShippingAddressModel::addShippingAddress($data);
		self::$errCode  = ShippingAddressModel::$errCode;
        self::$errMsg   = ShippingAddressModel::$errMsg;
		return $res;
    }

	/**
	 * ShippingAddressAct::act_updateShippingAddress()
	 * 修改发货地址
	 * @param string $cn_name 中文名称
	 * @param string $en_name 英文名称
	 * @return  bool
	 */
	public function act_updateShippingAddress(){
		$id			= isset($_POST["id"]) ? intval(trim($_POST["id"])) : 0;
		$cn_name	= isset($_POST["cn_name"]) ? post_check($_POST["cn_name"]) : "";
        $en_name	= isset($_POST["en_name"]) ? post_check($_POST["en_name"]) : "";
        $addres_code= isset($_POST["addres_code"]) ? post_check($_POST["addres_code"]) : "";
		$act		= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod		= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if	(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 10001;
			self::$errMsg   = "对不起,您无数据编辑权限！";
			return false;
		}
		if (empty($id) || !is_numeric($id)) {
			self::$errCode  = 10000;
			self::$errMsg   = "发货地址ID有误！";
			return false;
		}
		if (empty($cn_name) || empty($cn_name)) {
			self::$errCode  = 10001;
			self::$errMsg   = "发货地址中文名称或英文名称有误！";
			return false;
		}
		$data  = array(
			"addressNameCn"	=> $cn_name,
			"addressNameEn"	=> $en_name,
			"addressCode"	=> $addres_code,
		);
        $res			= ShippingAddressModel::updateShippingAddress($id, $data);
		self::$errCode  = ShippingAddressModel::$errCode;
        self::$errMsg   = ShippingAddressModel::$errMsg;
		return $res;
    }
	
	/**
	 * ShippingAddressAct::act_delShippingAddress()
	 * 删除发货地址
	 * @param int $id 发货地址管理ID
	 * @return  bool
	 */
	public function act_delShippingAddress(){
		$id			= isset($_POST["id"]) ? intval(trim($_POST["id"])) : 0;
		$act		= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod		= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if	(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 10001;
			self::$errMsg   = "对不起,您无数据删除权限！";
			return false;
		}
		if (empty($id) || !is_numeric($id)) {
			self::$errCode  = 10000;
			self::$errMsg   = "发货地址ID有误！";
			return false;
		}
        $res			= ShippingAddressModel::delShippingAddress($id);
		self::$errCode  = ShippingAddressModel::$errCode;
        self::$errMsg   = ShippingAddressModel::$errMsg;
		return $res;
    }
}
?>