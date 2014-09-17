<?php
	require_once "eBaySession.php";
	class GetOrdersIDAPI{
		private $handle;
		
		private $token;
		private $devID;
		private $appID;
		private $certID;
		private $compatabilityLevel;
		private $siteID;
		private $verb='GetOrders';
		
		public function __construct($ebay_account){
			require WEB_PATH_CONF_SCRIPTS_KEYS_EBAY.'keys_'.$ebay_account.'.php';
			$this->token=$userToken;
			$this->devID=$devID;
			$this->appID=$appID;
			$this->certID=$certID;
			$this->siteID=$siteID;
			$this->serverUrl=$serverUrl;
			$this->compatabilityLevel=$compatabilityLevel;
			
			$this->handle=new eBaySession($this->token, $this->devID, $this->appID, $this->certID,
										  $this->serverUrl, $this->compatabilityLevel, $this->siteID, $this->verb);
		}
		
		public function request($start,$end,$pcount){
			$requestXmlBody = '<?xml version="1.0" encoding="utf-8"?>
				<GetOrdersRequest xmlns="urn:ebay:apis:eBLBaseComponents">
				<RequesterCredentials>
					<eBayAuthToken>'.$this->token.'</eBayAuthToken>
				</RequesterCredentials>  
				<DetailLevel>ReturnAll</DetailLevel>
				<OutputSelector>PaginationResult</OutputSelector>
				<OutputSelector>HasMoreOrders</OutputSelector>
				<OutputSelector>ReturnedOrderCountActual</OutputSelector>				
				<OutputSelector>OrderArray.Order.OrderID</OutputSelector>
				<OutputSelector>OrderArray.Order.PaidTime</OutputSelector>
				<OutputSelector>OrderArray.Order.ShippedTime</OutputSelector>
				<OutputSelector>OrderArray.Order.CheckoutStatus</OutputSelector>
				<OutputSelector>OrderArray.Order.CheckoutStatus</OutputSelector>
				<OutputSelector>OrderArray.Order.TransactionArray.Transaction.ShippingDetails.SellingManagerSalesRecordNumber</OutputSelector>
				<ModTimeFrom>'.$start.'</ModTimeFrom>
				<ModTimeTo>'.$end.'</ModTimeTo>
				<Pagination>
					<EntriesPerPage>100</EntriesPerPage>
					<PageNumber>'.$pcount.'</PageNumber>
				</Pagination>
				<IncludeFinalValueFee>true</IncludeFinalValueFee>
				<OrderRole>Seller</OrderRole>
				<OrderStatus>All</OrderStatus>
			</GetOrdersRequest>';
			$responseXml = $this->handle->sendHttpRequest($requestXmlBody);
			return $responseXml;
		}
		
		public function GetSellerOrdersID($ebay_starttime,$ebay_endtime,$ebay_account){
			/*
			* 订单加载函数--获取时间段内所有订单号 只将有效订单号 放进队列
			* 封装进类中 add by Herman.Xi
			* addTime 2013-09-25
			*/
			global $dbConn;
			$pcount	= 1;
			$errors	= 1;
			$execution_frequency = 0;
			$count_add_total	=	0;
			do{
				echo	"抓取订单ID....\t";
				$responseXml=$this->request($ebay_starttime,$ebay_endtime,$pcount);
				if(empty($responseXml)){
					echo "ReturnEmpty...Sleep 10 seconds..";
					sleep(10);
					$hasmore=true;
					continue;
				}
				//网络出现代理Proxy error 脚本休眠20秒
				$poxy_error_p='#Proxy\s*Error#i';
				if(preg_match($poxy_error_p,$responseXml)){
					echo "ProxyError...Sleep 20 seconds..";
					sleep(20);
					$hasmore=true;
					continue;
				}
				echo "\n";
				$responseDoc = new DomDocument();	
				$responseDoc->loadXML($responseXml);
				
				$TotalNumberOfPages	 	= $responseDoc->getElementsByTagName('TotalNumberOfPages')->item(0)->nodeValue;
				$TotalNumberOfEntries	= $responseDoc->getElementsByTagName('TotalNumberOfEntries')->item(0)->nodeValue;
				$hasmore 				= $responseDoc->getElementsByTagName('HasMoreOrders')->item(0)->nodeValue;
				$Ack	 				= $responseDoc->getElementsByTagName('Ack')->item(0)->nodeValue;
					
				echo "正在请求订单ID:$pcount/$TotalNumberOfPages\t记录数[ $TotalNumberOfEntries ]\t";
				echo "同步状态: $Ack 还有更多:$hasmore\n";
			
				if($Ack == 'Failure'){
					$execution_frequency++;
					echo "eBayRequestFailure...Sleep 10 seconds then request again...\n";
					sleep(10);
					$hasmore=($execution_frequency < 3) ? true : false;
					continue;
				}else{
					//保存原始raw数据
					$raw_data_path	=EBAY_RAW_DATA_PATH.$ebay_account.'/date_range_orderid/'.date('Y-m').'/'.date('d').'/';
					$raw_data_filename	=str_replace(':','-',$ebay_starttime).'--'.str_replace(':','-',$ebay_endtime).'--p'.$pcount.'.xml';
					$raw_data_filename	=$raw_data_path.$raw_data_filename;
					$save_res	= save_ebay_raw_data($raw_data_filename,$responseXml);
					if($save_res!==false){
						echo "save raw data ok...\n";
					}else{
						echo "save raw data fail...\n";
					}
					
					$responseXml=null;unset($responseXml);
				}
				$execution_frequency = 0;
				/**/
				$SellerOrderArray	= $responseDoc->getElementsByTagName('Order');
				
				//调用订单ID入队列函数
				
				$count_add			=	$this->__handle_ebay_orderidxml($SellerOrderArray,$ebay_account);
				$count_add_total	+=	$count_add;
				$SellerOrderArray=null;unset($SellerOrderArray);
				if($pcount>= $TotalNumberOfPages ){			
					echo $hasmore."程序退出了.抓取Total:".$TotalNumberOfEntries."入队列Total:".$count_add_total."\n";
					break;
				}
				$pcount++;
				$hasmore =(strtolower($hasmore)=='true')?true:false;
			}while($hasmore);
		}
		
		function __handle_ebay_orderidxml(&$SellerOrderArray,$ebay_account){
			/*
			* 订单ID入队列函数
			* 封装进类中 add by Herman.Xi
			* addTime 2013-09-25
			*/
			global $dbConn;
			$n = 0;
			foreach( $SellerOrderArray as $SellerOrder){
				
				
				//每个订单号
				$oSellerOrderID		= $SellerOrder->getElementsByTagName('OrderID')->item(0)->nodeValue;
				//订单状态及付款状态
				$CheckoutStatus		= $SellerOrder->getElementsByTagName('CheckoutStatus')->item(0);			
				$oeBayPaymentStatus = $CheckoutStatus->getElementsByTagName('eBayPaymentStatus')->item(0)->nodeValue;
				$oCompleteStatus 	= $CheckoutStatus->getElementsByTagName('Status')->item(0)->nodeValue;	
				//付款时间			
				$oPaidTime 			= strtotime($SellerOrder->getElementsByTagName('PaidTime')->item(0)->nodeValue);
				$oShippedTime    	= strtotime($SellerOrder->getElementsByTagName('ShippedTime')->item(0)->nodeValue);
				echo $oCompleteStatus."--".$oeBayPaymentStatus."--".$oPaidTime; echo "\n";
				
				$shippingDeatil		= $SellerOrder->getElementsByTagName('ShippingDetails')->item(0);
			
				$oRecordNumber		= $shippingDeatil->getElementsByTagName('SellingManagerSalesRecordNumber')->item(0)->nodeValue;
	
				if($oCompleteStatus == "Complete" && $oeBayPaymentStatus == "NoPaymentFailure" && $oPaidTime > 0){
					$oOrderStatus	= 1;
				}
				if(($oPaidTime<=0 || $oPaidTime=='' || empty($oPaidTime)) && $oShippedTime <=0){
					echo "未付款,但是属于抓取范围\t";
					$oOrderStatus	= 687;
				}
				if($oShippedTime >0) $oOrderStatus	= 2;//已经发货
				if(	($oOrderStatus == 1 && $oShippedTime <=0 && $oPaidTime >0)  || ($oOrderStatus ==687 && $oShippedTime <=0 )){
					
					//把订单号放入对于ebay账号队列
					$ret	=	$this->push_ebay_orderid_queue($oSellerOrderID,$ebay_account,"", $oRecordNumber);
					if ($ret){					
						echo "eBay订单号[$oSellerOrderID]有效 入队列---->\n";
						$n++;
					
					}else{
						echo "eBay订单号[$oSellerOrderID]无效 不入队列\r\n";
					}
				}else{
					echo "eBay订单号[$oSellerOrderID]无效 不入队列\t";
					if($oShippedTime>0 || $oOrderStatus==2){
						echo "已经发货\t";
					}else if($oPaidTime<=0 || $oPaidTime=='' || empty($oPaidTime) ){
						echo "未付款\t";
					}
					echo "\n";
				}
			}
			echo "系统推送 ".$n." 条数据\n";
			return $n;
		}
		
		//把ebay订单号放到各自账号队列表
		function push_ebay_orderid_queue($ebay_orderid,$ebay_account,$rabbit="",$recordNumber=""){
			global $dbConn, $rabbitMQClass,$FLIP_GLOBAL_EBAY_ACCOUNT;
			//var_dump($rabbitMQClass);
			if($rabbit != ""){
				$rabbitMQClass = $rabbit;
			}
			$accountId = $FLIP_GLOBAL_EBAY_ACCOUNT[$ebay_account];
			
			/*
			//step 1 check ebay orderid statistic table
			$where = " where orderid='".$ebay_orderid."' and accountId='".$accountId."' ";
			if(OrderidsModel::judgeOrderidsList('orderid',$where) === true){
				echo "ebay orderid[$ebay_orderid] already exists in ebay orderid statistic table\n";
				return false;
			}else{
				//新增
			}
			*/
			
			$tName = 'om_unshipped_order_extension_ebay';
			$where = "WHERE orderId='$ebay_orderid'";
			$flagCountUnshipped = OmAvailableModel :: getTNameCount($tName, $where);
			var_dump($flagCountUnshipped);
			$tName = 'om_shipped_order_extension_ebay';
			$flagCountshipped = OmAvailableModel :: getTNameCount($tName, $where);
			if (!empty ($flagCountUnshipped) ||  !empty ($flagCountshipped)) { 
				return false;
			}
			
			//判断队列中是否有相同的值不能发布
			//$table_name='ebay_order_id_queue_'.$ebay_account;
			$exchange='ebay_order_id_queue_'.$ebay_account;
			$rabbitMQClass->queue_publish($exchange,$ebay_orderid);
			return true;
			/*$check_sql='select * from '.$table_name.' where ebay_orderid="'.$ebay_orderid.'"';
			$check=$dbConn->query($check_sql);
			$check=$dbConn->fetch_array_all($check);
			if(count($check)==0){
				$sql='insert into '.$table_name.' (ebay_orderid) value("'.$ebay_orderid.'")';
				
				$try_insert_count=0;
				while(1){
					$try_insert_count++;
					$res=$dbConn->query($sql);
					if($res){
						echo "Push ebay orderid[$ebay_orderid]  into queue table successfully!\n";
						break;
					}else{
						if($try_insert_count==3){
							$lost_orderid_path = EBAY_RAW_DATA_PATH.'lost_ebay_orderid/'.$ebay_account.'/lost_sql.txt';
							write_lost_sql($lost_orderid_path, $sql."\n");
							echo "oops...failed again,give this order[$ebay_orderid] up finally!\n";
							break;
						}
						echo "fail to push  ebay orderid[$ebay_orderid]  into queue table !Sleep 10 sconds then try again\n";
						sleep(10);
					}
				}
			}else{
				echo "ebay orderid[$ebay_orderid] already exists in queue\n";
			}*/
		}
		
		//把ebay订单号踢出各自账号队列表
		/*function pop_ebay_orderid_queue($ebay_orderid,$ebay_account){
			global $dbConn;
			$table_name='ebay_order_id_queue_'.$ebay_account;
			$sql='delete from '.$table_name.' where ebay_orderid="'.$ebay_orderid.'"';
			
			$try_insert_count=0;
			while(1){
				$try_insert_count++;
				$res=$dbConn->query($sql);
				if($res!==false){
					echo "kick ebay orderid[$ebay_orderid]  out  of queue table successfully!\n";
					break;
				}else{
					if($try_insert_count==3){
						echo "oops...failed again,give this order[$ebay_orderid] up finally!\n";
						break;
					}
					echo "fail to kick ebay orderid[$ebay_orderid]  out  of queue table !Sleep 10 sconds then try again\n";
					sleep(10);
				}
			}
		}*/
		
		//检验ebay orderid 是否存在 orderid汇总表【此表目前也用于漏单检验】
		/*function check_ebay_orderid_exists_in_statistic_table($ebay_orderid,$ebay_account){
			global $dbConn;
			$check_order_id_sql='select orderid from om_order_ids  
								 where 	orderid="'.$ebay_orderid.'" 
								 and	account="'.$ebay_account.'"';
			$check_order_id=$dbConn->query($check_order_id_sql);
			$check_order_id=$dbConn->fetch_array_all($check_order_id);
			if(count($check_order_id)==0){
				return false;
			}else{
				return true;
			}
		}*/
		
	}
?>