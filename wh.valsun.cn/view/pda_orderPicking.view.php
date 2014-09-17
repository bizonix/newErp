<?php
/**
 * pda页面
 * @author heminghua
 */
class pda_orderPickingView extends Pda_commonView{
    
    /*
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
    }
    public function view_pda_orderPicking(){
		$this->smarty->template_dir = WEB_PATH.'pda/html/';
		$this->smarty->assign("action","配货操作");
		$this->smarty->display("pda_orderPicking.htm");
	}
}