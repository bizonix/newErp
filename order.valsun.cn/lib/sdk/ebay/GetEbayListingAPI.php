<?php
	require_once "eBaySession.php";
	class GetEbayListingAPI{
		private	$verb='GetSellerList';
		private $token;
		private $devID;
		private $appID;
		private $certID;
		private $serverUrl;
		private $siteID;
		private $compatabilityLevel;
		
		private $handle;
		
		public function __construct($ebay_account){
			global 	$userToken,$devID,$appID,$certID,$siteID,$serverUrl,$compatabilityLevel;
			
			$this->token=$userToken;
			$this->devID=$devID;
			$this->appID=$appID;
			$this->certID=$certID;
			$this->serverUrl=$serverUrl;
			$this->siteID=$siteID;
			$this->compatabilityLevel=$compatabilityLevel;
			
			$this->handle=new eBaySession($this->token, $this->devID, $this->appID, $this->certID,
										  $this->serverUrl, $this->compatabilityLevel, $this->siteID, $this->verb);
		}
		public function GetToken(){
			$userToken = $this->token;
			return $userToken;
		}
		public function request($page,$startTimeFrom,$startTimeTo){
			 $requestXmlBody = '<?xml version="1.0" encoding="utf-8"?>
									<GetSellerListRequest xmlns="urn:ebay:apis:eBLBaseComponents">
									  <RequesterCredentials>
										<eBayAuthToken>'.$this->token.'</eBayAuthToken>
									  </RequesterCredentials>
									  <StartTimeFrom>'.$startTimeFrom.'</StartTimeFrom> 
									  <StartTimeTo>'.$startTimeTo.'</StartTimeTo> 
									  <ErrorLanguage>en_US</ErrorLanguage>
									  <WarningLevel>High</WarningLevel>
									  <DetailLevel>ReturnAll</DetailLevel>
									  <OutputSelector>PaginationResult</OutputSelector>
									  <OutputSelector>ItemArray.Item.ItemID</OutputSelector>
									  <OutputSelector>HasMoreItems</OutputSelector>
									  <OutputSelector>ItemArray.Item.ListingType</OutputSelector>
									  <OutputSelector>ItemArray.Item.SKU</OutputSelector>
									  <OutputSelector>ItemArray.Item.StartPrice</OutputSelector>
									  <OutputSelector>ItemArray.Item.Title</OutputSelector>
									  <OutputSelector>ItemArray.Item.Site</OutputSelector>
									  <OutputSelector>ItemArray.Item.Quantity</OutputSelector>
									  <OutputSelector>ItemArray.Item.Variations</OutputSelector>
									  <OutputSelector>ItemArray.Item.ListingDetails.ViewItemURL</OutputSelector>
									  <OutputSelector>ItemArray.Item.SellingStatus.QuantitySold</OutputSelector>
									  <OutputSelector>ItemArray.Item.SellingStatus.ListingStatus</OutputSelector>
									  <OutputSelector>ItemArray.Item.ShippingDetails</OutputSelector>
									  <Pagination>
										<EntriesPerPage>200</EntriesPerPage>
										<PageNumber>'.$page.'</PageNumber>
									  </Pagination>
									</GetSellerListRequest>';
			return $this->handle->sendHttpRequest($requestXmlBody);
		}
	}
?>