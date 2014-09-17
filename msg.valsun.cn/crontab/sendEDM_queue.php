<?php
error_reporting(E_ALL);
require_once __DIR__ . '/vendor/autoload.php';
include_once __DIR__.'/../framework.php';
Core::getInstance();
require_once WEB_PATH.'lib/class.phpmailer.php';
require_once WEB_PATH.'model/amazonmessage.model.php';
use PhpAmqpLib\Connection\AMQPConnection;
date_default_timezone_set('PRC');
$connection = new AMQPConnection(MQ_SERVER, 5672, 'guest', '123456','valsun_message');
$channel = $connection->channel();
$channel->exchange_declare('ex_aliEDM', 'fanout', false, true, false);
$channel->basic_qos(null, 1, null);//该设置让一个繁忙中的消费者不再接收邮件
//list($queue_name, ,) = $channel->queue_declare("message", false, false, true, false);
$channel->queue_declare('que_aliEDM', false, true, false, false);
$channel->queue_bind('que_aliEDM', 'ex_aliEDM');
echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";

$callback = function($msg){
	$msg_obj        = new amazonmessageModel();
	$aliMark_obj	= new AliMarketModel();
	$ms             = json_decode($msg->body,true);
	extract($ms); 
	$receive = preg_split('/@/',$sendid)[0];
	$mail    = new PHPMailer(); //建立邮件发送类
	$mail->SMTPDebug=1;
	$mail->CharSet    ="UTF-8";
	$mail->IsSMTP();                            // 设定使用SMTP服务
	$mail->SMTPAuth   = true;                   // 启用 SMTP 验证功能
	$mail->SMTPSecure = "ssl";                  // SMTP 安全协议
	$mail->Host       = "smtp.gmail.com";       // SMTP 服务器
	$mail->Port       = 465;                    // SMTP服务器的端口号
	$mail->Username   = "$recieveid";  // SMTP服务器用户名
	$mail->Password   = "$pwd";        // SMTP服务器密码
	$mail->SetFrom("$recieveid", "$seller");
	//$mail -> FromEmail ='ddddnxzy@gmail.com'; // 设置发件人地址和名称
	//$mail->AddReplyTo("xzy@gmail.com","阿娜塔");
	// 设置邮件回复人地址和名称
	$mail->Subject    = "$subject";                     // 设置邮件标题
	$mail->AltBody    = "为了查看该邮件，请切换到支持 HTML 的邮件客户端";
	// 可选项，向下兼容考虑
	$mail->MsgHTML($msgbody);                         // 设置邮件内容
	$mail->AddAddress("$sendid","$buyer");			//收件人地址和收件人
	//echo $attach;
	if(!$mail->Send()) {
		echo "发送失败：" . $mail->ErrorInfo;
		$aliMark_obj->insertLostMail($seller,$buyer,$recieveid,$sendid,time(),$mail->ErrorInfo);
	} else {
		echo "恭喜，邮件发送成功！\n";
		$msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
	}
};
$channel->basic_consume('que_aliEDM', '', false, false, false, false, $callback);
while(count($channel->callbacks)) {
	echo "Game Over!!!\n";
	$channel->wait();
}
$channel->close();
$connection->close();

?>