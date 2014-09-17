<?php
class TransportStrategyView extends BaseView {
	
	public function __construct() {
    	parent::__construct();
    }
    public function view_index(){
    	//面包屑
    	$navlist = array (
    			array ('url' => 'index.php?mod=Platform&act=index', 'title' => '系统设置'),
    			array ('url' => '#', 'title' => '运输方式策略管理'),
    	);
        F('order');
    	$perpage 	   = A('TransportStrategy')->act_getPerpage();
    	$ordercount    = A('Account')->act_getAccountCount();
    	$pageclass 	   = new Page($ordercount, $perpage, '', 'CN');
    	$pageformat    = $ordercount>$perpage ? array(0,1,2,3,4,5,6,7,8,9) : array(0,1,2,3,4);
    	$this->smarty->assign('toplevel',A('Topmenu')->act_getToplevel('transportStrategy') );
		$this->smarty->assign('secondlevel',A('Topmenu')->act_getSecondlevel('transportStrategy'));
    	$this->smarty->assign('navlist', $navlist);
    	$this->smarty->assign('toptitle', '运输方式策略管理');
    	$this->smarty->assign('accountList', A('TransportStrategy')->act_getTransportStrategyLists()); //循环列表
    	$this->smarty->assign('show_page', $pageclass->fpage($pageformat));
        $this->smarty->assign('accountListSearch', M('Account')->getAccountAll());
        $this->smarty->assign('channelIdArr', M('InterfaceTran')->getChannelList());
        $this->smarty->assign('transportIdArr', M('InterfaceTran')->key('id')->getCarrierList(2));
        $this->smarty->assign('priorityList', C('TRANSPORT_STRATEGY_PRIORITY'));
        $this->smarty->assign('currencyList', C('TRANSPORT_STRATEGY_CURRENCY_ARRAY'));//币种列表
        $this->smarty->assign('tranCountryList', M('InterfaceTran')->getAllCountryList());
        //print_r(M('InterfaceTran')->getCarrierList(2));exit;
    	$this->smarty->display("transportStrategyIndex.htm");
    }
    
    public function view_editConstraintType(){
    	$this->ajaxReturn(A('TransportStrategy')->act_editConstraintType(), A('TransportStrategy')->act_getErrorMsg());
    }
    
    public function view_editConditionTransport(){
    	$this->ajaxReturn(A('TransportStrategy')->act_editConditionTransport(), A('TransportStrategy')->act_getErrorMsg());
    }
    
    public function view_editConditionAmount(){
    	$this->ajaxReturn(A('TransportStrategy')->act_editConditionAmount(), A('TransportStrategy')->act_getErrorMsg());
    }
    
    public function view_editConditionCountry(){
    	$this->ajaxReturn(A('TransportStrategy')->act_editConditionCountry(), A('TransportStrategy')->act_getErrorMsg());
    }
    
    public function view_editConditionCurrency(){
    	$this->ajaxReturn(A('TransportStrategy')->act_editConditionCurrency(), A('TransportStrategy')->act_getErrorMsg());
    }
    
    public function view_delCondition(){
    	$this->ajaxReturn(A('TransportStrategy')->act_delCondition(), A('TransportStrategy')->act_getErrorMsg());
    }

}
?>