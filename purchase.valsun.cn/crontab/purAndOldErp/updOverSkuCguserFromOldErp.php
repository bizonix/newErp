<?php
require "/data/web/purchase.valsun.cn/framework.php";
Core::getInstance();
global $dbConn;
$paramArr   = array(
    'method'    => 'pur.getOldErpOverSkuCguser',  //API名称
    'format'    => 'json',  //返回格式
    'v'         => '1.0',   //API版本号
    'username'  => 'purchase',
    'action'    => 'getCguser'
);
$rtnData    = callOpenSystem($paramArr, 'local');
$rtn        = json_decode($rtnData, true);
if(!empty($rtn)){
	foreach($rtn as $k => $v){
		$sku 		= $v['sku'];
		$cguser 	= $v['overCguser'];//海外采购员名称
		$cguserId   = getUserIdByTrueName($cguser);
		if($cguserId != ''){
			$upd 		= "UPDATE pc_goods SET OverSeaSkuCharger = '{$cguserId}' WHERE sku = '{$sku}'";
			$rtnLog 	= $dbConn->query($upd);
			if($rtnLog){
				echo $upd."\n";
			}
		}
	}
}else{
	echo '没有数据';
}
?>
