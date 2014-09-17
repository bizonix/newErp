<?php
	/*
	 *新系统最优运费计算方法
	 *add by herman.xi @20140411
	 */
	function trans_carriers_best_get($totalweight,$ebay_countryname,$ebay_account,$ebay_total,$zipCode="",$noShipId=""){
		$username = 'finejo';
		require_once("opensys_functions.php");//开放系统文件
		$paramArr   = array(
			'method' 		=> 'trans.carriers.best.get',  //API名称
			'format' 		=> 'json',  //返回格式
			'v' 			=> '2.0',   //API版本号
			'username'	 	=> $username,
		);
		$paramArr['country'] = $ebay_countryname;
		$paramArr['weight'] = $totalweight;
		$paramArr['shipAddId'] = 1;
		if(isset($zipCode)){
			$paramArr['postCode'] = $zipCode;
		}
		if(isset($noShipId)){
			$paramArr['noShipId'] = $noShipId;
		}
		$rtn = callOpenSystem($paramArr);
		$rtn = json_decode($rtn, true);
		if(!isset($rtn['data'])){
			return false;
		}
		return $rtn['data'];
	}
	
	/*
	 *新系统固定运费计算方法
	 *add by herman.xi @20140411
	 */
	function trans_carriers_fix_get($ebay_carrier, $totalweight, $ebay_countryname){
		//$b	=	microtime()."\r\n";
		
		$username = 'finejo';
		require_once("opensys_functions.php");//开放系统文件
		$paramArr   = array(
			'method' 		=> 'trans.carriers.fix.get',  //API名称
			'format' 		=> 'json',  //返回格式
			'v' 			=> '2.0',   //API版本号
			'username'	 	=> $username,
		);
		if($ebay_carrier == "新加坡小包挂号"){
			$ebay_carrier = "新加坡邮政";
		}
		//$CarrierLists = getCarrierListById();
		$CarrierLists = array(1=> "中国邮政平邮", 2=> "中国邮政挂号", 3=> "香港小包平邮", 4=> "香港小包挂号", 5=> "EMS", 6=> "EUB", 8=> "DHL", 9=> "FedEx", 10=> "Global Mail", 46=> "UPS Ground", 47=> "USPS", 48=> "顺丰快递", 49=> "圆通快递", 50=> "申通快递", 51=> "韵达快递" , 52=> "新加坡邮政" , 53=> "德国邮政挂号" , 54=> "中通快递" , 55=> "汇通快递" , 56=> "国通快递" , 57=> "加运美快递" , 58=> "UPS" , 59=> "飞腾DHL" , 60=> "上门提货" , 61=> "运德物流" , 62=> "UPS美国专线" , 63=> "英国专线挂号" , 64=> "天天快递" , 65=> "SurePost" , 66=> "同城速递" , 67=> "国内快递" , 68=> "自提" , 69=> "送货上门" , 70=> "TNT" , 71=> "城市之星物流" , 72=> "优速快递" , 73=> "速尔快递" , 74=> "天地华宇物流" , 75=> "德邦物流" , 76=> "盛辉物流" , 77=> "vietnam" , 78=> "快捷快递" , 79=> "俄速通挂号" , 80=> "俄速通平邮" , 81=> "俄速通大包" , 82=> "海运运输", 83=>"新加坡DHL GM挂号",84=>"新加坡DHL GM平邮",85=>"郑州小包平邮",86=>"郑州小包挂号",87=>"瑞士小包平邮",88=>"瑞士小包挂号",89=>"比利时小包挂号",90=>"比利时小包平邮",91=>"USPS FirstClass",92=>"UPS Ground Commercia",93=>"澳邮宝挂号",94=>"澳邮宝平邮",95=>"UPS SurePost");
		$flip_CarrierLists = array_flip($CarrierLists);
		if(!isset($flip_CarrierLists[$ebay_carrier])){
			return false;
		}else{
			$carrierId = $flip_CarrierLists[$ebay_carrier];
		}
		$paramArr['carrierId'] = $carrierId;
		$paramArr['country'] = $ebay_countryname;
		$paramArr['weight']  = $totalweight;
		$rtn = callOpenSystem($paramArr);
		$rtn = json_decode($rtn, true);
		
		//$e	=	microtime()."\r\n";
		
		//file_put_contents("/usr/local/php/var/log/xxx.txt","B=".$b,FILE_APPEND);
		//file_put_contents("/usr/local/php/var/log/xxx.txt","T=".$e,FILE_APPEND);
		
		if(!isset($rtn['data'])){
			return false;	
		}
		return $rtn['data'];
	}
	//俄速通小包挂号运费计算 add by guanyongjun 2014-03-25
	function  calrustonpacketgh($totalweight,$countryname){
		global $dbcon;
		$shipfee	= 0;
		$totalfee	= 0;
		$sql 		= "SELECT * FROM trans_freight_ruston_packet_gh WHERE countrys like '%$countryname%' AND is_delete = 0 LIMIT 1";
		$query		= $dbcon->execute($sql);
		$arearow	= $dbcon->fetch_one($query);        
		if (empty($arearow)) return $shipfee;
		//计算运费
		$rate			= $arearow['discount'] ? $arearow['discount'] : 1;
		$price			= $arearow['price'];
		$maxWeight		= $arearow['maxWeight'];
		$handlefee		= $arearow['handlefee'];
		if ($totalweight>=$maxWeight) return $shipfee;
		$shipfee		= $price * $weight + $handlefee;
		$totalfee		= $shipfee;
		if ($rate > 0) $shipfee = $shipfee * $rate;
		$shipfee = round($shipfee, 4);
		return $shipfee;
	}

	//俄速通大包运费计算 add by guanyongjun 2014-03-25
	function  calrustonlargepackage($totalweight,$countryname){
		$shipfee	= 0;
		$totalfee	= 0;
		$sql 		= "SELECT * FROM trans_freight_ruston_large_package WHERE countrys like '%$countryname%' AND is_delete = 0 LIMIT 1";
		$query		= $dbcon->execute($sql);
		$arearow	= $dbcon->fetch_one($query);        
		if (empty($arearow)) return $shipfee;
		//计算运费
		$rate			= $arearow['discount'] ? $arearow['discount'] : 1;
		$price			= $arearow['price'];
		$nextPrice		= $arearow['nextPrice'];
		$minWeight		= $arearow['minWeight'];
		$handlefee		= $arearow['handlefee'];
		if ($totalweight < $minWeight) return $shipfee;
		$shipfee		= $price + (ceil($totalweight-1)*$nextPrice) + $handlefee;
		$totalfee		= $shipfee;
		if ($rate > 0) $shipfee = $shipfee * $rate;
		$shipfee = round($shipfee, 4);
		return $shipfee;
	}
	
	//计算UPS美国专线实际运费 add by guanyongjun 2014-01-14
	//type = 1(加急包裹经济型）2（特快包裹优先型） 
	function calcupsus($totalweight,$countryname,$type=1) {
		global $dbcon;
		$shipfee		= 0;
		$totalweight	= floatval($totalweight);
		$realWeight		= 0;
		if($totalweight > 20) {
			$realWeight	= $totalweight;
			$totalweight= ceil(floatval($totalweight));
		}
		if(!in_array($countryname,array('United States','US','USA'))) return $shipfee;
		if($totalweight < 21) {
			$sql		= "SELECT * FROM trans_freight_ups_us WHERE is_delete = 0 AND type = {$type} AND {$totalweight} > min_weight AND {$totalweight} <= max_weight LIMIT 1";
		} else {
			$sql		= "SELECT * FROM trans_freight_ups_us WHERE is_delete = 0 AND type = {$type} AND {$totalweight} >= min_weight AND {$totalweight} <= max_weight LIMIT 1";
		}
		$query			= $dbcon->execute($sql);
		$res			= $dbcon->fetch_one($query);
		if(count($res) > 0) {
			$shipfee	= floatval($res['price']);
			if($totalweight > 20) $shipfee	= $shipfee * $realWeight;
			$rate		= floatval($res['fuelcosts']);
			$vat		= floatval($res['vat']);
			if($rate > 0) $shipfee	= $shipfee*(1+$rate);
			if($vat > 0) $shipfee	= $shipfee*(1+$vat);
		}
		return $shipfee;
	}
	
	/* 计算香港小包平邮的实际运费 */
	function calchkpost($totalweight,$countryname){
		
		global $dbcon;
		$ss		= "select * from ebay_hkpostcalcfee where countrys like '%$countryname%'";
		$ss		= $dbcon->execute($ss);
		$ss		= $dbcon->getResultArray($ss);
		
		$rate			= $ss[0]['discount']?$ss[0]['discount']:1;
		$kg				= $ss[0]['firstweight'];
		$handlefee		= $ss[0]['handlefee'];
		
		
		$shipfee		= $kg * $totalweight + $handlefee;
		if($rate > 0) $shipfee		= $shipfee * $rate;
		return $shipfee;
						
	
	
	}
	
	/* 计算香港小包挂号的费用 */
	function calchkghpost($totalweight,$countryname){
	
		global $dbcon;
		$ss		= "select * from ebay_hkpostghcalcfee where countrys like '%$countryname%'";
		$ss		= $dbcon->execute($ss);
		$ss		= $dbcon->getResultArray($ss);
		
		$rate			= $ss[0]['discount']?$ss[0]['discount']:1;
		$kg				= $ss[0]['firstweight'];
		$handlefee		= $ss[0]['handlefee'];
		$shipfee		= $kg * $totalweight + $handlefee;
		if($rate > 0) $shipfee		= $shipfee * $rate;
		return $shipfee;
	
	}	
	
	function calcems($totalweight,$ebay_countryname){
		//EMS运费计算方式
		global $dbcon;
		$dd		= "SELECT * FROM  `ebay_emscalcfee` where countrys like '%$ebay_countryname%' ";
		$dd		= $dbcon->execute($dd);
		$dd		= $dbcon->getResultArray($dd);
		$firstweight	= $dd[0]['firstweight'];
		$nextweight		= $dd[0]['nextweight'];
		$handlefee		= $dd[0]['handlefee'];
		$discount		= $dd[0]['discount'];
		$firstweight0	= $dd[0]['firstweight0'];
		$files			= $dd[0]['files'];
									
		if($files == '1' && $totalweight <= 0.5){
										
		$firstweight	= $firstweight0;
		}
									
		if($totalweight <= 0.5){
							
		$shipfee	= $firstweight;
						
		}else{
								
		$shipfee	= ceil((($totalweight*1000)/500))*$nextweight + $firstweight + $handlefee;
		}
		
		$shipfee	= $shipfee *$discount;
		return $shipfee;							
	
	}
	
	
	//计算EUB的费用
	function calceub_old($totalweight,$countryname, $isdiscount=true){
		
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
	
	//计算EUB的费用-最新更新 add by guanyongjun 2014-2-11
	function calceub($totalweight,$countryname, $isdiscount=true){
		//500克以内87折，500克以上9折  
		if($totalweight <= 0.5){
			if($totalweight<=0.06) {
				$shipfee	= 80*0.06+7;
			} else {
				$shipfee	= 80*$totalweight+7;
			}
			if ($isdiscount) $shipfee = $shipfee*0.87;
		}else{
			$shipfee	= 80*$totalweight+7;
			if ($isdiscount) $shipfee = $shipfee*0.9;
		}
		return round($shipfee, 2);
	}
	
	
	/*function calcchinapostgh($totalweight,$ebay_countryname){
	
			global $dbcon;
			
			$dd		= "SELECT * FROM  `ebay_cpghcalcfee` where countrys like '%$ebay_countryname%' ";
			$dd		= $dbcon->execute($dd);
			$dd		= $dbcon->getResultArray($dd);
			if(count($dd)>=1){
				$firstweight	= $dd[0]['firstweight'];
				$nextweight		= $dd[0]['nextweight'];
				$handlefee		= $dd[0]['handlefee'];
				$discount		= $dd[0]['discount']?$dd[0]['discount']:1;
				$xx0			= $dd[0]['xx0'];
				$xx1			= $dd[0]['xx1'];
			    if($totalweight <= ($xx0/1000)){
				$shipfee	= $firstweight + $handlefee;
				}else{
				$shipfee	= ceil(((($totalweight*1000) -$xx0)/$xx1))*$nextweight + $firstweight + $handlefee;
				}
			}
			
			
			return $shipfee;
			
	}*/
	
	//中国邮政挂号
	function calcchinapostgh($totalweight,$countryname, $discount=true){
	
		global $dbcon;
		
		$dd		= "SELECT * FROM  `ebay_cpghcalcfee` where countrys like '%$countryname%' ORDER BY firstweight DESC ";
		$dd		= $dbcon->execute($dd);
		$dd		= $dbcon->getResultArray($dd);
		if (empty($dd)){
			return 11314;
		}
		$rate			= $dd[0]['discount']?$dd[0]['discount']:1;
		$kg				= $dd[0]['firstweight'];
		$handlefee		= $dd[0]['handlefee'];
		$shipfee		= $kg * $totalweight + $handlefee;
		if(in_array($countryname,array("Russian Federation","Russia"))){
		   $shipfee = 96.3*$totalweight+8;
		}
		if (!$discount){
			return $shipfee;
		}
		if($rate > 0) $shipfee = $shipfee * $rate;
		return $shipfee;
	
		/*if(in_array($countryname,array("Russian Federation","Russia"))){
		   $shipfee = 96.3*$totalweight+8;
		   return $shipfee;
		}
		$dd		= "SELECT * FROM  `ebay_cpghcalcfee` where countrys like '%$countryname%' ORDER BY firstweight DESC ";
		$dd		= $dbcon->execute($dd);
		$dd		= $dbcon->getResultArray($dd);
		if(count($dd)>=1){
			$rate			= $dd[0]['discount']?$dd[0]['discount']:1;
			$kg				= $dd[0]['firstweight'];
			$handlefee		= $dd[0]['handlefee'];
			$shipfee		= $kg * $totalweight + $handlefee;
			if($rate > 0) $shipfee = $shipfee * $rate;
			return $shipfee;
		}else{
			return false;
		}*/
	}
	
	function calchkpypost($totalweight,$ebay_countryname){
	
			global $dbcon;
			
			$dd		= "SELECT * FROM  `ebay_cppycalcfee` where countrys like '%$ebay_countryname%' ";
			$dd		= $dbcon->execute($dd);
			$dd		= $dbcon->getResultArray($dd);
			if(count($dd)>=1){
				$firstweight	= $dd[0]['firstweight'];
				$nextweight		= $dd[0]['nextweight'];
				$handlefee		= $dd[0]['handlefee'];
				$discount		= $dd[0]['discount']?$dd[0]['discount']:1;
				$xx0			= $dd[0]['xx0'];
				$xx1			= $dd[0]['xx1'];
			    if($totalweight <= ($xx0/1000)){
				$shipfee	= $firstweight + $handlefee;
				}else{
				$shipfee	= ceil(((($totalweight*1000) -$xx0)/$xx1))*$nextweight + $firstweight + $handlefee;
				}
			}
			
			
			return $shipfee;
			
	}
	
	//中国邮政平邮
	function calcchinapostpy($totalweight, $countryname, $discount=true){
		global $dbcon;
		if(in_array($countryname,array("Russian Federation","Russia"))){
		   $shipfee = 97.5*$totalweight;
		   return $shipfee; 
		}
		$dd		= "SELECT * FROM  `ebay_cppycalcfee` where countrys like '%$countryname%' ORDER BY firstweight DESC ";
		$dd		= $dbcon->execute($dd);
		$dd		= $dbcon->getResultArray($dd);
		if(count($dd)>=1){
			$rate			= $dd[0]['discount']?$dd[0]['discount']:1;
			$kg				= $dd[0]['firstweight'];
			
			$shipfee		= $kg * $totalweight;
			if (!$discount){
				return $shipfee;
			}
			if($rate > 0) $shipfee = $shipfee * $rate;
			return $shipfee;
		}else{
			return false;	
		}
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
			/*case 'FedEx' : $ordershipfee = calcfedex($totalweight,$countryname,$orderid);
				break;*/
			case 'Global Mail' : $ordershipfee = calcglobalmail($totalweight,$countryname);
				break;
			case 'DHL' : $ordershipfee = calcdhlshippingfee($totalweight,$countryname);
				break;
			case 'UPS美国专线' : $ordershipfee = calcupsus($totalweight,$countryname);
				break;
			case 'USPS FirstClass':
			case 'UPS SurePost':
			case 'UPS Ground Commercia': $ordershipfee = trans_carriers_fix_get($carrier, $totalweight, $countryname, $zipCode, $transitId);
				break;
			case 'FedEx' : 
			case '俄速通挂号'	:
			case '俄速通平邮':
			case '新加坡DHL GM平邮' : 
			case '新加坡DHL GM挂号' : 
			case '俄速通大包' :
			case '瑞士小包平邮' :
			case '瑞士小包挂号' : $ordershipfee = trans_carriers_fix_get($carrier, $totalweight, $countryname);
				break;
			default : $ordershipfee = 0;
		}
		if(in_array($carrier,array('瑞士小包平邮','瑞士小包挂号','USPS FirstClass','UPS SurePost','UPS Ground Commercia','俄速通大包','新加坡DHL GM平邮','新加坡DHL GM挂号','FedEx','俄速通挂号','俄速通平邮'))){
			$ordershipfee = $ordershipfee['totalFee'];
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
		case 'Global Mail' : 
		case 'DHL' : $ordershipfee = calctrueshippingfee($carrier, $totalweight, $countryname, $orderid);
			break;
		case 'UPS美国专线' : $ordershipfee = calcupsus($totalweight,$countryname);
			break;
		case 'USPS FirstClass':
		case 'UPS SurePost':
		case 'UPS Ground Commercia': $ordershipfee = trans_carriers_fix_get($carrier, $totalweight, $countryname, $zipCode, $transitId);
			break;
		case 'FedEx' : 
		case '俄速通挂号'	: 
		case '俄速通平邮': 
		case '新加坡DHL GM平邮' : 
		case '新加坡DHL GM挂号' : 
		case '俄速通大包' :
		case '瑞士小包平邮' :
		case '瑞士小包挂号' : $ordershipfee = trans_carriers_fix_get($carrier, $totalweight, $countryname);
			break;
		default : $ordershipfee = 0;
	}
	if(in_array($carrier,array('瑞士小包平邮','瑞士小包挂号','USPS FirstClass','UPS SurePost','UPS Ground Commercia','俄速通大包','新加坡DHL GM平邮','新加坡DHL GM挂号','FedEx','俄速通挂号','俄速通平邮'))){
		$ordershipfee = $ordershipfee['fee'];
	}

	return round($ordershipfee, 3);
}
?>