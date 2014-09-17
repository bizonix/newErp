<?php
/**
 * 装车扫描小包
 * @author chenxianyu
 */
class pda_orderLoadingView extends Pda_commonView {
    
    /*
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
    }
    public function view_pda_loading(){
		$toptitle = '装车扫描小包';
		$this->smarty->assign('toptitle', $toptitle);
		$this->smarty->assign('action', $toptitle);  
        $this->smarty->display('pda_orderLoading.htm');
    }
     public function view_pda_loading_express(){
		$toptitle = '装车扫描快递';
		$this->smarty->assign('toptitle', $toptitle);
		$this->smarty->assign('action', $toptitle);   
        $this->smarty->display('pda_loading_express.htm');
    }
}
?>