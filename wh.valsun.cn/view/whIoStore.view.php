<?php
/*
 * 出入库单View
 */
class WhIoStoreView extends CommonView {

	/**
	 * 渲染入库单列表
	 */
	public function view_getWhInStoreList() {
		
		$whIoStoreAct = new WhIoStoreAct();
		
		$Inlists = $whIoStoreAct->act_getInStoreList();
		$pageinfo = $Inlists['pageinfo'];
		unset($Inlists['pageinfo']);
		$typelist = $Inlists['typelist'];
		unset($Inlists['typelist']);

		//面包屑
		$navlist 		= array(           
								array('url'=>'','title'=>'单据业务'),
								array('url'=>'','title'=>'入库单'),
						   );
		$this->smarty->assign('toplevel', 3);
        $this->smarty->assign('secondlevel', 33);
		$this->smarty->assign('ioSearchName', '入库单类型');
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('show_page', $pageinfo['show_page']);
		$this->smarty->assign('invoiceTypeList', $typelist);
		$this->smarty->assign('ioType', 1);
		$this->smarty->assign('InStoreLists', $Inlists); //循环列表
		$this->smarty->display("whIoStore.htm");
	}
	
	/**
	 * 渲染出库单列表
	 */
	public function view_getWhOutStoreList() {
		
		$whIoStoreAct = new WhIoStoreAct();
		
		$Inlists = $whIoStoreAct->act_getOutStoreList();
		$pageinfo = $Inlists['pageinfo'];
		unset($Inlists['pageinfo']);
		$typelist = $Inlists['typelist'];
		unset($Inlists['typelist']);

		//面包屑
		$navlist 		= array(           
								array('url'=>'','title'=>'单据业务'),
								array('url'=>'','title'=>'出库单'),
						   );
		$this->smarty->assign('toplevel', 3);
        $this->smarty->assign('secondlevel', 32);
		$this->smarty->assign('ioSearchName', '出库单类型');
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('show_page', $pageinfo['show_page']);
		$this->smarty->assign('invoiceTypeList', $typelist);
		$this->smarty->assign('ioType', 2);
		$this->smarty->assign('InStoreLists', $Inlists); //循环列表
		$this->smarty->display("whIoStore.htm");
	}
    
    /**
     * 渲染对应审核人员待审核入库单审核列表
     */
	public function view_getAuditInStoreList(){
		$userid = $_SESSION['userId'];
        $whIoStoreAct = new WhIoStoreAct();
		$Inlists = $whIoStoreAct->act_getAuditInStoreList($userid);

		//面包屑
		$navlist 		= array(           
								array('url'=>'','title'=>'单据业务'),
								array('url'=>'','title'=>'待审核入库单'),
						   );
		$this->smarty->assign('toplevel', 3);
        $this->smarty->assign('secondlevel', 35);
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('ioType', 1);
		$this->smarty->assign('InStoreLists', $Inlists); //循环列表
		$this->smarty->display("whIoPendingStore.htm");
        
	}
	
	/**
     * 渲染对应审核人员待审核出库单审核列表
     */
	public function view_getAuditOutStoreList(){
        $userid = $_SESSION['userId'];
        $whIoStoreAct = new WhIoStoreAct();
		$Inlists = $whIoStoreAct->act_getAuditOutStoreList($userid);

		//面包屑
		$navlist 		= array(           
								array('url'=>'','title'=>'单据业务'),
								array('url'=>'','title'=>'待审核出库单'),
						   );
		$this->smarty->assign('toplevel', 3);
        $this->smarty->assign('secondlevel', 34);
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('ioType', 2);
		$this->smarty->assign('InStoreLists', $Inlists); //循环列表
		$this->smarty->display("whIoPendingStore.htm");
        
	}

}