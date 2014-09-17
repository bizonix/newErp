<?php
require 'PHPMailerAutoload.php';

$mail = new PHPMailer;

$mail->isSMTP();                                      // Set mailer to use SMTP
// $mail->Host = 'smtp.exmail.qq.com';  //SMTP 服务器
// $mail->Port = 25;
// $mail->SMTPAuth = true;
// $mail->Username = 'valsun@sailvan.com';  //服务器邮箱帐号
// $mail->Password = 'sailvan_va';			     //服务器邮箱密码
// $mail->SMTPSecure = 'ssl'; 
$mail->Host = 'email-smtp.us-east-1.amazonaws.com';  //SMTP 服务器
$mail->Port = 25;
$mail->SMTPAuth = true;
$mail->Username = 'AKIAJN3AZQFIIL7RMGEQ';  //服务器邮箱帐号
$mail->Password = 'Aj+YwHEGlDnakSyvI9OuiZSVYwI0oVu0NhafLpibmKag';			     //服务器邮箱密码
$mail->SMTPSecure = 'TLS'; 
//$mail->setFrom('guanyongjun@sailvan.com','华成云商');
$mail->From = 'hnqdgyj@gmail.com';
$mail->FromName = 'Mailer';
$mail->addAddress('guanyongjun@sailvan.com', 'aaa');  // Add a recipient
//$mail->addAddress('ellen@example.com');               // Name is optional
//$mail->addReplyTo('info@example.com', 'Information');
//$mail->addCC('cc@example.com');
//$mail->addBCC('bcc@example.com');

//$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
//$mail->isHTML(true);                                  // Set email format to HTML

$mail->Subject = 'Here is the subject';
$mail->Body    = 'This is the HTML message body <b>in bold!</b>';
$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

if(!$mail->send()) {
   echo 'Message could not be sent.';
   echo 'Mailer Error: ' . $mail->ErrorInfo;
   exit;
}

echo 'Message has been sent';
?>