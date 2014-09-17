<?php
require_once WEB_PATH."/lib/sdk/ebay/EbayBase.php";
class GetCategories extends EbayBase{
	private	$verb		=	'GetCategories';
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
		$this->xmlHeader =	'<?xml version="1.0" encoding="utf-8"?>
								<GetCategoriesRequest xmlns="urn:ebay:apis:eBLBaseComponents">
									<DetailLevel>ReturnAll</DetailLevel>
									<ErrorLanguage>en_US</ErrorLanguage>
									<RequesterCredentials>
										<eBayAuthToken>'.$this->getToken().'</eBayAuthToken>
									</RequesterCredentials>
									<WarningLevel>High</WarningLevel>';
		$this->xmlFooter	=	"</GetCategoriesRequest>?";
	}
	
	public function doGetCategorySpecifics(){
		$this->xmlBody	=	ia2xml($this->bodyData);
		$xmlRequest		=	$this->xmlHeader.$this->xmlBody.$this->xmlFooter;
		return $this->sendHttpRequest($xmlRequest);
	}

	/****************************************
	 * 获取待传递的xml信息
	 */
	public function getXml(){
		$this->xmlBody	=	ia2xml($this->bodyData);
		return $this->xmlHeader.$this->xmlBody.$this->xmlFooter;
	}

	public function setSiteId($siteId) {
		$this->bodyData['CategorySiteID']	=	$siteId;
	}

	public function setCategoryParent($parentId) {
		$this->bodyData['CategoryParent']	=	$parentId;
	}

	public function setLevelLimit($levelLimit) {
		$this->bodyData['LevelLimit'] = $levelLimit;
	}
}