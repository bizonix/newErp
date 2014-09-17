<?php
/*
 * 订单添加控制器，所以插入订单需要采用POST方式接受数据
 * @add by lzx ,date 20140609
 */
class OrderAddAct extends CheckAct{
	
	public function __construct(){
		parent::__construct();
	}
	
	public function act_insertOrder($orderData, &$failures=array()){
		$failures = array();
		foreach($orderData as $id => $order){//$orderData 中第一维只有一个元素，方便起见这里用foreach，虽然只循环一次
			M('Base')->begin();
			//计算订单属性
			if(count($order['orderDetail'])==1){
				$detail = current($order['orderDetail']);
				if($detail['orderDetail']['amount']==1){
					$order['order']['orderAttribute'] = 1;
				}else{
					$order['order']['orderAttribute'] = 2;
				}
			}else{
				$order['order']['orderAttribute'] = 3;
			}

			$platformId = intval($order['order']['platformId']);
			$ORtransport = $order['order']['ORtransport'];
			$tmp_arr = M('PlatformToCarrier')->getPlatformToCarrierByINA($platformId,$ORtransport);
			
			if(!empty($tmp_arr) && $tmp_arr[0]['isExpressDelivery']==1){
				$order['order']['isExpressDelivery'] = 1;
			}
			
			//调用旧系统接口，先插入数据到旧系统
			/**
			  
			 $rtn = M('InterfaceOldErp')->orderErpInsertorder($order);//待老系统接口更新后再调试
			
			if(empty($rtn)||$rtn['errcode']!=200){
				$failures[] = get_promptmsg(10119, $order['order']['recordNumber']);
				M('Base')->rollback();
				continue;
			}
			/**/
			$rtn = array();
			$insertData = array();
			$rtn_data = $rtn['data'];
			$orderId = $rtn_data['orderId'];
			$pmId = $rtn_data['pmId'];
			$totalweight = $rtn_data['totalweight'];
			$shipfee = $rtn_data['shipfee'];
			$carrier = $rtn_data['carrier'];
			$carrierId = $rtn_data['carrierId'];
			$status = $rtn_data['status'];
			$order['order']['id'] = rand(1400000, 2000000);//赋予新系统订单编号
			if($orderData['order']['calcWeight'] != $totalweight){
				$insertData['old_totalweight'] = $totalweight;
				$insertData['new_totalweight'] = $order['order']['calcWeight'];
				$order['order']['calcWeight'] = $totalweight;
			}
			if($orderData['order']['pmId'] != $pmId){
				$insertData['old_pmId'] = $pmId;
				$insertData['new_pmId'] = $order['order']['pmId'];
				$order['order']['pmId'] = $pmId;
			}

			if($orderData['order']['calcShipping'] != $shipfee){
				$insertData['old_shippfee'] = $shipfee;
				$insertData['new_shippfee'] = $order['order']['calcShipping'];
				$order['order']['calcShipping'] = $shipfee;
			}
			if($orderData['order']['transportId'] != $carrierId){
				$insertData['old_carrierId'] = $carrierId;
				$insertData['new_carrierId'] = $order['order']['transportId'];
				$order['order']['transportId'] = $carrierId;
			}

			if(!empty($insertData)){
				$insertData['ebay_id'] = $orderId;
				$insertData['addtime'] = time();
				M('orderAdd')->insertTempSyncRecords($insertData);// 插入临时对比记录表
			}
			
			//插入订单
			if (!M('OrderAdd')->insertOrderPerfect($order)) {
				$failures[] = get_promptmsg(10120, $order['order']['recordNumber']);
				M('Base')->rollback();
			}else{
                M('Base')->commit();
				
                
                
                
                //获取新插入订单的id
				$tmp_order_id = M('OrderAdd')->getInsertOrderId();
				$order['order']['id'] = $tmp_order_id;
				
				M('orderLog')->orderOperatorLog('no sql', '订单初始化成功，开始拦截逻辑', $tmp_order_id);
				
				
                //订单拦截 拦截后更新订单的状态和分类
				//F('FormatOrder')->interceptOrder($order);
				getInstance('formatorder')->interceptOrder($order);
				
				//统计订单中sku的日均销量
				F('SkuDailyInfo')->updateDailyAverageInfoByOrder($tmp_order_id);
				
				
				
				
				
				
                M('Base')->begin();
          
				//插入快递订单的快递描述
				if($order['order']['isExpressDelivery'] == 1 && !empty($order['order']['orderDetail'])){
					
					$tmp_detail_arr = $order['order']['orderDetail'];
					$declared_sku_arr = array();
					
					foreach($tmp_detail_arr as $tmp_detail){
						$declared_sku_arr[$tmp_detail['orderDetail']['sku']] = array(
								'amount'=>$tmp_detail['orderDetail']['itemPrice'],
								'price'=>$tmp_detail['orderDetail']['amount']
						);
					}
					
					if($declared_sku_arr){
						$result = M('ExpressRemark')->insertDeclarationRemark($tmp_order_id,$declared_sku_arr);
						if($result === true){
							M('orderLog')->orderOperatorLog('no sql', '快递描述生成成功', $tmp_order_id);
						}else{
							$log_msg = '快递描述生成失败：'.M('ExpressRemark')->getErrorMsg();
							M('orderLog')->orderOperatorLog('no sql', $log_msg, $tmp_order_id);
						}
					}
					
				}
				
				
				
				M('Base')->commit();
			}
		}
		return empty($failures) ? true : false;
	}
    
	/**
	 * 添加普通线下订单页面 ，目前就是出口通订单添加
	 * 需要和对应的用户平台、账号权限对应，只有自己有权限的才能添加 
	 * 获取相关权限  A('UserCompetence')->act_getCompetenceByUserId(get_userid())
	 * 以下为demo
	 * om_shipped_order
		om_shipped_order_detail
		om_shipped_order_detail_extension_aliexpress
		om_shipped_order_detail_extension_amazon
		om_shipped_order_detail_extension_cndl
		om_shipped_order_detail_extension_domestic
		om_shipped_order_detail_extension_ebay
		om_shipped_order_detail_extension_newegg
		om_shipped_order_detail_extension_tmall
		om_shipped_order_extension_aliexpress
		om_shipped_order_extension_amazon
		om_shipped_order_extension_cndl
		om_shipped_order_extension_domestic
		om_shipped_order_extension_ebay
		om_shipped_order_extension_newegg
		om_shipped_order_extension_tmall
		om_shipped_order_userInfo
	 * @return bool
	 * @author lzx
	 */
    public function act_insertOfflineOrder(){
    	################ start 格式化POST信息到统一数组 ##################
    	#$data['order'] = $_POST;//format post;  线下订单需要增加字段is_offorder = 1 
    	#$data['order_detail'] = $_POST;
    	#$data['order_userInfo'] = $_POST;
    	#$data['order_extension_xxxx'] = $_POST;  //xxxx表示对应平台
    	#$data['order_detail_extension_xxxx'] = $_POST;   //xxxx表示对应平台
        
        //$returnArr = array();//定义要返回的数组
        $is_offorder = 1;//线下订单，手动添加或导入的订单都算线下订单
        $platform_id      = trim($_POST['platform']);//平台id
		$username    	  = trim($_POST['fullname']);//买家全名
		$account_id  	  = trim($_POST['account']);//账号id
		$street1 	  	  = trim($_POST['street1']);//街道1
		$platformUsername = trim($_POST['userid']);//买家平台id
		$email  		  = trim($_POST['ebay_usermail1']);//买家邮箱1
		$street2 	  	  = trim($_POST['street2']);//买家地址2
		$recordNumber 	  = trim($_POST['orderid']);//对应的平台订单号        
        //这里还要验证该$account_id下这个$recordNumber是否存在
        if(M('OrderAdd')->checkIsExists(array('recordNumber'=>$recordNumber, 'accountId'=>$account_id))){
        	self::$errMsg[] = get_promptmsg(10043, $recordNumber);//"该recordNumber已经存在<br/>";
            return false;
        }
		$city 		  	  = trim($_POST['city']);//买家所在城市
		$ordersTime 	  = strtotime(trim($_POST['ebay_createdtime']));//订单在平台的生成时间
		$state       	  = trim($_POST['state']);//买家所在州
		$paymentTime      = strtotime(trim($_POST['ebay_paidtime']));//订单付款时间
		$countryname 	  = trim($_POST['country']);//国家
		$ebay_itemprice   = trim($_POST['ebay_itemprice']);//单品价格
		$zipCode    	  = trim($_POST['zip']);//邮编
		$shippingFee      = trim($_POST['ebay_shipfee']);//运费
		$ebay_tel1 		  = trim($_POST['tel1']);//电话
		$actualTotal 	  = trim($_POST['ebay_total']);//订单实际总价
		$ebay_tel2 	      = trim($_POST['tel2']);
		$ebay_tel3 	      = trim($_POST['tel3']);
		$currency    	  = trim($_POST['ebay_currency']);//币种
		$other_currency   = trim($_POST['other_currency']);
        if($currency=='其他'){
			$currency = $other_currency;
		}
        $phone			  = trim($_POST['tel1']);
		$transId 		  = trim($_POST['ebay_ptid']);//付款方式
		$other_ptid 	  = trim($_POST['other_ptid']);
        if($transId=='paypal' || $transId=='Escrow' || $transId=='其他'){
			$transId = $other_ptid;
		}
        $orderweight      = trim($_POST['orderweight']);//订单重量
		$ebay_usermail2   = trim($_POST['ebay_usermail2']);
		$ebay_carrier     = trim($_POST['ebay_carrier']);//运输方式id
		$ebay_usermail3   = trim($_POST['ebay_usermail3']);
		$ebay_noteb       = trim($_POST['ebay_noteb']);//卖家备注
		$orderStatus 	  = 100;
		$orderType   	  = 101;
		$tracknumber       = trim($_POST['ebay_tracknumber']);//跟踪号
    	################  end 格式化POST信息到统一数组   ##################       
        //order信息
        $orderData[$recordNumber]['order']['is_offline'] = $is_offorder;
		$orderData[$recordNumber]['order']['recordNumber'] = $recordNumber;
		$orderData[$recordNumber]['order']['ordersTime'] = $ordersTime;
		$orderData[$recordNumber]['order']['paymentTime'] = $paymentTime;
		$orderData[$recordNumber]['order']['actualTotal'] = $actualTotal;
		$orderData[$recordNumber]['order']['onlineTotal'] = $actualTotal;//默认线上总价和实际总价一样
		$orderData[$recordNumber]['order']['orderAddTime'] = time();
		$orderData[$recordNumber]['order']['calcWeight'] = $orderweight;//估算重量
		$orderData[$recordNumber]['order']['accountId']  = $account_id;
		$orderData[$recordNumber]['order']['platformId'] = $platform_id;
        $orderData[$recordNumber]['order']['transportId'] = $ebay_carrier;
		//添加状态信息
		$orderData[$recordNumber]['order']['orderStatus'] = 100;
		$orderData[$recordNumber]['order']['orderType'] = 101;        
        //orderExtend信息/order扩展信息
		$orderData[$recordNumber]['orderExtension']['currency'] 			=   $currency;
		$orderData[$recordNumber]['orderExtension']['paymentStatus']		=	"PAY_SUCCESS";
		$orderData[$recordNumber]['orderExtension']['PayPalPaymentId']		=	$PayPalPaymentId;
		$orderData[$recordNumber]['orderExtension']['platformUsername']		=	$platformUsername;
        //userInfo信息
		$orderData[$recordNumber]['orderUserInfo']['platformUsername'] = $platformUsername;
		$orderData[$recordNumber]['orderUserInfo']['username'] = $username;
		$orderData[$recordNumber]['orderUserInfo']['email'] = $email;
		$orderData[$recordNumber]['orderUserInfo']['street'] = $street1;
		$orderData[$recordNumber]['orderUserInfo']['currency'] = $currency;
		$orderData[$recordNumber]['orderUserInfo']['address2'] = $street2;
		$orderData[$recordNumber]['orderUserInfo']['city'] = $city;
		$orderData[$recordNumber]['orderUserInfo']['state'] = $state;
		$orderData[$recordNumber]['orderUserInfo']['zipCode'] = $zipCode;
		$orderData[$recordNumber]['orderUserInfo']['countryName'] = $countryname;
		$orderData[$recordNumber]['orderUserInfo']['landline'] = !empty($ebay_tel2)?$ebay_tel2:$ebay_tel3;
		$orderData[$recordNumber]['orderUserInfo']['phone'] = $phone;
        //orderNote信息/卖家备注
		if(!empty($ebay_noteb)){
			$orderData[$recordNumber]['orderNote']['content'] = $ebay_noteb;
		}
        //orderTrack信息
        if(!empty($tracknumber)){
            $orderData[$recordNumber]['orderTrack']['tracknumber'] = $tracknumber;
        }		
        //orderDetail信息
        $sku_list		  = $_POST['sku'];
		$sku_count 		  = $_POST['qty'];
		$ebay_itemtitle   = $_POST['name'];
		$count = count($sku_list);
		for($i=0;$i<$count;$i++){
			$sku = $sku_list[$i];
			$orderData[$recordNumber]['orderDetail'][$i]['orderDetail']['sku'] = $sku_list[$i];
			$orderData[$recordNumber]['orderDetail'][$i]['orderDetail']['amount'] = $sku_count[$i];//累加
			$orderData[$recordNumber]['orderDetail'][$i]['orderDetail']['recordNumber'] = $recordNumber;
			$orderData[$recordNumber]['orderDetail'][$i]['orderDetail']['createdTime'] = time();
            
			$orderData[$recordNumber]['orderDetail'][$i]['orderDetailExtension']['itemTitle'] = $ebay_itemtitle[$i];
		}
        ##################################
        
		return $this->act_insertOrder($orderData);
    }    
    
