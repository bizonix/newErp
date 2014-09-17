<?php
/**
 * pda页面 料号入库
 * @author heminghua
 */
class Pda_skuReturnView extends Pda_commonView{
    
    /*
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
    }
    public function view_pda_skuReturn(){
		$this->smarty->template_dir = WEB_PATH.'pda/html/';
		$this->smarty->assign("action","料号入库");
		$this->smarty->display("pda_skuReturn.htm");
	}
	
}
?>
