<?php
/**
 * 类名：OrderTranUpsModel
 * 功能：UPS美国专线订单信息导入导出model
 * 版本：1.0
 * 日期：2014/03/01
 * 作者：管拥军
 */
 
class OrderTranUpsModel{
	public static $dbConn;
	public static $errCode		= 0;
	public static $errMsg		= "";
	
	//初始化db类
	public static function	initDB(){
		global $dbConn;
		self::$dbConn	= $dbConn;
	}
			
	/**
	 * OrderTranUpsModel::export_ups_xml_info()
	 * 导出选中的UPS美国专线订单信息
	 * @param string $ids 订单编号
	 * @return  array
	 */
	public static function export_ups_xml_info($ids){
		self::initDB();
		$country	= "United States";
		$uid		= $_SESSION[C('USER_AUTH_ID')];
		$filename	= "ship_ups_us_".$uid.".xml";
		$shipxml	= WEB_PATH."html/temp/".$filename;
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
		$xml	= "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<OpenShipments xmlns=\"x-schema:OpenShipments.xdr\">";
		$status	= "400,600";
		$sql	= "SELECT id FROM om_unshipped_order WHERE transportId = 62 AND orderStatus NOT IN({$status}) ORDER BY id DESC";
		//echo $sql;
		$query	= self::$dbConn->query($sql);
		$ids	= self::$dbConn->fetch_array_all($query);
		if (count($ids)<=0) return "暂无数据需要导出！";
		foreach ($ids as $v) {
			$orderid	= $v['id'];
			$condition	= "1 AND a.id = {$orderid} ";
			$sql	= "SELECT a.id,a.calcWeight,a.platformId,a.accountId,
						b.username,b.email,b.zipCode,b.landline,b.street,b.city,b.state,b.countryName,b.countrySn,
						c.id as did,
						d.suffix
						FROM om_unshipped_order AS a
						LEFT JOIN om_unshipped_order_userInfo AS b ON a.id = b.omOrderId
						LEFT JOIN om_unshipped_order_detail AS c ON a.id = c.omOrderId 
						LEFT JOIN om_platform AS d ON a.platformId = d.id
						WHERE {$condition} AND b.countryName = '{$country}' LIMIT 1";
			//echo $sql;
			$query	= self::$dbConn->query($sql);
			$res	= self::$dbConn->fetch_array($query);
			//发货信息
			$r_name			= $res['username'];
			$r_postcode		= $res['zipCode'];
			$r_phone		= $res['landline'];
			$r_country		= 'US';
			$r_province		= $res['state'];
			$r_city			= $res['city'];
			$r_street		= $res['street'];
			$r_email		= $res['email'];
			$ordersn		= $res['did'];
			$add1			= substr($r_street,0,35);
			$add2			= substr($r_street,35,70);
			$add3			= substr($r_street,70,105);
			$packagetype	= 'CP';
			$servicelevel	= 'EX';
			$transportation	= 'prepaid';
			$description	= 'refer to invoice';
			$nums			= 1;
			$weight			= round($res['calcWeight'],1);
			//获取交易ID和paypal 交易号
			$tabname		= $res['suffix'];
			//if (empty($tabname)) continue;
			$sql			= "SELECT transId,PayPalPaymentId FROM om_unshipped_order_extension_{$tabname} WHERE omOrderId = '{$orderid}'";
			$query			= self::$dbConn->query($sql);
			$result			= self::$dbConn->fetch_array($query);
			$tid 			= $result['PayPalPaymentId'];
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
			$sql			= "SELECT 
								a.sku,a.amount,a.itemPrice,
								b.itemTitle
								FROM om_unshipped_order_detail AS a
								LEFT JOIN om_unshipped_order_detail_extension_{$tabname} AS b ON a.id = b.omOrderdetailId
								WHERE a.id = {$ordersn}";
			$query			= self::$dbConn->query($sql);
			$result			= self::$dbConn->fetch_array_all($query);
			$lineitem		= "";
			foreach ($result as $val) {
				$enname			= $val['itemTitle'];
				$count			= $val['amount'];
				$price			= $val['itemprice'];
				$internalcode	= 'USD';
				$original		= 'CN';
				$measure		= 'PCS';
				$code			= 'USD';
				$hscode			= '';
				$lineitem		.= "\n<LineItem>					
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
		$xml		.= "</OpenShipments>\n";
		unset($res);
		$res		= self::write_ups_file($shipxml,$xml);
		if ($res) {
			return $res;
		} else {
			return "XML文件导出失败,请重试！";
		}
	}
	
	/**
	 * OrderTranUpsModel::write_ups_file()
	 * 写入UPS美国专线文件信息
	 * @param string $filename 文件名
	 * @param string $data 写入数据
	 * @return  array
	 */
	private function write_ups_file($filename,$data){
		if (function_exists('write_w_file')) {
			write_w_file($filename, $data);	
		}
		if (file_exists($filename)) {
			return substr($filename,strpos($filename,"temp"));
		} else {
			return false;
		}			
	}			
}
?>
