<?php

/** 
 * @author heminghua
 * pda 复核扫描(非出库)
 */
class pda_expressrecheckView extends Pda_commonView{

    /**
     * 构造函数
     */
    public function __construct (){
        parent::__construct();
    }
    public function view_pda_expressrecheck(){
		//$this->smarty->template_dir = WEB_PATH.'pda/html/';
		$toptitle = '复核扫描';
        $this->smarty->assign('toptitle', $toptitle);
		$this->smarty->assign('action', $toptitle);
		$this->smarty->display('pda_expressrecheck.htm');
	}
}
?>	