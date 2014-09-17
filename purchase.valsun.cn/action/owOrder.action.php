<?php
/**
 * 海外仓备货单相关信息
 * Enter description here ...
 * @author 王民伟
 *
 */
error_reporting(-1);
class OwOrderAct {
	static $errCode = 0;
	static $errMsg = "";
	/**
	 * 生成海外备货单,根据下单人合并订单明细
	 * Enter description here ...
	 */
	public function createOwOrder() {
		global $dbConn;
		$skulist 		= $_POST['skulist'];
		$operator_id 	= $_SESSION[C('USER_AUTH_SYS_ID')];//操作人员ID
		$comid      	= $_SESSION[C('USER_COM_ID')];//公司ID
		$type 			= $_POST['type'];
		$rollback   	= false;
		$existSku       = '';
		$ismark         = 0;
		BaseModel::begin();//开始事务
		foreach($skulist as $key=>$sku){
			$price      = PurchaseOrderModel::getPriceBySku($sku['sku']);//SKU单价
			$purid 		= OwOrderModel::getPurchaseidBySku($sku['sku']);
			$parid 		= OwOrderModel::getPartnerId($sku['sku']);//供应商ID
			$parid 		= $parid['partnerId'];
			$storeid 	= 1;//仓库ID
			$orderData  = OwOrderModel::getOwOrderNum($operator_id);//判断同操作员跟踪号是否已存在(未审核状态下)
			$orderSN 	= $orderData['recordnumber'];
			$main    	= array();
			$detail  	= array();
			if(!empty($orderSN)){//存在符合条件的跟踪号，直接插入采购订单明细
				/*
				$orderHasSku                        = OwOrderModel::orderExistSku($sku['sku']);
				if($orderHasSku){
					$existSku .= '['.$sku['sku'].'],';
					continue;
				}*/
				$detail['sku']    					= $sku['sku'];
				$detail['price']  					= $price;//单价
				$detail['count']  					= $sku['rec'];//采购数量
				$detail['goods_recommend_count']  	= $sku['rec'];//采购数量
				$detail['recordnumber'] 			= $orderData['recordnumber'];
				$poid  								= $orderData['id'] ;//根据跟踪号取采购主订单编号
				$detail['parid']                    = $parid;//料号供应商ID
				$detail['po_id'] 					= $poid;
				$dataSet 							= array2sql($detail);
				$sql 								= "insert into ph_ow_order_detail set {$dataSet}  ";
				$rtndetail 							= $dbConn->execute($sql);
				if($rtndetail === false){
					$rollback = true;
				}else{
					$ismark   = 1;
				}
				
			}else{//不存在符合条件的跟踪号重新生成	
				$recordnumber = PurchaseOrderModel::autoCreateOrderSn($purid, $comid);//生成对应公司的采购订单跟踪号
				if(!empty ($recordnumber)) {//生成采购订单号成功
					$main['recordnumber'] 		= $recordnumber;//跟踪号
					$main['purchaseuser_id'] 	= $purid;//采购员ID
					$main['operator_id'] 		= $operator_id;//操作人员id
					$main['warehouse_id'] 		= $storeid;//仓库ID
					$main['company_id'] 		= $comid;//公司编号
					$main['partner_id'] 		= $parid;//供应商ID
					$main['addtime'] = time();
					if($type == "oversea"){
						$main['order_type'] = 5; // 给海外仓备货的订单
					}else{
						$main['order_type'] = 1; // 正常订单
					}
					$dataSet 	= array2sql($main);
					$sql 		= "insert into ph_ow_order set {$dataSet}  ";
					$rtnmain 	= $dbConn->execute($sql);
					if($rtnmain) {//主订单添加成功
						/*
						$orderHasSku                        = OwOrderModel::orderExistSku($sku['sku']);
						if($orderHasSku){
							$existSku .= '['.$sku['sku'].'],';
							continue;
						}*/
						$detail['sku']    					= $sku['sku'];
						$detail['price']  					= $price;//单价
						$detail['count']  					= $sku['rec'];//采购数量
						$detail['goods_recommend_count']  	= $sku['rec'];//采购数量
						$detail['parid']                    = $parid;//料号供应商ID
						$detail['recordnumber'] 			= $recordnumber;
						$poid  								= OwOrderModel::getOwPoid($recordnumber);//根据跟踪号取采购主订单编号
						$detail['po_id'] 					= $poid;
						$dataSet 							= array2sql($detail);
						$sql 								= "insert into ph_ow_order_detail set {$dataSet}  ";
						$rtndetail  = $dbConn->execute($sql);
						if($rtndetail === false) {
							$rollback = true;
						}else{
							$ismark   = 1;
						}
					}else{
						$rollback = true;
					}
				}else{
					$rollback = true;
				}
			}
		}
		//$existSku = substr($existSku, 0, strlen($existSku) - 1);
		if($rollback == false){
			//if($ismark == 1){
				BaseModel::commit();
	            BaseModel::autoCommit();
	            $result['msg'] = 'yes';
			//}else{
			//	$result['msg'] = 'warn';
			//}
            //$result['tip'] = $existSku;
		}else{
			BaseModel::rollback();
			BaseModel::autoCommit();
			$result['msg'] = 'no';
		}
		return json_encode($result);
	}
	
	/**
	 * 获取海外仓料号总个数
	 */
	public static function getNewOwSkuInfoCount(){
		$totalnum 			= OwOrderModel::getNewOwSkuInfoCount();
		$result['totalnum'] = $totalnum;
		return json_encode($result);
	}
	
	/**
	 * 返回海外仓新品基础信息
	 */
	public function getNewOwSkuInfo(){
		$page    = isset($_GET['page']) ? $_GET['page'] : 1;
		$pagenum = isset($_GET['pagenum']) ? $_GET['pagenum'] : 200;
		$rtnData = OwOrderModel::getNewOwSkuInfo($page, $pagenum);
		if(!empty($rtnData)){
			$result['code'] = '200';
			$result['data'] = $rtnData;
		}else{
			$result['code'] = '404';
		}
		return json_encode($result);
	}
}

?>
