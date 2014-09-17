<?php
/*
 * ebay平台对接接口
 * add by: linzhengxiang @date 20140618
 */
class AmazonButtAct extends CheckAct{
	
	private $authorize = array();
	
	public function __construct(){
		parent::__construct();
	}
	
	public function setToken($account, $site){
		######################以后扩展到接口获取 start ######################
		$AWS_ACCESS_KEY_ID		= 'AKIAJ723DWN5Y5WTF36A';
		$AWS_SECRET_ACCESS_KEY	= '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';
		$MERCHANT_ID			= 'AW10J8VB8Z74G';
		$MARKETPLACE_ID			= 'ATVPDKIKX0DER';
		$APPLICATION_NAME		= 'DB Order Synchronization';
		$APPLICATION_VERSION	= '0.1';
		$serviceUrl				= 'https://mws.amazonservices.com/Orders/2011-01-01';
	
		
		######################以后扩展到接口获取  end  ######################
		$this->authorize = array(	
									'appname'			=>$APPLICATION_NAME,
									'appversion'		=>$APPLICATION_VERSION,
									'acckeyid'			=>$AWS_ACCESS_KEY_ID,
									'acckey'			=>$AWS_SECRET_ACCESS_KEY,
									'merchantid'		=>$MERCHANT_ID,
									'marketplaceid'		=>$MARKETPLACE_ID,
									'serviceUrl'		=>$serviceUrl,
									'account'			=>$account,
									'site'				=>$site,
							);
	}
	
