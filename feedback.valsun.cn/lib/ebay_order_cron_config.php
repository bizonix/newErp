<?php
	define('DB_HOST','localhost');
	define('DB_USER','cerp');
	define('DB_PWD','123456');
	define('DB_NAME','cerp');
	
	/*define('DB_HOST','192.168.10.100');
	define('DB_USER','john');
	define('DB_PWD','123456');
	define('DB_NAME','test');*/
	
	///////////////some global configuration//////////////////
	$user='vipchen';
	/*##############液体的产品(订单只有单种物品时)直接设为香港小包#############START
	//added by john 2012-05-16   
	//$__liquid_items_postbyhkpost=array(488,950,1495,1515,1569,2090,2091,2092,2095,2096,2083,2420,2526,2693,306,344,829,830,831,1075,1463,1552,1553,2402,1559,1567,1493,1579,2052,1500,2434,2055);
	$__liquid_items_postbyhkpost=array('518','1013','1107','1293','1312','1313','1317','1750','1684','1692','1693','1694','1695','1696','1705','1706','986','987','1940','2021','2022','2556','2579','5756','2394','2812','4287','4766','4956','4956','4993','4994','4995','5774','5776','5821','4953','4878','396','2734','2735','2536','2524','2291','1974','1881','1873','1638','4996','4997','4998','4999','5000','5771','5772','5773','5775','5777','4952','4954','4370','2730','2535','2523','2506','1992','1897','1884','1805','1635','1406','1407','1233','1241','1114','880','819','778','700','353','313','303','215','298','307','308','312','329','333','334','470','471','485','847','848','852','867','868','869','1053','1167','1378','4789','1102','1284','1290','1294','1299','1304','3178_W','3178_B','3178_BL','3233_B','3233_W','3233_PU','3233_P','3233_BL','3285_B','3285_W','3285_P','3285_LBL','3285_C','3297_B','3297_W','3297_LBL','3297_P','3297_RR','3362_W','3362_B','3362_R','3363_B','3363_W','3363_P','3363_LBL','3695_B','3695_PU','3695_DBL','3695_RR','3695_O','3176_B','3176_W','3176_Y','3176_G','3176_LBL','3176_PU','3176_RR','3081','3888','1493','1497','5845','5844','4771','4772','5219','4058','4015','4012','3069','2526','2692','2434','2435','2438','2420','2085','2090','2091','2092','2095','2096','2083','2052','1586','1579','1566','1567','1493','1497','1556','2056','2435','1500','1550','2053','3905','4014','4016','4909','4910','5219','3248','969','1584','1585','1598','1599','2070','2438','3904','4010','4381','1598','2066','2067','2068','837','838','488','4918','5722','839','2094','4388','4777','4778','7052_W','7052_B','4359','4432','4573','4575','4627','4962','4963','4238','4397','4578','4621','4917','5721','1568','2069','1498','2693','4059','4516','5846','4430','4431','4476','4907','4913','5779','5573','924','1494','950','1495','2422','5364','2699','1236','344','2405','5721','4476','4280','2402','1131','186','1201','3176_B','3176_W','3176_Y','3176_G','3176_LBL','3176_PU','3176_RR');
	##############液体的产品(订单只有单种物品时)直接设为香港小包#############END
	##############高级货物走挂号#############START
	//added by linzhengxiang 2012-05-12
	$__liquid_items_postbyfedex=array('4631','4633','4653','4654','4657','4658','4659','4662','4665','4666','4671','4672','4673','4674','2167','2166','2531');
	##############高级货物走挂号#############END
	##############指甲油SKU(中国邮政转香港小包)#############START
	//add by Herman.Xi 2012-10-22
	$__liquid_items_cptohkpost=array('5573','7686','950','924','2085','2053','2052','1579','1566','1550','1500','1495','2092','2434','2693','3248','3905','4014','4016','4516','4909','4910','5219','5850','5851','6618','7924','5573_1','5573_2','5573_3','5573_4','5573_5','5573_6','5573_7','5573_8','5573_9','5573_10','5573_11','5573_12','7686_19','7686_20','7686_18','7686_17','7686_16','7686_15','7686_14','7686_13','7686_12','7686_11','7686_10','7686_9','7686_7','7686_8','7686_6','7686_5','7686_4','7686_2','7686_3','7686_1','3248_1','3248_2','3248_3','3248_4','3248_5','5219_001','5219_002','5219_004','5219_007','5219_009','5219_161','5219_012','5219_030','5219_031','5219_051','5219_054','5219_085','5219_098','5219_100','5219_157','5219_102','5219_120','5219_189','5219_096','5219_175','7924_3','7924_2','7924_1','2095','2055','2090','2091','2096','2420','2526','4012','4058','5844','5845','5846','5849', '2107','5760','5761','4376','TK0113');
	##############指甲油SKU(中国邮政转香港小包)#############END
	##############电子类产品SKU（指定国家的订单直接走香港小包)#############START
	//add by Herman.Xi 2012-10-26
	$__elecsku_countrycn_array = array('阿尔巴尼亚','奥地利','比利时','保加利亚','白俄罗斯','捷克','爱沙尼亚','希腊','克罗地亚','匈牙利','爱尔兰','冰岛','立陶宛','卢森堡','拉脱维亚','摩尔多瓦','马耳他','葡萄牙','罗马尼亚','塞尔维亚','瑞典','斯洛文尼亚','斯洛伐克');
	$__liquid_items_elecsku=array('7769_1','7769_2','7769_3','166','869','312','24','298','329','1039','1378','4243','4276','947','361','269','819','1114','1406','1897','1992','6342','1407','1992','4124','4766','4878','5884','5885','5886','734','1602','1603','1631','1632','1634','2351','3620_B','50','195','207','TK0014','TK0015','TK0016','TK0017','TK0018','TK0023','TK0025','TK0026','TK0049','TK0050','TK0051','TK0052','TK0053','TK0054','TK0055','TK0080','TK0087','TK0091','TK0092','TK0131','TK0132','TK0133','TK0134','TK0135','TK0136','TK0137','TK0138','TK0139','TK0140','TK0151','TK0152','TK0160','TK0163','TK0164','TK0165','TK0183','TK0187','TK0189','TK0193','TK0201','TK0213','TK0215','TK0217','TK0224','TK0230','TK0231','TK0233','TK0239','TK0244','TK0245','TK0246','TK0269');
	##############电子类产品SKU(指定国家的订单直接走香港小包)#############END*/
	
	///////////////some global configuration//////////////////
	include_once SCRIPT_ROOT.'config_row/config_database_row_scripts.php';
	include_once SCRIPT_ROOT."dbconnect.php";
	include_once SCRIPT_ROOT."xmlhandle.php";
	include_once SCRIPT_ROOT."cls_page.php";
	//include_once "ebay_order_cron_func.php";
	//include_once "shipping_calc_fun.php";
	
	$dbcon	= new DBClass();
	
	$sql		= "select category,skulist,original_transport,current_transport,country from ebay_adjust_transport "; //特殊料号数据库
	$result		= $dbcon->execute($sql);
	$ebay_adjust_transport	= $dbcon->getResultArray($result);
	$dbcon->free_result($result);
	$__liquid_items_postbyhkpost = explode(",", $ebay_adjust_transport[0]['skulist']);//液体产品
	$__liquid_items_postbyfedex = explode(",", $ebay_adjust_transport[1]['skulist']);//贵重物品走联邦
	$__liquid_items_cptohkpost = explode(",", $ebay_adjust_transport[2]['skulist']);//指甲油转香港小包
	$__liquid_items_elecsku = explode(",", $ebay_adjust_transport[3]['skulist']);//电子类产品走香港小包
	$__elecsku_countrycn_array = explode(",", $ebay_adjust_transport[3]['country']);//电子类产品指定国家
	$__liquid_items_fenmocsku = explode(",", $ebay_adjust_transport[4]['skulist']); //粉末状SKU
	$__liquid_items_BuiltinBattery  = explode(",", $ebay_adjust_transport[5]['skulist']); //内置电池类产品
	$__liquid_items_SuperSpecific = array_filter(explode(",", $ebay_adjust_transport[14]['skulist'])); //超规格的产品，长度大于60cm, 三边大于 90cm
	$__liquid_items_Paste = explode(",", $ebay_adjust_transport[6]['skulist']);//膏状SKU
	$__liquid_items_Wristwatch = array_filter(explode(",", $ebay_adjust_transport[10]['skulist']));//手表类SKU
	$__liquid_items_TempIntercept = array_filter(explode(",", $ebay_adjust_transport[11]['skulist']));//暂时拦截料号20131105
	
	/* 加载系统默认配置*/
	$ss		= "select * from ebay_config WHERE `ebay_user` ='$user' LIMIT 1";
	$ss		= $dbcon->execute($ss);
	$ss		= $dbcon->getResultArray($ss);
	$defaultstoreid				= $ss[0]['storeid'];
	$notesorderstatus			= $ss[0]['notesorderstatus'];
	$auditcompleteorderstatus	= $ss[0]['auditcompleteorderstatus'];
	unset($ss);
	
	//取统一包装材料重量数据
	$tt		="	select weight,model from ebay_packingmaterial 
				where  ebay_user ='$user' ";
	$tt		= $dbcon->execute($tt);
	$tt		= $dbcon->getResultArray($tt);
	
	$global_packingmaterial_weight=array();
	foreach($tt as	$t){
		$global_packingmaterial_weight[$t['model']]	= $t['weight'];
	}
	unset($tt,$t);
	
	//取统一国家中文名对应英文名
	$ec = "select * from ebay_countrys where ebay_user='$user' ";
	$result = $dbcon->execute($ec);
	$ebay_country_lists = $dbcon->getResultArray($result);
	$global_countrycn_coutryen = array();
	foreach($ebay_country_lists AS $ebay_country_list){
		$global_countrycn_coutryen[trim($ebay_country_list['countryen'])] = trim($ebay_country_list['countrycn']);
	}
	//取各个平台的账号名称
	$sql = "select ebay_account,ebay_platform from ebay_account where ebay_user='$user' order by ebay_platform ASC,ebay_account desc ";
	$result = $dbcon->execute($sql);
	$system_account_lists = $dbcon->getResultArray($result);
	$SYSTEM_ACCOUNTS = array();
	foreach($system_account_lists AS $system_account_list){
		$SYSTEM_ACCOUNTS[$system_account_list['ebay_platform']][] = $system_account_list['ebay_account'];
	}
?>
