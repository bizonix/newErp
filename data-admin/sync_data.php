<?php
include "dbconnect.php";
include_once "shipping_calc_fun.php";
include_once "ebay_order_cron_func.php";
include_once "class.curl.php";
$curl = new CURL();
$m = new MongoClient('mongodb://localhost:20000/');
$db = $m->selectDB("bigdata");
/*
$m->bigdata->collection->insert(
        [ 'client' => 'awesome' ], // ← document
        [ 'w' => 0 ]  // ← don't acknowledge writes for this insert
	);
 */

$dbcon = new DBClass();
/*
$sql = "SELECT * FROM `ebay_ordergrossrate` WHERE `is_effectiveorder` = 1 AND `is_delete` = 0 AND `order_scantime` BETWEEN '1404748800' AND '1404835199' ORDER BY `order_scantime` DESC limit 50,50000";
$sql = $dbcon->execute($sql);
$skuinfo = $dbcon->getResultArray($sql);
foreach($skuinfo as $item){
	$m->bigdata->ebay->insert($item);
}
 */

$startTimeStamp = 0;
$endTimeStamp   = 0;
$starttime = time();
$currentTime    = date('[ Y-m-d H:i:s ]');


$sql = "select model,price from ebay_packingmaterial";	
$sql = $dbcon->execute($sql);
$packinglists = $dbcon->getResultArray($sql);

foreach ($packinglists AS $packinglist){
	$packings[$packinglist['model']] = $packinglist['price'];
}
unset($packinglists);


//获取所有平台账号信息
$sql = "SELECT ebay_account,ebay_platform FROM ebay_account WHERE ebay_platform!='' ORDER BY ebay_platform ASC";
$sql  = $dbcon->execute($sql);
$eaccounts = $dbcon->getResultArray($sql);
$accounts = array();
$cn2enarr = array( //中文转英文
	'ebay平台' => 'ebay',
	'亚马逊' => 'amazon',
	'出口通' => 'chukoutong',
	'国内销售部' => 'guonei',
	'天猫哲果' => 'zegoo',
	'天猫芬哲' => 'fenjo',
	'海外仓' => 'oversea',
	'线下结算客户' => 'offline'
);
foreach ($eaccounts AS $eaccount){
	$enAccount = $cn2enarr[$eaccount['ebay_platform']];
	if(isset($enAccount)){
		$accounts[$enAccount][] = $eaccount['ebay_account'];
	}else{
		$accounts[$eaccount['ebay_platform']][] = $eaccount['ebay_account'];
	}
}


//建立名称和平台id的对应关系
$accountPlatform = array( 
	1=>"ebay",
	2=>"aliexpress",
	3=>"chukoutong",
	4=>"DHgate",
	8=>"cndirect",
	9=>"offline",
	10=>"dresslink",
	11=>"amazon",
	12=>"fenjo",
	13=>"zegoo",
	14=>"oversea",
	15=>"Newegg",
	16=>"guonei"
); 
//1.ebay 2.alipress 3.出口通 4.DHgate 6.B2B外单 8.cndirect 9.线下结算客户 10.dresslink 11.Amazon 12.天猫芬哲 
//13.天猫哲果 14.海外仓 15.Newegg 16.国内销售部


// 获取所有平台团队人员情况
$teamInfo_caigou = $db->teamInfo->find(array("teamType"=>"caigou"));
$team_caigou_arr = array();
foreach($teamInfo_caigou as $itemteam){
	$team_caigou_arr[$itemteam['teamLeader']] = $itemteam['member'];
}


$teamInfo_sale = $db->teamInfo->find(array("teamType"=>"sale"));
$team_sale_arr = array();
foreach($teamInfo_sale as $itemteam){
	$team_sale_arr[$itemteam['teamLeader']] = $itemteam['member'];
}


/*
$sql = "SELECT order_id FROM ebay_ordergrossrate_queue WHERE status=0 ORDER BY id DESC LIMIT 10";
$sql	 = $dbcon->query($sql);
$ids = $dbcon->getResultArray($sql);

$ids = _multi2single('order_id', $ids);
 */

//$sql = "SELECT ebay_id,recordnumber,ebay_ptid,ebay_ordersn,ebay_account,ebay_userid,ebay_currency,ebay_shipfee,ebay_countryname,orderweight2,ebay_carrier,scantime,ebay_total,is_main_order,is_sendreplacement FROM ebay_order WHERE ebay_status=2 AND ebay_combine!=1 AND ebay_id IN (".implode(',', $ids).") ORDER BY scantime ASC";
//


