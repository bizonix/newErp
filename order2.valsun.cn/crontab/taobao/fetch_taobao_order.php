<?php
$taobaoUser = trim($argv[1]);
if(empty($taobaoUser)){
	echo "empty user!\n";
	exit;
}
$taobaoUser = 'finejoly';
ini_set('max_execution_time', 1800);
include_once dirname(__DIR__)."/common.php";
$FZAccountList = M('Account')->getAccountNameByPlatformId(12);//芬哲
$ZGAccountList = M('Account')->getAccountNameByPlatformId(13);//哲果
$accountKV = array_merge($FZAccountList, $ZGAccountList);
$accountId = array_search($taobaoUser, $accountKV);
if($accountId === false){
    echo "no exist the user in taobaoAccountList!\n";
	exit;
}
$platformList = M('Account')->getPlatformid($accountId);
$platformId = $platformList[0]['platformId'];
echo "\n\n\nDate: ".date("Y-m-d H:i:s"). " 开始 淘宝(".$taobaoUser.")的订单同步\n";
$taobao = A('TaobaoButt');
$taobao->setConfig($taobaoUser);
$json_data = $taobao->taobaoTradesSoldGet();
var_dump($json_data);exit;

//print_r(get_account_id($account));die;
//var_dump($json_data);die;
//exit;
//分页获取后面的数据
$total_page	= 1;
if(isset($json_data['trades_sold_get_response']['total_results'])){
	$total = intval($json_data['trades_sold_get_response']['total_results']);
	if($total > $page_size){
		$total_page	= ceil($total/$page_size);
	}
	if($total == 0){
		echo "notice: no data, exit\n";
		exit;
	}
}else{
	echo "notice: no data, exit\n";
	exit;
}

