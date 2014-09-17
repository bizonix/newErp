<?php
/*
 * Amazon邮件抓取脚本
 */

error_reporting(E_ALL);
include_once __DIR__.'/../framework.php'; // 加载框架
Core::getInstance(); // 初始化框架对象
include_once WEB_PATH . 'lib/Get_Email.class.php';
include_once WEB_PATH . 'lib/opensys_functions.php';


$mail         =  new Get_Email();
$account_obj  =  new AmazonAccountModel();
$msg_obj      =  new amazonmessageModel();
$msgcat_obj   =  new amazonmessagecategoryModel();
$fam_obj      =  new FetchAmazonMessageModel();
$accounts     =  $account_obj->getAmazonAccountsGmail();
$path         =  '';
$connect      =  array();
$n            =  0;
foreach($accounts as $ac){
		if($ac['amazon_account']!="$argv[1]"){
			continue;
		}
		$path     =  WEB_PATH.'crontab/gmaillib/'. $ac['amazon_account'].'/'. preg_split('/@/',$ac['gmail'])[0].'/'.date('Y-m-d').'/';
		$connect  =  $mail->mailConnect('imap.gmail.com','993',$ac['gmail'],base64_decode($ac['password']),'INBOX','ssl');
		if(!$connect){
			die('连接失败') ;
		} else {
			echo "连接成功\r\n";
		}
		
		//获取未查看的邮件
		$date = date ( "d M Y", strToTime ( "-0 days" ) );
		echo $date."\r\n";
		$uids     = imap_search($connect, "SINCE \"$date\"  UNSEEN ", SE_UID);
		if(!$uids){
			echo '邮箱'.$ac['gmail']."是空的（︶︿︶）\r\n";
			echo $ac['gmail']." 连接断开\r\n";
			$mail->closeMail();
			continue;
		} else {
			echo '邮箱'.$ac['gmail']."有新邮件耶！＜（￣︶￣）＞\r\n";
		}
		$msgcount =  count($uids);
		echo "邮箱邮件数：$msgcount\r\n";
		$now = time();
		//$lastEmailSendLime = $msg_obj->getLastSendTime($ac['gmail']);
		$lasttime          = '';
		for($i = $msgcount - 1;$i >= 0;$i--){
			$msgno     		 =   imap_msgno($connect, $uids[$i]);
			$headinfo        =   $mail->getHeader($msgno);
			$header=imap_headerinfo($connect,$msgno);
			if(!$header){
				continue;
			}
/* 			if($headinfo['date']<$lastEmailSendLime){
				continue;
			}
 */  			/* if($now - strtotime($headinfo['date']) > 7200){
  				echo "过滤$uids[$i]\r\n";
  				break;
  			} */
  			$pattern_forbidden = '/seller-notification|do-not-reply|'.
  								 'haendler-benachrichtigungen|fba-ship-confirm|'.
  								 'nobody|auto-communication/';
  			if(preg_match($pattern_forbidden, $headinfo['from'])){
  				echo '过滤'.$headinfo['from']."\n";
  				continue;
  			}
			echo $uids[$i]."\n";
			$from_platform     = -1;
			$pattern_Amazon    = '/amazon-selling-coach|merch\.service05|seller-performance|'.
							     'notice|seller-evaluation|merchant-performance|seller-info|'.
							     'listing-error-feedback|seller-notification/';
			$pattern_Claims    = '/payments-guarantee-reply|seller-guarantee|'.
							     'transaction-inquiry-a|payments-guarantee|payments-garantie/';
			$pattern_Messager  = '/jiajust|mbula|liugaby/';
			if(preg_match($pattern_Amazon, $headinfo['from'])){
				$from_platform    = 0;
			}
			if(preg_match($pattern_Claims, $headinfo['from'])){
				$from_platform    = 1;
			}
			if(preg_match($pattern_Messager, $headinfo['from'])){
				$from_platform    = 2;
			}
			
			//print_r($headinfo);
			$message_id      =   $uids[$i];
			$subjet          =   $mail->imapUtf8($headinfo['subject']);
			$encode = mb_detect_encoding($subjet, array("ASCII",'UTF-8','GB2312',"GBK",'BIG5','ISO-8859-1'));
			if($encode!='UTF-8'){
				$subjet          =   mb_convert_encoding($subjet, 'UTF-8');
			}
			$from            =   $headinfo['from'];
			$fromName        =   $mail->imapUtf8($headinfo['fromName']);
			$fromName        =   mb_convert_encoding($fromName, 'utf8');
			$to              =   empty($headinfo['to']) ? $ac['gmail'] : $headinfo['to'];
			$toName          =   empty($headinfo['toName']) ? $ac['amazon_account'] : $headinfo['toName'];
			$sendtime        =   $headinfo['date'];
			$body            =   $mail->getBody($msgno);
			$pushtime        =   date('Y-m-d H:i:s');
			$fname           =   $toName.$message_id.'.html';
			$amazon_account  =   $account_obj->getAmazonAccountByGmail($to);
			$catrules        =   $msgcat_obj->getCatRules($to);//分类id和其规则
			$classid         =   0;
			imap_clearflag_full($connect, $uids[$i], '\\Seen',ST_UID);
			//分配邮件进入各个分类
			foreach ($catrules as $ct){ 
				$rules_arr = preg_split('/,/',$ct['rules']);
				if(in_array(substr($from, 0,1), $rules_arr)){
					$classid=$ct['id'];
					echo "找到了合适的分类\r\n";
					continue;
				}
			}
			echo $headinfo['date']."\n";
			//发件人首字符对应不到分类
			if($classid==0){
				echo '未能找到合适的分组\r\n';
				$result = $msgcat_obj->getCatRules($to);
				if(empty($result)){
					echo '符合的账号还没有建立相关的分类\r\n';
					$classid = -1;
				} else {
					$classid = $result[0]['id'];
				}
			}
			 if(preg_match('/\d{3}-\d{7}-\d{7}/', $subjet,$ordernum)){
			} else {
				preg_match('/\d{3}-\d{7}-\d{7}/', $body,$ordernum);
			} 
			$ordernum =$ordernum[0];
			//通过订单号或者发件人邮箱获取买家和卖家信息
			echo "获取买家卖家信息中···\n";
		    $email              =  $from;
		    $buyer              =  '';
		    $seller             =  '';
			$buyerandseller     =  getOpenSysApi(OPENGETAMAZONORDER,array('email'=>$email,'ordernumber'=>$ordernum));
			print_r($buyerandseller);
			$buyer              =  $buyerandseller[0]['ebay_userid'];
			$seller             =  $buyerandseller[0]['ebay_account'];
			if(!preg_match('/<\S+>/', $body)){
				$body="<pre>".$body."</pre>";
			} 
 			$mailinfo=array(
					'message_id'       =>  preg_split('/@/', $to)[0].$message_id,
 					'recieveid'        =>  $to,
 					'receivename'      =>  $toName,
 					'sendid'           =>  $from,
 					'sendname'         =>  mysql_escape_string($fromName),
					'subject'          =>  mysql_escape_string($subjet),
					'sendtime'         =>  strtotime($sendtime),
 					'classid'		   =>  $classid,
 					'amazon_account'   =>  $amazon_account[0]['amazon_account'],
 					'recievetimestamp' =>  strtotime($pushtime),
 					'from_platform'    =>  $from_platform,
 					'messagepath'      =>  mysql_escape_string($path.$fname),
 					'attachpath'       =>  '',
 					'attachname'	   =>  '',
 					'ordernum'		   =>  $ordernum,
 					'buyer'            =>  mysql_escape_string($buyer),
 					'seller'           =>  mysql_escape_string($seller),
 					//'ishtml'		   =>  $ishtml,	
			);
 			
 			/* $atpath	  =  WEB_PATH.'crontab/gmailattach/'.$mailinfo['msg_id'].'/';
 			if($mailAttach=$mail->getAttach($message_id,$atpath)){
 				echo "附件下载成功\r\n";
 				$atpath.= $mailAttach[0];
 				$mailinfo['attachname'] = $mailAttach[0];
 				$mailinfo['attachpath'] = $atpath;
 				imap_clearflag_full($connect,$message_id , '\\Seen',ST_UID);
 			} else {
 				echo "附件下载失败或者该邮件没有附件\r\n";
 			} */
 			
 			echo "存储开始\r\n";
 			if($msg_obj->getMsgId($mailinfo['message_id'])){
 				echo "已经存储过了\r\n";
 				continue;
 			}
		    if(write_w_file($path.$fname, $body)){
		    	echo("邮件存储成功\r\n");
				if($msg_obj->insertMessages($mailinfo)){
					echo "写入数据库成功!\r\n";
					$lasttime = $mailinfo['sendtime'];
					echo "\r\n";
				} else {
					echo "写入数据库失败!\r\n";
				}
		    } else {
		    	echo '邮件存储失败';
		    }
		}
		echo "本次抓取的最后一封邮件发送时间是".date('Y-m-d H:i:s',$lasttime);
		echo $ac['gmail']." 连接断开\r\n";
		echo '＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊わだぃわかわいのxzysaberです＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊'."\n";
		$mail->closeMail();
}
?>