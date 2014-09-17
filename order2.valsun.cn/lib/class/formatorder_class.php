<?php
/*
 * 订单添加相关格式化
 * @add by : linzhengxiang ,date : 20140611
 */

class FormatOrder{
	
	private $orderbelong = 0;
	private $errMsg = array();				//装载拦截过程中的异常信息，异常信息需要提交到数据库统一管理
	public $orderData = array();
	public $order_id = '';
	
	public function __construct(){

	}
	
	/**
	 * 赋值订单变量
	 * @param array $orderData
	 * @author lzx
	 */
	public function setOrder($orderData){
		$this->orderData = $orderData;
		$this->order_id = $this->orderData['order']['id'];
	}
    
    /**
	 * 获取$this->orderData的值
	 * @author zqt
	 */
	public function getOrder($orderData){
		return $this->orderData;
	}
	
	/**
	 * 获取错误信息
	 * @eturn array 错误信息数据需要打到订单相关表中，记录错误编号用于订单查询
	 * @author lzx 
	 */
	public function getErrMsg(){
		return $this->errMsg;
	}
	
	/**
	 * 订单拦截总流程
	 */
	public function interceptOrder($orderData=array()){
		
		
		if (!empty($orderData)){
			$this->setOrder($orderData);
		}
		
		if (empty($this->orderData)){
			$this->errMsg[10118] = get_promptmsg(10118);
			return false;
		}
		
		
		$tmp_order_id = $this->orderData['order']['id'];
		
		//可以根据这个获取orderstatus所以只需要这一个就可以了
		$StatusMenu = M('StatusMenu');
		$ORDER_PENDING = $StatusMenu->getOrderStatusByStatusCode('ORDER_PENDING','id');
		$ORDER_NO_NEED_SHIP = $StatusMenu->getOrderStatusByStatusCode('ORDER_NO_NEED_SHIP','id');
		$ORDER_INVALID_ORDER = $StatusMenu->getOrderStatusByStatusCode('ORDER_INVALID_ORDER','id');
		$ORDER_BLACKLIST = $StatusMenu->getOrderStatusByStatusCode('ORDER_BLACKLIST','id');
		
		$ORDER_AUTOMATIC = $StatusMenu->getOrderStatusByStatusCode('ORDER_AUTOMATIC','id');
		$order_type = 0;
		
		$result_status_type_arr = array();
		$order_log = "";
		
		$stmOb = F('SpecialTransportMethod');
		
		$stmOb->setOrder($orderData);
		$stmOb->updateOrderUsefulTransportId();
		$orderData = $stmOb->getOrder();
		$this->setOrder($orderData);
		
		
		M('orderLog')->orderOperatorLog('no sql', '物流策略拦截完成，开始其他拦截：', $tmp_order_id);
		
		//amazon平台无需发货订单
		if ($this->isfulfillByAmazon()){
			$order_log .= "该订单是Amazon发货订单;";
			$result_status_type_arr = array('status'=>$ORDER_PENDING, 'type'=>$ORDER_NO_NEED_SHIP);
		}
		
		//ebay平台检查paypal账号是否合法
		if (empty($result_status_type_arr) && ! $this->isValidPaypalAccount()){
			$order_log .= "该订单paypal账号非法;";
			$result_status_type_arr = array('status'=>$ORDER_PENDING, 'type'=>$ORDER_INVALID_ORDER);
		}
		//检查是否在黑名单
		$error = $this->isInBlackList();
		if (empty($result_status_type_arr) &&   false !==  $error  ){
			$order_log .= "该订单的买家信息在黑名单中:".$error.";";
			$result_status_type_arr = array('status'=>$ORDER_PENDING, 'type'=>$ORDER_BLACKLIST);
		}
		
		
		//检查订单信息是否完整
		$error = $this->isMissingInfoOrder();
		if (empty($result_status_type_arr) &&  false !==  $error ){
			$order_log .= "该订单信息不完整:".$error.";";
			
			$ORDER_MISSING_INFO = $StatusMenu->getOrderStatusByStatusCode('ORDER_MISSING_INFO','id');
			
			$result_status_type_arr = array('status'=>$ORDER_PENDING, 'type'=>$ORDER_MISSING_INFO);
		}
		//检查订单sku信息是否完整
		$error = $this->isContainInvalidSkuOrder();
		if (empty($result_status_type_arr) && false !==  $error){
			$order_log .= "该订单sku信息错误：".$error.";";
			$ORDER_INVALID_SKU = $StatusMenu->getOrderStatusByStatusCode('ORDER_INVALID_SKU','id');
			$result_status_type_arr = array('status'=>$ORDER_PENDING, 'type'=>$ORDER_INVALID_SKU);
		}
		
		//检查订单是否有留言
		if (empty($result_status_type_arr) && $this->isLeaveWordOrder() ){
			$order_log .= "该订单有留言;";
			$ORDER_LEAVE_WORD = $StatusMenu->getOrderStatusByStatusCode('ORDER_LEAVE_WORD','id');
			$result_status_type_arr = array('status'=>$ORDER_PENDING, 'type'=>$ORDER_LEAVE_WORD);
		}
		//检查订单是否是超大订单
		if (empty($result_status_type_arr) && $this->isSuperLargeOrder() ){
			$order_log .= "该订单是超大订单;";
			$ORDER_SUPER_LARGE = $StatusMenu->getOrderStatusByStatusCode('ORDER_SUPER_LARGE','id');
			$result_status_type_arr = array('status'=>$ORDER_AUTOMATIC, 'type'=>$ORDER_SUPER_LARGE);
		}
		//检查订单是否缺货
		if (empty($result_status_type_arr) && $outOfStockResult = $this->isOutOfStockOrder() ){
			
			if($outOfStockResult === 'ORDER_EXPRESS_OUT_OF_STOCK'){
				$order_log .= "该订单是快递缺货订单，需手动拆分;";
				$ORDER_EXPRESS_OUT_OF_STOCK = $StatusMenu->getOrderStatusByStatusCode('ORDER_EXPRESS_OUT_OF_STOCK','id');
				$result_status_type_arr = array('status'=>$ORDER_AUTOMATIC, 'type'=>$ORDER_EXPRESS_OUT_OF_STOCK);
			}else if( is_array($outOfStockResult) ){
				$orderId = $this->order_id;
				
				
				$error_tmp = A('orderManage')->act_splitOrderWithOrderDetail($orderId, $outOfStockResult);
				
				M('Base')->begin();
				
				M('orderLog')->orderOperatorLog('no sql', '自动拆分全部完成，返回数据：'.json_encode($error_tmp), $orderId);
				
				if(is_string($error_tmp)){
					$order_log .= $error_tmp;
				}
				$ORDER_PACKAGE_OUT_OF_STOCK = $StatusMenu->getOrderStatusByStatusCode('ORDER_PACKAGE_OUT_OF_STOCK','id');
				//原订单虽然已经删除，还是需要更新原订单的状态，防止下面的超重拆分
				$result_status_type_arr = array('status'=>$ORDER_AUTOMATIC, 'type'=>$ORDER_PACKAGE_OUT_OF_STOCK);
				
			}else if($outOfStockResult === true){
				$order_log .= "该订单是小包缺货订单，订单不能自动拆分;";
				$ORDER_PACKAGE_OUT_OF_STOCK = $StatusMenu->getOrderStatusByStatusCode('ORDER_PACKAGE_OUT_OF_STOCK','id');
				$result_status_type_arr = array('status'=>$ORDER_AUTOMATIC, 'type'=>$ORDER_PACKAGE_OUT_OF_STOCK);
			}else{
				$order_log .= "该订单是小包缺货订单，自动拆分失败;";
				$ORDER_PACKAGE_OUT_OF_STOCK = $StatusMenu->getOrderStatusByStatusCode('ORDER_PACKAGE_OUT_OF_STOCK','id');
				$result_status_type_arr = array('status'=>$ORDER_AUTOMATIC, 'type'=>$ORDER_PACKAGE_OUT_OF_STOCK);
			}
			
			//缺货同样占用库存 ， 更新该订单中sku的待发货数量
			F('SkuDailyInfo')->updateDailyAverageInfoByOrder($this->order_id);
		}
		
		
		if (empty($result_status_type_arr)){
			//超重订单处理（超重订单自动拆分，包括修改主订单和子订单的状态）
			$is_over_weight = M('orderManage')->handleOverWeightOrder($this->orderData);
			
			//订单只有一个sku，并且超重
			if($is_over_weight === 'ONLY_ONE_SKU_OVER_HEIGHT'){
				$order_log .= "该订单是单sku超重订单，不能拆分;";
				$ONLY_ONE_SKU_OVER_HEIGHT = $StatusMenu->getOrderStatusByStatusCode('ONLY_ONE_SKU_OVER_HEIGHT','id');
				$result_status_type_arr = array('status'=>$ORDER_AUTOMATIC, 'type'=>$ONLY_ONE_SKU_OVER_HEIGHT);
			}else if($is_over_weight === true){
				$order_log .= "该订单是超重订单，已经自动拆分;";
				$NO_NEED_SHIP = $StatusMenu->getOrderStatusByStatusCode('NO_NEED_SHIP','id');
				$result_status_type_arr = array('status'=>$ORDER_AUTOMATIC, 'type'=>$NO_NEED_SHIP);
			}else{
				$order_log .= $is_over_weight;
			}
		}
		
		
		
		if(empty($order_log)){
			$order_log = "订单拦截结果：正常订单";//进入待发货状态
			$ORDER_WAIT_SHIP = $StatusMenu->getOrderStatusByStatusCode('ORDER_WAIT_SHIP','id');
			$result_status_type_arr = array('status'=>$ORDER_WAIT_SHIP, 'type'=>$ORDER_WAIT_SHIP);
			
			
		}else{
			$order_log = "订单拦截结果：".$order_log;
		}

		$order_log = $order_log.json_encode($result_status_type_arr);
		
		M('orderLog')->orderOperatorLog('no sql', $order_log, $tmp_order_id);
		
		if (!empty($result_status_type_arr)){
			$update_data =  array('orderStatus'=>$result_status_type_arr['status'],'orderType'=>$result_status_type_arr['type']);
			M('orderModify')->updateOrderInfo($tmp_order_id, $update_data);
			
			//更新该订单中sku的待发货数量
			F('SkuDailyInfo')->updateDailyAverageInfoByOrder($this->order_id);
		}
		
		M('Base')->commit();
		
		
		/**
		//为后面的超大订单拦截和缺货拦截使用
		$this->orderbelong = $this->MarkOverSeaOrder();
		if ($ordertype = $this->interceptLargeOrder()){
			return $ordertype;
		}
		if ($ordertype = $this->interceptOutOfStockOrder()){
			return $ordertype;
		}
		/**/
		#####  其他需要拦截的以此类推    ######
		//通过所有拦截为正常待处理订单
		return true;
	}
	/**
	 * amazon平台无需发货订单（发货由amzon仓库完成）
	 * @eturn array 返回ordertype
	 * @author lzx 
	 */
	public function isfulfillByAmazon(){
		$order 			= $this->orderData['order'];
		$orderExtension = $this->orderData['orderExtension'];
		
		//订单是amazon平台 并且 订单有amazon配送，则无需发货
		if($order['platformId'] == C('PLATFORM_AMAZON') && $orderExtension['fulfillmentChannel'] == 'AFN'){
			return true;
		}
		
		return false;
	}
	/**
	 * //订单是ebay平台 ,并且待检查email在合法列表中，则返回true
	 * @return bool
	 * @author andy 
	 */
	public function isValidPaypalAccount(){
		$order 				= $this->orderData['order'];
		$PayPalEmailAddress = trim($this->orderData['orderExtension']['PayPalEmailAddress']);
		
		if($order['platformId'] == C('PLATFORM_EBAY') && empty($PayPalEmailAddress)){
			return false;
		}
		
		if($order['platformId'] == C('PLATFORM_EBAY') && !empty($PayPalEmailAddress) ){
			$num = M("paypalEmail")->getPaypalEmailCount($PayPalEmailAddress);
			if($num > 0){
				return true;
			}
			return false;
		}
		
		return true;
	}
	/**
	 * 检查是否在黑名单
	 * @return bool
	 * @author andy 
	 */
	public function isInBlackList(){
		$PayPalEmailAddress = trim($this->orderData['orderExtension']['PayPalEmailAddress']);
		$order 			= $this->orderData['order'];
		$orderExtension = $this->orderData['orderExtension'];
		$orderUserInfo  = $this->orderData['orderUserInfo'];
		
		$platformId 		= $order['platformId'];
		$accountId 			= $order['accountId'];
		
		$platformUsername 	= $orderUserInfo['platformUsername'];
		$username 			= $orderUserInfo['username'];
		$email 				= $orderUserInfo['email'];
		$address1 			= $orderUserInfo['address1'];
		$phone 				= $orderUserInfo['phone'];
		
		$black_list = array(
							'usermail'			=> $email,
						);
		
		if(A('Blacklist')->act_isExitInBlacklist($black_list)){
			return '该客户的email在黑名单中';
		}
		$black_list = array(
							'address1'			=> $address1,
						);
		
		if(A('Blacklist')->act_isExitInBlacklist($black_list)){
			return '该客户的街道在黑名单中';
		}
		$black_list = array(
							'phone'				=> $phone,
						);
		
		if(A('Blacklist')->act_isExitInBlacklist($black_list)){
			return '该客户的手机号在黑名单中';
		}
		$black_list = array('platformUsername' 	=> $platformUsername,
							'platformId'			=> $platformId,
						);
		
		if(A('Blacklist')->act_isExitInBlacklist($black_list)){
			return '该客户的(平台+用户名)在黑名单中';
		}
		
		
		return false;
	}
	
