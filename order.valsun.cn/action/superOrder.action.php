<?php
/*
 * 名称：SuperOrderAct
 * 功能：对超大订单的处理
 * 版本：v 1.0
 * 日期：2013/09/11
 * 作者：wxb
 * */
class SuperOrderAct{
	public static $errCode = 0;
	public static $errMsg = '';
	public function act_index(){
		$_POST = $_REQUEST;//for test
		if(empty($_POST["idArr"])){
			self::$errCode = "014";
			self::$errMsg = "传参非法";
			exit;
		}
		$idArr = $_POST["idArr"];
		$idStr = implode(",",$idArr); 
		$ret = SuperOrderModel::index($idStr);
		if($ret){
			self::$errCode = "001";
			self::$errMsg = "确认超大成功";
			return $ret;
		}
		self::$errCode = "444";
		self::$errMsg = "确认超大失败";
	}
	
	public function act_api_showOrder(){//对外接口,提供已确认超大订单
		
		//if(!empty($_GET["purchaseId"])){
			$purchaseId = trim($_GET["purchaseId"]);
			//$where = base64_decode($where);
			/*$whereArr = explode(",",$where);
			$whereOrder = $whereArr[0];
			$whereDetail = $whereArr[1];*/
		/*}else{
			self::$errCode = '5806';
			self::$errMsg  = 'purchaseId is not null';
			return false;
		}*/
		//拆分两条where 语句
		//echo $purchaseId; exit;
		$ret  =  SuperOrderModel::showOrderAPI($purchaseId);
		//var_dump($ret);
		/*if($ret["errCode"]=="111"){
			self::$errCode = $ret["errCode"];
			self::$errMsg = $ret["errMsg"];
			return false;
		}*/
		if($ret){
			self::$errCode = "200";
			self::$errMsg = "拉取数据成功";
			return $ret;
		}else{
			self::$errCode = "002";
			self::$errMsg = "get empty";
			return false;
		}
	}
	
	public function act_auditOrder(){
		if(!$_GET["orderid"]){
			self::$errCode = "001";
			self::$errMsg = "orderid is empty";
			return false;
		}else{
			$orderid = $_GET["orderid"];
		}
		if(!$_GET["sku"]){
			self::$errCode = "002";
			self::$errMsg = "sku is empty";
			return false;
		}else{
			$sku = $_GET["sku"];
		}
		if(!$_GET["purchaseId"]){
			self::$errCode = "002";
			self::$errMsg = "purchaseId is empty";
			return false;
		}else{
			$purchaseId = $_GET["purchaseId"];
		}
		if(!$_GET["type"]){
			self::$errCode = "002";
			self::$errMsg = "type is empty";
			return false;
		}else{
			$type = $_GET["type"];
			$status = $_GET["status"];
			$note = $_GET["pcontent"];
			if(!$status && !$note){
				self::$errCode = "002";
				self::$errMsg = "status or note is empty";
				return false;
			}
		}
		
		$storeId = 1;
		if(!empty($_GET['storeId']) && isset($_GET['storeId'])){
			$storeId = $_GET['storeId'];
		}
		//var_dump($note);
		$ret = SuperOrderModel::auditOrder($orderid, $sku, $type, $status, $purchaseId, $note, $storeId);
		self::$errCode = SuperOrderModel::$errCode;
		self::$errMsg = SuperOrderModel::$errMsg;
		return $ret;
	}
	
