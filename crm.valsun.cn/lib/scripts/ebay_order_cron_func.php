<?php
	//传入参数为中国重庆时区时间戳
	function get_ebay_timestamp($time){
		//默认八小时时差
		return	$time-(3600*8);
	}
	//传入参数为UTC时区时间串
	function get_china_timestamp($timestr){
		return strtotime($timestr)+3600*8;
	}
	function str_rep($str){
		$str  = str_replace("'","&acute;",$str);
		$str  = str_replace("\"","&quot;",$str);
		return $str;
	}
	function addlogs($log_name,$log_operatetime,$log_orderid,$log_notes,$tname,$log_ebayaccount,$start,$end,$type){
		global $dbcon;
		$nowtime=date("Y-m-d H:i:s");
		$ss		= "insert into	system_log(log_name,log_operationtime,log_orderid,log_notes,ebay_user,
										   currentime,log_ebay_account,starttime,endtime,type) 
				   values('$log_name','$log_operatetime','$log_orderid','$log_notes','$tname',
						  '$nowtime','$log_ebayaccount','$start','$end','$type')";
		$dbcon->execute($ss);
	}
	function calcshippingfee($totalweight,$ebay_countryname,$ebayid,$ebay_account,$ebay_total){
		global $dbcon,$user,$__liquid_items_postbyhkpost,$__liquid_items_postbyfedex,$__liquid_items_cptohkpost, $__liquid_items_elecsku, $global_countrycn_coutryen,$__elecsku_countrycn_array,$GLOBAL_EBAY_ACCOUNT,$__liquid_items_fenmocsku,$__liquid_items_BuiltinBattery,$__liquid_items_SuperSpecific,$__liquid_items_Paste,$SYSTEM_ACCOUNTS;

		$g_account = str_replace(',', '', $ebay_account);
		$ss		= "delete from ebay_lishicalcfee where orderid ='$ebayid' ";
		$dbcon->execute($ss);
		
		$shippment_hkpost_directly	=	false;
		$shippment_fedex_directly	=	false;
		$shippment_cptohkpost	=	false;
		//$shippment_elec_directly = false;
		############single line item order中如果有液体的产品直接设为香港小包###########1/2
		####added by john 2012-05-16
		$ss     = "select ebay_ordersn,ebay_orderid,ebay_couny,ebay_currency from ebay_order where ebay_id =$ebayid ";
		$ss		= $dbcon->execute($ss);
		$ss		= $dbcon->getResultArray($ss);
		
		$ebay_ordersn = $ss[0]['ebay_ordersn'];
		$ebay_orderid = $ss[0]['ebay_orderid'];
		$ebay_couny = $ss[0]['ebay_couny'];
		$ebay_currency = $ss[0]['ebay_currency'];
		
		$ss		= "select sku,ebay_itemprice from ebay_orderdetail where ebay_ordersn ='$ebay_ordersn'";
		$ss		= $dbcon->execute($ss);
		$ss		= $dbcon->getResultArray($ss);
		$sku_arr = array();
		$eub_to_py = false;//包含单价小于等于五的料号
		foreach ($ss AS $_ss){
			if(function_exists("get_realskuinfo")){
				$skus = get_realskuinfo($_ss['sku']);
				foreach($skus as $k => $n){//支持组合料号
					$sku_arr[] = trim($k);
				}
			}else{
				$sku_arr[] = trim($_ss['sku']);
			}
			/* add by Herman.Xi @2013-07-16 */
			if($_ss['ebay_itemprice'] <= 5){
				$eub_to_py = true;
			}
		}
		$array_intersect_elec = array_intersect($sku_arr, $__liquid_items_elecsku);
		$array_intersect_gaoji = array_intersect($sku_arr, $__liquid_items_postbyfedex);
		$array_intersect_zhijiayou = array_intersect($sku_arr, $__liquid_items_cptohkpost);
		$array_intersect_yieti = array_intersect($sku_arr, $__liquid_items_postbyhkpost);
		
		$array_intersect_fenmocsku = array_intersect($sku_arr, $__liquid_items_fenmocsku);
		$array_intersect_BuiltinBattery = array_intersect($sku_arr, $__liquid_items_BuiltinBattery);
		$array_intersect_SuperSpecific = array_intersect($sku_arr, $__liquid_items_SuperSpecific);
		$array_intersect_Paste = array_intersect($sku_arr, $__liquid_items_Paste);
		
		/*if(count($array_intersect_elec) > 0 && in_array($global_countrycn_coutryen[$ebay_countryname],$__elecsku_countrycn_array)){
			$shippment_elec_directly	=	true;
			echo "料号[ ".join(', ', $array_intersect_elec)." ]为电子类产品,运到[ ".$global_countrycn_coutryen[$ebay_countryname]." ]需要直接走香港小包\n";
		}else */if(count($array_intersect_gaoji) > 0){
			$shippment_fedex_directly	=	true;
			echo "料号[ ".join(', ', $array_intersect_gaoji)." ]为高级产品,需要直接走FedEx\n";
		}else if(count($array_intersect_zhijiayou) > 0){
			$shippment_cptohkpost	=	true;
			echo "料号[ ".join(', ', $array_intersect_zhijiayou)." ]为指甲油产品,需要直接走香港小包\n";	
		}else if(count($array_intersect_yieti) > 0){
			$shippment_hkpost_directly	=	true;
			echo "料号[ ".join(', ', $array_intersect_yieti)." ]为液体产品,需要直接走中国邮政\n";
		}
		############single line item order中如果有液体的产品直接设为香港小包###########1/2
		
		############ebay设置特定国家走挂号，包含特殊料号走平邮，超过70走挂号，币种为美元和英镑###########START
		//ADD BY Herman.Xi @ 2013-07-02
		$ecsql = "select countrys from ebay_cpghcalcfee where ebay_user='$user' and name in ('第六组','第七组','第八组','第九组','第十组') ";
		$ecresult = $dbcon->execute($ecsql);
		$ecarr = $dbcon->getResultArray($ecresult);
		$spec_countries = array('Turkey','Korea','North','Russian Federation','Spain','Armenia','Bosnia and Herzegovina','Vietnam','Palestine');
		$ec_countries = array();
		foreach($ecarr as $ecline){
			$strarr = array_filter(explode(',', $ecline['countrys']));
			foreach($strarr as $line){
				if(trim($line) != 'Puerto Rico'){ //波多黎各不挂号，add by herman.Xi @ 20130801
					$ec_countries[] = trim($line);
				}
			}
		}
		$union_countries = array_merge($spec_countries, $ec_countries);
		############ebay设置特定国家走挂号，包含特殊料号走平邮，超过70走挂号，币种为美元和英镑###########END
		
		$ss		= "select * from ebay_carrier where ebay_user ='$user' and country not like '%$ebay_countryname%'";
		$ss		= $dbcon->execute($ss);
		$ss		= $dbcon->getResultArray($ss);
		
		$data	= array();
		for($i=0;$i<count($ss);$i++){
			
			$shipfee	= 0;
			
			$name		= $ss[$i]['name'];
			$kg			= $ss[$i]['kg'];
			$handlefee	= $ss[$i]['handlefee'];
			$id			= $ss[$i]['id'];
			$rate		= $ss[$i]['rate'];
			$min		= $ss[$i]['min']; // 是否满足挂号条件
			
			
			if($name  == '香港小包挂号' ){
				$shipfee= calchkghpost($totalweight,$ebay_countryname);
				/*if(($ebay_total >= $min) || (in_array($ebay_couny, array('AR','BR','PE','CL','PY','BO','EC','GF','CO','GY','SR','UY','VE','RU')) && ($shippment_cptohkpost === true || $shippment_hkpost_directly === true || count($array_intersect_BuiltinBattery) > 0 || count($array_intersect_Paste) > 0))){
					$gg		= "insert into ebay_lishicalcfee(name,value,shippingid,orderid,totalweight)
							  values('$name','$shipfee','$id','$ebayid','$totalweight')";
					echo "$name:$shipfee\n";
					$dbcon->execute($gg);
				}*/
				if(($ebay_total >= $min)){
					$gg		= "insert into ebay_lishicalcfee(name,value,shippingid,orderid,totalweight)
							  values('$name','$shipfee','$id','$ebayid','$totalweight')";
					echo "$name:$shipfee\n";
					$dbcon->execute($gg);
				}
			}
			/****************************************************/
			if($name  == '香港小包平邮'){			
				$shipfee	= calchkpost($totalweight,$ebay_countryname);
				echo "$name:$shipfee\n";
				$gg			= "insert into ebay_lishicalcfee(name,value,shippingid,orderid,totalweight) 
								values('$name','$shipfee','$id','$ebayid','$totalweight')";
				$dbcon->execute($gg);
			}
			
			if($name  == 'EUB' && ($ebay_countryname == 'United States' || $ebay_countryname == 'US')){
			
				$discount	= $ss[$i]['discount']?$ss[$i]['discount']:1;
				if($totalweight <= 0.06){
					$shipfee	= 80*0.06+7;
				}else{
					$shipfee	= 80*$totalweight+7;
				}
				$shipfee	= $shipfee * $discount;
				$gg			= "insert into ebay_lishicalcfee(name,value,shippingid,orderid,totalweight) 
								values('$name','$shipfee','$id','$ebayid','$totalweight')";			
				$dbcon->execute($gg);
				echo "$name:$shipfee\n";
			}
			
			if($name  == '中国邮政平邮'){
				$shipfee = calcchinapostpy($totalweight, $ebay_countryname);
				if($shipfee !== false){
					$gg		= "insert into ebay_lishicalcfee(name,value,shippingid,orderid,totalweight) 
								values('$name','$shipfee','$id','$ebayid','$totalweight')";
					if($dbcon->execute($gg)){
						
					}else{
						echo "Fail : $gg\n";
					}
					echo "$name : $shipfee 满足重量区间: $totalweight 如果有重量区间,则以后面重量计算\n";
				}else{
					echo "{$ebay_countryname} 未开通中国邮政平邮\n";				
				}
			}
			
			if($name  == '中国邮政挂号'){
				$shipfee = calcchinapostgh($totalweight, $ebay_countryname);
				if($shipfee !== false){
					$gg		= "insert into ebay_lishicalcfee(name,value,shippingid,orderid,totalweight) 
								values('$name','$shipfee','$id','$ebayid','$totalweight')";
					if($dbcon->execute($gg)){
						
					}else{
						echo "Fail : $gg\n";
					}
					echo "$name : $shipfee 满足重量区间: $totalweight 如果有重量区间,则以后面重量计算\n";
				}else{
					echo "{$ebay_countryname} 未开通中国邮政挂号\n";
				}
			}
			
			if($name  == 'EMS'){			
				$dd		= "SELECT * FROM  `ebay_emscalcfee` where countrys like '%$ebay_countryname%' ";
				$dd		= $dbcon->execute($dd);
				$dd		= $dbcon->getResultArray($dd);
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
			
				$shipfee	= $shipfee *$discount+$declared_value;
				
				if($totalweight > 0){
				
					$gg		= "insert into ebay_lishicalcfee(name,value,shippingid,orderid,totalweight) 
								values('$name','$shipfee','$id','$ebayid','$totalweight')";
					$dbcon->execute($gg);
				}
				echo "$name : $shipfee 满足重量区间:$totalweight 如果有重量区间,则以后面重量计算\n";
			
			}
			
			if ($name  == 'FedEx'){
				if($shippment_cptohkpost === true || count($array_intersect_fenmocsku) > 0 || count($array_intersect_BuiltinBattery) > 0 || count($array_intersect_Paste) > 0 ){
					echo "包含特殊料号不走联邦!\n"; //add by Herman.Xi
				}else{
					$shipfee	= calcfedex($totalweight, $ebay_countryname, $ebayid);
					$gg		= "insert into ebay_lishicalcfee(name,value,shippingid,orderid,totalweight) 
										values('$name','$shipfee','$id','$ebayid','$totalweight')";
					$dbcon->execute($gg);
					echo "$name : $shipfee 满足重量区间:$totalweight 如果有重量区间,则以后面重量计算\n";
				}
			}
			
			if ($name  == 'Global Mail'&&in_array($g_account, $SYSTEM_ACCOUNTS['海外销售平台'])){
				$shipfee	= calcglobalmail($totalweight, $ebay_countryname);
				$gg		= "insert into ebay_lishicalcfee(name,value,shippingid,orderid,totalweight) 
									values('$name','$shipfee','$id','$ebayid','$totalweight')";
				$dbcon->execute($gg);
				echo "$name : $shipfee 满足重量区间:$totalweight 如果有重量区间,则以后面重量计算\n";
			}
			
			if ($name  == 'DHL'){
				$shipfee	= calcdhlshippingfee($totalweight, $ebay_countryname);
				$gg		= "insert into ebay_lishicalcfee(name,value,shippingid,orderid,totalweight) 
									values('$name','$shipfee','$id','$ebayid','$totalweight')";
				$dbcon->execute($gg);
				echo "$name : $shipfee 满足重量区间:$totalweight 如果有重量区间,则以后面重量计算\n";
			}
			
		}
		//sleep(10);//主从同步延时
		$ss		= "select * from ebay_carrier where ebay_account like '%$ebay_account%'";
		$ss		= $dbcon->execute($ss);
		$ss		= $dbcon->getResultArray($ss);
		
		$ff	= 0;
		if(count($ss) > 0){
			$ff	= 1;
		}
		
		$ss = "select * from ebay_lishicalcfee where orderid ='$ebayid' and value != '0' and name !='EUB' order by value asc ";
		$ss		= $dbcon->execute($ss);
		$ss		= $dbcon->getResultArray($ss);
		
		/*##############中国邮政挂号(总价大于40走挂号)#############START
		if ($ebay_total > 40){
			$ss = "select * from ebay_lishicalcfee where name = '中国邮政挂号' and orderid ='$ebayid' ";
			$ss		= $dbcon->execute($ss);
			$ss		= $dbcon->getResultArray($ss);
		}
		##############Global Mail(海外销售专走)#############START
		if (in_array($g_account, $SYSTEM_ACCOUNTS['海外销售平台'])){
			$ss = "select * from ebay_lishicalcfee where name = 'Global Mail' and orderid ='$ebayid' ";
			$ss		= $dbcon->execute($ss);
			$ss		= $dbcon->getResultArray($ss);
		}else
		##############(sunwebzone,enjoy24hours,charmday88,betterdeals255,360beauty这5个账号除了以上的设置，还需要额外对US站点进行以下修改：针对第6点，ERP在同步订单的时候，订单金额(不包含运费)小于等于5.00(不限币种)，发货国家为美国或者波多黎各时，自动选择一个最便宜的运输方式，不受EUB影响#############START
		if (in_array($g_account, array('sunwebzone','enjoy24hours','charmday88','betterdeals255','360beauty')) && ($ebay_countryname == 'United States' ||  $ebay_countryname == 'US' || $ebay_countryname == 'Puerto Rico') && $eub_to_py){
			if($shippment_cptohkpost === true || $shippment_hkpost_directly === true || count($array_intersect_BuiltinBattery) > 0 || count($array_intersect_Paste) > 0){
				if($ebay_total >= 70 ){
					$ss 	= "select * from ebay_lishicalcfee where orderid ='$ebayid' and value != '0' and name ='香港小包挂号' order by value asc ";
				}else{
					$ss 	= "select * from ebay_lishicalcfee where orderid ='$ebayid' and value != '0' and name = '香港小包平邮' order by value asc ";			
				}	
			}else{
				if($ebay_total > 40 ){
					$ss 	= "select * from ebay_lishicalcfee where orderid ='$ebayid' and value != '0' and name ='中国邮政挂号' order by value asc ";
				}else{
					$ss 	= "select * from ebay_lishicalcfee where orderid ='$ebayid' and value != '0' and name ='中国邮政平邮' order by value asc ";	
				}
			}
			$ss		= $dbcon->execute($ss);
			$ss		= $dbcon->getResultArray($ss);
		}else
		##############EUB(EBAY US 站点)#############START
		if($ff == 1 && ($ebay_countryname == 'United States' ||  $ebay_countryname == 'US' || $ebay_countryname == 'Puerto Rico')){
			$ss = "select * from ebay_lishicalcfee where name = 'EUB' and orderid ='$ebayid' ";
			$ss		= $dbcon->execute($ss);
			$ss		= $dbcon->getResultArray($ss);
		}else
		##############FedEx(包含单个超重料号或者贵重SKU)#############START
		if ($shippment_fedex_directly===true){
			$ss 	= "select * from ebay_lishicalcfee where orderid ='$ebayid' and value != '0' and name = 'FedEx' order by value asc ";			
			$ss		= $dbcon->execute($ss);
			$ss		= $dbcon->getResultArray($ss);
		}else
		//modified by Herman.Xi @ 2013-07-08
		if(in_array($g_account,array('betterdeals255','dealinthebox','easytrade2099','bestinthebox','fiveseason88','befdi','enicer','mysoulfor','newcandy789','estore456','eseasky68','swzeagoo','happyzone80','infourseas','emallzone','unicecho','vobeau','blessedness365'))){
			if($shippment_cptohkpost === true || $shippment_hkpost_directly === true || count($array_intersect_BuiltinBattery) > 0 || count($array_intersect_Paste) > 0){
				if($ebay_total >= 70 ){
					$ss 	= "select * from ebay_lishicalcfee where orderid ='$ebayid' and value != '0' and name ='香港小包挂号' order by value asc ";
				}else{
					$ss 	= "select * from ebay_lishicalcfee where orderid ='$ebayid' and value != '0' and name = '香港小包平邮' order by value asc ";			
				}
			}else{
				if($ebay_total > 40 ){
					$ss 	= "select * from ebay_lishicalcfee where orderid ='$ebayid' and value != '0' and name ='中国邮政挂号' order by value asc ";
				}else{
					$ss 	= "select * from ebay_lishicalcfee where orderid ='$ebayid' and value != '0' and name ='中国邮政平邮' order by value asc ";	
				}
			}
			$ss		= $dbcon->execute($ss);
			$ss		= $dbcon->getResultArray($ss);
		}
		else
		//以下这些国家在同步订单的时候，金额大于10才挂号寄出，不管什么币种都行
		//Japan,Korea, South,Malaysia,Singapore,Portugal,Czech Republic,Italy,Israel,Ireland 
		if(in_array($ebay_countryname, array('Japan','Korea, South','Malaysia','Singapore','Portugal','Czech Republic','Italy','Israel','Ireland')) && $ebay_total > 10){
			if($shippment_cptohkpost === true || $shippment_hkpost_directly === true || count($array_intersect_BuiltinBattery) > 0 || count($array_intersect_Paste) > 0){
				if($ebay_total >= 70 ){
					$ss 	= "select * from ebay_lishicalcfee where orderid ='$ebayid' and value != '0' and name ='香港小包挂号' order by value asc ";
				}else{
					$ss 	= "select * from ebay_lishicalcfee where orderid ='$ebayid' and value != '0' and name = '香港小包平邮' order by value asc ";			
				}
			}else{
				$ss 	= "select * from ebay_lishicalcfee where orderid ='$ebayid' and value != '0' and name ='中国邮政挂号' order by value asc ";	
			}
			$ss		= $dbcon->execute($ss);
			$ss		= $dbcon->getResultArray($ss);
		}else
		//南美洲国家开放走挂号，特殊料号走香港小包挂号
		//modified by Herman.Xi @ 2013.05.28
		//if(in_array($ebay_couny, array('AR','BR','PE','CL','PY','BO','EC','GF','CO','GY','SR','UY','VE','RU'))){
		if(in_array($ebay_countryname, $union_countries) && in_array($ebay_currency, array('GBP','USD'))){//指定的这些国家，并且订单币种为美元和英镑 Modified by Herman.Xi @ 2013-07-02
			if($shippment_cptohkpost === true || $shippment_hkpost_directly === true || count($array_intersect_BuiltinBattery) > 0 || count($array_intersect_Paste) > 0){
				if($ebay_total >= 70 ){
					$ss 	= "select * from ebay_lishicalcfee where orderid ='$ebayid' and value != '0' and name ='香港小包挂号' order by value asc ";
				}else{
					$ss 	= "select * from ebay_lishicalcfee where orderid ='$ebayid' and value != '0' and name = '香港小包平邮' order by value asc ";			
				}
			}else{
				$ss 	= "select * from ebay_lishicalcfee where orderid ='$ebayid' and value != '0' and name ='中国邮政挂号' order by value asc ";	
			}
			$ss		= $dbcon->execute($ss);
			$ss		= $dbcon->getResultArray($ss);
		}else
		##############液体(不含指甲油)SKU(中国邮政转香港小包)#############START
		##############指甲油SKU(中国邮政转香港小包)#############START
		##############电子类产品SKU(指定国家的订单 走香港小包)#############START
		##############内置电池SKU(中国邮政转香港小包)#############START
		##############膏状SKU(中国邮政转香港小包)#############START
		//add by Herman.Xi 2013-03-14
		if($shippment_cptohkpost === true || $shippment_hkpost_directly ===true || count($array_intersect_BuiltinBattery) > 0 || count($array_intersect_Paste) > 0){
			if($ebay_total >= 70 ){
				$ss 	= "select * from ebay_lishicalcfee where orderid ='$ebayid' and value != '0' and name ='香港小包挂号' order by value asc ";
			}else{
				$ss 	= "select * from ebay_lishicalcfee where orderid ='$ebayid' and value != '0' and name = '香港小包平邮' order by value asc ";			
			}
			$ss		= $dbcon->execute($ss);
			$ss		= $dbcon->getResultArray($ss);
		}*/
		//implode("\n", $dbcon->error)."\n\n";
		//modified by Herman.Xi @ 2013-07-18 23:45 所有订单按照原始逻辑判断最优运输方式计算
		##############Global Mail(海外销售专走)#############START
		//modified by Herman.Xi @ 2013-07-20 9:44 走意大利和包含特殊料号的订单保留原GM运输方式，非意大利国家订单不包含特殊料号走中国邮政平邮（只限于cndirect55,futurestar99）
		/*if (in_array($g_account, $SYSTEM_ACCOUNTS['海外销售平台'])){
			//add by Herman.Xi @ 20130725 小语种账号 1 欧元 冲销量 走中国邮政平邮
			if(in_array($g_account, array('cndirect998','easydealhere','tradekoo','allbestforu','easydeal365','enjoytrade99','freemart21cn','ishop2099')) && $ebay_total == 1 && $ebay_currency == 'EUR'){
				$ss 	= "select * from ebay_lishicalcfee where orderid ='$ebayid' and value != '0' and name ='中国邮政平邮' order by value asc ";
			}else
			//add by Herman.Xi @ 2013-07-24 eshoppingstar75,ishoppingclub68 两个账号，已经账号easyshopping095 当国家为美国是走联邦
			if((in_array($g_account, array('eshoppingstar75','ishoppingclub68'))) || (in_array($g_account, array('easyshopping095')) && ($ebay_countryname == 'United States' || $ebay_countryname == 'US'))){
				if($shippment_cptohkpost === true || count($array_intersect_fenmocsku) > 0 || count($array_intersect_BuiltinBattery) > 0 || count($array_intersect_Paste) > 0 ){
					$ss = "select * from ebay_lishicalcfee where name = 'Global Mail' and orderid ='$ebayid' ";
				}else{
					$ss 	= "select * from ebay_lishicalcfee where orderid ='$ebayid' and value != '0' and name = 'FedEx' order by value asc ";
				}
			}else
			if ($ebay_countryname!='Italia'){
				if ($shippment_fedex_directly===true){
					$ss 	= "select * from ebay_lishicalcfee where orderid ='$ebayid' and value != '0' and name = 'FedEx' order by value asc ";			
				}else
				if(in_array($g_account, array('cndirect55','futurestar99','easydeal365','cndirect998'))){
					if($shippment_cptohkpost === true || $shippment_hkpost_directly === true || count($array_intersect_BuiltinBattery) > 0 || count($array_intersect_Paste) > 0){
						$ss = "select * from ebay_lishicalcfee where name = 'Global Mail' and orderid ='$ebayid' ";
					}else{
						if($ebay_total > 40 ){
							$ss 	= "select * from ebay_lishicalcfee where orderid ='$ebayid' and value != '0' and name ='中国邮政挂号' order by value asc ";
						}else{
							$ss 	= "select * from ebay_lishicalcfee where orderid ='$ebayid' and value != '0' and name ='中国邮政平邮' order by value asc ";	
						}
					}
				}else{
					$ss = "select * from ebay_lishicalcfee where name = 'Global Mail' and orderid ='$ebayid' ";	
				}
			}else{
				$ss = "select * from ebay_lishicalcfee where name = 'Global Mail' and orderid ='$ebayid' ";
			}
			$ss		= $dbcon->execute($ss);
			$ss		= $dbcon->getResultArray($ss);
		}*/
		if(in_array($g_account, array('enjoytrade99','allbestforu','freemart21cn','easydealhere')) && in_array($ebay_countryname, array('Deutschland','Frankreich','Spanien','Italien','Allemagne','France','Espagne','Italie','Alemania','Francia','España','Italia','Germania','Francia','Spagna','Italia'))){
			$ss = "select * from ebay_lishicalcfee where name = 'Global Mail' and orderid ='$ebayid' ";
			$ss		= $dbcon->execute($ss);
			$ss		= $dbcon->getResultArray($ss);
		}else
		##############FedEx(包含单个超重料号或者贵重SKU)#############START
		if ($shippment_fedex_directly===true){
			$ss 	= "select * from ebay_lishicalcfee where orderid ='$ebayid' and value != '0' and name = 'FedEx' order by value asc ";			
			$ss		= $dbcon->execute($ss);
			$ss		= $dbcon->getResultArray($ss);
		}else
		/*
		 *(陈小霞)2013-08-31 09:44:11
		 *帮忙设置下这两个账号总金额(价格+运费)超过5的，发往美国和波多黎各的，改为EUB发货：mysoulfor,newcandy789
		 *雷贤容 加上 estore456
		*/
		if(in_array($ebay_countryname, array('United States','US','Puerto Rico')) && $ebay_total >= 5 && in_array($g_account,array('mysoulfor','newcandy789','estore456'))){
			$ss = "select * from ebay_lishicalcfee where name = 'EUB' and orderid ='$ebayid' ";
			$ss		= $dbcon->execute($ss);
			$ss		= $dbcon->getResultArray($ss);
		}else
		##############EUB(EBAY US 站点)#############START
		if($ff == 1 && ($ebay_countryname == 'United States' ||  $ebay_countryname == 'US')){
			$ss = "select * from ebay_lishicalcfee where name = 'EUB' and orderid ='$ebayid' ";
			$ss		= $dbcon->execute($ss);
			$ss		= $dbcon->getResultArray($ss);
		}
		else
		/*
		 *(陈小霞)2013-09-06 09:44:11
		 *帮忙设置下这些账号的Russian Federation,Russia,Brazil,Brasil,Argentina三个国家总金额超过8的，设置挂号发货，其他账号或者其他国家仍然跟以前一样，中国邮政超过40挂号，香港小包超过70挂号
		*/
		if(in_array($ebay_countryname, array('Russian Federation','Russia','Brazil','Brasil','Argentina')) && $ebay_total >= 8 && in_array($g_account,array('365digital','digitalzone88','itshotsale77','cndirect998','cndirect55','befdi','easydeal365','enicer','doeon','starangle88','zealdora','360beauty','befashion','charmday88','dresslink','easebon','work4best','eshop2098','happydeal88','easytrade2099','easyshopping678','futurestar99','wellchange','voguebase55')) && in_array($ebay_currency, array('GBP','USD','EUR'))){
			if($shippment_cptohkpost === true || $shippment_hkpost_directly === true || count($array_intersect_Paste) > 0){
			//if($shippment_cptohkpost === true || $shippment_hkpost_directly === true || count($array_intersect_BuiltinBattery) > 0 || count($array_intersect_Paste) > 0){ //20130905内置电池不走香港小包
				if($ebay_total >= 70 ){
					$ss 	= "select * from ebay_lishicalcfee where orderid ='$ebayid' and value != '0' and name ='香港小包挂号' order by value asc ";
				}else{
					$ss 	= "select * from ebay_lishicalcfee where orderid ='$ebayid' and value != '0' and name = '香港小包平邮' order by value asc ";			
				}
			}else{
				$ss 	= "select * from ebay_lishicalcfee where orderid ='$ebayid' and value != '0' and name ='中国邮政挂号' order by value asc ";	
			}
			$ss		= $dbcon->execute($ss);
			$ss		= $dbcon->getResultArray($ss);
		}
		/*else
		//modified by Herman.Xi @ 2013-07-08
		if(in_array($g_account,array('betterdeals255','dealinthebox','easytrade2099','bestinthebox','fiveseason88','befdi','enicer','mysoulfor','newcandy789','estore456','eseasky68','swzeagoo','happyzone80','infourseas','emallzone','unicecho','vobeau','blessedness365','niceforu365','365digital','charmday88','choiceroad','easebon'))){
			if($shippment_cptohkpost === true || $shippment_hkpost_directly === true || count($array_intersect_Paste) > 0){
			//if($shippment_cptohkpost === true || $shippment_hkpost_directly === true || count($array_intersect_BuiltinBattery) > 0 || count($array_intersect_Paste) > 0){ //20130905内置电池不走香港小包
				if($ebay_total >= 70 ){
					$ss 	= "select * from ebay_lishicalcfee where orderid ='$ebayid' and value != '0' and name ='香港小包挂号' order by value asc ";
				}else{
					$ss 	= "select * from ebay_lishicalcfee where orderid ='$ebayid' and value != '0' and name = '香港小包平邮' order by value asc ";			
				}
			}else{
				if($ebay_total > 40 ){
					$ss 	= "select * from ebay_lishicalcfee where orderid ='$ebayid' and value != '0' and name ='中国邮政挂号' order by value asc ";
				}else{
					$ss 	= "select * from ebay_lishicalcfee where orderid ='$ebayid' and value != '0' and name ='中国邮政平邮' order by value asc ";	
				}
			}
			$ss		= $dbcon->execute($ss);
			$ss		= $dbcon->getResultArray($ss);
		}*/
		/*else
		//以下这些国家在同步订单的时候，金额大于10才挂号寄出，不管什么币种都行
		//Japan,Korea, South,Malaysia,Singapore,Portugal,Czech Republic,Italy,Israel,Ireland 
		if(in_array($ebay_countryname, array('Japan','Korea, South','Malaysia','Singapore','Portugal','Czech Republic','Italy','Israel','Ireland')) && $ebay_total > 10){
			if($shippment_cptohkpost === true || $shippment_hkpost_directly === true || count($array_intersect_Paste) > 0){
			//if($shippment_cptohkpost === true || $shippment_hkpost_directly === true || count($array_intersect_BuiltinBattery) > 0 || count($array_intersect_Paste) > 0){ //20130905内置电池不走香港小包
				if($ebay_total >= 70 ){
					$ss 	= "select * from ebay_lishicalcfee where orderid ='$ebayid' and value != '0' and name ='香港小包挂号' order by value asc ";
				}else{
					$ss 	= "select * from ebay_lishicalcfee where orderid ='$ebayid' and value != '0' and name = '香港小包平邮' order by value asc ";			
				}
			}else{
				$ss 	= "select * from ebay_lishicalcfee where orderid ='$ebayid' and value != '0' and name ='中国邮政挂号' order by value asc ";	
			}
			$ss		= $dbcon->execute($ss);
			$ss		= $dbcon->getResultArray($ss);
		}*/
		/*else
		//南美洲国家开放走挂号，特殊料号走香港小包挂号
		//modified by Herman.Xi @ 2013.05.28
		//if(in_array($ebay_couny, array('AR','BR','PE','CL','PY','BO','EC','GF','CO','GY','SR','UY','VE','RU'))){
		if(in_array($g_account, array('keyhere','befdimall','Doeon','digitalzone88','enjoy24hours','sunwebhome','befashion','sunwebzone','wellchange','360beauty','itshotsale77','elerose88','cafase88','niceinthebox','starangle88','zealdora','voguebase55','dresslink','happydeal88','easyshopping678','work4best','eshop2098','estore2099')) && in_array($ebay_countryname, $union_countries) && in_array($ebay_currency, array('GBP','USD'))){//指定的这些国家，并且订单币种为美元和英镑 Modified by Herman.Xi @ 2013-07-02 last modified by Herman.Xi @ 2013-07-20 加账号限制
			if($shippment_cptohkpost === true || $shippment_hkpost_directly === true || count($array_intersect_Paste) > 0){
			//if($shippment_cptohkpost === true || $shippment_hkpost_directly === true || count($array_intersect_BuiltinBattery) > 0 || count($array_intersect_Paste) > 0){ //20130905内置电池不走香港小包
				if($ebay_total >= 70 ){
					$ss 	= "select * from ebay_lishicalcfee where orderid ='$ebayid' and value != '0' and name ='香港小包挂号' order by value asc ";
				}else{
					$ss 	= "select * from ebay_lishicalcfee where orderid ='$ebayid' and value != '0' and name = '香港小包平邮' order by value asc ";			
				}
			}else{
				$ss 	= "select * from ebay_lishicalcfee where orderid ='$ebayid' and value != '0' and name ='中国邮政挂号' order by value asc ";	
			}
			$ss		= $dbcon->execute($ss);
			$ss		= $dbcon->getResultArray($ss);
		}*/
		else
		{
			if($shippment_cptohkpost === true || $shippment_hkpost_directly === true || count($array_intersect_Paste) > 0){
			//if($shippment_cptohkpost === true || $shippment_hkpost_directly === true || count($array_intersect_BuiltinBattery) > 0 || count($array_intersect_Paste) > 0){ //20130905内置电池不走香港小包
				if($ebay_total >= 70 ){
					$ss 	= "select * from ebay_lishicalcfee where orderid ='$ebayid' and value != '0' and name ='香港小包挂号' order by value asc ";
				}else{
					$ss 	= "select * from ebay_lishicalcfee where orderid ='$ebayid' and value != '0' and name = '香港小包平邮' order by value asc ";			
				}	
			}else{
				if($ebay_total > 40 ){
					$ss 	= "select * from ebay_lishicalcfee where orderid ='$ebayid' and value != '0' and name ='中国邮政挂号' order by value asc ";
				}else{
					$ss 	= "select * from ebay_lishicalcfee where orderid ='$ebayid' and value != '0' and name ='中国邮政平邮' order by value asc ";	
				}
			}
			$ss		= $dbcon->execute($ss);
			$ss		= $dbcon->getResultArray($ss);	
		}
		$ssname	= $ss[0]['name'];
		$value	= $ss[0]['value'];
		echo "最终使用:$ssname---$value\n";
		$totalweight	= $ss[0]['totalweight'];
		
		if($totalweight == 0){$ssname = "中国邮政平邮";}//当可能出现总重量为零的情况下将运输方式设置为中国邮政平邮 add by Herman.Xi 2012-11-01
		$data	= array();
		$data[0]	= $ssname;
		$data[1]	= $value;
		$data[2]	= $totalweight;
		return $data;						
	}
	
	function calceveryweight($weightarray, $totalfee){
		$feearray = array();
		$totalweight = array_sum($weightarray);
		foreach ($weightarray AS $weight){
			$feearray[] = round(($totalfee*$weight/$totalweight), 2);
		}
		return $feearray;
	}
	
	function CheckOrderSN($recordnumber,$account){
		global $dbcon;		
		$sql	= " select ebay_ordersn from ebay_order 
					where recordnumber='$recordnumber' and ebay_account='$account'";
		$sql  	= $dbcon->execute($sql);
		$sql  	= $dbcon->getResultArray($sql);
		if(count($sql) == 0){
			$status	= "0";	
			echo "未添加 需入库\n";
		}else{
			$status = $sql[0]['ebay_ordersn'];
			echo "已经存在\n";
		}
		return $status;
	}
	function CheckeBayOrderIDExists($oSellerOrderID,$ebay_account){
		global $dbcon;		
		$sql	= " select ebay_orderid from ebay_order 
					where ebay_orderid='".$oSellerOrderID."' and ebay_account='".$ebay_account."'";
		$sql  	= $dbcon->execute($sql);
		$sql  	= $dbcon->getResultArray($sql);
		if(count($sql) == 0){
			echo "未添加 需入库\n";
			return false;
		}else{
			echo "已经存在\n";
			@pop_ebay_orderid_queue($oSellerOrderID,$ebay_account);
			return true;
		}
	}
	function get_good_location($sku,$user){
		global $dbcon;
		if(strpos($sku, '*')!==false){
			$skus = explode('*',$sku);
			$sku = $skus[1];
		}
		$ss	= "SELECT goods_location FROM  `ebay_goods` where ebay_user='$user' and goods_sn='$sku'";
		$ss	= $dbcon->execute($ss);
		$ss	= $dbcon->getResultArray($ss);
		return @$ss[0]['goods_location'];
	}
	function calc_packingweight($sku,$user){
		global $dbcon;
		$ee	= "SELECT ebay_packingmaterial,goods_weight,capacity 
				FROM ebay_goods 
				where goods_sn='$sku' 
				and ebay_user='$user' limit 1";
		echo "$ee\n";
		$ee	= $dbcon->execute($ee);
		$ee = $dbcon->getResultArray($ee);
		print_r($ee[0]);
		return $ee[0];
	}
	//计算某物品的包装材质及包裹重量
	//返回:包裹类型
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
				}	*/
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
			
			$pweight= @$global_packingmaterial_weight[$ebay_packingmaterial];
			
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
			//if($totalweight2>0) $totalweight2	+= 0.6 * $pweight ;
		}
		echo " sku(重量):$sku($goods_weight) 包装材料(重量):$ebay_packingmaterial($pweight)\n";
		return	$ebay_packingmaterial;
	}
	
	function recalcorderweight($ordersn, &$ebay_packingmaterial){
		/*
		***add by Herman.Xi
		***create date 20121012
		***计算订单总重量***
		一、单料号,数量为1:
			重量=料号重量+包材重量。
		二、单料号,数量为多个:
			当总数小于包材容量时,重量=料号重量*总数+包材重量;
			当总数大于包材容量时,重量=料号重量*总数+1个包材重量+(总数-包材容量)/包材容量*0.6*包材重量。
		三、单料号组合:
			当总数小于包材容量时,重量=料号重量*总数+包材重量;
			当总数大于包材容量时,重量=料号重量*总数+1个包材重量+(总数-包材容量)/包材容量*0.6*包材重量。
		四、多料号组合:
			重量=(料号1总数/包材1容量)*0.6*包材1重量 + (料号1重量 * 料号1总数)
				+(料号2总数/包材2容量)*0.6*包材2重量 + (料号2重量 * 料号2总数) 
				+ ....
		
		注:'/'是除,'*'是乘,'%'是求余。
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
	
	//订单加载函数--交易形式
	function GetSellerTransactions($ebay_starttime,$ebay_endtime,$ebay_account,$type,$id){
		global $api_gst,$oa,$user;
		global $dbcon,$mctime,$defaultstoreid;
	
		$pcount	= 1;
		$errors	= 1;	
		do{
			echo	"抓取....\t";
			$responseXml=$api_gst->request($ebay_starttime,$ebay_endtime,$pcount);
			if(empty($responseXml)){
				echo "Return Empty...Sleep 10 seconds..";
				sleep(10);
				$hasmore=true;
				continue;
			}
			//网络出现代理Proxy error 脚本休眠20秒
			$poxy_error_p='#Proxy\s*Error#i';
			if(preg_match($poxy_error_p,$responseXml)){
				echo "Proxy Error...Sleep 20 seconds..";
				sleep(20);
				$hasmore=true;
				continue;
			}
			echo "\n";
			$data=XML_unserialize($responseXml);
			$responseXml=null;
			unset($responseXml);
		
			$getorder 	= $data['GetSellerTransactionsResponse'];
			$data=null;
			unset($data);
			
			$TotalNumberOfPages	 	= @$getorder['PaginationResult']['TotalNumberOfPages'];
			$TotalNumberOfEntries	= @$getorder['PaginationResult']['TotalNumberOfEntries'];
		
			$hasmore 	= @$getorder['HasMoreTransactions'];
			$strline	= $TotalNumberOfPages.'/'.$TotalNumberOfEntries;
		
			$Ack	 	= @$getorder['Ack'];
		
			echo "正在请求:$pcount/$TotalNumberOfPages \t记录数[ $TotalNumberOfEntries ]\t
				  同步状态: $Ack 还有更多: $hasmore \n";
		
			if($id == '' && $type == '1'){
				if($Ack == '' ){
					$ss	= "insert into errors_ack(ebay_account,starttime,endtime,status,notes) 
							values('$ebay_account','$ebay_starttime','$ebay_endtime','0','Ack False')";
					$dbcon->execute($ss);
				}
				if($hasmore == '' || $Ack == '' ){
					$ss	= "insert into errors_ack(ebay_account,starttime,endtime,status,notes) 
							values('$ebay_account','$ebay_starttime','$ebay_endtime','0','Ack False')";
					$dbcon->execute($ss);
				}
			}
		
			if($id>0){
				if($Ack == 'Success'){
					$gg	= "update errors_ack set status = 1 where id='$id' ";				
				}else{
					$gg	= "update errors_ack set status = 0 where id='$id' ";
				}
				$dbcon->execute($gg);
			}
			/**/
			$log_name	 		= '同步订单';
			$log_operationtime  = $mctime;
			$log_notes	 	    = $ebay_account.":$pcount/$TotalNumberOfPages ,Ack=$Ack";
			
			addlogs($log_name,$log_operationtime,0,$log_notes,$user,
					$ebay_account,$ebay_starttime,$ebay_endtime,$type);
			
			/**/
			$Trans	= @$getorder['TransactionArray']['Transaction'];
			$ReturnedTransactionCountActual = @$getorder['ReturnedTransactionCountActual'];
			
			if($ReturnedTransactionCountActual == 1){
				$Trans	= array();
				$Trans[0] = $getorder['TransactionArray']['Transaction'];
			}	
		
			$getorder=null;
			unset($getorder);
		
			foreach((array)$Trans as $Transaction){
				//每笔记录编号
				$tran_recordnumber	= $Transaction['ShippingDetails']['SellingManagerSalesRecordNumber'];
				//交易状态
				$LastTimeModified 	= strtotime($Transaction['Status']['LastTimeModified']);			
				$eBayPaymentStatus 	= $Transaction['Status']['eBayPaymentStatus'];
				$CompleteStatus 	= $Transaction['Status']['CompleteStatus'];		
				$CheckStatus 		= $Transaction['Status']['CompleteStatus'];
				//其他交易信息比如payapl整合到ebay
				$ptid 				= @$Transaction['ExternalTransaction']['ExternalTransactionID'];
				
				$FeeOrCreditAmount 	= @$Transaction['ExternalTransaction']['FeeOrCreditAmount'];
				$FinalValueFee		= $Transaction['FinalValueFee'];
				
				$tid				= $Transaction['TransactionID'];//ebay 交易号
				$AmountPaid  		= @$Transaction['AmountPaid'];
				$Buyer 				= str_rep(@$Transaction['Buyer']);
				$Email 				= str_rep(@$Buyer['Email']); //email
				$UserID 			= str_rep(@$Buyer['UserID']);//userid
				$BuyerInfo 			= $Buyer['BuyerInfo']['ShippingAddress'];
				$Name 				= str_rep($BuyerInfo['Name']);
				$Name				= mysql_real_escape_string($Name);
				$Street1 			= str_rep($BuyerInfo['Street1']);
				$Street2 			= str_rep(@$BuyerInfo['Street2']);
				$CityName 			= str_rep(@$BuyerInfo['CityName']);
				$StateOrProvince 	= str_rep(@$BuyerInfo['StateOrProvince']);
				$Country 			= str_rep(@$BuyerInfo['Country']);
				$CountryName 		= str_rep(@$BuyerInfo['CountryName']);
				$PostalCode 		= str_rep(@$BuyerInfo['PostalCode']);
				$Phone 				= @$BuyerInfo['Phone'];
				//该交易的物品信息
				$Item 				= $Transaction['Item'];
				$CategoryID 		= $Item['PrimaryCategory']['CategoryID']; //ebay刊登物品的分类ID,备用字段
				$Currency 			= $Item['Currency'];  //货币类型
				$ItemID 			= $Item['ItemID']; //ebay物品id
				$ListingType 		= $Item['ListingType'];
				$Title 				= str_rep($Item['Title']);//ebay物品标题
				$sku 				= str_rep($Item['SKU']);
				$site				= $Item['Site'];
				$CurrentPrice 		= $Item['SellingStatus']['CurrentPrice'];//产品当前价格
				
				$QuantityPurchased 	= $Transaction['QuantityPurchased']; //购买数量
				$PaidTime 			= strtotime($Transaction['PaidTime']); //付款时间
				$CreatedDate 		= strtotime($Transaction['CreatedDate']);               //交易创建时间...********多个产品订单每个产品的创建时间不同判依据
				$ShippedTime    	= strtotime($Transaction['ShippedTime']);				
				$shipingservice		= $Transaction['ShippingServiceSelected']['ShippingService'];
				$shipingfee			= $Transaction['ShippingServiceSelected']['ShippingServiceCost'];
				$containing_order	= @$Transaction['ContainingOrder'];
				$combined_recordnumber	= @$containing_order['ShippingDetails']['SellingManagerSalesRecordNumber']; //合并后的recordnumber
					
				$orderid			= 0;
				if($combined_recordnumber != ''){
					$orderid 	= @$containing_order['OrderID'];
				}else{
					$orderid	= $ItemID.'-'.$tid;
				}
				$BuyerCheckoutMessage	= str_rep($Transaction['BuyerCheckoutMessage']);//顾客购买留言
				$BuyerCheckoutMessage	= str_replace('<![CDATA[','',$BuyerCheckoutMessage);
				$BuyerCheckoutMessage	= str_replace(']]>','',$BuyerCheckoutMessage);
				//店铺收款paypal account
				$PayPalEmailAddress	= $Transaction['PayPalEmailAddress'];    
				
				$addrecordnumber    = $tran_recordnumber;					
				if($combined_recordnumber != ''){	$addrecordnumber	= $combined_recordnumber;	}
								
				if($CompleteStatus  == "Complete" && $eBayPaymentStatus == "NoPaymentFailure" && $PaidTime > 0){
					$orderstatus	= 1;
				}
				if($ShippedTime >0) $orderstatus	= 2;//已经发货
				
				################################
				$RefundAmount	= 0; //表示未垦退款
				if($orderstatus == 1 && $ShippedTime <=0 && $PaidTime >0 ){
					echo "销售编号[$addrecordnumber]有效";
					//检查汇总表该 recordnumber是否已经存在 
					//主要是避免multiple line item 这种情况 造成重复添加 汇总数据
					$check_ordersn=CheckOrderSN($addrecordnumber,$ebay_account);
					
					$new_ebay_id=true;
					if($check_ordersn == "0"){//该交易还无汇总数据	添加订单汇总
						/* 生成一个本地系统订单号 */
						$our_sys_ordersn=date('Y-m-d-His').mt_rand(100,999).$addrecordnumber;
				
						$order_no		= '';//已废弃
						
						$obj_order		= new eBayOrder();
						$obj_order_data=array('ebay_paystatus'=>$CompleteStatus,
											  'ebay_ordersn'=>$our_sys_ordersn,
											  'ebay_tid'=>$tid,
											  'ebay_ptid'=>$ptid,
											  'ebay_orderid'=>$orderid,
											  'ebay_createdtime'=>$CreatedDate,
											  'ebay_paidtime'=>$PaidTime,
											  'ebay_userid'=>$UserID,
											  'ebay_username'=>$Name,
											  'ebay_usermail'=>$Email,
											  'ebay_street'=>$Street1,
											  'ebay_street1'=>$Street2,
											  'ebay_city'=>$CityName,
											  'ebay_state'=>$StateOrProvince,
											  'ebay_couny'=>$Country,
											  'ebay_countryname'=>$CountryName,
											  'ebay_postcode'=>$PostalCode,
											  'ebay_phone'=>$Phone,
											  'ebay_currency'=>$Currency,
											  'ebay_total'=>$AmountPaid,
											  'ebay_status'=>$orderstatus,
											  'ebay_user'=>$user,
											  'ebay_shipfee'=>$shipingfee,
											  'ebay_account'=>$ebay_account,
											  'recordnumber'=>$addrecordnumber,
											  'ebay_addtime'=>$mctime,
											  'ebay_note'=>$BuyerCheckoutMessage,
											  'ebay_site'=>$site,
											  'eBayPaymentStatus'=>$eBayPaymentStatus,
											  'PayPalEmailAddress'=>$PayPalEmailAddress,
											  'ShippedTime'=>$ShippedTime,
											  'RefundAmount'=>$RefundAmount,
											  'ebay_warehouse'=>$defaultstoreid,
											  'order_no'=>$order_no
											  );
						$obj_order->init($obj_order_data);
						$obj_order_data=null;
						unset($obj_order_data);
						
						$new_ebay_id=$oa->addOrder($obj_order);
						$obj_order=null;
						unset($obj_order);
						
						if($new_ebay_id!==false){
							echo "\t订单[$our_sys_ordersn] 汇总数据入库成功=>\n\tUserID:$UserID"." AMT:$AmountPaid recordNO:$addrecordnumber ";
							echo "付款状态:$CompleteStatus 交易ID:$ptid\n";
							$check_ordersn=$our_sys_ordersn;
							
							//检验ebay 订单号 是否在订单号汇总表中
							
							if(check_ebay_orderid_exists_in_statistic_table($orderid,$ebay_account)===false){
								save_ebay_orderid_table($new_ebay_id,$ptid,$orderid,$ebay_account,$CreatedDate);
							}
							
						}else{
							echo "\t订单[$our_sys_ordersn] 入库失败\n";
						}
					}
					if($new_ebay_id!==false){//添加订单明细
						$sql = "select 	ebay_id from ebay_orderdetail 
								where 	ebay_ordersn='$check_ordersn' 
								and 	recordnumber='$tran_recordnumber'";
						$sql = $dbcon->execute($sql);
						$sql = $dbcon->getResultArray($sql);
						
						if(count($sql) == 0){							
							/* 多属性订单 */
							$Variation	= @$Transaction['Variation']['VariationSpecifics']['NameValueList'];
							$attribute	= '';
							if(	!empty($Variation)	){
								if( (!isset($Variation['Name'])) || (!isset($Variation['Value'])) ){
									foreach($Variation as $variate){
										$aname	= $variate['Name'];
										$avalue	= $variate['Value'];
										$attribute	.= $aname.":".$avalue." ";
									}
								}else{
									$attribute	= $Variation['Name'].":".$Variation['Value'];
								}
								unset($Variation);
							}
							$obj_order_detail=new eBayOrderDetail();
							$obj_order_detail_data=array('ebay_ordersn'=>$check_ordersn,
														 'ebay_itemid'=>$ItemID,
														 'ebay_itemtitle'=>$Title,
														 'ebay_itemprice'=>$CurrentPrice,
														 'ebay_amount'=>$QuantityPurchased,
														 'ebay_createdtime'=>$CreatedDate,
														 'ebay_shiptype'=>$shipingservice,
														 'ebay_user'=>$user,
														 'sku'=>$sku,
														 'shipingfee'=>$shipingfee,
														 'ebay_account'=>$ebay_account,
														 'addtime'=>$mctime,
														 'ebay_itemurl'=>'',
														 'ebay_site'=>$site,
														 'recordnumber'=>$tran_recordnumber,
														 'storeid'=>'',
														 'ListingType'=>$ListingType,
														 'ebay_tid'=>$tid,
														 'FeeOrCreditAmount'=>$FeeOrCreditAmount,
														 'FinalValueFee'=>$FinalValueFee,
														 'attribute'=>$attribute,
														 'notes'=>$BuyerCheckoutMessage,
														 'goods_location'=>@get_good_location($sku,$user)
														 );
							$obj_order_detail->init($obj_order_detail_data);
							$obj_order_detail_data=null;
							unset($obj_order_detail_data);
							
							if(	false!==($oa->addOrderDetail($obj_order_detail)) ){
								echo "\t订单[$check_ordersn] 编号[$tran_recordnumber]明细入库OK!\n";
							}else{
								echo "\t订单[$check_ordersn] 编号[$tran_recordnumber]明细入库Error!\n";
							}
							$obj_order_detail=null;
							unset($obj_order_detail);
						}
						
						$sql = "select ebay_id from ebay_orderdetail 
								where ebay_ordersn='$check_ordersn' 
								and recordnumber='$tran_recordnumber'";
					
						$sql  = $dbcon->execute($sql);
						$sql  = $dbcon->getResultArray($sql);
						if(count($sql) >=2 && strlen($check_ordersn) >=5){
							$id		= $sql[0]['ebay_id'];
							$ss		= "delete from ebay_orderdetail where ebay_id='$id'";
							$dbcon->execute($ss);
						}
						if($ShippedTime >0){
							$ss	= "update ebay_order set ShippedTime='$ShippedTime',
									ebay_status='2',ebay_markettime='$ShippedTime' 
									where ebay_ordersn='$check_ordersn' and ebay_status='1'";
							$dbcon->execute($ss);
						}

					}
				}else{
					echo "销售编号[$addrecordnumber]无效 不入库...\n";
				}
			}
		
			if($id == '' && $type == '1'){
				if($Ack == '' || $Ack == 'Failure'){
					$ss	= "insert into errors_ack(ebay_account,starttime,endtime,status,notes,currentpage) 
							values('$ebay_account','$ebay_starttime','$ebay_endtime','0','Ack False','$pcount')";
					$dbcon->execute($ss);
				}
			}

			if($pcount>= $TotalNumberOfPages ){			
				echo $hasmore."程序退出了\n";
				break;
			}
			$pcount++;
			$hasmore =(strtolower($hasmore)=='true')?true:false;
		}while($hasmore);
	}
	//订单加载函数--订单形式
	function GetSellerOrders($ebay_starttime,$ebay_endtime,$ebay_account,$type,$id){
		global $api_go,$oa,$user;
		global $dbcon,$mctime,$defaultstoreid;
		$pcount	= 1;
		$errors	= 1;
		do{
			echo	"抓取....\t";
			$responseXml=$api_go->request($ebay_starttime,$ebay_endtime,$pcount);
			if(empty($responseXml)){
				echo "Return Empty...Sleep 10 seconds..";
				sleep(10);
				$hasmore=true;
				continue;
			}
			//网络出现代理Proxy error 脚本休眠20秒
			$poxy_error_p='#Proxy\s*Error#i';
			if(preg_match($poxy_error_p,$responseXml)){
				echo "Proxy Error...Sleep 20 seconds..";
				sleep(20);
				$hasmore=true;
				continue;
			}
			echo "\n";
			$responseDoc = new DomDocument();	
			$responseDoc->loadXML($responseXml);
			
			//保存原始raw数据
			$raw_data_path	=EBAY_RAW_DATA_PATH.$ebay_account.'/date_range_order/'.date('Y-m').'/'.date('d').'/';
			$raw_data_filename	=str_replace(':','-',$ebay_starttime).'--'.str_replace(':','-',$ebay_endtime).'--p'.$pcount.'.xml';
			$raw_data_filename	=$raw_data_path.$raw_data_filename;
			$save_res	=save_ebay_raw_data($raw_data_filename,$responseXml);
			if($save_res!==false){
				echo "save raw data ok...\n";
			}else{
				echo "save raw data fail...\n";
			}
			
			$responseXml=null;unset($responseXml);
			
			$TotalNumberOfPages	 	= $responseDoc->getElementsByTagName('TotalNumberOfPages')->item(0)->nodeValue;
			$TotalNumberOfEntries	= $responseDoc->getElementsByTagName('TotalNumberOfEntries')->item(0)->nodeValue;
			$hasmore 				= $responseDoc->getElementsByTagName('HasMoreOrders')->item(0)->nodeValue;
			$Ack	 				= $responseDoc->getElementsByTagName('Ack')->item(0)->nodeValue;
		
			echo "正在请求:$pcount/$TotalNumberOfPages\t记录数[ $TotalNumberOfEntries ]\t同步状态: $Ack 还有更多:$hasmore\n";
		
			if($id == '' && $type == '1'){
				if($Ack == 'Failure'){
					$ss	= "insert into errors_ack(ebay_account,starttime,endtime,status,notes) 
							values('$ebay_account','$ebay_starttime','$ebay_endtime','0','Ack False')";
					$dbcon->execute($ss);
				}
			}
		
			if($id>0){
				if($Ack == 'Success'||$Ack == 'Warning'){
					$gg	= "update errors_ack set status = 1 where id='$id' ";				
				}else{
					$gg	= "update errors_ack set status = 0 where id='$id' ";
				}
				$dbcon->execute($gg);
			}
			/**/
			$log_name	 		= '同步订单bygo';
			$log_operationtime  = $mctime;
			$log_notes	 	    = $ebay_account.":$pcount/$TotalNumberOfPages ,Ack=$Ack";
			
			addlogs($log_name,$log_operationtime,0,$log_notes,$user,
					$ebay_account,$ebay_starttime,$ebay_endtime,$type);
			
			/**/
			$SellerOrderArray	= $responseDoc->getElementsByTagName('Order');
			
			//调用订单入库函数
			__handle_ebay_orderxml($SellerOrderArray,$ebay_account);
			$SellerOrderArray=null;unset($SellerOrderArray);
			
			if($id == '' && $type == '1'){
				if($Ack == '' || $Ack == 'Failure'){
					$ss	= "insert into errors_ack(ebay_account,starttime,endtime,status,notes,currentpage) 
							values('$ebay_account','$ebay_starttime','$ebay_endtime','0','Ack False','$pcount')";
					$dbcon->execute($ss);
				}
			}

			if($pcount>= $TotalNumberOfPages ){			
				echo $hasmore."程序退出了\n";
				break;
			}
			$pcount++;
			$hasmore =(strtolower($hasmore)=='true')?true:false;
		}while($hasmore);
	}
	//订单加载函数--某一笔交易
	function GetCertainOrder($ebay_account,$order_ids){
		global $api_gco,$oa,$user;
		global $dbcon,$mctime,$defaultstoreid;
		
		$valid_order_ids=array();
		$invalid_order_ids=array();
		$has_invalid_order_id=false;
		
		$order_p1='#^\d{12}$#i';//multiple line item order
		$order_p2='#^\d{12}\-\d{12,14}$#i';//single line item order
		$order_p3='#^\d{12}\-0$#i';//single line item order(sometimes trans id is zero)
		if(is_array($order_ids)){				
			foreach($order_ids as $orderid){
				if(	preg_match($order_p1,$orderid) || preg_match($order_p2,$orderid) || preg_match($order_p3,$orderid) ){
					$valid_order_ids[]	=$orderid;
				}else{
					$invalid_order_ids[]=$orderid;
					$has_invalid_order_id=true;
				}
			}				
		}else{
			if(	preg_match($order_p1,$order_ids) ||	preg_match($order_p2,$order_ids) || preg_match($order_p3,$order_ids) ){
				$valid_order_ids[]=$order_ids;
			}else{
				$invalid_order_ids[]=$order_ids;
				$has_invalid_order_id=true;
			}	
		}
		
		if(	$has_invalid_order_id===true ){
			exit("Error: Pass invalid ebay order id[".implode(',',$invalid_order_ids)."]\n");
		}
		echo	"抓取....\t";
		while(1){
			$responseXml=$api_gco->request($valid_order_ids);
			if(empty($responseXml)){
				echo "Return Empty...Sleep 10 seconds..";
				sleep(10);
				continue;
			}
			//网络出现代理Proxy error 脚本休眠20秒
			$poxy_error_p='#Proxy\s*Error#i';
			if(preg_match($poxy_error_p,$responseXml)){
				echo "Proxy Error...Sleep 20 seconds..";
				sleep(20);
				continue;
			}
			break;
		}
		echo "\n";
		$responseDoc = new DomDocument();	
		$responseDoc->loadXML($responseXml);
		
		$raw_data_filename=EBAY_RAW_DATA_PATH.$ebay_account.'/certain_order/'.date('Y-m').'/'.date('d').'/'.date('Y-m-d_H-i-s').'.xml';
		$save_res=save_ebay_raw_data($raw_data_filename,$responseXml);
		if($save_res!==false){
			echo "save raw data ok...\n";
		}else{
			echo "save raw data fail...\n";
		}
		$responseXml=null;
		unset($responseXml);
		
		//$TotalNumberOfPages	 	= $responseDoc->getElementsByTagName('TotalNumberOfPages')->item(0)->nodeValue;
		//$TotalNumberOfEntries	= $responseDoc->getElementsByTagName('TotalNumberOfEntries')->item(0)->nodeValue;
	
		//$hasmore 	= $responseDoc->getElementsByTagName('HasMoreOrders')->item(0)->nodeValue;
		$Ack	 	= $responseDoc->getElementsByTagName('Ack')->item(0)->nodeValue;
		$pcount		= 1;
		echo "同步状态: $Ack \n";
		if($Ack == 'Failure'){
			echo "eBay Return Failure...return.. \n";
			return;
		}
		$SellerOrderArray	= $responseDoc->getElementsByTagName('Order');
		
		//调用订单入库函数
		__handle_ebay_orderxml($SellerOrderArray,$ebay_account);
	}
	//订单加载函数--获取时间段内所有订单号 只将有效订单号 放进队列
	function GetSellerOrdersID($ebay_starttime,$ebay_endtime,$ebay_account){
		global $api_goi,$oa,$user;
		global $dbcon,$mctime,$defaultstoreid;
		$pcount	= 1;
		$errors	= 1;
		$execution_frequency = 0;
		do{
			echo	"抓取订单ID....\t";
			$responseXml=$api_goi->request($ebay_starttime,$ebay_endtime,$pcount);
			if(empty($responseXml)){
				echo "ReturnEmpty...Sleep 10 seconds..";
				sleep(10);
				$hasmore=true;
				continue;
			}
			//网络出现代理Proxy error 脚本休眠20秒
			$poxy_error_p='#Proxy\s*Error#i';
			if(preg_match($poxy_error_p,$responseXml)){
				echo "ProxyError...Sleep 20 seconds..";
				sleep(20);
				$hasmore=true;
				continue;
			}
			echo "\n";
			$responseDoc = new DomDocument();	
			$responseDoc->loadXML($responseXml);		
			
			$TotalNumberOfPages	 	= $responseDoc->getElementsByTagName('TotalNumberOfPages')->item(0)->nodeValue;
			$TotalNumberOfEntries	= $responseDoc->getElementsByTagName('TotalNumberOfEntries')->item(0)->nodeValue;
			$hasmore 				= $responseDoc->getElementsByTagName('HasMoreOrders')->item(0)->nodeValue;
			$Ack	 				= $responseDoc->getElementsByTagName('Ack')->item(0)->nodeValue;		
			echo "正在请求订单ID:$pcount/$TotalNumberOfPages\t记录数[ $TotalNumberOfEntries ]\t";
			echo "同步状态: $Ack 还有更多:$hasmore\n";
		
			if($Ack == 'Failure'){
				$execution_frequency++;
				echo "eBayRequestFailure...Sleep 10 seconds then request again...\n";
				sleep(10);
				$hasmore=($execution_frequency < 3) ? true : false;
				continue;
			}else{
				//保存原始raw数据
				$raw_data_path	=EBAY_RAW_DATA_PATH.$ebay_account.'/date_range_orderid/'.date('Y-m').'/'.date('d').'/';
				$raw_data_filename	=str_replace(':','-',$ebay_starttime).'--'.str_replace(':','-',$ebay_endtime).'--p'.$pcount.'.xml';
				$raw_data_filename	=$raw_data_path.$raw_data_filename;
				$save_res	=save_ebay_raw_data($raw_data_filename,$responseXml);
				if($save_res!==false){
					echo "save raw data ok...\n";
				}else{
					echo "save raw data fail...\n";
				}
				
				$responseXml=null;unset($responseXml);
			}
			$execution_frequency = 0;
			/**/
			$SellerOrderArray	= $responseDoc->getElementsByTagName('Order');
			
			//调用订单ID入队列函数
			__handle_ebay_orderidxml($SellerOrderArray,$ebay_account);
			$SellerOrderArray=null;unset($SellerOrderArray);
			if($pcount>= $TotalNumberOfPages ){			
				echo $hasmore."程序退出了\n";
				break;
			}
			$pcount++;
			$hasmore =(strtolower($hasmore)=='true')?true:false;
		}while($hasmore);
	}
	//订单入库函数
	function __handle_ebay_orderxml(&$SellerOrderArray,$ebay_account){
		global $api_gco,$oa,$user;
		global $dbcon,$mctime,$defaultstoreid,$_allow_spide_itemid;
		$account_suffix = get_account_suffix($ebay_account);

		foreach( $SellerOrderArray as $SellerOrder){
			//每个订单号
			$oSellerOrderID		= $SellerOrder->getElementsByTagName('OrderID')->item(0)->nodeValue;
			//oCreatingUserRole用于判断是否是 combined payments
			$oCreatingUserRole	= @$SellerOrder->getElementsByTagName('CreatingUserRole')->item(0)->nodeValue;
			$oAmountPaid  		= $SellerOrder->getElementsByTagName('AmountPaid')->item(0)->nodeValue;
			
			$shippingDeatil		= $SellerOrder->getElementsByTagName('ShippingDetails')->item(0);
			$oRecordNumber		= $shippingDeatil->getElementsByTagName('SellingManagerSalesRecordNumber')->item(0)->nodeValue;
			$shippingDeatil=null;unset($shippingDeatil);
			//订单状态
			$CheckoutStatus		= $SellerOrder->getElementsByTagName('CheckoutStatus')->item(0);
			$LastTimeModified 	= strtotime($CheckoutStatus->getElementsByTagName('LastModifiedTime')->item(0)->nodeValue);			
			$oeBayPaymentStatus = $CheckoutStatus->getElementsByTagName('eBayPaymentStatus')->item(0)->nodeValue;
			$oCompleteStatus 	= $CheckoutStatus->getElementsByTagName('Status')->item(0)->nodeValue;	
			$oCheckStatus 		= $CheckoutStatus->getElementsByTagName('Status')->item(0)->nodeValue;
			$CheckoutStatus=null;unset($CheckoutStatus);
			
			//该订单交易信息
			$osoTransArray	= $SellerOrder->getElementsByTagName('Transaction');
			
			//其他交易信息比如payapl整合到ebay
			$oTid				=0;//兼容表结构,其实此时还没有交易号的概念
			$ExtTran			=$SellerOrder->getElementsByTagName('ExternalTransaction')->item(0);
			$noptid_trans		=false;
			if(!empty($ExtTran)){
				$oPtid 				=$ExtTran->getElementsByTagName('ExternalTransactionID')->item(0)->nodeValue;	
				$oFeeOrCreditAmount =$ExtTran->getElementsByTagName('FeeOrCreditAmount')->item(0)->nodeValue;
			}else{
				$oPtid				='0';
				$oFeeOrCreditAmount =0.0;
				echo " Notice : [$oSellerOrderID]Not ebay offical paypal trans\n";
				$noptid_trans		=true;
			}
			//以下信息强制以订单的transation数据中第一条交易为准而取
			if(is_object($osoTransArray->item(0)->getElementsByTagName('Buyer')->item(0))){
				$oEmail 			= str_rep($osoTransArray->item(0)->getElementsByTagName('Buyer')->item(0)->getElementsByTagName('Email')->item(0)->nodeValue);	
			}else{
				echo "\n同步订单未获取邮箱\n";
				$oEmail = "";
			}
			$oSite				= str_rep($osoTransArray->item(0)->getElementsByTagName('Item')->item(0)->getElementsByTagName('Site')->item(0)->nodeValue);
			if(empty($oSite)){
				$oSite		=str_rep($osoTransArray->item(0)->getElementsByTagName('TransactionSiteID')->item(0)->nodeValue);
			}
			//货币类型
			$oCurrency 			= $osoTransArray->item(0)->getElementsByTagName('TransactionPrice')->item(0)->attributes->item(0)->nodeValue;
			
			
			//userid
			$oUserID 			= str_rep($SellerOrder->getElementsByTagName('BuyerUserID')->item(0)->nodeValue);
			$BuyerInfo 			= $SellerOrder->getElementsByTagName('ShippingAddress')->item(0);
			$oName 				= str_rep($BuyerInfo->getElementsByTagName('Name')->item(0)->nodeValue);
			$oName				= mysql_real_escape_string($oName);
			$oStreet1 			= str_rep($BuyerInfo->getElementsByTagName('Street1')->item(0)->nodeValue);
			$oStreet2 			= str_rep($BuyerInfo->getElementsByTagName('Street2')->item(0)->nodeValue);
			$oCityName 			= str_rep($BuyerInfo->getElementsByTagName('CityName')->item(0)->nodeValue);
			$oStateOrProvince 	= str_rep($BuyerInfo->getElementsByTagName('StateOrProvince')->item(0)->nodeValue);
			$oCountry 			= str_rep($BuyerInfo->getElementsByTagName('Country')->item(0)->nodeValue);
			$oCountryName 		= str_rep($BuyerInfo->getElementsByTagName('CountryName')->item(0)->nodeValue);
			$oPostalCode 		= str_rep($BuyerInfo->getElementsByTagName('PostalCode')->item(0)->nodeValue);
			$oPhone 			= $BuyerInfo->getElementsByTagName('Phone')->item(0)->nodeValue;
			$BuyerInfo=null;unset($BuyerInfo);
			//顾客留言
			$oBuyerCheckoutMessage	= @str_rep($SellerOrder->getElementsByTagName('BuyerCheckoutMessage')->item(0)->nodeValue);//顾客购买留言
			$oBuyerCheckoutMessage	= str_replace('<![CDATA[','',$oBuyerCheckoutMessage);
			$oBuyerCheckoutMessage	= str_replace(']]>','',$oBuyerCheckoutMessage);
			//付款时间			
			$oPaidTime 			= strtotime($SellerOrder->getElementsByTagName('PaidTime')->item(0)->nodeValue);
			$oCreateTime		= strtotime($SellerOrder->getElementsByTagName('CreatedTime')->item(0)->nodeValue);
			$oShippedTime    	= @strtotime($SellerOrder->getElementsByTagName('ShippedTime')->item(0)->nodeValue);
			
			$SSS				= $SellerOrder->getElementsByTagName('ShippingServiceSelected')->item(0);
			$oShipingService	= $SSS->getElementsByTagName('ShippingService')->item(0)->nodeValue;
			$oShipingFee		= $SSS->getElementsByTagName('ShippingServiceCost')->item(0)->nodeValue;
			
			$SSS=null;unset($SSS);
			
			//店铺收款paypal account
			//$oPayPalEmailAddress = @$SellerOrder->getElementsByTagName('PayPalEmailAddress')->item(0)->nodeValue;
			$_itemid = $osoTransArray->item(0)->getElementsByTagName('ItemID')->item(0)->nodeValue;
			$oPayPalEmailAddress = $api_gco->getPayPalEmailAddress($_itemid);
			//var_dump($Item_Elements);
			//$oPayPalEmailAddress = $Item_Elements->getElementsByTagName('PayPalEmailAddress')->nodeValue;
			//echo $oCompleteStatus."======".$oeBayPaymentStatus."======".$oPaidTime; echo "\n";			
			if($oCompleteStatus == "Complete" && $oeBayPaymentStatus == "NoPaymentFailure" && $oPaidTime > 0){
				$oOrderStatus	= 1;
			}
			if($noptid_trans === true){//不是通过ebay官方交易的paypal交易
				if($oCompleteStatus == "Complete" && $oeBayPaymentStatus == "NoPaymentFailure" && $oPaidTime > 0){
					$oOrderStatus =687;
				}
			}
			
			$is_allow_spide_itemid = false;
			if($oPaidTime<=0 || $oPaidTime=='' || empty($oPaidTime) && count($osoTransArray)==1){
				$_QuantityPurchased = $osoTransArray->item(0)->getElementsByTagName('QuantityPurchased')->item(0)->nodeValue;
				if (in_array($_itemid, $_allow_spide_itemid) && $_QuantityPurchased>0){
					echo "未付款促销订单抓取--------";
					$oOrderStatus = $_QuantityPurchased==1 ? 687 : 688;
					$oAmountPaid = 9999;
					$oPaidTime = $oCreateTime;
					$is_allow_spide_itemid = true;
					$buyAddress = $api_gco->getSellerTransactions($oSellerOrderID);
					$oName 				= str_rep($buyAddress->getElementsByTagName('Name')->item(0)->nodeValue);
					$oName				= mysql_real_escape_string($oName);
					$oStreet1 			= str_rep($buyAddress->getElementsByTagName('Street1')->item(0)->nodeValue);
					$oCityName 			= str_rep($buyAddress->getElementsByTagName('CityName')->item(0)->nodeValue);
					$oStateOrProvince 	= str_rep($buyAddress->getElementsByTagName('StateOrProvince')->item(0)->nodeValue);
					$oCountry 			= str_rep($buyAddress->getElementsByTagName('Country')->item(0)->nodeValue);
					$oCountryName 		= str_rep($buyAddress->getElementsByTagName('CountryName')->item(0)->nodeValue);
					$oPostalCode 		= str_rep($buyAddress->getElementsByTagName('PostalCode')->item(0)->nodeValue);
					$oPhone 			= $buyAddress->getElementsByTagName('Phone')->item(0)->nodeValue;
				}
			}
			if($oShippedTime >0) $oOrderStatus	= 2;//已经发货
			$oRefundAmount	= 0; //表示未垦退款
			if(	($oOrderStatus == 1 && $oShippedTime <=0 && $oPaidTime >0) || ($oOrderStatus ==4 && $oShippedTime <=0 ) || ( in_array($oOrderStatus, array(687,688)) && $oShippedTime <=0 && $is_allow_spide_itemid == true)){
				echo "eBay订单号[$oSellerOrderID]有效 ,订单类型[{$oOrderStatus}] ";
				$check_ebayorderid = true;
				//检查汇总表该 eBay 订单号是否已经存在
				//echo "===={$oSellerOrderID}==={$ebay_account}====";//检测重复抓单信息
				$check_ebayorderid = CheckeBayOrderIDExists($oSellerOrderID,$ebay_account);
				$new_ebay_id=true;
				if($check_ebayorderid === false){//添加订单汇总
					/* 生成一个本地系统订单号 */
					$our_sys_ordersn=date('Y-m-d-His').mt_rand(100,999).$oRecordNumber;
					$oorder_no		= '';//已废弃
					
					$obj_order		= new eBayOrder();
					$obj_order_data=array('ebay_paystatus'=>$oCompleteStatus,
										  'ebay_ordersn'=>$our_sys_ordersn,
										  'ebay_tid'=>$oTid,//此处已没有交易号概念
										  'ebay_ptid'=>$oPtid,
										  'ebay_orderid'=>$oSellerOrderID,
										  'ebay_createdtime'=>$oCreateTime,
										  'ebay_paidtime'=>$oPaidTime,
										  'ebay_userid'=>$oUserID,
										  'ebay_username'=>$oName,
										  'ebay_usermail'=>$oEmail,
										  'ebay_street'=>$oStreet1,
										  'ebay_street1'=>$oStreet2,
										  'ebay_city'=>$oCityName,
										  'ebay_state'=>$oStateOrProvince,
										  'ebay_couny'=>$oCountry,
										  'ebay_countryname'=>$oCountryName,
										  'ebay_postcode'=>$oPostalCode,
										  'ebay_phone'=>$oPhone,
										  'ebay_currency'=>$oCurrency,
										  'ebay_total'=>$oAmountPaid,
										  'ebay_status'=>$oOrderStatus,
										  'ebay_user'=>$user,
										  'ebay_shipfee'=>$oShipingFee,
										  'ebay_account'=>$ebay_account,
										  'recordnumber'=>$oRecordNumber,
										  'ebay_addtime'=>$mctime,
										  'ebay_note'=>$oBuyerCheckoutMessage,
										  'ebay_site'=>$oSite,
										  'eBayPaymentStatus'=>$oeBayPaymentStatus,
										  'PayPalEmailAddress'=>$oPayPalEmailAddress,
										  'ShippedTime'=>$oShippedTime,
										  'RefundAmount'=>$oRefundAmount,
										  'ebay_warehouse'=>$defaultstoreid,
										  'order_no'=>$oorder_no
										  );
					$obj_order->init($obj_order_data);
					$obj_order_data=null;
					unset($obj_order_data);
					//避免有时候mysql繁忙 造成插入数据失败 最多重复插入3次
					$new_ebay_into_db_count=0;
					while(1){
						$new_ebay_into_db_count++;
						
						$new_ebay_id=$oa->addOrder($obj_order);
						
						if($new_ebay_id!==false){
							echo "本地订单号[$our_sys_ordersn] 汇总数据入库成功=>\n";
							pop_ebay_orderid_queue($oSellerOrderID,$ebay_account);
							break;
						}else{
							echo "\teBay订单号[$oSellerOrderID] 入库失败\n";
							echo "Sleep 10 seconds then try to insert data again\n";
							sleep(10);
							
							if($new_ebay_into_db_count==3){
								echo "Reach the max limit,Skip this order[$oSellerOrderID]\n";
								break;
							}							
						}
					}
					$obj_order=null;
					unset($obj_order);
					
					if($new_ebay_id!==false){						
						echo "\tUserID:$oUserID"." AMT:$oAmountPaid recordNO:$oRecordNumber 付款状态:$oCompleteStatus 付款时间:".date('Y-m-d H:i:s',$oPaidTime)."\n";
						//检验ebay 订单号 是否在订单号汇总表中						
						if(check_ebay_orderid_exists_in_statistic_table($oSellerOrderID,$ebay_account)===false){
						save_ebay_orderid_table($new_ebay_id,$oPtid,$oSellerOrderID,$ebay_account,$oCreateTime);
						}
						//添加订单明细
						foreach($osoTransArray	as $transaction){
							
							//该交易的销售编号
							$tran_recordnumber	= $transaction->getElementsByTagName('ShippingDetails')->item(0)->getElementsByTagName('SellingManagerSalesRecordNumber')->item(0)->nodeValue;
							$sql = "select 	ebay_id from ebay_orderdetail 
									where 	ebay_ordersn='$our_sys_ordersn' 
									and 	recordnumber='$tran_recordnumber'";
							$sql = $dbcon->execute($sql);
							$sql = $dbcon->getResultArray($sql);
							if(count($sql)>0){
								continue;
							}
							unset($sql);
							/* 多属性订单 */
							$attribute	= '';
							$buy_with_attr=false;
							$tran_varia=$transaction->getElementsByTagName('Variation')->item(0);
							if(is_object($tran_varia)){//未添加明细的属性 20130301
								if(	$tran_varia->hasChildNodes() ){
									$Variation	= $tran_varia->getElementsByTagName('NameValueList')->item(0);
									if( !empty($Variation) && $Variation->hasChildNodes() ){
										foreach($Variation as $variate){
											$aname	= $variate->getElementsByTagName('Name')->item(0)->nodeValue;
											$avalue	= $variate->getElementsByTagName('Value')->item(0)->nodeValue;
											$attribute	.= $aname.":".$avalue." ";
										}
									}/*else{
										$attribute	= $Variation['Name'].":".$Variation['Value'];
									}*/
									$buy_with_attr=true;
									$Variation=null;unset($Variation);
								}
							}
							$tran_id			= $transaction->getElementsByTagName('TransactionID')->item(0)->nodeValue;
							//该交易的物品信息
							$odItem 			= $transaction->getElementsByTagName('Item')->item(0);
							if($buy_with_attr===true){
								$odItemTitle 	= @$tran_varia->getElementsByTagName('VariationTitle')->item(0)->nodeValue;	
								$odSKU			= @$tran_varia->getElementsByTagName('SKU')->item(0)->nodeValue;
							}else{
								$odItemTitle	=str_rep($odItem->getElementsByTagName('Title')->item(0)->nodeValue);
								$odSKU			=str_rep($odItem->getElementsByTagName('SKU')->item(0)->nodeValue);
							}
							$is_suffix = 0;
							if (!empty($account_suffix)){
								list($truesku, $skusuffix) = explode(':', $odSKU);
								if (!empty($skusuffix)/*&&strpos($account_suffix, $skusuffix)!==false*/){
									$odSKU = $truesku;
									$is_suffix = 1;
								}
							}
							###########悲剧 目前getorder api 无法取得下面2个值#########
							//ebay刊登物品的分类ID,备用字段
							//$CategoryID 		= @$odItem->getElementsByTagName('PrimaryCategory')->item(0)->getElementsByTagName('CategoryID')->item(0)->nodeValue;
							//$ListingType 		= @$odItem->getElementsByTagName('ListingType')->item(0)->nodeValue;
							$CategoryID			=0;
							$ListingType		='';	
							//购买数量
							$QuantityPurchased=$transaction->getElementsByTagName('QuantityPurchased')->item(0)->nodeValue;
							//交易创建时间
							$CreatedDate = strtotime($transaction->getElementsByTagName('CreatedDate')->item(0)->nodeValue);
							$FinalValueFee	= $transaction->getElementsByTagName('FinalValueFee')->item(0)->nodeValue;
							$tran_price		= $transaction->getElementsByTagName('TransactionPrice')->item(0)->nodeValue;
							$goodsshippingcost = $transaction->getElementsByTagName('ActualShippingCost')->item(0)->nodeValue;
							$goodsshippingcost = empty($goodsshippingcost) ? '0.0' : $goodsshippingcost;
							$tran_itemid	= $odItem->getElementsByTagName('ItemID')->item(0)->nodeValue;
							$tran_site		= $odItem->getElementsByTagName('Site')->item(0)->nodeValue;
							$obj_order_detail	=new eBayOrderDetail();
							$obj_order_detail_data=array('ebay_ordersn'=>$our_sys_ordersn,
														 'ebay_itemid'=>$tran_itemid,
														 'ebay_itemtitle'=>$odItemTitle,
														 'ebay_itemprice'=>$tran_price,
														 'ebay_amount'=>$QuantityPurchased,
														 'ebay_createdtime'=>$CreatedDate,
														 'ebay_shiptype'=>$oShipingService,
														 'ebay_user'=>$user,
														 'sku'=>$odSKU,
														 'shipingfee'=>$goodsshippingcost,
														 'ebay_account'=>$ebay_account,
														 'addtime'=>$mctime,
														 'ebay_itemurl'=>'',
														 'ebay_site'=>@$tran_site,
														 'recordnumber'=>$tran_recordnumber,
														 'storeid'=>'',
														 'ListingType'=>$ListingType,
														 'ebay_tid'=>$tran_id,
														 'FeeOrCreditAmount'=>$oFeeOrCreditAmount,
														 'FinalValueFee'=>$FinalValueFee,
														 'attribute'=>$attribute,
														 'notes'=>$oBuyerCheckoutMessage,
														 'goods_location'=>@get_good_location($odSKU,$user),
														 'is_suffix'=>$is_suffix
														 );
							$obj_order_detail->init($obj_order_detail_data);
							$obj_order_detail_data=null;
							unset($obj_order_detail_data);
							if(	false!==($oa->addOrderDetail($obj_order_detail)) ){
								echo "\t销售编号[$tran_recordnumber]明细入库OK!\n";
							}else{
								echo "\t销售编号[$tran_recordnumber]明细入库Error!\n";
							}
							$obj_order_detail=null;
							unset($obj_order_detail);
							
						}
					}else{
						echo "\t本地订单号[$our_sys_ordersn] 入库失败\n";
					}
				}
			}else{
				echo "eBay订单号[$oSellerOrderID] 记录编号[$oRecordNumber] 无效 不入库...\t";
				if($oShippedTime>0 || $oOrderStatus==2){
					echo "已经发货\t";
				}
				if($oPaidTime<=0 || $oPaidTime=='' || empty($oPaidTime) ){
					echo "未付款\t";
				}
				echo "\n";
				@pop_ebay_orderid_queue($oSellerOrderID,$ebay_account);
			}
		}			
	}
	//订单ID入队列函数
	function __handle_ebay_orderidxml(&$SellerOrderArray,$ebay_account){
		global $api_gco,$oa,$user,$dbcon;
		
		foreach( $SellerOrderArray as $SellerOrder){
			//每个订单号
			$oSellerOrderID		= $SellerOrder->getElementsByTagName('OrderID')->item(0)->nodeValue;
			//订单状态及付款状态
			$CheckoutStatus		= $SellerOrder->getElementsByTagName('CheckoutStatus')->item(0);			
			$oeBayPaymentStatus = $CheckoutStatus->getElementsByTagName('eBayPaymentStatus')->item(0)->nodeValue;
			$oCompleteStatus 	= $CheckoutStatus->getElementsByTagName('Status')->item(0)->nodeValue;	
			//付款时间			
			$oPaidTime 			= strtotime($SellerOrder->getElementsByTagName('PaidTime')->item(0)->nodeValue);
			$oShippedTime    	= strtotime($SellerOrder->getElementsByTagName('ShippedTime')->item(0)->nodeValue);
			echo $oCompleteStatus."--".$oeBayPaymentStatus."--".$oPaidTime; echo "\n";
			if($oCompleteStatus == "Complete" && $oeBayPaymentStatus == "NoPaymentFailure" && $oPaidTime > 0){
				$oOrderStatus	= 1;
			}
			if(($oPaidTime<=0 || $oPaidTime=='' || empty($oPaidTime)) && $oShippedTime <=0){
				echo "未付款,但是属于抓取范围\t";
				$oOrderStatus	= 687;
			}
			if($oShippedTime >0) $oOrderStatus	= 2;//已经发货
			if(	($oOrderStatus == 1 && $oShippedTime <=0 && $oPaidTime >0)  || ($oOrderStatus ==687 && $oShippedTime <=0 )){
				echo "eBay订单号[$oSellerOrderID]有效 入队列---->\n";
				//把订单号放入对于ebay账号队列
				push_ebay_orderid_queue($oSellerOrderID,$ebay_account);
				
			}else{
				echo "eBay订单号[$oSellerOrderID]无效 不入队列\t";
				if($oShippedTime>0 || $oOrderStatus==2){
					echo "已经发货\t";
				}else if($oPaidTime<=0 || $oPaidTime=='' || empty($oPaidTime) ){
					echo "未付款\t";
				}
				echo "\n";
			}
		}
	}
	//把各账号的ebay 订单号放到汇总表
	function save_ebay_orderid_table($ebay_id,$ebay_ptid,$ebay_orderid,$ebay_account,$ebay_createtime){
		global $dbcon;
		$ebay_orderid_statistic_sql='insert into ebay_order_ids
										(ebay_id,ebay_ptid,ebay_orderid,ebay_account,ebay_saletime)
									 value('.$ebay_id.',"'.$ebay_ptid.'","'.$ebay_orderid.'",
										 "'.$ebay_account.'",'.$ebay_createtime.')';
		$dbcon->execute($ebay_orderid_statistic_sql);
	}
	//检验ebay orderid 是否存在 orderid汇总表【此表目前也用于漏单检验】
	function check_ebay_orderid_exists_in_statistic_table($ebay_orderid,$ebay_account){
		global $dbcon;
		$check_order_id_sql='select ebay_id from ebay_order_ids 
							 where 	ebay_orderid="'.$ebay_orderid.'" 
							 and	ebay_account="'.$ebay_account.'"';
		$check_order_id=$dbcon->execute($check_order_id_sql);
		$check_order_id=$dbcon->getResultArray($check_order_id);
		if(count($check_order_id)==0){
			return false;
		}else{
			return true;
		}
	}
	//把ebay订单号放到各自账号队列表
	function push_ebay_orderid_queue($ebay_orderid,$ebay_account){
		global $dbcon;
		//step 1 check ebay orderid statistic table
		if( check_ebay_orderid_exists_in_statistic_table($ebay_orderid,$ebay_account) === true){
			echo "ebay orderid[$ebay_orderid] already exists in ebay orderid statistic table\n";
			return ;
		}
		
		$table_name='ebay_order_id_queue_'.$ebay_account;
		
		$check_sql='select * from '.$table_name.' where ebay_orderid="'.$ebay_orderid.'"';
		$check=$dbcon->execute($check_sql);
		$check=$dbcon->getResultArray($check);
		if(count($check)==0){
			$sql='insert into '.$table_name.' (ebay_orderid) value("'.$ebay_orderid.'")';
			
			$try_insert_count=0;
			while(1){
				$try_insert_count++;
				$res=$dbcon->execute($sql);
				if($res){
					echo "Push ebay orderid[$ebay_orderid]  into queue table successfully!\n";
					break;
				}else{
					if($try_insert_count==3){
						$lost_orderid_path = EBAY_RAW_DATA_PATH.'lost_ebay_orderid/'.$ebay_account.'/lost_sql.txt';
						write_lost_sql($lost_orderid_path, $sql."\n");
						echo "oops...failed again,give this order[$ebay_orderid] up finally!\n";
						break;
					}
					echo "fail to push  ebay orderid[$ebay_orderid]  into queue table !Sleep 10 sconds then try again\n";
					sleep(10);
				}
			}
		}else{
			echo "ebay orderid[$ebay_orderid] already exists in queue\n";
		}
	}
	//把ebay订单号踢出各自账号队列表
	function pop_ebay_orderid_queue($ebay_orderid,$ebay_account){
		global $dbcon;
		$table_name='ebay_order_id_queue_'.$ebay_account;
		$sql='delete from '.$table_name.' where ebay_orderid="'.$ebay_orderid.'"';
		
		$try_insert_count=0;
		while(1){
			$try_insert_count++;
			$res=$dbcon->execute($sql);
			if($res!==false){
				echo "kick ebay orderid[$ebay_orderid]  out  of queue table successfully!\n";
				break;
			}else{
				if($try_insert_count==3){
					echo "oops...failed again,give this order[$ebay_orderid] up finally!\n";
					break;
				}
				echo "fail to kick ebay orderid[$ebay_orderid]  out  of queue table !Sleep 10 sconds then try again\n";
				sleep(10);
			}
		}
	}
	//标记发货函数
	function just_mark_order_shipped($ebay_orderid,$ebay_ordersn){
		global $api_cs,$user;
		global $dbcon,$mctime,$defaultstoreid;
		
		//获取订单明细
		$order_detail_sql='SELECT 	ebay_itemid,sku,ebay_tid	FROM ebay_orderdetail 
						   WHERE	ebay_ordersn="'.$ebay_ordersn.'" ';
		
		$order_detail	= $dbcon->execute($order_detail_sql);
		$order_detail	= $dbcon->getResultArray($order_detail);
		
		foreach($order_detail as $od){
			$tran_data=array();
			$tran_data['itemid']	=$od['ebay_itemid'];
			$tran_data['tid']		=$od['ebay_tid'];
			$tran_data['orderid']	=$ebay_orderid;
			
			echo "itemid:".$od['ebay_itemid']."\t tid:".$od['ebay_tid']."\t sku:".$od['sku']."\n";			
			
			$mark_res=$api_cs->just_mark_order_shipped($tran_data);
			
			$responseDoc = new DomDocument();	
			$responseDoc->loadXML($mark_res);
			
			$Ack	 	= $responseDoc->getElementsByTagName('Ack')->item(0)->nodeValue;
			
			if($Ack == "Success"){						
				echo "  已标记发出\n";
				return true;
				$dbcon->execute($sb);
			}else{
				return false;
				echo "  标记发出失败 ACK=$Ack \n";
			}
		}
	}
	//更新发货信息(trackno)到ebay
	function update_order_shippingdetail_to_ebay($ebay_orderid,$ebay_ordersn,$ebay_tracknumber,$ebay_carrier){
		global $api_cs,$user;
		global $dbcon,$mctime,$defaultstoreid;
		//获取订单明细
		$order_detail_sql='SELECT 	ebay_itemid,sku,ebay_amount,ebay_tid	FROM ebay_orderdetail 
						   WHERE	ebay_ordersn="'.$ebay_ordersn.'" ';
		
		$order_detail	= $dbcon->execute($order_detail_sql);
		$order_detail	= $dbcon->getResultArray($order_detail);
		
		foreach($order_detail as $od){
			$tran_data=array();
			$tran_data['itemid']				=$od['ebay_itemid'];
			$tran_data['tid']					=$od['ebay_tid'];
			$tran_data['orderid']				=$ebay_orderid;
			$tran_data['ebay_carrier']			=$ebay_carrier;
			$tran_data['ebay_tracknumber']		=$ebay_tracknumber;
			
			echo "itemid:".$od['ebay_itemid']."\t tid:".$od['ebay_tid']."\t sku:".$od['sku']."\n";
			echo "carrier:".$ebay_carrier."\t trackno:".$ebay_tracknumber."\n";
			
			$mark_res=$api_cs->update_order_shippingdetail_to_ebay($tran_data);
			
			$responseDoc = new DomDocument();	
			$responseDoc->loadXML($mark_res);
			
			$Ack	 	= $responseDoc->getElementsByTagName('Ack')->item(0)->nodeValue;
			
			if($Ack == "Success"){						
				echo "  更新shippingdetail成功\n";
				$sb		= " update ebay_order set ebay_markettime='$mctime',ShippedTime='$mctime' 
							where ebay_ordersn='$ebay_ordersn'";
				$dbcon->execute($sb);
				return true;
			}else{
				echo "  更新shippingdetail失败 ACK=$Ack \n";
				return false;
			}
		}
	}
	//更新发货信息订单编号到ebay
	function update_ebayid_shippingdetail_to_ebay($ebay_orderid,$ebay_ordersn,$ebay_tracknumber,$ebay_carrier){
		global $api_cs,$user;
		global $dbcon,$mctime,$defaultstoreid;
		//获取订单明细
		$order_detail_sql='SELECT 	ebay_itemid,sku,ebay_amount,ebay_tid	FROM ebay_orderdetail 
						   WHERE	ebay_ordersn="'.$ebay_ordersn.'" ';
		
		$order_detail	= $dbcon->execute($order_detail_sql);
		$order_detail	= $dbcon->getResultArray($order_detail);
		
		foreach($order_detail as $od){
			$tran_data=array();
			$tran_data['itemid']				=$od['ebay_itemid'];
			$tran_data['tid']					=$od['ebay_tid'];
			$tran_data['orderid']				=$ebay_orderid;
			$tran_data['ebay_carrier']			=$ebay_carrier;
			$tran_data['ebay_tracknumber']		=$ebay_tracknumber;
			
			echo "itemid:".$od['ebay_itemid']."\t tid:".$od['ebay_tid']."\t sku:".$od['sku']."\n";
			echo "carrier:".$ebay_carrier."\t trackno:".$ebay_tracknumber."\n";
			
			$mark_res=$api_cs->update_order_shippingdetail_to_ebay($tran_data);
			
			$responseDoc = new DomDocument();	
			$responseDoc->loadXML($mark_res);
			
			$Ack	 	= $responseDoc->getElementsByTagName('Ack')->item(0)->nodeValue;
			
			if($Ack == "Success"){						
				echo "  更新shippingdetail成功\n";
				return true;
			}else{
				echo "  更新shippingdetail失败 ACK=$Ack \n";
				return false;
			}
		}
	}
	function save_ebay_raw_data($fname,$raw_data){
		$tmp_dir=dirname($fname);
		if(!is_dir($tmp_dir)){
			mkdirs($tmp_dir);
		}
		
		$f=@fopen($fname,'w');
		$fsize=mb_strlen($raw_data);
		$res=@fwrite($f,$raw_data,$fsize);
		@fclose($f);
		return $res;
	}
	function mkdirs($path){
		$path_out=preg_replace('/[^\/.]+\/?$/','',$path);
		if(!is_dir($path_out)){			
			mkdirs($path_out);
		}
		mkdir($path);
	}
	function sql_str2array($content){
		$result = array();
		$array = explode('<br />', nl2br($content));
		foreach($array AS $_v){
			if(preg_match("/(insert|update|replace|delete|select)/i", $_v)){
				array_push($result, $_v);
			}
		}
		return $result;
	}
	function write_lost_sql($file, $data){
		$tmp_dir = dirname($file);
		if(!is_dir($tmp_dir)){
			mkdirs($tmp_dir);
		}
		if (!$handle=fopen($file, 'a')) {
			 return false;
		}
		if(flock($handle, LOCK_EX)) { 
			if (fwrite($handle, $data) === FALSE) {
				return false;
			}
			flock($handle, LOCK_UN);
		}
		fclose($handle);
		return true;
	}
	function read_lost_sql($file){
		if(!is_file($file)){
			return false;
		}
		return file_get_contents($file);;
	}

	function write_a_file($file, $data){
		$tmp_dir = dirname($file);
		if(!is_dir($tmp_dir)){
			mkdirs($tmp_dir);
		}
		if (!$handle=fopen($file, 'a')) {
			 return false;
		}
		if(flock($handle, LOCK_EX)) { 
			if (fwrite($handle, $data) === FALSE) {
				return false;
			}
			flock($handle, LOCK_UN);
		}
		fclose($handle);
		return true;
	}

	function write_w_file($file, $data){
		$tmp_dir = dirname($file);
		if(!is_dir($tmp_dir)){
			mkdirs($tmp_dir);
		}
		if (!$handle=fopen($file, 'w')) {
			 return false;
		}
		if(flock($handle, LOCK_EX)) { 
			if (fwrite($handle, $data) === FALSE) {
				return false;
			}
			flock($handle, LOCK_UN);
		}
		fclose($handle);
		return true;
	}
	
	function read_and_empty_lost_sql($file){
		if(!is_file($file)){
			return false;
		}
		$contents =  file_get_contents($file);
		if (!$handle=fopen($file, 'w')) {
			 return false;
		}
		return $contents;
	}
	function calcglobalmail_backup2($totalweight,$countryname){
	
		global $dbcon;
		
		include WEB_PATH.'/cache/shipfeee/globalmail.php';
		
		$cnum = '';
	
		foreach ($GLOBALMAIL_CONTRY_LIST AS $c=>$country){
			if ($countryname==$country){
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
	function calcglobalmail($totalweight,$countryname){
		global $dbcon;
		//add by heminghua @ 20130325
		if($totalweight<=0)
		{
		 return false;
		}else
		{
		 $ss="select * from ebay_globalmail where country = '{$countryname}'";
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
		   $shipfee *= $totalweight;
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
	function calcfedex($totalweight,$countryname,$orderid){
	
		global $dbcon;
		
		include WEB_PATH.'cache/shipfeee/fedex_1.php';
		
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
			if ($start_w<$totalweight&&$totalweight<$end_w){
				$wnum = $w;
				break;
			}
		}
		echo "国家区间({$cnum})----重量区间({$wnum})------价格({$FEDEX_PRICE_LIST_1[$wnum][$cnum]})----总价".$FEDEX_PRICE_LIST_1[$wnum][$cnum]*(1+$FEDEX_MYC_FEE_1);
		$shipfee = $totalweight>20.5 ? $totalweight*$FEDEX_PRICE_LIST_1[$wnum][$cnum]*(1+$FEDEX_MYC_FEE_1) : $FEDEX_PRICE_LIST_1[$wnum][$cnum]*(1+$FEDEX_MYC_FEE_1);
		return round($shipfee, 2);
		
	}
	
	function calcfedexyx($totalweight,$countryname,$postcode=0){
		
		include WEB_PATH.'cache/shipfeee/fedex_2.php';
		
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
					$cnum = $c+1;
				}
				break;
			}
		}
		if ($cnum===''){
			return 0;
		}
		foreach ($FEDEX_WEIGHT_LIST_2 AS $w=>$weight){
			list($start_w, $end_w) = explode('-', $weight);
			if ($start_w<$totalweight&&$totalweight<$end_w){
				$wnum = $w;
				break;
			}
		}
		
		return round($FEDEX_PRICE_LIST_2[$wnum][$cnum]*(1+$FEDEX_MYC_FEE_2), 2);
		//return 0;
	}
	/*function array2sql($array){
		$sql_array = array();
		foreach ($array AS $_k=>$_v){
			if (empty($_k)){
				continue;
			}
			$_v = trim($_v);
			if (is_numeric($_v)&&preg_match("/^[1-9][0-9]+$/", $_v)){
				$sql_array[] = "{$_k}={$_v}";
			}else{
				$sql_array[] = "{$_k}='{$_v}'";
			}
		}
		return implode(',', $sql_array);
	}*/
	function array2sql($array){
		$sql_array = array();
		foreach ($array AS $_k=>$_v){
			if (empty($_k)){
				continue;
			}
			$_v = trim($_v);
			//if (is_numeric($_v)&&preg_match("/^[1-9][0-9]+$/", $_v)){
			if (ctype_digit($_v)&&preg_match("/^[1-9][0-9]+$/", $_v)){ //modified by Herman.Xi is_numeric 对十六进制数判断不了 举例:0X792496944666339
				$sql_array[] = "{$_k}={$_v}";
			}else{
				$_v = Deal_SC($_v);
				$sql_array[] = "{$_k}='{$_v}'";
			}
		}
		return implode(',', $sql_array);
	}
	function Deal_SC($str){
		//处理特殊字符,add by Herman.Xi @ 20130307
		$str  = str_replace("'","&acute;",$str);
		$str  = str_replace("\"","&quot;",$str);
		$tes = array("=" , ")" , "(" , "{", "}");
		foreach($tes as $v){
			$str = str_replace($v,"",$str);
		}
		return addslashes($str);
	}
	function get_columns($table){
		
		global $dbcon;
		
		$sql = "SHOW COLUMNS FROM {$table}";
		$sql = $dbcon->execute($sql);
		$columns = $dbcon->getResultArray($sql);
		$fields = array(); 
		foreach ($columns AS $column){
			array_push($fields, $column['Field']);
		}
		return $fields;
	}
	//Message同步日志 #####wangminwei add by 2012-09-05#####
	function addlogsmessage($log_name,$log_operationtime,$log_orderid,$log_notes,$tname,$log_ebay_account,$start,$end,$type){
		global $dbcon;
		$nowtime=date("Y-m-d H:i:s");
		$ss		= "insert into system_logmessage(log_name,log_operationtime,log_orderid,log_notes,ebay_user,currentime,
					log_ebay_account,starttime,endtime,type) 
					values('$log_name','$log_operationtime','$log_orderid','$log_notes','$tname','$nowtime',
					'$log_ebay_account','$start','$end','$type')";
		$dbcon->execute($ss);
	}
	//Message加载函数 #####wangminwei add by 2012-09-05#####
	function GetMemberMessages($start,$end,$account,$type){
		global $dbcon,$api_messages,$user;
		$patch = '/home/html_include/ebay_message_body/';
		$pcount	= '1';
		while(true)
		{
			$responseXml=$api_messages->request($start,$end,$pcount,$account);
			if(stristr($responseXml, 'HTTP 404') || $responseXml == '') return 'id not found';
			$responseDoc = new DomDocument();
    		$responseDoc->loadXML($responseXml);
			$data	= XML_unserialize($responseXml);
			$Ack	= $data['GetMyMessagesResponse']['Ack'];
			
			if($Ack == '' || $Ack != 'Success' )
			{
				$ss			= "insert into errors_ackmessage(ebay_account,starttime,endtime,status,notes) values('$account','$start','$end','0','Ack False')";
				$dbcon->execute($ss);
			}
			echo $account.'  '.$Ack."\n";
			$log_name	 		= '同步message';
			$log_operationtime  = $mctime;
			$log_notes	 	    = '开始同步eBay帐号'.$account.'程序正常同步,加载到第'.$ic.'页'.$strline;
			$tname			    = $user;
			addlogsmessage($log_name,$log_operationtime,$log_orderid,$log_notes,$tname,$account,$start,$end,$type);
			
			$Trans = $data['GetMyMessagesResponse']['Messages']['Message'];
			$Sender	= $data['GetMyMessagesResponse']['Messages']['Message']['Sender'];
			if($Sender != '' ) 
			{
				$Trans		= array();
				$Trans[0] = $data['GetMyMessagesResponse']['Messages']['Message'];  
			}
			foreach((array)$Trans as $Transaction)
			{
				$Read					= $Transaction['Read']?1:0;
				$HighPriority			= $Transaction['HighPriority'];
				$Sender					= $Transaction['Sender'];
				$MessageID				= $Transaction['MessageID'];
				$RecipientUserID		= $Transaction['RecipientUserID'];
				$Subject				= str_rep($Transaction['Subject']);
				$MessageType			= $Transaction['MessageType'];
				$Replied				= $Transaction['Replied'];
				$ItemID					= $Transaction['ItemID'];
				$ExternalMessageID		= $Transaction['ExternalMessageID']; // 之前的id
				$ReceiveDate			= $Transaction['ReceiveDate'];
				$ItemTitle				= str_rep($Transaction['ItemTitle']);
				$createtime1			= strtotime($ReceiveDate);
				$date                   = date('Y-m-d',strtotime("$ReceiveDate + 8 hours"));
				$ss						= "select id from ebay_message where message_id='$MessageID' and ebay_account='$account'";
				$ss						= $dbcon->execute($ss);
				$ss						= $dbcon->getResultArray($ss);
				if(count($ss) == 0 )
				{
					if($Replied == 'false' )
					{
						$responseXml=$api_messages->requestMessagesID($MessageID);
						$www = $responseXml;
						if(stristr($responseXml, 'HTTP 404') || $responseXml == '') return 'id not found';
						$responseDoc = new DomDocument();
						$responseDoc->loadXML($responseXml);
						$data   	= XML_unserialize($responseXml);
						//$Body	    = mysql_real_escape_string($data['GetMyMessagesResponse']['Messages']['Message']['Text']);
						$Content	= $data['GetMyMessagesResponse']['Messages']['Message']['Text'];
						$status		= 0;
						$forms		= 0;
						$classid	= '0';
						$case_sendid = '';
						$disputeid  = '';
						if($Sender == 'eBay' || preg_match("/^(eBay[\w]*@[\w]*.com)|([\w]*@eBay.com[.]?[\w]*)|(eBay CS Support)$/",$Sender))
						{
							$forms = 2;
							
							$str_content = strip_tags($Content);//去除HTML、PHP标签
							$posA = strpos($Subject,'Case #');
							$posC = strpos($Subject,'個案編號');
							$posD = strpos($Subject,"有已成立的個案");
							if($posA===false){//标题不存在Case字眼	
								
							}else{//标题存在Case字眼
								$ppa       = strpos($Subject,'#');
								$disputeid = trim(substr($Subject,$ppa+2,10));//取纠纷ID
								$pposA = strpos($str_content,'Buyer:');
								$str_spilt = substr($str_content,$pposA,100);//截取Buyer:后10位字符
								$strlen    = strpos($str_spilt,'Case');
								$first     = substr($str_spilt,6,1);//截取买家ID首个字符
								$case_sendid = substr($str_spilt,6,$strlen-6);//买家ID
								$classid   = '415';
							}
							if($posC===false){
								
							}else{
								$disputeid = trim(substr($Subject,13,11));//取纠纷ID
								$pposC     = strpos($str_content,'買家:');
								$str_spilt = substr($str_content,$pposC,100);//截取Buyer:后50位字符
								$strlen    = strpos($str_spilt,'個案編號');
								$first     = substr($str_spilt,9,1);//截取买家ID首个字符
								$case_sendid = substr($str_spilt,9,$strlen-9);
								$classid   = '415';
							}
							if($posD===false){
								
							}else{
								$ppd       = strpos($Subject,'參考編號');
								$disputeid = trim(substr($Subject,$ppd+13,11));//取纠纷ID
								$pposD     = strpos($str_content,'買家:');
								$str_spilt = substr($str_content,$pposD,100);//截取Buyer:后50位字符//截取买家ID首个字符
								$strlen    = strpos($str_spilt,'個案編號');
								$first     = substr($str_spilt,9,1);
								$case_sendid = substr($str_spilt,9,$strlen-9);
								$classid   = '415';
							}
						}
						if($HighPriority == 'true')
						{
							$forms	= 3;
						}
						if($Sender != 'eBay' && !preg_match("/^(eBay[\w]*@[\w]*.com)|([\w]*@eBay.com[.]?[\w]*)|(eBay CS Support)$/",$Sender))
						{
							$first		= substr($Sender,0,1);
							$ss			= "select id from ebay_messagecategory where rules like '%$first%' and ebay_account ='$account' and ebay_user ='$user'";
							$ss			= $dbcon->execute($ss);
							$ss			= $dbcon->getResultArray($ss);
							if(count($ss) > 0)
							{
								$classid		= $ss[0]['id'];
							}
						}
						$sql	 = "INSERT INTO `ebay_message` (`message_id` , `message_type` , `question_type` , `recipientid` ";
						$sql	.= ", `sendmail` , `sendid` , `subject` , `itemid` , `starttime` , `endtime` , `currentprice` , ";
						$sql	.= "`title` , `createtime` , `ebay_user` , `add_time` , `itemurl` , `ebay_account`,`classid`,`createtime1`,`status`,`forms`,`Read`,`ExternalMessageID`,`case_sendid`,`disputeid`)VALUES ('$MessageID', '$MessageType' ,";
						$sql    .= " '$QuestionType' , '$RecipientUserID' , '$SenderEmail' , '$Sender' , '$Subject' , '$ItemID' , ";
						$sql	.= "'$starttime' , '$endtime' , '$price' , '$ItemTitle' , '$ReceiveDate' ,'$user' , '$mctime','$ViewItemURL','$account','$classid','$createtime1','$status','$forms','$Read','$ExternalMessageID','$case_sendid','$disputeid') ";
						if($dbcon->execute($sql))
						{
							echo "$MessageID Add Success"."\n";
							if(write_a_file($patch.$account.'/'.$date.'/'.$MessageID.'.html',$Content)===false)
							{
								write_a_file('/home/john/temp/www/'.$account.'/messageid.txt',$MessageID."\n");
							}
						}
						else
						{
							echo "$MessageID Add Failure"."\n";
						}
					
					}	
				}
				else
				{
					echo $MessageID.'exist'."\n";
				}
			}
		
			if(count($Trans)< 50)
			{
				break;
			}
			if($pcount >= 8) 
			{
				echo 'exit';
				break;
			}
			$pcount++;
		}
	}
	/****add by wmw 2013.04.03****/
	function getFirstStr($first,$account,$user){
		global  $dbcon;
		$sql	= "select id from ebay_messagecategory where rules like '%$first%' and ebay_account ='$account' and ebay_user ='$user'";
		$sql	= $dbcon->execute($sql);
		$sql	= $dbcon->getResultArray($sql);
		if(count($sql) > 0){
			$classid = $sql[0]['id'];
		}else{
			$classid = 0;
		}
		return $classid;
	}
	function get_account_suffix($ebay_account){
		
		global $dbcon;
		
		$sql = "SELECT account_suffix FROM ebay_account WHERE ebay_account='{$ebay_account}'";
		$sql = $dbcon->execute($sql);
		$result = $dbcon->getResultArray($sql);
		return isset($result[0]['account_suffix']) ? $result[0]['account_suffix'] : '';
	}
	function array2strarray($array){
		$results = array();
		foreach ($array AS $_k=>$_v){
			$results[$_k] = "'{$_v}'";
		}
		return $results;
	}
	function get_realskuinfo($sku){
	
		global $dbcon;
		
		$sql = "SELECT goods_sncombine FROM ebay_productscombine WHERE goods_sn ='{$sku}'";
		$sql = $dbcon->execute($sql);
		$combinelists = $dbcon->fetch_one($sql);
		
		if (empty($combinelists)){ //modified by Herman.Xi @ 2013-05-22
			$sku = get_conversion_sku($sku);
			return array($sku=>1);
		}
		$results = array();
		if (strpos($combinelists['goods_sncombine'], ',')!==false){
			$skulists = explode(',', $combinelists['goods_sncombine']);
			foreach ($skulists AS $skulist){
				list($_sku, $snum) = strpos($skulist, '*')!==false ? explode('*', $skulist) : array($skulist, 1);
				$_sku = get_conversion_sku($_sku);
				$results[trim($_sku)] = $snum;
			}
		}else if (strpos($combinelists['goods_sncombine'], '*')!==false){
			list($_sku, $snum) = explode('*', $combinelists['goods_sncombine']);
			$_sku = get_conversion_sku($_sku);
			$results[trim($_sku)] = $snum;
		}else{
			$sku = get_conversion_sku($sku);
			$results[trim($sku)] = 1;
		}
		return $results;
	}
	function get_conversion_sku($sku){
		/*add by Herman.Xi @ 2013-06-04
		新旧料号转换问题解决*/
		global $dbcon;
		$sql = "SELECT new_sku FROM purchase_sku_conversion WHERE old_sku ='{$sku}'";
		$sql = $dbcon->execute($sql);
		$conversion_sku = $dbcon->fetch_one($sql);
		if(empty($conversion_sku)){
			return	trim($sku);
		}
		return trim($conversion_sku['new_sku']);
	}
	function GetFeedback($account,$startpage,$endpage,$perPageCount)
	{
		global $dbcon,$api_feedback,$user;
		echo '同步feedback,开始于第'.$startpage.'页，结束于第'.$endpage.'页，每页同步'.$perPageCount.'条'."\n";
		$hasmore	= true;
		$status     = "";
		while(true)
		{
			echo '开始运行,第'.$startpage.'页'."\n";
			$responseXml = $api_feedback->request($startpage,$perPageCount);		
			if(stristr($responseXml, 'HTTP 404') || $responseXml == '') return 'id not found';
			$data=XML_unserialize($responseXml);
			$ack	= $data['GetFeedbackResponse']['Ack'];		 
			$TotalNumberOfPages		= $data['GetFeedbackResponse']['PaginationResult']['TotalNumberOfPages'];
			if($ack != "Success")
			{
				echo "<font color=red>评价加载失败</font>";
			}
			$feedback		= $data['GetFeedbackResponse']['FeedbackDetailArray']['FeedbackDetail'];
			foreach($feedback as $li)
			{
				$CommentingUser			= str_rep($li['CommentingUser']);
				$CommentingUserScore	= str_rep($li['CommentingUserScore']);
				$CommentText			= mysql_real_escape_string(str_rep($li['CommentText']));
				$CommentTime			= str_rep($li['CommentTime']);
				$feedbacktime 			= date('Y-m-d H:i:s',strtotime($CommentTime));
				$feedbacktime 			= date('Y-m-d H:i:s',strtotime("$feedbacktime - 900 minutes"));
				$feedbacktime 			= strtotime($feedbacktime);
				$CommentType			= str_rep($li['CommentType']);
				$ItemID					= str_rep($li['ItemID']);
				$FeedbackID				= str_rep($li['FeedbackID']);
				$TransactionID			= $li['TransactionID']?$li['TransactionID']:0;
				$ItemTitle				= str_rep($li['ItemTitle']);
				$currencyID				= str_rep($li['ItemPrice attr']['currencyID']);
				$ItemPrice				= str_rep($li['ItemPrice']);
			
				$ss			= "select a.ebay_ordersn as ebay_ordersn from ebay_order as a join ebay_orderdetail as b on a.ebay_ordersn=b.ebay_ordersn where a.ebay_userid='$CommentingUser' and b.ebay_itemid='$ItemID' and b.ebay_tid ='$TransactionID'";
				$ss			= $dbcon->execute($ss);
				$ss			= $dbcon->getResultArray($ss);
				$sorder		= $ss[0]['ebay_ordersn'];
				$ss			= "update ebay_orderdetail set ebay_feedback='$CommentType' where ebay_ordersn='$sorder' and ebay_itemid='$ItemID' and ebay_tid ='$TransactionID'";
				$dbcon->execute($ss);
				$sq		= "select id,CommentType from ebay_feedback where FeedbackID='$FeedbackID'";
				$sq		= $dbcon->execute($sq);
				$sq		= $dbcon->getResultArray($sq);
				if(count($sq) == 0)
				{
					$sql	= "INSERT INTO `ebay_feedback` (`CommentingUser` , `account` , `CommentingUserScore` , `CommentText` ,";
					$sql   .= "`CommentTime` , `CommentType` , `ItemID` , `FeedbackID` , `TransactionID` , `ItemTitle` , `currencyID` , `ItemPrice` , `status` ,`ebay_user`,`feedbacktime`)";
					$sql   .= "VALUES ('$CommentingUser', '$account', '$CommentingUserScore', '$CommentText', '$CommentTime', '$CommentType', ";
					$sql   .= "'$ItemID', '$FeedbackID', '$TransactionID', '$ItemTitle', '$currencyID', '$ItemPrice', '0','$user','$feedbacktime')";
					if($dbcon->execute($sql))
					{
						$get_sku = "select sku,ebay_amount from ebay_orderdetail where ebay_ordersn='$sorder' and ebay_itemid='$ItemID' and ebay_tid ='$TransactionID'";
						$get_sku = $dbcon->execute($get_sku);
						$get_sku = $dbcon->getResultArray($get_sku);
						$sku = $get_sku[0]['sku'];
						$ebay_amount = $get_sku[0]['ebay_amount'];
						echo $sku."\n";
						$update_sku = "update ebay_feedback set sku='$sku',Qty='$ebay_amount' where FeedbackID='$FeedbackID'";
						echo $update_sku."\n";
						$dbcon->execute($update_sku);
						
					}
					else
					{
						echo "Buyerid: $CommentingUser 评价添加失败,类型为:$CommentType"."\n";
					}

				}
				else
				{
					echo "Buyerid: $CommentingUser 评价已存在"."\n";
				}
				

			}
			if($startpage >= $endpage){
				break;
			}
			$startpage++;
		}
	}
	//获取eBay上已修改的中差评
	function GetFeedback_change($account)
	{
		global $dbcon,$api_feedback,$user;
		$verb = 'GetFeedback';
		$cc			= date("Y-m-d H:i:s");
		$start		= date('Y-m-d H:i:s',strtotime("$cc - 61 days"));
		$start      = strtotime($start);
		$end        = strtotime($cc);
		$file 		= EBAY_RAW_DATA_PATH.'feedback_lost_sql/lost_sql.txt';
		$file2      = EBAY_RAW_DATA_PATH.'feedback_lost_sql/success_sql.txt';
		$get 		= "select FeedbackID,CommentText,CommentingUser,ItemID,TransactionID,CommentType from ebay_feedback where account='$account' and (CommentType='Neutral' or CommentType='Negative') and feedbacktime between $start and $end";	
		$get 		= $dbcon->execute($get);
		$get 		= $dbcon->getResultArray($get);
		for($ii=0; $ii<count($get); $ii++)
		{  
			$status = "";
			$feedbackID 	= $get[$ii]['FeedbackID'];
			$commentingUser = $get[$ii]['CommentingUser'];
			$itemID 		= $get[$ii]['ItemID'];
			$transactionID  = $get[$ii]['TransactionID'];
			$commentType 	= $get[$ii]['CommentType'];
			$commentText 	= $get[$ii]['CommentText'];
			
			$responseXml = $api_feedback->request_change($itemID,$transactionID,$commentingUser);
			if(stristr($responseXml, 'HTTP 404') || $responseXml == '') return 'id not found';
			$data        = XML_unserialize($responseXml);
			
			$ack	         = $data['GetFeedbackResponse']['Ack'];	
			$feedbackRevised = $data['GetFeedbackResponse']['FeedbackDetailArray']['FeedbackDetail'];
			$feedbackRevised = $feedbackRevised[0]['FeedbackRevised'];
			if($ack !="Success")
			{
				echo '同步失败'."\n";
			}
			else
			{
				if($feedbackRevised == "true")
				{
					$feedback		 = $data['GetFeedbackResponse']['FeedbackDetailArray']['FeedbackDetail'];
					$feedbackType	 = $feedback[0]['CommentType'];
					$feedbackUser	 = $feedback[0]['CommentingUser'];
					if($commentingUser == $feedbackUser)
					{
						$feedbackText	= addslashes(str_rep($feedback[0]['CommentText']));
					}
					else
					{
						$feedbackText	= addslashes(str_rep($commentText));
					}
					if($commentType != $feedbackType)
					{
						if($commentType == "Neutral")
						{
							if($feedbackType == "Positive")
							{
								$status = "21"; //中评改好评
							}
							else if($feedbackType == "Negative")
							{
								$status = "23"; //中评改差评
							}
							else
							{
								$status = "22";//中评改中评
							}
						}
						else if($commentType == "Negative")
						{
							if($feedbackType == "Positive")
							{
								$status = "31"; //差评改好评
							}
							else if($feedbackType == "Neutral")
							{
								$status = "32"; //差评改中评
							}
							else
							{
								$status = "33"; //差评改差评
							}
						}
						else{}//过滤好评
						if($status != "")
						{
							$update_type = "update ebay_feedback set status='$status',CommentType='$feedbackType',CommentText='$feedbackText' where FeedbackID=$feedbackID";
							$sql = $update_type;
							echo $sql."\n";
							if($dbcon->execute($update_type))
							{
								echo 'Success '."\n";
								write_a_file($file2,$sql);
							}
							else
							{
								echo 'Failure '."\n";
								echo mysql_errno() . ": " . mysql_error() . "\n";
								write_a_file($file,$sql);	
							}	
							echo 'userID :'.$commentingUser.":".$commentType."------------>".$feedbackType."\n";
						}
					}
				}
	
			}
			
		}
		#########加载feedback_lost_sql########
		$relost_feedback_ids = '';
		$push_feedback_ids = '';
		$lost_feedback_path = $file;			
		$feedback_content = read_and_empty_lost_sql($lost_feedback_path);
		$feedback_lists = sql_str2array($feedback_content);
		if(!empty($feedback_lists)){
			foreach($feedback_lists AS $feedback_sql){
				if(!$dbcon->execute($feedback_sql)){
					$relost_feedback_ids .= $feedback_sql."\n";//获取失败语句
				}else{
					$push_feedback_ids .= $feedback_sql."\n";//获取成功语句
				}
			}
			if(!empty($relost_feedback_ids)){
				write_a_file($lost_feedback_path, $relost_feedback_ids);//执行失败再次写入
			}
			if(!empty($push_feedback_ids)){
				write_a_file(str_replace('lost_sql.txt', 'push_success_sql.txt', $lost_feedback_path), $push_feedback_ids);
			}
		}
	}
	//获取线上List
	function GetList($account,$startpage,$endpage,$perPageCount)
	{	
		global $dbcon,$api_list,$user,$item_list;
		echo '同步listing,开始于第'.$startpage.'页，结束于第'.$endpage.'页，每页同步'.$perPageCount.'条'."\n";
		while(true)
		{
			echo '开始运行,第'.$startpage.'页'."\n";
			$responseXml = $api_list->request($startpage,$perPageCount);
			if(stristr($responseXml, 'HTTP 404') || $responseXml == '') return 'id not found';
			$data       = XML_unserialize($responseXml); 	 
			$totalpages	= $data['GetMyeBaySellingResponse']['ActiveList']['PaginationResult']['TotalNumberOfPages'];				
			$result		= $data['GetMyeBaySellingResponse']['ActiveList']['ItemArray']['Item']; 
			for($i=0; $i<count($result); $i++)
			{
				$ItemID					= $result[$i]['ItemID'];				
			 	$ViewItemURL			= $result[$i]['ListingDetails']['ViewItemURL'];
				$ListingType			= $result[$i]['ListingType'];
				$Quantity				= $result[$i]['Quantity'];
				$StartPricecurrencyID	= $result[$i]['BuyItNowPrice attr']['currencyID'];
				$StartPrice				= $result[$i]['BuyItNowPrice'];
				$ShippingCost			= $result[$i]['ShippingDetails']['ShippingServiceOptions']['ShippingServiceCost'];
				$Title					= mysql_real_escape_string($result[$i]['Title']);
				$SKU					= $result[$i]['SKU'];
				$QuantityAvailable		= $result[$i]['QuantityAvailable'];	
				$QuantitySold			= $Quantity - $QuantityAvailable;	
				$ss						= "select id from ebay_list where ebay_user='$user' and ebay_account ='$account' and ItemID='$ItemID' ";
				$ss						= $dbcon->execute($ss);
				$ss						= $dbcon->getResultArray($ss);
				if(count($ss) == 0 && $ListingType != 'Chinese' )
				{
					$pos = strpos($SKU,':');
					if($pos===false){
						$realsku = str_pad($SKU,3,'0',STR_PAD_LEFT);
					}else{
						$realsku = substr($SKU,0,$pos);
					}
					if($StartPricecurrencyID=='EUR'){
						$responseItemXml = $item_list->request($ItemID);
						if(stristr($responseItemXml, 'HTTP 404') || $responseItemXml == '') return 'id not found';
						$itemData        = XML_unserialize($responseItemXml);
						$Site		     = $itemData['GetItemResponse']['Item']['Site'];
					}else{
						switch($StartPricecurrencyID){
							case 'GBP':
								$Site   = 'UK';
								break;
							case 'AUD':
								$Site   = 'Australia';
								break;
							case 'HKD':
								$Site   = 'HongKong';
								break;
							case 'USD':
								$Site   = 'US';
								break;
							case 'SGD':
								$Site   = 'Singapore';
								break;
							default:
							$Site   = '';
						}
					}
					$bb		= "insert into ebay_list(status,ItemID,ViewItemURL,QuantitySold,Quantity,Title,SKU,realSKU,ListingType,StartPrice,ebay_account,ebay_user,QuantityAvailable,StartPricecurrencyID,ShippingCost,Site,is_online) value('0','$ItemID','$ViewItemURL','$QuantitySold','$Quantity','$Title','$SKU','$realsku','$ListingType','$StartPrice','$account','$user','$QuantityAvailable','$StartPricecurrencyID','$ShippingCost','$Site','2')";
					if($dbcon->execute($bb))
					{
						echo $ItemID.'同步成功'."\n";		
						
						$Variations		= $result[$i]['Variations']['Variation'];
						if($Variations !='')
						{	
							if($result[$i]['Variations']['Variation']['StartPrice'] != '' )
							{
								$Variations		= array();
								$Variations[0]	= $result[$i]['Variations']['Variation'];
							}
							for($j=0;$j<count($Variations);$j++)
							{
								$SKU			= $Variations[$j]['SKU'];
								$Quantity		= $Variations[$j]['Quantity'];
								$StartPrice		= $Variations[$j]['StartPrice'];
								$QuantitySold	= $Variations[$j]['SellingStatus']['QuantitySold'];
								$tjstr			= '';
								$VariationSpecifics	= $Variations[$j]['VariationSpecifics'];
								if($VariationSpecifics != '')
								{
									$NameValueList	= $Variations[$j]['VariationSpecifics']['NameValueList']['Name'];
									if($NameValueList != '')
									{
										$NameValueList			= array();
										$NameValueList[0] 		= $Variations[$j]['VariationSpecifics']['NameValueList'];
									}
									else
									{											
										$NameValueList	= $Variations[$j]['VariationSpecifics']['NameValueList'];
									}		
									for($n=0;$n<count($NameValueList);$n++)
									{
										$Nname		= $NameValueList[$n]['Name'];
										$Nvalue		= $NameValueList[$n]['Value'];
										$tjstr		.= $Nname.'**'.$Nvalue.'++';
									}
									$tjstr			= mysql_real_escape_string($tjstr);

								}
								$QuantityAvailable	= $Quantity - $QuantitySold;
								if($SKU != '')
								{
									$dd = "select id from ebay_listvariations where ebay_account='$account' and itemid='$ItemID' and SKU='$SKU' ";	    
								}
								else
								{
									$dd = "select id from ebay_listvariations where ebay_account='$account' and itemid='$ItemID' and VariationSpecifics='$tjstr' ";
								}										
								$dd					= $dbcon->execute($dd);
								$dd					= $dbcon->getResultArray($dd);
								if(count($dd) == 0)
								{
									$rr = "insert into ebay_listvariations(SKU,Quantity,StartPrice,itemid,ebay_account,QuantitySold,QuantityAvailable,VariationSpecifics) values('$SKU','$Quantity','$StartPrice','$ItemID','$account','$QuantitySold','$QuantityAvailable','$tjstr')";
									$dbcon->execute($rr);
								}
							}
										
						}
										
					}
					else
					{
						echo $ItemID.'同步失败<br>';
					}
				}
				else
				{
					$ship_cost = $result[$i]['ShippingDetails']['ShippingServiceOptions']['ShippingServiceCost'];
					$update    = "update ebay_list set ShippingCost='$ship_cost',SKU='$SKU',is_online = '2' where ItemID='$ItemID'";
					echo $update."\n";
					$dbcon->execute($update);
				}
			}
			
			if($startpage >= $totalpages) break;
			if($startpage >= $endpage) break;
			$startpage++;
		}
	}
	//更新运费
	function UpdateShipCost($account){
		global $dbcon,$api_list,$user;
		$sql = "select ItemID from ebay_list where status=0 and ebay_account='$account'";
		$sql = $dbcon->execute($sql);
		$sql = $dbcon->getResultArray($sql);
		for($kk=0;$kk<count($sql);$kk++){
			$itemID = $sql[$kk]['ItemID'];
			$responseXml = $api_list->request($itemID);
			if(stristr($responseXml, 'HTTP 404') || $responseXml == '') return 'id not found';
			$data       = XML_unserialize($responseXml); 
			$ShipCost   = $data['GetItemResponse']['Item']['ShippingDetails']['ShippingServiceOptions']['ShippingServiceCost']; 
			$update    = "update ebay_list set ShippingCost='$ShipCost' where ItemID='$itemID'";
			echo $update."\n";
			$dbcon->execute($update);
		}
		
	}
	function GetSellerEvents($account,$start_time)
	{
		global $dbcon,$api_SellerEvents,$user;
		$start1	= date('Y-m-d H:i:s');	
		$start0	= date('Y-m-d H:i:s',strtotime("$start1 - $start_time minutes"));
		$start	= date('Y-m-d',strtotime("$start0+ 0 days")).'T'.date('H:i:s',strtotime($start0)).'.000Z';
		$responseXml = $api_SellerEvents->request($start);
		if(stristr($responseXml, 'HTTP 404') || $responseXml == '') return 'id not found';
		$data       = XML_unserialize($responseXml);
		$ack = $data['GetSellerEventsResponse']['Ack'];
		if($ack == "Success")
		{
			$result = $data['GetSellerEventsResponse']['ItemArray']['Item'];
			foreach($result as $dd)
			{
				$ItemID 			= $dd['ItemID'];
				$CurrentPrice   	= $dd['SellingStatus']['CurrentPrice'];
				$QuantitySold   	= $dd['SellingStatus']['QuantitySold'];
				$ListingStatus  	= $dd['SellingStatus']['ListingStatus'];
				$Site           	= $dd['Site'];
				$Quantity       	= $dd['Quantity'];
				$QuantityAvailable	= $Quantity - $QuantitySold;
				$getinfo	= "select StartPrice,QuantitySold,Quantity from ebay_list where ebay_user='$user' and  ebay_account ='$account' and ItemID='$ItemID' ";
				$getinfo    = $dbcon->execute($getinfo);
				$getinfo	= $dbcon->getResultArray($getinfo);
				if(count($getinfo) ==1)
				{
					$db_CurrentPrice = $getinfo[0]['StartPrice'];
					$db_QuantitySold = $getinfo[0]['QuantitySold'];
					$db_Quantity     = $getinfo[0]['Quantity'];
					if($ListingStatus !='Active')
					{
						if($QuantityAvailable == 0)
						{
							$update_status = "update ebay_list set status ='2',QuantityAvailable='$QuantityAvailable',QuantitySold='$QuantitySold' where ebay_account ='$account' and ebay_user='$user' and ItemID='$ItemID'";
							echo 'update_status: '.$update_status."\n";
						}
						else
						{
							$update_status = "update ebay_list set status ='1',QuantityAvailable='$QuantityAvailable',QuantitySold='$QuantitySold' where ebay_account ='$account' and ebay_user='$user' and ItemID='$ItemID'";
						}
						
						$dbcon->execute($update_status);
					}
					else
					{
						if($db_CurrentPrice != $CurrentPrice || $db_QuantitySold != $QuantitySold || $db_Quantity != $Quantity)
						{	
							//echo "db_CurrentPrice:".$db_CurrentPrice.'---CurrentPrice:'.$CurrentPrice."\n";
							//echo "db_QuantitySold:".$db_QuantitySold.'---QuantitySold:'.$QuantitySold."\n";
							//echo "db_Quantity:".$db_Quantity.'---Quantity:'.$Quantity."\n";
							$update_list = "update ebay_list set StartPrice='$CurrentPrice',Quantity='$Quantity',QuantityAvailable='$QuantityAvailable',QuantitySold='$QuantitySold' where ebay_account='$account' and ebay_user='$user' and ItemID=$ItemID";
							//echo 'update_list: '.$update_list."\n";
							$dbcon->execute($update_list);
						}
							/*检查多属性*/
							$Variations		= $dd['Variations']['Variation'];
							if($Variations != '')
							{
								foreach($Variations as $var)
								{
									$var_SKU          		= $var['SKU'];
									$var_StartPrice   		= $var['StartPrice'];
									$var_Quantity 	  		= $var['Quantity'];
									$var_QuantitySold 		= $var['SellingStatus']['QuantitySold'];
									$var_QuantityAvailable  = $var_Quantity - $var_QuantitySold;
									echo "*****QTY:".$var_Quantity."--------Price:".$var_StartPrice."\n";
									$tjstr			= '';
									$var_Specifics  = $var['VariationSpecifics'];
									if($var_Specifics !='')
									{
										$NameValueList	= $var_Specifics['NameValueList']['Name'];
										if($NameValueList != '')
										{
											$NameValueList	  = array();
											$NameValueList[0] = $var_Specifics['NameValueList'];
										}
										else
										{
											$NameValueList	= $var_Specifics['NameValueList'];
										}
										for($n=0;$n<count($NameValueList);$n++)
										{
											$Nname		= $NameValueList[$n]['Name'];
											$Nvalue		= $NameValueList[$n]['Value'];
											$tjstr		.= $Nname.'**'.$Nvalue.'++';
										}
										$tjstr			= mysql_real_escape_string($tjstr);
									}
									$get_info = "select StartPrice, Quantity,QuantitySold from ebay_listvariations where ebay_account='$account' and itemid='$ItemID' and SKU='$var_SKU'";
									$get_info = $dbcon->execute($get_info);
									$info 	  = $dbcon->getResultArray($get_info);
									if(count($info) ==0)
									{	
										//同步ebay上新加的子料号
										$rr = "insert into ebay_listvariations(SKU,Quantity,StartPrice,itemid,ebay_account,QuantitySold,QuantityAvailable,VariationSpecifics) values('$var_SKU','$var_Quantity','$var_StartPrice','$ItemID','$account','$var_QuantitySold','$var_QuantityAvailable','$tjstr')";
										//echo 'rr: '.$rr."\n";
										$dbcon->execute($rr);
									}
									else
									{
										$db_var_StartPrice   = $info[0]['StartPrice'];
										$db_var_Quantity     = $info[0]['Quantity'];
										$db_var_QuantitySold = $info[0]['QuantitySold'];
										if($db_var_StartPrice != $var_StartPrice || $db_var_Quantity != $var_Quantity || $db_var_QuantitySold != $var_QuantitySold)
										{
											//echo $var_SKU."\n";
											//echo 'db_var_StartPrice:'.$db_var_StartPrice.'----var_StartPrice:'.$var_StartPrice."\n";
											//echo 'db_var_Quantity:'.$db_var_Quantity.'----var_Quantity:'.$var_Quantity."\n";
											//echo 'db_var_QuantitySold:'.$db_var_QuantitySold.'----var_QuantitySold:'.$var_QuantitySold."\n";
											$update_var = "update ebay_listvariations set Quantity='$var_Quantity',StartPrice='$var_StartPrice',QuantitySold='$var_QuantitySold',QuantityAvailable='$var_QuantityAvailable',VariationSpecifics='$tjstr' where ebay_account='$account' and itemid=$ItemID and SKU='$var_SKU'";
											echo 'update_var: '.$update_var."\n";
											$dbcon->execute($update_var);
										}
									}
								}
							}
						
					}
				}
			}
			
		}
		print_r($data);
	}