function calc_order($ebay_id,$table_name){
	global $dbcon,$db,$accounts,$packings,$team_sale_arr,$team_caigou_arr;
	$sql = "SELECT ebay_id,recordnumber,ebay_ptid,ebay_ordersn,ebay_account,ebay_userid,ebay_currency,ebay_shipfee,ebay_countryname,orderweight2,ebay_carrier,scantime,ebay_total,is_main_order,is_sendreplacement FROM ebay_order WHERE ebay_status=2 AND ebay_combine!=1 AND ebay_id={$ebay_id} ORDER BY scantime ASC";
	$sql		= $dbcon->query($sql);
	$orderlists	= $dbcon->getResultArray($sql);
	foreach($orderlists AS $key=>$orderlist){
		//获取订单所属的平台
		foreach($accounts as $platform => $accountarr){
			if(in_array($orderlist['ebay_account'],$accountarr)){
				$order_platform = $platform;
				break;
			}
		}

		if($order_platform != "ebay"){ // 只计算ebay 平台的
			echo $order_platform."\n"; 
			//return false;
			return 1;
		}



		//$idsk = array_search($orderlist ['ebay_userid'], $ids);
		//unset($ids[$idsk]);
		
		$orderlist ['ebay_total'] = floatval($orderlist ['ebay_total']);       //订单总收入     item总收入 + 运费收入Gross rate
		$orderlist ['ebay_shipfee'] = floatval($orderlist ['ebay_shipfee']);
		
		$orderinfos = array();
		$mainlist = array();
		$mainlist['order_scantime'] = $orderlist ['scantime'];
		$mainlist['order_id'] = $orderlist['ebay_id'];
		$mainlist['recordnumber'] = $orderlist ['recordnumber'];
		$mainlist['ptid'] = $orderlist ['ebay_ptid'];
		$mainlist['order_currency'] = $orderlist ['ebay_currency'];                              //币种
		$mainlist['order_countryname'] = $orderlist ['ebay_countryname'];                        //国家名称
		$mainlist['order_weight'] = $orderlist ['orderweight2']/1000;                            //实际称重重量
		$mainlist['sale_userid'] = $orderlist ['ebay_userid'];                        			 //国家名称
		$mainlist['send_account'] = $orderlist ['ebay_account'];								 //账号
		$mainlist['send_carrier'] = $orderlist ['ebay_carrier'];                                 //实际发货方式
		$mainlist['send_allshipfee'] = calctrueshippingfee($orderlist ['ebay_carrier'], $mainlist['order_weight'], $orderlist ['ebay_countryname'], $orderlist ['ebay_id']);                                //实际发货方式
		$mainlist['send_allshipfee'] = in_array($orderlist ['ebay_carrier'], array('Global Mail')) ? _HKD2CNY($mainlist['send_allshipfee']) : $mainlist['send_allshipfee'];

		if(in_array($orderlist ['ebay_carrier'], array('中国邮政平邮','中国邮政挂号','EUB','EMS'))){
			$mainlist['send_rebateshipfee'] = calctrueshippingfee2($orderlist ['ebay_carrier'], $mainlist['order_weight'], $orderlist ['ebay_countryname'], $orderlist ['ebay_id']); //计算折扣
		}else{
			$mainlist['send_rebateshipfee'] = $mainlist['send_allshipfee'];
		}
		// 打折后的运费
		$mainlist['is_copyorder']	= $orderlist ['is_main_order']==1 ? 1 : ($orderlist ['is_main_order']==2 ? 2 : 0);
		//$judge_is_splitorder = judge_is_splitorder($orderlist ['ebay_id']);

		$es_sql = "select * from ebay_splitorder as es where (split_order_id = '{$orderlist ['ebay_id']}' OR main_order_id = '{$orderlist ['ebay_id']}') ";
		$result = $dbcon->execute($es_sql);
		$result = $dbcon->fetch_one($result);
		if($result['mode'] == 0){
			$mainlist['is_splitorder'] = 1;
		}else{
			$mainlist['is_splitorder'] = 0;
		}

		if($result['mode'] == 4){
			$mainlist['is_suppleorder']	= 1;
		}else{
			$mainlist['is_suppleorder']	= 0;
		}

		if($result['mode'] == 2 || $result['mode'] == 7){
			$is_effectiveorder = 1;
		}else{
			$is_effectiveorder = 0;
		}

		$mainlist['is_effectiveorder']	= $is_effectiveorder;
		//$mainlist['is_suppleorder']	= $orderlist ['is_sendreplacement'];
		$mainlist['order_platform'] = $order_platform;
		$mainlist['order_sendZone'] = _getSendZone($orderlist ['ebay_id']); //订单发货分区
		$splitorder_log = func_readlog_splitorder($orderlist ['ebay_id']);
		$mainlist['splitorder_log'] = $splitorder_log!==false ? $splitorder_log+1 : 0;
		$sql				= "select * from ebay_orderdetail where ebay_ordersn='{$orderlist ['ebay_ordersn']}'";
		$sql				= $dbcon->query($sql);
		$detaillists		= $dbcon->getResultArray($sql);

		
		$combineppfee = array();
		$combineskutotal = array();
		$combineskuapportion = array();
		$combineskushippingfee = array();

		if(count($detaillists) == 1 && !_is_combinesku($detaillists[0]['sku'])){ // 非组合料号订单
			$detaillist = $detaillists[0];
			$detaillist['shipingfee'] = floatval($detaillist['shipingfee']);
			$detaillist['ebay_itemprice'] = floatval($detaillist['ebay_itemprice']);

			$goodsinfo	= _get_skuinfo($detaillist['sku']);

			$orderinfo = $mainlist;
			list($spu) = explode('_', $detaillist['sku']);
			$orderinfo['csku'] = '';

	// 增加获取对应的销售
			$skuInfo = _getSkuInfo($detaillist['sku']);
			$orderinfo['sku'] = $detaillist['sku'];
			$orderinfo['spu'] = $skuInfo['spu'];
			$orderinfo['order_number'] = 1; //订单数量
			$members = _getMemberFromSpu($skuInfo['spu']);
			$membersArr = $members[$skuInfo['spu']];
			foreach($membersArr as $pkey=>$item){
				if($accountPlatform[$pkey] == $order_platform){
					$orderinfo['salemember'] = $item['global_user_name'];
					break;
				}
			}


			foreach($team_sale_arr as $teamname=> $team_arr){
				if(in_array($orderinfo['salemember'],$team_arr)){
					$orderinfo['sale_team'] = $teamname;
					break;
				}
			}

			$orderinfo['order_type'] = '普通';
			$orderinfo['order_total'] = $detaillist['ebay_itemprice']*intval($detaillist['ebay_amount']);                                    //订单总收入     item总收入 + 运费收入Gross rate
			$orderinfo['order_shipfee'] = $detaillist['shipingfee'];
			$orderinfo['order_usdtotal'] = round(_other2USD($orderlist ['ebay_currency'], $orderinfo ['order_total']+$orderinfo['order_shipfee']), 3);
			//$orderinfo['order_ppfee'] = in_array($orderlist ['ebay_account'], $EBAY_ACCOUNTS_CONFIG) ? round(_other2USD($orderlist ['ebay_currency'], _get_PPfee($orderinfo['order_total']+$orderinfo['order_shipfee'])), 3) : 0;  
			if(in_array($orderlist ['ebay_account'], $accounts['ebay'])){
				$order_ppfee = _other2USD($orderlist ['ebay_currency'], _get_PPfee($orderinfo['order_total']+$orderinfo['order_shipfee']));
				$orderinfo['order_ppfee'] = round($order_ppfee, 3);
			}else{
				$orderinfo['order_ppfee'] = 0;
			}
			$orderinfo['order_cnytotal'] = round(_USD2CNY($orderinfo['order_usdtotal']-$orderinfo['order_ppfee']), 3);
			$orderinfo['sell_count'] = intval($detaillist['ebay_amount']);
			$orderinfo['sell_skuprice'] = $orderinfo['order_usdtotal'];
			$orderinfo['sell_cskuprice'] = 0;
			$orderinfo['sell_onlineskuprice'] = $detaillist['ebay_itemprice']*intval($detaillist['ebay_amount']);
			$orderinfo['sell_onlineskushipfee'] = $detaillist['shipingfee'];
			$orderinfo['sku_cost'] = $goodsinfo['goods_cost']*$orderinfo['sell_count'];
			$orderinfo['sku_purchase'] = $goodsinfo['cguser'];

			//获取采购所在team 名字

			foreach($team_caigou_arr as $teamname=> $team_arr){
				if(in_array($orderinfo['sku_purchase'],$team_arr)){
					$orderinfo['caigou_team'] = $teamname;
					break;
				}
			}

			$orderinfo['sku_weight'] = $goodsinfo['goods_weight']*$orderinfo['sell_count'];
			$orderinfo['sku_packing'] = $goodsinfo['ebay_packingmaterial'];
			$orderinfo['sku_packingcost'] = isset($packings[$goodsinfo['ebay_packingmaterial']]) ? $packings[$goodsinfo['ebay_packingmaterial']] : 0;
			$orderinfo['sku_processingcost'] = sku_processingcost($orderinfo['sell_count']);
			$orderinfo['is_register'] = is_registershipping($mainlist['send_carrier']); //是否挂号
			$orderinfo['order_grossrate'] =$orderinfo['order_cnytotal']-$orderinfo['sku_cost']-$orderinfo['sku_packingcost']-$orderinfo['sku_processingcost']-$orderinfo['send_rebateshipfee'];
			$orderinfo['order_skugrossrate'] = $orderinfo['order_grossrate'];
			$orderinfo['order_cskugrossrate'] = 0;
			$orderinfo['order_grossmarginrate'] = round($orderinfo ['order_grossrate']*(1-0.135)/$orderinfo['order_cnytotal'], 5);
			//$orderinfo['is_effective'] = $orderlist ['ebay_shipfee']==$detaillist ['shipingfee']&&$orderlist ['ebay_total'].''==($orderlist ['ebay_shipfee']+$orderinfo['sell_onlineskuprice']).'' ? 0 : 1;
//$orderlist ['ebay_total'] == ($orderlist ['ebay_shipfee'] + $orderinfo['sell_onlineskuprice']);

			$is_effective = array(); 
			if($orderlist ['ebay_shipfee'] == $detaillist ['shipingfee']){
				$is_effective['shipingfee'] = 0;
			}else{
				$is_effective['shipingfee'] = 1;
			}
			$tmpVal = $orderlist['ebay_shipfee'] + $orderinfo['sell_onlineskuprice'];
			//var_dump($orderlist['ebay_total'],$tmpVal);
			//var_dump($orderlist ['ebay_total'] == $tmpVal);
			//var_dump(md5($orderlist['ebay_total'])==md5($tmpVal));
			/*
			if($orderlist ['ebay_total'] == $tmpVal){
			}else{
				echo "#####################";
				$orderinfo['is_effective'] = 1;
			}
			 */
			if(bccomp($orderlist ['ebay_total'],$tmpVal,2) == 0){ //比较浮点数
				$is_effective['ebay_total'] = 0;
			}else{
				$is_effective['ebay_total'] = 1;
			}

			if($orderinfo['sku_weight']==0 ){
				$is_effective['sku_weight'] = 1;
			}else{
				$is_effective['sku_weight'] = 0;
			}

			if(empty($orderinfo['sku_purchase'])){
				$is_effective['sku_purchase'] = 1;
			}else{
				$is_effective['sku_purchase'] = 0;
			}

			if($orderinfo['sku_cost']==0){
				$is_effective['sku_cost'] = 1;
			}else{
				$is_effective['sku_cost'] = 0;
			}

			if(empty($orderinfo['order_currency'])){
				$is_effective['order_currency'] = 1;
			}else{
				$is_effective['order_currency'] = 0;
			}

			$effectiveNum = array_sum($is_effective);
			if($effectiveNum > 0){ // 异常订单
				$is_effective['order_id'] = $orderlist['ebay_id'];
				$is_effective['sku'] = $detaillist['sku'];
				print_r($is_effective);
				$db->ebay_unorder->insert($is_effective); //异常订单
				unset($is_effective);
				$orderinfo['is_effective'] = 1;
			}else{ //木有异常进行修复处理
				$orderinfo['is_effective'] = 0;
			}

			$orderinfos["{$detaillist['ebay_id']}_{$detaillist['sku']}"] = $orderinfo;
		}else{
			$orderinfo = $mainlist;
			$skutotal = 0;
			$skushippingtotal = 0;
			$mixweightlist = array();
			
			$orderinfo['csku'] = 0;
			$orderinfo['sku'] = 0;
			$orderinfo['spu'] = 0;
			$orderinfo['order_type'] = '组合订单';
			$orderinfo['order_total'] = $orderlist ['ebay_total'];                                    //订单总收入     item总收入 + 运费收入Gross rate
			$orderinfo['order_shipfee'] = $orderlist ['ebay_shipfee'];
			$orderinfo['order_usdtotal'] = 0;
			$orderinfo['order_ppfee'] = 0;  
			$orderinfo['order_cnytotal'] = 0;
			$orderinfo['sell_count'] = 0;//后续
			$orderinfo['sell_skuprice'] = 0;
			$orderinfo['sell_cskuprice'] = 0;
			$orderinfo['sell_onlineskuprice'] = 0;
			$orderinfo['sell_onlineskushipfee'] = 0;
			$orderinfo['sku_packingcost'] = 0;
			$orderinfo['sku_processingcost'] = 0;
			$orderinfo['sku_cost'] = 0;//后续
			$orderinfo['sku_purchase'] = '无';
			$orderinfo['sku_weight'] = 0;
			$orderinfo['sku_packing'] = '无';
			$orderinfo['sku_packingcost'] = 0;
			$orderinfo['sku_processingcost'] = 0;
			$orderinfo['is_register'] = is_registershipping($mainlist['send_carrier']);
			$orderinfo['order_grossrate'] =0;
			$orderinfo['order_skugrossrate'] = 0;
			$orderinfo['order_cskugrossrate'] = 0;
			$orderinfo['order_grossmarginrate'] = 0;

			$orderinfo['order_number'] = 1; //订单数量
			$orderinfos['main'] = $orderinfo;

			foreach($detaillists AS $dkey=>$detaillist){
				
				$detaillist['shipingfee'] = floatval($detaillist['shipingfee']);
				$detaillist['ebay_itemprice'] = floatval($detaillist['ebay_itemprice']);

				$sql	= "select goods_location,goods_weight,goods_cost,ebay_packingmaterial,cguser from ebay_goods where goods_sn='{$detaillist['sku']}'";
				$sql	= $dbcon->query($sql);
				$goodsinfo	= $dbcon->fetch_one($sql);

				$skutotal += $detaillist['ebay_itemprice']*$detaillist['ebay_amount'];
				$skushippingtotal += $detaillist ['shipingfee'];
				
				if(_is_combinesku($detaillist['sku'])){ // 组合料号

					$goodsinfos = array();
					$combinepricelist = array();
					$combineweightlist = array();
					$combinskus = get_realskuinfo($detaillist['sku']);
					foreach($combinskus AS $sku=>$count){
						$goodsinfo	= _get_skuinfo($sku);
						$goodsinfos[$sku] = $goodsinfo;
						$combinepricelist["{$detaillist['ebay_id']}_{$sku}"] = $goodsinfo['goods_cost']*$count;
						$combineweightlist["{$detaillist['ebay_id']}_{$sku}"] = $goodsinfo['goods_weight']*$count;
					}

					$combineppfee["{$detaillist['ebay_id']}"] = round(_other2USD($orderlist ['ebay_currency'], _get_PPfee($detaillist['ebay_itemprice']*$detaillist['ebay_amount']+$detaillist['shipingfee'])), 3);
					$combineskutotal["{$detaillist['ebay_id']}"] = $detaillist['ebay_itemprice']*$detaillist['ebay_amount'];
					$combineskushippingfee["{$detaillist['ebay_id']}"] = $detaillist['shipingfee'];
					foreach ($combinskus AS $sku=>$count){
						
						$orderinfo = $mainlist;
						
						list($spu) = explode('_', $sku);
						$goodsinfo = $goodsinfos[$sku];
						
						$orderinfo['csku'] = $detaillist['sku'];

						// 增加获取对应的销售
						//$skuInfo = _getSkuInfo($detaillist['sku']);
						//$orderinfo['sku'] = $detaillist['sku'];
						//$orderinfo['spu'] = $skuInfo['spu'];
						$cspu_tmp = explode("_",$detaillist['sku']);
						$cspu = $cspu_tmp[0];
						$members = _getMemberFromSpu($cspu);
						$membersArr = $members[$cspu];
						foreach($membersArr as $pkey=>$item){
							if($accountPlatform[$pkey] == $order_platform){
								$orderinfo['csalemember'] = $item['global_user_name']; //组合料号的销售
								break;
							}
						}

						foreach($team_sale_arr as $teamname=> $team_arr){
							if(in_array($orderinfo['csalemember'],$team_arr)){
								$orderinfo['csale_team'] = $teamname;
								break;
							}
						}

						$orderinfo['sku'] = $sku;
						$skuInfo = _getSkuInfo($sku);
						//$orderinfo['sku'] = $detaillist['sku'];
						$orderinfo['spu'] = $skuInfo['spu'];
						$members = _getMemberFromSpu($skuInfo['spu']);
						$membersArr = $members[$skuInfo['spu']];
						foreach($membersArr as $pkey=>$item){
							if($accountPlatform[$pkey] == $order_platform){
								$orderinfo['salemember'] = $item['global_user_name'];
								break;
							}
						}


						foreach($team_sale_arr as $teamname=> $team_arr){
							if(in_array($orderinfo['salemember'],$team_arr)){
								$orderinfo['sale_team'] = $teamname;
								break;
							}
						}
						$orderinfo['spu'] = $spu;
						$orderinfo['order_type'] = '组合';

						$orderinfo['order_number'] = 0; //订单数量
						$orderinfo['sell_count'] = intval($detaillist['ebay_amount'])*$count;
						$orderinfo['sku_cost'] = $goodsinfo['goods_cost']*$orderinfo['sell_count'];
						$orderinfo['sku_purchase'] = $goodsinfo['cguser'];


						//print_r($team_caigou_arr);
						foreach($team_caigou_arr as $teamname=> $team_arr){
							if(in_array($orderinfo['sku_purchase'],$team_arr)){
								$orderinfo['caigou_team'] = $teamname;
								break;
							}
						}

						$orderinfo['sku_weight'] = $goodsinfo['goods_weight']*$orderinfo['sell_count'];
						$orderinfo['sku_packing'] = $goodsinfo['ebay_packingmaterial'];
						$orderinfo['sku_packingcost'] = isset($packings[$goodsinfo['ebay_packingmaterial']]) ? $packings[$goodsinfo['ebay_packingmaterial']] : 0;
						$orderinfo['sku_processingcost'] = sku_processingcost($orderinfo['sell_count']);
						$orderinfo['is_register'] = 0;
						$orderinfos["{$detaillist['ebay_id']}_{$sku}"] = $orderinfo;
						$mixweightlist["{$detaillist['ebay_id']}_{$sku}"] = $goodsinfo['goods_weight']*$detaillist['ebay_amount'];
						$combineskuapportion["{$detaillist['ebay_id']}_{$sku}"] = $goodsinfo['goods_cost'];
					}
					unset($goodsinfos, $goodsinfo);
					
				}else{
					
					$orderinfo = $mainlist;
					list($spu) = explode('_', $detaillist['sku']);
					
					$orderinfo['csku'] = 0;
					$orderinfo['sku'] = $detaillist['sku'];
					$orderinfo['spu'] = $spu;

					// 增加获取对应的销售
					$skuInfo = _getSkuInfo($detaillist['sku']);
					$orderinfo['sku'] = $detaillist['sku'];
					$orderinfo['spu'] = $skuInfo['spu'];
					$members = _getMemberFromSpu($skuInfo['spu']);
					$membersArr = $members[$skuInfo['spu']];
					foreach($membersArr as $pkey=>$item){
						if($accountPlatform[$pkey] == $order_platform){
							$orderinfo['salemember'] = $item['global_user_name'];
							break;
						}
					}

					foreach($team_sale_arr as $teamname=> $team_arr){
						if(in_array($orderinfo['salemember'],$team_arr)){
							$orderinfo['sale_team'] = $teamname;
							break;
						}
					}

					$orderinfo['order_type'] = '组合';
					$orderinfo['order_total'] = $detaillist ['ebay_itemprice']*$detaillist ['ebay_amount'];
					$orderinfo['order_shipfee'] = $detaillist ['shipingfee'];
					$orderinfo['order_usdtotal'] = round(_other2USD($orderlist ['ebay_currency'], $orderinfo['order_total']+$orderinfo['order_shipfee']), 3);

					if(in_array($orderlist ['ebay_account'], $accounts['ebay'])){
						$order_ppfee = _other2USD($orderlist ['ebay_currency'], _get_PPfee($orderinfo['order_total']+$orderinfo['order_shipfee']));
						$orderinfo['order_ppfee'] = round($order_ppfee, 3);
					}else{
						$orderinfo['order_ppfee'] = 0;
					}

					$orderinfo['order_cnytotal'] = round(_USD2CNY($orderinfo['order_usdtotal']-$orderinfo['order_ppfee']), 3);
					$orderinfo['sell_count'] = intval($detaillist['ebay_amount']);
					$orderinfo['sell_skuprice'] = $orderinfo['order_usdtotal'];
					$orderinfo['sell_cskuprice'] = 0;
					$orderinfo['sell_onlineskuprice'] = $detaillist['ebay_itemprice']*intval($detaillist['ebay_amount']);
					$orderinfo['sell_onlineskushipfee'] = $detaillist['shipingfee'];
					$orderinfo['sku_cost'] = $goodsinfo['goods_cost']*$orderinfo['sell_count'];
					$orderinfo['sku_purchase'] = $goodsinfo['cguser'];
					$orderinfo['order_number'] = 0; //订单数量

					foreach($team_caigou_arr as $teamname=> $team_arr){
						if(in_array($orderinfo['sku_purchase'],$team_arr)){
							$orderinfo['caigou_team'] = $teamname;
							break;
						}
					}
					$orderinfo['sku_weight'] = $goodsinfo['goods_weight']*$orderinfo['sell_count'];
					$orderinfo['sku_packing'] = $goodsinfo['ebay_packingmaterial'];
					$orderinfo['sku_packingcost'] = isset($packings[$goodsinfo['ebay_packingmaterial']]) ? $packings[$goodsinfo['ebay_packingmaterial']] : 0;
					$orderinfo['sku_processingcost'] = sku_processingcost($orderinfo['sell_count']);
					$orderinfo['is_register'] = 0;
					$orderinfos["{$detaillist['ebay_id']}_{$detaillist['sku']}"] = $orderinfo;
					$mixweightlist["{$detaillist['ebay_id']}_{$detaillist['sku']}"] = $goodsinfo['goods_weight']*$detaillist['ebay_amount'];
				}
			}
			//var_dump( $orderlist['ebay_shipfee'], $skushippingtotal, $orderlist['ebay_total'].''==($orderlist['ebay_shipfee']+$skutotal).'' );
			$orderinfos['main']['order_total'] = $skutotal;
			$orderinfos['main']['order_shipfee'] = $skushippingtotal;
			//$orderinfos['main']['is_effective'] = &&$orderlist['ebay_total'].''==($orderlist['ebay_shipfee']+$skutotal).'' ? 0 : 32;
			$is_effective = array();
			if($orderlist['ebay_shipfee'] == $skushippingtotal){
				$is_effective['shipingfee'] = 0; 
			}else{
				$is_effective['shipingfee'] = 1; 
			}

			$tmpVal = $orderlist['ebay_shipfee'] + $skutotal;

			if(bccomp($orderlist ['ebay_total'],$tmpVal,2) == 0){ //比较浮点数
				$is_effective['ebay_total'] = 0;
			}else{
				$is_effective['ebay_total'] = 1;
			}

			if(array_sum($is_effective) > 0){
				$orderinfos['main']['is_effective'] = 1;
			}else{
				$orderinfos['main']['is_effective'] = 0;
			}

			$veryallweight = _calceveryweight($mixweightlist, $mainlist['order_weight']);
			$veryallshipfee = _calceveryweight($veryallweight, $mainlist['send_allshipfee']);
			$rebateallshipfee = _calceveryweight($veryallweight, $mainlist['send_rebateshipfee']);
			$_combineskuapportion = array();
			foreach ($combineskuapportion AS $cakey=>$cavalue){
				list($_detailid) = explode('_', $cakey);
				$_combineskuapportion[$_detailid][$cakey] = $cavalue+$rebateallshipfee[$cakey];
			}
			foreach ($combineskutotal AS $detailid=>$cskutotal){
				$combineppfees[$detailid] = _calceveryweight($_combineskuapportion[$detailid], $combineppfee[$detailid]);
				$combineskutotals[$detailid] = _calceveryweight($_combineskuapportion[$detailid], $combineskutotal[$detailid]);
				$combineskushippingfees[$detailid] = _calceveryweight($_combineskuapportion[$detailid], $combineskushippingfee[$detailid]);
			}
			//$combineorderprices = _calceveryweight($combinepricelist, $detaillist['ebay_itemprice']*$detaillist['ebay_amount']);
			foreach ($combineskuapportion AS $cakey=>$cavalue){
				list($_detailid) = explode('_', $cakey);
				$orderinfos[$cakey]['order_total'] = $combineskutotals[$_detailid][$cakey];
				$orderinfos[$cakey]['order_shipfee'] = $combineskushippingfees[$_detailid][$cakey];
				$orderinfos[$cakey]['order_usdtotal'] = round(_other2USD($orderinfos[$cakey] ['order_currency'], $orderinfos[$cakey]['order_total']+$orderinfos[$cakey]['order_shipfee']), 3);
				//$orderinfos[$cakey]['order_ppfee'] = in_array($orderlist ['ebay_account'], $EBAY_ACCOUNTS_CONFIG) ? $combineppfees[$_detailid][$cakey] : 0;//round(_other2USD($orderinfos[$cakey]['order_currency'], _get_PPfee($orderinfos[$cakey]['order_total'])), 3);


				if(in_array($orderlist ['ebay_account'], $accounts['ebay'])){
					$orderinfos[$cakey]['order_ppfee'] = $combineppfees[$_detailid][$cakey];
				}else{
					$orderinfos[$cakey]['order_ppfee'] = 0;
				}

				$orderinfos[$cakey]['order_cnytotal'] = round(_USD2CNY($orderinfos[$cakey]['order_usdtotal']-$orderinfos[$cakey]['order_ppfee']), 3);
				$orderinfos[$cakey]['sell_skuprice'] = $orderinfos[$cakey]['order_usdtotal']*0.7;
				$orderinfos[$cakey]['sell_cskuprice'] = $orderinfos[$cakey]['order_usdtotal']*0.3;
				$orderinfos[$cakey]['sell_onlineskuprice'] = $combineskutotals[$_detailid][$cakey];
				$orderinfos[$cakey]['sell_onlineskushipfee'] = $combineskushippingfees[$_detailid][$cakey];
			}
			foreach ($mixweightlist AS $mwkey=>$veryweight){

				if($orderinfos[$mwkey]['sku_weight']==0 ){
					$is_effective['sku_weight'] = 1;
				}else{
					$is_effective['sku_weight'] = 0;
				}

				if(empty($orderinfos[$mwkey]['sku_purchase'])){
					$is_effective['sku_purchase'] = 1;
				}else{
					$is_effective['sku_purchase'] = 0;
				}

				if($orderinfos[$mwkey]['sku_cost']==0){
					$is_effective['sku_cost'] = 1;
				}else{
					$is_effective['sku_cost'] = 0;
				}

				if(empty($orderinfos[$mwkey]['order_currency'])){
					$is_effective['order_currency'] = 1;
				}else{
					$is_effective['order_currency'] = 0;
				}

				$effectiveNum = array_sum($is_effective);
				if($effectiveNum > 0){ // 异常订单
					$is_effective['order_id'] = $mainlist['order_id'];
					$is_effective['csku'] = $orderinfos[$mwkey]['csku'];
					$is_effective['sku'] = $orderinfos[$mwkey]['sku'];
					print_r($is_effective);
					$db->ebay_unorder->insert($is_effective); //异常订单
					unset($is_effective);
					$orderinfos[$mwkey]['is_effective'] = 1;
				}else{ //木有异常进行修复处理
					$orderinfos[$mwkey]['is_effective'] = 0;
				}

				$orderinfos[$mwkey]['order_weight'] = $veryallweight[$mwkey];
				$orderinfos[$mwkey]['send_allshipfee'] = $veryallshipfee[$mwkey];
				$orderinfos[$mwkey]['send_rebateshipfee'] = $rebateallshipfee[$mwkey];
				$orderinfos[$mwkey]['order_grossrate'] = $orderinfos[$mwkey]['order_cnytotal']-$orderinfos[$mwkey]['sku_cost']-$orderinfos[$mwkey]['sku_packingcost']-$orderinfos[$mwkey]['sku_processingcost']-$orderinfos[$mwkey]['send_rebateshipfee'];
				$orderinfos[$mwkey]['order_skugrossrate'] = $orderinfos[$mwkey]['csku']!='' ? $orderinfos[$mwkey]['order_grossrate']*0.7 : $orderinfos[$mwkey]['order_grossrate'];
				$orderinfos[$mwkey]['order_cskugrossrate'] = $orderinfos[$mwkey]['csku']!='' ? $orderinfos[$mwkey]['order_grossrate']*0.3 : 0;
				$orderinfos[$mwkey]['order_grossmarginrate'] = round($orderinfos[$mwkey] ['order_grossrate']*(1-0.135)/$orderinfos[$mwkey]['order_cnytotal'], 5);
			}
		}
		print_r($orderinfos);
		foreach($orderinfos as $insertItem){
			//$db->$table_name->insert($insertItem);
		}
		return true;
	}
}



