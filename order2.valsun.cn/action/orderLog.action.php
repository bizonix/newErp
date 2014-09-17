<?php
/**
 * 移动记录添加共用方法的编写
 * add by yxd
 **/
class OrderLogAct extends CheckAct{
	
	public function __construct(){
		parent::__construct();
	}
	
	/**
	 * @param orderId 订单编号ID
	 * 查询操作日志
	 */
	public function act_getOrderLogList(){
		return M('OrderLog')->getOrderLogList();
	}
}
?>