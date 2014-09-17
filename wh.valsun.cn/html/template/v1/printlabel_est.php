<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>俄速通</title>
<style type="text/css">
<!--
p{margin:0;padding:0;}
.main {border:#000000 1px solid; width:350px; height:350px;font-size:9px;margin:0;padding:0;}
.STYLE2 {font-size: 9px}
.footStyle0{ font-family:Arial; font-size:23.5px; text-align:center;}
.footStyle1{ font-family:Arial; font-size:14.5px; text-align:center;}
.footStyle2{ font-family:Arial; font-size:9px;}
.footStyle3{ font-family:Arial; font-size:9px;}
.styleBottom{ border-bottom: 1px solid #000;}
.styleBorder{ border: 10px solid #000;}
.styleRight{ border-right: 1px #000 solid;}
.styleLeft{ border-left: 1px #000 solid;}
.font10px{font-size:10px;}
.paddingRight{ padding-right:0;}
.floatRight{ float:right;}
.textalignR{ text-align:left;}
.f101{font-size:6px;}
.f18{font-size:18px;}
.tick{font-weight:bold;font-size:14px;}
-->
</style>
</head>
<body>
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

if(in_array($transportId, array(79, 80))){
 	
	$ebay_id       =   $shipOrderInfo['id'];
	$ebay_tracknumber  =   $shipOrderInfo['tracknumber'];
	$ebay_userid   =   $shipOrderInfo['platformUsername'];
	$ebay_total    =   $shipOrderInfo['total'];
	$ebay_total    =   $ebay_total >= 10 ? 10 : ($ebay_total == 0 ? rand(5,10) : $ebay_total);
	$ebay_usermail =   $shipOrderInfo['email'];
	$packingtype   =   $shipOrderInfo['packingtype'];
    
	$orderweight   =   round($shipOrderInfo['actualWeight']/1000,3);
	$ordershipfee  =   $shipOrderInfo['actualShipping'];
	$ebay_account  =   CommonModel::getAccountNameById($shipOrderInfo['accountId']);
	$cname         =   $shipOrderInfo['username'];
	$street1       =   $shipOrderInfo['street'];
	$street2       =   $shipOrderInfo['address2'] ? $shipOrderInfo['address2'] : '';
    $street2       .=  $shipOrderInfo['address3'] ? $shipOrderInfo['address3'] : '';
	$city          =   $shipOrderInfo['city'];
	$state         =   $shipOrderInfo['state'];
	$countryname   =   $shipOrderInfo['countryName'];
    $countrySn     =   $shipOrderInfo['countrySn'];
    $ebay_currency =   $shipOrderInfo['currency'];
    $countryCn     =   self::get_countryNameCn($countryname); //根据国家英文名获取国家中文名
	$zip           =   $shipOrderInfo['zipCode']; //邮编
	$tel1          =   !$shipOrderInfo['landline'] ? '' : $shipOrderInfo['landline'];
	$tel           =   $tel1 ? $tel1 : ($shipOrderInfo['phone'] ? $shipOrderInfo['phone'] : "");
    $tel           =   str_replace('-', '', $tel); //获取客户电话
    $ebay_print_num=   ($transportId == 79) ? $ebay_tracknumber : $ebay_id;
    $all_sku_info  =   self::get_all_sku_info($ebay_id); //获取订单料号明细
    $totalqty      =   $all_sku_info['totalqty']; //料号总数
    unset($all_sku_info['totalqty']);
    $skuArr        =   $all_sku_info;
    
	//邮编分区
    if(preg_match("/^1/", $zip)){
        $zone   =   1;
    }else if(preg_match("/^2/", $zip)){
        $zone   =   2;
    }else if(preg_match("/^3/", $zip)){
        $zone   =   3;
    }else if(preg_match("/^4/", $zip)){
        $zone   =   4;
    }else if(preg_match("/^6(0|1|2|40|41|42)+/", $zip)){
        $zone   =   4;
    }else{
        $zone   =   6;
    }
    		
	/*$tel					= $order_list['ebay_phone'];
	if($tel == 'Invalid Request') $tel = '';
    if(empty($userid)){
        echo "订单 $ebay_id 没有用户id信息,请联系销售人员!<br>";
        continue;
    }
	//add Herman.Xi 无地址不打印 2012-11-25
	if(!tep_not_null($countryname)){
		echo "订单 $ebay_id 没有国家及地址信息，请联系销售人员！<br>";
		continue;
	}else if(judge_has_condition($ebay_id)){
		echo "订单 $ebay_id 明细表中包含没有料号 或者 料号数量 或者 料号价值信息，请联系销售人员！";
		continue;
	}*/
	$addressline    =   $cname."<br />";
	if($street2 == ''){
		$addressline .= $street1."&nbsp;&nbsp".$city."&nbsp;&nbsp".$state."&nbsp;&nbsp".$countryname.'('.$countryCn.')';
	
	}else{
		$addressline .= $street1."&nbsp;&nbsp".$street2."&nbsp;&nbsp".$city."&nbsp;&nbsp".$state."&nbsp;&nbsp".$countryname.'('.$countryCn.')';
	}
?>
<div class='main'>
    <div style="border-bottom:1px solid #000000;padding:2px;height:53px;">
        <div style="float:left;margin-top:10px;margin-right:10px;">
            <img src="/images/chinapost_logo.jpg" style="vertical-align:middle;"/>
        </div>
        <div style="font-size:12px;float:left;text-align:center;font-weight:bold;">
            <p style="margin-top:2px;">航空</p>
            <p style="margin-top:2px;">Small packet</p>
            <p style="margin-top:2px;">BY AIR</p>
        </div>
        <div style="font-size:20px;float:left;line-height:0;text-align:center;font-weight:bold;margin-left:30px;">
            <p style="margin-top: 30px;">RU 001</p>
        </div>
        <div style="clear:both;">
        </div>
    </div>
    <div style="font-size:11px;border-bottom:1px solid #000000;padding:3px;">
        协议客户：<strong>黑龙江俄速通国际物流有限公司（23010104577000）</strong>
    </div>
    <div style="font-size:12px;border-bottom:1px solid #000000;height:50px;overflow-y: hidden;">
        <div style="float:left;padding:3px;">
            <div>
                <div style="float:left;">
                    FROM：
                </div>
                <div style="font-size:10px;font-weight:bold;float:left;width:240px;">
                    NO.7 TIANCHI ROAD PINGFANG DISTRICT HAERBIN CITY HEILONGJIANG STATE CHINA 
                </div>
            </div>
            <div style="clear:both;">
            </div>
            <div style="margin-top:2px;">
                <div style="float:left;">
                    ZIP：
                </div>
                <div style="float:left;width:240px;">
                    150060
                    <span style="margin-left:45px;">Tel:045151922298</span>
                </div>
            </div>
        </div>
        <div style="width:50px;float:right;border-left:1px solid #000;">
            <p style="border-bottom:1px solid #000;height:25px;padding-left:15px;margin-top:5px;">zone</p>
            <p style="height: 22px; padding-left:20px;margin-top:2px;"><?php echo $zone?></p>
        </div>
        <div style="clear:both;">
        </div>
    </div>
    
    <div style="font-size:11px;border-bottom:1px solid #000000;min-height:74px;">
        <div style="float:left;padding:-top:1px;padding-bottom:0px;">
            <div>
                <div style="float:left;">
                    TO :&nbsp;
                </div>
                <div style="font-size:12px;font-weight:bold;float:left;width:90%;">
                    <?php echo $addressline;?> 
                </div>
            </div>
            <div style="clear:both;">
            </div>
            <div style="margin: 0;">
                <div style="float:left;">
                    ZIP：
                </div>
                <div style="float:right;width:300px;">
                    <?php echo $zip?>
                    <span style="margin-left:45px;">Tel:<?php echo $tel?></span>
                </div>
            </div>
            
        </div>
        <div style="clear:both;">
        </div>
    </div>
    
    <div style="font-size:11px;border-bottom:1px solid #000000;padding:3px;">
        退件单位：<strong>哈尔滨国际小包收寄中心</strong>
    </div>
    <div style="text-align:center;font-size:14px;font-weight:bold;border-bottom:1px solid #000000;padding:2px;">
        <div class="print_image" image_left='70' image_top='240' image_type='jpg' style="width:260px;height:40px;" data="<?php echo base64_encode(file_get_contents(WEB_URL.'barcode128.class.php?data='.$ebay_print_num))?>">
            <!--<img src="./barcode128.class.php?data=<?php echo $ebay_print_num; ?>" width="260" height="40"/>-->
        </div>
        <div style="margin-top: 5px;"><?php echo $ebay_print_num?></div>
    </div>
    <div style="font-size:11px;padding:3px;">
        【75500011】Ref No:<?php echo $ebay_id?>
        <div style="clear:both;">
        </div>
        <div style="font-size:10px;">
            <div style="float:left;">
                SKU：
            </div>
            <div style="float:left;width:310px;">
               <?php
                $num  =   1;
    			foreach($all_sku_info as $sku=>$skuInfo){
                    if($num > 8){
                        break;
                    }
                    if($num%2 == 0){
                       echo "<span style='padding-left: 45px;'> {$skuInfo['info']}</span><br />";
                    }else{
                       echo "<span> {$skuInfo['info']}</span>";
                    }
                    $num++;
                    unset($all_sku_info[$sku]);
                }
              ?>
            </div>
        </div>
    </div>
</div>
<div style="page-break-after:always;">&nbsp;</div>

<?php
    if(!empty($all_sku_info)){?>
    <div class='main'>
        <div style="font-size:11px;padding:3px;">
            <div style="font-size:10px;">
                <div style="float:left;">
                    SKU：
                </div>
                <div style="width:310px;">
                   <?php
        			foreach($all_sku_info as $sku=>$skuInfo){
                        if($num%2 == 0){
                           echo "<span style='padding-left: 45px;'> {$skuInfo['info']}</span><br />";
                        }else{
                           echo "<span> {$skuInfo['info']}</span>";
                        }
                        $num++;
                        unset($all_sku_info[$sku]);
                    }
                  ?>
                </div>
            </div>
        </div>
    </div>
    <div style="page-break-after:always; height=0;">&nbsp;</div>
<?php
    }
?>
    
<table class="main" border="0" cellpadding="0" cellspacing="0">
<tr>
 	<td class="styleBottom" height="30px">
    <div style="font-family:Arial, Helvetica, sans-serif; font-size:10px">
        <table width="100%" border="0" cellpadding="0" cellspacing="0" height="30mm">
            <tr>
              <td width="25%" height="30px" align="left">
              	<div style="font-family:Arial;">
                <span class="footStyle2"><img src="/images/chinapost_logo.jpg" style="vertical-align:middle;"/></span>
                </div>
              </td>
              <td width="55%" align='center'>
              	<div style="font-family:Arial; font-size: 12px;">
    				<span>报关签条<br/>CUSTOMS DECLARATION</span>
    			</div>
              </td>
              <td width="20%" align='center'>
              <div style="font-family:Arial; font-size: 11px;">
    				<span>邮 2113<br/>CN22<br/></span>
		      </div>
              </td>
            </tr>
            <tr>
            <td style="border-top:1px solid #000;" align='left'>可以径行开拆</td>
            <td style="border-top:1px solid #000;" colspan="2" align='left'>&nbsp;&nbsp;May be opened officially</td> 
            </tr>			
        </table>
    </div>
    </td>
</tr>
<tr>
 	<td>
		<div style="font-family:Arial, Helvetica, sans-serif; font-size:9px">
			<table width="100%" height="100%" cellspacing="0" cellpadding="3" >
				<tr>
					<td class="styleBottom styleRight footStyle2" rowspan="2" style="padding-left:5px;"  align="center">邮件种类<br/>Category of item<br/>(请在适当的文字前划“√”)<br/>Tick as appropriate</td>
					<td class="styleBottom styleRight" style="width:20px;" align="center"></td>
					<td class="styleBottom styleRight" align="center"><span class="footStyle2">礼品<br/>Gift</span></td>
					<td class="styleBottom styleRight" style="width:20px;" align="center"></td>
					<td class="styleBottom" width="120px" align="center"><span class="footStyle2">商品货样<br/>Commercial sample</span></td>
				</tr>
				<tr>
					<td class="styleBottom styleRight" style="width:20px;" align="center"></td>
					<td class="styleBottom styleRight" align="center"><span class="footStyle2">文件<br/>Documents</span></td>
					<td class="styleBottom styleRight tick" style="width:20px;" align="center">√</td>
					<td class="styleBottom" align="center"><span class="footStyle2">其它<br/>	Other</span></td>
				</tr>
			</table>
		</div>
    </td>
</tr>
<tr>
 	<td>
		<div style="font-family:Arial, Helvetica, sans-serif; font-size:5px">
			<table width="100%" height="100%" cellspacing="0" cellpadding="3">
				<tr>
					<td colspan="2" width="63%" class="styleBottom styleRight footStyle3" style="padding-left:5px;">内件详细名称和数量<br/>Quantity and detailed description of contents</td>
					<td align="center" class="styleBottom styleRight footStyle3">重量(千克)<br/>Weight(KG)</td>
					<td colspan="2"  align="center" class="styleBottom footStyle3">价值<br/>Value</td>
				</tr>
                <?php
                    $total_price    =   0;
                    $total_weight   =   0;
                    foreach($skuArr as $val){
                        $name   =   $val['category'];
                        $num    =   $val['amount'];
                        $weight =   $val['goodsWeight'];
                        $price  =   $val['itemPrice'];
                        $total_weight   +=  $weight;
                        $total_price    +=  $price;
                ?>
                <tr>
					<td align="left" colspan="2" class="styleBottom styleRight footStyle3" style="padding-left:5px;"><?php echo $name.'*'.$num;?></td>
					<td align="center" class="styleBottom styleRight footStyle3"><?php echo $weight;?></td>
					<td height="20" align="center" colspan="2" class="styleBottom footStyle3"><?php echo $price?></td>
				</tr>
                <?php
                    }
                ?>
				
				<tr>
					<td align="left" colspan="2" class="styleBottom styleRight footStyle3" style="padding-left:5px;">协调系统税则号列和货物原产国<br/>(只对商品邮件填写)<br>HS tariff number and country of origin of goods(For commercial items only)</td>
					<td align="center" class="styleBottom styleRight footStyle3">总重量(千克)<br/>Total Weight(KG)</td>
					<td align="center" colspan="2"  class="styleBottom footStyle3">总价值<br/>Total Value</td>
				</tr>
				<tr height="20">
					<td align="left" colspan="2" class="styleRight footStyle3" style="padding-left:5px;"></td>
					<td align="center" class="styleRight footStyle3"><?php echo $total_weight;?></td>
					<td align="center" colspan="2" class="footStyle3"><?php echo $total_price?></td>
				</tr>
                <tr>
					<td style="border-top: 1px solid #000; font-size:9px;" align="left" colspan="5">我保证上述申报准确无误，本函件内未装寄法律或邮政和海关规章禁止邮寄的任何危险物品<br />
I,the undersigned,certify that particulars given in this declaration are correct and that this item dose not contain any dangerous article or artices prohibited by legislation or by postal or customs regulations</td>
				</tr>
                <tr style="margin: 0;padding:0;">
					<td align="left" colspan="5" style="font-size: 9px;"><div style="float: left;padding-top: 11px; padding-left:40px;">寄件人签字 Sender's signiture :</div><img src="/images/new_dhl_signature.jpg" width="136px" height="35" style="padding-top: 0px; float:left;"/></td>
				</tr>
			</table>
		</div>
    </td>
 </tr>
</table>
<?php
}
?>
</body>
</html>