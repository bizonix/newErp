<?php
/*
 * 订单信息查询
 * @author herman.xi
 * @modify by lzx ,date 20140603
 */
class orderModifyView extends BaseView {
	
    public function __construct() {
    	parent::__construct();
    }
    
    /**
     * 订单详情页面(ps:订单详情扩展的没有)
     */
    public function view_index(){
        $fullUnshippedOrderData = A('Order')->act_getFullUnshippedOrderByGetId();
        $this->smarty->assign('order', $fullUnshippedOrderData['order']);
        $this->smarty->assign('orderExtension', $fullUnshippedOrderData['orderExtension']);
        $this->smarty->assign('orderUserInfo', $fullUnshippedOrderData['orderUserInfo']);
        $this->smarty->assign('orderWarehouse', $fullUnshippedOrderData['orderWarehouse']);
        $this->smarty->assign('orderDetail', $fullUnshippedOrderData['orderDetail']);
        $this->smarty->assign('orderNote', $fullUnshippedOrderData['orderNote']);
        $this->smarty->assign('orderTracknumber', $fullUnshippedOrderData['orderTracknumber']);
        /*
        $toplevel = 2;      //一级菜单的序号
        $this->smarty->assign('toplevel', $toplevel);
        $secondlevel = 25;   //当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);   
        */
        $this->smarty->assign('toptitle', '订单编辑');
		$this->smarty->assign('curusername', get_username());//SESSION['userName']
		$this->smarty->display('orderModify.htm');
    }
    
    /**
     * 标记已处理
     * @author yxd
     */
    public function view_setOperated(){
    	$this->ajaxReturn(A('OrderModify')->act_setOperated(), A('OrderModify')->act_getErrorMsg());
    }
    /**
     * 编辑订单发货信息
     */
    public function view_updateOrderMailInfo(){
    	
    }
    
	/**
     * 编辑订单运输方式和包材
     */
    public function view_updateOrderShipInfo(){
        $this->ajaxReturn(A('orderModify')->act_updateOrderShipInfo(), A('orderModify')->act_getErrorMsg());
    }
    
	/**
     * 编辑订单客户联系方式
     */
    public function view_updateOrderUserContact(){
        $this->ajaxReturn(A('orderModify')->act_updateOrderUserContact(), A('orderModify')->act_getErrorMsg());
    }

    /**
     * 编辑订单金额和订单运费金额
     */
    public function view_updateOrderMoney(){
    	
    }
    
	/**
     * 编辑订单跟踪号包括添加跟踪号
     */
    public function view_updateOrderTrack(){
    	
    }
    
	/**
     * 编辑订单详细
     */
    public function view_updateOrderdetail(){

    }
    
	/**
     * 添加订单详细
     */
    public function view_addOrderdetail(){
    	
    }
    
	/**
     * 添加备注
     */
    public function view_addOrderNote(){
    	$this->ajaxReturn(A('orderModify')->act_addOrderNote(),A('orderModify')->act_getErrorMsg());
    }

    /**
     * 获取订单全部信息
     */
    public function view_getOrderList(){
        $this->ajaxReturn(A('orderModify')->act_getOrderList(), array('200'=>get_promptmsg(200,'获取客户的详情')));
    }

    public function view_getOrderLogs(){
        $this->ajaxReturn(A('orderModify')->act_getOrderLogs(),A('orderModify')->act_getErrorMsg());
    }

    public function view_changePlatformId(){
        $this->ajaxReturn(A('orderModify')->act_changePlatformId(),A('orderModify')->act_getErrorMsg());
    }

    public function view_getOrderDetail(){

    }

    /**
     * 订单明细
     * 添加,修改,删除订单详情
     */
    public function view_changeOrderDetail(){
        $this->ajaxReturn(A('orderModify')->act_changeOrderDetail(),A('orderModify')->act_getErrorMsg());
    }
}   