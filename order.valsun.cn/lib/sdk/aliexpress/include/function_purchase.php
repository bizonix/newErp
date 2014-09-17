<?php 
function create_purchase_list($name='', $readcache=true, $skuarray=array()){
	$run_starttime = time(); //test
	
	global $user,$truename,$dbcon,$PURCHASE_POWER_SQL;

	$days7 = 0.7;
	$days15 = 0.2;
	$days30 = 0.1;
	$dataarrays = array();
	
	$searchname = empty($name) ? $truename : $name;
	$searchsql = !empty($name) ? " AND cguser='{$name}' " : "AND cguser IN ({$PURCHASE_POWER_SQL})";
	
	if ($readcache){
		$sql	 = "SELECT * FROM ebay_sku_statistics WHERE purchaseuser='{$searchname}'";
		$sql	 = $dbcon->execute($sql);
		$storelists = $dbcon->getResultArray($sql);
		return $storelists;
	}
	
	$sql	 = "SELECT id,store_name FROM ebay_store WHERE ebay_user='$user'";
	$sql	 = $dbcon->execute($sql);
	$storelists = $dbcon->getResultArray($sql);

	foreach ($storelists AS $storelist){
		$storeid = $storelist['id'];
		$store_name = $storelist['store_name'];
		
		$skuwhere = !empty($skuarray) ? "AND goods_sn IN (".implode(',', array2strarray($skuarray)).")" : '';
		
		$sql = "SELECT goods_id,goods_sn,goods_name,factory,cguser FROM ebay_goods WHERE ebay_user='{$user}' {$searchsql} {$skuwhere}";
		
		if ($truename=='vipchen') echo $sql.'<br>';
		
		$sql = $dbcon->execute($sql);
		$goods_lists = $dbcon->getResultArray($sql);
		foreach ($goods_lists AS $goods_list){
			$dataarrays = array();
			$factory = $goods_list['factory'];
			$goods_sn = $goods_list['goods_sn'];
			$cguser = $goods_list['cguser']; //add by xiaojinhua 
			$sql = "SELECT goods_count,goods_sx,goods_days,purchasedays FROM ebay_onhandle WHERE goods_sn='{$goods_sn}' AND store_id={$storeid}";
			$sql = $dbcon->execute($sql);
			$onhandle_list = $dbcon->getResultArray($sql);
			if (empty($onhandle_list)){
				continue;
			}
			$goods_days = $onhandle_list[0]['goods_days'];
			$goods_count = $onhandle_list[0]['goods_count'];
			$sql = "SELECT * FROM ebay_sku_statistics WHERE sku='{$goods_sn}'";
			$sql = $dbcon->execute($sql);
			$sku_info = $dbcon->getResultArray($sql);
			if (!isset($sku_info[0]['first_sale'])||empty($sku_info[0]['first_sale'])){
				$first_sale = get_firstsale($goods_sn);
			}else{
				$first_sale = $sku_info[0]['first_sale'];
			}
			
// add by xiaojinhua
			$sql = "SELECT isuse FROM ebay_goods WHERE goods_sn='{$goods_sn}'";
			$sql = $dbcon->execute($sql);
			$goods_info = $dbcon->getResultArray($sql);
			$isuse = $goods_info[0]['isuse']; //sku 是否在线状态 0 在线 2零库存 1 下线

//2013-02-19
			$everyday_sale = isset($sku_info[0]['everyday_sale'])&&$sku_info[0]['everyday_sale']!='0.00' ? $sku_info[0]['everyday_sale'] : 5;
			$stockused = stockbookused($goods_sn,$storeid,$cguser);

			if ($first_sale>0){
				$_time = time()-$first_sale;
				$saleday = ceil($_time/(3600*24));
				list($packagingnums, $inums) = get_partsalenosend($goods_sn, $storeid);
				$salensend = getsaleandnosendall($goods_sn, $storeid)+$packagingnums;
				$interceptnums = getinterceptall($goods_sn, $storeid)+$inums;
				$autointerceptnums = get_autointercept($goods_sn, $storeid); //自动拦截数量
				$auditingnums = getauditingall($goods_sn, $storeid);
				$last_sale = isset($sku_info[0]['last_sale']) ? $sku_info[0]['last_sale'] : 0;
				$thirtycheck = time()-30*24*3600;
				$last_sale = get_lastsale($goods_sn);
				$total_count = $goods_count + $stockused;
 				$alarm_count = $total_count - $salensend - $interceptnums - $auditingnums - $autointerceptnums;
 				if ($truename=='vipchen') {
 					var_dump($last_sale, $total_count, $salensend, $interceptnums, $auditingnums, $stockused, $saleday, $last_sale,$thirtycheck);
 				}
				if ($saleday>30){
					if ($last_sale>$thirtycheck){

						$end1	= strtotime(date('Y-m-d').'23:59:59');	
						$start1	= $end1-7*24*3600;
						$qty1	= getSaleProducts($start1,$end1,$goods_sn,$storeid, $everyday_sale);
							
						$end2	= $start1;	
						$start2	= $end1-15*24*3600;
						$qty2	= getSaleProducts($start2,$end2,$goods_sn,$storeid, $everyday_sale);
						
						$end3	= $start2;	
						$start3	= $end1-30*24*3600;
						$qty3	= getSaleProducts($start3,$end3,$goods_sn,$storeid, $everyday_sale);
						
																										//  取得已经预订的产品数量
						if(!($isuse == "1" || $isuse == "3") ){// 没下线的sku才进行这种计算
							$everyday_sale = $qty1/7*$days7+$qty2/8*$days15+$qty3/15*$days30;
						}
						$needqty = ceil($everyday_sale*$goods_days) + $interceptnums;  									// 计算产品库存报警数量
 						if ($truename=='vipchen') var_dump($alarm_count, '----------'.($alarm_count<$needqty));
						$dataarray['factory']			= $factory;
						$dataarray['everyday_sale']		= $everyday_sale>0.005 ? round($everyday_sale, 2) : 0;
						$dataarray['storeid']			= $storeid;
						$dataarray['booknums']			= $stockused;
						$dataarray['sevendays']			= $qty1;
						$dataarray['fifteendays']		= $qty2;
						$dataarray['thirtydays']		= $qty3;
						$dataarray['last_sale']			= $last_sale;
						$dataarray['salensend']			= $salensend;
						$dataarray['interceptnums']		= $interceptnums;
						$dataarray['autointerceptnums']		= $autointerceptnums;
						$dataarray['auditingnums']		= $auditingnums;
						$dataarray['is_warning']		= $alarm_count<1||$alarm_count<$needqty ? 1 : 0;
					}else{
						$dataarray['factory']			= $factory;
						$dataarray['everyday_sale']		= 0;
						$dataarray['storeid']			= $storeid;
						$dataarray['booknums']			= $stockused;
						$dataarray['sevendays']			= 0;
						$dataarray['fifteendays']		= 0;
						$dataarray['thirtydays']		= 0;
						$dataarray['last_sale']			= $last_sale;
						$dataarray['salensend']			= $salensend;
						$dataarray['interceptnums']		= $interceptnums;

						$dataarray['autointerceptnums']		= $autointerceptnums;

						$dataarray['auditingnums']		= $auditingnums;
						$dataarray['is_warning']		= $alarm_count<0 ? 1 : 0;
					}
				}else{
					$end	= strtotime(date('Y-m-d').'23:59:59');	
					$start	= $end - $saleday*24*3600;
					$qty	= getSaleProducts($start, $end, $goods_sn, $storeid, $everyday_sale);

					if(!($isuse == "1" || $isuse == "3")){ //判断是否在线
						$everyday_sale = $qty/$saleday;
					}
					$stockused = stockbookused($goods_sn,$storeid,$cguser);
					$needqty = ceil($everyday_sale*$goods_days) + $interceptnums;  									// 计算产品库存报警数量
					$dataarray['factory']			= $factory;
					$dataarray['everyday_sale']		= round($everyday_sale, 2);
					$dataarray['storeid']			= $storeid;
					$dataarray['booknums']			= $stockused;
					$dataarray['sevendays']			= 0;
					$dataarray['fifteendays']		= 0;
					$dataarray['thirtydays']		= 0;
					$dataarray['last_sale']			= $last_sale;
					$dataarray['salensend']			= $salensend;
					$dataarray['interceptnums']		= $interceptnums;

					$dataarray['autointerceptnums']		= $autointerceptnums;

					$dataarray['auditingnums']		= $auditingnums;
					$dataarray['is_warning']		= $alarm_count<1||$alarm_count<$needqty ? 1 : 0;
				}
				
			}else{
				$dataarray['factory']			= $factory;
				$dataarray['everyday_sale']		= 0;
				$dataarray['storeid']			= $storeid;
				$dataarray['booknums']			= $stockused;
				$dataarray['sevendays']			= 0;
				$dataarray['fifteendays']		= 0;
				$dataarray['thirtydays']		= 0;
				$dataarray['last_sale']			= 0;
				$dataarray['salensend']			= 0;
				$dataarray['auditingnums']		= 0;
				$dataarray['interceptnums']		= 0;
				$dataarray['autointerceptnums']	= 0;
				$dataarray['is_warning']		= 0;
			}
			$dataarray['lastupdate'] = time();
			$dataarray['first_sale'] = $first_sale;
			$dataarray['purchaseuser'] = $goods_list['cguser'];
			$sql = !empty($sku_info) ? "UPDATE ebay_sku_statistics SET ".array2sql($dataarray)." WHERE sku='{$goods_sn}'" : "INSERT INTO ebay_sku_statistics SET sku='{$goods_sn}',".array2sql($dataarray);
			if ($truename=='vipchen') echo $sql."<br><br>";
			$dbcon->update($sql);
			$dataarrays[] = $dataarray;
			unset($dataarray);
		}
	}
	$run_endtime = time();
	$speed_time = $run_endtime - $run_starttime;
	echo "更新缓存一共花了  ".$speed_time."秒";
  	return $dataarrays;
}

