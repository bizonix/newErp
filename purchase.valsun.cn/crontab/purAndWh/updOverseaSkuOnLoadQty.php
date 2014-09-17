<?php
require "/data/web/purchase.valsun.cn/framework.php";
Core::getInstance();
global $dbConn;
$paramArr   = array(
    'method'    => 'pur.getWhOverseaSkuCount',  //API名称
    'format'    => 'json',  //返回格式
    'v'         => '1.0',   //API版本号
    'username'  => 'purchase',
    'type'      => 'getCount'
);
$rtnData    = callOpenSystem($paramArr);
$rtn        = json_decode($rtnData, true);
$code       = $rtn['errCode'];
echo date('Y-m-d H:i')."\n";
if($code == 200){
	$totalnum 	= $rtn['data'];
	$page     	= 1;
	$totalpage 	= ceil($totalnum / 100);
	echo '总共['.$totalnum.']记录,每页[100]条,分['.$totalpage.']页同步'."\n";
	while($page <= $totalpage){
		echo '开始更新第'.$page.'页'."\n";
		$paramArr2   = array(
		    'method'    => 'pur.getWhOverseaSkuInfo',  //API名称
		    'format'    => 'json',  //返回格式
		    'v'         => '1.0',   //API版本号
		    'username'  => 'purchase',
		    'page'      => $page
		);
		$rtnInfo 	= callOpenSystem($paramArr2);
		$rtnResult  = json_decode($rtnInfo, true);
		if($rtnResult['errCode'] == 200){
			$result = json_decode($rtnResult['data'], true);
			if(!empty($result)){
				foreach($result as $k => $v){
					$sku 		= $v['sku'];
					$num 		= $v['num'];//封箱库存
					$roadQty 	= $v['roadQty'];//海运在途数量
					$upd 		= "UPDATE ow_stock SET onWayCountNew = '{$roadQty}', inBoxQty = '{$num}' WHERE sku = '{$sku}'";
					$rtnLog 	= $dbConn->query($upd);
					if($rtnLog){
						echo $upd."\n";
					}
				}
			}
		}else{
			echo $rtnResult['errMsg'];
		}
		$page++;
		if($page > $totalpage){
			break;
		}
	}
}else{
	echo $code['errMsg'];
}
?>
