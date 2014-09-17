<?php
/**********************************************************************
 *      速卖通标记发货
 *      by  zhongyantai 2013-04-22
 */
if($argc!=2){
        exit("Usage: /usr/bin/php       $argv[0] eBayAccount \n");
}
if(!defined('WEB_PATH')){
        define("WEB_PATH","/data/web/order.valsun.cn/");
}
//define('SCRIPTS_PATH_CRONTAB', '/data/web/erpNew/order.valsun.cn/crontab/');
require_once WEB_PATH."crontab/scripts.comm.php";
require_once WEB_PATH_CONF_SCRIPTS."script.ebay.config.php";
require_once WEB_PATH_LIB_SDK_ALIEXPRESS."Aliexpress.class.php";
//速卖通账号与ERP后台账号的映射表
require_once WEB_PATH_CONF_SCRIPTS_KEYS_ALIEXPRESS."config/common.php";
require_once WEB_PATH_LIB_SCRIPTS_ALIEXPRESS."aliexpress_order_func.php";
require_once WEB_PATH."model/common.model.php";

define("ALI_LOG_DIR","/home/ebay_order_cronjob_logs/aliexpress/shipment/");

$omAvailableAct = new OmAvailableAct();
$where = 'WHERE is_delete=0 ';
$where .= 'AND platformId in(2,3,4,6,9) ';
$GLOBAL_EBAY_ACCOUNT = $omAvailableAct->act_getTNameList2arrById('om_account', 'id', 'account', $where);

$FLIP_GLOBAL_EBAY_ACCOUNT = array_flip($GLOBAL_EBAY_ACCOUNT);
//var_dump($FLIP_GLOBAL_EBAY_ACCOUNT); echo "\n"; exit;
//ERP账号与速卖通账号的映射表
$erp_user_mapping       =       array(
        "3ACYBER"               =>      "cn1000268236",
        "szsunweb"              =>      "cn1000421358",
        "E-Global"              =>      "cn1000616054",
        "beauty365"             =>      "cn1000960806",
        "caracc"                =>      "cn1000983412",
        "Bagfashion"			=>      "cn1000983826",
        "prettyhair"			=>      "cn1000999030",
        "LovelyBaby"			=>      "cn1001428059",
        "Finejo"                =>      "cn1001392417",
        "5season"               =>      "cn1001424576",
        "fashiondeal"			=>      "cn1001656836",
        "Sunshine"              =>      "cn1001711574",
        "fashionqueen"			=>      "cn1001718610",
        "shiningstar"			=>      "cn1001739224",
        "babyhouse"             =>      "cn1500053764",
        "fashionzone"			=>      "cn1500152370",
        "shoesacc"              =>      "cn1500226033",
        "superdeal"             =>      "cn1500293467",
        "istore"                =>      "cn1500439756",
        "ladyzone"              =>      "cn1500514645",
        "beautywomen"			=>      "cn1500688776",
        "womensworld"			=>      "cn1501288533",
        "myzone"                =>      "cn1501287427",
        "homestyle"             =>      "cn1501540493", //2013-08-01
        "championacc"			=>      "cn1501578304", //2013-08-01
        "digitallife"			=>      "cn1501595926", //2013-08-01
        "Etime"                 =>      "cn1501638006", //2013-08-20
        "citymiss"              =>      "cn1510304665",
        "zeagoo360"             =>      "cn1500440054",

        //taotaoAccount
        "taotaocart"			=>      "cn1501642501",
        "arttao"                =>      "cn1501654678",
        "taochains"             =>      "cn1501654797",
        "etaosky"               =>      "cn1501655651",
        "tmallbasket"			=>      "cn1501656206",
        "mucheer"               =>      "cn1501656494",
        "lantao"                =>      "cn1501657160",
        "direttao"              =>      "cn1501657334",
        "hitao"                 =>      "cn1501657572",
        "taolink"               =>      "cn1501686293",

        //----------

        //surfaceAccount
        "acitylife"             =>		"cn1510515579",
        "etrademart"			=>		"cn1510509503",
        "centermall"			=>		"cn1510509429",
        "viphouse"              =>		"cn1510514024",

        //---------

);

$surface_accounts = array('acitylife','etrademart','centermall','viphouse');

