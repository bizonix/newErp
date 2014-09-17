<?php
/**
 * Pda_waveReceiveView
 * 楼层分箱功能
 * @package 仓库系统
 * @author Gary
 * @copyright 2014.08.17
 * @version 1.0
 * @access public
 */
class Pda_waveReceiveView extends Pda_commonView {
	
	function view_index() {
	   $toptitle = '楼层分箱';
        $this->smarty->assign('toptitle', $toptitle);
		$this->smarty->assign('action', $toptitle);
	   $this->smarty->display('pda_waveReceive.htm');
	}
}
?>