	/**
	 * 检查平台信息是否完整
	 * @return bool
	 * @author andy 
	 */
	public function isMissingInfoOrder(){
		$accountId 	 = $this->orderData['order']['accountId'];
		//$transportId = $this->orderData['order']['transportId'];
		//$CarrierName = M("InterfaceTran")->getCarrierNameById($transportId);

		$usefulChannelId = $this->orderData['order']['usefulChannelId'];
		
		$actualTotal = floatval($this->orderData['order']['actualTotal']);
		$orderType 	 = $this->orderData['order']['orderType'];
		$actualShipping 	 = $this->orderData['order']['actualShipping'];
		$countryName = $this->orderData['orderUserInfo']['countryName'];
		$orderDetail = $this->orderData['orderDetail'];
		$trueTotal 	 = 0;
		
		foreach($orderDetail as $detail){
			$trueTotal += $detail['orderDetail']['itemPrice'] * $detail['orderDetail']['amount'];
			
		}
		
		//单价x数量+运费
		$trueTotal = $trueTotal + floatval($actualShipping);
		
		$platformUsername = $this->orderData['orderUserInfo']['platformUsername'];
		$username = $this->orderData['orderUserInfo']['username'];
		$email = $this->orderData['orderUserInfo']['email'];
		$address1 = $this->orderData['orderUserInfo']['address1'];
		$phone = $this->orderData['orderUserInfo']['phone'];
		$city = $this->orderData['orderUserInfo']['city'];
		$state = $this->orderData['orderUserInfo']['state'];
		
		
		if(in_array($email, array("", "Invalid Request")) || strpos($email,'@')==false ){
			return 'email格式错误;'; 
		}else if(strval($trueTotal) != strval($actualTotal) ){//比较2个实数的bug
			return '订单总金额('.$actualTotal.')和订单详情('.$trueTotal.')不匹配;';
		}else if(empty($countryName) || empty($city) || empty($address1) || empty($usefulChannelId)){
			return "订单国家(".$countryName.")、城市(".$city.")、街道(address1)(".$address1.")、可用运输方式(".$usefulChannelId.")之一错误;";
		}
		
		return false;
	}
	public function isContainInvalidSkuOrder(){
		$orderDetail = $this->orderData['orderDetail'];
		
		foreach($orderDetail as $detail){
			$tmp_sku = $detail['orderDetail']['sku'];
			//如果$tmp_sku是组合sku,返回的数组中包括了子sku
			$sku_info = M("InterfacePc")->getSkuinfo($tmp_sku);
			$sku_info = $sku_info['skuInfo'];
			
			//如果$tmp_sku是组合sku,其子sku都必须有仓位号
			foreach ($sku_info as $real_sku=>$real_sku_val){
				$skulocation = M("InterfaceWh")->getSkuPosition($real_sku);//获取仓位数组，包含多仓位
				if(!$skulocation){//缺少仓位号
					if($real_sku == $tmp_sku){
						return $real_sku.'缺少仓位号;'; 
					}else{
						return 'sku:'.$tmp_sku.'的子sku:'.$real_sku.'缺少仓位号;'; 
					}
					
					$sku_weight = M("InterfacePc")->getSkuWeight($real_sku);
					
					if(empty($sku_weight)){
						return 'sku:'.$tmp_sku.'的子sku:'.$real_sku.'缺少重量;'; 
					}
					//缺少重量或者采购人
					if( empty($real_sku_val['skuDetail']['purchaseId'])){
						return $tmp_sku.'缺少采购人;'; 
					}
					
					$spu = $real_sku_val['skuDetail']['spu'];
					$spu_arr = array($spu);
					$tmp_hs_arr = M("InterfacePc")->getHscodeInfoBySpuArr(json_encode($spu_arr));
					
					//缺少海关编码
					if(empty($tmp_hs_arr) ||  empty($tmp_hs_arr[$spu]['hsCode'])){
						return 'sku:'.$tmp_sku.'的子sku:'.$real_sku.'缺少海关编码;';
					}	
				}
			}
			
			//echo '==<pre>'.$tmp_sku;print_r($sku_info);exit;
			//如果该sku是组合sku，获取的重量是总重量
			
			if(empty($sku_info)){
				return $tmp_sku.'没有该sku信息;'; 
			}
			
			
			
		}
		
		return false;
	}
	/**
	 * 有留言订单拦截
	 * @eturn array 返回ordertype
	 * @author andy 
	 */
	public function isLeaveWordOrder(){
		$orderExtension = $this->orderData['orderExtension'];
		
		$StatusMenu = M('StatusMenu');
		//判断改订单是否是留言已经处理过
		$ORDER_LEAVE_WORD = $StatusMenu->getOrderStatusByStatusCode('ORDER_LEAVE_WORD','id');
		$tmp_arr = M('OrderOperated')->get_operateByAS($this->order_id, "", $ORDER_LEAVE_WORD);
		if(!empty($orderExtension['feedback']) && strtolower($orderExtension['feedback']) != 'none'){
				return true;
		}
		
		return false;
	}
	
