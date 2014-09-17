<?php
include_once WEB_PATH."action/purchaseOrder.action.php";
$type   = $_GET["type"];
if($type=="stockIn"){//到货入库接口对接仓库系统
	$sku		= $_GET["sku"] ? $_GET["sku"] : '' ;//入库料号
	$amount 	= $_GET["amount"] ? $_GET["amount"] : '';//入库数量
	$rtn_data	= PurchaseOrderAct::act_stockIn($sku, $amount);
	return $rtn_data;
}else if($type=='patchOrder'){//采购补单接口对接仓库系统

}else if($type=='secondStockIn'){//二次录入

}else{
	
}
?>