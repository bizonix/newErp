<?php
/**
 * 功能：ebay平台EUB跟踪号自动申请脚本
 * 版本：1.0
 * 日期：2014/09/01
 * 作者： czq
 */
error_reporting(-1);
set_time_limit(0);
ini_set('soap.wsdl_cache_enabled', "0");//解决SOAP传送缓存问题
ini_set('default_socket_timeout', "300");
//脚本参数检验
if($argc!=2){
	exit("Usage: /usr/bin/php	$argv[0] limit \n");
}
//限制
$limit	=	!empty($argv[1]) ? trim($argv[1]) : 100;

$mctime = time();

$time_start=time();
echo "\n=====[".date('Y-m-d H:i:s',$time_start)."]申请【EUB】跟踪号订单开始\n";

$path   =   str_replace("\\", '/', __DIR__); //脚本绝对路径
require_once $path.'/scripts.comm.php';
date_default_timezone_set("Asia/Chongqing");
$wsdl = "http://shippingapi.ebay.cn/production/v3/orderservice.asmx?wsdl";
//$wsdl	= "http://epacketws.pushauction.net/v3/orderservice.asmx?wsdl"; //测试沙盒

$soapclient = new soapclient($wsdl);

//ebay账户的eub线上申请跟踪号,1为ebay平台，14为海外仓发货
$where = ' AND a.platformId in(1,14) AND a.transportId = 6'; 
$shipOrders = WhWaveOrderTransportModel::getOrderTransportRecords($limit,1,$where);
if(!$shipOrders){
	echo "没有待申请的信息记录\n";
	exit;
}

