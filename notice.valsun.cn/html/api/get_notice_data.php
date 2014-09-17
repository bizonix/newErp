<?php
//mod by wxb 2013/10/30
error_reporting(-1);
$format	= isset($_GET['format']) ? trim($_GET['format']) : "json";
$errmsg_arr	= array(
	2000 		=> "操作成功",
	2001 		=> "Missing action",
	2002 		=> "Invalid action",
	2003 		=> "Missing type",
	2004 		=> "Invalid type",
	2005		=> "Missing addressee",
	2006		=> "Invalid addressee",
	2007		=> "Invalid format",
	2008		=> "Missing content",
	2009		=> "Invalid content",
	2010		=> "获取数据失败",
	2011		=> "Missing sender",
	2012		=> "Invalid sender",
	2013		=> "Sent successfully",
	2014		=> "Failed to send",
	2015		=> "Missing callback",
	2016		=> "Invalid callback",
	2131		=> "找不到发件人信息",
	2132       => "",
);

//获取JSONP相关信息
$callback = "";
if(isset($_GET['callback'])){				//可不定义回调函数
// 	if(trim($_GET['callback']=="")){
// 		echo $format == 'json' ? error_json_notice('2016') : error_xml_notic('2016');
// 		exit;
// 	}
	$callback = trim($_GET['callback']);
}

//验证参数action是否为send
if(!isset($_GET['action']) || (trim($_GET['action'])=="")){
	echo $format == 'json' ? error_json_notice('2001') : error_xml_notic_notic('2001');
	exit;
}
$action		= $_GET['action'];
if(!in_array($action,array('send','shownotice'))){
	echo $format == 'json' ? error_json_notice('2002') : error_xml_notic_notic('2002');
	exit;
}
// echo __LINE__."<p>";
//验证参数type是否为sms,email的自由组合
if(!isset($_GET['type']) || (trim($_GET['type'])=="")){
	echo $format == 'json' ? error_json_notice('2003') : error_xml_notic_notic('2003');
	exit;
}
$type_arr = explode(",",$_GET['type']);
$flag	  = 0;
foreach($type_arr as $v){
	if(!in_array($v,array('sms','email'))){
		$flag = 1;
	}
}
if($flag){
	echo $format == 'json' ? error_json_notice('2004') : error_xml_notic_notic('2004');
	exit;
}


//验证发信人是否为空
if(!isset($_GET['from']) || (trim($_GET['from'])=="")){
	echo $format == 'json' ? error_json_notice('2011') : error_xml_notic_notic('2011');
	exit;
}
$from		= urldecode($_GET['from']);

//如果是shownotice接口不检查下面的选项
if($action!="shownotice"){
	//验证收信人是否为空
	if(!isset($_GET['to']) || (trim($_GET['to'])=="")){
		echo $format == 'json' ? error_json_notice('2005') : error_xml_notic_notic('2005');
		exit;
	}
	$to		= urldecode($_GET['to']);
	$to_arrs= explode(",",$to);
	$to_arrs = array_filter($to_arrs,'removeEmpty');
	//验证消息内容是否为空
	if(!isset($_GET['content']) || (trim($_GET['content'])=="")){
		echo $format == 'json' ? error_json_notice('2008') : error_xml_notic_notic('2008');
		exit;
	}
	$content= urldecode($_GET['content']);
}


//验证数据返回格式
if(isset($_GET['format']) && !in_array($_GET['format'],array('xml','json'))){
	echo $format == 'json' ? error_json_notice('2007') : error_xml_notic_notic('2007');
	exit;
}
//检查是否有自定义标题
$title = '';
if(isset($_GET['title']) && !empty($_GET['title'])){
	$title = urldecode(trim($_GET['title']));
}
//检查是否只返回发送失败者名字 losePerson
$losePerson = '';
if(isset($_GET['losePerson']) && !empty($_GET['losePerson'])){
	$losePerson = trim($_GET['losePerson']);
}
//获取客户端ip
$client_ip = '';
if(isset($_GET['client_ip']) && !empty($_GET['client_ip'])){
	$client_ip = trim($_GET['client_ip']);
}
// $client_ip = $client_ip?$client_ip:'test';
$sysName = '';
if(isset($_GET['sysName']) && !empty($_GET['sysName'])){
	$sysName = urldecode(trim($_GET['sysName']));
}
//检查是否有第几页
$page = 1;//默认第一页
if(isset($_GET['page']) && !empty($_GET['page'])){
	$page = (int)$_GET['page'];
}

