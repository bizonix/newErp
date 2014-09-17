<?php
/**
 * 订单分区复核
 * @author chenxianyu
 */
class pda_PartitionCheckingView extends Pda_commonView {
    
    public function view_pda_partitionChecking(){
		$toptitle = '分区复核';
		$this->smarty->assign('toptitle', $toptitle);
		$this->smarty->assign('action', $toptitle);    
        $this->smarty->display('pda_partitionChecking.htm');
    }
    
}
?>