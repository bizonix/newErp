<?php
/**
 * 调整采购订单相关接口适应海外仓下采购订单
 * 不改变之前的
 * add by xiaojinhua
 */

class OrderApiAct{
	public static $dbConn;
	public static $errCode		= 0;
	public static $errMsg		= "";

	public function addNewOrder(){
		global $dbConn;
		$skulist 	= $_POST['skulist'];
		//$purchaseId = $_SESSION[C('USER_AUTH_SYS_ID')];//采购员ID
		$purchaseId = $skulist[0]["cguserId"];//采购员ID
		$comid      = $_SESSION[C('USER_COM_ID')];//公司ID
		$skuComObj = new CommonAct(); //重新计算这个sku 的已订购数量
		$purchaseOrder = new PurchaseOrderAct();
		$rollback   = false;
		foreach($skulist as $sku){
			$price      = $sku['price'];//SKU单价
			$partnerId  = $purchaseOrder->getPartnerId($sku['sku']);//供应商ID
			$partnerId = $partnerId['partnerId'];
			$storeid   = 1;//仓库ID
			$orderData = $purchaseOrder->getOrderSN( $partnerId, $purchaseId);//判断同供应商、采购员跟踪号是否已存在
			$orderSN = $orderData['recordnumber'];
			$main = array();
			$detail = array();
			if(!empty($orderSN)){//存在符合条件的跟踪号，直接插入采购订单明细
				$detail['sku']    = $sku['sku'];
				$detail['price']  = $price;//单价
				$detail['count']  = $sku['rec'];//采购数量
				$detail['goods_recommend_count']  = $sku['rec'];//采购数量
				$dataSet = array2sql($detail);
				$sql = "insert into ph_order_detail set {$dataSet}  ";
				$dbConn->execute($sql);
			}else{//不存在符合条件的跟踪号重新生成
				$recordnumber = PurchaseOrderModel::autoCreateOrderSn($purchaseId, $comid);//生成对应公司的采购订单跟踪号
				if(!empty ($recordnumber)) {//生成采购订单号成功
					$main['recordnumber'] 		= $recordnumber;//跟踪号
					$main['purchaseuser_id'] 	= $purchaseId;//采购员ID
					$main['warehouse_id'] 		= $storeid;//仓库ID
					$main['partner_id'] 		= $partnerId;//供应商ID
					$main['company_id'] 		= $comid;//公司编号
					$dataSet = array2sql($main);
					$sql = "insert into ph_order set {$dataSet}  ";
					$dbConn->execute($sql);
					if($rtnmain) {//主订单添加成功
						$detail['sku']    = $sku['sku'];
						$detail['price']  = $price;//单价
						$detail['count']  = $sku['rec'];//采购数量
						$detail['goods_recommend_count']  = $sku['rec'];//采购数量
						$detail['recordnumber'] = $recordnumber;
						$dataSet = array2sql($detail);
						$sql = "insert into ph_order_detail set {$dataSet}  ";
						$dbConn->execute($sql);
						$skuComObj->calcAlert($detail['sku']); //重新计算已订购数量
					}
				}
			}
		}
	}
}
?>
