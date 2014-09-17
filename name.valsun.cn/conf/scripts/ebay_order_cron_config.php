<?php
	include WEB_PATH."framework.php";
	Core::getInstance();
	
	//应许抓取的料号
	$_allow_spide_itemid = array('140964081873','130881450826','130897197539','130764833258','130821799772','130818857110','140964087285','130897195973','130820022776','130818876597','251251828933','350762254788','350626780178','350643165801','251171970582','251243381001','251251856230','350674579598');
	
	require_once SCRIPT_ROOT."conf/xmlhandle.php";
	//include_once "cls_page.php";
	include_once WEB_PATH_LIB."ebay_order_cron_func.php";
	//include_once "shipping_calc_fun.php";
	
	/*$sql		= "select category,skulist,original_transport,current_transport,country from ebay_adjust_transport "; //特殊料号数据库
	$result		= $dbcon->execute($sql);
	$ebay_adjust_transport	= $dbcon->getResultArray($result);
	$dbcon->free_result($result);
	$__liquid_items_postbyhkpost = array_filter(explode(",", $ebay_adjust_transport[0]['skulist']));//液体产品
	$__liquid_items_postbyfedex = array_filter(explode(",", $ebay_adjust_transport[1]['skulist']));//贵重物品走联邦
	$__liquid_items_cptohkpost = array_filter(explode(",", $ebay_adjust_transport[2]['skulist']));//指甲油转香港小包
	$__liquid_items_elecsku = array_filter(explode(",", $ebay_adjust_transport[3]['skulist']));//电子类产品走香港小包
	$__elecsku_countrycn_array = array_filter(explode(",", $ebay_adjust_transport[3]['country']));//电子类产品指定国家
	$__liquid_items_fenmocsku = array_filter(explode(",", $ebay_adjust_transport[4]['skulist'])); //粉末状SKU
	$__liquid_items_BuiltinBattery  = array_filter(explode(",", $ebay_adjust_transport[5]['skulist'])); //内置电池类产品
	$__liquid_items_SuperSpecific = array('6471','14995'); //超规格的产品，长度大于60cm, 三边大于 90cm
	$__liquid_items_Paste = array_filter(explode(",", $ebay_adjust_transport[6]['skulist']));//膏状SKU*/
	
	/*//取统一包装材料重量数据
	$tt		="	select weight,model from ebay_packingmaterial 
				where  ebay_user ='$user' ";
	$tt		= $dbcon->execute($tt);
	$tt		= $dbcon->getResultArray($tt);
	
	$global_packingmaterial_weight=array();
	foreach($tt as	$t){
		$global_packingmaterial_weight[$t['model']]	= $t['weight'];
	}
	unset($tt,$t);*/
	$MaterInfo = CommonModel::getMaterInfo();
	
	/*//取统一国家中文名对应英文名
	$ec = "select * from ebay_countrys where ebay_user='$user' ";
	$result = $dbcon->execute($ec);
	$ebay_country_lists = $dbcon->getResultArray($result);
	$global_countrycn_coutryen = array();
	foreach($ebay_country_lists AS $ebay_country_list){
		$global_countrycn_coutryen[trim($ebay_country_list['countryen'])] = trim($ebay_country_list['countrycn']);
	}*/
	
	//取各个平台的账号名称
	$sql = "select ebay_account,ebay_platform from ebay_account where ebay_user='$user' order by ebay_platform ASC,ebay_account desc ";
	$result = $dbConn->execute($sql);
	$system_account_lists = $dbConn->getResultArray($result);
	$SYSTEM_ACCOUNTS = array();
	foreach($system_account_lists AS $system_account_list){
		$SYSTEM_ACCOUNTS[$system_account_list['ebay_platform']][] = $system_account_list['ebay_account'];
	}
?>