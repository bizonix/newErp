<?php
/**
 * 类名：exportsToXlsAct
 * 功能：订单导出excel
 * 版本：2013-12-20
 * 作者：任达海
 */
class ExportsToXlsView extends BaseView{

	public function view_exportsToXls1(){
		error_reporting(E_ALL);
		/*$searchPlatformId			=	isset($_GET['platformId']) ? $_GET['platformId'] : '';				//搜索平台
		$searchAccountId			=	isset($_GET['accountId']) ? $_GET['accountId'] : '';				//搜索账号
		$searchIsNote				=	isset($_GET['isNote']) ? $_GET['isNote'] : '';						//是否有留言
		$searchTransportationType	=	isset($_GET['transportationType']) ? $_GET['transportationType'] : '';//运输类型
		$searchTransportation		=	isset($_GET['transportation']) ? $_GET['transportation'] : '';		//运输方式
		$searchIsBuji				=	isset($_GET['isBuji']) ? $_GET['isBuji'] : '';						//是否补寄订单
		$searchIsLock				=	isset($_GET['isLock']) ? $_GET['isLock'] : '';						//是否锁定
		$searchOrderTime1			=	isset($_GET['OrderTime1']) ? $_GET['OrderTime1'] : '';				//搜索下单初始时间
		$searchOrderTime2			=	isset($_GET['OrderTime2']) ? $_GET['OrderTime2'] : '';				//搜索下单结束时间
		//order_detail表
		$searchReviews				=	isset($_GET['reviews']) ? $_GET['reviews'] : '';					//是否评价
		$searchSku					=	isset($_GET['sku']) ? $_GET['sku'] : '';							//sku
		$searchOmOrderId			=	'';																	//订单编号
		$searchOrderType			=	isset($_GET['selectOrderType']) ? $_GET['selectOrderType'] : '';*/
		//foreach($_GET as $k=>$v){
//			//if(isset($_))
//		}
		
		$type = isset($_GET['type'])?trim($_GET['type']):"";
		$order = isset($_GET['order'])?trim($_GET['order']):"";
		
		$where = " WHERE ";
		$where_arr = array();
		
		$accountList = $_SESSION['accountList'];
		$platformList = $_SESSION['platformList'];
		//echo "<pre>"; print_r($accountList); exit;
		$platformsee = array();
		for($i=0;$i<count($platformList);$i++){
			$platformsee[]	= $platformList[$i];
		}
		if($platformsee){
			$where_arr[] = ' da.platformId IN ( '.join(',', $platformsee).' ) ';
		}
		$accountsee = array();
		for($i=0;$i<count($accountList);$i++){
			$accountsee[]	= $accountList[$i];
		}
		if($accountsee){
			$where_arr[]  = ' da.accountId IN ( '.join(",", $accountsee).' ) ';
		}
		
		if($type == 1){
			$arr = explode(",",$order);
			$ostatus = $arr[0];
			$otype = $arr[1];
			
			$where_arr[] = " da.is_delete=0 ";
			if($ostatus){
				$where_arr[] = " da.orderStatus='{$ostatus}' ";	
			}
			if($otype){
				$where_arr[] = " da.orderType='{$otype}' ";
			}
			//$where = "where da.orderStatus='{$ostatus}' and da.orderType='{$otype}' and da.is_delete=0";
		}elseif($type == 2){
			$where_arr[] = " da.id in($order) ";
		}
		$where .= join(' AND ', $where_arr);
		//echo $where; exit;
		$orderarr = OrderindexModel::showSearchOrderList("om_unshipped_order",$where);
		//echo count($orderarr); exit;
		$exporter = new ExportDataExcel("browser", "xls1".$date.".xls");
		//echo "<pre>"; print_r($orderarr[98]);exit;
		$exporter->initialize(); // starts streaming data to web browser
		$exporter->addRow(array("日期", "账号","订单编号","重量","邮费","运输方式", "交易号","客户ID", "仓位号", "料号", "数量","国家", "包裹总价值", "币种","包装员", "挂号条码", "是/否"));
        foreach($orderarr as $key =>$value){
			$paymentTime		= date("Y-m-d",$value['orderData']['paymentTime']);
			$accountArr 		= OmAccountModel::accountInfo($value['orderData']['accountId']);
			$account 			= $accountArr['account'];
			//$account 			= $account['account'];
			$orderid 			= $key;
			
			$weight				= $value['orderWarehouse']['actualWeight'];
			$shipfee			= $value['orderWarehouse']['actualShipping'];
			$packagerId			= $value['orderWarehouse']['packagerId'];
			$packager			= UserModel::getUsernameById($packagerId);
			$transportation = CommonModel::getCarrierList();   //所有的
			$transportationList = array();
			foreach($transportation as $tranValue){
				if($tranValue['id']==$value['orderData']['transportId']){
					$transport 	= $tranValue['carrierNameCn'];
					break;
				}
				//$transportationList[$tranValue['id']] = $tranValue['carrierNameCn'];
			}
			//$plateform 			= exportsToXlsModel::plateformIdToName($value['orderData']['platformId']);
			$plateformArr 		= OmAccountModel::platformListById($value['orderData']['platformId']);
			$plateform          = $plateformArr[$value['orderData']['platformId']];
			//$plateform			= $plateform['platform'];
			$transId 			= $value['orderExtenData']['transId'];
			$currency 			= $value['orderExtenData']['currency'];
			
			$userId 			= $value['orderUserInfoData']['platformUsername'];
			$countryName 		= $value['orderUserInfoData']['countryName'];
			$actualTotal 		= $value['orderData']['actualTotal'];
			$recordNumber 		= $value['orderData']['recordNumber'];
			$trackNumber 		= $value['orderTracknumber'][0]['tracknumber'];
			$orderDetails 		= $value['orderDetail'];
			if(count($orderDetails)==1){
				foreach($value['orderDetail'] as $key=>$detail){
					$skuId = $key;
				}
				$exporter->addRow(array($paymentTime,$account,$orderid,$weight,$shipfee,$transport,$recordNumber,$userId,"",$orderDetails[$skuId]['orderDetailData']['sku'],$orderDetails[$skuId]['orderDetailData']['amount'],$countryName,$actualTotal,$currency,$packager,$trackNumber,""));
			}else{
				$exporter->addRow(array($paymentTime,$account,$orderid,$weight,$shipfee,$transport,$recordNumber,$userId,"","","",$countryName,$actualTotal,$currency,$packager,$trackNumber,""));
				foreach($orderDetails as $detail){
					$exporter->addRow(array($paymentTime,$account,$orderid,"","",$transport,"","","",$detail['orderDetailData']['sku'],$detail['orderDetailData']['amount'],$countryName,"","","","",""));
				}
			}
			
		}
		$exporter->finalize(); // writes the footer, flushes remaining data to browser.
		
		exit();
	}
	