	/**
	 * 是否是超大拦截
	 * @eturn array 返回ordertype
	 * @author andy 
	 */
	public function isSuperLargeOrder(){
		
		F('order');//引进order_functions.php
		
		$orderDetail = $this->orderData['orderDetail'];
		$order_log = '检测是否超大信息：';
		
		$splittedMainOrderId = M('order')->getSplittedMainOrderId($this->order_id);
		
		//判断该订单是否是超大审核通过 或者 如果该订单是拆分出来的子订单
		if( $GLOBALS['is_from_over_weight_order'] == true || M('SuperAmountSku')->checkIsAuditted($this->order_id) === true || M('SuperAmountSku')->checkIsAuditted($splittedMainOrderId) === true){
			$GLOBALS['is_from_over_weight_order'] = false;
			M('orderLog')->orderOperatorLog('no sql', "超大审核通过", $this->order_id);
			return false;
		}
		
		//如果该订单是超重订单拆分出来的子订单，不拦截

		foreach($orderDetail as $detail){
			$hava_goodscount = true;
			$sku 		= $detail['orderDetail']['sku'];
			$amount 	= $detail['orderDetail']['amount'];
			$detail_id 	= $detail['orderDetail']['id'];
			$storeId 	= $detail['orderDetail']['storeId'];
			
			//$sku有可能是组合sku
			$skuinfo 	= M("InterfacePc")->getSkuinfo($sku);
			
			/***筛选订单中的超大订单料号 Start ***/
			foreach($skuinfo['skuInfo'] as $or_sku => $skuinfoDetailValue){
				//$allnums = $skuinfoDetailValue['amount'];
				
				//实际数量等于 组合料号中的配置数量乘以购买数量
				$real_buy_numbers = ceil($amount * $skuinfoDetailValue['amount']);
				$order_log .= "<br>sku {$or_sku}实际购买数量为：{$real_buy_numbers},";
				//从仓库接口获取可用库存
				$real_stock_num = getSkuRealStock($or_sku, $storeId);//料号可以用150测试
				$order_log .= "实际库存：{$real_stock_num},";
				//获取日均销量
				$average_day_sale = F('SkuDailyInfo')->getSkuAverageDailyCount($sku);
				$order_log .= "每日均量：{$average_day_sale},";
				
				$average_num_10times = ceil($average_day_sale * 10);
				$buy_numbers_equ_stock_numbers_times = $real_stock_num>0?round($real_buy_numbers / $real_stock_num,2):0;//购买数量是实际库存的倍数
				
				$is_super_sku = false;
				
				/**
				 * 1.购买数量大于9  且 大于10倍均量
				 * 2.购买数量大于库存的一半   且 大于10倍均量
				 */
				if($real_buy_numbers > 9 && $real_buy_numbers > $average_num_10times){
					$is_super_sku = true;
				}else if($buy_numbers_equ_stock_numbers_times > 0.5 
							&& $real_buy_numbers > $average_num_10times 
							&& $real_stock_num>0 && $average_num_10times>0){
								
					$is_super_sku = true;
					
				}
				
				
				if ($is_super_sku){
					M('orderLog')->orderOperatorLog('no sql', $order_log."  {$or_sku}超大", $this->order_id);
					//超大订单状态
					//加入超大sku到表
					if(empty($detail_id)) $detail_id = 0;
					
						$super_amount_sku_data = array('omOrderId'=>$this->order_id,
														'auditTime'=>time(),
														'auditUser'=>0,
														//'creator'=>empty($_SESSION['sysUserId'])?'0':$_SESSION['sysUserId'],
														'omOrderdetailId'=>$detail_id,
														'sku'=>$sku,'amount'=>$amount);
						
						M('SuperAmountSku')->addSuperAmountSku($super_amount_sku_data);
						publishMQ(json_encode($super_amount_sku_data));
						
					return true;
				}
				
				M('orderLog')->orderOperatorLog('no sql', $order_log."  {$or_sku}不是超大", $this->order_id);
			}
		}
		
		return false;
	}
	/**
	 * 是否是缺货订单 
	 * @eturn array 返回ordertype
	 * @author andy 
	 */
	public function isOutOfStockOrder(){
		$record_details = array();
		$orderStatus = $this->orderData['order']['orderStatus'];
		$ORDER_STATUS = C('ORDER_STATUS');
		$isExpressDelivery = empty($this->orderData['order']['isExpressDelivery'])? 0 : $this->orderData['order']['isExpressDelivery'];
		
		$orderType = $this->orderData['order']['orderType'];
		$accountId = $this->orderData['order']['accountId'];
		$transportId = $this->orderData['order']['transportId'];
		$countryName = $this->orderData['orderUserInfo']['countryName'];
		$username = $this->orderData['orderUserInfo']['username'];
		$orderDetail = $this->orderData['orderDetail'];
		
		$in_stock_sku_arr = array();
		$out_of_stock_sku_arr = array();
			
		foreach($orderDetail as $detail){
			$hava_goodscount = true;
			$sku = $detail['orderDetail']['sku'];
			$storeId = $detail['orderDetail']['storeId'];
			$amount = $detail['orderDetail']['amount'];
			$detail_id = $detail['orderDetail']['id'];
			$omOrderId = $detail['orderDetail']['omOrderId'];
			
			$skuinfo_from_pc = M("InterfacePc")->getSkuinfo($sku);//支持虚拟sku
			if(empty($skuinfo_from_pc) || empty($skuinfo_from_pc['skuInfo'])){
				continue;
			}
			
			$skuInfo_tmp = $skuinfo_from_pc['skuInfo'];
			
			foreach($skuInfo_tmp as $pc_sku=>$pc_sku_detail_or_amount){
				
					//根据sku和storeID获取实际库存（不同sku的storeid不一样，所以只能一个一个获取库存）
					$sku_stock_info = M("interfaceWh")->getSkuStock(array($storeId=>array($pc_sku)));
					$tmp_stock_number = 0;
					if(!empty($sku_stock_info)){
						$tmp_stock_number = $sku_stock_info[$pc_sku][$storeId];
					}
					
					$pc_amount = $pc_sku_detail_or_amount['amount'];
					
					//客户购买数量乘以虚拟料号中的数量
					$total_detail_amount = $amount * $pc_amount;
					
					//获取待发货库存
					
					//当前订单如果是在待发货状态，需要排除掉
					 //判断当前待拆分订单的状态，拆分后改订单作废。如果是待发货或者缺货状态，需要减少待发货数量
		            if(in_array($orderType,$ORDER_STATUS['waitingsend'])){
		            	//更新该订单中sku的待发货数量
						$sku_wait_ship_number = F('SkuDailyInfo')->getWaitingSendCount($sku,$total_detail_amount);
		            }else{
		            	$sku_wait_ship_number = F('SkuDailyInfo')->getWaitingSendCount($sku);
		            }
            
					//$sku_wait_ship_number = $sku_wait_ship_number + $total_detail_amount;//待发货的数量+当前订单购买数量
					
					$order_log = '开始检验sku '.$pc_sku.'('.$sku.')是否缺货：仓库实际库存为：'.$tmp_stock_number.', 该订单购买数量：'.$total_detail_amount.',待发货数量：'.$sku_wait_ship_number;
					M('orderLog')->orderOperatorLog('no sql', $order_log, $this->order_id);
					
					if($tmp_stock_number - $sku_wait_ship_number  < 0){//该订单全部缺货
						
						//如果是快递订单，直接进入 快递缺货状态
						if($isExpressDelivery == 1){
							return 'ORDER_EXPRESS_OUT_OF_STOCK';
						}
						
						$out_of_stock_sku_arr[$pc_sku] = $amount;
						
					}else if($tmp_stock_number - $sku_wait_ship_number >0 &&  $tmp_stock_number - $sku_wait_ship_number - $total_detail_amount  < 0){//部分缺货处理
						
						//如果是快递订单，直接进入 快递缺货状态
						if($isExpressDelivery == 1){
							return 'ORDER_EXPRESS_OUT_OF_STOCK';
						}
						
						//把该sku分为缺货和不缺货2部分
						$in_stock_sku_arr[$pc_sku] = $tmp_stock_number - $sku_wait_ship_number;
						$out_of_stock_sku_arr[$pc_sku] = intval($total_detail_amount) - intval($in_stock_sku_arr[$pc_sku]);
						
					}else{
						$in_stock_sku_arr[$pc_sku] = $amount;
					}
			
			}
			
			
		}
		//echo '11:';print_r($out_of_stock_sku_arr);print_r($in_stock_sku_arr);exit;
		//全部sku缺货，无需拆分
		if(count($out_of_stock_sku_arr) > 0 && count($in_stock_sku_arr) == 0){
			M('orderLog')->orderOperatorLog('no sql', '该订单sku均无库存', $this->order_id);
			return true;//缺货订单
		}
		
		//部分缺货,需要拆分订单，重新进行拦截处理
		if(count($out_of_stock_sku_arr) > 0 && count($in_stock_sku_arr) > 0){
		
			$split_order_info = array($in_stock_sku_arr, $out_of_stock_sku_arr);
			M('orderLog')->orderOperatorLog('no sql', '该订单拆分规则为：'.json_encode($split_order_info), $this->order_id);
			return $split_order_info;
			
			
		}
		
		return false;
	}
	/**
	 * 非订单拦截，只拦截黑名单和非法邮箱， 待开发对接paypal验证订单付款
	 * @eturn array 返回ordertype
	 * @author lzx 
	 */
	public function interceptIllegalOrder(){
		$accountId = $this->orderData['order']['accountId'];
		$PayPalEmailAddress = $this->orderData['orderExtensionAliexpress']['PayPalEmailAddress'];
		if(!empty($PayPalEmailAddress) && !in_array(strtolower($PayPalEmailAddress),A('PaypalEmail')->act_getPaypalEmailByAccountId($accountId))){
			return $ordertype = C('STATEPENDING_EXCPAY'); //付款邮箱如果不在对应邮箱中
		}
		$platformUsername = $this->orderData['orderUserInfo']['platformUsername'];
		$username = $this->orderData['orderUserInfo']['username'];
		$email = $this->orderData['orderUserInfo']['email'];
		$street = $this->orderData['orderUserInfo']['street'];
		$phone = $this->orderData['orderUserInfo']['phone'];
		
		$black_list = array('platformUsername'=>$platformUsername,'username'=>$username,'usermail'=>$email,'street'=>$street,'phone'=>$phone,'account'=>$accountId);
		if(A('Blacklist')->act_isExitInBlacklist($black_list)){
			return $ordertype = C('STATEPENDING_BL'); //付款邮箱如果不在对应邮箱中
		}
		return false;
	}
	
