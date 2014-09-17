<?php
    //脚本参数检验
    if($argc != 2){
    	exit("must be have account info \n");
    }
    //平台对ERP账号对应关系，后期改到配置文件
    $erp_user_mapping = array(
	"cn1000268236"	=>	"3ACYBER",
	"cn1000421358"	=>	"szsunweb",
	"cn1000616054"	=>	"E-Global",
	"cn1000960806"	=>	"beauty365",
	"cn1000983412"	=>	"caracc",
	"cn1000983826"	=>	"Bagfashion",
	"cn1000999030"	=>	"prettyhair",
	"cn1001428059"	=>	"LovelyBaby",
	"cn1001392417"	=>	"Finejo",
	"cn1001424576"	=>	"5season",
	"cn1001656836"	=>	"fashiondeal",
	"cn1001711574"	=>	"Sunshine",
	"cn1001718610"	=>	"fashionqueen",
	"cn1001739224"	=>	"shiningstar",
	"cn1500053764"	=>	"babyhouse",
	"cn1500152370"	=>	"fashionzone",
	"cn1500226033"	=>	"shoesacc",
	"cn1500293467"	=>	"superdeal",
	"cn1500439756"	=>	"istore",
	"cn1500514645"	=>	"ladyzone",
	"cn1500688776"	=>	"beautywomen",
	"cn1501288533"	=>	"womensworld",
	"cn1501287427"	=>	"myzone",
	"cn1501540493"	=>	"homestyle",	//2013-08-01
	"cn1501578304"	=>	"championacc",	//2013-08-01
	"cn1501595926"	=>	"digitallife",	//2013-08-01
	"cn1501638006"	=>	"Etime",		//2013-08-20
	"cn1510304665"	=>	"citymiss",
	"cn1500440054"	=>	"zeagoo360",
	//taotaoAccount
	"cn1501642501"	=>	"taotaocart",
	"cn1501654678"	=>	"arttao",
	"cn1501654797"	=>	"taochains",
	"cn1501655651"	=>	"etaosky",
	"cn1501656206"	=>	"tmallbasket",
	"cn1501656494"	=>	"mucheer",
	"cn1501657160"	=>	"lantao",
	"cn1501657334"	=>	"direttao",
	"cn1501657572"	=>	"hitao",
	"cn1501686293"	=>	"taolink",
	//------挂号转平邮账号-----
	"cn1510515579"	=>	"acitylife",
	"cn1510509503"	=>	"etrademart",
	"cn1510509429"	=>	"centermall",
	"cn1510514024"	=>	"viphouse",
	//-------------
    );  
    $taotao_account = array("cn1501642501"	=>	"taotaocart",
                        	"cn1501654678"	=>	"arttao",
                        	"cn1501654797"	=>	"taochains",
                        	"cn1501655651"	=>	"etaosky",
                        	"cn1501656206"	=>	"tmallbasket",
                        	"cn1501656494"	=>	"mucheer",
                        	"cn1501657160"	=>	"lantao",
                        	"cn1501657334"	=>	"direttao",
                        	"cn1501657572"	=>	"hitao",
                        	"cn1501686293"	=>	"taolink"
                            );
    include_once dirname(__DIR__)."/common.php";
    //$aliexpress_user = trim($argv[1]);
    $aliexpress_user	=	"cn1001656836";
    if(!array_key_exists($aliexpress_user, $erp_user_mapping)){
    	echo "error：账号不存在: ".$aliexpress_user."\n";
    	exit;
    }
    $aliAccount = $erp_user_mapping[$aliexpress_user];
	$accountKV = M('Account')->getAccountNameByPlatformId(2);//aliexpress 中accountId 和 account 的KV 形式
    $aliAccountId = array_search($aliAccount, $accountKV);
	if ($aliAccountId === false){
		exit("{$argv[1]} is not in erp platform=2 accountArr!\n");
	}
    
    echo "\n\n\nDate: ".date("Y-m-d H:i:s"). " 开始速卖通(".$aliexpress_user.")的订单同步\n";
    
    $createDateStart = '08/10/2014  12:00:00';
    $createDateEnd = '09/03/2014  23:59:59';
    
    
    
    $aliexpress = A('AliexpressButt');
    $aliexpress->setToken($aliexpress_user);    
    $orderList = $aliexpress->findOrderListQuery($createDateStart,$createDateEnd);
    //print_r($orderList);exit;
    $totalDataNum =	count($orderList);
    echo "-----此次共拉取到 ".$totalDataNum." 条数据\n";    
    //exit;
    //$transportList = M('InterfaceTran')->getCarrierList(2);
    //$expressArr = M('InterfaceTran')->getCarrierList(1);
    $index	=	0;
    if($totalDataNum > 0){
    	foreach($orderList as $order){
    	    $orderdata = array();
            $orderExtAli = array();
    		$orderDetail2 = $order['v'];
    		$order	      =	$order['detail'];
            //print_r($order);
            //print_r($orderDetail2);
            //exit;
    		//只同步已付款24小时后未发货的订单
    		//订单是美国时间（实行夏令时， 比上海时间慢12小时）
    		$pay_time	  =	$aliexpress->time_shift($order['gmtPaySuccess']);
    		$left_time	  =	$pay_time[1]-$pay_time[0];
    		if($left_time <= 86400)	continue;
    		//-------------------------------
			$insertOrder = array();
            /***************BEGIN 订单统一表头数据***************/
     	    $orderdata['recordNumber'] = $order['id'];
            $orderdata['platformId']   = 2; //Aliexpress's platformId is 2
            $orderdata['accountId']	   = $aliAccountId;
            
            if(M('OrderAdd')->checkIsExists(array('recordNumber'=>$orderdata['recordNumber'], 'accountId'=>$orderdata['accountId']))){
    			echo "--{$order['id']}--系统已经存在这个订单\n";
    			continue;
    		}
            $orderdata['orderStatus']	= 0;
			$gmtCreate = $aliexpress->time_shift($order['gmtCreate']);
    		$orderdata['ordersTime']		    =	$gmtCreate[0];
    		$orderdata['paymentTime']			=	$pay_time[0];
            $orderdata['onlineTotal']			=	$order['initOderAmount']['amount'];  //线上总金额
    		$orderdata['actualTotal']			=	$orderDetail2['payAmount']['amount'];//付款总金额    	
            $orderdata['calcShipping']			=	$order['logisticsAmount']['amount']; //物流费用

            $orderdata['currency']			=	$orderDetail2['payAmount']['currencyCode'];; //物流费用
            
    		$orderdata['orderAddTime']			=	time();
            //if(count($orderDetail2['productList']) == 1){
//                $orderdata['ORtransport']	    =   $orderDetail2['productList'][0]['logisticsServiceName'];//订单只指定了一种运输方式的时候，多种运输方式的时候放到客户留言中去
//            }
            $orderdata['transportId']		    =	0;//运输方式id，默认为0
            $orderdata['orderAttribute']		=	1;//默认值
            $orderdata['pmId']		            =	0;//默认值
            $orderdata['channelId']		        =	0;//默认值
            $orderdata['orderAddTime']		    =	time();//默认值
            $orderdata['completeTime']		    =	0;//默认值
            /***************END 订单表数据***************/            
            
            /***************BEGIN 订单表头扩展数据***************/
                     
            $orderExtAli['declaredPrice']		=	$order['orderAmount']['amount'];  
            $orderExtAli['initOderAmount']		=	$order['initOderAmount']['amount']; //线上产品总价
            $orderExtAli['buyerLoginid']	    =	$order['buyerloginid'];//平台用户信息唯一标识;
            //$orderExtAli[PayPalPaymentId"]	=	'';
            //$orderExtAli["site"]			    =	'';
            $orderExtAli['orderStatus']			=	$order['orderStatus'];//平台的订单类型
            $orderExtAli['frozenStatus']	    =	$order['frozenStatus'];//冻结状态      
            $orderExtAli['logisticsStatus']		=	$order['logisticsStatus'];//物流状态      
            $orderExtAli['issueStatus']	        =	$order['issueStatus'];//纠纷状态
            $orderExtAli['loanStatus']	        =	$order['loanStatus']; //放款状态;
            $orderExtAli['fundStatus']	        =	$order['fundStatus']; //资金状态;
            $orderExtAli['sellerSignerFullname']=	$order['sellerSignerFullname']; //卖家名称;
            $orderExtAli['issueContent']	    =	$order['loanStatus']; //放款状态;
            $orderExtAli['loanStatus']	        =	$order['loanStatus']; //放款状态;           
            /***************END 订单扩展表数据***************/
            
            /***************BEGIN 订单用户表数据***************/
            $orderUserInfo = array();           
            $orderUserInfo['username']			=	$order['receiptAddress']['contactPerson'];            
            $orderUserInfo['platformUsername']  =	$order['buyerSignerFullname'];
            $orderUserInfo['email']			    =	$order['buyerInfo']['email'];            
            $orderUserInfo['countryName']	 	=	$aliexpress->get_country_name($order["receiptAddress"]["country"]);//get_country_name 根据国家简码返回国家全英文名
            $orderUserInfo['countrySn']			=	$order['receiptAddress']['country'];         
            $orderUserInfo['currency']          =	$order['orderAmount']['currencyCode'];      	
    		$orderUserInfo['state']			    =	$order['receiptAddress']['province'];
    		$orderUserInfo['city']				=	$order['receiptAddress']['city'];           	
            $orderUserInfo['address1']			=	$order['receiptAddress']['detailAddress'];
    		$orderUserInfo['address2']			=	isset($order['receiptAddress']['address2']) ? $order['receiptAddress']['address2'] : "";
            $orderUserInfo['zipCode']			=	$order['receiptAddress']['zip'];            
    		if(isset($order['receiptAddress']['phoneNumber'])){
    			if(isset($order['receiptAddress']['phoneArea'])){
    				$orderUserInfo['phone'] = isset($order['receiptAddress']['mobileNo']) ? $order['receiptAddress']['mobileNo']: "";
    			}else{
    				$orderUserInfo['phone'] = isset($order['receiptAddress']['mobileNo']) ? $order['receiptAddress']['mobileNo']: "";
    			}
    		}else{
    			$orderUserInfo['phone'] = $order['receiptAddress']['mobileNo'];
    		}
           /*************END 订单用户表数据***************/	   
			$obj_order_detail_data = array();
            $ORtransportArr = array();//运输方式数组
            $feedbackArr = array();//客户留言数组
			foreach($orderDetail2['productList'] as $orderdetail){
				//明细表
                $orderdata_detail	=	array();                       
				$orderdata_detail['recordNumber']	=	$order['id'];
                $orderdata_detail['itemId']	        =	$orderdetail['productId'];			 				
				$orderdata_detail['itemPrice']      =	$orderdetail['productUnitPrice']['amount'];
                $orderdata_detail['onlinesku']		=	$orderdetail['skuCode'];
                $orderdata_detail['sku']			=	substr($orderdetail['skuCode'],0,stripos($orderdetail['skuCode'],'#')); 
				$orderdata_detail['amount']     	=	$orderdetail['productCount']; 
				//$orderdata_detail["shippingFee"]	=	''; 
				//$orderdata_detail["reviews"]	    =	''; 
				$orderdata_detail['createdTime']    =	time();
                if(!empty($orderdetail['logisticsServiceName'])){
                    $ORtransportArr[] = $orderdetail['logisticsServiceName'];
                }
                if(!empty($orderdetail['memo'])){//客户留言
                    $feedbackArr[] = $orderdetail['memo'];
                }     				           	
				//明细扩展表
                $orderDetailExtAli = array();               
                //$orderDetailExtAli['initOrderAmtAmount']=	$order['childOrderList']['initOrderAmt']['amount'];
                $orderDetailExtAli['itemTitle']	        =	$orderdetail['productName'];
                $orderDetailExtAli['itemURL']	        =	$orderdetail['productSnapUrl'];
				$obj_order_detail_data[] = array('orderDetail' => $orderdata_detail,			
											'orderDetailExtension' => $orderDetailExtAli
											);
			}
            if(!empty($feedbackArr)){
                $orderExtAli['feedback'] = implode(',', $feedbackArr);//客户留言
            }
            $ORtransportArr = array_unique($ORtransportArr);
            if(count($ORtransportArr) == 1){
                $orderdata['ORtransport'] = $ORtransportArr[0];//订单在平台上只有一种运输方式的时候
            }elseif($ORtransportArr > 1){
                $orderExtAli['feedback'] .= implode(',', $ORtransportArr);//运输方式加到客户留言中去
            }
            
            $insertOrder = array('order' => $orderdata,
								'orderExtension' => $orderExtAli,					  
								'orderUserInfo' => $orderUserInfo
								);
								
			$insertOrder['orderDetail'] = $obj_order_detail_data;
            //print_r($insertOrder);exit;
            //订单估算信息，包材及是否是单料号单个，单料号多个或者多料号订单要等更新了可用运输方式后才去更新
            //
            //$calcOrderShippingObj = F('CalcOrderShipping');
//            $calcOrderShippingObj->setOrder($insertOrder);
//            $calcInfo = $calcOrderShippingObj->calcOrderWeight();//计算重量和包材
//			var_dump($calcInfo); exit;
//            $orderCalculationArr = array();
//			$insertOrder['orderCalculation']['calcWeight'] = $calcInfo[0];//订单自己估算的重量
//			$insertOrder['order']['pmId'] = $calcInfo[2];//包材id
//			if(count($insertOrder['orderDetail']) > 1){
//				$insertOrder['order']['orderAttribute'] = 3;
//			}else if(isset($insertOrder['orderDetail'][0]['orderDetail']['amount']) && $insertOrder['orderDetail'][0]['orderDetail']['amount'] > 1){
//				$insertOrder['order']['orderAttribute'] = 2;
//			}
//			$calcShippingInfo = $calcOrderShippingObj->calcOrderCarrierAndShippingFee();//计算运费
//			$insertOrder['order']['channelId'] = $calcShippingInfo['channelId'];	

			//if(M('OrderAdd')->insertOrderPerfect($insertOrder)){
			if(A('OrderAdd')->act_insertOrder(array($insertOrder))){
				//echo 'insert success!' . "\n";
				echo $log	=	"-----".date("Y-m-d H:i:s").", 新增订单{$order["id"]}成功\r\n";
			}else{
				echo $log	=	"-----".date("Y-m-d H:i:s").", 新增订单{$order["id"]}失败\r\n";
				print_r(M('OrderAdd')->getErrorMsg());
			}
     		$index++;
            //exit;
    	}
    }
    
    //非淘代销订单， 需要进行拦截
    /*if(!in_array($aliexpress_user, $taotao_account)){
    	$sql	= "select * from ebay_order as a where ebay_user='$user' and ebay_account = '$account' and ebay_combine!='1' and ebay_status = '595'  ";
    	$sql	= $dbConn->execute($sql);
    	$sql	= $dbConn->getResultArray($sql);
    	auto_contrast_intercept($sql);
    }*/
    
    echo "End ".date("Y-m-d H:i:s")."-----------------此次共新增 ".$index." 条数据\n\n\n";
    exit;
?>