function get_firstsale($sku){

	global $dbcon;
	
	$combineskus = get_combinesku($sku);
	$skus = empty($combineskus) ? array() : array_keys($combineskus);
	array_push($skus, $sku);
	$skus = array2strarray($skus);
	
	$sql = "SELECT a.ebay_paidtime,a.ebay_addtime  FROM  ebay_order as a left join ebay_orderdetail as b on a.ebay_ordersn=b.ebay_ordersn WHERE b.sku IN (".implode(',', $skus).") ORDER BY a.ebay_id ASC LIMIT 1";
	$sql = $dbcon->execute($sql);
	$order = $dbcon->getResultArray($sql);
	return $order[0]['ebay_paidtime'] != '' ? $order[0]['ebay_paidtime'] : $order[0]['ebay_addtime'];
	
}

function get_lastsale($sku){
	global $dbcon;
	
	$combineskus = get_combinesku($sku);
	$skus = empty($combineskus) ? array() : array_keys($combineskus);
	array_push($skus, $sku);
	$skus = array2strarray($skus);
	
	$sql = "SELECT a.ebay_paidtime,a.ebay_addtime  FROM  ebay_order as a left join ebay_orderdetail as b on a.ebay_ordersn=b.ebay_ordersn WHERE b.sku IN (".implode(',', $skus).") ORDER BY a.ebay_id DESC LIMIT 1";
	$sql = $dbcon->execute($sql);
	$order = $dbcon->getResultArray($sql);
	return $order[0]['ebay_paidtime'] != '' ? $order[0]['ebay_paidtime'] : $order[0]['ebay_addtime'];
	
}



function get_combinesku($sku){
	
	global $dbcon;
	
	//$sql = "SELECT goods_sn,goods_sncombine FROM ebay_productscombine WHERE truesku LIKE '%[{$sku}]%'";
	$sql = "SELECT goods_sn,goods_sncombine FROM ebay_productscombine WHERE truesku LIKE '[{$sku}]%' OR truesku LIKE '%[{$sku}]'";//modified by Herman.Xi @ 2013-06-04
	$sql = $dbcon->execute($sql);
	$combinelists = $dbcon->getResultArray($sql);
	
	if (empty($combinelists)){
		return array();
	}
	$results = array();
	foreach ($combinelists AS $combinelist){
		$results[$combinelist['goods_sn']] = $combinelist['goods_sncombine'];
	}
	return $results;
}

