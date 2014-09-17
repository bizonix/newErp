<?php
/**
 * pda页面
 * @author heminghua
 */
class pda_whShelfView extends Pda_commonView{
    
    /*
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
    }
    
    public function view_pda_inStore(){
		$toptitle = '入库操作';
        $this->smarty->assign('toptitle', $toptitle);
		$this->smarty->assign('action', $toptitle);
		$this->smarty->display("pda_inStore.htm");
	}
    
    public function view_pda_whShelf(){
		$toptitle = '上架操作';
        $this->smarty->assign('toptitle', $toptitle);
		$this->smarty->assign('action', $toptitle);
		$data       = isset($_GET['data'])?$_GET['data']:"";
        $storeId    =   intval(trim($_GET['storeId']));
        $storeId    =   $storeId ? $storeId : 1;
		$this->smarty->assign("successLog",$data);
        $this->smarty->assign('storeId', $storeId);
		$this->smarty->display("pda_whShelf.htm");
	}
	
	public function view_pda_whShelfB(){
		$toptitle = 'B仓上架操作';
        $this->smarty->assign('toptitle', $toptitle);
		$this->smarty->assign('action', $toptitle);
		$this->smarty->display("pda_whShelfB.htm");
	}
    
    public function view_pda_package(){
		$toptitle = '包材入库';
        $this->smarty->assign('toptitle', $toptitle);
		$this->smarty->assign('action', $toptitle);
		$this->smarty->display("pda_package.htm");
	}
    /** PDA盘点查询 Gary**/
    public function view_pda_inventorySearch(){
		$toptitle = '盘点查询';
        $this->smarty->assign('toptitle', $toptitle);
		$this->smarty->assign('action', $toptitle);
		$this->smarty->display("pda_inventorySearch.htm");
	}
    
    public function view_pda_contactPosition(){
		$toptitle = '关联仓位';
        $this->smarty->assign('toptitle', $toptitle);
		$this->smarty->assign('action', $toptitle);
		$this->smarty->display("pda_contactPosition.htm");
    }
}
