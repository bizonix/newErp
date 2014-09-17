<?php
/**
 * pda B仓订单出库操作
 * @author Gary
 */
class Pda_orderOutBView extends Pda_commonView {
    
    /*
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
    }
    /** B仓订单配货出库**/
	public function view_pda_orderOutB(){
		$this->smarty->assign("action","B仓订单配货");
		$this->smarty->display('pda_orderOutB.htm');
	}
    
    /** B仓订单接收复核**/
    public function view_pda_orderInCheckB(){
		$this->smarty->assign("action","B仓订单接收复核");
		$this->smarty->display('pda_orderInCheckB.htm');
	}
}
?>