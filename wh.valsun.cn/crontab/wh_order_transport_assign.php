<?php
/**
 * 自动脚本通过物流系统获取运输方式
 * @author czq
 * @Date 2014-08-14
 */
//require_once SCRIPTS_PATH_CRONTAB."scripts.comm.php";
$path   =   str_replace("\\", '/', __DIR__); //脚本绝对路径
require_once $path.'/scripts.comm.php';

$limit 		= isset($argv[1]) ? $argv[1] : 100;
$shipOrders = WhWaveOrderTransportModel::getOrderTransportRecords($limit,0); 

if(!$shipOrders){
	echo "没有待申请的信息记录\n";
	exit;
}
$data = array();
$log = '';
foreach($shipOrders as $order){
	if(empty($order['countryName'])){
		$log .= "发货单{$order['shipOrderId']}没有国家名称\n";
		continue;
	}
	if(empty($order['orderWeight'])){
		$log .= "发货单{$order['shipOrderId']}没有重量\n";
		continue;
	}
	if(empty($order['selectShipping'])){
		$log .= "发货单{$order['shipOrderId']}没有可选择的运输方式\n";
		continue;
	}
	//获取真实订单ID
	$orderId  = WhShippingOrderRelationModel::get_orderId($order['shipOrderId']);
	//传递给物流系统的数据
	$data[$orderId] = array(
			'channelId'		=>	$order['selectShipping'],
			'country' 		=> 	rawurlencode($order['countryName']),
			'weight' 		=> 	$order['orderWeight']/1000,
	);
	//赛维美国专线传递转运中心和邮编前三位
	if(count(array_intersect(array(115,119,116), explode(',',$order['selectShipping']))) > 0 ){
		$data[$orderId]['postCode']	= substr($order['zipCode'],0,3); //邮编前三位
		$data[$orderId]['transitId'] 	= 2;  //转运中心
	}
}
$transportInfo = CommonModel::getTransportByApi($data);  //调用物流系统接口
if(empty($transportInfo['data'])){
	echo '没有获取申请信息';
	exit;
}

foreach($transportInfo['data'] as $orderId=>$transports){
	$transportId 		= 0;
	$channelId			= 0;
	$shipFee			= 999999999;//暂时取最大值处理
	$compareTrasprot	= array();
	$lastTransport		= array();
	//通过真实订单获取发货单ID
	$shipOrderId		= WhShippingOrderRelationModel::get_shipOrderId($orderId);
	foreach($transports['shipFee'] as $transport){
		if($transport['fee'] == 0){
			$log .= "发货单：{$shipOrderId}对应的运输方式{$transport['channelId']}没有获取运费\n";
			continue;
		}
		if($transport['carrierId'] == 0){
			$log .= "发货单：{$shipOrderId}没有获取运输方式\n";
			continue;
		}
		if($transport['carrierId'] == 6){
			$compareTrasprot = $transport;
		}
		if($transport['fee'] < $shipFee){
			$shipFee = $transport['fee'];
			$lastTransport 	= $transport; 			
		}
	}
	
	//运费最优策略,暂时只有EUB
	$bestTransport = getBestTransport($compareTrasprot,$lastTransport);
	$shipFee 		= $bestTransport['fee'];
	$transportId 	= $bestTransport['carrierId'];
	$channelId		= $bestTransport['channelId'];
	
	$insertData = array(
		'transportId' 	=> 	$transportId,
		'status'		=> 	1,
		'transportTime'	=>	time(),
	);
	//启用事物
	WhWaveOrderTransportModel::begin();
	//更新申请记录表
	if(!WhWaveOrderTransportModel::update($insertData,' shipOrderId = '.$shipOrderId.' AND status=0')){
		$log .= "发货单：{$shipOrderId} 更新运输方式-{$transportId}记录表失败\n";
		WhWaveOrderTransportModel::rollback();
		continue;
	}
	//更新发货单表
	$shipOrderData = array(
		'transportId'		=>	$transportId,
		'actualShipping'	=>	$shipFee,
		'channelId'			=>  $channelId,
	);
	if(!WhShippingOrderModel::update($shipOrderData,$shipOrderId)){
		$log .= "发货单：{$shipOrderId} 更新运输方式-{$transportId},运费-{$shipFee}发货单表失败\n";
		WhWaveOrderTransportModel::rollback();
		continue;
	}
	$log .= "发货单：{$shipOrderId} 更新运输方式 - {$transportId}，运费 - {$shipFee}记录表成功\n";
	WhWaveOrderTransportModel::commit();
}

//写日志
write_log("transport_assign/".date('Y-m')."/".date('H').".txt",$log);

?>