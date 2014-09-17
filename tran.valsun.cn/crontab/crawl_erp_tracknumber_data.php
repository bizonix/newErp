<?php
error_reporting(0);
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
require_once '/data/web/tran.valsun.cn/crontab/config.php';
require_once SCRIPT_PATH."framework.php";
Core::getInstance();
$res 		= "";
$carrierId	= 0;
$data		= array();
$type 		= isset($argv[1]) ? $argv[1] : 0;//运输方式名,不填暂不通过
$days		= isset($argv[2]) ? $argv[2] : 1;//几天内跟踪号,不填默认1天内
$cat		= isset($argv[3]) ? $argv[3] : 'day'; //日期类型默认几天，可以选date日期
if(empty($type)) exit("运输方式名填写有误");
if(!in_array($cat,array('day','date'))) exit("日期类型有误！");
$res 		= TransOpenApiModel::getErpTrackNumList($type, $days , $cat);
echo $res,"\n\n";
$res		= json_decode($res, true);
if (!is_array($res) || !count($res)) {
	print_r($argv);
	exit("没有数据被获取，请确认条件！");
}
switch ($type) {
	case "中国邮政挂号":
		$carrierId	= 2;
	break;
	case "ems":
		$carrierId	= 5;
	break;
	case "eub":
		$carrierId	= 6;
	break;
	case "dhl":
		$carrierId	= 8;
	break;
	case "fedex":
		$carrierId	= 9;
	break;
	case "global mail":
		$carrierId	= 10;
	break;
	case "ups ground":
		$carrierId	= 46;
	break;
	case "usps":
		$carrierId	= 47;
	break;
	case "顺丰快递":
		$carrierId	= 48;
	break;
	case "圆通快递":
		$carrierId	= 49;
	break;
	case "申通快递":
		$carrierId	= 50;
	break;
	case "韵达快递":
		$carrierId	= 51;
	break;
	case "新加坡小包挂号":
		$carrierId	= 52;
	break;
	case "德国邮政挂号":
		$carrierId	= 53;
	break;
	case "UPS美国专线":
		$carrierId	= 62;
	break;
	case "UPS英国专线":
		$carrierId	= 96;
	break;
	case "UPS法国专线":
		$carrierId	= 97;
	break;
	case "UPS德国专线":
		$carrierId	= 98;
	break;
	case "俄速通挂号":
		$carrierId	= 79;
	break;
	case "俄速通大包":
		$carrierId	= 81;
	break;
	case "SurePost":
		$carrierId	= 65;
	break;
	case "UPS SurePost":
		$carrierId	= 95;
	break;
	case "俄速通平邮":
	case "新加坡DHL GM平邮":
	case "瑞士小包平邮":
	case "香港小包平邮":
	case "中国邮政平邮":
		$carrierId	= 61;
	break;
	case "新加坡DHL GM挂号":
		$carrierId	= 83;
	break;
	case "郑州小包挂号":
		$carrierId	= 86;
	break;
	case "瑞士小包挂号":
		$carrierId	= 88;
	break;
	case "比利时小包EU":
		$carrierId	= 89;
	case "澳邮宝挂号":
		$carrierId	= 93;
	break;
	default:
	exit("运输方式名有误！");
}
$total			= count($res);	
foreach($res as $v) {
	$timestr	= date('Y-m-d H:i:s');
	$v['ebay_tracknumber']	= str_replace(array('CNEE','CNRB','SGEM'),array('CN,EE','CN,RB','SG,EM'), $v['ebay_tracknumber']);
	$numArr					= preg_split("/[和,\s]+/",$v['ebay_tracknumber']);
	foreach($numArr as $val) {
		$flag				= TransOpenApiModel::checkTrackNumber($val, $carrierId);
		if(!$flag) {
			$res			= TransOpenApiModel::getCountriesStandardByName($v['ebay_countryname']);
			$countryId		= isset($res['id']) ? $res['id'] : 0;
			$data			= array(
								'trackNumber'	=> $val,
								'orderSn'		=> $v['ebay_id'],
								'weight'		=> $v['realWeight'],
								'cost'			=> $v['ordershipfee'],
								'carrierId'		=> $carrierId,
								'toCountry'		=> $v['ebay_countryname'],
								'countryId'		=> $countryId,
								'scanTime'		=> $v['scantime'],
								'recordId'		=> $v['recordnumber'],
								'platAccount'	=> $v['ebay_account'],
								'platForm'		=> $v['PlatForm'],
								'toCity'		=> $v['ebay_city'],
								'toUserId'		=> $v['ebay_userid'],
								'toUserEmail'	=> $v['ebay_usermail'],
								'toMarkTime'	=> $v['ShippedTime'],
								'fhTime'		=> $v['fhTime'],
							);
			$res			= TransOpenApiModel::addTrackNumber($data);
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
echo "\n\n".date('Y-m-d H:i:s')."=====一共处理：".$total."个跟踪号信息\n";
exit;
?>
