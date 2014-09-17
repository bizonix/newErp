<?php
require_once WEB_PATH."/lib/sdk/ebay/EbayBase.php";

class GeteBayDetailAPI extends EbayBase{
	private	$verb	=	'GeteBayDetails';
	private $xmlHeader	=	"";
	private $xmlBody	=	"";
	private $xmlFooter	=	"";
	private $xmlRequest	=	"";
	private $xmlQuery	=	"";
	private $bodyData	=	array();
		
	public function __construct($ebay_account){
		parent::setEbayBase($ebay_account, $this->verb);
		$this->setXmlBase();						 
	}
	
	public function setXmlBase(){
		$this->xmlHeader	=	'<?xml version="1.0" encoding="utf-8"?>
								<GeteBayDetailsRequest xmlns="urn:ebay:apis:eBLBaseComponents">
								<ErrorLanguage>en_US</ErrorLanguage>
								<WarningLevel>High</WarningLevel>
								
								<RequesterCredentials>
									<eBayAuthToken>'.$this->getToken().'</eBayAuthToken>
								</RequesterCredentials>';
		$this->xmlFooter	=	"</GeteBayDetailsRequest>";
	}

	//ShippingServiceDetails
	public function setQueryContent($content){
		$this->xmlQuery	=	"<DetailName>$content</DetailName>";
	}

	public function doGet(){
		$xmlRequest	=	$this->xmlHeader.$this->xmlBody.$this->xmlQuery.$this->xmlFooter;
		return $this->sendHttpRequest($xmlRequest);
	}
	
	/****************************************
	 * 获取待传递的xml信息
	 */
	public function getXml(){
		$this->xmlBody	=	ia2xml($this->bodyData);
		return $this->xmlHeader.$this->xmlBody.$this->xmlQuery.$this->xmlFooter;
	}
}
?>