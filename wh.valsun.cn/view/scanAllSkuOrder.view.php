<?php
/*
 * 配货单常规出库
 *add by:hws
 */
class ScanAllSkuOrderView extends BaseView{  
	//配货清单出库页面
    public function view_scanOrder(){
		
		$this->smarty->assign('curusername', $_SESSION['userName']);
	
		$this->smarty->display('scanAllSkuOrder.htm');
    }

}