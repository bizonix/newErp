<?php
/**
 * 快递跟踪号与发货单号绑定
 * @author chenxianyu
 * add 2014-9-3
 */
class Pda_TrackingView extends Pda_commonView {
    
    /*
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
    }
    public function view_pda_tracking(){
		$toptitle = '快递跟踪号绑定';
        $this->smarty->assign('toptitle', $toptitle);
		$this->smarty->assign('action', $toptitle);
        $this->smarty->display('pda_tracking.htm');
    }
    
}
?>