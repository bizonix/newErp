<?php
/**
*类名：配货单常规出库
*功能：处理配货单常规出库相关操作
*作者：hws
*
*/
class ScanAllSkuOrderAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";	
	public $userId;
	
	//获取配货单信息
	function act_orderDetail(){
		$userId 	  = $_SESSION['userId'];
		$starttime 	  = time();
		$state_status = array(402); //准备状态
		$final_status = array(403);//配货完成状态
		$no_express_delivery = array('中国邮政平邮','中国邮政挂号','香港小包平邮','香港小包挂号','德国邮政','新加坡邮政','EUB','Global Mail');
		$order_id 	  = $_POST['ebay_id'];

		//先核对订单
		//兼容 EUB或者 包裹 扫描的是 trackno 而非ebayid
		$p_real_ebayid='#^\d+$#';
		$p_trackno_eub='#^(LK|RA|RB|RC|RR|RF|LN)\d+(CN|HK|DE200)$#';
		$is_eub_package_type=false;
		if(	preg_match($p_real_ebayid,$order_id) ){
		}else if( preg_match($p_trackno_eub,$order_id) ){
			$is_eub_package_type=true;
		}else{
			self::$errCode = "001";
			self::$errMsg  = "订单号[".$order_id."]格式有误";
			return false;			
		}

		if($is_eub_package_type===true){
			$ordercheck = ShippingOrderModel::getShippingOrderInfo("b.*","where a.tracknumber='$order_id' and a.is_delete=0");
		}else{
			$ordercheck = ShippingOrderModel::getShippingOrder("*","where id='$order_id'");
		}
		
		if(empty($ordercheck)){
			self::$errCode = '001';
			self::$errMsg  = '未找到订单/跟踪号['.$order_id.']';
			return false;
		}else{
			$ebay_carrier = CommonModel::getShipingNameById($ordercheck[0]['transportId']); 
			//print_r($ebay_carrier);exit;
			if(!in_array($ebay_carrier,$no_express_delivery)){
				self::$errCode = '001';
				self::$errMsg  = '请选择非快递订单!';
				return false;
			}
		}
		if ($ordercheck[0]['orderStatus']==900){
			self::$errCode = '002';
			self::$errMsg  = "该发货单[{$order_id}][已经废弃]!";
			return false;
		}else if(in_array($ordercheck[0]['orderStatus'], $final_status)){
			self::$errCode = '005';
			self::$errMsg  = "该发货单已经扫描完成!";
			return false;
		}
		if (!in_array($ordercheck[0]['orderStatus'], $state_status)){
			self::$errCode = '002';
			self::$errMsg  = "该发货单[{$order_id}][不在待配货]!";
			return false;
		}
		//检测是否有已扫描没作废料号(可不用插入)
		$eosr_arr	 = array();
		$scan_record = OrderPickingRecordsModel::getPickingRecords("shipOrderdetailId,sku,pName","where shipOrderId={$ordercheck[0]['id']} and is_delete=0");
		if($scan_record){
			foreach($scan_record as $scan){
				//$eosr_arr[] = $scan['sku']."-".$scan['pName'];
				$eosr_arr[] = $scan['shipOrderdetailId'];
			}
		}

		//配货单所有料号及数量
		$skuinfos = array();
		$skuinfos = GroupRouteModel::getOrderPositionID($ordercheck[0]['id']);
		foreach($skuinfos as $info){
			//$sku_pName = $info['sku']."-".$info['pName'];
			if(!in_array($info['id'], $eosr_arr)){
				$i_data = array(
					'shipOrderId' => $ordercheck[0]['id'],
					'shipOrderdetailId' => $info['id'],
					'sku'         => $info['sku'],
					'pName'       => $info['pName'],
					'totalNums'   => $info['amount'],
					'scanUserId'  => $userId,
					'scanTime'    => time(),
				);
				OrderPickingRecordsModel::insertRow($i_data);
			}
		}
		
		$eosr_arr2 = OrderPickingRecordsModel::getPickingRecords("*","where shipOrderId={$ordercheck[0]['id']} and isScan=0 and is_delete=0 order by pName");
		
		if(!$eosr_arr2){
			self::$errCode = '005';
			self::$errMsg  = "该发货单已经扫描完成!";
			return false;
		}else{
			foreach($eosr_arr2 as $value){
				$goods_sn = array();				
				$skuInfo  = GroupRouteModel::getSkuPosition("where a.sku='{$value['sku']}' and c.pName='{$value['pName']}' and b.is_delete=0");
				$goods_sn['detailId'] 	 = $value['shipOrderdetailId'];
				$goods_sn['sku']  		 = $value['sku'];
				$goods_sn['gl']   		 = $value['pName'];
				$goods_sn['nums']        = $value['totalNums'];
				$goods_sn['goods_count'] = $skuInfo[0]['nums'];
				$res_data['detail'][]    = $goods_sn;
			}
		}
		
		if (!isset($res_data['detail'])||count($res_data['detail'])==0){
			self::$errCode = '006';
			self::$errMsg  = "该订单没有需要包货料号!";
			return false;
		}else{
			$gl_arr = array();
			$s_arr  = array();
			foreach($res_data['detail'] as $value){
				$s_arr[]  = $value['sku'];
				$gl_arr[] = $value['gl'];
			}
			array_multisort($gl_arr, $s_arr, $res_data['detail']);
			$difftime = time()-$starttime;

			self::$errMsg  = '开始料号扫描!'.'--'.$difftime;
			return $res_data;
		}
	}
	
	//检测sku
	function act_checkSku(){
		$userId 	  = $_SESSION['userId'];
		$starttime 	  = time();
		$Msg 		  = '';
		$res_data     = array();
		$state_status = array(402); //准备状态
		$final_status = array(403);//配货完成状态
		$no_express_delivery = array('中国邮政平邮','中国邮政挂号','香港小包平邮','香港小包挂号','德国邮政','新加坡邮政','EUB','Global Mail');
		$order_id 	  = $_POST['ebay_id'];

		//先核对订单
		//兼容 EUB或者 包裹 扫描的是 trackno 而非ebayid
		$p_real_ebayid='#^\d+$#';
		$p_trackno_eub='#^(LK|RA|RB|RC|RR|RF|LN)\d+(CN|HK|DE200)$#';
		$is_eub_package_type=false;
		if(	preg_match($p_real_ebayid,$order_id) ){
		}else if( preg_match($p_trackno_eub,$order_id) ){
			$is_eub_package_type=true;
		}else{
			self::$errCode = "001";
			self::$errMsg  = "订单号[".$order_id."]格式有误";
			return false;			
		}

		if($is_eub_package_type===true){
			$ordercheck = ShippingOrderModel::getShippingOrderInfo("b.*","where a.tracknumber='$order_id' and a.is_delete=0");
		}else{
			$ordercheck = ShippingOrderModel::getShippingOrder("*","where id='$order_id'");
		}
		
		if(empty($ordercheck)){
			self::$errCode = '001';
			self::$errMsg  = '未找到订单/跟踪号['.$order_id.']';
			return false;
		}else{
			$ebay_carrier = CommonModel::getShipingNameById($ordercheck[0]['transportId']); 
			if(!in_array($ebay_carrier,$no_express_delivery)){
				self::$errCode = '001';
				self::$errMsg  = '请选择非快递订单!';
				return false;
			}
		}
		if ($ordercheck[0]['orderStatus']==900){
			self::$errCode = '002';
			self::$errMsg  = "该发货单[{$order_id}][已经废弃]!";
			return false;
		}else if(in_array($ordercheck[0]['orderStatus'], $final_status)){
			self::$errCode = '005';
			self::$errMsg  = "该发货单已经扫描完成!";
			return false;
		}
		if (!in_array($ordercheck[0]['orderStatus'], $state_status)){
			self::$errCode = '002';
			self::$errMsg  = "该发货单[{$order_id}][不在待配货]!";
			return false;
		}
		
		//配货单所有料号及数量
		$skuinfos = array();
		$key_skuinfos = array();
		$skuinfo = GroupRouteModel::getOrderPositionID($ordercheck[0]['id']);
		foreach($skuinfo as $info){
			//$s_key = $info['sku']."-".$info['pName'];
			$s_key = $info['id'];
			$skuinfos[$s_key] = $info['amount'];
			$key_skuinfo[$info['sku']] = $info['amount'];
		}
		//print_r($skuinfos);die;
		$eosr_arr2 = OrderPickingRecordsModel::getPickingRecords("*","where shipOrderId={$ordercheck[0]['id']} and isScan=0 and is_delete=0");		
		if(!$eosr_arr2){
			self::$errCode = '005';
			self::$errMsg  = "该发货单已经扫描完成!";
			return false;
		}else if (count($eosr_arr2) <= count($skuinfos)){//初次和二次配货
			$ebay_sku     = trim($_POST['ebay_sku']);
			$ebay_sku     = get_goodsSn($ebay_sku);
			$now_sku      = trim($_POST['now_sku']);
			$now_pname    = trim($_POST['now_pname']);
			$now_detailid = trim($_POST['now_detailid']);
			//$s_key 		  = $now_sku."-".$now_pname;
			$s_key 		  = $now_detailid;
			$detail_info  = OmAvailableModel::getTNameList("wh_shipping_orderdetail","storeId,shipOrderId","where id={$now_detailid}");
			$arr_eosr     = OrderPickingRecordsModel::getPickingRecords("sku","where shipOrderId={$ordercheck[0]['id']} and sku='$ebay_sku' and pName='$now_pname' and is_delete=0");
			
			$key_skuinfos = array_keys($key_skuinfo);
			
			if($ebay_sku!=$now_sku){
				self::$errCode = '011';
				self::$errMsg  = "记录料号与扫描料号不符1!".$ebay_sku;
				return $ebay_sku;
			}
			if(!empty($arr_eosr)){
				if(!in_array($ebay_sku, $key_skuinfos)){
					self::$errCode = '009';
					self::$errMsg  = "记录料号与扫描料号不符2!".$ebay_sku;
					return $ebay_sku;
				}else if($skuinfos[$s_key] < 1){
					self::$errCode = '021';
					self::$errMsg  = "料号数量小于1!({$key_skuinfos[0]})";
					return $ebay_sku;
				}else if($skuinfos[$s_key] > 1){
					if(isset($_POST['pskunum'])){
						if($_POST['pskunum']!=$skuinfos[$s_key]){	
							self::$errCode = '022';
							self::$errMsg  = "请输入正确的数量,不要配错数量{$_POST['pskunum']}-{$skuinfos[$s_key]}!";
							return $ebay_sku;
						}
					}else{
						self::$errCode = '020';
						self::$errMsg  = "多数量料号需要输入数量!";
						return $ebay_sku;
					}
				}
				if($arr_eosr[0]['isScan']==0){
					//$sku_stock = OrderPickingRecordsModel::getSkuStock("actualStock","where sku='$ebay_sku'");
					$sku_stock = GroupRouteModel::getSkuPosition("where a.sku='$ebay_sku' and c.pName='$now_pname' and b.is_delete=0");
					if($detail_info[0]['storeId']==1){
						if(isset($sku_stock[0]['nums'])){
							if($sku_stock[0]['nums'] >= $skuinfos[$s_key]){
								$ssname=$order_id.",".$ebay_sku;
								session_start();
								if(isset($_SESSION[$ssname])&&$_SESSION[$ssname]=="yes"){
									self::$errCode = '502';
									self::$errMsg  = "数据同步中!请不要重复提交！";
									return false;
								} 

								$u_data = array();
								$u_data = array(
									'isScan'     => 1,
									'scanUserId' => $userId,
									'amount'     => $skuinfos[$s_key],
									'scanTime'   => time(),
								);
								OrderPickingRecordsModel::update($u_data,"and shipOrderId='{$ordercheck[0]['id']}' and sku='$ebay_sku' and pName='$now_pname' and is_delete=0");
								
								$position_info = OmAvailableModel::getTNameList("wh_position_distribution","id","where pName='$now_pname' and storeId=1");
								$positionId    = $position_info[0]['id'];
								$skuinfo 	   = whShelfModel::selectSku(" where sku = '{$ebay_sku}'");
								
								$paraArr = array(
									'ordersn' 	 => $ordercheck[0]['id'],
									'sku'     	 => $ebay_sku,
									'amount'  	 => $skuinfos[$s_key],
									'purchaseId' => $skuinfo['purchaseId'],
									'ioType'	 => 1,
									'ioTypeId'   => 2,
									'userId'	 => $userId,
									'reason'	 => '配货单配货出库',
									'positionId' => $positionId
								);
								$WhIoRecordsAct = new WhIoRecordsAct();
								$WhIoRecordsAct->act_addIoRecoresForWh($paraArr);     //出库记录
																					
								self::$errCode = '300';
								$Msg  = "实际料号出库扫描成功!".$ebay_sku;

								$eosr_arr2 = OrderPickingRecordsModel::getPickingRecords("*","where shipOrderId={$ordercheck[0]['id']} and isScan=0 and is_delete=0");									
								if(!$eosr_arr2){
									//更新订单到复核状态	
									GroupDistributionModel::updateShipOrder(array('orderStatus'=>403),"and id='{$ordercheck[0]['id']}' and orderStatus=402");
									WhPushModel::pushOrderStatus($ordercheck[0]['id'],'STATESHIPPED_PENDREVIEW',$_SESSION['userId'],time());        //状态推送
										
									self::$errCode = '005';
									self::$errMsg  = "该订单已经扫描完成!";
									return true;
								}
								
							}else{
								self::$errCode = '007';
								$Msg           = "所需数量大于库存,禁止出库!";
							}
						}else{
							self::$errCode = '008';
							$Msg  		   = "该料号仓储信息有误!".$ebay_sku;
						}
					}else{
						$snapStock_info = OmAvailableModel::getTNameList("wh_order_review_records_b","snapStock,id","where FIND_IN_SET('{$detail_info[0]['shipOrderId']}',shipOrderId) and status=1 and sku='$ebay_sku' order by id desc limit 1");
						if(empty($snapStock_info)){
							self::$errCode = '020';
							self::$errMsg  = "该B仓料号提货未复核";
							return $ebay_sku;
						}else{
							if($snapStock_info[0]['snapStock']>=$skuinfos[$s_key]){
								$u_data = array();
								$u_data = array(
									'isScan'     => 1,
									'scanUserId' => $userId,
									'amount'     => $skuinfos[$s_key],
									'scanTime'   => time(),
								);
								OrderPickingRecordsModel::update($u_data,"and shipOrderId='{$ordercheck[0]['id']}' and sku='$ebay_sku' and pName='$now_pname' and is_delete=0");
								
								ReviewBModel::updateSnapStock($skuinfos[$s_key],$snapStock_info[0]['id']);
								
								self::$errCode = '300';
								$Msg  = "实际料号出库扫描成功!".$ebay_sku;

								$eosr_arr2 = OrderPickingRecordsModel::getPickingRecords("*","where shipOrderId={$ordercheck[0]['id']} and isScan=0 and is_delete=0");									
								if(!$eosr_arr2){
									//更新订单到复核状态	
									GroupDistributionModel::updateShipOrder(array('orderStatus'=>403),"and id='{$ordercheck[0]['id']}' and orderStatus=402");
									WhPushModel::pushOrderStatus($ordercheck[0]['id'],'STATESHIPPED_PENDREVIEW',$_SESSION['userId'],time());        //状态推送
										
									self::$errCode = '005';
									self::$errMsg  = "该订单已经扫描完成!";
									return true;
								}
							}else{
								self::$errCode = '013';
								$Msg           = "B仓提货数量不够配货,请确认";
							}
						}
					}
					
					
					foreach($eosr_arr2 as $value){
						$goods_sn = array();				
						$eg  = GroupRouteModel::getSkuPosition("where a.sku='{$value['sku']}' and c.pName='{$value['pName']}' and b.is_delete=0");
						$goods_sn['detailId'] 	 = $value['shipOrderdetailId'];
						$goods_sn['sku']  		 = $value['sku'];
						$goods_sn['gl']   		 = $value['pName'];
						$goods_sn['nums']        = $value['totalNums'];
						$goods_sn['goods_count'] = $eg[0]['nums'];
						$res_data['detail'][]    = $goods_sn;
					}
					
					$gl_arr = array();
					$s_arr  = array();
					foreach($res_data['detail'] as $value){
						$s_arr[]  = $value['sku'];
						$gl_arr[] = $value['gl'];
					}
					array_multisort($gl_arr, $s_arr, $res_data['detail']);
				}else{
					self::$errCode = '010';
					$Msg  = "请不要重复扫描该订单下的料号!".$ebay_sku;
				}
			}else{
				self::$errCode = '009';
				$Msg  = "记录料号与扫描料号不符!".$ebay_sku;
			}
		}else{
			self::$errCode = '009';
			$Msg  		  = "记录料号与扫描料号不符!";
		}
		$difftime 	  = time()-$starttime;
		self::$errMsg = $Msg.'--'.$difftime;
		return $res_data;
	}
	
	//sku查询
	function act_searchSku(){
		$order_id = $_POST['ebay_id'];
		$sku      = trim($_POST['sku']);
		$sku 	  = get_goodsSn($sku);
		$now_pname= trim($_POST['now_pname']);
		$state_status = array(402); //准备状态
		$final_status = array(403);//配货完成状态
		$no_express_delivery = array('中国邮政平邮','中国邮政挂号','香港小包平邮','香港小包挂号','德国邮政','新加坡邮政','EUB','Global Mail');
		
		//先核对订单
		//兼容 EUB或者 包裹 扫描的是 trackno 而非ebayid
		$p_real_ebayid='#^\d+$#';
		$p_trackno_eub='#^(LK|RA|RB|RC|RR|RF|LN)\d+(CN|HK|DE200)$#';
		$is_eub_package_type=false;
		if(	preg_match($p_real_ebayid,$order_id) ){
		}else if( preg_match($p_trackno_eub,$order_id) ){
			$is_eub_package_type=true;
		}else{
			self::$errCode = "001";
			self::$errMsg  = "订单号[".$order_id."]格式有误";
			return false;			
		}

		if($is_eub_package_type===true){
			$ordercheck = ShippingOrderModel::getShippingOrderInfo("b.*","where a.tracknumber='$order_id' and a.is_delete=0");
		}else{
			$ordercheck = ShippingOrderModel::getShippingOrder("*","where id='$order_id'");
		}
		if(empty($ordercheck)){
			self::$errCode = '001';
			self::$errMsg  = '未找到订单/跟踪号['.$order_id.']';
			return false;
		}else{
			$ebay_carrier = CommonModel::getShipingNameById($ordercheck[0]['transportId']);
			//$ebay_carrier = '中国邮政平邮';
			if(!in_array($ebay_carrier,$no_express_delivery)){
				self::$errCode = '001';
				self::$errMsg  = '请选择非快递订单!';
				return false;
			}
		}
		if ($ordercheck[0]['orderStatus']==900){
			self::$errCode = '002';
			self::$errMsg  = "该发货单[{$order_id}][已经废弃]!";
			return false;
		}else if(in_array($ordercheck[0]['orderStatus'], $final_status)){
			self::$errCode = '005';
			self::$errMsg  = "该发货单已经扫描完成!";
			return false;
		}
		if (!in_array($ordercheck[0]['orderStatus'], $state_status)){
			self::$errCode = '002';
			self::$errMsg  = "该发货单[{$order_id}][不在待配货]!";
			return false;
		}
		
		//配货单所有料号及数量
		$skuinfos = array();
		$skuinfo = GroupRouteModel::getOrderPositionID($ordercheck[0]['id']);
		foreach($skuinfo as $info){
			$s_key = $info['sku']."-".$info['pName'];
			$skuinfos[$s_key] = $info['amount'];
		}
		
		$eosr_arr = OrderPickingRecordsModel::getPickingRecords("*","where shipOrderId={$ordercheck[0]['id']} and sku='$sku' and is_delete=0");
		if(!$eosr_arr){
			self::$errCode = "012";
			self::$errMsg  = "请扫描正确料号!";
			return false;
		}else{
			$sku_stock = GroupRouteModel::getSkuPosition("where a.sku='$sku' and c.pName='$now_pname' and b.is_delete=0");
			$s_key = $sku."-".$now_pname;
			$goods_sn = array();
			//$gsi = get_sku_info($sku);
			$goods_sn['sku']  = $sku;
			//$goods_sn['gc'] = $gsi['realnums'];
			$goods_sn['gc']   = isset($sku_stock[0]['nums'])?$sku_stock[0]['nums']:0;
			//$goods_sn['day']= floor($gsi['realnums']/$gsi['everyday_sale']); //库存天数
			$goods_sn['day']  = ""; //库存天数
			$goods_sn['nums'] =$skuinfos[$s_key];
			$goods_sn['is_scan'] = ($eosr_arr[0]['isScan']==1) ? "已扫描" : "未扫描";
			//$eg  = GroupRouteModel::getSkuPosition("where a.sku='$sku'");
			$goods_sn['gl'] = $now_pname;

			self::$errCode = "400";
			self::$errMsg  = '成功搜索该料号信息!'.$sku;
			return $goods_sn;
		}
	}


}


?>