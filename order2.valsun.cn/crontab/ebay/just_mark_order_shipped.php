<?php
	//脚本参数检验
	$nowtime 	= time();
	$account	= trim($argv[1]);
	include_once dirname(__DIR__)."/common.php";
	$accounts = M('Account')->getAccountNameByPlatformId(1);
	if (!in_array($account, $accounts)){
		exit("{$argv[1]} is wrong!\n");
	}
	//获取标记发货表未上传ebay的订单信息
	$accountId 		= M('Account')->getAccountIdByName($account);
	$markOrderIds	= M('Order')->getMarkOrders($accountId);
	if(!$markOrderIds){
		exit("No order to handel\n");
	}
	$markOrders		= M('Order')->getFullunshippedOrderById($markOrderIds);
	if(!$markOrders){
		exit('没有获取到订单信息');
	}
	$carrier = M('InterfaceTran')->getCarrierListById(2);
	
	#############类或API 实例化##############
	$time_start=time();
	$mctime = time();
	$ebaybutt = A('EbayButt');
	$ebaybutt->setToken($account);
	
	echo "=====[".date('Y-m-d H:i:s',$time_start)."]系统【开始】处理账号【 $account 】订单的只标发货====>\n";
	
	foreach($markOrders as $order_id=>$orders){
		$orderId 		= $orders['orderExtension']['orderId'];
		$carrier		= $carrier[$orders['order']['transportId']];
		//组装数据
		$tran_datas = array();
		foreach($orders['orderDetail'] as $orderdetail){
			$tran_data=array(
				'itemid' 	=> $orderdetail['orderDetailExtension']['itemId'],
				'tid'	 	=> $orderdetail['orderDetailExtension']['transId'],
				'orderid'	=> $orderId,
			);
			$tran_datas[] = array('sku'=>$orderdetail['orderDetail']['sku'],'tran'=>$tran_data);
		}
		$mark_shipped = false;
		if(empty($orderId)){   //线下导入订单不需要标记
			$mark_shipped == true;
		}else{
			if($ebaybutt->markOrderShipped($tran_datas)){
				$mark_shipped == true;
			}
		}
		//更新已经标记发货的订单状态
		if($mark_shipped){
			M('OrderModify')->updateMarkOrder($order_id,array('status'=>1),array('account'=>$accountId,'status'=>0)); //标记发货
			M('OrderModify')->updateData($order_id,array('ShippedTime'=>time()));		//更新标记发货时间
		}
	}

	$time_end=time();
	echo "\t\t\t[耗时:".ceil(($time_end-$time_start)/60)."分钟]\n";
	echo "<=====[".date('Y-m-d H:i:s',$time_end)."]系统【结束】处理账号【 $account 】订单的只标发货====\n";
?>
