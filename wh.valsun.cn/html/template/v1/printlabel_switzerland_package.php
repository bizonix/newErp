<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>瑞士小包</title>
<style>
@charset "utf-8";
/* CSS Document */
html{height:100%;}
body {width:100%;margin:auto;padding:0;height:100%; min-width:1200px;}
h1, h2, h3, h4, h5, h6 {margin:0;padding:0;font-size:11px;}
ul, li, dl, dd, dt, p {margin:0;padding:0;}
ul li {list-style:none;}
img {border:none;}
.main{ width: 350px; height:540px; border: 1px solid #000; margin: 2px;padding: 2px;}
.barcode{ text-align: center; float: left;}
.fontWeight{ font-weight: bold;}
.tab{ float: right;}
.zone{ border: 1px solid #a4a4a4;float: left; margin-top: 7px;width: 330px;}
.zone div{padding: 2px;word-wrap: break-word;word-break:normal;}
.zone1{ border-bottom: 1px solid #a4a4a4;}
.expres{ float: right; margin-top: 5px;margin-right: 5px;}
.zone2{ border: 1px solid #a4a4a4;padding: 2px; margin-top: 5px;}
.zone2 span{display: inline-block;}
.check{ border-top: 1px solid #a4a4a4; border-bottom: 1px solid #a4a4a4;padding: 2px; margin-top: 2px;}
.zone3{ border-top: 1px solid #a4a4a4; border-bottom: 1px solid #a4a4a4; padding: 2px;word-wrap: break-word;word-break:normal;}
.zoneWay{ width: 100%;}
.chinapost div,.chinapost p,.chinapost img,.chinapost span,.chinapost table,.chinapost td,.chinapost tr,.chinapost tbody,.chinapost thead,.chinapost tfoot{ margin:0;padding: 0;}
.chinapost table td{ border:1px solid #000; padding: 0 2px;}
.chinapost .tab td{ text-align:center; vertical-align:middle; border-right:none; border-bottom:none;}
</style>
</head>
<body style="font-family:Arial;font-size:10px">
<?php
error_reporting(1);
//@session_start();
require_once WEB_PATH."framework.php";
Core::getInstance();
global $dbConn;

$shipOrderId    =   intval($_SESSION['shipOrderId']);
$transport      =   trim($_SESSION['transport']);

$shipOrderInfo  =   WhShippingOrderModel::get_order_info_union_table($shipOrderId); //获取发货单基本信息

$transportId    =   $shipOrderInfo['transportId']; //运输方式ID

if(in_array($transportId, array(87, 88))){

	$ebay_id       =   $shipOrderInfo['id'];
	$ebay_tracknumber  =   $shipOrderInfo['tracknumber'];
	$ebay_userid   =   $shipOrderInfo['platformUsername'];
	$ebay_total    =   $shipOrderInfo['total'];
	$ebay_usermail =   $shipOrderInfo['email'];
	$packingtype   =   $shipOrderInfo['packingtype'];
	$orderweight   =   $shipOrderInfo['actualWeight'];
	$ordershipfee  =   $shipOrderInfo['actualShipping'];
	$ebay_account  =   CommonModel::getAccountNameById($shipOrderInfo['accountId']);
	$ebay_currency =   $shipOrderInfo['currency'];
	$cname         =   $shipOrderInfo['username'];
	$street1       =   @$shipOrderInfo['street'];
	$street2       =   @$shipOrderInfo['address2'] ? @$shipOrderInfo['address2'] : '';
    $street2       .=  $shipOrderInfo['address3'] ? $shipOrderInfo['address3'] : '';
	$city          =   $shipOrderInfo['city'];
	$state         =   $shipOrderInfo['state'];
	$countryname   =   $shipOrderInfo['countryName'];
    $countrySn     =   $shipOrderInfo['countrySn'];
	$zip           =   $shipOrderInfo['zipCode']; 
	$tel1          =   $shipOrderInfo['landline'] ? $shipOrderInfo['landline'] : '';
	$tel           =   $tel1 ? $tel1 : ($shipOrderInfo['phone'] ? $shipOrderInfo['phone'] : "");
    $tel           =   str_replace('-', '', $tel);
    $countryCn     =   self::get_countryNameCn($countryname); //根据国家英文名获取国家中文名
    $all_sku_info  =   self::get_all_sku_info($ebay_id);
    $totalqty      =   $all_sku_info['totalqty'];
    unset($all_sku_info['totalqty']);
	/*if(empty($userid)){
		echo "订单 $ebay_id 没有用户id信息,请联系销售人员!<br>";
		continue;
	}
	if(!tep_not_null($countryname)){
	echo "订单 $ebay_id 没有国家及地址信息,请联系销售人员!<br>";
	continue;
	}else if(judge_has_condition($ebay_id)){
	echo "订单 $ebay_id 明细表中包含没有料号 或者 料号数量 或者 料号价值信息,请联系销售人员!";
	continue;
	}*/
	if($street2 == ''){
	   $addressline   =   $cname."<br>".$street1.",".$city.", ".$state.",".$zip.",".$countryname.'('.$countryCn.')';
	}else{
	   $addressline   =   $cname."<br>".$street1.",".$street2.",".$city.", ".$state.",".$zip.",".$countryname.'('.$countryCn.')';
	}
    
	$tel = isset($tel) ? trim($tel) : '';
	if($tel != '' && strtolower($tel) != 'invalid request') {
	   $addressline  .= ',Tel:'.$tel;
	}

	//挂号	
	$isRegister = false;
	if($transportId == '88'){		
		$isRegister = true;
	}

    $total_value = 0;
    $total_weight = 0;
?>
	<div class="main">
            <div style="height: 85px;margin-top:2px;">
                <div class="barcode" style="height:100%;">
                    <p style="margin-bottom: 0;"><?php echo $ebay_id; ?></p>
                    <div class="print_image" image_left='22' image_top='25' image_type='jpg' style="width:140px; height:50px;" data="<?php echo base64_encode(file_get_contents(WEB_URL.'barcode128.class.php?data='.$ebay_id))?>"></div>
                    <!--<img src="barcode128.class.php?data=<?php echo $ebay_id; ?>" width="140" height="60" />-->             
                    <p style="margin-top: 5px;"><?php echo '1/1';?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total Qty:<?php echo $totalqty; ?></p>
                </div>
                <div class="tab" style="height:100%;"> 
                    <table cellpadding="0" cellspacing="0" style="width:210px;border-collapse:collapse;text-align:center;" border="1">
                        <tr>
                            <td style="" colspan="2">
                                <strong>PRIORITY</strong>
                            </td>
                        </tr>
                        <tr>
                            <td width="68%" style="font-size:11px;" align="left">                            
                                &nbsp;If undeliverable,please &nbsp;return to: Exchange Office
                                &nbsp;SPIHKG 00009156 <br />
                                &nbsp;8010 Zurich-Mulligen
                                &nbsp;Switzerland
                            </td>
                            <td>
                                <strong>P.P.</strong>
                                <p style="font-size:11px;">
                                    Swiss Post
                                    CH-8010 Zurich
                                    Mulligen
                                </p>
                            </td>
                        </tr>
                    </table>                
                </div>
            </div>
            <div style="clear:both;">
            </div>
            
            <div class="zone" style="font-size:11px;width:100%">
                <p class="fontWeight">Deliver To:</p>
                <p style="font-size:12px;"><?php echo $addressline; ?></p>
            </div>
            
            <div style="margin: 0;padding:0;width:100%;height:60px;">
                <div class="barcode" style="padding-top:5px;width:80%;">
                    <img src="/images/SwissPost.jpg" style="float:left;" widht="138" height="25" />
                </div>
                <div style="font-size: 13px;margin-top: 4px;float:right;" class="tab">
                    CN22
                </div>
                <div class="barcode" style="width:40%;">
                    <strong style="float:left;">Post administration</strong>
                </div>
                <div class="tab" style="font-size:12px;width:50%;">
                    <strong style="float:right;">CUSTOMS&DECLARATION</strong>
                </div>
                <div class="tab" style="font-size:10px;margin: 0;padding:0;width:100%;">
                    <span style="float:right;">(May be opened officially)</span>
                </div>
            </div>
            
            <div style="clear:both;">
            </div>         

            <div style="clear:both;">
            </div>
            <div class="zone2" style="margin: 0;">
                <span style="margin-bottom: 2px;padding:0;height:12px;">Please check on appropriate option</span>
                <div class="check" style="margin: 0px;padding:0; height:20px;">
                    <label><input style="margin: 0px; height:15px;" type="checkbox" checked="checked"/><span style="margin-top: 0px;">Gift</span></label>
                    <label><input type="checkbox"/><span style="margin-top: 0px;">Commercial sample</span></label>
                    <label><input type="checkbox"/><span style="margin-top: 0px;">Document</span></label>
                    <label><input type="checkbox"/><span style="margin-top: 0px;">Other</span></label>
                </div>
                <div>
                    <table cellpadding="0" cellspacing="0" style="width:100%;border-collapse:collapse;" border="1">
                        <tr style="font-size:11px;font-weight:bold;">
                            <td>
                                Quantity and detailed description of contents  
                            </td>
                            <td>
                                Weight(kg)
                            </td>
                            <td>
                                Value  
                            </td>
                        </tr>  
                        <?php foreach($all_sku_info as $skuInfo){ ?>
                        <tr style="font-size:11px;">
                            <td>
                                <div style="border:1px #000 solid; margin:1px;word-break:break-all;">
                                <?php
                                    $spu    =   $skuInfo['spu'];
                                    $hsInfo =   self::get_hsInfo(array($spu));
                                    $title  =   empty($hsInfo[$spu]['customsNameEN']) ? $skuInfo['itemtitle'] :  $hsInfo[$spu]['customsNameEN'];
                                    echo $skuInfo['info'].$title;
                                ?>  
                                </div>                                                  
                            </td>
                            <td>
                                <?php
                                    $tmpWeight      =   $skuInfo['goodsWeight'] * $skuInfo['amount'];
                                    $total_weight   +=  $tmpWeight;
                                    echo $tmpWeight;
                                ?>
                            </td>
                            <td>
                            <?php
                                $temp_value =   self::rand_float($skuInfo['itemPrice'],$countryname);
                                $total_value +=  $temp_value*$skuInfo['amount'];                                
                                echo $ebay_currency.$temp_value;
                            ?>
                            </td>
                        </tr>  
                        <?php } ?> 
                        <tr style="font-size:11px;font-weight:bold;">
                            <td colspan="3">
                                Origin:CN&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total Weight:<?php echo $total_weight; ?>(kg)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total Value:<?php echo $ebay_currency.$total_value; ?> 
                            </td>
                        </tr>

                    </table>
                </div>
                <div style="clear:both;">
                </div>
                <div class="zone3" style="font-size:10px;line-height:11px;">
                    <span style="margin: 0;padding:0;">
                        I, the undersigned, whose name and address are given on the item,
                        certify that the particulars given in this declaration are correct and that this item does not contain dangerous article or articles prohibited by legislation or by postal or customs regulations.    
                    </span>
				</div>
                <div>
                    <div style="float:left;">
                        <p>Date and Senders Signature</p>
                        <p><?php echo date("j-M-Y"); ?></p>
                    </div>
                    <div style="float:right">
                        <img style="margin-top: 2px;" src="/images/new_dhl_signature.jpg" width="136" height="40" />
                    </div>
                </div>
                <div style="clear:both;">
                </div>
            </div>
            <?php if($isRegister) { ?>
            <div>                
                <div class="barcode" style="margin-top:10px;margin-left:40px;">  
                    <span style="float:left;font-size:30px;font-weight:bold;">
                     R       
                    </span>
                    <div class="print_image" image_left='90' image_top='435' image_type='jpg' style="width:220px; height:30px;" data="<?php echo base64_encode(file_get_contents(WEB_URL.'barcode128.class.php?data='.$ebay_tracknumber))?>"></div>
                    <!--<img src="barcode128.class.php?data=<?php echo $ebay_tracknumber; ?>" width="220" height="30" />-->             
                    <p style="font-size:13px;font-weight:bold;"><?php echo $ebay_tracknumber; ?></p>
                </div>
                <div style="clear:both;">
                </div>
            </div>
            <?php }?>
	  </div>
<?php
}
?>
</body>
</html>