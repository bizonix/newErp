<?php
/*
 * 申请 usps label
 */
class ApplyUSPSLabelModel extends ExpressLabelApplyModel {
    public static  $errCode    = 0;
    public static  $errMsg     = '';
    private $requestId      = "llmt";                               
    private $passPhrase     = "Shenzhensailvan168";
    private $accoutId       = 894869;      
    
    /*
     * 构造函数
     */
    public function __construct(){
        parent::__construct();
    }
    
    /*
     * 申请 usps label
    */
    public function aplyUSPSLabel($data, $typeInfo){
        $recipients     = isset($data['recipients']) ? $this->strreplace($data['recipients']) : '';                     //收货人
        $re_phone       = isset($data['re_phone'])   ? $this->strreplace($data['re_phone'])   : '';                     //收货人电话
        $re_address1    = isset($data['re_address1'])? $this->strreplace($data['re_address1']): '';                     //收件人地址1
        $re_address2    = isset($data['re_address2'])? $this->strreplace($data['re_address2']): '';                     //收件人地址2
        $re_city        = isset($data['re_city'])    ? $this->strreplace($data['re_city'])    : '';                     //收件人城市
        $re_state_code  = isset($data['re_state_code']) ? $data['re_state_code'] : '';                                  //收件人所在州简码
        $re_post_code   = isset($data['re_post_code'])  ? $data['re_post_code']  : '';                                  //收件人邮编
        $weight         = isset($data['weight'])         ?$data['weight'] : '';                                         //重量 KG
        $weight_oz      = ceil($this->kg2ounce($weight));                                                               //kg转盎司
        
        if(strpos($re_post_code,"-") === false){
        }else{
            $postcode_arr 	= explode("-",$re_post_code);
            $re_post_code 		= $postcode_arr[0];
        }
        
        //其他信息
        $orderId        = isset($data['orderId'])       ? $data['orderId']      : '';                                    //订单号
        $sku_position   = isset($data['sku_position'])  ? $data['sku_position'] : '';                                    //位置
        $show_detail    = isset($data['show_detail'])   ? $data['show_detail']  : '';                                    //详细
    
        $requestId      = $this->requestId;
        $passPhrase     = $this->passPhrase;
        $accoutId       = $this->accoutId;
        
        $mailClass      = $typeInfo['mailClass'];                                                                         //运输类型
        $packageType    = $typeInfo['packageType'];                                                                       //包裹类型
        
        $MailpieceShape = empty($packageType) ? '' : "<MailpieceShape>$packageType</MailpieceShape>" ;
        $LabelType      = '';
        if ($packageType == 'Letter') {
        	$LabelType = 'LabelType="DestinationConfirm"';
        }
        
        $requestXML     = <<<EOF
     <LabelRequest ImageFormat="GIF" Test="NO" $LabelType >
        <RequesterID>$requestId</RequesterID>
        <AccountID>$accoutId</AccountID>
        <PassPhrase>$passPhrase</PassPhrase>$MailpieceShape<MailClass>$mailClass</MailClass>
        <DateAdvance>0</DateAdvance>
         <WeightOz>$weight_oz</WeightOz>
         <Stealth>TRUE</Stealth>
    	 <ValidateAddress>FALSE</ValidateAddress>
         <Services InsuredMail="OFF" SignatureConfirmation="OFF" />
         <Value>0</Value>
         <Description>Sample Label</Description>
         <PartnerCustomerID>12345ABCD</PartnerCustomerID>
         <PartnerTransactionID>6789EFGH</PartnerTransactionID>
         <ToName>$recipients</ToName>
         <ToCompany>United States Postal Service</ToCompany>
         <ToAddress1>$re_address1</ToAddress1>
         <ToAddress2>$re_address2</ToAddress2>
         <ToCity>$re_city</ToCity>
         <ToState>$re_state_code</ToState>
         <ToPostalCode>$re_post_code</ToPostalCode>
         <ToPhone>$re_phone</ToPhone>
         <FromName>LINEMART INC</FromName>
         <FromCompany>LINEMART INC</FromCompany>
         <ReturnAddress1>16518 E. Gale Ave</ReturnAddress1>
         <FromCity>City of Industry</FromCity>
         <FromState>CA</FromState>
         <FromPostalCode>91745</FromPostalCode>
         <FromZIP4>1864</FromZIP4>
         <FromPhone></FromPhone>
    	 <RubberStamp1>SN:$orderId</RubberStamp1>
    	 <RubberStamp2>$show_detail</RubberStamp2>
    	 <RubberStamp3>$sku_position</RubberStamp3>
    </LabelRequest>
EOF;
//     echo $requestXML;exit;
        $postData       = array('labelRequestXML'=>$requestXML);
        
        $strGetLabelURL = "https://LabelServer.Endicia.com/LabelService/EwsLabelService.asmx/GetPostageLabelXML";       //生产线
//         $strGetLabelURL = "https://www.envmgr.com/LabelService/EwsLabelService.asmx/GetPostageLabelXML";                    //测试
        $params = array('http' => array(
                'method' 	=> 'POST',
                'content' 	=> 'labelRequestXML='.$requestXML,
                'header' 	=> 'Content-Type: application/x-www-form-urlencoded')
        );
        
        $ctx 	= stream_context_create($params);
        $fp  	= fopen($strGetLabelURL, 'rb', false, $ctx);
        if (!$fp) {
            self::$errMsg   = "发送申请失败!!!";
            return FALSE;
        }
        $response = stream_get_contents($fp);
        
        if (FALSE == $response) {
        	self::$errMsg   = "发送申请失败!!!";
            return FALSE;
        }
        
        $parseResult    = $this->parseXMLResult($response);                                                             //解析结果
        if (FALSE === $parseResult) {
        	return FALSE;
        }
        
//         var_dump($parseResult);
        
        $status = intval($parseResult->Status);
//         echo $status, 'xx';
        if ($status != 0) {                             //结果部位0 则表示有错
            self::$errMsg   = strval($parseResult->ErrorMessage);
        	return FALSE;
        }
//         var_dump(strval($parseResult->Base64LabelImage));
        $Base64LabelImage	= base64_decode(strval($parseResult->Base64LabelImage));                                              //获得标签图片
        $trackNumber        = strval($parseResult->TrackingNumber);                                                               //跟踪号
        $totalMoney         = strval($parseResult->FinalPostage);                                                                 //运费
        $labelSavePath      = $this->generatePathStr($orderId, 'usps/');
        $labelSavePath      = $labelSavePath."$orderId.gif";
        $saveResult         = $this->saveLabelPic($labelSavePath, $Base64LabelImage);
        if (FALSE === $saveResult) {
            $this->errCode  = 8002;
            $this->errMsg   = 'label文件存储出错 --trackNumber -- '.$trackNumber;
            return FALSE;
        }
        $returnData = array('trackNumber'=>$trackNumber, 'shippFee'=>$totalMoney, 'imagePath'=>$labelSavePath);
        return $returnData;
    }
    
    /*
     * usps 退款
     * 成功返回TRUE 失败返回false
     */
    public function refoundUSPS($trackNumber){
        $requestId      = $this->requestId;
        $passPhrase     = $this->passPhrase;
        $accoutId       = $this->accoutId;
        $url            = "https://www.endicia.com/ELS/ELSServices.cfc?wsdl";
        
        $requestXML = <<<EOF
        <RefundRequest>
        <AccountID>$accoutId</AccountID>
        <PassPhrase>$passPhrase</PassPhrase>
        <Test>N</Test>
        <RefundList> 
        <PICNumber>$trackNumber</PICNumber> 
        </RefundList>
        </RefundRequest>
EOF;
        echo $requestXML, "\n\n";
        
        $client 		= new SoapClient($url);
        $result 		= $client->RefundRequest($xml);
        $value   		= $result->RefundResponse->RefundList->PICNumber->IsApproved;
        print_r($result);
        
        $value  = $parsedResult->RefundResponse->RefundList->PICNumber->IsApproved;
        if ($value == 'YES') {
        	return TRUE;
        } else {
            return FALSE;
        }
    }
}

?>