//

//$sql = "SELECT ebay_id,recordnumber,ebay_ptid,ebay_ordersn,ebay_account,ebay_userid,ebay_currency,ebay_shipfee,ebay_countryname,orderweight2,ebay_carrier,scantime,ebay_total,is_main_order,is_sendreplacement FROM ebay_order WHERE ebay_status=2 AND ebay_combine!=1 AND scantime BETWEEN {$start} and $end  ORDER BY scantime ASC";




function is_registershipping($carrier){
	return in_array($carrier, array('中国邮政平邮','香港小包平邮')) ? 1 : 2;
}

function validate_trackingnumber($num){
	if(empty($num)||preg_match("/^0/", $num)){
		return false;
	}else{
		return true;
	}
}

function sku_processingcost($num){
	$num = intval($num);
	return $num>0 ? 0.65+($num-1)*0.05 : 0;
}

function round_num($f, $n){
	$num = pow(10, $n);
	$intn = intval(round($f*$num));
	$r = $intn/$num;
	$r = $r + 0.00001;
	return number_format($r,2);
}

function judge_is_splitorder($ebay_id){
	//判断订单是否为拆分订单
	global $dbcon;
	$es_sql = "select * from ebay_splitorder as es where (split_order_id = '$ebay_id' OR main_order_id = '$ebay_id') ";
	$result = $dbcon->execute($es_sql);
	$result = $dbcon->fetch_one($result);
	return $result;
}

