<?php
/*
 * iqc检测信息
 */
class IqcInfoView extends BaseView{
    
	//iqc检测信息
    public function view_iqcScanList(){
		$this->smarty->assign('secnev','3');               //二级导航
		$this->smarty->assign('module','SKU等待领取');
		$this->smarty->assign('username',$_SESSION['userName']);
		
		$navarr = array("<a href='index.php?mod=iqcInfo&act=iqcScanList'>iqc检测信息</a>",">>","iqc已检测信息");
        $this->smarty->assign('navarr',$navarr);
		
		$this->smarty->display('iqcScanList.html');
	}
	

}