<?php
class UnusualOrderView extends BaseView{
	public function view_index(){
	 	global $mod,$act;
        $this->smarty->assign('title','异常到货处理');
        $this->smarty->assign('mod',$mod);//模块权限
		$order 		= new PurchaseOrderAct();
		$purtowh    = new PurToWhAct();
		$listInfo 	= $order->getUnnormalSkuReach();
        $perNum 	= 100; 
		$totalNum 	= $listInfo["totalNum"];
		$list 		= $listInfo["skuInfo"];
		//$purtowh->updUnusualSkuConfirmQty($list);//重新计算未处理状态下的待确认数量
		$pageobj 	= new Page($totalNum, $perNum);
		$pageStr 	= $pageobj->fpage();
		$newInfo 	= $order->getUnnormalSkuReach();//获取更新后的数据
		//$purchaseList	= CommonAct::actGetPurchaseList();
		$purchaseList = getPurchaseUserList();
        $this->smarty->assign('purchaseList',$purchaseList);//采购列表 
		$this->smarty->assign('pageStr', $pageStr);//分页输出
		$this->smarty->assign('userid', $_SESSION['userId']);//登录用户userid
		$this->smarty->assign('list', $newInfo['skuInfo']);//循环赋值
		$this->smarty->display('unusualOrder.htm');
	}


	public function view_list(){
	 	global $mod,$act;
        $this->smarty->assign('title','到货查询');
		$skuItem = new SkuAct();
		$listInfo = $skuItem->show_reach_list();
        $perNum = 100; 
		$totalNum = $listInfo["totalNum"];
		$list = $listInfo["skuInfo"];
		$pageobj = new Page($totalNum, $perNum);
		$pageStr = $pageobj->fpage();
		$this->smarty->assign('pageStr', $pageStr);//分页输出
		$this->smarty->assign('list', $list);//循环赋值
		$this->smarty->display('reach_list.htm');
	}
}
?>
