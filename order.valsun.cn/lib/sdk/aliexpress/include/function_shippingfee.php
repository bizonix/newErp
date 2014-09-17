<?php
//计算香港小包平邮的实际运费 
function calchkpost($totalweight,$countryname){
	
	global $dbcon;
	
	$ss		= "select * from ebay_hkpostcalcfee where countrys like '%$countryname%'";
	$ss		= $dbcon->execute($ss);
	$ss		= $dbcon->getResultArray($ss);
	
	$rate			= $ss[0]['discount']?$ss[0]['discount']:1;
	$kg				= $ss[0]['firstweight'];
	$handlefee		= $ss[0]['handlefee'];
	
	$shipfee		= $kg * $totalweight + $handlefee;
	if($rate > 0) $shipfee = $shipfee * $rate;
	return $shipfee;
}

//计算香港小包挂号的费用 
function calchkghpost($totalweight,$countryname){

	global $dbcon;
	
	$ss		= "select * from ebay_hkpostghcalcfee where countrys like '%$countryname%'";
	$ss		= $dbcon->execute($ss);
	$ss		= $dbcon->getResultArray($ss);
	
	$rate			= $ss[0]['discount']?$ss[0]['discount']:1;
	$kg				= $ss[0]['firstweight'];
	$handlefee		= $ss[0]['handlefee'];
	$shipfee		= $kg * $totalweight + $handlefee;
	if($rate > 0) $shipfee = $shipfee * $rate;
	return $shipfee;
}

//中国邮政平邮
function calcchinapostpy($totalweight, $countryname, $discount=true){
	
	global $dbcon;
	
	$dd		= "SELECT * FROM  `ebay_cppycalcfee` where countrys like '%$countryname%' ";
	$dd		= $dbcon->execute($dd);
	$dd		= $dbcon->getResultArray($dd);
	
	$rate			= $dd[0]['discount']?$dd[0]['discount']:1;
	$kg				= $dd[0]['firstweight'];
	
	$shipfee		= $kg * $totalweight;
	if (!$discount){
		return $shipfee;
	}
	if($rate > 0) $shipfee = $shipfee * $rate;
	return $shipfee;
}

//中国邮政挂号
function calcchinapostgh($totalweight,$countryname, $discount=true){

	global $dbcon;
	if(in_array($countryname,array("Russian Federation","Russia"))){
	   $shipfee = 96.3*$totalweight+8;
	   return $shipfee;
	}
	$dd		= "SELECT * FROM  `ebay_cpghcalcfee` where countrys like '%$countryname%' ";
	$dd		= $dbcon->execute($dd);
	$dd		= $dbcon->getResultArray($dd);
	if (empty($dd)){
		return 11314;
	}
	$rate			= $dd[0]['discount']?$dd[0]['discount']:1;
	$kg				= $dd[0]['firstweight'];
	$handlefee		= $dd[0]['handlefee'];
	$shipfee		= $kg * $totalweight + $handlefee;
	if (!$discount){
		return $shipfee;
	}
	if($rate > 0) $shipfee = $shipfee * $rate;
	return $shipfee;
}



//计算EMS的费用
function calcems($totalweight,$countryname, $isdiscount=true){
	
	global $dbcon;
	
	$dd		= "SELECT * FROM  `ebay_emscalcfee` where countrys like '%$countryname%' ";
	$dd		= $dbcon->execute($dd);
	$dd		= $dbcon->getResultArray($dd);
	if (empty($dd)){
		return 11314;
	}
	$firstweight	= $dd[0]['firstweight'];
	$nextweight		= $dd[0]['nextweight'];
	$discount		= $dd[0]['discount'];
	$firstweight0	= $dd[0]['firstweight0'];
	$files			= $dd[0]['files'];
	$declared_value = $dd[0]['declared_value'];
								
	if($files == '1' && $totalweight <= 0.5){								
		$firstweight	= $firstweight0;
	}

	if($totalweight <= 0.5){						
		$shipfee	= $firstweight;
	}else{							
		$shipfee	= ceil((($totalweight*1000-500)/500))*$nextweight + $firstweight;
	}
	if (!$isdiscount){
		return $shipfee+$declared_value;
	}
	return $shipfee*$discount+$declared_value;

}

//计算EUB的费用
function calceub_backup($totalweight,$countryname, $isdiscount=true){
	
	global $dbcon,$user;
	/**
	 * 单件邮件不超过65克（含65g）：7.8元
		单件邮件66-250克：每克0.12元
		单件邮件251-300克：30元
		单件邮件301-2000克：每克0.1元
	 */
	$ss		= "select * from ebay_carrier where ebay_user ='$user' and name ='EUB' ";
	$ss		= $dbcon->execute($ss);
	$ss		= $dbcon->getResultArray($ss);
	
	$handlefee = floatval($ss[0]['handlefee']);
	$discount = empty($ss[0]['discount']) ? 1 : $ss[0]['discount'];
	if($totalweight <= 0.065){
		$shipfee	= $ss[0]['kg'];
	}else if (0.065<$totalweight&&$totalweight<=0.25){
		$shipfee	= $totalweight * $ss[0]['handlefee'];
	}else if (0.25<$totalweight&&$totalweight<=0.3){
		$shipfee = $ss[0]['kg2'];
	}else if (0.3<$totalweight&&$totalweight<=2){
		$shipfee	= $totalweight * $ss[0]['handlefee4'];
	}
	if (!$isdiscount){
		return $shipfee;
	}
	return round($shipfee * $discount, 2);
}

//计算EUB的费用-最新更新
function calceub($totalweight,$countryname, $isdiscount=true){
	
	global $dbcon,$user;
	/*大于60克，资费是0.08*重量(克)+7
	小于60克,包括60克，资费是0.08*60+7*/
	$ss		= "select * from ebay_carrier where ebay_user ='$user' and name ='EUB' ";
	$ss		= $dbcon->execute($ss);
	$ss		= $dbcon->getResultArray($ss);
	
	//$handlefee = floatval($ss[0]['handlefee']);
	$discount = empty($ss[0]['discount']) ? 1 : $ss[0]['discount'];
	if($totalweight <= 0.06){
		$shipfee	= 80*0.06+7;
	}else{
		$shipfee	= 80*$totalweight+7;
	}
	if (!$isdiscount){
		return $shipfee;
	}
	return round($shipfee * $discount, 2);
}