	public function act_judgeStaus($omOrderId){
		$omOrderId = trim($omOrderId);
		$table = "`om_unshipped_order_detail` ";
		$fields = "id";
		$where = " is_delete = '0' AND omOrderId ='{$omOrderId}' ";
		$detailIdArr = SuperOrderModel::selectResult($table, $fields,$where);
		$detailIdArrOne = array();
		if(!empty($detailIdArr)){
			 foreach($detailIdArr as $detIdArrVal){ //转成一唯数组
			 	$detailIdArrOne[] = $detIdArrVal["id"];
			  }
		}
		$table = "`om_records_order_audit` ";
		$fields = "omOrderdetailId";
		$where = " omOrderId ='{$omOrderId}' AND auditStatus = '1'";
		$auditArr = SuperOrderModel::selectResult($table, $fields,$where);//通过
		$auditArrOne = array();
		if(!empty($auditArr)){
			foreach($auditArr as $auditArrVal){//转成一唯数组
				$auditArrOne[] = $auditArrVal['omOrderdetailId']; 
			}
		}
		
		$where = " omOrderId = '{$omOrderId}' AND auditStatus = '2'";
		$auditNotArr = SuperOrderModel::selectResult($table, $fields,$where);//未通过
		$auditNotArrOne = array();
		if(!empty($auditNotArr)){
			foreach($auditNotArr as $audNotArrVal){//转成一唯数组
				$auditNotArrOne[] = $audNotArrVal['omOrderdetailId'];
			}
		}
		//状态判断start
		if(empty($auditArr) && empty($auditNotArr)){
			return array(200,202);//没有审核或拦截纪录时
		}else if(empty($auditArr) || empty($auditNotArr) ){
			$judgeAudit = array_diff($detailIdArrOne, $auditNotArrOne);
		 	if(empty($judgeAudit)){
					return array(200,205);//全部审核不通过,被拦截（全为拦截状态）
			}
			
			$judge = array_diff($detailIdArrOne, $auditArrOne);
			 if(empty($judge)){
					return array(200,204);//全部审核通过（全为通过状态）
				}
			if(empty($auditArr)){//没有审核纪录，有部分拦截纪录
				return array(200,202);
			}
			if(empty($auditNotArr)){//没有拦截纪录，有部分审核纪录
				return array(200,203);
			}	
		}else{
			return array(200,203);//部分审核通过（有审核纪录，也有拦截纪录）
		}	
	
	}
	public function act_partPackage(){
		if(!$_POST["idArr"]){
			self::$errCode = "003";
			self::$errMsg = "传参有误";
			return false;
		}
		$idArr = $_POST['idArr'];
		$idStr = implode(",",$idArr);
		$table = 'om_unshipped_order';
		$fields = '*';
		$storeId = 1;
		$where = " WHERE is_delete = '0' AND storeId = ".$storeId." AND id in ({$idStr})";
		$orderList =  OrderindexModel::showOrderList($table, $where);//获取订单
		//var_dump($ret); exit;
		if(empty($orderList)){
			self::$errCode = "197";
			self::$errMsg = "订单数据获取失败";
			return false;
		}
		$OrderindexAct = new OrderindexAct();
		//分别为第条订单加入shipOrderDetail 详情 并根据生成配货单接口调整字段
		$errMsg = '';
		foreach($orderList as $omOrderId=>$orderValue){
			$orderData = $orderValue['orderData'];
			$orderDetail = $orderValue['orderDetail'];
			//var_dump($orderDetail); exit;
			$orderAudit = $orderValue['orderAudit'];
			
			$exCarrIds = CommonModel::getCarrierInfoById(1);
			if(!in_array($orderData['transportId'],$exCarrIds)){
				self::$errCode = "006";
				self::$errMsg = "非快递订单不允许部分配货";
				continue;
			}
			$orderAuditBySku = array();
			foreach($orderAudit as $auditvalue){
				$orderAuditBySku[$auditvalue['sku']] = $auditvalue;
			}
			//var_dump($orderAuditBySku);
			$new_orderDetail = array();
			if(!empty($orderAudit)){
				//var_dump($orderAuditBySku); echo "<br>";
				$orderDetail = $orderValue['orderDetail'];
				foreach($orderDetail as $omOrderDetailId => $detailValue){
					//var_dump($omOrderDetailId);
					$orderDetailData = $detailValue['orderDetailData'];
					//var_dump($orderDetailData);
					$orderDetailExtenData = $detailValue['orderDetailExtenData'];
					//var_dump($orderDetailExtenData);
					$usku = $orderDetailData['sku'];
					//echo $usku; echo "<br>";
					$combineSkuinfo = GoodsModel::getCombineSkuinfo($usku);
					//var_dump($skuinfo);
					if($combineSkuinfo){
						//组合料号
						$combineAudit = true;
						$skuinfoDetail = $combineSkuinfo['detail'];
						foreach($skuinfoDetail as $skuinfoDetailValue){
							$_sku = $skuinfoDetailValue['sku'];
							if($orderAuditBySku[$_sku]['auditStatus'] == 1){
							//$scount = $skuinfoDetailValue['count'];
								$pickRecords = WarehouseAPIModel::getOrderSkuPickingRecords($omOrderId, $_sku);
								//$pickRecords['amount']==$pickRecords['totalNums']
								if(!empty($pickRecords)){
									$combineAudit = false;
								}
							}else{
								$combineAudit = false;	
							}
						}
						if($combineAudit){//组合料号下判断
							$new_orderDetail[$omOrderDetailId]['orderDetailData'] = $orderDetailData;
							$new_orderDetail[$omOrderDetailId]['orderDetailExtenData'] = $orderDetailExtenData;
						}
					}else{
						//单料号
						if($orderAuditBySku[$usku]['auditStatus'] == 1){
							$pickRecords = WarehouseAPIModel::getOrderSkuPickingRecords($omOrderId, $usku);
							//var_dump($pickRecords); echo "<br>";
							//$pickRecords['amount']==$pickRecords['totalNums']
							if(empty($pickRecords)){
								$new_orderDetail[$omOrderDetailId]['orderDetailData'] = $orderDetailData;
								$new_orderDetail[$omOrderDetailId]['orderDetailExtenData'] = $orderDetailExtenData;
							}
						}
					}
				}
				//var_dump($new_orderDetail); exit;
				if($new_orderDetail){
					$orderValue['orderDetail'] = $new_orderDetail;
					$calcInfo = CommonModel :: calcAddOrderWeight($new_orderDetail);//计算重量和包材
					//var_dump($calcInfo); exit;
					$orderValue['orderData']['calcWeight'] = $calcInfo[0];
					$orderValue['orderData']['pmId'] = $calcInfo[1];
					if(count($orderValue['orderDetail']) > 1){
						$orderValue['orderData']['orderAttribute'] = 3;
					}else if(isset($orderValue['orderDetail'][0]['orderDetailData']['amount']) && $orderValue['orderDetail'][0]['orderDetailData']['amount'] > 1){
						$orderValue['orderData']['orderAttribute'] = 2;
					}
					$calcShippingInfo = CommonModel :: calcAddOrderShippingFee($insertOrder,1);//计算运费
					$orderValue['orderData']['channelId'] = $calcShippingInfo['fee']['channelId'];
					$orderValue['orderData']['calcShipping'] = $calcShippingInfo['fee']['fee'];
					$rtn = OrderPushModel::listPushOneMessage($orderValue, 2);
					self::$errCode = OrderPushModel::$errCode;
					self::$errMsg = OrderPushModel::$errMsg;
					return $rtn;
				}else{
					self::$errCode = 040;
					self::$errMsg = "已经全部推送并且配货！";
					return false;
				}
				//var_dump($orderValue); exit;
			}else{
				$errMsg .= '订单编号 '.$omOrderId.' 没有审核记录，不能申请部分配货<br />';	
			}
			//exit;
			//var_dump($orderAudit);
		}
		//exit;
	}
	