$aliexpress_user	= trim($argv[1]);
$account			= $erp_user_mapping[$aliexpress_user];
$accountId			= $FLIP_GLOBAL_EBAY_ACCOUNT[$aliexpress_user];
$delivery_arr		= CommonModel::getCarrierListById();
//var_dump($GLOBAL_EBAY_ACCOUNT); echo "\n";exit;
if(!in_array($aliexpress_user,$GLOBAL_EBAY_ACCOUNT)){
        exit("$aliexpress_user is not support now !\n");
}

echo "-------------------start ".date("Y-m-d H:i:s")."------------------------\n";

if($aliexpress_user == "womensworld") {
	$start = strtotime("-24 hour");
}else{
	$start = strtotime("-48 hour");
}
$order_sql	= 	"select id,recordNumber,transportId,combinePackage,orderStatus 
				from om_shipped_order as a 
				left join om_shipped_order_warehouse as b 
				on a.id = b.omOrderId
				where a.accountId = '$accountId' 
				and a.orderStatus ='".C("STATESHIPPED")."'
				and a.orderType = '".C("STATEHASSHIPPED_CONV")."' 
				and b.weighTime > $start and (a.ShippedTime ='' or a.ShippedTime is null) 
				ORDER BY b.weighTime ";
// and ebay_status ='2' 
//$testsql = " ORDER BY scantime"; $sql .= $testsql;  // for test 

/*$order_sql	= "	select 	a.omOrderId
					from 	om_unshipped_order_warehouse as a
					where	(a.weighTime BETWEEN $start AND $end)
					and 	a.storeId = 1 ";*/
//echo $order_sql;exit;
$order_db	= $dbConn->query($order_sql);
$alldata	= $dbConn->fetch_array_all($order_db);
//var_dump($alldata); echo "\n"; exit;
$sum		= sizeof($alldata);
if($sum<=0){
        exit("No order to handel\n");
}
$transportData = CommonModel::getCarrierListById();

