<?php
error_reporting(0);
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
require_once '/data/web/tran.valsun.cn/crontab/config.php';
require_once SCRIPT_PATH."framework.php";
Core::getInstance();
$numberList 	= array();//跟踪号列表
$page 			= isset($argv[1]) ? abs(intval($argv[1])) : 1;//页码
$pagenum		= isset($argv[2]) ? abs(intval($argv[2])) : 2000;//每页多少条
$carrierId		= isset($argv[3]) ? abs(intval($argv[3])) : "";//运输方式ID
$where			= isset($argv[4]) ? $argv[4] : "scanTime>=".strtotime("-3 day".' 00:00:01')." AND toMarkTime = 0";//跟踪号状态
$numberList 	= TransOpenApiModel::getTrackNumberList($page, $pagenum, $carrierId, $where);//获取符合条件的跟踪号列表
$total			= count($numberList);
echo $total."条数据处理开始,时间".date('Y-m-d H:i:s',time())."\n\n";
if ($total>0) {
	$data	= array();
	$idArr	= array();
	$nums	= 100;
	$pages	= 1;
	$id		= 0;
	foreach ($numberList as $v) {
		$idArr[]	= $v['orderSn'];
	}
	$pages	= ceil(count($idArr)/$nums);
	for($i=1; $i<=$pages; $i++){
		for($j=0; $j<$nums; $j++){
			$id		= array_pop($idArr);
			if(!empty($id)) $idArrs[]	= $id;	
		}
		$ids		= implode(",",$idArrs);
		echo date('Y-m-d H:i:s')."===第{$i}/{$pages}批==={$ids}\n";
		unset($idArrs);
		$data 		= TransOpenApiModel::getErpOrderInfo($ids);
		$data		= json_decode($data, true);
		foreach ($data as $v) {
			$vals	= array(
						'weight'		=> $v['realWeight'],
						'cost'			=> $v['ordershipfee'],
						'toCountry'		=> $v['ebay_countryname'],
						'recordId'		=> $v['recordnumber'],
						'platAccount'	=> $v['ebay_account'],
						'platForm'		=> $v['PlatForm'],
						'toCity'		=> $v['ebay_city'],
						'toUserId'		=> $v['ebay_userid'],
						'toUserEmail'	=> $v['ebay_usermail'],
						'toMarkTime'	=> $v['ShippedTime'],
						'fhTime'		=> $v['fhTime'],
					);
			print_r($vals);
			$res	= TransOpenApiModel::updateTrackOrderInfo($v['ebay_id'], $vals);
			echo $v['ebay_id'],"=====更新完成,状态",$res,"\n";
		}
		echo date('Y-m-d H:i:s')."===第{$i}/{$pages}批===数据同步完毕\n";
		unset($data);
	}
}
if (!count($numberList)) {
	print_r($argv);
	echo "无数据，请确认你输入的条件！\n";
}
echo "\n\n全部数据抓取执行完毕,完成时间".date('Y-m-d H:i:s',time())."\n";
$dbConn->close();
exit;
?>
