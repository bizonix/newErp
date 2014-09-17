<?php
/**
 * 发货组复核
 * @author chenxianyu
 */
class Pda_shippingGroupReviewView extends Pda_commonView {
    
    /*
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
    }
    public function view_pda_shippingGroupReview(){
		$toptitle = '发货组复核';
		$this->smarty->assign('toptitle', $toptitle);
		$this->smarty->assign('action', $toptitle); 
        $this->smarty->display('pda_shippingGroupReview.htm');
    }
    
}
?>