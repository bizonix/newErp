<?php
/**
 * pda页面
 * @author heminghua
 */
class pda_inventoryView extends Pda_commonView{
    
    /*
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
    }
    public function view_pda_inventory(){
		//$this->smarty->template_dir = WEB_PATH.'pda/html/';
		$state = isset($_GET['state'])?$_GET['state']:"";
		$type = isset($_GET['type'])?$_GET['type']:"";
		$reason = InvReasonModel::getInvReasonList("*","where storeId=1");
		$this->smarty->assign('reason', $reason);
		$reasonId = isset($_GET['reasonId'])?$_GET['reasonId']:"";
		$this->smarty->assign('reasonId', $reasonId);
		if($type=="error"){
			$this->smarty->assign("errorLog",$state);
		}else{
			$this->smarty->assign("successLog",$state);
		}
		$toptitle = '复核查询';
        $this->smarty->assign('toptitle', $toptitle);
		$this->smarty->assign('action', $toptitle);
		$this->smarty->display("pda_inventory.htm");
	}
	public function view_pda_sumbInv(){
		$InventoryAct = new InventoryAct();
		$reasonId = isset($_POST['reasonId'])?$_POST['reasonId']:"";
		
		$inv          = $InventoryAct->act_sumbInv();
		if(!$inv){
			$status = $InventoryAct->$errMsg;
			header("location:index.php?mod=pda_inventory&act=pda_inventory&type=error&reasonId={$reasonId}&state=".$status);exit;
		}
		header("location:index.php?mod=pda_inventory&act=pda_inventory&reasonId={$reasonId}&state=操作成功,请盘点下一料号");exit;
	}	
}