function func_readlog_splitorder($ebay_id){
	//ebay_order_splitorder 已经升级作为一些操作的记录表,读取这个表的记录信息
	//add by Herman.Xi @ 20130309
	global $dbcon;
	$es_sql = "select * from ebay_splitorder as es where split_order_id = '$ebay_id' ";
	$result = $dbcon->execute($es_sql);
	$es		= $dbcon->fetch_one($result);
	if(empty($es)){
		return false;
	}else{
		return $es['mode'];
	}
}

function _calceveryweight($weightarray, $totalfee){
	$feearray = array();
	$totalweight = array_sum($weightarray);
	foreach ($weightarray AS $key=>$weight){
		$feearray[$key] = round(($totalfee*$weight/$totalweight), 3);
	}
	return $feearray;
}

function _is_combinesku($sku){
	//判断料号是否为组合料号
	global $dbcon, $user;
	$iscombine = false;
	$rr	= "select * from ebay_productscombine where ebay_user='vipchen' and goods_sn='$sku'";
	$rr	= $dbcon->execute($rr);
	$rr = $dbcon->getResultArray($rr);
	if(count($rr) > 0) $iscombine = true;
	return $iscombine;
}

function _get_PPfee($amount){
    if ($amount < 7) {
    	return ( $amount * 0.06 ) + 0.05;
    } else {
        return ( $amount * 0.029 ) + 0.3;
    }
}

