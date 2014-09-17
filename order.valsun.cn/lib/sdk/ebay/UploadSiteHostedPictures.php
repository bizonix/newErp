<?php
require_once WEB_PATH."/lib/sdk/ebay/EbayBinBase.php";

class UploadSiteHostedPictures extends EbayBinBase{
	private	$verb		=	'UploadSiteHostedPictures';
	private $xmlHeader	=	"";
	private $xmlBody	=	"";
	private $xmlFooter	=	"";
	private $xmlRequest	=	"";
	private $bodyData	=	array();
	private $boundary	= "MIME_boundary";

	public function __construct($ebay_account){  //2013/6/26 edit by zxh
		parent::setEbayBase($ebay_account, $this->verb,"MIME_boundary");
		$this->setXmlBase();						 
	}
	
	public function setXmlBase(){
		$this->xmlHeader	=	'<?xml version="1.0" encoding="utf-8"?>
								<UploadSiteHostedPicturesRequest xmlns="urn:ebay:apis:eBLBaseComponents">
								<ErrorLanguage>en_US</ErrorLanguage>
								<WarningLevel>High</WarningLevel>
								<RequesterCredentials>
									<eBayAuthToken>'.$this->getToken().'</eBayAuthToken>
								</RequesterCredentials>';
		$this->xmlFooter	=	"</UploadSiteHostedPicturesRequest>";
	}

	public function doGetStore($multiPartImageData){
		$xmlRequest	=	$this->xmlHeader.$this->xmlBody.$this->xmlFooter;
		$binCode = $this->setImageData($multiPartImageData,$xmlRequest);
		return $this->sendHttpRequest($binCode);
	}

	public function setPictureName($pictureName) {
		$this->bodyData['PictureName'] = $pictureName;
	}

	/**
	 * 设置图片的大小
	 * $type 的值为：Supersize和Standard两种
	 */
	public function setPictureSize($type) {
		$this->bodyData['PictureSet'] = $type;
	}

	//图片加水印，目录为两种：User，使用账号名称做水印。Icon，使用ebay默认的图标做水印
	public function setPictureWatermark($pictureWatermark) {
		$this->bodyData['PictureWatermark'] = $pictureWatermark;
	}
	public function setImageData($multiPartImageData,$xmlReq) {
		//$boundary	= "MIME_boundary";
		$CRLF		= "\r\n";
		$secondPart	= "";
		// The complete POST consists of an XML request plus the binary image separated by boundaries
		$firstPart	 = '';
		$firstPart	.= "--" . "MIME_boundary" . $CRLF;
		$firstPart	.= 'Content-Disposition: form-data; name="XML Payload"' . $CRLF;
		$firstPart  .= 'Content-Type: text/xml;charset=utf-8' . $CRLF . $CRLF;
		$firstPart  .= $xmlReq;
		$firstPart  .= $CRLF;
		
		$secondPart .= "--" . "MIME_boundary" . $CRLF;
		$secondPart .= 'Content-Disposition: form-data; name="dummy"; filename="dummy"' . $CRLF;
		$secondPart .= "Content-Transfer-Encoding: binary" . $CRLF;
		$secondPart .= "Content-Type: application/octet-stream" . $CRLF . $CRLF;
		$secondPart .= $multiPartImageData;
		$secondPart .= $CRLF;
		$secondPart .= "--" . "MIME_boundary" . "--" . $CRLF;
		
		$fullPost = $firstPart . $secondPart;

		return $fullPost;
	}
}