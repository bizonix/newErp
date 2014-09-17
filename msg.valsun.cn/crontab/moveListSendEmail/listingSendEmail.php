<?php
//exit('以下发送形式为群发');
set_time_limit(0);
include_once '/data/web/msg.valsun.cn/framework.php';
require_once 'class.phpmailer.php';
require_once 'class.smtp.php';
Core::getInstance(); 
$emailContent       = "Hi, sorry to disturb you.<br/>";
$emailContent      .= "We would like to let you know that there is something wrong with eBay system, so some listings in our shop have been removed by mistake recently. Maybe you have received a message from eBay which says this item has been removed and advise you opening a case to solve this problem. Please don’t worry, we have sent the item within 24 hours after we receive payment. The average time for items to arrive is 20~30 days.";
$emailContent      .= "<br/>If there is any problem with your order or you haven’t received your order yet after about 20~30 days, please reply this message directly, we will try our best to reply your message and help you solve your problem in 28 hours.";
$emailContent      .= "<br/>Hope you have a nice day!<br/>Yours Sincerely,<br/>Yulan<br/>Customer Service Department";
$sql 			  	= "SELECT itemId, account FROM msg_movelistingemailsend WHERE status = 1 ";
$query            	= $dbConn->query($sql);
$result           	= $dbConn->fetch_array_all($query);
if(!empty($result)){
	foreach($result as $k => $v){
		$itemId 	= $v['itemId'];
		$account    = $v['account'];
		$getEmail   = "SELECT ebayEmail, passWord FROM msg_movelistingebayaccount WHERE account = '{$account}'";
		$getEmail   = $dbConn->fetch_first($getEmail);
		$FromEmail  = $getEmail['ebayEmail'];
		$FromPw     = $getEmail['passWord'];
		$sqlstr 	= "SELECT id, userId, email FROM msg_movelistinguser WHERE itemId = '{$itemId}' AND status = 1";
		$querystr 	= $dbConn->query($sqlstr);
		$detail   	= $dbConn->fetch_array_all($querystr);
		if(!empty($detail)){
			foreach($detail as $kk => $vv){
				$ismark             = 0;
				$emailId    		= $vv['id'];
				$ToUserName 		= $vv['userId'];
				$ToEmail  			= $vv['email'];
				$mail 			  	= new PHPMailer(); 					//建立邮件发送类
				$mail->CharSet    	= "UTF-8";                 
				$mail->IsSMTP();            
				$mail->SMTPDebug  	= 1;                				// 设定使用SMTP服务
				$mail->SMTPAuth   	= true;                   			// 启用 SMTP 验证功能
				$mail->SMTPSecure 	= "ssl";                  			// SMTP 安全协议
				$mail->Host       	= "smtp.gmail.com";     			// SMTP 服务器
				$mail->Port       	= 465;                 			    // SMTP服务器的端口号
				$mail->Username 	= $FromEmail;
				$mail->Password 	= $FromPw;
				$mail->SetFrom($FromEmail, $account);//发件人
				$mail->Subject  	= '';//邮件标题
				$mail->MsgHTML($emailContent);//邮件内容
				$mail->AddAddress($ToEmail, $ToUserName);//收件人及收件邮箱
				if(!$mail->Send()) {
					echo "发送失败：" . $mail->ErrorInfo."\n";
				}else {
					$ismark = 1;
					$upd 	= "UPDATE msg_movelistinguser SET status = 2 WHERE id = '{$emailId}'";
					$dbConn->query($upd);
					echo $ToUserName."-----邮件发送成功！"."\n";
				}
			}
		}
		$sendsql 		= "SELECT COUNT(*) AS totalNum FROM msg_movelistinguser WHERE itemId = '{$itemId}' AND status = 2";
		$sendArr 		= $dbConn->fetch_first($sendsql);
		$sendCount    	= $sendArr['totalNum'];
		$sendtime       = time();
		if($ismark == 1){
			$updsql     = "UPDATE msg_movelistingemailsend SET sendqty = '{$sendCount}', status = 2, sendtime = '{$sendtime}' WHERE itemId = '{$itemId}'";
			$dbConn->query($updsql);
		}
	}
}else{
	echo '没有需要发送的信息';
}
?>