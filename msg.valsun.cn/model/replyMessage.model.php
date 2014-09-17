<?php
/*
 * 回复message到系统
 */
class ReplyMessageModel
{
    private $dbconn = null;
    public static $errCode  = 0;
    public static $errMsg   = '';
    public static $sender   = null;
    public static $sendMsg  = '';
    
    /*
     * 构造函数
     */
    public function __construct(){
        global $dbConn ;
        $this->dbconn = $dbConn;
    }
    
    /*
     * 回复message内容
     * $msid id message表的主键id
     * $content string 回复message内容
     * $replyuer int  回复人的id
     * $mailsent bool 是否回复
     */
    public function replyMessage($msid, $content, $mailsent)
    {
        //return FALSE;
        //return rand(0, 1) ? TRUE : FALSE;
        /*----- 全局变量 -----*/
        global $devID, $appID, $certID, $serverUrl, $siteID,  $compatabilityLevel, $userToken;
        
        /* $v = array($devID, $appID, $certID, $serverUrl, $siteID,  $compatabilityLevel, $userToken);
        print_r($v);exit; */
        
        $compatabilityLevel = 657;
        /*----- 全局变量 -----*/
        
        /*----- 是否抄送到发送者 -----*/
        $copystatus = 'true';
        if ($mailsent == 1){
            $copystatus = 'true';
        } else {
            $copystatus = 'false';
        }
        //var_dump($mailsent);exit;
        /*----- 是否抄送到发送者 -----*/
        
        /*----- 获取messagei信息 -----*/
        $verb       = 'AddMemberMessageRTQ';
        $msg_obj    = new messageModel();
        $msginof    = $msg_obj->getMessageInfo(array($msid));
        //$msginof    = $msg_obj->getMessageInfoByMessageId($msid);
        if (empty($msginof)) {                                  //该message信息不存在 
        	self::$errCode = '8001';
        	self::$errMsg = 'message不存在';
        	return FALSE;
        }
        
        $msginof            = $msginof[0];                      //message信息数组
        $itemid             = $msginof['itemid'];               //itemid
        $message_id         = $msginof['message_id'];           //messageid
        $ExternalMessageID  = $msginof['ExternalMessageID'];    //扩展messageid
        $replyid            = $message_id;
        if ($ExternalMessageID != '')
            $replyid = $ExternalMessageID;
        $sendid             = $msginof['sendid'];
        $account            = $msginof['ebay_account'];
        /*----- 获取messagei信息 -----*/

        /* $content = str_replace("&", "&amp;", $content);
        $content = str_replace("\\", "", $content); */
        $content = htmlspecialchars($content);  //转义特殊字符
        
        $token = $userToken;                                    //账号对应的token
        if ( !isset($userToken) || (strlen($userToken)==0) ) {
        	self::$errCode = 8002;
        	self::$errMsg  = 'token数据缺失!';
        	self::$sender  = $msginof['replyuser_id'];
        	self::$sendMsg = 'token数据缺失! MSGID:'.$msid;
        	return FALSE;
        }
        
        $status = "1";
        
        $requestXmlBody = '<?xml version="1.0" encoding="utf-8"?>
		<AddMemberMessageRTQRequest xmlns="urn:ebay:apis:eBLBaseComponents">
    		<ItemID>' . $itemid . '</ItemID>
    		<MemberMessage>
        		<Body>' . $content . '</Body>
        		<EmailCopyToSender>' . $mailsent . '</EmailCopyToSender>
        		<ParentMessageID>' . $replyid . '</ParentMessageID>
        		<RecipientID>' . $sendid . '</RecipientID>
    		</MemberMessage>
    		<RequesterCredentials>
    		  <eBayAuthToken>' . $token . '</eBayAuthToken>
		    </RequesterCredentials>
		</AddMemberMessageRTQRequest>';
         //echo $requestXmlBody;exit;
        /*----- 发送请求 -----*/
        $session = new eBaySession($token, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $siteID, $verb);
        $responseXml = $session->sendHttpRequest($requestXmlBody);
        //echo $responseXml;exit;
        if (stristr($responseXml, 'HTTP 404') || $responseXml == '' || $responseXml === FALSE){
            self::$errCode  = 8003;  
            self::$errMsg   = __FILE__.'发送请求失败! in line '.__LINE__.' response  --'.$responseXml."\n";
            self::$sender   = $msginof['replyuser_id'];
            self::$sendMsg  = __FILE__.'发送请求失败! in line '.__LINE__.'MSGID:'.$msid ."\n";
            return FALSE;
        }
        /*----- 发送请求 -----*/
        
        $responseDoc = new DomDocument();
        $responseDoc->loadXML($responseXml);
        if ($responseDoc === FALSE) {                       //xml解析失败
        	self::$errCode     = 8004;
        	self::$sender      = $msginof['replyuser_id'];
            self::$errMsg      = __METHOD__.'xml解析失败! in line '.__LINE__.' response -- '.$responseXml."\n";
            self::$sendMsg     = __METHOD__.'xml解析失败! in line '.__LINE__.'MSGID: '.$msid;
            return FALSE;
        }
        $root   = $responseDoc->documentElement;
        $ack    = $responseDoc->getElementsByTagName('Ack');
        $reack  = $ack->item(0)->nodeValue;
        $content = str_rep($content);
        if ($reack == 'Success') {
            return TRUE;
        } else {
            self::$sender   = $msginof['replyuser_id'];
            self::$errMsg   = $requestXmlBody.'-----'.$responseXml.__LINE__."\n";
            $obj_return     = simplexml_load_string($responseXml);
            if ($obj_return && isset($obj_return->Errors->LongMessage)) {
            	self::$sendMsg = 'MSGID:'.$msid.'发送人：'.$sendid.' ++++ 原因 ：'.$obj_return->Errors->LongMessage;
            } else {
                self::$sendMsg  = 'MSGID:'.$msid.'发送人：'.$sendid.' ++++ '.'原因未知';
            }
            return FALSE;
        }
    }
    
