<?php
/**
 * pdaҳ��
 * @author heminghua
 */
class pda_indexView extends Pda_commonView{
    
    /*
     * 
     */
    /*public function __construct() {
        parent::__construct();
        $this->smarty->template_dir = WEB_PATH.'pda/html/';
    }*/
    public function view_pda_index(){
		$toptitle = '深圳仓库';
        $this->smarty->assign('toptitle', $toptitle);
		$this->smarty->assign('action', $toptitle);
		$this->smarty->display("pda_index.htm");
	}
	public function view_pda_index0(){
		$toptitle = '仓库导航';
        $this->smarty->assign('toptitle', $toptitle);
		$this->smarty->assign('action', $toptitle);
		//$this->smarty->template_dir = WEB_PATH.'pda/html/';
		$this->smarty->display("pda_index0.htm");
	}
	public function view_pda_index1(){
		$toptitle = '配货操作';
        $this->smarty->assign('toptitle', $toptitle);
		$this->smarty->assign('action', $toptitle);
		//$this->smarty->template_dir = WEB_PATH.'pda/html/';
		$this->smarty->display("pda_index1.htm");
	}
	public function view_pda_index2(){
		//$this->smarty->template_dir = WEB_PATH.'pda/html/';
		$this->smarty->display("pda_index2.htm");
	}
	public function view_pda_index3(){
		$toptitle = '包装操作';
        $this->smarty->assign('toptitle', $toptitle);
		$this->smarty->assign('action', $toptitle);
		$this->smarty->display("pda_index3.htm");
	}
	public function view_pda_index4(){
		//$this->smarty->template_dir = WEB_PATH.'pda/html/';
		$toptitle = '退货操作';
        $this->smarty->assign('toptitle', $toptitle);
		$this->smarty->assign('action', $toptitle);
		$this->smarty->display("pda_index4.htm");
	}
	public function view_pda_index5(){
		//$this->smarty->template_dir = WEB_PATH.'pda/html/';
		$toptitle = 'B仓操作';
        $this->smarty->assign('toptitle', $toptitle);
		$this->smarty->assign('action', $toptitle);
		$this->smarty->display("pda_index5.htm");
	}
	
	/*
	 * 海外仓补货页面
	 */
	public function view_pda_index6(){
	   $toptitle = '海外仓补货';
        $this->smarty->assign('toptitle', $toptitle);
		$this->smarty->assign('action', $toptitle);
	    $this->smarty->display("pda_index6.htm");
	}
	
    /**
     * pda_indexView::view_pda_index7()
     * 仓库调拨
     * @return void
     */
    public function view_pda_index7(){
		$toptitle = '仓库调拨';
        $this->smarty->assign('toptitle', $toptitle);
		$this->smarty->assign('action', $toptitle);
		//$this->smarty->template_dir = WEB_PATH.'pda/html/';
		$this->smarty->display("pda_index7.htm");
	}
	
	public function view_pda_index8(){
		$toptitle = '装车扫描';
        $this->smarty->assign('toptitle', $toptitle);
		$this->smarty->assign('action', $toptitle);
		//$this->smarty->template_dir = WEB_PATH.'pda/html/';
		$this->smarty->display("pda_index8.htm");
	}
	
	/*
	 * 分拣操作
	 */
	public function view_pda_index9(){
	   $toptitle = '分拣操作';
        $this->smarty->assign('toptitle', $toptitle);
		$this->smarty->assign('action', $toptitle);
	    $this->smarty->display("pda_index9.htm");
	}
	
	/*
	 * 复核操作
	 */
	public function view_pda_index10(){
	   $toptitle = '复核操作';
        $this->smarty->assign('toptitle', $toptitle);
		$this->smarty->assign('action', $toptitle);
	    $this->smarty->display("pda_index10.htm");
	}
	
	/*
	 * 出库操作
	 */
	public function view_pda_index11(){
	   $toptitle = '出库操作';
        $this->smarty->assign('toptitle', $toptitle);
		$this->smarty->assign('action', $toptitle);
	    $this->smarty->display("pda_index11.htm");
	}
	
	/*
	 * 分区操作
	 */
	public function view_pda_index12(){
	   $toptitle = '分区操作';
        $this->smarty->assign('toptitle', $toptitle);
		$this->smarty->assign('action', $toptitle);
	    $this->smarty->display("pda_index12.htm");
	}
	/*
	 * 复核操作
	 */
	public function view_pda_index13(){
	   $toptitle = '复核操作';
        $this->smarty->assign('toptitle', $toptitle);
		$this->smarty->assign('action', $toptitle);
	    $this->smarty->display("pda_index13.htm");
	}
}