	/**
	 * 拦截信息不全订单，包括国家格式不正确、订单料号缺失、订单金额对比订单详情金额不匹配、运输方式无法识别、料号有误、仓位不存在等
	 * @eturn array 返回ordertype
	 * @author lzx 
	 */
	public function interceptMissInfoOrder(){
		$accountId = $this->orderData['order']['accountId'];
		$transportId = $this->orderData['order']['transportId'];
		$CarrierName = M("InterfaceTran")->getCarrierNameById($transportId);
		$actualTotal = $this->orderData['order']['actualTotal'];
		$orderType = $this->orderData['order']['orderType'];
		$countryName = $this->orderData['orderUserInfo']['countryName'];
		$orderDetail = $this->orderData['orderDetail'];
		$trueTotal = 0;
		foreach($orderDetail as $detail){
			$trueTotal += $detail['orderDetail']['itemPrice'] * $detail['orderDetail']['amount'];
			$skulocation = M("InterfaceWh")->getSkuPosition($detail['orderDetail']['sku']);//获取仓位数组，包含多仓位
			if(!$skulocation){
				return $orderType = C('STATESYNCINTERCEPT_NL');//无仓位订单移动到同步异常订单 
			}
		}
		$platformUsername = $this->orderData['orderUserInfo']['platformUsername'];
		$username = $this->orderData['orderUserInfo']['username'];
		$email = $this->orderData['orderUserInfo']['email'];
		$street = $this->orderData['orderUserInfo']['street'];
		$phone = $this->orderData['orderUserInfo']['phone'];
		
		if(in_array($email, array("", "Invalid Request")) && $CarrierName=='EUB'){//EUB运输方式匹配
			return $orderType = C('STATESYNCINTERCEPT_AB');//移动到同步异常订单中
		}else if($trueTotal != $actualTotal && $orderType == C('STATEPENDING_CONV')){
			//ebay total 和单价数量不一致问题移动异常订单
			return $orderType = C('STATESYNCINTERCEPT_AB');//移动到同步异常订单中
		}else if(empty($countryName)){
			return $orderType = C('STATESYNCINTERCEPT_AB');//移动到同步异常订单中
		}else if(empty($transportId) || !$CarrierName){
			return $orderType = C('STATESYNCINTERCEPT_AB');//移动到同步异常订单中
		}
		if(in_array($countryName, array('Russian Federation', 'Russia', 'Belarus','Brazil','Brasil','Argentina','Ukraine')) && str_word_count($username) < 2){
			return $orderType = C('STATESYNCINTERCEPT_AD');
		}
		return false;
	}
	
