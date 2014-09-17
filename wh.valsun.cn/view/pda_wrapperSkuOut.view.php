<?php
/**
 * 包材出库
 * @author heminghua
 */
class pda_wrapperSkuOutView extends Pda_commonView{
    
    /*
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
    }
    public function view_pda_wrapperSkuOut(){
		$this->smarty->template_dir = WEB_PATH.'pda/html/';
		$this->smarty->assign("action","包材出库");
		$this->smarty->display("pda_wrapperSkuOut.htm");
	}
	
}
