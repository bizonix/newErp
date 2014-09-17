<?php

$fp = fsockopen("smtp.exmail.qq.com",25,$errno,$errstr,60); 
if(!$fp) {
    echo $errstr.$errno."<br/>"; 
} else { 
    echo 'ok <br> \n ';
}

require 'PHPMailerAutoload.php';

$mail = new PHPMailer;
$mail->CharSet    ="UTF-8";                 //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置为 UTF-8
$mail->IsSMTP();                            // 设定使用SMTP服务
$mail->SMTPAuth   = true;                   // 启用 SMTP 验证功能
$mail->SMTPSecure = "ssl";                  // SMTP 安全协议
$mail->Host       = "smtp.exmail.qq.com";       // SMTP 服务器
$mail->Port       = 25;                    // SMTP服务器的端口号
$mail->Username   = "valsun@sailvan.com";  // SMTP服务器用户名
$mail->Password   = "sailvan_va";        // SMTP服务器密码
$mail->SetFrom('valsun@sailvan.com', 'aa');    // 设置发件人地址和名称
$mail->AddReplyTo("valsun@sailvan.com","aa"); 
                                            // 设置邮件回复人地址和名称
$mail->Subject    = '';                     // 设置邮件标题
$mail->AltBody    = "为了查看该邮件，请切换到支持 HTML 的邮件客户端"; 
                                            // 可选项，向下兼容考虑
$mail->MsgHTML('this is test');                         // 设置邮件内容
$mail->AddAddress('guanyongjun@sailvan.com', "gyj");
//$mail->AddAttachment("images/phpmailer.gif"); // 附件 
if(!$mail->Send()) {
    echo "发送失败：" . $mail->ErrorInfo;
} else {
    echo "恭喜，邮件发送成功！";
}

// $mail->isSMTP();   
// $mail->CharSet ="UTF-8";                                    // Set mailer to use SMTP
// $mail->Host = 'smtp.exmail.qq.com';  // Specify main and backup server
// $mail->SMTPAuth = true;
// $mail->Port = 25;                              // Enable SMTP authentication
// $mail->Username = 'valsun@sailvan.com';                            // SMTP username
// $mail->Password = 'sailvan_va';                           // SMTP password
// $mail->SMTPSecure = 'ssl';                            // Enable encryption, 'ssl' also accepted

// $mail->From = 'valsun@sailvan.com';
// $mail->FromName = 'Mailer';
// $mail->addAddress('23171778@qq.com', 'aaa');  // Add a recipient
//$mail->addAddress('ellen@example.com');               // Name is optional
//$mail->addReplyTo('info@example.com', 'Information');
//$mail->addCC('cc@example.com');
//$mail->addBCC('bcc@example.com');

//$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
//$mail->isHTML(true);                                  // Set email format to HTML

// $mail->Subject = 'Here is the subject';
// $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
// $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

// if(!$mail->send()) {
   // echo 'Message could not be sent.';
   // echo 'Mailer Error: ' . $mail->ErrorInfo;
   // exit;
// }

// echo 'Message has been sent';
?>