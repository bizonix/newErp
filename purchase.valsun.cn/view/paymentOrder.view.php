<?php
class PaymentOrderView extends BaseView {

       public function __construct(){
    	// 继承父类，并分配模块通用的变量:mod,act,WEB_API，WEB_URL
    		parent:: __construct();
    		if(isset($_GET["mod"]) && !empty($_GET["mod"])){
                $mod=$_GET["mod"];
        	}
        	if(isset($_GET["act"]) && !empty($_GET["act"])){
        		$act=$_GET["act"];
        	}
    		$this->smarty->assign('act',$act);//模块权限
    		$this->smarty->assign('mod',$mod);//模块权限
    		$this->smarty->caching 		= false;
    		$this->smarty->debugging 	= false;
    		$this->smarty->assign("WEB_API", WEB_API);
    		$this->smarty->assign("WEB_URL", WEB_URL);
    	}

    	public function view_index(){
    		$searchWhere = "1";
    		$flag1 = false;
    		$flag2 = false;
    		$searchGet = array_map("trim",$_GET);
    		if(isset($searchGet["type"]) && !empty($searchGet["type"])){
    			$this->smarty->assign("type",$searchGet["type"]);
    			$flag1 = true;
    		}
    		if(isset($searchGet["keyWord"]) && !empty($searchGet["keyWord"])){
    			$this->smarty->assign("keyWord",$searchGet["keyWord"]);
    			$flag2 = true;
    		}
    		if($flag1 && $flag2){
    			if($searchGet["type"] == "sku"){
    				$searchWhere .= ' AND pd.'.$searchGet["type"].'="'.$searchGet["keyWord"].'"';
    			}else{
    				$searchWhere .= ' AND (po.'.$searchGet["type"].' like "'.$searchGet["keyWord"].'%"';
    				$searchWhere .= ' OR po.'.$searchGet["type"].' like "%'.$searchGet["keyWord"].'")';
    			}
    		}
    		$flag1 = false;
    		$flag2 = false;
    		$flag3 = false;
    		if(isset($searchGet["status"]) && !empty($searchGet["status"])){
    			$this->smarty->assign("status",$searchGet["status"]);
    			$flag1 = true;
    		}
    		if(isset($searchGet["starTime"]) && !empty($searchGet["starTime"])){
    			$this->smarty->assign("starTime",$searchGet["starTime"]);
    			$flag2 = true;
    			$startTime = strtotime($searchGet["starTime"]." 00:00:00 ");
    		}
    		if(isset($searchGet["endTime"]) && !empty($searchGet["endTime"])){
    			$this->smarty->assign("endTime",$searchGet["endTime"]);
    			$flag3 = true;
    			$endTime = strtotime($searchGet["endTime"]." 23:59:59 ");
    		}
    		if($flag1 && $flag2 && $flag3){
    			$searchWhere .= ' AND po.'.$searchGet["status"].'  BETWEEN "'.$startTime.'" AND "'.$endTime.'"';
    		}
    		if(isset($searchGet["search-pur"]) && !empty($searchGet["search-pur"])){
    			$this->smarty->assign("search_pur",$searchGet["search-pur"]);
    			$searchWhere .= ' AND po.purchaseuser_id = "'.$searchGet["search-pur"].'"';
    		}
			if(isset($searchGet["paystatus"]) && !empty($searchGet["paystatus"])){
				$this->smarty->assign("paystatus",$searchGet["paystatus"]);
				$searchWhere .= ' AND po.paystatus = "'.$searchGet["paystatus"].'"';
			}
			$powerlist 		= commonAct::actGetPurchaseAccess(); //获取采购订单显示权限
			$con = '';
			if($powerlist != ''){
				$powerinfo    = $powerlist['power_ids'];
				$searchWhere .= " AND po.purchaseuser_id in (".$powerinfo.")";
				$con         .= $powerinfo;
			}
    		$PO  			= new PaymentOrderAct();
    		$waitpay 	= $PO->countByStatus(2,$con);//等待付款
    		$haspay 	= $PO->countByStatus(3,$con);//已付款
    		$orderListPage 	= $PO->getOrderList($searchWhere);
    		$purchaseList	= CommonAct::actGetPurchaseList();
			$this->smarty->assign('purchaseList',$purchaseList);//采购列表
    		$this->smarty->assign("waitpay",$waitpay);
    		$this->smarty->assign("haspay",$haspay);
    		$this->smarty->assign("orderList",$orderListPage[0]);
    		$this->smarty->assign("fpage",$orderListPage[1]);
    		$this->smarty->display("paymentOrder.htm");
    	}
}
?>