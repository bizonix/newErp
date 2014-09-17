<?php
date_default_timezone_set('Asia/Shanghai');
include_once __DIR__.'/../framework.php'; // 加载框架
Core::getInstance(); // 初始化框架对象
include_once WEB_PATH . 'crontab/scriptcommon.php';  //脚本公共文件
include_once WEB_PATH . 'lib/opensys_functions.php';
include_once WEB_PATH . 'lib/xmlhandle.php';
set_time_limit(0);
error_reporting(-1);
$jsonp			= 1;
$where			= '';
$Data			= new sendEbayCaseMailModel();
$ecm_obj    	= new EbayCsMailManageModel();
$tpl_obj   		= new CommonModel('msg_ebaycstpl');
$BuyerList		= $Data->getBuyerMsg($where);

//根据导入时间的筛选买家信息
$BuyerData		= array();
if(isset($BuyerList) && !empty($BuyerList)){
	foreach($BuyerList as $keyData=>$valueData){
		$years			= date("Y");
		$months			= date("m");
		$dates			= date("d");
		$lastDay		= strtotime($years.$months.($dates-1)."000000");
		$today			= strtotime($years.$months.$dates."232359");
		if ($valueData['create_time'] >= $lastDay && $valueData['create_time'] <= $today ){
			$BuyerData[]	= $valueData;
			continue;
		}
	}
}else{
	continue;
}

//var_dump($BuyerList);
$returnData 	= array('code'=>'fail', 'msg'=>'', 'itemid'=>'');
$trackTempl		= array();
$trackNum		= array();
foreach($BuyerData as $k => $v){
	$trackArr               = array();
	$sellId					= $v['seller_id'];
	$itemId					= $v['item_id'];
	$tranSubject			= $v['transaction_item'];
	$id						= $v['id'];
	$tranDate				= date("Y-m-d", $v['transaction_date']);
	$delayArriveTime		= date('Y-m-d', $v['delay_arrive_time']);
	$buyerId				= $v['buyer_id'];
	$buyerTryOpenTime		= date('Y-m-d', $v['buyer_try_open_time']);
	$buyerCountry           = '';
	//调用接口获取订单信息
	$orderData				= getOpenSysApi(OPENGETORDER, array('type'=>'orderinfo','buyeraccount'=>$buyerId, 'selleraccount'=>$sellId));
	$orderMainData          = isset($orderData['data']) ? $orderData['data'] : '';
	if(!empty($orderMainData)){
		$mark  = 0;
		foreach($orderMainData as $kk => $vv){
			if($kk === 'totalbuy' || $kk === 'totalnum'){
				break;
			}
			$ebayId 			= isset($vv['ebay_id']) ? $vv['ebay_id'] : '';
			$trackNum 			= isset($vv['ebay_tracknumber']) ? $vv['ebay_tracknumber'] : '';
			$carrier  			= isset($vv['ebay_carrier']) ? $vv['ebay_carrier'] : '';
			$buyerCountry       = $vv['ebay_couny'];
			if(empty($trackNum)){
				$trackArr[] = 0;
			}else{
				$trackArr[] = 1;
			}
			$mark++;
		}
		//echo $buyerCountry;exit;
		if(!in_array(1, $trackArr)){//订单全部没有跟踪号
			$useArr[$buyerId.'#####'.$sellId]['trackNum'] 	= '';
			$useArr[$buyerId.'#####'.$sellId]['carrier'] 	= '';
			echo '无跟踪号';exit;
		}
		if(!in_array(0, $trackArr)){//订单全部有跟踪号,取第一个订单的跟踪号
			$userArr[$buyerId.'#####'.$sellId]['trackNum'] 	= isset($orderMainData[0]['ebay_tracknumber']) ? $orderMainData[0]['ebay_tracknumber'] : '';
			$userArr[$buyerId.'#####'.$sellId]['carrier'] 	= isset($orderMainData[0]['ebay_carrier']) ? $orderMainData[0]['ebay_carrier'] : '';
		}
		 $buyerCountry = $orderMainData[0]['ebay_couny'];
		$trackArrCount = count($trackArr);
		if($trackArrCount > 1){
			if(in_array(0, $trackArr) && in_array(1, $trackArr)){//订单存在有跟踪号且存在没有跟踪号,取最靠近的跟踪号
				for($ii = 0; $ii < $mark; $ii++){
					$firstTrackNum = $orderMainData[$ii]['ebay_tracknumber'];
					$firstCarrier  = $orderMainData[$ii]['ebay_carrier'];
					if(!empty($firstTrackNum)){
						$userArr[$buyerId.'#####'.$sellId]['trackNum'] = $firstTrackNum;
						$userArr[$buyerId.'#####'.$sellId]['carrier']  = $firstCarrier;
						break;
					}else{
						$userArr[$buyerId.'#####'.$sellId]['trackNum'] = '';
						$userArr[$buyerId.'#####'.$sellId]['carrier']  = '';
					}
				}
			}
		}
		$userArr[$buyerId.'#####'.$sellId]['id'] 					= $id;
		$userArr[$buyerId.'#####'.$sellId]['sellId'] 				= $sellId;
		$userArr[$buyerId.'#####'.$sellId]['itemId'] 				= $itemId;
		$userArr[$buyerId.'#####'.$sellId]['tranSubject'] 			= $tranSubject;
		$userArr[$buyerId.'#####'.$sellId]['tranDate'] 				= $tranDate;
		$userArr[$buyerId.'#####'.$sellId]['delayArriveTime'] 		= $delayArriveTime;
		$userArr[$buyerId.'#####'.$sellId]['buyerTryOpenTime'] 		= $buyerTryOpenTime;
		$userArr[$buyerId.'#####'.$sellId]['buyerCountry'] 		    = $buyerCountry;
	} else {
		$Data->updateIsSend('fail', $id,'木有订单信息');
	}
}

