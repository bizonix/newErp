<?php
	//脚本参数检验
	$nowtime 	= time();
	$account	= trim($argv[1]);
	include_once dirname(__DIR__)."/common.php";
	$accounts = M('Account')->getAccountNameByPlatformId(1);
	if (!in_array($account, $accounts)){
		exit("{$argv[1]} is wrong!\n");
	}
	
	$carriers = M('InterfaceTran')->getCarrierListById(2);
	
	#############类或API 实例化##############
	$ebaybutt = A('EbayButt');
	$ebaybutt->setToken($account);
	
	$carrierList = array();
	$carrierList['香港小包平邮'] 	= 'HKPost';
	$carrierList['香港小包挂号'] 	= 'HKpost';
	$carrierList['中国邮政平邮'] 	= 'ChinaPost';
	$carrierList['中国邮政挂号'] 	= 'ChinaPost';
	$carrierList['EUB'] 		= 'ChinaPost';
	$carrierList['UPS'] 		= 'UPS';
	$carrierList['DHL'] 		= 'DHL';
	$carrierList['FedEx'] 		= 'FedEx';
	$carrierList['USPS'] 		= 'USPS';
	$carrierList['UPS Ground'] 	= 'UPS Ground';
	$carrierList['飞腾DHL'] 		= '飞腾DHL';
	$carrierList['UPS美国专线'] 	= 'UPS_US';
	$carrierList['SurePost'] 	= 'SurePost';
	
	
	$nowtime	= time();
	$mctime		= $nowtime;
	$accountId 	= M('Account')->getAccountIdByName($account);
	$orderids 	= M('Order')->getUploadTrackOrderIds($accountId,$platformId);
	if(!$orderids){
		exit("没有获取订单信息\n");
	}
	$uploadTrackOrders   = M('Order')->getFullshippedOrderById($orderids); 
	
	$time_start=time();	
	echo "=====[".date('Y-m-d H:i:s',$time_start)."]系统【开始】处理账号【 $account 】订单 上传发货信息====>\n";
	
	foreach($uploadTrackOrders as $order_id=>$orders){
		$orderId 		= $orders['orderExtension']['orderId'];
		$transportId	= $carrier[$orders['order']['transportId']];
		$tracknumber	= $orders['orderTracknumber']['trancknumber'];
		if(empty($tracknumber)){
			continue; //无跟踪号不上传
		}
		//组装数据
		$tran_datas = array();
		foreach($orders['orderDetail'] as $orderdetail){
			$tran_data=array(
					'itemid' 			=> $orderdetail['orderDetailExtension']['itemId'],
					'tid'	 			=> $orderdetail['orderDetailExtension']['transId'],
					'orderid'			=> $orderId,
					'ebay_carrier'		=> $carrierList[$carriers[$transportId]],
					'ebay_tracknumber'	=> $tracknumber,
			);
			$tran_datas[] = array('sku'=>$orderdetail['orderDetail']['sku'],'tran'=>$tran_data);
		}

		echo "eBay订单号: $ebay_orderid 来自账号: $account \n";
		if($ebaybutt->uploadTrackNo($tran_datas)){
			if(!checkMarkShipped($order_id)){
				//检查是否之前没有标记发货成功
				M('OrderModify')->updateMarkOrder($order_id,array('status'=>1)); //标记发货
			}
			M('OrderModify')->updateShippedOrder($order_id,array('ShippedTime' => time(),'marketTime'=>time()));		//更新发货时间
		}
	}
	$time_end=time();
	echo "\t\t\t[耗时:".ceil(($time_end-$time_start)/60)."分钟]\n";
	echo "<=====[".date('Y-m-d H:i:s',$time_end)."]系统【结束】处理账号【 $account 】订单 上传发货信息====\n";
?>