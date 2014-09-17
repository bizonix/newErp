<?php
/**
*类名：订单添加管理
*功能：订单添加处理
*作者：hws
*
*/
class OrderAddAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";

	//获取平台信息
	function act_getPlatformInfo(){
		$id   = $_POST['id'];
		$list =	OmAvailableModel::getTNameList("om_account","*","where is_delete=0 and platformId='$id'");
		if($list){
			return $list;
		}else{
			self::$errCode = OmAvailableModel::$errCode;
			self::$errMsg  = OmAvailableModel::$errMsg;
			return false;
		}
	}

    /*
	 * 根据platformId取得对应账号，连接权限
	 */
	function act_getAccountListByPlatform() {
	    $pid   = !empty($_POST['platformId'])?$_POST['platformId']:0;
        $accountList = omAccountModel::accountListByPid($pid);
		if(!$accountList){
			self::$errCode = 400;
			self::$errMsg = "没取到对应账号列表！";
			return $accountList;
		}else{
			self::$errCode = 200;
			self::$errMsg = "获取到账号列表！";
			return $accountList;
		}
	}

	//填充产品
	function  act_getSkuInfo(){
		$sku_row     = trim($_POST['sku_row']);
		$all_sku_arr = array();
		$sku_arr     = array();
		$all_sku_arr = array_filter(explode("\n",$sku_row));
		foreach($all_sku_arr as $all_sku){
			$sku_arr[] = explode("\t",$all_sku);
		}
		return 	$sku_arr;
	}

	//获取用户信息
	function  act_getUserInfo(){
		$userid 	= trim($_POST['userid']);
		$platformId = trim($_POST['platformId']);
		$user_info  = OrderAddModel::getBuyerInfo("*","where platformId={$platformId} and platformUsername='$userid'");
		if($user_info){
			return $user_info;
		}else{
			self::$errCode = 003;
			self::$errMsg  = "目前系统不存在该用户信息，请自行添加！";
			return false;
		}
	}

	//检测订单是否存在
	function  act_checkOrder(){
		$orderid 	 = trim($_POST['orderid']);
		$account 	 = trim($_POST['account']);
		$checkrecord = OmAvailableModel::getTNameList("om_shipped_order","*","where is_delete=0 and recordNumber='$orderid' and accountId='$account'");

		if(!$checkrecord){
			return true;
		}else{
			self::$errCode = 003;
			self::$errMsg  = "该订单号码已经存在，请确认！";
			return false;
		}
	}

	//检测订单是否存在
	/*function  act_addOrderAPI(){
		$orderData 	 = !empty($_POST['orderData']) ? trim($_POST['orderData']) : '';
		if($orderData){

		}
		$insert = OrderAddModel::insertAllOrderRowNoEvent($orderData);
		if($_spitId = OrderAddModel :: insertAllOrderRowNoEvent($insertOrderData)){
			if(!OrderLogModel::insertOrderLog($_spitId, 'INSERT ORDER')){
				self :: $errCode = '001';
				self :: $errMsg = "插入订单日志失败!";
				return false;
			}
			if(!OrderRecordModel::insertSpitRecords($orderId,$_spitId)){
				self :: $errCode = '002';
				self :: $errMsg = "插入拆分日志失败!";
				return false;
			}
		}else{
			self::$errCode = 003;
			self::$errMsg  = "订单插入成功！";
			return false;
		}
	}*/

	//检测订单是否存在
	function  act_addOrderAPI(){
		$orderData 	 = !empty($_POST['orderData']) ? trim($_POST['orderData']) : '';
		if($orderData){

		}
		$insert = OrderAddModel::insertAllOrderRowNoEvent($orderData);
		if($_spitId = OrderAddModel :: insertAllOrderRowNoEvent($insertOrderData)){
			if(!OrderLogModel::insertOrderLog($_spitId, 'INSERT ORDER')){
				self :: $errCode = '001';
				self :: $errMsg = "插入订单日志失败!";
				return false;
			}
			if(!OrderRecordModel::insertSpitRecords($orderId,$_spitId)){
				self :: $errCode = '002';
				self :: $errMsg = "插入拆分日志失败!";
				return false;
			}
		}else{
			self::$errCode = 003;
			self::$errMsg  = "订单插入成功！";
			return false;
		}
	}

	//增加订单
	function  act_sureAddOrder(){
		$order_data    = array();
		$detail_data   = array();
		$exten_data    = array();
		$userinfo_data = array();
		$buyer_data	   = array();
		$time 		   = time();

		$platform_id      = trim($_POST['platform']);
		$username    	  = trim($_POST['fullname']);
		$account_id  	  = trim($_POST['account']);
		$street1 	  	  = trim($_POST['street1']);
		$platformUsername = trim($_POST['userid']);
		$email  		  = trim($_POST['ebay_usermail1']);
		$street2 	  	  = trim($_POST['street2']);
		$recordNumber 	  = trim($_POST['orderid']);
		$city 		  	  = trim($_POST['city']);
		$ordersTime 	  = strtotime(trim($_POST['ebay_createdtime']));
		$state       	  = trim($_POST['state']);
		$paymentTime      = strtotime(trim($_POST['ebay_paidtime']));
		$countryname 	  = trim($_POST['country']);
		$ebay_itemprice   = trim($_POST['ebay_itemprice']);
		$zipCode    	  = trim($_POST['zip']);
		$shippingFee      = trim($_POST['ebay_shipfee']);
		$ebay_tel1 		  = trim($_POST['tel1']);
		$actualTotal 	  = trim($_POST['ebay_total']);
		$ebay_tel2 	      = trim($_POST['tel2']);
		$ebay_tel3 	      = trim($_POST['tel3']);
		$currency    	  = trim($_POST['ebay_currency']);
		$other_currency   = trim($_POST['other_currency']);

        $isCheckOrder = self::act_checkOrder();

        $returnArr = array();//返回的数组信息

        if(!$isCheckOrder){
        	$returnArr['errCode'] = self::$errCode;
        	$returnArr['errMsg'] = self::$errMsg;
            return $returnArr;
        }

		if($currency=='其他'){
			$currency = $other_currency;
		}

		$phone			  = trim($_POST['tel1']);
		$transId 		  = trim($_POST['ebay_ptid']);
		$other_ptid 	  = trim($_POST['other_ptid']);
		if($transId=='paypal' || $transId=='Escrow' || $transId=='其他'){
			$transId = $other_ptid;
		}

		$PayPalPaymentId = $transId;

		$orderweight      = trim($_POST['orderweight']);
		$ebay_usermail2   = trim($_POST['ebay_usermail2']);
		$ebay_carrier     = trim($_POST['ebay_carrier']);
		$ebay_usermail3   = trim($_POST['ebay_usermail3']);
		$ebay_tracknumber = trim($_POST['ebay_tracknumber']);
		$ebay_noteb       = trim($_POST['ebay_noteb']);
		$orderStatus 	  = 100;
		$orderType   	  = 101;
		$tracknumber       = trim($_POST['ebay_tracknumber']);
		//order信息
		$orderData[$recordNumber]['orderData']['recordNumber'] = $recordNumber;
		$orderData[$recordNumber]['orderData']['ordersTime'] = $ordersTime;
		$orderData[$recordNumber]['orderData']['paymentTime'] = $paymentTime;
		$orderData[$recordNumber]['orderData']['actualTotal'] = $actualTotal;
		$orderData[$recordNumber]['orderData']['onlineTotal'] = $actualTotal;//默认线上总价和实际总价一样
		$orderData[$recordNumber]['orderData']['orderAddTime'] = time();
		//$orderData[$recordNumber]['orderData']['calcWeight'] = $orderweight;//估算重量

		$orderData[$recordNumber]['orderData']['accountId']  = $account_id;
		$orderData[$recordNumber]['orderData']['platformId'] = $platform_id;
		//添加状态信息
		$orderData[$recordNumber]['orderData']['orderStatus'] = 100;
		$orderData[$recordNumber]['orderData']['orderType'] = 101;

		$SYS_ACCOUNTS = OmAvailableModel::getPlatformAccount();
		foreach($SYS_ACCOUNTS as $platform=>$accounts){
			foreach($accounts as $accountId =>$accountname){
				if($account_id == $accountId){
					if($platform == 'ebay'){//为ebay平台
						$orderData[$recordNumber]['orderData']['isFixed'] = 2;
					}else{
						$orderData[$recordNumber]['orderData']['isFixed'] = 1;
					}
				}
			}
		}
		$transportation = CommonModel::getCarrierList();   //所有的
		foreach($transportation as $tranValue){
			if($tranValue['id'] == $ebay_carrier){
				$orderData[$recordNumber]['orderData']['transportId'] = $tranValue['id'];
				break;
			}
		}

		//order扩展信息
		$orderData[$recordNumber]['orderExtenData']['currency'] 			=   $currency;
		$orderData[$recordNumber]['orderExtenData']['paymentStatus']		=	"PAY_SUCCESS";
		//$orderData[$recordNumber]['orderExtenData']['transId']			    =	$transId;
		$orderData[$recordNumber]['orderExtenData']['PayPalPaymentId']		=	$PayPalPaymentId;
		$orderData[$recordNumber]['orderExtenData']['platformUsername']		=	$platformUsername;

		//user信息
		$orderData[$recordNumber]['orderUserInfoData']['platformUsername'] = $platformUsername;
		$orderData[$recordNumber]['orderUserInfoData']['username'] = $username;
		$orderData[$recordNumber]['orderUserInfoData']['email'] = $email;
		$orderData[$recordNumber]['orderUserInfoData']['street'] = $street1;
		$orderData[$recordNumber]['orderUserInfoData']['currency'] = $currency;
		$orderData[$recordNumber]['orderUserInfoData']['address2'] = $street2;
		$orderData[$recordNumber]['orderUserInfoData']['city'] = $city;
		$orderData[$recordNumber]['orderUserInfoData']['state'] = $state;
		$orderData[$recordNumber]['orderUserInfoData']['zipCode'] = $zipCode;
		$orderData[$recordNumber]['orderUserInfoData']['countryName'] = $countryname;
		$orderData[$recordNumber]['orderUserInfoData']['landline'] = !empty($ebay_tel2)?$ebay_tel2:$ebay_tel3;
		$orderData[$recordNumber]['orderUserInfoData']['phone'] = $phone;


		//note信息
		if(!empty($ebay_noteb)){
			$orderData[$recordNumber]['orderNote']['content'] = $ebay_noteb;
			$orderData[$recordNumber]['orderNote']['userId'] = $_SESSION['sysUserId'];
			$orderData[$recordNumber]['orderNote']['createdTime'] = time();
		}

		//tracknumer信息
		$orderData[$recordNumber]['orderTrack']['tracknumber'] = $tracknumber;


		$sku_list		  = $_POST['sku'];
		$sku_count 		  = $_POST['qty'];
		$ebay_itemtitle   = $_POST['name'];

		$count = count($sku_list);
		for($i=0;$i<$count;$i++){
			//detail信息
			$sku = $sku_list[$i];
			$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailData']['sku'] = $sku_list[$i];
			$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailData']['amount'] += $sku_count[$i];//累加
			$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailData']['recordNumber'] = $recordNumber;
			$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailData']['createdTime'] = time();
			$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailExtenData']['itemTitle'] = $ebay_itemtitle[$i];
		}

		foreach($orderData as $id => $order){//$orderData 中第一维只有一个元素，方便起见这里用foreach，虽然只循环一次

			$ret = commonModel::checkOrder($recordNumber);
			if($ret){
				$returnArr['errCode'] = 101;
	        	$returnArr['errMsg'] = "订单{$recordNumber}已存在！";
	            return $returnArr;
			}

			//计算订单属性
			if(count($order['orderDetail'])==1){
				$detail = current($order['orderDetail']);
				if($detail['orderDetailData']['amount']==1){
					$orderData[$id]['orderData']['orderAttribute'] = 1;
				}else{
					$orderData[$id]['orderData']['orderAttribute'] = 2;
				}
			}else{
				$orderData[$id]['orderData']['orderAttribute'] = 3;
			}
			//计算订单重量及包材
			$obj_order_detail_data = array();
			foreach($order['orderDetail'] as $sku => $detail){
				$obj_order_detail_data[] = $detail['orderDetailData'];
			}

			$weightfee = commonModel::calcOrderWeight($obj_order_detail_data);
			$orderData[$id]['orderData']['calcWeight'] = $weightfee[0];
			$orderData[$id]['orderData']['pmId'] = $weightfee[1];

			$calcShippingInfo = CommonModel :: calcAddOrderShippingFee($orderData[$id],$orderData[$id]['orderData']['isFixed']);//计算运费
			$orderData[$id]['orderData']['channelId'] 	= $calcShippingInfo['channelId'];
				$orderData[$id]['orderData']['calcShipping'] = $calcShippingInfo['fee'];

			//调用旧系统接口，先插入数据到旧系统
			$rtn = OldsystemModel::orderErpInsertorder($orderData[$id]);
			$insertData = array();
			if(empty($rtn)){
				$returnArr['errCode'] = 102;
	        	$returnArr['errMsg'] = "接口返回异常，请重试！";
	            return $returnArr;
			}
			if($rtn['errcode'] == 200){
				$rtn_data = $rtn['data'];
				$orderId = $rtn_data['orderId'];
				//echo "插入老系统成功，订单编号 [$orderId] \n";
				$pmId = $rtn_data['pmId'];
				$totalweight = $rtn_data['totalweight'];
				$shipfee = $rtn_data['shipfee'];
				$carrier = $rtn_data['carrier'];
				$carrierId = $rtn_data['carrierId'];
				$status = $rtn_data['status'];

				$orderData[$id]['orderData']['id'] = $orderId;//赋予新系统订单编号

				if($orderData['orderData']['calcWeight'] != $totalweight){
					$insertData['old_totalweight'] = $totalweight;
					$insertData['new_totalweight'] = $orderData[$id]['orderData']['calcWeight'];
                    $orderData[$id]['orderData']['calcWeight'] = $totalweight;
				}
				if($orderData['orderData']['pmId'] != $pmId){
					$insertData['old_pmId'] = $pmId;
					$insertData['new_pmId'] = $orderData[$id]['orderData']['pmId'];
                    $orderData[$id]['orderData']['pmId'] = $pmId;
				}

				if($orderData['orderData']['calcShipping'] != $shipfee){
					$insertData['old_shippfee'] = $shipfee;
					$insertData['new_shippfee'] = $orderData[$id]['orderData']['calcShipping'];
                    $orderData[$id]['orderData']['calcShipping'] = $shipfee;
				}
				if($orderData['orderData']['transportId'] != $carrierId){
					$insertData['old_carrierId'] = $carrierId;
					$insertData['new_carrierId'] = $orderData[$id]['orderData']['transportId'];
                    $orderData[$id]['orderData']['transportId'] = $carrierId;
				}

				if(!empty($insertData)){
					$insertData['ebay_id'] = $orderId;
					$insertData['addtime'] = time();
					//var_dump($insertData);
					OldsystemModel::insertTempSyncRecords($insertData);// 插入临时对比记录表
				}
                //缺货拦截
				$orderData[$id] = AutoModel :: auto_contrast_intercept($orderData[$id]);
				//插入订单
				$info = OrderAddModel::insertAllOrderRowNoEvent($orderData[$id]);
				if($info){
					$returnArr['errCode'] = 200;
		        	$returnArr['errMsg'] = "订单{$id}上传成功！";

				}else{
					$returnArr['errCode'] = 404;
		        	$returnArr['errMsg'] = "订单{$id}上传失败！";
				}
			}else{
				$returnArr['errCode'] = 400;
		        $returnArr['errMsg'] = "添加失败，原因为：{$rtn['msg']}！";
			}
		}
		return $returnArr;
	}



	/***
	 * 检测系统特定平台是否存在重复订单
	 * 同时检测已发货表， 待发货两个表
	 * @param	string	$orderId(recordumber)	各平台的订单号
	 * @return	boolean	true(重复)		false(不重复)
	 */
	function checkDuplicateOrder($orderId, $platformId){

		$omAvailableAct = new OmAvailableAct();
		$where  = 	" WHERE recordNumber = '$orderId' AND is_delete = 0 and platformId = '$platformId'";
        $res	=  	$omAvailableAct->act_getTNameList('om_unshipped_order', 'id', $where);
        $res2	=  	$omAvailableAct->act_getTNameList('om_shipped_order', 'id', $where);

        if(empty($res) && empty($res2)){
        	return false;
        }else{
        	return true;
        }

	}
	function act_syncOrder(){      //手动同步旧系统订单至新系统
		$orderDatas = isset($_GET['orderDatas'])?$_GET['orderDatas']:"";

		if(!$orderDatas){
			self::$errCede = '002';
			self::$errMsg = '参数为空！';
			return false;
		}
		$orderDatas = json_decode($orderDatas,true);
		foreach($orderDatas as $key=>$order){
			$where = "id={$order['order']['id']}";
			$msg1 = OmAvailableModel::getTNameList("om_unshipped_order","*",$where);
			$msg2 = OmAvailableModel::getTNameList("om_shipped_order","*",$where);
			if($msg1 || $msg2){
				self::$errMsg .= "订单{$order['order']['id']}已存在！";
				unset($orderDatas[$key]);
			}
		}
		$log_data = '';
		$tablekeys = array();
		$tabledatas = array();
		foreach ($orderDatas AS $odk=>$orderD){
			if (!isset($orderD['order']['orderStatus'])){
				//var_dump($orderD,$orderD['order'],$orderD['order']['orderStatus']);//exit;
				//echo "no status{$orderD['order']['id']}\n";
			}
			//'orderStauts'=>900, //0
			//'orderType'=>21, //2

			$tou = $orderD['order']['orderStatus']==900&&$orderD['order']['orderType']==21 ? 'om_shipped_' : 'om_unshipped_';
			foreach ($orderD AS $dk=>$dataL){
				if ($dk=='orderDetail'){
					foreach ($dataL AS $ssdata){
						foreach ($ssdata AS $tkey=>$tdata){
							//var_dump($tkey, $tdata);
							$tablekeys[$tou.$tkey] = array_keys($tdata);
							$tabledatas[$tou.$tkey][] = '('.implode(',', _array2strarray(array_values($tdata))).')';
							//$log_data .= "INSERT INTO {$tkey} SET "._array2sql($tdata)."\n\n";
						}
					}
				}else{
					$tou2 = in_array($dk, array('om_order_notes', 'om_order_tracknumber')) ? '' : $tou;
					$tablekeys[$tou2.$dk] = array_keys($dataL);
					$tabledatas[$tou2.$dk][] = '('.implode(',', _array2strarray(array_values($dataL))).')';
				}
			}
		}
		unset($orderDatas);
		//echo $log_data;
		$sql = '';
		foreach ($tablekeys AS $tablen=>$tablekey){
			//if (empty($tabledatas[$tablen])) var_dump($tabledatas[$tablen], $tabledatas);
			//INSERT INTO `valsun_ordermanage`.`om_unshipped_order` (`id`, `recordNumber`, `platformId`, `accountId`, `ordersTime`, `paymentTime`, `onlineTotal`, `actualTotal`, `transportId`, `marketTime`, `ShippedTime`, `orderStatus`, `orderType`, `orderAttribute`, `pmId`, `isFixed`, `channelId`, `calcWeight`, `calcShipping`, `orderAddTime`, `isSendEmail`, `isNote`, `isCopy`, `isSplit`, `combinePackage`, `combineOrder`, `isBuji`, `isLock`, `lockUser`, `lockTime`, `storeId`, `is_delete`) VALUES (NULL, 'fds', '', '', '', '', '0.00', '0.00', '', NULL, NULL, '', NULL, '1', NULL, '2', NULL, '0.000', '0.000', '', '0', '0', '0', '0', '0', '0', '0', '0', NULL, NULL, '1', '0');
			//$sql = "INSERT INTO {$tablen}(".implode(',', $tablekey).") VALUES ".implode(',', $tabledatas[$tablen]).";\n";
			$key = implode(',', $tablekey);
			$value = implode(',', $tabledatas[$tablen]);
			$info = OmAvailableModel::insertRowUseValue($tablen,$key,$value);
			if(!$info){
				self::$errMsg .= "插入订单{$tabledatas[$tablen][0]}失败！";
			}
		}
		if(self::$errMsg==""){
			self::$errMsg = "所选订单已插入新系统！";
			self::$errCode = '200';
			return true;
		}else{
			self::$errCode = '002';
			return false;
		}
		/*$info = orderAddModel::syncOrder($orderDatas);
		if($info){
			self::$errCode = orderAddModel::$errCode;
			self::$errMsg = orderAddModel::$errMsg;
			return true;
		}else{
			self::$errCode = orderAddModel::$errCode;
			self::$errMsg = orderAddModel::$errMsg;
			return false;
		}*/

	}



}


?>