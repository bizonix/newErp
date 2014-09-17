<?php 
	@session_start();
	header("Content-Type:text/xml"); 
	$user = 'vipchen';
	$_SESSION['user'] = $user;
	error_reporting(0);
    require_once 'config1.php';
	require_once 'ebay_order_cron_config.php';
    require_once 'ebaylibrary/eBaySession.php';
	if($argc !=3)
	{
		exit("Usage: /usr/bin/php $argv[0] eBayAccount userid");
	}
	$ebay_account  = trim($argv[1]);
	$userid        = trim($argv[2]);
	global $devID,$appID,$certID,$serverUrl,$siteID,$detailLevel,$compatabilityLevel,$dbcon,$user;
	
	$accAct 	 = new AccountAct();
	$accountInfo = $accAct->act_getAccountList('token',"where account = '{$ebay_account}' and is_delete = 0");
	$token		 = $accountInfo[0]['token'];
	
	$FBAct 	  = new EbayFeedbackAct();
	$info	  = $FBAct->act_getRequestChangeList('*'," where modifyStatus=0 and ebayUserId='$userid'");
	$count1	  = count($info);	
	$verb	  = 'GetFeedback';	
	
	for($kk=0; $kk<$count1; $kk++)
	{
		$id  		  = $info[$kk]['id'];
		$ebay_userid  = $info[$kk]['ebayUserId'];
		$ebay_account = $info[$kk]['account'];
		
		$where    = " where account='{$ebay_account}' and CommentingUser ='{$ebay_userid}' and (CommentType='Neutral' or CommentType='Negative') ";
		$field	  = " FeedbackID,CommentText,CommentingUser,ItemID,TransactionID,CommentType ";
		$get_info = $FBAct->act_getOrderList($field,$where);
		$orderNum = count($get_info);
		
		
		for($ii=0; $ii<$orderNum; $ii++)
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
								print_r($requestXmlBody);
			$session = new eBaySession($token, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $siteID, $verb);
			$responseXml = $session->sendHttpRequest($requestXmlBody);
			if(stristr($responseXml, 'HTTP 404') || $responseXml == '') return 'id not found';
			$data        = XML_unserialize($responseXml);
			//print_r($responseXml);
			$ack	         = $data['GetFeedbackResponse']['Ack'];	
			$feedbackRevised = $data['GetFeedbackResponse']['FeedbackDetailArray']['FeedbackDetail'];
			//print_r($feedbackRevised);
			$feedbackRevised = $feedbackRevised[0]['FeedbackRevised'];
			if($ack !="Success")
			{
				echo 'faile'."\n";
			}
			else
			{
				if($feedbackRevised == "true")
				{
					$feedback		 = $data['GetFeedbackResponse']['FeedbackDetailArray']['FeedbackDetail'];
					$feedbackType	 = $feedback[0]['CommentType'];
					$feedbackUser	 = $feedback[0]['CommentingUser'];
					if($commentingUser == $feedbackUser)
					{
						$feedbackText	= addslashes(str_rep($feedback[0]['CommentText']));
					}
					else
					{
						$feedbackText	= addslashes(str_rep($commentText));
					}
					if($commentType != $feedbackType)
					{
						if($commentType == "Neutral")
						{
							if($feedbackType == "Positive")
							{
								$status = "21"; //中评改好评
							}
							else if($feedbackType == "Negative")
							{
								$status = "23"; //中评改差评
							}
							else
							{
								$status = "22";//中评改中评
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
						else{}
						if($status != "")
						{
							/*$update_type = "update ebay_feedback set status='$status',CommentType='$feedbackType',CommentText='$feedbackText' where FeedbackID=$feedbackID";
							$sql = $update_type;
							echo $sql."\n";*/
							$data = array(
									'status'		=> $status,
									'CommentType' 	=> $feedbackType,
									'CommentText'	=> $feedbackText,
									'FeedbackID'	=> $feedbackID,
							);
							$upd = EbayFeedbackModel::requestChangeUpateStatus($data);
							if ($upd) {
								echo "Success!\n";
							} else {
								echo "Failure!\n";
							}	
							
							echo 'userID :'.$commentingUser.":".$commentType."---->".$feedbackType."\n";
						}
					}
				}
	
			}
			
		}
	}
?>