function write_body_html($nn,$ebay_account)
{
	global $dbcon;
	$array_account = array('wellchange','elerose88','zealdora','choiceroad','cafase88','easytrade2099','easyshopping678','befashion','360beauty','charmday88','easebon','estore2099','fiveseason88','voguebase55','dresslink','happydeal88','work4best','eshop2098','futurestar99');
	if(in_array($ebay_account,$array_account))
	{
		if($nn=='09')
		{
			$total = 31;
		}
		else if($nn=='10')
		{
			$total = 32;
		}
		else
		{
			$total = 10;
		}
		for($mm=1;$mm<$total;$mm++)
		{
			if($mm<=9)
			{
				$mm = '0'.$mm;
			}
			$start_time = '2012-'.$nn.'-'.$mm.'T00:00:00.000Z';
			$end_time   = '2012-'.$nn.'-'.$mm.'T23:59:59.000Z';
			$content = "select message_id,body,createtime from ebay_message where ebay_account = '$ebay_account' and createtime between '$start_time' and '$end_time'";
			$content = $dbcon->execute($content);
			$content = $dbcon->getResultArray($content);
			for($kk=0; $kk<count($content); $kk++)
			{
				$mid  =  $content[$kk]['message_id'];
				$body =  $content[$kk]['body'];
				$date_time = $content[$kk]['createtime'];
				$date = date('Y-m-d',strtotime("$date_time + 8 hours"));
				$dir2  = '/home/html_include/ebay_message_body/'.$ebay_account.'/'.$date.'/'.$mid.'.html';
				write_a_file($dir2,$body);
			}
		}
	}
	else
	{
		if($nn=='11')
		{
			for($mm=1;$mm<10;$mm++)
			{
				if($mm<=9)
				{
					$mm = '0'.$mm;
				}
				$start_time = '2012-'.$nn.'-'.$mm.'T00:00:00.000Z';
				$end_time   = '2012-'.$nn.'-'.$mm.'T23:59:59.000Z';
				$content = "select message_id,body,createtime from ebay_message where ebay_account = '$ebay_account' and createtime between '$start_time' and '$end_time'";
				$content = $dbcon->execute($content);
				$content = $dbcon->getResultArray($content);
				for($kk=0; $kk<count($content); $kk++)
				{
					$mid  =  $content[$kk]['message_id'];
					$body =  $content[$kk]['body'];
					$date_time = $content[$kk]['createtime'];
					$date = date('Y-m-d',strtotime("$date_time + 8 hours"));
					$dir2  = '/home/html_include/ebay_message_body/'.$ebay_account.'/'.$date.'/'.$mid.'.html';
					write_a_file($dir2,$body);
				}
			}
		}
	}
}
function generateOrdersn(){
	/**
	*自动获取产品 ordersn 的方法
	*/
	global $dbcon;
	$val = date("Y-m-d-His"). mt_rand(100, 999);
	while(true){
		$sql = "SELECT ebay_id AS num FROM ebay_order WHERE ebay_ordersn='$val'";
		$sql = $dbcon->query($sql);
		$si = $dbcon->num_rows($sql);
		if($si==0){
			break;	
		}
		$val = date("Y-m-d-His"). mt_rand(100, 999);
	}
	return $val;
}