	/**
	 * 标记订单是否包括海外仓料号，0为异常订单、1为国内订单、2为包含海外料号和国内料号订单、3国内混仓订单，4为美国A仓订单（先考虑手动拆分，如果业务部门OK考虑自动拆分）
	 * 预留数字3、4、5、6.....扩展到多个海外仓订单
	 * 该功能关联到后面的缺货拦截和超大订单拦截
	 */
	public function MarkOverSeaOrder(){
		$storelist 	  = array();
		$domesticskus = array();
		foreach($this->orderData['orderDetail'] as $key=>$detail){
			$onlinesku = $detail['orderDetail']['onlinesku'];
			if(preg_match("/^US01\++/i", $onlinesku)>0 || preg_match("/^US1\+.+/i", $onlinesku)>0){
				array_push($storelist, 4);
				$this->orderData['orderDetail'][$key]['orderDetail']['storeId'] = 4;
				continue;
			}
			$domesticskus[$key] = $detail['orderDetail']['sku'];
		}
		
		$stores = M('InterfaceWh')->getSkuStores(array_values($domesticskus));
		foreach ($domesticskus AS $key=>$sku){
			$_store = $stores[$sku][0];
			if (!empty($_store)){
				array_push($storelist, $_store['storeId']);
				$this->orderData['orderDetail'][$key]['orderDetail']['storeId'] = $_store['storeId'];
			}else{
				array_push($storelist, 0);
				$this->orderData['orderDetail'][$key]['orderDetail']['storeId'] = 0;
			}
		}
		$storelist = array_unique($storelist);
		if (in_array(0, $storelist)){
			$this->orderData['order']['orderStore'] = 0;
		}else if (in_array(4, $storelist)&&(in_array(1, $storelist)||in_array(2, $storelist))){
			$this->orderData['order']['orderStore'] = 2;
		}else if (in_array(1, $storelist)&&in_array(2, $storelist)){
			$this->orderData['order']['orderStore'] = 3;
		}else if (in_array(1, $storelist)||in_array(2, $storelist)){
			$this->orderData['order']['orderStore'] = 1;
		}else{
			$this->orderData['order']['orderStore'] = 0;
		}
	}