function calcdhlshippingfee($totalweight,$countryname){
	//计算DHL的运费,包含重量大于20kg时的算法和重量小于等于20kg时的算法
	//add by Herman.Xi @2013-01-14
	global $dbcon;
	if($totalweight <= 0) return false;
	$shipfee = 0;
	if($totalweight <= 20){
		$mode = 1;
	}else{
		$mode = 2;
	}
	$sql = "SELECT * FROM ebay_dhlcalcfee WHERE country like '%[$countryname]%' and mode = '{$mode}' ";
	$result = $dbcon->execute($sql);
	$dhlcalcfee = $dbcon->fetch_one($result);
	$dbcon->free_result($result);
	if(empty($dhlcalcfee)) return 0; //没有该国家DHL设置信息
	$weight_freight = $dhlcalcfee['weight_freight'];
	$weight_freight_arr = explode(',', $weight_freight);
	foreach($weight_freight_arr as $wf_value){
		$wf_value_arr = explode(':', $wf_value);
		$w_range = explode('-', $wf_value_arr[0]);
		if($mode == 1){
			if($totalweight > $w_range[0] && $totalweight <= $w_range[1]){
				$shipfee = $wf_value_arr[1];
				break;
			}
		}else if($mode == 2){
			if(empty($w_range[1])){
				if($totalweight > $w_range[0]){
					$shipfee = $totalweight * $wf_value_arr[1];
				}
			}else{
				if($totalweight > $w_range[0] && $totalweight <= $w_range[1]){
					$shipfee = $totalweight * $wf_value_arr[1];
				}
	
			}
		}
	}
	$shipfee = $shipfee * (1 + $dhlcalcfee['fuelcosts']);
	return round($shipfee, 2);
	
}

function calcfedex($totalweight,$countryname,$orderid){
	
	global $dbcon;
	
	include dirname(dirname(__FILE__)).'/cache/shipfeee/fedex_1.php';
	
	$cnum = '';
	$sql = "SELECT ebay_postcode FROM ebay_order WHERE ebay_id={$orderid}";
	$sql = $dbcon->execute($sql);
	$code = $dbcon->getResultArray($sql);
	$postcode = $code[0]['ebay_postcode'];

	foreach ($FEDEX_CONTRY_LIST_1 AS $c=>$country){
		$countrylist = explode(',', $country);
		if (in_array($countryname, $countrylist)){
			if ($countryname=='United States'){
				$postcode_lists = explode('#', $countrylist[1]);
				foreach ($postcode_lists AS $postcode_list){
					list($post_start, $post_end) = explode('-', $postcode_list);
					if ($post_start<$postcode&&$postcode<$post_end){
						$cnum = $c;
						break;
					}
				}
				if ($cnum===''){
					$cnum = $c+1;
				}
			}
			if ($cnum===''){
				$cnum = $c;
			}
			break;
		}
	}
	if ($cnum===''){
		return calcfedexyx($totalweight, $countryname, $postcode);
	}
	foreach ($FEDEX_WEIGHT_LIST_1 AS $w=>$weight){
		list($start_w, $end_w) = explode('-', $weight);
		if ($start_w<$totalweight&&$totalweight<=$end_w){
			$wnum = $w;
			break;
		}
	}
	$shipfee = $totalweight>20.5 ? $totalweight*$FEDEX_PRICE_LIST_1[$wnum][$cnum]*(1+$FEDEX_MYC_FEE_1) : $FEDEX_PRICE_LIST_1[$wnum][$cnum]*(1+$FEDEX_MYC_FEE_1);
	return round($shipfee, 2);
	
}

function calcglobalmail($totalweight,$countryname){
	
	global $dbcon;
	
	include dirname(dirname(__FILE__)).'/cache/shipfeee/globalmail.php';
	
	$cnum = '';
	foreach ($GLOBALMAIL_CONTRY_LIST AS $c=>$country){
		if ($countryname==trim($country)){
			$cnum = $c;
			break;
		}
	}
	foreach ($GLOBALMAIL_WEIGHT_LIST AS $w=>$weight){
		list($start_w, $end_w) = explode('-', $weight);
		if ($start_w<$totalweight&&$totalweight<=$end_w){
			$wnum = $w;
			break;
		}
	}
	list($price, $addprice) = explode('_', $GLOBALMAIL_PRICE_LIST[$wnum][$cnum]);

	return $price*$totalweight+$addprice;
	
}

function calcglobalmail_backup($totalweight,$countryname){
	
	global $dbcon;
	//add by heminghua @ 20130325
	if($totalweight<=0)
    {
     return false;
    }else
	{
     $ss="select * from ebay_globalmail where country like '%[$countryname]%'";
     $ss=$dbcon->execute($ss);
     $result=$dbcon->fetch_one($ss);
     $dbcon->free_result($result);
	 if(empty($result))
	 {
	   return 0;
	 }
	 else
	 {
	   /*运费计算*/
	   $weight_freight=$result['weight_freight'];
	   $weight_freight_arr=explode(',',$weight_freight);
	   foreach($weight_freight_arr as $key1 => $value1)
	   {
	     $value1_arr=explode(':',$value1);
		 $weight_range=explode('-',$value1_arr[0]);
		 if($totalweight>$weight_range[0] && $totalweight<=$weight_range[1])
		 {
		   $shipfee=$value1_arr[1];
		   break;
		 }
		 
	   }
	   
	   /*油费计算*/
	   $fuelcosts=$result['fuelcosts'];
	   $fuelcosts_arr=explode(',',$fuelcosts);
	   foreach($fuelcosts_arr as $key2 => $value2)
	   {
	     $value2_arr=explode(':',$value2);
		 $weight_range=explode('-',$value2_arr[0]);
		 if($totalweight>$weight_range[0] && $totalweight<=$weight_range[1])
		 {
		   $fuelfee = $value2_arr[1];
		   break;
		 }
	   }
	   
	   $shipfee += $fuelfee;
	 }

    }
	return $shipfee;
}

