<?php
require "/data/web/wh.valsun.cn/framework.php";
Core::getInstance();

$onhand = OmAvailableModel::getTNameList("wh_sku_location","*","where arrivalInventory>0");
foreach($onhand as $on){
	$total = 0;
	$onhand1 = OmAvailableModel::getTNameList("pc_goods","*","where sku='{$on['sku']}'");
	$onhand2 = OmAvailableModel::getTNameList("wh_product_position_relation","*","where pId={$onhand1[0]['id']} and is_delete=0");
	foreach($onhand2 as $re){
		$total += $re['nums'];
	}
	
	$update = OmAvailableModel::updateTNameRow("wh_sku_location","set actualStock={$total}","where sku='{$on['sku']}'");
	echo $on['sku'];
	/*
	exit;
	print_r($total);exit;
	$fahuo2 = OmAvailableModel::getTNameList("wh_shipping_order as a inner join wh_shipping_order_relation as b on a.id=b.shipOrderId","a.id,b.originOrderId","where a.orderStatus=900 and b.originOrderId={$f['originOrderId']}");
	if(!empty($fahuo2)){
		print_r($fahuo2);echo"<br>";
		foreach($fahuo2 as $ttt){
			$fp   = fopen('repeat.txt', 'a+');
			$str  = $ttt['id'];
			$str  = "$str \n";
			fwrite($fp, $str);
		}
	}
	*/
}
exit;

?>
