<?php
/********************************************
 * 获取分类的商品详情的模板信息
 * by winday
 * 2013-5-22
 */
require_once WEB_PATH."/lib/sdk/ebay/EbayBase.php";

class GetDescriptionTemplatesAPI extends EbayBase{
	private	$verb	=	'GetDescriptionTemplates';
	private $xmlHeader	=	"";
	private $xmlBody	=	"";
	private $xmlFooter	=	"";
	private $xmlRequest	=	"";
	private $bodyData	=	array();
		
	public function __construct($ebay_account){
		parent::setEbayBase($ebay_account, $this->verb);
		$this->setXmlBase();						 
	}
	
	public function setXmlBase(){
		$this->xmlHeader	=	'<?xml version="1.0" encoding="utf-8"?>
								<GetDescriptionTemplatesRequest xmlns="urn:ebay:apis:eBLBaseComponents">
								<ErrorLanguage>en_US</ErrorLanguage>
								<WarningLevel>High</WarningLevel>
									<RequesterCredentials>
									<eBayAuthToken>'.$this->getToken().'</eBayAuthToken>
								</RequesterCredentials>';
		$this->xmlFooter	=	"</GetDescriptionTemplatesRequest>";
	}


	public function doGetDescriptionTemplates(){
		$this->xmlBody	=	ia2xml($this->bodyData);
		$xmlRequest	=	$this->xmlHeader.$this->xmlBody.$this->xmlFooter;
		return $this->sendHttpRequest($xmlRequest);
	}
	
	/****************************************
	 * 获取待传递的xml信息
	 */
	public function getXml(){
		$this->xmlBody	=	ia2xml($this->bodyData);
		return $this->xmlHeader.$this->xmlBody.$this->xmlFooter;
	}
	
	public function setCategoryID($CategoryID){
		$this->bodyData['CategoryID']		=	$CategoryID;
	}

}
?>