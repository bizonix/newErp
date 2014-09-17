<?php
/**
 * pda记录扫描
 * @author heminghua
 */
class pda_scanRecordView extends Pda_commonView{
    
    /*
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
    }
    public function view_pda_scanRecord(){
		$this->smarty->template_dir = WEB_PATH.'pda/html/';
		$this->smarty->assign("action","记录扫描");
		$this->smarty->display("pda_scanRecord.htm");
	}
	
}