function _other2USD($cur,$amount){
	
    global $dbcon;
    
	if ($cur == 'USD') {
    	return $amount;
    }
    
    $sql = "SELECT * FROM `ebay_kpi_currency` WHERE oldCurrency='{$cur}' AND currency='USD'";
    $sql = $dbcon->query($sql);
	$currencyinfo = $dbcon->fetch_one($sql);
    
    if (!empty($currencyinfo)){
    	return $amount * $currencyinfo['rates'];
    } else {
        return 0;
    }
}
	
/*
 * 美元转人民币
 */
function _USD2CNY($amount){
	
	global $dbcon;
    
    $sql = "SELECT * FROM `ebay_kpi_currency` WHERE oldCurrency='USD' AND currency='CNY'";
    $sql	= $dbcon->query($sql);
	$currencyinfo	= $dbcon->fetch_one($sql);
    
    if (!empty($currencyinfo)){
    	return $amount * $currencyinfo['rates'];
    } else {
        return 0;
    }
}

/*
 * 港币转人民币
 */
function _HKD2CNY($amount){
	
	global $dbcon;
    
    $sql = "SELECT * FROM `ebay_kpi_currency` WHERE oldCurrency='HKD' AND currency='CNY'";
    $sql	= $dbcon->query($sql);
	$currencyinfo	= $dbcon->fetch_one($sql);
    
    if (!empty($currencyinfo)){
    	return $amount * $currencyinfo['rates'];
    } else {
        return 0;
    }
}

