<?php
/*
 * 发货地址管理action层页面 shippingAddressManage.action.php
 * ADD BY 陈伟 2013.7.26
 */
class ShippingAddressManageAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";
	public $where   =   "";
	/*
     * 构造函数 初始化数据库连接
     */
    public function __construct($where = '') {
        $this->where = $where;
    }
	
	/*
     * 发货地址管理数据调用->分页计算总条数
     */
	function  act_getShippingAddressListNum(){	
		//调用model层获取数据
		$shippingAddressManageModel = new shippingAddressManageModel();
		$num 				  =	$shippingAddressManageModel->getShippingAddressListNum();
		if($num){
			return $num;
		}else{
			return false;
		}
	}
	
	
	/*
     * 发货地址管理数据调用
     */
	function  act_shippingAddressManage($where=''){
		//调用model层获取数据
		$shippingAddressManageModel = new shippingAddressManageModel();
		$list =	$shippingAddressManageModel->shippingAddressList($where);
		if($list){
			return $list;
		}else{
			return false;
		}
	}	
	
	/*
     * 添加发货地址数据插入
     */
	function  act_shippingAddressAdd($shippingAddressSql,$sellerName,$name){
		//调用model层获取数据
		$shippingAddressManageModel = new shippingAddressManageModel();
		$list =	$shippingAddressManageModel->shippingAddressAdd($shippingAddressSql,$sellerName,$name);
		if($list){
			return $list;
		}else{
			return false;
		}
	}
	
	/*
     * 编辑发货地址显示数据
     */
	function  act_shippingAddressEdit($where){
		//调用model层获取数据
		$shippingAddressManageModel = new shippingAddressManageModel();
		$list =	$shippingAddressManageModel->shippingAddressEdit($where);
		if($list){
			return $list;
		}else{
			return false;
		}
	}
	
	/*
     * 编辑发货地址UPDATE
     */
	function  act_shippingAddressEditIn($shippingAddressArr,$where){
		//调用model层获取数据
		$shippingAddressManageModel = new shippingAddressManageModel();
		$list =	$shippingAddressManageModel->shippingAddressEditIn($shippingAddressArr,$where);
		if($list){
			return $list;
		}else{
			return false;
		}
	}
	
	/*
     * 删除发货地址
     */
	function  act_shippingAddressDel($where){
		//调用model层获取数据
		$shippingAddressManageModel = new shippingAddressManageModel();
		$list =	$shippingAddressManageModel->shippingAddressDel($where);
		if($list){
			return $list;
		}else{
			return false;
		}
	}
}
?>