	/**
	 * 添加ebay线下订单页面 
	 */
    public function act_insertEbayOfflineOrder(){
		A('OrderAdd')->insertOfflineOrder();
    }
    
	

	/**
	 * 添加amazon线下订单页面 
	 */
    public function act_insertAmazonOfflineOrder(){
		A('OrderAdd')->insertOfflineOrder();
    }

	/**
	 * 国内销售订单导入页面
	 * 以下为导入的demo
	 * @return bool
	 * @author lzx
	 */
    public function act_insertDomesticOrder(){
        if(isset($_FILES['orderUpfile']['tmp_name'])){
            $filePath  = $_FILES['orderUpfile']['tmp_name'];
            $PHPExcel  = E('PHPExcel');
            $PHPReader = new PHPExcel_Reader_Excel2007();
            if(!$PHPReader->canRead($filePath)){
                $PHPReader = new PHPExcel_Reader_Excel5();
                if(!$PHPReader->canRead($filePath)){
                    self::$errMsg[] = get_promptmsg(10058);
                    return false;
                }
            }

            $PHPExcel      = $PHPReader->load($filePath);
            $currentSheet = $PHPExcel->getSheet(0);

            $orderid = array();
            $orderData = array();
            $c = 2;
            while(true){
                $aa = 'A'.$c;
                $bb	= 'B'.$c;
                $cc	= 'C'.$c;
                $dd	= 'D'.$c;
                $ee	= 'E'.$c;
                $ff	= 'F'.$c;
                $gg	= 'G'.$c;
                $hh	= 'H'.$c;
                $ii	= 'I'.$c;
                $jj	= 'J'.$c;
                $kk	= 'K'.$c;
                $ll	= 'L'.$c;
                $mm	= 'M'.$c;
                $nn	= 'N'.$c;
                $oo	= 'O'.$c;
                $pp	= 'P'.$c;
                $qq	= 'Q'.$c;
                $rr	= 'R'.$c;
                $ss	= 'S'.$c;
                $tt	= 'T'.$c;
                $uu	= 'U'.$c;
                $vv	= 'V'.$c;
                $ww	= 'W'.$c;
                $xx	= 'X'.$c;
                $yy	= 'Y'.$c;
                $zz	= 'Z'.$c;
                $c++;
                $account 			= trim($currentSheet->getCell($aa)->getValue());
                $recordNumber 		= trim($currentSheet->getCell($bb)->getValue());
                $platformUsername 	= trim($currentSheet->getCell($cc)->getValue());
                $email				= trim($currentSheet->getCell($dd)->getValue());
                $ordersTime 		= trim($currentSheet->getCell($ee)->getValue());
                $paymentTime 		= trim($currentSheet->getCell($ff)->getValue());
                $sku 				= trim($currentSheet->getCell($gg)->getValue());
                $amount 			= trim($currentSheet->getCell($hh)->getValue());
                $itemTitle 			= trim($currentSheet->getCell($ii)->getValue());
                $note 				= trim($currentSheet->getCell($jj)->getValue());
                $itemPrice 			= trim($currentSheet->getCell($kk)->getValue());
                $shippingFee 		= trim($currentSheet->getCell($ll)->getValue());
                $actualTotal 		= trim($currentSheet->getCell($mm)->getValue());
                $currency 			= trim($currentSheet->getCell($nn)->getValue());
                $transId 			= trim($currentSheet->getCell($oo)->getValue());
                $username 			= trim($currentSheet->getCell($pp)->getValue());
                $street 			= trim($currentSheet->getCell($qq)->getValue());
                $address2 			= trim($currentSheet->getCell($rr)->getValue());
                $address3 			= trim($currentSheet->getCell($ss)->getValue());
                $city 				= trim($currentSheet->getCell($tt)->getValue());
                $state 				= trim($currentSheet->getCell($uu)->getValue());
                $zipCode 			= trim($currentSheet->getCell($vv)->getValue());
                $countryName 		= trim($currentSheet->getCell($ww)->getValue());
                $landline 			= trim($currentSheet->getCell($xx)->getValue());
                $carrierNameCn 		= trim($currentSheet->getCell($yy)->getValue());

                $ordersTime_arr 	= explode(".",$ordersTime);
                $ordersTime			= strtotime(implode("-",$ordersTime_arr));
                $paymentTime_arr 	= explode(".",$paymentTime);
                $paymentTime		= strtotime(implode("-",$paymentTime_arr));
                if(empty($recordNumber)){
                    break;
                }
                $accountId = M('Account')->getAccountIdByName($account);
                if(M('OrderAdd')->checkIsExists(array('recordNumber'=>$recordNumber, 'accountId'=>$accountId))){
                    self::$errMsg[] = get_promptmsg(10043, $recordNumber);//"该recordNumber已经存在<br/>";
                    continue;
                }

                if(in_array($recordNumber,$orderid)){
                    $orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['sku']            = $sku;
                    $orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['amount']         = $amount;
                    $orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['itemPrice']      = $itemPrice;
                    $orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['shippingFee']    = $shippingFee;
                    $orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['recordNumber']   = $recordNumber;
                    $orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['createdTime']    = time();
                    $orderData[$recordNumber]['orderDetail'][$sku]['orderDetailExtension']['itemTitle'] = $itemTitle;
                }else{
                    $orderid[] = $recordNumber;
                    //order信息
                    $platformList = M('Account')->getPlatformid($accountId);
                    $orderData[$recordNumber]['order']['recordNumber']   = $recordNumber;
                    $orderData[$recordNumber]['order']['platformId']     = $platformList[0]['platformId'];//这里要根据accountId得到对应的platformId
                    $orderData[$recordNumber]['order']['accountId']      = $accountId;
                    $orderData[$recordNumber]['order']['orderStatus']    = 0;
                    $orderData[$recordNumber]['order']['ordersTime']     = $ordersTime;
                    $orderData[$recordNumber]['order']['paymentTime']    = $paymentTime;
                    $orderData[$recordNumber]['order']['onlineTotal']    = $actualTotal;
                    $orderData[$recordNumber]['order']['actualTotal']    = $actualTotal;
                    $orderData[$recordNumber]['order']['actualShipping'] = $shippingFee;
                    $orderData[$recordNumber]['order']['currency']       = $currency;       //取默认,待以后确认处理
                    $orderData[$recordNumber]['order']['transportId']    = 0;               //走拦截流程,取消默认
                    $orderData[$recordNumber]['order']['pmId']           = 0;               //包材ID,默认0
                    $orderData[$recordNumber]['order']['channelId']      = 0;               //渠道ID,默认0
                    $orderData[$recordNumber]['order']['orderAddTime']   = time();
                    $orderData[$recordNumber]['order']['calcShipping']   = $shippingFee;
                    $orderData[$recordNumber]['order']['orderType']      = 0;
                    $orderData[$recordNumber]['order']['completeTime']   = 0;               //完结时间,默认0
                    $orderData[$recordNumber]['order']['is_offline']     = 1;               //标识为线下订单
                    $orderData[$recordNumber]['order']['ORtransport']    = $carrierNameCn;

                    //order扩展信息
                    $orderData[$recordNumber]['orderExtension']['feedback']         = $note;
                    $orderData[$recordNumber]['orderExtension']['payPalPaymentId']          = $transId;
                    $orderData[$recordNumber]['orderExtension']['currency']         = $currency;
                    $orderData[$recordNumber]['orderExtension']['paymentStatus']    = 'PAY_SUCCESS';
                    $orderData[$recordNumber]['orderExtension']['declaredPrice']    = $actualTotal;

                    //user信息
                    $orderData[$recordNumber]['orderUserInfo']['platformUsername'] = $platformUsername;
                    $orderData[$recordNumber]['orderUserInfo']['username']         = $username;
                    $orderData[$recordNumber]['orderUserInfo']['email']            = $email;
                    $orderData[$recordNumber]['orderUserInfo']['countryName']      = $countryName;
                    $orderData[$recordNumber]['orderUserInfo']['currency']         = 'USD';//$currency;
                    $orderData[$recordNumber]['orderUserInfo']['state']            = $state;
                    $orderData[$recordNumber]['orderUserInfo']['city']             = $city;
                    $orderData[$recordNumber]['orderUserInfo']['county']           = ' ';   //区县默认空
                    $orderData[$recordNumber]['orderUserInfo']['address1']         = $street;
                    $orderData[$recordNumber]['orderUserInfo']['address2']         = $address2;
                    $orderData[$recordNumber]['orderUserInfo']['address3']         = $address3;
                    $orderData[$recordNumber]['orderUserInfo']['mobilePhone']      = $landline;
                    $orderData[$recordNumber]['orderUserInfo']['phone']            = $landline;
                    $orderData[$recordNumber]['orderUserInfo']['zipCode']          = $zipCode;

                    //detail信息
                    $orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['sku']            = $sku;
                    $orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['amount']         = $amount;
                    $orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['itemPrice']      = $itemPrice;
                    $orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['shippingFee']    = $shippingFee;
                    $orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['recordNumber']   = $recordNumber;
                    $orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['createdTime']    = time();
                    $orderData[$recordNumber]['orderDetail'][$sku]['orderDetailExtension']['itemTitle'] = $itemTitle;
                }
            }
            return $this->act_insertOrder($orderData);
        }else{
            self::$errMsg[] = get_promptmsg(10053);
            return false;
        }
    }
    
