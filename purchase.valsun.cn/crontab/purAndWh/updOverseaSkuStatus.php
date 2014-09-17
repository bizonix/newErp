<?php
require "/data/web/purchase.valsun.cn/framework.php";
Core::getInstance();
global $dbConn;
echo date('Y-m-d H:i')."\n";
$totalNum 	= getOwSkuIsWarnCount();
$page       = 1;
$totalPage 	= ceil($totalNum / 50);
echo '海外料号共['.$totalNum.']个暂时停售料号,每页更新[50]条记录，共分['.$totalPage.']页同步'."\n";

while($page <= $totalPage){
	echo '开始同步第['.$page.']页'."\n";
	$skuArr 	= getOwSkuIsWarnInfo($page);
	$skuArr     = json_encode($skuArr);
	$paramArr   = array(
		'method'    => 'pur.getSkuInStockQty',//API名称
		'format'    => 'json',  //返回格式
		'v'         => '1.0',   //API版本号
		'username'  => 'purchase',
		'sku'       => $skuArr
	);
	$rtnInfo 	= callOpenSystem($paramArr);
	$rtnResult  = json_decode($rtnInfo, true);
	$errCode    = $rtnResult['errCode'];
	if($errCode == 200){
		$result = $rtnResult['data'];
		if(!empty($result)){
			foreach($result AS $k => $v){
				$sku      	= $v['sku'];
				$totalQty 	= $v['totalNum'];
				$status 	= getOwSkuStatus($sku);
				if($totalQty == 0){
					echo '['.$sku.']近三天没有入库信息'."\n";
				}else{
					if($status == 2){
						$upd 	= "UPDATE ph_sku_status_change SET ebay_status = 1,`b2b_status` = 1, `amazon_status` = 1, `gongxiaoshan_status` = 1,`oversea_status` = 1, `guonei_status` = 1 WHERE sku = '{$sku}'";
						$rtnUpd = self::$dbConn->query($upd);
						if($rtnUpd){
							echo '['.$sku.']修改在线状态成功'."\n";
						}else{
							echo '['.$sku.']修改在线状态失败'."\n";
						}
					}
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
 * 获取海外仓料号暂时停售个数
 */
function getOwSkuIsWarnCount(){
	global $dbConn;
	$sql 		= "SELECT COUNT(*) AS totalNum FROM ow_stock AS a JOIN ph_sku_status_change AS b ON a.sku = b.sku WHERE b.oversea_status = 2";
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
	$sql 		= "SELECT a.sku FROM ow_stock AS a JOIN ph_sku_status_change AS b ON a.sku = b.sku WHERE b.oversea_status = 2 ";
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
