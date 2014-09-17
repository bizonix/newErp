<?php
/********************************************
 * 获取CategoryFeatures的属性值
 * by winday
 * 2013-5-21
 */
require_once WEB_PATH."/lib/sdk/ebay/EbayBase.php";

class GetCategoryFeaturesAPI extends EbayBase{
	private	$verb	=	'GetCategoryFeatures';
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
								<GetCategoryFeaturesRequest xmlns="urn:ebay:apis:eBLBaseComponents">
								<ErrorLanguage>en_US</ErrorLanguage>
								<WarningLevel>High</WarningLevel>
									<RequesterCredentials>
									<eBayAuthToken>'.$this->getToken().'</eBayAuthToken>
								</RequesterCredentials>';
		$this->xmlFooter	=	"</GetCategoryFeaturesRequest>";
	}


	public function doGetCategoryFeatures(){
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
	
	public function setFeatureID($FeatureID){
		$this->bodyData['FeatureID']		=	$FeatureID;
	}
	
	public function setDetailLevel($DetailLevel){
		$this->bodyData['DetailLevel']		=	$DetailLevel;
	}
}
?>