    /**
	 * 速卖通订单导入 
	 */
    public function act_insertAliexpressOrder(){
        if(isset($_FILES['aliexpressFile']['tmp_name'])){
            $filePath = $_FILES['aliexpressFile']['tmp_name'];
            $PHPExcel = E('PHPExcel');
			$PHPReader = new PHPExcel_Reader_Excel2007();
			if(!$PHPReader->canRead($filePath)){
				$PHPReader = new PHPExcel_Reader_Excel5();
				if(!$PHPReader->canRead($filePath)){
					self::$errMsg[] = get_promptmsg(10058);
                    return false;
				}
			}
			$PHPExcel = $PHPReader->load($filePath);
			$currentSheet = $PHPExcel->getSheet(0);
			$orderid = array();
			$orderData = array();
			$account = $_POST['aliexpressAccount'];//accountId
            if(intval($account) <= 0){
                self::$errMsg[] = get_promptmsg(10054);
			    return false;
            }
			$c = 2;
			while(true){
				$aa = 'A'.$c;
				$bb	= 'B'.$c;
				$cc	= 'C'.$c;
				$dd	= 'D'.$c;
				$ee	= 'E'.$c;
				$ff	= 'F'.$c;
				$gg	= 'G'.$c;
				$hh	= 'H'.$c;
				$ii	= 'I'.$c;
				$jj	= 'J'.$c;
				$kk	= 'K'.$c;
				$ll	= 'L'.$c;
				$mm	= 'M'.$c;
				$nn	= 'N'.$c;
				$oo	= 'O'.$c;
				$pp	= 'P'.$c;
				$qq	= 'Q'.$c;
				$rr	= 'R'.$c;
				$ss	= 'S'.$c;
				$tt	= 'T'.$c;
				$uu	= 'U'.$c;
				$vv	= 'V'.$c;
				$ww	= 'W'.$c;
				$xx	= 'X'.$c;
				$yy	= 'Y'.$c;
				$zz	= 'Z'.$c;
				$c++;

				$recordNumber 		= trim($currentSheet->getCell($aa)->getValue());
                if(empty($recordNumber)){
                    break;
                }
                if(M('OrderAdd')->checkIsExists(array('recordNumber'=>$recordNumber, 'accountId'=>$account))){
                    self::$errMsg[] = get_promptmsg(10043, $recordNumber);//"该recordNumber已经存在<br/>";
                    continue;
                }
				$email				= trim($currentSheet->getCell($ee)->getValue());
				$ordersTime 		= trim($currentSheet->getCell($ff)->getValue());
				$ordersTime 		= str_replace('.', '-', $ordersTime).':01';
				$ordersTime 		= strtotime($ordersTime);
				$paymentTime 		= trim($currentSheet->getCell($gg)->getValue());
				$paymentTime 		= str_replace('.', '-', $paymentTime).':01';
				$paymentTime 		= strtotime($paymentTime);
				$onlineTotal  		= trim($currentSheet->getCell($hh)->getValue());
				$shippingFee 		= trim($currentSheet->getCell($ii)->getValue());
				$actualTotal 		= trim($currentSheet->getCell($jj)->getValue());
				$productsinformation= trim($currentSheet->getCell($ll)->getValue());//订单明细信息
				$note 				= trim($currentSheet->getCell($mm)->getValue());
				$address2 			= trim($currentSheet->getCell($nn)->getValue());
				$username 			= trim($currentSheet->getCell($oo)->getValue());
				$countryName 		= trim($currentSheet->getCell($pp)->getValue());
				$state 				= trim($currentSheet->getCell($qq)->getValue());
				$city 				= trim($currentSheet->getCell($rr)->getValue());
				$street 			= trim($currentSheet->getCell($ss)->getValue());
				$zipCode 			= trim($currentSheet->getCell($tt)->getValue());
				$landline 			= trim($currentSheet->getCell($uu)->getValue());
				$phone				= trim($currentSheet->getCell($vv)->getValue());
				$carrierNameEn 		= trim($currentSheet->getCell($ww)->getValue());
				$platformUsername 	= trim($currentSheet->getCell($dd)->getValue());
                
				$goods_list 		 = array();
				$productsinformation = explode('【',$productsinformation);
				for($j=0; $j<count($productsinformation);$j++){
					$labelstr		= $productsinformation[$j];
					if($labelstr != '' ){
						$title = $qty = $sku = $_ebay_carrier = '';
						$data  = explode('<br />', nl2br($labelstr));
						$title = substr($data[0],4);
						foreach ($data as $value){
							//print_r($value);
							if(strpos($value, '商家编码')!=false){
								list($t, $sku) = explode(':', $value);
								$sku = trim(trim($sku,')'),'）');
								$sku = substr($sku, 0, strlen($sku)-1);
							}else if(strpos($value, '产品数量')!=false){
								list($t, $qty) = explode(':', $value);
								$qty = explode(' ',$qty);
								$qty = intval(trim($qty[0]));
							}
							if(strpos($value, '产品单价')!=false){
								list($t, $itemPrice) = explode('：', $value);
								$currency	= substr($itemPrice,0,1);
								$itemPrice  = substr($itemPrice,1,strlen($itemPrice)-1);
							}
						}

						$sku 		= trim($sku);
						$amount 	= trim($qty);
						$itemTitle  = trim($title);
                        if(empty($itemPrice)){
                            $itemPrice = 0;
                        }
						$goods_list[] = $sku;

						$orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['sku']            = $sku;
    					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['amount']         = $amount;
    					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['itemPrice']      = $itemPrice;
    					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['shippingFee']    = $shippingFee;
    					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['recordNumber']   = $recordNumber;
    					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['createdTime']    = time();
    					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailExtension']['itemTitle'] = $itemTitle;
					}
				}

				if(in_array($recordNumber,$orderid)){
					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['sku']            = $sku;
					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['amount']         = $amount;
					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['itemPrice']      = $itemPrice;
					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['shippingFee']    = $shippingFee;
					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['recordNumber']   = $recordNumber;
					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['createdTime']    = time();
					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailExtension']['itemTitle'] = $itemTitle;
				}else{
					$orderid[] = $recordNumber;
					//order信息
                    $platformList = M('Account')->getPlatformid($account);
					$orderData[$recordNumber]['order']['recordNumber']   = $recordNumber;
                    $orderData[$recordNumber]['order']['platformId']     = $platformList[0]['platformId'];//这里要根据accountId得到对应的platformId
                    $orderData[$recordNumber]['order']['accountId']      = $account;
                    $orderData[$recordNumber]['order']['orderStatus']    = 0;
                    $orderData[$recordNumber]['order']['ordersTime']     = $ordersTime;
                    $orderData[$recordNumber]['order']['paymentTime']    = $paymentTime;
                    $orderData[$recordNumber]['order']['onlineTotal']    = $onlineTotal;
                    $orderData[$recordNumber]['order']['actualTotal']    = $actualTotal;
                    $orderData[$recordNumber]['order']['actualShipping'] = $shippingFee;
                    $orderData[$recordNumber]['order']['currency']       = 'USD';           //取默认,待以后确认处理
                    $orderData[$recordNumber]['order']['transportId']    = 0;  //走拦截流程,取消默认
                    $orderData[$recordNumber]['order']['pmId']           = 0;               //包材ID,默认0
                    $orderData[$recordNumber]['order']['channelId']      = 0;               //渠道ID,默认0
                    $orderData[$recordNumber]['order']['orderAddTime']   = time();
                    $orderData[$recordNumber]['order']['calcShipping']   = $shippingFee;
                    $orderData[$recordNumber]['order']['orderType']      = 0;
                    $orderData[$recordNumber]['order']['completeTime']   = 0;               //完结时间,默认0
                    $orderData[$recordNumber]['order']['ORtransport']    = $carrierNameEn;

                    //order扩展信息
					$orderData[$recordNumber]['orderExtension']['feedback']			    =	$note;


					//user信息
					$orderData[$recordNumber]['orderUserInfo']['platformUsername'] = $platformUsername;
					$orderData[$recordNumber]['orderUserInfo']['username']         = $username;
					$orderData[$recordNumber]['orderUserInfo']['email']            = $email;
                    $orderData[$recordNumber]['orderUserInfo']['countryName']      = $countryName;
                    $orderData[$recordNumber]['orderUserInfo']['currency']         = 'USD';//$currency;
                    $orderData[$recordNumber]['orderUserInfo']['state']            = $state;
                    $orderData[$recordNumber]['orderUserInfo']['city']             = $city;
                    $orderData[$recordNumber]['orderUserInfo']['county']           = ' ';   //区县默认空
                    $orderData[$recordNumber]['orderUserInfo']['address1']         = $street;
                    $orderData[$recordNumber]['orderUserInfo']['mobilePhone']      = $landline;
                    $orderData[$recordNumber]['orderUserInfo']['phone']            = $phone;
                    $orderData[$recordNumber]['orderUserInfo']['zipCode']          = $zipCode;

					//detail信息
					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['sku']            = $sku;
					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['amount']         = $amount;
					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['itemPrice']      = $itemPrice;
					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['shippingFee']    = $shippingFee;
					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['recordNumber']   = $recordNumber;
					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['createdTime']    = time();
					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailExtension']['itemTitle'] = $itemTitle;                    
				}
			}
            return $this->act_insertOrder($orderData);
        }else{
            self::$errMsg[] = get_promptmsg(10053);
            return false;
        }
    }
    
