<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>新加坡DHL GM</title>
<style>
@charset "utf-8";
/* CSS Document */
html{height:100%;}
body {width:100%;margin:auto;padding:0;height:100%; min-width:1200px;}
h1, h2, h3, h4, h5, h6 {margin:0;padding:0;font-size:10px;}
ul, li, dl, dd, dt, p {margin:0;padding:0;}
ul li {list-style:none;}
img {border:none;}
.main{ width: 350px; height:540px; border: 1px solid #000; margin: 2px;padding: 2px;}
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

if(in_array($transportId, array(83, 84))){
 	
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
	$street1       =   @$shipOrderInfo['street'];
	$street2       =   @$shipOrderInfo['address2'] ? @$shipOrderInfo['address2'] : '';
    $street2       .=  $shipOrderInfo['address3'] ? $shipOrderInfo['address3'] : '';
	$city          =   $shipOrderInfo['city'];
	$state         =   $shipOrderInfo['state'];
	$countryname   =   $shipOrderInfo['countryName'];
    $countrySn     =   $shipOrderInfo['countrySn'];
    $ebay_currency =   $shipOrderInfo['currency'];
    $countryCn     =   self::get_countryNameCn($countryname); //根据国家英文名获取国家中文名
	$zip           =   $shipOrderInfo['zipCode'];
	$tel1          =   !$shipOrderInfo['landline'] ? "" : str_replace('-', '', $shipOrderInfo['landline']);
	$tel           =   $tel1 ? $tel1 : ($shipOrderInfo['phone'] ? $shipOrderInfo['phone'] : "");    
    
	if($street2 == ''){
		$addressline  =   $cname."<br>".$street1.",".$city.", ".$state.",".$zip.",".$countryname.'('.$countryCn.')';
	}else{
		$addressline  =   $cname."<br>".$street1.",".$street2.",".$city.", ".$state.",".$zip.",".$countryname.'('.$countryCn.')';
	}

	$tel   =   isset($tel) ? trim($tel) : '';
	if($tel != '' /*&& $tel != 'Invalid Request'*/) {
	   $addressline  .= ',Tel:'.$tel;
	}
	
	//挂号
	$isPlus 	= 'Product: GM PACKET <span style="margin-left:50px;">Service level: Standard</span>';
	$isRegister = false;
    
	if($transportId == '83') {	 //新加坡挂号
		$isPlus 	= 'Product: GM PACKET <B>PLUS</B> STANDARD';
		$isRegister = true;
	}
  
    $all_sku_info   =   self::get_all_sku_info($ebay_id);
    $totalqty   =   $all_sku_info['totalqty'];
    unset($all_sku_info['totalqty']);
     
    $total_value = 0;

?>
<div class="main">
    <div>
        <div class="barcode" style="margin: 0;">
            <p style="margin-bottom: 0px;margin-top: 3px;"><?php echo $ebay_id; ?></p>
            <div class="print_image" image_top='29' image_left='30' image_type='jpg' style="padding-left:5px; margin:0; width:135px; height:75px;" data="<?php echo base64_encode(file_get_contents(WEB_URL.'barcode128.class.php?data='.$ebay_id))?>"></div>
            <!--<img src="./barcode128.class.php?data=<?php echo $ebay_id; ?>" width="140" height="80" />-->             
            <p style="margin-top: 5px;;">Total Qty:<?php echo $totalqty; ?></p>
        </div>
        <div class="tab">
        <?php if (strtolower($countryname) == 'germany') { ?>  
            <img src="/images/new_dhl_Packet_Germany.jpg" width="205" height="120" />
        <?php } else if (strtolower($countryname) == 'italy') { ?>
            <img src="/images/new_dhl_Packet_Italy.jpg" width="205" height="120" />
        <?php } else { ?>
            <div style="width:205px;height: 110px ; border:1px solid #000;font-size: 9px;">
                <div style="border-bottom: 1px solid #000; font-size:12px; font-weight: bold; text-align: center;">PRIORITAIRE</div>
                <div>
                    <div style="float: left; border-right: 1px solid #000; width: 100px; min-height:90px;line-height:16px;">
                        
                        &nbsp;En cas de non  remise
                        
                        &nbsp;prière de retourner à
                        <p style="font-size: 11px;"><strong>
                        &nbsp;Postfach 1100
                        &nbsp;36243 Niederaula
                        &nbsp;ALLEMAGNE
                        </strong>
                        </p>
                        
                    </div>
                    <div style="float: right; width:104px; line-height:17px;text-align: center;">
                        <div style="font-weight: bold;  font-size: 12px;border-bottom: 1px solid #000;">Deutsche Post</div>
                        <div style=" border-bottom: 1px solid #000;">
                            <p><strong>Port payé</strong></p>
                            <p>60544 Frankfurt</p>
                            <p>Allemagne</p>
                        </div>
                        <div>Luftpost/Prioritaire</div>
                    </div>
                    <div style="clear: both;"></div>
                </div>
            </div>
        <?php } ?>            
        </div>
        <div style="clear:both;">
        </div>
    
        <?php if (strtolower($countryname) == 'australia') { ?>
        <div class="zone">
            <div class="zone1">              
            <?php echo $isPlus;?>                  
            </div>
            <div>
                <p class="fontWeight">Deliver To:</p>
                <p style="font-size:15px;"><?php echo $addressline; ?></p>                  
            </div>
        </div>
        <div class="expres">
            <img src="/images/new_singapore_express.jpg" width="80" height="110" />
        </div>
        <?php }  else { ?>
    
        <div class="zone" style="width:100%">
            <div class="zone1">              
            <?php echo $isPlus;?>                  
            </div>
            <div>
                <p class="fontWeight">Deliver To:</p>
                <p style="font-size:12px;"><?php echo $addressline; ?></p>                  
            </div>
        </div>
        <?php } ?>
    
        <div style="clear:both;">
        </div>
        <div class="zone2">
            <div style="float:left;">
                <p class="fontWeight">CUSTOMS DECLAREATION</p>
                <p style="font-size:10px;">Postal administration (May be opened officially)</p>
            </div>
            <div style="float:right;">
                <p class="fontWeight">CN22</p>
                <p style="font-size:10px;">Important</p>
            </div>
            <div style="clear:both;">
            </div>
            <div class="check">
                <label><input type="checkbox"><span style="width: 110px;" >Gift</span></label>
                <label><input type="checkbox"><span>Commercial Sample</span></label>
                <br />
                <label><input type="checkbox"><span style="width: 110px;">Document</span></label>
                <label><input type="checkbox" checked="checked" ><span>Other(Tick as appropriate)</span></label>
            </div>
            <div style="float:left; padding:5px;">
                <p class="fontWeight">Detailed description of contents</p>
                <?php
                foreach($all_sku_info as $sku=>$skuInfo){
					echo "<div style=\"border:1px #000; margin:1px;word-break:break-all;\">";
                        $spu      =   $skuInfo['spu'];
						$hsInfo   =   self::get_hsInfo(array($skuInfo['spu'])); 
                        if (empty($hsInfo[$spu]['customsNameEN'])) {
                            $title  = $skuInfo['itemTitle'];
                        }else{
                            $title  = $hsInfo[$spu]['customsNameEN'];
                        }
                        echo $skuInfo['info'].$title;
					echo "</div>";
                }?>
            </div>
            <div style="float:right;padding:5px;border-left:1px solid #a4a4a4;">
                <p class="fontWeight">Value</p>
                <?php foreach($all_sku_info as $sku){
                	$total_value +=  self::rand_float($sku['itemPrice'],$countryname);                    
                	echo "<div style=\"border:1px #000; margin:1px;word-break:break-all;\">";
                	echo "<p>".$ebay_currency.self::rand_float($sku['itemPrice'],$countryname)."</p>";
                	echo "</div>"; 
                }?>            
            </div>
            <div style="clear:both;">
            </div>
            <div style="border-bottom:1px solid #a4a4a4;font-size:11px;">
                <div style="float:left; border-right:1px solid #a4a4a4;padding:3px; ">
                    Origin:CN
                </div>
                <div style="float:left; border-right:1px solid #a4a4a4;padding:3px;">
                    <p>Total Weight(kg):<?php echo sprintf('%.3f',$orderweight);?></p>
                    <!--p><?php echo $orderweight;?></p-->
                </div>
                <div style="float:left;padding:3px;">
                    <p>Total Value:<?php echo $ebay_currency.sprintf('%.2f',$total_value);?></p>
                    <!--p><?php echo $ebay_currency.$total_value;?></p-->
                </div>
            </div>
            <div style="clear:both;">
            </div>
            <div class="zone3" style="font-size:11px;">
            I, the undersigned, whose name and address are given on the item, certify that the particulars given in this declaration are correct and that this item does not contain dangerous article or articles prohibited by legislation or by postal or customs regulations.
    		</div>
            <div>
                <div style="float:left; font-size:9px;">
                    <p>Date and Senders Signature</p>
                    <p><?php echo date("j-M-Y"); ?>
                </div>
                <div style="float:left; margin-left:5px;">
                    <p style="padding-left: 20px;">0000511066</p>
                    <img src="barcode128.class.php?data=0000511066" width="105" height="35" />
                </div>
                <div style="float:right">
                    <img src="/images/new_dhl_signature.jpg" width="115"/>
                </div>
            </div>
            <div style="clear:both;">
            </div>
        </div>
        <?php if($isRegister) { ?>
        <div style="padding:10px;">
            <div>
                <div style="float:left;">
                    <p>DHL Internal Use Only</p>
                    <p>DGM#&nbsp;&nbsp;&nbsp;<?php echo $ebay_tracknumber; ?></p>
                </div>
                <div style="float:right">                      
                    <img src="./barcode128.class.php?data=<?php echo $ebay_tracknumber; ?>" width="350" height="40" />
                    <!--p><?php echo $ebay_tracknumber; ?></p-->
                </div>
            </div>
            <div style="clear:both;">
            </div>
        </div>
        <?php } ?>
    </div>
</div>
<?php
}
?>
</body>
</html>