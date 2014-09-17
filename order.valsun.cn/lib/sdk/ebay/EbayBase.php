<?php
class EbayBase
{
	private $requestToken;
	private $devID;
	private $appID;
	private $certID;
	private $serverUrl;
	private $compatLevel;
	private $siteID;
	private $verb;
	
	public function setEbayBase($ebay_account, $verb){
		$this->requestToken	=	$ebay_account['userToken'];
		$this->devID	=	$ebay_account['devID'];
		$this->appID	=	$ebay_account['appID'];
		$this->certID	=	$ebay_account['certID'];
		$this->serverUrl=	$ebay_account['serverUrl'];
		$this->siteID	=	$ebay_account['siteID'];
		$this->compatLevel	=	$ebay_account['compatabilityLevel'];
		$this->verb		=	$verb;
	}

	public function getToken(){
		return $this->requestToken;
	}
	
	/**	sendHttpRequest
		Sends a HTTP request to the server for this session
		Input:	$requestBody
		Output:	The HTTP Response as a String
	*/
	public function sendHttpRequest($requestBody)
	{
		//build eBay headers using variables passed via constructor
		
		$headers = $this->buildEbayHeaders();
		//Log::write("\r\ntimeX:".time()."\r\n");
		//initialise a CURL session
		$connection = curl_init();
		//set the server we are using (could be Sandbox or Production server)
		
		//curl_setopt($connection, CURLOPT_CONNECTTIMEOUT, 60);	//设置超时时间
		curl_setopt($connection, CURLOPT_URL, $this->serverUrl);
		curl_setopt($connection, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
		curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($connection, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($connection, CURLOPT_POST, 1);
		curl_setopt($connection, CURLOPT_POSTFIELDS, $requestBody);
		curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);

		$response = curl_exec($connection);
		$curl_info	=	curl_getinfo($connection);
		$x	=	var_export($curl_info, true);
		//Log::write("\r\ncurl:".$x."\r\n");
		//close the connection
		curl_close($connection);
		//Log::write("\r\ntimeY:".time()."\r\n");
		//return the response
		//print_r($response);
		return $response;
	}
	
	
	
	
	
	/**	buildEbayHeaders
		Generates an array of string to be used as the headers for the HTTP request to eBay
		Output:	String Array of Headers applicable for this call
	*/
	public function buildEbayHeaders()
	{
		$headers = array (
			//Regulates versioning of the XML interface for the API
			'X-EBAY-API-COMPATIBILITY-LEVEL: ' . $this->compatLevel,
			
			//set the keys
			'X-EBAY-API-DEV-NAME: ' . $this->devID,
			'X-EBAY-API-APP-NAME: ' . $this->appID,
			'X-EBAY-API-CERT-NAME: ' . $this->certID,
			
			//the name of the call we are requesting
			'X-EBAY-API-CALL-NAME: ' . $this->verb,			
			
			//SiteID must also be set in the Request's XML
			//SiteID = 0  (US) - UK = 3, Canada = 2, Australia = 15, ....
			//SiteID Indicates the eBay site to associate the call with
			'X-EBAY-API-SITEID: ' . $this->siteID,
		);
		
		return $headers;
	}
}
?>