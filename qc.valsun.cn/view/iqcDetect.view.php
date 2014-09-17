<?php
/*
 * iqc检测
 */
class IqcDetectView extends BaseView{   
    
	//iqc检测
    public function view_iqcScan(){
		$this->smarty->assign('secnev','2');               //二级导航
		$this->smarty->assign('module','入库检测');
		$this->smarty->assign('username',$_SESSION['userName']);
		
		$navarr = array("<a href='index.php?mod=iqcDetect&act=iqcScan'>QC检测</a>",">>","入库检测");
        $this->smarty->assign('navarr',$navarr);
		$bad_reason = array(1=>'色差',
							2=>'破损',
							3=>'少配件',
							4=>'性能不良',
							5=>'脏污',
							6=>'码数错误',
							7=>'同边脚',
							9=>'做工问题',
							10=>'来货款式不一样',
							11=>'料号错误',
							12=>'滞销品');
		$this->smarty->assign('bad_reason',$bad_reason);
		$this->smarty->display('iqcScan.htm');
    }
	
	//iqc退件处理页面
    public function view_backScan(){
		$this->smarty->assign('secnev','2');               //二级导航
		$this->smarty->assign('module','退件处理');
		$this->smarty->assign('username',$_SESSION['userName']);
		
		$navarr = array("<a href='index.php?mod=iqcDetect&act=iqcScan'>QC检测</a>",">>","QC退件处理");
        $this->smarty->assign('navarr',$navarr);
		$bad_reason = array(1=>'色差',
							2=>'破损',
							3=>'少配件',
							4=>'性能不良',
							5=>'脏污',
							6=>'码数错误',
							7=>'同边脚',
							9=>'做工问题',
							10=>'来货款式不一样',
							11=>'料号错误',
							12=>'滞销品');
		$this->smarty->assign('bad_reason',$bad_reason);
		$this->smarty->display('iqcBackScan.htm');

    }
	
	//库存不良品处理
    public function view_stockScan(){
		$this->smarty->assign('secnev','2');               //二级导航
		$this->smarty->assign('module','常规检测');
		$this->smarty->assign('username',$_SESSION['userName']);
		
		$navarr = array("<a href='index.php?mod=iqcDetect&act=iqcScan'>QC检测</a>",">>","库存常规检测");
        $this->smarty->assign('navarr',$navarr);
		$bad_reason = array(1=>'色差',
							2=>'破损',
							3=>'少配件',
							4=>'性能不良',
							5=>'脏污',
							6=>'码数错误',
							7=>'同边脚',
							9=>'做工问题',
							10=>'来货款式不一样',
							11=>'料号错误',
							12=>'滞销品');
		$this->smarty->assign('bad_reason',$bad_reason);
		$this->smarty->display('stockScan.htm');
    }
}