function check_is_intercept($order_sn){
	//订单自动拦截方法 支持虚拟料号
	global $dbcon;
	
	$log_data = "[".date("Y-m-d H:i:s")."]\t---";
	
	$sql = "SELECT sku,ebay_amount FROM ebay_orderdetail WHERE ebay_ordersn='{$order_sn}'";
	$sql		= $dbcon->execute($sql);
	$orderdetaillist = $dbcon->getResultArray($sql);
	
	foreach ($orderdetaillist AS $orderdetail){
		$sku_arr = get_realskuinfo($orderdetail['sku']);

		$testlog = var_export($sku_arr, TRUE);

		foreach($sku_arr as $or_sku => $or_nums){
			$allnums = $or_nums*$orderdetail['ebay_amount'];
			if (!check_sku($or_sku, $allnums,$order_sn)){
				$sql = "UPDATE ebay_order SET ebay_status=640 WHERE ebay_ordersn='{$order_sn}'";
				write_log('check_is_intercept_'.date("Ymd").'/'.date("H").'.txt', $log_data.$sql."\n\n");
				$dbcon->execute($sql);

				$sql = "select ebay_id,ebay_status,ebay_account from ebay_order where ebay_ordersn='{$order_sn}'";
				$sql = $dbcon->execute($sql);
				$order_info = $dbcon->getResultArray($sql);
				$sql = "select * from ebay_mark_shipping where ebay_id={$order_info[0]['ebay_id']} limit 1 ";
				$sql = $dbcon->execute($sql);
				$mark_info = $dbcon->getResultArray($sql);
				if(count($mark_info) == 0){
					$datetime = date("Y-m-d H:i:s");
					$sql = "INSERT INTO ebay_mark_shipping SET ebay_id={$order_info[0]['ebay_id']}, ebay_status={$order_info[0]['ebay_status']}, type=1, ebay_account='{$order_info[0]['ebay_account']}', addtime='{$datetime}'";
					$dbcon->execute($sql);
				}

				return true;
			}
		}
	}
	return false;
}

function insert_mark_shipping($ebay_id){
	global $dbcon;
	$sql = "SELECT ebay_status FROM ebay_mark_shipping WHERE ebay_id={$ebay_id}";
	$handle	= $dbcon->execute($sql);
	$num = $dbcon->num_rows($handle);
	$datetime = date("Y-m-d H:i:s");
	if($num==0){
		$sql = "SELECT ebay_id,ebay_status,ebay_account FROM ebay_order WHERE ebay_id={$ebay_id}";
		$handle	= $dbcon->execute($sql);
		$order_info	= $dbcon->fetch_one($handle);
		
		if (empty($order_info)){
			return false;
		}
		
		$sql = "INSERT INTO ebay_mark_shipping SET ebay_id={$ebay_id}, ebay_status={$order_info['ebay_status']}, type=1, ebay_account='{$order_info['ebay_account']}', addtime='{$datetime}'";
		$dbcon->execute($sql);
	}
	return true;
}