//批量获取订单详细信息
$orderids = array();
foreach($shipOrders as $orderinfo){
	$orderids[] = WhShippingOrderRelationModel::get_orderId($orderinfo['id']);
}
$orderisStr = implode("','",$orderids);
//调用订单系统接口批量获取订单信息
$orders = CommonModel::get_orderInfoFromOrderSys($orderisStr); 
$orders = $orders['data'];
if(empty($orders)){
	exit('未获取申请跟踪号的订单详细信息');
}
foreach($shipOrders as $orderinfo){
	$shipOrderId      	= $orderinfo['id'];
	$ebay_platformId	= $orderinfo['platformId'];  //平台名称
	$orderId			= WhShippingOrderRelationModel::get_orderId($shipOrderId);
	
	if(!in_array($ebay_platformId, array(1,14))){
		echo $ebay_account."非EBAY账号！\n";
		continue;
	}
	
	$ebay_username		= $orderinfo['username'];  
	$ebay_usermail		= $orderinfo['email'];
	$ebay_phone			= $orderinfo['phone'];  
	$ebay_street		= $orderinfo['street'].' '.$orderinfo['address2'].' '.$orderinfo['address3'];
	$ebay_city			= $orderinfo['city'];
	$ebay_state			= $orderinfo['state']; 
	$ebay_postcode		= $orderinfo['zipCode'];
	$ebay_countryname	= $orderinfo['countryName'];
	$ebay_couny			= $orderinfo['countrySn'];
	$ebay_currency		= $orderinfo['currency'];
	$ebay_userid		= $orderinfo['platformUsername'];
	$ebay_tid			= $orderinfo['payPalPaymentId']?$orderinfo['payPalPaymentId']:0;   //交易id
	$recordnumber		= $orderinfo['recordNumber'];
	$ebay_total			= $orderinfo['total'];
	$orderweight		= $orderinfo['orderWeight']/1000; //转化为KG
	$accountId			= $orderinfo['accountId'];
	$paymentTime		= $orders[$orderId]['order']['paymentTime'];
	$ordersTime			= $orders[$orderId]['order']['ordersTime'];
	
	if($orderweight > 2){
		echo $err_msg = "订单编号 {$shipOrderId} 重量 {$orderweight} 大于 2kg,不能走 EBAY-EUB!\n";
		continue;
	}
	$paymentTime = 1409655098;   //预留
	$ordersTime	 = 1409655098;  
	
	if(empty($paymentTime)){  
		echo $err_msg = "订单编号 {$shipOrderId} 没有付款时间,请联系销售人员添加!\n";
		continue;
	}
	$ebay_paidtime		= date('Y-m-d',$paymentTime)."T".date('H:i:s',$paymentTime);
	$ebay_createdtime	= date('Y-m-d',$ordersTime)."T".date('H:i:s',$ordersTime);
	//从缓存文件获取
	$eubaccount = whBaseModel::cache('accountId'.$accountId);
	if(!$eubaccount){
		$eubaccount = CommonModel::get_eub_account($accountId);
		$eubaccount = $eubaccount['data'];
		whBaseModel::cache('accountId'.$accountId,$eubaccount,3600*24*30);
	}
	if(empty($eubaccount)){
		echo $err_msg= "订单编号:{$shipOrderId}没有EUB授权设置,请联系物料部张容处理下!\n";
		continue;
	}
	
	$account_suffix 	= $eubaccount['account_suffix'];
	$APIDevUserID		= $eubaccount['account'];
	$APISellerUserID	= $eubaccount['dev_id'];
	$APIPassword		= $eubaccount['dev_sig'];
	
	$pname					= $eubaccount['pname'];
	$pcompany				= $eubaccount['pcompany'];
	$pcountry				= $eubaccount['pcountry'];
	
	$pprovince				= $eubaccount['pprovince'];
	$pcity					= $eubaccount['pcity'];
	$pdis					= $eubaccount['pdis'];
	$pstreet				= $eubaccount['pstreet'];
	$pzip					= $eubaccount['pzip'];
	$ptel					= $eubaccount['ptel'];
	$phone					= $eubaccount['phone'];
	$pemail					= $eubaccount['pemail'];
	
	/* return address */
	$rname			 		  = $eubaccount['rname'];	
	$rcompany			 	  = $eubaccount['rcompany'];	
	$rcountry			 	  = $eubaccount['rcountry'];	
	$rprovince			 	  = $eubaccount['rprovince'];	
	$rdis			 	  	  = $eubaccount['rdis'];	
	$rstreet			 	  = $eubaccount['rstreet'];	
	$rcity			 	 	  = $eubaccount['rcity'];	
	
	$ReturnAddress		= array(
		'Contact' 	=> $rname,
		'Company' 	=> $rcompany,
		'Street' 	=> $rstreet,
		'District' 	=> $rdis,
		'City' 		=> $rcity,
		'Province' 	=> $rprovince,
		'Postcode' 	=> $pzip,
		'Country'	=> '中国',
	
	);
	
	$ShipToAddress		= array(
		'Email' 		=> $ebay_usermail,
		'Company' 		=> '',
		'Contact' 		=> $ebay_username,
		'Phone' 		=> $ebay_phone,
		'Street' 		=> $ebay_street,
		'City' 			=> $ebay_city,
		'Province' 		=> $ebay_state,
		'Postcode' 		=> $ebay_postcode,  
		'Country' 		=> $ebay_countryname,  
		'CountryCode' 	=> $ebay_couny
	);
	
	$PickUpAddress		= array(
		'Contact' 		=> $pname,
		'Company' 		=> $pcompany,
		'Street' 		=> $pstreet,
		'District' 		=> $pdis,
		'City' 			=> $pcity,
		'Province' 		=> $pprovince,
		'Postcode' 		=> $pzip,
		'Country' 		=> $pcountry,  
		'Email' 		=> $pemail,  
		'Mobile' 		=> $ptel,
		'Phone' 		=> $phone
	);
	
	$dname					= $eubaccount['dname'];
	$dcompany				= $eubaccount['dcompany'];
	$dcountry				= $eubaccount['dcountry'];
	$dprovince				= $eubaccount['dprovince'];
	$dcity					= $eubaccount['dcity'];
	$ddis					= $eubaccount['ddis'];
	$dstreet				= $eubaccount['dstreet'];
	$dzip					= $eubaccount['dzip'];
	$dtel					= $eubaccount['dtel'];
	$demail					= $eubaccount['demail'];
	$shiptype				= $eubaccount['shiptype'];
	
	/* ShipFromAddress */
	$ShipFromAddress		= array(
							 "Contact" 	=> $dname,
							 "Company" 	=> $dcompany,
							 "Street"  	=> $dstreet,
							 "District" => $ddis,
							 "City" 	=> $dcity,
							 "Province" => $dprovince,
							 "Postcode" => $dzip,
							 "Country" 	=> $dcountry,
							 "Email" 	=> $demail,
							 "Mobile" 	=> $dtel,	
							);
	
	$orderdetails		= $orders[$orderId]['orderDetail']; //订单明细
	$unique_item_tid 	= array();
	$item = array();
	foreach($orderdetails as $sku=>$detail){

		$ebay_itemid		= $detail['orderDetail']['itemId']; //平台产品id
		$ebay_itemtitle		= $detail['orderDetailExtension']['itemTitle'];    
		$ebay_amount		= $detail['orderDetail']['amount'];
		$ebay_itemprice		= $detail['orderDetail']['itemPrice'];
		$sku				= $sku;
		$ebay_tid			= $detail['orderDetailExtension']['transactionID'];   //交易号
		$onliesku			= $detail['orderDetail']['onlinesku'];
		
		$weight = 0;
		foreach($detail['skuDetail']['skuInfo'] as $realskus){
			$weight += $realskus['skuDetail']['goodsWeight'] * $realskus['amount'];
		}
		
		$weight				= $weight > 0 ? $weight : 0.1;
		//取最后一个的报关信息
		$hsinfo = CommonModel::get_hsInfo(json_encode(array($realskus['skuDetail']['spu'])));
		$hsinfo = $hsinfo['data'][$realskus['skuDetail']['spu']];
		
		$goodsinfo['customsNameEN'] = $hsinfo['customsNameEN'];;
		$goodsinfo['customsName'] 	= $hsinfo['customsName'];
		$customsNameEN		= $hsinfo['customsNameEN'] ? $hsinfo['customsNameEN'] : $ebay_itemtitle;
		$customsName		= $hsinfo['customsName'] ? $goodsinfo['customsName']  : $ebay_itemtitle;
		$DeclaredValue		= 1; //不知从哪里获取，定义为1
		
		if((!in_array($ebay_itemid.'-'.$ebay_tid, $unique_item_tid) )){
			$unique_item_tid[] = $ebay_itemid.'-'.$ebay_tid;
			$item[]		= array(
				'CurrencyCode' 		=> $ebay_currency,
				'EBayEmail' 		=> $ebay_usermail,
				'EBayBuyerID' 		=> $ebay_userid,
				'EBayItemID' 		=> $ebay_itemid,
				'EBayItemTitle' 	=> $ebay_itemtitle,
				'EBayMessage' 		=> '',
				'EBaySiteID' 		=> "0",
				'EBayTransactionID' => $ebay_tid,  
				'Note' 				=> '',  
				'OrderSalesRecordNumber' => $recordnumber,
				'PaymentDate' 		=> $ebay_paidtime,
				'PayPalEmail' 		=> "0",
				'PayPalMessage' 	=> '',
				'PostedQTY' 		=> $ebay_amount,
				'ReceivedAmount' 	=> $ebay_total,
				'SalesRecordNumber' => $recordnumber,
				'SoldDate'			=> $ebay_createdtime,
				'SoldPrice'			=> $ebay_itemprice,
				'SoldQTY' 			=> $ebay_amount,
				'SKU'				=>array(
									'SKUID' => $sku,
									'Weight' => $weight * $ebay_amount,
									'CustomsTitleCN' => $customsName,
									'CustomsTitleEN' => $customsNameEN.' '.$sku,
									'DeclaredValue' => $DeclaredValue*$ebay_amount,
									'OriginCountryName' => "China",
									'OriginCountryCode' => "CN",
									)
				);
		}
	}
	
	sort($item);//EUB异常订单申请订单,修改料号,没有对应的物品交易号,不能申请跟踪号问题解决

	$params	= array(
				'Version' => "3.0.0",
				'APIDevUserID' => $APIDevUserID,
				'APIPassword' => $APIPassword,
				'APISellerUserID' => $APISellerUserID,
				'MessageID' => "135625622432",
				"OrderDetail"=> array("PickUpAddress"=>$PickUpAddress,
									  "ShipFromAddress"=>$ShipFromAddress,
									  "ShipToAddress"=>$ShipToAddress,
									  "ItemList"=>array("Item"=>$item),
									  "EMSPickUpType"=>$shiptype,
									  "ReturnAddress"=>$ReturnAddress
									  )
	         );
	var_dump($params);
	try {
		$functions = @$soapclient->AddAPACShippingPackage(array("AddAPACShippingPackageRequest"=>$params));
	}catch(Exception $e) {   
		print $e->getMessage();
		continue;
	}

	var_dump($shipOrderId,$APIDevUserID,$functions);
	
	foreach($functions as $aa){
		$bb		= (array)$aa;
		$ack	= $bb['Ack'];
		if($ack != 'Failure'){
			$TrackCode	= $bb['TrackCode'];
			echo $TrackCode."\n";
			$trackNumberData = array(
					'tracknumber'	=> $TrackCode,
					'shipOrderId'	=> $shipOrderId,
					'createdTime'	=> time(),
			);
			if(!WhOrderTracknumberModel::insert($trackNumberData)){
				echo $log .= "发货单-{$shipOrderId}插入跟踪号{$TrackCode}信息表失败\n";
			}
		}else{
			$Message = $bb['Message'];
			echo "订单编号:".$ordersn." EUB订单上传失败,失败原因是:[".$Message."]\n";
			$err_msg	= mysql_real_escape_string($Message);
			$params	= array(
						'Version' => "3.0.0",
						'APIDevUserID' => $APIDevUserID,
						'APIPassword' => $APIPassword,
						'APISellerUserID' => $APISellerUserID,
						'MessageID' => "135625622432",
						'EBayItemID' => $ebay_itemid,
						'EBayTransactionID' => $ebay_tid
						);
			$getTrackCode = @$soapclient->GetAPACShippingTrackCode(array("GetAPACShippingTrackCodeRequest"=>$params));
			if($getTrackCode->GetAPACShippingTrackCodeResult->Ack == 'Success'){
				echo "<font color=\"#0000FF\">注意：订单编号".$ordersn." EUB订单 已申请，跟踪号为 {$getTrackCode->GetAPACShippingTrackCodeResult->TrackCode}</font>";
			}else{
				var_dump($ordersn,$getTrackCode);
				$err_msg	= "{$err_msg}";
			}
		}
	}
}
$time_end = time();
echo "\n=====[耗时:".ceil(($time_end-$time_start)/60)."分钟]====\n";
echo "\n=====[".date('Y-m-d H:i:s',$time_end)."]申请【EUB】跟踪号订单结束\n";

?>