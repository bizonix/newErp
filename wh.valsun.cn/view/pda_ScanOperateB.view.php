<?php
/*
 * B仓操作
 *add by:hws
 */
class pda_ScanOperateBView extends Pda_commonView{  
	//配货清单出库页面
	public function __construct() {
        parent::__construct();
    }
	
	//出库
    public function view_pda_scanPickList(){
		$this->smarty->template_dir = WEB_PATH.'pda/html/';
		$this->smarty->assign("action","提货单配货");
		$this->smarty->display('pda_scanPdaPickListB.htm');
    }
	
	//复核
	public function view_pda_reviewB(){
		$this->smarty->template_dir = WEB_PATH.'pda/html/';
		$this->smarty->assign("action","提货单复核");
		$this->smarty->display('pda_reviewB.htm');
    }
}