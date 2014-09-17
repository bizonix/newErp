<?php
/********************************************
 * 获取Category的属性值
 * by winday
 * 2013-5-16
 */
require_once WEB_PATH."/lib/sdk/ebay/EbayBase.php";

class GetCategorySpecificsAPI extends EbayBase{
	private	$verb	=	'GetCategorySpecifics';
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
								<GetCategorySpecificsRequest xmlns="urn:ebay:apis:eBLBaseComponents">
								<ErrorLanguage>en_US</ErrorLanguage>
								<WarningLevel>High</WarningLevel>
									<RequesterCredentials>
									<eBayAuthToken>'.$this->getToken().'</eBayAuthToken>
								</RequesterCredentials>';
		$this->xmlFooter	=	"</GetCategorySpecificsRequest>";
	}


	public function doGetCategorySpecifics(){
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
	
	public function setDetailLevel($DetailLevel){
		$this->bodyData['DetailLevel']		=	$DetailLevel;
	}
}
?>