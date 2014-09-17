<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Global Mail打印</title>
<style>
@charset "utf-8";
div.gm { font-size:9px; color:#000000;font-family: Arial, Helvetica, sans-serif;width:350px;}
.font18{ font-size:9px; color:#000000; }
.font16{ font-size:14px;}
.box_bottom{ border-bottom:1px dashed #000;}
.box1{ border-top:1px solid #000;}
.box2{ border-left:1px dashed #000; border-right:1px dashed #000;}
.font12{ font-size:10px; font-weight:bold;}
.font14{ font-size:12px; font-weight:bold;}
.box3{ border-bottom:1px dashed #000; border-top:1px dashed #000;}
.font70{ font-size:80px;}
</style>
</head>

<div class="gm" id="page1">
<?php
//@session_start();
require_once WEB_PATH."framework.php";
Core::getInstance();
error_reporting(-1);
global $dbConn;

$shipOrderId    =   intval($_SESSION['shipOrderId']);
$transport      =   trim($_SESSION['transport']);

$shipOrderInfo  =   WhShippingOrderModel::get_order_info_union_table($shipOrderId); //获取发货单基本信息

$transportId    =   $shipOrderInfo['transportId']; //运输方式ID

if(in_array($transportId, array(53))){
 	
	$ebay_id       =   $shipOrderInfo['id'];
	$ebay_tracknumber  =   $shipOrderInfo['tracknumber'];
	$ebay_userid   =   $shipOrderInfo['platformUsername'];
	$ebay_total    =   $shipOrderInfo['total'];
//	$ebay_total    =   $ebay_total >= 10 ? 10 : ($ebay_total == 0 ? rand(5,10) : $ebay_total);
	$ebay_usermail =   $shipOrderInfo['email'];
	$packingtype   =   $shipOrderInfo['packingtype'];
    $pmName        =   CommonModel::getMaterInfoById($shipOrderInfo['pmId']);
    
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
    //$ebay_print_num=   ($transportId == 79) ? $ebay_tracknumber : $ebay_id;
    $all_sku_info  =   self::get_all_sku_info($ebay_id); //获取订单料号明细
    $totalqty      =   $all_sku_info['totalqty']; //料号总数
    unset($all_sku_info['totalqty']);
    $skuArr        =   $all_sku_info;
	//if(empty($ebay_userid)){
//		echo "订单 $ebay_id 没有用户id信息,请联系销售人员!<br>";
//		continue;
//	}
//	//add Herman.Xi 无地址不打印 2012-11-25
//	if(!tep_not_null($countryname)){
//		continue;
//	}
	//地址信息
	if($street2 == ''){
		$addressline  =   "Send To:<br>".$cname."<br>".$street1."<br>".$city.", ".$state."<br>".$zip."<br>".$countryname;
	}else{
		$addressline  =   "Send To:<br>".$cname."<br>".$street1."<br>".$street2."<br>".$city.", ".$state."<br>".$zip."<br>".$countryname;
	}
	$salesaccountinfo  =   CommonModel::getAccountNameById($orinfval['accountId']);
	$appname           =   empty($salesaccountinfo) ?  '' : $salesaccountinfo['appname'];
	
	//包裹重量
	//$gg		= "select * from ebay_packingmaterial where model ='$packingtype'";
//	$gg		= $dbcon->execute($gg);
//	$gg		= $dbcon->getResultArray($gg);
//	$weight	= $gg[0]['weight'];
    /** 回邮地址**/
    if($countryCn != '德国'){
        $postReturnAddress  =   '<table width="275" border="1" align="center" cellpadding="0" bgcolor="#000000" style="border-collapse:collapse ">
                				<tr>
                				  <td colspan="2" valign="top" bgcolor="#FFFFFF">
                					<span class="font18"><strong>PRIORITAIRE</strong></span><br/>
                				  </td>
                				</tr>
                				<tr>
                				  <td align="center" valign="top" bgcolor="#FFFFFF">
                					<strong class="font18">
                					En cas de non remise<br/>
                					Prière de retourner à<br/>
                					</strong>
                					<span class="font18">
                					Postfash 2007<br/>
                					36243 Niederaula<br/>
                					ALLEMAGNE<br/>
                					</span>
                				 </td>
                				  <td align="center" valign="top" bgcolor="#FFFFFF">
                				  <span class="font12"><strong> Deutsche Post</strong></span><br />
                				  <span class="font18">
                					 Port payé<br/>
                					60544 Frankfurt<br/>
                					(2378)
                				  </span>
                				  </td>
                				</tr>
                			  </table>';
    }else{
        $postReturnAddress  =   '<table width="275" border="1" align="center" cellpadding="0" bgcolor="#000000" style="border-collapse:collapse ">
                				<tr>
                				  <td rowspan="2" valign="top" bgcolor="#FFFFFF">
                					Wenn  unzustellbar,<br/> 
                					zurück  an&nbsp;<br/>
                					<span class="font18">Postfach 2007  </span><br/>
                					36243 Niederaula
                				  </td>
                				  <td align="center" valign="top" bgcolor="#FFFFFF"><span class="font12"><strong> Deutsche Post</strong></span><br /></td>
                				</tr>
                				<tr>
                				  <td align="center" valign="top" bgcolor="#FFFFFF"><strong class="font18">
                					Entgelt  bezahlt</strong><br/>
                					60544 Frankfurt<br/>
                					(2378)
                				  </td>
                				</tr>
                			  </table>';
    }
    
	
    $title_nums = 0;
	$totalweight = 0;
	//$totalweight2 = 0;
	$good_info = '';
	$goods_title = array();	
	$detailnum = count($all_sku_info);
    foreach($all_sku_info as $sku=>$skuInfo){
        $ebay_amount    =   $skuInfo['amount'];
        $title_nums     =   count($goods_title);
		if(($detailnum > 3 && $title_nums < 2) || ($detailnum <= 3 && $title_nums == 0)){
			$goods_title[]   =   !empty($skuInfo['itemTitle']) ? ($title_nums+1).' '.$skuInfo['itemTitle'] : '';
		}
        $good_info .= "<span class=\"font14\">".$skuInfo['info']."</span><br />";
    }
    $total_info = "<span class=\"font18\">"."Total Qty:{$totalqty}<br></span>";
	
	//重量等级
	if($orderweight<0.1){
		$weightmark = 'P';
		$ordershipfee2 = rand(100, 500)/100;
	}else if ($orderweight<0.5){
		$weightmark = 'G';
		$ordershipfee2 = rand(501, 1000)/100;
	}else if ($orderweight<2){
		$weightmark = 'E';
		$ordershipfee2 = rand(1001, 1500)/100;
	}else{
		$weightmark = '超重';
	}
	$ordershipfee2 = number_format($ordershipfee2/$detailnum, 2);
	$title_info = implode('<br />', $goods_title);
  ?>
  
 <table width="345" border="0" align="center" cellpadding="0" cellspacing="0" style="font-size:7px; color:#000000;font-family: Arial, Helvetica, sans-serif;">
  <tr>
	<td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
	  <tr>
		<td><table width="345" border="0" cellspacing="0" cellpadding="0" class="box_bottom">
		  <tr>
			<td width="85%"><span class="font18">ZOLLINHALTSERKLÄRUNG</span> Kann amtlich geöffnet werden.<br />
			  <span class="font18">DÉCLARATION EN DOUANE </span>Peut être ouvert d’office</td>
			<td width="15%" class="font16"><strong>CN 22</strong></td>
		  </tr>
		</table></td>
	  </tr>
	  <tr>
		<td><table width="345" border="0" cellspacing="0" cellpadding="0">
		  <tr>
			<td width="46%"><p>Postverwaltung<br />
			  Administration des postes<br/>
			  <img src="<?php echo WEB_URL?>images/logo.jpg" width="107" height="18" /></p></td>
			<td width="11%">&nbsp;</td>
			<td width="43%"><span class="font16">Wichtig! Important!</span><br />
			  Hinweise auf der Rückseite<br />
			  Voir instructions au verso</td>
		  </tr>
		</table></td>
	  </tr>
	  <tr>
		<td><table width="345" border="0" cellspacing="0" cellpadding="0" class="box1">
		  <tr>
			<td width="22%"><table width="88%" border="0" cellspacing="0" cellpadding="0">
			  <tr>
				<td width="34%"><img src="<?php echo WEB_URL?>images/kuan.jpg" width="14" height="12" /></td>
				<td width="66%">Geschenk<br />
				  Cadeau</td>
			  </tr>
			</table></td>
			<td width="28%"><table width="93%" border="0" cellspacing="0" cellpadding="0">
			  <tr>
				<td><img src="<?php echo WEB_URL?>images/kuan.jpg" width="14" height="12" /></td>
				<td>Dokumente<br />
				  Documents</td>
			  </tr>
			</table></td>
			<td width="25%"><table width="100%" border="0" cellspacing="0" cellpadding="0">
			  <tr>
				<td width="26%"><img src="<?php echo WEB_URL?>images/kuan.jpg" width="14" height="12" /></td>
				<td width="74%">Warenmuster<br />
				  Echantillon commercial</td>
			  </tr>
			</table></td>
			<td width="25%"><table width="100%" border="0" cellspacing="0" cellpadding="0">
			  <tr>
				<td><img src="<?php echo WEB_URL?>images/kuan0.jpg" width="14" height="12" /></td>
				<td>Sonstige<br />
				  Autre</td>
			  </tr>
			</table></td>
		  </tr>
		  <tr>
			<td colspan="4">Bitte ein oder mehrere Kästchen ankreuzen.<br/> Coucher la ou les cases appropriées.</td>
			</tr>
		</table></td>
	  </tr>
	  <tr>
		<td><table width="345" border="1" cellpadding="0" cellspacing="0" style= "border-collapse:collapse">
		  <tr>
			<td valign="top" bgcolor="#FFFFFF">Anzahl und detaillierte Beschreibung des Inhalts (1)<br />
			  Quantité et description detaillée du contenu</td>
			<td valign="top" bgcolor="#FFFFFF">Gewicht (in kg) (2)<br />
			  Poids (en kg)</td>
			<td valign="top" bgcolor="#FFFFFF">Wert (3)<br />
			  Valeur</td>
		  </tr>
		  <tr>
			<td height="50" bgcolor="#FFFFFF"><strong style="font-size:10px"><?php echo $title_info; ?></strong></td>
			<td bgcolor="#FFFFFF"><strong style="font-size:10px"><?php echo $orderweight;?>KG</strong></td>
			<td bgcolor="#FFFFFF"><strong style="font-size:10px">EUR€ <?php echo $ordershipfee2; ?></strong></td>
		  </tr>
		  <tr>
			<td valign="top" bgcolor="#FFFFFF">Nur für Handelswaren<br />
			  Pour les envois commerciaux seulement<br />
			  (Falls bekannt) Zolltarifnr. nach dem HS (4) und<br />
			  Ursprungsland per Waren (5)<br />
			  N° tarifaire du SH et pays d’origine des marchandises<br />
			  (si connus)</td>
			<td valign="top" bgcolor="#FFFFFF">Gesamtgewicht<br />
			  (in kg) (6)<br />
			  Poids total (en kg)</td>
			<td valign="top" bgcolor="#FFFFFF">Gesamtwert (7)<br />
			  Valeur totale</td>
		  </tr>
		  <tr>
			<td align="center" bgcolor="#FFFFFF">Hong kong</td>
			<td bgcolor="#FFFFFF">&nbsp;</td>
			<td bgcolor="#FFFFFF">&nbsp;</td>
		  </tr>
		</table></td>
	  </tr>
	  <tr>
		<td>
		  <table width="345" border="0" cellspacing="1" cellpadding="0" style='font-size:8px;'>
		  <tr>
			<td>Ich, der/die Unterzeichnende, dessen/deren Name und Adresse auf der Sendung angeführt sind, bestätige,dass die in der vorliegenden Zollinhaltserklärung angegebenen Daten korrekt sind und dass diese Sendung
				keine gefährlichen, gesetzlich oder auf Grund postalischer oder zollrechtlicher Regelungen verbotenen
				Gegenstände enthält. Ich übergebe insbesondere keine Güter, deren Versand, Beförderung oder Lagerung
				gemäß den AGB von Deutsche Post ausgeschlossen ist.
			</td>
		  </tr>
		  <tr>
			<td>Je, soussigné dont le nom et l’adresse figurent sur l’envoi, certifie que les renseignements donnés dansla présente déclaration sont exacts et que cet envoi ne contient aucun objet dangereux ou interdit par la
				législation ou la réglementation postale ou l’entreposage est exclu par les Conditions générales de<br />
				Deutsche Post.
			</td>
		  </tr>
		  <tr>
			<td>Datum und Unterschrift des Absenders (8)/Date et signature de l’expéditeur:</td>
		  </tr>
		  </table>
		</td>
	  </tr>
	</table></td>
  </tr>
</table>
<div style="page-break-after:always;">&nbsp;</div>
<table width="345" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
	<td colspan="2" align="center">
	  <?php echo $postReturnAddress;?>
	</td>
  </tr>
  <tr>
	<td>
	  <table width="350" border="0" cellspacing="0" cellpadding="0">
		<tr>
		  <td rowspan="2" valign="top">
			<table style="width: 150px;" border="0" align="center" cellpadding="3" cellspacing="0" bgcolor="#e1e1e1" class="box2 font12">
			  <tr>
				<td valign="top" bgcolor="#FFFFFF">
				  <?php echo $good_info; ?>
				  <?php echo $total_info; ?> 
				</td>
			  </tr>
			</table>
		  </td>
		  <td>
			<table width="95%" border="0" align="center" cellpadding="3" cellspacing="0">
			  <tr>
				<td width="52%" height="116"><?php echo $ebay_tracknumber;?> <br />
				  Global Mail <br />
				  ZL: <?php echo $orderweight; ?> <br />
				  YF: <?php echo $ordershipfee;?> <br />
				  <?php echo $pmName;?>  <?php echo $ebay_total; ?><br/> mj&nbsp;<?php echo $ebay_userid;?>  <br />
				  <span class="font12">Good<br />
				  <?php echo $appname.'<br>1/1';?> </span><br /></td>
				<td width="48%" align="center" class="font70"><?php echo $weightmark ;?></td>
			  </tr>
			</table>
		  </td>
		</tr>
		<tr>
		  <td>
			<table width="95%" border="0" align="center" cellpadding="3" cellspacing="0" class="box3">
			  <tr>
				<td width="53%" height="31">
                    <div class="print_image" image_left='170' image_top='580' image_type='jpg' style="width:150px;height:40px;" data="<?php echo base64_encode(file_get_contents(WEB_URL.'barcode128.class.php?data='.$ebay_id))?>">
                    <!--<img src="./barcode128.class.php?data=<?php echo $ebay_id; ?>" width="150" height="40" />-->
                </td>
				<td width="47%">
				  <span class="font16"><strong>IG</strong></span>&nbsp;&nbsp;&nbsp;&nbsp;
				  <span class="font12"><?php echo $ebay_id; ?></span>
				</td>
			  </tr>
			  <tr>
				<td colspan="2">
				  <div class="font12"><?php echo $addressline;?></div>
				</td>
			  </tr>
			</table>
		  </td>
		</tr>
	  </table>
	</td>
  </tr>
</table>
<?php
}
?>
</div>
</body>
</html>