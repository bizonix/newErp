<?php
/*
 * 异常缺货拆分对接仓库系统のACTION
 * ADD BY Herman.Xi @20140120
 */
class AbnormalStockAct{
	static $errCode = 0;
	static $errMsg = "";
	
	/*
	 *缺货拆分问题跟踪
     */
	public function act_operateAbOrderAPI(){
		if(!isset($_POST['omData'])){
			self::$errCode = 500;
			self::$errMsg = '未成功接收数据！';
			return false;
		}
		$ostatus = 770;
		$otype = 0;
		$storeId = 1;
		$orderstr = $_POST['omData'];
		$orderstr2 = $_POST['omData2'];
		$orderids = array_unique(explode(',', $orderstr));
		$invoiceids = array_unique(explode(',', $orderstr2));
		if(!$orderids){
			self::$errCode = 400;
			self::$errMsg = '未获取订单编号信息！';
			return false;
		}
		//var_dump($orderids); exit;
		$OrderindexAct = new OrderindexAct();
		$WarehouseAPIAct = new WarehouseAPIAct();
		foreach($orderids as $k => $orderId){
			BaseModel :: begin(); //开始事务
			$insertOrderData = array();
			$AbOrderInfo = $WarehouseAPIAct->act_getAbOrderInfo($invoiceids[$k]);
			//var_dump($AbOrderInfo); echo "<br>";
			//exit;
			$where = ' WHERE id = '.$orderId.' and is_delete = 0 AND storeId = '.$storeId.' LIMIT 1';
			$orderList = $OrderindexAct->act_showOrderList($ostatus, $otype, $where);
			//var_dump($orderList);exit;
			$order = $orderList[$orderId];
			$orderData = $order['orderData'];
			$insert_orderData = $orderData;
			unset($insert_orderData['id']);
			$insert_orderData['orderStatus'] = C('STATEOUTOFSTOCK');
			$insert_orderData['orderType'] = C('STATEOUTOFSTOCK_ABNORMAL');
			$orderExtenData = $order['orderExtenData'];
			$insert_orderExtenData = $orderExtenData;
			unset($insert_orderExtenData['omOrderId']);
			$orderUserInfoData = $order['orderUserInfoData'];
			$insert_orderUserInfoData = $orderUserInfoData;
			unset($insert_orderUserInfoData['omOrderId']);
			
			//$orderTracknumber = $order['orderTracknumber'];
			//$orderAudit = $order['orderAudit'];
			$orderDetail = $order['orderDetail'];
			
			$insertOrderData['orderData'] = $insert_orderData;
			$insertOrderData['orderExtenData'] = $insert_orderExtenData;
			$insertOrderData['orderUserInfoData'] = $insert_orderUserInfoData;
			
			$insert_orderDetail = array();
			$need_delete_ids = array();
			foreach($orderDetail as $dkey => $detailValue){
				$orderDetailData = $detailValue['orderDetailData'];
				$orderDetailExtenData = $detailValue['orderDetailExtenData'];
				if(!isset($AbOrderInfo[$orderDetailData['sku']]) || $AbOrderInfo[$orderDetailData['sku']] == 0){
					$insert_orderDetailData = $orderDetailData;
					unset($insert_orderDetailData['id']);
					unset($insert_orderDetailData['omOrderId']);
					$insert_orderDetailExtenData = $orderDetailExtenData;
					unset($insert_orderDetailExtenData['omOrderdetailId']);
					$insert_orderDetail[$dkey]['orderDetailData'] = $insert_orderDetailData;
					$insert_orderDetail[$dkey]['orderDetailExtenData'] = $insert_orderDetailExtenData;
					$need_delete_ids[] = $orderDetailData['id'];
				}
			}
			$insertOrderData['orderDetail'] = $insert_orderDetail;
			$count_insert_orderDetail = count($insert_orderDetail);
			$count_orderDetail = count($orderDetail);
			$tableName = "om_unshipped_order";
			if($count_insert_orderDetail > 0 && ($count_insert_orderDetail < $count_orderDetail)){
				$calcWeight = CommonModel::calcNowOrderWeight($orderId);//重新计算原来订单的重量
				if(!OrderindexModel::deleteOrderDetail(array('is_delete'=>1), ' where id in('.join(',',$need_delete_ids).')')){
					BaseModel :: rollback();
					self :: $errCode = '005';
					self :: $errMsg = "删除原订单明细失败!";
					return false;
				}
				$insertOrderData['orderData']['isCopy'] = 2;
				$insertOrderData['orderData']['actualTotal'] = 0.00;
				//var_dump($orderData);exit;
				$calcInfo = CommonModel :: calcAddOrderWeight($insert_orderDetail);//计算重量和包材
				//var_dump($calcInfo); exit;
				$insertOrderData['orderData']['calcWeight'] = $calcInfo[0];
				$insertOrderData['orderData']['pmId'] = $calcInfo[1];
				if(count($insertOrderData['orderDetail']) > 1){
					$insertOrderData['orderData']['orderAttribute'] = 3;
				}else if(isset($insertOrderData['orderDetail'][0]['orderDetailData']['amount']) && $insertOrderData['orderDetail'][0]['orderDetailData']['amount'] > 1){
					$insertOrderData['orderData']['orderAttribute'] = 2;
				}
				$calcShippingInfo = CommonModel :: calcAddOrderShippingFee($insertOrder,1);//计算运费
				$insertOrderData['orderData']['channelId'] = $calcShippingInfo['fee']['channelId'];
				$insertOrderData['orderData']['calcShipping'] = $calcShippingInfo['fee']['fee'];
				//print_r($insertOrderData); exit;
				if($_spitId = OrderAddModel :: insertAllOrderRowNoEvent($insertOrderData)){
					if(!OrderLogModel::insertOrderLog($_spitId, 'INSERT ORDER')){
						BaseModel :: rollback();
						self :: $errCode = '001';
						self :: $errMsg = "插入订单日志失败!";
						return false;
					}
					if(!OrderRecordModel::insertSpitRecords($orderId,$_spitId)){
						BaseModel :: rollback();
						self :: $errCode = '002';
						self :: $errMsg = "插入拆分日志失败!";
						return false;
					}
				}else{
					BaseModel :: rollback();
					self :: $errCode = '010';
					self :: $errMsg = "插入订单信息失败!";
					return false;
				}
				$returnStatus0 = array('isCopy'=>1,'calcWeight'=>$calcWeight);
				$rtn = $WarehouseAPIAct->act_operateAbOrder($invoiceids[$k],$calcWeight);
				if(!$rtn){
					BaseModel :: rollback();
					self :: $errCode = '011';
					self :: $errMsg = "调用仓库系统拆分功能失败!";
					return false;
				}
			}else{
				$returnStatus0 = array('orderStatus'=>$insert_orderData['orderStatus'],'orderType'=>$insert_orderData['orderType']);
			}
			if(!OrderindexModel::updateOrder($tableName,$returnStatus0,$where)){
				BaseModel :: rollback();
				self :: $errCode = '002';
				self :: $errMsg = "订单移动到缺货异常失败!";
				return false;
			}
			BaseModel :: commit();
			BaseModel :: autoCommit();
		}
		self::$errCode = 200;
		self::$errMsg = '缺货拆分完成！';
		return true;
    }
}
?>	