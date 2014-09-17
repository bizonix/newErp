<?php
	/********************************************************************************
	  * AUTHOR: Michael Hawthornthwaite - Acid Computer Services (www.acidcs.co.uk) *
	  *******************************************************************************/

class eBaySession
{
	private $requestToken;
	private $serverUrl;
	
	/**	__construct
		Constructor to make a new instance of eBaySession with the details needed to make a call
		Input:	$userRequestToken - the authentication token fir the user making the call
				$developerID - Developer key obtained when registered at http://developer.ebay.com
				$applicationID - Application key obtained when registered at http://developer.ebay.com
				$certificateID - Certificate key obtained when registered at http://developer.ebay.com
				$useTestServer - Boolean, if true then Sandbox server is used, otherwise production server is used
				$compatabilityLevel - API version this is compatable with
				$siteToUseID - the Id of the eBay site to associate the call iwht (0 = US, 2 = Canada, 3 = UK, ...)
				$callName  - The name of the call being made (e.g. 'GeteBayOfficialTime')
		Output:	Response string returned by the server
	*/
	public function __construct($userRequestToken, $serverUrl)
	{
		$this->requestToken = $userRequestToken;
        $this->serverUrl = $serverUrl;	
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
		
		//initialise a CURL session
		$connection = curl_init();
		//set the server we are using (could be Sandbox or Production server)
		curl_setopt($connection, CURLOPT_URL, $this->serverUrl);
		
		//stop CURL from verifying the peer's certificate
		curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);
		
		//set the headers using the array of headers
		curl_setopt($connection, CURLOPT_HTTPHEADER, $headers);
		
		//set method as POST
		curl_setopt($connection, CURLOPT_POST, 1);
		
		//set the XML body of the request
		curl_setopt($connection, CURLOPT_POSTFIELDS, $requestBody);
		
		//set it to return the transfer as a string from curl_exec
		curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
		
		//Send the Request
		$response = curl_exec($connection);
		
		//close the connection
		curl_close($connection);
		
		//return the response
		return $response;
	}
	
	
	
	/**	buildEbayHeaders
		Generates an array of string to be used as the headers for the HTTP request to eBay
		Output:	String Array of Headers applicable for this call
	*/
	private function buildEbayHeaders()
	{
		$headers = array (
			'X-EBAY-SOA-SERVICE-NAME: ResolutionCaseManagementService',
			'X-EBAY-SOA-OPERATION-NAME: getUserCases',
			'X-EBAY-SOA-SERVICE-VERSION: 1.1.0',
			'X-EBAY-SOA-GLOBAL-ID: EBAY-US',
			'X-EBAY-SOA-SECURITY-TOKEN:AgAAAA**AQAAAA**aAAAAA**VIDNTw**nY+sHZ2PrBmdj6wVnY+sEZ2PrA2dj6AEkICjD5OKpQ6dj6x9nY+seQ**ebsAAA**AAMAAA**JVYUPWC6Cpx6J8imP1TWN43csOInFPKSTcHtGC6nYi1d4lCDBNKWKiuGBrMGE93yLpoi9Qkb20QHY3k+iFyxqT/jy9Wr7O7sntssH4g56A7wLQyasG7ECZk/n4z/aHaZQ/U1NLvQ7ml7cloJlo1Q7fxTK/PjZeXnfA55v1nNl84JHxeRvlSEfYo/YQ9yCDuiGfFZmaoP8jKiapo1tHMUPGWLTK8hKTrjQF+kxTZoa3T/UnsWyXsgzv4vEh7U1L74+L3EI8NpXffQCscbwvdwSm28PCt/eibYiEjl3zvD2Eu186SEEFWOV8By5BYXE0sMa4JfRahlPq3/mADlvt+uca2TD1+A3GY0Wp69DmOpL913dHymFmibvcaJC1ksBlnrkWq6KkYcToj7+nQAIMss4iEm9szkTH9889W7wv6aicwx4lxGbGLcqiQzlXDU9YHrJboAJTk6jltcst238DV56/4SvQk98/OHCQ/Nk8QaQ9n/57Y8Sc10j+k9Y5rdBN6EDSjc+IcjNjpVzYehwLZEThuD1gbRgWpaaDmhwDKSG+j+FtF4HqDP/PFsX0g1tdKbw2g41fPkfqkLWoIpnLJfWLFnxtNXkYr4M5I4mGrwICAfus40phSydt03h46fIu4TxUgowSTtTxpzVXM622t080GWBE9fjV1FZuDDxBTcSRyAX+KXfRWZpeoW7Nidc5gyUS7DDDlSAzlNLiqxBpSIwJwUHoW4r0yHmIX88h0INi7o0H5FyliR2PLI4LX+3m8A',
			'X-EBAY-SOA-REQUEST-DATA-FORMAT: XML',
			'X-EBAY-SOA-MESSAGE-PROTOCOL: SOAP12',
		);	
		return $headers;
	}
}
?>