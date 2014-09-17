<?php
require_once WEB_PATH."/lib/sdk/ebay/EbayBase.php";

class AddItemAPI extends EbayBase{
	private	$verb	=	'AddItem';
	private $xmlHeader	=	"";
	private $xmlBody	=	"";
	private $xmlFooter	=	"";
	private $xmlRequest	=	"";
	private $bodyData	=	array();
		
	public function setBase($ebay_account){
		parent::setEbayBase($ebay_account, $this->verb);
		$this->setXmlBase();						 
	}
	
	public function setXmlBase(){
		$this->xmlHeader	=	'<?xml version="1.0" encoding="utf-8"?>
								<'.$this->verb.'Request xmlns="urn:ebay:apis:eBLBaseComponents">
								<ErrorLanguage>en_US</ErrorLanguage>
								<WarningLevel>High</WarningLevel>
								<RequesterCredentials>
									<eBayAuthToken>'.$this->getToken().'</eBayAuthToken>
								</RequesterCredentials>';
		$this->xmlFooter	=	"</".$this->verb."Request>";
	}

	public function setVerb($verb){
		if(!in_array($verb, array("AddItem","VerifyAddItem","AddFixedPriceItem"))){
			return false;
		}
		//echo $verb."<br>";
		
		$this->verb	=	$verb;
	}

