<?php
/*
 * ebay 收货邮件推送处理
 */
class EbayCsMailManageModel {
    public static $errCode  = 0;
    public static $errMsg   = '';
    private $dbConn         = NULL;
    
    /*
     * 构造函数
     */
    public function __construct(){
        global $dbConn;
        $this->dbConn   = $dbConn;
    }
    
    /*
     * 判断是否需要推送售后邮件
     */
    public function validateSend($buyer, $seller, $buyTime, $itemId){
        $buyer  = mysql_real_escape_string($buyer);
        $seller = mysql_real_escape_string($seller);
        $sql    = "select id from msg_message where sendid='$buyer' and ebay_account='$seller' and createtimestamp>=$buyTime";
//         echo $sql;exit;
        $row    = $this->dbConn->fetch_first($sql);
        if ($row) {
        	return FALSE;
        } else {
            return TRUE;
        }
    }
    
    /*
     * 推送ebay邮件
     */
    public function sendEbayCsMail($itemId, $buyerId, $sendContent, $subject){
        include_once WEB_PATH.'lib/ebaylibrary/eBaySession.php';
        global $devID, $appID, $certID, $serverUrl, $siteID,  $compatabilityLevel, $userToken;
        $verb    = 'AddMemberMessageAAQToPartner';
        $session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $siteID, $verb);
        $requestXML = <<<EOF
        <?xml version="1.0" encoding="utf-8"?>
        <AddMemberMessageAAQToPartnerRequest xmlns="urn:ebay:apis:eBLBaseComponents">
          <RequesterCredentials>
            <eBayAuthToken>$userToken</eBayAuthToken>
          </RequesterCredentials>
          <ItemID>$itemId</ItemID>
          <MemberMessage>
            <Subject>$subject</Subject>
            <Body>$sendContent</Body>
            <EmailCopyToSender>true</EmailCopyToSender>
            <QuestionType>General</QuestionType>
            <RecipientID>$buyerId</RecipientID>
          </MemberMessage>
        </AddMemberMessageAAQToPartnerRequest>
EOF;
        $responseXml   = $session->sendHttpRequest($requestXML);
        if(stristr($responseXml, 'HTTP 404') || $responseXml == '' || $responseXml === FALSE) {
            self::$errMsg   = __METHOD__.'发送请求失败! in line '.__LINE__;
            return FALSE;
        }

        $data	= XML_unserialize($responseXml);
        $ack	= $data['AddMemberMessageAAQToPartnerResponse'];
        $ack	= $ack['Ack'];
        if($ack == 'Success'){
        	return TRUE;
        } else {
            $err    = $data['AddMemberMessageAAQToPartnerResponse']['Errors']['LongMessage'];
            self::$errMsg   = '处理失败 ::ERR INFO ==> '.$err;
            return FALSE;
        }
    }
}

?>