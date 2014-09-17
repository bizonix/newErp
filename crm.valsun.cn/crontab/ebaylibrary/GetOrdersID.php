<?php
	require_once "eBaySession.php";
	class GetOrdersIDAPI{
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
		public function request($start,$end,$pcount){
			$requestXmlBody = '<?xml version="1.0" encoding="utf-8"?>
				<GetOrdersRequest xmlns="urn:ebay:apis:eBLBaseComponents">
				<RequesterCredentials>
					<eBayAuthToken>'.$this->token.'</eBayAuthToken>
				</RequesterCredentials>  
				<DetailLevel>ReturnAll</DetailLevel>
				<OutputSelector>PaginationResult</OutputSelector>
				<OutputSelector>HasMoreOrders</OutputSelector>
				<OutputSelector>ReturnedOrderCountActual</OutputSelector>				
				<OutputSelector>OrderArray.Order.OrderID</OutputSelector>
				<OutputSelector>OrderArray.Order.PaidTime</OutputSelector>
				<OutputSelector>OrderArray.Order.ShippedTime</OutputSelector>
				<OutputSelector>OrderArray.Order.CheckoutStatus</OutputSelector>
				<ModTimeFrom>'.$start.'</ModTimeFrom>
				<ModTimeTo>'.$end.'</ModTimeTo>
				<Pagination>
					<EntriesPerPage>100</EntriesPerPage>
					<PageNumber>'.$pcount.'</PageNumber>
				</Pagination>
				<IncludeFinalValueFee>true</IncludeFinalValueFee>
				<OrderRole>Seller</OrderRole>
				<OrderStatus>All</OrderStatus>
			</GetOrdersRequest>';
			$responseXml = $this->handle->sendHttpRequest($requestXmlBody);
			return $responseXml;
		}
	}
?>