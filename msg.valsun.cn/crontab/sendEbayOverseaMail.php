<?php
date_default_timezone_set('Asia/Shanghai');
include_once __DIR__.'/../framework.php'; // 加载框架
Core::getInstance(); // 初始化框架对象
include_once WEB_PATH . 'crontab/scriptcommon.php';  //脚本公共文件
include_once WEB_PATH . 'lib/opensys_functions.php';
include_once WEB_PATH . 'lib/xmlhandle.php';
set_time_limit(0);
error_reporting(0);
$tpl_obj   		= new CommonModel('msg_ebaycstpl');
$Orderlist      = getOpenSysApi(OPENGETOVERSEAORDER, array('vitualarg'=>'v')); 
$UPS            = $Orderlist['data1']; //需要以UPS、USPS、SurePost模板发送的相关订单
$Letter         = $Orderlist['data2']; //需要以Letter模板发送的相关订单
$AllOrder       = array_merge($UPS,$Letter);
//print_r($AllOrder);
//根据不同订单使用对应模板发送站内信

foreach ($AllOrder as $k=>$v) {
	switch($v['ebay_carrier']) {
		case "SurePost":
			$Track_website	= "www.ups.com";
			break;
		case "UPS":
			$Track_website	= "www.ups.com";
			break;
		case "USPS":
			$Track_website	= "www.usps.com";
			break;
		default:
			$Track_website	= "";
	}
	//根据跟踪号长度获取对应回复内容模板
	if(strlen($v['ebay_tracknumber']) == 31) {
		$trackTempl			= $tpl_obj->findOne('*', " WHERE `id` = '41' AND `is_delete` = 0"); //Letter模板
	}else{
		$trackTempl			= $tpl_obj->findOne('*', " WHERE `id` = '40' AND `is_delete` = 0");
	}
	//处理模板内容
	$buyer_id					= $v['ebay_userid'];
	$seller_id					= $v['ebay_account'];
	$trackNum					= isset($v['ebay_tracknumber']) ? $v['ebay_tracknumber'] : '';
	$item_id			        = isset($v['ebay_itemid']) ? $v['ebay_itemid'] : '';
	$content        	        = $trackTempl['content'];
	$content			        = str_replace('{buyer}', $buyer_id, $content);
	$content                    = str_replace('{tracknum}', $trackNum, $content);
	$content			        = str_replace('{url}', "<a href ='$Track_website'>".$Track_website.'</a>', $content);
	$content		 	        = str_replace("&","&amp;",$content);
	$returnData['itemid']   	= $item_id;
	$tokenFile  				= WEB_PATH.'lib/ebaylibrary/keys/keys_'.$seller_id.".php";
	if (!file_exists($tokenFile)) {
		$returnData['code']    	= 'fail';
		$returnData['msg']     	= 'Cound not find token!';
		echo json_encode($returnData);
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
	//推送邮件
	$result		 = sendEbayOverseaMail($item_id, $buyer_id, $content, $trackTempl['subject']);
	/* $rtnCode     = $result['rtn'];
	$rtnErrorMsg = $result['errMsg'];
	if (FALSE === $rtnCode) { //推送失败插入数据库
		$update	= $Data->insertEbayOrder($trackNum, $item_id, $buyer_id, $create_time, 1, $rtnErrorMsg);
		if($update === 'success'){
			echo '';
		}
	}  */
	
}
function sendEbayOverseaMail($itemId, $buyerId, $sendContent, $subject){
	/* include_once WEB_PATH.'lib/ebaylibrary/eBaySession.php';
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
	print_r($requestXML);
	$responseXml   = $session->sendHttpRequest($requestXML);
	if(stristr($responseXml, 'HTTP 404') || $responseXml == '' || $responseXml === FALSE) {
		$errMsg   = __METHOD__.'发送请求失败! in line '.__LINE__;
		return array('errMsg'=>$errMsg, 'rtn'=>FALSE);
	}
	$data	= XML_unserialize($responseXml);
	$ack	= $data['AddMemberMessageAAQToPartnerResponse'];
	$ack	= $ack['Ack'];
	if($ack == 'Success'){
		echo $buyerId.'-------'.$itemId.'-----success'."\n";
		return array('errMsg'=>'', 'rtn'=>TRUE);
	}else {
		$errMsg = $data['AddMemberMessageAAQToPartnerResponse']['Errors']['LongMessage'];
		echo $buyerId.'-------'.$itemId.'-----'.$errMsg."\n";
		return array('errMsg'=>$errMsg, 'rtn'=>FALSE);
	} */
}