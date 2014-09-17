<?php
/*
 * 配货清单配货
 *add by:hws
 */
class pda_ScanPdaPickListView extends Pda_commonView{  
	//配货清单出库页面
	public function __construct() {
        parent::__construct();
    }
    public function view_pda_scanPickList(){
		//$this->smarty->template_dir = WEB_PATH.'pda/html/';
		//$this->smarty->assign('curusername', $_SESSION['userName']);
		$this->smarty->assign("action","配货清单配货");
		$this->smarty->display('pda_scanPdaPickList.htm');
    }
	
}