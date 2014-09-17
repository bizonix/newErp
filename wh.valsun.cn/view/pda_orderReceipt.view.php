<?php
/**
 * 配货收货
 * @author czq
 */
class pda_orderReceiptView extends Pda_commonView{
    
    /*
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * 配货收货view层
     * @author czq
     * @date 2014.07.23
     */
    public function view_pda_orderReceipt(){
		$toptitle = "PDA配货收货";
		$this->smarty->assign('toptitle', $toptitle);
		$this->smarty->assign('action', $toptitle);
		$this->smarty->template_dir = WEB_PATH.'pda/html/';
		$this->smarty->display("pda_orderReceipt.htm");
	}
	
	public function view_pda_orderPickRoute(){
		$toptitle = "PDA收货路由";
		$this->smarty->assign('toptitle', $toptitle);
		$this->smarty->assign('action', $toptitle);
		$this->smarty->template_dir = WEB_PATH.'pda/html/';
		$this->smarty->display("pda_orderPickRoute.htm");
	}
}