	/**
	 * 超大订单拦截，只拦截超大订单
	 * @eturn array 返回ordertype
	 * @author lzx 
	 */
	public function interceptLargeOrder(){
		$is_640 = false;
		$accountId = $this->orderData['order']['accountId'];
		$orderDetail = $this->orderData['orderDetail'];
		foreach($orderDetail as $detail){
			$hava_goodscount = true;
			$sku 		= $detail['orderDetail']['sku'];
			$amount 	= $detail['orderDetail']['amount'];
			$skuinfo 	= M("InterfacePc")->getSkuinfo($sku);
			/***筛选订单中的超大订单料号 Start ***/
			foreach($skuinfo['skuInfo'] as $or_sku => $skuinfoDetailValue){
				//$allnums = $skuinfoDetailValue['amount'];
				if (!M("InterfacePurchase")->check_sku($skuinfoDetailValue['skuDetail'], $amount)){
					//超大订单状态
					$is_640 = true;
					break;
				}
			}
		}
		if($is_640){
			if(in_array($accountId, M('Account')->getAccountNameByPlatformId(2))){
				return $orderType = C('STATEOVERSIZEDORDERS_CONFIRM');
			}else{
				return $orderType = C('STATEOVERSIZEDORDERS_PEND');
			}
		}
		return false;
	}

