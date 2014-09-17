<?php
/*
 * 从线上获取message信息   针对ebay
 */
require_once WEB_PATH . 'lib/ebaylibrary/GetMemberMessages.php';//订单抓取脚本
require_once WEB_PATH . 'lib/xmlhandle.php';    //xml处理脚本
require_once WEB_PATH . 'lib/ebay_order_cron_func.php';    //公用处理函数
class FetchMessageModel{
    /*
     * 构造函数
     */
    private $dbconn = null;
    public static $errCode = 0;
    public static $errMsg  = '';
    
    public function __construct(){
        global $dbConn;
    }
    
    /*
     * 获取ebaymessage
     * $messageid int messageid号
     * $account   string 账号信息
     * 返回值
     * 成功 返回message文件路径
     * 失败返回false
     */
    public function fetchMessageBody($messageid, $account){
        /*----- 加载token文件 -----*/
        $tokenfile = WEB_PATH.'lib/ebaylibrary/keys/keys_'. $account . '.php';
        if (!file_exists($tokenfile)) { //授权文件不存在
        	self::$errCode = 10050;
        	self::$errMsg  = '账号授权文件不存在';
        	return FALSE;
        }
        include_once ''.$tokenfile;
        /*----- 导出为全局变量 ugly code -----*/
        $GLOBALS['siteID']              = $siteID;
        $GLOBALS['production']          = $production;
        $GLOBALS['compatabilityLevel']  = $compatabilityLevel;
        $GLOBALS['devID']               = $devID;
        $GLOBALS['appID']               = $appID;
        $GLOBALS['certID']              = $certID;
        $GLOBALS['serverUrl']           = $serverUrl;
        $GLOBALS['userToken']           = $userToken;
        /*----- 导出为全局变量 -----*/
        
        /*----- 加载token文件 -----*/
        
        $getmsgobj = new GetMemberMessagesAPI($account);
        $responseXml  = $getmsgobj->requestMessagesID($messageid);
        //var_dump($responseXml);exit;
        if (stristr($responseXml, 'HTTP 404') || $responseXml == ''){
            self::$errCode = 10051;
            self::$errMsg  = '获取失败!';
            return FALSE;
        }
        $responseDoc = new DomDocument();
        $responseDoc->loadXML($responseXml);
        $data = XML_unserialize($responseXml);
        //print_r($data);exit;
        $Content = $data['GetMyMessagesResponse']['Messages']['Message']['Text'];
        if (empty($Content)){
            self::$errCode = 10051;
            self::$errMsg  = '获取失败!';
            return FALSE;
        }
        $date = date('Y-m-d', time());
        $filepath = MSGBODYSAVEPATH . $account . '/' . $date . '/' . $messageid . '.html';
        if (write_a_file(MSGREALPREFIX.$filepath, $Content) === false) {
            self::$errCode = 10052;
            self::$errMsg  = 'message文件保存失败!';
            return FALSE;
        }
        return $filepath;
    }
}

?>