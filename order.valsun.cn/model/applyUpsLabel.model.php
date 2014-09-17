<?php
/*
 * 申请ups label
 */
class ApplyUpsLabelModel extends ExpressLabelApplyModel {
    public static  $errCode    = 0;
    public static  $errMsg     = '';
    
    /*
     * 构造函数
     */
    public function __construct(){
        parent::__construct();
    }
    
    /*
     * 建立请求的申请usp label
     */
    public function applyUPSLabel($data){
        $recipients     = isset($data['recipients'])        ? $this->strreplace($data['recipients']) : '';              //收货人
        $re_phone       = isset($data['re_phone'])          ? $this->strreplace($data['re_phone'])   : '';              //收货人电话
        $re_address1    = isset($data['re_address1'])       ? $this->strreplace($data['re_address1']): '';              //收件人地址1
        $re_address2    = isset($data['re_address2'])       ? $this->strreplace($data['re_address2']): '';              //收件人地址2
        $re_city        = isset($data['re_city'])           ? $this->strreplace($data['re_city'])    : '';              //收件人城市
        $re_state_code  = isset($data['re_state_code'])     ? $data['re_state_code'] : '';                              //收件人所在州简码
        $re_post_code   = isset($data['re_post_code'])      ? $data['re_post_code']  : '';                              //收件人邮编
        $re_country_code= isset($data['re_country_code'])   ? $data['re_country_code']: '';                             //所在国家简码
        $weight         = isset($data['weight'])            ? $data['weight'] : '';                                //重量
        
        //其他信息
        $orderId        = isset($data['orderId'])       ? $data['orderId']      : '';                                    //订单号
        $sku_position   = isset($data['sku_position'])  ? $data['sku_position'] : '';                                    //位置
        $show_detail    = isset($data['show_detail'])   ? $data['show_detail']  : '';                                    //详细
        
        $requestXml     = <<<EOF
        <?xml version="1.0"?>
        <AccessRequest xml:lang="en-US">
          <AccessLicenseNumber>0CBABC2A72B88046</AccessLicenseNumber>
          <UserId>linemartups</UserId>
          <Password>LineMart9988</Password>
        </AccessRequest> 
        <?xml version="1.0"?>
        <ShipmentConfirmRequest xml:lang="en-US">
          <Request>
            <TransactionReference>
              <CustomerContext>Customer Comment</CustomerContext>
              <XpciVersion/>
            </TransactionReference>
            <RequestAction>ShipConfirm</RequestAction>
            <RequestOption>nonvalidate</RequestOption>
          </Request>
          <LabelSpecification>
            <LabelPrintMethod>
              <Code>GIF</Code>
              <Description>GIF file</Description>
            </LabelPrintMethod>
            <LabelImageFormat>
              <Code>GIF</Code>
              <Description>gif</Description>
            </LabelImageFormat>
        	<LabelStockSize>
        	  <Height>4</Height>
        	  <Width>6</Width>
        	</LabelStockSize>
          </LabelSpecification>
          <Shipment>
           <RateInformation>
              <NegotiatedRatesIndicator/> 
            </RateInformation> 
        	<Description/>
            <Shipper>
              <Name>Linemart Inc</Name>
              <PhoneNumber></PhoneNumber>
              <ShipperNumber>A305W6</ShipperNumber>
        	  <TaxIdentificationNumber></TaxIdentificationNumber>
              <Address>
            	<AddressLine1>16518 E. Gale Ave</AddressLine1>
            	<City>City of Industry</City>
            	<StateProvinceCode>CA</StateProvinceCode>
            	<PostalCode>91745</PostalCode>
            	<PostcodeExtendedLow></PostcodeExtendedLow>
            	<CountryCode>US</CountryCode>
             </Address>
            </Shipper>
        	<ShipTo>  
             <CompanyName>$recipients</CompanyName>
              <AttentionName>$recipients</AttentionName>
              <PhoneNumber>$re_phone</PhoneNumber>
              <Address>
                <AddressLine1>$re_address1</AddressLine1>
                <AddressLine2>$re_address2</AddressLine2>
                <City>$re_city</City>
                <StateProvinceCode>$re_state_code</StateProvinceCode>
                <PostalCode>$re_post_code</PostalCode>
                <CountryCode>$re_country_code</CountryCode>
              </Address>
            </ShipTo>
            <ShipFrom>
              <CompanyName>LINEMART INC.</CompanyName>
              <AttentionName>LINEMART INC.</AttentionName>
              <PhoneNumber></PhoneNumber>
        	  <TaxIdentificationNumber></TaxIdentificationNumber>
              <Address>
                <AddressLine1>16518 E. Gale Ave</AddressLine1>
                <City>City of Industry</City>
            	<StateProvinceCode>CA</StateProvinceCode>
            	<PostalCode>91745</PostalCode>
            	<CountryCode>US</CountryCode>
              </Address>
            </ShipFrom>
             <PaymentInformation>
        		<BillThirdParty>
        			<BillThirdPartyShipper>
        				<AccountNumber>E6W710</AccountNumber>	
        				<ThirdParty>
        				<Address>
        				<PostalCode>60544</PostalCode>
        				<CountryCode>US</CountryCode>
        				</Address>
        				</ThirdParty>
        			</BillThirdPartyShipper>
        		</BillThirdParty>
            </PaymentInformation>
            <Service>
              <Code>03</Code>
              <Description>Ground</Description>
            </Service>
            <Package>
              <PackagingType>
                <Code>02</Code>
                <Description>Customer Supplied</Description>
              </PackagingType>
              <Description>Package Description</Description>
        	  <ReferenceNumber>
        	  	<Code>00</Code>
        		<Value>$orderId , $sku_position</Value>
        	  </ReferenceNumber>
        	  <ReferenceNumber>
        	  	<Code>01</Code>
        		<Value>$show_detail</Value>
        	  </ReferenceNumber>
              <PackageWeight>
                <UnitOfMeasurement/>
                <Weight>$weight</Weight>
              </PackageWeight>
              <AdditionalHandling>0</AdditionalHandling>
            </Package>
          </Shipment>
        </ShipmentConfirmRequest>
EOF;
// echo $requestXml;exit;
        $confirmUrl = 'https://onlinetools.ups.com/ups.app/xml/ShipConfirm';                        //生产环境
//         $confirmUrl = 'https://wwwcie.ups.com/ups.app/xml/ShipConfirm';                              //测试环境地址
        $result = $this->sendRequest($requestXml, $confirmUrl);
        if (FALSE === $result){
            return FALSE;
        }
        
        $parsedResult   = $this->parseXMLResult($result);
        if (FALSE === $parsedResult) {
        	return FALSE;
        }
//         print_r($parsedResult);
        if ($parsedResult->Response->ResponseStatusDescription == 'Failure') {                        //请求失败
            self::$errMsg   = strval($parsedResult->Response->Error->ErrorDescription);
        	return FALSE;
        }
        
//         print_r($parsedResult);exit;
        $shipDes    = strval( $parsedResult->ShipmentDigest ) ;
//         var_dump($shipDes);exit;
        $requestXml_accetp = <<<EOF
        <?xml version="1.0" encoding="ISO-8859-1"?>
    	<AccessRequest>
    		<AccessLicenseNumber>0CBABC2A72B88046</AccessLicenseNumber>
    		<UserId>linemartups</UserId>
    		<Password>LineMart9988</Password>
    	</AccessRequest>
    	<?xml version="1.0" encoding="ISO-8859-1"?>
    	<ShipmentAcceptRequest>
    		<Request>
    			<TransactionReference>
    				<CustomerContext>Customer Comment</CustomerContext>
    			</TransactionReference>
    			<RequestAction>ShipAccept</RequestAction>
    			<RequestOption>1</RequestOption>
    		</Request>
    		<ShipmentDigest>$shipDes</ShipmentDigest>
    	</ShipmentAcceptRequest>
EOF;
        $accept_curl    = "https://onlinetools.ups.com/ups.app/xml/ShipAccept";                         //生产环境地址
//         $accept_curl    = "https://wwwcie.ups.com/ups.app/xml/ShipAccept";                              //测试环境地址
        $accept_result  = $this->sendRequest($requestXml_accetp, $accept_curl);
        if (FALSE === $accept_result) {
        	return FALSE;
        }
        $accept_parseResult = $this->parseXMLResult($accept_result);
        if (FALSE === $accept_parseResult) {
        	return FALSE;
        }
//         print_r($accept_parseResult);
        
        if ($accept_parseResult->Response->ResponseStatusDescription == 'Failure') {                              //返回错误
            self::$errMsg   = strval($accept_parseResult->Response->Error->ErrorDescription);
            return FALSE;
        }
        
        $trackNumber    = strval($accept_parseResult->ShipmentResults->PackageResults->TrackingNumber);                 //跟踪号
        $totalMoney     = strval($accept_parseResult->ShipmentResults->ShipmentCharges->TotalCharges->MonetaryValue);   //运费
        $Base64LabelImage   = strval($accept_parseResult->ShipmentResults->PackageResults->LabelImage->GraphicImage);   //label数据
        $labelContent       = base64_decode($Base64LabelImage);
        $labelSavePath      = $this->generatePathStr($orderId, 'ups/');
        $labelSavePath     .= "$orderId.gif";
        $saveResult     = $this->saveLabelPic($labelSavePath, $labelContent);
        if (FALSE === $saveResult) {
            $this->errCode  = 8002;
            $this->errMsg   = 'label文件存储出错 --trackNumber -- '.$trackNumber;
        	return FALSE;
        }
        $returnData = array('trackNumber'=>$trackNumber, 'shippFee'=>$totalMoney, 'imagePath'=>$labelSavePath);
        return $returnData;
    }

}