function auto_contrast_intercept($ebay_orders){
	//订单自动拦截完整版 支持虚拟料号
	//add Herman.Xi 2012-12-20
	/*
	* 订单进入系统首先判断 是否为超大订单，如果为超大订单，文件夹为640；
	* B2B 抓取超大订单自动进入 超大订单待确认 modified by Herman.Xi @ 2013.05.20
	* 判断订单下，料号是否全部有货，部分有货，全部没货：
	     如果部分有货，判断其运输方式，如果为快递，文件夹为659；非快递则为660；（订单自动部分包货）
		 如果全部没货，判断其运输方式，如果为快递，文件夹为658；非快递则为661；（订单自动拦截）
		 如果全部有货：
		 	先判断如果为组合订单，文件夹为606；
			如果超重订单，文件夹为608；
			如果快递订单，文件夹为639；
			全部不满足则为导入状态
	  自动拦截时，判断自动拦截快递，非快递，自动部分包货快递，非快递里面的订单，自动每隔十五分钟执行一次
	  //支持 速卖通导入，敦煌导入，ebay线下导入，dresslink导入，快递导入，出口通导入
	*/
	global $dbcon, $defaultstoreid, $SYSTEM_ACCOUNTS, $order_statistics,$__liquid_items_fenmocsku,$__liquid_items_SuperSpecific,$__liquid_items_postbyhkpost,$__liquid_items_cptohkpost,$__liquid_items_BuiltinBattery,$__liquid_items_Paste;
	

	$log_data = "";
	$express_delivery = array('UPS','DHL','TNT','EMS','FedEx');
	$no_express_delivery = array('中国邮政平邮','中国邮政挂号','香港小包平邮','香港小包挂号','EUB','Global Mail');
	foreach($ebay_orders as $ebay_order){
		//$import_status = now_order_status_log($osn, false);
		$ebay_id = $ebay_order['ebay_id'];
		$ebay_status = $ebay_order['ebay_status'];
		//$ebay_orderid = $ebay_order['ebay_orderid'];
		$ebay_note = $ebay_order['ebay_note'];
		$order_sn = $ebay_order['ebay_ordersn'];
		$ebay_carrier = $ebay_order['ebay_carrier'];
		$ebay_countryname = $ebay_order['ebay_countryname'];
		$ebay_account = $ebay_order['ebay_account'];
		$ebay_username = $ebay_order['ebay_username'];
		$totalweight = $ebay_order['orderweight'];
		$ebay_total = $ebay_order['ebay_total'];
		
		$recal_weight = recalcorderweight($order_sn, $ebay_packingmaterial); //modified by Herman.Xi 2012-10-17
		if(empty($totalweight)){//按照如果没有重量的即默认为
			$totalweight = $recal_weight;
		}
		
		$sql = "SELECT sku,ebay_amount FROM ebay_orderdetail WHERE ebay_ordersn='{$order_sn}'";
		$result = $dbcon->execute($sql);
		$orderdetaillist = $dbcon->getResultArray($result);
		
		$contain_special_item = false;
		$shippment_cptohkpost = false;
		foreach ($orderdetaillist AS $orderdetail){
			$sku_arr = get_realskuinfo($orderdetail['sku']);
			foreach($sku_arr as $or_sku => $or_nums){
				if(in_array($or_sku,$__liquid_items_fenmocsku) || in_array($or_sku,$__liquid_items_SuperSpecific)){ //粉末状，超规格产品 走福建邮局
					$contain_special_item = true;
				}
				if(in_array($or_sku,$__liquid_items_postbyhkpost) || in_array($or_sku,$__liquid_items_cptohkpost) || in_array($or_sku,$__liquid_items_BuiltinBattery) || in_array($or_sku,$__liquid_items_Paste)){ //包含特殊料号，中国邮政转香港小包
					$shippment_cptohkpost = true;
				}
			}
		}


		if(!empty($ebay_carrier)){
			//$fees			    = calcshippingfee($totalweight,$ebay_countryname,$ebay_id,$ebay_account,$ebay_total);
			if($shippment_cptohkpost && strpos($ebay_carrier, '中国邮政')!==false){
				if(count($orderdetaillist) == 1){
					$ebay_carrier   = '香港小包挂号';
				}else{
					$ebay_carrier	= '';
					$aliexpress_ebay_noteb = "系统提示：包含特殊料号不走中国邮政，请重新选择运输方式或者拆包！";
				}
				//$fee			= $fees[1];
				//$totalweight	= $fees[2];
				$bb = "update ebay_order set ebay_carrier = '$ebay_carrier', orderweight ='$totalweight', packingtype ='$ebay_packingmaterial', ebay_noteb = '$aliexpress_ebay_noteb' where ebay_id ='$ebay_id' ";
			}else{
				//$ebay_carrier	= $fees[0];
				//$fee			= $fees[1];
				//$totalweight	= $fees[2];
				$shipfee = calctrueshippingfee($ebay_carrier, $totalweight, $ebay_countryname, $ebay_id);
				$bb = "update ebay_order set ebay_carrier = '$ebay_carrier', ordershipfee='$shipfee', orderweight ='$totalweight', packingtype ='$ebay_packingmaterial' where ebay_id ='$ebay_id' ";
			}
			$dbcon->execute($bb);
		}
		if($contain_special_item){
			$sql = "update ebay_order set ebay_carrierstyle ='1' where ebay_id ={$ebay_id}"; //add by Herman.Xi 记录该订单含有特殊料号
			$dbcon->execute($sql);
			$log_data .= "[".date("Y-m-d H:i:s")."]\t包含粉末状超规格产品---{$ebay_id}---!\n\n";
		}
		
		$record_details = array();
		$is_640 = false;
		foreach ($orderdetaillist AS $orderdetail){
			$sku_arr = get_realskuinfo($orderdetail['sku']);
			$hava_goodscount = true;
			foreach($sku_arr as $or_sku => $or_nums){
				$allnums = $or_nums*$orderdetail['ebay_amount'];
				if (!check_sku($or_sku, $allnums, $ebay_id)){
					//超大订单状态
					if(in_array($ebay_account, $SYSTEM_ACCOUNTS['aliexpress']) || in_array($ebay_account, $SYSTEM_ACCOUNTS['B2B外单']) || in_array($ebay_account, $SYSTEM_ACCOUNTS['DHgate']) || in_array($ebay_account, $SYSTEM_ACCOUNTS['出口通']) || in_array($ebay_account, $SYSTEM_ACCOUNTS['线下结算客户'])){
						$sql = "UPDATE ebay_order SET ebay_status='698' WHERE ebay_id ='$ebay_id' ";
						$log_data .= "[".date("Y-m-d H:i:s")."]\t自动跳转---{$ebay_id}---的状态为---698!\n\n";
					}else{
						$sql = "UPDATE ebay_order SET ebay_status='640' WHERE ebay_id ='$ebay_id' ";
						$log_data .= "[".date("Y-m-d H:i:s")."]\t自动跳转---{$ebay_id}---的状态为---640!\n\n";
					}
					$dbcon->execute($sql) or die("Fail : $sql");
					//$order_statistics->replaceData($order_sn, array('mask'=>1), array('mask'=>1));
					insert_mark_shipping($ebay_id);
					write_log('contrast_intercept_'.date("Ymd").'/'.date("H").'.txt', $log_data."\n\n");
					$is_640 = true;
					break;
				}else{
					$skuinfo = get_sku_info($or_sku);
					$salensend = getsaleandnosendall($or_sku, $defaultstoreid);
					$sql = "UPDATE ebay_sku_statistics SET salensend = $salensend WHERE sku = 'or_sku' ";
					$dbcon->execute($sql);
					$log_data .= "[".date("Y-m-d H:i:s")."]\t---{$sql}\n\n";
					if(($skuinfo['realnums'] - $salensend) < 0){
						$hava_goodscount = false;
						break;
					}
				}
			}
			if($hava_goodscount){$record_details[] = $orderdetail;}
		}
		if($is_640){ continue; }
		$count_record_details = count($record_details);
		$count_orderdetaillist = count($orderdetaillist);
		$final_status = $ebay_status;
		if($count_record_details == 0){
			//更新至自动拦截发货状态
			if (!in_array($ebay_carrier, $no_express_delivery)){
				$final_status = 658;
			}else {
				$final_status = 661;
			}
			$sql = "UPDATE ebay_order SET ebay_status='$final_status' WHERE ebay_id ='$ebay_id' ";
			$log_data .= "[".date("Y-m-d H:i:s")."]\t自动跳转---{$ebay_id}---的状态为---$final_status!\n\n";
			$dbcon->execute($sql) or die("Fail : $sql");
			//$order_statistics->replaceData($order_sn, array('mask'=>1), array('mask'=>1));
			insert_mark_shipping($ebay_id);
			write_log('contrast_intercept_'.date("Ymd").'/'.date("H").'.txt', $log_data."\n\n");
			continue;
		}else if($count_record_details < $count_orderdetaillist){
			//更新至自动部分发货状态
			if (!in_array($ebay_carrier, $no_express_delivery)){
				//$final_status = 659;
				$final_status = 640; //modified by Herman.Xi @ 20130401
			}else {
				$final_status = 660;
			}
			$sql = "UPDATE ebay_order SET ebay_status='$final_status' WHERE ebay_id ='$ebay_id' ";
			$log_data .= "[".date("Y-m-d H:i:s")."]\t自动跳转---{$ebay_id}---的状态为---$final_status!\n\n";
			$dbcon->execute($sql) or die("Fail : $sql");
			//$order_statistics->replaceData($order_sn, array('mask'=>1), array('mask'=>1));
			insert_mark_shipping($ebay_id);
			write_log('contrast_intercept_'.date("Ymd").'/'.date("H").'.txt', $log_data."\n\n");
			continue;
		}else if($count_record_details == $count_orderdetaillist){
			//正常发货状态
			if(in_array($ebay_account,$SYSTEM_ACCOUNTS['ebay平台'])){
				if($ebay_note != ''){
					$final_status = 593;
				}else{
					$final_status = 1;
				}
			}else if(in_array($ebay_account,$SYSTEM_ACCOUNTS['海外销售平台'])){
				$final_status = 629; //德国订单
				$sql = "UPDATE ebay_order SET ebay_status='$final_status' WHERE ebay_id ='$ebay_id' ";
				$log_data .= "[".date("Y-m-d H:i:s")."]\t自动跳转---{$ebay_id}---的状态为---$final_status!\n\n";
				$dbcon->execute($sql) or die("Fail : $sql");
				//$order_statistics->replaceData($order_sn, array('mask'=>1), array('mask'=>1));
				write_log('contrast_intercept_'.date("Ymd").'/'.date("H").'.txt', $log_data."\n\n");
				continue;
			}else if(in_array($ebay_account, $SYSTEM_ACCOUNTS['aliexpress']) || in_array($ebay_account, $SYSTEM_ACCOUNTS['B2B外单'])){
				if($ebay_note != ''){
					$final_status = 593;
				}else{
					$final_status = 595;
				}
				if(in_array($ebay_countryname, array('Russian Federation', 'Russia')) && strpos($ebay_carrier, '中国邮政')!==false && str_word_count($ebay_username) < 2){
					$final_status = 683;
					$sql = "UPDATE ebay_order SET ebay_status='$final_status' WHERE ebay_id ='$ebay_id' ";
					$log_data .= "[".date("Y-m-d H:i:s")."]\t自动跳转---{$ebay_id}---的状态为---$final_status!\n\n";
					$dbcon->execute($sql) or die("Fail : $sql");
					//$order_statistics->replaceData($order_sn, array('mask'=>1), array('mask'=>1));
					write_log('contrast_intercept_'.date("Ymd").'/'.date("H").'.txt', $log_data."\n\n");
					continue;
				}
			}else if(in_array($ebay_account, $SYSTEM_ACCOUNTS['DHgate'])){
				$final_status = 620;	
			}else if(in_array($ebay_account, $SYSTEM_ACCOUNTS['dresslink'])){
				$final_status = 1;
			}else if(in_array($ebay_account, $SYSTEM_ACCOUNTS['cndirect'])){
				$final_status = 1;
			}else{
				$final_status = 1;
			}
			if(judge_contain_combinesku($order_sn)){
				$final_status = 606;
			}
			
			if($totalweight > 2){
				$final_status = 608;
			}
			if (!in_array($ebay_carrier, $no_express_delivery)){
				if(in_array($ebay_account,$SYSTEM_ACCOUNTS['ebay平台']) || in_array($ebay_account,$SYSTEM_ACCOUNTS['海外销售平台'])){
					$final_status = 641;//ebay和海外都跳转到 待打印线下和异常订单
				}else{
					$final_status = 639;
				}
			}
			$sql = "UPDATE ebay_order SET ebay_status='$final_status' WHERE ebay_id ='$ebay_id' ";
			$log_data .= "[".date("Y-m-d H:i:s")."]\t自动跳转---{$ebay_id}---的状态为---$final_status!\n\n";
			$dbcon->execute($sql) or die("Fail : $sql");
			//$order_statistics->replaceData($order_sn, array('mask'=>1), array('mask'=>1));
			write_log('contrast_intercept_'.date("Ymd").'/'.date("H").'.txt', $log_data."\n\n");
		}else{
			$log_data .= "[".date("Y-m-d H:i:s")."]\t订单$ebay_id同步状态有误，请联系IT解决！";	
		}
	}
	if(!empty($log_data)){
		write_log('contrast_intercept_'.date("Ymd").'/'.date("H").'.txt', $log_data."\n\n");
	}
}

