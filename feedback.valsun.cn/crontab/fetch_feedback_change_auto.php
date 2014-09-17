<?php 
/**
 * 根据客服申请要修改的评价，拉取线ebay平台卖家留的最终评价与本地数据
 * 比较，修改本地的评价为最新的评价
 * add by 姚晓东
 */
	@session_start();
	error_reporting(-1);
	//$_SESSION['user'] = $user;
	date_default_timezone_set('Asia/Shanghai');
	require_once "scripts.comm.php";
	require_once "config1.php";
	if($argc !=2)
	{
		exit("Usage: /usr/bin/php $argv[0] eBayAccount");
	}

	$startTime    = strtotime(date('Y-m-d 08:00:00'));
	$endTime      = strtotime(date('Y-m-d 09:30:00'));
	$nowTime      = strtotime(date('Y-m-d H:i:s'));
	
	if($nowTime > $startTime && $nowTime < $endTime){
		exit('此时间段不执行');
	}
	
	
	$ebay_account  = trim($argv[1]);
	global $devID,$appID,$certID,$serverUrl,$siteID,$detailLevel,$compatabilityLevel,$dbcon,$user;
	$accAct 	 = new AccountAct();
	$accountInfo = $accAct->act_getAccountList('token',"where account = '{$ebay_account}' and is_delete = 0");
	$token		 = $accountInfo[0]['token'];
	$verb        ='GetFeedback';
	$FBAct 	     = new EbayFeedbackAct();		
	$info	     = $FBAct->act_getRequestChangeList('*'," where modifyStatus=0 and account='$ebay_account'");
	$count1	     = count($info);
	
	for($kk=0; $kk<$count1; $kk++)//请求次数
	{
		$id  		    = $info[$kk]['id'];
		echo "-----update fb_request_change_ebay {$id}----\n";
		$ebay_userid    = trim($info[$kk]['ebayUserId']);
		$where          = " where account='{$ebay_account}' and CommentingUser ='{$ebay_userid}' and (CommentType='Neutral' or CommentType='Negative') ";
		$field	        = " FeedbackID,CommentText,CommentingUser,ItemID,TransactionID,CommentType ";
		$get_info       = $FBAct->act_getOrderList($field,$where);
		$orderNum       = count($get_info);
		for($ii=0; $ii<$orderNum; $ii++)//同一用户留下评价的条数
		{  
			$status = "";
			$feedbackID 	= $get_info[$ii]['FeedbackID'];
			$commentingUser = $get_info[$ii]['CommentingUser'];
			$itemID 		= $get_info[$ii]['ItemID'];
			$transactionID  = $get_info[$ii]['TransactionID'];
			$commentType 	= $get_info[$ii]['CommentType'];
			$commentText 	= $get_info[$ii]['CommentText'];
			$requestXmlBody = '<?xml version="1.0" encoding="utf-8"?> 
								<GetFeedbackRequest xmlns="urn:ebay:apis:eBLBaseComponents"> 
									<RequesterCredentials> 
										<eBayAuthToken>'.$token.'</eBayAuthToken> 
									</RequesterCredentials>
									<ItemID>'.$itemID.'</ItemID>
									<TransactionID>'.$transactionID.'</TransactionID>
									<UserID>'.$commentingUser.'</UserID>
									<FeedbackType>FeedbackReceived</FeedbackType>
									<DetailLevel>ReturnAll</DetailLevel>
								</GetFeedbackRequest>';	
			$session = new eBaySession($token, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $siteID, $verb);
			$responseXml = $session->sendHttpRequest($requestXmlBody);
			if(stristr($responseXml, 'HTTP 404') || $responseXml == '') return 'id not found';
			$data  = XML_unserialize($responseXml);
			$ack	         = $data['GetFeedbackResponse']['Ack'];	
			$feedbackRevised = $data['GetFeedbackResponse']['FeedbackDetailArray']['FeedbackDetail'];
			$feedbackRevised = $feedbackRevised[0]['FeedbackRevised'];
			if($ack !="Success")
			{
				echo "failed\n";
			}
			else
			{
					$feedback		 = $data['GetFeedbackResponse']['FeedbackDetailArray']['FeedbackDetail'];
					//$feedbackType	 = isset($feedback[0]['CommentType']) ? $feedback[0]['CommentType'] : $feedback['CommentType'];
					if($feedback['CommentType']){
						$feedbackType    = $feedback['CommentType'];
					}else{
						foreach ($feedback as $value) {
							if($value['Role']=='Buyer'){
								$feedbackType    = $value['CommentType'];
							}
						}
					}
					$feedbackUser	 = $feedback[0]['CommentingUser'];
					if($commentingUser == $feedbackUser)
					{
						$feedbackText	= addslashes(str_rep($feedback[0]['CommentText']));
					}
					else
					{
						$feedbackText	= addslashes(str_rep($commentText));
					}
					if($commentType != $feedbackType && $feedbackType)
					{
						if($commentType == "Neutral")
						{
							if($feedbackType == "Positive")
							{
								$status = "21"; //中评改好评
							}
							else if($feedbackType == "Negative")
							{
								$status = "23"; 
							}
							else
							{
								$status = "22";
							}
						}
						else if($commentType == "Negative")
						{
							if($feedbackType == "Positive")
							{
								$status = "31"; 
							}
							else if($feedbackType == "Neutral")
							{
								$status = "32"; 
							}
							else
							{
								$status = "33"; 
							}
						}
						
						if($status != "")
						{						
							$data = array(
									'status'		=> $status,
									'CommentType' 	=> $feedbackType,
									'CommentText'	=> $feedbackText,
									'FeedbackID'	=> $feedbackID,
							);
							$upd = EbayFeedbackModel::requestChangeUpateStatus($data,$id,$ebay_account,$commentingUser);
							if ($upd) {
								echo "Success!\n";
							} else {
								echo "Failure!\n";
							}	
							echo 'userID :'.$commentingUser.":".$commentType."----->".$feedbackType."\n";
						}
					}else{
						echo 'userID:'.$commentingUser.' no change feedback'."\n\n";
					}
			}
			
		}//end of 可以修改的 fb_request_change_ebay条数
		/* $updataNum --;//执行一个 fb_request_change_ebay表对于的 Id，updataNUm减一
		if($updataNum>0){//应对同一用户留下多个评价  执行一个 ID 时把所有的评价改回来了，但对应得请求修改表的记录没有置为1，改的还是第一次循环的Id.
			$upres    = EbayFeedbackModel::resetRequestChange($id);
			if($upres){
				echo "$id 更新成功";
			}else{
				echo "$id 更新失败";
			}
			$updataNum --;//执行一次，updataNUm减一
		} */
	}
	exit();
?>