include_once "../../framework.php";
Core::getInstance();
// include_once WEB_PATH."lib/mail.class.php";
include_once WEB_PATH."lib/sms.class.php";
include_once WEB_PATH."lib/class.phpmailer.php";
include_once WEB_PATH."lib/class.smtp.php";
include_once WEB_PATH."action/noticeApi.action.php";
$noticeApi	= new NoticeApiAct();
switch($action){
	//发送消息
	case "send":
		$errTo 				= array();	//收集不存在的接收者
		$toSelf 			= false;	//标志发送者和接收者是否是同一个人
		$noEmail 			= array();	//记录是否存在对方的邮件
		$noMobile 			= array();	//记录是否存在对方手机号
		$smSendFail 		= array();	//收集发送手机失败 名字
		$emailSendFail 		= array();	//收集邮件发送失败 名字
		$to_detail 			= array();	//收件人信息
		$email_avaliable 	= array();	//可用邮件收件人

		$table = "";

		$table = "`power_global_user` ";
		$filed = "global_user_email,global_user_phone,global_user_name,global_user_login_name";
		//获取发送者信息 start
		$where = "(global_user_login_name = '{$from}' OR global_user_name = '{$from}') AND global_user_status = 1 AND global_user_is_delete = 0   LIMIT 1 ";
		$ret   = NoticeApiAct::selectOneTable($table, $filed, $where);
		if(empty($ret) || empty($ret[0]['global_user_name']) ){
			echo $format == 'json' ? error_json_notice('2131') : error_xml_notic('2131');
			exit;
		} else {
			$from_email 		= $ret[0]['global_user_email'];
			$from_mobile		= $ret[0]['global_user_phone'];
			$from 				= $ret[0]['global_user_name'];			//显示中文名
			$fromUserName 		= $from;
			$from_login_name 	= $ret[0]['global_user_login_name'];
		}
		//获取发送者信息 end
		//去掉重复的接收者
			$to_arrs = array_unique($to_arrs);
		//获取并过滤接收者信息 start
			foreach($to_arrs as $tonames){//查询数据库,去除有误的收件人
				$table = "`power_global_user` ";
				$filed = "global_user_email,global_user_phone,global_user_name,global_user_login_name";
				$where = "(global_user_login_name = '{$tonames}' OR global_user_name = '{$tonames}' ) AND global_user_status = 1 AND global_user_is_delete = 0  LIMIT 1 ";
				$ret = NoticeApiAct::selectOneTable($table,$filed,$where);
				if(empty($ret) || empty($ret[0]['global_user_name']) ){
					$errTo[] = $tonames;//不存在接收人便进入下一个循环
					continue;
				}
				$to_email = $ret[0]['global_user_email'];
				$to_mobile= $ret[0]['global_user_phone'];
				$to_name = $ret[0]['global_user_name'];//显示中文名
				$to_login_name = $ret[0]['global_user_login_name'];//显示拼音名
				//对比是否是发送者本人
				if($to_login_name == $from_login_name){
					//如果有系统参数
					if(empty($sysName)){
						$toSelf = true;
						continue;
					}else{
						$sql = "SELECT * FROM `power_system` WHERE system_isdelete = 0  ";
						$sql .= "  AND system_name = '{$sysName}' AND system_principal = '{$from}'";
						$sql .= " LIMIT 1";
						$query = $dbConn->query($sql);
						$res = $dbConn->fetch_array($query);
						if(empty($res)){
							$toSelf = true;
							continue;
						}
					}
				}
				//存储接收者信息 至少有一个地址不为空
				if(!empty($to_email) || !empty($to_mobile) ){
					$to_detail[] =array(
						'to_name'=>$to_name,
						'to_login_name'=>$to_login_name ,
						'to_email'=>$to_email,
						'to_mobile'=>$to_mobile
					);
				}
				//邮件是否为空
				if(empty($to_email) ){
					$noEmail[] = $to_name;
				}else{
					//非空邮件
					$email_avaliable[] = array(
							'to_name'=>$to_name,
							'to_email'=>$to_email
					);
				}
				//手机是否为空
				if(empty($to_mobile)){
					$noMobile[] = $to_name;
				}
			}
			if(count($to_detail)==0){
				$errmsg_arr['2132'] = $losePerson?implode(',',$to_arrs):'无收件人数据或不应为本人';
				echo $format == 'json' ? error_json_notice('2132') : error_xml_notic('2131');
				exit;//无任何可用数据直接退出
			}
//获取并过滤接收者信息 end
				$status = "";
				//先发邮件
				if(in_array("email",$type_arr)){
					if(empty($title)){
						$title      = $from.'给您发了一条新的消息,请及时阅读【华成云商】';//邮件标题
					}
					$from_info=array($from,$from_email);
					$status 	=  newSendEmail($title,$content,$email_avaliable,$from_info);
					$sendFailArr = array();
					if($status != '1'){
						 if(is_array($status)){
						 	$emailSendFail = array_merge($emailSendFail,$status[1]);//返回数组
						 	$sendFailArr = $status[1];
						}else{
							$sendFailArr = explode(',', $status);//返回以,号隔开的发送失败者邮箱
							$emailSendFail = array_merge($emailSendFail,$sendFailArr);
						}
					}
					$table		= "nt_email";
					foreach($email_avaliable as $email_avaliable_val){
						$to_email = $email_avaliable_val['to_email'];
						$to_name = $email_avaliable_val['to_name'];
						if(in_array($to_email,$sendFailArr)){
							$status = 0;
						}else{
							$status = 1;
						}
						$data		= array(
									"from_email" => $from_email,
									"to_email" => $to_email, //
									"content" => post_check($content),
									"from_name" => $from,
									"to_name" => $to_name,//
									"addtime" => time(),
									"status" => $status,	//
									"from_login_name"=>$from_login_name,
									"to_login_name" =>$to_login_name,
									"client_ip" =>$client_ip
								);
							$result		= $noticeApi->actInsert($data,$table);
					}
				}
				//发短信
				if(in_array("sms",$type_arr)){
					$table		= "nt_sms";
					foreach($to_detail as $to_detail_val){
						//单条发送消息start
						$to_name = $to_detail_val['to_name'];
						if(in_array($to_name,$noMobile)){
							continue;
						}
						$to_mobile = $to_detail_val['to_mobile'];
						$to_login_name = $to_detail_val['to_login_name'];
// 						var_dump($to_mobile,$content);
						$res_sms	= Sms::send_sms_post($to_mobile,$content."【华成云商】",'');
						$status = substr($res_sms,0,strpos($res_sms,"/"));//000 表示成功
// 						var_dump($res_sms,$status);
						if($status !== '000'){
							$smSendFail[]= $to_name;
						}
						$data = array(
									"from_mobile" => $from_mobile,
									"to_mobile" => $to_mobile,
									"content" => post_check($content),
									"from_name" => $from,
									"to_name" => $to_name,
									"addtime" => time(),
									"status" => $status,
									"from_login_name"=>$from_login_name,
									"to_login_name" =>$to_login_name,
									"client_ip" =>$client_ip
								);
						$result		= $noticeApi->actInsert($data,$table);
					//单条发送消息end
					}
				}

// 				$status  短信000 或者邮件1 为成功
		if(in_array("sms",$type_arr) && !in_array("email",$type_arr)){
			$noEmail = array();
		}else if(in_array("email",$type_arr) && !in_array("sms",$type_arr)){
			$noMobile = array();
		}
		if($losePerson){
			//收集发送失败人
			$errUser = array();//集合所有发送失败者名
			$errUser = array_merge($errTo,$noEmail,$noMobile,$smSendFail,$emailSendFail);
			$errUser = array_unique($errUser);
			$errUserStr = implode(',',$errUser);
			$errmsg_arr['2132'] = $errUserStr;
		}else{
			$errToal = array();//集合所有错误
			if(count($errTo)>0){//找不到收件人
				$errTo = array_unique($errTo);
				$errStr = implode(' | ',$errTo);
				$errToal[] =   $errStr." 找不到接收人信息";
			}
			if($toSelf){//收发人是自己
				$errToal[] = "发送者{$fromUserName}和接收者不应为同一人";
			}
			if(count($noEmail)>0){//无邮件
				$noEmailStr = array_unique($noEmail);
				$noEmailStr = implode(' | ',$noEmailStr);
				$errToal[] =   $noEmailStr." 找不到邮件地址";
			}
			if(count($noMobile)>0){//无手机号
				$noMobileStr = array_unique($noMobile);
				$noMobileStr = implode(' | ',$noMobileStr);
				$errToal[] =   $noMobileStr." 找不到手机号信息";
			}
			if(count($smSendFail)>0){//手机发关失败
				$smSendFail = array_unique($smSendFail);
				$smSendFailStr = implode(' | ',$smSendFail);
				$errToal[] =   $smSendFailStr." 手机短信发送失败";
			}
			if(count($emailSendFail)>0){//邮件发送失败
				$emailSendFail = array_unique($emailSendFail);
				$emailSendFailStr = implode(' | ',$emailSendFail);
				$errToal[] =   $emailSendFailStr." 邮件发送失败";
			}
			if(count($errToal)>0){//有错误记录，全部写入2132
				$errToalStr = implode('<br/>', $errToal);
				$errmsg_arr['2132'] = $errToalStr;
			}
		}
		if(!empty($errmsg_arr['2132'])){//有错误记录
			echo $format == 'json' ? error_json_notice('2132') : error_xml_notic('2132');
			exit;
		}
		echo $format == 'json' ? error_json_notice('2013') : error_xml_notic('2013');//2014
	break;

	//拉取某个用户最近10条消息
	case "shownotice":
		foreach($type_arr as $t){
			if($t=='email') $table = 'nt_email';
			if($t=='sms') 	$table = 'nt_sms';
			$r	 = $noticeApi->actDetailList($from,$table,$page);
		}
		if(count($r)>0){
			$jsondata 	= $callback."(".json_encode($r).")";
			echo $jsondata;
		}else{
			echo $format == 'json' ? error_json_notice('2010') : error_xml_notic('2010');
		}
	break;
	default:
		echo $format == 'json' ? error_json_notice('2010') : error_xml_notic('2010');

}
exit;