// 通过sku 获取 采购成员 和对应销售 add by xiaojinhua

function _getMemberFromSpu($spu){
	global $curl;
	$memcache = new Memcache;
	$memcache->connect('192.168.200.161', 11211) or die ("Memcache Could not connect");
	$spuArr = json_encode(array($spu));
	$spukey = md5("spu".$spu);
	$cache_data = $memcache->get($spukey);
	//var_dump($cache_data);
	if(!$cache_data){
		$url = "http://api.pc.valsun.cn/json.php?mod=OmAvailableApi&act=getSpuSalerIUNBySpuArr&jsonp=1&spuArr={$spuArr}";
		$rtnInfo = $curl->get($url,0);
		$rtnArr = json_decode($rtnInfo,true);
		$memcache->set($spukey, $rtnArr['data'], false, 60*60*12);
		$memcache->close();
		return $rtnArr['data'];
	}else{
		$memcache->close();
		return $cache_data;
	}

}


function _getSkuInfo($sku){
	global $curl,$memcache;
	$memcache = new Memcache;
	$memcache->connect('192.168.200.161', 11211) or die ("Memcache Could not connect");
	$spuArr = json_encode(array($spu));
	$skukey = md5("sku".$sku);
	$cache_data = $memcache->get($skukey);
	if(!$cache_data){
		$url = "http://api.pc.valsun.cn/json.php?mod=OmAvailableApi&act=getGoodsInfoBySku&jsonp=1&sku={$sku}";
		$rtnInfo = $curl->get($url,0);
		$rtnArr = json_decode($rtnInfo,true);
		$memcache->set($skukey, $rtnArr['data'], false, 60*60*12);
		$memcache->close();
		return $rtnArr['data'];
	}else{
		return $cache_data;
	}
}

