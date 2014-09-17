<?php
/**
 * 自动脚本申请跟踪号
 * @author czq
 * @Date 2014-08-14
 */ 
//require_once SCRIPTS_PATH_CRONTAB."scripts.comm.php";
$path   =   str_replace("\\", '/', __DIR__); //脚本绝对路径
require_once $path.'/scripts.comm.php';

$limit 		= isset($argv[1]) ? $argv[1] : 100;  //参数
$shipOrders = WhWaveOrderTransportModel::getOrderTransportRecords($limit,1); 
if(!$shipOrders){
	echo "没有待申请的信息记录\n";
	exit;
}

$data 	= array();
$orders = array();
$log 	= '';
foreach($shipOrders as $order){
	if(empty($order['countryName'])){
		$log .= "发货单{$order['shipOrderId']}没有国家名称\n";
		continue;
	}
	if(empty($order['transportId'])){
		$log .= "发货单{$order['shipOrderId']}没有可选择的运输方式\n";
		continue;
	}
	//如果是中国邮政，那么需要传递渠道id
	if($order['transportId']==1 || $order['transportId']==2){
		if($order['channelId'] == 0){
			$log .= "发货单{$order['shipOrderId']}没有渠道id\n";
			continue;
		}
	}
	$orderId = WhShippingOrderRelationModel::get_orderId($order['shipOrderId']);
	$data[$orderId] = array(
			'carrierId' 	=> 	$order['transportId'],
			'country' 		=> 	$order['countryName'],
			'channelId'		=> 	$order['channelId'],
	);
	$orders[$order['shipOrderId']] 	= $order;
}
//批量申请跟踪号
$transportInfo = CommonModel::getTracknumberByApi($data);

if(empty($transportInfo['data'])){
	echo '没有获取申请信息';
	exit;
}
foreach($transportInfo['data'] as $orderId=>$transport){
	$nowtime = time();
	$insertData = array(
		'tracknumber'		=>	$transport['trackNums']['trackNumber'],
		'status'			=> 	2,
		'tracknumberTime'	=>	$nowtime,
	);
	//通过真实订单id获取发货单id
	$shipOrderId = WhShippingOrderRelationModel::get_shipOrderId($orderId);
	if(empty($transport['trackNums']['trackNumber'])){
		$log .= "发货单：{$shipOrderId}未申请到跟踪号\n";
		continue;
	}
	
	WhWaveOrderTransportModel::begin();		//启用事物提价
	//更新申请记录表
	if(!WhWaveOrderTransportModel::update($insertData,' shipOrderId = '.$shipOrderId.' AND status=1')){
		$log .= "发货单：{$shipOrderId}更新申请跟踪号-{$transport['trackNums']['trackNumber']}记录表失败\n";
		WhWaveOrderTransportModel::rollback();
		continue;
	}
	
	$trackNumberData = array(
		'tracknumber'	=> $transport['trackNums']['trackNumber'],
		'shipOrderId'	=> $shipOrderId,
		'createdTime'	=> $nowtime,
	);
	if(!WhOrderTracknumberModel::insert($trackNumberData)){
		$log .= "发货单-{$shipOrderId}插入跟踪号{$transport['trackNums']['trackNumber']}信息表失败\n";
		WhWaveOrderTransportModel::rollback();
		continue;
	}
	//推送运输方式，跟踪信息，重量到订单系统
	$orderDataInfo = array(
		'orderId' 			=> WhShippingOrderRelationModel::get_orderId($shipOrderId),
		'orderWeight' 		=> $orders[$shipOrderId]['orderWeight'],
		'transportId' 		=> $orders[$shipOrderId]['transportId'],
		'channelId'			=> $orders[$shipOrderId]['channelId'],
		'tracknumber'		=> $transport['trackNums']['trackNumber'],
		'actualShipping'	=> $orders[$shipOrderId]['actualShipping'],
		'tracknumberTime'	=> $nowtime,
	);
	WhPushModel::pushTransportInfo($orderDataInfo);  //推送成功还是返回false
	//$log .= "发货单-{$shipOrderId}推送信息".json_encode($orderDataInfo)."失败\n";
	$log .= "发货单：{$shipOrderId}申请跟踪号-{$transport['tracknumber']}记录成功\n";
	WhWaveOrderTransportModel::commit();
}
//写日志
write_log("tracknumber_assign/".date('Y-m')."/".date('H').".txt",$log);
?>