	/**
	 * 从详情里移除 取出审核通过的 但未申请包货的 sku
	 * @param $idStr order id
	 * @param $ret 订单详情
	 * @return 去除了不符要求的sku详情二组数组
	 * */
	public function remSkuInDet($idStr,$ret){
		$table = "`om_records_order_audit` ";
		$fields = " omOrderdetailId ";
		$where = " omOrderId in ('{$idStr}') AND auditStatus = '1' AND appStatus ='0'";
		//通过审核并且未申请包货
		$checkedSku =  SuperOrderModel::selectResult($table, $fields,$where);
		$checkedSkuOne = array();//转成一维数组,包含所有审核通过的sku 详情 id
		if(!empty($checkedSku)){//流程正常下是都不为空或者通过审核的都已经被申请过了
			foreach($checkedSku as $checkedSkuVal){
				$checkedSkuOne [] = $checkedSkuVal["omOrderdetailId"];
			}
		}else{
			$where = " omOrderId in ('{$idStr}') AND auditStatus = '1'";//判断是否有审核纪录
			$checked =  SuperOrderModel::selectResult($table, $fields,$where);
			if(!$checked){
				self::$errCode = "212";
				self::$errMsg = "订单异常:没有审核纪录";
				return false;
			}
			self::$errCode = "213";
			self::$errMsg = "已审核通过的sku全部已申请";
			return false;
		}
		//去除未审核通过的sku
		foreach($ret  as $retKey => $retVal ){
			$detailId = $retVal["id"];
			if(!in_array($detailId, $checkedSkuOne)){
				unset($ret[$retKey]);
			}
		}
		return $ret;
	}
}
?>