    /**
	 * 速卖通线下订单导入
	 */
    public function act_insertAliexpressOfflineOrder(){
		if(isset($_FILES['aliexpressFile']['tmp_name'])){
            $filePath = $_FILES['aliexpressFile']['tmp_name'];
            $PHPExcel = E('PHPExcel');
			$PHPReader = new PHPExcel_Reader_Excel2007();
			if(!$PHPReader->canRead($filePath)){
				$PHPReader = new PHPExcel_Reader_Excel5();
				if(!$PHPReader->canRead($filePath)){
					self::$errMsg[] = get_promptmsg(10058);
                    return false;
				}
			}
			$PHPExcel = $PHPReader->load($filePath);
			$currentSheet = $PHPExcel->getSheet(0);
			$orderid = array();
			$orderData = array();
			$account = $_POST['aliexpressAccount'];//accountId
            if(intval($account) <= 0){
                self::$errMsg[] = get_promptmsg(10054);
			    return false;
            }
			$c = 2;
			while(true){
				$aa = 'A'.$c;
				$bb	= 'B'.$c;
				$cc	= 'C'.$c;
				$dd	= 'D'.$c;
				$ee	= 'E'.$c;
				$ff	= 'F'.$c;
				$gg	= 'G'.$c;
				$hh	= 'H'.$c;
				$ii	= 'I'.$c;
				$jj	= 'J'.$c;
				$kk	= 'K'.$c;
				$ll	= 'L'.$c;
				$mm	= 'M'.$c;
				$nn	= 'N'.$c;
				$oo	= 'O'.$c;
				$pp	= 'P'.$c;
				$qq	= 'Q'.$c;
				$rr	= 'R'.$c;
				$ss	= 'S'.$c;
				$tt	= 'T'.$c;
				$uu	= 'U'.$c;
				$c++;

				$recordNumber 		= trim($currentSheet->getCell($aa)->getValue());
                if(empty($recordNumber)){
					break;
				}
                if(M('OrderAdd')->checkIsExists(array('recordNumber'=>$recordNumber, 'accountId'=>$account))){
                	self::$errMsg[] = get_promptmsg(10043, $recordNumber);//"该recordNumber已经存在<br/>";
                    continue;
                }
				$email				= trim($currentSheet->getCell($cc)->getValue());
				$ordersTime 		= trim($currentSheet->getCell($dd)->getValue());
				$ordersTime 		= str_replace('.', '-', $ordersTime).' 00:00:01';
				$ordersTime 		= strtotime($ordersTime);
				$paymentTime 		= trim($currentSheet->getCell($ee)->getValue());
				$paymentTime 		= str_replace('.', '-', $paymentTime).' 00:00:01';
				$paymentTime 		= strtotime($paymentTime);
				$onlineTotal  		= trim($currentSheet->getCell($ff)->getValue());
				$shippingFee 		= trim($currentSheet->getCell($gg)->getValue());
				$actualTotal 		= trim($currentSheet->getCell($hh)->getValue());
				$currency 			= trim($currentSheet->getCell($ii)->getValue());
				$transId 			= trim($currentSheet->getCell($jj)->getValue());
				$productsinfo       = trim($currentSheet->getCell($kk)->getValue());
				$note 				= trim($currentSheet->getCell($ll)->getValue());
				$username 			= trim($currentSheet->getCell($mm)->getValue());
				$countryName 		= trim($currentSheet->getCell($nn)->getValue());
				$state 				= trim($currentSheet->getCell($oo)->getValue());
				$city 				= trim($currentSheet->getCell($pp)->getValue());
				$street 			= trim($currentSheet->getCell($qq)->getValue());
				$zipCode 			= trim($currentSheet->getCell($rr)->getValue());
				$landline 			= trim($currentSheet->getCell($ss)->getValue());
				$phone				= trim($currentSheet->getCell($tt)->getValue());
				$carrierNameCn 		= trim($currentSheet->getCell($uu)->getValue());
				$platformUsername 	= trim($currentSheet->getCell($bb)->getValue());

                $dataarray = array();
        		$goods_list = array();
        		$pinfos = array_map('trim', explode('<br />', nl2br($productsinfo)));

        		foreach ($pinfos AS $pinfo){
                    $sku = $itemTitle = '';
                    $amount =  $itemPrice =  0;
        			list($amount, $sku) = array_map('trim', explode('*', $pinfo));
        			$amount = intval($amount);
        			$sku = strpos($sku, '#')!==false ? str_replace('#', '', $sku) : $sku;
    				if(in_array($recordNumber,$orderid)){
    					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['sku']            = $sku;
    					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['amount']         = $amount;
    					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['itemPrice']      = $itemPrice;
    					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['shippingFee']    = $shippingFee;
    					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['recordNumber']   = $recordNumber;
    					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['createdTime']    = time();
                        $orderData[$recordNumber]['orderDetail'][$sku]['orderDetailExtension']['itemTitle'] = $itemTitle;
                        if(!empty($note)){
        					$orderData[$recordNumber]['orderNote']['content'] = $note;
        					$orderData[$recordNumber]['orderNote']['userId'] = $_SESSION['sysUserId'];
        					$orderData[$recordNumber]['orderNote']['noteTypeForWh'] = 2;
                            $orderData[$recordNumber]['orderNote']['createdTime'] = time();
        				}
    				}else{
    					$orderid[] = $recordNumber;
    					//order信息
                        $platformList = M('Account')->getPlatformid($account);
                        $orderData[$recordNumber]['order']['recordNumber']   = $recordNumber;
                        $orderData[$recordNumber]['order']['platformId']     = $platformList[0]['platformId'];//这里要根据accountId得到对应的platformId
                        $orderData[$recordNumber]['order']['accountId']      = $account;
                        $orderData[$recordNumber]['order']['orderStatus']    = 0;
                        $orderData[$recordNumber]['order']['ordersTime']     = $ordersTime;
                        $orderData[$recordNumber]['order']['paymentTime']    = $paymentTime;
                        $orderData[$recordNumber]['order']['onlineTotal']    = $onlineTotal;
                        $orderData[$recordNumber]['order']['actualTotal']    = $actualTotal;
                        $orderData[$recordNumber]['order']['actualShipping'] = $shippingFee;
                        $orderData[$recordNumber]['order']['calcShipping']   = $shippingFee;
                        $orderData[$recordNumber]['order']['currency']       = 'USD';           //取默认,待以后确认处理
                        $orderData[$recordNumber]['order']['transportId']    = 0;               //走拦截流程,取消默认
                        $orderData[$recordNumber]['order']['pmId']           = 0;               //包材ID,默认0
                        $orderData[$recordNumber]['order']['channelId']      = 0;               //渠道ID,默认0
                        $orderData[$recordNumber]['order']['orderAddTime']   = time();
                        $orderData[$recordNumber]['order']['orderType']      = 0;
                        $orderData[$recordNumber]['order']['completeTime']   = 0;               //完结时间,默认0
                        $orderData[$recordNumber]['order']['is_offline']     = 1;               //标识为线下订单
                        $orderData[$recordNumber]['order']['ORtransport']    = $carrierNameCn;

                        //order扩展信息
                        $orderData[$recordNumber]['orderExtension']['payPalPaymentId']         =	$transId;
    					//user信息
                        $orderData[$recordNumber]['orderUserInfo']['platformUsername'] = $platformUsername;
                        $orderData[$recordNumber]['orderUserInfo']['username']         = $username;
                        $orderData[$recordNumber]['orderUserInfo']['email']            = $email;
                        $orderData[$recordNumber]['orderUserInfo']['countryName']      = $countryName;
                        $orderData[$recordNumber]['orderUserInfo']['currency']         = $currency;//$currency;
                        $orderData[$recordNumber]['orderUserInfo']['state']            = $state;
                        $orderData[$recordNumber]['orderUserInfo']['city']             = $city;
                        $orderData[$recordNumber]['orderUserInfo']['county']           = ' ';   //区县默认空
                        $orderData[$recordNumber]['orderUserInfo']['address1']         = $street;
                        $orderData[$recordNumber]['orderUserInfo']['mobilePhone']      = $landline;
                        $orderData[$recordNumber]['orderUserInfo']['phone']            = $phone;
                        $orderData[$recordNumber]['orderUserInfo']['zipCode']          = $zipCode;

    					//detail信息
    					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['sku']                = $sku;
    					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['amount']             = $amount;
    					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['itemPrice']          = $itemPrice;
    					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['shippingFee']        = $shippingFee;
    					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['recordNumber']       = $recordNumber;
    					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['createdTime']        = time();
                        $orderData[$recordNumber]['orderDetail'][$sku]['orderDetailExtension']['itemTitle'] = $itemTitle;
                        if(!empty($note)){
        					$orderData[$recordNumber]['orderNote']['content'] = $note;
        					$orderData[$recordNumber]['orderNote']['userId'] = $_SESSION['sysUserId'];
                            $orderData[$recordNumber]['orderNote']['createdTime'] = time();
                            $orderData[$recordNumber]['orderNote']['noteTypeForWh'] = 2;
        				}
    				}
    			}
            }
            return $this->act_insertOrder($orderData);
        }else{
            self::$errMsg[] = get_promptmsg(10053);
            return false;
        }
    }
    
