<?php
/**
* @name functon_general.php
* @author Herman.Xi (席慧超)
* @version 1.0
* @modify 2012-09-15 10:19:00
* @last modified Herman.Xi
* @last modified date 2012-09-15
* 重新定义的一些公用方法
**/
function tep_not_null($value){
	//判断一个数不为空
	if (is_array($value)) {
		if (sizeof($value) > 0) {
			return true;
		} else {
			return false;
		}
	} else {
		if (($value != '') && (strtolower($value) != 'null') && (strlen(trim($value)) > 0)) {
			return true;
		} else {
			return false;
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

function auto_swith_transport($ebay_carrier){
	switch($ebay_carrier){
		case 'hongkong post air mail' :
		case 'hk post air mail' :
		case 'hkpam' :
		case 'hongkong post airmail' :
		case 'hk post airmail' :
			$ebay_carrier = '香港小包挂号';
			break;
		case 'upss' :
		case 'ups express saver' :
			$ebay_carrier = 'UPS';
			break;
		case 'dhl' :
			$ebay_carrier = 'DHL';
			break;
		case 'ems' :
			$ebay_carrier = 'EMS';
			break;
		case 'chinapost post air mail' :
		case 'china post air mail' :
		case 'cpam' :
		case 'china post airmail' :
			$ebay_carrier = '中国邮政挂号';
			break;
		case 'china post air mail (surface)' :
			$ebay_carrier = '中国邮政平邮';
			break;
		case 'epacket' :
			$ebay_carrier = 'EUB';
			break;
		case 'fedex' :
		case 'fedex ip' :
		case 'fedex ie' :
			$ebay_carrier = 'FedEx';
			break;
		default :
			$ebay_carrier = '';
	}
	return $ebay_carrier;
}

function judge_has_location($user, $sku){
	//判断sku下是否有仓存
	//当为多个产品的组合sku其中一个无仓存和多产品定制商品sku时,无仓存标识
	global $dbcon;
	$ep = array();
	$eg = array();
	$no_location = false;
	$ep_sql = "select * from ebay_productscombine where ebay_user='$user' and goods_sn='$sku'";
	$result	= $dbcon->execute($ep_sql);
	$ep = $dbcon->fetch_one($result);
	$dbcon->free_result($result);
	if(tep_not_null($ep)){
		$goods_sncombines = explode(',', $ep['goods_sncombine']);
		foreach($goods_sncombines as $goods_sncombine){
			$pline = explode('*',$goods_sncombine);
			$eg_sql = "select * from ebay_goods where goods_sn = '{$pline[0]}' and ebay_user = '$user' ";
			$result	= $dbcon->execute($eg_sql);
			$eg = $dbcon->fetch_one($result);
			$dbcon->free_result($result);
			if(!tep_not_null($eg['goods_location']) && $eg['goods_location'] != 0){
				$no_location = true;
			}
		}
	}else{
		$eg_sql = "select * from ebay_goods where goods_sn = '$sku' and ebay_user = '$user' ";
		$result	= $dbcon->execute($eg_sql);
		$eg = $dbcon->fetch_one($result);
		$dbcon->free_result($result);
		if(empty($eg)){
			$no_location = true;
		}else if(!tep_not_null($eg['goods_location']) && $eg['goods_location'] != 0){
			$no_location = true;
		}
	}
	return $no_location;
}

function judge_is_splitorder($ebay_id){
	//判断订单是否为拆分订单
	global $dbcon;
	$es_sql = "select * from ebay_splitorder as es where split_order_id = '$ebay_id' and mode in(0,5) ";
	$result = $dbcon->execute($es_sql);
	return $dbcon->num_rows($result);
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

function get_all_sku_info($ordersn){
	//获取订单下面所有料号的信息,包括有没有包材,总的数量和显示的信息
	global $dbcon, $user;
	$all_sku_info = array();
    $bb		= "select * from ebay_orderdetail where ebay_ordersn in ('{$ordersn}') order by goods_location";
	//echo $bb; echo "<br>";
    $gg		= $dbcon->execute($bb);
    $gg		= $dbcon->getResultArray($gg);
    $totalqty	= 0;
    $no_packingmaterial = false; //所有料号无包料即订单无包料
	$has_packingstatus = '';
	$package_type = '';
	if(count($gg)){
		for($t=0;$t<count($gg);$t++){

			$sku			= $gg[$t]['sku'];
			$ebay_amount	= $gg[$t]['ebay_amount'];

			$ee					= "SELECT * FROM ebay_goods where goods_sn='$sku' and ebay_user='$user'";
			$ee					= $dbcon->execute($ee);
			$ee 			 	= $dbcon->getResultArray($ee);

			$goods_location		=  $ee[0]['goods_location'];
			$ebay_packingmaterial = $ee[0]['ebay_packingmaterial'];
			$with_pack_weight = $ee[0]['goods_weight2'];
			$without_pack_weight = $ee[0]['goods_weight3'];
			$package_type = $ee[0]['package_type'];

			$rr			= "select * from ebay_productscombine where ebay_user='$user' and goods_sn='$sku'";
			$rr			= $dbcon->execute($rr);
			$rr 	 	= $dbcon->getResultArray($rr);

			//if(count($rr) > 0 && empty($ee)){//modified by Herman.Xi @20121225
			if(count($rr) > 0){//modified by Herman.Xi @20121225
				$goods_sncombine	= $rr[0]['goods_sncombine'];
				$goods_sncombine    = explode(',',$goods_sncombine);

				for($e=0;$e<count($goods_sncombine);$e++){

					$pline			= explode('*',$goods_sncombine[$e]);
					$goods_sn		= $pline[0];
					$goddscount     = $pline[1] * $ebay_amount;
					$totalqty		= $totalqty + $goddscount;
					if($goods_sn!= ''){
						$ee			= "SELECT * FROM ebay_goods where goods_sn='$goods_sn' and ebay_user='$user'";
						$ee			= $dbcon->execute($ee);
						$ee 	 	= $dbcon->getResultArray($ee);
						$goods_location		=  $ee[0]['goods_location'];
						$ebay_packingmaterial = $ee[0]['ebay_packingmaterial'];
						$with_pack_weight = $ee[0]['goods_weight2'];
						$without_pack_weight = $ee[0]['goods_weight3'];
						$package_type = $ee[0]['package_type'];

						if(!tep_not_null($ebay_packingmaterial) || $ebay_packingmaterial == '0'){
							$no_packingmaterial = true;
						}
						if(tep_not_null($with_pack_weight)){
							$has_packingstatus = 'Y&nbsp;';
						}
						if(tep_not_null($without_pack_weight)){
							$has_packingstatus = 'N&nbsp;';
						}
						$all_sku_info['detail'][] = $has_packingstatus.'['.$goods_location.'] '.$goods_sn.'*'.$goddscount;
					}
				}
			}else{

				if(!tep_not_null($ebay_packingmaterial) || $ebay_packingmaterial == '0'){
					$no_packingmaterial = true;
				}
				if(tep_not_null($with_pack_weight)){
					$has_packingstatus = 'Y&nbsp;';
				}
				if(tep_not_null($without_pack_weight)){
					$has_packingstatus = 'N&nbsp;';
				}
				$totalqty		= $totalqty + $ebay_amount;
				$all_sku_info['detail'][] = $has_packingstatus.'['.$goods_location.'] '.$sku.'*'.$ebay_amount;
			}
		}
		$all_sku_info['totalqty'] = $totalqty;
		$all_sku_info['no_packingmaterial'] = $no_packingmaterial;
		$all_sku_info['has_packingstatus'] = $has_packingstatus;
		$all_sku_info['package_type'] = $package_type;
	}
	return $all_sku_info;
}

function get_all_sku_info2($ordersn){
	//获取订单下面所有料号的信息,包括有没有包材,总的数量和显示的信息
	//标记组合料号和虚拟料号的区别 add by Herman.Xi@ 20130220
	global $dbcon, $user;
	$all_sku_info = array();
    $bb		= "select * from ebay_orderdetail where ebay_ordersn in ('{$ordersn}') order by goods_location";
	//echo $bb; echo "<br>";
    $gg		= $dbcon->execute($bb);
    $gg		= $dbcon->getResultArray($gg);
    $totalqty	= 0;
    $no_packingmaterial = false; //所有料号无包料即订单无包料
	$has_packingstatus = '';
	$package_type = '';
	if(count($gg)){
		for($t=0;$t<count($gg);$t++){

			$sku			= $gg[$t]['sku'];
			$ebay_amount	= $gg[$t]['ebay_amount'];

			$ee					= "SELECT * FROM ebay_goods where goods_sn='$sku' and ebay_user='$user'";
			$ee					= $dbcon->execute($ee);
			$ee 			 	= $dbcon->getResultArray($ee);

			$goods_location		=  $ee[0]['goods_location'];
			$ebay_packingmaterial = $ee[0]['ebay_packingmaterial'];
			$with_pack_weight = $ee[0]['goods_weight2'];
			$without_pack_weight = $ee[0]['goods_weight3'];
			$package_type = $ee[0]['package_type'];

			$rr			= "select * from ebay_productscombine where ebay_user='$user' and goods_sn='$sku'";
			$rr			= $dbcon->execute($rr);
			$rr 	 	= $dbcon->getResultArray($rr);

			//if(count($rr) > 0 && empty($ee)){//modified by Herman.Xi @20121225
			if(count($rr) > 0){//modified by Herman.Xi @20121225
				$goods_sncombine	= $rr[0]['goods_sncombine'];
				$goods_sncombine    = explode(',',$goods_sncombine);

				for($e=0;$e<count($goods_sncombine);$e++){

					$pline			= explode('*',$goods_sncombine[$e]);
					$goods_sn		= $pline[0];
					$goddscount     = $pline[1] * $ebay_amount;
					$totalqty		= $totalqty + $goddscount;
					if($goods_sn!= ''){
						$ee			= "SELECT * FROM ebay_goods where goods_sn='$goods_sn' and ebay_user='$user'";
						$ee			= $dbcon->execute($ee);
						$ee 	 	= $dbcon->getResultArray($ee);
						$goods_location		=  $ee[0]['goods_location'];
						$ebay_packingmaterial = $ee[0]['ebay_packingmaterial'];
						$with_pack_weight = $ee[0]['goods_weight2'];
						$without_pack_weight = $ee[0]['goods_weight3'];
						$package_type = $ee[0]['package_type'];

						if(!tep_not_null($ebay_packingmaterial) || $ebay_packingmaterial == '0'){
							$no_packingmaterial = true;
						}
						if(tep_not_null($with_pack_weight)){
							$has_packingstatus = 'Y&nbsp;';
						}
						if(tep_not_null($without_pack_weight)){
							$has_packingstatus = 'N&nbsp;';
						}
						$all_sku_info['detail'][$sku]['info'][] = $has_packingstatus.'['.$goods_location.'] '.$goods_sn.'*'.$goddscount;
						$all_sku_info['detail'][$sku]['combine'] = 1;
					}
				}
			}else{

				if(!tep_not_null($ebay_packingmaterial) || $ebay_packingmaterial == '0'){
					$no_packingmaterial = true;
				}
				if(tep_not_null($with_pack_weight)){
					$has_packingstatus = 'Y&nbsp;';
				}
				if(tep_not_null($without_pack_weight)){
					$has_packingstatus = 'N&nbsp;';
				}
				$totalqty		= $totalqty + $ebay_amount;
				$all_sku_info['detail'][$sku]['info'][] = $has_packingstatus.'['.$goods_location.'] '.$sku.'*'.$ebay_amount;
				$all_sku_info['detail'][$sku]['combine'] = 0;
			}
		}
		$all_sku_info['totalqty'] = $totalqty;
		$all_sku_info['no_packingmaterial'] = $no_packingmaterial;
		$all_sku_info['has_packingstatus'] = $has_packingstatus;
		$all_sku_info['package_type'] = $package_type;
	}
	return $all_sku_info;
}

function get_sku_pack_info($goods_sn){
	//获取某个料号是否带包装信息
	global $dbcon, $user;
	$has_packingstatus = '';
	$ee			= "SELECT * FROM ebay_goods where goods_sn='$goods_sn' and ebay_user='$user'";
	$ee			= $dbcon->execute($ee);
	$ee 	 	= $dbcon->getResultArray($ee);
	$goods_location		=  $ee[0]['goods_location'];
	$ebay_packingmaterial = $ee[0]['ebay_packingmaterial'];
	$with_pack_weight = $ee[0]['goods_weight2'];
	$without_pack_weight = $ee[0]['goods_weight3'];
	$package_type = $ee[0]['package_type'];

	if(tep_not_null($with_pack_weight)){
		$has_packingstatus = 'Y&nbsp;';
	}
	if(tep_not_null($without_pack_weight)){
		$has_packingstatus = 'N&nbsp;';
	}
	return $has_packingstatus;
}

function get_global_packingmaterial_weight(){
	//取统一包装材料重量数据
	global $dbcon, $user;
	$tt		="	select weight,model from ebay_packingmaterial
				where  ebay_user ='$user' ";
	$tt		= $dbcon->execute($tt);
	$tt		= $dbcon->getResultArray($tt);

	$global_packingmaterial_weight=array();
	foreach($tt as	$t){
		$global_packingmaterial_weight[$t['model']]	= $t['weight'];
	}
	return $global_packingmaterial_weight;
}

function get_right_packagingmaterial(){
	#####理论上最优选择包装材料的计算方式#######未完成
	//add by Herman.Xi 2012-11-23
	global $dbcon, $user;
	$pm = 0;
	$global_packingmaterial_weight = get_global_packingmaterial_weight();
	$ebay_pm_contrast = array('A1'=>array('A1'=>'1','A2'=>'2','A3'=>'3','A4'=>'4','A5'=>'5','A6'=>'6','A7'=>'7'),'A2'=>array('A1'=>'2','A2'=>'1'),'A3'=>array('A1'=>'3','A2'=>'2','A3'=>'1'),'A4'=>array('A1'=>'4','A2'=>'2','A3'=>'2','A4'=>'1'));

	$theory_nums = 4;
	if($theory_nums != 1){
		$theory_weight = $theory_nums*$global_packingmaterial_weight[$ebay_packingmaterial];
		$actual_weights = array();
		foreach($ebay_pm_contrast[$ebay_packingmaterial] as $key => $value){
			if(ceil($theory_nums/$value) == 1){
				$actual_weights[$key] = $lilun_nums/$value*$global_packingmaterial_weight[$key];
			}
		}
		$flip_actual_weights = array_flip($actual_weights);
		if(min($actual_weights) > $theory_weight){
			$pm = $ebay_packingmaterial;
		}else{
			$pm = $flip_actual_weights[min($actual_weights)];
		}
	}else{
		$pm = $ebay_packingmaterial;
	}
	return $pm;
}

function update_orderweight_by_sku($update_sku){
	#####料号更新重量,及时更新包含该料号的待发货订单重量#######
	//add by Herman.Xi 2012-12-05
	global $dbcon, $user, $EBAY_ACCOUNTS_CONFIG;
	$sql = "SELECT a.ebay_id,a.ebay_countryname,a.ebay_carrier,a.ebay_account,a.ebay_total,b.sku
			FROM ebay_order a
			INNER JOIN ebay_orderdetail b ON a.ebay_ordersn = b.ebay_ordersn
			WHERE a.ebay_status not in ('612','663','613','2','617','625')
			AND	  a.ebay_combine != '1'
			AND	  a.ebay_user = '$user'
			ORDER BY a.ebay_id
			";
	$result = $dbcon->execute($sql);
	$ebay_orderArray = $dbcon->getResultArray($result);
	$dbcon->free_result($result);
	$record_order = array();
	foreach($ebay_orderArray as $value){
		$ebay_id = $value['ebay_id'];
		$ebay_countryname = $value['ebay_countryname'];
		$ebay_carrier = $value['ebay_carrier'];
		$ebay_account = $value['ebay_account'];
		$ebay_total = $value['ebay_total'];
		if(!in_array($ebay_id, $record_order)){
			$record_order[] = $ebay_id;
			$realskus = array_keys(get_realskuinfo($value['sku']));
			if(count(array_intersect($realskus,$update_sku))){
				if(in_array($ebay_account, $EBAY_ACCOUNTS_CONFIG)){
					$totalweight = recalcorderweight($ordersn, $ebay_packingmaterial);
					if (empty($ebay_countryname)){
						$ebay_carrier = '';
						$fee = 0;
					}else{
						$fees			= calcshippingfee($totalweight,$ebay_countryname,$ebay_id,$ebay_account,$ebay_total);
						$ebay_carrier	= $fees[0];
						$fee			= $fees[1];
						$totalweight	= $fees[2];
					}
					$sql	= "update ebay_order set ebay_carrier='$ebay_carrier', ordershipfee='$fee', orderweight ='$totalweight' ,packingtype ='$ebay_packingmaterial' where ebay_id ='$ebay_id' ";
				}else{
					if (empty($ebay_countryname)){
						$shipfee = 0;
					}else{
						$shipfee = calctrueshippingfee($ebay_carrier, $totalweight, $ebay_countryname, $ebay_id);
					}
					$sql	= "update ebay_order set ordershipfee='$shipfee', orderweight ='$totalweight' ,packingtype ='$ebay_packingmaterial' where ebay_id ='$ebay_id' ";
				}
				$dbcon->execute($bb);
			}
		}
	}
}

function judge_ordermove654to667($order_sn){
	//自动包货物完全之后自动跳转到部分发货快递
	global $dbcon;
	$sql = "select count(*) from ebay_orderdetail where ebay_ordersn = '{$order_sn}' ";
	$result= $dbcon->execute($sql);
	$o_num = $dbcon->num_rows($result);
    $gg		= $dbcon->getResultArray($result);
	$sql = "SELECT count(*)
			FROM ebay_packing_status
			WHERE order_sn = '$order_sn'
			AND realnum = totalnum ";
	$result = $dbcon->execute($sql);
	$p_num = $dbcon->num_rows($result);
	if($o_num == $p_num){
		now_order_status_log($order_sn);
		mark_shipping($order_sn, '624');//
	}
}

function judge_has_condition($ebay_id){
	//判断明细表中料号有无料号名字和数量,如果没有返回错误
	global $dbcon, $user;
	$str_where = is_numeric($ebay_id)&&strlen($ebay_id)<9 ? "a.ebay_id={$ebay_id}" : "a.ebay_ordersn='{$ebay_id}'"; //判断是 ebay_id 还是 ebay_ordersn
	$condition = array('b.sku', 'b.ebay_itemprice', 'b.ebay_amount');
	$sql = "select ".join(',', $condition)." from ebay_order as a inner join ebay_orderdetail as b on a.ebay_ordersn = b.ebay_ordersn where $str_where and a.ebay_combine != '1' and a.ebay_user = '$user' ";
	$result = $dbcon->execute($sql);
	$ebay_orderArray = $dbcon->getResultArray($result);
	foreach($ebay_orderArray as $line){
		/*if(!tep_not_null(trim($line['sku'])) || !tep_not_null(trim($line['ebay_itemprice'])) || empty($line['ebay_amount'])){
			return true;
		}*/
		if(!tep_not_null(trim($line['sku'])) || empty($line['ebay_amount'])){
			return true;
		}
	}
	return false;
}

function get_ebay_account(){
	global $dbcon,$user;
	$sql = "select ea.ebay_account, ep.id from ebay_account ea inner join ebay_platform ep on ea.ebay_platform = ep.ebay_platform where ea.ebay_user='$user' order by ea.ebay_platform ASC,ea.ebay_account desc ";
	$result = $dbcon->execute($sql);
	$ep_arr = $dbcon->getResultArray($result);
	$arr = array();
	foreach($ep_arr as $row){
		$arr[$row['id']] = $arr['ebay_account'];
	}
	return $arr;
}
function get_ebay_platform(){
	global $dbcon;
	$sql = "select * from ebay_platform";
	$result = $dbcon->execute($sql);
	$ep_arr = $dbcon->getResultArray($result);
	$arr = array();
	foreach($ep_arr as $row){
		$arr[$row['id']] = $row['ebay_platform'];
	}
	return $arr;
}

function get_order_productsweight($ebay_ordersn){
	//获取单个订单的产品理论重量
	global $dbcon, $user;

    $sql	= "select * from ebay_orderdetail where ebay_ordersn = '$ebay_ordersn' ";
    $result	= $dbcon->execute($sql);
    $gg		= $dbcon->getResultArray($result);

	$orderweight = 0;
	$goodcosts = 0;
	$itemprices = 0;
	if(count($gg)){
		for($t=0;$t<count($gg);$t++){
			$sku			= $gg[$t]['sku'];
			$ebay_amount	= $gg[$t]['ebay_amount'];
			$ebay_itemprice	= $gg[$t]['ebay_itemprice'];

			$ee					= "SELECT * FROM ebay_goods where goods_sn='$sku' and ebay_user='$user'";
			$ee					= $dbcon->execute($ee);
			$ee 			 	= $dbcon->getResultArray($ee);

			$rr			= "select * from ebay_productscombine where ebay_user='$user' and goods_sn='$sku'";
			$rr			= $dbcon->execute($rr);
			$rr 	 	= $dbcon->getResultArray($rr);

			if(count($rr) > 0 && empty($ee)){//modified by Herman.Xi @20121225
				$goods_sncombine	= $rr[0]['goods_sncombine'];
				$goods_sncombine    = explode(',',$goods_sncombine);

				for($e=0;$e<count($goods_sncombine);$e++){

					$pline			= explode('*',$goods_sncombine[$e]);
					$goods_sn		= $pline[0];
					$goddscount     = $pline[1] * $ebay_amount;
					if($goods_sn!= ''){
						$ee			= "SELECT * FROM ebay_goods where goods_sn='$goods_sn' and ebay_user='$user'";
						$ee			= $dbcon->execute($ee);
						$ee 	 	= $dbcon->getResultArray($ee);

						$orderweight += $ee[0]['goods_weight']*$goddscount;
						$goodcosts += $ee[0]['goods_cost']*$goddscount;
					}
				}
			}else{
				$orderweight += $ee[0]['goods_weight']*$ebay_amount;
				$goodcosts += $ee[0]['goods_cost']*$ebay_amount;
			}
			$itemprices += $ebay_itemprice*$ebay_amount;
		}
	}
	return array($orderweight,$goodcosts,$itemprices);
}
function pda_out_warehouse($ebay_id){
	global $dbcon;
	addoutorder($ebay_id);//配货完成之后扣除库存
	$sql = "update ebay_order set ebay_status = '672' where ebay_id = '$ebay_id' and ebay_combine != 1";//自动跳转到等待包装
	$dbcon->execute($sql) or die("Fail : $sql");
}
function func_strreplace($str){
	//处理特殊字符
	$str  = str_replace("'","&acute;",$str);
	$str  = str_replace("\"","&quot;",$str);
	$tes = array("=" , ")" , "(" , "{", "}");
	foreach($tes as $v){
		$str = str_replace($v,"",$str);
	}
	return addslashes($str);
}

function tep_selectHTML($ebay_topmenus, $id, $selected, $showall=true){
	$selectHTML = "<select id=\"$id\" name=\"$id\">";
	if($showall){ $selectHTML .= "<option value=\"all\">All</option>";}
	foreach($ebay_topmenus as $key => $value){
		$selectHTML .= "<option value=\"$key\"";
		if($key != 'all' && $key == $selected){ $selectHTML .= " selected=\"selected\""; }
		$selectHTML .= " >$value</option>";
	}
	$selectHTML .= "</select>";
	echo $selectHTML;
}

function get_realskulist($ebay_id){
	//add by Herman.Xi ·2013-01-08
	//获取订单号下面所有的料号信息,包括合并包裹,多料号,单料号,组合订单,合并订单
	global $dbcon, $user;

	$ordercheck		= "select ebay_id,ebay_tid,ebay_ordersn,ebay_countryname,ebay_carrier,ebay_status,ebay_account,ebay_noteb,orderweight from ebay_order where ebay_id=$ebay_id ";
	$ordercheck		= $dbcon->execute($ordercheck);
	$ordercheck		= $dbcon->getResultArray($ordercheck);
	$skuinfos = array();
	if(!empty($ordercheck)){
		$orderdetails = "SELECT sku, ebay_amount FROM ebay_orderdetail where ebay_ordersn='{$ordercheck[0]['ebay_ordersn']}'";
		$orderdetails = $dbcon->execute($orderdetails);
		$orderdetails = $dbcon->getResultArray($orderdetails);

		foreach ($orderdetails AS $_k => $odlist){
			$sku = trim($odlist['sku']);
			$ebay_amount = $odlist['ebay_amount'];
			$sku_arr = get_realskuinfo($sku);
			foreach($sku_arr as $or_sku => $or_nums){
				if(isset($skuinfos[$or_sku])){
					$skuinfos[$or_sku]+=$or_nums * $ebay_amount;
				}else{
					$skuinfos[$or_sku]=$or_nums * $ebay_amount;
				}
			}
		}

		/*$es_sql	= "SELECT split_order_id FROM `ebay_splitorder` WHERE `main_order_id` = '$ebay_id' AND mode = 3 AND recordnumber = '1' ";
		$result			= $dbcon->execute($es_sql);
		$ordercheck2	= $dbcon->getResultArray($result);*/
		$es_sql	= "SELECT ebay_id FROM `ebay_order` WHERE `combine_package` = '$ebay_id' AND ebay_user = '$user' and ebay_combine != 1 ";
		$result			= $dbcon->execute($es_sql);
		$ordercheck2	= $dbcon->getResultArray($result);
		$split_order_ids = array();
		foreach($ordercheck2 as $value){
			$split_order_ids[] = $value['ebay_id'];
		}
		if(!empty($split_order_ids)){
			$sql = "select ebay_id,ebay_ordersn from ebay_order where ebay_id IN (".join(",",$split_order_ids).") and ebay_status='{$ordercheck[0]['ebay_status']}' ";
			$sql = $dbcon->execute($sql);
			$orderlists = $dbcon->getResultArray($sql);
			if(count($split_order_ids) != count($orderlists)){
				return 1; //当合并包裹不在同一个文件夹当中
			}else{
				//$ebay_ordersns = array();
				foreach($orderlists as $_orderlist){
					//$ebay_ordersns[] = $_orderlist['ebay_ordersn'];
					$orderdetails = "SELECT sku, ebay_amount FROM ebay_orderdetail where ebay_ordersn='{$_orderlist['ebay_ordersn']}'";
					$orderdetails = $dbcon->execute($orderdetails);
					$orderdetails = $dbcon->getResultArray($orderdetails);

					foreach ($orderdetails AS $_k => $odlist){
						$sku = trim($odlist['sku']);
						$ebay_amount = $odlist['ebay_amount'];
						$sku_arr = get_realskuinfo($sku);
						foreach($sku_arr as $or_sku => $or_nums){
							if(isset($skuinfos[$or_sku])){
								$skuinfos[$or_sku]+=$or_nums * $ebay_amount;
							}else{
								$skuinfos[$or_sku]=$or_nums * $ebay_amount;
							}
						}
					}
				}
			}
		}
	}
	return $skuinfos;
}

function autoJump_part_express($ebay_id){
	//部分包货订单包货完全自动跳转
	//add Herman.Xi @ 20120108
	//global $dbcon;
	global $dbcon;
	$skuinfos = get_realskulist($ebay_id);
	$sql = "SELECT sku FROM ebay_packing_status WHERE ebay_id='{$ebay_id}' AND realnum = totalnum";
	$result	 = $dbcon->execute($sql);
    $eps_arr = $dbcon->getResultArray($result);
	$all_packaging = array();
	foreach($eps_arr as $row){
		$all_packaging[] = $row['sku'];
	}
	$autojump = true;
	foreach($skuinfos as $sku => $nums){
		if(!in_array($sku, $all_packaging)){
			$autojump = false;
			break;
		}
	}
	if($autojump){
		$update_sql = "UPDATE ebay_order SET ebay_status=667 WHERE ebay_id='{$ebay_id}'";
		$dbcon->execute($update_sql);//部分包货完全之后自动跳转到部分发货快递
		$log_data = "[".date("Y-m-d H:i:s")."]\t部分包货成功跳转---{$ebay_id}---的状态为---{667}!";
		write_log('move_order_'.date("Ymd").'/'.date("H").'.txt', $log_data."\n\n");
	}
	return $autojump;
}

function part_get_scanrecord($ebay_id){
	global $dbcon;
	$arr = array();
	$sql = "select * from ebay_packing_status where ebay_id = '{$ebay_id}' and realnum = totalnum and status = '1' ";
	$result = $dbcon->execute($sql);
	$pgs_arr = $dbcon->getResultArray($result);
	foreach($pgs_arr as $line){
		$arr[] = $line['sku'];
	}
	$dbcon->free_result($result);
	return $arr;
}

function pda_get_scanrecord($ebay_id){
	global $dbcon;
	$arr = array();
	$sql = "select * from ebay_order_scan_record where ebay_id = '{$ebay_id}' and is_scan = '1' ";
	$result = $dbcon->execute($sql);
	$pgs_arr = $dbcon->getResultArray($result);
	foreach($pgs_arr as $line){
		$arr[] = $line['sku'];
	}
	$dbcon->free_result($result);
	return $arr;
}

function judge_has_goods($sku){
	//判断料号是组合料号,并且不属于单个料号
	global $dbcon,$user;

	$ee			= "SELECT * FROM ebay_goods where goods_sn='$sku' and ebay_user='$user'";
	$ee			= $dbcon->execute($ee);
	$ee 		= $dbcon->getResultArray($ee);

	$rr			= "select * from ebay_productscombine where ebay_user='$user' and goods_sn='$sku'";
	$rr			= $dbcon->execute($rr);
	$rr 	 	= $dbcon->getResultArray($rr);

	if(count($ee) > 0 && empty($rr)){
		return 1; //单料号
	}
	if(count($rr) > 0 && empty($ee)){
		return 2; //组合料号
	}
	if(count($rr) > 0 && count($ee) > 0){
		return 3; //既是单料号又是组合料号
	}
	return false;
}

function scientific_convert_digital($ret){
	//读取的科学计数法转换成数字
	//add By Herman.Xi @ 20130222
	if(is_numeric($ret)){
		$ret = number_format($ret,'0','','');
		return $ret;
	}
	return $ret;
}

function verify_order_is_exists($ebay_id, $recordnumber, $is_combine){
	//提供的参数验证订单是否存在
	//add by Herman.Xi @ 20130224
	global $dbcon,$user;
	$param_array[] = "ebay_id = '$ebay_id'";
	$param_array[] = "ebay_user = '$user'";
	if(tep_not_null($recordnumber)){
		$param_array[] = "recordnumber = '$recordnumber'";
	}
	if(tep_not_null($is_combine)){
		$param_array[] = "ebay_combine != '$is_combine'";
	}
	$param_str = join(" and ", $param_array);
	$sql = "select * from ebay_order where $param_str ";
	$sql = $dbcon->execute($sql);
	$sql = $dbcon->num_rows($sql);
	return $sql;
}

function func_manually_split_orders($ebay_id, $other=array()){
	/**
	* @author Herman.Xi (席慧超)
	* @version 1.0
	* add by Herman.Xi @ 20130224
	* 手动拆分订单函数
	*/
	global $dbcon,$user;
	$split_log = "\r\n\r\n";
	$isend = false;
	if($isend) continue; //如果是里面有拦截的不能自动拦截
	$sql = "select a.* from ebay_order as a where ebay_id = '{$ebay_id}' and ebay_combine != '1' and ebay_user = '$user' ";
	$result = $dbcon->execute($sql);
	$corder = $dbcon->fetch_one($result);
	if(empty($corder)){
		echo "目前系统无法找到订单 $ebay_id !";
	}
	$ebay_ordersn = $corder['ebay_ordersn'];
	$ebay_paystatus = $corder['ebay_paystatus'];
	$recordnumber = $corder['recordnumber'];
	$ebay_tid = $corder['ebay_tid'];
	$ebay_ptid = $corder['ebay_ptid'];
	$ebay_total0 = $corder['ebay_total'];
	$ebay_orderid = $corder['ebay_orderid'];
	$ebay_createdtime = $corder['ebay_createdtime'];
	$ebay_paidtime = $corder['ebay_paidtime'];
	//$ebay_user = $corder['ebay_user'];
	$ebay_userid = mysql_real_escape_string($corder['ebay_userid']);
	$ebay_username = addslashes($corder['ebay_username']);
	$ebay_usermail = addslashes($corder['ebay_usermail']);
	$ebay_street = addslashes($corder['ebay_street']);
	$ebay_street1 = addslashes($corder['ebay_street1']);
	$ebay_city = addslashes($corder['ebay_city']);
	$ebay_state = addslashes($corder['ebay_state']);
	$ebay_countryname = addslashes($corder['ebay_countryname']);
	$ebay_postcode = addslashes($corder['ebay_postcode']);
	$ebay_phone = $corder['ebay_phone'];
	$ebay_status = 655;
	$ebay_addtime = time();
	$ebay_shipfee = $corder['ebay_shipfee'];
	$ebay_tracknumber = $corder['ebay_tracknumber'];
	$ebay_account = $corder['ebay_account'];
	$ebay_note = mysql_real_escape_string($corder['ebay_note']);
	$ebay_carrier = $corder['ebay_carrier'];
	$ebay_warehouse = $corder['ebay_warehouse'];
	$ebay_currency = $corder['ebay_currency'];
	$ebay_phone1 = $corder['ebay_phone1'];
	$is_main_order = $corder['is_main_order'];
	$combine_package = $corder['combine_package'];
	$packingtype = $corder['packingtype'];
	$scantime = $corder['scantime'];
	$ebay_couny = $corder['ebay_couny'];
	$ebayorder_site = $corder['ebay_site'];
	$eBayPaymentStatus = $corder['eBayPaymentStatus'];
	$orderweight = $corder['orderweight'];

	if($ebay_account == 'dresslink.com'){//支持dresslink.com
		$ebay_noteb = $corder['ebay_noteb'].' 拆分 订单';
	}else{
		$ebay_noteb = '拆分 订单';
	}
	if (in_array($ebay_carrier, array('UPS','DHL','TNT','EMS','FedEx'))){
		echo "订单 $ebay_id 运输方式为快递,请确认该订单是否需要拆分!<br>";
		continue;
	}
	$order_statistics->deleteAll($ebay_ordersn); //删除statistics表中的记录

	if(empty($ebay_carrier)){
		$fees = calcshippingfee($orderweight,$ebay_countryname,$ebay_id,$ebay_account,$corder['ebay_total']);
		$ebay_carrier = $fees[0];
	}
	$dbcon->free_result($result);
	$sql = "SELECT * FROM ebay_orderdetail as eo WHERE eo.ebay_ordersn = '$ebay_ordersn'";
	$result = $dbcon->execute($sql);
	$eo = $dbcon->getResultArray($result);
	$dbcon->free_result($result);
	$weightlists = array();
	$skuinfo = array();
	$goods_sn_nums = 0;
	$shippfee_arr = array();
	foreach($eo as $k=>$f){
		$sku = $f['sku'];
		$ebay_amount = $f['ebay_amount'];
		$goods_sn_nums += $ebay_amount;
		if(strpos($sku, "#")){
			$sku = str_replace("#", "", $sku);
		}
		$shippfee_arr[$sku] = round($f['shipingfee']/$ebay_amount,3);
		$sql = "select * from ebay_goods where goods_sn='$sku' and ebay_user ='$user' ";
		$result = $dbcon->execute($sql);
		$eg = $dbcon->fetch_one($result);
		$dbcon->free_result($result);
		$ebay_packingmaterial = $eg['ebay_packingmaterial'];
		$goods_weight = $eg['goods_weight'];
		$capacity = $eg['capacity'];
		$sql = "select * from ebay_packingmaterial where  model='$ebay_packingmaterial' and ebay_user ='$user' ";
		$result = $dbcon->execute($sql);
		$ep = $dbcon->fetch_one($result);
		$dbcon->free_result($result);
		$pweight = isset($ep['weight']) ? $ep['weight'] : 0;

		$skuinfo[$sku] = $f;
		for($i=1; $i<=$ebay_amount; $i++){
			$var = $sku;
			if($ebay_amount <= $capacity){
				$ppweight			= $pweight/$ebay_amount;
			}else{
				$ppweight			= (1 + ($ebay_amount-$capacity)/$capacity*0.6)*$pweight/$ebay_amount;
			}
			$weightlists[$var][] = $goods_weight + $ppweight;
		}
	}
	//echo $goods_sn_nums; echo "<br>";
	if($goods_sn_nums <= 1) continue;

	$sql = "update ebay_order set ebay_combine = '1' where ebay_id = '$ebay_id' ";
	$split_log .= "更新被拆分的订单信息,设置为隐藏\r\n".$sql ."\r\n";
	$dbcon->execute($sql) or die("Fail : $sql");

	//echo "<pre>"; print_r($skuinfo); echo "\r\n";
	//echo "<pre>"; print_r($weightlists); echo "\r\n";
	$keyarray = array();
	$keyarrays = array();
	$checkweight = 0;
	$arrinfo = get_order_productsweight($ebay_ordersn);
	$realweight = $arrinfo[0];
	$realcosts = $arrinfo[1];
	$itemprices = $arrinfo[2];
	/*foreach($weightlists AS $wk => $wv){
		foreach($wv as $weightlist){
			$checkweight += $weightlist;
		}
	}
	echo $checkweight; echo "<br>"; exit;*/
	foreach($weightlists AS $wk => $wv){
		foreach($wv as $weightlist){
			$checkweight += $weightlist;
			//$realweight += $weightlist;
			if($checkweight>1.85){
				$keyarrays[] = $keyarray;
				$keyarray = array();
				$checkweight = $weightlist;
				$keyarray[$wk][] = $wk;
			}else{
				$keyarray[$wk][] = $wk;
			}
		}
	}
	if(!empty($keyarray)){
		$keyarrays[] = $keyarray;
	}
	/*echo "<pre>"; print_r($keyarrays); echo "\r\n";
	exit;*/
	foreach($keyarrays as $keyarray){
		$ebay_total = 0;
		$totalweight = 0;
		$insert_ebay_ids = array();
		foreach($keyarray as $k => $kav){
			$ebay_total += $skuinfo[$k]['ebay_itemprice'] * count($kav);
		}
		//echo $ebay_total; echo "\r\n";
		$shipfee = 0;
		$val = generateOrdersn();
		$sql = "insert into  ebay_order(ebay_ordersn,ebay_paystatus,recordnumber,ebay_tid,ebay_ptid,ebay_orderid,ebay_createdtime,ebay_paidtime,ebay_userid,ebay_username,ebay_usermail,ebay_street,ebay_street1,ebay_city,ebay_state,ebay_couny,ebay_countryname,ebay_postcode,ebay_phone,ebay_total,ebay_status,ebay_user,ebay_addtime,ebay_shipfee,ebay_account,ebay_note,ebay_noteb,ebay_carrier,ebay_warehouse,ebay_tracknumber,ebay_site,eBayPaymentStatus,ebay_currency,ordershipfee,scantime,orderweight,ebay_phone1,packingtype,is_main_order,combine_package) values('$val','$ebay_paystatus','$recordnumber','$ebay_tid','$ebay_ptid','$ebay_orderid','$ebay_createdtime','$ebay_paidtime','$ebay_userid','$ebay_username','$ebay_usermail','$ebay_street','$ebay_street1','$ebay_city','$ebay_state','$ebay_couny','$ebay_countryname','$ebay_postcode','$ebay_phone','$ebay_total','$ebay_status','$user','$mctime','$ebay_shipfee','$ebay_account','$ebay_note','$ebay_noteb','$ebay_carrier','$ebay_warehouse','$ebay_tracknumber','$ebayorder_site','$eBayPaymentStatus','$ebay_currency','$shipfee','$scantime','$totalweight','$ebay_phone1','$packingtype','$is_main_order','$combine_package')";
		//$split_log .= "添加拆分订单信息到订单列表中\r\n".$sql ."\r\n";
		if($dbcon->execute($sql)){

			$insert_ebay_id = $dbcon->insert_id();
			$insert_ebay_ids[] = $insert_ebay_id;
			mark_shipping($insert_ebay_id, $ebay_status);
			$sql = "insert into ebay_splitorder (recordnumber, main_order_id, split_order_id, create_date) values ('$recordnumber', '$ebay_id', '$insert_ebay_id', '".date("Y-m-d H:i:s")."')";
			$split_log .= "添加主定单和拆分订单到关系表中\r\n".$sql ."\r\n";
			$dbcon->execute($sql) or die("Fail : $sql");

			foreach($keyarray as $k => $kav){

				$ebay_itemid = $skuinfo[$k]['ebay_itemid'];
				$ebay_itemtitle = mysql_real_escape_string($skuinfo[$k]['ebay_itemtitle']);
				$ebay_shiptype = $skuinfo[$k]['ebay_shiptype'];
				$shipingfee = $skuinfo[$k]['shipingfee'];
				$ebay_itemurl = $skuinfo[$k]['ebay_itemurl'];
				$ebay_site = $skuinfo[$k]['ebay_site'];
				$storeid = $skuinfo[$k]['storeid'];
				$ListingType = $skuinfo[$k]['ListingType'];
				$ebaydetail_tid = $skuinfo[$k]['ebay_tid'];
				$FeeOrCreditAmount = $skuinfo[$k]['FeeOrCreditAmount'];
				$FinalValueFee = $skuinfo[$k]['FinalValueFee'];
				$attribute = $skuinfo[$k]['attribute'];
				$notes = $skuinfo[$k]['notes'];
				$sku = $k;
				$ebay_amount = count($kav);
				if($ebay_account == 'dresslink.com'){//支持dresslink.com
					if(isset($shippfee_arr[$sku])){
						$shipingfee = $shippfee_arr[$sku]*$ebay_amount;//相同料号运费拆分
					}
				}
				$ebay_itemprice = $skuinfo[$k]['ebay_itemprice'];
				$goods_location		= $skuinfo[$k]['goods_location'];

				$sql = "INSERT INTO `ebay_orderdetail` (`ebay_ordersn` ,`ebay_itemid` ,`ebay_itemtitle` ,`ebay_itemprice` ,";
				$sql .= "`ebay_amount` ,`ebay_createdtime` ,`ebay_shiptype` ,`ebay_user`,`sku`,`shipingfee`,`ebay_account`,`addtime`,`ebay_itemurl`,`ebay_site`,`recordnumber`,`storeid`,`ListingType`,`ebay_tid`,`FeeOrCreditAmount`,`FinalValueFee`,`attribute`,`notes`,`goods_location`)VALUES ('$val', '$ebay_itemid' , '$ebay_itemtitle' , '$ebay_itemprice' , '$ebay_amount'";
				$sql .= " , '$mctime' , '$ebay_shiptype' , '$user','$sku','$shipingfee','$ebay_account','$mctime','$ebay_itemurl','$ebay_site','$recordnumber','$storeid','$ListingType','$ebaydetail_tid','$FeeOrCreditAmount','$FinalValueFee','$attribute','$notes','$goods_location')";
				//$split_log .= "添加对应的产品orderdetail信息\r\n".$sql ."\r\n";
				$dbcon->execute($sql) or die("Fail : $sql");
			}
			$totalweight 	= recalcorderweight($val, $ebay_packingmaterial);
			if($ebay_account == 'dresslink.com'){
				$ordershipfee = calctrueshippingfee($ebay_carrier, $totalweight, $ebay_countryname, $insert_ebay_id);
				$arrinfo2 = get_order_productsweight($val);
				$splitweight = $arrinfo2[0];
				$splitcosts = $arrinfo2[1];
				$splititemprices = $arrinfo2[2];
				$ebay_total2 = round(($splititemprices/$itemprices)*$ebay_total0,2);//成本价比例拆分
				$ebay_shipfee2 = round(($splitweight/$realweight)*$ebay_shipfee,3);//运费按重量拆分
				//echo $val."--------".$splititemprices."------------".$itemprices."<br>";
				$sql = "update ebay_order set ebay_total = '$ebay_total2', ebay_shipfee = '$ebay_shipfee2', ordershipfee='$ordershipfee', orderweight ='$totalweight' ,packingtype ='$ebay_packingmaterial' where ebay_id ='$insert_ebay_id' ";
				//echo $sql."<br>";
			}else{
				$ordershipfee = calctrueshippingfee2($ebay_carrier, $totalweight, $ebay_countryname, $insert_ebay_id);
				$sql = "update ebay_order set ordershipfee='$ordershipfee', orderweight ='$totalweight' ,packingtype ='$ebay_packingmaterial' where ebay_id ='$insert_ebay_id' ";
				//$split_log .= "超重订单 $ebay_id 拆分出新订单 $insert_ebay_id \r\n";
			}
			$dbcon->execute($sql) or die("Fail : $sql");

			$order_statistics->replaceData($val, array('mask'=>1), array('mask'=>1)); //添加statistics记录
			$split_log .= "[".date("Y-m-d H:i:s")."]\t原订单---{$ebay_id}--被{$truename}--拆分出新订单( ".join(',', $insert_ebay_ids)." )\r\n";
		}else{
			echo "超重订单 $recordnumber 拆分失败!<br>";
			break;
		}
	}
	//echo $split_log;
	write_log('split_order_'.date("Ymd").'/'.date("H").'.txt', $split_log);
}

function judge_has_Special_item($sku){
	//判断订单下该料号是否是特殊料号
	//add by Herman.Xi @ 20130304
	global $dbcon, $user;
	global $__liquid_items_postbyhkpost,$__liquid_items_cptohkpost,$__liquid_items_fenmocsku,$__liquid_items_BuiltinBattery,$__liquid_items_Paste;

	$real_sku = array();
	$ep_sql = "select * from ebay_productscombine where ebay_user='$user' and goods_sn='$sku'";
	$result	= $dbcon->execute($ep_sql);
	$ep = $dbcon->fetch_one($result);
	$dbcon->free_result($result);
	if(tep_not_null($ep)){
		$goods_sncombines = explode(',', $ep['goods_sncombine']);
		foreach($goods_sncombines as $goods_sncombine){
			$pline = explode('*',$goods_sncombine);
			$real_sku[] = $pline[0];
		}
	}else{
		$real_sku[] = $sku;
	}
	$array_intersect_yieti = array_intersect($real_sku, $__liquid_items_postbyhkpost);
	$array_intersect_zhijiayou = array_intersect($real_sku, $__liquid_items_cptohkpost);
	$array_intersect_fenmoc = array_intersect($real_sku, $__liquid_items_fenmocsku);
	$array_intersect_bin = array_intersect($real_sku, $__liquid_items_BuiltinBattery);
	$array_intersect_Paste = array_intersect($real_sku, $__liquid_items_Paste);
	if(count($array_intersect_yieti) > 0 || count($array_intersect_zhijiayou) > 0 || count($array_intersect_fenmoc) > 0 || count($array_intersect_bin) > 0 || count($$array_intersect_Paste) > 0){
		return true;
	}
	return false;
}

function check_part_package($ebay_id){
	//检查是否是部分包货订单,拦截和审核存在关系
	//add by Herman.Xi @ 20130321
	global $dbcon;
	$isend     = false;
	$ordercheck		= "select ebay_id,ebay_ordersn from ebay_order where ebay_id={$ebay_id} and ebay_status = '654' ";
	$ordercheck		= $dbcon->execute($ordercheck);
	$ordercheck		= $dbcon->fetch_one($ordercheck);
	if(!empty($ordercheck)){
		$skuinfos = get_realskulist($ebay_id);
		foreach($skuinfos as $or_sku => $num){
			$compare_sql = "SELECT ebay_ordersn,sku,check_status FROM ebay_unusual_order_check WHERE ebay_id = '$ebay_id' and sku='{$or_sku}' ";
			$compare_sql = $dbcon->execute($compare_sql);
			$compare_sql = $dbcon->fetch_one($compare_sql);
			if (!empty($compare_sql)){
				$isend = true;
				break;
			}
		}
	}
	return $isend;
}

function assign_item_to_partPackage($ebay_id, $scan_sku, $scan_num){
	//PDA 扫描部分包货订单,自动分配到已经审核的订单中去
	//add by Herman.Xi @ 20130321
	global $dbcon, $truename, $user;

	$sql = "select ebay_status,ebay_ordersn from ebay_order where ebay_id = $ebay_id and ebay_user = '$user' and ebay_combine != 1 ";
	$result = $dbcon->execute($sql);
	$ordercheck = $dbcon->fetch_one($result);
	if(empty($ordercheck)){
		return false;
	}

	$orderdetail = array();
	$orderdetails = "SELECT * FROM ebay_orderdetail where ebay_ordersn = '{$ordercheck['ebay_ordersn']}' ";
	$orderdetails = $dbcon->execute($orderdetails);
	$orderdetails = $dbcon->getResultArray($orderdetails);
	foreach ($orderdetails AS $_orderdetail){
		$sku_array = get_realskuinfo($_orderdetail['sku']);
		foreach($sku_array AS $real_sku=>$sku_num){
			$orderdetail[$_orderdetail['ebay_id']] = array('ebay_id'=>$_orderdetail['ebay_id'], 'ebay_ordersn'=>$_orderdetail['ebay_ordersn'], 'sku'=>$real_sku, 'ebay_amount'=>$sku_num*$_orderdetail['ebay_amount']);
		}
	}
	unset($orderdetails);
	//echo "<pre>"; print_r($orderdetail); echo "<br>";
	if(empty($orderdetail)){
		return false;
	}

	if($ordercheck['ebay_status'] == '654'){//已打印部分包货
		$checksql = "SELECT a.ebay_id,a.ebay_ordersn,a.ebaydetail_id,a.sku,b.realnum,b.totalnum FROM ebay_unusual_order_check a left join ebay_packing_status b ON a.ebay_id = b.ebay_id AND a.ebaydetail_id = b.ebaydetail_id AND a.sku = b.sku  WHERE a.ebay_id='{$ebay_id}' AND a.sku = '$scan_sku' AND a.check_status = 1 ORDER BY modtime ASC ";//审核通过的料号需求量
		$checksql = $dbcon->execute($checksql);
		$checksql = $dbcon->getResultArray($checksql);

		if(count($checksql) == 0){
			return false;
		}/*else if(count($checksql) == 1){
			if(empty($totalnum)){
				$sql = "INSERT INTO ebay_packing_status SET ebay_id={$ebay_id},ebaydetail_id={$checksql[0]['ebaydetail_id']},order_sn='{$checksql[0]['ebay_ordersn']}',sku='{$checksql[0]['sku']}',realnum=$scan_num,totalnum={$orderdetail[$checksql[0]['ebaydetail_id']]['ebay_amount']},packagingstaff='',scanuser='{$truename}',status=1,time=".time();
				$dbcon->query($sql);
				echo "新增配货 $ebay_id 明细 ID $ebaydetail_id 料号 $sku 数量 $scan_num 需求量 $orderdetail[$ebaydetail_id]['ebay_amount']"; echo "<br>";
			}else{
				$sql = "UPDATE ebay_packing_status SET realnum=realnum+{$scan_num} WHERE ebay_id={$ebay_id} AND ebaydetail_id={$checksql[0]['ebaydetail_id']}";
				$dbcon->query($sql);
				echo "新增配货 $ebay_id 明细 ID $ebaydetail_id 料号 $sku 数量 $scan_num 需求量 $orderdetail[$ebaydetail_id]['ebay_amount']"; echo "<br>";
			}
		}*/else{
			$xu_nums = $scan_num;
			foreach($checksql as $value){
				if($xu_nums <= 0) break;
				$ebaydetail_id = $value['ebaydetail_id'];
				$ebay_ordersn = $value['ebay_ordersn'];
				$sku = $value['sku'];
				$realnum = $value['realnum'];
				$totalnum = $value['totalnum'];
				if(empty($totalnum)){//没有配货记录
					$need_amount = $orderdetail[$ebaydetail_id]['ebay_amount'];
					if($need_amount > $xu_nums){
						$realnum = $xu_nums;
					}else{
						$realnum = $need_amount;
					}
					$xu_nums -= $need_amount;
					$sql = "INSERT INTO ebay_packing_status SET ebay_id={$ebay_id},ebaydetail_id={$ebaydetail_id},order_sn='{$ebay_ordersn}',sku='{$sku}',realnum=$realnum,totalnum={$orderdetail[$ebaydetail_id]['ebay_amount']},packagingstaff='',scanuser='{$truename}',status=1,time=".time();
					//$dbcon->query($sql);
					echo "新增配货 $ebay_id 明细 ID $ebaydetail_id 料号 $sku 数量 $realnum 需求量 {$orderdetail[$ebaydetail_id]['ebay_amount']}"; echo "<br>";
				}else{//有配货记录
					$need_amount = $orderdetail[$ebaydetail_id]['ebay_amount'] - $realnum;
					if($need_amount > $xu_nums){
						$realnum = $xu_nums;
					}else{
						$realnum = $need_amount;
					}
					$xu_nums -= $need_amount;
					$sql = "UPDATE ebay_packing_status SET realnum=realnum+$realnum WHERE ebay_id={$ebay_id} AND ebaydetail_id={$did}";
					//$dbcon->query($sql);
					echo "新增配货 $ebay_id 明细 ID $ebaydetail_id 料号 $sku 数量 $realnum 需求量 {$orderdetail[$ebaydetail_id]['ebay_amount']}"; echo "<br>";
				}
			}
		}
	}else if($ordercheck['ebay_status'] == '624'){//正常快递
		$xu_nums = $scan_num;
		foreach($orderdetail as $value){
			if($xu_nums <= 0) break;
			$ebaydetail_id = $value['ebay_id'];
			$ebay_ordersn = $value['ebay_ordersn'];
			$sku = $value['sku'];
			$schecksql = "SELECT realnum,totalnum FROM ebay_packing_status WHERE ebay_id={$ebay_id} AND ebaydetail_id={$ebaydetail_id} AND sku = '{$sku}' ";
			$schecksql = $dbcon->execute($schecksql);
			$schecksql = $dbcon->fetch_one($schecksql);
			if(empty($schecksql)){//没有配货记录
				$need_amount = $orderdetail[$ebaydetail_id]['ebay_amount'];
				if($need_amount > $xu_nums){
					$realnum = $xu_nums;
				}else{
					$realnum = $need_amount;
				}
				$xu_nums -= $need_amount;
				$sql = "INSERT INTO ebay_packing_status SET ebay_id={$ebay_id},ebaydetail_id={$ebaydetail_id},order_sn='{$ebay_ordersn}',sku='{$sku}',realnum=$realnum,totalnum={$orderdetail[$ebaydetail_id]['ebay_amount']},packagingstaff='',scanuser='{$truename}',status=1,time=".time();
				//$dbcon->query($sql);
				echo "新增配货 $ebay_id 明细 ID $ebaydetail_id 料号 $sku 数量 $realnum 需求量 {$orderdetail[$ebaydetail_id]['ebay_amount']}"; echo "<br>";
			}else{//有配货记录
				$realnum = $schecksql['realnum'];
				$totalnum = $schecksql['totalnum'];
				$need_amount = $orderdetail[$ebaydetail_id]['ebay_amount'] - $realnum;
				if($need_amount > $xu_nums){
					$realnum = $xu_nums;
				}else{
					$realnum = $need_amount;
				}
				$xu_nums -= $need_amount;
				$sql = "UPDATE ebay_packing_status SET realnum=realnum+$realnum WHERE ebay_id={$ebay_id} AND ebaydetail_id={$did}";
				//$dbcon->query($sql);
				echo "新增配货 $ebay_id 明细 ID $ebaydetail_id 料号 $sku 数量 $realnum 需求量 {$orderdetail[$ebaydetail_id]['ebay_amount']}"; echo "<br>";
			}
		}
	}
}

/*function func_transport_adjustment(){

}*/