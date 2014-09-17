<?php
error_reporting(E_ALL);
require_once __DIR__ . '/vendor/autoload.php';
include_once __DIR__.'/../framework.php';
Core::getInstance();
require_once WEB_PATH.'lib/class.phpmailer.php';
require_once WEB_PATH.'lib/class.smtp.php';
require_once WEB_PATH.'lib/Get_Email.class.php';
require_once WEB_PATH.'model/amazonmessage.model.php';
use PhpAmqpLib\Connection\AMQPConnection;
date_default_timezone_set('PRC');
$connection = new AMQPConnection(MQ_SERVER, 5672, MQ_USER, MQ_PSW);
$channel = $connection->channel();

$channel->exchange_declare(MQ_EXCHANGE_AMAZON, 'fanout', false, true, false);
//$channel->basic_qos(null, 1, null);//该设置让一个繁忙中的消费者不再接收邮件
//list($queue_name, ,) = $channel->queue_declare("message", false, false, true, false);
$channel->queue_declare(MQ_QUEUE_AMAZON, false, true, false, false);
$channel->queue_bind(MQ_QUEUE_AMAZON, MQ_EXCHANGE_AMAZON);
echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";

$callback = function($msg){
	//$msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
	$msg_obj   = new amazonmessageModel();
	$ms = json_decode($msg->body,true);
	var_dump($msg_obj);
	extract($ms); 
	$mail_get = new Get_Email();
	$receive = preg_split('/@/',$sendid)[0];
	$send    = preg_split('/@/',$recieveid)[0];
	//$message_id = $msg_obj->getMessageId($mid);  //message表中的message_id
	$msg_uid  =  preg_replace("/$send/",'',$msg_obj->getMessageId($mid)['message_id']);
	echo "The Next\n";
	$connect = $mail_get->mailConnect('imap.gmail.com','993',"$recieveid","$pwd",'INBOX','ssl');
	//$msgno=imap_msgno($connect, $massage_id);
	
	$mail = new PHPMailer(); //建立邮件发送类
	$mail->SMTPDebug=1;
	$mail->CharSet    ="UTF-8";
	$mail->IsSMTP();                            // 设定使用SMTP服务
	$mail->SMTPAuth   = true;                   // 启用 SMTP 验证功能
	$mail->SMTPSecure = "ssl";                  // SMTP 安全协议
	$mail->Host       = "smtp.gmail.com";       // SMTP 服务器
	$mail->Port       = 465;                    // SMTP服务器的端口号
	$mail->Username   = "$recieveid";  // SMTP服务器用户名
	$mail->Password   = "$pwd";        // SMTP服务器密码
	$mail->SetFrom("$recieveid", "$send");
	//$mail -> FromEmail ='ddddnxzy@gmail.com'; // 设置发件人地址和名称
	//$mail->AddReplyTo("xzy@gmail.com","阿娜塔");
	// 设置邮件回复人地址和名称
	$mail->Subject    = "RE: $subject";                     // 设置邮件标题
	$mail->AltBody    = "为了查看该邮件，请切换到支持 HTML 的邮件客户端";
	// 可选项，向下兼容考虑
	$mail->MsgHTML($msgbody);                         // 设置邮件内容
	$mail->AddAddress("$sendid","$receive");			//收件人地址和收件人
	//echo $attach;
	$mail->AddAttachment($attach); // 附件
	if(!$mail->Send()) {
		echo "发送失败：" . $mail->ErrorInfo;
	} else {
		echo "恭喜，邮件发送成功！\n";
		$msg_obj->updateMessageStatus(array($mid), '2');
		imap_setflag_full($connect,$msg_uid,"\\Seen",ST_UID);
		$msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
		$mail_get->closeMail();
		//imap_setflag_full($connect,$massage_id,'\Answered \Seen',SE_UID);
	}
};
$channel->basic_consume(MQ_QUEUE_AMAZON, '', false, false, false, false, $callback);
while(count($channel->callbacks)+10) {
	echo "Game Over!!!";
	$channel->wait();
}
$channel->close();
$connection->close();

?>