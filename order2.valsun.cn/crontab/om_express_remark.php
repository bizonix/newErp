<?php
error_reporting(-1);
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
require "/data/web/order.valsun.cn/framework.php";
Core::getInstance();
$sql             = "select * from om_express_remark_bak group by omOrderId ";
$query	         = $dbConn->query($sql);
$express_remarks = $dbConn->fetch_array_all($query);
echo count($express_remarks); echo "\n";
//var_dump($om_status_menu); echo "\n"; exit;
foreach($express_remarks as $express_remark){
	BaseModel :: begin();
	$omOrderId = $express_remark['omOrderId'];
	$osql = "select * from om_express_remark where omOrderId = '{$omOrderId}' ";
	$query	= $dbConn->query($osql);
	$oldremarks =   $dbConn->fetch_array_all($query);
	if($oldremarks){
		$usql = "delete from om_express_remark where omOrderId = '{$omOrderId}' ";
		if(!$dbConn->query($usql)){
			echo $usql; echo "\n";
			BaseModel :: rollback();
		}else{
			$osql = "select * from om_express_remark_bak where omOrderId = '{$omOrderId}' ";
			$query	= $dbConn->query($osql);
			$remarkValues =   $dbConn->fetch_array_all($query);
			foreach($remarkValues as $info){
				$info['lastModified'] = time();
				$info['description'] = mysql_real_escape_string(trim($info['description']));
				$string = array2sql_extral($info);
				$sql = "insert into om_express_remark set ".$string;
				//echo $sql; echo "\n";
				if(!$dbConn->query($sql)){
					echo $sql; echo "\n";
					BaseModel :: rollback();
				}
			}
		}
	}
	BaseModel :: commit();
	BaseModel :: autoCommit();
}
exit;
?>