function judge_contain_combinesku($order_sn){
	//判断订单是否包含组合料号
	global $dbcon, $user;
	
	$sql = "SELECT sku,ebay_amount FROM ebay_orderdetail WHERE ebay_ordersn='{$order_sn}'";
	$sql = $dbcon->execute($sql);
	$orderdetaillist = $dbcon->getResultArray($sql);
	$iszuhe	= false;		
	foreach ($orderdetaillist AS $orderdetail){
		$sku = $orderdetail['sku'];
		$rr	= "select * from ebay_productscombine where ebay_user='$user' and goods_sn='$sku'";
		$rr	= $dbcon->execute($rr);
		$rr = $dbcon->getResultArray($rr);
		if(count($rr) > 0) $iszuhe = true;
	}
	return $iszuhe;
}

function judge_combinesku($sku){
	//判断料号是否为组合料号
	global $dbcon, $user;
	
	$iscombine = false;
	$rr	= "select * from ebay_productscombine where ebay_user='$user' and goods_sn='$sku'";
	$rr	= $dbcon->execute($rr);
	$rr = $dbcon->getResultArray($rr);
	if(count($rr) > 0) $iscombine = true;
	return $iscombine;
}

function check_sku($sku, $num,$order_sn){
	//检索料号是否缺货
	global $dbcon;

	$sku = trim($sku); //为了保险起见
	
	$sql = "SELECT o.goods_count,g.cguser FROM ebay_goods AS g LEFT JOIN ebay_onhandle AS o ON o.goods_sn=g.goods_sn WHERE o.goods_sn='{$sku}'";
	$sql		= $dbcon->execute($sql);
	$goodsinfo = $dbcon->getResultArray($sql);

	//$testlog = var_export($goodsinfo,TRUE);

	if (empty($goodsinfo)||empty($goodsinfo[0]['cguser'])){
		return true;
	}
	
	
	$sql = "SELECT * FROM ebay_sku_statistics WHERE sku='{$sku}'";
	$sql = $dbcon->execute($sql);
	$sku_info = $dbcon->getResultArray($sql);
	if(empty($sku_info)){
		return true;
	}


//超大订单拦截
	$takenum = ceil($sku_info[0]['everyday_sale']*10);
	if ($num>9&&$num>$takenum){
		write_log('check_is_intercept_'.date("Ymd").'/'.date("H").'.txt', "拦截的{$sku}当前均量：".$sku_info[0]['everyday_sale']."\n\n订单号".$order_sn."\n\n");
		return false;
	}

	$actuallaygoods = $goodsinfo[0]['goods_count'];
	$goods_bili = $num / $actuallaygoods;
	if($actuallaygoods <= 0){
		/*if ($num>9&&$num>$takenum){
			write_log('check_is_intercept_'.date("Ymd").'/'.date("H").'.txt', "拦截的{$sku}当前均量：".$sku_info[0]['everyday_sale']."\n\n订单号".$order_sn."\n\n");
			return false;
		}*/
	}else if($goods_bili > 0.5 && $num>$takenum && $takenum >0){
		write_log('check_is_intercept_'.date("Ymd").'/'.date("H").'.txt', "拦截的{$sku}当前均量：".$sku_info[0]['everyday_sale']."\n\n订单号".$order_sn."\n\n");
		return false;
	}


	return true;
}


