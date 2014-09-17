<?php
@session_start();
error_reporting(-1);
set_time_limit(0);

$orderid 	= isset($_POST['ids']) ? trim($_POST['ids']) : '';
$po_obj 	= new PackingOrderModel();
$ordersinfo = $po_obj->getaSetOfOrderInfo($orderid);
$text = '';
$ln = "\r\n";
foreach($ordersinfo as $order){

	$text .= '0,"020"'.$ln;
	$text .= '1,"'.$order['recordNumber'].'"'.$ln;
	########################### 发件人信息 start ############################
	$text .= '4,"Shenzhen Sailvan Network TECHNOLOGY"'.$ln;
	$text .= '5,"Yaoan Ind Park"'.$ln;
	$text .= '6,"No. 53, Xiantian RD"'.$ln;
	$text .= '7,"Shenzhen"'.$ln;
	$text .= '9,"518116"'.$ln;
	$text .= '10,"319665412"'.$ln;
	$text .= '32,"Randy.qian"'.$ln;
	$text .= '117,"CN"'.$ln;
	$text .= '183,"86-755-89619623"'.$ln;
	############################ 发件人信息 end #############################

	$text .= '31,"Ms chen"'.$ln;
	$text .= '11,""'.$ln;
	$text .= '12,"'.$order['username'].'"'.$ln;
	$text .= '13,"'.$order['street'].'"'.$ln;
	$text .= '14,"'.$order['address2'].'"'.$ln;
	$text .= '15,"'.$order['city'].'"'.$ln;
	//$text .= '16,"NY"'.$ln;
	$text .= '17,"'.$order['zipCode'].'"'.$ln;
	$text .= !empty($order['landline']) ? '18,"'.$order['landline'].'"'.$ln : '18,"'.$order['phone'].'"'.$ln;
	$text .= '20,"319665412"'.$ln;		//联邦账号
	$text .= '23,"1"'.$ln;				//付款方式
	$text .= '24,"'.date("Ymd").'"'.$ln;				//邮寄日期
	$text .= '25,"'.$order['id'].'"'.$ln;				//客户note
	$text .= '29,""'.$ln;				//跟踪号
	$text .= '50,"'.$order['countrySn'].'"'.$ln;				//收件人国家简码 
	$text .= '68,"'.$order['currency'].'"'.$ln;			//币种
	$text .= '70,"2"'.$ln;										//关税付款方式
	$text .= '71,""'.$ln;										//关税账户
	$text .= '72,"1"'.$ln;										//销售条件
	$text .= '75,"KGS"'.$ln;									//重量单位
	$text .= '77,""'.$ln;
	$text .= '78,""'.$ln;

	$order_details = CommonModel::getExpressRemark($order['id']);
	if(!empty($order_details)){
		foreach ($order_details AS $key=>$order_detail){
			$i = $key+1;
			
			$text .= '79-'.$i.',"'.str_replace('´','\'',stripslashes($order_detail['description'])).'"'.$ln;
			$text .= '80-'.$i.',"CN"'.$ln;
			$text .= '81-'.$i.',"'.$order_detail['hamcodes'].'"'.$ln;
			$text .= '82-'.$i.',"'.$order_detail['amount'].'"'.$ln;
			$text .= '414-'.$i.',"PCS"'.$ln;
			$text .= '1030-'.$i.',"'.number_format($order_detail['price'],2,".","").'"'.$ln;		
			//$total_sku += $order_detail['ebay_amount'];
		}
	}
	
	$text .= '112,"'.round($order['calcWeight']*10, 2).'"'.$ln;		//包裹重量
	$text .= '113,"Y"'.$ln;
	$text .= '116,"1"'.$ln;
	$text .= '119,""'.$ln;
	$text .= '190,"N"'.$ln;
	//$text .= '414,"PCS"'.$ln;
	$text .= '541,"YNNNNNNNY"'.$ln;
	$text .= '1150,"Dylan Chen"'.$ln;
	$text .= '1273,"01"'.$ln;
	$text .= '1274,"3"'.$ln;
	if($order['countryName']=="United States"){
		$text .= '418,"Personal User Item"'.$ln;
	}
	$text .= '99,""'.$ln;
}

$titlename = date("YmdHis").'.txt';

header("Content-Type: application/octet-stream");    
if (preg_match("/MSIE/", $_SERVER['HTTP_USER_AGENT']) ) {    
	header('Content-Disposition:  attachment; filename="' .  $titlename . '"');    
} elseif (preg_match("/Firefox/", $_SERVER['HTTP_USER_AGENT'])) {    
    header('Content-Disposition: attachment; filename*="' .  $titlename . '"');    
} else {    
    header('Content-Disposition: attachment; filename="' .  $titlename . '"');    
}   
echo $text;
exit;

