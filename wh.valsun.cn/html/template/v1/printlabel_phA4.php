<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>配货清单</title>
</head>

<body>

<?php
/**
* 配货清单打印
* add by hws 2013-08-27
**/
@session_start();
require_once "../../../framework.php";
Core::getInstance();
global $dbConn;
$userName 	  = $_SESSION['userName'];
$order_groups = array();
$order_group_temp = array();
$create_time  = strtotime(date("Y-m-d"));
$order_group  = trim($_REQUEST['order_group']);

if(!empty($order_group)){
	$sql = "select * from wh_shipping_order_group where shipOrderGroup='$order_group'";
}else{
	$status_sql = "select * from wh_shipping_order_group where createdTime>'{$create_time}' and user='$userName' order by id desc limit 0,1";
	$ret        = $dbConn->fetch_first($status_sql);

	$todaySequence = $ret['todaySequence'];
	
	$sql = "select * from wh_shipping_order_group where todaySequence='{$todaySequence}' and createdTime>'{$create_time}' and user='$userName' order by id asc";
	$sql_group = "select shipOrderGroup from wh_shipping_order_group where todaySequence='{$todaySequence}' and createdTime>'{$create_time}' and user='$userName' group by shipOrderGroup order by id asc";
}

$g_query = $dbConn->query($sql);
if($g_query){
	$group_info = $dbConn->fetch_array_all($g_query);
}
if(empty($group_info)){
	echo "配货清单不存在！";exit;
}

//插入今日清单打印表 begin
if($sql_group){
	$sql_group = $dbConn->query($sql_group);
	$sql_group = $dbConn->fetch_array_all($sql_group);
	$time = time();
	$begintime = strtotime(date("Y-m-d"));

	foreach($sql_group as $group_record){
		$r_sql = "select * from wh_shipping_order_group_print where shipOrderGroup='{$group_record['shipOrderGroup']}' and groupPrintTime>'$begintime' ";
		$r_sql = $dbConn->query($r_sql);
		$r_sql = $dbConn->fetch_array_all($r_sql);
		if(empty($r_sql)){
			$i_sql = "insert into wh_shipping_order_group_print(shipOrderGroup,groupPrintUser,groupPrintTime) values('{$group_record['shipOrderGroup']}','$userName','$time')";
			$dbConn->query($i_sql);
		}
	}
}
//end

foreach($group_info as $info){
	$temp = $info['pName']."|".$info['sku'];
	if(isset($order_group_temp[$info['shipOrderGroup']][$temp])){
		$skuAmount   = $order_group_temp[$info['shipOrderGroup']][$temp]['skuAmount'] + $info['skuAmount'];
		$shipOrderId = $order_group_temp[$info['shipOrderGroup']][$temp]['shipOrderId'].",".$info['shipOrderId'];
		$carNumber   = $order_group_temp[$info['shipOrderGroup']][$temp]['carNumber'].",".$info['carNumber']."(".$info['skuAmount'].")";

		$order_group_temp[$info['shipOrderGroup']][$temp] = array(
		'sku'         => $info['sku'],
		'skuAmount'   => $skuAmount,
		'shipOrderId' => $shipOrderId,
		'carNumber'   => $carNumber,
		);
	}else{
		$order_group_temp[$info['shipOrderGroup']][$temp] = array(
		'sku'   	  => $info['sku'],
		'skuAmount'   => $info['skuAmount'],
		'shipOrderId' => $info['shipOrderId'],
		'carNumber'   => $info['carNumber']."(".$info['skuAmount'].")",
		);
	}	
}

foreach($order_group_temp as $g=>$group){
	foreach($group as $s=>$s_info){
		$p_num = strpos($s,"|");
		$pname = substr($s,0,$p_num);
		$order_groups[$g][] = array(
		'pName'		 => $pname,
		'sku'        => $s_info['sku'],
		'sku_amount' => $s_info['skuAmount'],
		'orders'     => $s_info['shipOrderId'],
		'car_number' => $s_info['carNumber']
		);
	}
}
//print_r($order_groups);die;

$page_num = 40;               //一页几个sku
foreach($order_groups as $key=>$groups){
$count = count($groups);
$pages = ceil($count/$page_num);
$k = 0;
for($i=0;$i<$pages;$i++){
$n = $i+1;
?>
<table width="100%" border="1" cellspacing="0" cellpadding="0">
	<tr>
		<td style="padding:5px;">配货清单：<font color="black"><?php echo $key;?></font><span style="padding-left:100px" color="black"><?php echo "(".$n."/".$pages.")";?></span><span style="padding-left:200px" color="black"><?php echo date('Y-m-d',time());?></span></td>
		<td width="50%" align="right" style="padding:5px;"><img src="http://192.168.200.200:9999/barcode128.class.php?data=<?php echo $key; ?>" alt="" width="250" height="40"/></td>
	</tr>
	<tr>
		<td colspan="2">
		<table width="99%" border="1" cellspacing="0" cellpadding="0" style="margin:5px;">
			<tr>
				<td width="10%" align="center">仓位</td>
				<td width="10%" align="center">sku</td>
				<!--td width="40%" align="center">描述</td-->
				<td width="10%" align="center">总数量</td>	
				<td width="25%" style="word-break:break-all" align="center">筐号(数量)</td>				
				<td width="42%" style="word-break:break-all" align="center">订单号</td>
				<td width="13%" align="center">备注</td>
			</tr>
			<?php 
			//foreach($groups as $group){
			for($j=0;$j<$page_num;$j++){				
				$num = $j+($page_num*$k);
				if($num==$count){break;}				
			?>
			<tr>
				<td width="10%"><?php echo $groups[$num]['pName'];?></td>
				<td width="10%"><?php echo $groups[$num]['sku'];?></td>
				<!--td width="40%"><?php echo $good_info['goods_name'];?></td-->
				<td width="10%"><?php echo $groups[$num]['sku_amount'];?></td>		
				<td width="25%" style="word-break:break-all"><?php echo $groups[$num]['car_number'];?></td>
				<td width="42%" style="word-break:break-all"><?php echo $groups[$num]['orders'];?></td>				
				<td width="13%"></td>
			</tr>
			<?php }?>
		</table>
		</td>
	</tr>
</table>

<?php 
echo '<div style="page-break-after:always;">&nbsp;</div>';
$k++;
}}
?>
</body>
</html>
