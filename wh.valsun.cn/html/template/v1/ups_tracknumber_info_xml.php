<?php
/**
 * 功能：UPS美国专线跟踪号信息生成接口(XMl)
 * 版本：1.0
 * 日期：2014/01/16
 * 作者：管拥军
 * add cxy 
 * data 2014-8-20
 */
 
error_reporting(-1);
set_time_limit(0);
require_once WEB_PATH."framework.php";
//Core::getInstance();
global $dbConn;
$userId  = $_SESSION['userId'];
if (empty($userId)) exit("{\"errCode\":\"-1\",\"errMsg\":\"登录超时\"}");
############### 基础信息配置 ######################
$truename = getUserNameById($userId);
//echo $truename;exit;
$orderid_arr	= isset($_POST['ids']) ? trim($_POST['ids']) : '';
//print_r($orderid_arr);exit;
if (empty($orderid_arr)) exit('{"errCode":"-1","errMsg":"没有选择需要导出的订单信息！"}');
$accouts	= "'360beauty','365digital','befashion','befdi','befdimall','bestinthebox','betterdeals255','cafase88','charmday88','choiceroad','cndirect55','cndirect998','dealinthebox','digitalzone88','doeon','dresslink','easebon','easydeal365','easydealhere','easyshopping678','easytrade2099','elerose88','enicer','enjoy24hours','eshop2098','freemart21cn','futurestar99','happydeal88','ishop2099','itshotsale77','keyhere','niceforu365','niceinthebox','starangle88','sunwebhome','sunwebzone','tradekoo','voguebase55','wellchange','work4best','zealdora'";
$country	= "United States";
//$author		= array('vipchen','guanyongjun','孙学轩','陈前');
$today		= date('Y-m-d',time());
$stime		= strtotime($today."-1 day 00:00:01");
$etime		= strtotime($today." 23:59:59");
//$condition	= "1 AND ebay_id IN({$orderids}) AND ebay_carrier='UPS美国专线' ";
//$filename	= "ship_ups_us_".$truename."_".$today."_".time().".xml";
//$shipxml	= WEB_PATH."/html/temp/".$filename;
$shipfrom	= "\n<ShipFrom>											
					<CompanyOrName>Shenzhen Sailvan Network TECHNOLOGY</CompanyOrName>					
					<Attention>Miss Zhang</Attention>					
					<Address1>Yaoan Ind Park No. 53</Address1>					
					<Address2>Xiantian RD Shenzhen China</Address2>					
					<Address3></Address3>					
					<CountryTerritory>CN</CountryTerritory>					
					<PostalCode>518116</PostalCode>					
					<CityOrTown>ShenZhen</CityOrTown>									
					<Telephone>86-075589619635</Telephone>									
					<UpsAccountNumber>R9W668</UpsAccountNumber>								
				</ShipFrom>\n";
