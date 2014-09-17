<?php
$taobaoUser	=	trim($argv[1]);
if(empty($taobaoUser)){
	echo "empty user!\n";
	exit;
}
ini_set('max_execution_time', 1800);
$FZAccountList = M('Account')->getAccountNameByPlatformId(12);//芬哲
$ZGAccountList = M('Account')->getAccountNameByPlatformId(13);//哲果
$accountKV = array_merge($FZAccountList, $ZGAccountList);
$accountId = array_search($taobaoUser, $accountKV);
if($accountId === false){
    echo "no exist the user in taobaoAccountList!\n";
	exit;
}
$carrierKV = array();
$carrierList = M('InterfaceTran')->getCarrierList(2);//获取所有的运输方式
foreach($carrierList as $key => $trans) {
    $carrierKV[$trans['id']] = $trans['carrierNameCn'];
}

$startTime = strtotime("-72 hour");
$hasShipmentOrderList = M('Order')->getUnMarkShippingOrdersByAS($accountId,$startTime);//根据账号及称重时间，筛选出需要标记发货的订单记录

$sum = count($hasShipmentOrderList);

if($sum > 0){
    $taobao = A('TaobaoButt');
    $taobao->setConfig($taobaoUser);
	foreach($hasShipmentOrderList as $val){
		$log_data	=	array();
		$omOrderId = $val['id'];
		$carrier = $carrierKV[$val['transportId']];
        $orderTracknumberList = M('Order')->getOrderTracknumberList(array($omOrderId)); //获取订单的跟踪号
        $orderTracknumber = $orderTracknumberList[$omOrderId]['tracknumber'][0]['tracknumber'];//该omOrderId的跟踪号
		if(empty($orderTracknumber)){
		   echo "omOrderId=$omOrderId no tracknumber,continue!\n";
           continue;
		}
        $recordnumber = $val['recordnumber'];
        $company_code = $taobao->getLogisticCode($carrier);
		
		$json_data = $taobao->taobaoLogisticsOfflineSend($recordnumber, $company_code, $orderTracknumber);
		if(isset($json_data['error_response'])){
			echo "omOrderId=$omOrderId! 标记失败\n";
		}else{
			echo "omOrderId=$omOrderId! 标记成功\n";
		}
	}
}

?>