if($sum > 0){
	foreach($alldata as $val){
		$omOrderId		= $val['id'];
		$transportId	= $val['transportId'];
		$carrier		= $delivery_arr[$transportId];
		echo "开始上传订单【{$omOrderId}】 ----------运输方式={$carrier}------\n\n";

		$recordnumber	= $val['recordNumber'];
		//$ebay_carrier	= $val['transportId'];
		/*$where = " where id = {$omOrderId} and storeId = 1 and is_delete = 0 ";
		$orderList = OrderindexModel::showOrderList($tableName, $where);
		$orderTracknumber = $orderList[$omOrderId]['orderTracknumber'];*/
		$orderList			= is_array($omOrderId) ? implode(',',$omOrderId) : $omOrderId;
		$orderTracknumber	= getTracknumber($orderList); //获取订单的跟踪号
		if(empty($orderTracknumber)){
			continue;//无跟踪号不处理
		}

		$trackno		= $orderTracknumber[0]['tracknumber'];	//$val['ebay_tracknumber'];
		$update_ebay_id = $val['id'];	//$val['ebay_id'];
		$mctime			=       time();

		/*$sql    =       "select ebay_id,recordnumber,ebay_carrier,combine_package ,ebay_status, ShippedTime, ebay_combine
								from ebay_order where recordnumber = '$recordnumber' and ebay_status in (2,594,614,636,671,689,672,673)";*/
		$sql = "SELECT id,recordNumber,transportId,combinePackage ,orderStatus, ShippedTime, combineOrder 
				FROM om_shipped_order 
				WHERE recordNumber='$recordnumber' 
				AND orderStatus ='".C("STATESHIPPED")."'
				AND orderType = '".C("STATEHASSHIPPED_CONV")."' ";

		$query	= $dbConn->query($sql);
		$data	= $dbConn->fetch_array_all($query);
		$total  = sizeof($data);
		//echo $total."\n\n";exit;
		$no_set_shipping_flag = 0;
		if (in_array($aliexpress_user, $surface_accounts)) {	//平邮账号
				$surface_carrier	= get_surface_trackno($data[0]['id'], $total == 1 ? 1 : 2);
				//print_r($surface_carrier);
				//$ebay_carrier		= $surface_carrier['ebay_carrier'];
				$transportId		= isset($surface_carrier['transportId']) ? $surface_carrier['transportId'] : $surface_carrier['ebay_carrier'];
				$trackno			= isset($surface_carrier['tracknumber']) ? $surface_carrier['tracknumber'] : $surface_carrier['ebay_tracknumber'];
		}
		//echo $trackno."\r\n".$transportId;exit;
		switch ($transportId) {		//$ebay_carrier
			case "4":	//香港小包挂号
				$serviceName            = 'HKPAM';      //Hongkong Post Air Mail
				break;
			case "46":	//UPS
				$serviceName            = 'UPS';
				break;
			case "8":	//DHL
				$serviceName            = 'DHL';
				break;
			case "9":	//Fedex
				$serviceName            = 'FEDEX_IE';
				break;
			case "70":
				$serviceName            = 'TNT';
				break;
			case "5":
				$serviceName            = 'EMS';
				break;
			case "2":	//中国邮政挂号
				$serviceName            = 'CPAM';       //China Post Air Mail
				break;
			case "6":	//EUB
				$serviceName            = 'EMS_ZX_ZX_US';       //EUB
				break;

			case "52":	//新加坡小包挂号
				$serviceName            = 'SGP';
				break;
			case "61":		//WEDO
				$serviceName            = 'Other';
				break;
			default:
				$serviceName			= $delivery_arr[$transportId];	//$ebay_carrier;
				$no_set_shipping_flag	= 1;
				break;
		}

		$Website = $serviceName=='Other' ? "http://www.wedoexpress.com/index.php?mod=trackInquiry&act=index&carrier=wedo&tracknum={$trackno}" : '';
		//echo $serviceName."\n".$Website."\n".$trackno;exit;
		if($total == 1){
			//B2B 没有合并包裹， 只有合并订单
			if(!empty($data[0]['combineOrder'])){	//ebay_combine
				$other_order = explode("##",$data[0]['combineOrder']);		//ebay_combine
				foreach($other_order as $v){
					if(empty($v)) continue;
					//取被合并订单的信息。 同时也标记发货
					/*$sql    =       "select ebay_id,recordnumber,ebay_carrier,combine_package
											from ebay_order where ebay_id = '".$v."'";*/
					$sql		= "SELECT id,recordNumber FROM `om_shipped_order` where id = '".$v."'";
					$query		= $dbConn->query($sql);
					$ret		= $dbConn->fetch_one($query);

					$dat		= sellerShipment($account,$ret[0]['recordNumber'],$serviceName,$trackno,"all", $no_set_shipping_flag, $Website);	//上传跟踪号
					//print_r($ret);
					if($dat) {
						update_order_shippedmarked_time($ret[0]['id'], $mctime,$dat);
					}
				}
				
			}
			$ret = sellerShipment($account,$val['recordNumber'],$serviceName,$trackno,"all", $no_set_shipping_flag, $Website);	//上传跟踪号
			//print_r($ret);
			if($ret){
				update_order_shippedmarked_time($update_ebay_id, $mctime, $ret);
			}
		}
		if($total > 1) { //print_r($data); exit("total 大于1 \n");
			$send_type		= "all";
			$total_empty	= 0;
			foreach ($data as $v){
				if(empty($v['ShippedTime'])){   //存在未发货的
					$total_empty++;
				}
			}
			if($total_empty > 1){
				$send_type = "part";
			}
			//echo $account."  ||  ".$val['recordNumber']."  ||  ".$serviceName."  ||  ".$trackno."  ||  ".$send_type."  ||  ".$no_set_shipping_flag."  ||  ". $Website;
			$ret = sellerShipment($account,$val['recordNumber'],$serviceName,$trackno,$send_type,$no_set_shipping_flag, $Website);
			//print_r($ret);
			if($ret) {
				update_order_shippedmarked_time($update_ebay_id, $mctime, $ret);
			}
		}
		exit('fffff');
	}
}

echo "-------------------end ".date("Y-m-d H:i:s")."------------------------\n";
exit;