	/**
	 * 敦煌订单导入
	 */
    public function act_insertDhgateOrder(){
		if(isset($_FILES['DHFile']['tmp_name'])){
            $filePath = $_FILES['DHFile']['tmp_name'];
            $PHPExcel = E('PHPExcel');
			$PHPReader = new PHPExcel_Reader_Excel2007();
			if(!$PHPReader->canRead($filePath)){
				$PHPReader = new PHPExcel_Reader_Excel5();
				if(!$PHPReader->canRead($filePath)){
					self::$errMsg[] = get_promptmsg(10058);
                    return false;
				}
			}
			$PHPExcel = $PHPReader->load($filePath);
			$currentSheet = $PHPExcel->getSheet(0);
			$orderid = array();
			$orderData = array();
			$account = $_POST['dhgateAccount'];//accountId
            if(intval($account) <= 0){
                self::$errMsg[] = get_promptmsg(10054);
			    return false;
            }
			$c = 2;
			while(true){
				$aa = 'A'.$c;
				$bb	= 'B'.$c;
				$cc	= 'C'.$c;
				$dd	= 'D'.$c;
				$ee	= 'E'.$c;
				$ff	= 'F'.$c;
				$gg	= 'G'.$c;
				$hh	= 'H'.$c;
				$ii	= 'I'.$c;
				$jj	= 'J'.$c;
				$kk	= 'K'.$c;
				$ll	= 'L'.$c;
				$mm	= 'M'.$c;
				$nn	= 'N'.$c;
				$oo	= 'O'.$c;
				$pp	= 'P'.$c;
				$qq	= 'Q'.$c;
				$rr	= 'R'.$c;
				$ss	= 'S'.$c;
				$tt	= 'T'.$c;
				$uu	= 'U'.$c;
				$vv	= 'V'.$c;
				$ww	= 'W'.$c;
				$xx	= 'X'.$c;
				$yy	= 'Y'.$c;
				$zz	= 'Z'.$c;
				$c++;

				$recordNumber 		= trim($currentSheet->getCell($aa)->getValue());
                if(empty($recordNumber)){
					break;
				}
                if(M('OrderAdd')->checkIsExists(array('recordNumber'=>$recordNumber, 'accountId'=>$account))){
                	self::$errMsg[] = get_promptmsg(10043, $recordNumber);//"该recordNumber已经存在<br/>";
                    continue;
                }
				$transId 			        = '';
				$userId				        = str_rep(trim($currentSheet->getCell($cc)->getValue())); //买家名称
				$ordersTime			        = strtotime(trim($currentSheet->getCell($dd)->getValue())); // 下单时间
				$paymentTime		        = strtotime(trim($currentSheet->getCell($ee)->getValue())); // 付款时间
				$shippingFee		        = str_rep(trim(trim($currentSheet->getCell($gg)->getValue()),'﻿$'));   //物流费用
				$actualTotal		        = str_rep(trim(trim($currentSheet->getCell($jj)->getValue()),'﻿$'));   //订单总金额
                $username				    = str_rep(trim($currentSheet->getCell($nn)->getValue())); 	//收货人名称
                $platformUsername 		    = $username;
                $countryName			    = str_rep(trim($currentSheet->getCell($oo)->getValue()));   //收货国家
                $countryName			    = mysql_real_escape_string($countryName);
                $state					    = str_rep(trim($currentSheet->getCell($pp)->getValue()));	//州/省
                $city					    = str_rep(trim($currentSheet->getCell($qq)->getValue()));	//城市
                $street					    = str_rep(trim($currentSheet->getCell($rr)->getValue()));	//地址
                $zipCode				    = str_rep(trim($currentSheet->getCell($ss)->getValue()));	//邮编
                $phone					    = str_rep(trim($currentSheet->getCell($tt)->getValue()));	//联系电话（座机）
                $ebay_carrier			    = str_rep(trim($currentSheet->getCell($uu)->getValue()));   //买家选择物流
                $develiverytime			    = strtotime(str_rep(trim($currentSheet->getCell($vv)->getValue())));   //发货期限
                $productsinformation		= str_rep(trim($currentSheet->getCell($kk)->getValue()));   //订单信息

                $productsinformation 		= explode('【',$productsinformation);
				$goods_list 				= array();

                for($j=0; $j<count($productsinformation);$j++){
                    $labelstr		= $productsinformation[$j];
                    if($labelstr != '' ){
                        $title = $qty = $sku = $_ebay_carrier = '';
                        $data  = explode('<br />', nl2br($labelstr));
                        $title = substr($data[0],4);
                        foreach ($data as $value){
                            if(strpos($value, '商品编号/工厂编号')!=false){
                                list($t, $sku) = explode('：', $value);
                                $sku = trim(trim($sku,')'),'）');
                            }else if(strpos($value, '数量')!=false){
                                list($t, $qty) = explode('：', $value);
                                $qty = intval(trim($qty));
                            }
                            if(strpos($value, '产品单价')!=false){
                                list($t, $itemPrice) = explode('：', $value);
                                $currency	= substr($itemPrice,0,1);
                                $itemPrice  = substr($itemPrice,1,strlen($itemPrice)-1);

                            }
                        }

                        $sku 		= trim($sku);
                        $amount 	= trim($qty);
                        $itemTitle  = trim($title);
                        if(empty($itemPrice)){
                            $itemPrice = 0;
                        }
                        $goods_list[] = $sku;

                        $orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['sku']            = $sku;
                        $orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['amount']         = $amount;
                        $orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['itemPrice']      = $itemPrice;
                        $orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['shippingFee']    = $shippingFee;
                        $orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['recordNumber']   = $recordNumber;
                        $orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['createdTime']    = time();
                        $orderData[$recordNumber]['orderDetail'][$sku]['orderDetailExtension']['itemTitle'] = $itemTitle;
                    }
                }

                if(in_array($recordNumber,$orderid)){
                    $orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['sku']            = $sku;
                    $orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['amount']         = $amount;
                    $orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['itemPrice']      = $itemPrice;
                    $orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['shippingFee']    = $shippingFee;
                    $orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['recordNumber']   = $recordNumber;
                    $orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['createdTime']    = time();
                    $orderData[$recordNumber]['orderDetail'][$sku]['orderDetailExtension']['itemTitle'] = $itemTitle;
                }else{
                    $orderid[] = $recordNumber;
                    //order信息
                    $platformList = M('Account')->getPlatformid($account);
                    $orderData[$recordNumber]['order']['recordNumber']   = $recordNumber;
                    $orderData[$recordNumber]['order']['platformId']     = $platformList[0]['platformId'];//这里要根据accountId得到对应的platformId
                    $orderData[$recordNumber]['order']['accountId']      = $account;
                    $orderData[$recordNumber]['order']['orderStatus']    = 0;
                    $orderData[$recordNumber]['order']['ordersTime']     = $ordersTime;
                    $orderData[$recordNumber]['order']['paymentTime']    = $paymentTime;
                    $orderData[$recordNumber]['order']['onlineTotal']    = $actualTotal;
                    $orderData[$recordNumber]['order']['actualTotal']    = $actualTotal;
                    $orderData[$recordNumber]['order']['actualShipping'] = $shippingFee;
                    $orderData[$recordNumber]['order']['currency']       = 'USD';       //取默认,待以后确认处理
                    $orderData[$recordNumber]['order']['transportId']    = 0;               //走拦截流程,取消默认
                    $orderData[$recordNumber]['order']['pmId']           = 0;               //包材ID,默认0
                    $orderData[$recordNumber]['order']['channelId']      = 0;               //渠道ID,默认0
                    $orderData[$recordNumber]['order']['orderAddTime']   = time();
                    $orderData[$recordNumber]['order']['calcShipping']   = $shippingFee;
                    $orderData[$recordNumber]['order']['orderType']      = 0;
                    $orderData[$recordNumber]['order']['completeTime']   = 0;               //完结时间,默认0
                    $orderData[$recordNumber]['order']['is_offline']     = 1;               //标识为线下订单
                    $orderData[$recordNumber]['order']['ORtransport']    = $ebay_carrier;


                    //order扩展信息
                    $orderData[$recordNumber]['orderExtension']['payPalPaymentId']         =	$transId;

                    //user信息
                    $orderData[$recordNumber]['orderUserInfo']['platformUsername'] = $platformUsername;
                    $orderData[$recordNumber]['orderUserInfo']['username']         = $username;
                    $orderData[$recordNumber]['orderUserInfo']['email']            = ' ';
                    $orderData[$recordNumber]['orderUserInfo']['countryName']      = $countryName;
                    $orderData[$recordNumber]['orderUserInfo']['currency']         = $currency;         //$currency;
                    $orderData[$recordNumber]['orderUserInfo']['state']            = $state;
                    $orderData[$recordNumber]['orderUserInfo']['city']             = $city;
                    $orderData[$recordNumber]['orderUserInfo']['county']           = ' ';   //区县默认空
                    $orderData[$recordNumber]['orderUserInfo']['address1']         = $street;
                    $orderData[$recordNumber]['orderUserInfo']['phone']            = $phone;
                    $orderData[$recordNumber]['orderUserInfo']['zipCode']          = $zipCode;

                    //detail信息
                    $orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['sku']            = $sku;
                    $orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['amount']         = $amount;
                    $orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['itemPrice']      = $itemPrice;
                    $orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['shippingFee']    = $shippingFee;
                    $orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['recordNumber']   = $recordNumber;
                    $orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['createdTime']    = time();
                    $orderData[$recordNumber]['orderDetail'][$sku]['orderDetailExtension']['itemTitle'] = $itemTitle;
                }

            }
            return $this->act_insertOrder($orderData);
        }else{
            self::$errMsg[] = get_promptmsg(10053);
            return false;
        }
    }
    
