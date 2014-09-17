<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>芬哲圆通快递单打印页面</title>
<style type="text/css">
#body{
	padding:0;
	margin:0;}
#main{
	font-family:宋体;
	font-weight:bolder;
	text-align:center;
	height:409px;
	width:742px;
	position:relative;
	left:44px;
	top:5px;
	}
#From{
		height:20px;
		width:87px;
		position:absolute;
		top:80px;
		left:66px;}
#Departure{
			  text-align:left;
		      height:20px;
		      width:200px;
		      position:absolute;
		      top:80px;
		      left:200px;}
#Company{
				  		height:23px;
		                width:235px;
		                position:absolute;
		                top:103px;
		                left:116px;}
#Add{
		height:60px;
		width:300px;
		position:absolute;
		top:130px;
		left:54px;}
#Name{
		height:20px;
		width:190px;
		position:absolute;
		top:253px;
		left:56px;}
#Sender{
		height:37px;
		width:140px;
		position:absolute;
		top:350px;
		left:59px;}
#Picked{
		height:29px;
		width:86px;
		font-size:25px;
		position:absolute;
		top:360px;
		left:206px;}
#To{
		height:20px;
		width:85px;
		font-size:20px;
		position:absolute;
		top:95px;
		left:425px;}
#City{
		height:20px;
		width:150px;
		font-size:20px;
		position:absolute;
		top:95px;
		left:534px;}
#Address{
		height:53px;
		width:310px;
		font-size:20px;
		position:absolute;
		top:150px;
		left:410px;}
#Mobile{
		height:20px;
		width:110px;
		font-size:20px;
		position:absolute;
		top:211px;
		left:480px;}

#BarCode{
		text-align:right;
		height:72px;
		width:200px;
		position:absolute;
		top:350px;
		left:460px;}
</style>
</head>
<body>
<?php
error_reporting(1);
//@session_start();
require_once WEB_PATH."framework.php";
Core::getInstance();
global $dbConn;

$shipOrderId   =   intval($_SESSION['shipOrderId']);//发货单ID
$transport     =   trim($_SESSION['transport']);//运输方式的ID
$order         =   WhShippingOrderModel::get_order_info_union_table($shipOrderId);
$ebay_id       = $order['id'];
$recordnumber  = $order['recordNumber']; //订单编号
$ebay_userid   = $order['platformUsername']; //买家ID
$ebay_username = $order['username'];
$ebay_account  = CommonModel::getAccountNameById($order['accountId']);//店铺账号

$ebay_state    = $order['state'];
$ebay_city     = $order['city'];
$ebay_street   = $order['street'];
$ebay_address  = $ebay_state . $ebay_city . $ebay_street; //买家地址

$ebay_phone    = $order['landline'];
$ebay_phone1   = $order['phone'];
//$ebay_ordersn = $order['ebay_ordersn']; //order表联系orderdetail表的foreginkey
$tel1          =   !$order['landline'] ? "" : str_replace('-', '', $order['landline']);
$tel           =   $tel1 ? $tel1 : ($order['phone'] ? $order['phone'] : "");
$detail        = WhShippingOrderdetailModel::getShipDetails($shipOrderId);//获取发货单明细
$totalAmount   = 0; //总件数
$goodsInfo     = ""; //订单中的商品sku及对应数量的组合信息，用"/"隔开
foreach($detail as $val){
    $totalAmount += $val['amount'];
    $sku         = $val['sku'];
    $cs          = strstr($val['itemTitle'] == '##' ? "" : $val['itemTitle'], '#'); //截取itemtile字段，得到color and size
   	$goodsInfo   = $goodsInfo . $sku . ' ' . $cs . $val['amount'] . '件/ ';
		
}
$count =count($detail);
?>
<div id="main">
<div id="From">
芬哲(<?php echo $ebay_account['appname'];?>)
</div>
<div id="Departure">
<?php echo $ebay_userid ;?>
</div>
<div id="Company">
<?php echo $recordnumber;?>
</div>
<div id="Add">
<?php
	if (strlen($totalAmount . '件 /' . $goodsInfo) > 140) {
		echo "<span style='font-size:12px'>$totalAmount.'件 /'.$goodsInfo</span>";
	} else {
		echo $totalAmount . '件 /' . $goodsInfo;
	}
?>
</div>
<div id="Name">
0755-89619970
</div>
<div id="Sender">
<?php echo date('Y-m-d').'<br/>'.date('h:i:s');?>
</div>
<div id="Picked">
7061
</div>
<div id="To">
<?php echo $ebay_username;?>
</div>
<div id="City">
<?php echo $ebay_city;?>
</div>
<div id="Address">
<?php
	if (strlen($ebay_address) > 75) {
		echo "<span style='font-size:16px'>$ebay_address</span>";
	} else {
		echo $ebay_address;
	}
?>
</div>
<div id="Mobile">
<?php echo $tel;?>
</div>
<div id='BarCode'>
<?php echo "<font size='3'>$ebay_id&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 1/$count</font>";?>
<img src="http://192.168.200.188/barcode128.class.php?data=<?php echo $ebay_id;?>" alt="" width="260" height="45"/>
<?php echo "<font size='3'>$ebay_userid</font>";?>
</div>
</div>
<?php
//if($count > 1 && $i != $count)
//echo '<div style="page-break-after:always;">&nbsp;</div>';
//} ?>
</body>
</html>