function get_sku_info($sku){
	//获取单料号库存和待发货信息
	global $dbcon;
	
	//$sql = "SELECT cguser FROM ebay_goods WHERE goods_sn='{$sku}'";
	$sql = "SELECT o.goods_count,g.cguser FROM ebay_goods AS g LEFT JOIN ebay_onhandle AS o ON o.goods_sn=g.goods_sn WHERE o.goods_sn='{$sku}'";
	$sql		= $dbcon->execute($sql);
	$goodsinfo = $dbcon->getResultArray($sql);
	
	if (empty($goodsinfo)||empty($goodsinfo[0]['cguser'])){
		return array();
	}
	$sql = "SELECT * FROM ebay_sku_statistics WHERE sku='{$sku}'";
	$sql = $dbcon->execute($sql);
	$sku_info = $dbcon->getResultArray($sql);
	
	$purchaseinfo = !empty($sku_info) ? $sku_info[0] : array();
	$purchaseinfo['realnums'] = $goodsinfo[0]['goods_count'];
	
	return $purchaseinfo;
}
function get_realskuinfo($sku){
	//获取料号下详细信息
	global $dbcon,$truename;
	
	$sql = "SELECT goods_sncombine FROM ebay_productscombine WHERE goods_sn ='{$sku}'";
	$sql = $dbcon->execute($sql);
	$combinelists = $dbcon->fetch_one($sql);
	
	if (empty($combinelists)){
		return array($sku=>1);
	}
	$results = array();
	if (strpos($combinelists['goods_sncombine'], ',')!==false){
		$skulists = explode(',', $combinelists['goods_sncombine']);
		foreach ($skulists AS $skulist){
			list($_sku, $snum) = strpos($skulist, '*')!==false ? explode('*', $skulist) : array($skulist, 1);
			$results[$_sku] = $snum;
		}
	}else if (strpos($combinelists['goods_sncombine'], '*')!==false){
		list($_sku, $snum) = explode('*', $combinelists['goods_sncombine']);
		$results[$_sku] = $snum;
	}else{
		$results[$sku] = 1;
	}
	return $results;
}
function get_purchase_info($order_sn){
	//获取订单下真实料号情况
	global $dbcon;
	
	$results = array();
	$sql = "SELECT sku,ebay_amount FROM ebay_orderdetail WHERE ebay_ordersn='{$order_sn}'";
	$sql		= $dbcon->execute($sql);
	$orderdetaillist = $dbcon->getResultArray($sql);
	foreach ($orderdetaillist AS $orderdetail){
		$sku_arr = get_realskuinfo($orderdetail['sku']);
		foreach($sku_arr as $or_sku => $or_nums){
			$results[$or_sku] = get_sku_info($or_sku);
		}
	}
	return $results;
}
function getsaleandnosendall($sku, $storeid){
	//获取虚拟/待发货库存
	global $dbcon;
	
	$totalnums = 0;
	$combineskus = get_combinesku($sku);
	$skus = empty($combineskus) ? array() : array_keys($combineskus);
	array_push($skus, $sku);

	foreach ($skus AS $_sku){
		$realtimes = 1;
		if ($_sku!=$sku&&$combineskus[$_sku]){
			$skulist = explode(',', $combineskus[$_sku]);
			foreach ($skulist AS $sku_info){
				list($_s,$times) = explode('*', $sku_info);
				if ($_s==$sku){
					$realtimes = $times;
				}
			}
			
		}else{
			$realtimes = 1;
		}
		$sql = "SELECT sum(b.ebay_amount) AS qty 
					FROM ebay_order AS a 
					LEFT JOIN ebay_orderdetail AS b ON a.ebay_ordersn = b.ebay_ordersn 
					WHERE a.ebay_status NOT IN (0, 2, 613, 615, 617, 625, 640, 642,652, 653, 654, 663,667,658,659,660,661,669,670,673,674,681,612,624,671)
					AND b.sku='{$_sku}'
					AND a.ebay_combine!='1'
					LIMIT 1"; //增加658和661 状态自动拦截 ,653 ,624,671 2013-04-06 不算待发货状态 add by xiaojinhua 
		$sql = $dbcon->execute($sql);
		$skunums = $dbcon->getResultArray($sql);

		$tendaytime = time() -10*24*60*60;
		$sql1 = "SELECT sum(b.ebay_amount) AS qty 
					FROM ebay_order AS a 
					LEFT JOIN ebay_orderdetail AS b ON a.ebay_ordersn = b.ebay_ordersn 
					WHERE a.ebay_status IN (612)
					AND b.sku='{$_sku}'
					AND a.ebay_combine!='1'
					AND a.ebay_paidtime > {$tendaytime}
					LIMIT 1"; // add by xiaojinhua 修改612暂不寄计算待发货方式
		$sql1 = $dbcon->execute($sql1);
		$skunums1 = $dbcon->getResultArray($sql1);

		$sql2 = "SELECT sum(b.ebay_amount) AS qty 
					FROM ebay_order AS a 
					LEFT JOIN ebay_orderdetail AS b ON a.ebay_ordersn = b.ebay_ordersn 
					LEFT JOIN ebay_order_scan_record as c ON a.ebay_id = c.ebay_id 
					WHERE a.ebay_status IN (624,671)
					AND b.sku='{$_sku}'
					AND a.ebay_combine!='1'
					AND	(c.is_scan=0 or c.is_scan is null) 
					LIMIT 1"; // add by xiaojinhua 修改624,671计算待发货方式
		$sql2 = $dbcon->execute($sql2);
		$skunums2 = $dbcon->getResultArray($sql2);
		if (!empty($skunums)){
			$totalnums += (int)$skunums[0]['qty']*$realtimes;
			$totalnums += (int)$skunums1[0]['qty']*$realtimes;
			$totalnums += (int)$skunums2[0]['qty']*$realtimes;
		}
	}
	return $totalnums;
}
function getauditingall($sku, $storeid){
	//获取待审核数量
	global $dbcon;
	
	$totalnums = 0;
	$combineskus = get_combinesku($sku);
	$skus = empty($combineskus) ? array() : array_keys($combineskus);
	array_push($skus, $sku);

	foreach ($skus AS $_sku){
		$realtimes = 1;
		if ($_sku!=$sku&&$combineskus[$_sku]){
			$skulist = explode(',', $combineskus[$_sku]);
			foreach ($skulist AS $sku_info){
				list($_s,$times) = explode('*', $sku_info);
				if ($_s==$sku){
					$realtimes = $times;
				}
			}
			
		}else{
			$realtimes = 1;
		}
		$sql = "SELECT sum(b.ebay_amount) AS qty 
					FROM ebay_order AS a 
					LEFT JOIN ebay_orderdetail AS b ON a.ebay_ordersn = b.ebay_ordersn 
					WHERE a.ebay_status=640
					AND b.sku='{$_sku}'
					AND a.ebay_combine!='1'
					LIMIT 1";
		$sql = $dbcon->execute($sql);
		$skunums = $dbcon->getResultArray($sql);
		if (!empty($skunums)){
			$totalnums += $skunums[0]['qty']*$realtimes;
		}
	}
	return $totalnums;
}
function getinterceptall($sku, $storeid){
	//获取已拦截数量
	global $dbcon;
	
	$totalnums = 0;
	$combineskus = get_combinesku($sku);
	$skus = empty($combineskus) ? array() : array_keys($combineskus);
	array_push($skus, $sku);

	foreach ($skus AS $_sku){
		$realtimes = 1;
		if ($_sku!=$sku&&$combineskus[$_sku]){
			$skulist = explode(',', $combineskus[$_sku]);
			foreach ($skulist AS $sku_info){
				list($_s,$times) = explode('*', $sku_info);
				if ($_s==$sku){
					$realtimes = $times;
				}
			}
			
		}else{
			$realtimes = 1;
		}
		$sql = "SELECT sum(b.ebay_amount) AS qty 
					FROM ebay_order AS a 
					LEFT JOIN ebay_orderdetail AS b ON a.ebay_ordersn = b.ebay_ordersn 
					WHERE a.ebay_status=642
					AND b.sku='{$_sku}'
					AND a.ebay_combine!='1'
					LIMIT 1";
		$sql = $dbcon->execute($sql);
		$skunums = $dbcon->getResultArray($sql);
		if (!empty($skunums)){
			$totalnums += $skunums[0]['qty']*$realtimes;
		}
	}
	return $totalnums;
}