	/**
	 * 诚信通订单导入页面
	 */
    public function act_insertTrustOrder(){
		if(isset($_FILES['aliexpressFile']['tmp_name'])){
            $filePath = $_FILES['aliexpressFile']['tmp_name'];
            $PHPExcel = E('PHPExcel');
			$PHPReader = new PHPExcel_Reader_Excel2007();
			if(!$PHPReader->canRead($filePath)){
				$PHPReader = new PHPExcel_Reader_Excel5();
				if(!$PHPReader->canRead($filePath)){
					self::$errMsg[] = get_promptmsg(10058);
                    return false;
				}
			}
			$PHPExcel = $PHPReader->load($filePath);
			$currentSheet = $PHPExcel->getSheet(0);
			$orderid = array();
			$orderData = array();
			$account = $_POST['aliexpressAccount'];//accountId
            if(intval($account) <= 0){
                self::$errMsg[] = get_promptmsg(10054);
			    return false;
            }
			$c = 2;
			while(true){
				$aa = 'A'.$c;
				$bb	= 'B'.$c;
				$cc	= 'C'.$c;
				$dd	= 'D'.$c;
				$ee	= 'E'.$c;
				$ff	= 'F'.$c;
				$gg	= 'G'.$c;
				$hh	= 'H'.$c;
				$ii	= 'I'.$c;
				$jj	= 'J'.$c;
				$kk	= 'K'.$c;
				$ll	= 'L'.$c;
				$mm	= 'M'.$c;
				$nn	= 'N'.$c;
				$oo	= 'O'.$c;
				$pp	= 'P'.$c;
				$qq	= 'Q'.$c;
				$rr	= 'R'.$c;
				$ss	= 'S'.$c;
				$tt	= 'T'.$c;
				$uu	= 'U'.$c;
				$vv	= 'V'.$c;
				$ww	= 'W'.$c;
				$xx	= 'X'.$c;
				$yy	= 'Y'.$c;
				$zz	= 'Z'.$c;
				$ab	= 'AB'.$c;
				$ac	= 'AC'.$c;
				$ad	= 'AD'.$c;
				$ae	= 'AE'.$c;
				$af	= 'AF'.$c;
				$c++;

				$recordNumber 		= trim($currentSheet->getCell($aa)->getValue());
                if(empty($recordNumber)){
					break;
				}
                if(M('OrderAdd')->checkIsExists(array('recordNumber'=>$recordNumber, 'accountId'=>$account))){
                	self::$errMsg[] = get_promptmsg(10043, $recordNumber);//"该recordNumber已经存在<br/>";
                    continue;
                }
				$email				= trim($currentSheet->getCell($cc)->getValue());
                $ordersTime 		= trim($currentSheet->getCell($ff)->getValue());
				$paymentTime 		= trim($currentSheet->getCell($gg)->getValue());
				$onlineTotal  		= trim($currentSheet->getCell($hh)->getValue());
				$shippingFee 		= trim($currentSheet->getCell($ii)->getValue());
				$actualTotal 		= trim($currentSheet->getCell($jj)->getValue());
				$currency 			= trim($currentSheet->getCell($kk)->getValue());
				$transId 			= trim($currentSheet->getCell($ll)->getValue());
				$declare 			= trim($currentSheet->getCell($mm)->getValue());
				$evaluateWeight		= trim($currentSheet->getCell($nn)->getValue());//估算重量
				$sku 	 			= trim($currentSheet->getCell($oo)->getValue());
				$amount				= trim($currentSheet->getCell($pp)->getValue());
				$itemTitle 			= trim($currentSheet->getCell($qq)->getValue());
				$note 				= trim($currentSheet->getCell($rr)->getValue());
				$username 			= trim($currentSheet->getCell($ss)->getValue());
				$countryName 		= trim($currentSheet->getCell($tt)->getValue());
				$state 				= trim($currentSheet->getCell($uu)->getValue());
				$city 				= trim($currentSheet->getCell($vv)->getValue());
				$street 			= trim($currentSheet->getCell($ww)->getValue());
				$zipCode 			= trim($currentSheet->getCell($xx)->getValue());
				$landline			= trim($currentSheet->getCell($yy)->getValue());
				$phone 				= trim($currentSheet->getCell($ab)->getValue());
				$carrierNameCn 		= trim($currentSheet->getCell($ac)->getValue());
				$oppositeSku    	= trim($currentSheet->getCell($ae)->getValue());
				$oppositeBarCode    = trim($currentSheet->getCell($af)->getValue());
                $platformUsername 	= trim($currentSheet->getCell($bb)->getValue());
                //$extra_field		= $oppositeSku.'-'.$oppositeSku;

                $ordersTime 		= strtotime(str_replace('/','-',$ordersTime));
                $paymentTime 		= strtotime(str_replace('/','-',$paymentTime));
                $itemPrice = $onlineTotal/$amount;
                $platformList = M('Account')->getPlatformid($account);
                $platformId   = $platformList[0]['platformId'];

				if(in_array($recordNumber,$orderid)){
                    $orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['sku']                        = $sku;
                    $orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['amount']                     = $amount;
                    $orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['itemPrice']                  = $itemPrice;
                    $orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['createdTime']                = time();
                    $orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['shippingFee']                = $shippingFee;
                    $orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['recordNumber']               = $recordNumber;
                    $orderData[$recordNumber]['orderDetail'][$sku]['orderDetailExtension']['itemTitle']         = $itemTitle;
                    if($platformId != 2){
                        $orderData[$recordNumber]['orderDetail'][$sku]['orderDetailExtension']['oppositeSku']       = $oppositeSku;//对方的SKU，兰亭SKU
                        $orderData[$recordNumber]['orderDetail'][$sku]['orderDetailExtension']['oppositeBarCode']   = $oppositeBarCode;//对方条码，兰亭条码
                    }

                    $orderData[$recordNumber]['order']['actualTotal'] += $actualTotal;
                    $orderData[$recordNumber]['order']['actualShipping'] += $shippingFee;
				}else{
					$orderid[] = $recordNumber;
                    //order信息
                    $orderData[$recordNumber]['order']['recordNumber']   = $recordNumber;
                    $orderData[$recordNumber]['order']['platformId']     = $platformId;//这里要根据accountId得到对应的platformId
                    $orderData[$recordNumber]['order']['accountId']      = $account;
                    $orderData[$recordNumber]['order']['orderStatus']    = 0;
                    $orderData[$recordNumber]['order']['ordersTime']     = $ordersTime;
                    $orderData[$recordNumber]['order']['paymentTime']    = $paymentTime;
                    $orderData[$recordNumber]['order']['onlineTotal']    = $onlineTotal;
                    $orderData[$recordNumber]['order']['actualTotal']    = $actualTotal;    //$actualTotal+$shippingFee
                    $orderData[$recordNumber]['order']['actualShipping'] = $shippingFee;
                    $orderData[$recordNumber]['order']['currency']       = $currency;        //取默认,待以后确认处理
                    $orderData[$recordNumber]['order']['transportId']    = 0;               //走拦截流程,取消默认
                    $orderData[$recordNumber]['order']['pmId']           = 0;               //包材ID,默认0
                    $orderData[$recordNumber]['order']['channelId']      = 0;               //渠道ID,默认0
                    $orderData[$recordNumber]['order']['orderAddTime']   = time();
                    $orderData[$recordNumber]['order']['calcShipping']   = $shippingFee;
                    $orderData[$recordNumber]['order']['orderType']      = 0;
                    $orderData[$recordNumber]['order']['completeTime']   = 0;               //完结时间,默认0
                    $orderData[$recordNumber]['order']['ORtransport']    = $carrierNameCn;

                    //order扩展信息
                    $orderData[$recordNumber]['orderExtension']['feedback']		   = $note;
                    $orderData[$recordNumber]['orderExtension']['payPalPaymentId']		   = $transId;

                    //user信息
                    $orderData[$recordNumber]['orderUserInfo']['platformUsername'] = $platformUsername;
                    $orderData[$recordNumber]['orderUserInfo']['username']         = $username;
                    $orderData[$recordNumber]['orderUserInfo']['email']            = $email;
                    $orderData[$recordNumber]['orderUserInfo']['countryName']      = $countryName;
                    $orderData[$recordNumber]['orderUserInfo']['currency']         = $currency;     //$currency;
                    $orderData[$recordNumber]['orderUserInfo']['state']            = $state;
                    $orderData[$recordNumber]['orderUserInfo']['city']             = $city;
                    $orderData[$recordNumber]['orderUserInfo']['county']           = ' ';   //区县默认空
                    $orderData[$recordNumber]['orderUserInfo']['address1']         = $street;
                    $orderData[$recordNumber]['orderUserInfo']['mobilePhone']      = $landline;
                    $orderData[$recordNumber]['orderUserInfo']['phone']            = $phone;
                    $orderData[$recordNumber]['orderUserInfo']['zipCode']          = $zipCode;

                    //detail信息
                    $orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['sku']                        = $sku;
                    $orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['amount']                     = $amount;
                    $orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['itemPrice']                  = $itemPrice;
                    $orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['createdTime']                = time();
                    $orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['shippingFee']                = $shippingFee;
                    $orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['recordNumber']               = $recordNumber;
                    $orderData[$recordNumber]['orderDetail'][$sku]['orderDetailExtension']['itemTitle']         = $itemTitle;
                    if($platformId != 2){
                        $orderData[$recordNumber]['orderDetail'][$sku]['orderDetailExtension']['oppositeSku']       = $oppositeSku;//对方的SKU，兰亭SKU
                        $orderData[$recordNumber]['orderDetail'][$sku]['orderDetailExtension']['oppositeBarCode']   = $oppositeBarCode;//对方条码，兰亭条码
                    }
				}
            }
            return $this->act_insertOrder($orderData);
        }else{
            self::$errMsg[] = get_promptmsg(10053);
            return false;
        }
    }
    
