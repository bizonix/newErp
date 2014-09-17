<?php
error_reporting(0);
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
require_once '/data/web/tran.valsun.cn/crontab/config.php';
require_once SCRIPT_PATH."framework.php";
Core::getInstance();
$res 	= "";
$carrierId	= 0;
$data		= array();
$carrierId 	= isset($argv[1]) ? abs(intval($argv[1])) : 0;//运输方式ID
$days		= isset($argv[2]) ? abs(intval($argv[2])) : 1;//几天内跟踪号,不填默认1天内
if (empty($carrierId)) exit("运输方式名填写有误");
if (!is_numeric($days)) exit("天数只能为数字");
$res 	= TransOpenApiModel::getOrderTrackNumList($carrierId, $days);
echo $res,"\n\n";
$res	= json_decode($res, true);
if (!is_array($res['data']) || !count($res['data'])) {
	print_r($argv);
	exit("没有数据被获取，请确认条件！");
}
$res 	= $res['data'];
foreach ($res as $v) {
	$timestr= date('Y-m-d h:i:s', time());
	$v['ebay_tracknumber']	= str_replace(array('CNEE','CNRB','SGEM'),array('CN,EE','CN,RB','SG,EM'), $v['ebay_tracknumber']);
	$numArr	= preg_split("/[\|和,\s]+/",$v['ebay_tracknumber']);
	foreach ($numArr as $val) {
		$flag	= TransOpenApiModel::checkTrackNumber($val, $carrierId);
		if(!$flag) {
			if (empty($val)) continue;
			$res	= TransOpenApiModel::getCountriesStandardByName($v['ebay_countryname']);
			$countryId	= isset($res['id']) ? $res['id'] : 0;
			$data	= array(
						'trackNumber'	=> $val,
						'orderSn'		=> $v['ebay_id'],
						'weight'		=> round($v['orderweight']/1000,3),
						'cost'			=> $v['ordershipfee'],
						'carrierId'		=> $carrierId,
						'toCountry'		=> $v['ebay_countryname'],
						'countryId'		=> $countryId,
						'scanTime'		=> $v['scantime'],
						'recordId'		=> $v['recordnumber'],
						'platAccount'	=> $v['ebay_account'],
						'platForm'		=> $v['PlatForm'],
					);
			$res	= TransOpenApiModel::addTrackNumber($data);
			if ($res) {
				echo $res,"======",$v['ebay_id'],"=====",$val,"===添加成功===[$timestr]\n";
			} else {
				echo $res,"======",$v['ebay_id'],"=====",$val,"===添加失败===[$timestr]\n";
				echo "原因:[",TransOpenApiModel::$errMsg,"]\n";
			}
		} else {
			echo $val,"===已添加===[$timestr]\n";
		}
	}
}
exit;
?>
