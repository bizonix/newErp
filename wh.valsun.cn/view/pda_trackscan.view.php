<?php
/**
 * pda页面
 * @author heminghua
 */
class pda_trackscanView extends Pda_commonView{
    
    /*
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
    }
    public function view_pda_trackscan(){
		$this->smarty->template_dir = WEB_PATH.'pda/html/';
		$this->smarty->assign("action","快递复核");
		$this->smarty->display("pda_trackscan.htm");
	}
	
}
