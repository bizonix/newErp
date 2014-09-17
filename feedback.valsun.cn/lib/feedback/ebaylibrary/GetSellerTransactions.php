<?php
	require_once "eBaySession.php";
	class GetSellerTransactionsAPI{
		private $handle;
		
		private $token;
		private $devID;
		private $appID;
		private $certID;
		private $compatabilityLevel;
		private $siteID;
		private $verb='GetSellerTransactions';
		
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
				<GetSellerTransactionsRequest xmlns="urn:ebay:apis:eBLBaseComponents">
				<RequesterCredentials>
					<eBayAuthToken>'.$this->token.'</eBayAuthToken>
				</RequesterCredentials>  
				<DetailLevel>ReturnAll</DetailLevel>
				<OutputSelector>TransactionArray.Transaction.Variation.SKU</OutputSelector>
				<OutputSelector>TransactionArray.Transaction.Variation.VariationSpecifics</OutputSelector>
				<OutputSelector>PaginationResult</OutputSelector>		  
				<OutputSelector>TransactionArray.Transaction.AmountPaid</OutputSelector>
				<OutputSelector>TransactionArray.Transaction.Status.LastTimeModified</OutputSelector>
				<OutputSelector>TransactionArray.Transaction.Status.eBayPaymentStatus</OutputSelector>
				<OutputSelector>TransactionArray.Transaction.Status.CompleteStatus</OutputSelector>
				<OutputSelector>TransactionArray.Transaction.PayPalEmailAddress</OutputSelector>			<OutputSelector>TransactionArray.Transaction.ExternalTransaction.ExternalTransactionID</OutputSelector>
				<OutputSelector>TransactionArray.Transaction.ExternalTransaction.FeeOrCreditAmount</OutputSelector>
				<OutputSelector>TransactionArray.Transaction.ShippingDetails.SellingManagerSalesRecordNumber</OutputSelector>
				<OutputSelector>TransactionArray.Transaction.ShippingDetails.ShippingType</OutputSelector> 
				<OutputSelector>TransactionArray.Transaction.TransactionID</OutputSelector> 
				<OutputSelector>TransactionArray.Transaction.ContainingOrder.OrderID</OutputSelector>
				<OutputSelector>TransactionArray.Transaction.ContainingOrder.ShippingDetails.SellingManagerSalesRecordNumber</OutputSelector> 
				<OutputSelector>TransactionArray.Transaction.AmountPaid</OutputSelector>
				<OutputSelector>TransactionArray.Transaction.FinalValueFee</OutputSelector>
				<OutputSelector>TransactionArray.Transaction.Buyer.BuyerInfo</OutputSelector> 
				<OutputSelector>TransactionArray.Transaction.Buyer.UserID</OutputSelector> 
				<OutputSelector>TransactionArray.Transaction.Buyer.Email</OutputSelector> 
				<OutputSelector>TransactionArray.Transaction.BuyerCheckoutMessage</OutputSelector> 
				<OutputSelector>TransactionArray.Transaction.ShippingServiceSelected.ShippingService</OutputSelector>
				<OutputSelector>TransactionArray.Transaction.ShippingServiceSelected.ShippingServiceCost</OutputSelector>
				<OutputSelector>TransactionArray.Transaction.ShippedTime</OutputSelector>
				<OutputSelector>TransactionArray.Transaction.CreatedDate</OutputSelector>
				<OutputSelector>TransactionArray.Transaction.PaidTime</OutputSelector>
				<OutputSelector>TransactionArray.Transaction.QuantityPurchased</OutputSelector>
				<OutputSelector>TransactionArray.Transaction.SellingStatus.CurrentPrice</OutputSelector>
				<OutputSelector>TransactionArray.Transaction.ExternalTransaction.ExternalTransactionID</OutputSelector>
				<OutputSelector>TransactionArray.Transaction.Item.Currency</OutputSelector>
				<OutputSelector>TransactionArray.Transaction.Item.ItemID</OutputSelector>
				<OutputSelector>TransactionArray.Transaction.Item.Title</OutputSelector>
				<OutputSelector>TransactionArray.Transaction.Item.Site</OutputSelector>
				<OutputSelector>TransactionArray.Transaction.Item.SKU</OutputSelector>
				<OutputSelector>TransactionArray.Transaction.Item.ListingType</OutputSelector>
				<OutputSelector>TransactionArray.Transaction.Item.SellingStatus.CurrentPrice.currencyID</OutputSelector>
				<OutputSelector>HasMoreTransactions</OutputSelector>
				<OutputSelector>ReturnedTransactionCountActual</OutputSelector>
				<ModTimeFrom>'.$start.'</ModTimeFrom>
				<ModTimeTo>'.$end.'</ModTimeTo>
				<Pagination>
					<EntriesPerPage>50</EntriesPerPage>
					<PageNumber>'.$pcount.'</PageNumber>
				</Pagination>
				<IncludeFinalValueFee>true</IncludeFinalValueFee>
				<IncludeContainingOrder>true</IncludeContainingOrder>
			</GetSellerTransactionsRequest>';
    	
			$responseXml = $this->handle->sendHttpRequest($requestXmlBody);
			return $responseXml;
		}
	}
?>