function get_surface_trackno($ebayid, $packingstatus){
	global $dbConn;
	global $transportData;	//运输方式
	//ebay_account 为新系统的 accountId; 
	//ebay_userid  为新系统的 platformUsername
		//$sql = "SELECT ebay_countryname,ebay_state,ebay_city,ebay_postcode,ebay_carrier,ebay_account,ebay_userid,ebay_usermail FROM ebay_order WHERE ebay_id='{$ebayid}'";

		//$sql = "SELECT ebay_countryname,ebay_state,ebay_city,ebay_postcode,ebay_carrier,ebay_account,ebay_userid,ebay_usermail FROM ebay_order WHERE ebay_id='{$ebayid}'";	//查询订单的地址信息
	$sql = 'SELECT sou.`countryName`,sou.`state`,sou.`city`,sou.`zipCode`,so.`transportId`,so.`accountId`,sou.`platformUsername`,sou.`email` 
			FROM `om_shipped_order` AS so
			LEFT JOIN `om_account` AS ea ON so.`accountId` = ea.`id`
			LEFT JOIN `om_shipped_order_userInfo` AS sou ON so.`id` = sou.`omOrderId`
			WHERE so.id = \''.$ebayid.'\'';		//获取订单的运输方式等信息 
	$query		= $dbConn->query($sql);
	$orderinfo	= $dbConn->fetch_one($query);

	$uesrid			= $orderinfo['platformUsername'];
	$uesremail		= $orderinfo['email'];
	$ebay_carrier	= str_replace('平邮', '挂号', $transportData[$orderinfo['transportId']]);

	$starttime	= time()-3*24*3600;
	$endtime	= time();

	/*$sql = "SELECT ebay_account,ebay_tracknumber,ebay_carrier,ebay_userid,ebay_usermail FROM ebay_order WHERE ebay_combine!='1' AND scantime>'{$starttime}' AND scantime<'{$endtime}' AND ebay_carrier='$ebay_carrier' AND ebay_postcode='{$orderinfo['ebay_postcode']}' ORDER BY scantime DESC LIMIT 10";*/
	$sql = 'SELECT so.`id`,so.`recordNumber`,so.`accountId`,so.`ShippedTime`,so.`transportId`,so.`marketTime`,so.`orderStatus`,ea.`account`,
				sou.`countrySn`,sou.`countryName`,sou.`city`,sou.`email`,sou.`platformUsername`,sou.`zipCode`,sow.`weighTime`,sow.`weighStaffId`,ot.`tracknumber` 
			FROM `om_shipped_order` AS so
			LEFT JOIN `om_account` AS ea ON so.`accountId` = ea.`id`
			LEFT JOIN `om_shipped_order_userInfo` AS sou ON so.`id` = sou.`omOrderId` 
			LEFT JOIN `om_shipped_order_warehouse` AS sow ON so.`id` = sow.`omOrderId` 
			LEFT JOIN `om_order_tracknumber` AS ot ON so.`id` = ot.`omOrderId` 
			WHERE so.combineOrder != 1 AND sow.`weighTime` >= '.$starttime.' AND sow.`weighTime` <= '.$endtime.'
			AND so.`transportId` = \''.$orderinfo['transportId'].'\'
			AND sou.`zipCode` = \''.$orderinfo['zipCode'].'\' ORDER BY sow.`weighTime` DESC LIMIT 10';		//获取订单的运输方式等信息
	//echo $sql."\n\n";
	$query	= $dbConn->query($sql);
	$orders	= $dbConn->fetch_array_all($query);
	//print_r($orders);exit;
	if (empty($orders)){
		$_orderinfo = $orderinfo;
		unset($_orderinfo['zipCode'], $_orderinfo['transportId'], $_orderinfo['platformUsername'], $_orderinfo['email']);	//$_orderinfo['accountId']
		//ebay_countryname,ebay_state,ebay_city
		$wheres = array();
		while (!empty($_orderinfo)){
			$wheres[] = $_orderinfo;
			array_pop($_orderinfo);
		}
		//print_r($wheres);exit;
		foreach ($wheres AS $where){
			$where = array2strarray($where);
			$sql_where = array();
			foreach ($where AS $_k=>$_v){
				if (empty($_k)){
						continue;
				}
				$_v = trim($_v);
				$sql_where[] = "{$_k}={$_v}";
			}
			//$sql = "SELECT ebay_account,ebay_tracknumber,ebay_carrier,ebay_userid,ebay_usermail FROM ebay_order WHERE ebay_combine!='1' AND scantime>'{$starttime}' AND scantime<'{$endtime}' AND ebay_carrier='$ebay_carrier' AND ".implode(' AND ', $sql_where)." ORDER BY scantime DESC LIMIT 10";
			$sql = 'SELECT so.`id`,so.`recordNumber`,so.`accountId`,so.`ShippedTime`,so.`transportId`,so.`marketTime`,so.`orderStatus`,ea.`account`,
						sou.`countrySn`,sou.`countryName`,sou.`city`,sou.`email`,sou.`platformUsername`,sou.`zipCode`,sow.`weighTime`,sow.`weighStaffId`,ot.`tracknumber`
					FROM `om_shipped_order` AS so 
					LEFT JOIN `om_account` AS ea ON so.`accountId` = ea.`id` 
					LEFT JOIN `om_shipped_order_userInfo` AS sou ON so.`id` = sou.`omOrderId` 
					LEFT JOIN `om_shipped_order_warehouse` AS sow ON so.`id` = sow.`omOrderId` 
					LEFT JOIN `om_order_tracknumber` AS ot ON so.`id` = ot.`omOrderId` 
					WHERE so.combineOrder != 1 AND sow.`weighTime` >= '.$starttime.' AND sow.`weighTime` <= '.$endtime.'  
					AND so.`transportId` = \''.$orderinfo['transportId'].'\' AND '.implode(' AND ',$sql_where).' ORDER BY sow.`weighTime` DESC LIMIT 10	';
			//echo "$sql\n\n";exit;
			$sql	= $dbConn->query($sql);
			$orders = $dbConn->fetch_array_all($sql);

			if (!empty($orders)) break;
		}

	}
	foreach ($orders AS $order){
		if ($uesrid!=$order['id']&&$uesremail!=$order['email']&&!check_is_useful($order['tracknumber'], $orderinfo['accountId'], $orderinfo['platformUsername'], $orderinfo['email'])){
			$surfacedata = array();
			$surfacedata['order_id']		= $ebayid;
			$surfacedata['account']			= $order['accountId'];			//被使用的账号
			$surfacedata['use_account']		= $orderinfo['accountId'];		//当前上传跟踪号的账号
			$surfacedata['shippingstatus']	= 1;
			$surfacedata['packingstatus']	= $packingstatus;
			$surfacedata['trackno']			= $order['tracknumber'];
			$surfacedata['carrier']			= $transportData[$order['transportId']];	//$order['ebay_carrier'];
			$surfacedata['saleuser']		= $orderinfo['platformUsername'];
			$surfacedata['saleemail']		= $orderinfo['email'];

			backup_surfaceid($surfacedata);
			unset($order['ebay_userid'], $order['email']);
			return $order;
		}
	}

	$trackno = 'WD'.str_pad($ebayid, 9, "0", STR_PAD_LEFT)."CN";
	if (!check_is_useful($trackno, $orderinfo['accountId'], $orderinfo['platformUsername'], $orderinfo['email'])){
		$surfacedata = array();
		$surfacedata['order_id']		= $ebayid;
		$surfacedata['account']			= 'wedo';	//被使用的账号
		$surfacedata['use_account']		= $orderinfo['accountId'];		//当前上传跟踪号的账号
		$surfacedata['shippingstatus']	= 1;
		$surfacedata['packingstatus']	= $packingstatus;
		$surfacedata['trackno']			= $trackno;
		$surfacedata['carrier']			= 'wedo';
		$surfacedata['saleuser']		= $orderinfo['platformUsername'];
		$surfacedata['saleemail']		= $orderinfo['email'];

		backup_surfaceid($surfacedata);
	}//print_r(array('ebay_account'=>'wedo', 'ebay_tracknumber'=>$trackno, 'ebay_carrier'=>'61'));exit;
	return array('ebay_account'=>'wedo', 'ebay_tracknumber'=>$trackno, 'ebay_carrier'=>'61');		//'ebay_carrier'=>'wedo'
}

