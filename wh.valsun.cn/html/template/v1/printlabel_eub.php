<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>EUB</title>
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

if(in_array($transportId, array(6))){
 	
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
    $all_sku_info  =   self::get_all_sku_info($ebay_id);
    $totalqty      =   $all_sku_info['totalqty'];
    unset($all_sku_info['totalqty']);
    $tnum          =   count($all_sku_info);
    $height        =   $tnum > 1 ? intval(100/$tnum) : 100;
    
	$zip0          =   explode("-",$zip);
    $zip           =   $zip0[0];		
	$isd           =   intval(substr($zip,0,2));
	if($isd >= 0 && $isd <= 34){
		$isd  =   1;
	}else if($isd >= 35 && $isd <= 74){
		$isd  =   '3';	
	}else if($isd >= 75 && $isd <= 93){
		$isd  =   '4';	
	}else if($isd >= 94 && $isd <= 99){
		$isd  =   '2';
	}else{
		$isd  =   '1';
	}
	
    $rr         =   self::get_EUBReturnAdress($shipOrderInfo['accountId']); //根据帐号获取回邮地址
    //var_dump($rr);exit;
    $dname      =   $rr['pname'];
    $dstreet    =   $rr['dstreet'];
    $dcity      =   $rr['dcity'];
    $dprovince  =   $rr['dprovince'];
    $dzip       =   $rr['dzip'];
    $dtel       =   $rr['dtel'];
    		
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
	if($street2 == ''){		
		$addressline  =   "<strong>".$cname."</strong><br>".$street1."<br>".$city." ".$state."<br>".$countryname." ".$zip;
	}else{
		$addressline  =   "<strong>".$cname."</strong><br>".$street1." ".$street2."<br>".$city." ".$state."<br>".$countryname." ".$zip;
	}
?>
<table  border="0" cellpadding="0" cellspacing="0" style="border:#000000 1px solid; width:350px; height:350px;">
  <tr>
	<td>
        <table width="100%" border="0" cellspacing="0" cellpadding="0" style=" margin-left:5px">
    	  <tr>
    		<td width="20%" style=" margin-right:120px;">
                <table width="60" height="62" border="0" cellpadding="0" cellspacing="0" style="border:2px solid #000; text-align:center; margin-top:0px;">
        		  <tr>
        			<td width="60" height="60" >&nbsp;<font style="font-family:Arial; font-size:70px; line-height:68px;"><strong>F</strong></font></td>
        		  </tr>
        		</table>
            </td>
    		<td width="54%" align="center"><table width="86%" border="0" cellspacing="0" cellpadding="0">
    		  <tr>
    			<td align="center" ><img src="/images/01.jpg" width="100" height="25" style="margin-top:5px;"/></td>
    		  </tr>
    		  <tr>
    			<td align="center"></td>
    		  </tr>
    		  <tr>
    			<td align="center" style="padding-top:6px;"><img src="/images/02.jpg" width="170" height="45" /></td>
    		  </tr>
    		</table></td>
    		<td width="26%"><table width="100%" border="0" cellspacing="0" cellpadding="0">
    		  <tr>
    			<td align="center"><table width="90%" border="0" align="left" cellpadding="0" cellspacing="0" style="border:2px solid #000; text-align:center; margin-top:5px; margin-right:5px">
    			  <tr>
    				<td width="47" height="45" align="left"><span style="font-family:Arial, Helvetica, sans-serif; font-size:10px; line-height:13px"> 
    				  &nbsp;&nbsp;&nbsp;&nbsp;Aimail<br/>
    				  &nbsp;&nbsp;&nbsp;&nbsp;Postage&nbsp;Paid<br/>
    				  &nbsp;&nbsp;&nbsp;&nbsp;China&nbsp;Post</span></td>
    			  </tr>
    			</table></td>
    		  </tr>
    		  <tr>
    			<td align="center"><font style="font-family:Arial; font-size:16px"><?php echo $isd;?></font>&nbsp;</td>
    		  </tr>
    		</table></td>
    	  </tr>
    	  <tr>
    		<td height="7" colspan="3" valign="top" style=" margin-right:120px"><span style="font-family:Arial, Helvetica, sans-serif; font-size:9px">From:</span></td>
    	  </tr>
	</table>
	<div style="font-family:Arial, Helvetica, sans-serif; font-size:7px"></div>
    </td>
  </tr>
  <tr>
	<td height="" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="2" style=" border-bottom:#000 1px solid; border-top:#000 1px solid">
	  <tr>
		<td width="85%" valign="top" style="border-right:#000 1px solid">
            <div style="width:100%;font-family:Arial, Helvetica, sans-serif; font-size:10px; margin-left:5px; line-height:11.6px;">
                  <?php echo $dname;?><br />
				  <?php echo $dstreet;?><br />
				  <?php echo $dcity;?>&nbsp; <?php echo $dprovince;?><br />
		          CHINA 363000<?php //echo $dzip;?>
            </div>
        </td>
		<td width="15%" rowspan="2" valign="top" style="border-left:#000 1px solid;">
            <table width="100%" border="0" cellspacing="3" cellpadding="0">
    		  <tr>
    			<td align="center" ><table width="100%" border="0" cellspacing="0" cellpadding="0">
    			  <tr>
    				<td align="center">
                        <div class="print_image" image_top='105' image_left='252' style="margin-top:3px;width:110px;height:50px;" data='<?php echo base64_encode(file_get_contents(WEB_URL.'barcode128.class.php?data=420'.$zip));?>'>
                        <!--<img src="./barcode128.class.php?data=<?php echo '420'.$zip;?>" width="110" height="50" />-->
                        </div>
                    </td>
    			  </tr>
    			  <tr>
    				<td align="center" valign="bottom"><div style="font-size:13px; margin-top:0px;"><strong>ZIP <?php echo $zip;?></strong></div></td>
    			  </tr>
    			</table></td>
    		  </tr>
    		</table>
        </td>
	  </tr>
	  <tr>
		<td height="20" valign="top" style="border-right:#000 1px solid">
            <div style="width:100%; font-family:Arial, Helvetica, sans-serif; font-size:7px; margin-top:0px ;margin-left:5px; vertical-align:bottom; line-height:6px;">
                Customs information avaliable on attached CN22.<br />
                USPS Personnel Scan barcode below for delivery event information
            </div>
        </td>
	  </tr>
	</table></td>
  </tr>
  <tr>
	<td height="23" valign="top"><table width="100%" height="60" border="0" cellpadding="0" cellspacing="0">
	  <tr>
		<td width="15%" height="60" style=" border-right: 1px solid #000">
            <div style="font-family:Arial, Helvetica, sans-serif; font-size:18px; margin-left:12px">
                To:
            </div>
        </td>
		<td width="85%" valign="top">
            <div style="font-family:Arial; font-size:12px; line-height:12px; padding-top:4px;margin-left:2px;">
                <?php echo $addressline; ?>
            </div>
        </td>
	  </tr>
	</table>
    </td>
  </tr>
  <tr>
	<td valign="bottom" style="border-bottom:0px"><table width="100%" border="0" cellspacing="0" cellpadding="0" style=" border-bottom:#000 5px solid; border-top:#000 5px solid">
	  <tr>
		<td height="80" valign="top" style="border-right:#000 1px solid; font-size: 9px;"><table width="100%" border="0" cellspacing="2" cellpadding="0">
		  <tr>
			<td height="20" align="center" valign="bottom"><span style=" font-family: Arial, Helvetica, sans-serif; font-size:13px;margin-top:1px;"><strong>USPS DELIVERY CONFIRMATION</strong></span>&reg;</td>
		  </tr>
		  <tr>
			<td align="center">
            <div class="print_image" image_top='270' image_left='60' style="margin-top:3px;width:280px;height:60px;" data='<?php echo base64_encode(file_get_contents(WEB_URL.'barcode128.class.php?data='.$ebay_tracknumber));?>'>
                <!--<img src="./barcode128.class.php?data=<?php echo $ebay_tracknumber;?>" width="280" height="70" />-->
            </div>
            </td>
		  </tr>
		  <tr>
			<td align="center" valign="top"><div style="font-size:13px; line-height:12px;"><strong><?php echo $ebay_tracknumber;?></strong></div></td>
		  </tr>
		</table></td>
	  </tr>
	</table></td>
  </tr>
</table>
<div style="page-break-after:always;">&nbsp;</div>
<table border="0" cellpadding="0" cellspacing="0" style="border:#000000 1px solid; width:350px; height:350px; font-size: 9px; font-family: Arial, Helvetica, sans-serif;">
 <tr>
	<td height="31"><div style="font-family:Arial, Helvetica, sans-serif; font-size:7px">
  <table width="100%" border="0" cellspacing="1" cellpadding="3">
	<tr>
	  <td width="50%" valign="top"><table width="100%" height="65" border="0" cellpadding="0" cellspacing="0" style="">
		<tr>
		  <td colspan="2" valign="top"><img src="/images/01.jpg" alt="" width="90" height="24" /></td>
		</tr>
		<tr>
		  <td width="55%" height="30" valign="bottom"><div style="font-family:Arial; font-size:8px; line-height:10px;">IMPORTANT:<br/>
			The item/parcel may be<br />
			opened officially.<br />
			Please print in English<br />
		  </div></td>
		  <td width="45%"><table width="36" height="32" border="0" align="center" cellpadding="0" cellspacing="0" style="border:1px solid #000; text-align:center; margin-right:">
			<tr>
			  <td width="85" height="20" ><font style="font-family:Arial; font-size:20px"><?php echo $isd;?></font>&nbsp;</td>
			</tr>
		  </table></td>
		</tr>
	  </table></td>
	  <td width="47%"><table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
		  <td align="center" valign="top">
          <div class="print_image" image_top='392' image_left='185' style="margin-top:3px;width:180px;height:38px;" data='<?php echo base64_encode(file_get_contents(WEB_URL.'barcode128.class.php?data='.$ebay_tracknumber));?>'>
            <!--<img src="./barcode128.class.php?data=<?php echo $ebay_tracknumber;?>" alt="" width="180" height="38" />-->
          </div>
          </td>
		</tr>
		<tr>
		  <td align="center" valign="top"><div style="font-size:11px"><strong><?php echo $ebay_tracknumber;?></strong></div></td>
		</tr>
	  </table></td>
	</tr>
  </table>
</div></td>
 </tr>
 <tr>
   <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
	<td width="52%" valign="top" style="border-bottom: 1px solid #000; border-right: 1px #000 solid"><div style="font-family:Arial; font-size:9px; padding-left:6px;"> FROM:<?php echo $dname;?><br />
			<?php echo $dstreet;?><br />
			<?php echo $dcity;?>&nbsp; <?php echo $dprovince;?><br />
	  CHINA 363000<?php //echo $dzip;?> <br/>
	  <div style="height:5px;"></div>
	  PHONE:<?php echo $dtel;?></div></td>
	<td width="48%" rowspan="2" valign="top" style="border-top:#000 solid 1px"><div style=" font-size:11px">SHIP TO: <?php echo $addressline; ?></div></td>
  </tr>
  <tr >
	<td style="border-bottom: 1px solid #000; border-right:#000 solid 1px"><div style=" font-size:9px; padding-left:5px;">Fees(US $):</div></td>
  </tr>
  <tr >
	<td height="14" style="border-bottom: 1px solid #000; border-right:#000 solid 1px"><div style="font-family:Arial; font-size:9px; padding-left:5px;">Certificate No.</div></td>
	<td style="border-bottom: 1px solid #000"><div style=" font-size:10px">PHONE: <?php echo $tel;?></div></td>
  </tr>
  <tr >
	<td height="16" colspan="2" style="border-bottom: 1px solid #000; border-right:#000 solid 1px"><table width="100%" border="0" cellspacing="0" cellpadding="0">
	  <tr>
		<td width="3%" align="center"  style="border-bottom: 1px solid #000; border-right:#000 1px solid"><span class="STYLE2">No</span></td>
		<td width="5%" align="center"  style="border-bottom: 1px solid #000; border-right:#000 1px solid"><span class="STYLE2">Qty</span></td>
		<td width="64%" height="20" align="center"  style="border-bottom: 1px solid #000; border-right:#000 1px solid"><span class="STYLE2">Description of Contents</span></td>
		<td width="9%" align="center"  style="border-bottom: 1px solid #000; border-right:#000 1px solid"><span class="STYLE2">Kg.</span></td>
		<td width="9%" align="center"  style="border-bottom: 1px solid #000; border-right:#000 1px solid"><span class="STYLE2">Val（us$） </span></td>
		<td width="10%" align="center"  style="border-bottom: 1px solid #000; "><span class="STYLE2">Goods Origin</span></td>
	  </tr>
	   <?php
            $i  =   1;
            $totalweight    =   0;
            $total          =   0;
            foreach($all_sku_info as $sku=>$skuInfo){
                $price      =   rand(300, 600)/100;
                $total      +=  $price;
                $goods_count    =   $skuInfo['amount'];
                $goods_weight   =   $skuInfo['goodsWeight']*$goods_count;
                $totalweight    +=  $goods_weight;
                $ebay_packingmaterial   =   CommonModel::getMaterInfoById($skuInfo['pmId']);
        ?>
	    <tr>
        <td align="center" valign="top" style="border-right:#000 1px solid; border-bottom:#000 1px solid; font-size:9px;"><?php echo $i;?>&nbsp;</td>
        <td align="center" valign="top" style="border-right:#000 1px solid; border-bottom:#000 1px solid;font-size:9px; "><?php echo $goods_count?>&nbsp;</td>
        <td height="<?php echo $height?>" align="left" valign="top" style="border-bottom:#000 1px solid;">
            <div style=" font-size:8px;color#000;">
                <strong><?php echo $skuInfo['itemTitle'].'  '.$skuInfo['category']?>
                仓:<span style='font-size:11px;'><?php echo $skuInfo['pName']?></span>
                sku:<span style='font-size:11px;'><?php echo $sku?></span>
                # 包: <?php echo $ebay_packingmaterial.' '.$skuInfo['packStatus']?>
                </strong>
            </div>
        </td>
        <td align="center" valign="top" style=" border-right:#000 1px solid;border-bottom:#000 1px solid;border-left:#000 1px solid; font-size:10px; "><?php echo $goods_weight?>&nbsp;</td>
        <td align="center" valign="top" style= "border-right:#000 1px solid; border-bottom:#000 1px solid; font-size:10px;"><?php echo $price?>&nbsp;</td>
        <td align="center" valign="top" style="font-size:10px; border-top:#000 1px solid; border-bottom:#000 1px solid;">China&nbsp;</td>
        </tr>   
	   <?php 
        $i++;
       }?>
	   <tr>
		<td height="18" align="center"  style="border-right:#000 1px solid;  font-size:8px;">&nbsp;</td>
		<td align="center"  style="border-right:#000 1px solid; font-size:9px; "><?php echo $totalqty;?>&nbsp;</td>
		<td align="left"  style=" "><div style=" font-size:9px">Total Gross Weight (Kg.):</div></td>
		<td align="center"  style=" border-right:#000 1px solid; font-size:9px; border-left:#000 1px solid; "><?php echo $totalweight;?>&nbsp;</td>
		<td align="center"  style= "border-right:#000 1px solid; font-size:9px;"><?php echo $total;?>&nbsp;</td>
		<td align="center"  style="font-size:8px;">&nbsp;</td>
	  </tr>
	</table></td>
  </tr>
</table></td>
 </tr>
    <tr>
    <td height="13" valign="bottom"><div style="font-family:Arial; font-size:6px; padding-left:5px;margin-top:3px;">I certify the particulars given in this customs declaration are correct. This item does not contain any dangerous article, or articles prohibited by
    legislation or by postal or customs regulations. I have met all applicable export filing requirements under the Foreign Trade Regulations. </div></td>
    </tr>
    <tr>
    <td height="21" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
     <tr>
       <td width="79%" valign="bottom"><div style="font-family:Arial; font-size:8px; padding-left:5px;"><strong>Sender's Signature &amp; Date Signed:</strong></div></td>
       <td width="21%" align="right" valign="top"><div style="font-family:Arial; font-size:16px; text-align:center;">CN22</div></td>
     </tr>
    </table></td>
    </tr>
</table>
<?php
}
?>
</body>
</html>