	/*
	*功能：刊登产品
	*说明：最终提交给eBay的数据必须是XML格式，我们先要把数组格式转换为：XML格式
	*/
	public function doAddItem(){
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

	/****************************************
	 * 设置“物品标题”
	 */
	public function setTitle($title){
		$this->bodyData['Item']['Title']		=	$title;
	}
	
	public function setSku($sku){
		$this->bodyData['Item']['SKU']	=	$sku;
	}
	
	/****************************************
	 * 设置具体描述
	 */
	public function setDescription($Description){
		//$this->bodyData['Item']['Description']		=	htmlspecialchars($Description);
		$this->bodyData['Item']['Description']		=	$Description;
		//Log::write('AddItem');
	}
	
	/****************************************
	 * 设置产品分类
	 */
	//int
	public function setCategoryID($CategoryID){
		$this->bodyData['Item']['PrimaryCategory']['CategoryID']		=	$CategoryID;
	}
	
	public function setStoreCategoryID($StoreCategoryID){
		$this->bodyData['Item']['Storefront']['StoreCategoryID']		=	$StoreCategoryID;
	}

	//double
	public function setStartPrice($StartPrice){
		//
		$this->bodyData['Item']['StartPrice']		=	$StartPrice;
	}

	//int
	public function setConditionID($ConditionID){
		$this->bodyData['Item']['ConditionID']		=	$ConditionID;
	}

	//boolean
	public function setCategoryMappingAllowed($CategoryMappingAllowed){
		$this->bodyData['Item']['CategoryMappingAllowed']		=	$CategoryMappingAllowed;
	}

	//string 两位的国家简称 US  UK  AU
	public function setCountry($Country){
		$this->bodyData['Item']['Country']		=	$Country;
	}

	//string 对应的站点货币简码 USD  RMB
	public function setCurrency($Currency){
		$this->bodyData['Item']['Currency']		=	$Currency;
	}

	//int	待发货最长的时间
	public function setDispatchTimeMax($DispatchTimeMax){
		$this->bodyData['Item']['DispatchTimeMax']		=	$DispatchTimeMax;
	}

	//string	listing的刊登时间  Days_7  GTC
	public function setListingDuration($ListingDuration){
		$this->bodyData['Item']['ListingDuration']		=	$ListingDuration;
	}

	//http://developer.ebay.com/DevZone/XML/docs/Reference/eBay/types/ListingTypeCodeType.html
	//单个 Chinese(单属性)  AdType   FixedPriceItem 多属性   Half
	public function setListingType($ListingType){	//可选
		$this->bodyData['Item']['ListingType']		=	$ListingType;
	}

	//付款方式
	public function setPaymentMethods($PaymentMethods){
		$this->bodyData['Item']['PaymentMethods'][]		=	$PaymentMethods;
	}
	
	//付款说明ShippingDetails.PromotionalShippingDiscount
	public function setPaymentInstructions($PaymentInstructions){
		$this->bodyData['Item']['ShippingDetails']['PaymentInstructions']		=	$PaymentInstructions;
	}

	//收款的账号
	public function setPayPalEmailAddress($PayPalEmailAddress){
		$this->bodyData['Item']['PayPalEmailAddress']		=	$PayPalEmailAddress;
	}

	//可支持多张图片， 默认第一张为橱窗主图
	public function setPictureURL($PictureURL){
		$this->bodyData['Item']['PictureDetails']['PictureURL'][]		=	$PictureURL;
	}
	
	public function setExternalPictureURL($ExternalPictureURL){
		$this->bodyData['Item']['PictureDetails']['ExternalPictureURL'][]		=	$ExternalPictureURL;
	}
	
	public function setGalleryType($GalleryType){
		$this->bodyData['Item']['PictureDetails']['GalleryType']	=	$GalleryType;
	}
	
	public function setGalleryURL($GalleryURL){
		$this->bodyData['Item']['PictureDetails']['GalleryURL']	=	$GalleryURL;
	}

	//物品所在邮局的post code
	public function setPostalCode($PostalCode){
		$this->bodyData['Item']['PostalCode']		=	$PostalCode;
	}

	//Location
	public function setLocation($Location){
		$this->bodyData['Item']['Location']		=	$Location;
	}

	//int 设置库存数量
	public function setQuantity($Quantity){
		$this->bodyData['Item']['Quantity']		=	$Quantity;
	}

	//退换货政策-------------
	public function setReturnsAcceptedOption($ReturnsAcceptedOption){
		$this->bodyData['Item']['ReturnPolicy']['ReturnsAcceptedOption']		=	$ReturnsAcceptedOption;
	}

	public function setRefundOption($RefundOption){
		$this->bodyData['Item']['ReturnPolicy']['RefundOption']		=	$RefundOption;
	}

	public function setReturnsWithinOption($ReturnsWithinOption){
		$this->bodyData['Item']['ReturnPolicy']['ReturnsWithinOption']		=	$ReturnsWithinOption;
	}

	public function setReturnPolicyDescription($Description){
		$this->bodyData['Item']['ReturnPolicy']['Description']		=	$Description;
	}

	public function setShippingCostPaidByOption($ShippingCostPaidByOption){
		$this->bodyData['Item']['ReturnPolicy']['ShippingCostPaidByOption']		=	$ShippingCostPaidByOption;
	}
	//--------------end 退换货


	
	//运输方式
	public function setShippingType($ShippingType){
		$this->bodyData['Item']['ShippingDetails']['ShippingType']		=	$ShippingType;
	}
	
	//
	public function setShippingInternationalRateTable($InternationalRateTable="Default"){
		$this->bodyData['Item']['ShippingDetails']['RateTableDetails']['InternationalRateTable']=	$InternationalRateTable;
	}
	
	//不运送国家设置
	public function setExcludeShipToLocation($ExcludeShipToLocation){
		$this->bodyData['Item']['ShippingDetails']['ExcludeShipToLocation'][]		=	$ExcludeShipToLocation;
	}

	public function setShippingServicePriority($ShippingServicePriority){
		$this->bodyData['Item']['ShippingDetails']['ShippingServiceOptions']['ShippingServicePriority']		=	$ShippingServicePriority;
	}

	public function setShippingService($ShippingService){
		$this->bodyData['Item']['ShippingDetails']['ShippingServiceOptions']['ShippingService']		=	$ShippingService;
	}

	public function setShippingServiceCost($ShippingServiceCost){
		$this->bodyData['Item']['ShippingDetails']['ShippingServiceOptions']['ShippingServiceCost']		=	$ShippingServiceCost;
	}

	public function setShippingServiceAdditionalCost($ShippingServiceAdditionalCost){
		$this->bodyData['Item']['ShippingDetails']['ShippingServiceOptions']['ShippingServiceAdditionalCost']		=	$ShippingServiceAdditionalCost;
	}
	//--------------end 运输方式
	

	public function setSite($Site){
		$this->bodyData['Item']['Site']		=	$Site;
	}
	
	//设置计数器样式，我们暂时就只设置两种：HiddenStyle 和 BasicStyle
	public function setHitCounter($HitCounter){
		$this->bodyData['Item']['HitCounter']		=	$HitCounter;
	}
	
	//
	public function setUUID($UUID){
		$this->bodyData['Item']['UUID']		=	$UUID;
	}
	
	
	//设置多销售属性------------------------------------
	public function setItemCompatibilityListCompatibilityNotes($CompatibilityNotes){
		$this->bodyData['ItemCompatibilityList']['CompatibilityNotes "xxx"="cccc" vvv="ccc"']		=	$CompatibilityNotes;
	}
	
	public function setItemCompatibilityListNameValueListName($name){
		$this->bodyData['ItemCompatibilityList']['NameValueList']['Name'][]		=	$name;
	}
	
	public function setItemCompatibilityListNameValueListValue($value){
		$this->bodyData['ItemCompatibilityList']['NameValueList']['Value'][]	=	$value;
	}
	//end ---------------------------------------------
	
	
	public function setItemSpecifics($Name,$Value){
		//$Name	=	preg_replace(array("/</","/>/","/&/","/'/",'/"/'),array("&lt;","&gt;","&amp;","&apos;","&quot;"),$Name);
		//$Value	=	preg_replace(array("/</","/>/","/&/","/'/",'/"/'),array("&lt;","&gt;","&amp;","&apos;","&quot;"),$Value);
		$this->bodyData['Item']['ItemSpecifics']['NameValueList'][]='<Name>'.$Name.'</Name><Value>'.$Value.'</Value>';
	}
	
	public function setShippingServiceOptionsFreeShipping($FreeShipping){
		$this->bodyData['Item']['ShippingDetails']['ShippingServiceOptions']['FreeShipping']		=	$FreeShipping;
	}
	
	public function setInternationalShippingServiceOptionShippingService($ShippingService){
		$this->bodyData['Item']['ShippingDetails']['InternationalShippingServiceOption']['ShippingService']		=	$ShippingService;
	}
	
	public function setInternationalShippingServiceOptionShippingServiceCost($ShippingServiceCost, $currency){
		$key	=	'ShippingServiceCost  currencyID="'.$currency.'"';
		$this->bodyData['Item']['ShippingDetails']['InternationalShippingServiceOption'][$key]		=	$ShippingServiceCost;
	}
	
	public function setInternationalShippingServiceOptionShippingServiceAdditionalCost($ShippingServiceAdditionalCost, $currency){
		$key	=	'ShippingServiceAdditionalCost  currencyID="'.$currency.'"';
		$this->bodyData['Item']['ShippingDetails']['InternationalShippingServiceOption'][$key]		=	$ShippingServiceAdditionalCost;
	}
	
	public function setInternationalShippingServiceOptionShippingServicePriority($ShippingServicePriority){
		$this->bodyData['Item']['ShippingDetails']['InternationalShippingServiceOption']['ShippingServicePriority']		=	$ShippingServicePriority;
	}
	
	public function setInternationalShippingServiceOptionShipToLocation($ShipToLocation){
		$this->bodyData['Item']['ShippingDetails']['InternationalShippingServiceOption']['ShipToLocation'][]		=	$ShipToLocation;
	}
	
	
	public function setShipToRegistrationCountry($ShipToRegistrationCountry){
		$this->bodyData['Item']['BuyerRequirementDetails']['ShipToRegistrationCountry']	= $ShipToRegistrationCountry;
	}
	
	public function setLinkedPayPalAccount($LinkedPayPalAccount){
		$this->bodyData['Item']['BuyerRequirementDetails']['LinkedPayPalAccount']= $LinkedPayPalAccount;
	}
	
	public function setMaximumBuyerPolicyViolationsCount($Count){
		$this->bodyData['Item']['BuyerRequirementDetails']['MaximumBuyerPolicyViolations']['Count']= $Count;
	}
	
	public function setMaximumBuyerPolicyViolationsPeriod($Period){
		$this->bodyData['Item']['BuyerRequirementDetails']['MaximumBuyerPolicyViolations']['Period']= $Period;
	}
	
	public function setMaximumItemRequirementsMaximumItemCount($MaximumItemCount){
		$this->bodyData['Item']['BuyerRequirementDetails']['MaximumItemRequirements']['MaximumItemCount']	= $MaximumItemCount;
	}
	
	public function setMaximumItemRequirementsMinimumFeedbackScore($MinimumFeedbackScore){
		$this->bodyData['Item']['BuyerRequirementDetails']['MaximumItemRequirements']['MinimumFeedbackScore']	= $MinimumFeedbackScore;
	}

	public function setBuyerRequirementDetailsMinimumFeedbackScore($score){
		$this->bodyData['Item']['BuyerRequirementDetails']['MinimumFeedbackScore']= $score;
	}
	
	public function setMaximumUnpaidItemStrikesInfoCount($Count){
		$this->bodyData['Item']['BuyerRequirementDetails']['MaximumUnpaidItemStrikesInfo']['Count']= $Count;
	}
	
	
	public function setMaximumUnpaidItemStrikesInfoPeriod($Period){
		$this->bodyData['Item']['BuyerRequirementDetails']['MaximumUnpaidItemStrikesInfo']['Period']= $Period;
	}
	
}
?>