function calcfedexyx($totalweight,$countryname,$postcode=0){
	
	include dirname(dirname(__FILE__)).'/cache/shipfeee/fedex_2.php';
	
	$cnum = '';
	foreach ($FEDEX_CONTRY_LIST_2 AS $c=>$country){
		$countrylist = explode(',', $country);
		if (in_array($countryname, $countrylist)){
			if ($countryname=='United States'){
				$postcode_lists = explode('#', $countrylist[1]);
				foreach ($postcode_lists AS $postcode_list){
					list($post_start, $post_end) = explode('-', $postcode_list);
					if ($post_start<$postcode&&$postcode<$post_end){
						$cnum = $c;
						break;
					}
				}
				if ($cnum===''){
					$cnum = $c+1;
				}
			}
			if ($cnum===''){
				$cnum = $c;
			}
			break;
		}
	}
	if ($cnum===''){
		return 0;
	}
	foreach ($FEDEX_WEIGHT_LIST_2 AS $w=>$weight){
		list($start_w, $end_w) = explode('-', $weight);
		if ($start_w<$totalweight&&$totalweight<=$end_w){
			$wnum = $w;
			break;
		}
	}
	
	return $FEDEX_PRICE_LIST_2[$wnum][$cnum]*(1+$FEDEX_MYC_FEE_2);
}

//插入历史计算费用
function insert_history_calcfee($name, $shipfee,$id ,$orderid, $totalweight){
	
	global $dbcon;
	
	$sql = "insert into ebay_lishicalcfee(name,value,shippingid,orderid,totalweight) 
			values('$name','$shipfee','$id','$orderid','$totalweight')";
	$dbcon->execute($sql);
}

//确认订单可以走的邮寄方式 
function special_calcfee($orderid, $ebay_total){
	
	global $dbcon,$__liquid_items_postbyhkpost,$__liquid_items_postbyfedex,$__liquid_items_cptohkpost,$__elecsku_countrycn_array,$__liquid_items_elecsku, $global_countrycn_coutryen, $SYSTEM_ACCOUNTS,$__liquid_items_fenmocsku,$__liquid_items_BuiltinBattery,$__liquid_items_SuperSpecific,$__liquid_items_Paste;
	
	$shippment_hkpost_directly	=	false;
	$shippment_fedex_directly	=	false;
	$shippment_cptohkpost		=	false;
	$shippment_elec_directly	=	false;
	
	$shipping = array();
	$sql 		= "select ebay_ordersn,ebay_account,ebay_countryname,ebay_postcode from ebay_order where ebay_id ={$orderid}";
	$sql		= $dbcon->execute($sql);
	$orderinfo	= $dbcon->getResultArray($sql);
	
	$ebay_account = $orderinfo[0]['ebay_account'];
	$ebay_countryname = $orderinfo[0]['ebay_countryname'];
	
	$sql		= "select sku from ebay_orderdetail where ebay_ordersn ='{$orderinfo[0]['ebay_ordersn']}'";
	$sql		= $dbcon->execute($sql);
	$orderdetail	= $dbcon->getResultArray($sql);
	$skuinfo = array();
	foreach($orderdetail as $row){
		if(function_exists("get_realskuinfo")){
			foreach(get_realskuinfo($row['sku']) as $k => $n){
				$skuinfo[] = trim($k);
			}	
		}else{
			$skuinfo[] = trim($row['sku']);	
		}
	}
	//var_dump($skuinfo);
	if($ebay_total >= 40 ){
		$shipping = array('中国邮政挂号');
	}
	
	/*if(count($skuinfo)==1){
		if(in_array($skuinfo[0]['sku'],$__liquid_items_postbyhkpost)){
			if ($ebay_total<40){
				$shipping = array('中国邮政平邮');
			}else{
				$shipping = array('中国邮政挂号');
			}
		}
		if(in_array($skuinfo[0]['sku'],$__liquid_items_postbyfedex)) {
			$shipping = array('FedEx');
		}
	}else{
		foreach ($skuinfo AS $sku){
			if(in_array($skuinfo[0]['sku'],$__liquid_items_postbyhkpost)){
				if ($ebay_total<40){
					$shipping = array('中国邮政平邮');
				}else{
					$shipping = array('中国邮政挂号');
				}
			}
			if(in_array($sku['sku'],$__liquid_items_postbyfedex)) {
				$shipping = array('FedEx');
			}
		}
	}*/
	$array_intersect_elec = array_intersect($skuinfo, $__liquid_items_elecsku);
	$array_intersect_gaoji = array_intersect($skuinfo, $__liquid_items_postbyfedex);
	$array_intersect_zhijiayou = array_intersect($skuinfo, $__liquid_items_cptohkpost);
	$array_intersect_yieti = array_intersect($skuinfo, $__liquid_items_postbyhkpost);
	
	$array_intersect_fenmocsku = array_intersect($skuinfo, $__liquid_items_fenmocsku);
	$array_intersect_BuiltinBattery = array_intersect($skuinfo, $__liquid_items_BuiltinBattery);
	$array_intersect_SuperSpecific = array_intersect($skuinfo, $__liquid_items_SuperSpecific);
	$array_intersect_Paste = array_intersect($skuinfo, $__liquid_items_Paste);
			
	/*if(count($array_intersect_elec) > 0 && in_array($global_countrycn_coutryen[$ebay_countryname],$__elecsku_countrycn_array)){
		$shippment_elec_directly	=	true;
	}else*/if(count($array_intersect_gaoji) > 0){
		$shippment_fedex_directly	=	true;
	}else if(count($array_intersect_zhijiayou) > 0){
		$shippment_cptohkpost	=	true;
	}else if(count($array_intersect_yieti) > 0){
		$shippment_hkpost_directly	=	true;
	}
	############single line item order中如果有液体的产品直接设为香港小包###########2/2
	/*if($shippment_hkpost_directly ===true){
		if ($ebay_total<40){
			$shipping = array('中国邮政平邮');
		}else{
			$shipping = array('中国邮政挂号');
		}
	}*/
	if ($shippment_fedex_directly===true){
		$shipping = array('FedEx');
	}
	##############液体（不含指甲油）SKU(中国邮政转香港小包)#############START
	##############指甲油SKU(中国邮政转香港小包)#############START
	##############电子类产品SKU(指定国家的订单 走香港小包)#############START
	##############内置电池SKU(中国邮政转香港小包)#############START
	##############膏状SKU(中国邮政转香港小包)#############START
	//add by Herman.Xi 2013-03-14
	//if($shippment_cptohkpost || $shippment_hkpost_directly || count($array_intersect_BuiltinBattery) > 0 || count($array_intersect_Paste) > 0){20130905内置电池不走香港小包
	if($shippment_cptohkpost || $shippment_hkpost_directly || count($array_intersect_Paste) > 0){
		if(in_array($ebay_account,$SYSTEM_ACCOUNTS['ebay平台'])){
			if ($ebay_total<70){
				$shipping = array('香港小包平邮');
			}else{
				$shipping = array('香港小包挂号');
			}
		}else{
			//B2B
			$shipping = array('香港小包挂号');
		}
	}
	##############指甲油SKU(中国邮政转香港小包)#############END
	
	##############电子类产品SKU(指定国家的订单 走香港小包)#############START
	//add by Herman.Xi 2012-10-26
	/*if($shippment_elec_directly ===true){
		if(in_array($ebay_account,$SYSTEM_ACCOUNTS['ebay平台'])){
			if ($ebay_total<70){
				$shipping = array('香港小包平邮');
			}else{
				$shipping = array('香港小包挂号');
			}
		}else{
			//B2B
			$shipping = array('香港小包挂号');
		}
	}*/
	##############电子类产品SKU(指定国家的订单 走香港小包)#############END
	
	$ss		= "select * from ebay_carrier where ebay_account like '%$ebay_account,%'";
	$ss		= $dbcon->execute($ss);
	$ss		= $dbcon->getResultArray($ss);
	if (count($ss)> 0 && in_array($orderinfo[0]['ebay_countryname'], array('United States','US'))){
		$sql		= "select * from ebay_carrier where name='EUB' AND ebay_account like '%{$orderinfo[0]['ebay_account']}%'";
		$sql		= $dbcon->execute($sql);
		$eubcount	= $dbcon->num_rows($sql);
		if ($eubcount>0){
			$shipping = array('EUB');
			return $shipping;
		}
	}
	
	if (in_array(strtolower($orderinfo[0]['ebay_account']), array('ishop2099','cndirect998','cndirect55','easydeal365','tradekoo','futurestar99'))){
		$shipping = array('Global Mail');
	}
	
	return !empty($shipping) ? $shipping : array('香港小包平邮', '中国邮政平邮');
}