	/**
	 * 根据订单号抓取订单列表
	 * @param datetime $starttime
	 * @param datetime $endtime
	 * @return array
	 * @author lzx
	 */
	public function spiderOrderLists($starttime, $endtime){
		$OrderObject = F('amazon.package.GetOrders');
		F('xmlhandle');
		$OrderObject->setRequestConfig($this->authorize);
		
		$simplelists = array();
		
		//使用启用缓存方便调试：0：不使用; 1：正常使用缓存; 2：更新缓存中的数据
		if(!empty($GLOBALS['memc_obj']) && C('ENABLE_AMAZON_GET_ORDER_CACHE') != 0){
			$cache_mode  = C('ENABLE_AMAZON_GET_ORDER_CACHE');
			
			$simplelists = $GLOBALS['memc_obj']->get('ENABLE_AMAZON_GET_ORDER_CACHE');
			
			if(empty($simplelists) || $cache_mode == 2){
				$simplelists = $OrderObject->getOrderLists($starttime, $endtime);
				//amazon订单数据存入缓存
				$GLOBALS['memc_obj']->set('ENABLE_AMAZON_GET_ORDER_CACHE', $simplelists, 10800);
			}
		}else{
			$simplelists = $OrderObject->getOrderLists($starttime, $endtime);
		}
		
		$StatusMenu = M('StatusMenu');
		$ORDER_INIT = $StatusMenu->getOrderStatusByStatusCode('ORDER_INIT','id');
		
		foreach($simplelists as $xml_simplelist){
			$simplelist = object_array($xml_simplelist);
			$orders = $simplelist['' . "\0" . '*' . "\0" . '_fields']['Orders']['FieldValue'];
			foreach($orders as $order){
				$orderInfo = $order['' . "\0" . '*' . "\0" . '_fields'];
				//订单表数据
				$order = array(
						'recordNumber'		=> $orderInfo['AmazonOrderId']['FieldValue'],
						'platformId'		=> 11,
						'site'				=> $this->authorize['site'],
						'paymentMethod'		=> $orderInfo['PaymentMethod']['FieldValue'],
						'currency'			=> $orderInfo['OrderTotal']['FieldValue']['' . "\0" . '*' . "\0" . '_fields']['CurrencyCode']['FieldValue'],
						'accountId'			=> M('Account')->getAccountIdByName($this->authorize['account']),
						'ordersTime'		=> strtotime($orderInfo['PurchaseDate']['FieldValue']),
						'paymentTime' 		=> strtotime($orderInfo['LastUpdateDate']['FieldValue']),
						'onlineTotal' 		=> $orderInfo['OrderTotal']['FieldValue']['' . "\0" . '*' . "\0" . '_fields']['Amount']['FieldValue'],
						'actualTotal' 		=> $orderInfo['OrderTotal']['FieldValue']['' . "\0" . '*' . "\0" . '_fields']['Amount']['FieldValue'],
						'transportId'  		=> 0, //数据库字段不能为空，先置为0
						'actualShipping' 	=> 0, //amazon没有抓取此值
						'marketTime'		=> 0,
						'ShippedTime' 		=> 0,
						'ORtransport'		=> $orderInfo['ShipServiceLevel']['FieldValue'],
						'orderStatus'		=> $ORDER_INIT,
						'orderType' 		=> $ORDER_INIT,
						'orderAttribute'	=> 1, //
						'pmId'				=> 0,
						'channelId' 		=> 0,
						'calcWeight' 		=> 0,
						'calcShipping'		=> 0,
						'orderAddTime'		=> time(),
						'isSendEmail'		=> 0,
						'isNote'			=> 0, //amazon没有此值
						'isCopy'			=> 0,
						'isSplit'			=> 0,
						'combinePackage'	=> 0,
						'combineOrder'		=> 0,
						'completeTime'		=> 0,
						'storeId'			=> 1,
						'is_offline'		=> 0,
						'is_delete'			=> 0,
						'isExpressDelivery' => 0,
				);
				//订单扩展表
				$orderExtension = array(
						'declaredPrice'		=> 0.00,
						'orderStatus'		=> $orderInfo['OrderStatus']['FieldValue'],
						'fulfillmentChannel'=> $orderInfo['FulfillmentChannel']['FieldValue'],
						'salesChannel'		=> $orderInfo['SalesChannel']['FieldValue'],
						'shipServiceLevel'	=> $orderInfo['ShipServiceLevel']['FieldValue'],
						'marketplaceId'		=> $orderInfo['MarketplaceId']['FieldValue'],
						'shipmentServiceLevelCategory' => $orderInfo['ShipmentServiceLevelCategory']['FieldValue']
				);
				
				$ShippingAddress = $orderInfo['ShippingAddress']['FieldValue']['' . "\0" . '*' . "\0" . '_fields'];
				$street2 = htmlentities($ShippingAddress['AddressLine1']['FieldValue']);
				$street3 = htmlentities($ShippingAddress['AddressLine2']['FieldValue']);
				if (!empty($ShippingAddress['County']['FieldValue'])) { //郡，县
					$street2 = htmlentities($ShippingAddress['County']['FieldValue'] . ' ' . $street2, ENT_QUOTES);
					$street3 = htmlentities($ShippingAddress['County']['FieldValue'] . ' ' . $street3, ENT_QUOTES);
				}
				$countrySn 			= $ShippingAddress['CountryCode']['FieldValue'] == 'GB' ? 'UK' : $ShippingAddress['CountryCode']['FieldValue'];
				$countryNameInfo 	= M('Order')->getCountrieInfoBySn($countrySn);
				$countryName 		= $countryNameInfo['regions_en'];
				//用户表
				$orderUserInfo = array(
						'username'			=> htmlentities($ShippingAddress['Name']['FieldValue'], ENT_QUOTES),
						'platformUsername'	=> htmlentities($orderInfo['BuyerName']['FieldValue'], ENT_QUOTES),
						'email'				=> $orderInfo['BuyerEmail']['FieldValue'],
						'countryName'		=> $countryName,
						'countrySn'			=> $countrySn,
						'county'			=> '',
						'currency'			=> $orderInfo['OrderTotal']['FieldValue']['' . "\0" . '*' . "\0" . '_fields']['CurrencyCode']['FieldValue'],
						'state' 			=> htmlentities($ShippingAddress['StateOrRegion']['FieldValue'],ENT_QUOTES),
						'city' 				=> htmlentities($ShippingAddress['City']['FieldValue'],ENT_QUOTES),
						'address1' 			=> htmlentities($ShippingAddress['AddressLine1']['FieldValue']),
						'address2' 			=> $street2,
						'address3' 			=> $street3,
						'phone' 			=> $ShippingAddress['Phone']['FieldValue'], 
						'zipCode' 			=> htmlentities($ShippingAddress['PostalCode']['FieldValue'],ENT_QUOTES),
				);
					
				//订单详细表
				//使用启用缓存方便调试：0：不使用; 1：正常使用缓存; 2：更新缓存中的数据
				if(!empty($GLOBALS['memc_obj']) && C('ENABLE_AMAZON_GET_ORDER_CACHE') != 0){
					$cache_mode  = C('ENABLE_AMAZON_GET_ORDER_CACHE');
					
					$orderDetailObjList = $GLOBALS['memc_obj']->get('ENABLE_AMAZON_GET_ORDER_DETAIL_CACHE');
					
					if(empty($orderDetailObjList) || $cache_mode == 2){
						$orderDetailObjList	= $OrderObject->getOrderDetailLists($orderInfo['AmazonOrderId']['FieldValue']);
						//amazon订单数据存入缓存
						$GLOBALS['memc_obj']->set('ENABLE_AMAZON_GET_ORDER_DETAIL_CACHE', $orderDetailObjList, 10800);
					}
				}else{
					$orderDetailObjList	= $OrderObject->getOrderDetailLists($orderInfo['AmazonOrderId']['FieldValue']);
				}
				
				$orderDetailLists 	= object_array($orderDetailObjList);		
				$orderDetail = array();
				foreach($orderDetailLists as $orderDetailList){
					$orderDetails = $orderDetailList['' . "\0" . '*' . "\0" . '_fields']['OrderItems']['FieldValue'];
					foreach($orderDetails as $orderItems){
						$orderItem = $orderItems['' . "\0" . '*' . "\0" . '_fields'];
						$itemPrices = $orderItem['ItemPrice']['FieldValue']['' . "\0" . '*' . "\0" . '_fields'];
						$orderDetail[] = array(
								'orderDetail'=> array(
										'recordNumber'		=> $orderItem['OrderItemId']['FieldValue'],
										'itemPrice'			=> round($itemPrices['Amount']['FieldValue'] / $orderItem['QuantityOrdered']['FieldValue'], 2),
										'sku'				=> str_pad(preg_replace('/^(0|\*)*/', '', $orderItem['SellerSKU']['FieldValue']), 3, '0', STR_PAD_LEFT),
										'amount'			=> $orderItem['QuantityOrdered']['FieldValue'],
										'shippingFee'		=> 0, //amazon的邮费没有？
										'createdTime'		=> time(),
										'storeId'			=> 1,
										'is_delete'			=> 0,
										'itemId'			=> $orderItem['ASIN']['FieldValue'],
								),
								'orderDetailExtension' => array(
										'itemTitle'			=> htmlentities($orderItem['Title']['FieldValue'], ENT_QUOTES),
										'itemURL'			=> '',
										'shippingTax'		=> $orderItem['ShippingTax']['FieldValue']['' . "\0" . '*' . "\0" . '_fields']['Amount']['FieldValue'],
										'shippingDiscount'	=> $orderItem['ShippingDiscount']['FieldValue']['' . "\0" . '*' . "\0" . '_fields']['Amount']['FieldValue'],
										'shippingPrice'		=> $orderItem['ShippingPrice']['FieldValue']['' . "\0" . '*' . "\0" . '_fields']['Amount']['FieldValue'],
										'conditionNote'		=> empty($orderItem['ConditionNote']['FieldValue'])?'not found':$orderItem['ConditionNote']['FieldValue'],
										'conditionSubtypeId'=> $orderItem['ConditionSubtypeId']['FieldValue'],
										'conditionId'		=> $orderItem['ConditionId']['FieldValue'],
										
								)
						);
		
					}
				}
				
				//组装数据
				$inserOrder[] = array(
						'order' 			=>	$order,
						'orderExtension' 	=> 	$orderExtension,
						'orderUserInfo' 	=>	$orderUserInfo,
						'orderDetail'		=> 	$orderDetail
				);
				
				//拦截逻辑，计算重量运费
				/**待实现**/
			}
			return $inserOrder;
		}
	}
}
?>