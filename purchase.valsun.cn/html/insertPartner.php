<?php
include "config.php";
include "/data/web/purchase.valsun.cn/lib/functions.php";
error_reporting(-1);
$sql = "select * from ebay_partner  where 1 ";
$sql = $dbconn->execute($sql);
$partnerInfo = $dbconn->getResultArray($sql);
foreach($partnerInfo as $item){
	$userId = getUserId($item['purchaseuser']);
	if(empty($userId)){
		$userId = 2;
	}
	$sql = "INSERT INTO `ph_partner` 
		(id,`company_name`, `username`, `tel`, `phone`, `fax`, `QQ`, `AliIM`, `e_mail`, `shoplink`, `city`, `address`, `sms_status`, `email_status`, `purchaseuser_id`) VALUES 
		({$item['id']},'{$item['company_name']}','{$item['username']}','{$item['tel']}','{$item['mobile']}','{$item['fax']}','{$item['QQ']}','{$item['AliIM']}','{$item['mail']}','{$item['shop_link']}','{$item['city']}','{$item['address']}','{$item['is_sms']}','{$item['is_email']}',{$userId})";
	if($dbconn->execute($sql)){
		echo "添加数据成功。。。。\n";
	}
}

function getUserId($user){
	global $dbconn;
	$sql = "select global_user_id from power_global_user where global_user_name='{$user}'";
	$sql = $dbconn->execute($sql);
	$userInfo = $dbconn->fetch_one($sql);
	return $userInfo['global_user_id'];
}
?>
