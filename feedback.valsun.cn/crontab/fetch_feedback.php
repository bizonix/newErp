<?php 
	@session_start();
	error_reporting(E_ALL);

	if($argc < 5)
	{
		exit("Usage: /usr/bin/php $argv[0] startpage endpage perPageCount eBayAccount1 ");
		
	}
	$startpage 		  = trim($argv[1]);
	$endpage 		  = trim($argv[2]);
	$perPageCount     = trim($argv[3]);
	$ebayaccount      = trim($argv[4]);
	

	$GLOBAL_EBAY_ACCOUNT = array('niceinthebox','betterdeals255','enjoy24hours',
			'365digital','niceforu365','bestinthebox','dealinthebox','digitalzone88',
			'itshotsale77','keyhere','befdi','befdimall','sunwebhome','cndirect55',
			'cndirect998','easydeal365','ishop2099','sunwebzone','tradekoo','enicer',
			'doeon','starangle88','elerose88','zealdora','wellchange','cafase88',
			'choiceroad','befashion','360beauty','voguebase55','charmday88',
			'dresslink','happydeal88','easytrade2099','easyshopping678','work4best',
			'eshop2098','fiveseason88','easebon','estore2099','futurestar99',
			'mysoulfor','newcandy789','estore456','happyzone80','eseasky68',
			'allbestforu','enjoytrade99','infourseas','unicecho','vobeau','swzeagoo',
			'easyshopping095','beromantic520','easydealhere','freemart21cn',
			'sunweb','emallzone','ishoppingclub68','eshoppingstar75','zeagoo889',
			'lantomall','ulifestar','cndirectstore',	'betterlift99','enjoydeal99',
			'etrade77','goonline55','greatdeal456','hotdeal77','linemall','okmart88',
			'utarget88','worlddepot','Finejo2099','easydealsmall','efashionforu',
			'etradestar58','happyforu19','edealsmart','ecnonline','cooforu','betterlife99');

	
	require_once "scripts.comm.php";		
	
	/*
	$startpage 		  = 1;
	$endpage 		  = 3;
	$perPageCount     = 200;
	$ebayaccount      = 'betterdeals255';*/
	
	if(!preg_match('#^[\da-zA-Z]+$#i',$ebayaccount)){
		exit("Invaild ebay account:$ebayaccount");
	}
	if(!preg_match('#^[\d]+$#i',$startpage)){
		exit("Invaild Page:$startpage");
	}
	if(!preg_match('#^[\d]+$#i',$endpage)){
		exit("Invaild Page:$endpage");
	}
	if(!preg_match('#^[25,50,100,200]+$#i',$perPageCount)){
		exit("Invaild PageCount:$perPageCount");
	}
	if(!in_array($ebayaccount,$GLOBAL_EBAY_ACCOUNT)){
		exit("$ebayaccount is not support now !");
	}
	
	$_SESSION['user']='vipchen';
	//require_once WEB_PATH.'ebay_order_cron_config.php';	
	
	
	require_once '/data/web/feedback.valsun.cn/lib/feedback/ebaylibrary/keys/keys_'.$ebayaccount.'.php';
	//require_once '/data/htdocs/yaoxiaodong.dev.com/feedback.valsun.cn/lib/feedback/ebaylibrary/keys/keys_'.$ebayaccount.'.php';
	$api_feedback = new GetFeedbackAPI($ebayaccount);
	//echo $userToken;  echo strlen($userToken);
	
	
	//var_dump($api_feedback);
	//exit;
	
	$startTime    = strtotime(date('Y-m-d 08:00:00'));
	$endTime      = strtotime(date('Y-m-d 09:30:00'));
	$nowTime      = strtotime(date('Y-m-d H:i:s'));
	
	if($nowTime > $startTime && $nowTime < $endTime){
		//exit('此时间段不执行');
	}
	
	GetFeedback($ebayaccount,$startpage,$endpage,$perPageCount);
	echo $ebayaccount.' Success';
	exit();
	
	function GetFeedback($account,$startpage,$endpage,$perPageCount){
		//require_once  WEB_PATH."lib/xmlhandle.php";
		
		global $dbcon,$api_feedback,$user;
		echo '同步feedback,开始于第'.$startpage.'页，结束于第'.$endpage.'页，每页同步'.$perPageCount.'条'."\n";
		$hasmore	= true;
		$status     = "";
		$FBAct = new EbayFeedbackAct();
		
		while(true){
			echo '开始运行,第'.$startpage.'页'."\n";
			$responseXml = $api_feedback->request($startpage,$perPageCount);
			if(stristr($responseXml, 'HTTP 404') || $responseXml == '') return 'id not found';
			$data=XML_unserialize($responseXml);
			//var_dump($data);
			$ack	= $data['GetFeedbackResponse']['Ack'];
			$TotalNumberOfPages		= $data['GetFeedbackResponse']['PaginationResult']['TotalNumberOfPages'];
			if($ack != "Success")
			{
				echo "<font color=red>评价加载失败</font>";
				//var_dump($data['GetFeedbackResponse']);exit;
			}
			$feedback	= $data['GetFeedbackResponse']['FeedbackDetailArray']['FeedbackDetail'];
			foreach($feedback as $li){
				$CommentingUser			= str_rep($li['CommentingUser']);
				$CommentingUserScore	= str_rep($li['CommentingUserScore']);
				$CommentText			= mysql_real_escape_string(str_rep($li['CommentText']));
				$CommentTime			= str_rep($li['CommentTime']);
				$feedbacktime 			= date('Y-m-d H:i:s',strtotime($CommentTime));
				$feedbacktime 			= date('Y-m-d H:i:s',strtotime("$feedbacktime - 900 minutes"));
				$feedbacktime 			= strtotime($feedbacktime);
				$CommentType			= str_rep($li['CommentType']);
				$ItemID					= str_rep($li['ItemID']);
				$FeedbackID				= str_rep($li['FeedbackID']);
				$TransactionID			= $li['TransactionID']?$li['TransactionID']:0;
				$ItemTitle				= str_rep($li['ItemTitle']);
				$currencyID				= str_rep($li['ItemPrice attr']['currencyID']);
				$ItemPrice				= str_rep($li['ItemPrice']);
				$data = array(
					'CommentingUser'		=> $CommentingUser,
					'account' 				=> $account, 
					'CommentingUserScore'	=> $CommentingUserScore,
					'CommentText'			=> $CommentText,						
					'CommentTime'			=> $CommentTime,
					'CommentType'			=> $CommentType,
					'ItemID'				=> $ItemID,
					'FeedbackID'			=> $FeedbackID,
					'TransactionID'			=> $TransactionID,//"'{$TransactionID}'",
					'ItemTitle'				=> $ItemTitle,
					'currencyID'			=> $currencyID,
					'ItemPrice'				=> $ItemPrice,
					//'ebay_user'				=> $user,
					'feedbacktime'			=> $feedbacktime,
				);
				$list  = $FBAct->act_getOrderList('id'," where FeedbackID='$FeedbackID' ");				
				if(!$list){ //不存在，则插入					
					$ret = EbayFeedbackModel::insertRow($data);						
					if($ret) {

						echo "insert success!\n";
						$field = 'a.ebay_ordersn, a.ebay_paidtime, b.sku, b.ebay_amount';
						$orderInfo = UserCacheModel::getErpOrderInfo($CommentingUser, $ItemID, $TransactionID, $field);
						/* var_dump($orderInfo);
						exit; */
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
												
					} else {
						echo "insert failed!\n";
					}
				
				} else {
				
					echo "Exsited!\n";
				}
			}
			if($startpage >= $endpage){
				break;
			}
			$startpage++;
		}
	}
	
?>