//发送邮件函数
function sendEmail($title,$content,$email)
{
	$MailServer = 'smtp.exmail.qq.com';  //SMTP 服务器
	$MailPort   = '25';					 //SMTP服务器端口号 默认25
	$MailId     = 'valsun@sailvan.com';  //服务器邮箱帐号
	$MailPw     = 'sailvan_va';			     //服务器邮箱密码
	$smtp = new smtp($MailServer,$MailPort,true,$MailId,$MailPw);
	$smtp->debug = false;
	if($smtp->sendmail($email,$MailId, $title, $content, "HTML")){
		 return '1';
	} else {
		 return '0';
	}
}
//add by wxb 2013/12/1
function newSendEmail($title,$content,$arrEmailName,$from){
	global $mail;
	if(empty($title) || empty($content) || !is_array($arrEmailName)  || !is_array($from)){
		return false;
	}
	//Tell PHPMailer to use SMTP
	$mail->isSMTP();
// 	$mail->CharSet = "utf-8";
	//Enable SMTP debugging
	// 0 = off (for production use)
	// 1 = client messages
	// 2 = client and server messages
	$mail->SMTPDebug = 0;
	//Ask for HTML-friendly debug output
// 	$mail->Debugoutput = 'html';
	//Set the hostname of the mail server
	$mail->Host = 'smtp.exmail.qq.com';  //SMTP 服务器
	//Set the SMTP port number - likely to be 25, 465 or 587
	$mail->Port = 25;
	//Whether to use SMTP authentication
	$mail->SMTPAuth = true;
	//Username to use for SMTP authentication
	$mail->Username = 'valsun@sailvan.com';  //服务器邮箱帐号
	//Password to use for SMTP authentication
	$mail->Password = 'sailvan_va';			     //服务器邮箱密码
	//Set who the message is to be sent from
	$mail->setFrom('valsun@sailvan.com','华成云商');
	//Set an alternative reply-to address
	$mail->addReplyTo($from[1],$from[0]);
	//Set who the message is to be sent to
	$recordAll = array();
	foreach($arrEmailName as $val){
		$mail->addAddress($val['to_email'],$val['to_name']);
		$recordAll[] = $val['to_email'];
	}
	//Set the subject line
// 	$mail->Subject = $title;
	$mail->Subject = "=?utf-8?B?" . base64_encode($title) . "?="; //mod 2013/12/09 防止标题中文乱码
	//Read an HTML message body from an external file, convert referenced images to embedded,
	//convert HTML into a basic plain-text alternative body
// 	$mail->msgHTML(file_get_contents('contents.html'), dirname(__FILE__));
	$mail->msgHTML($content);
	//Replace the plain text body with one created manually
	$mail->AltBody = 'This is a plain-text message body';
	//Attach an image file
	$mail->addAttachment('./phpmailer_mini.gif');
	$mail->addAttachment('./phpmailer.gif');
	//send the message, check for errors
	if (!$mail->send()) {
// 		echo "Mailer Error: " . $mail->ErrorInfo;
		$err = $mail->ErrorInfo;
		if(strpos($err,'recipients failed')!==false){
			$errTmp = substr(strrchr($err,':'),1);
			return $errTmp; //返回错误者邮件,号隔开 str
		}else if (strpos($err,'data not accepted')!==false){
			return array('all miss',$recordAll);//所有邮件地址错误
		}else{
			return array('other error',$recordAll);//非邮件地址错误
		}

	} else {
// 		echo "Message sent!";
		return 1;
	}
}

//XML错误信息输出
function error_xml_notic($errcode){
    header("Content-type: text/xml");
    global $errmsg_arr;
	$err_msg = "<?xml version=\"1.0\" encoding=\"UTF-8\"?><error_response><code>{$errcode}</code><msg>{$errmsg_arr[$errcode]}</msg></error_response>";
	return $err_msg;
}


//JSON 或 JSONP错误信息输出
function error_json_notice($errcode){
    global $errmsg_arr,$callback;
	if(empty($callback)){
		$err_msg = '{"errCode":"'.$errcode.'","errMsg":"'.$errmsg_arr[$errcode].'"}';
	}else {
		$err_msg = $callback.'({"errCode":"'.$errcode.'","errMsg":"'.$errmsg_arr[$errcode].'"})';
	}
	return $err_msg;
}
function removeEmpty($v){
	if(trim($v)!==''){
		return true;
	}
}
?>

