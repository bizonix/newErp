<?php
/*
*类名：ReviseFixedPriceItem
*功能：修改 item
*继续开发人：冯赛明
*继续开发时间：2013-5-25
*/
require_once WEB_PATH."/lib/sdk/ebay/EbayBase.php";

class ReviseFixedPriceItemAPI extends EbayBase{
	private	$verb	=	'ReviseFixedPriceItem';//告诉eBay我们要做的动作是：修改多属性的item
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
								<ReviseFixedPriceItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">
								<ErrorLanguage>en_US</ErrorLanguage>
								<WarningLevel>High</WarningLevel>
								<RequesterCredentials>
								<eBayAuthToken>'.$this->getToken().'</eBayAuthToken>
								</RequesterCredentials>';
		$this->xmlFooter	=	"</ReviseFixedPriceItemRequest>";
	}


	public function doReviseFixedPriceItem(){
		
		$xmlRequest	=	$this->xmlHeader.$this->xmlBody.$this->xmlFooter;
		return $this->sendHttpRequest($xmlRequest);
	}
	
	public function getBody(){
		$this->xmlBody	=	ia2xml($this->bodyData);
		return $this->xmlBody;
	}
	
	public function setBody($dat){
		$this->xmlBody	=	$dat;
	}
	
	/****************************************
	 * 获取待传递的xml信息
	 */
	public function getXml(){
		$this->xmlBody	=	ia2xml($this->bodyData);
		return $this->xmlHeader.$this->xmlBody.$this->xmlFooter;
	}
	
	//、
	public function setApplicationData($ApplicationData){
		$this->bodyData['Item']['ApplicationData']		=	$ApplicationData;
	}
	
	//、
	public function setValueLiteral($ValueLiteral){
		$this->bodyData['Item']['AttributeSetArray']['Attribute']['Value']['ValueLiteral'] = $ValueLiteral;
	}

    //、
	public function setValueID($ValueID){
		$this->bodyData['Item']['AttributeSetArray']['Attribute']['Value']['ValueID'] =	$ValueID;
	}
	
	//、
	public function setLookupAttributeArray($LookupAttributeArray){
		$this->bodyData['Item']['LookupAttributeArray'] = $LookupAttributeArray;
	}
	
	//boolean
	public function setAutoPay($AutoPay){
		$this->bodyData['Item']['AutoPay'] = $AutoPay;
	}
	
	//int
	public function setCharityNumber($CharityNumber){
		$this->bodyData['Item']['Charity']['CharityNumber'] = $CharityNumber;
	}
	
	//float
	public function setDonationPercent($DonationPercent){
		$this->bodyData['Item']['Charity']['DonationPercent'] = $DonationPercent;
	}
	
	//string
	public function setCharityID($CharityID){
		$this->bodyData['Item']['Charity']['CharityID'] = $CharityID;
	}
	
    //1、item title 必须部分，且不能提交重复标题内容
	public function setTitle($title){
		$this->bodyData['Item']['Title']		=	$title;
	}

	//2、Description（描述）
	public function setDescription($Description){
		$this->bodyData['Item']['Description']		=	$Description;
	}
	
	//3、item的第一类别ID，返回int  
	public function setCategoryID($CategoryID){
		$this->bodyData['Item']['PrimaryCategory']['CategoryID']		=	$CategoryID;
	}

	//boolean  
	public function setPrivateListing($PrivateListing){
		$this->bodyData['Item']['PrivateListing']		=	$PrivateListing;
	}

	//boolean  
	public function setIncludeStockPhotoURL($IncludeStockPhotoURL){
		$this->bodyData['Item']['IncludeStockPhotoURL']		=	$IncludeStockPhotoURL;
	}
	
	//boolean  
	public function setIncludePrefilledItemInformation($IncludePrefilledItemInformation){
		$this->bodyData['Item']['ProductListingDetails']['IncludePrefilledItemInformation']		=	$IncludePrefilledItemInformation;
	}

	//boolean  
	public function setUseStockPhotoURLAsGallery($UseStockPhotoURLAsGallery){
		$this->bodyData['Item']['ProductListingDetails']['UseStockPhotoURLAsGallery']		=	$UseStockPhotoURLAsGallery;
	}
	
	//string  
	public function setProductReferenceID($ProductReferenceID){
		$this->bodyData['Item']['ProductListingDetails']['ProductReferenceID']		=	$ProductReferenceID;
	}
	
	//string  
	public function setReturnSearchResultOnDuplicates($ReturnSearchResultOnDuplicates){
		$this->bodyData['Item']['ProductListingDetails']['ReturnSearchResultOnDuplicates']		=	$ReturnSearchResultOnDuplicates;
	}
	
	//string  
	public function setListIfNoProduct($ListIfNoProduct){
		$this->bodyData['Item']['ProductListingDetails']['ListIfNoProduct']		=	$ListIfNoProduct;
	}
	
	//string  
	public function setGTIN($GTIN){
		$this->bodyData['Item']['ProductListingDetails']['GTIN']		=	$GTIN;
	}
	
	//string  
	public function setISBN($ISBN){
		$this->bodyData['Item']['ProductListingDetails']['ISBN']		=	$ISBN;
	}
	
	//string  
	public function setUPC($UPC){
		$this->bodyData['Item']['ProductListingDetails']['UPC']		=	$UPC;
	}
	
	//string  
	public function setEAN($EAN){
		$this->bodyData['Item']['ProductListingDetails']['EAN']		=	$EAN;
	}
	
	//string
	public function setBrand($Brand){
		$this->bodyData['Item']['ProductListingDetails']['BrandMPN']['Brand']		=	$Brand;
	}
	
	//string
	public function setMPN($MPN){
		$this->bodyData['Item']['ProductListingDetails']['BrandMPN']['MPN']		=	$MPN;
	}
	
	//string
	public function setEventTitle($EventTitle){
		$this->bodyData['Item']['ProductListingDetails']['TicketListingDetails']['EventTitle'] =	$EventTitle;
	}
	
	//string
	public function setVenue($Venue){
		$this->bodyData['Item']['ProductListingDetails']['TicketListingDetails']['Venue'] =	$Venue;
	}
	
	//string
	public function setPrintedDate($PrintedDate){
		$this->bodyData['Item']['ProductListingDetails']['TicketListingDetails']['PrintedDate'] = $PrintedDate;
	}
	
	//string
	public function setPrintedTime($PrintedTime){
		$this->bodyData['Item']['ProductListingDetails']['TicketListingDetails']['PrintedTime'] = $PrintedTime;
	}
	
	//boolean
	public function setUseFirstProduct($UseFirstProduct){
		$this->bodyData['Item']['ProductListingDetails']['UseFirstProduct'] = $UseFirstProduct;
	}
	
	//boolean  
	public function setProductID($ProductID){
		$this->bodyData['Item']['ProductListingDetails']['ProductID'] =	$ProductID;
	}
	
	//boolean  
	public function setProductListingDetailsIncludeStockPhotoURL($IncludeStockPhotoURL){
		$this->bodyData['Item']['ProductListingDetails']['IncludeStockPhotoURL']=$IncludeStockPhotoURL;
	}

	//4、Store category(商店类别)的First category(第一分类)
	public function setStoreCategoryID($StoreCategoryID){
		$this->bodyData['Item']['Storefront']['StoreCategoryID']		=	$StoreCategoryID;
	}
	
	/*
	*5、Item specifics and condition（自定义物品属性）
	*  参数的类型是：array(name=>value),name是属性名称，value是属性值
	*/
	public function setItemSpecifics($Name,$Value){
		$this->bodyData['Item']['ItemSpecifics']['NameValueList'][]='<Name>'.$Name.'</Name><Value>'.$Value.'</Value>';
	}
	
	//6、固价中的StartPrice(价格) 返回数据类型：double
	public function setStartPrice($StartPrice){
		$this->bodyData['Item']['StartPrice']		=	$StartPrice;
	}
	
	//boolean 
	public function setBusinessSeller($BusinessSeller){
		$this->bodyData['Item']['VATDetails']['BusinessSeller']	= $BusinessSeller;
	}
	
	// 
	public function setCrossBorderTrade($CrossBorderTrade){
		$this->bodyData['Item']['CrossBorderTrade']	= $CrossBorderTrade;
	}
	
	// 
	public function setShipToRegistrationCountry($ShipToRegistrationCountry){
		$this->bodyData['Item']['BuyerRequirementDetails']['ShipToRegistrationCountry']	= $ShipToRegistrationCountry;
	}
	
	// 
	public function setZeroFeedbackScore($ZeroFeedbackScore){
		$this->bodyData['Item']['BuyerRequirementDetails']['ZeroFeedbackScore']	= $ZeroFeedbackScore;
	}
	
	// int 
	public function setMaximumItemCount($MaximumItemCount){
		$this->bodyData['Item']['BuyerRequirementDetails']['MaximumItemRequirements']['MaximumItemCount']	= $MaximumItemCount;
	}
	
	// int 
	public function setMaximumItemRequirementsMinimumFeedbackScore($MinimumFeedbackScore){
		$this->bodyData['Item']['BuyerRequirementDetails']['MaximumItemRequirements']['MinimumFeedbackScore']	= $MinimumFeedbackScore;
	}
	
	// int 
	public function setLinkedPayPalAccount($LinkedPayPalAccount){
		$this->bodyData['Item']['BuyerRequirementDetails']['LinkedPayPalAccount']= $LinkedPayPalAccount;
	}
	
	
	// boolean  
	public function setVerifiedUser($VerifiedUser){
		$this->bodyData['Item']['BuyerRequirementDetails']['VerifiedUserRequirements']['VerifiedUser']= $VerifiedUser;
	}
		
	// int   
	public function setMinimumFeedbackScore($MinimumFeedbackScore){
		$this->bodyData['Item']['BuyerRequirementDetails']['VerifiedUserRequirements']['MinimumFeedbackScore']= $MinimumFeedbackScore;
	}
	
	// int   
	public function setCount($Count){
		$this->bodyData['Item']['BuyerRequirementDetails']['MaximumUnpaidItemStrikesInfo']['Count']= $Count;
	}
	
	// Days_30|Days_180|Days_360   
	public function setPeriod($Period){
		$this->bodyData['Item']['BuyerRequirementDetails']['MaximumUnpaidItemStrikesInfo']['Period']= $Period;
	}
	
	// int   
	public function setMaximumBuyerPolicyViolationsCount($Count){
		$this->bodyData['Item']['BuyerRequirementDetails']['MaximumBuyerPolicyViolations']['Count']= $Count;
	}
	
	// Days_30|Days_180 
	public function setMaximumBuyerPolicyViolationsPeriod($Period){
		$this->bodyData['Item']['BuyerRequirementDetails']['MaximumBuyerPolicyViolations']['Period']= $Period;
	}
	
	//boolean 
	public function setRestrictedToBusiness($RestrictedToBusiness){
		$this->bodyData['Item']['VATDetails']['RestrictedToBusiness']	= $RestrictedToBusiness;
	}
	
	//float  
	public function setVATPercent($VATPercent){
		$this->bodyData['Item']['VATDetails']['VATPercent']	= $VATPercent;
	}
	
	//boolean   
	public function setDisableBuyerRequirements($DisableBuyerRequirements){
		$this->bodyData['Item']['DisableBuyerRequirements']	= $DisableBuyerRequirements;
	}
	
	//   
	public function setBestOfferEnabled($BestOfferEnabled){
		$this->bodyData['Item']['BestOfferDetails']['BestOfferEnabled']	= $BestOfferEnabled;
	}
	
	//   
	public function setThirdPartyCheckout($ThirdPartyCheckout){
		$this->bodyData['Item']['ThirdPartyCheckout'] = $ThirdPartyCheckout;
	}
	
	//   
	public function setUseTaxTable($UseTaxTable){
		$this->bodyData['Item']['UseTaxTable'] = $UseTaxTable;
	}
	
	//   
	public function setGetItFast($GetItFast){
		$this->bodyData['Item']['GetItFast'] = $GetItFast;
	}
	
	//   
	public function setCategoryBasedAttributesPrefill($CategoryBasedAttributesPrefill){
		$this->bodyData['Item']['CategoryBasedAttributesPrefill'] = $CategoryBasedAttributesPrefill;
	}
	
	//7、物品状况（Item condition），给用户选择<select>， 返回类型：int
	public function setConditionID($ConditionID){
		$this->bodyData['Item']['ConditionID']		=	$ConditionID;
	}
	
	public function setConditionDescription($ConditionDescription){
		$this->bodyData['Item']['ConditionDescription']		=	$ConditionDescription;
	}	
	
	public function setTaxCategory($TaxCategory){
		$this->bodyData['Item']['TaxCategory']		=	$TaxCategory;
	}	
	
	public function setPostCheckoutExperienceEnabled($PostCheckoutExperienceEnabled){
		$this->bodyData['Item']['PostCheckoutExperienceEnabled']		=	$PostCheckoutExperienceEnabled;
	}	
	
	//8、boolean
	public function setCategoryMappingAllowed($CategoryMappingAllowed){
		$this->bodyData['Item']['CategoryMappingAllowed']		=	$CategoryMappingAllowed;
	}

	//9、“物品所在地”中的“国家”，返回数据类型：string ，两位的国家简称 US  UK  AU
	public function setCountry($Country){
		$this->bodyData['Item']['Country']		=	$Country;
	}

	//10、“价格”后面的“单位”，返回数据类型：string ，对应的站点货币简码 USD  RMB
	public function setCurrency($Currency){
		$this->bodyData['Item']['Currency']		=	$Currency;
	}

	//int
	public function setGiftIcon($GiftIcon){
		$this->bodyData['Item']['GiftIcon']		=	$GiftIcon;
	}
	
	//GiftExpressShipping|GiftShipToRecipient|GiftWrap|CustomCode
	public function setGiftServices($GiftServices){
		$this->bodyData['Item']['GiftServices']		=	$GiftServices;
	}

	//NoHitCounter|HonestyStyle|GreenLED|Hidden|BasicStyle|RetroStyle|HiddenStyle|CustomCode
	public function setHitCounter($HitCounter){
		$this->bodyData['Item']['HitCounter']=$HitCounter;
	}
	
	//AmountType
	public function setMinimumBestOfferPrice($MinimumBestOfferPrice){
		$this->bodyData['Item']['ListingDetails']['MinimumBestOfferPrice']	=	$MinimumBestOfferPrice;
	}
	
	//string
	public function setLocalListingDistance($LocalListingDistance){
		$this->bodyData['Item']['ListingDetails']['LocalListingDistance']		=	$LocalListingDistance;
	}	
	
	//AmountType
	public function setBestOfferAutoAcceptPrice($BestOfferAutoAcceptPrice){
		$this->bodyData['Item']['ListingDetails']['BestOfferAutoAcceptPrice']		=	$BestOfferAutoAcceptPrice;
	}
	
	//int
	public function setLayoutID($LayoutID){
		$this->bodyData['Item']['ListingDesigner']['LayoutID'] = $LayoutID;
	}
	
	//boolean
	public function setOptimalPictureSize($OptimalPictureSize){
		$this->bodyData['Item']['ListingDesigner']['OptimalPictureSize'] = $OptimalPictureSize;
	}
	
	//int
	public function setThemeID($ThemeID){
		$this->bodyData['Item']['ListingDesigner']['ThemeID'] = $ThemeID;
	}
	
	//
	public function setSkypeEnabled($SkypeEnabled){
		$this->bodyData['Item']['SkypeEnabled'] = $SkypeEnabled;
	}
	
	//
	public function setSkypeID($SkypeID){
		$this->bodyData['Item']['SkypeID'] = $SkypeID;
	}
	
	//Chat|Voice|CustomCode
	public function setSkypeContactOption($SkypeContactOption){
		$this->bodyData['Item']['SkypeContactOption'] = $SkypeContactOption;
	}
	
	//
	public function setThirdPartyCheckoutIntegration($ThirdPartyCheckoutIntegration){
		$this->bodyData['Item']['ThirdPartyCheckoutIntegration'] = $ThirdPartyCheckoutIntegration;
	}
	
	//string 
	public function setProStoresStoreName($ProStoresStoreName){
		$this->bodyData['Item']['ListingCheckoutRedirectPreference']['ProStoresStoreName'] = $ProStoresStoreName;
	}
	
	//string 
	public function setSellerThirdPartyUsername($SellerThirdPartyUsername){
		$this->bodyData['Item']['ListingCheckoutRedirectPreference']['SellerThirdPartyUsername'] = $SellerThirdPartyUsername;
	}
	
	//int	待发货最长的时间
	public function setDispatchTimeMax($DispatchTimeMax){
		$this->bodyData['Item']['DispatchTimeMax']		=	$DispatchTimeMax;
	}

	//11、string	listing的刊登天数  Days_7  GTC
	public function setListingDuration($ListingDuration){
		$this->bodyData['Item']['ListingDuration']		=	$ListingDuration;
	}
		   
    //  Border|BoldTitle|Featured|Highlight|HomePageFeatured|ProPackBundle|BasicUpgradePackBundle   
	//  |ValuePackBundle|ProPackPlusBundle|CustomCode
	public function setListingEnhancement($ListingEnhancement){
		$this->bodyData['Item']['ListingEnhancement']		=	$ListingEnhancement;
	}
	
	//http://developer.ebay.com/DevZone/XML/docs/Reference/eBay/types/ListingTypeCodeType.html
	//单个 Chinese(单属性)  AddType   FixedPriceItem 多属性   Half
	public function setListingType($ListingType){	//可选
		$this->bodyData['Item']['ListingType']		=	$ListingType;
	}

	//付款方式
	public function setPaymentMethods($PaymentMethods){
		$this->bodyData['Item']['PaymentMethods'][]		=	$PaymentMethods;
	}

	//收款的账号
	public function setPayPalEmailAddress($PayPalEmailAddress){
		$this->bodyData['Item']['PayPalEmailAddress']		=	$PayPalEmailAddress;
	}

	//可支持多张图片， 默认第一张为橱窗主图
	public function setPictureURL($PictureURL){
		$this->bodyData['Item']['PictureDetails']['PictureURL'][]		=	$PictureURL;
	}
	
	//None|Featured|Gallery|Plus|CustomCode
	public function setGalleryType($GalleryType){
		$this->bodyData['Item']['PictureDetails']['GalleryType'][]		=	$GalleryType;
	}
	
	public function setGalleryURL($GalleryURL){
		$this->bodyData['Item']['PictureDetails']['GalleryURL'][]		=	$GalleryURL;
	}
	
	public function setPhotoDisplay($PhotoDisplay){
		$this->bodyData['Item']['PictureDetails']['PhotoDisplay'][]		=	$PhotoDisplay;
	}
	
	//物品所在邮局的post code
	public function setPostalCode($PostalCode){
		$this->bodyData['Item']['PostalCode']		=	$PostalCode;
	}

	public function setShippingTermsInDescription($ShippingTermsInDescription){
		$this->bodyData['Item']['ShippingTermsInDescription']		=	$ShippingTermsInDescription;
	}
	
	public function setExternalProductID($ExternalProductID){
		$this->bodyData['Item']['ExternalProductID']		=	$ExternalProductID;
	}
	
	//Location
	public function setLocation($Location){
		$this->bodyData['Item']['Location']		=	$Location;
	}

	//int 设置库存数量
	public function setQuantity($Quantity){
		$this->bodyData['Item']['Quantity']		=	$Quantity;
	}
	
	//string
	public function setPrivateNotes($PrivateNotes){
		$this->bodyData['Item']['PrivateNotes']		=	$PrivateNotes;
	}

	//string
	public function setScheduleTime($ScheduleTime){
		$this->bodyData['Item']['ScheduleTime']		=	$ScheduleTime;
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

	public function setWarrantyOfferedOption($WarrantyOfferedOption){
		$this->bodyData['Item']['ReturnPolicy']['WarrantyOfferedOption']		=	$WarrantyOfferedOption;
	}

	public function setWarrantyTypeOption($WarrantyTypeOption){
		$this->bodyData['Item']['ReturnPolicy']['WarrantyTypeOption']		=	$WarrantyTypeOption;
	}
	
	//token 
	public function setWarrantyDurationOption($WarrantyDurationOption){
		$this->bodyData['Item']['ReturnPolicy']['WarrantyDurationOption']		=	$WarrantyDurationOption;
	}
	
	//token 
	public function setReturnPolicyEAN($EAN){
		$this->bodyData['Item']['ReturnPolicy']['EAN']		=	$EAN;
	}



	public function setShippingCostPaidByOption($ShippingCostPaidByOption){
		$this->bodyData['Item']['ReturnPolicy']['ShippingCostPaidByOption']		=	$ShippingCostPaidByOption;
	}
	

	public function setRestockingFeeValueOption($RestockingFeeValueOption){
		$this->bodyData['Item']['ReturnPolicy']['RestockingFeeValueOption']		=	$RestockingFeeValueOption;
	}
	//--------------end 退换货

	public function setInventoryTrackingMethod($InventoryTrackingMethod){
		$this->bodyData['Item']['InventoryTrackingMethod']		=	$InventoryTrackingMethod;
	}
	
	//$NameValueList为字符串类型，格式为：'Name:Value;Name:Value'
	public function setVariation($SKU,$Quantity,$StartPrice,$NameValueList)
	{
		$str='<SKU>'.$SKU.'</SKU>';
		$str.='<Quantity>'.$Quantity.'</Quantity>';
		if(!empty($StartPrice)) 
		{
			$str.='<StartPrice>'.$StartPrice.'</StartPrice>';
		}
		if(!empty($NameValueList))
		{
			$N=array();
			$N=explode(';',$NameValueList);
			$str.='<VariationSpecifics>';
			foreach($N as $v)
			{
				$nv=explode(':',$v);
				$str.='<NameValueList>';
				$str.='<Name>'.$nv[0].'</Name>';
				$str.='<Value>'.$nv[1].'</Value>';
				$str.='</NameValueList>';
			}
			$str.='</VariationSpecifics>';
		}
		//echo $str;
		$this->bodyData['Item']['Variations']['Variation'][]=$str;
	}

	//这里的属性名称和属性值是一对一的关系
	public function setVariationSpecificsNameValueList($Name,$value){
		$this->bodyData['Item']['Variations']['Variation']['VariationSpecifics']['NameValueList'][] ='<Name>'.$Name.'</Name><Value>'.$value.'</Value>';
	}
	
	// AmountType 
	public function setOriginalRetailPrice($OriginalRetailPrice){
		$this->bodyData['Item']['Variations']['Variation']['DiscountPriceInfo']['OriginalRetailPrice'] = $OriginalRetailPrice;
	}
	
	// AmountType 
	public function setMinimumAdvertisedPrice($MinimumAdvertisedPrice){
		$this->bodyData['Item']['Variations']['Variation']['DiscountPriceInfo']['MinimumAdvertisedPrice'] = $MinimumAdvertisedPrice;
	}
	
	// AmountType 
	public function setMinimumAdvertisedPriceExposure($MinimumAdvertisedPriceExposure){
		$this->bodyData['Item']['Variations']['Variation']['DiscountPriceInfo']['MinimumAdvertisedPriceExposure'] = $MinimumAdvertisedPriceExposure;
	}
	
	
	// AmountType 
	public function setSoldOneBay($SoldOneBay){
		$this->bodyData['Item']['Variations']['Variation']['DiscountPriceInfo']['SoldOneBay'] = $SoldOneBay;
	}
	
	// AmountType 
	public function setSoldOffeBay($SoldOffeBay){
		$this->bodyData['Item']['Variations']['Variation']['DiscountPriceInfo']['SoldOffeBay'] = $SoldOffeBay;
	}
	
	// AmountType 
	public function setMadeForOutletComparisonPrice($MadeForOutletComparisonPrice){
		$this->bodyData['Item']['Variations']['Variation']['DiscountPriceInfo']['MadeForOutletComparisonPrice'] = $MadeForOutletComparisonPrice;
	}
	
	// boolean   
	public function setUseRecommendedProduct($UseRecommendedProduct){
		$this->bodyData['Item']['Variations']['Pictures']['UseRecommendedProduct'] = $UseRecommendedProduct;
	}
	
	// boolean   
	public function setVIN($VIN){
		$this->bodyData['Item']['VIN'] = $VIN;
	}
	
	
	// boolean   
	public function setVRM($VRM){
		$this->bodyData['Item']['VRM'] = $VRM;
	}
	
	// int    
	public function setMinimumRemnantSet($MinimumRemnantSet){
		$this->bodyData['Item']['QuantityInfo']['MinimumRemnantSet'] = $MinimumRemnantSet;
	}
	
	// long     
	public function setShippingProfileID($ShippingProfileID){
		$this->bodyData['Item']['SellerProfiles']['SellerShippingProfile']['ShippingProfileID'] = $ShippingProfileID;
	}
	
	// string      
	public function setShippingProfileName($ShippingProfileName){
		$this->bodyData['Item']['SellerProfiles']['SellerShippingProfile']['ShippingProfileName'] = $ShippingProfileName;
	}
	
	// long     
	public function setReturnProfileID($ReturnProfileID){
		$this->bodyData['Item']['SellerProfiles']['SellerReturnProfile']['ReturnProfileID'] = $ReturnProfileID;
	}
	
	// string      
	public function setReturnProfileName($ReturnProfileName){
		$this->bodyData['Item']['SellerProfiles']['SellerReturnProfile']['ReturnProfileName'] = $ReturnProfileName;
	}
	
	// string      
	public function setPaymentProfileID($PaymentProfileID){
		$this->bodyData['Item']['SellerProfiles']['SellerPaymentProfile']['PaymentProfileID'] = $PaymentProfileID;
	}
	
	// string      
	public function setPaymentProfileName($PaymentProfileName){
		$this->bodyData['Item']['SellerProfiles']['SellerPaymentProfile']['PaymentProfileName'] = $PaymentProfileName;
	}
		
	//MeasureType     
	public function setPackageDepth($PackageDepth){
		$this->bodyData['Item']['ShippingPackageDetails']['PackageDepth'] = $PackageDepth;
	}
	
	//由于Name对Value是一对多关系，这里的Value参数格式必须是：字符串格式，且每个值之间用英文逗号隔开，
	//例如：'S,M,L,XL'
	public function setVariationSpecificsSetNameValueList($Name,$Value){
		$str='<Name>'.$Name.'</Name>';
		$Values=explode(',',$Value);
		foreach($Values as $v)
		{
			$str.='<Value>'.$v.'</Value>';
		}
		//echo $str;
		$this->bodyData['Item']['Variations']['VariationSpecificsSet']['NameValueList'][] = $str;
	}
	
	public function setPicturesVariationSpecificName($VariationSpecificName){
		$this->bodyData['Item']['Variations']['Pictures']['VariationSpecificName'] = $VariationSpecificName;
	}
	
	//多属性
//	public function setPicturesVariationSpecificValue($VariationSpecificValue){
//		$this->bodyData['Item']['Variations']['Pictures']['VariationSpecificPictureSet']['VariationSpecificValue'] = $VariationSpecificValue;
//	}
//	
	//多属性
	public function setVariationSpecificPictureSet($VariationSpecificValue,$PictureURL){
		$Pictures=explode(',',$PictureURL);
		$str='<VariationSpecificValue>'.$VariationSpecificValue.'</VariationSpecificValue>';
		foreach($Pictures as $p)
		{
			$str.='<PictureURL>'.$p.'</PictureURL>';
		}
		$this->bodyData['Item']['Variations']['Pictures']['VariationSpecificPictureSet'][]=$str;
	}
	
	public function setVariationSpecificPictureSetNameValueListValue($value){
		$this->bodyData['Item']['Variations']['Pictures']['VariationSpecificPictureSet']['NameValueList']['value'] = $value;
	}
	
	//运输方式
	public function setShippingType($ShippingType){
		$this->bodyData['Item']['ShippingDetails']['ShippingType']		=	$ShippingType;
	}
	
	//
	public function setGlobalShipping($GlobalShipping){
		$this->bodyData['Item']['ShippingDetails']['GlobalShipping'] = $GlobalShipping;
	}
	
	//string 
	public function setOriginatingPostalCode($OriginatingPostalCode){
		$this->bodyData['Item']['ShippingDetails']['CalculatedShippingRate']['CalculatedShippingRate']['OriginatingPostalCode'] = $OriginatingPostalCode;
	}
	
	//MeasureType 
	public function setMeasurementUnit($MeasurementUnit){
		$this->bodyData['Item']['ShippingDetails']['CalculatedShippingRate']['CalculatedShippingRate']['MeasurementUnit'] = $MeasurementUnit;
	}
	
	//MeasureType 
	public function setCalculatedShippingRatePackageDepth($PackageDepth){
		$this->bodyData['Item']['ShippingDetails']['CalculatedShippingRate']['CalculatedShippingRate']['PackageDepth'] = $PackageDepth;
	}
	
	//MeasureType 
	public function setPackageLength($PackageLength){
		$this->bodyData['Item']['ShippingDetails']['CalculatedShippingRate']['CalculatedShippingRate']['PackageLength'] = $PackageLength;
	}
	
	//MeasureType 
	public function setPackageWidth($PackageWidth){
		$this->bodyData['Item']['ShippingDetails']['CalculatedShippingRate']['CalculatedShippingRate']['PackageWidth'] = $PackageWidth;
	}
	
	//boolean  
	public function setShippingIrregular($ShippingIrregular){
		$this->bodyData['Item']['ShippingDetails']['CalculatedShippingRate']['CalculatedShippingRate']['ShippingIrregular'] = $ShippingIrregular;
	}
	
	//None|Letter|LargeEnvelope|USPSLargePack|VeryLargePack|ExtraLargePack|UPSLetter|USPSFlatRateEnvelope|PackageThickEnvelope|Roll(Only showing first 10 of 31)	 
	public function setShippingPackage($ShippingPackage){
		$this->bodyData['Item']['ShippingDetails']['CalculatedShippingRate']['CalculatedShippingRate']['ShippingPackage'] = $ShippingPackage;
	}
	
	//MeasureType 	 
	public function setWeightMajor($WeightMajor){
		$this->bodyData['Item']['ShippingDetails']['CalculatedShippingRate']['CalculatedShippingRate']['WeightMajor'] = $WeightMajor;
	}
	
	//MeasureType 	 
	public function setShippingPackageDetailsWeightMinor($WeightMinor){
		$this->bodyData['Item']['ShippingPackageDetails']['WeightMinor'] = $WeightMinor;
	}
	
	//MeasureType 	 
	public function setShippingPackageDetailsMeasurementUnit($MeasurementUnit){
		$this->bodyData['Item']['ShippingPackageDetails']['MeasurementUnit'] = $MeasurementUnit;
	}
	
	
	//MeasureType 	 
	public function setShippingPackageDetailsPackageDepth($PackageDepth){
		$this->bodyData['Item']['ShippingPackageDetails']['PackageDepth'] = $PackageDepth;
	}
	
	//MeasureType 	 
	public function setShippingPackageDetailsPackageLength($PackageLength){
		$this->bodyData['Item']['ShippingPackageDetails']['PackageLength'] = $PackageLength;
	}
	
	
	//MeasureType 	 
	public function setShippingPackageDetailsShippingIrregular($ShippingIrregular){
		$this->bodyData['Item']['ShippingPackageDetails']['ShippingIrregular'] = $ShippingIrregular;
	}
	
	//MeasureType 	 
	public function setShippingPackageDetailsShippingPackage($ShippingPackage){
		$this->bodyData['Item']['ShippingPackageDetails']['ShippingPackage'] = $ShippingPackage;
	}
	
	//MeasureType 	 
	public function setShippingPackageDetailsPackageWidth($PackageWidth){
		$this->bodyData['Item']['ShippingPackageDetails']['PackageWidth'] = $PackageWidth;
	}
	
	//MeasureType 	 
	public function setShippingPackageDetailsWeightMajor($WeightMajor){
		$this->bodyData['Item']['ShippingPackageDetails']['WeightMajor'] = $WeightMajor;
	}
	
	//MeasureType 	 
	public function setRateInsuranceFee($InsuranceFee){
		$this->bodyData['Item']['ShippingDetails']['CalculatedShippingRate']['InsuranceFee'] = $InsuranceFee;
	}
	
	// 	 
	public function setMaximumQuantity($MaximumQuantity){
		$this->bodyData['Item']['QuantityRestrictionPerBuyer']['MaximumQuantity'] = $MaximumQuantity;
	}
	// 	 
	public function setIncludeRecommendations($IncludeRecommendations){
		$this->bodyData['Item']['IncludeRecommendations'] = $IncludeRecommendations;
	}
	
	
	//Optional|Required|NotOffered|IncludedInShippingHandling|CustomCode 	 
	public function setInsuranceOption($InsuranceOption){
		$this->bodyData['Item']['ShippingDetails']['InsuranceDetails']['InsuranceOption'] = $InsuranceOption;
	}
	
	//
	public function setDetailsInsuranceFee($InsuranceFee){
		$this->bodyData['Item']['ShippingDetails']['InternationalInsuranceDetails']['InsuranceFee'] = $InsuranceFee;
	}
	
	//
	public function setShippingDiscountProfileID($ShippingDiscountProfileID){
		$this->bodyData['Item']['ShippingDetails']['ShippingDiscountProfileID'] = $ShippingDiscountProfileID;
	}
	
	//
	public function setPromotionalShippingDiscount($PromotionalShippingDiscount){
		$this->bodyData['Item']['ShippingDetails']['PromotionalShippingDiscount'] = $PromotionalShippingDiscount;
	}
	
	//string
	public function setInternationalShippingDiscountProfileID($InternationalShippingDiscountProfileID){
		$this->bodyData['Item']['ShippingDetails']['InternationalShippingDiscountProfileID'] = $InternationalShippingDiscountProfileID;
	}
	
	//string
	public function setInternationalPromotionalShippingDiscount($InternationalPromotionalShippingDiscount){
		$this->bodyData['Item']['ShippingDetails']['InternationalPromotionalShippingDiscount'] = $InternationalPromotionalShippingDiscount;
	}
	
	//string
	public function setCODCost($CODCost){
		$this->bodyData['Item']['ShippingDetails']['CODCost'] = $CODCost;
	}
	
	//string
	public function setExcludeShipToLocation($ExcludeShipToLocation){
		$this->bodyData['Item']['ShippingDetails']['ExcludeShipToLocation'] = $ExcludeShipToLocation;
	}
	
	//string  
	public function setPaymentInstructions($PaymentInstructions){
		$this->bodyData['Item']['ShippingDetails']['CalculatedShippingRate']['PaymentInstructions'] = $PaymentInstructions;
	}
	
	//string  
	public function setDomesticRateTable($DomesticRateTable){
		$this->bodyData['Item']['ShippingDetails']['RateTableDetails']['DomesticRateTable'] = $DomesticRateTable;
	}
	
	//string  
	public function setInternationalRateTable($InternationalRateTable){
		$this->bodyData['Item']['ShippingDetails']['RateTableDetails']['InternationalRateTable'] = $InternationalRateTable;
	}
	
	//float   
	public function setSalesTaxPercent($SalesTaxPercent){
		$this->bodyData['Item']['ShippingDetails']['CalculatedShippingRate']['SalesTax']['SalesTaxPercent'] = $SalesTaxPercent;
	}
	
	//string    
	public function setShipToLocations($ShipToLocations){
		$this->bodyData['Item']['ShipToLocations'] = $ShipToLocations;
	}
	
	//float   
	public function setShippingIncludedInTax($ShippingIncludedInTax){
		$this->bodyData['Item']['ShippingDetails']['CalculatedShippingRate']['SalesTax']['ShippingIncludedInTax'] = $ShippingIncludedInTax;
	}
	
	//PackagingHandlingCosts 
	public function setPackagingHandlingCosts($PackagingHandlingCosts){
		$this->bodyData['Item']['ShippingDetails']['CalculatedShippingRate']['CalculatedShippingRate']['PackagingHandlingCosts'] = $PackagingHandlingCosts;
	}
	
	//int
	public function setShippingServicePriority($ShippingServicePriority){
		$this->bodyData['Item']['ShippingDetails']['ShippingServiceOptions']['ShippingServicePriority']		=	$ShippingServicePriority;
	}
	
	//string 
	public function setShipToLocation($ShipToLocation){
		$this->bodyData['Item']['ShippingDetails']['ShippingServiceOptions']['ShipToLocation']		=	$ShipToLocation;
	}

	//token 
	public function setShippingService($ShippingService){
		$this->bodyData['Item']['ShippingDetails']['ShippingServiceOptions']['ShippingService']		=	$ShippingService;
	}

	public function setShippingServiceCost($ShippingServiceCost){
		$this->bodyData['Item']['ShippingDetails']['ShippingServiceOptions']['ShippingServiceCost']		=	$ShippingServiceCost;
	}

	public function setShippingServiceAdditionalCost($ShippingServiceAdditionalCost){
		$this->bodyData['Item']['ShippingDetails']['ShippingServiceOptions']['ShippingServiceAdditionalCost']		=	$ShippingServiceAdditionalCost;
	}
	
	//AmountType 
	public function setShippingSurcharge($ShippingSurcharge){
		$this->bodyData['Item']['ShippingDetails']['ShippingServiceOptions']['ShippingSurcharge']		=	$ShippingSurcharge;
	}
	
	//boolean  
	public function setFreeShipping($FreeShipping){
		$this->bodyData['Item']['ShippingDetails']['ShippingServiceOptions']['ShippingSurcharge']		=	$FreeShipping;
	}
	
	/*
	*
	*/
	public function setItemID($ItemID){
		$this->bodyData['Item']['ItemID']		=	$ItemID;
	}

	//--------------end 运输方式
	
	public function setSite($Site){
		$this->bodyData['Item']['Site']		=	$Site;
	}

	public function setUUID($UUID){
		$this->bodyData['Item']['UUID']		=	$UUID;
	}
	
	//设置多销售属性------------------------------------
	public function setItemCompatibilityListCompatibilityNotes($CompatibilityNotes){
		$this->bodyData['ItemCompatibilityList']['CompatibilityNotes']		=	$CompatibilityNotes;
	}
	
	public function setItemCompatibilityListNameValueListName($name){
		$this->bodyData['ItemCompatibilityList']['NameValueList']['Name'][]		=	$name;
	}
	
	public function setItemCompatibilityListNameValueListValue($value){
		$this->bodyData['ItemCompatibilityList']['NameValueList']['Value'][]	=	$value;
	}
	//end ---------------------------------------------	
}
?>