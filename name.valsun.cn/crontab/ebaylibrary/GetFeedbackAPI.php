<?php
	require_once "eBaySession.php";
	class GetFeedbackAPI{
		private	$verb='GetFeedback';
		
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
		public function request($page,$perPageCount){
			 $requestXmlBody = '<?xml version="1.0" encoding="utf-8"?> 
								<GetFeedbackRequest xmlns="urn:ebay:apis:eBLBaseComponents"> 
									<RequesterCredentials> 
										<eBayAuthToken>'.$this->token.'</eBayAuthToken> 
									</RequesterCredentials>
									<Pagination>		 
										<EntriesPerPage>'.$perPageCount.'</EntriesPerPage>
										<PageNumber>'.$page.'</PageNumber>
									</Pagination>
									<FeedbackType>FeedbackReceived</FeedbackType>
									<DetailLevel>ReturnAll</DetailLevel>		 
								</GetFeedbackRequest>';
			return $this->handle->sendHttpRequest($requestXmlBody);
		}
		public function request_change($itemid,$transid,$userid)
		{
			$requestXmlBody = '<?xml version="1.0" encoding="utf-8"?> 
								<GetFeedbackRequest xmlns="urn:ebay:apis:eBLBaseComponents"> 
									<RequesterCredentials> 
										<eBayAuthToken>'.$this->token.'</eBayAuthToken> 
									</RequesterCredentials>
									<ItemID>'.$itemid.'</ItemID>
									<TransactionID>'.$transid.'</TransactionID>
									<UserID>'.$userid.'</UserID>
									<FeedbackType>FeedbackReceived</FeedbackType>
									<DetailLevel>ReturnAll</DetailLevel>
								</GetFeedbackRequest>';	
			return $this->handle->sendHttpRequest($requestXmlBody);
		}
	}
?>