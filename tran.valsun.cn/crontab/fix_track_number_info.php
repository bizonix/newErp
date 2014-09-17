<?php
error_reporting(-1);
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
require_once '/data/web/tran.valsun.cn/crontab/config.php';
require_once SCRIPT_PATH."framework.php";
Core::getInstance();
$numberList 	= array();//跟踪号列表
$act 			= isset($argv[1]) ? $argv[1] : "";//act
$carrierId		= isset($argv[2]) ? abs(intval($argv[2])) : "";//运输方式ID
$condition		= isset($argv[3]) ? $argv[3] : "1";//条件
if (empty($carrierId)) exit("运输方式ID不能为空！");
if (empty($condition)) exit("查询条件不能为空！");
echo $act."===修复开始,时间".date('Y-m-d H:i:s',time())."\n\n";
switch ($act) {
	
	//修复跟踪详细信息乱码问题
	case "errWarnCode":
		$sql 	= "SELECT trackNumber FROM trans_track_number_detail_{$carrierId} WHERE `event` LIKE '%??%' AND {$condition} GROUP BY trackNumber";
		echo $sql,"\n";
		$query	= $dbConn->query($sql);
		$res	= $dbConn->fetch_array_all($query);
		foreach ($res as $v) {
			array_push($numberList, $v['trackNumber']);
		}
		if (count($numberList)>0) {
			$numberList	= array_map("map_commar",$numberList);
			$numberStr	= implode(",",$numberList);
			//重置跟踪摘要信息
			$sql		= "UPDATE trans_track_number set lastEvent = '',lastPostion = '', lastTime = 0 ,`status` = 0 WHERE trackNumber IN({$numberStr})";
			echo $sql,"\n";
			$res		= $dbConn->query($sql);
			echo $res,"==执行结果\n";
			//删除详细追踪信息，便于重新追踪
			$sql 		= "DELETE FROM trans_track_number_detail_{$carrierId} WHERE trackNumber IN({$numberStr})";
			echo $sql,"\n";
			$res		= $dbConn->query($sql);
			echo $res,"==执行结果\n";
			unset($numberList);
		} else {
			echo "没有符合条件的结果！";
		}			
	break;
	
	//修复新加坡EMS跟踪号归属问题
	case "errEmsSG":
		$sql 	= "SELECT trackNumber FROM trans_track_number WHERE carrierId = {$carrierId} AND trackNumber LIKE 'E%SG' AND {$condition}";
		echo $sql,"\n";
		$query	= $dbConn->query($sql);
		$res	= $dbConn->fetch_array_all($query);
		foreach ($res as $v) {
			array_push($numberList, $v['trackNumber']);
		}
		if (count($numberList)>0) {
			$numberList	= array_map("map_commar",$numberList);
			$numberStr	= implode(",",$numberList);
			//更新跟踪摘要信息
			$sql		= "UPDATE trans_track_number set carrierId = 52,channelId = 71 WHERE trackNumber IN({$numberStr})";
			echo $sql,"\n";
			$res		= $dbConn->query($sql);
			echo $res,"==执行结果\n";
			unset($numberList);
		} else {
			echo "没有符合条件的结果！";
		}
	break;
	
	//重置某个运输方式的跟踪信息
	case "errCarrier":
		if(in_array($carrierId,array('2','6'))) exit("不支持的运输方式重置！\n");
		$sql	= "SELECT trackNumber FROM trans_track_number WHERE carrierId = {$carrierId} AND {$condition}";
		echo $sql,"\n";
		$query	= $dbConn->query($sql);
		$res	= $dbConn->fetch_array_all($query);
		foreach ($res as $v) {
			array_push($numberList, $v['trackNumber']);
		}
		if (count($numberList)>0) {
			$numberList	= array_map("map_commar",$numberList);
			$numberStr	= implode(",",$numberList);
			//重置跟踪摘要信息
			$sql		= "UPDATE trans_track_number set lastEvent = '',lastPostion = '', lastTime = 0 ,`status` = 0 WHERE trackNumber IN({$numberStr})";
			echo $sql,"\n";
			$res		= $dbConn->query($sql);
			echo $res,"==执行结果\n";
			//删除详细追踪信息，便于重新追踪
			$sql 		= "DELETE FROM trans_track_number_detail_{$carrierId} WHERE trackNumber IN({$numberStr})";
			echo $sql,"\n";
			$res		= $dbConn->query($sql);
			echo $res,"==执行结果\n";
			unset($numberList);
		} else {
			echo "没有符合条件的结果！";
		}		
	break;
	
	//重置处理时间为负的跟踪数据
	case "errProTime":
		$sql	= "SELECT trackNumber FROM trans_track_number_warn_info WHERE carrierId = {$carrierId} AND processTime < 0 GROUP BY trackNumber";
		echo $sql,"\n";
		$query	= $dbConn->query($sql);
		$res	= $dbConn->fetch_array_all($query);
		foreach ($res as $v) {
			array_push($numberList, $v['trackNumber']);
		}
		if (count($numberList)>0) {
			$numberList	= array_map("map_commar",$numberList);
			$numberStr	= implode(",",$numberList);
			//重置跟踪摘要信息
			$sql		= "UPDATE trans_track_number set lastEvent = '',lastPostion = '', lastTime = 0 ,`status` = 0 WHERE trackNumber IN({$numberStr})";
			echo $sql,"\n";
			$res		= $dbConn->query($sql);
			echo $res,"==执行结果\n";
			//删除详细追踪信息，便于重新追踪
			$sql 		= "DELETE FROM trans_track_number_detail_{$carrierId} WHERE trackNumber IN({$numberStr})";
			echo $sql,"\n";
			$res		= $dbConn->query($sql);
			echo $res,"==执行结果\n";
			unset($numberList);
		} else {
			echo "没有符合条件的结果！";
		}		
	break;
	
	//修复订单发货时间异常问题
	case "errScanTime":
		$nums	= 100;
		$pages	= 1;
		$id		= 0;
		$data	= array();
		$status	= array(663,613,666,615,669,674,670,681,716,717,723,721);
		$sql	= "SELECT orderSn FROM `trans_track_number` WHERE scanTime = 0 AND status < 2 ORDER BY id ASC";
		echo $sql,"\n";
		$query	= $dbConn->query($sql);
		$res	= $dbConn->fetch_array_all($query);
		foreach ($res as $v) {
			$idArr[] = $v['orderSn'];
		}
		$pages	= ceil(count($idArr)/$nums);
		for($i=1; $i<=$pages; $i++){
			for($j=0; $j<$nums; $j++){
				$id			= array_pop($idArr);
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
							'trackNumber'	=> $v['ebay_tracknumber'],
							'scanTime'		=> $v['scantime'],
						);
				if(in_array($v['ebay_status'],$status)) $vals['status'] = 8;
				$res	= TransOpenApiModel::updateTrackOrderInfo($v['ebay_id'], $vals);
				echo $v['ebay_id'],"=====更新完成,状态",$res,"\n";
			}
			echo date('Y-m-d H:i:s')."===第{$i}/{$pages}批===数据修复完毕\n";
			unset($data);
		}
	break;
	
	default:
	exit('非法的修复参数！');
}
echo $act."===修复完成,时间".date('Y-m-d H:i:s',time())."\n\n";
exit;
?>