	/**
	 * 独立商城订单导入页面
	 */
    public function act_insertDresslinkOrder(){
        //独立商城的扩展表尚未建立
        return false;
		if(isset($_FILES['cndlAccountListFile']['tmp_name'])){
            $filePath = $_FILES['cndlAccountListFile']['tmp_name'];
            $PHPExcel = E('PHPExcel');
			$PHPReader = new PHPExcel_Reader_Excel2007();
			if(!$PHPReader->canRead($filePath)){
				$PHPReader = new PHPExcel_Reader_Excel5();
				if(!$PHPReader->canRead($filePath)){
					self::$errMsg[] = get_promptmsg(10058);
                    return false;
				}
			}
			$PHPExcel = $PHPReader->load($filePath);
			$currentSheet = $PHPExcel->getSheet(0);
			$orderid = array();
			$orderData = array();
            F('order');//引入lib/funciond的order_functions
			$account = $_POST['cndlAccountId'];//accountId
            if(intval($account) <= 0){
                self::$errMsg[] = get_promptmsg(10054);
			    return false;
            }
            $platformId = get_platFromidbyaccountid($account);
            if(intval($platformId) <= 0){
                self::$errMsg[] = get_promptmsg(10121);
			    return false;
            }
//            $carrierList = M('InterfaceTran')->getCarrierList(2);//获取所有的运输方式
//            $carrierKV = array();
//            foreach($carrierList as $key => $trans) {
//                $carrierKV[$trans['id']] = $trans['carrierNameCn'];
//            }

			$c = 2;
            $dresslinks = array();
			$ebay_fedex_remark = array();
			$ChineseDescs = array();
			while(true){
				$aa	= 'A'.$c;
				$bb	= 'B'.$c;
				$cc	= 'C'.$c;
				$dd	= 'D'.$c;
				$ee	= 'E'.$c;
				$ff	= 'F'.$c;
				$gg	= 'G'.$c;
				$hh	= 'H'.$c;
				$ii	= 'I'.$c;
				$jj	= 'J'.$c;
				$kk	= 'K'.$c;
				$ll	= 'L'.$c;
				$mm	= 'M'.$c;
				$nn	= 'N'.$c;
				$oo	= 'O'.$c;
				$pp	= 'P'.$c;
				$qq	= 'Q'.$c;
				$rr	= 'R'.$c;
				$ss	= 'S'.$c;
				$tt	= 'T'.$c;
				$uu	= 'U'.$c;
				$vv	= 'V'.$c;
				$ww	= 'W'.$c;
				$xx	= 'X'.$c;
				$yy	= 'Y'.$c;
				$zz	= 'Z'.$c;
				$aaa = 'AA'.$c;
				$abb = 'AB'.$c;
				$acc = 'AC'.$c;
				$add = 'AD'.$c;
				$aee = 'AE'.$c;
				$aff = 'AF'.$c;
				$agg = 'AG'.$c;
				$ahh = 'AH'.$c;
				$aii = 'AI'.$c;
				$ajj = 'AJ'.$c;
				$akk = 'AK'.$c;
				$all = 'AL'.$c;
				$amm = 'AM'.$c;
				$ann = 'AN'.$c;
				$aww = 'AW'.$c;

                $recordNumber 		= trim($currentSheet->getCell($aa)->getValue()); //订单号
				if(empty($recordNumber)){
                    break;
				}

                /***************判断订单是否已存在***************/
                if(M('OrderAdd')->checkIsExists(array('recordNumber'=>$recordNumber, 'accountId'=>$account))){
                	self::$errMsg[] = get_promptmsg(10043, $recordNumber);//"该recordNumber已经存在<br/>";
                    continue;
                }                    
				/**************/
                $is_order 			= intval($currentSheet->getCell($bb)->getValue());//1代表为订单，0代表订单明细
				if ($is_order != 0){//为订单
				    //这个验证可以不用
					//if($cndlAccounts[$account]=="dresslink.com"){
//					   $str = substr($recordNumber,0,2);
//					   if($str!=="DL"){
//						  $message .= "<font color=red> {$recordNumber}不在账号{$cndlAccounts[$account]}中！</font><br>";
//						  continue;
//					   }
//					}elseif($cndlAccounts[$account]=="cndirect.com"){
//					   $str = substr($recordNumber,0,2);
//					   if($str!=="CN"){
//						  $message .= "<font color=red> {$recordNumber}不在账号{$cndlAccounts[$account]}中！</font><br>";
//						  continue;
//					   }
//					}

					$platformUsername 			= mysql_real_escape_string(trim($currentSheet->getCell($cc)->getValue()));
					$email						= mysql_real_escape_string(trim($currentSheet->getCell($dd)->getValue()));
					$transId				 	= mysql_real_escape_string(trim($currentSheet->getCell($ee)->getValue()));
					$ordersTime 				= (array)PHPExcel_Shared_Date::ExcelToPHPObject(trim($currentSheet->getCell($ll)->getValue()));
					$paymentTime 				= (array)PHPExcel_Shared_Date::ExcelToPHPObject(trim($currentSheet->getCell($mm)->getValue()));

					$shippingFee 				= round_num(trim($currentSheet->getCell($oo)->getValue()), 2);
					$calcWeight 				= round_num(trim($currentSheet->getCell($ahh)->getValue()), 3);
					$actualTotal 				= round_num(trim($currentSheet->getCell($pp)->getValue()), 2);
					$onlineTotal 				= round_num(trim($currentSheet->getCell($aff)->getValue()), 2);
					$currency 					= mysql_real_escape_string(trim($currentSheet->getCell($qq)->getValue()));
					//$orders['ebay_orderqk'] = round_num(trim($currentSheet->getCell($rr)->getValue()), 2);
					$note		 				= mysql_real_escape_string(trim($currentSheet->getCell($ss)->getValue()));
					$username 					= mysql_real_escape_string(trim($currentSheet->getCell($tt)->getValue()));
					$countryName 				= mysql_real_escape_string(trim($currentSheet->getCell($uu)->getValue()));
					$state 						= mysql_real_escape_string(trim($currentSheet->getCell($vv)->getValue()));
					$city 						= mysql_real_escape_string(trim($currentSheet->getCell($ww)->getValue()));
					$street 					= mysql_real_escape_string(trim($currentSheet->getCell($xx)->getValue()));
					$address2 					= mysql_real_escape_string(trim($currentSheet->getCell($yy)->getValue()));
					$zipCode 					= mysql_real_escape_string(trim($currentSheet->getCell($zz)->getValue()));
					$phone 						= mysql_real_escape_string(trim($currentSheet->getCell($abb)->getValue()));
					$landline 					= mysql_real_escape_string(trim($currentSheet->getCell($aaa)->getValue()));

//					if($account == 400){    //dresslink.com
//						$feedback 				= mysql_real_escape_string(trim($currentSheet->getCell($ann)->getValue()));
//					}elseif($account == 410){   //cndirect.com
//						$feedback 				= mysql_real_escape_string(trim($currentSheet->getCell($akk)->getValue()));
//					}

					$carrierNameCn 				= strtolower(mysql_real_escape_string(trim($currentSheet->getCell($kk)->getValue())));

					$payment_method 			= mysql_real_escape_string(trim($currentSheet->getCell($ff)->getValue()));
					$payment_module 			= mysql_real_escape_string(trim($currentSheet->getCell($gg)->getValue()));
					$bank_account 				= mysql_real_escape_string(trim($currentSheet->getCell($hh)->getValue()));
					$bank_country 				= mysql_real_escape_string(trim($currentSheet->getCell($ii)->getValue()));
					$shipping_method 			= mysql_real_escape_string(trim($currentSheet->getCell($jj)->getValue()));
					$shipping_module 			= mysql_real_escape_string(trim($currentSheet->getCell($kk)->getValue()));
                    
                    //这个dresslinks_info表在新系统已经废除了
					//$dresslinks['payment_method'] = $payment_method;
//					$dresslinks['payment_module'] = $payment_module;
//					$dresslinks['bank_account'] = $bank_account;
//					$dresslinks['bank_country'] = $bank_country;
//					$dresslinks['shipping_method'] = $shipping_method;
//					$dresslinks['shipping_module'] = $shipping_module;

					$PayPalPaymentId 			= $transId;
                    $ordersTime = strtotime($ordersTime['date']);
                    $paymentTime = strtotime($paymentTime['date']);
					/***************BEGIN 订单表数据***************/
                    //order信息
                    $orderData[$recordNumber]['order']['recordNumber']   = $recordNumber;
                    $orderData[$recordNumber]['order']['platformId']     = $platformId;//这里要根据accountId得到对应的platformId
                    $orderData[$recordNumber]['order']['accountId']      = $account;
                    $orderData[$recordNumber]['order']['orderStatus']    = 0;
                    $orderData[$recordNumber]['order']['ordersTime']     = $ordersTime;
                    $orderData[$recordNumber]['order']['paymentTime']    = $paymentTime;
                    $orderData[$recordNumber]['order']['onlineTotal']    = $onlineTotal;
                    $orderData[$recordNumber]['order']['actualTotal']    = $actualTotal;    //$actualTotal+$shippingFee
                    $orderData[$recordNumber]['order']['actualShipping'] = $shippingFee;
                    $orderData[$recordNumber]['order']['currency']       = $currency;        //取默认,待以后确认处理
                    $orderData[$recordNumber]['order']['transportId']    = 0;               //走拦截流程,取消默认
                    $orderData[$recordNumber]['order']['pmId']           = 0;               //包材ID,默认0
                    $orderData[$recordNumber]['order']['channelId']      = 0;               //渠道ID,默认0
                    $orderData[$recordNumber]['order']['orderAddTime']   = time();
                    $orderData[$recordNumber]['order']['calcShipping']   = $shippingFee;
                    $orderData[$recordNumber]['order']['orderType']      = 0;
                    $orderData[$recordNumber]['order']['completeTime']   = 0;               //完结时间,默认0
                    $orderData[$recordNumber]['order']['ORtransport']    = $carrierNameCn;

                    //order扩展信息


                    //user信息
                    $orderData[$recordNumber]['orderUserInfo']['platformUsername'] = $platformUsername;
                    $orderData[$recordNumber]['orderUserInfo']['username']         = $username;
                    $orderData[$recordNumber]['orderUserInfo']['email']            = $email;
                    $orderData[$recordNumber]['orderUserInfo']['countryName']      = $countryName;
                    $orderData[$recordNumber]['orderUserInfo']['currency']         = $currency;     //$currency;
                    $orderData[$recordNumber]['orderUserInfo']['state']            = $state;
                    $orderData[$recordNumber]['orderUserInfo']['city']             = $city;
                    $orderData[$recordNumber]['orderUserInfo']['county']           = ' ';   //区县默认空
                    $orderData[$recordNumber]['orderUserInfo']['address1']         = $street;
                    $orderData[$recordNumber]['orderUserInfo']['mobilePhone']      = $landline;
                    $orderData[$recordNumber]['orderUserInfo']['phone']            = $phone;
                    $orderData[$recordNumber]['orderUserInfo']['zipCode']          = $zipCode;

                    //detail信息
                    $orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['sku']                        = $sku;
                    $orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['amount']                     = $amount;
                    $orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['itemPrice']                  = $itemPrice;
                    $orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['createdTime']                = time();
                    $orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['shippingFee']                = $shippingFee;
                    $orderData[$recordNumber]['orderDetail'][$sku]['orderDetail']['recordNumber']               = $recordNumber;
                    $orderData[$recordNumber]['orderDetail'][$sku]['orderDetailExtension']['itemTitle']         = $itemTitle;
					/***************END 订单表数据***************/

					/***************BEGIN 订单扩展表数据***************/
                    $orderData[$recordNumber]['orderExtension']['feedback']		        = $note;
                    $orderData[$recordNumber]['orderExtension']['payPalPaymentId']		        = $transId;

					$orderData[$recordNumber]['orderExtension']['paymentStatus']		=	"Complete";
					$orderData[$recordNumber]['orderExtension']['transId']			    =	$transId;
					$orderData[$recordNumber]['orderExtension']['PayPalPaymentId']		=	$PayPalPaymentId;
					$orderData[$recordNumber]['orderExtension']['paymentMethod']		=	$payment_method;
					$orderData[$recordNumber]['orderExtension']['paymentModule']		=	$payment_module;
					$orderData[$recordNumber]['orderExtension']['shippingMethod']		=	$shipping_method;
					$orderData[$recordNumber]['orderExtension']['ShippingModule']		=	$shipping_module;
					$orderData[$recordNumber]['orderExtension']['currency']				=	$currency;
					$orderData[$recordNumber]['orderExtension']['feedback']				=	$feedback;    //客户留言
					/***************END 订单扩展表数据***************/

					/***************BEGIN 订单用户表数据***************/
					$orderData[$recordNumber]['orderUserInfo']['username']			=	$username;
					$orderData[$recordNumber]['orderUserInfo']['platformUsername']  =	$platformUsername;
					$orderData[$recordNumber]['orderUserInfo']['email']			    =	$email;
					$orderData[$recordNumber]['orderUserInfo']['countryName']	 	=	$countryName;
					$orderData[$recordNumber]['orderUserInfo']['currency']          =	$currency;
					$orderData[$recordNumber]['orderUserInfo']['state']			    =	$state;			// 省
					$orderData[$recordNumber]['orderUserInfo']['city']				=	$city;		 // 市
					$orderData[$recordNumber]['orderUserInfo']['street']			=	$street;
					$orderData[$recordNumber]['orderUserInfo']['address2']			=	$address2;
					$orderData[$recordNumber]['orderUserInfo']['landline']			=	$landline;			// 座机电话
					$orderData[$recordNumber]['orderUserInfo']['phone']				=	$phone;			            // 手机
					$orderData[$recordNumber]['orderUserInfo']['zipCode']			=	$zipCode;				// 邮编
					/*************END 订单用户表数据***************/
					//note信息
					if(!empty($note)){
						$orderData[$recordNumber]['orderNote']['content'] = $note;
					}
				}else{
					$sku 		= mysql_real_escape_string(trim($currentSheet->getCell($acc)->getValue()));
					$itemTitle	= mysql_real_escape_string(trim($currentSheet->getCell($add)->getValue()));
					$itemPrice 	= round_num(trim($currentSheet->getCell($aff)->getValue()), 2);
					$amount 	= intval(trim($currentSheet->getCell($agg)->getValue()));
					$shipingfee = round_num(trim($currentSheet->getCell($ahh)->getValue()), 2);

					/***************BEGIN 订单详细数据***************/
					$orderData[$recordNumber]['orderDetail'][$c]['orderDetail']['recordNumber']	=	$recordnumber;
					$orderData[$recordNumber]['orderDetail'][$c]['orderDetail']['sku']			=	$sku;
					$orderData[$recordNumber]['orderDetail'][$c]['orderDetail']['itemPrice']      =	$itemPrice;
					$orderData[$recordNumber]['orderDetail'][$c]['orderDetail']['amount']     	=	$amount;
					$orderData[$recordNumber]['orderDetail'][$c]['orderDetail']["shippingFee"]	=	$shipingfee;
					$orderData[$recordNumber]['orderDetail'][$c]['orderDetail']['createdTime']    =	time();
					/*************END 订单详细数据***************/


					/***************BEGIN 订单详细扩展表数据***************/
					$orderData[$recordNumber]['orderDetail'][$c]['orderDetailExtension']['itemTitle'] = $itemTitle;
					$orderData[$recordNumber]['orderDetail'][$c]['orderDetailExtension']['transId'] 	= $transId;
					//$orderData['orderDetail']['orderDetailExtenData']['note'] = $value[10];
					$categoryName	  = trim($currentSheet->getCell($ajj)->getValue());
					$customCode		  = trim($currentSheet->getCell($akk)->getValue());
					$material		  = trim($currentSheet->getCell($all)->getValue());
					$ShenBaoQuantity  = trim($currentSheet->getCell($amm)->getValue());
					$ShenBaoUnitPrice = trim($currentSheet->getCell($ann)->getValue());
					$ChineseDesc 	  = trim($currentSheet->getCell($aww)->getValue());
					//$salePrice						   =	round_num(mysql_real_escape_string(trim($detail['SalePrice'])), 2);	//实际SKU付款价
					/*************END 订单详细扩展表数据***************/
                    
                    
					$ebay_fedex_remark[$recordNumber][$categoryName][] = array('real_price'=>$ShenBaoUnitPrice,'qty'=>$ShenBaoQuantity,'hamcodes'=>$customCode,'detail'=>$material);
					$orderData[$recordNumber]['fedexRemark'] = $ebay_fedex_remark[$recordNumber];
				}
			}
            //fedexRemark 逻辑处理
            foreach($orderData as $recordNumber=>$tmpArr){
                $fedexRemarkArr = $tmpArr['fedexRemark'];
                $transportId = $orderData['order']['transportId'];
                if(!empty($fedexRemarkArr)){
                    $tmpArr1 = array();
                    foreach($fedexRemarkArr as $k=>$v){
                        $tmpArr1['description'] = trim("[No Brand]".$k."{$v[0]['detail']}");
                        if(in_array($carrierKV[$transportId], array('FedEx'))){
                            $tmpArr1['type'] = 1;
                        }elseif(in_array($carrierKV[$transportId], array('DHL','EMS','UPS美国专线'))){
                            $tmpArr1['type'] = 2;
                        }else{
                            break;
                        }
                        $sku_price = 0;
						$qty = 0;
						foreach($v as $v0){
							$sku_price 	+= $v0['real_price'];
							$qty 		+= $v0['qty'];
						}
                        $tmpArr1['price'] 	 = round($sku_price/$qty,2);
						$tmpArr1['amount'] 	 = $qty;
						$tmpArr1['hamcodes'] = $v[0]['hamcodes'];
						if(in_array($carrierKV[$transportId], array('DHL','EMS','UPS美国专线'))){
							$tmpArr1['price'] = round($sku_price,2);
						}
                    }
                    $orderData[$recordNumber]['fedexRemark'] = $tmpArr1;
                }
            }
            var_dump($orderData);
            exit;
            //return $this->act_insertOrder($orderData);
        }else{
            self::$errMsg[] = get_promptmsg(10053);
            return false;
        }
    }	
}


?>