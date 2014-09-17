<?php
error_reporting(E_ALL);
require_once('SimpleEmailService.php');
require_once('SimpleEmailServiceRequest.php');
require_once('SimpleEmailServiceMessage.php');


//$ses = new SimpleEmailService('AKIAJPVRUB4LGXGH3TIA', 'KnbZBhbold1tmaKXJTQWkojbN/U0Ui90LmzNb/VW');
$ses = new SimpleEmailService('AKIAIA6Y4B5I5AGUJL4A', 'PZbv8lalsoKGZUsmdBcBp9ugAlM+/hV/3uZoj1+s');
// print_r($ses->verifyEmailAddress('acitylife88@gmail.com'));

//print_r($ses->listVerifiedEmailAddresses());

$m = new SimpleEmailServiceMessage();
$m->addTo('guanyongjun@sailvan.com'); //收件人
// $m->addCC(array('23171778@qq.com','hnqdgyj@qq.com')); //抄送 收件人
// $m->addBCC('hnqdgyj@hotmail.com'); //密送 收件人
$m->setFrom('acitylife88@gmail.com'); //发件人
$m->setSubject('Hello, world!'); // 邮件标题
$m->setMessageFromString(NULL, '<b>This is the message body.<b><br/><font color="red">gyj</font>'); //内容
//设置标题和内容编码
$m->setSubjectCharset('UTF-8');
$m->setMessageCharset('UTF-8');

print_r($ses->sendEmail($m)); //发送邮件结果

print_r($ses->getSendQuota()); //摘要统计
print_r($ses->getSendStatistics()); //发送统计

//发邮件 base 64编码,针对中文
function address_encode($str) {
    return '=?UTF-8?B?' . base64_encode($str) . '?=';
}
?>