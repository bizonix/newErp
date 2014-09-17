<?php
error_reporting(-1);
require_once "scripts.comm.php";
$accountArr     = UserCacheModel::getOpenSysApi("getaccount", array('add'=>1));
$platformArr    = array("aliexpress"=>2,"cndirect"=>8,"DHgate"=>4,
                         "dresslink"=>10,"ebay平台"=>1,"Newegg"=>15,
                         "亚马逊"=>11,"出口通"=>3,"国内销售部"=>16,
                         "天猫哲果"=>13,"天猫芬哲"=>12,"海外仓"=>14,
                          "海外销售平台"=>1,"线下结算客户"=>9);
foreach ($accountArr as $account){
	$insertarr      = array();
	foreach($platformArr as $key=>$value){
		if($key==$account['ebay_platform']){
			$platformId    = $value;
			break;
		}
	}
	$insertarr['id']             = $account['id'];
	$insertarr['account']        = $account['ebay_account'];
	$insertarr['addTime']        = 0;
	$insertarr['addUser']        = $account['ebay_user'];
	$insertarr['platformId']     = $platformId;
	$insertarr['appname']        = $account['appname'];
	$insertarr['email']          = $account['mail'];
	$insertarr['suffix']         = $account['account_suffix'];
	$insertarr['token']          = $account['ebay_token'];
	$count     = OmAvailableModel::getTNameList("fb_account","id"," where id={$account['id']}");
	$sql       = array2sql($insertarr);
	if(!count($count)>0){
		if(OmAvailableModel::insertRow("fb_account"," set $sql")){
			echo "{$account['id']} \n";
		}else{
			echo "失败";
		}
		
	}
	unset($insertarr);
}
?>