<?php
/*
 * iqc检测
 */
class IqcDetectView extends BaseView{   
    
	//iqc检测
    public function view_iqcScan(){
		$this->smarty->assign('secnev','2');               //二级导航
		$this->smarty->assign('module','iqc检测');
		$this->smarty->assign('username',$_SESSION['userName']);
		
		$navarr = array("<a href='index.php?mod=iqcDetect&act=iqcScan'>iqc检测</a>",">>","iqc检测");
        $this->smarty->assign('navarr',$navarr);
		
		$this->smarty->display('iqcScan.html');
    }
	
	//iqc退件处理页面
    public function view_backScan(){
		$this->smarty->assign('secnev','2');               //二级导航
		$this->smarty->assign('module','iqc退件处理');
		$this->smarty->assign('username',$_SESSION['userName']);
		
		$navarr = array("<a href='index.php?mod=iqcDetect&act=iqcScan'>iqc检测</a>",">>","iqc退件处理");
        $this->smarty->assign('navarr',$navarr);
		
		$this->smarty->display('iqcBackScan.html');

    }
	
	//库存不良品处理
    public function view_stockScan(){
		$this->smarty->assign('secnev','2');               //二级导航
		$this->smarty->assign('module','iqc退件处理');
		$this->smarty->assign('username',$_SESSION['userName']);
		
		$navarr = array("<a href='index.php?mod=iqcDetect&act=iqcScan'>iqc检测</a>",">>","库存不良品处理");
        $this->smarty->assign('navarr',$navarr);
		
		$this->smarty->display('stockScan.html');
    }
}