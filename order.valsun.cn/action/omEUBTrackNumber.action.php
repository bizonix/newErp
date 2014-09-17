<?php


/*
 * EUB申请跟踪号action
 * ADD BY zqt 2013.9.10
 */
class OmEUBTrackNumberAct extends Auth {
	public static $errCode = 0;
	public static $errMsg = "";

	/*
	 * 申请跟踪号,可以批量申请
	 */
	function act_applyEUBTrackNumber() { //
		global $memc_obj; //调用memcache获取sku信息
		$addUser = $_SESSION['sysUserId'];
		$omData  = isset ($_POST['omData']) ? $_POST['omData'] : '';
		if (empty($omData)) {
			self :: $errCode = '1000';
			self :: $errMsg  = "订单ID参数为空";
			return false;
		}		
		$omOrderId 	= $omData;
		$tName = 'om_unshipped_order';
		$select = '*';
		$where = "WHERE id='$omOrderId'";
		$unShippedOrderList = OmAvailableModel :: getTNameList($tName, $select, $where);
		if (empty ($unShippedOrderList)) { //订单号在未发货表中不存在
			self :: $errCode = '0001';
			self :: $errMsg = "empty unShippedOrderList";
			return 1;
		}
		$platformId = $unShippedOrderList[0]['platformId'];
		$tName = 'om_platform';
		$select = 'platform';
		$where = "WHERE id='$platformId'";
		$platformList = OmAvailableModel :: getTNameList($tName, $select, $where);
		if (empty ($platformList)) {
			self :: $errCode = '0002';
			self :: $errMsg = "empty platformList";
			return 2;
		}
		$platform = $platformList[0]['platform'];
		if (strcasecmp(trim($platform), 'ebay') != 0) { //订单所在平台不是ebay时
			self :: $errCode = '0003';
			self :: $errMsg = "is not a ebay order";
			return 3;
		}
		//这里还要判断一下运输方式是不是EUB
		////////////////////////////////

		//return 4;
		////////////////////////////////
		$accountId = $unShippedOrderList[0]['accountId']; //订单中的账号id
		$tName = 'om_eub_account';
		$select = '*';
		$where = "WHERE accountId='$accountId'";
		$accountList = OmAvailableModel :: getTNameList($tName, $select, $where);
		if (empty ($accountList)) {
			self :: $errCode = '0005';
			self :: $errMsg = "orderAccountId is not in EubAccount";
			return 5;
		}
		if(!in_array($accountList[0]['account'], array('eshoppingstar75','ishoppingclub68','newcandy789','mysoulfor','estore456'))){
			self :: $errCode = '0006';
			self :: $errMsg = "目前只有 eshoppingstar75,ishoppingclub68,newcandy789,mysoulfor,estore456 可以执行线上申请跟踪号";
			return false;	
		}
		//print_r($accountList);
		$APIDevUserID = $accountList[0]['account']; //API参数
		$APISellerUserID = $accountList[0]['dev_id']; //API参数
		$APIPassword = $accountList[0]['dev_sig']; //API参数
		//揽货地址
		$pname = $accountList[0]['pname'];
		$pcompany = $accountList[0]['pcompany'];
		$pcountry = $accountList[0]['pcountry'];
		$pprovince = $accountList[0]['pprovince'];
		$pcity = $accountList[0]['pcity'];
		$pdis = $accountList[0]['pdis'];
		$pstreet = $accountList[0]['pstreet'];
		$pzip = $accountList[0]['pzip'];
		$ptel = $accountList[0]['ptel'];
		$pte1 = $accountList[0]['pte1'];
		$pemail = $accountList[0]['pemail'];
		//寄件人地址
		$dname = $accountList[0]['dname'];
		$dcompany = $accountList[0]['dcompany'];
		$dcountry = $accountList[0]['dcountry'];
		$dprovince = $accountList[0]['dprovince'];
		$dcity = $accountList[0]['dcity'];
		$ddis = $accountList[0]['ddis'];
		$dstreet = $accountList[0]['dstreet'];
		$dzip = $accountList[0]['dzip'];
		$dtel = $accountList[0]['dtel'];
		$demail = $accountList[0]['demail'];
		$shiptype = $accountList[0]['shiptype']; //EMS派送类型，上门或自送
		//退货地址
		$rname = $accountList[0]['rname'];
		$rcompany = $accountList[0]['rcompany'];
		$rcountry = $accountList[0]['rcountry'];
		$rprovince = $accountList[0]['rprovince'];
		$rdis = $accountList[0]['rdis'];
		$rstreet = $accountList[0]['rstreet'];
		$rcity = $accountList[0]['rcity'];
		//收件人地址
		$tName = 'om_unshipped_order_userInfo';
		$select = '*';
		$where = "WHERE omOrderId='$omOrderId'";
		$userInfoList = OmAvailableModel :: getTNameList($tName, $select, $where);
		if (empty ($userInfoList)) {
			self :: $errCode = '0006';
			self :: $errMsg = "empty userInfoList";
			return 6;
		}
		$tusername = $userInfoList[0]['username']; //收件人
		$tstreet = $userInfoList[0]['street']; //街道
		$tcity = $userInfoList[0]['city']; //城市
		$tstate = $userInfoList[0]['state']; //省份
		$tcountry = $userInfoList[0]['countryName']; //国家名称
		$tcountryCode = $userInfoList[0]['countrySn']; //国家简称
		$tpostcode = $userInfoList[0]['zipCode']; //邮编
		$tphone = $userInfoList[0]['phone'] ? $userInfoList[0]['phone'] : $userInfoList[0]['landline']; //固话，手机号为空时，用座机号
		$temail = $userInfoList[0]['email']; //电邮
		//置入参数数组中
			$PickUpAddress = array (//揽货地址
	'Contact' => $pname,
			'Company' => $pcompany,
			'Street' => $pstreet,
			'District' => $pdis,
			'City' => $pcity,
			'Province' => $pprovince,
			'Postcode' => $pzip,
			'Country' => $pcountry,
			'Email' => $pemail,
			'Mobile' => $ptel,
			'Phone' => $pte1
		);

			$ShipFromAddress = array (//寄件地址
	"Contact" => $dname,
			"Company" => $dcompany,
			"Street" => $dstreet,
			"District" => $ddis,
			"City" => $dcity,
			"Province" => $dprovince,
			"Postcode" => $dzip,
			"Country" => $dcountry,
			"Email" => $demail,
			"Mobile" => $dtel,

		);

			$ReturnAddress = array (//退货地址
	'Contact' => $rname,
			'Company' => $rcompany,
			'Street' => $rstreet,
			'District' => $rdis,
			'City' => $rcity,
			'Province' => $rprovince,
			'Postcode' => $pzip,
			'Country' => '中国',


		);

			$ShipToAddress = array (//收件人地址
			'Email' => $temail,
			'Company' => '',
			'Contact' => $tusername,
			'Phone' => $tphone,
			'Street' => $tstreet,
			'City' => $tcity,
			'Province' => $tstate,
			'Postcode' => $tpostcode,
			'Country' => $tcountry,
			'CountryCode' => $tcountryCode
		);
		////////////////////////////
		//Item参数
		//orderDetail
		$omOrderDetailIds = OmEUBTrackNumberModel::getOrderDetailIds($omOrderId);
		//print_r($omOrderDetailIds);
		if(count($omOrderDetailIds)==1){
			$info = $omOrderDetailIds[0]['id'];
		}else{
			$info = array();
			foreach($omOrderDetailIds as $value){
				$info[] = $value['id'];
			}
			$info = implode(",",$info);
		}
		$tName = 'om_unshipped_order_detail_extension_ebay';
		$select = '*';
		$where = "WHERE omOrderdetailId in ($info)";
		$ebayOrderDetailList = OmAvailableModel :: getTNameList($tName, $select, $where);
		if (empty ($ebayOrderDetailList)) {
			self :: $errCode = '0007';
			self :: $errMsg = "empty ebayOrderDetailList";
			return 7;
		}
		//print_r($ebayOrderDetailList);
		
		$tName = 'om_unshipped_order_extension_ebay';
		$select = '*';
		$where = "WHERE omOrderId='$omOrderId'";
		$ebayShippedOrderList = OmAvailableModel :: getTNameList($tName, $select, $where);
		if (empty ($ebayShippedOrderList)) {
			self :: $errCode = '0008';
			self :: $errMsg = "empty ebayShippedOrderList";
			return 8;
		}

		//查询订单下的货品数量
		$tName = 'om_unshipped_order_detail';
		$select = 'count(amount) amount';
		$where = "WHERE omOrderId='$omOrderId'";
		$amountList = OmAvailableModel :: getTNameList($tName, $select, $where);
		$amounts = $amountList[0]['amount'];
		if (empty ($amounts)) {
			self :: $errCode = '0009';
			self :: $errMsg = "empty amounts";
			return 9;
		}

		$Item = array ();
		/*foreach ($ebayOrderDetailList as $ebayOrderDetail) {
			print_r($ebayShippedOrderList);
			$EBayItemID = $ebayOrderDetail['itemId']; //API参数，物品号
			$EBayTransactionID = $ebayOrderDetail['transId']; //API参数，交易号，拍卖物品为0
			$EBayBuyerID = $userInfoList[0]['platformUsername']; //API参数，买家ID
			$email = $userInfoList[0]['email']; //API参数，买家ID
			$PostedQTY = $amounts; //寄货数量
			$currency = $ebayShippedOrderList[0]['currency'];
			$omOrderdetailId = $ebayOrderDetail['omOrderdetailId'];
			$tName = 'om_unshipped_order_detail';
			$select = '*';
			$where = "WHERE id='$omOrderdetailId'";
			$omOrderDetailList = OmAvailableModel :: getTNameList($tName, $select, $where);
			if (empty ($omOrderDetailList)) {
				self :: $errCode = '0010';
				self :: $errMsg = "empty omOrderDetailList";
				return 10;
			}
			$sku = $omOrderDetailList[0]['sku'];
			$amount = $omOrderDetailList[0]['amount'];
			$skuInfo = $memc_obj->get_extral("sku_info_" . $sku);
			$DeclaredValue = $skuInfo['goods_sbjz'] * $amount ? $skuInfo['goods_sbjz'] * $amount : 1; //申报价值
			$Weight = $skuInfo['goods_weight'] * $amount ? $skuInfo['goods_weight'] * $amount : 0.01; //重量
			$CustomsTitleCN = $skuInfo['goods_zysbmc'] ? $skuInfo['goods_zysbmc'] : $sku; //中文申报名称
			$CustomsTitleEN = $skuInfo['goods_ywsbmc'] ? $skuInfo['goods_ywsbmc'] : $sku; //英文申报名称
			$OriginCountryCode = 'CN'; //原产地简码
			//echo "##".$EBayItemID."##";
			/*if((!in_array($ebay_itemid.'-'.$ebay_tid, $unique_item_tid))){
				$unique_item_tid[] = $ebay_itemid.'-'.$ebay_tid;
				$item[$i]		= array(
				'CurrencyCode' => $ebay_currency,
				'EBayEmail' => $ebay_usermail,
				'EBayBuyerID' => $ebay_userid,
				'EBayItemID' => $ebay_itemid,
				'EBayItemTitle' => $ebay_itemtitle,
				'EBayMessage' => $ebay_note,
				'EBaySiteID' => "0",
				'EBayTransactionID' => $ebay_tid,  
				'Note' => $ebay_noteb,  
				'OrderSalesRecordNumber' => $recordnumber,
				'PaymentDate' => $ebay_paidtime,
				'PayPalEmail' => "0",
				'PayPalMessage' => $ebay_note,
				'PostedQTY' => $ebay_amount,
				'ReceivedAmount' => $ebay_total,
				'SalesRecordNumber' => $recordnumber1,
				'SoldDate'			=> $ebay_createdtime,
				'SoldPrice'			=> $ebay_itemprice,
				'SoldQTY' 			=> $ebay_amount,
				'SKU'				=>array(
									'SKUID' => $sku,
									'Weight' => $weight * $ebay_amount,
									'CustomsTitleCN' => $goods_zysbmc,
									'CustomsTitleEN' => $goods_ywsbmc.' '.$sku,
									'DeclaredValue' => $goods_sbjz*$ebay_amount,
									'OriginCountryName' => "China",
									'OriginCountryCode' => "CN",
									)
				);
			}*/
		$where = "WHERE omOrderId='$omOrderId'";
		$orderarr = OrderindexModel::showSearchOrderList("om_unshipped_order",$where);
		//print_r($orderarr);
		$unique_item_tid = array();
		foreach($orderarr as $key=>$order){
			foreach($order['orderDetail'] as $k =>$detail){
				$sku = $detail['orderDetailData']['sku'];
				$amount = $detail['orderDetailData']['amount'];
				//$goods = ExportsToXlsModel::getGoods($detail['orderDetailData']['sku']);
				$skus = GoodsModel::get_realskuinfo($detail['orderDetailData']['sku']);
				foreach ( $skus as $k => $v ) {
					$goods = GoodsModel::getSkuList($k);
					if((!in_array($detail['orderDetailExtenData']['itemId'].'-'.$detail['orderDetailExtenData']['transId'], $unique_item_tid))){
					$unique_item_tid[] = $detail['orderDetailExtenData']['itemId'].'-'.$detail['orderDetailExtenData']['transId'];
						$Item[] = array (
							'EBayBuyerID' 			=> $order['orderUserInfoData']['platformUsername'],
							'EBayItemID' 			=> $detail['orderDetailExtenData']['itemId'],
							'EBayEmail' 			=> $order['orderUserInfoData']['email'],
							'EBayTransactionID' 	=> $detail['orderDetailExtenData']['transId'],
							'PostedQTY' 			=> $detail['orderDetailData']['amount'],
							'EBaySiteID'			=> "0",
							'PayPalEmail' 			=> "0",
							'EBayItemTitle' 		=> $detail['orderDetailExtenData']['itemTitle'],
							'OrderSalesRecordNumber'=> $order['orderData']['recordNumber'],
							'EBayMessage' 			=> $order['orderExtenData']['feedback'],
							'CurrencyCode' 			=>$order['orderExtenData']['currency'],
							'SoldDate'				=> date("Y-m-d",$order['orderData']['ordersTime']),
							'SoldPrice'				=> $detail['orderDetailData']['itemPrice'],
							'SoldQTY' 				=> $detail['orderDetailData']['amount'],
							'ReceivedAmount' 		=> $order['orderData']['actualTotal'],
							'PayPalMessage' 		=> $order['orderExtenData']['feedback'],
							'PaymentDate' 			=> date("Y-m-d",$order['orderData']['paymentTime']),
							'SalesRecordNumber' 	=> $order['orderData']['recordNumber'],
							'Note' 					=> isset($order['orderNote'][0]['content'])?$order['orderNote'][0]['content']:"",
							'SKU' => array (
									'SKUID' 			=> $detail['orderDetailData']['sku'],
									'Weight' 			=> $goods['goodsWeight'] * $amount * $v,
									'CustomsTitleCN' 	=> $goods['goodsName'],
									'CustomsTitleEN' 	=> $sku,//缺失英文申报名称
									'DeclaredValue' 	=> $detail['orderDetailData']['itemPrice'] * $amount * $v,
									'OriginCountryName' => "China",
									'OriginCountryCode' => "CN"
							)
						);
					}
				}
			}
		}
		$url_test = "http://epacketws.pushauction.net/v3/orderservice.asmx?WSDL";
		$url = "http://shippingapi.ebay.cn/production/v3/orderservice.asmx?wsdl";
		$soapclient = new soapclient($url);
		$params = array (
			'Version' 			=> "3.0.0",
			'APIDevUserID' 		=> $APIDevUserID,
			'APIPassword' 		=> $APIPassword,
			'APISellerUserID' 	=> $APISellerUserID,
			"OrderDetail" 		=> array (
				"PickUpAddress" 	=> $PickUpAddress,
				"ShipFromAddress" 	=> $ShipFromAddress,
				"ShipToAddress" 	=> $ShipToAddress,
				"ItemList" 			=> array (
							"Item"  => $Item
				),
				"EMSPickUpType" 	=> $shiptype,
				"ReturnAddress" 	=> $ReturnAddress
			)
		);
		try {
			//print_r($params);
			$functions = $soapclient->AddAPACShippingPackage(array (
				"AddAPACShippingPackageRequest" => $params
			));
			//echo "dfg";
			foreach ($functions as $value) {
				$bb = (array) $value;
				$ack = $bb['Ack'];
				if ($ack == 'Success') {
					$TrackCode = $bb['TrackCode'];
					//这里插入记录到records表中
					$tName = 'om_order_tracknumber';
					$set = "SET omOrderId='$omOrderId',addUser='$addUser',tracknumber='$TrackCode',createdTime='".time()."'";
					$affectRow = OmAvailableModel :: insertRow($tName, $set);
					if ($affectRow) {
						self :: $errCode = '200';
						self :: $errMsg = "success";
						return 200;
					} else {
						self :: $errCode = '0011';
						self :: $errMsg = "添加跟踪号失败";
						return 11;
					}
				} else {
					$tName 		= 'om_order_notes';
					$set		= "SET omOrderId='{$omOrderId}',content='{$bb['Message']}',userId='{$addUser}',createdTime='".time()."'";
					$affectRow 	= OmAvailableModel :: insertRow($tName, $set);
					self :: $errCode = '0012';
					self :: $errMsg = $bb['Message'];
					return 12;
				}
			}
		} catch (Exception $e) {
			self :: $errCode = '0013';
			self :: $errMsg = $e->getMessage();
			return 0;
		}		
	}
	
