<?php
error_reporting(-1);
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
require_once '/data/web/tran.valsun.cn/crontab/config.php';
require_once SCRIPT_PATH."framework.php";
Core::getInstance();
$numberList 	= array();//跟踪号列表
$nodeList		= array();//预警节点列表
$page 			= isset($argv[1]) ? abs(intval($argv[1])) : 1;//页码
$pagenum		= isset($argv[2]) ? abs(intval($argv[2])) : 2000;//每页多少条
$carrierId		= isset($argv[3]) ? abs(intval($argv[3])) : "";//运输方式ID
$where			= isset($argv[4]) ? $argv[4] : "";//跟踪号状态
$hours			= isset($argv[5]) ? $argv[5] : 0;//几个小时内
$errnum 		= 1;//连续失败次数
$errMaxNum		= 3;//最大失败次数
$row			= 1;
$type			= 'email,sms';
$from			= '管拥军';
$to				= '管拥军';
$numberList 	= TransOpenApiModel::getTrackNumberList($page, $pagenum, $carrierId, $where, $hours);//获取符合条件的跟踪号列表
$total			= count($numberList);
echo $total."条数据抓取开始,时间".date('Y-m-d H:i:s',time())."\n\n";
foreach($numberList as $v) {
	$data		= array();
	$timestr	= date('Y-m-d H:i:s', time());
	//目的地国家信息跟踪
	$trackNames			= TransOpenApiModel::getTrackNameByCountry($carrierId, $v['toCountry']);
	if(empty($trackNames)) continue;
	$trackName			= $trackNames['trackName'];
	$detailInfo 		= TransOpenApiModel::getTrackInfo($v['trackNumber'], $trackName);
	echo "{$timestr}======",$v['trackNumber'],"=====第{$row}条记录=====状态:{$v['status']}\n";
	echo $detailInfo,"\n";
	$trackNumber		= $v['trackNumber'];
	$detailInfo			= json_decode($detailInfo, true);
	print_r($detailInfo);
	if(is_array($detailInfo)) {
		$detailCount	= count($detailInfo['trackingEventList']);
		if($detailCount > 0) { //有跟踪数据插入
			foreach($detailInfo['trackingEventList'] as $key=>$val) {
				$postion		= post_check($val['place']);
				$event			= post_check($val['details']);
				$trackTime		= strtotime($val['date']) ? strtotime($val['date']) : strtotime(trim(substr($val['date'],0,strpos($val['date'],' '))));
				$addTime		= time();
				array_push($data, "('{$trackNumber}','{$postion}','{$event}','{$trackTime}','{$addTime}')");
			}
			array_push($data, $trackNumber);
			print_r($data);
			$sql_data	= $data;//批量插入跟踪号详细信息数据
			$data		= array( //更新跟踪号数据
							'status'		=> $detailInfo['Response_Info']['status'],
						);
			$res_detail	= TransOpenApiModel::addTrackNumberDetailByCountry($v['carrierId'], $sql_data);//详细数据插入
			if($v['status'] != '0') {
				$res_number	= TransOpenApiModel::updateTrackNumber($trackNumber, $data);//更新跟踪号摘要信息
			} else {
				$res_number = $v['trackNumber'];
			}
			echo $res_detail,"=====",$res_number,"=====",date('Y-m-d H:i:s', time()),"\n";
			$errnum = 1;
		} else {
			if(isset($detailInfo['errCode'])) {
				if($errnum>=$errMaxNum) {
					echo date('Y-m-d H:i:s', time()),"===数据连续抓取失败{$errMaxNum},即将退出数据抓取\n";
					$message = TransOpenApiModel::sendMessage("{$type}","{$from}","{$to}",'物流系统服务器(121.40.69.217)报错,'.$detailInfo['ReturnValue'].'-cid:'.$carrierId.',请及时处理下！','物流系统服务器报错'.$detailInfo['errCode']);
					echo date('Y-m-d H:i:s', time()),"===信息发送状态:",$message,"\n";
					break;
				}
				$errnum++;
			} else {
				$res		= TransOpenApiModel::updateTrackNumber($trackNumber, array("trackTime"=>time()));
				check_cancel_order();
				$errnum 	= 1;
			}
		}	
	}
	$row++;
}

//检查ERP是否有邮局退回
function check_cancel_order(){
	global $dbConn,$carrierId,$trackNumber;
	echo date('Y-m-d H:i:s', time()),"===检查订单状态开始===\n";
	empty($carrierId) ? $condition = 1 : $condition = "carrierId={$carrierId}";
	$sql		= "SELECT orderSn,lastTime FROM trans_track_number WHERE {$condition} AND trackNumber = '{$trackNumber}'";
	$query		= $dbConn->query($sql);
	$res		= $dbConn->fetch_array($query);
	$id			= isset($res['orderSn']) ? $res['orderSn'] : 0;
	$lastTime 	= isset($res['lastTime']) ? $res['lastTime'] : 0;
	if(!empty($id)) {
		$data 	= TransOpenApiModel::getOrderInfo($id);
		$data	= json_decode($data, true);
		print_r($data);
		$status	= isset($data[0]['ebay_status']) ? intval($data[0]['ebay_status']) : 0;
		if(in_array($status,array(663,613,666,615,669,674,670,681,716,717,723,721))) {
			$res= TransOpenApiModel::updateTrackOrderInfo($id, array('status'=>8));
			echo $id,"=====更新退件完成,状态",$res,"\n";
		}
	}
	echo date('Y-m-d H:i:s', time()),"===检查订单状态结束===\n";
}

if(!count($numberList)) {
	print_r($argv);
	echo "无数据，请确认你输入的条件！\n";
}
echo "\n\n全部数据抓取执行完毕,完成时间".date('Y-m-d H:i:s',time())."\n";
exit;
?>