//费用最优计算
function calcshippingfee($totalweight,$ebay_countryname,$orderid,$ebay_account,$ebay_total){
	
	global $dbcon,$user;

	echo "\n-------------------订单号:$orderid ----------$ebay_countryname------------\n";

	//清空该订单已有历史费用
	$sql = "delete from ebay_lishicalcfee where orderid ='$orderid' ";
	$dbcon->execute($sql);

	//检测可以走的运输方式
	$special_shipping = special_calcfee($orderid, $ebay_total);
	$_special_shipping = array();
	foreach ($special_shipping AS $_shipping){
		$_special_shipping[] = "'{$_shipping}'";
	}
	$sql = "SELECT * FROM ebay_carrier WHERE ebay_user='{$user}' AND name IN (".implode(',', $_special_shipping).") AND country NOT LIKE '%$ebay_countryname%'";

	$sql = $dbcon->execute($sql);
	$carrier_lists = $dbcon->getResultArray($sql);
	foreach ($carrier_lists AS $i=>$carrier){

		$shipfee				= 0;
		$name					= $carrier['name'];
		$kg						= $carrier['kg'];
		$handlefee				= $carrier['handlefee'];
		$id						= $carrier['id'];
		$rate					= $carrier['rate'];
		$min					= $carrier['min']; // 是否满足挂号条件
		
		echo '<br><br>';

		/* 计算实际运费 */
		if($name  == '香港小包挂号' ){
			$shipfee = calchkghpost($totalweight,$ebay_countryname);
			echo "$name : $shipfee\n";				
			insert_history_calcfee($name, $shipfee, $id, $orderid, $totalweight);
		}else if($name  == '香港小包平邮'){
			$shipfee = calchkpost($totalweight,$ebay_countryname);
			echo $name.':'.$shipfee."\n";
			insert_history_calcfee($name, $shipfee,$id ,$orderid, $totalweight);
		}else if($name  == 'EUB' && ($ebay_countryname == 'United States' || $ebay_countryname == 'US')){								
			$shipfee = calceub($totalweight, $ebay_countryname);
			echo $name.':'.$shipfee."\n";
			insert_history_calcfee($name, $shipfee, $id, $orderid, $totalweight);
		}else if($name  == '中国邮政平邮'){
			$shipfee = calcchinapostpy($totalweight, $ebay_countryname);
			echo $name.':'.$shipfee."\n";
			insert_history_calcfee($name, $shipfee, $id, $orderid, $totalweight);
		}else if($name  == '中国邮政挂号'){
			$shipfee = calcchinapostgh($totalweight,$ebay_countryname);
			echo $name.':'.$shipfee."\n";
			insert_history_calcfee($name, $shipfee, $id, $orderid, $totalweight);
		}else if($name  == 'EMS'){
			$shipfee = calcems($totalweight,$ebay_countryname);
			echo $name.':'.$shipfee."\n";
			insert_history_calcfee($name, $shipfee, $id, $orderid, $totalweight);
		}else if($name == 'FedEx'){
			$shipfee = calcfedex($totalweight,$ebay_countryname, $orderid);
			echo $name.':'.$shipfee."\n";
			insert_history_calcfee($name, $shipfee, $id, $orderid, $totalweight);
		}else if ($name == 'Global Mail'){
			$shipfee = calcglobalmail($totalweight,$ebay_countryname);
			echo $name.':'.$shipfee."\n";
			insert_history_calcfee($name, $shipfee, $id, $orderid, $totalweight);
		}else if($name == 'DHL'){
			$shipfee = calcdhlshippingfee($totalweight,$countryname);
			echo $name.':'.$shipfee."\n";
			insert_history_calcfee($name, $shipfee, $id, $orderid, $totalweight);
		}
	}
	
	$sql = "select name,value,totalweight from ebay_lishicalcfee where orderid ={$orderid} and value != '0' order by value asc limit 1";
	$sql = $dbcon->execute($sql);
	$shippingdata = $dbcon->getResultArray($sql);
	return !empty($shippingdata) ? array_values($shippingdata[0]) : array($carrier_lists[0]['name'], 0, $totalweight);				
}

