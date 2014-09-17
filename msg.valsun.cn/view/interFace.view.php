<?php
/*
 * message 提供接口
 */
include WEB_PATH.'lib/xmlhandle.php';
class InterFaceView {
    /*
     * 构造函数
     */
    public function __construct(){
    }
    
    /*
     * 发送客服跟踪邮件
     */
    public function view_sendCsMail(){
        $returnData = array('code'=>'fail', 'msg'=>'', 'itemid'=>'');
        $itemId     = isset($_GET['itemId'])  ? trim($_GET['itemId']) : '';                 //ItemId
        $buyerId    = isset($_GET['userId'])  ? trim($_GET['userId']) : '';                 //买家id
        $seller     = isset($_GET['account']) ? trim($_GET['account']) : '';                //卖家ID
        $contryCode = isset($_GET['country']) ? trim($_GET['country']) : '';                //国家代码
        $buytime    = isset($_GET['paidTime'])? intval($_GET['paidTime'])   : '';           //购买时间
        $returnData['itemid']   = $itemId;
        
        if ( empty($itemId) || empty($contryCode)) {
        	$returnData['msg'] = '缺少参数';
        	echo json_encode($returnData);
        	exit;
        }
        
        $ecm_obj    = new EbayCsMailManageModel();
        $v_result   = $ecm_obj->validateSend($buyerId, $seller, $buytime, $itemId);                          //验证是否需要发送
        if (!$v_result) {
        	$returnData['msg'] = '无需发送';
        	$returnData['code'] = 'noneed';
        	echo json_encode($returnData);
        	exit;
        }
        
        $tokenFile  = WEB_PATH.'lib/ebaylibrary/keys/keys_'.$seller.".php";
//         echo $tokenFile;exit;
        if (!file_exists($tokenFile)) {
        	$returnData['code']    = 'fail';
        	$returnData['msg']     = '没找到账号token!';
        	echo json_encode($returnData);
        	exit;
        }
        include $tokenFile;
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
        
        $tpl_obj    = new CommonModel('msg_ebaycstpl');
        $tplRel_obj = new CommonModel('msg_ebaycsrel');
        $country    = CommonModel::transSafetySql(array($contryCode));
        $contryCode = $country[0];
        $row        = $tplRel_obj->findOne('*', "where countryCode='$contryCode'");
        if (empty($row)) {
            $returnData['code']    = 'fail';
            $returnData['msg']     = '未找到国家代码!';
            echo json_encode($returnData);
            exit;
        }
        
        $tplID  = $row['tplId'];
        $tplInfo    = $tpl_obj->findOne('*', " where id='$tplID'");
        if (!$tplInfo) {
            $returnData['code']    = 'fail';
            $returnData['msg']     = '没设置模板!';
            echo json_encode($returnData);
            exit;
        } else {
            $returnData['tplId']   = $tplID;
        }
        
        $result = $ecm_obj->sendEbayCsMail($itemId, $buyerId, $tplInfo['content'], $tplInfo['subject']);
//         $result = TRUE;
        if (FALSE === $result) {
            $returnData['code']    = 'fail';
            $returnData['msg']     = EbayCsMailManageModel::$errMsg;
            echo json_encode($returnData);
            exit;
        } else {
            $returnData['code']    = 'success';
            $returnData['msg']     = '处理成功!';
            echo json_encode($returnData);
        }
        
    }
    
    /*
     * 返回当前可以推送消息的接口
     */
    public function view_sendAccountInfo(){
        $mpush_obj  = new MailPushAlarmModel();
        include_once WEB_PATH.'lib/global_ebay_accounts.php';                   //导入ebay平台账号
        $account    = $mpush_obj->decisionPushAccount($GLOBAL_EBAY_ACCOUNT);
        echo json_encode($account);
    }
}

