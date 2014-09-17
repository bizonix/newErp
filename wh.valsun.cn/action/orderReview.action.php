<?php
/**
*类名：小包订单复核
*功能：处理订单复核信息
*作者：hws
*Modify By czq
*
*/
class OrderReviewAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";	
	
	//检测订单号
	function act_checkOrder(){
		$userId 	  = $_SESSION['userId'];
		$order_id     = trim($_POST['ebay_id']);
		$state_status = array(PKS_WIQC);
		//先核对订单
		$p_real_ebayid='#^\d+$#';
		if(!preg_match($p_real_ebayid,$order_id) ){
			self::$errCode = "001";
			self::$errMsg  = "发货单号[".$order_id."]格式有误";
			return false;			
		}
		$ordercheck = ShippingOrderModel::getShippingOrder("*","where id='$order_id'");
		if(empty($ordercheck)){
			self::$errCode = '001';
			self::$errMsg  = '未找到发货单['.$order_id.']';
			return false;
		}
		if($ordercheck[0]['isExpressDelivery'] == 1){
			self::$errCode = '001';
			self::$errMsg  = '此发货单是快递运输方式，请转到快递复核界面操作！';
			return false;
		}
		$orderinfos = array();
		$skuinfos   = array();
		$orderinfos = get_realskunum($ordercheck[0]['id']);    //配货单所有料号及数量
		
		foreach($orderinfos as $or_sku => $or_nums){
			$sku_info = OrderReviewModel::getSkuInfo("goodsName","where sku='$or_sku'");
			$skuinfos[]=array(
				'sku'  		=> $or_sku,
				'goodsName' => $sku_info['goodsName'],
				'num'  		=> $or_nums,
			);
		}
		
		$string = "";
		$time   = strtotime(date('Y-m-d H:i:s'));
		foreach($skuinfos as $info){			
			$string .= "('".$ordercheck[0]['id']."','". $info['sku']."','". $info['goodsName']."','0','". $info['num']."','".$userId."','".$time."','0'),";
		}
		$string = trim($string,",");
		
		OrderReviewModel::update(array('is_delete'=>1),"and shipOrderId='{$ordercheck[0]['id']}'");
		$insert_info = OrderReviewModel::insert($string);
		if($insert_info){
			if (!in_array($ordercheck[0]['orderStatus'], $state_status)){
				if($ordercheck[0]['orderStatus']==PKS_PROCESS_GET_GOODS){
	
					$where  = "where shipOrderId='{$ordercheck[0]['id']}' and is_delete=0";
					$list	= OmAvailableModel::getTNameList("wh_order_picking_records","*",$where);
					$eosr_arrlist = array();
					foreach($list as $row){
						$eosr_arrlist[] = $row['shipOrderdetailId'];
					}
					
					//$skuinfos = get_realskunum($orderid);
					$skuinfos = OmAvailableModel::getTNameList("wh_shipping_orderdetail","*","where shipOrderId='{$ordercheck[0]['id']}' and is_delete = 0");
					foreach($skuinfos as $info){
						$order_detail = array();
						if(!in_array($info['id'], $eosr_arrlist)){
							$order_detail['shipOrderId'] = $info['shipOrderId'];
							$order_detail['shipOrderdetailId'] = $info['id'];
							$order_detail['sku'] 		 = $info['sku'];
							$order_detail['pName'] 		 = $info['pName'];
							$order_detail['totalNums'] 	 = $info['amount'];
							$order_detail['isScan'] 	 = 0;
							$order_detail['is_delete'] 	 = 0;
							$field =  ' SET '.array2sql($order_detail);
							OmAvailableModel::addTNameRow("wh_order_picking_records",$field);
						}
					}
					
					$where  = "where shipOrderId='{$ordercheck[0]['id']}' and is_delete=0 and isScan=0";
					$p_list	= OmAvailableModel::getTNameList("wh_order_picking_records","*",$where);

					$str = '';
					if(!empty($p_list)){
						foreach($p_list as $eo){
							$op_amount = empty($eo['amount']) ? 0 : $eo['amount'];
							$str .= "料号 {$eo['sku']} 未配货,";
							$str .= " 已配货 {$op_amount} 还需配货 ".($eo['totalNums']-$op_amount);
							$str .= " PDA扫描时间:".($eo['scanTime'] ? date('Y-m-d H:i:s', $eo['scanTime']) : ' 无 ');					
							$str .= "<br>";
						}
					}

					self::$errCode = '004';
					self::$errMsg  = "该订单[{$order_id}]在等待配货状态,请确认!<br/>".$str;
					return false;
				}
				self::$errCode = '001';
				self::$errMsg  = "该订单[{$order_id}]在".LibraryStatusModel::getStatusNameByStatusCode($ordercheck[0]['orderStatus'])."状态,请确认!";
				return false;
			}
			$fist_skuinfos = OrderReviewModel::getReviewList("*","where shipOrderId='{$ordercheck[0]['id']}' and is_delete=0 and storeId=1");
			$note_info     = OmAvailableModel::getTNameList("wh_shipping_order_note_record","*","where shipOrderId='{$ordercheck[0]['id']}'");
			if(!empty($note_info)){
				self::$errMsg  = "订单有效,请复核该订单下的料号及数量!<br/>备注：".$note_info[0]['content'];
			}else{
				self::$errMsg  = "订单有效,请复核该订单下的料号及数量!";
			}
			return $fist_skuinfos;
		}else{
			self::$errCode = '003';
			self::$errMsg  = "订单料号初始化出错,请重试";
			return false;
		}
	
	}
	
	//检测sku
	function act_checkSku(){
		$userId 	  = $_SESSION['userId'];
		$order_id     = trim($_POST['ebay_id']);
		$ebay_sku 	  = trim($_POST['ebay_sku']);
		$ebay_sku     = get_goodsSn($ebay_sku);
		$state_status = array(PKS_WIQC);
		//先核对订单
		$p_real_ebayid='#^\d+$#';
		if(!preg_match($p_real_ebayid,$order_id) ){
			self::$errCode = "100";
			self::$errMsg  = "订单号[".$order_id."]格式有误";
			return false;			
		}
		
		$ordercheck = ShippingOrderModel::getShippingOrder("*","where id='$order_id'");
		if(empty($ordercheck)){
			self::$errCode = '100';
			self::$errMsg  = '未找到订单['.$order_id.']';
			return false;
		}else{
			if (!in_array($ordercheck[0]['orderStatus'], $state_status)){
				self::$errCode = '001';
				self::$errMsg  = "该订单[{$order_id}]在".LibraryStatusModel::getStatusNameByStatusCode($ordercheck[0]['orderStatus'])."状态,请确认!";
				return false;
			}
		}
					
		$bool 	  = 0;
		$sku_info = OrderReviewModel::getReviewList("*","where shipOrderId='{$ordercheck[0]['id']}' and sku='$ebay_sku' and is_delete=0 and storeId=1");
		if(empty($sku_info)){
			self::$errCode = '005';
			self::$errMsg  = '订单不存在该料号['.$ebay_sku.'],请重试';
			return false;
		}elseif($sku_info[0]['totalNums']>=5){ //超个5个就手动输入数量
			self::$errCode = '100';
			self::$errMsg  = '料号['.$ebay_sku.']数量为'.$sku_info[0]['totalNums'].'，请输入配货数量核对';
		}elseif($sku_info[0]['amount']+1>$sku_info[0]['totalNums']){
			self::$errCode = '008';
			self::$errMsg  = '料号['.$ebay_sku.']数量为'.$sku_info[0]['totalNums'].',扫描数量已超出,请检查';
			return false;
		}else{
			if($sku_info[0]['totalNums']==1){
				self::$errMsg  = '料号['.$ebay_sku.']数量为[1],如正确可扫描下一个 ';
               	$u_sql = OrderReviewModel::updateRow("set amount=1,isScan=1","where shipOrderId='{$ordercheck[0]['id']}' and sku='$ebay_sku' and is_delete=0 and storeId=1");

			}else{
				self::$errMsg  = '料号['.$ebay_sku.']数量为'.$sku_info[0]['totalNums'].',请扫描核对 ';
			
    			if($sku_info[0]['amount']+1==$sku_info[0]['totalNums']){
    				$u_sql = OrderReviewModel::updateRow("set amount=amount+1,isScan=1","where shipOrderId='{$ordercheck[0]['id']}' and sku='$ebay_sku' and is_delete=0 and storeId=1");
    			}else{
    				$u_sql = OrderReviewModel::updateRow("set amount=amount+1,isScan=0","where shipOrderId='{$ordercheck[0]['id']}' and sku='$ebay_sku' and is_delete=0 and storeId=1");
    			}
            }
				$skuinfos = OrderReviewModel::getReviewList("*","where shipOrderId='{$ordercheck[0]['id']}' and is_delete=0 and storeId=1");
	
			//判断料号是否全部符合正确
            if($skuinfos){
                foreach($skuinfos as $info){
				if($info['isScan']==0){
					$bool = 0;
					break;
				}else{
					$bool = 1;
				}
		    	}
            }

			
			self::$errCode = $bool;
			return $skuinfos;
		}
	
	}
	
	//数量验证
	function act_scanNum(){
		$userId 	  = $_SESSION['userId'];
		$order_id     = trim($_POST['ebay_id']);
		$ebay_sku 	  = trim($_POST['ebay_sku']);
		$ebay_sku     = get_goodsSn($ebay_sku);
		$sku_num  	  = trim($_POST['sku_num']);
		$state_status = array(PKS_WIQC);
		//先核对订单
		$p_real_ebayid = '#^\d+$#';
		if(	!preg_match($p_real_ebayid,$order_id) ){
			self::$errCode = "1100";
			self::$errMsg  = "订单号[".$order_id."]格式有误";
			return false;			
		}
		if($is_eub_package_type===true){
			$ordercheck = ShippingOrderModel::getShippingOrderInfo("b.*","where a.tracknumber='$order_id' and a.is_delete=0");
		}else{
			$ordercheck = ShippingOrderModel::getShippingOrder("*","where id='$order_id'");
		}
		
		if(empty($ordercheck)){
			self::$errCode = '100';
			self::$errMsg  = '未找到订单['.$order_id.']';
			return false;
		}else{
			if (!in_array($ordercheck[0]['orderStatus'], $state_status)){
				self::$errCode = '100';
				self::$errMsg  = "该订单[{$order_id}]在".LibraryStatusModel::getStatusNameByStatusCode($ordercheck[0]['orderStatus'])."状态,请确认!";
				return false;
			}
		}
					
		$bool 	  = 0;
		$sku_info = OrderReviewModel::getReviewList("*","where shipOrderId='{$ordercheck[0]['id']}' and sku='$ebay_sku' and is_delete=0 and storeId=1");
		if(empty($sku_info)){
			self::$errCode = '005';
			self::$errMsg  = '订单不存在该料号['.$ebay_sku.'],请重试';
			return false;
		}elseif($sku_num!=$sku_info[0]['totalNums']){
			self::$errCode = '007';
			self::$errMsg  = '料号['.$ebay_sku.']正确数量应为['.$sku_info[0]['totalNums'].'],请检查输入数量';
  	        $u_sql = OrderReviewModel::updateRow("set amount='{$sku_num}',isScan=1","where shipOrderId='{$ordercheck[0]['id']}' and sku='$ebay_sku' and is_delete=0 and storeId=1");
				
		}else{
			self::$errMsg = '料号['.$ebay_sku.']数量['.$sku_info[0]['totalNums'].']正确,如无误请扫描下一料号 ';
			$u_sql = OrderReviewModel::updateRow("set amount='{$sku_num}',isScan=1","where shipOrderId='{$ordercheck[0]['id']}' and sku='$ebay_sku' and is_delete=0 and storeId=1");
			
			if($u_sql){
				$skuinfos = OrderReviewModel::getReviewList("*","where shipOrderId='{$ordercheck[0]['id']}' and is_delete=0 and storeId=1");
			}
			//判断料号是否全部符合正确
			foreach($skuinfos as $info){
				if($info['isScan']==0){
					$bool = 0;
					break;
				}else{
					$bool = 1;
				}
			}
			

		}
		    self::$errCode = $bool;
			return $skuinfos;
	}
	
	//复核完成
	function act_complete(){
		$order_id     = trim($_POST['ebay_id']);
		//先核对订单
		$p_real_ebayid='#^\d+$#';
		if(!preg_match($p_real_ebayid,$order_id) ){
			self::$errCode = "100";
			self::$errMsg  = "订单号[".$order_id."]格式有误";
			return false;			
		}
		$ordercheck = ShippingOrderModel::getShippingOrder("*","where id='$order_id'");

		$complete = ShippingOrderModel::update(array('orderStatus'=>PKS_WWEIGHING),"and id='{$ordercheck[0]['id']}'");
	//	$complete = 111;
        if($complete){
			$time = time();
			OmAvailableModel::updateTNameRow("wh_shipping_order_records","set reviewerId='{$_SESSION['userId']}',reviewTime={$time}","where shipOrderId={$ordercheck[0]['id']}");
			WhPushModel::pushOrderStatus($ordercheck[0]['id'],'PKS_WWEIGHING',$_SESSION['userId'],$time);        //状态推送，需要改为待包装称重（订单系统提供状态常量）
			self::$errMsg  = '订单['.$ordercheck[0]['id'].']复核(拍照)成功,请扫描复核下一订单';
			return $ordercheck[0]['id'];
		}else{
			self::$errCode = "006";
			self::$errMsg  = '订单复核出现异常,请重新复核';
			return false;
		}
	}
    //手动完结有异常的复核分拣的时候显示料号的相关信息
    public function act_setShippingEnd(){
       	$order_id     = $_POST['ebay_id'];
		//先核对订单
		$p_real_ebayid='#^\d+$#';
		if(!preg_match($p_real_ebayid,$order_id) ){
			self::$errCode = "100";
			self::$errMsg  = "订单号[".$order_id."]格式有误";
			return false;			
		}
       	$ordercheck = ShippingOrderModel::getShippingOrder("*","where id='$order_id'");
        if($ordercheck){
        	$skuinfos = OrderReviewModel::getReviewList("*","where shipOrderId='{$ordercheck[0]['id']}' and is_delete = 0");
	        if($skuinfos){
      	 	    self::$errCode = 200;
		      	self::$errMsg  = '确定手动完结复核该订单吗';
		      	return $skuinfos;

	        }else{
	         	self::$errCode = "007";
		      	self::$errMsg  = '订单没有在复核记录表中,请确认复核';
		      	return false;
	        }
        }else{
         	self::$errCode = "006";
			self::$errMsg  = '订单复核出现异常,请重新复核';
			return false;
        }
    }
    //手动完结有异常的复核分拣
    public function act_completion(){
        $order_id     = $_POST['ebay_id'];
		//先核对订单
		$p_real_ebayid='#^\d+$#';
		if(!preg_match($p_real_ebayid,$order_id) ){
			self::$errCode = "100";
			self::$errMsg  = "订单号[".$order_id."]格式有误";
			return false;			
		}
        	$complete = ShippingOrderModel::update(array('orderStatus'=>PKS_UNUSUAL_SHIPPING_INVOICE),"and id='{$order_id}'");
    //	$complete = 111;
    	if($complete){
			$time = time();
            	OmAvailableModel::updateTNameRow("wh_order_review_records","set scanUserId='{$_SESSION['userId']}',scanTime={$time},isScan = 1","where shipOrderId={$order_id}");	
		//	WhPushModel::pushOrderStatus($ordercheck[0]['id'],PKS_UNUSUAL_SHIPPING_INVOICE,$_SESSION['userId'],$time);        //状态推送，需要改为异常发货单（订单系统提供状态常量）
			self::$errCode = 200;
        	self::$errMsg  = '订单['.$order_id.']复核(拍照)成功,请扫描复核下一订单';
            $res ['shipOrderId'] =$order_id;
			return $res;
		}else{
			self::$errCode = "006";
			self::$errMsg  = '订单复核出现异常,请重新复核';
			return false;
		}
    }
	
}


?>