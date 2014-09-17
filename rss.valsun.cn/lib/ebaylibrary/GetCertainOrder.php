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
			require 'keys/keys_'.$ebay_account.'.php';
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
	}
?>