foreach ($userArr as $userKey=>$userValue) {
	switch($userValue['carrier']) {
		case "EUB":
			$Track_website	= "www.usps.com";
			break;
		case "中国邮政挂号":
			$Track_website	= "http://www.17track.net/index_en.shtml";
			break;
		case "UPS":
			$Track_website	= "www.ups.com";
			break;
		case "USPS":
			$Track_website	= "www.usps.com";
			break;
		case "DHL":
			$Track_website	= "www.dhl.com";
			break;
		case "FedEx":
			$Track_website	= "www.fedex.com";
			break;
		case "EMS":
			$Track_website	= "www.ems.com";
			break;
		case "俄速通":
			$Track_website	= "http://www.17track.net/index_en.shtml";
			break;
		default:
			$Track_website	= "";
	}
	//根据有无跟踪号获取对应回复内容模板
	if($userValue['trackNum'] != '' && $userValue['carrier'] != '' && $userValue['carrier'] != '中国邮政平邮') {
		$trackTempl			= $tpl_obj->findOne('*', " WHERE `id` = '38' AND `is_delete` = 0");
	}elseif($userValue['buyerCountry'] === 'DE'){//无跟踪号 德国
		$trackTempl			= $tpl_obj->findOne('*', " WHERE `id` = '42' AND `is_delete` = 0");
	}else{ //无跟踪号 非德国
		$trackTempl			= $tpl_obj->findOne('*', " WHERE `id` = '39' AND `is_delete` = 0");
	}
	//处理模板内容
	$buyerMsg			= array();
	$tranDateTime		= isset($userValue['tranDate']) ?  $userValue['tranDate'] : '';
	$buyerArr			= isset($userKey) ?  $userKey : '';
	$buyerMsg			= explode("#####", $buyerArr);
	$buyer_id			= $buyerMsg[0];
	$delay_arrive_time	= isset($userValue['delayArriveTime']) ?  $userValue['delayArriveTime'] : '';
	$seller_id			= isset($userValue['sellId']) ?  $userValue['sellId'] : '';
	$trackNums			= isset($userValue['trackNum']) ? $userValue['trackNum'] : '';
	$item_id			= isset($userValue['itemId']) ? $userValue['itemId'] : '';
	$sendtime			= date('Y-m-d',strtotime('+1 day',strtotime($tranDateTime)));
		
	$content        	= $trackTempl['content'];
	$content			= str_replace('{Buyer_id}', $buyer_id, $content);
	$content			= str_replace('{Shipped_date}', $sendtime, $content);
	$content			= str_replace('{Delivered_date}', $delay_arrive_time, $content);
	$content			= str_replace('{Seller_id}', $seller_id, $content);

	if($userValue['trackNum'] != '' && $userValue['carrier'] != '') {
		$content		= str_replace('{Track_website}',$Track_website, $content);
		$content		= str_replace('{TrackNum}',$userValue['trackNum'], $content);
	}
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
	$result		 = sendEbayCaseMail($item_id, $buyer_id, $content, $trackTempl['subject']);
	$rtnCode     = $result['rtn'];
	$rtnErrorMsg = $result['errMsg'];
	if (FALSE === $rtnCode) {
		$update	= $Data->updateIsSend('fail', $userValue['id'],$rtnErrorMsg);
	} else {
		$update	= $Data->updateIsSend('success', $userValue['id']);
	}
}
function sendEbayCaseMail($itemId, $buyerId, $sendContent, $subject){
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
	}
}