<?php
/**
 * pda快递配货扫描
 * @author heminghua
 */
class pda_expressScanView extends Pda_commonView {
    
    /*
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
    }
	public function view_pda_expressScan(){
		//$this->smarty->template_dir = WEB_PATH.'pda/html/';
		$toptitle = '快递配货扫描';
        $this->smarty->assign('toptitle', $toptitle);
		$this->smarty->assign('action', $toptitle);
		$this->smarty->display('pda_expressScan.htm');
	}
}
?>