function _getNamefromId($id){
	global $curl,$memcache;
	$url = "http://purchase.valsun.cn/json.php?mod=Common&act=getUsername&userId={$id}";
	$rtnInfo = $curl->get($url,0);
	return $rtnInfo;
}


function _get_skuinfo($sku){
	//判断料号是否为组合料号
	global $dbcon;
	$sql	= "select goods_location,goods_weight,goods_cost,ebay_packingmaterial,cguser from ebay_goods where goods_sn='{$sku}'";
	$sql	= $dbcon->query($sql);
	$goodsinfo	= $dbcon->fetch_one($sql);
	return $goodsinfo;
}

function _multi2single($key, $arrays){
	$results = array();
	foreach ($arrays AS $array){
		array_push($results, $array[$key]);
	}
	return $results;
}

//获取订单发货的分区
function _getSendZone($orderid){
	global $dbcon;
	$MAILWAYCONFIG = array(0=>'EUB', 1=>'深圳', 2=>'福州', 3=>'三泰', 4=>'泉州', 5=>'义乌', 6=>'福建', 7=>'中外联', 8=>'GM', 9=>'香港', 10=>'快递');

	$sql = "SELECT * FROM  `ebay_scan_mailway` where ebay_id={$orderid} ";
	$sql = $dbcon->execute($sql);
	$mailway = $dbcon->fetch_one($sql);
	if(empty($mailway['mailway'])){
		$sql = "SELECT main_order_id FROM  `ebay_splitorder` WHERE  `split_order_id` ={$orderid}";
		$sql = $dbcon->execute($sql);
		$main_id = $dbcon->fetch_one($sql);
		$sql = "SELECT * FROM  `ebay_scan_mailway` where ebay_id={$main_id['main_order_id']}";
		$sql = $dbcon->execute($sql);
		$mailway = $dbcon->fetch_one($sql);
	}
	return $MAILWAYCONFIG[$mailway['mailway']];
}


?>

