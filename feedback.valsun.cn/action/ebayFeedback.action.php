<?php
/**
*类名：ebayFeedback管理
*
*/
class EbayFeedbackAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";	
	
	//获取当前记录列表
	function  act_getOrderList($select ='*',$where){
		$list =	EbayFeedbackModel::getOrderList($select,$where);	
		if($list){
			return $list;
		}else{
			self::$errCode = EbayFeedbackModel::$errCode;
			self::$errMsg  = EbayFeedbackModel::$errMsg;
			return array();
		}
	}
	
	//获取当前记录数量
	function act_getOrderNum($where){
		//调用model层获取数据		
		$list =	EbayFeedbackModel::getOrderNum($where);	
		if($list){
			return $list;
		}else{
			self::$errCode = EbayFeedbackModel::$errCode;
			self::$errMsg  = EbayFeedbackModel::$errMsg;
			return false;
		}
	}
	
	//添加评价模板
	function act_feedbackOrderAdd($data){	
		$data = isset($_POST['account']) ? $data : '';		
		if ($data == '' ) {
			self::$errCode = '001';
			self::$errMsg  = "参数错误！";
			return false;
		}
		$ret1 = EbayFeedbackModel::insertRow($data);		
		return $ret1;		
	}
	
	function act_orderMutilDel(){
		//调用model层获取数据
		$billArr = $_POST['bill'];
		if(empty($billArr)){
			self::$errCode = '001';
			self::$errMsg  = "参数错误！";
			return false;
		}
		foreach ($billArr as $id) {
			$id  = trim($id);
			$ret = EbayFeedbackModel::orderMutilDel($id);
			if(!$ret) {
				self::$errCode = '002';
				self::$errMsg  .= "{$id}删除失败！<br>";
				return false;
			}
		}
		return 'ok';		
	}
	

	//获取当前记录列表
	function  act_getRequestChangeList($select ='*',$where){
		$list =	EbayFeedbackModel::getRequestChangeList($select,$where);	
		if($list){
			return $list;
		}else{
			self::$errCode = EbayFeedbackModel::$errCode;
			self::$errMsg  = EbayFeedbackModel::$errMsg;
			return array();
		}
	}

	//获取当前记录数量
	function act_getRequestChangeNum($where){
		//调用model层获取数据		
		$list =	EbayFeedbackModel::getRequestChangeNum($where);	
		if($list){
			return $list;
		}else{
			self::$errCode = EbayFeedbackModel::$errCode;
			self::$errMsg  = EbayFeedbackModel::$errMsg;
			return false;
		}
	}
	
	//获取当前记录数量
	function act_getEbayReasonCategoryInfo($select, $where){
		//调用model层获取数据
		$list =	EbayFeedbackModel::getEbayReasonCategoryInfo($select, $where);
		if($list){
			return $list;
		}else{
			self::$errCode = EbayFeedbackModel::$errCode;
			self::$errMsg  = EbayFeedbackModel::$errMsg;
			return false;
		}
	}
	
	//添加请求修改
	function act_requestChangeAdd(){
		//调用model层获取数据
		$account = isset($_POST['account']) ? trim($_POST['account']) : '';
		$userId  = isset($_POST['userId']) ? trim($_POST['userId']) : '';
		//print_r($_POST);
		if ($account == '' || $userId == '') {
			self::$errCode = '001';
			self::$errMsg  = "参数错误！";
			return false;
		}
		/* $ret1 = EbayFeedbackModel::checkChangeExist($account, $userId);
		if ($ret1) {
			self::$errCode = '002';
			self::$errMsg  = "修改请求已存在！";
			return false;
		} */
		$data = array(
				'account'		=>	$account,
				'ebayUserId'	=>	$userId,
				'addUser'		=>  $_SESSION['userCnName'],
				'addTime' 		=>  time(),
		);
		$ret = EbayFeedbackModel::requestChangeAdd($data);
		if (!$ret) {
			self::$errCode = '003';
			self::$errMsg  = "content插入失败！";
			return false;
		}
		return 'ok';
	}
	
	//统计账户报表
	function act_accountCount($accountCondition,$countCondition){
		$accAct 	 		= 	new AccountAct();
		$accountList 		= 	$accAct->act_getAccountList('id,account',"where platformId = 1 and token!='' and is_delete = 0 $accountCondition");
		$tName				=	"fb_comment_record_ebay";
		foreach($accountList as $accountInfo){
			$account		=	$accountInfo['account'];
			
			$PositiveSql		=	" where account='$account' and CommentType='Positive' $countCondition";
			$PositiveRes		=	OmAvailableModel::getTNameCount($tName, $PositiveSql);//好评数
			
			$NeutralSql			=	" where account='$account' and CommentType='Neutral' $countCondition";
			$NeutralRes			=	OmAvailableModel::getTNameCount($tName, $NeutralSql);//中评数
			
			$NegativeSql		=	" where account='$account' and CommentType='Negative' $countCondition";
			$NegativeRes		=	OmAvailableModel::getTNameCount($tName, $NegativeSql);//差评数
			
			$upNeutralsql		=	" where account='$account'  and status='21' $countCondition";
			$upNeutralRes		=	OmAvailableModel::getTNameCount($tName, $upNeutralsql);//中评修改数
			
			$upNegativeSql		=	" where account='$account' and status='31' $countCondition";
			$upNegetiveRes		=	OmAvailableModel::getTNameCount($tName, $upNegativeSql);//差评修改数
			
			$total				=	$PositiveRes + $NeutralRes*0.6 + $NegativeRes;//总评数
			$per_positive 		= 	$PositiveRes/$total;
			$per_positive 		= 	round($per_positive * 100,2);
			
			$countRes["$account"]		=	array("PositiveRes"=>$PositiveRes,
													   "NeutralRes"=>$NeutralRes,
													   "NegativeRes"=>$NegativeRes,
			                                           "upNeutralRes"=>$upNeutralRes,
			                                           "upNegetiveRes"=>$upNegetiveRes,
			                                           "total"=>$total,
			                                           "per_positive"=>$per_positive);
			
		}
		return $countRes;
		var_dump($countRes);exit;
	}
	
	//删除请求修改数据
	function act_requestChangeDel(){
		//调用model层获取数据
		$id = isset($_POST['id']) ? trim($_POST['id']) : '';	
		if ($id == '') {
			self::$errCode = '001';
			self::$errMsg  = "参数错误！";
			return false;
		}		
		$data = array(
				'is_delete'		=>	1,				
		);
		$ret = EbayFeedbackModel::requestChangeDel($id);
		if (!$ret) {
			self::$errCode = '002';
			self::$errMsg  = "删除失败！";
			return false;
		}
		return 'ok';
	}	
	
	function act_requestChangeMutilDel(){
		//调用model层获取数据		
		$billArr = $_POST['bill'];		
		if(empty($billArr)){
			self::$errCode = '001';
			self::$errMsg  = "参数错误！";
			return false;
		}		
		foreach ($billArr as $id) {
			$id  = trim($id);
			$ret = EbayFeedbackModel::requestChangeDel($id);
			if(!$ret) {
				self::$errCode = '002';
				self::$errMsg  .= "{$id}删除失败！<br>";	
				return false;
			}
		}		
		return 'ok';	
	}	
	function act_addsku(){
		$id				= 	isset($_POST['id']) ? post_check($_POST['id']) : '';
		$sku			= 	isset($_POST['sku']) ? post_check($_POST['sku']) : '';
		$amount			= 	isset($_POST['amount']) ? post_check($_POST['amount']) : '';
		$arr			=	array("sku"=>$sku,"Qty"=>$amount);
		$where			=	" and id = $id";
		//echo $where;exit;
		$ret = EbayFeedbackModel::update($arr,$where);
	}
	//重新获取sku 应对买家延迟付款
	function act_getsku(){
		$TransactionID		=	isset($_POST['TransactionID']) ? post_check($_POST['TransactionID']) : '';
		$TransactionID		=	trim($TransactionID,"´");
		$CommentingUser		=	isset($_POST['CommentingUser']) ? post_check($_POST['CommentingUser']) : '';
		$ItemID				=	isset($_POST['itemId']) ? post_check($_POST['itemId']) : '';
		$FeedbackID				=	isset($_POST['FeedbackID']) ? post_check($_POST['FeedbackID']) : '';
		$CommentType			=	isset($_POST['CommentType']) ? post_check($_POST['CommentType']) : '';
		$field = 'a.ebay_ordersn, a.ebay_paidtime, b.sku, b.ebay_amount';
		//echo "$CommentingUser,$ItemID,$TransactionID";
		$orderInfo = UserCacheModel::getErpOrderInfo($CommentingUser, $ItemID, $TransactionID, $field);
		//var_dump($orderInfo);exit;
		$ordersn    	= $orderInfo['data'][0]['ebay_ordersn'];//订单号
		$sku 			= $orderInfo['data'][0]['sku'];//料号
		$amount 		= $orderInfo['data'][0]['ebay_amount'];//数量
		$orderPayTime	= $orderInfo['data'][0]['ebay_paidtime'];//付款时间
		$data = array(
				'Qty' 			=> $amount,
				'sku' 			=> $sku,
				'orderPayTime' 	=> $orderPayTime,
		);
		EbayFeedbackModel::update($data," and FeedbackID = '$FeedbackID'");
		UserCacheModel::updateErpOrderInfoFeedback($ordersn, $ItemID, $TransactionID, $CommentType);
	}
	
	function act_ebayReasonSave(){	
		//print_r($_POST);
		$id				= isset($_POST['id']) ? post_check($_POST['id']) : '';
		$reasonId		= isset($_POST['reasonId']) ? post_check($_POST['reasonId']) : '';
		if ($id == '' || $reasonId == '') {
			self::$errCode = '001';
			self::$errMsg  = "参数错误！";
			return false;
		}
	
		$data 	= array('reasonId' => $reasonId);
		$where 	= " and id='$id' ";
		$ret	= EbayFeedbackModel::update($data,$where);
		if (!$ret) {
			self::$errCode = '002';
			self::$errMsg  = "原因保存失败！";
			return false;
		}
		return 'ok';
	}
	//修改买家评价
	function act_ebayRequestUpdate(){
		$successNum			= 0;
		$errordetail        = NULL;
		include  WEB_PATH."lib/feedback/ebaylibrary/ebay_config.php";
		$verb				=	'GetFeedback';
		$ebayUserId			=	isset($_POST['user_id'])?$_POST['user_id']:'';
		$account			=	isset($_POST['ebay_account'])?$_POST['ebay_account']:"";
		$select				=	"  `id` ";
		$where				=	" where `modifyStatus`=0 and `is_delete`=0 and ebayUserId='$ebayUserId' and `account`='$account' ";
		$info				=	EbayFeedbackModel::getRequestChangeList($select, $where);//获取请求修改列表 
		foreach($info as $value){
			$id			=	$value['id'];
			$select		=	" `token` ";
			$where 		=	"	where `account` = '$account' ";
			$token		=	AccountModel::getAccountList($select, $where);
			$token		=	$token[0]['token'];
			$select		=	" `FeedbackID`,`CommentText`,`CommentingUser`,`ItemID`,`TransactionID`,`CommentType` ";
			$where		=	" where account='$account' and CommentingUser ='$ebayUserId' ";//and (CommentType='Neutral' or CommentType='Negative')
			$get_info	=	EbayFeedbackModel::getOrderList($select, $where);//卖家评价信息
			//var_dump($get_info);
			foreach($get_info as $v){
				$status 			= 	"";
				$feedbackID 		= 	$v['FeedbackID'];
				$commentingUser 	= 	$v['CommentingUser'];
				$itemID 			= 	$v['ItemID'];
				$transactionID  	= 	$v['TransactionID'];
				$commentType 		= 	$v['CommentType'];
				$commentText 		= 	$v['CommentText'];
				$transactionID		=	html_entity_decode($transactionID);
				$transactionID		=	trim($transactionID,"´");
				//var_dump($transactionID);
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
				if(stristr($responseXml, 'HTTP 404') || $responseXml == '') {
					self::$errCode = '002';
					self::$errMsg  = "拉取eaby feedback 失败";
					return 'id not found';
				}
				
				$data        = XML_unserialize($responseXml);
				//print_r($responseXml);exit;
				$ack	         = $data['GetFeedbackResponse']['Ack'];
				$feedbackRevised = $data['GetFeedbackResponse']['FeedbackDetailArray']['FeedbackDetail'];
				//print_r($feedbackRevised);
				$feedbackRevised = $feedbackRevised[0]['FeedbackRevised'];
				if($ack !="Success")
				{
					self::$errCode = '002';
					self::$errMsg  = "拉取eaby feedback 失败";
					return false;
				}else{
					//var_dump($data,$feedbackRevised);exit;
					/* if(1 == "true")
					{ */
						$feedback		 	= 	$data['GetFeedbackResponse']['FeedbackDetailArray']['FeedbackDetail'];
						$feedbackType	 	= 	isset($feedback[0]['CommentType']) ? $feedback[0]['CommentType'] : $feedback['CommentType'];
						$feedbackUser	 	= 	$feedback[0]['CommentingUser'];
						if($commentingUser == $feedbackUser)
						{
							$feedbackText		= 	addslashes(str_rep($feedback[0]['CommentText']));
						}
						else
						{
							$feedbackText		= 	addslashes(str_rep($commentText));
						}
						$status		=	"";
						if($commentType 	!= 	$feedbackType && $feedbackType)//线上评价与本地评价不同
						{
							if($commentType == "Neutral")
							{
								if($feedbackType == "Positive")
								{
									$status 	= 	"21"; //中评改好评
								}
								else if($feedbackType == "Negative")
								{
									$status 	= 	"23"; //中评改差评
								}
								else
								{
									$status 	= 	"22";//中评改中评
								}
							}
							else if($commentType == "Negative")
							{
								if($feedbackType == "Positive")
								{
									$status 	= 	"31";
								}
								else if($feedbackType == "Neutral")
								{
									$status 	= 	"32";
								}
								else
								{
									$status 	= 	"33";
								}
							}
							else{}//好评情况不做处理
							if($status != "")
							{
								$tName		=	" fb_comment_record_ebay ";
								$set 		= 	" set status='{$status}',CommentType='{$feedbackType}',CommentText='{$feedbackText}'";
								$where		=	"  where FeedbackID='$feedbackID' ";
								$sql = "$tName $set $where ";
								//echo $sql."\n";
								if(OmAvailableModel::updateTNameRow($tName, $set, $where))
								{
									$tName		=	" fb_request_change_ebay ";
									$set		=	" set modifyStatus=1 ";
									$where		=	" where id =$id ";
									$sql		=	"$tName $set $where";
									$res		=	OmAvailableModel::updateTNameRow($tName, $set, $where);
									if($res){
										$successNum    +=1;
										//self::$errMsg  = "修改成功";
									}else{
										/* self::$errCode = '002';
										self::$errMsg  = "更新状态失败$sql"; */
									}
								}else{
									//echo 'Failure '."\n";
									/* self::$errCode = '002';
									self::$errMsg  = "更新评价失败！"; */
								}
								//echo 'userID :'.$commentingUser.":".$commentType."------------>".$feedbackType."\n";
							}/* else{
								self::$errCode = '002';
								self::$errMsg  = "客户评价没有修改！";
								return false;
							} */
						}else{//end if线上评价与本地评价不同
							$errordetail  .= "$commentingUser ";
						}
					/* }else{//end feedbackRevised =ture
						self::$errCode = '002';
						self::$errMsg  = "客户还没有修改评价！";
						return false;
					} */
				}//end of 获取ebay feedback接口成功
			}
			/* self::$errCode = '002';
			self::$errMsg  = "$ebayUserId  $account 系统不存此评价信息";
			return false; */
		}//foreach
		if($successNum>0){
			self::$errMsg  = "修改成功{$successNum}个";
			return true;
		}else{
			self::$errCode = '002';
			self::$errMsg  = "{$errordetail} 还未修改评价";
			return false;
		}
	}
	
	
	function act_feedbackReply(){
		include  WEB_PATH."lib/feedback/ebaylibrary/ebay_config.php";
			
		$feedbackid	= isset($_POST['feedbackID']) ? post_check($_POST['feedbackID']) : '';
		$userid		= isset($_POST['commentinguser']) ? post_check($_POST['commentinguser']) : '';
		$itemid		= isset($_POST['itemID']) ? post_check($_POST['itemID']) : '';
		$tranid		= isset($_POST['transactionID']) ? post_check($_POST['transactionID']) : '';
		$account	= isset($_POST['account']) ? post_check($_POST['account']) : '';
		$content	= isset($_POST['content']) ? post_check($_POST['content']) : '';
		$content	= htmlspecialchars($content);		
		$content    = str_replace("\\","",$content);	
		if ($feedbackid=='' || $userid=='' || $itemid=='' || $account=='' || $tranid=='' || $content=='') {
			self::$errCode = '001';
			self::$errMsg  = "参数错误！";
			return false;
		}
		$accAct 	 = new AccountAct();
		$accountInfo = $accAct->act_getAccountList('token',"where account = '{$account}' and is_delete = 0");
		$token		 = $accountInfo[0]['token'];		
		$verb='RespondToFeedback';
		$requestXmlBody = '<?xml version="1.0" encoding="utf-8"?>
						<RespondToFeedbackRequest xmlns="urn:ebay:apis:eBLBaseComponents">
						  <RequesterCredentials>
							<eBayAuthToken>'.$token.'</eBayAuthToken>
						  </RequesterCredentials>
						  <FeedbackID>'.$feedbackid.'</FeedbackID>
						  <TargetUserID>'.$userid.'</TargetUserID>
						  <TransactionID>'.$tranid.'</TransactionID>
						  <ItemID>'.$itemid.'</ItemID>
						  <ResponseText>'.$content.'</ResponseText>
						  <ResponseType>Reply</ResponseType>
						</RespondToFeedbackRequest>';
		 
		$session = new eBaySession($token, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $siteID, $verb);
		$responseXml = $session->sendHttpRequest($requestXmlBody);
		if(stristr($responseXml, 'HTTP 404') || $responseXml == '') {			
			self::$errCode = '002';
			self::$errMsg  = 'id not found';
			return false;
		}			
		$data=XML_unserialize($responseXml);			
		$ack	= $data['RespondToFeedbackResponse']['Ack'];
		if($ack != "Failure"){	
			$content	=mysql_real_escape_string($content);			
			$upData  	= array('reply_feedback' => $content);
			$where 	= " and FeedbackID='$feedbackid' ";
			EbayFeedbackModel::update($upData,$where);	
			return 'ok';		
		}else {			
			self::$errCode = '003';
			self::$errMsg  = "Reply失败！{$data['RespondToFeedbackResponse']['Errors']['ShortMessage']}";
			return false;
		}
	}
	
	
	function act_feedbackMutilReply(){
		include  WEB_PATH."lib/feedback/ebaylibrary/ebay_config.php";	
		
		$bills		= isset($_POST['bill']) ? $_POST['bill'] : '';	
		$content	= isset($_POST['content']) ? $_POST['content'] : '';
		$content	= htmlspecialchars($content);
		$content    = str_replace("\\","",$content);	
		if ($bills=='' || $content=='') {
			self::$errCode = '001';
			self::$errMsg  = "参数错误！";
			return false;
		}		
		$accountArr = array();
		foreach ($bills as $bill) {
			$accountArr[] = $bill['account'];
		}
		$accountArr = array_unique($accountArr);
		$accountStr = implode("','", $accountArr);
				
		$accAct 	 = new AccountAct();
		$accountInfo = $accAct->act_getAccountList('account,token',"where account in ('{$accountStr}') and is_delete = 0");
		$accountTokens = array();
		foreach ($accountInfo as $v){
			$accountTokens[$v['account']] = $v['token'];			
		}
		$len    = count($bills);//回复数组大小
	     for($i=0;$i<$len;$i++){
			$bills[$i]['token'] = $accountTokens[$bill['account']];	
		}

		/* var_dump($bills);
		foreach ($bills as &$bill) {
			$bill['token'] = $accountTokens[$bill['account']];			
		}	 */	
		$verb='RespondToFeedback';
		$errorArr=NUll;
		$cc   = 0;
		foreach ($bills as $bil) {			
			$feedbackid	= 	$bil['feedbackID'];
			$userid		= 	$bil['commentinguser'];
			$itemid		= 	$bil['itemID'];
			$tranid		= 	$bil['transactionID'];
			$account	= 	$bil['account'];
			$token		=	$bil['token'];
			$requestXmlBody = '<?xml version="1.0" encoding="utf-8"?>
						<RespondToFeedbackRequest xmlns="urn:ebay:apis:eBLBaseComponents">
						  <RequesterCredentials>
							<eBayAuthToken>'.$token.'</eBayAuthToken>
						  </RequesterCredentials>
						  <FeedbackID>'.$feedbackid.'</FeedbackID>
						  <TargetUserID>'.$userid.'</TargetUserID>
						  <TransactionID>'.$tranid.'</TransactionID>
						  <ItemID>'.$itemid.'</ItemID>
						  <ResponseText>'.$content.'</ResponseText>
						  <ResponseType>Reply</ResponseType>
						</RespondToFeedbackRequest>';
			$session = new eBaySession($token, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $siteID, $verb);
			$responseXml = $session->sendHttpRequest($requestXmlBody);
			if(stristr($responseXml, 'HTTP 404') || $responseXml == '') {			
				self::$errCode = '002';
				self::$errMsg  = 'id not found';
				return false;
			}
			$data=XML_unserialize($responseXml);
			$ack	= $data['RespondToFeedbackResponse']['Ack'];
			//var_dump($data['RespondToFeedbackResponse']);
			if($ack != "Failure") {
				$cc++;
				$content	=	mysql_real_escape_string($content);		
				$upData  	= array('reply_feedback' => $content);
				$where 	= " and FeedbackID='$feedbackid' ";
				if(!EbayFeedbackModel::update($upData,$where)){
					self::$errCode = '003';
					self::$errMsg  = "$userid,$feedbackid的回复内容 $content插入数据库失败,请联系IT负责人";
					return false;
				}
			} else {
				$errors		=	$data['RespondToFeedbackResponse']['Errors']['ShortMessage'];
				if($errors=="Reply to Feedback already submitted."){
					$errorArr	.="{$userid}对应的{$feedbackid},";
					//echo $errorArr;
				}
				//var_dump( $bill,$data['RespondToFeedbackResponse']);
				self::$errCode = '003';
				self::$errMsg  = "批量Reply失败！";
			}	
					
		}
		self::$errCode = '0';
		self::$errMsg  ="回馈消息:";
		if($errorArr){
			self::$errMsg  .="{$errorArr}回复过了，注意查看";
		}else{
			self::$errMsg	.="$cc  success";
		}
		return 'ok';
	
	}
	
	//回复邮件接口      add by yaoxiaodong  2014/05/22
	function act_feedbackMessage(){		
		include  WEB_PATH."lib/feedback/ebaylibrary/ebay_config.php";
		
		$feedbackid	= isset($_POST['feedbackID']) ? $_POST['feedbackID'] : '';
		$userid		= isset($_POST['commentinguser']) ? $_POST['commentinguser'] : '';
		$itemid		= isset($_POST['itemID']) ? $_POST['itemID'] : '';		
		$account	= isset($_POST['account']) ? $_POST['account'] : '';
		$content	= isset($_POST['content']) ? $_POST['content'] : '';		
		//echo html_entity_decode($content) ;exit;
		if ($feedbackid=='' || $userid=='' || $itemid=='' || $account=='' || $content=='') {
			self::$errCode = '001';
			self::$errMsg  = "参数错误！";
			return false;;
		}		
		$accAct 	 = new AccountAct();
		$accountInfo = $accAct->act_getAccountList('token',"where account = '{$account}' and is_delete = 0");
		$token		 = $accountInfo[0]['token'];	
		$verb='AddMemberMessageAAQToPartner';
		$requestXmlBody = '<?xml version="1.0" encoding="utf-8"?>
			<AddMemberMessageAAQToPartnerRequest xmlns="urn:ebay:apis:eBLBaseComponents">
				<RequesterCredentials>
					<eBayAuthToken>'.$token.'</eBayAuthToken>
				</RequesterCredentials>
				<ItemID>'.$itemid.'</ItemID>
				<MemberMessage>
					<EmailCopyToSender>true</EmailCopyToSender>
					<Body>'.$content.'</Body>
					<QuestionType>General</QuestionType>
					<RecipientID>'.$userid.'</RecipientID>
				</MemberMessage>
			</AddMemberMessageAAQToPartnerRequest>';
		$session = new eBaySession($token, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $siteID, $verb);
		$responseXml = $session->sendHttpRequest($requestXmlBody);
		if(stristr($responseXml, 'HTTP 404') || $responseXml == '')  {			
			self::$errCode = '002';
			self::$errMsg  = 'id not found';
			return false;
		}
		$data	= XML_unserialize($responseXml);
		$ack	= $data['AddMemberMessageAAQToPartnerResponse']['Ack'];
		$error	= $data['AddMemberMessageAAQToPartnerResponse']['Errors']['LongMessage'];		
		if($ack != "Failure") {
			/* $data 	= array('reply_feedback' => $content);
			$where 	= " and FeedbackID='$feedbackid' ";
			EbayFeedbackModel::update($data,$where); */
			return 'ok';	
					
		} else {			
			self::$errCode = '003';
			self::$errMsg  = "Message失败！".$error;
			return false;
			
		}			
	}
	
	function act_feedbackMutilMessage(){
		include  WEB_PATH."lib/feedback/ebaylibrary/ebay_config.php";	
	
		$bills		= isset($_POST['bill']) ? $_POST['bill'] : '';
		$content	= isset($_POST['content']) ? $_POST['content'] : '';
		//$content	= htmlspecialchars($content);
		//$content    = str_replace("\\","",$content);
		if ($bills=='' || $content=='') {
			self::$errCode = '001';
			self::$errMsg  = "参数错误！";
			return false;;
		}
		$accountArr = array();
		foreach ($bills as $bill) {
			$accountArr[] = $bill['account'];
		}
		$accountArr = array_unique($accountArr);
		$accountStr = implode("','", $accountArr);
	
		$accAct 	 = new AccountAct();
		$accountInfo = $accAct->act_getAccountList('account,token',"where account in ('{$accountStr}') and is_delete = 0");
		$accountTokens = array();
		foreach ($accountInfo as $v){
			$accountTokens[$v['account']] = $v['token'];
		}
		$len    = count($bills);//回复数组大小
		for($i=0;$i<$len;$i++){
			$bills[$i]['token'] = $accountTokens[$bill['account']];
		}
		/* foreach ($bills as &$bill) {
			$bill['token'] = $accountTokens[$bill['account']];
		} */
		//print_r($bills);echo  strlen($bills[0]['token']);exit;
	
		$verb='AddMemberMessageAAQToPartner';
		foreach ($bills as $bil) {
			//$feedbackid	= $bill['feedbackID'];
			$userid		= 	$bil['commentinguser'];
			$itemid		= 	$bil['itemID'];
			$tranid		= 	$bil['transactionID'];
			$account	= 	$bil['account'];
			$token		=	$bil['token'];
			//echo "$token";exit;
			$requestXmlBody = '<?xml version="1.0" encoding="utf-8"?>
			<AddMemberMessageAAQToPartnerRequest xmlns="urn:ebay:apis:eBLBaseComponents">
				<RequesterCredentials>
					<eBayAuthToken>'.$token.'</eBayAuthToken>
				</RequesterCredentials>
				<ItemID>'.$itemid.'</ItemID>
				<MemberMessage>
					<EmailCopyToSender>true</EmailCopyToSender>
					<Body>'.$content.'</Body>
					<QuestionType>General</QuestionType>
					<RecipientID>'.$userid.'</RecipientID>
				</MemberMessage>
			</AddMemberMessageAAQToPartnerRequest>';
			$session = new eBaySession($token, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $siteID, $verb);
			$responseXml = $session->sendHttpRequest($requestXmlBody);
			if(stristr($responseXml, 'HTTP 404') || $responseXml == '') {
				self::$errCode = '002';
				self::$errMsg  = 'id not found';
				return false;
			}
			$data	= XML_unserialize($responseXml);
			$ack	= $data['AddMemberMessageAAQToPartnerResponse']['Ack'];
			$error	= $data['AddMemberMessageAAQToPartnerResponse']['Errors']['LongMessage'];
			//var_dump($data['AddMemberMessageAAQToPartnerResponse']['Errors']);exit;
			if($ack != "Failure") {				
				/* $data  	= array('reply_feedback' => $content);
				$where 	= " and FeedbackID='$feedbackid' ";
				EbayFeedbackModel::update($data,$where); */
				
			} else {
				self::$errCode = '003';
				self::$errMsg  = "批量Message失败！".$error;
				return false;
			}
		}
		return 'ok';
	}	
	
	function act_feedbackChangeMutilMessage(){
		include  WEB_PATH."lib/feedback/ebaylibrary/ebay_config.php";
		
		//print_r($_POST);//exit;
		$bills		= isset($_POST['bill']) ? $_POST['bill'] : '';
		$content	= isset($_POST['content']) ? $_POST['content'] : '';
		$content	= htmlspecialchars($content);
		$content    = str_replace("\\","",$content);
		if ($bills=='' || $content=='') {
			self::$errCode = '001';
			self::$errMsg  = "参数错误！";
			return false;;
		}
		$accountArr = array();
		foreach ($bills as $bill) {
			$accountArr[] = $bill['account'];
		}
		$accountArr = array_unique($accountArr);
		$accountStr = implode("','", $accountArr);
	
		$accAct 	 = new AccountAct();
		$accountInfo = $accAct->act_getAccountList('account,token',"where account in ('{$accountStr}') and is_delete = 0");
		$accountTokens = array();
		foreach ($accountInfo as $v){
			$accountTokens[$v['account']] = $v['token'];
		}
		
		foreach ($bills as &$bill) {
			$bill['token'] = $accountTokens[$bill['account']];			
			$where = " where account = '{$bill['account']}' and CommentingUser = '{$bill['ebayUserId']}' limit 1";			
			$lists = EbayFeedbackModel::getOrderList('itemID',$where);
			$bill['itemID'] = $lists[0]['itemID'];
		}
		//print_r($bills);exit;
	
		$verb='AddMemberMessageAAQToPartner';
		foreach ($bills as $bil) {
			//$feedbackid	= $bill['feedbackID'];
			$userid		= $bil['ebayUserId'];
			$itemid		= $bil['itemID'];
			$account	= $bil['account'];
			$token		= $bil['token'];
			$requestXmlBody = '<?xml version="1.0" encoding="utf-8"?>
			<AddMemberMessageAAQToPartnerRequest xmlns="urn:ebay:apis:eBLBaseComponents">
				<RequesterCredentials>
					<eBayAuthToken>'.$token.'</eBayAuthToken>
				</RequesterCredentials>
				<ItemID>'.$itemid.'</ItemID>
				<MemberMessage>
					<EmailCopyToSender>true</EmailCopyToSender>
					<Body>'.$content.'</Body>
					<QuestionType>General</QuestionType>
					<RecipientID>'.$userid.'</RecipientID>
				</MemberMessage>
			</AddMemberMessageAAQToPartnerRequest>';
			$session = new eBaySession($token, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $siteID, $verb);
			//print_r($session);
			//continue;//exit;
			$responseXml = $session->sendHttpRequest($requestXmlBody);
			if(stristr($responseXml, 'HTTP 404') || $responseXml == '') {
				self::$errCode = '002';
				self::$errMsg  = 'id not found';
				return false;
			}
			$data	= XML_unserialize($responseXml);
			$ack	= $data['AddMemberMessageAAQToPartnerResponse']['Ack'];
			$error	= $data['AddMemberMessageAAQToPartnerResponse']['Errors']['LongMessage'];
			if($ack != "Failure") {
				$data  	= array('reply_feedback' => $content);
				$where 	= " and FeedbackID='$feedbackid' ";
				EbayFeedbackModel::update($data,$where);
				return 'ok';
			} else {
				self::$errCode = '003';
				self::$errMsg  = "批量Message失败！".$error;
				return false;
			}
		}
	}
	
}
?>