function check_is_useful($tracknumber, $account, $saleuser, $saleemail){
	global $dbConn;
	$sql = "SELECT COUNT(*) AS num FROM om_aliexpress_surface WHERE trackno='{$tracknumber}' AND (account='{$account}' OR saleuser='{$saleuser}' OR saleemail='{$saleemail}')";
	$sql = $dbConn->query($sql);
	$check_result = $dbConn->fetch_one($sql);

	return $check_result['num']>0 ? true : false;
}

function backup_surfaceid($data){
	global $dbConn;
	$sql = "INSERT INTO om_aliexpress_surface SET ".array2sql($data).",shipingtime=".time();
	return $dbConn->query($sql);
}


function update_order_shippedmarked_time($ebay_id,$mctime,$requestData){
	global $dbConn;

	echo "标记订单({$ebay_id})上传跟踪号-------上传时间".date("Y-m-d H:i:s",$mctime)."\n\n";
	/*$sql    =       "update ebay_order set ebay_markettime='$mctime',ShippedTime='$mctime'
							where ebay_id='$ebay_id'";*/

	$requestStatus	= isset($requestData['error_code']) ? 2 : 1;
	$requestMsg		= isset($requestData['error_message'])? $requestData['error_message'] : 'true';
	if(!isset($requestData['error_code'])) {	//更新标记发货时间
		$sql = "update om_shipped_order set marketTime='$mctime', ShippedTime='$mctime' where id='$ebay_id'";
		//print_r($sql);
		$ret = $dbConn->query($sql);
	}

	$sql	= 'UPDATE om_aliexpress_surface SET mark_ret=\''.$requestStatus.'\', mark_msg=\''.$requestMsg.'\' WHERE order_id=\''.$ebay_id.'\'';
	$ret1	= $dbConn->query($sql);
	return $ret;
}


