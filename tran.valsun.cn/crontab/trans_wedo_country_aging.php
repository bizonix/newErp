<?php
error_reporting(-1);
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
require_once '/data/web/tran.valsun.cn/crontab/config.php';
require_once SCRIPT_PATH."framework.php";
Core::getInstance();
$numberList 	= array();//跟踪号列表
$data			= array();
$trackTime		= 0;
$page 			= isset($argv[1]) ? abs(intval($argv[1])) : 1;//页码
$pagenum		= isset($argv[2]) ? abs(intval($argv[2])) : 2000;//每页多少条
$carrierId		= isset($argv[3]) ? abs(intval($argv[3])) : "";//运输方式ID
$where			= isset($argv[4]) ? $argv[4] : "";//跟踪号状态
$hours			= isset($argv[5]) ? $argv[5] : 0;//最近几个小时内数据
$countrys		= TransOpenApiModel::getCountriesStandard("ALL");
$nodes			= TransOpenApiModel::getTrackNodeList(61);
print_r($nodes);
if(empty($nodes)) exit("未设置运德物流预警节点，暂没法分析数据！\n");
foreach($countrys as $c) {
	$country		= $c['countryNameEn'];
	$postion		= empty($where) ? "toCountry = '{$country}'" : $where." AND toCountry = '{$country}'";
	$numberList 	= TransOpenApiModel::getTrackNumberList($page, $pagenum, $carrierId, $postion, $hours, 'DESC');//获取符合条件的跟踪号列表
	$total			= count($numberList);
	echo date('Y-m-d H:i:s',time())."===".$c['countryNameEn']."===".$total."条数据分析开始!"."\n\n";
	//得到原始数据
	foreach($numberList as $v) {
		$timestr	= date('Y-m-d H:i:s', time());
		$times		= $v['scanTime'];
		$details	= TrackWarnInfoModel::listTrackNumberInfo($carrierId, $v['trackNumber']);
		$details	= json_decode($details, true);
		foreach($details as $detail) {
			foreach($nodes as $node) {
				if(strpos($detail['event'], $node['nodeName'])!==false) {
					$trackTime	= strtotime($detail['trackTime']);
					if($trackTime!==false) {
						$aging	= $trackTime - $times;
						$aging 	= ($aging <=0) ? 0 : $aging;
						$data[$v['toCountry']][$node['nodeName']][]	= $aging;
						//$data[$v['toCountry']]['trackNumbers'][]	= $v['trackNumber'];
						$times	= $trackTime;
						break;
					}
				}
			}
		}
	}
	Analysis_data($data);
	echo date('Y-m-d H:i:s',time())."===".$c['countryNameEn']."===".$total."条数据分析结束!"."\n\n";
}

//分析数据
function Analysis_data($data){
	global	$nodes;
	foreach($data as $key=>$v) {
		$country		= $key;
		foreach($nodes as $node) {
			if(!empty($v[$node['nodeName']])) {
				$total	= array_sum($v[$node['nodeName']]);
				$nums	= count($v[$node['nodeName']]);
				$aging	= ceil(($total / $nums) / 3600);
				$datas	= array("nodeId"=>$node['id'], "aging"=>$aging, "country"=>$country, "addTime"=>time(), "add_user_id"=>71);
				$res 	= TrackWarnNodeDataModel::addTrackWarnNodeData($datas);
				//echo date('Y-m-d H:i:s')."===".$node['id']."====".$country."===".$aging."===from:".implode(',',$v['trackNumbers'])."\n";
				echo date('Y-m-d H:i:s')."===".$node['id']."====".$country."===".$aging."\n";
			}
		}
	}
	unset($data);
}

if(!count($numberList)) {
	print_r($argv);
	echo "无数据，请确认你输入的条件！\n";
}
echo "\n\n全部数据分析完毕,完成时间===".date('Y-m-d H:i:s',time())."\n";
exit;
?>