function calccombineshippingfee($totalweight, $ebay_countryname, $totalmoney, $shippinglist=array()){
	
	global $__liquid_items_postbyhkpost;
	
	if (empty($shippinglist)||in_array('香港小包挂号', $shippinglist)){
		$shipfee = calchkghpost($totalweight,$ebay_countryname);
		if ($shipfee>0){
			$shipping_name = '香港小包挂号';
		}
	}
	if (empty($shippinglist)||in_array('香港小包平邮', $shippinglist)){
		$hkpost_shipfee = calchkpost($totalweight,$ebay_countryname);
		if ($hkpost_shipfee<$shipfee||$shipfee==0){
			$shipfee = $hkpost_shipfee;
			$shipping_name = '香港小包平邮';
		}
	}
	if (empty($shippinglist)||in_array('中国邮政平邮', $shippinglist)){
		$chinapostpy_shipfee = calcchinapostpy($totalweight, $ebay_countryname);
		if ($chinapostpy_shipfee<$shipfee||$shipfee==0){
			$shipfee = $chinapostpy_shipfee;
			$shipping_name = '中国邮政平邮';
		}
	}
	if (empty($shippinglist)||in_array('中国邮政挂号', $shippinglist)){
		$chinapostgh_shipfee = calcchinapostgh($totalweight,$ebay_countryname);
		if ($chinapostgh_shipfee<$shipfee||$shipfee==0){
			$shipfee = $chinapostgh_shipfee;
			$shipping_name = '中国邮政挂号';
		}
	}
	if (in_array('Global Mail', $shippinglist)){
		$globalmail_shipfee = calcglobalmail($totalweight,$ebay_countryname);
		if ($globalmail_shipfee<$shipfee||$shipfee==0){
			$shipfee = $globalmail_shipfee;
			$shipping_name = 'Global Mail';
		}
	}
	if ($totalmoney>=40&&strpos($shipping_name, '中国邮政')!==false){
		$shipfee = calcchinapostgh($totalweight,$ebay_countryname);
		echo "**大于40中国邮政挂号={$shipfee}";
		$shipping_name = '中国邮政挂号';
	}
	if ($totalmoney>=70&&strpos($shipping_name, '香港小包')!==false){
		$shipfee = calchkghpost($totalweight,$ebay_countryname);
		echo "**大于70香港小包挂号={$shipfee}";
		$shipping_name = '香港小包挂号';
	}
	echo ",香港小包平邮={$hkpost_shipfee},中国邮政平邮={$chinapostpy_shipfee},中国邮政挂号={$chinapostgh_shipfee}**\n\n";
	return !empty($shipping_name) ? array($shipping_name, $shipfee, $totalweight) : array('中国邮政平邮', 0, $totalweight);
}

function calceveryweight($weightarray, $totalfee){
	$feearray = array();
	$totalweight = array_sum($weightarray);
	foreach ($weightarray AS $weight){
		$feearray[] = round(($totalfee*$weight/$totalweight), 2);
	}
	return $feearray;
}

function calctrueshippingfee($carrier, $totalweight, $countryname, $orderid){

	switch ($carrier){
		case '香港小包平邮' : $ordershipfee = calchkpost($totalweight,$countryname);
			break;
		case '香港小包挂号' : $ordershipfee = calchkghpost($totalweight,$countryname);
			break;
		case '中国邮政平邮' : $ordershipfee = calcchinapostpy($totalweight,$countryname,false);
			break;
		case '中国邮政挂号' : $ordershipfee = calcchinapostgh($totalweight,$countryname,false);
			break;
		case 'EUB' : $ordershipfee = calceub($totalweight,$countryname,false);
			break;
		case 'EMS' : $ordershipfee = calcems($totalweight,$countryname);
			break;
		case 'FedEx' : $ordershipfee = calcfedex($totalweight,$countryname,$orderid);
			break;
		case 'Global Mail' : $ordershipfee = calcglobalmail($totalweight,$countryname);
			break;
		case 'DHL' : $ordershipfee = calcdhlshippingfee($totalweight,$countryname);
			break;
		default : $ordershipfee = 0;
	}

	return $ordershipfee;
}

function calctrueshippingfee2($carrier, $totalweight, $countryname, $orderid){
	//根据运输方式，订单总重量，运去的国家，和订单ID，计算打折后的运费 (中国邮政平邮,中国邮政挂号,EUB,EMS)
	//add by Herman.Xi 2012-09-14
	switch ($carrier){
		case '中国邮政平邮' : $ordershipfee = calcchinapostpy($totalweight,$countryname);
			break;
		case '中国邮政挂号' : $ordershipfee = calcchinapostgh($totalweight,$countryname);
			break;
		case 'EUB' : $ordershipfee = calceub($totalweight,$countryname);
			break;
		case 'EMS' : $ordershipfee = calcems($totalweight,$countryname);
			break;
		case '香港小包平邮' : 
		case '香港小包挂号' : 
		case 'FedEx' : 
		case 'DHL' : 
		case 'Global Mail' : $ordershipfee = calctrueshippingfee($carrier, $totalweight, $countryname, $orderid);
			break;
		default : $ordershipfee = 0;
	}

	return round($ordershipfee, 3);
}