    /*
     * 将消息标记为已经回复
     * $messageid  int messageid
     */
    public function markAsRead($messageid,$type='Read', $flag='') {
        //return TRUE;
        $msg_obj    = new messageModel();
        $msginof    = $msg_obj->getMessageInfo(array($messageid));
        $verb = 'ReviseMyMessages';
        global $devID, $appID, $certID, $serverUrl, $siteID,  $compatabilityLevel, $mctime, $userToken;
        $xmlRequest		= '<?xml version="1.0" encoding="utf-8"?>
			<ReviseMyMessagesRequest xmlns="urn:ebay:apis:eBLBaseComponents">
  			<WarningLevel>High</WarningLevel>
  			<MessageIDs>
    		<MessageID>'.$msginof[0]['message_id'].'</MessageID>
  			</MessageIDs>
        ';
        $xmlRequest		.='<Read>true</Read>';
        $xmlRequest		.='
  			<RequesterCredentials>
    		<eBayAuthToken>'.$userToken.'</eBayAuthToken>
  			</RequesterCredentials>
  			<WarningLevel>High</WarningLevel>
			</ReviseMyMessagesRequest>
			';
        $session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $siteID, $verb);
        $responseXml = $session->sendHttpRequest($xmlRequest);
		//echo $responseXml;exit;
        if(stristr($responseXml, 'HTTP 404') || $responseXml == '' || $responseXml === FALSE) {
            self::$errCode  = 9001;
            self::$sender   = $msginof[0]['replyuser_id'];
            self::$errMsg   = __METHOD__.'发送请求失败! in line '.__LINE__;
            self::$sendMsg  = __METHOD__.'发送请求失败! in line '.__LINE__;
            return FALSE;
        }
        $data	= XML_unserialize($responseXml);
        //print_r($data);
        $ack	= $data['ReviseMyMessagesResponse'];
        $ack	= $ack['Ack'];
        if ($ack == 'Success') {
            return TRUE;
        } else {
            self::$sender   = $msginof[0]['replyuser_id'];
            self::$errMsg = $xmlRequest.'-----'.$responseXml.__LINE__."\n";
            $obj_return     = simplexml_load_string($responseXml);
            if ($obj_return && isset($obj_return->Errors->LongMessage)) {
                self::$sendMsg = 'MSGID:'.$msginof[0]['message_id'].'发送人：'.$msginof[0]['sendid'].' ++++ 原因 ：'.$obj_return->Errors->LongMessage;
            } else {
                self::$sendMsg  = 'MSGID:'.$msginof[0]['message_id'].'发送人：'.$msginof[0]['sendid'].' ++++ '.'原因未知';
            }
            return FALSE;
        }
    }
}