	/**
	 * 缺货拦截，只拦截订单是否有货
	 * @eturn array 返回ordertype
	 * @author lzx 
	 */
	public function interceptOutOfStockOrder(){
		$record_details = array();
		$orderStatus = $this->orderData['order']['orderStatus'];
		$orderType = $this->orderData['order']['orderType'];
		$accountId = $this->orderData['order']['accountId'];
		$transportId = $this->orderData['order']['transportId'];
		$countryName = $this->orderData['orderUserInfo']['countryName'];
		$username = $this->orderData['orderUserInfo']['username'];
		$orderDetail = $this->orderData['orderDetail'];
		foreach($orderDetail as $detail){
			$hava_goodscount = true;
			$sku = $detail['orderDetail']['sku'];
			$skuinfo = M("InterfacePc")->getSkuinfo($sku);
			/***筛选订单中的超大订单料号 Start ***/
			foreach($skuinfo['skuInfo'] as $or_sku => $skuinfoDetailValue){
				$allnums = $skuinfoDetailValue['amount'];
				
				$salensend = getpartsaleandnosendall($or_sku, $defaultstoreid);//预留接口
				
				//$sql = "UPDATE ebay_sku_statistics SET salensend = $salensend WHERE sku = '$or_sku' ";
				//$dbcon->execute($sql);
				//$log_data .= "[".date("Y-m-d H:i:s")."]\t---{$sql}\n\n";
				//$log_data .= "订单===$ebay_id===料号==$or_sku===实际库存为{$skuinfo['realnums']}===B仓库库存为{$skuinfo['secondCount']}===需求量为{$allnums}===待发货数量为{$salensend}===\n";
				$realnums = isset($skuinfo['realnums']) ? $skuinfo['realnums'] : 0;
				$secondCount = isset($skuinfo['secondCount']) ? $skuinfo['secondCount'] : 0;
				if(in_array($orderType, array(C('STATEOUTOFSTOCK_KD'),C('STATEOUTOFSTOCK_AO'),C('STATEOUTOFSTOCK_PO'),C('STATEOUTOFSTOCK_BKD')))){
					$remainNum = $realnums + $secondCount - $allnums - $salensend;
				}else{
					$remainNum = $realnums + $secondCount - $salensend;	
				}
				if($remainNum < 0){
					$hava_goodscount = false;
					break;
				}
			}
			if($hava_goodscount){$record_details[] = $detail;}
		}
		$count_record_details = count($record_details);
		$count_orderdetaillist = count($orderDetail);
		//$orderType = $orderStatus; //原始状态
		if($count_record_details == 0){
			//更新至自动拦截发货状态
			if (!in_array($transportId, M("InterfaceTran")->getCarrierNameList(0, true))){//非快递
				$orderType = C('STATEOUTOFSTOCK_KD');
			}else {
				$orderType = C('STATEOUTOFSTOCK_AO');
			}
			return $orderType;
		}else if($count_record_details < $count_orderdetaillist){
			//更新至自动部分发货状态
			if (!in_array($transportId, M("InterfaceTran")->getCarrierNameList(0, true))){
				$orderType = C('STATEOVERSIZEDORDERS_PEND');
				if(in_array($accountId, $SYSTEM_ACCOUNTS['cndirect']) || in_array($accountId, $SYSTEM_ACCOUNTS['dresslink'])){
					$orderType = C('STATEOUTOFSTOCK_BKD');//add by Herman.Xi@20131202 部分包货料号订单进入
				}
			}else {
				$orderType = C('STATEOUTOFSTOCK_PO');
			}
			return $orderType;
		}else if($count_record_details == $count_orderdetaillist){
			//正常发货状态
			if(in_array($accountId, M('Account')->getAccountNameByPlatformId(1)) || in_array($accountId, M('Account')->getAccountNameByPlatformId(14))){
				$status683 = false;
				if(in_array($countryName, array('Belarus','Brazil','Brasil','Argentina','Ukraine')) && str_word_count($username) < 2){
					$status683 = true;
				}
				if($status683){
					$orderType = C('STATESYNCINTERCEPT_AD');
				}
				if(in_array($orderType, array(C('STATEOUTOFSTOCK_KD'),C('STATEOUTOFSTOCK_AO'),C('STATEOUTOFSTOCK_PO'),C('STATEOUTOFSTOCK_BKD'),C('STATESYNCINTERCEPT_NL')))){
					//$orderType = 618;//ebay订单自动拦截有货不能移动到待处理和有留言 modified by Herman.Xi @ 20130325(移动到缺货需打印中)
					/*if($ebay_note != ''){
						echo "有留言\t";
						$orderType = 593;
					}else{*/
						$orderType = C('STATESYNCINTERCEPT_QXP');
					//}
				}else{
					/*if($ebay_note != ''){
						echo "有留言\t";
						$orderType = 593;
					}else{*/
						$orderType = C('STATEPENDING_CONV');
					//}
				}
			}else if(in_array($accountId, M('Account')->getAccountNameByPlatformId(2)) /*|| in_array($accountId, $SYSTEM_ACCOUNTS['B2B外单'])*/){
				$orderType = C('STATEPENDING_ALIEXPRESS');
				$status683 = false;
				if(in_array($countryName, array('Russian Federation', 'Russia')) && strpos($CarrierName, '中国邮政')!==false && str_word_count($username) < 2){
					$status683 = true;
				}
				if(in_array($countryName, array('Belarus','Brazil','Brasil','Argentina','Ukraine')) && str_word_count($ebay_username) < 2){
					$status683 = true;
				}
				if($status683){
					$orderType = C('STATESYNCINTERCEPT_AD');
				}
			}else if(in_array($accountId, M('Account')->getAccountNameByPlatformId(4))){//aliexpress
				$orderType = C('STATEPENDING_DHGATE');
			}else if(in_array($accountId, M('Account')->getAccountNameByPlatformId(10))){//dresslink.com
				$orderType = C('STATEPENDING_CONV');
			}else if(in_array($accountId, M('Account')->getAccountNameByPlatformId(8))){//cndirect.com
				$orderType = C('STATEPENDING_CONV');
			}else if(in_array($accountId, M('Account')->getAccountNameByPlatformId(11))){//Amazon
				if(in_array($orderType, array(C('STATEOUTOFSTOCK_KD'),C('STATEOUTOFSTOCK_AO'),C('STATEOUTOFSTOCK_PO'),C('STATEOUTOFSTOCK_BKD'),C('STATESYNCINTERCEPT_NL')))){
					if (in_array($transportId, M("InterfaceTran")->getCarrierNameList(0, true))){
						$orderType = C('STATESYNCINTERCEPT_QXP'); //modified by Herman.Xi @20131106 刘丽需要修改成缺货需打印中
					}else if($CarrierName == 'FedEx'){
						$orderType = C('STATEPENDING_OFFLINE'); //modified by Herman.Xi @20131213 刘丽需要修改线下订单导入
					}else{
						$orderType = C('STATEOVERSIZEDORDERS_TA'); //modified by Herman.Xi @20131119 刘丽需要修改成待打印线下和异常订单
					}
				}else{
					$orderType = C('STATEPENDING_CONV');
				}
			}else{
				$orderType = C('STATEPENDING_CONV');
			}
			return $orderType;
		}
		return false;
	}
	
