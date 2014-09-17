<?php
	require_once "eBaySession.php";
	
	class GetMemberMessagesAPI{
		private $handle;
		private $token;
		private $devID;
		private $appID;
		private $certID;
		private $compatabilityLevel='765';
		private $siteID;
		private $verb='GetMyMessages';
		
		public function __construct($ebay_account){
			
			global 	$userToken,$devID,$appID,$certID,$siteID,$serverUrl,$compatabilityLevel;
			
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
		public function GetToken(){
			
			$userToken=$this->token;
			return $userToken;
		}
		public function request($start,$end,$pcount,$ebay_account){
			$requestXmlBody = '<?xml version="1.0" encoding="utf-8"?>
							<GetMyMessagesRequest xmlns="urn:ebay:apis:eBLBaseComponents">
							<RequesterCredentials>
							<eBayAuthToken>'.$this->token.'</eBayAuthToken>
							</RequesterCredentials>  
							<StartTime>'.$start.'</StartTime>	
							<EndTime>'.$end.'</EndTime>
							<DetailLevel>ReturnHeaders</DetailLevel>
							<Pagination> 
							<EntriesPerPage>199</EntriesPerPage> 
							<PageNumber>'.$pcount.'</PageNumber> 
							</Pagination> 
							</GetMyMessagesRequest>';
			$responseXml = $this->handle->sendHttpRequest($requestXmlBody);
			return $responseXml;
		}
		public function requestMessagesID($MessageID){    
					$requestXmlBody ='<?xml version="1.0" encoding="utf-8"?> 
					<GetMyMessagesRequest xmlns="urn:ebay:apis:eBLBaseComponents"> 
			  		<DetailLevel>ReturnMessages</DetailLevel>
			  		<RequesterCredentials> 
			   		<eBayAuthToken>'.$this->token.'</eBayAuthToken>
			  		</RequesterCredentials> 
			  		<MessageIDs><MessageID>'.$MessageID.'</MessageID> 
			  		</MessageIDs> 
					</GetMyMessagesRequest>';
			$responseXml = $this->handle->sendHttpRequest($requestXmlBody);
			return $responseXml;
		}
	}
	
?>
