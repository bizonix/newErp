<?php
/*
*类名：EndItemAPI
*功能：下架 item
*开发人：王长先
*开发时间：2013-8-6
*/
require_once WEB_PATH."/lib/sdk/ebay/EbayBase.php";

class EndItemAPI extends EbayBase{
	private	$verb	=	'EndItem';//告诉eBay我们要做的动作是：修改多属性的item
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
								<EndItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">
								<ErrorLanguage>en_US</ErrorLanguage>
								<WarningLevel>High</WarningLevel>
								<RequesterCredentials>
								<eBayAuthToken>'.$this->getToken().'</eBayAuthToken>
								</RequesterCredentials>';
		$this->xmlFooter	=	"</EndItemRequest>";
	}


	public function doEndItem(){
	 	$xmlRequest	=	$this->xmlHeader.$this->xmlBody.$this->xmlFooter;
		return $this->sendHttpRequest($xmlRequest);
	}
	
	/****************************************
	 * 获取待传递的xml信息 (将bodyData转换成XML格式)
	 */
	public function getXml(){
		$this->xmlBody	=	ia2xml($this->bodyData);
		return $this->xmlHeader.$this->xmlBody.$this->xmlFooter;
	}
	
	//、必须：下架理由(CustomCode,Incorrect,LostOrBroken,NotAvailable,OtherListingError,SellToHighBidder,Sold)
	public function setEndingReason($ApplicationData){
		$this->bodyData['EndingReason']		=	$ApplicationData;
	}
	
	//、必须：设置物品ID
	public function setItemID($ApplicationData){
		$this->bodyData['ItemID']		=	$ApplicationData;
	}
	
	//、只能用于 Half.com
	public function setSellerInventoryID($ApplicationData){
		$this->bodyData['SellerInventoryID']		=	$ApplicationData;
	}
}
?>