$carrierKV = array();
$carrierList = M('InterfaceTran')->getCarrierList(2);//获取所有的运输方式
foreach($carrierList as $key => $trans) {
    $carrierKV[$trans['carrierNameCn']] = $trans['id'];
}
$total = 0;	//总订单条数
$error_data	= array();
for($cur_page=1;$cur_page<=$total_page;$cur_page++){
	if($cur_page > 1){
		$json_data = $taobao->taobaoTradesSoldGet();
	}
	//出错处理
	if(isset($json_data['error_response'])){
		echo "error: ".$json_data['error_response']['msg']. " error code:". $json_data['error_response']['code']."\n";
	}else{
		//数据入库
		$data = $json_data['trades_sold_get_response']['trades']['trade'];
		foreach($data as $trade){
		    $recordnumber =	$trade['sid'];//淘宝订单号
            if(empty($recordnumber) || M('OrderAdd')->checkIsExists(array('recordNumber'=>$recordNumber, 'accountId'=>$accountId))){
            	$error_data[] = "淘宝订单号 {$recordnumber} 为空 或 已经存在与系统中!\n";
				continue;
            }
			$insertOrder  = array();
			$trade_data	  =	$taobao->taobaoTradeGet($recordnumber);				
			if(!empty($recordnumber)){				
				/***************BEGIN 订单表数据***************/
				$orderdata = array();
				$orderdata['recordNumber']	        =	$recordnumber;
				$orderdata['platformId']			=	$platformId;
				$orderdata['accountId']	            =	$accountId;
				$orderdata['orderStatus']			=	C('STATEPENDING');
				$orderdata['orderType']			    =	C('STATEPENDING_CONV');            
				$orderdata['ordersTime']		    =	strtotime($trade['created']);		// 成交时间
				$orderdata['paymentTime']			=	strtotime($trade['pay_time']);		// 付款时间
				$orderdata['onlineTotal']			=	$trade['price'];  				    //线上总金额
				$orderdata['actualTotal']			=	$trade['payment'];                  //付款总金额  
				$orderdata['transportId']			=	$carrierKV[$defalut_carrier];//运输方式id,默认为该account下设置的默认运输方式，该参数要通过account动态获取
				$orderdata['isFixed']				=	1;
				$orderdata['calcWeight']			=	'';   							//估算重量
				$orderdata['calcShipping']			=	round_num($trade['post_fee'], 2);   //物流费用    	
				$orderdata['orderAddTime']			=	time();
				$orderdata['isNote']			    =	isset($trade_data['trade_get_response']['trade']['buyer_message']) ? 1:0;
				/***************END 订单表数据***************/
				
				/***************BEGIN 订单扩展表数据***************/
				$orderExtTaobao = array(); //          
				$orderExtTaobao['paymentStatus']		=	"Complete";  
				$orderExtTaobao['transId']			    =	$recordnumber;   // 交易id;;
				$orderExtTaobao['platformUsername']		=	$trade['buyer_nick'];            
				$orderExtTaobao['currency']				=	"RMB";  
				$orderExtTaobao['feedback']				=	isset($trade_data['trade_get_response']['trade']['buyer_message']) ? $trade_data['trade_get_response']['trade']['buyer_message']:"";    //客户留言 
				//$ebay_noteb								=	isset($trade_data['trade_get_response']['trade']['seller_memo']) ? $trade_data['trade_get_response']['trade']['seller_memo']:"";		// 卖家订单备注		        
				/***************END 订单扩展表数据***************/
				
				/***************BEGIN 订单用户表数据***************/
				$orderUserInfo = array();           
				$orderUserInfo['username']			=	$trade['receiver_name'];            
				$orderUserInfo['platformUsername']  =	$trade['buyer_nick'];
				$orderUserInfo['email']			    =	"";            
				$orderUserInfo['countryName']	 	=	"China";
				$orderUserInfo['countrySn']			=	"CN";            
				$orderUserInfo['currency']          =	"RMB";      	
				$orderUserInfo['state']			    =	$trade['receiver_state'];			// 省
				$orderUserInfo['city']				=	$trade['receiver_city'];			// 市           	
				$t_street							=	$trade['receiver_state']." ".$trade['receiver_city']." ".$trade['receiver_district']." ".$trade['receiver_address'];	
				$t_street							=	htmlentities($t_street, ENT_QUOTES, "UTF-8");	
				$orderUserInfo['street']			=	$t_street;
				$orderUserInfo['address2']			=	"";
				$orderUserInfo['landline']			=	$trade['receiver_phone'];			// 座机电话           
				$orderUserInfo['phone']				=	$trade['receiver_mobile'];			// 手机  
				$orderUserInfo['zipCode']			=	$trade['receiver_zip'];				// 邮编  
			   /*************END 订单用户表数据***************/
				
				//新增购物明细（每个sku一条数据）
				$orders	=	$trade['orders']['order'];
				$orderweight	=	0;
				$sku_infos = array();
				$obj_order_detail_data = array();
				foreach($orders as $order){
					/***************BEGIN 订单详细数据***************/
					$orderdata_detail = array();                     
					$orderdata_detail['recordNumber'] = $recordnumber; 
					$sku = $order['outer_sku_id'];				//SKU
					if(isset($order['outer_iid']) && !isset($order['outer_sku_id'])){
						$sku = $order['outer_iid'];
					}
					$sku_infos[] = $sku;
					$orderdata_detail['sku']			=	$sku; 
					$orderdata_detail['itemPrice']      =	round_num($order['price'], 2);		//淘宝产品标价 
					$orderdata_detail['amount']     	=	$order['num'];					//SKU数量
					//$orderdata_detail["shippingFee"]	=	''; 
					//$orderdata_detail["reviews"]	    =	''; 
					$orderdata_detail['createdTime']    =	time(); 
					/*************END 订单详细数据***************/
					
					/***************BEGIN 订单详细扩展表数据***************/
					$orderDetailExtTaobao	=	array();               
					$orderDetailExtTaobao['itemTitle']	   =	$order['title']."#".$order['sku_properties_name']."#";	//产品名称; 
					$orderDetailExtTaobao['itemURL']	   =	$order['pic_path'];                      
					$orderDetailExtTaobao['itemId']	       =	$order['sku_id'];
					$orderDetailExtTaobao['transId']	   =	$recordnumber; // 交易id;
					$orderDetailExtTaobao['note']	       =	round_num($order['payment'], 2);	//实际SKU付款价 
					/*************END 订单详细扩展表数据***************/
					
					$obj_order_detail_data[] = array('orderDetail' => $orderdata_detail,			
													'orderDetailExtension' => $orderDetailExtTaobao
													);
				}
				
				//包含HH555料号的订单移动到淘宝待审核 
				if(in_array('HH555', $sku_infos) /*|| strpos($ebay_noteb, 'ERP审核订单')!==false*/){
					$orderdata['orderType'] = C('STATEPENDING_LYNXPEND');
				}
				
				$insertOrder = array('order'          => $orderdata,
									 'orderExtension' => $orderExtTaobao,					  
									 'orderUserInfo'  => $orderUserInfo,
                                     'orderDetail'    => $obj_order_detail_data
									);
                $calcOrderShippingObj = F('CalcOrderShipping');
                $calcOrderShippingObj->setOrder($insertOrder);
                $calcInfo = $calcOrderShippingObj->calcOrderWeight();//计算重量和包材
				//var_dump($calcInfo); exit;
				$insertOrder['orderData']['calcWeight'] = $calcInfo[0];
				$insertOrder['orderData']['pmId'] = $calcInfo[1];
				if(count($insertOrder['orderDetail']) > 1){
					$insertOrder['orderData']['orderAttribute'] = 3;
				}else if(isset($insertOrder['orderDetail'][0]['orderDetailData']['amount']) && $insertOrder['orderDetail'][0]['orderDetailData']['amount'] > 1){
					$insertOrder['orderData']['orderAttribute'] = 2;
				}
				$calcShippingInfo = $calcOrderShippingObj->calcOrderCarrierAndShippingFee();//计算运费
				
				$insertOrder['orderData']['channelId'] = $calcShippingInfo['channelId'];
				
				//$insertOrder = AutoModel :: auto_contrast_intercept($insertOrder);//拦截逻辑
                try{
                    if(M('OrderAdd')->insertOrderPerfect($insertOrder)){//这里后续可能要添加插入到老系统的接口
    					echo "-----".date("Y-m-d H:i:s").", 新增订单{$orderdata["recordNumber"]}成功\r\n";
    				}else{
    					echo "-----".date("Y-m-d H:i:s").", 新增订单{$orderdata["recordNumber"]}失败\r\n";
                        continue;
    				}
                }catch(Exception $e){
                    $reason = $e->getMessage();
                    echo "-----".date("Y-m-d H:i:s").", 新增订单{$orderdata["recordNumber"]}失败，try/catch,原因为：$reason \r\n";
                    continue;
                }				
				$total++;
			}	
		}
	}
}

echo "DATE: ".date("Y-m-d H:i:s"). "-----------------------------------\n";
echo "\n".implode("\n", $error_data);
echo "\n订单导入完成, 共导入".$total."条订单\n";
exit;
?>