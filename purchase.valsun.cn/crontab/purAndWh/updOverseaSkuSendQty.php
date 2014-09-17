<?php
require "/data/web/purchase.valsun.cn/framework.php";
Core::getInstance();
global $dbConn;
echo '开始更新时间:'.date('Y-m-d H:i:s')."\n";
$sql 		= "SELECT distinct(b.sku) AS sku FROM ph_ow_order AS a JOIN ph_ow_order_detail AS b ON a.id = b.po_id WHERE b.stockqty > 0 ORDER BY a.id ";
$query 	    = $dbConn->query($sql);
$result     = $dbConn->fetch_array_all($query);
if(!empty($result)){
	foreach($result as $k => $v){
		$sku 		= $v['sku'];
		$paramArr   = array(
    		'method'    => 'pur.getSkuSendQty', //API名称
    		'format'    => 'json',  //返回格式
    		'v'         => '1.0',   //API版本号
    		'username'  => 'purchase',
    		'sku'       => $sku
		);
		$rtnData    	= callOpenSystem($paramArr);
		$rtn        	= json_decode($rtnData, true);
		$totalSendQty 	= $rtn['data'];//获取仓库料号发货总数
		$str 			= "SELECT a.id AS mid, b.id AS did, b.count, b.stockqty, b.sendqty FROM ph_ow_order AS a JOIN ph_ow_order_detail AS b ON a.id = b.po_id WHERE b.sku = '{$sku}'";
		$strquery 	    = $dbConn->query($str);
		$info           = $dbConn->fetch_array_all($strquery);
		if(!empty($info)){
			foreach($info AS $kk => $vv){
				$mid 		= $vv['mid'];
				$did 		= $vv['did'];
				$stockQty 	= $vv['stockqty'];
				$sendQty    = $vv['sendqty'];
				if($totalSendQty > 0){
					if($totalSendQty >= $stockQty){//如果总发货数量大等于订单配货数量
						$sendQty = $stockQty;//订单发货数量=配货数量
					}else{
						$sendQty = $totalSendQty;
					}
					$upd 			= "UPDATE ph_ow_order_detail SET sendqty = '{$sendQty}' WHERE id = '{$did}'";
					echo $upd."\n";
					$totalSendQty 	= $totalSendQty - $sendQty;
					$dbConn->query($upd);
				}
			}
		}
	}
}
$sql 		= "SELECT id, recordnumber FROM ph_ow_order WHERE status = 4";
$sql 		= $dbConn->execute($sql);
$mainData 	= $dbConn->fetch_array_all($sql);
if(!empty($mainData)){
	foreach($mainData AS $k => $v){
		$recordnumber = $v['recordnumber'];
		$id 		= $v['id'];
		$mark       = true;
		$sqlstr 	= "SELECT count, stockqty, sendqty FROM ph_ow_order_detail WHERE po_id = '{$id}'";
		$query 		= $dbConn->execute($sqlstr);
		$detailData = $dbConn->fetch_array_all($query);
		if(!empty($detailData)){
			foreach($detailData AS $kk => $vv){
				$count 		= $vv['count'];
				$stockqty 	= $vv['stockqty'];
				$sendqty  	= $vv['sendqty'];
				if($count != $stockqty || $stockqty != $sendqty){
					$mark = false;
					break;
				}else{
					continue;
				}
			}
		}
		if($mark){
			$upd 	= "UPDATE ph_ow_order SET status = 5 WHERE id = '{$id}'";
			$rtnUpd = $dbConn->execute($upd);
			if($rtnUpd){
				echo '订单号【'.$recordnumber.'】完成发货'."\n";
			}
		}
	}
}
?>
