<?php
/**
 * pda移库操作
 * @author hws
 */
class Pda_packageCheckView extends Pda_commonView {
    
    /*
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
    }
	public function view_pda_packageCheck(){
		$this->smarty->template_dir = WEB_PATH.'pda/html/';
		$this->smarty->assign("action","点货调整");
		$this->smarty->display('pda_packageCheck.htm');
	}
}
?>