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
$hours			= isset($argv[5]) ? $argv[5] : 0;//几个小时内更新
$errnum 		= 1;//连续失败次数
$errMaxNum		= 3;//最大失败次数
$row			= 1;
$type			= 'email,sms';
$from			= '管拥军';
$to				= '管拥军';
$numberList 	= TransOpenApiModel::getTrackNumberList($page, $pagenum, $carrierId, $where, $hours);//获取符合条件的跟踪号列表
$total			= count($numberList);
$chidArr		= array('9'=>23,'8'=>43,'46'=>65,'47'=>66,'53'=>72);
echo $total."条数据抓取开始,时间".date('Y-m-d H:i:s')."\n\n";
foreach($numberList as $v) {
	$data		= array();
	$timestr	= date('Y-m-d H:i:s');
	$detailInfo = TransOpenApiModel::getTrackInfo($v['trackNumber'], $v['trackName']);
	echo "第{$row}/{$total}条记录======状态:{$v['status']}======",$v['trackNumber'],"===[$timestr]\n";
	echo $detailInfo,"\n";
	$trackNumber		= $v['trackNumber'];
	$detailInfo			= json_decode($detailInfo, true);
	print_r($detailInfo);
	if(is_array($detailInfo)) {
		$detailCount	= count($detailInfo['trackingEventList']);
		if($detailCount > 0) { //有跟踪数据插入
			$detailInfo['numberInfo']	= $v;
			foreach($detailInfo['trackingEventList'] as $key=>$val) {
				if($key == 0) { //得到跟踪号渠道信息
					$channelId	= TransOpenApiModel::getCarrierChannelByPostName($detailInfo['numberInfo']['carrierId'], $val['place']);
					if(!empty($channelId)) {
						$detailInfo['numberInfo']['channelId'] = $channelId;
					} else {
						$detailInfo['numberInfo']['channelId'] = $chidArr[$v['carrierId']];
					}
					if(eregi("^(R.+SG)",$v['trackNumber'])) $detailInfo['numberInfo']['channelId'] = 70;
					if(eregi("^(E.+SG)",$v['trackNumber'])) $detailInfo['numberInfo']['channelId'] = 71;
				}
				$trackNumber	= $v['trackNumber'];
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
							'realToCountry'	=> $detailInfo['Response_Info']['trackDes'],
							'lastEvent'		=> $event,
							'lastPostion'	=> $postion,
							'lastTime'		=> $trackTime,
							'trackTime'		=> time(),
						);
			//print_r($data);
			print_r($detailInfo);
			//目的地国家信息跟踪
			// $trackNames			= TransOpenApiModel::getTrackNameByCountry($carrierId, $v['toCountry']);
			$res_country		= 0;
			if(!empty($trackNames)) {
				// $trackName		= $trackNames['trackName'];
				// $res_country	= track_number_detail_country($trackName);
			}
			$res_detail			= TransOpenApiModel::addTrackNumberDetail($v['carrierId'], $sql_data);//详细数据插入
			$res_number			= TransOpenApiModel::updateTrackNumber($trackNumber, $data);//更新跟踪号摘要信息
			$res_warn			= TransOpenApiModel::autoWarnInfo($detailInfo);//自动预警
			echo $res_detail,"=====",$res_number,"=====",$res_warn,"=====",$res_country,"=====",date('Y-m-d H:i:s'),"\n";
			$errnum 			= 1;
		} else {
			if(isset($detailInfo['errCode'])) {
				if($errnum >= $errMaxNum) {
					echo date('Y-m-d H:i:s'),"===数据连续抓取失败{$errMaxNum},即将退出数据抓取\n";
					$message 	= TransOpenApiModel::sendMessage("{$type}","{$from}","{$to}",'物流系统服务器(121.40.69.217)报错,'.$detailInfo['ReturnValue'].'-cid:'.$carrierId.',请及时处理下！','物流系统服务器报错'.$detailInfo['errCode']);
					echo date('Y-m-d H:i:s'),"===信息发送状态:",$message,"\n";
					break;
				}
				$errnum++;
			} else {
				$res		= TransOpenApiModel::updateTrackNumber($trackNumber, array("trackTime"=>time()));
				check_cancel_order();
				$errnum		= 1;
			}
		}	
	}
	echo "第{$row}/{$total}条记录处理完毕!\n";
	$row++;
}

//跟踪目的地国家信息
function track_number_detail_country($trackName){
	global $trackNumber,$carrierId;
	$detailInfo	= TransOpenApiModel::getTrackInfo($trackNumber, $trackName);
	echo $detailInfo,"\n";
	$detailInfo	= json_decode($detailInfo, true);
	print_r($detailInfo);
	$data		= array();
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
			$sql_data	= $data;
			$data		= array( //更新跟踪号数据
							'status'		=> $detailInfo['Response_Info']['status'],
						);
			$res_number	= TransOpenApiModel::updateTrackNumber($trackNumber, $data);//更新跟踪号信息
			$res_detail	= TransOpenApiModel::addTrackNumberDetailByCountry($carrierId, $sql_data);//详细数据插入
			echo $res_detail,"=====",$res_number,"=====",date('Y-m-d H:i:s'),"\n";
			return $detailInfo['Response_Info']['status'];
		} else {
			if(!isset($detailInfo['errCode'])) {
				$res	= TransOpenApiModel::updateTrackNumber($trackNumber, array("trackTime"=>time()));
				// check_cancel_order();
				return 0;
			} else {
				return -1;
			}
		}	
	} else {
		return -1;
	}	
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
		$data 		= TransOpenApiModel::getOrderInfo($id);
		$data		= json_decode($data, true);
		print_r($data);
		$status		= isset($data[0]['ebay_status']) ? intval($data[0]['ebay_status']) : 0;
		if(in_array($status,array(663,613,666,615,669,674,670,681,716,717,723,721))) {
			$res	= TransOpenApiModel::updateTrackOrderInfo($id, array('status'=>8));
			echo $id,"=====更新退件完成,状态",$res,"\n";
		}
	}
	echo date('Y-m-d H:i:s', time()),"===检查订单状态结束===\n";
}

if(!count($numberList)) {
	print_r($argv);
	echo "无数据，请确认你输入的条件！\n";
}
echo "\n\n全部数据抓取执行完毕,完成时间".date('Y-m-d H:i:s')."\n";
exit;
?>
