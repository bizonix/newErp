<?php
require_once WEB_PATH."/lib/sdk/ebay/EbayBase.php";

class GetItemAPI extends EbayBase{
	private	$verb	=	'GetItem';
	private $xmlHeader	=	"";
	private $xmlBody	=	"";
	private $xmlFooter	=	"";
	private $xmlRequest	=	"";
	private $bodyData	=	array();
		
	public function __construct($ebay_account){//要提供相应的eBay账户
		parent::setEbayBase($ebay_account, $this->verb);//
		$this->setXmlBase();						 
	}
	
	public function setXmlBase(){
		$this->xmlHeader	=	'<?xml version="1.0" encoding="utf-8"?>
								<GetItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">
								<ErrorLanguage>en_US</ErrorLanguage>
								<WarningLevel>High</WarningLevel>
								<RequesterCredentials>
									<eBayAuthToken>'.$this->getToken().'</eBayAuthToken>
								</RequesterCredentials>';
		$this->xmlFooter	=	"</GetItemRequest>";
	}

	
	public function doGetItem(){
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
	public function setItemID($ItemID){
		$this->bodyData['ItemID']		=	$ItemID;
	}
	
	public function setIncludeItemSpecifics($IncludeItemSpecifics){
		$this->bodyData['IncludeItemSpecifics']		=	$IncludeItemSpecifics;
	}
	
	//ReturnAll
	public function setDetailLevel($DetailLevel){
		$this->bodyData['DetailLevel']		=	$DetailLevel;
	}
	
	/*****************************************
	 * 多个filter逗号连接
	 */
	public function setReturnFilter($filter){
		$this->bodyData['OutputSelector']		=	$filter;
	}
	
}
?>