//add by xiaojinhua
function get_autointercept($sku, $storeid){
	
	global $dbcon;
	
	$totalnums = 0;
	$combineskus = get_combinesku($sku);
	$skus = empty($combineskus) ? array() : array_keys($combineskus);
	array_push($skus, $sku);

	foreach ($skus AS $_sku){
		$realtimes = 1;
		if ($_sku!=$sku&&$combineskus[$_sku]){
			$skulist = explode(',', $combineskus[$_sku]);
			foreach ($skulist AS $sku_info){
				list($_s,$times) = explode('*', $sku_info);
				if ($_s==$sku){
					$realtimes = $times;
				}
			}
			
		}else{
			$realtimes = 1;
		}
		$sql = "SELECT sum(b.ebay_amount) AS qty 
					FROM ebay_order AS a 
					LEFT JOIN ebay_orderdetail AS b ON a.ebay_ordersn = b.ebay_ordersn 
					WHERE a.ebay_status in (658,661)
					AND b.sku='{$_sku}'
					AND a.ebay_combine!='1'
					LIMIT 1";
		$sql = $dbcon->execute($sql);
		$skunums = $dbcon->getResultArray($sql);
		if (!empty($skunums)){
			$totalnums += $skunums[0]['qty']*$realtimes;
		}
	}
	return $totalnums;
}

//add by xiaojinhua end

function get_partsalenosend($sku, $storeid){
	//获取在部分包货中的数量
	global $dbcon;
	
	$packagingnums = 0;
	$interceptnums = 0;
	$combineskus = get_combinesku($sku);
	$skus = empty($combineskus) ? array() : array_keys($combineskus);
	array_push($skus, $sku);
	
	foreach ($skus AS $_sku){
		$realtimes = 1;
		if ($_sku!=$sku&&$combineskus[$_sku]){
			$skulist = explode(',', $combineskus[$_sku]);
			foreach ($skulist AS $sku_info){
				list($_s,$times) = explode('*', $sku_info);
				if ($_s==$sku){
					$realtimes = $times;
				}
			}
			
		}else{
			$realtimes = 1;
		}
		$sql = "SELECT a.ebay_id,b.ebay_amount FROM ebay_order AS a 
					LEFT JOIN ebay_orderdetail AS b ON a.ebay_ordersn=b.ebay_ordersn 
					WHERE a.ebay_userid !='' 
					AND a.ebay_status IN (652,653, 654) 
					AND a.ebay_combine!='1' 
					AND b.sku='{$_sku}'";
		$sql = $dbcon->execute($sql);
		$lists = $dbcon->getResultArray($sql);
		foreach ($lists AS $list){
			$checksql = "SELECT check_status ,ebaydetail_id FROM ebay_unusual_order_check WHERE ebay_id={$list['ebay_id']} AND sku='{$_sku}'";
			$checksql = $dbcon->execute($checksql);
			$checksql = $dbcon->getResultArray($checksql);
			
			if (empty($checksql)||$checksql[0]['check_status']==2){
				$interceptnums += $realtimes*$list['ebay_amount'];
				continue;
			}
			$schecksql = "SELECT realnum,totalnum FROM ebay_packing_status WHERE ebay_orderdetail='{$checksql['ebaydetail_id']}' AND sku='{$sku}'";
			$schecksql = $dbcon->execute($schecksql);
			$schecksql = $dbcon->getResultArray($schecksql);
			if (!empty($schecksql)&&$schecksql[0]['realnum']==$schecksql[0]['totalnum']){
				continue;
			}else if(!empty($schecksql)&&$schecksql[0]['realnum']<$schecksql[0]['totalnum']){
				$packagingnums += $realtimes*($schecksql[0]['totalnum']-$schecksql[0]['realnum']);
			}else{
				//$packagingnums += $realtimes*$list['ebay_amount'];
				$packagingnums += $realtimes*($schecksql[0]['totalnum']-$schecksql[0]['realnum']);
			}
		}
	}
	return array($packagingnums,$interceptnums);
}
function getorderbysku($sku){
	//通过sku获取不在已经发货或者拦截审核里面的订单
	global $dbcon;
	
	$results = array();
	$combineskus = get_combinesku($sku);
	$skus = empty($combineskus) ? array() : array_keys($combineskus);
	array_push($skus, $sku);

	foreach ($skus AS $_sku){
		$sql = "SELECT a.ebay_id 
					FROM ebay_order AS a 
					LEFT JOIN ebay_orderdetail AS b ON a.ebay_ordersn = b.ebay_ordersn  
					WHERE a.ebay_status NOT IN (0, 2, 613, 615, 617, 625, 640, 642, 652, 654, 663)
					AND b.sku='{$_sku}'
					AND a.ebay_combine!='1'";
		$sql = $dbcon->execute($sql);
		$ebay_ids = $dbcon->getResultArray($sql);
		if (!empty($ebay_ids)){
			foreach ($ebay_ids AS $ebay_id){
				$results[] = $ebay_id['ebay_id'];
			}
		}
	}
	return $results;
}
function get_ordersn(){
	
	global $dbcon,$purchasenumber;
	
	while (1){
		if(strpos($purchasenumber, '_')!==false){
			list($fname, $snumber) = explode('_', $purchasenumber);
			$io_ordersn = $fname.date("ymd").$snumber.rand(100, 999);
		}else{
			$io_ordersn = "SWB".date("ymd").$purchasenumber.rand(100, 999);
		}
		$sql = "SELECT io_ordersn FROM ebay_iostore WHERE io_ordersn='{$io_ordersn}'";
		$result = $dbcon->query($sql);
		$num = $dbcon->num_rows($result);
		if ($num==0){
			return $io_ordersn;
		}
	}
}

