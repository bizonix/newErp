<?php
/*
 * 海外仓备货单PDA扫描出货记录
 */
class PdaPreGoodView extends Pda_commonView{
    /*
     * 构造函数
     */
    function __construct() {
        parent::__construct();
    }
    
    /*
     * 配货扫描页面
     */
    public function view_showScanPage(){
        $this->smarty->template_dir = WEB_PATH.'pda/html/';
        $this->smarty->assign("action","海外仓配货扫描");
        $this->smarty->display("pda_owscanpage.htm");
    }
    
    /*
     * 备货单复核扫描
    */
    public function view_orderRecheck(){
        $this->smarty->template_dir = WEB_PATH.'pda/html/';
        $this->smarty->assign("action","海外仓配货复核");
        $this->smarty->display("pda_owrecheckpage.htm");
    }
    
    /*
     * 装箱扫描
     */
    public function view_inboxScan(){
        $this->smarty->template_dir = WEB_PATH.'pda/html/';
        $this->smarty->assign("action","海外仓装箱扫描");
        $this->smarty->display("pda_owInboxpage.htm");
    }
    
    /*
     * 装箱复核
     */
    public function view_inboxReview(){
        $this->smarty->template_dir = WEB_PATH.'pda/html/';
        $this->smarty->assign("action","海外仓装箱复核");
        $this->smarty->display("pda_owInboxReview.htm");
    }
	
	/*
     * 箱号长、宽、高、重量录入
     */
    public function view_boxInfo(){
        $this->smarty->template_dir = WEB_PATH.'pda/html/';
        $this->smarty->assign("action","箱号信息录入");
        $this->smarty->display("pda_boxInInfo.htm");
    }
    
    /*
     * 发货扫描
     */
    public function view_sendScan(){
        $this->smarty->template_dir = WEB_PATH.'pda/html/';
        $this->smarty->assign("action","海外仓发货扫描");
        $this->smarty->display("pda_owSendScan.htm");
    }
    
	/*
     * 退箱扫描
     */
    public function view_returnbox(){
        $this->smarty->template_dir = WEB_PATH.'pda/html/';
        $this->smarty->assign("action","海外仓退箱扫描");
        $this->smarty->display("pda_owreturnboxpage.htm");
    }
}