############## 基础信息配置END ####################
//if (!in_array($truename,$author)) exit('{"errCode":"-1","errMsg":"您暂无权限使用此功能"}');
$xml	= "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<OpenShipments xmlns=\"x-schema:OpenShipments.xdr\">";
$orderid_arr = explode(',',$orderid_arr);            
foreach ($orderid_arr as $orderid) {
    $res = WhShippingOrderModel::select_shiping_by_id_country($country,$orderid);

	if(empty($res)) continue;	
	//发货信息
	$r_name			= $res['username'];
	$r_name			= str_replace(array("&#039;","&acute;","&amp;","&"),array("'","'"," "," "),$r_name);
	$r_postcode		= $res['zipCode'];
	$r_phone		= $res['phone'];
	$r_country		= 'US';
	$r_province		= strlen(trim($res['state']))==2 ? $res['state'] : $res['ups_code'];
	$r_city			= $res['city'];
	$r_street		= $res['street'];
	if(!empty($res['address2'])) $r_street .= " ".$res['address2'];
	$r_email		= $res['email'];
	//$ordersn		= $res['ebay_ordersn'];
	$orderid		= $res['id'];
	// $tid			= strpos($v['ebay_orderid'],'-') ? substr($v['ebay_orderid'],strpos($v['ebay_orderid'],'-')+1) : '';
	// if (empty($tid)) continue;
	$tid 			= $res['payPalPaymentId'];//付款交易号
	$add1			= substr($r_street,0,35);
	$add2			= substr($r_street,35,70);
	$add3			= substr($r_street,70,105);
	$add1			= str_replace(array("&#039;","&acute;","&amp;","&"),array("'","'"," "," "),$add1);
	$add2			= str_replace(array("&#039;","&acute;","&amp;","&"),array("'","'"," "," "),$add2);
	$add3			= str_replace(array("&#039;","&acute;","&amp;","&"),array("'","'"," "," "),$add3);
	$packagetype	= 'CP';
	$servicelevel	= 'EX';
	$transportation	= 'prepaid';
	$description	= 'Clothing and Accessories';
	$nums			= 1;
	$weight			= round($res['calcWeight'],1);
	$receiver		= "\n<Receiver>						
							<CompanyName>{$r_name}</CompanyName>					
							<ContactPerson>{$r_name}</ContactPerson>					
							<AddressLine1>{$add1}</AddressLine1>					
							<AddressLine2>{$add2}</AddressLine2>					
							<AddressLine3>{$add3}</AddressLine3>					
							<City>{$r_city}</City>					
							<CountryCode>{$r_country}</CountryCode>					
							<PostalCode>{$r_postcode}</PostalCode>					
							<StateOrProvince>{$r_province}</StateOrProvince>								
							<Phone>{$r_phone}</Phone>									
						</Receiver>\n";
	$shipment		= "\n<Shipment>
							<ServiceLevel>{$servicelevel}</ServiceLevel>
							<PackageType>{$packagetype}</PackageType>					
							<NumberOfPackages>{$nums}</NumberOfPackages>
							<ShipmentActualWeight>{$weight}</ShipmentActualWeight>													
							<DescriptionOfGoods>{$description}</DescriptionOfGoods>									
							<Reference1>{$orderid}</Reference1>					
							<Reference2>{$tid}</Reference2>										
							<BillingOption>PP</BillingOption>									
						</Shipment>\n";
	$xml			.= "\n<OpenShipment ShipmentOption=\"\" ProcessStatus=\"\">\n";
	$xml			.= $receiver;
	$xml			.= $shipfrom;
	$xml			.= $shipment;
	$inovice		= "\n<Invoice>
							<ReasonForExport></ReasonForExport>					
							<TermOfSale></TermOfSale>														
							<InvoiceCurrency>USD</InvoiceCurrency>";
	//发票信息
    $detail         = WhShippingOrderdetailModel::getShipDetails($orderid);
     $tPrice = 0;
    if($detail){
        foreach($detail as $values){
            $prices     += $detail['itemPrice']*$detail['amount'];
        }              
    }
    $tPrice		    = $tPrice != 0 ? round(floatval($tPrice),4) : 0;
//	$sql 			= "SELECT SUM(ebay_itemprice*ebay_amount) as tPrice FROM ebay_orderdetail WHERE ebay_ordersn = '{$ordersn}'";
	//$query			= $dbcon->execute($sql);
//	$result 		= $dbcon->fetch_one($query);
//	$tPrice			= isset($result['tPrice']) ? round(floatval($result['tPrice']),4) : 0;
	$nPrice			= rand(140,190);
	$pFlag			= $tPrice>200 ? true : false;
    $result         =  WhShippingOrderdetailModel::select_datail_category($orderid);
//	$sql			= "SELECT b.goods_category,a.sku,a.ebay_itemtitle,a.ebay_amount,a.ebay_itemprice FROM ebay_orderdetail as a 
	//					LEFT JOIN ebay_goods AS b ON a.sku = b.goods_sn
	//					WHERE a.ebay_ordersn = '{$ordersn}'";
	//$query			= $dbcon->execute($sql);
//	$result			= $dbcon->getResultArray($query);
	$lineitem		= "";
	foreach ($result as $val) {
		$category	= $val['goodsCategory'];
		if (substr($category,0,2)=='1-') {
			$enname	= str_replace(array("&#039;","&acute;","&amp;","&"),array("'","'"," "," "),'(cotton:95%,spandex:5%)'.$val['itemTitle']);
		} else {
			$enname	= str_replace(array("&#039;","&acute;","&amp;","&"),array("'","'"," "," "),$val['itemTitle']);
		}
		$count			= $val['amount'];
		$price			= $val['itemPrice'];
		$internalcode	= 'USD';
		$original		= 'CN';
		$measure		= 'PCS';
		$code			= 'USD';
		$hscode			= '';
		//如果描述有中文 add by gyj 2014-04-22 
		// if(preg_match("/[\x{4e00}-\x{9fa5}]+/u",$enname)) {
			// $inovice	= '';
			// break;
		// }
		//如果总价值大于200美金,拆分发票价格
		if ($pFlag) $price	= round($price*$count/$tPrice*$nPrice/$count,2);
		$lineitem			.= "\n<LineItem>					
								<Product>
									<HarmonizedCode></HarmonizedCode>							
									<ProductDescription>{$enname}</ProductDescription>
									<UnitOfMeasure>{$measure}</UnitOfMeasure>			
									<CountryOfOriginCode>{$original}</CountryOfOriginCode>			
									<CurrencyCode>{$internalcode}</CurrencyCode>			
									<UnitPrice>{$price}</UnitPrice>			
								</Product>				
								<NumberOfUnits>{$count}</NumberOfUnits>				
							</LineItem>\n";
		
	}
	$inovice	.= $lineitem;
	$inovice	.= "</Invoice>\n</OpenShipment>\n";
	$xml		.= $inovice;
}
$xml			.= "</OpenShipments>\n";
unset($res);

//$res			= export_xml($shipxml,$xml);
if ($xml) {
    $titlename = date("YmdHis").'.txt';

header("Content-Type: application/octet-stream");    
if (preg_match("/MSIE/", $_SERVER['HTTP_USER_AGENT']) ) {    
	header('Content-Disposition:  attachment; filename="' .  $titlename . '"');    
} elseif (preg_match("/Firefox/", $_SERVER['HTTP_USER_AGENT'])) {    
    header('Content-Disposition: attachment; filename*="' .  $titlename . '"');    
} else {    
    header('Content-Disposition: attachment; filename="' .  $titlename . '"');    
}   
echo $xml;
exit;
} else {
	echo '{"errCode":"-1","errMsg":"XML文件导出失败,请重试！"}';
}

//xml文件导出函数
function export_xml($filename,$data) {
	if (function_exists('write_w_file')) {
		write_w_file($filename, $data);	
	}
	if (file_exists($filename)) {
		return substr($filename,strpos($filename,"temp"));
	} else {
		return false;
	}
}
//echo "\n"."memory used: " . number_format(memory_get_peak_usage());
exit;
?>