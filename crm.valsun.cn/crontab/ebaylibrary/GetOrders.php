<?php
	require_once "eBaySession.php";
	class GetOrdersAPI{
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
				<OutputSelector>OrderArray.Order.BuyerUserID</OutputSelector> 
				<OutputSelector>OrderArray.Order.BuyerCheckoutMessage</OutputSelector> 
				<OutputSelector>OrderArray.Order.PaidTime</OutputSelector>
				<OutputSelector>OrderArray.Order.ShippedTime</OutputSelector>
				<OutputSelector>OrderArray.Order.OrderStatus</OutputSelector>
				<OutputSelector>OrderArray.Order.AmountPaid</OutputSelector>
				<OutputSelector>OrderArray.Order.CheckoutStatus</OutputSelector>
				<OutputSelector>OrderArray.Order.ShippingDetails.SellingManagerSalesRecordNumber</OutputSelector>
				<OutputSelector>OrderArray.Order.CreatingUserRole</OutputSelector>			
				<OutputSelector>OrderArray.Order.CreatedTime</OutputSelector>			
				<OutputSelector>OrderArray.Order.PaymentMethods</OutputSelector>			
				<OutputSelector>OrderArray.Order.ShippingAddress</OutputSelector>			
				<OutputSelector>OrderArray.Order.ShippingServiceSelected.ShippingService</OutputSelector>			
				<OutputSelector>OrderArray.Order.ShippingServiceSelected.ShippingServiceCost</OutputSelector>			
				<OutputSelector>OrderArray.Order.Subtotal</OutputSelector>
				<OutputSelector>OrderArray.Order.Total</OutputSelector>
				<OutputSelector>OrderArray.Order.ExternalTransaction.ExternalTransactionID</OutputSelector>
				<OutputSelector>OrderArray.Order.ExternalTransaction.FeeOrCreditAmount</OutputSelector>
				<OutputSelector>OrderArray.Order.ExternalTransaction.PaymentOrRefundAmount</OutputSelector>
				<OutputSelector>OrderArray.Order.TransactionArray.Transaction.Buyer</OutputSelector>
				<OutputSelector>OrderArray.Order.TransactionArray.Transaction.ShippingDetails.SellingManagerSalesRecordNumber</OutputSelector>
				<OutputSelector>OrderArray.Order.TransactionArray.Transaction.CreatedDate</OutputSelector>
				<OutputSelector>OrderArray.Order.TransactionArray.Transaction.Item.ItemID</OutputSelector>
				<OutputSelector>OrderArray.Order.TransactionArray.Transaction.Item.Title</OutputSelector>
				<OutputSelector>OrderArray.Order.TransactionArray.Transaction.Item.Site</OutputSelector>
				<OutputSelector>OrderArray.Order.TransactionArray.Transaction.Item.SKU</OutputSelector>
				<OutputSelector>OrderArray.Order.TransactionArray.Transaction.QuantityPurchased</OutputSelector>
				<OutputSelector>OrderArray.Order.TransactionArray.Transaction.TransactionID</OutputSelector>
				<OutputSelector>OrderArray.Order.TransactionArray.Transaction.TransactionPrice</OutputSelector>
				<OutputSelector>OrderArray.Order.TransactionArray.Transaction.FinalValueFee</OutputSelector>
				<OutputSelector>OrderArray.Order.TransactionArray.Transaction.TransactionSiteID</OutputSelector>
				<OutputSelector>OrderArray.Order.TransactionArray.Transaction.OrderLineItemID</OutputSelector>
				<OutputSelector>OrderArray.Order.TransactionArray.Transaction.Variation.SKU</OutputSelector>
				<OutputSelector>OrderArray.Order.TransactionArray.Transaction.Variation.VariationSpecifics</OutputSelector>
				<OutputSelector>OrderArray.Order.TransactionArray.Transaction.Variation.VariationTitle</OutputSelector>
				<ModTimeFrom>'.$start.'</ModTimeFrom>
				<ModTimeTo>'.$end.'</ModTimeTo>
				<Pagination>
					<EntriesPerPage>100</EntriesPerPage>
					<PageNumber>'.$pcount.'</PageNumber>
				</Pagination>
				<IncludeFinalValueFee>true</IncludeFinalValueFee>
				<OrderRole>Seller</OrderRole>
				<OrderStatus>Completed</OrderStatus>
			</GetOrdersRequest>';
			$responseXml = $this->handle->sendHttpRequest($requestXmlBody);
			return $responseXml;
		}
	}
?>