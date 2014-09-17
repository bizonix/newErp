<?php
/******************************************
 * 分销商订单操作相关接口
 * windayzhong	2014-03-06
 */
include_once WEB_PATH."lib/sdk/valsun/ValSun.class.php";
global $app_key_mapping, $app_token_mapping;
include WEB_PATH."conf/distributor/distributor_token_config.php";
include_once WEB_PATH."lib/scripts/aliexpress/aliexpress_order_func.php";


//, $app_token_mapping;
class openSystemApiAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";	
	
	/************************************************
	 * 开发者授权验证
	 * @param	string	$app_key	授权key
	 * @return	boolean	
	 */
	function checkAuth($app_key, $sign){
		global	$app_token_mapping;
		$valsun	=	new Valsun();
		$valsun->setConfig($app_key, $app_token_mapping[$app_key]);
		$server_sign	=	$valsun->createSign($_POST);
		if($server_sign != $sign){
			return false;	//签名错误
		}
		return true;
	}
	
	/*******************************************************
	 * 通过app_key获取系统里对应的account
	 */
	function getOrderSystemAccount($app_key){
		global	$app_key_mapping;
		
		if(!in_array($app_key, array_keys($app_key_mapping))){
			return false;
		}
		$account	=	$app_key_mapping[$app_key];
		return $account;
	}
	
	
	function act_xxxx(){
	
		//OrderindexModel::killAllOrderRowNoEvent("9742345","16",'om_unshipped_order');
		//OrderindexModel::killAllOrderRowNoEvent("9742344","16",'om_unshipped_order');

	}
	
	/************************************
	 * 开发者平台新增订单接口
	 * 
	 */
	function act_addDistributorOrder(){
		

		
		$orderArrJson	=	json_decode($_POST["orderArr"],true);	
		$app_key		=	trim($_POST['app_key']);
		$account		=	$this->getOrderSystemAccount($app_key);
		$accountInfo	=	omAccountModel::getAccountInfoByName($account);
		$accountId		=	$accountInfo['id'];

		$orderAddAct	=	new	OrderAddAct();
		
		$rtnArr			=	array();	
		foreach($orderArrJson as $val){
			$itemRtn	=	array();
			$orderId	=	$val['orderId'];
			
			$check		=	$orderAddAct->checkDuplicateOrder($orderId,'16');
			if ($check){
				$itemRtn["errcode"] = 	80001;
				$itemRtn["orderId"] = 	$orderId;
				$itemRtn["msg"] 	= 	"系统已经存在[".$orderId."]这个订单";
				$rtnArr[] = $itemRtn;
				continue;
			}
			//return $rtnArr;
			
			
			$insertOrder = array();
            /***************BEGIN 订单表数据***************/
            //$unshipedOrder = array();
     	    $orderdata['recordNumber']	        =	$orderId;
            $orderdata['platformId']			=	16; //国内销售部
            $orderdata['accountId']	            =	$accountId;
            $orderdata['orderStatus']			=	C('STATEPENDING');
			$orderdata['orderType']			    =	C('STATEPENDING');	//daichu

			//$gmtCreate = time_shift($val['gmtCreate']);
    		$orderdata['ordersTime']		    =	$val['gmtCreate'];
    		$orderdata['paymentTime']			=	"";//$pay_time[0];           
            $orderdata['onlineTotal']			=	"";//$order['initOderAmount']['amount'];  //线上总金额
    		$orderdata['actualTotal']			=	"";//$orderDetail2['payAmount']['amount'];//付款总金额    	
            $orderdata['calcShipping']			=	//$order['logisticsAmount']['amount']; //物流费用    	
    		$orderdata['orderAddTime']			=	time();
			$orderdata['isFixed']				=	1;
            /***************END 订单表数据***************/            
            
            /***************BEGIN 订单扩展表数据***************/
            $orderExtAli = array(); //          
            $orderExtAli['declaredPrice']		=	$val['orderAmount']['amount'];  
            $orderExtAli['paymentStatus']		=	"";//$order['fundStatus'];  
            $orderExtAli['transId']			    =	"";//$val['tradeID'];//$order['id'];//$orderdetail["id"]; // 交易id;;
            //$orderExtAli[PayPalPaymentId"]	=	'';
            //$orderExtAli["site"]			    =	'';
            $orderExtAli['orderId']			    =	$val['orderId'];
            $orderExtAli['platformUsername']	=	"";//$order['buyerSignerFullname'];;            
            $orderExtAli['currency']			=	$val['orderAmount']['currencyCode'];          
            $orderExtAli['PayPalEmailAddress']	=	"";//$order['buyerInfo']['email'];;
            $orderExtAli['eBayPaymentStatus']	=	"";//1?//$order['orderStatus']; //订单状态;            
            /***************END 订单扩展表数据***************/
            
            /***************BEGIN 订单用户表数据***************/
            $orderUserInfo = array();           
            $orderUserInfo['username']			=	$val['receiptAddress']['contactPerson'];            
            $orderUserInfo['platformUsername']  =	"";//$order['buyerSignerFullname'];
            $orderUserInfo['email']			    =	"";//$order['buyerInfo']['email'];            
            $orderUserInfo['countryName']	 	=	get_country_name($val['receiptAddress']['country']);
            $orderUserInfo['countrySn']			=	$val['receiptAddress']['country'];         
            $orderUserInfo['currency']          =	$val['orderAmount']['currencyCode'];      	
    		$orderUserInfo['state']			    =	$val['receiptAddress']['province'];
    		$orderUserInfo['city']				=	$val['receiptAddress']['city'];           	
            $orderUserInfo['street']			=	$val['receiptAddress']['address1'];//?//$val['receiptAddress']['detailAddress'];
    		$orderUserInfo['address2']			=	isset($val['receiptAddress']['address2']) ? $val['receiptAddress']['address2'] : "";
            $orderUserInfo['zipCode']			=	$val['receiptAddress']['zip']; 

            $orderUserInfo['phone']             =   $val['receiptAddress']['phoneNumber'];
           /*************END 订单用户表数据***************/
		   
            $carrier	=	array();
            $item_notes	=	array();
            $noteb		=	array();       

			
            
            
			$insertOrder = array('orderData' => $orderdata,
								'orderExtenData' => $orderExtAli,					  
								'orderUserInfoData' => $orderUserInfo
								);
			
			$orderweight	=	"";
			$obj_order_detail_data = array();
			
			
			foreach($val['childOrderList'] as $orderdetail){
				//明细表
                $orderdata_detail	=	array();       
                //$orderdata_detail['omOrderId']	    =	$insertId;//$order["id"];                 
				$orderdata_detail['recordNumber']	=	$orderId;    			 
				$orderdata_detail['sku']			=	$orderdetail['productAttributes']['sku'];//substr($orderdetail['skuCode'],0,stripos($orderdetail['skuCode'],'#')); 
				$orderdata_detail['itemPrice']      =	$orderdetail['productAttributes']['itemPrice']; 
				$orderdata_detail['amount']     	=	$orderdetail['lotNum']; 
				//$orderdata_detail["shippingFee"]	=	''; 
				//$orderdata_detail["reviews"]	    =	''; 
				$orderdata_detail['createdTime']    =	time();    				
            	
				//明细扩展表
                $orderDetailExtAli	=	array();               
                $orderDetailExtAli['itemTitle']	        =	$orderdetail['productAttributes']['itemTitle']; 
                $orderDetailExtAli['itemURL']	        =	$orderdetail['productAttributes']['skuUrl'];                      
                $orderDetailExtAli['itemId']	        =	"";//$orderdetail['productId'];
                $orderDetailExtAli['transId']	        =	0;//$orderId;//$orderdetail['orderId']; // 交易id;
                $orderDetailExtAli['note']	            =	"";//$item_notes[$orderdetail['orderId']]; 
                      
				$obj_order_detail_data[] = array('orderDetailData' => $orderdata_detail,			
											'orderDetailExtenData' => $orderDetailExtAli
											);
			}
			$insertOrder['orderDetail'] = $obj_order_detail_data;
			$calcInfo = CommonModel :: calcAddOrderWeight($obj_order_detail_data);//计算重量和包材
			$insertOrder['orderData']['calcWeight'] = $calcInfo[0];
			$insertOrder['orderData']['pmId'] = $calcInfo[1];
			if(count($insertOrder['orderDetail']) > 1){
				$insertOrder['orderData']['orderAttribute'] = 3;
			}else if(isset($insertOrder['orderDetail'][0]['orderDetailData']['amount']) && $insertOrder['orderDetail'][0]['orderDetailData']['amount'] > 1){
				$insertOrder['orderData']['orderAttribute'] = 2;
			}
			$calcShippingInfo = CommonModel :: calcAddOrderShippingFee($insertOrder,1);//计算运费
			$insertOrder['orderData']['channelId'] = $calcShippingInfo['fee']['channelId'];

			$insertOrder = AutoModel :: auto_contrast_intercept($insertOrder);

			$opflag	=	"false";		
			if(OrderAddModel :: insertAllOrderRow($insertOrder)){
				$itemRtn["errcode"] = 0;
				$itemRtn["orderId"] = $orderId;
				$itemRtn["msg"] 	= "success";
				$rtnArr[] 			= $itemRtn;
				$opflag	=	"success";
			}else{
				$itemRtn["errcode"] = 80005;
				$itemRtn["orderId"] = $orderId;
				$itemRtn["msg"] 	= "添加订单失败";
				$rtnArr[] 			= $itemRtn;
			}
			$logfile	=	date("Y-m-d").".log";
			@file_put_contents("/home/ebay_order_cronjob_logs/auto_contrast_intercept/".$account."/".$logfile, $_POST["orderArr"]."==".$opflag."\r\n", FILE_APPEND);
		}
		
		$rtnStr = json_encode($rtnArr);
		echo $rtnStr; 

	}
	
	
}