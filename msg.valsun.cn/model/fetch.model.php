<?php
/*
 * 抓取msg数据
 */
class FetchModel
{
    private $dbconn = null;    
    public static $errMsg = '';
    public static $errCode = 0;

    /*
     * 构造函数
     */
    public function __construct() {
        global $dbConn;
        $this->dbconn = $dbConn;
    }
    
    /*
     * 批量抓取message
     */
    public function GetMemberMessages($start, $end, $account, $type, $idlimit=FALSE) {
        $api_messages=new GetMemberMessagesAPI($account);
        $patch = MSGBODYSAVEPATH;
        $pcount = 0;
        while ( ($pcount++) < 50 ) {      //一次最多抓取八页内容
//             echo $pcount, "\n";continue;
            /*----- 抓取message并解析数据 -----*/
            $responseXml = $api_messages->request($start, $end, $pcount, $account);  //发送抓取请求
            
            if (stristr($responseXml, 'HTTP 404') || $responseXml == ''){
                self::$errCode  = 5001;
                self::$errMsg   = '抓取数据失败 in code line --'.__LINE__;
                return FALSE;
            }
//             echo $responseXml, "\n\n";
            $responseDoc = new DomDocument();
            $responseDoc->loadXML($responseXml);
            $data = XML_unserialize($responseXml);
            /*----- 抓取message并解析数据 -----*/
            
            /*----- 根据返回结果记录log -----*/
            $Ack = $data['GetMyMessagesResponse']['Ack'];
            if ($Ack == '' || $Ack != 'Success' ) {
                echo $responseXml;
                echo "\n".'-- 获取数据失败 --'.$account . '  ' . $Ack .' at line '.__LINE__. "\n";
//                 $pcount++;
                continue;
            }
            
            $mctime = time();
            
            /* ----- 判断返回结果里面是否包含了message ----- */
            if ( !(is_array($data['GetMyMessagesResponse']['Messages']))
                 || empty($data['GetMyMessagesResponse']['Messages']['Message'])) {
            	$Trans = array();
            } else {
                $Trans = $data['GetMyMessagesResponse']['Messages']['Message'];
                $Sender = $data['GetMyMessagesResponse']['Messages']['Message']['Sender'];
                if ($Sender != '') {
                    $Trans = array();
                    $Trans[0] = $data['GetMyMessagesResponse']['Messages']['Message'];
                }
            }
            /* ----- 判断返回结果里面是否包含了message ----- */
            
            foreach ($Trans as $Transaction) {      //循环抓取message内容
                $Read               = $Transaction['Read'] ? 1 : 0;
                $HighPriority       = $Transaction['HighPriority'];
                $Sender             = $Transaction['Sender'];   
                $MessageID          = $Transaction['MessageID'];
                $RecipientUserID    = $Transaction['RecipientUserID'];
                $Subject            = str_rep($Transaction['Subject']);
                $MessageType        = $Transaction['MessageType'];
                $Replied            = $Transaction['Replied'];
                $ItemID             = $Transaction['ItemID'];
                $ExternalMessageID  = $Transaction['ExternalMessageID']; // 之前的id
                $ReceiveDate        = $Transaction['ReceiveDate'];
                $ItemTitle          = str_rep($Transaction['ItemTitle']);
                $createtime1        = strtotime($ReceiveDate);
                $date               = date('Y-m-d', strtotime("$ReceiveDate + 8 hours"));
                if ($idlimit !== FALSE) {
                	if ($idlimit >= $MessageID) {
                		echo 'in limit -- '.$MessageID."\n";
                		continue;
                	}
                }
                $check_sql = "select id from msg_message where message_id='$MessageID' ";
                $res = $this->dbconn->query($check_sql);
                $checkresult = $this->dbconn->fetch_array_all($res);                //获取结果集
                if (count($checkresult) == 0) {                                     //判断该message之前是否已经被抓取过了
                    if ($Replied == 'false') {
                        $responseXml = $api_messages->requestMessagesID($MessageID);
//                         echo $responseXml, "\n";
                        $www         = $responseXml;
                        if (stristr($responseXml, 'HTTP 404') || $responseXml == ''){
                            self::$errCode = 5000;
                            self::$errMsg  = '获取message信息失败 in code line ---'.__LINE__;
                            continue;
                        }
                        $responseDoc = new DomDocument();
                        $responseDoc->loadXML($responseXml);
                        $data = XML_unserialize($responseXml);
                        //print_r($data);exit;
                        $Content = $data['GetMyMessagesResponse']['Messages']['Message']['Text'];
                        $status = 0;
                        $forms = 0;
                        $classid = '0';
                        $case_sendid = '';
                        $disputeid = '';
                        $official  = array('eBay','csfeedback@ebay.com');
                        if (in_array($Sender, $official)) {                                             //系统邮件 不用理会
                            $classid    = 415;
                            $forms      = 2;
                        } else {
                            $first = substr($Sender, 0, 1);
                            $ss = "select id from msg_messagecategory where rules like '%$first%' and ebay_account ='$account'";
                            $rear = $this->dbconn->fetch_array_all($this->dbconn->query($ss));
                            if (count($rear) > 0) {
                                $classid = $rear[0]['id'];
                            } else {
                                $classid = -1;
                            }
                        }
//                         echo $classid, "\n";
                        if ($HighPriority == 'true') {
                            $forms = 3;
                        }
                        
                        $filepath = $patch . $account . '/' . $date . '/' . $MessageID . '.html';   //文件存储路径
                        
                        $sql = "INSERT INTO `msg_message` (`message_id` , `message_type` ,  `recipientid` ";
                        $sql .= ",  `sendid` , `subject` , `itemid` , ";
                        $sql .= "`title` , `createtime` ,  `add_time` , `ebay_account`,`classid`,`createtimestamp`,`status`,`forms`,`Read`,`ExternalMessageID`,`case_sendid`,`disputeid`, `filepath`)VALUES ('$MessageID', '$MessageType' ,";
                        $sql .= "  '$RecipientUserID' ,  '$Sender' , '$Subject' , '$ItemID' , ";
                        $sql .= "  '$ItemTitle' , '$ReceiveDate' , '$mctime', '$account','$classid','$createtime1','$status','$forms','$Read','$ExternalMessageID','$case_sendid','$disputeid', '$filepath') ";
                        if ($this->dbconn->query($sql)) {
                            echo "$MessageID Add Success" . "\n";
                            if (write_a_file(MSGREALPREFIX.$filepath, $Content) === false) {
                            }
                        } else {
                            echo "$MessageID Add Failure" . "\n";
                        }
                    }
                } else {
                    echo $MessageID . ' -- has exists' . "\n";
                }
            }
            
            if (count($Trans) < 199) {
                break;
            }
        }
    }
    
    /*
     * 根据messageid获得message信息
     */
    public function getMessageInfoByMsgId($msgid){
        $sql = "select createtime1 from msg_message where message_id='$msgid'";
        $row = $this->dbconn->fetch_first($sql);
        return $row;
    }
    
}