function getSaleProducts($start,$end,$sku,$storeid,$esale){
	
	global $dbcon,$truename;
	
	$totalnums = 0;
	$combineskus = get_combinesku($sku);
	$skus = empty($combineskus) ? array() : array_keys($combineskus);
	array_push($skus, $sku);
	foreach ($skus AS $_sku){
		$realtimes = 1;
		if ($_sku!=$sku&&$combineskus[$_sku]){
			$skulist = explode(',', $combineskus[$_sku]);
			foreach ($skulist AS $sku_info){
				list($_s,$times) = explode('*', $sku_info);
				if ($_s==$sku){
					$realtimes = $times;
				}
			}
			
		}else{
			$realtimes = 1;
		}
		$maxnums = $esale>=5 ? ceil(10*$esale/$realtimes) : 50;
		$sql = "SELECT sum(b.ebay_amount) as qty 
					FROM ebay_order AS a 
					LEFT JOIN ebay_orderdetail AS b ON a.ebay_ordersn = b.ebay_ordersn 
					WHERE a.ebay_status NOT IN (0, 615, 617, 625, 663)
					AND a.ebay_paidtime>{$start} 
					AND a.ebay_paidtime<{$end} 
					AND b.sku='{$_sku}'
					AND b.ebay_amount<{$maxnums}
					LIMIT 1";
		$sql = $dbcon->execute($sql);
		$skunums = $dbcon->getResultArray($sql);
		if (!empty($skunums)){
			$totalnums += $skunums[0]['qty']*$realtimes;
		}
	}
	return $totalnums;
}

/* 已订购库存 */
function stockbookused($goods_sn,$storeid,$cguser){

	global $dbcon;
	$gsql	 = "SELECT SUM(b.goods_count) as qty FROM ebay_iostore AS a 
						LEFT JOIN ebay_iostoredetail AS b ON a.io_ordersn = b.io_ordersn
						WHERE b.goods_sn = '{$goods_sn}' 
						AND (a.io_status ='0' or a.io_status ='1') 
						AND type ='2' 
						AND a.io_purchaseuser='{$cguser}'
						AND a.io_warehouse={$storeid}
						LIMIT 1";
	$gsql	 = $dbcon->execute($gsql);
	$gsql	 = $dbcon->getResultArray($gsql);
	$usedqty =  $gsql[0]['qty']?$gsql[0]['qty']:0;
	
	$gsql 	 = "SELECT SUM(b.goods_count) AS ordernum,SUM(b.stockqty) AS reachnum FROM ebay_iostore AS a 
						LEFT JOIN ebay_iostoredetail AS b ON a.io_ordersn = b.io_ordersn
						WHERE b.goods_sn = '{$goods_sn}' 
						AND a.io_status ='3' 
						AND type ='2' 
						AND a.io_warehouse={$storeid} 
						LIMIT 1";
	$gsql	 = $dbcon->execute($gsql);
	$gsql	 = $dbcon->fetch_one($gsql);
	if (!empty($gsql)){
		$usedqty = $usedqty+$gsql['ordernum']-$gsql['reachnum'];
	}
	return $usedqty;	
}



function getaccountsale($sku, $accounts){
	
	global $dbcon;
	
	$totalnums = 0;
	$combineskus = get_combinesku($sku);
	$mouthtime = strtotime(date("Y-m").'-1 00:00:01');
	$skus = empty($combineskus) ? array() : array_keys($combineskus);
	array_push($skus, $sku);
	$account_sql = implode(',', array2strarray($accounts));
	foreach ($skus AS $_sku){
		$realtimes = 1;
		if ($_sku!=$sku&&$combineskus[$_sku]){
			$skulist = explode(',', $combineskus[$_sku]);
			foreach ($skulist AS $sku_info){
				list($_s,$times) = explode('*', $sku_info);
				if ($_s==$sku){
					$realtimes = $times;
				}
			}
			
		}else{
			$realtimes = 1;
		}
		$sql = "SELECT sum(b.ebay_amount) AS qty 
					FROM ebay_order AS a 
					LEFT JOIN ebay_orderdetail AS b ON a.ebay_ordersn = b.ebay_ordersn 
					WHERE a.ebay_status=2
					AND b.sku='{$_sku}'
					AND a.ebay_account IN ({$account_sql})
					AND a.scantime>{$mouthtime}
					LIMIT 1";
		$sql = $dbcon->execute($sql);
		$skunums = $dbcon->getResultArray($sql);
		if (!empty($skunums)){
			$totalnums += $skunums[0]['qty']*$realtimes;
		}
	}
	return $totalnums;
}

// 检查采购订单是否已经全部入库 
function checkreachgoodsover($io_ordersn){
	
	global $dbcon;

	$sql = "SELECT io_status FROM ebay_iostore WHERE io_ordersn ='{$io_ordersn}' LIMIT 1";
	$sql = $dbcon->execute($sql);
	$order = $dbcon->fetch_one($result);
	if (!in_array($order['io_status'], array(2,3))){
		return false;
	}
	
	$sql = "SELECT stockqty,goods_count FROM ebay_iostoredetail WHERE io_ordersn ='{$io_ordersn}' LIMIT 1";
	$sql = $dbcon->execute($sql);
	$otherskus = $dbcon->getResultArray($sql);
	$status = 1;
	foreach ($otherskus AS $othersku){
		if($othersku['stockqty']!=$othersku['goods_count']){
			$status	= 0;
			break;
		}
	}
	if($status==1&&$order['io_status']==3){
		$sql = "update ebay_iostore set io_status=2 where io_ordersn ='{$io_ordersn}'";
		$dbcon->execute($sql);
		return true;
	}else if($status==0&&$order['io_status']==2){
		$sql = "update ebay_iostore set io_status=3 where io_ordersn ='{$io_ordersn}'";
		$dbcon->execute($sql);
		return true;
	}
	return false;
}
?>