function recalcorderweight($ordersn, &$ebay_packingmaterial){
	/*
	***add by Herman.Xi
	***create date 20121012
	***计算订单总重量***
	一、单料号，数量为1：
		重量=料号重量+包材重量。
	二、单料号，数量为多个：
		当总数小于包材容量时，重量=料号重量*总数+包材重量；
		当总数大于包材容量时，重量=料号重量*总数+1个包材重量+（总数-包材容量）/包材容量*0.6*包材重量。
	三、单料号组合：
		当总数小于包材容量时，重量=料号重量*总数+包材重量；
		当总数大于包材容量时，重量=料号重量*总数+1个包材重量+（总数-包材容量）/包材容量*0.6*包材重量。
	四、多料号组合：
		重量=(料号1总数/包材1容量)*0.6*包材1重量 + (料号1重量 * 料号1总数)
			+(料号2总数/包材2容量)*0.6*包材2重量 + (料号2重量 * 料号2总数) 
			+ ....
	
	注：'/'是除，'*'是乘，'%'是求余。
	*/
	global $dbcon, $user;
		
	/* 计算包装材料和订单总重量 */
	$st	= "select * from ebay_orderdetail where ebay_ordersn='$ordersn'";
	$st = $dbcon->execute($st);
	$st	= $dbcon->getResultArray($st);
	
	$totalweight = 0;
	if(count($st)  == 1){
		/* 计算订单中单个物品包材的重量 */
		$sku						=  $st[0]['sku'];
		$ebay_amount				=  $st[0]['ebay_amount'];
		
		/* 开始检查是否是组合产品 */
		$rr			= "select * from ebay_productscombine where ebay_user='$user' and goods_sn='$sku'";
		$rr			= $dbcon->execute($rr);
		$rr 	 	= $dbcon->getResultArray($rr);
		if(count($rr) > 0){
			$goods_sncombine	= $rr[0]['goods_sncombine'];
			$goods_sncombine    = explode(',',$goods_sncombine);
			if(count($goods_sncombine) == 1){
				$pline			= explode('*',$goods_sncombine[0]);
				$goods_sn		= $pline[0];
				$goddscount     = $pline[1] * $ebay_amount;

				$ee			= "SELECT * FROM ebay_goods where goods_sn='$goods_sn' and ebay_user='$user'";
				$ee			= $dbcon->execute($ee);
				$ee 	 	= $dbcon->getResultArray($ee);
				$ebay_packingmaterial		=  $ee[0]['ebay_packingmaterial'];			
				$goods_weight				=  $ee[0]['goods_weight'];					// 产品重量子力学
				$capacity					=  $ee[0]['capacity'];						//产品容量
				
				$ss					= "select * from ebay_packingmaterial where  model='$ebay_packingmaterial' and ebay_user ='$user' ";
				$ss					= $dbcon->execute($ss);
				$ss					= $dbcon->getResultArray($ss);
				$pweight			= $ss[0]['weight'];
				
				if($goddscount <= $capacity){
					$totalweight			= $pweight + ($goods_weight * $goddscount);
				}else{
					// 计算多个包材的重量   $ebay_amount 单个sku购买的数量 ebay_packingmaterial 包材的重量
					$totalweight			= (1 + ($goddscount-$capacity)/$capacity*0.6)*$pweight + ($goods_weight * $goddscount);
					//$totalweight			= (($goddscount/$capacity) + ((($goddscount%$capacity)/$capacity)*0.6))*$pweight + ($goods_weight * $goddscount);
				}
			}else{
				for($e=0;$e<count($goods_sncombine);$e++){
					$pline			= explode('*',$goods_sncombine[$e]);
					$goods_sn		= $pline[0];
					$goddscount     = $pline[1] * $ebay_amount;
				
					$ee			= "SELECT * FROM ebay_goods where goods_sn='$goods_sn' and ebay_user='$user'";
					$ee			= $dbcon->execute($ee);
					$ee 	 	= $dbcon->getResultArray($ee);
					$ebay_packingmaterial		=  $ee[0]['ebay_packingmaterial'];			
					$goods_weight				=  $ee[0]['goods_weight'];					// 产品重量子力学
					$capacity					=  $ee[0]['capacity'];						//产品容量
					
					$ss					= "select * from ebay_packingmaterial where  model='$ebay_packingmaterial' and ebay_user ='$user' ";
					$ss					= $dbcon->execute($ss);
					$ss					= $dbcon->getResultArray($ss);
					$pweight			= isset($ss[0]['weight']) ? $ss[0]['weight'] : 0;
					
					$totalweight		+= ($goddscount/$capacity)*0.6*$pweight + ($goods_weight * $goddscount);
				}
			}
		}else{
			$ss							= "select * from ebay_goods where goods_sn='$sku' and ebay_user ='$user' ";
			$ss							= $dbcon->execute($ss);
			$ss							= $dbcon->getResultArray($ss);
			$ebay_packingmaterial		=  $ss[0]['ebay_packingmaterial'];			
			$goods_weight				=  $ss[0]['goods_weight'];					// 产品重量子力学
			$capacity					=  $ss[0]['capacity'];						//产品容量
			
			$ss					= "select * from ebay_packingmaterial where  model='$ebay_packingmaterial' and ebay_user ='$user' ";
			$ss					= $dbcon->execute($ss);
			$ss					= $dbcon->getResultArray($ss);
			$pweight			= isset($ss[0]['weight']) ? $ss[0]['weight'] : 0;

			if($ebay_amount <= $capacity){
				$totalweight			= $pweight + ($goods_weight * $ebay_amount);
			}else{
				// 计算多个包材的重量   $ebay_amount 单个sku购买的数量 ebay_packingmaterial 包材的重量
				$totalweight			= (1 + ($ebay_amount-$capacity)/$capacity*0.6)*$pweight + ($goods_weight * $ebay_amount);
				//$totalweight			= (($ebay_amount/$capacity) + ((($ebay_amount%$capacity)/$capacity)*0.6))*$pweight + ($goods_weight * $ebay_amount);
			}
		}
		
	}else{
		
		/* 计算订单中多个物品包材的重量 */
		for($f=0;$f<count($st); $f++){
			$sku						=  $st[$f]['sku'];
			$ebay_amount				=  $st[$f]['ebay_amount'];
			
			/* 开始检查是否是组合产品 */
			$rr			= "select * from ebay_productscombine where ebay_user='$user' and goods_sn='$sku'";
			$rr			= $dbcon->execute($rr);
			$rr 	 	= $dbcon->getResultArray($rr);
			
			if(count($rr) > 0){
				$goods_sncombine	= $rr[0]['goods_sncombine'];
				$goods_sncombine    = explode(',',$goods_sncombine);

				if(count($goods_sncombine) == 1){
					$pline			= explode('*',$goods_sncombine[0]);
					$goods_sn		= $pline[0];
					$goddscount     = $pline[1] * $ebay_amount;
				
					$ee			= "SELECT * FROM ebay_goods where goods_sn='$goods_sn' and ebay_user='$user'";
					$ee			= $dbcon->execute($ee);
					$ee 	 	= $dbcon->getResultArray($ee);
					$ebay_packingmaterial		=  $ee[0]['ebay_packingmaterial'];			
					$goods_weight				=  $ee[0]['goods_weight'];					// 产品重量子力学
					$capacity					=  $ee[0]['capacity'];						//产品容量
					
					$ss					= "select * from ebay_packingmaterial where  model='$ebay_packingmaterial' and ebay_user ='$user' ";
					$ss					= $dbcon->execute($ss);
					$ss					= $dbcon->getResultArray($ss);
					$pweight			= $ss[0]['weight'];
					
					if($goddscount <= $capacity){
						$totalweight			+= $pweight + ($goods_weight * $goddscount);
					}else{
						// 计算多个包材的重量   $ebay_amount 单个sku购买的数量 ebay_packingmaterial 包材的重量
						$totalweight			+= (1 + ($goddscount-$capacity)/$capacity*0.6)*$pweight + ($goods_weight * $goddscount);
						//$totalweight			+= (($goddscount/$capacity) + ((($goddscount%$capacity)/$capacity)*0.6))*$pweight + ($goods_weight * $goddscount);
					}
				}else{
					for($e=0;$e<count($goods_sncombine);$e++){
						$pline			= explode('*',$goods_sncombine[$e]);
						$goods_sn		= $pline[0];
						$goddscount     = $pline[1] * $ebay_amount;
					
						$ee			= "SELECT * FROM ebay_goods where goods_sn='$goods_sn' and ebay_user='$user'";
						$ee			= $dbcon->execute($ee);
						$ee 	 	= $dbcon->getResultArray($ee);
						$ebay_packingmaterial		=  $ee[0]['ebay_packingmaterial'];			
						$goods_weight				=  $ee[0]['goods_weight'];					// 产品重量子力学
						$capacity					=  $ee[0]['capacity'];						//产品容量
						
						$ss					= "select * from ebay_packingmaterial where  model='$ebay_packingmaterial' and ebay_user ='$user' ";
						$ss					= $dbcon->execute($ss);
						$ss					= $dbcon->getResultArray($ss);
						$pweight			= $ss[0]['weight'];
						
						$totalweight		+= ($goddscount/$capacity)*0.6*$pweight + ($goods_weight * $goddscount);
					}
				}
			}else{
				$ss							= "select * from ebay_goods where  goods_sn='$sku' and ebay_user ='$user' ";
				$ss							= $dbcon->execute($ss);
				$ss							= $dbcon->getResultArray($ss);
				$ebay_packingmaterial		=  $ss[0]['ebay_packingmaterial'];			
				$goods_weight				=  $ss[0]['goods_weight'];					// 产品重量子力学
				$capacity					=  $ss[0]['capacity'];						//产品容量

				$ss					= "select * from ebay_packingmaterial where  model='$ebay_packingmaterial' and ebay_user ='$user' ";
				$ss					= $dbcon->execute($ss);
				$ss					= $dbcon->getResultArray($ss);
				$pweight			= isset($ss[0]['weight']) ? $ss[0]['weight'] : 0;

				if($ebay_amount <= $capacity){
					$totalweight			+= $pweight + $goods_weight*$ebay_amount;
				}else{
					// 计算多个包材的重量   $ebay_amount 单个sku购买的数量 ebay_packingmaterial 包材的重量
					$totalweight			+= (1 + ($ebay_amount-$capacity)/$capacity*0.6)*$pweight + ($goods_weight * $ebay_amount);
					//$totalweight			+= (($ebay_amount/$capacity) + ((($ebay_amount%$capacity)/$capacity)*0.6))*$pweight + ($goods_weight * $ebay_amount);
				}
				//echo "sku = $sku-------goods_weight = $goods_weight-------ebay_amount = $ebay_amount--------ebay_packingmaterial = $ebay_packingmaterial----------capacity = $capacity-------pweight = $pweight---------totalweight = $totalweight"; echo "<br>";
			}
		}
	}
	return $totalweight;
}

