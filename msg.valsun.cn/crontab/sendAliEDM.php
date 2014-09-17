<?php
error_reporting(E_ALL);
date_default_timezone_set('Asia/Shanghai');
include_once __DIR__.'/../framework.php'; // 加载框架
Core::getInstance(); // 初始化框架对象
//AWS SES
require_once  WEB_PATH . 'lib/SimpleEmailService.php';
require_once  WEB_PATH . 'lib/SimpleEmailServiceRequest.php';
require_once  WEB_PATH . 'lib/SimpleEmailServiceMessage.php';
set_time_limit(0);
//$tpl_obj   		= new CommonModel('msg_aliEDMcstpl');
$aliMark_obj	=  new AliMarketModel();
$EDMDatas		= $aliMark_obj->getAllEDMData();//获取Excell信息
unset($aliMark_obj);
foreach ($EDMDatas as $data){
	$seller  	 	=  $data['seller_id'];
	$customer_s  	=  $data['customer_s'];
	$gmail       	=  $data['gmail'];
	$shopnum     	=  $data['shopnum'];
	$aliMark_obj	=  new AliMarketModel();
	$orderDatas  	=  $aliMark_obj->getOrderinfo($seller);//查询订单系统，获取买家邮箱和姓名
if($seller !=='babyhouse' && $seller !=='Etime'){
	echo "$seller\n";
	continue;
}
	foreach ($orderDatas as $order){
		$counter = 0;
		if($counter++ > 20 ){
			sleep(3);
		}
		if(empty($order['ebay_usermail'])){
			continue;
		}
		//print_r($order);exit;
		$buyer          = empty($order['ebay_userid']) ? 'friend' : $order['ebay_userid'];
		$buyermail      = $order['ebay_usermail'];
		//看是否已经发送成功过邮件了，这个后面一定需要优化
		if(!($aliMark_obj->getSentMail($buyer, $seller, $buyermail, $gmail))){
			echo "seller:$seller   buyer:$buyer   buyermail:$buyermail has been saved!\n";
			continue;
		}
		$text           = <<<EOF
<pre style="font: normal 20px/30px  'STKaiti','Times New Roman',Georgia,Serif;">
		
Dear <span style='font-weight:bold;'>$buyer</span>,
		
We appreciate your orders from us all the time. We have a 10% discount sale from <span style='font-weight:bold;'>July-22 to
		
July-24</span>,welcome to my store: <a style='font-weight:bold;color:#000' href = 'http://www.aliexpress.com/store/$shopnum'>http://www.aliexpress.com/store/$shopnum</a>
		
If you have any further questions, please feel free to contact me. Thanks and have a nice day^-^
		
Best Regards,
		
<span style='font-weight:bold;'>$customer_s</span></pre>
EOF;
		$ses         =  new SimpleEmailService('AKIAIU65Y4DAUM55JPMQ','65cJ2xXwGO40w/Qs6nOyZV7j9d855019HhGuzn34');
		$message     =  new SimpleEmailServiceMessage();
		 //$message->addTo('hanqingxin@sailvan.com'); 			       		 //收件人
		 $message->addTo($buyermail); 			       		                     //收件人
		$message->setFrom($gmail);                                       //发件人
		//print_r($ses->listVerifiedEmailAddresses());
		$message->setSubject('Message from Aliexpress Store: '.$seller); // 邮件标题
		$message->setMessageFromString(NULL,$text); 		   			 //内容
		//设置标题和内容编码
		$message->setSubjectCharset('UTF-8');
		$message->setMessageCharset('UTF-8');
		try{
			if($ses->sendEmail($message)){
				echo '发送成功'."\n";
				$aliMark_obj->insertOKMail($seller, mysql_escape_string($buyer), $gmail, mysql_escape_string($buyermail), time(), mysql_escape_string($text));
			} else {
				echo '发送失败1'."\n";
			}
			unset($aliMark_obj);
		} catch (Exception $e){
			echo '发送失败2'."\n";
		}
		
			
	}
}