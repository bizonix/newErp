<?php
require "/data/web/purchase.valsun.cn/framework.php";
Core::getInstance();
global $dbConn;
echo date('Y-m-d H:i')."\n";
$totalNum 	= getOwSkuIsWarnCount();
$page       = 1;
$totalPage 	= ceil($totalNum / 50);
echo '海外料号共['.$totalNum.']个预警,每页更新[50]条记录，共分['.$totalPage.']页同步'."\n";
while($page <= $totalPage){
	echo '开始同步第['.$page.']页'."\n";
	$skuArr 	= getOwSkuIsWarnInfo($page);
	foreach($skuArr as $k => $v){
		$sku 		 = $v['sku'];
		$isAlert     = $v['is_alert'];
		$paramArr2   = array(
		    'method'    => 'pur.getOwSkuReachDay',//API名称
		    'format'    => 'json',  //返回格式
		    'v'         => '1.0',   //API版本号
		    'username'  => 'purchase',
		    'sku'       => $sku
		);
		$rtnInfo 	= callOpenSystem($paramArr2);
		$rtnResult  = json_decode($rtnInfo, true);
		if($rtnResult['errCode'] == 200){
			$day = $rtnResult['data'];
			if($day != 1000){
				$upd 		= "UPDATE ow_stock SET reach_days = '{$day}' WHERE sku = '{$sku}'";
				$rtnUpd 	= $dbConn->query($upd);
				if($rtnUpd){
					echo '料号['.$sku.']更新成功'."\n";
				}else{
					echo '料号['.$sku.']更新失败'."\n";
				}
			}else{//料号不存在海运在途
				$status = getOwSkuStatus($sku);//获取料号海外仓销售状态,1:在线;2:暂时停售;3:停售
				if($status == 3 ){
					$defaultDay = 100;//停售默认设置100天
				}else{
					$defaultDay = 40;//其它海运在途找不到的料号默认设置40天
				}
				$updStr 	= "UPDATE ow_stock SET reach_days = '{$defaultDay}' WHERE sku = '{$sku}'";
				$rtnUpdStr 	= $dbConn->query($updStr);
				if($rtnUpdStr){
					echo '料号['.$sku.']更新成功'."\n";
				}else{
					echo '料号['.$sku.']更新失败'."\n";
				}
			}
		}
	}
	$page++;
	if($page > $totalPage){
		break;
	}
}
/**
 * 获取海外仓预警料号个数
 */
function getOwSkuIsWarnCount(){
	global $dbConn;
	$sql 		= "SELECT COUNT(*) AS totalNum FROM ow_stock";
	$query  	= $dbConn->query($sql);
	$data   	= $dbConn->fetch_one($query);
	return $data['totalNum'];
}
	
/**
 * 分页获取海外仓预警料号
 */
function getOwSkuIsWarnInfo($page){
	global $dbConn;
	$rtnData 	= array();
	$start      = ($page - 1) * 50;
	$pagenum    = 50;
	$sql 		= "SELECT sku, is_alert FROM ow_stock ";
	$sql       .= "limit $start, $pagenum ";
	$query  	= $dbConn->query($sql);
	$data   	= $dbConn->fetch_array_all($query);
	if(!empty($data)){
		$rtnData = $data;
	}
	return $rtnData;
}

/**
 *获取料号海外仓销售状态
 */
 function getOwSkuStatus($sku){
 	global $dbConn;
 	$sql 	= "SELECT oversea_status FROM ph_sku_status_change WHERE sku = '{$sku}'";
 	$query  = $dbConn->query($sql);
	$data   = $dbConn->fetch_one($query);
	return $data['oversea_status'];
 }
?>
