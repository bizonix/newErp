<?php
/**
 * pda移库操作
 * @author hws
 */
class Pda_shiftLibraryView extends Pda_commonView {
    
    /*
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
    }
	public function view_pda_shiftLibrary(){
		//$this->smarty->template_dir = WEB_PATH.'pda/html/';
		$toptitle = '移库操作';
        $this->smarty->assign('toptitle', $toptitle);
		$this->smarty->assign('action', $toptitle);
		$this->smarty->display('pda_shiftLibrary.htm');
	}
}
?>