function CheckID($recordnumber,$account){
	global $dbcon;
	$sql		= "select ebay_ordersn from ebay_order where recordnumber='$recordnumber' and ebay_account='$account'";
	$sql  = $dbcon->execute($sql);
	$sql  = $dbcon->getResultArray($sql);
	if(count($sql) == 0){
		$status			= false;
	}else{
		$status 		= $sql[0]['ebay_ordersn'];
	}
	return $status;
}
function CheckdetailID($recordnumber,$account){
	global $dbcon;
	$sql		= "select ebay_ordersn from ebay_orderdetail where recordnumber='$recordnumber' and ebay_account='$account'";
	$sql  = $dbcon->execute($sql);
	$sql  = $dbcon->getResultArray($sql);
	if(count($sql) == 0){
		$status			= false;
	}else{
		$status 		= $sql[0]['ebay_ordersn'];
	}
	return $status;
}


function check_blacklist($order){
	global $dbcon;

	$ebay_userid = $order['ebay_userid'];
	$ebay_username = $order['ebay_username'];
	$ebay_usermail = $order['ebay_usermail'];
	$ebay_street = $order['ebay_street'];
	$ebay_phone = $order['ebay_phone'];
	$ebay_account = $order['ebay_account'];
	$sql = "select count(*)  as totalnum from ebay_blacklist ";
	$blackcondition = array();
	if($ebay_userid != ""){
		$blackcondition[] = "ebay_userid='{$ebay_userid}'";
	}
	if($ebay_username != ""){
		$blackcondition[] = "ebay_username='{$ebay_username}'";
	}
	if($ebay_usermail != ""){
		$blackcondition[] = "ebay_usermail='{$ebay_usermail}'";
	}
	if($ebay_street != ""){
		$blackcondition[] = "ebay_street='{$ebay_street}'";
	}
	if($ebay_phone != ""){
		$blackcondition[] = "ebay_phone='{$ebay_phone}'";
	}
	$bconditon = implode(' OR ', $blackcondition);
	$blackwhere = count($blackcondition)	> 0 ? " where {$bconditon} and ebay_accounts like '%[{$ebay_account}]%' " : 'where 0';
	$sql = $sql.$blackwhere;

	$sql	= $dbcon->execute($sql);
	$black_list	= $dbcon->fetch_one($sql);
	if($black_list['totalnum'] > 0){
		$ss = "update ebay_order set ebay_status=684 where ebay_id={$order['ebay_id']}";

		if($dbcon->execute($ss)){
			insert_mark_shipping($order['ebay_id']);
			echo "订单id{$order['ebay_id']}进入黑名单文件夹";
		}else{
			echo "订单id{$order['ebay_id']}移动进黑名单文件夹失败";
		}
	}
}
?>
