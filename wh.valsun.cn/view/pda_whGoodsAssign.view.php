<?php
/**
 * pda_whGoodsAssignView
 * 调拨出库pda页面
 * @package 仓库系统
 * @author Gary
 * @copyright 2014
 * @version $Id$
 * @access public
 */
class pda_whGoodsAssignView extends Pda_commonView{  
	//配货清单出库页面
	public function __construct() {
        parent::__construct();
    }
    
    /**
     * pda_whGoodsAssignView::view_pda_inStoreOperate()
     * 调拨入库操作
     */
    public function view_pda_inStoreOperate(){
        $this->smarty->assign("action","调拨入库操作");
		$this->smarty->display('pda_inStoreOperate.htm');
    }
    
    /**
     * pda_whGoodsAssignView::view_pda_outStoreOperate()
     * 调拨入库操作
     */
    public function view_pda_outStoreOperate(){
        $this->smarty->assign("action","调拨出库操作");
		$this->smarty->display('pda_outStoreOperate.htm');
    }
    
    /**
     * pda_whGoodsAssignView::view_pda_whSkuOut()
     * 调拨清单出库
     * @return void
     */
    public function view_pda_whSkuOut(){
		$this->smarty->assign("action","调拨清单配货");
		$this->smarty->display('pda_whSkuOut.htm');
    }
    
    /**
     * pda_whGoodsAssignView::view_pda_skuOutCheck()
     * 调拨清单出库复核
     * @return void
     */
    public function view_pda_skuOutCheck(){
		$this->smarty->assign("action","调拨出库复核");
		$this->smarty->display('pda_skuOutCheck.htm');
    }
    
    /**
     * pda_whGoodsAssignView::view_pda_SkuOut()
     * 调拨清单出库
     * @return void
     */
    public function view_pda_SkuOut(){
        $this->smarty->assign("action","调拨清单出库");
		$this->smarty->display('pda_skuOut.htm');
    }
    
    /**
     * pda_whGoodsAssignView::view_pda_inSkuCheck()
     * 调拨清单接收复核
     * @return void
     */
    public function view_pda_inSkuCheck(){
		$this->smarty->assign("action","调拨清单接收");
		$this->smarty->display('pda_inSkuCheck.htm');
    }
    
    /**
     * pda_whGoodsAssignView::view_pda_whAssignSkuReturn()
     * 调拨退库操作
     * @return void
     */
    public function view_pda_whAssignSkuReturn(){
        $this->smarty->assign("action","调拨退库");
		$this->smarty->display('pda_whAssignSkuReturn.htm');
    }
    /**
     * pda_whGoodsAssignView::view_pda_whAssignSkuReturn()
     * 调拨退库操作
     */
    public function view_pda_whEndAssign(){
        $this->smarty->assign("action","完结调拨单");
		$this->smarty->display('pda_whEndAssign.htm');
    }
    /**
     * pda_whGoodsAssignView::view_pda_whAssignSkuReturn()
     * 调拨退库操作
     */
    public function view_pda_whShelfB(){
        $this->smarty->assign("action","B仓调拨入库");
		$this->smarty->display('pda_goodsAssignWhselfB.htm');
    }
    
    /**
     * pda_whGoodsAssignView::view_pda_makeAssignList()
     * pda生成调拨清单
     */
    public function view_pda_makeAssignList(){
        $this->smarty->assign("action","生成调拨清单");
		$this->smarty->display('pda_makeAssignList.htm');
    }
	
}