	/*
	 * 线下申请跟踪号,可以批量申请
	 */
	function act_applyTheLineEUBTrackNumber() {
		global $memc_obj; //调用memcache获取sku信息
		//var_dump($SYSTEM_ACCOUNTS);
		require_once WEB_PATH."lib/PHPExcel.php";//PHPExcel
		//exit;
		$addUser = $_SESSION['sysUserId'];
		$filePath = WEB_PATH."html/upload/eub/";
		
		//var_dump($_FILES); exit;
		if (!empty($_FILES['theline_upfile']['tmp_name'])){
			$uploadfile = date("Y").date("m").date("d").rand(1,3009).".xls";
			$filePath .= $uploadfile;
			//echo $filePath;
			if (!move_uploaded_file($_FILES['theline_upfile']['tmp_name'], $filePath)) {
				self :: $errCode = '003';
				self :: $errMsg = "文件上传失败！";
				echo self :: $errMsg;
				return false;
			}
		}else{
			self :: $errCode = '003';
			self :: $errMsg = "文件上传不成功！";
			echo self :: $errMsg;
			return false;	
		}
		//var_dump($addUser);
		$PHPExcel = new PHPExcel();
		//var_dump($PHPExcel); exit;
		$PHPReader = new PHPExcel_Reader_Excel2007();
		//var_dump($PHPReader); exit;
		//exit;
		//var_dump($PHPReader->canRead($filePath)); exit;
		if(!$PHPReader->canRead($filePath)){
			$PHPReader = new PHPExcel_Reader_Excel5(); 
			//var_dump($PHPReader); exit;
			if(!$PHPReader->canRead($filePath)){      
				echo 'no Excel';
				return ;
			}
		}
		//exit;
		
		$PHPExcel      = $PHPReader->load($filePath);
		$excellists    = excel2array($PHPExcel, $filePath, 2, 0);
		//echo "<pre>";print_r($excellists); exit;
		$adjust_time   = date('Y-m-d H:i:s');
		$recordnumbers = array();
		$tracknumbers = array();
		
		foreach ($excellists AS $key=> $excellist){

			$recordnumbers[] = array_shift($excellist);
			$tracknumbers[] = array_shift($excellist);
		}
		
		$total   = count($recordnumbers);
		BaseModel :: begin(); //开始事务
		
		for($ii=1; $ii<$total; $ii++){
			
			$recordnumber = $recordnumbers[$ii];
			$tracknumber = $tracknumbers[$ii];//盘点数量
			$row    = $ii;
			if(!preg_match('/^LN[0-9]{9}CN$/', $tracknumber)){
				self :: $errMsg .= "   第".$row."行".$tracknumber." 跟踪号格式有误<br>";
			}
			if($tracknumber==""){
				self :: $errMsg .= "   第".$row."行跟踪号为空请查证<br>";
			}
			if(!OmEUBTrackNumberModel::applyTheLineEUBTrackNumber($recordnumber,$tracknumber)){
				BaseModel :: rollback();
			}
		}
		self :: $errCode .= OmEUBTrackNumberModel::$errCode;
		self :: $errMsg .= OmEUBTrackNumberModel::$errMsg;
		
		BaseModel :: commit();
		BaseModel :: autoCommit();
		return true;

	}
}
?>
