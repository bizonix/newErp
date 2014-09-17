<?php
function write_scripts_log($action, $ebay_account, $data){
	//add by Herman.Xi @ 20130306
	// /data/ebay_order_cronjob_logs/auto_contrast_intercept/${ebay_account}/${year_month}/${today}/
	$dirPath = "/home/ebay_order_cronjob_logs/{$action}/{$ebay_account}/".date("Y-m")."/".date("d");
	/*$dirPath = "/home/ebay_order_cronjob_logs/{$action}";
	if (!is_dir($dirPath)){
    	mkdir($dirPath);
		//chmod($dirPath, 0777);//设置权限@modified by Herman.Xi 2013-04-03
    }
	$dirPath .= "/{$ebay_account}";
	if (!is_dir($dirPath)){
    	mkdir($dirPath);
		//chmod($dirPath, 0777);
    }
	$dirPath .= "/".date("Y-m");
	if (!is_dir($dirPath)){
    	mkdir($dirPath);
		//chmod($dirPath, 0777);
    }
	$dirPath .= "/".date("d");
	if (!is_dir($dirPath)){
    	mkdir($dirPath);
		//chmod($dirPath, 0777);
    }*/
	if (!is_dir($dirPath)){
		mkdirs($dirPath);
	}
	$filename = date("H").".txt";
    $readpath = $dirPath.'/'.$filename;
	//chmod($readpath, 0777);
	if (!$handle=fopen($readpath, 'a+')) {
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


function get_firstsale($sku){

	global $dbcon;
	
	$combineskus = get_combinesku($sku);
	$skus = empty($combineskus) ? array() : array_keys($combineskus);
	array_push($skus, $sku);
	$skus = array2strarray($skus);
	
	$sql = "SELECT a.ebay_paidtime,a.ebay_addtime  FROM  ebay_order as a left join ebay_orderdetail as b on a.ebay_ordersn=b.ebay_ordersn WHERE b.sku IN (".implode(',', $skus).") ORDER BY a.ebay_id ASC LIMIT 1";
	//echo $sql;
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
	
	$sql = "SELECT a.ebay_paidtime,a.ebay_addtime  FROM  ebay_order as a left join ebay_orderdetail as b on a.ebay_ordersn=b.ebay_ordersn WHERE b.sku IN (".implode(',', $skus).") ORDER BY a.ebay_id desc LIMIT 1";
	//echo $sql;
	$sql = $dbcon->execute($sql);
	$order = $dbcon->getResultArray($sql);
	return $order[0]['ebay_paidtime'] != '' ? $order[0]['ebay_paidtime'] : $order[0]['ebay_addtime'];
	
}

function get_combinesku($sku){
	
	global $dbcon,$MemcacheObj;
	
	$MemcacheObj->connect('192.168.200.168', 11211);
	
	//windayzhong 2014-07-03===========================
	$is_cache	=	false;
	$sql		=	"";
	$memkey		=	md5("truesku_cache");
	$memresult	=	$MemcacheObj->get($memkey);
	$need_truesku		=	array();
	$need_cache_truesku	=	array();
		
	$memresult_array	=	json_decode($memresult);
	if(!$memresult || empty($memresult) || empty($memresult_array)){
		$MemcacheObj->delete($memkey);
		//缓存set
		$sql			=	"SELECT truesku FROM ebay_productscombine GROUP BY truesku";
		$query_truesku 	= 	$sql = $dbcon->execute($sql);
		$truesku_ar		=	$dbcon->getResultArray($query_truesku);
		
		foreach($truesku_ar as $v){
			$need_cache_truesku[]	=	$v['truesku'];
			$strpos	=	strpos($v['truesku'], "[".$sku."]");
			$c		=	strlen($v['truesku']) - strlen("[".$sku."]");
			if((intval($strpos) == 0 || intval($strpos) == $c) && substr_count($v['truesku'], "[".$sku."]"))	$need_truesku[]	=	$v['truesku'];
		}
		$json_cache		=	json_encode($need_cache_truesku);
		if(sizeof($need_cache_truesku) > 0){
			$op	=	$MemcacheObj->add($memkey,$json_cache,false,50000);
		}
		unset($truesku_ar);

	}else{
		//缓存get
		$truesku_ar		=	json_decode($memresult);
		foreach($truesku_ar as $v){
			$strpos	=	strpos($v, "[".$sku."]");
			$c		=	strlen($v) -strlen("[".$sku."]");
			//if(substr_count($v, $sku))	$need_truesku[]	=	$v;
			if(($strpos == 0 || $strpos == $c) && substr_count($v, "[".$sku."]"))	$need_truesku[]	=	$v;
		}
		unset($truesku_ar);
	}

	$need_truesku_str	=	"";
	if(sizeof($need_truesku) > 0 ){
		$need_truesku_str	=	implode("','", $need_truesku);
	}
	unset($need_truesku);
	unset($need_cache_truesku);

	if(!empty($need_truesku_str)){
		//命中
		$is_cache	=	true;
		$sql	=	"SELECT goods_sn,goods_sncombine FROM ebay_productscombine WHERE truesku in ('".$need_truesku_str."')";
		
	}
	//===============================end
	//file_put_contents("/usr/local/php/var/log/xxx.txt",$sql."xxxx\r\n",FILE_APPEND);
	
	$memkey = 	md5("get_combinesku_cachexx_".$sku);
	$memresult = $MemcacheObj->get($memkey);
	if(!$memresult){
		if(!$is_cache){
			$sql = "SELECT goods_sn,goods_sncombine FROM ebay_productscombine WHERE truesku LIKE '[{$sku}]%' OR truesku LIKE '%[{$sku}]'";//modified by Herman.Xi @ 2013-06-04
		}
		//file_put_contents("/usr/local/php/var/log/xxx.txt",$sql."444444444\r\n",FILE_APPEND);
		$sql 			= 	$dbcon->execute($sql);
		$combinelists 	= 	$dbcon->getResultArray($sql);
		if(empty($combinelists)){
			$MemcacheObj->add($memkey,"1",false,43200);	//为空
		}else{
			$MemcacheObj->add($memkey,$combinelists,FALSE,43200);
		}
	}
	$combinelists = $MemcacheObj->get($memkey);
	if (empty($combinelists) || $combinelists == "1"){
			return array();
	}
	$results = array();
	foreach ($combinelists AS $combinelist){
			$results[$combinelist['goods_sn']] = $combinelist['goods_sncombine'];
	}
	$MemcacheObj->close();
	return $results;
}



function check_is_intercept($order_sn){
	//订单自动拦截方法 支持虚拟料号
	global $dbcon;
	
	$sql = "SELECT sku,ebay_amount FROM ebay_orderdetail WHERE ebay_ordersn='{$order_sn}'";
	$sql		= $dbcon->execute($sql);
	$orderdetaillist = $dbcon->getResultArray($sql);
	
	foreach ($orderdetaillist AS $orderdetail){
		$sku_arr = get_realskuinfo($orderdetail['sku']);
		foreach($sku_arr as $or_sku => $or_nums){
			$allnums = $or_nums*$orderdetail['ebay_amount'];
			if (!check_sku($or_sku, $allnums)){
				$sql = "UPDATE ebay_order SET ebay_status=640 WHERE ebay_ordersn='{$order_sn}'";
				//echo "\n{$sql}\n";
				$dbcon->execute($sql);
				
				$sql = "select ebay_id,ebay_status,ebay_account from ebay_order where ebay_ordersn='{$order_sn}'";
				$dbcon->execute($sql);
				$order_info = $dbcon->getResultArray($sql);
				$datetime = date("Y-m-d H:i:s");
				$sql = "INSERT INTO ebay_mark_shipping SET ebay_id={$order_info[0]['ebay_id']}, ebay_status={$order_info[0]['ebay_status']}, type=1, ebay_account='{$order_info[0]['ebay_account']}', addtime='{$datetime}'";
				$dbcon->execute($sql);
				return true;
			}
		}
	}
	return false;
}


function check_is_intercept_tmp($order_sn){
	//订单自动拦截方法 支持虚拟料号
	global $dbcon;
	
	$sql = "SELECT sku,ebay_amount FROM ebay_orderdetail WHERE ebay_ordersn='{$order_sn}'";
	$sql		= $dbcon->execute($sql);
	$orderdetaillist = $dbcon->getResultArray($sql);
	
	foreach ($orderdetaillist AS $orderdetail){
		$sku_arr = get_realskuinfo($orderdetail['sku']);
		foreach($sku_arr as $or_sku => $or_nums){
			$allnums = $or_nums*$orderdetail['ebay_amount'];
			if (!check_sku($or_sku, $allnums)){
				//$sql = "UPDATE ebay_order SET ebay_status=640 WHERE ebay_ordersn='{$order_sn}'";
				//echo "\n{$sql}\n";
				//$dbcon->execute($sql);
			}else{
				$sql = "UPDATE ebay_order SET ebay_status=1 WHERE ebay_ordersn='{$order_sn}'";
				echo "\n{$sql}\n";
				$dbcon->execute($sql);
				return true;
			}
		}
	}
	return false;
}


//一次性获取所有平台的发货数据
//add by xiaojinhua
function getallsale($sku, $accounts, $wheretime=array()){
	global $dbcon;
	$totalnums = 0;
	$combineskus = get_combinesku($sku);
	$skus = empty($combineskus) ? array() : array_keys($combineskus);
	array_push($skus, $sku);
	$skus_str = implode("','",$skus);
	$skus_str = "'".$skus_str."'";
	$scantime = "";
	if (count($wheretime)==2){
		list($starttime, $endtime) = $wheretime;
		$scantime = "AND a.scantime BETWEEN {$starttime} AND {$endtime}";
	}

	$sql = "SELECT b.ebay_amount , b.sku,a.ebay_account
					FROM ebay_order AS a 
					LEFT JOIN ebay_orderdetail AS b ON a.ebay_ordersn = b.ebay_ordersn 
					WHERE a.ebay_status=2
					AND b.sku in ({$skus_str})
					{$scantime}";

	$sql = $dbcon->execute($sql);
	$skunums = $dbcon->getResultArray($sql);
	//分平台取出发货数据
	$saleNum = array();
	foreach($accounts as $platform => $accountarr){
		$saleNum[$platform] = 0;
	}
	foreach($skunums as $sku_info){
		foreach($accounts as $platform => $accountarr){
			if(in_array($sku_info["ebay_account"],$accountarr)){
				$realtimes = get_realtime($sku_info["sku"]);
				$saleNum[$platform] += ($sku_info["ebay_amount"]*$realtimes);
				break;
			}
		}
		//$realtimes = get_realtime($sku_info["sku"]);
		$totalnums += ($sku_info["ebay_amount"]*$realtimes);
	}
	return array("totalnums"=>$totalnums,"platformnums"=>$saleNum);
}


//一次性获取所有平台的自动拦截
function get_all_autointercept($sku, $accounts){
	global $dbcon;
	$totalnums = 0;
	$combineskus = get_combinesku($sku);
	$skus = empty($combineskus) ? array() : array_keys($combineskus);
	array_push($skus, $sku);
	$skus_str = implode("','",$skus);
	$skus_str = "'".$skus_str."'";
	$sql = "SELECT b.ebay_amount , b.sku,a.ebay_account  
				FROM ebay_order AS a 
				LEFT JOIN ebay_orderdetail AS b ON a.ebay_ordersn = b.ebay_ordersn 
				WHERE a.ebay_status in (658,661)
				AND b.sku in ({$skus_str})
				AND a.ebay_combine!='1'";
	//echo "{$sql}\n";
	$sql = $dbcon->execute($sql);
	$skunums = $dbcon->getResultArray($sql);

	$saleNum = array();
	foreach($skunums as $sku_info){
		foreach($accounts as $platform => $accountarr){
			if(in_array($sku_info["ebay_account"],$accountarr)){
				$realtimes = get_realtime($sku_info["sku"]);
				$saleNum[$platform] += ($sku_info["ebay_amount"]*$realtimes);
				break;
			}
		}
		//$realtimes = get_realtime($sku_info["sku"]);
		$totalnums += ($sku_info["ebay_amount"]*$realtimes);
	}
	//echo "总数{$totalnums}";
	//var_dump($totalnums,$saleNum);
	return array("totalnums"=>$totalnums,"platformnums"=>$saleNum);
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

function get_TempIntercept($type){
	//获取邮路下的料号
	global $dbcon, $user;
	$sql = "select path from ebay_goodscategory where mailwayId = {$type} ";
	$result = $dbcon->execute($sql);
	$goodscategory = $dbcon->getResultArray($result);
	//var_dump($goodscategory); exit;
	$tempArr = array();
	foreach($goodscategory as $value){
		$sql = "select goods_sn from ebay_goods where goods_category = '{$value['path']}' and ebay_user = '$user' ";
		$result = $dbcon->execute($sql);
		$goods = $dbcon->getResultArray($result);
		foreach($goods as $v){
			$tempArr[] = $v['goods_sn'];
		}
	}
	return $tempArr;
}

function auto_contrast_intercept($ebay_orders){
	//订单自动拦截完整版 支持虚拟料号
	//add Herman.Xi 2012-12-20
	/*
	* 订单进入系统首先判断 是否为超大订单,如果为超大订单,文件夹为640;
	* 判断订单下,料号是否全部有货,部分有货,全部没货:
	     如果部分有货,判断其运输方式,如果为快递,文件夹为659;非快递则为660;(订单自动部分包货)
		 如果全部没货,判断其运输方式,如果为快递,文件夹为658;非快递则为661;(订单自动拦截)
		 如果全部有货:
		 	先判断如果为组合订单,文件夹为606;
			如果超重订单,文件夹为608;
			如果快递订单,文件夹为639;
			全部不满足则为导入状态
	  自动拦截时,判断自动拦截快递,非快递,自动部分包货快递,非快递里面的订单,自动每隔十五分钟执行一次
	  添加缺货和合并包裹缺货处理
	  增加俄速通运输方式 - 俄罗斯  add by zhiqiang.chen
	*/
	global $dbcon, $defaultstoreid, $order_statistics, $SYSTEM_ACCOUNTS,$__liquid_items_fenmocsku,$__liquid_items_BuiltinBattery,$__liquid_items_SuperSpecific,$__liquid_items_Paste,$__liquid_items_postbyhkpost,$__liquid_items_cptohkpost,$__liquid_items_Wristwatch,$__liquid_items_TempModifySZ,$__liquid_items_TempModifyRU;
	//$__liquid_items_TempIntercept = get_TempIntercept(4);
	$express_delivery = array('UPS','DHL','TNT','EMS','FedEx');
	$no_express_delivery = array('中国邮政平邮','中国邮政挂号','香港小包平邮','香港小包挂号','德国邮政挂号','新加坡小包挂号','EUB','Global Mail','俄速通平邮','俄速通挂号');
	mysql_ping();
	foreach($ebay_orders as $ebay_order){
		/*if(!$dbcon->link){
			include_once 'config_row/config_database_row_master.php';
			unset($dbcon);
			$dbcon	= new DBClass();
		}*/
		$contain_DGMPY = false;
		$contain_DGMGH = false;
		$log_data = "";
		$ebay_total0 = 0; //该订单实际总数
		//$import_status = now_order_status_log($osn, false);
		$ebay_id = $ebay_order['ebay_id'];
		$order_sn = $ebay_order['ebay_ordersn'];
		$ebay_status = $ebay_order['ebay_status'];
		$ebay_note = $ebay_order['ebay_note'];
		$ebay_carrier = @$ebay_order['ebay_carrier'];
		$ebay_countryname = $ebay_order['ebay_countryname'];
		$ebay_account = $ebay_order['ebay_account'];
		$ebay_total	= $ebay_order['ebay_total'];
		$ebay_username = $ebay_order['ebay_username'];
		$ebay_orderid = $ebay_order['ebay_orderid'];
		$ebay_usermail = $ebay_order['ebay_usermail'];
		$PayPalEmailAddress = @$ebay_order['PayPalEmailAddress'];
		$shipfee = @$ebay_order['ordershipfee'];
		
		$ebay_street = $ebay_order['ebay_street'];
		$ebay_postcode = $ebay_order['ebay_postcode'];
		$ebay_state = $ebay_order['ebay_state'];
		$ebay_city = $ebay_order['ebay_city'];
		$ebay_phone = $ebay_order['ebay_phone'];
		$ebay_phone1 = $ebay_order['ebay_phone1'];		
		
		echo "------ebayid:$ebay_id----$ebay_countryname-----\n";
		echo "订单号:$order_sn\t\n";
		
		$sql = "SELECT ebay_id,sku,ebay_amount,ebay_itemprice,shipingfee FROM ebay_orderdetail WHERE ebay_ordersn='{$order_sn}'";
		$result = $dbcon->execute($sql);
		$orderdetaillist = $dbcon->getResultArray($result);
		
		if(in_array($ebay_account, array('edealsmart','eshoppingstar75','easyshopping095','beromantic520','happyforu19','ishoppingclub68','lantomall','estore2099'))){
			//海外仓订单同步调试
			var_dump($orderdetaillist);
		}
		$contain_special_item = false;
		$contain_sz_item = false;
		$contain_os_item = false;
		$contain_wh_item = false;
		$contain_hold131103 = false; //add by herman.xi @20131103
		$ow_status = array();
		$allskuinfo = array();
		foreach ($orderdetaillist AS $orderdetail){
			$sku_arr = get_realskuinfo($orderdetail['sku']);
			$ebay_total0 += $orderdetail['ebay_itemprice']*$orderdetail['ebay_amount'] + $orderdetail['shipingfee'];
			foreach($sku_arr as $or_sku => $or_nums){
				$allskuinfo[] = $or_sku;
				//if(in_array($or_sku,$__liquid_items_fenmocsku) || in_array($or_sku,$__liquid_items_SuperSpecific) || in_array($or_sku,$__liquid_items_BuiltinBattery)){ //粉末状,超规格产品 走福建邮局
				if(in_array($or_sku,$__liquid_items_SuperSpecific)){ //超规格产品
					$contain_special_item = true;
				}
				if(in_array($or_sku,$__liquid_items_TempModifySZ)){
					$contain_sz_item = true;	
				}
				if(preg_match("/^US01\+.*/", $or_sku, $matchArr) || preg_match("/^US1\+.*/", $or_sku, $matchArr) ){
					$log_data .= "[".date("Y-m-d H:i:s")."]\t包含海外仓料号订单---{$ebay_id}-----料号：{$or_sku}--!\n\n";
					$contain_os_item = true;
					if(strpos($or_sku,"US01+") !== false){
						$matchStr=substr($matchArr[0],5);//去除前面
						//$matchStr = str_replace("US1+", "", $or_sku);
					}else{
						//$matchStr=substr($matchArr[0],5);//去除前面
						$matchStr = str_replace("US1+", "", $or_sku);
					}
					$n=strpos($matchStr,':');//寻找位置
					if($n){$matchStr=substr($matchStr,0,$n);}//删除后面

					if(preg_match("/^0+(\w+)/",$matchStr,$matchArr)){
						$matchStr = $matchArr[1];
					}

					echo $sql = "update ebay_orderdetail set sku ='{$matchStr}' where ebay_id = {$orderdetail['ebay_id']} "; //add by Herman.Xi 替换海外仓料号为正确料号
					echo "\n";
					$dbcon->execute($sql);
					$virtualnum = check_oversea_stock($matchStr); //检查海外仓虚拟库存
					echo $virtualnum; echo "\n";
				    insert_mark_shipping($ebay_id);
					if($virtualnum >= 0){
						$ow_status[] = 705;
					}else{
						$ow_status[] = 714; //海外仓缺货
					}
				}
				
				/*if(!$contain_os_item && empty($ebay_note) && $totalweight <=2){
					//如果不是海外仓的，就去检查是否为B仓的料号
					$location = get_sku_location($or_sku);
					if(strpos($location,'WH') === 0 || strpos($location,'HW') === 0){
						$contain_wh_item = true;
					}
				}*/
			}
		}
		/*if(in_array($ebay_account, array('edealsmart','eshoppingstar75','easyshopping095','beromantic520','happyforu19','ishoppingclub68','lantomall','estore2099'))){
			//海外仓订单同步调试
			var_dump($ow_status,$contain_os_item);
		}*/
		#################################START##################################################
		$array_intersect_zhijiayou = array_intersect($allskuinfo, $__liquid_items_cptohkpost);
		$array_intersect_yieti = array_intersect($allskuinfo, $__liquid_items_postbyhkpost);
		$array_intersect_fenmocsku = array_intersect($allskuinfo, $__liquid_items_fenmocsku);
		$array_intersect_BuiltinBattery = array_intersect($allskuinfo, $__liquid_items_BuiltinBattery);
		$array_intersect_Paste = array_intersect($allskuinfo, $__liquid_items_Paste);
		$array_intersect_Wristwatch = array_intersect($allskuinfo, $__liquid_items_Wristwatch);//add by Herman.Xi @ 20131103
		$array_intersect_TempModifyRU = array_intersect($allskuinfo, $__liquid_items_TempModifyRU);
		//$array_intersect_TempIntercept = array_intersect($allskuinfo, $__liquid_items_TempIntercept);//add by Herman.Xi @ 20131105
		
		/*if(count($array_intersect_zhijiayou) > 0 || count($array_intersect_yieti) > 0 || count($array_intersect_fenmocsku) > 0 || count($array_intersect_Paste) > 0 || (count($array_intersect_BuiltinBattery) > 0 && count($array_intersect_Wristwatch) == 0)){	
		if(count($array_intersect_TempIntercept) > 0){*/
		if(count($array_intersect_zhijiayou) > 0 || count($array_intersect_yieti) > 0 || count($array_intersect_fenmocsku) > 0 || count($array_intersect_Paste) > 0 || (count($array_intersect_BuiltinBattery) > 0)){
			$contain_hold131103 = true;
		}
		
		#################################END##################################################
		
		if($contain_special_item){
			$sql = "update ebay_order set ebay_carrierstyle ='1' where ebay_id ={$ebay_id}"; //add by Herman.Xi 记录该订单含有特殊料号
			$dbcon->execute($sql);
			$log_data .= "[".date("Y-m-d H:i:s")."]\t包含粉末状超规格产品---{$ebay_id}---!\n\n";
		}
		if($contain_sz_item){
			$sql = "update ebay_order set ebay_carrierstyle ='4' where ebay_id ={$ebay_id}"; //add by Herman.Xi 记录该订单含有临时改深圳的料号
			$dbcon->execute($sql);
			$log_data .= "[".date("Y-m-d H:i:s")."]\t记录该订单含有临时改深圳的料号---{$ebay_id}---!\n\n";
		}
		if($contain_os_item){

			$final_status = 705;

			echo $sql = "update ebay_order set ebay_status ='{$final_status}' where ebay_id ={$ebay_id} and ebay_status = '{$ebay_status}'"; //add by Herman.Xi 记录该订单含有海外仓料号 20130927
			echo "\n";
			$dbcon->execute($sql);
			$log_data .= "[".date("Y-m-d H:i:s")."]\t更新海外仓料号订单状态为{$final_status}---{$ebay_id}--{$sql}-!\n\n";
			if($final_status == 705){
				$totalweight = calcWeight($ebay_id);
				echo "calcWeight=>".$totalweight; echo "\n";
				$skunums	 = checkSkuNum($ebay_id);
				echo "checkSkuNum=>".$skunums; echo "\n";
				if($skunums === true){
					continue;
				}else if ($totalweight>20) {
					if($skunums==1){
						usCalcShipCost($ebay_id);
						echo "1_usCalcShipCost=>".$skunums; echo "\n";
					}
				} else {
					usCalcShipCost($ebay_id);
					echo "2_usCalcShipCost=>".$skunums; echo "\n";
				}
			}


			$log_data .= "[".date("Y-m-d H:i:s")."]\t包含海外仓料号---自动跳转---{$ebay_id}---的状态为---$final_status!\n\n";
			//write_scripts_log('auto_contrast_intercept', $ebay_account, $log_data);
			//$totalweight = recalcorderweight($order_sn, $ebay_packingmaterial); //modified by Herman.Xi 2012-10-17
			unset($log_data); unset($ebay_total0); unset($ebay_id); unset($order_sn); unset($ebay_status); unset($ebay_note); unset($ebay_carrier); unset($ebay_countryname); unset($ebay_account); unset($ebay_total); unset($ebay_username); unset($ebay_orderid); unset($ebay_usermail); unset($PayPalEmailAddress);
			continue;
		}
		
		$totalweight = recalcorderweight($order_sn, $ebay_packingmaterial); //modified by Herman.Xi 2012-10-17
		echo "计算重量:$totalweight\t\n";
		echo "ebay_orderid:$ebay_orderid\t\n";
		if(in_array($ebay_account,$SYSTEM_ACCOUNTS['亚马逊'])){//非线下amazon账号订单
			//ebay 平台可以重新计算运输方式 @ 20130301
			if (empty($ebay_countryname)){
				$ebay_carrier = '';
				$shipfee = 0;
				echo "\n该订单的国家为空{$ebay_id}\n";
				$final_status = 692;//移动到同步异常订单中
				$sql = "UPDATE ebay_order SET ebay_status='$final_status' WHERE ebay_id ='$ebay_id' and ebay_status = '{$ebay_status}' ";
				$log_data .= "[".date("Y-m-d H:i:s")."]\t自动跳转---{$ebay_id}---的状态为---$final_status!\n\n";
				$dbcon->execute($sql) or die("Fail : $sql");
				//write_scripts_log('auto_contrast_intercept', $ebay_account, $log_data);
				unset($log_data); unset($ebay_total0); unset($ebay_id); unset($order_sn); unset($ebay_status); unset($ebay_note); unset($ebay_carrier); unset($ebay_countryname); unset($ebay_account); unset($ebay_total); unset($ebay_username); unset($ebay_orderid); unset($ebay_usermail); unset($PayPalEmailAddress);
				continue;
			}
		}
		/*if(in_array($ebay_account,$SYSTEM_ACCOUNTS['cndirect'])){//CN暂时处理的单
			if (in_array($ebay_usermail, array('cbt1top@peacesoft.net','order1top@peacesoft.net'))){
				$final_status = 612;//移动到暂时不寄
				$sql = "UPDATE ebay_order SET ebay_status='$final_status' WHERE ebay_id ='$ebay_id' and ebay_status = '{$ebay_status}' ";
				$log_data .= "[".date("Y-m-d H:i:s")."]\t自动跳转---{$ebay_id}---的状态为---$final_status!\n\n";
				$dbcon->execute($sql) or die("Fail : $sql");
				write_scripts_log('auto_contrast_intercept', $ebay_account, $log_data);
				continue;
			}
		}*/
		$contain_eub = false;
		if((in_array($ebay_account, $SYSTEM_ACCOUNTS['ebay平台']) || in_array($ebay_account,$SYSTEM_ACCOUNTS['海外销售平台'])) && !empty($ebay_orderid)){//非线下ebay账号订单
			//ebay 平台可以重新计算运输方式 @ 20130301
			if (empty($ebay_countryname)){
				$ebay_carrier = '';
				$shipfee = 0;
				echo "\n该订单的国家为空{$ebay_id}\n";
				$final_status = 692;//移动到同步异常订单中
				$sql = "UPDATE ebay_order SET ebay_status='$final_status' WHERE ebay_id ='$ebay_id' and ebay_status = '{$ebay_status}' ";
				$log_data .= "[".date("Y-m-d H:i:s")."]\t自动跳转---{$ebay_id}---的状态为---$final_status!\n\n";
				$dbcon->execute($sql) or die("Fail : $sql");
				//write_scripts_log('auto_contrast_intercept', $ebay_account, $log_data);
				unset($log_data); unset($ebay_total0); unset($ebay_id); unset($order_sn); unset($ebay_status); unset($ebay_note); unset($ebay_carrier); unset($ebay_countryname); unset($ebay_account); unset($ebay_total); unset($ebay_username); unset($ebay_orderid); unset($ebay_usermail); unset($PayPalEmailAddress);
				continue;
			}else{
				$fees			= calcshippingfee($totalweight,$ebay_countryname,$ebay_id,$ebay_account,$ebay_total);
				$ebay_carrier	= $fees[0];
				$shipfee		= $fees[1];
				if(empty($shipfee)){
					$logfile	=	"/home/ebay_order_cronjob_logs/empty_shippfee/sync_order_".date("Y-m-d").".log";
					$emptyshippfee_log		=	"Date: ".date("Y-m-d H:i:s"). " 订单号 ".$ebay_id." 计算重量 ".$totalweight." 原运输方式 ".$ebay_carrier." 原运费为空 大于UPS 运费 \n";
					@file_put_contents($logfile, $emptyshippfee_log, FILE_APPEND);	
				}
				$totalweight	= isset($fees[2]) ? $fees[2] : $totalweight;
				echo "\n经计算 运费 $shipfee 重量 $totalweight 包装材料 $ebay_packingmaterial\n";
			}
			$bb	= "update ebay_order set ebay_carrier='$ebay_carrier',ordershipfee='$shipfee',
						  orderweight ='$totalweight',packingtype ='$ebay_packingmaterial' 
				   where  ebay_id ='$ebay_id' ";
			$dbcon->execute($bb);
			if($ebay_total != $ebay_total0){
				//var_dump($ebay_total);
				//var_dump($ebay_total0);
				$ebay_total0 = (string) $ebay_total0;
				//var_dump($ebay_total);
				//var_dump($ebay_total0);
				/*if($ebay_total != $ebay_total0){
					echo "不相等"; echo "\n";
				}*/
			}
			if($ebay_carrier == 'EUB' && empty($ebay_note)){
				$contain_eub = true;
			}
			if($ebay_carrier == '新加坡DHL GM平邮' && empty($ebay_note)){
				$contain_DGMPY = true;
			}
			echo "[".date("Y-m-d H:i:s")."]\t总价记录---{$ebay_id}---系统总价{$ebay_total}---计算总价{$ebay_total0}\n";
			if(in_array($ebay_usermail, array("", "Invalid Request")) && $ebay_carrier=='EUB'){
				$final_status = 692;//移动到同步异常订单中
				$sql = "UPDATE ebay_order SET ebay_status='$final_status',ebay_noteb = '未获取买家邮箱' WHERE ebay_id ='$ebay_id' and ebay_status = '{$ebay_status}' ";
				$log_data .= "[".date("Y-m-d H:i:s")."]\t自动跳转---{$ebay_id}---的状态为---$final_status!\n\n";
				$dbcon->execute($sql) or die("Fail : $sql");
				//write_scripts_log('auto_contrast_intercept', $ebay_account, $log_data);
				unset($log_data); unset($ebay_total0); unset($ebay_id); unset($order_sn); unset($ebay_status); unset($ebay_note); unset($ebay_carrier); unset($ebay_countryname); unset($ebay_account); unset($ebay_total); unset($ebay_username); unset($ebay_orderid); unset($ebay_usermail); unset($PayPalEmailAddress);
				continue;
			}else if($ebay_total != $ebay_total0 && $ebay_status == 1){
				//ebay total 和单价数量不一致问题移动异常订单
				$final_status = 692;//移动到同步异常订单中
				$sql = "UPDATE ebay_order SET ebay_status='$final_status',ebay_noteb = '{$ebay_total}--{$ebay_total0}总价计算和单价相加不一致' WHERE ebay_id ='$ebay_id' and ebay_status = '{$ebay_status}' ";
				$log_data .= "[".date("Y-m-d H:i:s")."]\t自动跳转---{$ebay_id}---的状态为---$final_status!\n{$sql}\n";
				$dbcon->execute($sql) or die("Fail : $sql");
				$relation_sql = "insert into ebay_splitorder (recordnumber, main_order_id, split_order_id, mode, create_date) values ('{$ebay_id}', '{$ebay_id}', '{$ebay_id}', 7, '".date("Y-m-d H:i:s")."')";
				$dbcon->execute($relation_sql);
				//write_scripts_log('auto_contrast_intercept', $ebay_account, $log_data);
				unset($log_data); unset($ebay_total0); unset($ebay_id); unset($order_sn); unset($ebay_status); unset($ebay_note); unset($ebay_carrier); unset($ebay_countryname); unset($ebay_account); unset($ebay_total); unset($ebay_username); unset($ebay_orderid); unset($ebay_usermail); unset($PayPalEmailAddress);
				continue;
			}else if(!empty($PayPalEmailAddress) && !in_array(strtolower($PayPalEmailAddress),get_account_paypalemails($ebay_account)) && $ebay_status == 1){
				$final_status = 696;//付款邮箱如果不在对应邮箱中
				$sql = "UPDATE ebay_order SET ebay_status='$final_status',ebay_noteb = '{$PayPalEmailAddress}不属于该账号的收款邮箱,请确认!' WHERE ebay_id ='$ebay_id' and ebay_status = '{$ebay_status}' ";
				$log_data .= "[".date("Y-m-d H:i:s")."]\t自动跳转---{$ebay_id}---的状态为---$final_status!\n\n";
				$dbcon->execute($sql) or die("Fail : $sql");
				//write_scripts_log('auto_contrast_intercept', $ebay_account, $log_data);
				unset($log_data); unset($ebay_total0); unset($ebay_id); unset($order_sn); unset($ebay_status); unset($ebay_note); unset($ebay_carrier); unset($ebay_countryname); unset($ebay_account); unset($ebay_total); unset($ebay_username); unset($ebay_orderid); unset($ebay_usermail); unset($PayPalEmailAddress);
				continue;	
			}
		}else if(in_array($ebay_account, $SYSTEM_ACCOUNTS['dresslink']) || in_array($ebay_account,$SYSTEM_ACCOUNTS['cndirect'])){
			$shipfee = calctrueshippingfee($ebay_carrier, $totalweight, $ebay_countryname, $ebay_id);
			$bb = "update ebay_order set ordershipfee='$shipfee',orderweight ='$totalweight',packingtype ='$ebay_packingmaterial' where ebay_id ='$ebay_id' ";
			$dbcon->execute($bb);
			echo "\n经计算 独立商城 运费 $shipfee 重量 $totalweight 包装材料 $ebay_packingmaterial\n";
		}
		$judage_ups = true;
		if((in_array($ebay_account, $SYSTEM_ACCOUNTS['ebay平台']) || in_array($ebay_account,$SYSTEM_ACCOUNTS['海外销售平台'])) && $ebay_carrier == 'EUB'){
			$judage_ups = false;
		}
		if(in_array($ebay_account,$SYSTEM_ACCOUNTS['国内销售部'])){
			$judage_ups = false;	
		}
		if((in_array($ebay_account,$SYSTEM_ACCOUNTS['dresslink']) || in_array($ebay_account,$SYSTEM_ACCOUNTS['cndirect'])) && in_array($ebay_carrier,$express_delivery)){
			$judage_ups = false;
		}
		$contain_ups = false;
		if($judage_ups && !$contain_hold131103){//特殊料号不走UPS @add by Herman.Xi 20140317
			$containsku = polling_type_of_goods($allskuinfo);
			if($containsku){
				$oshipfee = calctrueshippingfee2($ebay_carrier, $totalweight, $ebay_countryname, $ebay_id);
				$UPSshipfee = calctrueshippingfee("UPS美国专线", $totalweight, $ebay_countryname, $ebay_id);
				if(($UPSshipfee < $oshipfee) && !empty($UPSshipfee) && !empty($oshipfee)){
					$bb = "update ebay_order set ordershipfee='$UPSshipfee',ebay_carrier ='UPS美国专线' where ebay_id ='$ebay_id' ";
					$dbcon->execute($bb);
					echo "\n经计算 UPS美国专线 运费 $UPSshipfee 重量 $totalweight \n";
					$logfile	=	"/home/ebay_order_cronjob_logs/ups/sync_order_".date("Y-m-d").".log";
					$ups_log		=	"Date: ".date("Y-m-d H:i:s"). " 订单号 ".$ebay_id." 计算重量 ".$totalweight." 原运输方式 ".$ebay_carrier." 原运费 ".$oshipfee." 大于UPS 运费 ".$UPSshipfee." 选择走UPS \n";
					@file_put_contents($logfile, $ups_log, FILE_APPEND);
					$contain_ups = true;
				}
			}
		}
		
		if(in_array($ebay_account,$SYSTEM_ACCOUNTS['dresslink']) || in_array($ebay_account,$SYSTEM_ACCOUNTS['cndirect'])){
			if(in_array($ebay_countryname, array('United States','US')) && $ebay_carrier == '中国邮政挂号'){
				$oshipfee = calctrueshippingfee2($ebay_carrier, $totalweight, $ebay_countryname, $ebay_id);
				$EUBshipfee = calctrueshippingfee("EUB", $totalweight, $ebay_countryname, $ebay_id);
				if(($EUBshipfee < $oshipfee) && !empty($EUBshipfee) && !empty($oshipfee)){
					$bb = "update ebay_order set ordershipfee='$EUBshipfee',ebay_carrier ='EUB' where ebay_id ='$ebay_id' ";
					$dbcon->execute($bb);
					echo "\n经计算 EUB 运费 $EUBshipfee 重量 $totalweight \n";
					$logfile	=	"/home/ebay_order_cronjob_logs/ups/eub_order_".date("Y-m-d").".log";
					$ups_log		=	"Date: ".date("Y-m-d H:i:s"). " 订单号 ".$ebay_id." 计算重量 ".$totalweight." 原运输方式 ".$ebay_carrier." 原运费 ".$oshipfee." 大于EUB 运费 ".$EUBshipfee." 选择走EUB \n";
					@file_put_contents($logfile, $ups_log, FILE_APPEND);
					if(empty($ebay_note)){
						$contain_eub = true;
					}
				}
			}
		}
		
		//增加俄速通运输方式，跟邮政运输方式做比较，选择最优。目前国内销售平台暂时未用
		$contain_xru_track = false;
		$contain_xru_notrack = false;
		$xru_compare = true;
		if(empty($ebay_street) || empty($ebay_username) || empty($ebay_state) || empty($ebay_city) || ($ebay_phone == '' && $ebay_phone1 == '' && strtolower($ebay_phone) == 'invalid request') || ($ebay_postcode == '' && $ebay_postcode =='none')){
			$xru_compare = false;
		}
		if(!in_array($ebay_account,$SYSTEM_ACCOUNTS['国内销售部']) && in_array($ebay_countryname, array('Russische Föderation','Russie','Russian Federation','Russian','Russia','Russian Federatuon'))){
				//服装配饰及鞋子箱包类且总价在200以下
			//$is_contain = polling_type_of_goods($allskuinfo,array(1,2));
			if($ebay_total <= 200 && count($array_intersect_TempModifyRU) == 0 && $totalweight <=2 && empty($ebay_note) && ($xru_compare && strlen($ebay_username) < 45 && strlen($ebay_postcode) == 6)){
				if($ebay_carrier == '中国邮政平邮' || $ebay_carrier == '中国邮政挂号'){
					$oshipfee = calctrueshippingfee2($ebay_carrier, $totalweight, $ebay_countryname, $ebay_id);
					//调用开放借口获得固定运输方式的运费
					$XRU_carrier = '俄速通挂号';
					if($ebay_carrier=='中国邮政平邮'){
						$XRU_carrier = '俄速通平邮';
					}
					$XRUshipfee = trans_carriers_fix_get($XRU_carrier, $totalweight, $ebay_countryname);
					$XRUshipfee = $XRUshipfee['fee'];
					if($XRUshipfee && !empty($oshipfee) && ($XRUshipfee < $oshipfee)){
						$sql = "UPDATE ebay_order SET ordershipfee = '$XRUshipfee', ebay_carrier = '$XRU_carrier' WHERE ebay_id = '$ebay_id' ";
						$dbcon->execute($sql);
						
						echo "\n经计算 $XRU_carrier 运费 $XRUshipfee 重量 $totalweight \n";
						$logfile	=	"/home/ebay_order_cronjob_logs/ups/xru_order_".date("Y-m-d").".log";
						$xru_log		=	"Date: ".date("Y-m-d H:i:s"). " 订单号 ".$ebay_id." 计算重量 ".$totalweight." 原运输方式 ".$ebay_carrier." 原运费 ".$oshipfee." 大于 ".$XRU_carrier." 运费 ".$XRUshipfee." 选择走".$XRU_carrier." \n";
						@file_put_contents($logfile, $xru_log, FILE_APPEND);
						if($XRU_carrier == '俄速通挂号'){
							$contain_xru_track = true;
						}else if($XRU_carrier == '俄速通平邮'){
							$contain_xru_notrack = true;
						}
					}
				}
			}
		}
		
		//增加瑞士小包
		$contain_swtch_track = false;
		if(strpos($ebay_carrier, '瑞士小包')!==false){
			$contain_swtch_track = true;
		}
		if(empty($ebay_note) && $ebay_status == 1 && count($allskuinfo) == 1 && $xru_compare && !in_array($ebay_account,$SYSTEM_ACCOUNTS['国内销售部']) && in_array($ebay_carrier, array('香港小包挂号','新加坡小包挂号','中国邮政挂号'))){
			if((in_array($ebay_account,$SYSTEM_ACCOUNTS['ebay平台']) || in_array($ebay_account,$SYSTEM_ACCOUNTS['海外销售平台'])) && in_array($ebay_countryname, array('USA','United States'))){
					
			}else{
				//if(count($array_intersect_zhijiayou) == 0 && count($array_intersect_yieti) == 0 && count($array_intersect_fenmocsku) == 0 && count($array_intersect_Paste) == 0 && count($array_intersect_BuiltinBattery) > 0){
					$oshipfee = calctrueshippingfee2($ebay_carrier, $totalweight, $ebay_countryname, $ebay_id);
					//调用开放借口获得固定运输方式的运费
					//if(strpos($ebay_carrier,'挂号') !== false){
						$XRU_carrier = '瑞士小包挂号';
					//}else{
//						$XRU_carrier = '瑞士小包平邮';
//					}
					$XRUshipfee = trans_carriers_fix_get($XRU_carrier, $totalweight, $ebay_countryname);
					$XRUshipfee = $XRUshipfee['fee'];
					$logfile	=	"/home/ebay_order_cronjob_logs/ups/switch_order_test_".date("Y-m-d").".log";
					$xru_log		=	"ebay: Date: ".date("Y-m-d H:i:s"). " 订单号 ".$ebay_id." 计算重量 ".$totalweight." 原运输方式 ".$ebay_carrier." 原运费 ".$oshipfee." 对比运输方式 ".$XRU_carrier." 运费 ".$XRUshipfee." \n";
					@file_put_contents($logfile, $xru_log, FILE_APPEND);
					if($XRUshipfee && !empty($oshipfee) && ($XRUshipfee < $oshipfee)){
						$sql = "UPDATE ebay_order SET ordershipfee = '$XRUshipfee', ebay_carrier = '$XRU_carrier' WHERE ebay_id = '$ebay_id' ";
						$dbcon->execute($sql);
						
						echo "\n经计算 $XRU_carrier 运费 $XRUshipfee 重量 $totalweight \n";
						$logfile	=	"/home/ebay_order_cronjob_logs/ups/switch_order_".date("Y-m-d").".log";
						$xru_log		=	"Date: ".date("Y-m-d H:i:s"). " 订单号 ".$ebay_id." 计算重量 ".$totalweight." 原运输方式 ".$ebay_carrier." 原运费 ".$oshipfee." 大于 ".$XRU_carrier." 运费 ".$XRUshipfee." 选择走".$XRU_carrier." \n";
						@file_put_contents($logfile, $xru_log, FILE_APPEND);
						$contain_swtch_track = true;
					}
				//}
			}
		}
		
		if(in_array($ebay_status, array('614','637','658','661','660','659'))){
			//ebay 线上订单EUB大于5天,平邮和挂号大于7天不发货,不包括快递
			//海外销售十天
			$timeout = false;
			$ebay_orderid = isset($ebay_order['ebay_orderid']) ? $ebay_order['ebay_orderid'] : '';
			$ebay_paidtime = isset($ebay_order['ebay_paidtime']) ? $ebay_order['ebay_paidtime'] : '';
			if(!empty($ebay_paidtime)){//线上订单,付款时间不能为空
				$diff_time = ceil((time()-$ebay_paidtime)/(3600*24));
				if(in_array($ebay_account, $SYSTEM_ACCOUNTS['ebay平台']) || in_array($ebay_account,$SYSTEM_ACCOUNTS['海外销售平台']) || in_array($ebay_account, $SYSTEM_ACCOUNTS['dresslink']) || in_array($ebay_account,$SYSTEM_ACCOUNTS['cndirect'])){
					if($ebay_carrier == 'EUB' && $diff_time > 7){
						$timeout = true;
					}else if((strpos($ebay_carrier, '平邮')!==false || strpos($ebay_carrier, '挂号')!==false) && $diff_time > 7){
						$timeout = true;
					}else if($ebay_carrier == 'Global Mail' && $diff_time > 10){
						$timeout = true;	
					}
				}else if(in_array($ebay_account,$SYSTEM_ACCOUNTS['亚马逊'])){
					if($diff_time > 7){
						$timeout = true;
					}
				}/*else if(in_array($ebay_account,$SYSTEM_ACCOUNTS['海外销售平台'])){
					if((strpos($ebay_carrier, '中国邮政平邮')!==false && $diff_time > 5) || $diff_time > 10){
						$timeout = true;
					}
				}*/
			}
			if($timeout){
				$log_data .= "\n缺货订单={$ebay_id}======移动到缺货需退款中======\n";
				$final_status = 617;//移动到缺货需退款中
				$sql = "UPDATE ebay_order SET ebay_status='$final_status' WHERE ebay_id ='$ebay_id' and ebay_status = '{$ebay_status}' ";
				$log_data .= "[".date("Y-m-d H:i:s")."]\t自动跳转---{$ebay_id}---的状态为---$final_status!\n\n";
				$dbcon->execute($sql) or die("Fail : $sql");
				//$order_statistics->replaceData($order_sn, array('mask'=>1), array('mask'=>1));
				//write_scripts_log('auto_contrast_intercept', $ebay_account, $log_data);
				unset($log_data); unset($ebay_total0); unset($ebay_id); unset($order_sn); unset($ebay_status); unset($ebay_note); unset($ebay_carrier); unset($ebay_countryname); unset($ebay_account); unset($ebay_total); unset($ebay_username); unset($ebay_orderid); unset($ebay_usermail); unset($PayPalEmailAddress);
				continue;
			}
		}
		
		if(in_array($ebay_status, array('614','637'))){//缺货处理\合并包裹处理
			$have_goodscount = true;
			foreach ($orderdetaillist AS $orderdetail){
				$sku_arr = get_realskuinfo($orderdetail['sku']);
				foreach($sku_arr as $or_sku => $or_nums){
					$allnums = $or_nums*$orderdetail['ebay_amount'];
					$skuinfo = get_sku_info($or_sku);
					$salensend = getpartsaleandnosendall($or_sku, $defaultstoreid);
					$sql = "UPDATE ebay_sku_statistics SET salensend = $salensend WHERE sku = '$or_sku' ";
					$dbcon->execute($sql);
					$log_data .= "[".date("Y-m-d H:i:s")."]\t---{$sql}\n\n";
					$log_data .= "订单===$ebay_id===料号==$or_sku===实际库存为{$skuinfo['realnums']}===B仓库库存为{$skuinfo['secondCount']}===需求量为{$allnums}===待发货数量为{$salensend}===\n";
					$realnums = isset($skuinfo['realnums']) ? $skuinfo['realnums'] : 0;
					$secondCount = isset($skuinfo['secondCount']) ? $skuinfo['secondCount'] : 0;
					if(in_array($ebay_status, array('658','661','660','659'))){
						$remainNum = $realnums + $secondCount - $allnums - $salensend;
					}else{
						$remainNum = $realnums + $secondCount - $salensend;	
					}
					if($remainNum < 0){
					//if((!isset($skuinfo['realnums']) && !isset($skuinfo['secondCount'])) || ((($skuinfo['realnums']+$skuinfo['secondCount']) - $allnums - $salensend) < 0)){//缺货本身算待发货，不能重复扣除
						$have_goodscount = false;
						break;
					}
				}
			}
			if($have_goodscount){
				$log_data .= "\n缺货订单={$ebay_id}======有货至待打印======\n";
				$final_status = 618;
				$anomalous_sql = "SELECT count(*) as anomalous_num FROM ebay_splitorder WHERE split_order_id = '$ebay_id' AND mode = 2";
				$anomalous_sql = $dbcon->execute($anomalous_sql);
				$anomalous_sql = $dbcon->fetch_one($anomalous_sql);
				if($anomalous_sql['anomalous_num'] != 0 ){
					$final_status = 686;//调到异常缺货需打印 add by chenwei 2013.3.27
				}
				/*if($contain_wh_item && in_array($ebay_carrier,$no_express_delivery)){
					$final_status = 712;
				}else */if($contain_ups){
					$final_status = 731;	
				}else if($contain_xru_track){ // add by zhiqiang.chen 2014-5-2
					$final_status = 743; //俄速通小包挂号
				}else if($contain_xru_notrack){
					$final_status = 744; //俄速通小包平邮
				}else if($contain_eub){
					$final_status = 725; //移到EUB跟踪号申请异常订单
				}else if($contain_swtch_track){
					$final_status = 750;
				}else if($contain_DGMPY){
					$final_status = 745; //移到新加坡DGM
				}
				$stock_sql = "SELECT nums FROM repeat_stock_statistics WHERE ebay_id = '$ebay_id' ";
				$stock_sql = $dbcon->execute($stock_sql);
				$stock_sql = $dbcon->fetch_one($stock_sql);
				if(!isset($stock_sql['nums'])){
					$stock_data = array('ebay_id'=>$ebay_id,'nums'=>1,'startTime'=>time());
					$sql = "INSERT INTO repeat_stock_statistics SET ".array2sql($stock_data);
					$dbcon->execute($sql);
				}else if($stock_sql['nums'] <= 2){
					$stock_data = array('nums'=>$stock_sql['nums']+1,'endTime'=>time());
					$sql = "UPDATE repeat_stock_statistics SET ".array2sql($stock_data)." WHERE ebay_id = ".$ebay_id;
					$dbcon->execute($sql);
				}else{
					$final_status = 735;
					$stock_data = array('nums'=>0,'startTime'=>0,'endTime'=>0);
					$sql = "UPDATE repeat_stock_statistics SET ".array2sql($stock_data)." WHERE ebay_id = ".$ebay_id;
					$dbcon->execute($sql);
				}
				//缺货需打印
				$sql = "UPDATE ebay_order SET ebay_status='$final_status' WHERE ebay_id ='$ebay_id' and ebay_status = '{$ebay_status}' ";
				$log_data .= "[".date("Y-m-d H:i:s")."]\t自动跳转---{$ebay_id}---的状态为---$final_status!\n\n";
				$dbcon->execute($sql) or die("Fail : $sql");
				//$order_statistics->replaceData($order_sn, array('mask'=>1), array('mask'=>1));
				//write_scripts_log('auto_contrast_intercept', $ebay_account, $log_data);
				unset($log_data); unset($ebay_total0); unset($ebay_id); unset($order_sn); unset($ebay_status); unset($ebay_note); unset($ebay_carrier); unset($ebay_countryname); unset($ebay_account); unset($ebay_total); unset($ebay_username); unset($ebay_orderid); unset($ebay_usermail); unset($PayPalEmailAddress);
				unset($stock_data);
				continue;
			}
		}else{
			$record_details = array();
			$is_640 = false;
			$is_no_location = false;
			foreach ($orderdetaillist AS $orderdetail){
				$sku_arr = get_realskuinfo($orderdetail['sku']);
				$hava_goodscount = true;
				/***筛选订单中的超大订单料号 Start ***/
				foreach($sku_arr as $t_sku => $t_num){
					$t_allnums = $t_num * $orderdetail['ebay_amount'];
					if(!check_sku($t_sku, $t_allnums)){
						$bigSkuPath = '/home/html_include/exportfile/bigOrderSkuLog/'.date('Y-m-d').'.txt';
						$bigSkuLog 	= '主订单号'.$ebay_id.'细订单号'.$orderdetail['ebay_id'].'料号'.$t_sku.'订单数量'.$t_allnums."\r\n";
						writeBigOrderSkuLog($bigSkuPath, $bigSkuLog);//日志记录
						addBigOrderSkuLog($ebay_id, $orderdetail['ebay_id'], $t_sku, $t_allnums);//添加超大订单料号日志 add by wangminwei 2014-04-16
					}
				}
				/***筛选订单中的超大订单料号 End ***/
				foreach($sku_arr as $or_sku => $or_nums){
					$allnums = $or_nums*$orderdetail['ebay_amount'];
					if (!check_sku($or_sku, $allnums)){
						//超大订单状态
						$sql = "UPDATE ebay_order SET ebay_status='640' WHERE ebay_id ='$ebay_id' and ebay_status = '{$ebay_status}' ";
						$log_data .= "[".date("Y-m-d H:i:s")."]\t自动跳转---{$ebay_id}---的状态为---640!\n\n";
						$dbcon->execute($sql) or die("Fail : $sql");
						//$order_statistics->replaceData($order_sn, array('mask'=>1), array('mask'=>1));
						insert_mark_shipping($ebay_id);
						$is_640 = true;
						break;
					}else{
						$skuinfo = get_sku_info($or_sku);
						if(empty($skuinfo['goods_location'])){
							$is_no_location = true;
						}
						$salensend = getpartsaleandnosendall($or_sku, $defaultstoreid);
						$sql = "UPDATE ebay_sku_statistics SET salensend = $salensend WHERE sku = '$or_sku' ";
						$dbcon->execute($sql);
						$log_data .= "[".date("Y-m-d H:i:s")."]\t---{$sql}\n\n";
						$log_data .= "订单===$ebay_id===料号==$or_sku===实际库存为{$skuinfo['realnums']}===B仓库库存为{$skuinfo['secondCount']}===需求量为{$allnums}===待发货数量为{$salensend}===\n";
						$realnums = isset($skuinfo['realnums']) ? $skuinfo['realnums'] : 0;
						$secondCount = isset($skuinfo['secondCount']) ? $skuinfo['secondCount'] : 0;
						if(in_array($ebay_status, array('658','661','660','659'))){
							$remainNum = $realnums + $secondCount - $allnums - $salensend;
						}else{
							$remainNum = $realnums + $secondCount - $salensend;	
						}
						if($remainNum < 0){
						//if((!isset($skuinfo['realnums']) && !isset($skuinfo['secondCount'])) || ((($skuinfo['realnums']+$skuinfo['secondCount']) - $allnums - $salensend) < 0)){//缺货本身算待发货，不能重复扣除
						//if(!isset($skuinfo['realnums']) || empty($skuinfo['realnums']) || ($skuinfo['realnums'] - $salensend - $allnums) < 0){
							$hava_goodscount = false;
							break;
						}
					}
				}
				if($hava_goodscount){$record_details[] = $orderdetail;}
			}
			if($is_640){ 
				//write_scripts_log('auto_contrast_intercept', $ebay_account, $log_data); 
				unset($log_data); unset($ebay_total0); unset($ebay_id); unset($order_sn); unset($ebay_status); unset($ebay_note); unset($ebay_carrier); unset($ebay_countryname); unset($ebay_account); unset($ebay_total); unset($ebay_username); unset($ebay_orderid); unset($ebay_usermail); unset($PayPalEmailAddress);
				continue;
			}
			$count_record_details = count($record_details);
			$count_orderdetaillist = count($orderdetaillist);
			$final_status = $ebay_status; //原始状态
			if($is_no_location/* && (in_array($ebay_account, $SYSTEM_ACCOUNTS['ebay平台']) || in_array($ebay_account,$SYSTEM_ACCOUNTS['海外销售平台']) || in_array($ebay_account, $SYSTEM_ACCOUNTS['cndirect']) || in_array($ebay_account, $SYSTEM_ACCOUNTS['dresslink']))*/){
				$final_status = 720;//无仓位订单移动到同步异常订单 add by Herman.Xi @20131129
				$sql = "UPDATE ebay_order SET ebay_status='$final_status' WHERE ebay_id ='$ebay_id' and ebay_status = '{$ebay_status}' ";
				$log_data .= "[".date("Y-m-d H:i:s")."]\t自动跳转---{$ebay_id}---的状态为---$final_status!\n\n";
				$dbcon->execute($sql) or die("Fail : $sql");
				insert_mark_shipping($ebay_id);
				//write_scripts_log('auto_contrast_intercept', $ebay_account, $log_data);
				unset($log_data); unset($ebay_total0); unset($ebay_id); unset($order_sn); unset($ebay_status); unset($ebay_note); unset($ebay_carrier); unset($ebay_countryname); unset($ebay_account); unset($ebay_total); unset($ebay_username); unset($ebay_orderid); unset($ebay_usermail); unset($PayPalEmailAddress);
				continue;
			}
			if($count_record_details == 0){
				//更新至自动拦截发货状态
				if (!in_array($ebay_carrier, $no_express_delivery)){
					$final_status = 658;
				}else {
					$final_status = 661;
				}
				$sql = "UPDATE ebay_order SET ebay_status='$final_status' WHERE ebay_id ='$ebay_id' and ebay_status = '{$ebay_status}' ";
				$log_data .= "[".date("Y-m-d H:i:s")."]\t自动跳转---{$ebay_id}---的状态为---$final_status!\n\n";
				$dbcon->execute($sql) or die("Fail : $sql");
				//$order_statistics->replaceData($order_sn, array('mask'=>1), array('mask'=>1));
				insert_mark_shipping($ebay_id);
				//write_scripts_log('auto_contrast_intercept', $ebay_account, $log_data);
				unset($log_data); unset($ebay_total0); unset($ebay_id); unset($order_sn); unset($ebay_status); unset($ebay_note); unset($ebay_carrier); unset($ebay_countryname); unset($ebay_account); unset($ebay_total); unset($ebay_username); unset($ebay_orderid); unset($ebay_usermail); unset($PayPalEmailAddress);
				continue;
			}else if($count_record_details < $count_orderdetaillist){
				//更新至自动部分发货状态
				if (!in_array($ebay_carrier, $no_express_delivery)){
					$final_status = 640;
					if(in_array($ebay_account, $SYSTEM_ACCOUNTS['cndirect']) || in_array($ebay_account, $SYSTEM_ACCOUNTS['dresslink'])){
						$final_status = 659;//add by Herman.Xi@20131202 部分包货料号订单进入
					}
				}else {
					$final_status = 660;
				}
				$sql = "UPDATE ebay_order SET ebay_status='$final_status' WHERE ebay_id ='$ebay_id' and ebay_status = '{$ebay_status}' ";
				$log_data .= "[".date("Y-m-d H:i:s")."]\t自动跳转---{$ebay_id}---的状态为---$final_status!\n\n";
				$dbcon->execute($sql) or die("Fail : $sql");
				//$order_statistics->replaceData($order_sn, array('mask'=>1), array('mask'=>1));
				insert_mark_shipping($ebay_id);
				//write_scripts_log('auto_contrast_intercept', $ebay_account, $log_data);
				unset($log_data); unset($ebay_total0); unset($ebay_id); unset($order_sn); unset($ebay_status); unset($ebay_note); unset($ebay_carrier); unset($ebay_countryname); unset($ebay_account); unset($ebay_total); unset($ebay_username); unset($ebay_orderid); unset($ebay_usermail); unset($PayPalEmailAddress);
				continue;
			}else if($count_record_details == $count_orderdetaillist){
				//正常发货状态
				if(in_array($ebay_account,$SYSTEM_ACCOUNTS['ebay平台'])){
					$status683 = false;
					if(in_array($ebay_countryname, array('Belarus','Brazil','Brasil','Argentina','Ukraine')) && str_word_count($ebay_username) < 2){
						$status683 = true;
					}
					if($status683){
						$final_status = 683;
						$sql = "UPDATE ebay_order SET ebay_status='$final_status' WHERE ebay_id ='$ebay_id' and ebay_status = '{$ebay_status}' ";
						$log_data .= "[".date("Y-m-d H:i:s")."]\t自动跳转---{$ebay_id}---的状态为---$final_status!\n\n";
						$dbcon->execute($sql) or die("Fail : $sql");
						//$order_statistics->replaceData($order_sn, array('mask'=>1), array('mask'=>1));
						//write_scripts_log('auto_contrast_intercept', $ebay_account, $log_data);
						unset($log_data); unset($ebay_total0); unset($ebay_id); unset($order_sn); unset($ebay_status); unset($ebay_note); unset($ebay_carrier); unset($ebay_countryname); unset($ebay_account); unset($ebay_total); unset($ebay_username); unset($ebay_orderid); unset($ebay_usermail); unset($PayPalEmailAddress);
						continue;	
					}
					if(in_array($ebay_status, array(658,659,660,661,720))){
						//$final_status = 618;//ebay订单自动拦截有货不能移动到待处理和有留言 modified by Herman.Xi @ 20130325(移动到缺货需打印中)
						/*if($ebay_note != ''){
							echo "有留言\t";
							$final_status = 593;
						}else{*/
							$final_status = 618;
						//}
					}else{
						/*if($ebay_note != ''){
							echo "有留言\t";
							$final_status = 593;
						}else{*/
							$final_status = 1;
						//}
					}
				}else if(in_array($ebay_account,$SYSTEM_ACCOUNTS['海外销售平台'])){
					$status683 = false;
					if(in_array($ebay_countryname, array('Belarus','Brazil','Brasil','Argentina','Ukraine')) && str_word_count($ebay_username) < 2){
						$status683 = true;
					}
					if($status683){
						$final_status = 683;
						$sql = "UPDATE ebay_order SET ebay_status='$final_status' WHERE ebay_id ='$ebay_id' and ebay_status = '{$ebay_status}' ";
						$log_data .= "[".date("Y-m-d H:i:s")."]\t自动跳转---{$ebay_id}---的状态为---$final_status!\n\n";
						$dbcon->execute($sql) or die("Fail : $sql");
						//$order_statistics->replaceData($order_sn, array('mask'=>1), array('mask'=>1));
						//write_scripts_log('auto_contrast_intercept', $ebay_account, $log_data);
						unset($log_data); unset($ebay_total0); unset($ebay_id); unset($order_sn); unset($ebay_status); unset($ebay_note); unset($ebay_carrier); unset($ebay_countryname); unset($ebay_account); unset($ebay_total); unset($ebay_username); unset($ebay_orderid); unset($ebay_usermail); unset($PayPalEmailAddress);
						continue;	
					}
					if(in_array($ebay_status, array(658,659,660,661,720))){
						//$final_status = 629; //德国订单区别于正常订单
						$final_status = 618; //modified by Herman.Xi @20130823 雷贤容需要修改成缺货需打印中
						$sql = "UPDATE ebay_order SET ebay_status='$final_status' WHERE ebay_id ='$ebay_id' and ebay_status = '{$ebay_status}' ";
						$log_data .= "[".date("Y-m-d H:i:s")."]\t自动跳转---{$ebay_id}---的状态为---$final_status!\n\n";
						$dbcon->execute($sql) or die("Fail : $sql");
						//$order_statistics->replaceData($order_sn, array('mask'=>1), array('mask'=>1));
						//write_scripts_log('auto_contrast_intercept', $ebay_account, $log_data);
						unset($log_data); unset($ebay_total0); unset($ebay_id); unset($order_sn); unset($ebay_status); unset($ebay_note); unset($ebay_carrier); unset($ebay_countryname); unset($ebay_account); unset($ebay_total); unset($ebay_username); unset($ebay_orderid); unset($ebay_usermail); unset($PayPalEmailAddress);
						continue;
					}else{
						/*if($ebay_note != ''){
							echo "德国订单有留言\t";
							$final_status = 593;
						}else{*/
							$final_status = 1;
						//} 
						//德国订单进入正常订单流程
					}
				}else if(in_array($ebay_account, $SYSTEM_ACCOUNTS['aliexpress']) /*|| in_array($ebay_account, $SYSTEM_ACCOUNTS['B2B外单'])*/){
					$final_status = 595;
					$status683 = false;
					if(in_array($ebay_countryname, array('Russian Federation', 'Russia')) && strpos($ebay_carrier, '中国邮政')!==false && str_word_count($ebay_username) < 2){
						$status683 = true;
					}
					if(in_array($ebay_countryname, array('Belarus','Brazil','Brasil','Argentina','Ukraine')) && str_word_count($ebay_username) < 2){
						$status683 = true;
					}
					if($status683){
						$final_status = 683;
						$sql = "UPDATE ebay_order SET ebay_status='$final_status' WHERE ebay_id ='$ebay_id' and ebay_status = '{$ebay_status}' ";
						$log_data .= "[".date("Y-m-d H:i:s")."]\t自动跳转---{$ebay_id}---的状态为---$final_status!\n\n";
						$dbcon->execute($sql) or die("Fail : $sql");
						//$order_statistics->replaceData($order_sn, array('mask'=>1), array('mask'=>1));
						//write_scripts_log('auto_contrast_intercept', $ebay_account, $log_data);
						unset($log_data); unset($ebay_total0); unset($ebay_id); unset($order_sn); unset($ebay_status); unset($ebay_note); unset($ebay_carrier); unset($ebay_countryname); unset($ebay_account); unset($ebay_total); unset($ebay_username); unset($ebay_orderid); unset($ebay_usermail); unset($PayPalEmailAddress);
						continue;	
					}
				}else if(in_array($ebay_account, $SYSTEM_ACCOUNTS['DHgate'])){
					$final_status = 620;
				}else if(in_array($ebay_account, $SYSTEM_ACCOUNTS['dresslink'])){
					$final_status = 1;
				}else if(in_array($ebay_account, $SYSTEM_ACCOUNTS['cndirect'])){
					$final_status = 1;
				}else if(in_array($ebay_account, $SYSTEM_ACCOUNTS['亚马逊'])){
					if(in_array($ebay_status, array(658,659,660,661,720))){
						if (in_array($ebay_carrier, $no_express_delivery)){
							$final_status = 618; //modified by Herman.Xi @20131106 刘丽需要修改成缺货需打印中
						}else if($ebay_carrier == 'FedEx'){
							$final_status = 639; //modified by Herman.Xi @20131213 刘丽需要修改线下订单导入
						}else{
							$final_status = 641; //modified by Herman.Xi @20131119 刘丽需要修改成待打印线下和异常订单
						}
						$sql = "UPDATE ebay_order SET ebay_status='$final_status' WHERE ebay_id ='$ebay_id' and ebay_status = '{$ebay_status}' ";
						$log_data .= "[".date("Y-m-d H:i:s")."]\t自动跳转---{$ebay_id}---的状态为---$final_status!\n\n";
						$dbcon->execute($sql) or die("Fail : $sql");
						//write_scripts_log('auto_contrast_intercept', $ebay_account, $log_data);
						unset($log_data); unset($ebay_total0); unset($ebay_id); unset($order_sn); unset($ebay_status); unset($ebay_note); unset($ebay_carrier); unset($ebay_countryname); unset($ebay_account); unset($ebay_total); unset($ebay_username); unset($ebay_orderid); unset($ebay_usermail); unset($PayPalEmailAddress);
						continue;
					}else{
						$final_status = 1;
					}
				}else{
					$final_status = 1;
				}
				
				/*if(judge_contain_combinesku($order_sn)){
					$final_status = 606;
				}*/
				if(in_array($ebay_account,$SYSTEM_ACCOUNTS['ebay平台']) || in_array($ebay_account,$SYSTEM_ACCOUNTS['海外销售平台'])){
					if($ebay_note != ''){
						//echo "有留言\t";
						$final_status = 593;
					}
				}
				if($totalweight > 2){
					//echo "\t 超重订单";
					$final_status = 608;
				}
				
				if (!in_array($ebay_carrier, $no_express_delivery) && !empty($ebay_carrier)){
					if(in_array($ebay_account,$SYSTEM_ACCOUNTS['ebay平台']) || in_array($ebay_account,$SYSTEM_ACCOUNTS['海外销售平台'])){
						$final_status = 641;//ebay和海外都跳转到 待打印线下和异常订单
					}else{
						$final_status = 639;
					}
				}
				
				/*add by Herman.Xi @20131103
				 *包含粉末，膏状，液体，指甲油，内置电池(除手表外)非快递先临时拦截下来。
				 */
				/*if(in_array($ebay_carrier,array('中国邮政平邮','中国邮政挂号','香港小包平邮','香港小包挂号','德国邮政挂号','新加坡小包挂号')) && $contain_hold131103){
					$final_status = 704;
					insert_mark_shipping($ebay_id);	 
				}*/
					/*if($contain_wh_item && in_array($ebay_carrier,$no_express_delivery)){
						$final_status = 712;
						insert_mark_shipping($ebay_id);
					}else */if($contain_ups){// add by Herman.Xi @20140317
						$final_status = 731;
						insert_mark_shipping($ebay_id);
					}else if($contain_xru_track){ // add by zhiqiang.chen 2014-5-2
						$final_status = 743; //俄速通小包挂号
						insert_mark_shipping($ebay_id);
					}else if($contain_xru_notrack){
						$final_status = 744; //俄速通小包平邮
						insert_mark_shipping($ebay_id);
					}else if($contain_eub){
						$final_status = 725; //移到EUB跟踪号申请异常订单
						insert_mark_shipping($ebay_id);
					}else if($contain_swtch_track){
						$final_status = 750;
						insert_mark_shipping($ebay_id);
					}else if($contain_DGMPY){
						$final_status = 745; //移到新加坡DGM
						insert_mark_shipping($ebay_id);
					}
				
				$sql = "UPDATE ebay_order SET ebay_status='$final_status' WHERE ebay_id ='$ebay_id' and ebay_status = '{$ebay_status}' ";
				$log_data .= "[".date("Y-m-d H:i:s")."]\t自动跳转---{$ebay_id}---的状态为---$final_status!\n\n";
				$dbcon->execute($sql) or die("Fail : $sql");
				//$order_statistics->replaceData($order_sn, array('mask'=>1), array('mask'=>1));
				$log_data .= "\n-------------------end ----------------------\n";
			}else{
				$log_data .= "[".date("Y-m-d H:i:s")."]\t订单$ebay_id同步状态有误,请联系IT解决!";
			}
		}
		//write_scripts_log('auto_contrast_intercept', $ebay_account, $log_data);
		unset($log_data); unset($ebay_total0); unset($ebay_id); unset($order_sn); unset($ebay_status); unset($ebay_note); unset($ebay_carrier); unset($ebay_countryname); unset($ebay_account); unset($ebay_total); unset($ebay_username); unset($ebay_orderid); unset($ebay_usermail); unset($PayPalEmailAddress);
	}
	unset($ebay_orders);
}



function function_wrapped_unmerge($ebay_orders){
	//缺货订单,合并包裹缺货订单,解除其合并包裹关系之后再进行自动拦截判断
	//其余未解除合并包裹关系的包裹重新合并
	//add Herman.Xi 2013-02-28
	global $dbcon, $user, $defaultstoreid, $order_statistics, $SYSTEM_ACCOUNTS;
	$express_delivery = array('UPS','DHL','TNT','EMS','FedEx');
	$no_express_delivery = array('中国邮政平邮','中国邮政挂号','香港小包平邮','香港小包挂号','德国邮政挂号','新加坡小包挂号','EUB','Global Mail','俄速通平邮','俄速通挂号');
	foreach($ebay_orders as $ebay_order){
		$log_data = "";//日志记录
		$ebay_id = $ebay_order['ebay_id'];
		$order_sn = $ebay_order['ebay_ordersn'];
		$ebay_status = $ebay_order['ebay_status'];
		$ebay_note = $ebay_order['ebay_note'];
		$ebay_carrier = $ebay_order['ebay_carrier'];
		$ebay_countryname = $ebay_order['ebay_countryname'];
		$ebay_account = $ebay_order['ebay_account'];
		$ebay_total	= $ebay_order['ebay_total'];
		$ebay_noteb = $ebay_order['ebay_noteb'];
		$combine_package = $ebay_order['combine_package'];
		if($ebay_status == '614'){
			//缺货
			$sql = "select ebay_id,ebay_status,ebay_noteb from ebay_order where combine_package = '$ebay_id' and ebay_combine != 1 and ebay_user = '$user' ";
			$result = $dbcon->execute($sql);
			$eo_arr = $dbcon->getResultArray($result);
			$quehuo_array = array();
			$quehuo_info = array();
			foreach($eo_arr as $row){
				$quehuo_array[$row['ebay_status']][] = $row['ebay_id'];
				$quehuo_info[$row['ebay_id']] = $row;
			}
			if(count($quehuo_array)){
				//合并主订单,主订单缺货的情况。
				if(isset($quehuo_array['614'])){
					foreach($quehuo_array['614'] as $val){
						$log_data .= "[".date("Y-m-d H:i:s")."]\t缺货状态的合并包裹子订单 {$val} 解除合并包裹关系,主定单号为 {$ebay_id} \n\n";
						echo "\n[".date("Y-m-d H:i:s")."]\t缺货状态的合并包裹子订单 {$val} 解除合并包裹关系,主定单号为 {$ebay_id} \n";
						$new_ebay_noteb = preg_replace("/该订单被\d{1,}合并/", "", $quehuo_info[$val]['ebay_noteb']);
						$update_sql = "update ebay_order set combine_package = 0, ebay_noteb = '{$new_ebay_noteb}' where ebay_id = '$val' ";
						$dbcon->execute($update_sql);
					}
				}
				if(isset($quehuo_array['2'])){
					foreach($quehuo_array['2'] as $val){
						$log_data .= "[".date("Y-m-d H:i:s")."]\t缺货状态的合并包裹子订单 {$val} 解除合并包裹关系,主定单号为 {$ebay_id} \n\n";
						echo "\n[".date("Y-m-d H:i:s")."]\t缺货状态的合并包裹子订单 {$val} 解除合并包裹关系,主定单号为 {$ebay_id} \n";
						$new_ebay_noteb = preg_replace("/该订单被\d{1,}合并/", "", $quehuo_info[$val]['ebay_noteb']);
						$update_sql = "update ebay_order set combine_package = 0, ebay_noteb = '{$new_ebay_noteb}' where ebay_id = '$val' ";
						$dbcon->execute($update_sql);
					}
					if(count($quehuo_array['2'])>1){
						echo "\n======已经发货的需要重新合并包裹的子订单,导出数据需要======\n";
						function_merging_parcel($quehuo_array['2'],$ebay_countryname,$ebay_id,$ebay_account);
					}
				}
				$new_ebay_noteb = preg_replace("/该订单为\#\[.{1,}\]\#合并包裹发货/", "", $ebay_noteb);
				$update_sql = "update ebay_order set combine_package = 0, ebay_noteb = '{$new_ebay_noteb}' where ebay_id = '$ebay_id' ";
				$dbcon->execute($update_sql);
				foreach($quehuo_info as $qvalue){
					$new_ebay_noteb = preg_replace("/该订单被\d{1,}合并/", "", $qvalue['ebay_noteb']);
					$update_sql = "update ebay_order set combine_package = 0, ebay_noteb = '{$new_ebay_noteb}' where ebay_id = '{$qvalue['ebay_id']}' ";
					$dbcon->execute($update_sql);
				}
				$sql = "update ebay_splitorder set recordnumber = 0 where main_order_id = $ebay_id and mode = 3 ";
				$log_data .= "[".date("Y-m-d H:i:s")."]\t合并包裹主订单缺货,目前在缺货状态。解除主订单 {$ebay_id} 合并包裹的关系\n\n";
				echo "\n[".date("Y-m-d H:i:s")."]\t合并包裹主订单缺货,目前在缺货状态。解除主订单 {$ebay_id} 合并包裹的关系\n\n";
				$dbcon->execute($sql);
			}else if($combine_package != 0){
				//子订单解除关系
				$sql = "select combine_package from ebay_order where ebay_id = $ebay_id and ebay_combine != 1 and ebay_user = '$user'";
				$result = $dbcon->execute($sql);
				$son_order = $dbcon->fetch_one($result);
				
				$sql = "select ebay_id,ebay_status,ebay_noteb from ebay_order where ebay_id = '{$son_order['combine_package']}' and ebay_combine != 1 and ebay_user = '$user' ";
				$result = $dbcon->execute($sql);
				$par_order = $dbcon->fetch_one($result);
				if(in_array($par_order['ebay_status'], array(614,637))){
					//合并主订单同时没货
					$sql = "update ebay_splitorder set recordnumber = 0 where main_order_id = {$son_order['combine_package']} and mode = 3 ";
					$log_data .= "[".date("Y-m-d H:i:s")."]\t合并包裹主订单缺货,目前在缺货状态。解除主订单 {$son_order['combine_package']} 合并包裹的关系\n\n";
					echo "\n[".date("Y-m-d H:i:s")."]\t合并包裹主订单缺货,目前在缺货状态。解除主订单 {$son_order['combine_package']} 合并包裹的关系\n\n";
					$new_ebay_noteb = preg_replace("/该订单为\#\[.{1,}\]\#合并包裹发货/", "", $par_order['ebay_noteb']);
					$update_sql = "update ebay_order set combine_package = 0, ebay_noteb = '{$new_ebay_noteb}' where ebay_id = '{$son_order['combine_package']}' ";
					$dbcon->execute($update_sql);
					$sql = "select ebay_id,ebay_status,ebay_noteb from ebay_order where combine_package = '{$son_order['combine_package']}' and ebay_combine != 1 and ebay_user = '$user' ";
					$result = $dbcon->execute($sql);
					$all_son_orders = $dbcon->getResultArray($result);
					foreach($all_son_orders as $all_order_value){
						//合并子订单
						$log_data .= "[".date("Y-m-d H:i:s")."]\t缺货状态的合并包裹子订单 {$all_order_value['ebay_id']} 解除合并包裹关系,主定单号为 {$son_order['combine_package']} \n\n";
						echo "\n[".date("Y-m-d H:i:s")."]\t缺货状态的合并包裹子订单 {$all_order_value['ebay_id']} 解除合并包裹关系,主定单号为 {$son_order['combine_package']} \n\n";
						$new_ebay_noteb = preg_replace("/该订单被\d{1,}合并/", "", $all_order_value['ebay_noteb']);
						$update_sql = "update ebay_order set combine_package = 0, ebay_noteb = '{$new_ebay_noteb}' where ebay_id = '{$all_order_value['ebay_id']}' ";
						$dbcon->execute($update_sql);
						$sql = "update ebay_splitorder set recordnumber = 0 where main_order_id = {$son_order['combine_package']} and split_order_id = {$all_order_value['ebay_id']} and mode = 3 ";
						$log_data .= "[".date("Y-m-d H:i:s")."]\t合并包裹子订单{$all_order_value['ebay_id']}订单缺货,目前在缺货状态。解除子订单 {$all_order_value['ebay_id']} 合并包裹的关系\n\n";
						echo "\n[".date("Y-m-d H:i:s")."]\t合并包裹主订单缺货,目前在缺货状态。解除主订单 {$son_order['combine_package']} 合并包裹的关系\n\n";
						$dbcon->execute($sql);
					}
				}else{
					//合并子订单
					$log_data .= "[".date("Y-m-d H:i:s")."]\t缺货状态的合并包裹子订单 {$ebay_id} 解除合并包裹关系,主定单号为 {$combine_package} \n\n";
					echo "\n[".date("Y-m-d H:i:s")."]\t缺货状态的合并包裹子订单 {$ebay_id} 解除合并包裹关系,主定单号为 {$combine_package} \n\n";
					$new_ebay_noteb = preg_replace("/该订单被\d{1,}合并/", "", $ebay_noteb);
					$update_sql = "update ebay_order set combine_package = 0, ebay_noteb = '{$new_ebay_noteb}' where ebay_id = '$ebay_id' ";
					$dbcon->execute($update_sql);
					$sql = "update ebay_splitorder set recordnumber = 0 where main_order_id = {$son_order['combine_package']} and split_order_id = {$ebay_id} and mode = 3 ";
					$log_data .= "[".date("Y-m-d H:i:s")."]\t合并包裹子订单{$ebay_id}订单缺货,目前在缺货状态。解除子订单 {$ebay_id} 合并包裹的关系\n\n";
					echo "\n[".date("Y-m-d H:i:s")."]\t合并包裹主订单缺货,目前在缺货状态。解除主订单 {$son_order['combine_package']} 合并包裹的关系\n\n";
					$dbcon->execute($sql);
				}
			}
		}else if($ebay_status == '637'){
			//合并包裹缺货
			if($combine_package!=0){//从订单
				$log_data .= "[".date("Y-m-d H:i:s")."]\t缺货状态的合并包裹子订单 {$ebay_id} 解除合并包裹关系,主定单号为 {$combine_package} \n\n";
				echo "\n[".date("Y-m-d H:i:s")."]\t缺货状态的合并包裹子订单 {$ebay_id} 解除合并包裹关系,主定单号为 {$combine_package} \n\n";
				$new_ebay_noteb = preg_replace("/该订单被\d{1,}合并/", "", $ebay_noteb);
				$update_sql = "update ebay_order set combine_package = 0, ebay_noteb = '{$new_ebay_noteb}' where ebay_id = '$ebay_id' ";
				$dbcon->execute($update_sql);
				$sql = "update ebay_splitorder set recordnumber = 0 where main_order_id = {$combine_package} and split_order_id = {$ebay_id} and mode = 3 ";
				$log_data .= "[".date("Y-m-d H:i:s")."]\t合并包裹子订单{$ebay_id}订单缺货,目前在缺货状态。解除子订单 {$ebay_id} 合并包裹的关系\n\n";
				echo "\n[".date("Y-m-d H:i:s")."]\t合并包裹主订单缺货,目前在缺货状态。解除主订单 {$combine_package} 合并包裹的关系\n\n";
				$dbcon->execute($sql);
			}else{//住订单
				$log_data .= "[".date("Y-m-d H:i:s")."]\t缺货状态的合并包裹主订单 {$ebay_id} 解除合并包裹关系,主定单号为 {$ebay_id} \n\n";
				echo "\n[".date("Y-m-d H:i:s")."]\t缺货状态的合并包裹主订单 {$ebay_id} 解除合并包裹关系,主定单号为 {$ebay_id} \n\n";
				$new_ebay_noteb = preg_replace("/该订单为\#\[.{1,}\]\#合并包裹发货/", "", $ebay_noteb);
				$update_sql = "update ebay_order set combine_package = 0, ebay_noteb = '{$new_ebay_noteb}' where ebay_id = '$ebay_id' ";
				$dbcon->execute($update_sql);
				
				$sql = "update ebay_splitorder set recordnumber = 0 where main_order_id = $ebay_id and mode = 3 ";
				$log_data .= "[".date("Y-m-d H:i:s")."]\t解除主订单 {$ebay_id} 合并包裹的关系\n\n";
				echo "\n[".date("Y-m-d H:i:s")."]\t解除主订单 {$ebay_id} 合并包裹的关系\n\n";
				$dbcon->execute($sql);
			}
		}
		if(!empty($log_data)){
			write_scripts_log('out_stock_intercept', $ebay_account, $log_data);
		}
	}
}

function function_merging_parcel($ebay_ids,$ebay_countryname,$ebay_id,$ebay_account){
	//合并包裹功能----手动合并功能。
	//add by Herman.Xi @ 20130228
	global $dbcon, $user, $mctime;
	echo "\n===合并子订单号为".join(",", $ebay_ids)."===主订单号为{$ebay_id}\n";
	$mailsql	= "SELECT mailway FROM ebay_scan_mailway WHERE ebay_id={$ebay_id} ";
	$mailsql	= $dbcon->execute($mailsql);
	$mailsql	= $dbcon->fetch_one($mailsql);
	
	$sql = "SELECT * FROM ebay_order WHERE ebay_id in (".join(",", $ebay_ids).") and combine_package = 0 and ebay_user = '$user' ";
	$sql = $dbcon->execute($sql);
	$sameorders = $dbcon->getResultArray($sql);
	//判断组合后订单是否超重(2KG)
	$totalweight = 0;
	$totalmoney = 0;
	$weightarray = array();
	$order_sn = array();
	$shippinglist = array();
	foreach ($sameorders AS $sameorder){
		$totalweight += $sameorder['orderweight'];
		$totalmoney += $sameorder['ebay_total'];
		$order_sn[] = $sameorder['ebay_ordersn'];
		$weightarray[] = $sameorder['orderweight'];
	}
		
	//开始合并准备操作
	$combineorder = array();
	$firstorder = array_shift($sameorders);
	$fees			= calcshippingfee($totalweight,$ebay_countryname,$ebay_id,$ebay_account,$totalmoney);
	$ebay_carrier	= $fees[0];
	$totalweight	= $fees[2];
	$weight2fee = calceveryweight($weightarray, $fees[1]);
	//ebay_unmerge_temp_table
	$firstweightfee = array_shift($weight2fee);
	foreach ($sameorders AS $_k=>$sameorder){
		$combineorder[] = $sameorder['ebay_id'];
		$ebay_noteb = "该订单被{$firstorder['ebay_id']}合并";
		$sql = "UPDATE ebay_order SET ebay_noteb='{$ebay_noteb}',combine_package={$firstorder['ebay_id']}, ordershipfee={$weight2fee[$_k]} WHERE ebay_id={$sameorder['ebay_id']}";
		$dbcon->execute($sql);
		$sql = "insert into ebay_splitorder (recordnumber, main_order_id, split_order_id, mode, create_date) values ('1', '{$firstorder['ebay_id']}', '{$sameorder['ebay_id']}', 3, '".date("Y-m-d H:i:s")."')";
		$dbcon->execute($sql);
	}
	$sql = "REPLACE INTO ebay_unmerge_temp_table SET ebay_id={$ebay_id},merge_str='".join(",", $ebay_ids)."',now_primaryid='{$firstorder['ebay_id']}',mailway='{$mailsql['mailway']}',addtime={$mctime}";
	$dbcon->execute($sql);
	$combinestr = implode(',', $combineorder);
	$ebay_noteb = "该订单为#[{$combinestr}]#合并包裹发货";
	
	//同步合并后信息
	$sql = "UPDATE ebay_order SET ebay_noteb='{$ebay_noteb}', ordershipfee={$firstweightfee} WHERE ebay_id ={$firstorder['ebay_id']}";
	$dbcon->execute($sql);
	echo "\n==重新合并从订单,首订单{$firstorder['ebay_id']}==从订单为{$combinestr}\n";
}

function function_split_partinterpet($ebay_orders){
	//自动部分包货拦截,超过半天的库存有的拆分出来
	//add Herman.Xi 2013-03-15
	/* 海外销售
	我这边两个或者两个以上SKU有货的,请拆分有货的发货(雷贤容)
	*/
	/* B2B
	1、两个SKU以上有货就拆分出来发货
	2、拆分订单,导出销售数据时需标记拆分订单(ebay的数据导出有这个,麻烦确认此规则B2B这边是否有实现?之前我们都没有用自动拆分订单的)
	3、拆分订单的订单金额B2B这边不根据货本分摊,请只把订单金额保留在主订单/被拆分订单上,拆分订单的金额显示为0,此规则参照B2B复制订单规则(ebay那边维持现有的拆分规则不做改动)。
	*/
	/*
	ebay
	chenxiaoxia(陈小霞) 2013-03-11 23:29:31
	有货就发
	*/
	global $dbcon, $user, $defaultstoreid, $order_statistics, $SYSTEM_ACCOUNTS;
	
	/*include_once '/data/scripts/ebay_order_cron_job/config_row/config_database_row_master.php';
	unset($dbcon);
	$dbcon	= new DBClass();*/
	//$express_delivery = array('UPS','DHL','TNT','EMS','FedEx');
	$no_express_delivery = array('中国邮政平邮','中国邮政挂号','香港小包平邮','香港小包挂号','德国邮政挂号','新加坡小包挂号','EUB','Global Mail','俄速通平邮','俄速通挂号');
	//echo count($ebay_orders); echo "\n";
	foreach($ebay_orders as $ebay_order){
		$log_data = "";//日志记录
		$ebay_id = $ebay_order['ebay_id'];
		$order_sn = $ebay_order['ebay_ordersn'];
		$ebay_status = $ebay_order['ebay_status'];
		//$ebay_status = $ebay_order['ebay_status'];
		$ebay_note = $ebay_order['ebay_note'];
		$ebay_carrier0 = $ebay_order['ebay_carrier'];//原订单运输方式
		$ebay_tracknumber = $ebay_order['ebay_tracknumber'];
		if($ebay_carrier0 == 'EUB' && empty($ebay_tracknumber) && (in_array($ebay_account,$SYSTEM_ACCOUNTS['ebay平台']) || in_array($ebay_account,$SYSTEM_ACCOUNTS['海外销售平台']))){
			$log_data .= " 自动部分发货订单 $ebay_id 运输方式为 EUB 需要先申请跟踪号才能进行 不能进行拆分操作，直接过滤!\n";
			continue;
		}
		$ebay_countryname = $ebay_order['ebay_countryname'];
		$ebay_account = $ebay_order['ebay_account'];
		
		$recordorder = 'order'.$ebay_id;
		if(isset($$recordorder)){
			$log_data .= " 自动部分发货订单 $ebay_id 已经有拆分操作，PHP直接过滤!\n";
			continue;
		}
		$sql = "SELECT * FROM ebay_splitorder WHERE main_order_id = '{$ebay_id}' and mode = 5 ";
		$sql = $dbcon->execute($sql);
		//var_dump($dbcon->error);
		$epNums = $dbcon->num_rows($sql);
		if($epNums > 0){
			continue;
		}
		$dbcon->error = '';
		
		$sql = "SELECT sku,ebay_amount FROM ebay_orderdetail WHERE ebay_ordersn='{$order_sn}'";
		$result = $dbcon->execute($sql);
		$orderdetaillist = $dbcon->getResultArray($result);
		
		$part_intercept = array();
		foreach ($orderdetaillist AS $orderdetail){
			$sku_arr = get_realskuinfo($orderdetail['sku']);
			$hava_goodscount = true;
			foreach($sku_arr as $or_sku => $or_nums){
				$allnums 	= $or_nums*$orderdetail['ebay_amount'];
				$skuinfo 	= get_sku_info($or_sku);
				$salensend 	= getpartsaleandnosendall($or_sku, $defaultstoreid);
				if((!isset($skuinfo['realnums']) && !isset($skuinfo['secondCount'])) || ((($skuinfo['realnums']+$skuinfo['secondCount']) - $salensend - $allnums) < 0)){
				//if(!isset($skuinfo['realnums']) || empty($skuinfo['realnums']) || (($skuinfo['realnums'] - $salensend - $allnums) < 0)){
					$hava_goodscount = false;
					break;
				}
			}
			if($hava_goodscount){
				$part_intercept['yes'][] = $orderdetail['sku'];//部分有货
			}else{
				$part_intercept['no'][] = $orderdetail['sku'];//部分没货
			}
		}
		
		if(in_array($ebay_status, array('660'))){//自动部分包货非快递拆分处理
			if(!in_array($ebay_carrier0, $no_express_delivery)){
				continue;
			}
			if(isset($part_intercept['yes']) && isset($part_intercept['no']) && (count($part_intercept['yes']) < count($orderdetaillist)) && (count($part_intercept['no']) < count($orderdetaillist))){
				if(in_array($ebay_account,$SYSTEM_ACCOUNTS['ebay平台'])){
					echo "\n===========".$ebay_id."============\n";
					echo "\n-------ebay--------\n";
					//echo "yes\n"; print_r($part_intercept['yes']); echo "\n";
					//echo "no\n"; print_r($part_intercept['no']); echo "\n";
					if(count($part_intercept['yes']) > 0){ //有货的等于或者超过1个
						$all_weight = array();
						$all_ebay_id = array();
						$all_ebay_total = array();
						$insertarr = $ebay_order;
						unset($insertarr['ebay_id']);
						if($ebay_carrier0 != 'EUB'){
							unset($insertarr['ebay_tracknumber']);
						}
						$new_ordersn = generateOrdersn();
						$insertarr['ebay_ordersn'] = $new_ordersn;
						$insertarr['ebay_status'] = 618;
						$insertarr['ebay_addtime'] = time();
						$sql = "INSERT INTO ebay_order SET ".array2sql($insertarr);
						$dbcon->execute($sql);
						$insert_ebay_id = $dbcon->insert_id();
						$log_data .= "\n[".date("Y-m-d H:i:s")."]\t 【ebay】自动部分发货非快递订单 $ebay_id 拆分出来有货的订单 $insert_ebay_id \n";
						$sql = "select * from ebay_orderdetail where ebay_ordersn = '{$ebay_order['ebay_ordersn']}'";
						$result = $dbcon->execute($sql);
						$orderdetails = $dbcon->getResultArray($result);
						$total_moneny = '';
						$ebay_total = 0;
						foreach ($orderdetails AS $orderdetail){
							if(in_array($orderdetail['sku'], $part_intercept['yes'])){
								$ebay_total += $orderdetail['ebay_itemprice']*$orderdetail['ebay_amount'] + $orderdetail['shipingfee'];
								$insertarr = $orderdetail;
								unset($orderdetail['ebay_id']);
								$orderdetail['ebay_ordersn'] = $new_ordersn;
								$total_moneny = ($orderdetail['sku'] + $orderdetail['sku']) * $orderdetail['sku'];
								$sql = "INSERT INTO ebay_orderdetail SET ".array2sql($orderdetail);
								if($dbcon->execute($sql)){
									$log_data .= "\n[".date("Y-m-d H:i:s")."]\t 【ebay】自动部分发货非快递订单 $ebay_id 拆分出来有货的订单 $insert_ebay_id --添加明细成功!\n";
								}else{
									$log_data .= "\n[".date("Y-m-d H:i:s")."]\t 【ebay】自动部分发货非快递订单 $ebay_id 拆分出来有货的订单 $insert_ebay_id --添加明细失败\n";	
								}
							}
						}
						//$order_statistics->replaceData($new_ordersn, array('mask'=>1), array('mask'=>1));
						$totalweight 	= recalcorderweight($new_ordersn, $ebay_packingmaterial);
						$all_weight[] = $totalweight;
						$all_ebay_id[] = $insert_ebay_id;
						$all_ebay_total[] = $ebay_total;
						
						if(count($part_intercept['no']) > 0){//没货的
							$insertarr = $ebay_order;
							unset($insertarr['ebay_id']);
							unset($insertarr['ebay_tracknumber']);
							$new_ordersn = generateOrdersn();
							$insertarr['ebay_ordersn'] = $new_ordersn;
							$insertarr['ebay_status'] = 661; //自动拦截非快递
							$insertarr['ebay_addtime'] = time();
							$sql = "INSERT INTO ebay_order SET ".array2sql($insertarr);
							$dbcon->execute($sql);
							$insert_ebay_id = $dbcon->insert_id();
							$log_data .= "\n[".date("Y-m-d H:i:s")."]\t 【ebay】自动部分发货非快递订单 $ebay_id 拆分出来无货的订单 $insert_ebay_id \n";
							$sql = "select * from ebay_orderdetail where ebay_ordersn = '{$ebay_order['ebay_ordersn']}'";
							$result = $dbcon->execute($sql);
							$orderdetails = $dbcon->getResultArray($result);
							$ebay_total = 0;
							foreach ($orderdetails AS $orderdetail){
								if(in_array($orderdetail['sku'], $part_intercept['no'])){
									$ebay_total += $orderdetail['ebay_itemprice']*$orderdetail['ebay_amount'] + $orderdetail['shipingfee'];
									$insertarr = $orderdetail;
									unset($orderdetail['ebay_id']);
									$orderdetail['ebay_ordersn'] = $new_ordersn;
									$sql = "INSERT INTO ebay_orderdetail SET ".array2sql($orderdetail);
									if($dbcon->execute($sql)){
										$log_data .= "\n[".date("Y-m-d H:i:s")."]\t 【ebay】自动部分发货非快递订单 $ebay_id 拆分出来无货的订单 $insert_ebay_id --添加明细成功!\n";
									}else{
										$log_data .= "\n[".date("Y-m-d H:i:s")."]\t 【ebay】自动部分发货非快递订单 $ebay_id 拆分出来无货的订单 $insert_ebay_id --添加明细失败\n";
									}
								}
							}
							//$order_statistics->replaceData($new_ordersn, array('mask'=>1), array('mask'=>1));
							$totalweight 	= recalcorderweight($new_ordersn, $ebay_packingmaterial);
							$all_weight[] = $totalweight;
							$all_ebay_id[] = $insert_ebay_id;
							$all_ebay_total[] = $ebay_total;
						}
						echo "重量\n"; print_r($all_weight); echo "\n";
						echo "价格\n"; print_r($all_ebay_total); echo "\n";
						echo "ids\n"; print_r($all_ebay_id); echo "\n";
						foreach($all_ebay_id as $i => $v){
							//$ebay_total = round($ebay_order['ebay_total']*($all_weight[$i]/array_sum($all_weight)),2);
							//$shipfee 	= calctrueshippingfee($ebay_carrier, $all_weight[$i], $ebay_countryname, $v);
							$fees			= calcshippingfee($all_weight[$i],$ebay_countryname,$all_ebay_id[$i],$ebay_account,$all_ebay_total[$i]);
							$ebay_carrier	= $fees[0];
							$shipfee		= $fees[1];
							//$totalweight	= $fees[2];
							echo "\n经计算 运费 $shipfee 重量 {$all_weight[$i]} 包装材料\n";
							$bb	= "update ebay_order set ordershipfee='$shipfee', orderweight ='{$all_weight[$i]}' ,ebay_carrier = '$ebay_carrier', ebay_total = '{$all_ebay_total[$i]}' where ebay_id ='$v' ";
							$dbcon->execute($bb);
							$sql = "insert into ebay_splitorder (recordnumber, main_order_id, split_order_id, mode, create_date) values 
							('{$ebay_order['recordnumber']}', '$ebay_id', '$v', '5', '".date("Y-m-d H:i:s")."')";
							$dbcon->execute($sql);
						}
						/*if($all_weight[0] > 2){//拆分之后的有货订单自动跳转到超重订单中
							$sql	= "update ebay_order set ebay_status='608' where ebay_id ='{$all_ebay_id[0]}' ";
							$log_data .= "ebay==自动部分发货非快递有货订单{$all_ebay_id[0]} 超重移动到超重订单文件夹中!\n";
							$dbcon->execute($sql);
						}*/
						$$recordorder = true;
						if(count($all_ebay_id) == 2){
							$log_data .= "\n[".date("Y-m-d H:i:s")."]\t 【ebay】自动部分发货非快递订单 是原订单 移动到回收站中!\n";
							if($ebay_carrier0 != 'EUB'){
								$sql = "update ebay_order set ebay_status = '615' where ebay_id ='$ebay_id' "; // 原来的订单隐藏
							}else{//modified by Herman.Xi @20140517 EUB原订单拆分需要取消跟踪号
								$sql = "update ebay_order set ebay_status = '615',ebay_tracknumber='' where ebay_id ='$ebay_id' "; // 原来的订单隐藏	
							}
							$dbcon->execute($sql);	
						}
					}	
				}else if(in_array($ebay_account,$SYSTEM_ACCOUNTS['海外销售平台'])){
					echo "\n===========".$ebay_id."============\n";
					echo "\n-------海外销售--------\n";
					//echo "yes\n"; print_r($part_intercept['yes']); echo "\n";
					//echo "no\n"; print_r($part_intercept['no']); echo "\n";
					if(count($part_intercept['yes']) > 1){ //有货的等于或者超过2个
						$all_weight = array();
						$all_ebay_id = array();
						$all_ebay_total = array();
						
						$insertarr = $ebay_order;
						unset($insertarr['ebay_id']);
						if($ebay_carrier0 != 'EUB'){
							unset($insertarr['ebay_tracknumber']);
						}
						$new_ordersn = generateOrdersn();
						$insertarr['ebay_ordersn'] = $new_ordersn;
						$insertarr['ebay_status'] = 618;
						$insertarr['ebay_addtime'] = time();
						$sql = "INSERT INTO ebay_order SET ".array2sql($insertarr);
						$dbcon->execute($sql);
						$insert_ebay_id = $dbcon->insert_id();
						$log_data .= "\n[".date("Y-m-d H:i:s")."]\t 【海外】自动部分发货非快递订单 $ebay_id 拆分出来有货的订单 $insert_ebay_id \n";
						$sql = "select * from ebay_orderdetail where ebay_ordersn = '{$ebay_order['ebay_ordersn']}'";
						$result = $dbcon->execute($sql);
						$orderdetails = $dbcon->getResultArray($result);
						$ebay_total = 0;
						foreach ($orderdetails AS $orderdetail){
							if(in_array($orderdetail['sku'], $part_intercept['yes'])){
								$ebay_total += $orderdetail['ebay_itemprice']*$orderdetail['ebay_amount'] + $orderdetail['shipingfee'];
								$insertarr = $orderdetail;
								unset($orderdetail['ebay_id']);
								$orderdetail['ebay_ordersn'] = $new_ordersn;
								$sql = "INSERT INTO ebay_orderdetail SET ".array2sql($orderdetail);
								if($dbcon->execute($sql)){
									$log_data .= "\n[".date("Y-m-d H:i:s")."]\t 【海外】 自动部分发货非快递订单 $ebay_id 拆分出来有货的订单 $insert_ebay_id --添加明细成功!\n";
								}else{
									$log_data .= "\n[".date("Y-m-d H:i:s")."]\t 【海外】 自动部分发货非快递订单 $ebay_id 拆分出来有货的订单 $insert_ebay_id --添加明细失败\n";
								}
							}
						}
						//$order_statistics->replaceData($new_ordersn, array('mask'=>1), array('mask'=>1));
						$totalweight 	= recalcorderweight($new_ordersn, $ebay_packingmaterial);
						$all_weight[] = $totalweight;
						$all_ebay_id[] = $insert_ebay_id;
						$all_ebay_total[] = $ebay_total;
						if(count($part_intercept['no']) > 0){//没货的
							$insertarr = $ebay_order;
							unset($insertarr['ebay_id']);
							unset($insertarr['ebay_tracknumber']);
							$new_ordersn = generateOrdersn();
							$insertarr['ebay_ordersn'] = $new_ordersn;
							$insertarr['ebay_status'] = 661; //自动拦截非快递
							$insertarr['ebay_addtime'] = time();
							$sql = "INSERT INTO ebay_order SET ".array2sql($insertarr);
							$dbcon->execute($sql);
							$insert_ebay_id = $dbcon->insert_id();
							$log_data .= "\n 自动部分发货非快递订单 $ebay_id 拆分出来无货的订单 $insert_ebay_id \n";
							$sql = "select * from ebay_orderdetail where ebay_ordersn = '{$ebay_order['ebay_ordersn']}'";
							$result = $dbcon->execute($sql);
							$orderdetails = $dbcon->getResultArray($result);
							$ebay_total = 0;
							foreach ($orderdetails AS $orderdetail){
								if(in_array($orderdetail['sku'], $part_intercept['no'])){
									$ebay_total += $orderdetail['ebay_itemprice']*$orderdetail['ebay_amount'] + $orderdetail['shipingfee'];
									$insertarr = $orderdetail;
									unset($orderdetail['ebay_id']);
									$orderdetail['ebay_ordersn'] = $new_ordersn;
									$sql = "INSERT INTO ebay_orderdetail SET ".array2sql($orderdetail);
									if($dbcon->execute($sql)){
										$log_data .= "\n[".date("Y-m-d H:i:s")."]\t 【海外】 自动部分发货非快递订单 $ebay_id 拆分出来无货的订单 $insert_ebay_id --添加明细成功!\n";
									}else{
										$log_data .= "\n[".date("Y-m-d H:i:s")."]\t 【海外】 自动部分发货非快递订单 $ebay_id 拆分出来无货的订单 $insert_ebay_id --添加明细失败\n";
									}
								}
							}
							//$order_statistics->replaceData($new_ordersn, array('mask'=>1), array('mask'=>1));
							$totalweight 	= recalcorderweight($new_ordersn, $ebay_packingmaterial);
							$all_weight[] = $totalweight;
							$all_ebay_id[] = $insert_ebay_id;
							$all_ebay_total[] = $ebay_total;
						}
						echo "重量\n"; print_r($all_weight); echo "\n";
						echo "价格\n"; print_r($all_ebay_total); echo "\n";
						echo "ids\n"; print_r($all_ebay_id); echo "\n";
						foreach($all_ebay_id as $i => $v){
							//$ebay_total = round($ebay_order['ebay_total']*($all_weight[$i]/array_sum($all_weight)),2);
							$shipfee 	= calctrueshippingfee($ebay_carrier0, $all_weight[$i], $ebay_countryname, $v);
							$bb	= "update ebay_order set ordershipfee='$shipfee', orderweight ='{$all_weight[$i]}' ,packingtype ='$ebay_packingmaterial', ebay_total = '{$all_ebay_total[$i]}' where ebay_id ='$v' ";
							$dbcon->execute($bb);
							$sql = "insert into ebay_splitorder (recordnumber, main_order_id, split_order_id, mode, create_date) values ('{$ebay_order['recordnumber']}', '$ebay_id', '$v', '5', '".date("Y-m-d H:i:s")."')";
							$dbcon->execute($sql);
						}
						if($all_weight[0] > 2){//拆分之后的有货订单自动跳转到超重订单中
							$sql	= "update ebay_order set ebay_status='608' where ebay_id ='{$all_ebay_id[0]}' ";
							$log_data .= "\n[".date("Y-m-d H:i:s")."]\t 【海外】自动部分发货非快递有货订单 {$all_ebay_id[0]} 超重移动到超重订单文件夹中!\n";
							$dbcon->execute($sql);
						}
						$$recordorder = true;
						if(count($all_ebay_id) == 2){//拆分订单完全
							$log_data .= "\n[".date("Y-m-d H:i:s")."]\t 【海外】自动部分发货非快递订单 $ebay_id 是原订单 移动到回收站中!\n";
							if($ebay_carrier0 != 'EUB'){
								$sql = "update ebay_order set ebay_status = '615' where ebay_id ='$ebay_id' "; // 原来的订单隐藏
							}else{//modified by Herman.Xi @20140517 EUB原订单拆分需要取消跟踪号
								$sql = "update ebay_order set ebay_status = '615',ebay_tracknumber='' where ebay_id ='$ebay_id' "; // 原来的订单隐藏	
							}
							$dbcon->execute($sql);	
						}
					}	
				}elseif(in_array($ebay_account,$SYSTEM_ACCOUNTS['dresslink'])||in_array($ebay_account,$SYSTEM_ACCOUNTS['cndirect'])){
					$now = time();
					$ebay_addtime = $ebay_order['ebay_addtime'];
					if(($now-$ebay_addtime)<2*24*3600){
						continue;                  //小于2天跳过
					}
					echo "\n===========".$ebay_id."============\n";
					echo "\n-------独立商城--------\n";
					//echo "yes\n"; print_r($part_intercept['yes']); echo "\n";
					//echo "no\n"; print_r($part_intercept['no']); echo "\n";
					if(count($part_intercept['yes']) > 0){ //有货的等于或者超过1个
						$all_weight = array();
						$all_ebay_id = array();
						$all_ebay_total = array();
						$insertarr = $ebay_order;
						unset($insertarr['ebay_id']);
						unset($insertarr['ebay_tracknumber']);
						$new_ordersn = generateOrdersn();
						$insertarr['ebay_ordersn'] = $new_ordersn;
						$insertarr['ebay_status'] = 660;
						$insertarr['ebay_addtime'] = time();
						$sql = "INSERT INTO ebay_order SET ".array2sql($insertarr);
						$dbcon->execute($sql);
						$insert_ebay_id = $dbcon->insert_id();
						$log_data .= "\n[".date("Y-m-d H:i:s")."]\t 【{$ebay_account}】自动部分发货非快递订单 $ebay_id 拆分出来有货的订单 $insert_ebay_id \n";
						$sql = "select * from ebay_orderdetail where ebay_ordersn = '{$ebay_order['ebay_ordersn']}'";
						$result = $dbcon->execute($sql);
						$orderdetails = $dbcon->getResultArray($result);
						$total_moneny = '';
						$ebay_total = 0;
						foreach ($orderdetails AS $orderdetail){
							if(in_array($orderdetail['sku'], $part_intercept['yes'])){
								$ebay_total += $orderdetail['ebay_itemprice']*$orderdetail['ebay_amount'] + $orderdetail['shipingfee'];
								$insertarr = $orderdetail;
								unset($orderdetail['ebay_id']);
								$orderdetail['ebay_ordersn'] = $new_ordersn;
								$total_moneny = ($orderdetail['sku'] + $orderdetail['sku']) * $orderdetail['sku'];
								$sql = "INSERT INTO ebay_orderdetail SET ".array2sql($orderdetail);
								if($dbcon->execute($sql)){
									$log_data .= "\n[".date("Y-m-d H:i:s")."]\t 【{$ebay_account}】自动部分发货非快递订单 $ebay_id 拆分出来有货的订单 $insert_ebay_id --添加明细成功!\n";
								}else{
									$log_data .= "\n[".date("Y-m-d H:i:s")."]\t 【{$ebay_account}】自动部分发货非快递订单 $ebay_id 拆分出来有货的订单 $insert_ebay_id --添加明细失败\n";	
								}
							}
						}
						//$order_statistics->replaceData($new_ordersn, array('mask'=>1), array('mask'=>1));
						$totalweight 	= recalcorderweight($new_ordersn, $ebay_packingmaterial);
						$all_weight[] = $totalweight;
						$all_ebay_id[] = $insert_ebay_id;
						$all_ebay_total[] = $ebay_total;
						
						if(count($part_intercept['no']) > 0){//没货的
							$insertarr = $ebay_order;
							unset($insertarr['ebay_id']);
							unset($insertarr['ebay_tracknumber']);
							$new_ordersn = generateOrdersn();
							$insertarr['ebay_ordersn'] = $new_ordersn;
							$insertarr['ebay_status'] = 614; //
							$insertarr['ebay_addtime'] = time();
							$sql = "INSERT INTO ebay_order SET ".array2sql($insertarr);
							$dbcon->execute($sql);
							$insert_ebay_id = $dbcon->insert_id();
							$log_data .= "\n[".date("Y-m-d H:i:s")."]\t 【{$ebay_account}】自动部分发货非快递订单 $ebay_id 拆分出来无货的订单 $insert_ebay_id \n";
							$sql = "select * from ebay_orderdetail where ebay_ordersn = '{$ebay_order['ebay_ordersn']}'";
							$result = $dbcon->execute($sql);
							$orderdetails = $dbcon->getResultArray($result);
							$ebay_total = 0;
							foreach ($orderdetails AS $orderdetail){
								if(in_array($orderdetail['sku'], $part_intercept['no'])){
									$ebay_total += $orderdetail['ebay_itemprice']*$orderdetail['ebay_amount'] + $orderdetail['shipingfee'];
									$insertarr = $orderdetail;
									unset($orderdetail['ebay_id']);
									$orderdetail['ebay_ordersn'] = $new_ordersn;
									$sql = "INSERT INTO ebay_orderdetail SET ".array2sql($orderdetail);
									if($dbcon->execute($sql)){
										$log_data .= "\n[".date("Y-m-d H:i:s")."]\t 【{$ebay_account}】自动部分发货非快递订单 $ebay_id 拆分出来无货的订单 $insert_ebay_id --添加明细成功!\n";
									}else{
										$log_data .= "\n[".date("Y-m-d H:i:s")."]\t 【{$ebay_account}】自动部分发货非快递订单 $ebay_id 拆分出来无货的订单 $insert_ebay_id --添加明细失败\n";
									}
								}
							}
							//$order_statistics->replaceData($new_ordersn, array('mask'=>1), array('mask'=>1));
							$totalweight 	= recalcorderweight($new_ordersn, $ebay_packingmaterial);
							$all_weight[] = $totalweight;
							$all_ebay_id[] = $insert_ebay_id;
							$all_ebay_total[] = $ebay_total;
						}
						echo "重量\n"; print_r($all_weight); echo "\n";
						echo "价格\n"; print_r($all_ebay_total); echo "\n";
						echo "ids\n"; print_r($all_ebay_id); echo "\n";
						foreach($all_ebay_id as $i => $v){
							//$ebay_total = round($ebay_order['ebay_total']*($all_weight[$i]/array_sum($all_weight)),2);
							//$shipfee 	= calctrueshippingfee($ebay_carrier, $all_weight[$i], $ebay_countryname, $v);
							$shipfee			= calctrueshippingfee($ebay_carrier0,$all_weight[$i],$ebay_countryname,$all_ebay_id[$i]);
							//$ebay_carrier	= $fees[0];
							//$shipfee		= $fees[1];
							//$totalweight	= $fees[2];
							echo "\n经计算 运费 $shipfee 重量 {$all_weight[$i]} 包装材料\n";
							$bb	= "update ebay_order set ordershipfee='$shipfee', orderweight ='{$all_weight[$i]}', ebay_total = '{$all_ebay_total[$i]}' where ebay_id ='$v' ";
							$dbcon->execute($bb);
							$sql = "insert into ebay_splitorder (recordnumber, main_order_id, split_order_id, mode, create_date) values 
							('{$ebay_order['recordnumber']}', '$ebay_id', '$v', '5', '".date("Y-m-d H:i:s")."')";
							$dbcon->execute($sql);
						}
						/*if($all_weight[0] > 2){//拆分之后的有货订单自动跳转到超重订单中
							$sql	= "update ebay_order set ebay_status='608' where ebay_id ='{$all_ebay_id[0]}' ";
							$log_data .= "ebay==自动部分发货非快递有货订单{$all_ebay_id[0]} 超重移动到超重订单文件夹中!\n";
							$dbcon->execute($sql);
						}*/
						$$recordorder = true;
						if(count($all_ebay_id) == 2){
							$log_data .= "\n[".date("Y-m-d H:i:s")."]\t 【ebay】自动部分发货非快递订单 是原订单 移动到回收站中!\n";
							$sql = "update ebay_order set ebay_status = '615' where ebay_id ='$ebay_id' "; // 原来的订单隐藏
							$dbcon->execute($sql);	
						}
					}	
				}
			}else{
				$log_data .= "$ebay_id 部分缺货非快递 拆分统计有误,请核实!\n";
			}
		}else if(in_array($ebay_status, array('659'))){//自动部分包货快递拆分处理
			if(in_array($ebay_carrier0, $no_express_delivery)){
				continue;
			}
			if(isset($part_intercept['yes']) && isset($part_intercept['no']) && (count($part_intercept['yes']) < count($orderdetaillist)) && (count($part_intercept['no']) < count($orderdetaillist))){
				if(in_array($ebay_account,$SYSTEM_ACCOUNTS['dresslink'])||in_array($ebay_account,$SYSTEM_ACCOUNTS['cndirect'])){
					$now = time();
					$ebay_addtime = $ebay_order['ebay_addtime'];
					if(($now-$ebay_addtime)<2*24*3600){
						continue;                  //小于2天跳过
					}
					echo "\n===========".$ebay_id."============\n";
					echo "\n-------独立商城--------\n";
					//echo "yes\n"; print_r($part_intercept['yes']); echo "\n";
					//echo "no\n"; print_r($part_intercept['no']); echo "\n";
					if(count($part_intercept['yes']) > 0){ //有货的等于或者超过1个
						$all_weight = array();
						$all_ebay_id = array();
						$all_ebay_total = array();
						$insertarr = $ebay_order;
						unset($insertarr['ebay_id']);
						unset($insertarr['ebay_tracknumber']);
						$new_ordersn = generateOrdersn();
						$insertarr['ebay_ordersn'] = $new_ordersn;
						$insertarr['ebay_status'] = 641;
						$insertarr['ebay_addtime'] = time();
						$sql = "INSERT INTO ebay_order SET ".array2sql($insertarr);
						$dbcon->execute($sql);
						$insert_ebay_id = $dbcon->insert_id();
						$log_data .= "\n[".date("Y-m-d H:i:s")."]\t 【{$ebay_account}】自动部分发货非快递订单 $ebay_id 拆分出来有货的订单 $insert_ebay_id \n";
						$sql = "select * from ebay_orderdetail where ebay_ordersn = '{$ebay_order['ebay_ordersn']}'";
						$result = $dbcon->execute($sql);
						$orderdetails = $dbcon->getResultArray($result);
						$total_moneny = '';
						$ebay_total = 0;
						foreach ($orderdetails AS $orderdetail){
							if(in_array($orderdetail['sku'], $part_intercept['yes'])){
								$ebay_total += $orderdetail['ebay_itemprice']*$orderdetail['ebay_amount'] + $orderdetail['shipingfee'];
								$insertarr = $orderdetail;
								unset($orderdetail['ebay_id']);
								$orderdetail['ebay_ordersn'] = $new_ordersn;
								$total_moneny = ($orderdetail['sku'] + $orderdetail['sku']) * $orderdetail['sku'];
								$sql = "INSERT INTO ebay_orderdetail SET ".array2sql($orderdetail);
								if($dbcon->execute($sql)){
									$log_data .= "\n[".date("Y-m-d H:i:s")."]\t 【{$ebay_account}】自动部分发货快递订单 $ebay_id 拆分出来有货的订单 $insert_ebay_id --添加明细成功!\n";
								}else{
									$log_data .= "\n[".date("Y-m-d H:i:s")."]\t 【{$ebay_account}】自动部分发货快递订单 $ebay_id 拆分出来有货的订单 $insert_ebay_id --添加明细失败\n";	
								}
							}
						}
						
						$sql = "select * from fedex_remark where ebay_ordersn = '{$ebay_order['ebay_ordersn']}'";
						$result = $dbcon->execute($sql);
						$fedexremarks = $dbcon->getResultArray($result);
						//$total_moneny = '';
						//$ebay_total = 0;
						foreach ($fedexremarks AS $fedexremark){
							//if(in_array($orderdetail['sku'], $part_intercept['yes'])){
								//$ebay_total += $orderdetail['ebay_itemprice']*$orderdetail['ebay_amount'] + $orderdetail['shipingfee'];
								$insertarr = $fedexremark;
								unset($insertarr['id']);
								$insertarr['ebay_ordersn'] = $new_ordersn;
								$insertarr['datetime'] = date('Y-m-d H:i:s',time());
								//$total_moneny = ($orderdetail['sku'] + $orderdetail['sku']) * $orderdetail['sku'];
								$sql = "INSERT INTO fedex_remark SET ".array2sql_bak($insertarr);
								if($dbcon->execute($sql)){
									$log_data .= "\n[".date("Y-m-d H:i:s")."]\t 【{$ebay_account}】自动部分发货快递订单 $ebay_id 拆分出来有货的订单 $insert_ebay_id --添加快递描述成功!\n";
								}else{
									$log_data .= "\n[".date("Y-m-d H:i:s")."]\t 【{$ebay_account}】自动部分发货快递订单 $ebay_id 拆分出来有货的订单 $insert_ebay_id --添加快递描述失败--{$sql}\n";	
								}
							//}
						}
						
						//$order_statistics->replaceData($new_ordersn, array('mask'=>1), array('mask'=>1));
						$totalweight 	= recalcorderweight($new_ordersn, $ebay_packingmaterial);
						$all_weight[] = $totalweight;
						$all_ebay_id[] = $insert_ebay_id;
						$all_ebay_total[] = $ebay_total;
						
						if(count($part_intercept['no']) > 0){//没货的
							$insertarr = $ebay_order;
							unset($insertarr['ebay_id']);
							unset($insertarr['ebay_tracknumber']);
							$new_ordersn = generateOrdersn();
							$insertarr['ebay_ordersn'] = $new_ordersn;
							$insertarr['ebay_status'] = 682; //
							$insertarr['ebay_addtime'] = time();
							$sql = "INSERT INTO ebay_order SET ".array2sql($insertarr);
							$dbcon->execute($sql);
							$insert_ebay_id = $dbcon->insert_id();
							$log_data .= "\n[".date("Y-m-d H:i:s")."]\t 【{$ebay_account}】自动部分发货快递订单 $ebay_id 拆分出来无货的订单 $insert_ebay_id \n";
							$sql = "select * from ebay_orderdetail where ebay_ordersn = '{$ebay_order['ebay_ordersn']}'";
							$result = $dbcon->execute($sql);
							$orderdetails = $dbcon->getResultArray($result);
							$ebay_total = 0;
							foreach ($orderdetails AS $orderdetail){
								if(in_array($orderdetail['sku'], $part_intercept['no'])){
									$ebay_total += $orderdetail['ebay_itemprice']*$orderdetail['ebay_amount'] + $orderdetail['shipingfee'];
									$insertarr = $orderdetail;
									unset($orderdetail['ebay_id']);
									$orderdetail['ebay_ordersn'] = $new_ordersn;
									$sql = "INSERT INTO ebay_orderdetail SET ".array2sql($orderdetail);
									if($dbcon->execute($sql)){
										$log_data .= "\n[".date("Y-m-d H:i:s")."]\t 【{$ebay_account}】自动部分发货快递订单 $ebay_id 拆分出来无货的订单 $insert_ebay_id --添加明细成功!\n";
									}else{
										$log_data .= "\n[".date("Y-m-d H:i:s")."]\t 【{$ebay_account}】自动部分发货快递订单 $ebay_id 拆分出来无货的订单 $insert_ebay_id --添加明细失败\n";
									}
								}
							}
							
							$sql = "select * from fedex_remark where ebay_ordersn = '{$ebay_order['ebay_ordersn']}'";
							$result = $dbcon->execute($sql);
							$fedexremarks = $dbcon->getResultArray($result);
							//$total_moneny = '';
							//$ebay_total = 0;
							foreach ($fedexremarks AS $fedexremark){
								//if(in_array($orderdetail['sku'], $part_intercept['yes'])){
									//$ebay_total += $orderdetail['ebay_itemprice']*$orderdetail['ebay_amount'] + $orderdetail['shipingfee'];
									$insertarr = $fedexremark;
									unset($insertarr['id']);
									$insertarr['ebay_ordersn'] = $new_ordersn;
									$insertarr['datetime'] = date('Y-m-d H:i:s',time());
									//$total_moneny = ($orderdetail['sku'] + $orderdetail['sku']) * $orderdetail['sku'];
									$sql = "INSERT INTO fedex_remark SET ".array2sql_bak($insertarr);
									if($dbcon->execute($sql)){
										$log_data .= "\n[".date("Y-m-d H:i:s")."]\t 【{$ebay_account}】自动部分发货快递订单 $ebay_id 拆分出来有货的订单 $insert_ebay_id --添加快递描述成功!\n";
									}else{
										$log_data .= "\n[".date("Y-m-d H:i:s")."]\t 【{$ebay_account}】自动部分发货快递订单 $ebay_id 拆分出来有货的订单 $insert_ebay_id --添加快递描述失败--{$sql}\n";	
									}
								//}
							}
							
							//$order_statistics->replaceData($new_ordersn, array('mask'=>1), array('mask'=>1));
							$totalweight 	= recalcorderweight($new_ordersn, $ebay_packingmaterial);
							$all_weight[] = $totalweight;
							$all_ebay_id[] = $insert_ebay_id;
							$all_ebay_total[] = $ebay_total;
						}
						echo "重量\n"; print_r($all_weight); echo "\n";
						echo "价格\n"; print_r($all_ebay_total); echo "\n";
						echo "ids\n"; print_r($all_ebay_id); echo "\n";
						foreach($all_ebay_id as $i => $v){
							//$ebay_total = round($ebay_order['ebay_total']*($all_weight[$i]/array_sum($all_weight)),2);
							//$shipfee 	= calctrueshippingfee($ebay_carrier, $all_weight[$i], $ebay_countryname, $v);
							$shipfee			= calctrueshippingfee($ebay_carrier0,$all_weight[$i],$ebay_countryname,$all_ebay_id[$i]);
							//$ebay_carrier	= $fees[0];
							//$shipfee		= $fees[1];
							//$totalweight	= $fees[2];
							echo "\n经计算 运费 $shipfee 重量 {$all_weight[$i]} 包装材料\n";
							$bb	= "update ebay_order set ordershipfee='$shipfee', orderweight ='{$all_weight[$i]}', ebay_total = '{$all_ebay_total[$i]}' where ebay_id ='$v' ";
							$dbcon->execute($bb);
							$sql = "insert into ebay_splitorder (recordnumber, main_order_id, split_order_id, mode, create_date) values 
							('{$ebay_order['recordnumber']}', '$ebay_id', '$v', '5', '".date("Y-m-d H:i:s")."')";
							$dbcon->execute($sql);
						}
						/*if($all_weight[0] > 2){//拆分之后的有货订单自动跳转到超重订单中
							$sql	= "update ebay_order set ebay_status='608' where ebay_id ='{$all_ebay_id[0]}' ";
							$log_data .= "ebay==自动部分发货非快递有货订单{$all_ebay_id[0]} 超重移动到超重订单文件夹中!\n";
							$dbcon->execute($sql);
						}*/
						$$recordorder = true;
						if(count($all_ebay_id) == 2){
							$log_data .= "\n[".date("Y-m-d H:i:s")."]\t 【{$ebay_account}】自动部分发货快递订单 $ebay_id 是原订单 移动到回收站中!\n";
							$sql = "update ebay_order set ebay_status = '615' where ebay_id ='$ebay_id' "; // 原来的订单隐藏
							$dbcon->execute($sql);
						}
					}	
				}
			}else{
				$log_data .= "$ebay_id 部分缺货非快递 拆分统计有误,请核实!\n";
			}	
		}
		if(!empty($log_data)){
			write_scripts_log('auto_contrast_intercept', $ebay_account, $log_data); 
		}
		//$dbcon->close();
	}
}

function get_account_paypalemails($ebay_account){
	/*
	*获取该账号下的收款邮箱
	*add by Herman.Xi
	*date 20130514
	*/
	global $dbcon;
	$sql = "SELECT email FROM ebay_paypalemail WHERE ebay_account='{$ebay_account}'";
	$result = $dbcon->execute($sql);
	$arr = $dbcon->getResultArray($result);
	$returnArr = array();
	foreach($arr as $row){
		$returnArr[] = strtolower(trim($row['email']));
	}
	return $returnArr;
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

function check_sku($sku, $num){
	
	global $dbcon;

	$sku = trim($sku);
	
	$sql = "SELECT o.goods_count,g.cguser FROM ebay_goods AS g LEFT JOIN ebay_onhandle AS o ON o.goods_sn=g.goods_sn WHERE o.goods_sn='{$sku}'";
	$sql		= $dbcon->execute($sql);
	$goodsinfo = $dbcon->getResultArray($sql);
	
	if (empty($goodsinfo)||empty($goodsinfo[0]['cguser'])){
		echo "\n该料号没有添加采购人员!\n";
		return true;
	}
	
	$sql = "SELECT * FROM ebay_sku_statistics WHERE sku='{$sku}'";
	$sql = $dbcon->execute($sql);
	$sku_info = $dbcon->getResultArray($sql);
	
	if(empty($sku_info)){
		echo "\n该料号没有统计信息!\n";
		return true;
	}
	
	/*if ($num>9&&$num>$goodsinfo[0]['goods_count']){
		echo "\n该料号数量{$num},而实际库存是{$goodsinfo[0]['goods_count']}!\n";
		return false;
	}
	
	if ($sku_info['everyday_sale']=='0.00'&&$num>9){
		echo "\n该料号第一次卖出,数量{$num},实际库存{$goodsinfo[0]['goods_count']}!\n";
		return false;
	}*/
	
	$takenum = ceil($sku_info[0]['everyday_sale']*10);
	
	$actuallaygoods = $goodsinfo[0]['goods_count'];
	/*if($actuallaygoods == 0){
		echo "\n通过料号检测,数量{$num},实际库存{$goodsinfo[0]['goods_count']}!\n";
		return true;
	}*/
	if($actuallaygoods){
		$goods_bili = $num / $actuallaygoods;
	}else{
		$goods_bili = 0;
	}
	if ($num>9 && $num>$takenum){
		echo "\n该料号超出10天的销售量,数量{$num},实际库存{$goodsinfo[0]['goods_count']},每天销售量{$sku_info[0]['everyday_sale']}!\n";
		return false;
	}else if($goods_bili>0.5 && $num>$takenum && $actuallaygoods > 0 && $takenum > 0){
		echo "\n该料号超出10天的销售量,且发货数量大于库存数量一半,数量{$num},实际库存{$goodsinfo[0]['goods_count']},每天销售量{$sku_info[0]['everyday_sale']}!\n";
		return false;
	}else{
		echo "\n通过料号检测,数量{$num},实际库存{$goodsinfo[0]['goods_count']}!\n";
		return true;
	}
}

function get_sku_info($sku){
	
	global $dbcon;
	
	//$sql = "SELECT cguser FROM ebay_goods WHERE goods_sn='{$sku}'";
	$sql = "SELECT o.goods_count,o.second_stock_count,g.cguser,g.goods_location FROM ebay_goods AS g LEFT JOIN ebay_onhandle AS o ON o.goods_sn=g.goods_sn WHERE o.goods_sn='{$sku}'";
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
	$purchaseinfo['secondCount'] = $goodsinfo[0]['second_stock_count'];//B仓库库存信息
	$purchaseinfo['goods_location'] = $goodsinfo[0]['goods_location'];
	
	return $purchaseinfo;
}

function get_sku_location($sku){
	//获取产品的仓位
	global $dbcon;
	$sql = "select goods_location from ebay_goods where goods_sn='{$sku}' limit 1";
	$sql = $dbcon->query($sql);
	$res = $dbcon->fetch_one($sql);
	return $res['goods_location'];
}

function get_purchase_info($order_sn){
	
	global $dbcon;
	
	$results = array();
	$sql = "SELECT sku,ebay_amount FROM ebay_orderdetail WHERE ebay_ordersn='{$order_sn}'";
	$sql		= $dbcon->execute($sql);
	$orderdetaillist = $dbcon->getResultArray($sql);
	foreach ($orderdetaillist AS $orderdetail){
		if (strpos($orderdetail['sku'], ',')!==false){
			$skulists = explode(',', $orderdetail['sku']);
			foreach ($skulists AS $skulist){
				list($snum, $sku) = strpos($skulist, '*')!==false ? explode('*', $skulist) : array(1, $skulist);
				$results[$sku] = get_sku_info($sku);
			}
		}else{
			list($snum, $sku) = strpos($orderdetail['sku'], '*')!==false ? explode('*', $orderdetail['sku']) : array(1, $orderdetail['sku']);
			$results[$sku] = get_sku_info($sku);
		}
	}
	return $results;
}
function getsaleandnosendall1($sku, $storeid){
	
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
					LEFT JOIN ebay_order_scan_record as c ON a.ebay_id = c.ebay_id
					WHERE a.ebay_status NOT IN (0, 2, 613, 615, 617, 625, 640, 642,652, 653, 654, 663,667,658,659,660,661,669,670,673,674,681,689,612,624,671,690,691,693,698,699,700,682,753)
					AND b.sku='{$_sku}'
					AND a.ebay_combine!='1'
					AND	(c.is_scan = 0 or c.is_scan is null)
					AND (c.is_show = 0 or c.is_show is null)
					 "; //增加658和661 状态自动拦截 ,653 ,624,671 2013-04-06 不算待发货状态 add by xiaojinhua ,689 2013-05-20 等待复核 不算代发货 add by guanyongjun
		// 增加699 快递待包装 不算待发货 add by xiaojinhua 2013-06-15 		
		$sql = $dbcon->execute($sql);
		$skunums = $dbcon->getResultArray($sql);

		$tendaytime = time() -10*24*60*60;
		$sql1 = "SELECT sum(b.ebay_amount) AS qty 
					FROM ebay_order AS a 
					LEFT JOIN ebay_orderdetail AS b ON a.ebay_ordersn = b.ebay_ordersn 
					LEFT JOIN ebay_order_scan_record as c ON a.ebay_id = c.ebay_id 
					WHERE a.ebay_status IN (612)
					AND b.sku='{$_sku}'
					AND a.ebay_combine!='1'
					AND a.ebay_paidtime > {$tendaytime}
					AND	(c.is_scan=0 or c.is_scan is null)
					AND (c.is_show = 0 or c.is_show is null)
					"; // add by xiaojinhua 修改612暂不寄计算待发货方式
		$sql1 = $dbcon->execute($sql1);
		$skunums1 = $dbcon->getResultArray($sql1);

		$sql2 = "SELECT sum(b.ebay_amount) AS qty 
					FROM ebay_order AS a 
					LEFT JOIN ebay_orderdetail AS b ON a.ebay_ordersn = b.ebay_ordersn 
					LEFT JOIN ebay_order_scan_record as c ON a.ebay_id = c.ebay_id 
					WHERE a.ebay_status IN (624,671,682)
					AND b.sku='{$_sku}'
					AND a.ebay_combine!='1'
					AND	(c.is_scan=0 or c.is_scan is null)
					AND (c.is_show = 0 or c.is_show is null)
				    "; // add by xiaojinhua 修改624,671计算待发货方式
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


function get_realtime($sku){
	$realtimes = 1;
	if(strpos($sku,"*") === false){
		return 1;
	}else{
		$sku_arr = explode('*', $sku);
		$realtimes = $sku_arr[0];
		return $realtimes;
	}
}
function getsaleandnosendall($sku, $storeid){
	//获取虚拟/待发货库存
	global $dbcon;
	
	$totalnums = 0;
	$combineskus = get_combinesku($sku);
	$skus = empty($combineskus) ? array() : array_keys($combineskus);
	array_push($skus, $sku);

	$skus_str = implode("','",$skus);
	$skus_str = "'".$skus_str."'";
	
	//增加文件夹状态723不算代发货的逻辑,add by guanyongjun 2013-12-23
	$sql = "SELECT b.ebay_amount ,b.sku
					FROM ebay_order AS a 
					LEFT JOIN ebay_orderdetail AS b ON a.ebay_ordersn = b.ebay_ordersn 
					WHERE a.ebay_status NOT IN (0, 2, 613, 615, 617, 625, 640, 642,652, 653,654, 663,667,658,659,660,661,669,670,672,673,674,681,689,612,690,691,693,698,699,700,705,706,707,708,709,710,713,714,715,716,717,719,723,721,753)
					AND b.sku in ({$skus_str})
					AND a.ebay_combine!='1'
					"; 
	$sql = $dbcon->execute($sql);
	$skunums = $dbcon->getResultArray($sql);
	foreach($skunums as $sku_info){
		$realtimes = get_realtime($sku_info["sku"]);
		$totalnums += ($sku_info["ebay_amount"]*$realtimes);
	}
	$tendaytime = time() -10*24*60*60;
	$sql1 = "SELECT b.ebay_amount ,b.sku
				FROM ebay_order AS a 
				LEFT JOIN ebay_orderdetail AS b ON a.ebay_ordersn = b.ebay_ordersn 
				WHERE a.ebay_status IN (612)
				AND b.sku in ({$skus_str})
				AND a.ebay_combine!='1'
				AND a.ebay_paidtime > {$tendaytime}
				"; // add by xiaojinhua 修改612暂不寄计算待发货方式
	$sql1 = $dbcon->execute($sql1);
	$skunums1 = $dbcon->getResultArray($sql1);
	foreach($skunums1 as $sku_info){
		$realtimes = get_realtime($sku_info["sku"]);
		$totalnums += ($sku_info["ebay_amount"]*$realtimes);
	}

	$sql2_s = "SELECT b.ebay_amount ,b.sku
				FROM ebay_order AS a 
				LEFT JOIN ebay_orderdetail AS b ON a.ebay_ordersn = b.ebay_ordersn 
				LEFT JOIN ebay_order_scan_record as c ON a.ebay_id = c.ebay_id 
				WHERE a.ebay_status IN (624,671,682,701,702)
				AND b.sku in ({$skus_str})
				AND c.sku = '{$sku}'
				AND a.ebay_combine!='1'
				AND	c.is_scan = 1
				AND c.is_show = 0
				"; //找出已经配货的记录 
	$scan_num = 0;
	$sql2_s = $dbcon->execute($sql2_s);
	$skunums3 = $dbcon->getResultArray($sql2_s);
	if(count($skunums3) > 0){
		foreach($skunums3 as $sku_info){
			$realtimes = get_realtime($sku_info["sku"]);
			$scan_num += ($sku_info["ebay_amount"]*$realtimes);
		}
	}

	$sql2_s = "SELECT b.ebay_amount ,b.sku
				FROM ebay_order AS a 
				LEFT JOIN ebay_orderdetail AS b ON a.ebay_ordersn = b.ebay_ordersn 
				LEFT JOIN ebay_order_scan_record as c ON a.ebay_id = c.ebay_id 
				WHERE a.ebay_status =612
				AND a.ebay_paidtime > {$tendaytime}
				AND b.sku in ({$skus_str})
				AND c.sku = '{$sku}'
				AND a.ebay_combine!='1'
				AND	c.is_scan = 1
				AND c.is_show = 0
				"; //找出已经配货的记录 

	//echo $sql2_s."\n";
	$scan_num1 = 0;
	$sql2_s = $dbcon->execute($sql2_s);
	$skunums3 = $dbcon->getResultArray($sql2_s);
	if(count($skunums3) > 0){
		foreach($skunums3 as $sku_info){
			$realtimes = get_realtime($sku_info["sku"]);
			$scan_num1 += ($sku_info["ebay_amount"]*$realtimes);
		}
	}
	$totalnums = $totalnums - $scan_num - $scan_num1;
	//$totalnums = $totalnums - $scan_num;
	return $totalnums;
}

function getpartsaleandnosendall($sku, $storeid){
	//获取虚拟代发货库存+部分包货占用代发货库存
	//add by Herman.Xi @20131021
	list($packagingnums, $inums) = get_partsalenosend($sku, $storeid);
	$salensend = getsaleandnosendall($sku, $storeid)+$packagingnums;
	return $salensend;
}

function getsaleandnosendallres($sku, $storeid){// 输出代发货计算过程日志 add by guanyongjun 2013-04-24
	global $dbcon;
	$res = '';
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
		// $sql = "SELECT sum(b.ebay_amount) AS qty,a.ebay_id 
					// FROM ebay_order AS a 
					// LEFT JOIN ebay_orderdetail AS b ON a.ebay_ordersn = b.ebay_ordersn 
					// WHERE a.ebay_status NOT IN (0, 2, 613, 615, 617, 625, 640, 642,652, 653, 654, 663,667,658,659,660,661,669,670,673,674,681,612,624,671,690,698,699,700)
					// AND b.sku='{$_sku}'
					// AND a.ebay_combine!='1'
					// LIMIT 1"; //增加658和661 状态自动拦截 ,653 ,624,671 2013-04-06 不算待发货状态 add by xiaojinhua
		$sql = "SELECT b.ebay_amount ,a.ebay_id 
					FROM ebay_order AS a 
					LEFT JOIN ebay_orderdetail AS b ON a.ebay_ordersn = b.ebay_ordersn 
					WHERE a.ebay_status NOT IN (0, 2, 613, 615, 617, 625, 640, 642,652, 653, 654, 663,667,658,659,660,661,669,670,673,674,681,689,612,624,671,690,691,698,699,700)
					AND b.sku='{$_sku}'
					AND a.ebay_combine!='1'
					"; //增加658和661 状态自动拦截 ,653 ,624,671 2013-04-06 不算待发货状态 add by xiaojinhua ,689 2013-05-20 等待复核 不算代发货 add by guanyongjun
		$sql0 = $dbcon->execute($sql);
		$skunums = $dbcon->getResultArray($sql0);
		$qty = 0;
		$ids = array();
		foreach($skunums as $value){
			$qty +=  (int)$value["ebay_amount"];
			array_push($ids,$value["ebay_id"]);
		}

		$tendaytime = time() -10*24*60*60;
		$sql1 = "SELECT b.ebay_amount,a.ebay_id 
					FROM ebay_order AS a 
					LEFT JOIN ebay_orderdetail AS b ON a.ebay_ordersn = b.ebay_ordersn 
					WHERE a.ebay_status IN (612)
					AND b.sku='{$_sku}'
					AND a.ebay_combine!='1'
					AND a.ebay_paidtime > {$tendaytime}
					"; // add by xiaojinhua 修改612暂不寄计算待发货方式
		$sql10 = $dbcon->execute($sql1);
		$skunums1 = $dbcon->getResultArray($sql10);
		$qty1 = 0;
		$ids1 = array();
		foreach($skunums1 as $value){
			$qty1 +=  (int)$value["ebay_amount"];
			array_push($ids1,$value["ebay_id"]);
		}
		
		$sql2 = "SELECT b.ebay_amount,a.ebay_id 
					FROM ebay_order AS a 
					LEFT JOIN ebay_orderdetail AS b ON a.ebay_ordersn = b.ebay_ordersn 
					LEFT JOIN ebay_order_scan_record as c ON a.ebay_id = c.ebay_id 
					WHERE a.ebay_status IN (624,671)
					AND b.sku='{$_sku}'
					AND a.ebay_combine!='1'
					AND	(c.is_scan=0 or c.is_scan is null) 
					AND (c.is_show = 0 or c.is_show is null) 
					"; // add by xiaojinhua 修改624,671计算待发货方式
		$sql20 = $dbcon->execute($sql2);
		$skunums2 = $dbcon->getResultArray($sql20);
		$qty2 = 0;
		$ids2 = array();
		foreach($skunums2 as $value){
			$qty2 +=  (int)$value["ebay_amount"];
			array_push($ids2,$value["ebay_id"]);
		}
		
		if (!empty($skunums)){
			$totalnums += (int)$qty*$realtimes;
			$totalnums += (int)$qty1*$realtimes;
			$totalnums += (int)$qty2*$realtimes;
			$res = "step1 sql:\n".$sql."\nstep1 res:".$qty*$realtimes."--ebay_id:".implode(",",$ids)."\n\nstep2 sql:\n".$sql1."\nstep2 res:".$qty1*$realtimes."--ebay_id:".implode(',',$ids1)."\n\nstep3 sql:\n".$sql2."\nstep3 res:".$qty2*$realtimes."---ebay_id:".implode(',',$ids2);
		}else{
			$res = "step1 sql:\n".$sql."\nstep1 res:".$qty*$realtimes."--ebay_id:".implode(",",$ids);
		}
	}
	return $res;
}


function getauditingall($sku, $storeid){
	//获取待审核数量
	$start = microtime();
	global $dbcon;
	
	$totalnums = 0;
	$combineskus = get_combinesku($sku);
	$skus = empty($combineskus) ? array() : array_keys($combineskus);
	array_push($skus, $sku);
	$skus_str = implode("','",$skus);
	$skus_str = "'".$skus_str."'";
	$sql = "SELECT b.ebay_amount ,b.sku
				FROM ebay_order AS a 
				LEFT JOIN ebay_orderdetail AS b ON a.ebay_ordersn = b.ebay_ordersn 
				WHERE a.ebay_status=640
				AND b.sku in ({$skus_str})
				AND a.ebay_combine!='1'
				";
	$sql = $dbcon->execute($sql);
	$skunums = $dbcon->getResultArray($sql);
	foreach($skunums as $sku_info){
		$realtimes = get_realtime($sku_info["sku"]);
		$totalnums += ($sku_info["ebay_amount"]*$realtimes);
	}
	$end = microtime();
	$time = $end - $start;
	echo "待审核花的时间".$time;
	return $totalnums;
}

function getinterceptall($sku, $storeid){
	//获取已拦截数量
	global $dbcon;
	$totalnums = 0;
	$combineskus = get_combinesku($sku);
	$skus = empty($combineskus) ? array() : array_keys($combineskus);
	array_push($skus, $sku);
	$skus_str = implode("','",$skus);
	$skus_str = "'".$skus_str."'";

	$sql = "SELECT b.ebay_amount ,b.sku
				FROM ebay_order AS a 
				LEFT JOIN ebay_orderdetail AS b ON a.ebay_ordersn = b.ebay_ordersn 
				WHERE a.ebay_status=642
				AND b.sku in ({$skus_str})
				AND a.ebay_combine!='1'
				";
	$sql = $dbcon->execute($sql);
	$skunums = $dbcon->getResultArray($sql);
	foreach($skunums as $sku_info){
		$realtimes = get_realtime($sku_info["sku"]);
		$totalnums += ($sku_info["ebay_amount"]*$realtimes);
	}

	return $totalnums;
}


//add by xiaojinhua
function get_autointercept($sku, $storeid){
	$start = microtime();
	global $dbcon;
	$totalnums = 0;
	$combineskus = get_combinesku($sku);
	$skus = empty($combineskus) ? array() : array_keys($combineskus);
	array_push($skus, $sku);
	$skus_str = implode("','",$skus);
	$skus_str = "'".$skus_str."'";

	$sql = "SELECT b.ebay_amount ,b.sku 
				FROM ebay_order AS a 
				LEFT JOIN ebay_orderdetail AS b ON a.ebay_ordersn = b.ebay_ordersn 
				WHERE a.ebay_status in (658,661)
				AND b.sku in ({$skus_str})
				AND a.ebay_combine!='1'
				";
	$sql = $dbcon->execute($sql);
	$skunums = $dbcon->getResultArray($sql);
	foreach($skunums as $sku_info){
		$realtimes = get_realtime($sku_info["sku"]);
		$totalnums += ($sku_info["ebay_amount"]*$realtimes);
	}
	$end = microtime();
	$time = $end - $start;
	return $totalnums;
}



// 自动拦截区分平台
function get_autointercept_platform($sku, $ebay_platform){
	global $dbcon;
	$totalnums = 0;
	$combineskus = get_combinesku($sku);
	$skus = empty($combineskus) ? array() : array_keys($combineskus);
	array_push($skus, $sku);
	$skus_str = implode("','",$skus);
	$skus_str = "'".$skus_str."'";
	$accounts = get_accounts($ebay_platform);
	$accounts_str = implode("','",$accounts);
	$sql = "SELECT b.ebay_amount , b.sku  
				FROM ebay_order AS a 
				LEFT JOIN ebay_orderdetail AS b ON a.ebay_ordersn = b.ebay_ordersn 
				WHERE a.ebay_status in (658,661)
				AND b.sku in ({$skus_str})
				and a.ebay_account in ('{$accounts_str}')
				AND a.ebay_combine!='1'";
	//echo "{$sql}\n";
	$sql = $dbcon->execute($sql);
	$skunums = $dbcon->getResultArray($sql);
	foreach($skunums as $sku_info){
		$realtimes = get_realtime($sku_info["sku"]);
		$totalnums += ($sku_info["ebay_amount"]*$realtimes);
	}
	//echo "{$ebay_platform} 数量为{$totalnums}\n";
	return $totalnums;
}

//通过平台获取所有账户
function get_accounts($ebay_platform){
	global $dbcon;
	$sql = "SELECT ebay_account FROM  `ebay_account`  where ebay_platform='{$ebay_platform}'";
	$sql = $dbcon->execute($sql);
	$accounts = $dbcon->getResultArray($sql);
	$rtnAccounts = array();
	foreach($accounts as $item){
		$rtnAccounts[] = $item['ebay_account'];
	}
	return $rtnAccounts;
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
		$sql = "SELECT a.ebay_id,b.ebay_id as detail_id,b.ebay_amount FROM ebay_order AS a 
					LEFT JOIN ebay_orderdetail AS b ON a.ebay_ordersn=b.ebay_ordersn 
					WHERE a.ebay_userid !='' 
					AND a.ebay_status IN (652,653, 654) 
					AND a.ebay_combine!='1' 
					AND b.sku='{$_sku}'";
		$sql = $dbcon->execute($sql);
		$lists = $dbcon->getResultArray($sql);
		foreach ($lists AS $list){
			$checksql = "SELECT check_status ,ebaydetail_id FROM ebay_unusual_order_check WHERE ebay_id={$list['ebay_id']} AND ebaydetail_id = '{$list['detail_id']}' AND sku='{$sku}'";
			$checksql = $dbcon->execute($checksql);
			$checksql = $dbcon->fetch_one($checksql);
			
			if (empty($checksql)||$checksql['check_status']==2){
				$interceptnums += $realtimes*$list['ebay_amount'];
				continue;
			}
			$schecksql = "SELECT realnum,totalnum FROM ebay_packing_status WHERE ebaydetail_id='{$checksql['ebaydetail_id']}' AND sku='{$sku}'";
			$schecksql = $dbcon->execute($schecksql);
			$schecksql = $dbcon->getResultArray($schecksql);
			if (!empty($schecksql)&&$schecksql[0]['realnum']==$schecksql[0]['totalnum']){
				continue;
			}else if(!empty($schecksql)&&$schecksql[0]['realnum']<$schecksql[0]['totalnum']){
				$packagingnums += $realtimes*($schecksql[0]['totalnum']-$schecksql[0]['realnum']);
			}else if(!empty($schecksql)){
				//$packagingnums += $realtimes*$list['ebay_amount'];
				$packagingnums += $realtimes*($schecksql[0]['totalnum']-$schecksql[0]['realnum']);
			}else{ //没有包货记录的情况下 add by xiaojinhua 2013-05-24
				$sql = "select  ebay_amount from ebay_orderdetail where ebay_id='{$checksql['ebaydetail_id']}'";
				$sql = $dbcon->execute($sql);
				$rtn = $dbcon->fetch_one($sql);
				$packagingnums += $realtimes * $rtn["ebay_amount"];
			}
		}
	}
	return array($packagingnums,$interceptnums);
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
	global $dbcon;
	$totalnums = 0;
	$combineskus = get_combinesku($sku);
	$skus = empty($combineskus) ? array() : array_keys($combineskus);
	array_push($skus, $sku);
	$skus_str = implode("','",$skus);
	$skus_str = "'".$skus_str."'";

	$sql = "SELECT b.ebay_amount ,b.sku
				FROM ebay_order AS a 
				LEFT JOIN ebay_orderdetail AS b ON a.ebay_ordersn = b.ebay_ordersn 
				WHERE a.ebay_status NOT IN (0, 615, 617, 625, 663 ,690,705,706,707,708,709,710,714,721)
				AND (a.ebay_paidtime>{$start} OR a.ebay_addtime>{$start})
				AND (a.ebay_paidtime<{$end} OR a.ebay_addtime<{$end})
				AND b.sku in ({$skus_str})
				";
	$sql = $dbcon->execute($sql);
	$skunums = $dbcon->getResultArray($sql);
	foreach($skunums as $sku_info){
		$realtimes = get_realtime($sku_info["sku"]);
		$maxnums = $esale>=5 ? ceil(10*$esale/$realtimes) : 50;
		if($sku_info['ebay_amount'] < $maxnums){
			$totalnums += ($sku_info["ebay_amount"]*$realtimes);
		}
	}
	$end = microtime();
	$time = $end - $start;
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
						";
	$gsql	 = $dbcon->execute($gsql);
	$gsql	 = $dbcon->getResultArray($gsql);
	$usedqty =  $gsql[0]['qty']?$gsql[0]['qty']:0;
	
	$gsql 	 = "SELECT SUM(b.goods_count) AS ordernum,SUM(b.stockqty) AS reachnum FROM ebay_iostore AS a 
						LEFT JOIN ebay_iostoredetail AS b ON a.io_ordersn = b.io_ordersn
						WHERE b.goods_sn = '{$goods_sn}' 
						AND a.io_status ='3' 
						AND type ='2' 
						AND a.io_warehouse={$storeid} 
						";
	$gsql	 = $dbcon->execute($gsql);
	$gsql	 = $dbcon->fetch_one($gsql);
	if (!empty($gsql)){
		$usedqty = $usedqty+$gsql['ordernum']-$gsql['reachnum'];
	}
	return $usedqty;	
}

function getaccountsale($sku, $accounts, $wheretime=array(), $debug=false){
	global $dbcon;
	$totalnums = 0;
	$combineskus = get_combinesku($sku);
	$skus = empty($combineskus) ? array() : array_keys($combineskus);
	array_push($skus, $sku);
	$account_sql = implode(',', array2strarray($accounts));
	$scantime = "";
	if (count($wheretime)==2){
		list($starttime, $endtime) = $wheretime;
		$scantime = "AND a.scantime BETWEEN {$starttime} AND {$endtime}";
	}
	
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
					{$scantime}";
		//echo "==={$_sku}==={$realtimes}===".$sql."\n\n"; //注释掉防止生成的日志文件过大打不开 add by guanyongjun 2013-09-04
		$query		= $dbcon->execute($sql);
		$res 		= $dbcon->fetch_one($query);
		$skunums	= isset($res['qty']) ? intval($res['qty']) : 0;
		if (!empty($skunums)){
			$totalnums += $skunums*$realtimes;
		}
		if($debug) { //debug 为true时打印db及sql add by guanyongjun 2014-04-08
			echo date("Y-m-d H:i:s",time()),"=+=+=+=".$sql."=+=+销售数量=+=+".$totalnums."\n";
		}
		//echo "==={$_sku}=销售数量=".$totalnums."\n";
	}	
	return $totalnums;
}

//海外仓每月发货统计 add by guanyongjun 2013-10-26
function getaccountsaleow($sku, $accounts, $wheretime=array()){
	
	global $dbcon;
	
	$totalnums = 0;
	$combineskus = get_combinesku($sku);
	$skus = empty($combineskus) ? array() : array_keys($combineskus);
	array_push($skus, $sku);
	$account_sql = implode(',', array2strarray($accounts));
	$scantime = "";
	if (count($wheretime)==2){
		list($starttime, $endtime) = $wheretime;
		$scantime = "AND a.scantime BETWEEN {$starttime} AND {$endtime}";
	}
	
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
					WHERE a.ebay_status=709
					AND b.sku='{$_sku}'
					AND a.ebay_account IN ({$account_sql})
					{$scantime}";
		//echo "==={$_sku}==={$realtimes}===".$sql."\n\n"; //注释掉防止生成的日志文件过大打不开 add by guanyongjun 2013-09-04
		$sql = $dbcon->execute($sql);
		$skunums = $dbcon->getResultArray($sql);
		if (!empty($skunums)){
			$totalnums += $skunums[0]['qty']*$realtimes;
		}
		//echo "==={$_sku}=销售数量=".$totalnums."\n";
	}
	return $totalnums;
}
//海外仓料号、库存、代发货数据返回 add by guanyongjun 2013-10-26
function getOwStock($sku){
	global $dbcon;
	$sql 	= "SELECT * from ow_stock where sku='{$sku}'";
	$query	= $dbcon->execute($sql);
	$res 	= $dbcon->fetch_one($query);
	return $res;
}

// 海外仓料号判断
function checkSkuNum($orderid){
	global $dbcon;
	$sql = "SELECT ebay_ordersn from ebay_order where ebay_id={$orderid} ";
	$sql = $dbcon->execute($sql);
	$ordersn_arr = $dbcon->fetch_one($sql);
	$ordersn = $ordersn_arr["ebay_ordersn"];
	//$sql = "SELECT count(*) as total FROM ebay_orderdetail WHERE ebay_ordersn = '{$ordersn}'";
	$sql = "SELECT sku,ebay_amount  FROM ebay_orderdetail WHERE ebay_ordersn = '{$ordersn}'";
	$sql = $dbcon->execute($sql);
	//$ret = $dbcon->fetch_one($sql);
	$ret_arr = $dbcon->getResultArray($sql);
	$totals = 0;
	$order_status = array();
	foreach($ret_arr as $ret){
		$totals += $ret['ebay_amount'];
		$combine = checkCombine($ret["sku"]);
		if($combine){
			$combine_arr = explode(",",$combine);
			for($j=0; $j < count($combine_arr); $j++){
				$sku 	= substr($combine_arr[$j],0,strpos($combine_arr[$j],'*'));
				$amount = substr($combine_arr[$j],strpos($combine_arr[$j],'*')+1);

				$virtualnum = check_oversea_stock($sku);
				$orderNum = $ret["ebay_amount"] * $amount;
				$log_data = "订单号：{$orderid} sku :{$sku} 虚拟库存：{$virtualnum} 订单sku数：{$orderNum} \n\n";
				write_scripts_log('auto_contrast_intercept', "oversea", $log_data);
				if($virtualnum < 0){
					$order_status[] = 714; // 海外仓缺货
				}
			}
			$real_sku = $sku;
		}else{
			$virtualnum = check_oversea_stock($ret["sku"]);
			if($virtualnum < 0){
				$order_status[] = 714; // 海外仓缺货
			}
		}
	}
	$stockout = false;
	if(in_array(714,$order_status)){
		$sql = "update ebay_order set ebay_status ='714' where ebay_id ={$orderid}  "; 
		insert_mark_shipping($orderid);
		if($dbcon->execute($sql)){
			$stockout = true;
		}
	}
	if($stockout){
		return $stockout;
	}else{
		return $totals;
	}
}


function checkCombine($sku){
	global $dbcon;
	$sql = "SELECT goods_sncombine FROM ebay_productscombine WHERE goods_sn = '{$sku}'";
	$query = $dbcon->execute($sql);
	$ret = $dbcon->fetch_one($query);
	if($ret){
		return $ret['goods_sncombine'];
	}else{
		return 0;
	}
}

function check_oversea_stock($sku){ //检查虚拟库存
	global $dbcon ;
	$sql = "select count from ow_stock where sku='{$sku}'";
	$sql = $dbcon->execute($sql);
	$real_count = $dbcon->fetch_one($sql);
	$totalnums = get_waiting_sale($sku);
	//var_dump($real_count['count'],$totalnums);
	$virtualnum = $real_count['count'] - $totalnums ;
	return $virtualnum;
}


function get_waiting_sale($sku){
	global $dbcon;
	$totalnums = 0;
	$combineskus = get_combinesku($sku);
	$skus = empty($combineskus) ? array() : array_keys($combineskus);
	array_push($skus, $sku);

	$skus_str = implode("','",$skus);
	$skus_str = "'".$skus_str."'";

	$sql = "SELECT b.ebay_amount ,b.sku
					FROM ebay_order AS a 
					LEFT JOIN ebay_orderdetail AS b ON a.ebay_ordersn = b.ebay_ordersn 
					WHERE a.ebay_status  IN (705,706,707,710)
					AND b.sku in ({$skus_str})
					AND a.ebay_combine!='1'
					"; 
	$sql = $dbcon->execute($sql);
	$skunums = $dbcon->getResultArray($sql);
	foreach($skunums as $sku_info){
		$realtimes = get_realtime($sku_info["sku"]);
		$totalnums += ($sku_info["ebay_amount"]*$realtimes);
	}
	return $totalnums;
}

function calcWeight($orderid){
	global $dbcon;
	$sql = "SELECT ebay_ordersn from ebay_order where ebay_id={$orderid} ";
	$sql = $dbcon->execute($sql);
	$ordersn_arr = $dbcon->fetch_one($sql);
	$ordersn = $ordersn_arr["ebay_ordersn"];
	$totalweight = recalcorderweight($ordersn, $ebay_packingmaterial);
	$sql = "update ebay_order set  orderweight ='$totalweight' where ebay_id ={$orderid} "; 
	$dbcon->execute($sql);
	return $totalweight;
}
function usCalcShipCost($ebayid){
	global $dbcon;
	$shipCost  = '';
	$carrier   = '';
	$getInfo   = "SELECT ebay_id,ebay_ordersn,orderweight, ebay_postcode FROM ebay_order where ebay_id = '{$ebayid}' AND ebay_countryname = 'United States' ";
	$getInfo   = $dbcon->execute($getInfo);
	$getInfo   = $dbcon->fetch_one($getInfo);
	
	$weight    = $getInfo['orderweight'];//订单重量
	$zipCode   = $getInfo['ebay_postcode'];//目的地邮编
	$zipCode   = substr($zipCode, 0, 3);//只需取邮编前3位数字
	$ebayid = $getInfo["ebay_id"];
	
	$getZone   = "SELECT zone FROM ow_zone_postcode WHERE zip_code like '%$zipCode%'";
	$getZone   = $dbcon->execute($getZone);
	$getZone   = $dbcon->fetch_one($getZone);
	
	$zone      = $getZone['zone'];//邮编所属分区
	
	$weight_g  	 = $weight * 1000;//kg转换成g;
	$weight_oz 	 = ceil($weight_g / 28.35);//g转换成盎司
	$weight_lbs  = ceil($weight / 0.4536);//kg转换成磅
	
	include_once 'ow_common.php'; //海外仓公用函数
	include_once 'OverseaShipfeeCul.php'; //海外仓运费计算类
	/*------------- 运输方式选择   ---------------*/
	$culModel   = new OverseaShipfeeCul();
	$shipsetting    = $culModel->getShipSettings();
	
	$rate       = $shipsetting['firerate'];
	$home       = $shipsetting['homeshipfee'];
	$exchange   = $shipsetting['usdexchange'];
	
	$splitedSkuList    = generateSkuList($getInfo['ebay_ordersn']);
// 	print_r($splitedSkuList);exit;
	$packageLWH        = culPackageLWH($splitedSkuList); //包裹的长宽高
// 	print_r($packageLWH);
	$length            = $packageLWH['L'];
	$width             = $packageLWH['W'];
	$hight             = $packageLWH['H'];
	$weidht            = $weight;
	$sitesing = 0.39370078740157;
	$lwh = array (
		'L' => $length*$sitesing,
		'W' => $width*$sitesing,
		'H' => $hight*$sitesing
	);
	
	// 	echo $weidht, "===\n";
	$result            = array();                                                                      //可成立的运输方式列表    格式 array(运输方式名称=>运费)
	/*
	 $usps_fix   = $culModel->uspsShipfee_fix($length,$width, $hight, 'fix', 'inside');                     //usps 固定运费
	if ($usps_fix) {
	$result['usps_fix']    = $usps_fix;
	}
	
	$usps_A     = $culModel->usps_serviceA($length,$width, $hight,$weidht);
	if ($usps_A) {                                                                                         //usps 套餐A
	$result['usps_A']    = $usps_A;
	}
	
	$usps_B     = $culModel->usps_serviceB($length,$width, $hight, $weidht);                               //usps 套餐B
	if ($usps_B) {
	$result['usps_B']    = $usps_B;
	}
	
	$usps_C     = $culModel->usps_serviceC($length,$width, $hight, $weidht);
	if ($usps_C) {
	$result['usps_C']    = $usps_C;
	}
	*/
	$ground_re  = $culModel->ground_re($weidht, $zone, $lwh);                                                    //GROUND RESIDENTIAL
	
	if ($ground_re) {
	    $result['ground_re']    = ($ground_re+($ground_re*$rate));                                         //运费加燃油附加费 + 住宅运送费
	}
	
	$ground_co  = $culModel->ground_co($weidht, $zone, $lwh);                                                    //GROUND COMMERCIAL
	
	if ($ground_co) {
	    $result['ground_co']    = ($ground_co+($ground_co*$rate)+$home);                                   //运费加燃油附加费
	}
	
	$SurePost   = $culModel->SurePost($length, $width, $hight, $weidht, $zone);                            //SurePost运费
	if ($SurePost) {
	    $result['SurePost']    = ($SurePost + ($SurePost*$rate));                                          //运费加燃油附加费
	}
	
	/*
	 $ups        = $culModel->upsShipfee($weidht);                                                          //ups运费计算
	if ($ups) {
	$result['UPS Ground']    = $ups + ($ups*$rate) + $home;
	}
	*/
	$usps_gel   = $culModel->uspsGeneral($weidht, $zone,$lwh);                                                  //usps通用运费计算
	if ($usps_gel) {
	    $result['USPS']    = $usps_gel;
	}
	
	// 	print_r($result);
	$mini  = array('ship'=>'', 'fee'=>10000);
	foreach ($result as $key=>$fee){
	    if ($fee < $mini['fee']) {
	        $mini['ship'] = $key;
	        $mini['fee']  = $fee;
	    }
	}
	$carrier       = carrerMap($mini['ship']);
	$shipCost      = $mini['fee'];
// 	echo $carrier, "++", $shipCost;
	/*
	if($weight_oz <= 13){//重量小于13盎司直接选USPS运输方式
		$getUspsCost = "SELECT cost FROM ow_usps_calcfree WHERE weight = '{$weight_oz}' AND unit = 'oz'";
		
		$getUspsCost = $dbcon->execute($getUspsCost);
		$getUspsCost = $dbcon->fetch_one($getUspsCost);
		$shipCost    = $getUspsCost['cost'];
		$carrier     = 'USPS';
	}else{
		$getUspsCost = "SELECT cost FROM ow_usps_calcfree WHERE weight = '{$weight_lbs}' AND zone = '{$zone}' AND unit = 'lbs'";
		$getUspsCost = $dbcon->execute($getUspsCost);
		$getUspsCost = $dbcon->fetch_one($getUspsCost);
		$uspsCost    = $getUspsCost['cost'];//USPS运费
		
		$getUpsCost  = "SELECT cost FROM ow_ups_calcfree WHERE weight = '{$weight_lbs}' AND zone = '{$zone}' AND unit = 'lbs'";
		$getUpsCost  = $dbcon->execute($getUpsCost);
		$getUpsCost  = $dbcon->fetch_one($getUpsCost);
		$upsCost     = $getUpsCost['cost'];//UPS运费
		$upsCost     = $upsCost*(1+0.07); //添加 燃油附加费
		if($uspsCost <= $upsCost){//运费对比
			$shipCost = $uspsCost;
			$carrier  = 'USPS';
		}else{
			$shipCost = $upsCost;
			$carrier  = 'UPS Ground';
		}
	}*/
	/*------------- 运输方式选择   ---------------*/
	
	if($shipCost != '' && $carrier != ''){
		$update = "UPDATE ebay_order SET ebay_carrier = '{$carrier}', ordershipfee = '{$shipCost}',	ebay_status = 706 WHERE ebay_id = '{$ebayid}'";
		if($dbcon->execute($update)){
			insert_mark_shipping($ebayid);
			return 1;
		}else{
			return 0;
		}
	}
}

//超大订单料号记录表 add by wangminwei 2014-04-16
function addBigOrderSkuLog($mainid, $ebayid, $sku, $amount){
	global $dbcon;
	$nowtime 	= time();
	$insert 	= "INSERT INTO bigOrderSkuLog(mainid, ebayid, sku, amount, addtime) VALUES ('{$mainid}', '{$ebayid}', '{$sku}', '{$amount}', '{$nowtime}')";
	$dbcon->execute($insert);
}
function mkpath($path){
	$path_out=preg_replace('/[^\/.]+\/?$/','',$path);
	if(!is_dir($path_out)){			
		mkpath($path_out);
	}
	mkdir($path);
}
	
function writeBigOrderSkuLog($file, $data){
	$tmp_dir = dirname($file);
	if(!is_dir($tmp_dir)){
		mkpath($tmp_dir);
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

function judgeSpecial($str){
	$arr = array('`','~','!','@','#','$','%','^','&','*','(',')','_','-','+','=','[','{',']','}','\\','|',':',';','\'','"','.','/','<','>','?');
	$judgeStatus = false;
	foreach($arr as $val){
		if(strpos($str,$val)!==false){
			$judgeStatus = true;
			break;
		}
	}
	return $judgeStatus;
}

?>
