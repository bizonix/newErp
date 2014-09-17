<?php
	require_once "eBaySession.php";
	class GetCertainOrderAPI{
		private $handle;
		private $token;
		private $devID;
		private $appID;
		private $certID;
		private $compatabilityLevel;
		private $siteID;
		private $verb='GetOrders';
		
		public function __construct($ebay_account){
			require WEB_PATH_CONF_SCRIPTS_KEYS_EBAY.'keys_'.$ebay_account.'.php';
			$this->token=$userToken;
			$this->devID=$devID;
			$this->appID=$appID;
			$this->certID=$certID;
			$this->siteID=$siteID;
			$this->serverUrl=$serverUrl;
			$this->compatabilityLevel=$compatabilityLevel;
			
			$this->handle=new eBaySession($this->token, $this->devID, $this->appID, $this->certID,
										  $this->serverUrl, $this->compatabilityLevel, $this->siteID, $this->verb);
		}
		public function request($order_ids){
			$requestXmlBody = '<?xml version="1.0" encoding="utf-8"?>
				<GetOrdersRequest xmlns="urn:ebay:apis:eBLBaseComponents">
				<RequesterCredentials>
					<eBayAuthToken>'.$this->token.'</eBayAuthToken>
				</RequesterCredentials>  
				<DetailLevel>ReturnAll</DetailLevel>
				<IncludeFinalValueFee>true</IncludeFinalValueFee>
				<OrderRole>Seller</OrderRole><OrderStatus>Completed</OrderStatus>
				';

			$valid_orderids=array();				
			
			$order_p1='#^\d{12}$#i';//multiple line item order
			$order_p2='#^\d{12}\-\d{12,14}$#i';//single line item order
			$order_p3='#^\d{12}\-0$#i';//single line item order(sometimes trans id is zero)
			
			if(is_array($order_ids)){				
				foreach($order_ids as $orderid){
					if(	preg_match($order_p1,$orderid) || preg_match($order_p2,$orderid) || preg_match($order_p3,$orderid) ){
						$valid_orderids[]='<OrderID>'.$orderid.'</OrderID>';
					}
				}				
			}else{
				if(	preg_match($order_p1,$order_ids) ||	preg_match($order_p2,$order_ids)  ||	preg_match($order_p3,$order_ids) ){
					$valid_orderids[]='<OrderID>'.$orderid.'</OrderID>';
				}
			}
			
			if(count($valid_orderids)>0){
				$requestXmlBody.='<OrderIDArray>'.implode('',$valid_orderids).'</OrderIDArray>';
			}else{
				return FALSE;
			}
			$requestXmlBody.='</GetOrdersRequest>';
			$responseXml = $this->handle->sendHttpRequest($requestXmlBody);
			return $responseXml;
		}
		
		public function getPayPalEmailAddress($itemid){			
			$handle=new eBaySession($this->token, $this->devID, $this->appID, $this->certID, $this->serverUrl, $this->compatabilityLevel, $this->siteID, 'GetItem');
			$requestXmlBody = ' <?xml version="1.0" encoding="utf-8"?>
									<GetItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">
									<RequesterCredentials>
										<eBayAuthToken>'.$this->token.'</eBayAuthToken>
									</RequesterCredentials>
									<OutputSelector>Item.PayPalEmailAddress</OutputSelector>
									<ItemID>'.$itemid.'</ItemID>
									<WarningLevel>High</WarningLevel>
								</GetItemRequest>';
			$responseXml = $handle->sendHttpRequest($requestXmlBody);
			$responseDoc = new DomDocument();	
			$responseDoc->loadXML($responseXml);
			$paypaladress = $responseDoc->getElementsByTagName('PayPalEmailAddress')->item(0)->nodeValue;
			echo "收款paypal：{$paypaladress}\n\n";
			return $paypaladress;
		}
		
		public function getSellerTransactions($orderid){		
			$handle=new eBaySession($this->token, $this->devID, $this->appID, $this->certID, $this->serverUrl, $this->compatabilityLevel, $this->siteID, 'GetOrderTransactions');
			$requestXmlBody = ' <?xml version="1.0" encoding="utf-8"?>
									<GetOrderTransactionsRequest xmlns="urn:ebay:apis:eBLBaseComponents">
									<RequesterCredentials>
										<eBayAuthToken>'.$this->token.'</eBayAuthToken>
									</RequesterCredentials>
									<DetailLevel>ReturnAll</DetailLevel>
									<OutputSelector>OrderArray.Order.TransactionArray.Transaction.Buyer.BuyerInfo.ShippingAddress</OutputSelector>
									<IncludeFinalValueFee>true</IncludeFinalValueFee>
									<OrderRole>Seller</OrderRole>
									<OrderStatus>Completed</OrderStatus>
									<OrderIDArray>
										<OrderID>'.$orderid.'</OrderID>
									</OrderIDArray>
								</GetOrderTransactionsRequest>';
			$responseXml = $handle->sendHttpRequest($requestXmlBody);
			$responseDoc = new DomDocument();
			$responseDoc->loadXML($responseXml);
			return $responseDoc->getElementsByTagName('ShippingAddress')->item(0);
		}
		
		//订单加载函数--某一笔交易
		function GetCertainOrder($ebay_account, $order_ids=array()){
			global $rabbitMQClass;
			//var_dump(CommonModel::getTransCarrierInfo()); exit;
			//var_dump(CommonModel::calcAddOrderShippingFee2($arr)); exit;
			//var_dump(CommonModel::getMaterInfo('59')); exit;//获取包材信息
			//var_dump(CommonModel::getSkuinfo('TK0307')); exit;
			//var_dump(CommonModel::getCombineSkuinfo('TK_CB94')); exit;
			//CommonModel::calcAddOrderWeight();exit;
			if(empty($order_ids)){
				$exchange = 'ebay_order_id_queue_'.$ebay_account;
				$queue = 'ebay_from_queue_'.$ebay_account;
				$order_ids = $rabbitMQClass->queue_subscribe($exchange,$queue);
				echo "已接收队列中 ".count($order_ids)." 条数据\n";
			}
			$count_order_ids = count($order_ids);
			if($count_order_ids == 0){
				return false;
			}
			
			$valid_order_ids=array();
			$invalid_order_ids=array();
			$has_invalid_order_id=false;
			
			$order_p1='#^\d{12}$#i';//multiple line item order
			$order_p2='#^\d{12}\-\d{12,14}$#i';//single line item order
			$order_p3='#^\d{12}\-0$#i';//single line item order(sometimes trans id is zero)
			if(is_array($order_ids)){				
				foreach($order_ids as $orderid){
					if(	preg_match($order_p1,$orderid) || preg_match($order_p2,$orderid) || preg_match($order_p3,$orderid) ){
						$valid_order_ids[]	=$orderid;
					}else{
						$invalid_order_ids[]=$orderid;
						$has_invalid_order_id=true;
					}
				}				
			}else{
				if(	preg_match($order_p1,$order_ids) ||	preg_match($order_p2,$order_ids) || preg_match($order_p3,$order_ids) ){
					$valid_order_ids[]=$order_ids;
				}else{
					$invalid_order_ids[]=$order_ids;
					$has_invalid_order_id=true;
				}	
			}
			
			if(	$has_invalid_order_id===true ){
				exit("Error: Pass invalid ebay order id[".implode(',',$invalid_order_ids)."]\n");
			}
			
			//分页抓取.---------------------------------------------------------------------
			$count	=	0;
			$per_page_ids	=	array();
			//var_dump($valid_order_ids);
			foreach ($valid_order_ids as $per_id){
				$page	=	ceil($count/50);
				//echo $page;
				$per_page_ids[$page][]	=	$per_id;
				$count++;
			}
			
			//var_dump($per_page_ids);
			//exit;
			
			echo	"抓取....\t";
			
			foreach($per_page_ids as $per_ids){
				while(1){
					$responseXml=$this->request($per_ids);
					if(empty($responseXml)){
						echo "Return Empty...Sleep 5 seconds..";
						sleep(5);
						continue;
					}
					//网络出现代理Proxy error 脚本休眠20秒
					$poxy_error_p='#Proxy\s*Error#i';
					if(preg_match($poxy_error_p,$responseXml)){
						echo "Proxy Error...Sleep 10 seconds..";
						sleep(10);
						continue;
					}
					break;
				}
				echo "\n";
				$responseDoc = new DomDocument();	
				$responseDoc->loadXML($responseXml);
				
				$raw_data_filename=EBAY_RAW_DATA_PATH.$ebay_account.'/certain_order/'.date('Y-m').'/'.date('d').'/'.date('Y-m-d_H-i-s').'.xml';
				$save_res=save_ebay_raw_data($raw_data_filename,$responseXml);
				if($save_res!==false){
					echo "save raw data ok...\n";
				}else{
					echo "save raw data fail...\n";
				}
				$responseXml=null;
				unset($responseXml);
				
				//$TotalNumberOfPages	 	= $responseDoc->getElementsByTagName('TotalNumberOfPages')->item(0)->nodeValue;
				//$TotalNumberOfEntries	= $responseDoc->getElementsByTagName('TotalNumberOfEntries')->item(0)->nodeValue;
			
				//$hasmore 	= $responseDoc->getElementsByTagName('HasMoreOrders')->item(0)->nodeValue;
				$Ack	 	= $responseDoc->getElementsByTagName('Ack')->item(0)->nodeValue;
				$pcount		= 1;
				echo "同步状态: $Ack \n";
				if($Ack == 'Failure'){
					echo "eBay Return Failure...return.. \n";
					return "eBay Return Failure...return..";
				}
				$SellerOrderArray	= $responseDoc->getElementsByTagName('Order');
				
				//调用订单入库函数
				$message = $this->__handle_ebay_orderxml($SellerOrderArray,$ebay_account);
			
		
			}
			//return $message;
		}
		
		//订单入库函数
		function __handle_ebay_orderxml(&$SellerOrderArray,$ebay_account){
			global $FLIP_GLOBAL_EBAY_ACCOUNT;
			if(!isset($FLIP_GLOBAL_EBAY_ACCOUNT)){
				$omAvailableAct = new OmAvailableAct();
				$GLOBAL_EBAY_ACCOUNT = $omAvailableAct->act_getTNameList2arrById('om_account', 'id', 'account', ' WHERE is_delete=0 ');
				$FLIP_GLOBAL_EBAY_ACCOUNT = array_flip($GLOBAL_EBAY_ACCOUNT);
			}
			global $mctime,$_allow_spide_itemid;
			$account_suffix = get_account_suffix($ebay_account);
			$message = "";
			foreach( $SellerOrderArray as $SellerOrder){
				//每个订单号
				$oSellerOrderID		= $SellerOrder->getElementsByTagName('OrderID')->item(0)->nodeValue;
				//oCreatingUserRole用于判断是否是 combined payments
				$oCreatingUserRole	= @$SellerOrder->getElementsByTagName('CreatingUserRole')->item(0)->nodeValue;
				$oAmountPaid  		= $SellerOrder->getElementsByTagName('AmountPaid')->item(0)->nodeValue;
				
				$shippingDeatil		= $SellerOrder->getElementsByTagName('ShippingDetails')->item(0);
				$oRecordNumber		= $shippingDeatil->getElementsByTagName('SellingManagerSalesRecordNumber')->item(0)->nodeValue;
				$shippingDeatil=null;unset($shippingDeatil);
				//订单状态
				$CheckoutStatus		= $SellerOrder->getElementsByTagName('CheckoutStatus')->item(0);
				$LastTimeModified 	= strtotime($CheckoutStatus->getElementsByTagName('LastModifiedTime')->item(0)->nodeValue);			
				$oeBayPaymentStatus = $CheckoutStatus->getElementsByTagName('eBayPaymentStatus')->item(0)->nodeValue;
				$oCompleteStatus 	= $CheckoutStatus->getElementsByTagName('Status')->item(0)->nodeValue;	
				$oCheckStatus 		= $CheckoutStatus->getElementsByTagName('Status')->item(0)->nodeValue;
				$CheckoutStatus=null;unset($CheckoutStatus);
				
				//该订单交易信息
				$osoTransArray	= $SellerOrder->getElementsByTagName('Transaction');
				
				//其他交易信息比如payapl整合到ebay
				$oTid				=0;//兼容表结构,其实此时还没有交易号的概念
				$ExtTran			=$SellerOrder->getElementsByTagName('ExternalTransaction')->item(0);
				$noptid_trans		=false;
				if(!empty($ExtTran)){
					$oPtid 				=$ExtTran->getElementsByTagName('ExternalTransactionID')->item(0)->nodeValue;	
					$oFeeOrCreditAmount =$ExtTran->getElementsByTagName('FeeOrCreditAmount')->item(0)->nodeValue;
				}else{
					$oPtid				='0';
					$oFeeOrCreditAmount =0.0;
					echo " Notice : [$oSellerOrderID]Not ebay offical paypal trans\n";
					$noptid_trans		=true;
				}
				//以下信息强制以订单的transation数据中第一条交易为准而取
				if(is_object($osoTransArray->item(0)->getElementsByTagName('Buyer')->item(0))){
					$oEmail 			= str_rep($osoTransArray->item(0)->getElementsByTagName('Buyer')->item(0)->getElementsByTagName('Email')->item(0)->nodeValue);	
				}else{
					echo "\n同步订单未获取邮箱\n";
					$oEmail = "";
				}
				$oSite				= str_rep($osoTransArray->item(0)->getElementsByTagName('Item')->item(0)->getElementsByTagName('Site')->item(0)->nodeValue);
				if(empty($oSite)){
					$oSite		=str_rep($osoTransArray->item(0)->getElementsByTagName('TransactionSiteID')->item(0)->nodeValue);
				}
				//货币类型
				$oCurrency 			= $osoTransArray->item(0)->getElementsByTagName('TransactionPrice')->item(0)->attributes->item(0)->nodeValue;
				
				//userid
				$oUserID 			= str_rep($SellerOrder->getElementsByTagName('BuyerUserID')->item(0)->nodeValue);
				$BuyerInfo 			= $SellerOrder->getElementsByTagName('ShippingAddress')->item(0);
				$oName 				= str_rep($BuyerInfo->getElementsByTagName('Name')->item(0)->nodeValue);
				$oName				= mysql_real_escape_string($oName);
				$oStreet1 			= str_rep($BuyerInfo->getElementsByTagName('Street1')->item(0)->nodeValue);
				$oStreet2 			= str_rep($BuyerInfo->getElementsByTagName('Street2')->item(0)->nodeValue);
				$oCityName 			= str_rep($BuyerInfo->getElementsByTagName('CityName')->item(0)->nodeValue);
				$oStateOrProvince 	= str_rep($BuyerInfo->getElementsByTagName('StateOrProvince')->item(0)->nodeValue);
				$oCountry 			= str_rep($BuyerInfo->getElementsByTagName('Country')->item(0)->nodeValue);
				$oCountryName 		= str_rep($BuyerInfo->getElementsByTagName('CountryName')->item(0)->nodeValue);
				$oPostalCode 		= str_rep($BuyerInfo->getElementsByTagName('PostalCode')->item(0)->nodeValue);
				$oPhone 			= $BuyerInfo->getElementsByTagName('Phone')->item(0)->nodeValue;
				$BuyerInfo=null;unset($BuyerInfo);
				//顾客留言
				$oBuyerCheckoutMessage	= @str_rep($SellerOrder->getElementsByTagName('BuyerCheckoutMessage')->item(0)->nodeValue);//顾客购买留言
				$oBuyerCheckoutMessage	= str_replace('<![CDATA[','',$oBuyerCheckoutMessage);
				$oBuyerCheckoutMessage	= str_replace(']]>','',$oBuyerCheckoutMessage);
				//付款时间			
				$oPaidTime 			= strtotime($SellerOrder->getElementsByTagName('PaidTime')->item(0)->nodeValue);
				$oCreateTime		= strtotime($SellerOrder->getElementsByTagName('CreatedTime')->item(0)->nodeValue);
				$oShippedTime    	= @strtotime($SellerOrder->getElementsByTagName('ShippedTime')->item(0)->nodeValue);
				
				$SSS				= $SellerOrder->getElementsByTagName('ShippingServiceSelected')->item(0);
				$oShipingService	= $SSS->getElementsByTagName('ShippingService')->item(0)->nodeValue;
				$oShipingFee		= $SSS->getElementsByTagName('ShippingServiceCost')->item(0)->nodeValue;
				
				$SSS=null;unset($SSS);
				
				//店铺收款paypal account
				//$oPayPalEmailAddress = @$SellerOrder->getElementsByTagName('PayPalEmailAddress')->item(0)->nodeValue;
				$_itemid = $osoTransArray->item(0)->getElementsByTagName('ItemID')->item(0)->nodeValue;
				$oPayPalEmailAddress = $this->getPayPalEmailAddress($_itemid);
				//var_dump($Item_Elements);
				//$oPayPalEmailAddress = $Item_Elements->getElementsByTagName('PayPalEmailAddress')->nodeValue;
				//echo $oCompleteStatus."======".$oeBayPaymentStatus."======".$oPaidTime; echo "\n";			
				if($oCompleteStatus == "Complete" && $oeBayPaymentStatus == "NoPaymentFailure" && $oPaidTime > 0){
					$oOrderStatus	= 1;
				}
				if($noptid_trans === true){//不是通过ebay官方交易的paypal交易
					if($oCompleteStatus == "Complete" && $oeBayPaymentStatus == "NoPaymentFailure" && $oPaidTime > 0){
						$oOrderStatus =687;
					}
				}
				
				$is_allow_spide_itemid = false;
				if($oPaidTime<=0 || $oPaidTime=='' || empty($oPaidTime) && count($osoTransArray)==1){
					$_QuantityPurchased = $osoTransArray->item(0)->getElementsByTagName('QuantityPurchased')->item(0)->nodeValue;
					if (in_array($_itemid, $_allow_spide_itemid) && $_QuantityPurchased>0){
						echo "未付款促销订单抓取--------";
						$oOrderStatus = $_QuantityPurchased==1 ? 687 : 688;
						$oAmountPaid = 9999;
						$oPaidTime = $oCreateTime;
						$is_allow_spide_itemid = true;
						$buyAddress = $this->getSellerTransactions($oSellerOrderID);
						$oName 				= str_rep($buyAddress->getElementsByTagName('Name')->item(0)->nodeValue);
						$oName				= mysql_real_escape_string($oName);
						$oStreet1 			= str_rep($buyAddress->getElementsByTagName('Street1')->item(0)->nodeValue);
						$oCityName 			= str_rep($buyAddress->getElementsByTagName('CityName')->item(0)->nodeValue);
						$oStateOrProvince 	= str_rep($buyAddress->getElementsByTagName('StateOrProvince')->item(0)->nodeValue);
						$oCountry 			= str_rep($buyAddress->getElementsByTagName('Country')->item(0)->nodeValue);
						$oCountryName 		= str_rep($buyAddress->getElementsByTagName('CountryName')->item(0)->nodeValue);
						$oPostalCode 		= str_rep($buyAddress->getElementsByTagName('PostalCode')->item(0)->nodeValue);
						$oPhone 			= $buyAddress->getElementsByTagName('Phone')->item(0)->nodeValue;
					}
				}
				if($oShippedTime >0) $oOrderStatus	= 2;//已经发货
				$oRefundAmount	= 0; //表示未垦退款
				if(	($oOrderStatus == 1 && $oShippedTime <=0 && $oPaidTime >0) || ($oOrderStatus ==4 && $oShippedTime <=0 ) || ( in_array($oOrderStatus, array(687,688)) && $oShippedTime <=0 && $is_allow_spide_itemid == true)){
					echo "eBay订单号[$oSellerOrderID]有效 ,订单类型[{$oOrderStatus}] ";
					$check_ebayorderid = true;
					//检查汇总表该 eBay 订单号是否已经存在
					//echo "===={$oSellerOrderID}==={$ebay_account}====";//检测重复抓单信息
					$where 				= 	" where orderid='".$oSellerOrderID."' and accountId='".$FLIP_GLOBAL_EBAY_ACCOUNT[$ebay_account]."' ";
					$check_ebayorderid 	= 	OrderidsModel::judgeOrderidsList('orderid',$where);
					//取消
					$check_ebayorderid == false;
					$new_ebay_id		=	true;
					if($check_ebayorderid === false){//添加订单汇总
						/* 生成一个本地系统订单号 */
						//$our_sys_ordersn=date('Y-m-d-His').mt_rand(100,999).$oRecordNumber;
						$oorder_no		= '';//已废弃
						
						$isNote = 0;
						if(!empty($oBuyerCheckoutMessage)){
							$isNote = 1;		
						}
						$orderData = array();
						$orderData = array('orderData' => array('recordNumber'=>$oRecordNumber,
																  'platformId'=>1,
																  'accountId'=>$FLIP_GLOBAL_EBAY_ACCOUNT[$ebay_account],
																  'ordersTime'=>$oCreateTime,
																  'paymentTime'=>$oPaidTime,
																  'onlineTotal'=>$oAmountPaid,
																  'actualTotal'=>$oAmountPaid,
																  'transportId'=>'',
																  'actualShipping'=>$oShipingFee,
																  'orderStatus'=>C("STATEPENDING"),
																  'orderType'=>C("STATEPENDING_CONV"),
																  'orderAttribute'=>1,
																  //'pmId'=>'',
																  'channelId'=>'',
																  //'calcWeight'=>0.000,
																  //'calcShipping'=>'',
																  'orderAddTime'=>$mctime,
																  'isNote'=>$isNote,
																  'storeId'=>1,
														  ),
											'orderExtenData' => array('declaredPrice'=>0.00,
																	  'paymentStatus'=>$oCompleteStatus,
																	  'transId'=>1,
																	  'PayPalPaymentId'=>$oPtid,
																	  'site'=>$oSite,
																	  'orderId'=>$oSellerOrderID,
																	  'platformUsername'=>$oUserID,
																	  'currency'=>$oCurrency,
																	  'feedback'=>$oBuyerCheckoutMessage,
																	  'PayPalEmailAddress'=>$oPayPalEmailAddress,
																	  'eBayPaymentStatus'=>$oeBayPaymentStatus,
															  ),					  
											'orderUserInfoData' => array('username'=>$oName,
																	  'platformUsername'=>$oUserID,
																	  'email'=>$oEmail,
																	  'countryName'=>$oCountryName,
																	  'countrySn'=>$oCountry,
																	  'currency'=>$oCurrency,
																	  'state' =>$oStateOrProvince,
																	  'city' =>$oCityName,
																	  'street' =>$oStreet1,
																	  'address2' =>$oStreet2,
																	  'address3' =>'',
																	  'landline' =>$oPhone,
																	  'phone' =>$ebay_state,
																	  'zipCode' =>$oPostalCode,
																	  )
															  );
					
						echo "\tUserID:$oUserID"." AMT:$oAmountPaid recordNO:$oRecordNumber 付款状态:$oCompleteStatus 付款时间:".date('Y-m-d H:i:s',$oPaidTime)."\n";
							
						//添加订单明细
						$obj_order_detail_data = array();
						foreach($osoTransArray	as $transaction){
							//该交易的销售编号
							$tran_recordnumber	= $transaction->getElementsByTagName('ShippingDetails')->item(0)->getElementsByTagName('SellingManagerSalesRecordNumber')->item(0)->nodeValue;
							/* 多属性订单 */
							$attribute	= '';
							$buy_with_attr=false;
							$tran_varia=$transaction->getElementsByTagName('Variation')->item(0);
							if(is_object($tran_varia)){//未添加明细的属性 20130301
								if(	$tran_varia->hasChildNodes() ){
									$Variation	= $tran_varia->getElementsByTagName('NameValueList')->item(0);
									if( !empty($Variation) && $Variation->hasChildNodes() ){
										foreach($Variation as $variate){
											$aname	= $variate->getElementsByTagName('Name')->item(0)->nodeValue;
											$avalue	= $variate->getElementsByTagName('Value')->item(0)->nodeValue;
											$attribute	.= $aname.":".$avalue." ";
										}
									}
									$buy_with_attr=true;
									$Variation=null;unset($Variation);
								}
							}
							$tran_id			= $transaction->getElementsByTagName('TransactionID')->item(0)->nodeValue;
							//该交易的物品信息
							$odItem 			= $transaction->getElementsByTagName('Item')->item(0);
							if($buy_with_attr===true){
								$odItemTitle 	= @$tran_varia->getElementsByTagName('VariationTitle')->item(0)->nodeValue;	
								$odSKU			= @$tran_varia->getElementsByTagName('SKU')->item(0)->nodeValue;
							}else{
								$odItemTitle	=str_rep($odItem->getElementsByTagName('Title')->item(0)->nodeValue);
								$odSKU			=str_rep($odItem->getElementsByTagName('SKU')->item(0)->nodeValue);
							}
							$is_suffix = 0;
							if (!empty($account_suffix)){
								list($truesku, $skusuffix) = explode(':', $odSKU);
								if (!empty($skusuffix)/*&&strpos($account_suffix, $skusuffix)!==false*/){
									$odSKU = $truesku;
									$is_suffix = 1;
								}
							}
							###########悲剧 目前getorder api 无法取得下面2个值#########
							//ebay刊登物品的分类ID,备用字段
							//$CategoryID 		= @$odItem->getElementsByTagName('PrimaryCategory')->item(0)->getElementsByTagName('CategoryID')->item(0)->nodeValue;
							//$ListingType 		= @$odItem->getElementsByTagName('ListingType')->item(0)->nodeValue;
							$CategoryID			=0;
							$ListingType		='';	
							//购买数量
							$QuantityPurchased=$transaction->getElementsByTagName('QuantityPurchased')->item(0)->nodeValue;
							//交易创建时间
							$CreatedDate = strtotime($transaction->getElementsByTagName('CreatedDate')->item(0)->nodeValue);
							$FinalValueFee	= $transaction->getElementsByTagName('FinalValueFee')->item(0)->nodeValue;
							$tran_price		= $transaction->getElementsByTagName('TransactionPrice')->item(0)->nodeValue;
							$goodsshippingcost = $transaction->getElementsByTagName('ActualShippingCost')->item(0)->nodeValue;
							$goodsshippingcost = empty($goodsshippingcost) ? '0.0' : $goodsshippingcost;
							$tran_itemid	= $odItem->getElementsByTagName('ItemID')->item(0)->nodeValue;
							$tran_site		= $odItem->getElementsByTagName('Site')->item(0)->nodeValue;
							//$obj_order_detail	=new eBayOrderDetail();
								
							$obj_order_detail_data[] = array('orderDetailData' => array('recordNumber'=>$tran_recordnumber,
																						'itemPrice'=>$tran_price,
																						'sku'=>strtoupper($odSKU),
																						'amount'=>$QuantityPurchased,
																						'shippingFee'=>$goodsshippingcost,
																						'createdTime'=>$mctime,
																						),			
															'orderDetailExtenData' => array('itemId'=>$tran_itemid,
																						 'transId'=>$tran_id,
																						 'itemTitle'=>$odItemTitle,
																						 'itemURL'=>'',
																						 'shippingType'=>$oShipingService,
																						 'FinalValueFee'=>$FinalValueFee,
																						 'FeeOrCreditAmount'=>$oFeeOrCreditAmount,
																						 'ListingType'=>$ListingType,
																						 'note'=>$oBuyerCheckoutMessage,
																						 //'attribute'=>$attribute,
																						 //'is_suffix'=>$is_suffix
																						 )
															);	 
						}
						$orderData['orderDetail'] = $obj_order_detail_data;
						$rtn = OldsystemModel::orderErpInsertorder($orderData);
						//var_dump($rtn);
						$insertData = array();
						if($rtn['errcode'] == 200){
							var_dump($rtn);
							$rtn_data = $rtn['data'];
							$orderId = $rtn_data['orderId'];
							echo "插入老系统成功，订单编号 [$orderId] \n";
							$pmId = $rtn_data['pmId'];
							$totalweight = $rtn_data['totalweight'];
							$shipfee = $rtn_data['shipfee'];
							$carrier = $rtn_data['carrier'];
							$carrierId = $rtn_data['carrierId'];
							$status = $rtn_data['status'];
							
							$orderData['orderData']['id'] = $orderId;//赋予新系统订单编号@20140501
							
							$calcInfo = CommonModel :: calcAddOrderWeight($obj_order_detail_data);//计算重量和包材
							//var_dump($calcInfo);
							$orderData['orderData']['ORcalcWeight'] = $calcInfo[0];
							$orderData['orderData']['calcWeight'] = $calcInfo[0];
							$orderData['orderData']['pmId'] = $calcInfo[1];
							if($orderData['orderData']['calcWeight'] != $totalweight){
								$insertData['old_totalweight']=$totalweight;
								$insertData['new_totalweight']=$orderData['orderData']['calcWeight'];
							}
							if($orderData['orderData']['pmId'] != $pmId){
								$insertData['old_pmId']=$pmId;
								$insertData['new_pmId']=$orderData['orderData']['pmId'];
							}
							if(count($orderData['orderDetail']) > 1){
								$orderData['orderData']['orderAttribute'] = 3;
							}else if(isset($orderData['orderDetail'][0]['orderDetailData']['amount']) && $orderData['orderDetail'][0]['orderDetailData']['amount'] > 1){
								$orderData['orderData']['orderAttribute'] = 2;	
							}
							$calcShippingInfo = TransAPIModel::trans_carriers_best_get($orderData['orderData']['calcWeight'],$orderData['orderUserInfoData']['countryName'],$ebay_account,$orderData['orderData']['actualTotal']);//计算运费
							//var_dump($calcShippingInfo);
							$orderData['orderData']['calcShipping'] = $calcShippingInfo['fee'];
							$orderData['orderData']['transportId'] = $calcShippingInfo['carrierId'];
							$orderData['orderData']['ORtransportId'] = $calcShippingInfo['carrierId'];
							$orderData['orderData']['channelId'] = $calcShippingInfo['channelId'];
							$orderData['orderData']['ORchannelId'] = $calcShippingInfo['channelId'];
							
							if($orderData['orderData']['calcShipping'] != $shipfee){
								$insertData['old_shippfee']=$shipfee;
								$insertData['new_shippfee']=$orderData['orderData']['calcShipping'];
							}
							if($orderData['orderData']['transportId'] != $carrierId){
								$insertData['old_carrierId']=$carrierId;
								$insertData['new_carrierId']=$orderData['orderData']['transportId'];
							}
							
							if(!empty($insertData)){
								$insertData['ebay_id'] = $orderId;
								$insertData['addtime'] = time();
								var_dump($insertData);
								OldsystemModel::insertTempSyncRecords($insertData);// 插入临时对比记录表
							}
							
							$orderData = AutoModel :: auto_contrast_intercept($orderData);
							$statusArr = StatusMenuModel::getStatusMenuByOldStatus($status);
							if(empty($statusArr)){
								echo "未获取老系统状态{$status}转换新码\n";
							}else{
								$orderData['orderData']['ORorderStatus'] = $statusArr[0];
								$orderData['orderData']['ORorderType'] = $statusArr[1];
								$orderData['orderData']['orderStatus'] = $statusArr[0];
								$orderData['orderData']['orderType'] = $statusArr[1];	
							}
							if(OrderAddModel :: insertAllOrderRow($orderData)){
								echo "本地 Record No. [$oRecordNumber] 入库成功\n";
								$message .= "<font color='green'>本地订单号 [$oRecordNumber] 入库成功</font><br>";
							}else{
								echo OrderAddModel :: $errMsg;
								$message .= "<font color='red'>本地订单号 [$oRecordNumber] 入库失败</font><br>";
							}
							
						}else{
							var_dump($rtn);
						}
						//exit;
					}else{
						echo "本地订单号 [$oRecordNumber] 入库失败\n";
						$message .= "<font color='red'>本地订单号 [$oRecordNumber] 入库失败</font><br>";
					}
				}else{
					echo "eBay订单号[$oSellerOrderID] 记录编号[$oRecordNumber] 无效 不入库...\t";
					$message .=  "<font color='red'>{$oSellerOrderID}无效 不入库...</font><br>";
					if($oShippedTime>0 || $oOrderStatus==2){
						echo "已经发货\t";
						$message .= "<font color='red'>{$oSellerOrderID}已经发货</font><br>";
					}
					if($oPaidTime<=0 || $oPaidTime=='' || empty($oPaidTime) ){
						echo "未付款\t";
						$message .=  "<font color='red'>{$oSellerOrderID}未付款</font><br>";
					}
					echo "\n";
					//pop_ebay_orderid_queue($oSellerOrderID,$ebay_account);
				}
			}
			return 	$message;		
		}
		
		//把各账号的ebay 订单号放到汇总表
		/*function save_ebay_orderid_table($ebay_id,$ebay_ptid,$ebay_orderid,$ebay_account,$ebay_createtime){
			global $dbConn;
			$ebay_orderid_statistic_sql='insert into ebay_order_ids (omOrderId,PayPalPaymentId,orderid,account,saletime) value('.$ebay_id.',"'.$ebay_ptid.'","'.$ebay_orderid.'","'.$ebay_account.'",'.$ebay_createtime.')';
			$dbConn->query($ebay_orderid_statistic_sql);
		}*/
	
	}
?>