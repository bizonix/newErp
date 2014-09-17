<?php
require_once WEB_PATH."/lib/sdk/ebay/EbayBase.php";
class UploadSiteHostedPicturesByUrl extends EbayBase{
	private	$verb		=	'UploadSiteHostedPictures';
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
		$this->xmlHeader = '<?xml version="1.0" encoding="utf-8"?>
								<UploadSiteHostedPicturesRequest xmlns="urn:ebay:apis:eBLBaseComponents">
								<ErrorLanguage>en_US</ErrorLanguage>
								<WarningLevel>High</WarningLevel>
								<RequesterCredentials>
									<eBayAuthToken>'.$this->getToken().'</eBayAuthToken>
								</RequesterCredentials>';
		$this->xmlFooter = "</UploadSiteHostedPicturesRequest>";
	}

	public function doUploadSiteHostedPicturesByUrl(){
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

	public function setPictureName($pictureName) {
		$this->bodyData['PictureName'] = $pictureName;
	}

	//图片加水印，目录为两种：User，使用账号名称做水印。Icon，使用ebay默认的图标做水印
	public function setPictureWatermark($pictureWatermark) {
		$this->bodyData['PictureWatermark'] = $pictureWatermark;
	}
	
	/**
	 * 设置图片的Url地址
	 */
	public function setPictureUrl($url) {
		$this->bodyData['ExternalPictureURL'] = $url;
	}

	/**
	 * 设置图片的大小
	 * $type 的值为：Supersize和Standard两种
	 */
	public function setPictureSize($type) {
		$this->bodyData['PictureSet'] = $type;
	}
}