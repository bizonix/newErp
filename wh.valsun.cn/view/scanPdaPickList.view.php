<?php
/*
 * 配货清单配货
 *add by:hws
 */
class ScanPdaPickListView extends BaseView{  
	//配货清单出库页面
    public function view_scanPickList(){
		
		$this->smarty->assign('curusername', $_SESSION['userName']);
	
		$this->smarty->display('scanPdaPickList.htm');
    }
	
}