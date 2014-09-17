<?php
/**
 * PDA快递称重
 * @author chenxianyu
 * add 2014-8-18
 */
class pda_ExpressWeighingView extends Pda_commonView {
    
    /*
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
    }
    public function view_pda_ExpressWeighing(){
		$toptitle = '快递包装';
        $this->smarty->assign('toptitle', $toptitle);
		$this->smarty->assign('action', $toptitle); 
        $this->smarty->display('pda_ExpressWeighing.htm');
    }
    
}
?>