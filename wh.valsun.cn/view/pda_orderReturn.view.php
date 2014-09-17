<?php
/**
 * pda页面 异常退货
 * @author heminghua
 */
class pda_orderReturnView extends Pda_commonView{
    
    /*
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
    }
    public function view_pda_orderReturn(){
		$this->smarty->template_dir = WEB_PATH.'pda/html/';
		$this->smarty->assign("action","异常退货");
		$this->smarty->display("pda_orderReturn.htm");
	}
	
}
?>
