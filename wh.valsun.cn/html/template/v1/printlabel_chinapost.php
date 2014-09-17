<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>中国邮政</title>
<style>
@charset "utf-8";
/* CSS Document */
html{height:100%;}
body {width:100%;margin:auto;padding:0;height:100%; min-width:1200px;}
h1, h2, h3, h4, h5, h6 {margin:0;padding:0;font-size:11px;}
ul, li, dl, dd, dt, p {margin:0;padding:0;}
ul li {list-style:none;}
img {border:none;}
.main{ width: 350px; height:540px; border: 1px solid #000; margin: 2px;padding: 2px;font-size:9px;}
.barcode{ text-align: center; float: left;}
.fontWeight{ font-weight: bold;}
.tab{ float: right;}
.zone{ border: 1px solid #a4a4a4;float: left; margin-top: 10px;width: 330px;}
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
<body style="font-family:Arial;font-size:11px">
<?php
error_reporting(1);
//@session_start();
require_once WEB_PATH."framework.php";
Core::getInstance();
global $dbConn;

$shipOrderId    =   intval($_SESSION['shipOrderId']);
$transport      =   trim($_SESSION['transport']);

$shipOrderInfo  =   WhShippingOrderModel::get_order_info_union_table($shipOrderId);
//$shipOrderDetail=   WhShippingOrderdetailModel::getShipDetails($shipOrderId);

//if($unTracking){
//	echo "以下订单没有跟踪号，请移出后再执行打印！<br>".implode(', ', $unTracking);
//	exit;
//}

//一次性获取全部海关信息
//$hsInfo = self::get_hsInfo($spus);
$transportId    =   $shipOrderInfo['transportId']; //运输方式ID

if(in_array($transportId, array(1, 2))){
    
	$ebay_id       =   $shipOrderInfo['id'];
	$ebay_tracknumber  =   $shipOrderInfo['tracknumber'];
	//$ebay_userid   =   $shipOrderInfo['ebay_userid'];
	$ebay_total    =   $shipOrderInfo['total'];
	$ebay_total    =   $ebay_total >= 10 ? 10 : ($ebay_total == 0 ? rand(5,10) : $ebay_total);
	$ebay_usermail =   $shipOrderInfo['email'];
	//$packingtype   =   $shipOrderInfo['packingtype'];
    
	$orderweight   =   round($shipOrderInfo['actualWeight']/1000,3);
	$ordershipfee  =   $shipOrderInfo['actualShipping'];
	$ebay_account  =   CommonModel::getAccountNameById($shipOrderInfo['accountId']);
	$cname         =   $shipOrderInfo['username'];
	$street1       =   @$shipOrderInfo['street'];
	$street2       =   @$shipOrderInfo['address2'] ? @$shipOrderInfo['address2'] : "";
    $street2       .=  $shipOrderInfo['address3'] ? $shipOrderInfo['address3'] : '';
	$city          =   $shipOrderInfo['city'];
	$state         =   $shipOrderInfo['state'];
	$countryname   =   $shipOrderInfo['countryName'];
    $countrySn     =   $shipOrderInfo['countrySn']; //国家简称
    $countryCn     =   self::get_countryNameCn($countryname); //根据国家英文名获取国家中文名
	$zip           =   $shipOrderInfo['zipCode'];
	$tel1          =   !$shipOrderInfo['landline'] ? "" : str_replace('-', '', $shipOrderInfo['landline']);
	$tel           =   $tel1 ? $tel1 : ($shipOrderInfo['phone'] ? $shipOrderInfo['phone'] : "");

	/*if($ebay_carrier=='中国邮政挂号' && $ebay_tracknumber == ''){
		exit("订单 {$ebay_id} 没有跟踪号");
	}*/
	$ebay_print_num    =   ($transportId == '2') ? $ebay_tracknumber : $ebay_id;
	$all_sku_info      =   self::get_all_sku_info($ebay_id);
	$rtnProAddress     =   self::get_retAndProAdress($ebay_id); //获取退件地址及发货地址
	$register          =   ($transportId == '2') ? true : false;
	$addressline = "<strong>".$cname."</strong>";
	if($street2 == ''){
		$addressline .= "<br>".$street1."<br>".$city.", ".$state."<br>".$countryname."&nbsp;&nbsp;&nbsp;&nbsp;".$zip;
	}else{
		$addressline .= "<br>".$street1."<br>".$street2."<br>".$city.", ".$state."<br>".$countryname."&nbsp;&nbsp;&nbsp;&nbsp;".$zip;
	}
	
	
?>
<div class="main chinapost">
	<div>
        <div style="padding:2px; line-height:15px; width:100%;">
            <div style="float:left; text-align:center; font-size:11px;margin-top:2px;width:121px;">
                <img src="/images/chinapost_logo.jpg"/>
                <p>Small Packet BY AIR</p>
                <p style="width:16px;padding:3px;border:1px solid #000;display:inline-block; margin:0; line-height:10px;"><?php echo $countrySn;?></p>
            </div>
            <?php if(!$register):?>
            <div style="float:left;text-align:center;font-size:11px;font-weight:bold;margin-top:15px; margin-left:3px">
            	<p>untracked</p>
            	<p>平小包</p>
            </div>
            <?php endif;?>
            <div style="float:right; text-align:center; font-size:10px;width:<?php echo !$register ? 160 : 225;?>px;">
                <div class="print_image" image_left='<?php echo !$register ? 215 : 150;?>' image_top='12' image_type='jpg' style="width:<?php echo !$register ? 160 : 225;?>px;height:50px;" data="<?php echo base64_encode(file_get_contents(WEB_URL.'barcode128.class.php?data='.$ebay_print_num))?>">                
                </div>
                <!--<img class="barcode" src="./barcode128.class.php?data=" />-->
                <p> 
                	<span><?php echo $ebay_print_num; ?></span>
                </p>
            </div>
            <div style="clear:both;">
            </div>
        </div>
        <div style="border-top:1px solid #000;">
            <div style="float:left;font-size:11px;width:140px;;word-break:break-all;word-wrap:break-word;">
                <div style="padding:5px;min-height:95px;">
                    <p>
                    <?php                  
                    	echo $rtnProAddress['fromAddress'];      
                	?>      
                	</p>
                    <p style="font-weight:bold;">phone:86-0755-89619601</p>
                </div>
                <div style="border-top:1px solid #000;padding:2px;font-size:11px;">
                    <p><?php echo $rtnProAddress['retUnit'];?></p>
                </div>
            </div>
            <div style="padding:2px;float:right;font-size:12px;width:200px;;word-break:break-all;word-wrap:break-word; min-height:120px; border-left:1px solid #000;">
                <p>Ship To：</p>
                <p><?php echo $addressline;?></p>
                <p>Phone:<?php echo  $tel==''?'--':$tel;?><span style="padding-left:6px;"><strong><?php echo $countryCn;?></strong></span></p>
            </div>
            <div style="clear:both;">
            </div>
        </div>
        <div style="border-top:1px solid #000;font-size:11px;padding:2px">
        	<?php echo $rtnProAddress['proCustomer'];?>
        </div>
        <div>
        	<table class="tab" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;font-size:10px;">
        		<tr>
        			<td style="border-left: none;" rowspan="2">
        				<p>邮件种类</p>
        				<p>gatrgor of item</p>
        				<p>请在当前内容打"X"</p>
        			</td>
        			<td>
        			
        			</td>
        			<td>
        				<p>礼品</p>
        				<p>Gift</p>
        			</td>
        			<td>
						X
        			</td>
        			<td>
        				<p>商品货样</p>
        				<p>CORMARCI SAMPLE</p>
        			</td>
        		</tr>
        		<tr>
        			<td>
        	
        			</td>
        			<td>
        				<p>文件</p>
        				<p>Documents</p>
        			</td>
        			<td>

        			</td>
        			<td>
        				<p>其它</p>
        				<p>Other</p>
        			</td>
        		</tr>
        	</table>
        </div>
        <div>
            <table cellpadding="0" cellspacing="0" width="100%" style="border-top:1px solid #000;border-collapse:collapse;font-size:10px;">
                <thead style="font-weight:bold;">
                    <td style="border-left: none;">
                        内件详细名称和数量<br/>Quantity and detailed description of contents
                    </td>
                    <td>
                        重量(千克)<br/>Weight(kg)
                    </td>
                    <td style="border-right: none;">
                       	价值<br/>Value
                    </td>
                </thead>
                <?php 
                foreach($all_sku_info as $sku=>$gl_value){
                    $spu    =   $gl_value['spu'];
                    $hsInfo =   self::get_hsInfo(array($spu));
					if(empty($hsInfo[$spu]['customsNameEN'])) {
                		$title  = $gl_value['ebay_itemtitle'];
                	}else {
	                	$title  = $hsInfo[$spu]['customsNameEN'];
                	}
                    break; //只获取一个料号信息
                }?>
                <tbody>
                    <td style="border-left: none;">
                        <?php echo $title;?>
                    </td>
                    <td>
                        <?php echo $orderweight;?>
                    </td>
                    <td style="border-right: none;">
                        <?php echo $ebay_total;?>
                    </td>
                </tbody>
                <tfoot>
	                <tr>
		                <td style="border-left: none;">
		                                       协调系统税则号列和货物原产国(只对商品邮件填写)<br>HS tariff number and country of origin of goods(For commercial items only)
		                </td>
		                <td>
		                	总重量(千克)<br/>Total Weight(kg)
		                </td>
		                <td style="border-right: none;">
		                	总价值<br/>Total Value
		                </td>
	                </tr>
	                <tr>
	                    <td style="border-left: none;">
	                        Total Gross Weight（Kg）
	                    </td>
	                    <td>
	                    	<?php echo $orderweight;?>
	                    </td>
	                    <td style="border-right: none;">
	                      	<?php echo $ebay_total;?>
	                    </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div style="font-size:10px;width: 100%;">
            <div style="width: 100%;">
            	我保证上述申报准确无误，本函件内未装寄法律或邮政和海关规章禁止邮寄的任何危险物品<br/>
                I,the undersigned,certify that particulars given in this declaration are correct and that this item dose not contain any dangerous article or artices prohibited by legislation or by postal or customs regulations
            </div>
            <div style="height:35px;width:65%;float:left;">
                <span style="float: left;">寄件人签字Sender's Signiture:</span>
                <img  style="float: left;margin-left:5px;height:100%;" src="/images/new_dhl_signature.jpg"/>
            </div>
            <span style="float: left;margin-left:5px;margin-top: 10px;width:30%;">CN22</span>
        </div>
        <div style="font-size:10px;border-top:1px solid #000;overflow: hidden;zoom: 1;width:100%;">
        	<div>
        		<strong><?php echo 'Total:' .$all_sku_info['totalqty'] . ($transportId == '2' ? '('.$ebay_id.')' : '' );?></strong>
        	</div>
            <?php
			foreach($all_sku_info as $sku=>$gl_value){
            ?>
            <span style="width:170px; float:left;"><?php echo $gl_value['info'].$gl_value['category']; ?></span>
            <?php
        	}
            ?>
             <div style="clear: both;"></div>
        </div>
    </div>
</div>
<?php
}
?>
</body>
</html>