function sellerShipment($account,$recordnumber,$serviceName,$tracknumber,$type,$no_set_shipping_flag,$Website=""){

	$logfile	=	ALI_LOG_DIR."order_shipment_".$account."_".date("Y-m-d").".log";
	$configFile	=	WEB_PATH_CONF_SCRIPTS_KEYS_ALIEXPRESS."config/config_{$account}.php";
	if (file_exists($configFile)){
			include $configFile;
	}else{
			return false;
	}

	$aliexpress = new Aliexpress();
	$aliexpress->setConfig($appKey,$appSecret,$refresh_token);
	$aliexpress->doInit();
	$log_data			= array();
	$log_data['time']		= date("Y-m-d H:i:s");
	$log_data['recordnumber']	= $recordnumber;
	$log_data['serviceName']	= $serviceName;
	$log_data['tracknumber']	= $tracknumber;
	$log_data['type']		= $type;
	if(!$no_set_shipping_flag){
		$data = $aliexpress->sellerShipment($serviceName, $tracknumber, $type, $recordnumber, '', $Website);
		//print_r($data);
		echo "交易号=$recordnumber-------运输方式=$serviceName---------跟踪号=$tracknumber--------类型=$type----------URL=$Website-----上传结果=".(isset($data['error_code']) && !empty($data['error_code']) ? 'failure' : 'success')."\n\n";
	}else {
		echo "交易号=$recordnumber-------运输方式=$serviceName---------跟踪号=$tracknumber--------类型=$type----------不支持该运输方式上传\n\n";
		//return false;
		return array('error_code' => 'SYS_ERROR','error_message' => "不支持该运输方式上传!,交易号: $recordnumber,运输方式:$serviceName, 跟踪号:$tracknumber");
	}

	if(isset($data['error_code']) && !empty($data['error_code'])) {
		//echo $data['error_message']."\n\n";
		$log_data['msg']	= $data['error_message'];
		$log			= json_encode($log_data)."\r\n";
		$ret = @file_put_contents($logfile, $log, FILE_APPEND);
		print_r($ret);echo "ssssss\n";
		//return preg_match("/Operation\sfailed\sin\sAuthorization/i", $data['error_message'])>0 ? true : false;
		return $data;
	}else{exit('gggg');
		$json_data = json_encode($data);
		if(empty($data) || empty($json_data)){
			$log_data['msg']	= "op fail";
			$log			= json_encode($log_data)."\r\n";
			@file_put_contents($logfile, $log, FILE_APPEND);
			//return false;
			return array('error_code' => 'SYS_ERROR','error_message' => '速卖通接口无数据返回!');
		}else{
			$log_data['msg']	= "success";
			$log			= json_encode($log_data)."_______".$json_data."\r\n";
			@file_put_contents($logfile, $log, FILE_APPEND);
			return $data;
		}
	}
}

function getTracknumber($omOrderIds) {
	$url = 'http://api.wh.valsun.cn/json.php?mod=orderTracknumber&act=getOrderTracknumber&jsonp=1&orderIds='.htmlentities($omOrderIds,ENT_QUOTES);
	$data = file_get_contents($url);
	if(!empty($data)) {
		$data = json_decode($data,true);
		$data = $data['data'];
		return $data;
	} else {
		return false;
	}
}