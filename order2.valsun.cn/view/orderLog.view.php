<?php
/**
* 订单流程管理
*add by:yxd
*/
class OrderLogView extends BaseView{
	/**
	 * 
	 * 构造函数
	 */
	public function __construct() {
		parent::__construct();
	}
	
	public function view_index(){
		//面包屑
		f('order');
    	$navlist = array (
    			array ('url' => 'index.php?mod=Platform&act=index', 'title' => '系统设置'),
    			array ('url' => '#', 'title' => '订单流程管理'),
    	);
    	$this->smarty->assign('toplevel', 3);
    	$this->smarty->assign('secondlevel', '39');
    	$this->smarty->assign('navlist', $navlist);
    	$this->smarty->assign('toptitle', '订单流程管理');
    	$this->smarty->assign('orderOperationLogArr', A('OrderLog')->act_getOrderLogList()); //循环列表
    	$this->smarty->display("statusMenuindex.htm");
	}
}
?>