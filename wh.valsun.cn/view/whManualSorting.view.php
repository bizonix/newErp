<?php
/*
 * 人工分拣
 * add: Herman.Xi
 * Time: 2014-08-18
 */
class WhManualSortingView extends Pda_commonView{
	//A仓位信息
    public function view_manualSortingList(){
		
		$toptitle = '手工分拣';
        $this->smarty->assign('toptitle', $toptitle);
		$this->smarty->assign('action', $toptitle);
		$this->smarty->assign('curusername', $_SESSION['userCnName']);
		
		$this->smarty->display('manualSortingList.htm');
    }
	
}