	/**
	 * 有留言订单拦截
	 * @eturn array 返回ordertype
	 * @author lzx 
	 */
	public function interceptHaveMessageOrder(){
		$orderType = $this->orderData['order']['orderType'];
		$feedback = $this->orderData['extens']['feedback'];
		//if(in_array($accountId,$SYSTEM_ACCOUNTS['ebay平台']) || in_array($accountId,$SYSTEM_ACCOUNTS['海外销售平台'])){
			if($feedback != ''){
				//echo "有留言\t";
				return $orderType = C('STATEPENDING_MSG');
			}
		//}
		return false;
	}
	
	/**
	 * 超重订单拦截
	 * @eturn array 返回ordertype
	 * @author lzx 
	 */
	public function interceptOverWeightOrder(){
		$orderType = $this->orderData['order']['orderType'];
		$calcWeight = $this->orderData['order']["calcWeight"];
		if($calcWeight > 2){
			//echo "\t 超重订单";
			return $orderType = C('STATEPENDING_OW');
		}
		return false;
	}
	
	/**
	 * 快递订单拦截
	 * @eturn array 返回ordertype
	 * @author lzx 
	 */
	public function interceptExpressOrder(){
		$orderType = $this->orderData['order']['orderType'];
		$accountId = $this->orderData['order']['accountId'];
		$transportId = $this->orderData['order']['transportId'];
		if (!in_array($transportId, M("InterfaceTran")->getCarrierNameList(0, true)) && !empty($transportId)){
			if(in_array($accountId,$SYSTEM_ACCOUNTS['ebay平台']) || in_array($accountId,$SYSTEM_ACCOUNTS['海外销售平台'])){
				$orderType = C('STATEOVERSIZEDORDERS_TA');//ebay和海外都跳转到 待打印线下和异常订单
			}else{
				$orderType = C('STATEPENDING_OFFLINE');
			}
			return $orderType;
		}
		return false;
	}
	
	/**
	 * 刷单订单拦截
	 * @eturn array 返回ordertype
	 * @author lzx 
	 */
	public function interceptBrushTradeOrder(){
		//预留接口
	}
	
	/**
	 * 无需发货订单拦截
	 * @eturn array 返回ordertype
	 * @author lzx 
	 */
	public function interceptUnshippingOrder(){
		//预留接口
	}
	###可扩展专线测试拦截等
}
?>