	public function view_exportsToXls2(){
		
		$type = isset($_GET['type'])?$_GET['type']:"";
		$order = isset($_GET['order'])?$_GET['order']:"";
		//echo $order;
			
		if($type == 1){
			$arr = explode(",",$order);
			$ostatus = $arr[0];
			$otype = $arr[1];
			$where = " WHERE ";
			$where_arr = array();
			$where_arr[] = " da.is_delete=0 ";
			if($ostatus){
				$where_arr[] = " da.orderStatus='{$ostatus}' ";	
			}
			if($otype){
				$where_arr[] = " da.orderType='{$otype}' ";
			}
			$where .= join(' AND ', $where_arr);
			//$where = "where da.orderStatus='{$ostatus}' and da.orderType='{$otype}' and da.is_delete=0";
		}elseif($type == 2){
			$where = "where da.id in($order)";
		}
		//echo $where; exit;
		$orderarr = OrderindexModel::showSearchOrderList("om_unshipped_order",$where);

		//
		//echo "<pre>"; print_r($orderarr);exit;
		$exporter = new ExportDataExcel("browser", "xls2".$date.".xls");
		
		$exporter->initialize(); // starts streaming data to web browser
		$exporter->addRow(array("订单标识", "商品交易号","商品SKU","数量","收件人姓名（英文）","收件人地址1（英文）", "收件人地址2（英文）","收件人地址3（英文）", "收件人城市", "收件人州", "收件人邮编","收件人国家", "收件人电话", "收件人电子邮箱"));
        foreach($orderarr as $id => $order){
			if(count($order['orderDetail'])==1){
				foreach($order['orderDetail'] as $key=>$value){
					$skuId = $key;
				}
				$exporter->addRow(array($order['orderData']['recordNumber'],$order['orderExtenData']['transId'],$order['orderDetail'][$skuId]['orderDetailData']['sku'],
										$order['orderDetail'][$skuId]['orderDetailData']['amount'],$order['orderUserInfoData']['username'],
										$order['orderUserInfoData']['street'],$order['orderUserInfoData']['address2'],$order['orderUserInfoData']['address3'],
										$order['orderUserInfoData']['city'],$order['orderUserInfoData']['state'],$order['orderUserInfoData']['zipCode'],
										$order['orderUserInfoData']['countrySn'],!empty($order['orderUserInfoData']['landline'])?$order['orderUserInfoData']['landline']:$order['orderUserInfoData']['phone'],$order['orderUserInfoData']['email']));
		
			}else{
				$exporter->addRow(array($order['orderData']['recordNumber'],$order['orderExtenData']['transId'],"",
										"",$order['orderUserInfoData']['username'],
										$order['orderUserInfoData']['street'],$order['orderUserInfoData']['address2'],$order['orderUserInfoData']['address3'],
										$order['orderUserInfoData']['city'],$order['orderUserInfoData']['state'],$order['orderUserInfoData']['zipCode'],
										$order['orderUserInfoData']['countrySn'],!empty($order['orderUserInfoData']['landline'])?$order['orderUserInfoData']['landline']:$order['orderUserInfoData']['phone'],$order['orderUserInfoData']['email']));
				
				foreach($order['orderDetail'] as $key=>$value){
					$exporter->addRow(array($order['orderData']['recordNumber'],$order['orderExtenData']['transId'],$value['orderDetailData']['sku'],
										$value['orderDetailData']['amount'],$order['orderUserInfoData']['username'],
										$order['orderUserInfoData']['street'],$order['orderUserInfoData']['address2'],$order['orderUserInfoData']['address3'],
										$order['orderUserInfoData']['city'],$order['orderUserInfoData']['state'],$order['orderUserInfoData']['zipCode'],
										$order['orderUserInfoData']['countrySn'],!empty($order['orderUserInfoData']['landline'])?$order['orderUserInfoData']['landline']:$order['orderUserInfoData']['phone'],$order['orderUserInfoData']['email']));
				
				}
			}
		}
		$exporter->finalize(); // writes the footer, flushes remaining data to browser.
		
		exit();

	}
	public function view_exportsToXls3(){
		
		error_reporting(E_ALL);
		$type = isset($_GET['type'])?$_GET['type']:"";
		$order = isset($_GET['order'])?$_GET['order']:"";
		//echo typeof();
			
		if($type == 1){
			$arr = explode(",",$order);
			$ostatus = $arr[0];
			$otype = $arr[1];
			$where = "where da.orderStatus='{$ostatus}' and da.orderType='{$otype}' and da.is_delete=0";
		}elseif($type == 2){

			$where = "where da.id in($order)";
		}
		//
		$orderarr = OrderindexModel::showSearchOrderList("om_unshipped_order",$where);
		
		//echo "<pre>"; print_r($orderarr[98]);exit;
		$exporter = new ExportDataExcel("browser", "xls3".$date.".xls");
		
		$exporter->initialize(); // starts streaming data to web browser
								
		
		$exporter->addRow(array("SKU编号", "商品中文名称","商品英文名称","重量（3位小数）","报关价格(整数)","原寄地", "保存至系统SKU"));
        
		foreach($orderarr as $id => $order){

			foreach($order['orderDetail'] as $key=>$value){
				//$goods = exportsToXlsModel::getGoods($value['orderDetailData']['sku']);
				$goods = GoodsModel::getSkuList($value['orderDetailData']['sku']);
				//print_r($goods);
				$exporter->addRow(array($value['orderDetailData']['sku'],$goods['goodsName'],$value['orderDetailExtenData']['itemTitle'],$goods['goodsWeight'],"3","CN","1"));
			
			}
			
		}
		$exporter->finalize(); // writes the footer, flushes remaining data to browser.
		
		exit();
	}
}
?>