function calc_packingweight($sku,$user){
	global $dbcon;
	$ee	= "SELECT ebay_packingmaterial,goods_weight,capacity 
			FROM ebay_goods 
			where goods_sn='$sku' 
			and ebay_user='$user' limit 1";
	$ee	= $dbcon->execute($ee);
	$ee = $dbcon->getResultArray($ee);
	return $ee[0];
}

function calc_itemandpacking_weight($sku,$ebay_amount,$user,&$totalweight){
	global $dbcon,$global_packingmaterial_weight;
	/* 开始检查是否是组合产品 */
	$rr	= " select 	goods_sncombine 
			from 	ebay_productscombine 
			where 	ebay_user='$user' 
			and 	goods_sn='$sku'";
	$rr	= $dbcon->execute($rr);
	$rr = $dbcon->getResultArray($rr);
	if(count($rr) > 0){
		$goods_sncombine	= $rr[0]['goods_sncombine'];
		$goods_sncombine	= explode(',',$goods_sncombine);
		if(count($goods_sncombine) == 1){
			$pline		= explode('*',$goods_sncombine[0]);
			$goods_sn	= $pline[0];
			$goddscount = $pline[1] * $ebay_amount;
			unset($pline);
			
			$packingweight_data=calc_packingweight($goods_sn,$user);
			$ebay_packingmaterial	= $packingweight_data['ebay_packingmaterial'];			
			$goods_weight	= $packingweight_data['goods_weight'];	// 产品重量
			$capacity	= $packingweight_data['capacity'];	//产品容量
			unset($packingweight_data);
			
			$pweight = @$global_packingmaterial_weight[$ebay_packingmaterial];
			/*if($goddscount <= $capacity){
				$totalweight  += $pweight*$goddscount + ($goods_weight * $goddscount);
			}else{
				// 计算多个包材的重量   $ebay_amount 单个sku购买的数量 ebay_packingmaterial 包材的重量
				$totalweight2 += $goods_weight*$ebay_amount + $pweight;
			}*/
			if($goddscount <= $capacity){
				$totalweight			+= $pweight + ($goods_weight * $goddscount);
			}else{
				// 计算多个包材的重量   $ebay_amount 单个sku购买的数量 ebay_packingmaterial 包材的重量
				$totalweight			+= (1 + ($goddscount-$capacity)/$capacity*0.6)*$pweight + ($goods_weight * $goddscount);
			}
		}else{
			for($e=0;$e<count($goods_sncombine);$e++){
				$pline		= explode('*',$goods_sncombine[$e]);
				$goods_sn	= $pline[0];
				$goddscount = $pline[1] * $ebay_amount;
				unset($pline);
				
				$packingweight_data=calc_packingweight($goods_sn,$user);
				$ebay_packingmaterial	= $packingweight_data['ebay_packingmaterial'];			
				$goods_weight	= $packingweight_data['goods_weight'];	// 产品重量
				$capacity	= $packingweight_data['capacity'];	//产品容量
				unset($packingweight_data);
				
				$pweight = @$global_packingmaterial_weight[$ebay_packingmaterial];
				/*if($goddscount <= $capacity){
					$totalweight  += $pweight*$goddscount + ($goods_weight * $goddscount);
				}else{
					// 计算多个包材的重量   $ebay_amount 单个sku购买的数量 ebay_packingmaterial 包材的重量
					$totalweight2 += $goods_weight*$ebay_amount + $pweight;
				}*/
				$totalweight		+= ($goddscount/$capacity)*0.6*$pweight + ($goods_weight * $goddscount);
			}
		}
		//if($totalweight2>0) $totalweight2	+= 0.6*$pweight ;
	}else{
		$packingweight_data=calc_packingweight($sku,$user);
		$ebay_packingmaterial	= $packingweight_data['ebay_packingmaterial'];			
		$goods_weight	= $packingweight_data['goods_weight'];	// 产品重量
		$capacity	= $packingweight_data['capacity'];	//产品容量
		unset($packingweight_data);
		
		$pweight= isset($global_packingmaterial_weight[$ebay_packingmaterial]) ? $global_packingmaterial_weight[$ebay_packingmaterial] : 0;
		
		/*if($ebay_amount <= $capacity){				
			$totalweight += $pweight + $goods_weight*$ebay_amount;				
		}else{
			// 计算多个包材的重量   $ebay_amount 单个sku购买的数量 ebay_packingmaterial 包材的重量
			$totalweight2	+= $goods_weight*$ebay_amount + $pweight;				
		}*/
		if($ebay_amount <= $capacity){
			$totalweight			+= $pweight + $goods_weight*$ebay_amount;
		}else{
			// 计算多个包材的重量   $ebay_amount 单个sku购买的数量 ebay_packingmaterial 包材的重量
			$totalweight			+= (1 + ($ebay_amount-$capacity)/$capacity*0.6)*$pweight + ($goods_weight * $ebay_amount);
		}
		//echo "sku = $sku-------goods_weight = $goods_weight-------ebay_amount = $ebay_amount--------ebay_packingmaterial = $ebay_packingmaterial----------capacity = $capacity-------pweight = $pweight---------totalweight = $totalweight"; echo "<br>";
		//if($totalweight2>0) $totalweight2	+= 0.6 * $pweight ;
	}
	return	$ebay_packingmaterial;
}
function checkprintcard($ebay_id){
	
	global $dbcon,$SYSTEM_ACCOUNTS;
	
	$ebay_id = intval($ebay_id);
	$sql = "SELECT ebay_orderid,ebay_userid,ebay_carrier,ebay_account,recordnumber FROM ebay_order WHERE ebay_id={$ebay_id} AND ebay_combine!=1 LIMIT 1";
	$sql = $dbcon->execute($sql);
	$mainorder = $dbcon->fetch_one($sql);

	if (empty($mainorder)){
		return '';
	}
	if (!in_array($mainorder['ebay_carrier'], array('中国邮政挂号','中国邮政平邮','香港小包挂号','香港小包平邮','EUB'))){
		return '';
	}
	if (in_array($mainorder['ebay_account'], $SYSTEM_ACCOUNTS['ebay平台'])&&empty($mainorder['ebay_orderid'])){
		return '';
	}
	if (!in_array($mainorder['ebay_account'], $SYSTEM_ACCOUNTS['ebay平台'])&&preg_match("/[a-z]+/i", $mainorder['recordnumber'])){
		return '';
	}
	if ($mainorder['ebay_account']=='dresslink.com'){
		return '';
	}
	$sql = "SELECT ebay_street FROM ebay_order WHERE ebay_userid='{$mainorder['ebay_userid']}' AND ebay_combine!=1";
	$sql = $dbcon->execute($sql);
	$orders = $dbcon->getResultArray($sql);

	$streets = array_unique(array_filter(multi2single('ebay_street', $orders)));
	
	if (count($streets)==1){
		return 'KP';
	}else {
		return '';
	}
}
?>