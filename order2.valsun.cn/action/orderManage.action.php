<?php
/*
 * 订单通用操作类控制器，业务逻辑在这里实现
 * @author zyp
 * @modify by lzx ,date 20140603
 */
class OrderManageAct extends CheckAct{
	
	public function __construct(){
		parent::__construct();
	}

	/**
	 * 合并订单，根据POST过来的订单编号进行合并
	 * @return boolean
	 *@author czq
	 */
    public function act_combineOrder(){
    	$orderIdArr	= array();
    	$postData   = trim($_POST['omData']);//订单编号队列
    	$orderIdArr = explode(',',$postData);
    	$orderLists = M('Order')->getFullUnshippedOrderById($orderIdArr);
    	//需要检查是否有权限合并对应订单操作权限
    			/**待开发**/
    	
    	//检测是否推送给仓库，如果推送了不能合并
    			/**待开发（怎么检测??）**/
  
    	
    	//订单是否可以合并判断
    	$ifCombine = $this->act_judgeCombine($orderLists);
    	if(!$ifCombine){
    		return false;
    	}
    	//合并订单信息整理
    	$mainoid = array_shift($orderIdArr);
    	$mainorder = array_shift($orderLists);  		//获取主单
 
    	$onlineTotal	= 0;//线上总价
    	$actualTotal	= 0;//实际收款总价
    	$calcWeight     = 0;//估算重量，单位是kg
    	$calcShipping   = 0;//估算运费
    	$insertOrder = array();
    	foreach($orderLists as $order_id=>$orders){
    		$selectArr 		= $orders['order'];
    		$onlineTotal 	+= 	$selectArr['onlineTotal'];
    		$actualTotal	+= 	$selectArr['actualTotal'];
    		$calcWeight		+=  $selectArr['calcWeight'];
    		$calcShipping   +=  $selectArr['calcShipping'];

    		foreach($orders['orderDetail'] as $detail){
	    		$obj_orderDetail = $detail['orderDetail'];
	    		unset($obj_orderDetail['id']);
	    		$obj_orderDetail['omOrderId'] = $mainoid;
	    		$orderDetailData = $obj_orderDetail;
	    			
	    		$obj_orderDetailExten 	  = $detail['orderDetailExtension'];
	    		unset($obj_orderDetailExten['omOrderdetailId']);
	    		$orderDetailExtenData 	  = $obj_orderDetailExten;
	    			
	    		$insertOrder['orderdetail'][]= array('orderDetail' => $orderDetailData,'orderDetailExtension' => $orderDetailExtenData);
    		}
    	}
    	$insertOrder['order']['onlineTotal'] = $onlineTotal;
    	$insertOrder['order']['actualTotal'] = $actualTotal;
    	$insertOrder['order']['calcWeight'] = $calcWeight;
    	$insertOrder['order']['calcShipping'] = $calcShipping;
    	$insertOrder['order']['orderAddTime'] = time();
    	$insertOrder['order']['combineOrder'] = 2;
    	$insertOrder['order']['orderAttribute'] = 3;
    	
    	//订单明细
    	$orderManageObj = M('OrderManage');
    	$orderManageObj->begin(); 			//开始事物
    	//插入订单
    	if(!$orderManageObj->combineOrder($insertOrder,$mainoid,$orderIdArr)){
    		$orderManageObj->rollback();
    		return false;
    	}
    	self::$errMsg[200] = get_promptmsg(200,'订单合并');
    	$orderManageObj->commit();
    	return $mainoid;
    }
    
    /**
     * 复制订单操作
     * add by yxd
     */
    public function act_copyOrder(){
    	$orderid       = $_REQUEST['orderid'];//被复制订单编号
    	$tablekey	   = isset($_POST['tablekey']) ? $_POST['tablekey'] : '';//ship or unship
    	//获取订单信息
    	$orders           = A('Order')->act_getOrderById($tablekey,array($orderid));
    	if ($orders['isCopy'] == 2) {
    		self::$errMsg[10060] = get_promptmsg(10060);
    		return false;
    	}
    	$theOrdersData    = $orders[$orderid];//被复制订单数据
    	/* foreach($theOrdersData as $key=>$value){
    		echo $key."===";var_dump($value);
    	} */
    	$copyOrderData    = $theOrdersData;//被复制订单数据
    	unset($copyOrderData['orderNote']);//暂时不用
    	unset($copyOrderData['orderWarehouse']);//暂时不用
    	unset($copyOrderData['orderTracknumber']);//暂时不用
    	//unset($copyOrderData['orderUserInfo']);//不用重复插入
    	$copyid                = $copyOrderData['order']['id'];
    	unset($copyOrderData['order']['id']);//删除订单id
    	$copyOrderData['order']['isCopy']    = 2;//更新为复制订单状态
    	$recordNumber    = $copyOrderData['order']['recordNumber'];
    	/* $copyOrderData['order']['recordNumber']         = time(); */
    	unset($copyOrderData['orderExtension']['omOrderId']);//删除订单关联id
    	unset($copyOrderData['orderUserInfo']['omOrderId']);//删除订单关联id
    	foreach ($copyOrderData['orderDetail'] as $key=>$value){//删除订单关联id
    		unset($copyOrderData['orderDetail'][$key]['orderDetail']['id']);
    		unset($copyOrderData['orderDetail'][$key]['orderDetail']['omOrderId']);
    		unset($copyOrderData['orderDetail'][$key]['orderDetailExtension']['omOrderdetailId']);
    	}
    	
    	//var_dump($copyOrderData);exit;
    	foreach($copyOrderData as $key1=>$data1){
    		
    		foreach($copyOrderData[$key1] as $key2=>$data2){
    			if(empty($data2)){
    				unset($copyOrderData[$key1][$key2]);
    			}
    		}
    	}
    	
    	$orderAdd       = M('orderAdd');//需重新跑拦截逻辑
    	$orderManage    = M('orderManage');
    	
    	$GLOBALS['allow_override_order'] = true;
    	
    	M('Base')->begin();//开始事物
    	//更新被复制订单状态和插入新的复制订单。
    	if($orderAdd->insertOrderPerfect($copyOrderData)&&$orderManage->copyOrder($tablekey,$orderid)){
    		$newid    = $orderAdd->getInsertOrderId();
    		$GLOBALS['allow_override_order'] = false;
    		$copyrelationdata    = array("copy_order_id"=>$copyid,"new_order_id"=>$newid);
    		
    		if($orderAdd->insertCopyOrderRecord($copyrelationdata)){
	    		self::$errMsg[200] = get_promptmsg(200,"订单 $orderid 复制");
	    		M('orderLog')->orderOperatorLog('no sql', "该订单复制来源于{$copyid}", $newid);
	    		$orderManage->commit();
	    		return true;
    		}else{
    			M('Base')->rollback();
    			self::$errMsg[10088] = "复制订单插入失败--关系";
    			return false;
    		}
    	 }else{
    	 	
    	 	$GLOBALS['allow_override_order'] = false;
    	 	
    	 	M('Base')->rollback();
    	 	self::$errMsg[10088] = "复制订单插入失败--insert";
    	 	return false;
    	 }	
    }
    /**
     * 判断订单是否可以合并
     * @param unknown $orderLists
     * @return boolean
     *@author czq
     */
    private  function act_judgeCombine($orderLists){
    	//判断一：订单数量小于两个
    	if(count($orderLists) < 2){
    		self::$errMsg[10063] = get_promptmsg(10063);
    		return false;
    	}
    	foreach($orderLists as $order_id=>$orders){
    		$selectArr = $orders['order'];
    		//判断二：订单被其他人 <锁定> 订单判断
    		if($selectArr['isLock'] == 1){	
    			self :: $errMsg[10064]  = get_promptmsg(10064,$selectArr['id'],get_usernamebyid($selectArr['lockUser']));
    			return false;
    		}
    		//判断三：已合并订单，无法再次合并判断
    		if(in_array($selectArr['combineOrder'], array(1,2))){
    			self :: $errMsg[10065]  = get_promptmsg(10065,$selectArr['id']);
    			return false;
    		}	
    		//判断四：已合并包裹订单，无法合并判断
    		if(in_array($selectArr['combinePackage'], array(1,2))){
    			self :: $errMsg[10066]  = get_promptmsg(10066,$selectArr['id']);
    			return false;
    		}
    		//判断五：订单信息不相同判断
    		$userinfsql	  		  =  $orders['userinfo'];
    		$tempArr = array();
    		$tempArr['accountId'] = trim($selectArr['accountId']);
    		$tempArr['platformUsername'] = trim($userinfsql['platformUsername']);
    		$tempArr['username'] = trim($userinfsql['username']);
    		$tempArr['countryName'] = trim($userinfsql['countryName']);
    		$tempArr['state'] = trim($userinfsql['state']);
    		$tempArr['city'] = trim($userinfsql['city']);
    		$tempArr['street'] = trim($userinfsql['street']);
    		$tempArr['currency'] = trim($userinfsql['currency']);//币种判断
    			
    		if(!empty($userinfo) && $userinfo != $tempArr){
    			self :: $errMsg[10067]  = get_promptmsg(10067);
    			return false;
    		}
    		$userinfo = $tempArr;//订单信息相同，进入下次比较。
    		
    		//判断六：同状态判断
    		$orderStatus    = "";//订单状态一
    		$orderType      = "";//订单状态二
    		$orderStatus = $selectArr['orderStatus'];
    		$orderType = $selectArr['orderType'];
    		if(!empty($temporderStatus) && $temporderStatus != $orderStatus){
    			self :: $errMsg[10068]  = get_promptmsg(10068);
    			return false;
    		}
    		$temporderStatus = $orderStatus;
    			
    		if(!empty($temporderStatus2) && $temporderStatus2 != $orderType){
    			self :: $errMsg[10068]  = get_promptmsg(10068);
    			return false;
    		}    			
    		$temporderStatus2 = $orderType;
    	}
    	return true;
    }
	
   
    
    /**
     * 合并包裹，无输入参数，直接在对应的用户权限账号下面合并 
     *@author czq
     */
    public function act_combineOrderPackage(){
    	$str = isset($_POST['str'])?$_POST['str']:"";
    	$id_array = explode(",",$str);
    	$id_array = array_filter($id_array);
    	//获取非快递运输方式
    	$carrierInfo = M('InterfaceTran')->getCarrierList(0); 
    	$carrierIds = array();
    	foreach($carrierInfo as $carrier){
    		if(in_array($carrier['carrierNameCn'], array('中国邮政平邮','中国邮政挂号'))){
    			$carrierIds[] = $carrier['id'];
    		}
    	}
    	$plateform_arr = array(1,8,10,12,13);  //允许的平台ebay,淘宝，DL,CN
    	$list = M('Order')->getCombieList($plateform_arr,$carrierIds,1,$id_array);
    	if(!$list){
    		self::$errMsg[10071]  = get_promptmsg(10071);
    		return false;
    	}
    	
    	$orderManageObj	= M('OrderManage');
    	$orderManageObj->begin();   //开始事物
    	$combineNum = 0;
    	foreach($list as $key=>$value){
    			$userInfo = array(
    				'userName'=>$value['userName']
    			);
    		/*$userInfo = array(
    			'userName' 		=> $value['userName'],
    			'countryName'	=> $value['countryName'],
    			'accountId'		=> $value['accountId'],
				'transportId'	=> $$value['transportId'],
				'state'			=> $value['state'],
    			'city'			=> $value['city'],
    			'street'		=> $value['street'],
    			'orderType' 	=> $value['orderType'],
  				'orderStatus'	=> $value['orderStatus'],
    			'orderType'		=> $value['orderType']
    		);*/
    		$records = M('Order')->getCombineOrders($userInfo, 1,$id_array);
    		if(!$records){
    			continue;
    		}else{
    			$weightlists = array();
    			$orderinfo = array();
    			$countryName = $records[0]['countryName'];
    			$transportId = $records[0]['transportId'];
    			foreach($records as $record){
    				$omOrderId = $record['id'];
    				//重量集合
    				$weightlists[$omOrderId] = $record['calcWeight'];
    				$orderinfo[$omOrderId] = $record;
    			}
    			$combineOrders 	= array();
    			$keyarray  		= array();
    			$checkweight 	= 0;
    			 
    			foreach($weightlists as $order_id => $weight){
    				$checkweight += $weight;
    				if($checkweight>1.85){
    					$combineOrders[] 	= $keyarray;
    					$checkweight 		= $weight;
    					$keyarray 			= array();
    					$keyarray[] 		= $order_id;
    				}else{
    					$keyarray[] 		= $order_id;
    				}
    			}
    			if(!empty($keyarray)){
    				$combineOrders[]		 = $keyarray;
    			}
    			
    			foreach($combineOrders as $orderlist){
    				if(count($orderlist) < 2){
    					continue;
    				}
    				$ordervalueweight = array();
    				$ordervalueactualTotal = array();
    				foreach($orderlist as $orderid){
    					$ordervalueweight[$orderid] = $weightlists[$orderid];
    					$ordervalueactualTotal[$orderid] = $orderinfo[$orderid]['actualTotal'];
    				}
    				$firstorder = array_shift($orderlist);//第一个订单编号信息
    				/******邮费计算需要提供(待完成)*****/
    				/*$combineInfo     = CommonModel::calcshippingfee(array_sum($ordervalueweight), $countryName, array_sum($ordervalueactualTotal), $transportId);//邮寄方式计算
    				$weight2fee      = calceveryweight($ordervalueweight, $combineInfo['fee']['fee']);
    				$firstweightfee  = array_shift($weight2fee);//第一个订单重量运费信息*/

    				$data = array();
    				$data['combinePackage'] = 1;
    				$data['orderStatus'] 	= C('STATEPENDING');
    				$data['orderType'] 		= C('STATEPENDING_CONPACK');
    				if(!$orderManageObj->updateData($firstorder,$data)){
    					self::$errMsg[10069] = get_promptmsg(10069);
    					$orderManageObj->rollback();
    					return false;
    				}
    				foreach($orderlist as $sonorder){
    					$data['combinePackage'] = 2;
    					$data['orderStatus'] = C('STATEPENDING');
    					$data['orderType'] = C('STATEPENDING_CONPACK');
    					$where = ' WHERE id = '.$sonorder;
    					if(!$orderManageObj->updateData($sonorder,$data)){
    						self::$errMsg[10070]  = get_promptmsg(10070);
    						$orderManageObj->rollback();
    						return false;
    					}
    					//插入包裹合并记录
    					$CombinePackageRecord	= array(
    							'main_order_id'	=> $firstorder,
    							'split_order_id'=> $sonorder,
    							'createdTime'	=> time(),
    							'creator'		=> $_SESSION['sysUserId']
    					);
    					$tableName = C('DB_PREFIX').'records_combinePackage';
    					if(!$orderManageObj->insertData($CombinePackageRecord,$tableName)){
    						self::$errMsg[10090] = get_promptmsg(10090); //插入合并包裹记录失败
    						$orderManageObj->rollback();
    						return false;
    					}
    				}
    				$combineNum++;
    			}
    		}
    	}
    	self::$errMsg[200]  = get_promptmsg(200,'合并包裹');
    	$orderManageObj->commit();
    	return $combineNum;
    }
    
    /**
     * 取消合并包裹关系
     * @ return
     * @author czq
     */
    
    public function act_cancelOrderPackageRelation(){
    	$str 		= isset($_POST['str'])?$_POST['str']:"";
    	$orderids 	= explode(",",$str);
    	$userId 	= $_SESSION['sysUserId'];
    	
    	$orderManageObj = M('OrderManage');
    	$orderManageObj->begin();   //开始事物
    	$orders = M('Order')->getUnshippedOrderById($orderids);
    	$sonOrderIds = array();
    	foreach($orders as $order){
    		if(in_array($order['id'],$sonOrderIds)){
    			continue;
    		}
    		//通用数据
    		$data = array(
    				'orderStatus'=>C('STATEPENDING'),
    				'orderType'=>C('STATEPENDING_CONV'),
    				'combinePackage'=>0,
    		);
    		$recordData = array(
    				'is_enable' => 0,
    				'cancelTime'=> time(),
    				'cancelUser'=> $userId,
    		);
    		if($order['combinePackage']==1){
    			if(!$orderManageObj->updateData($order['id'],$data)){
    				self::$errMsg[10069]  = get_promptmsg(10069);
    				$orderManageObj->rollback();
    				return false;
    			}
    			$sonOrders = M('Order')->getSonOrder($order['id']);
    			foreach($sonOrders as $sonorder){
    				if(!$orderManageObj->updateData($sonorder['split_order_id'],$data)){
    					self::$errMsg[10070] = get_promptmsg(10070);
    					$orderManageObj->rollback();
    					return false;
    				}
    				$sonOrderIds[] = $sonorder['split_order_id'];
    				/*if(in_array($sonorder['split_order_id'],$orderids)){
    				 unset($orderids[$sonorder['split_order_id']]);//$orderids key为数字键值，实际未有效果
    				}*/
    			}
    			if(!$orderManageObj->updateCombinePackageRecord($order['id'], $recordData)){
    				self::$errMsg[10074]  = get_promptmsg(10074);
    				$orderManageObj->rollback();
    				return false;
    			}
    		}
    		if($order['combinePackage']==2){
    			$mainOrder = M('Order')->getMainOrder($order['id']);
    			$sonOrders = M('Order')->getSonOrder($mainOrder);
    			if(count($sonOrders)==1){
    				if(!$orderManageObj->updateData($mainOrder, $data)){
	    				self::$errMsg[10069] = get_promptmsg(10069);
	    				$orderManageObj->rollback();
	    				return false;
    				}
    				$sonOrderIds[] = $mainOrder;
    				/*if(in_array($mainOrder,$orderids)){
    				unset($orderids[$mainOrder]);
    				}*/
    				if(!$orderManageObj->updateData($sonOrders[0]['split_order_id'], $data)){
    					self::$errMsg[10070]  = get_promptmsg(10070);
    					$orderManageObj->rollback();
    					return false;
    				}
    			}else{
    				if(!$orderManageObj->updateData($order['id'], $data)){
    					self::$errMsg[10069]  = get_promptmsg(10069);
    					$orderManageObj->rollback();
    					return false;
    				}
    			}
    			if(!$orderManageObj->updateCombinePackageRecord($order['id'],$recordData,true)){
    				self::$errMsg[10074]  = get_promptmsg(10074);
    				$orderManageObj->rollback();
    				return false;
    			}
    		}
    	}
    	$orderManageObj->commit();
    	self::$errMsg[200] = get_promptmsg(10091);
    	return true;
    }
    
    /**
     * 手动拆分订单
     * @return boolean
     *@author czq
     */
    public function act_handSplitOrder(){
    	$skus = isset($_POST['skus'])?$_POST['skus']:"";
    	$orderid = isset($_POST['orderid'])?$_POST['orderid']:"";
    	$type = isset($_POST['type'])?$_POST['type']:"";
    	$sku_arr = explode(",",$skus);
    	
    	if (empty($orderid)) {
    		self :: $errMsg[10079] = get_promptmsg(10079);
    		return false;
    	}
    	$mctime = time();
    	$orderData = M('Order')->getFullUnshippedOrderById(array($orderid));
    	$orderDetail = $orderData[$orderid]['orderDetail'];
    	$order = $orderData[$orderid]['order'];
    	$orderExtenData = $orderData[$orderid]['orderExtension'];
    	$orderUserInfoData = $orderData[$orderid]['orderUserInfo'];
    	$_actualTotal = $order['actualTotal'];
    	$_actualShipping = $order['actualShipping'];
    	$_platformId = $order['platformId'];
    	$_mainId = $order['id'];
    	$_transportId = $order['transportId'];
    	
    	$isExpressDelivery = $order['isExpressDelivery'];
        
        if($isExpressDelivery != 1){
        	
        	self :: $errMsg[1000] = '非快递订单，禁止手动拆分';
        	return false;
        	
        }
        
    	/*if(!in_array($_platformId, array(1,5,8,10,16))){//预留，和独立出来
    		self :: $errMsg[10077] = get_promptmsg(10077);
    		return false;
    	}*/
    	if($_transportId==6){
    		self :: $errMsg[10078] = get_promptmsg(10078);
    		return false;
    	}
    	if(!$order){
    		self::$errMsg[10080]  = get_promptmsg(10080);
    		return false;
    	}
    	if($order['isSplit']==2){
    		self::$errMsg[10081]  = get_promptmsg(10081);
    		return false;
    	}
    	if(count($orderDetail) <= 1){
    		self :: $errMsg[10082] = get_promptmsg(10082);
    		return false;
    	}
    	
    	//组装数据插入
    	$insertOrder = array();
    	unset($order['id']);
    	$order['isSplit'] = 2; //标记为复制定单
    	$insertOrder['order'] = $order;
    	unset($orderExtenData['omOrderId']);
    	$insertOrder['orderExtension'] = $orderExtenData;
    	unset($orderUserInfoData['omOrderId']);
    	$insertOrder['orderUserInfo'] = $orderUserInfoData;
    	
    	//组装订单明细信息
    	$deleteDetail = array();			//存放逻辑删除的明细
    	foreach($orderDetail as $detail){
    		$orderDetail = $detail['orderDetail'];
    		$orderDetailExtension = $detail['orderDetailExtension'];
    		$sku = $orderDetail['sku'];
    		if(!in_array($sku,$sku_arr)){
    			continue;
    		}
    		$deleteDetail[] = $orderDetail['id'];
    		unset($orderDetail['id']);
    		unset($orderDetail['omOrderId']);
    		unset($orderDetailExtension['omOrderdetailId']);
    		$insertOrder['orderDetail'][] = array('orderDetail'=>$orderDetail,'orderDetailExtension'=>$orderDetailExtension);
    	}
    	$orderManageObj = M('OrderManage');
    	$orderManageObj->begin();
    	//重新计算重量和运费
    	/************待实现*************/
    	
    	$insert_id = M('OrderManage')->handSplitOrder($insertOrder,$orderid);
 		if(!$insert_id){
 			$arr = M('OrderManage')->getErrorMsg();echo '<pre>';print_r($arr);exit;
 			
 			$orderManageObj->rollback();
 			self::$errMsg[10085] = get_promptmsg(10085);
 			return false;
 		}
 		//逻辑删除订单明细
 		$tableName = C('DB_PREFIX').'unshipped_order_detail';
 		foreach($deleteDetail as $id){
 			$data = array(
 				'is_delete'=> 1
 			);
 			if(!$orderManageObj->updateData($id,$data,$tableName)){
 				$orderManageObj->rollback();
 				self::$errMsg[10086] = get_promptmsg(10086);
 				return false;
 			}
 		}
 		$orderManageObj->commit();
 		self::$errMsg[200] = get_promptmsg(10087,$orderid,$insert_id);
 		return true;
    }
    
    /**
     *自动拆分订单 
     */
    public function act_autoSplitOrder(){
    	
    }
    
    /**
     * 超重拆分,只能是针对一个订单拆分
     * @return
     * @author czq
     */
    public function act_overWeightSplit(){
    	$omOrderId = isset($_POST['omOrderId']) ? intval($_POST['omOrderId']) : 0; //选中要拆分的订单
    	if (empty($omOrderId)) {
    		self::$errMsg[10079] = get_promptmsg(10079);
    		return false;
    	}
    	
    	//根据id获取订单信息
    	$orderLists = M('Order')->getFullUnshippedOrderById(array($omOrderId));
    	//为了方便，对一维数组进行循环
    	foreach($orderLists as $order_id=>$orders){ 
    		$order 				= $orders['order'];
    		$orderExtenData 	= $orders['orderExtension'];
    		$orderUserInfoData 	= $orders['orderUserInfo'];
    		$_actualTotal 		= $order['actualTotal'];
    		$_actualShipping 	= $order['actualShipping'];
    		$_platformId 		= $order['platformId'];
    		$_mainId 			= $order['id'];
    		$_transportId 		= $order['transportId'];
    		
    		if($_transportId==C('TRANSPORT_EUB')){
    			//EUB运势方式不可以拆分
    			self::$errMsg[10078] = get_promptmsg(10078);
    			return false;
    		}
    		/*if(!in_array($_platformId, array(C('RESERVED_PLATFORM')))){//预留平台，先独立出来
    			self :: $errMsg[10077] = get_promptmsg(10077);
    			return false;
    		}*/
    		
    		$weightlists = array();
    		$skuinfo = array();
    		$shippfee_arr = array();
    		foreach($orders['orderDetail'] as $detailinfo){
    			if (count($orders['orderDetail']==1)&&$detailinfo['orderDetail']['amount']<=1) {
    				self :: $errMsg[10082] = get_promptmsg(10082);
    				return false;
    			}
    			$skuweight = M('InterfacePc')->getSkuWeight($detailinfo['orderDetail']['sku']);
    			$shippfee_arr[$detailinfo['orderDetail']['sku']] = round($detailinfo['orderDetail']['shippingFee']/$detailinfo['orderDetail']['amount'],3);//单个料号的运费
    			$skuinfo[$detailinfo['orderDetail']['sku']] = $detailinfo;
    			for($i=1; $i<=$detailinfo['orderDetail']['amount']; $i++){
    				$weightlists[$detailinfo['orderDetail']['sku']][] = $skuweight;
    			}
    		}
    		$splitWeigths = array();
    		foreach($weightlists as $sku =>$weights){
    			foreach($weights as $weight){
    				$checkweight += $weight;
    				if($checkweight>1.8){
    					$splitWeigths[] = $keyarray;
    					$keyarray = array();
    					$checkweight = $weight;
    					$keyarray[$sku] = 1;
    				}else{
    					$keyarray[$sku] += 1;
    				}
    			}
    		}
    		//处理最后一个
    		if(!empty($keyarray)){
    			$splitWeigths[] = $keyarray;
    		}
    		
    		//组装通用插入订单数据结构
    		$insert_orderData = array();
    		foreach($splitWeigths as $splitWeigth){
    			$ebay_total = 0;
    			$totalweight = 0;
    			$obj_order_detail_data = array();
    			foreach($splitWeigth as $sku =>$amount){
    				$ebay_total += ($skuinfo[$sku]['orderDetail']['itemPrice'] + $shippfee_arr[$sku]) * $amount;
    				$insert_orderDetailData = $skuinfo[$sku]['orderDetail'];
    				unset($insert_orderDetailData['id']);
    				unset($insert_orderDetailData['omOrderId']);
    				$insert_orderDetailData['sku'] = strtoupper($sku);
    				$insert_orderDetailData['amount'] = $amount;
    				$insert_orderDetailData['createdTime'] = time();
    				if(isset($shippfee_arr[$sku])){
    					$insert_orderDetailData['shippingFee'] = $shippfee_arr[$sku]*$amount;//相同料号运费拆分
    				}
    				$insert_orderDetailExtenData = $skuinfo[$sku]['orderDetailExtension'];
    				unset($insert_orderDetailExtenData['omOrderdetailId']);
    				
    				$obj_order_detail_data[] = array('orderDetail' => $insert_orderDetailData,
    						'orderDetailExtension' => $insert_orderDetailExtenData,
    				);
    			}
    			//订单
    			$insert_obj_order_data = $order;
    			unset($insert_obj_order_data['id']);
    			$insert_obj_order_data['actualTotal'] = $ebay_total;
    			$insert_obj_order_data['orderType'] = C('STATEPENDING_OWDONE');
    			$insert_obj_order_data['orderAddTime'] = time();
    			$insert_obj_order_data['isSplit'] = 2;
    				
    			$insert_orderExtenData = $orderExtenData;
    			unset($insert_orderExtenData['omOrderId']);
    			$insert_orderUserInfoData = $orderUserInfoData;
    			unset($insert_orderUserInfoData['omOrderId']);
    			$insert_orderData = array(
    					'order' 			=> $insert_obj_order_data,
    					'orderExtension' 	=> $insert_orderExtenData,
    					'orderDetail'		=> $obj_order_detail_data,
    					'orderUserInfo' 	=> $insert_orderUserInfoData,
    			);
    			/***重量和运费计算**/
    			/***待实现**/
    			$orderManageObj = M('OrderManage');
    			$orderManageObj->begin();			//开始事物
    			//把主订单逻辑删除
    			$data = array(
    				'is_delete'=>1
    			);
    			if(!$orderManageObj->updateData($order_id,$data)){
    				$orderManageObj->rollback();
    				self::$errMsg[10083] = get_promptmsg(10083);
    				return false;
    			}			
 				//return $insert_orderData;
    			if(!$orderManageObj->overWeightSplit($insert_orderData,$order_id)){
    				$orderManageObj->rollback();
    				self::$errMsg[10084] = get_promptmsg(10084);
    				return false;
    			}
    		}
    		$orderManageObj->commit();
    	}
    	self::$errMsg[200] = get_promptmsg(200,'超重拆分');
    	return true;
    }
    
    /**
     * 获取复制订单补寄信息
     */
    public function act_getSendReplacement(){
    	$return = array();
    	$SendReplacementType = M('Order')->getSendReplacement();
    	$SendReplacementReason = M('Order')->getSendReplacementReason();
    	if ($SendReplacementType) {
    		$return['SendReplacementType'] = $SendReplacementType;
    	}
    	if ($SendReplacementType) {
    		$return['SendReplacementReason'] = $SendReplacementReason;
    	}
    	return $return;
    }
    
    /**
     * 获取状态信息
     */
    public function act_getStatusMenu(){
    	$ostatus = $_POST['ostatus'];
    	$statusmenu = A('StatusMenu')->act_getTypeMenuByUserId(get_userid(), $ostatus);
    	$list = array();	 
    	foreach ($statusmenu AS $id){
    		$_list[$id] = get_statusmenunamebyid($id);
    	}
    	return isset($_list)&&is_array($_list) ? $_list : false;
    }
    
	/**
     * 复制订单补寄，只针对已完结订单!!!
     * modify yxd
     */
    public function act_copyOrderForResend(){
    	$orderid 		= isset($_POST['orderid']) ? intval($_POST['orderid']) : 0;
    	$type 			= isset($_POST['type']) ? intval ($_POST['type']) : 1;
    	$resendArr 		= isset($_POST['resendArr']) ? $_POST['type'] : '';
    	$reason_noteb 	= isset($_POST['reason_noteb']) ? $_POST['reason_noteb'] : '';
    	$extral_noteb 	= isset($_POST['extral_noteb']) ? $_POST['extral_noteb'] : '';
    	$tablekey		= isset($_POST['tablekey']) ? $_POST['tablekey'] : '';
    	
    	$SendReplacementType = M('Order')->getSendReplacementTypeById($resendArr);
    	$SendReplacementReason = M('Order')->getSendReplacementReasonById($reason_noteb);
    	$note = " 补寄 订单(".$SendReplacementType.")--{$extral_noteb},".$SendReplacementReason;

    	//获取订单信息
    	$orders = A('Order')->act_getOrderById($tablekey,array($orderid));
    	var_dump($orders);exit;
    	$order = $orders[$orderid]['order'];
    	/* if ($order['isBuji'] == 2){
    		self::$errMsg[10059] = get_promptmsg(10059);
    		return false;
    	} */
    	if ($order['isCopy'] == 2) {
    		self::$errMsg[10060] = get_promptmsg(10060);
    		return false;
    	}
    	$orderData = array();
    	unset ($order['id']);
    	if ($type == 1) {
    		$order['isCopy'] = 2;
    		$order['actualTotal'] = 0.00;
    	} else {
    		$order['isCopy'] = 2;
    		$order['isBuji'] = 2;
    		$order['actualTotal'] = 0.00;
    		$order['orderStatus'] = C('STATEBUJI');
    		$order['orderType'] = C('STATEBUJI_DONE');
    	}
    	//需要封装成OrderAdd类的通用插入订单数据格式
    	//订单信息
    	$orderData['order'] = $order;
    	 
    	//扩展信息
    	$orderExtenData = $orders[$orderid]['orderExtension'];
    	unset ($orderExtenData['omOrderId']);
    	$orderData['orderExtension'] = $orderExtenData;
    	 
    	//用户信息
    	$orderUserInfoData = $orders[$orderid]['orderUserInfo'];
    	unset ($orderUserInfoData['omOrderId']);
    	$orderData['orderUserInfo'] = $orderUserInfoData;
    	 
    	//备注
    	$orderNote = array (
    			'content' => $note,
    			'userId' => $_SESSION['sysUserId'],
    			'createdTime' => time()
    	);
    	$orderData['orderNote'] = $orderNote;
    	 
    	//订单详细信息
    	$i = 0;
    	foreach ($orders[$orderid]['orderDetail'] as $detail) {
    		$insert_orderDetailData = $detail['orderDetail'];
    		unset ($insert_orderDetailData['id']);
    		unset ($insert_orderDetailData['omOrderId']);
    		$insert_orderDetailExtenData = $detail['orderDetailExtension'];
    		unset ($insert_orderDetailExtenData['omOrderdetailId']);
    		$orderData['orderDetail'][$i]['orderDetail']=$insert_orderDetailData;
    		$orderData['orderDetail'][$i]['orderDetailExtension'] = $insert_orderDetailExtenData;
    		$i++;
    	}
    	$orderManageObj = M('OrderManage');
    	$orderManageObj->begin();//开始事物
    	
    	//设置原来的订单为被复制订单(is_copy=1)，新的订单为复制订单(is_copy=2)
    	$inserOrders[] = array(
    			'order' 			=>	$order,
    			'orderExtension' 	=> 	$orderExtension,
    			'orderUserInfo' 	=>	$orderUserInfo,
    			'orderDetail'		=> 	$orderDetail
    	);
    	 if(!$orderManageObj->copyOrderForResend($orderData,$tablekey,$orderid)){
    	 	$orderManageObj->rollback();
    	 	self::$errMsg[10088] = get_promptmsg(10088);
    	 	return false;
    	 }
    	 self::$errMsg[200] = get_promptmsg(200,"订单 $orderid 复制");
    	 $orderManageObj->commit();
    	 return true;
    }
    
	/**
     * 申请打印订单
     * @author czq
     */
    public function act_applyAllPrintOrder(){
    	$orderid_arr  = $_POST['orderid_arr'];   //整个文件夹打印
    	$order_status = $_POST['ostatus'];
    	$order_type   = isset($_POST['otype']) ? $_POST['otype'] : '';
    	$flag		  = isset($_POST['flag']) ? $_POST['flag'] : '';
		
    	if(empty($order_status) || empty($order_type)){
    		self::$errMsg[10100] = get_promptmsg(10100);
    		return false;
    	}
    	
    	$idStr = "";
    	if(is_array($idArr)){
    		$idStr = " AND id in (".join(',',$idArr).") ";
    	}
    	$table = 'om_unshipped_order';
    	$fields = '*';
    	$storeId = 1;
    	$where = " WHERE is_delete = '0' {$idStr} AND orderStatus = {$ostatus} AND orderType = {$otype} ";
    	/**队列推送到仓库,预留**/
    	$rtn = OrderPushModel::listPushOneMessage($orderValue);
    	return $rtn;
    }
    
    /**
     * 申请部分打印
     * @author czq
     */
    public function act_applyPartPrintOrder(){
    	
    }

    /**
     * 获取需要取消合并的包裹
     * @return string $data
     *@author czq
     */
    public function act_findCombineOrder(){
    	$str = isset($_POST['str'])?$_POST['str']:"";
    	$id_arr = explode(",",$str);
    	$id_arr = array_filter($id_arr);
    	$orderObj = M('Order');
    	$orders = $orderObj->getCancelCombineOrder($str);
    	if(!$orders){
    		self::$errMsg[10073]  = get_promptmsg(10073);
    		return false;
    	}
    	$data = "";
    	foreach($orders as $key=>$value){
    		if($value['combinePackage']==1){
    			$data .= "#".$value['id']."*";
    			$sonOrders = $orderObj->getSonOrder($value['id']);
    			foreach($sonOrders as $sonorder){
    				if($key==0){
    					$data .= $sonorder['split_order_id'];
    				}else{
    					$data .= ",".$sonorder['split_order_id'];
    				}
    			}
    		}
    		if($value['combinePackage']==2){
    			$mainOrder = $orderObj->getMainOrder($value['id']);
    			$sonOrders = $orderObj->getSonOrder($mainOrder);
    			$data .= "#".$mainOrder."*";
    			foreach($sonOrders as $key=>$sonorder){
    				if($key==0){
    					$data .= $sonorder['split_order_id'];
    				}else{
    					$data .= ",".$sonorder['split_order_id'];
    				}
    			}
    		}
    	}
    	return $data;
    }
    
    /**
     * 批量修改订单状态和运输方式
     * @return boolean
     * @author czq
     */
    public function act_batchMove(){
    	$type		=	$_POST['type'];
    	$ostatus	=	$_POST['ostatus'];
    	$otype		=	$_POST['otype'];
    	$valuestr	=	$_POST['valuestr'];
    	$sysUserId  = 	$_POST['sysUserId'];
    	$accountacc = 	$_POST['accountacc'];
    	
    	if (empty($ostatus)||empty($otype)){
    		self::$errMsg[10094] = get_promptmsg(10094);
    		return false;
    	}
    	
    	$orderIds = explode(',', $valuestr);
    	
    	$where = ' 1';
    	if(!empty($valuestr)){
    		$where .= ' AND id in ('.join(',', $orderIds).') ';
    	}
    	if($ostatus){
    		$where .= ' AND orderStatus = '.$ostatus;
    	}
    	if($otype){
    		$where .= ' AND orderType = '.$otype;
    	}
    	if($accountacc){
    		$where .= ' AND ('.$accountacc.') ';
    	}
    	
    	$orderManageObj = M('OrderManage');
    	$orderManageObj->begin(); //开始事务
    	//移动文件夹权限设置(待开发)
    	if($type == 1){
    		/*$ProductStatus = new ProductStatus();
    		$UserCompetenceAct = new UserCompetenceAct();
    		$batch_otype_val = $_REQUEST['batch_otype_val'];
    		$batch_ostatus_val = $_REQUEST['batch_ostatus_val'];
    		$visible_movefolder = $UserCompetenceAct->act_getInStatusIds($otype, $sysUserId);
    		if($batch_ostatus_val == 900){
    			self :: $errCode = 400;
    			self :: $errMsg = "注意不能直接移动到 仓库发货 ，需要申请打印";
    			return false;
    		}
    		if($otype && !in_array($batch_otype_val,$visible_movefolder)){
    			self :: $errCode = 500;
    			self :: $errMsg = "无权限从 {$otype} 移动到 {$batch_otype_val}";
    			return false;
    		}*/
    		
    		$batch_ostatus_val 	= $_POST['batch_ostatus_val'];
    		$batch_otype_val	= $_POST['batch_otype_val'];
    		$statusData = array(
    			'orderStatus'=> $batch_ostatus_val,
    			'orderType'  => $batch_otype_val
    		);
    		/**此方法用处？**/
    		//if($ProductStatus->updateSkuStatusByOrderStatus($idArr, $batch_ostatus_val, $batch_otype_val));    
    		$ret = $orderManageObj->updateDataByCondition($statusData, $where);
    		if(!$ret){
    			$orderManageObj->rollback();
    			self::$errMsg[10097] = get_promptmsg(10097);
    			return false;
    		}else{
    			self::$errMsg[200] = get_promptmsg(200,'批量更新运订单状态');
    			$orderManageObj->commit();
    			return $ret;	 
    		}
    	}else if($type == 2){
    		$batch_transport_val = $_POST['batch_transport_val'];
    		$transportData = array(
    			'transportId' => $batch_transport_val
    		);
    		$ret = $orderManageObj->updateDataByCondition($transportData, $where);
    		if(!$ret){
    			$orderManageObj->rollback();
    			self::$errMsg[10098] = get_promptmsg(10098);
    			return false;
    		}else{
    			self::$errMsg[200] = get_promptmsg(200,'批量更新运输方式');
    			$orderManageObj->commit();
    			return $ret;
    		}
    	}
    	
    }
    
    /**
     * 提供给WH的拆分订单接口
     * @param array 批量的主订单号及其拆分的虚拟发货单号对应的详情信息,格式形如 
     * array(
     * '主订单号1'=>array(
     *                '虚拟发货单号1'=> array(
     *                                      'sku1'=>'amount1',
     *                                      'sku2'=>'amount2',
     *                                       ........
     *                                  )
     *                  .......
     *              )
     *  .......
     * )
     * @return array 对应主订单以及虚拟发货单对应的订单号
     * @author zqt
     */
    public function act_splitOrderWithOrderDetailBatch(){
        $returnArr = array();//要返回的数组
    	$splitData = json_decode($_REQUEST['splitData'], true);
        if(empty($splitData) || !is_array($splitData)){
            get_promptmsg(10125,'splitData');
            return false;
        }
        //print_r($splitData);exit;
        foreach($splitData as $mainOrderId=>$virtInfoArr){
            M('Base')->begin();//事务
            $splitOrderWithOrderDetailData = $this->act_splitOrderWithOrderDetail($mainOrderId,$virtInfoArr);
            if(count($splitOrderWithOrderDetailData) == count($virtInfoArr)){//简单的判断传过来的发货单数量是否和生成的子单数量一致，一致则提交，否则rollback
                M('Base')->commit();
                $returnArr[$mainOrderId] = $splitOrderWithOrderDetailData;
            }else{
                M('Base')->rollback();
                $returnArr[$mainOrderId] = false;//拆分失败的对应订单的值为false;
            }                        
        }
        //将原来的新的发货单号变为具体的订单大数组
        if(!empty($returnArr)){
            foreach($returnArr as $mainOrderId=>$newOrderVirtRelaArr){
                if(!empty($newOrderVirtRelaArr)){
                    foreach($newOrderVirtRelaArr as $virtId=>$newOrderId){
                        if(intval($newOrderId) > 0){
                            $tmpArr = array();
                            //$returnArr[$mainOrderId][$virtId] = M('Order')->getFullUnshippedOrderById(array($newOrderId));
                            $orderList = M('Order')->getUnshippedOrderById(array($newOrderId));
                            if(!empty($orderList[0])){
                                $tmpArr['omOrderId'] = $newOrderId;
                                $tmpArr['actualTotal'] = $orderList[0]['actualTotal'];//实际订单总价
                                $tmpArr['usefulChannelId'] = $orderList[0]['usefulChannelId'];//实际订单总价
                                $orderCalcList = M('Order')->getOrderCalcListById($newOrderId);
                                if(!empty($orderCalcList[0])){
                                    $tmpArr['calOrderWeight'] = $orderCalcList[0]['calOrderWeight'];
                                    $tmpArr['calOrderTransportId'] = $orderCalcList[0]['calOrderTransportId'];
                                    $tmpArr['calOrderChannelId'] = $orderCalcList[0]['calOrderChannelId'];
                                    $tmpArr['calcOrderShipping'] = $orderCalcList[0]['calcOrderShipping'];
                                }
                            }
                            $returnArr[$mainOrderId][$virtId] = $tmpArr;
                        }
                    }
                }
            }
        }
        return $returnArr;
    }
    
    /**
     * 取消交易
     * @param int omOrderid int type type=3为暂不寄 type=1取消交易type=2废弃订单type=4异常处理
     * @return  bool 
     * @author yxd 
     * 
     */
    public function act_cancelDeal(){
    	$omOrderid    = $_POST['orderid'] ? intval($_POST['orderid']) :0;
    	$type         = $_POST['type'] ? intval($_POST['type']) :0;
    	if(empty($omOrderid)){
    		self::$errMsg[101]    = "订单号获取失败";
    		return false;
    	}
    	if(empty($type)){
    		self::$errMsg[102]    = "类型获取失败";
    		return false;
    	}
    	$exsit    = M('order')->getUnshippedOrderById(array($omOrderid));
    	if(!is_array($exsit) && count($exsit)<1){//不存在该订单记录
    		self::$errMsg[401]    = "不存在改订单记录";
    		return false;
    	}
    	
    	if($exsit[0]['is_delete']==1){
    		self::$errMsg[402]    = "改订单已被删除，不能操作";
    		return false;
    	}
    	
    	if (M("statusMenu")->getOrderStatusByStatusCode("ORDERS_FINISHED","id") == $exsit[0]['orderStatus']) {//订单已发货不能取消交易
    		self::$errMsg[304] = '订单已完成不能取消交易!';
    		return false;
    	}
    	
    	
    	
    	if ($type == 1) { //取消交易
    		$orderStatus        = M("statusMenu")->getOrderStatusByStatusCode("ORDERS_RETURNED","id");
    		$orderType          = M("statusMenu")->getOrderStatusByStatusCode("CANCLE_ORDER","id");
    		$order_type_name    = M("statusMenu")->getOrderStatusByStatusCode("CANCLE_ORDER","statusName");
    		
    	}
    	elseif ($type == 2) { //废弃订单
    		
    	   $orderStatus        = M("statusMenu")->getOrderStatusByStatusCode("ORDERS_RETURNED","id");
    	   $orderType          = M("statusMenu")->getOrderStatusByStatusCode('ORDERS_DISCARD',"id");
    	   $order_type_name    = M("statusMenu")->getOrderStatusByStatusCode("ORDERS_DISCARD","statusName");
    	}
    	elseif ($type == 3) { //暂不寄
    	   $orderStatus        = M("statusMenu")->getOrderStatusByStatusCode("ORDERS_RETURNED","id");
    	   $orderType          = M("statusMenu")->getOrderStatusByStatusCode("UNSHIP_TEMP","id");
    	   $order_type_name    = M("statusMenu")->getOrderStatusByStatusCode("UNSHIP_TEMP","statusName");
    	}
    	elseif ($type == 4) { //异常处理
    	   $orderStatus    = C('STATEINTERCEPTSHIP');
    	   $orderType      = C('STATEINTERCEPTSHIP_PEND');
    	}
    	$condition    = array(
    			                "id"=>array('$e'=>$omOrderid),
    			                "orderType"=>array('$e'=>$orderType),
    			                "orderStatus"=>array('$e'=>$orderStatus),
    							"is_delete"=>array('$e'=>0)
    	                      );
    	if(M('order')->checkExistsByCondition($condition)){
    		self::$errMsg[201]    = "你已经操作过了不要重复操作";
    		return false;
    	}
    	
    	####404仓库系统对应的发货单已经发货！#####
    	####403仓库系统订单废弃失败！###########
    	####402找不到仓库系统对应的发货单！#########
    	#####101'参数有误######################
    	if(M("statusMenu")->getOrderStatusByStatusCode("ORDERS_SHIPPING","id") == $exsit[0]['orderStatus']){
	    	$sendresult = M("InterfaceWh")->ordersDiscard2Wh($omOrderid);//WarehouseAPIModel::discardShippingOrder($orderid);
	   			
	    	if (!$sendresult) {	//推送消息到仓库系统失败
	    		self::$errMsg[305] = "发货单仓库废弃失败".M('InterfaceWh')->getErrorMsg();
	    		return false;
	    	}
	    	
	    	if (M("orderModify")->updateOrderStatusById($omOrderid,$orderType,$orderStatus)) {
	    		M('orderLog')->orderOperatorLog('no sql', "该订单被废弃 状态由{$exsit[0]['orderType']}---{$order_type_name}", $omOrderid);
	    		self :: $errMsg[200] = '操作成功';
	    		
	    		return false;
	    	} else {
	    		self :: $errMsg[408] = '订单更新失败';
	    		return false;
	    	}
    	}else{
    		if (M("orderModify")->updateOrderStatusById($omOrderid,$orderType,$orderStatus)) {
    			M('orderLog')->orderOperatorLog('no sql', "该订单被废弃 状态由{$exsit[0]['orderType']}---{$order_type_name}", $omOrderid);
    			self :: $errMsg[200] = '操作成功';
    			 
    			return false;
    		} else {
    			self :: $errMsg[408] = '订单更新失败';
    			return false;
    		}
    	}
    }
    /**
     * 重新计算运费
     * @param id 
     * @author yxd
     */
    public function act_calshippingfee(){
    	$ids    = $_POST['id'];
    	$ids    = explode(",", $ids);
    	$ids    = array_map("intval", $ids);
    	$orderDatas           = M('order')->getFullUnshippedOrderById($ids);//获取完整订单信息
    	$updataDatas          = array();//要更新的数据
    	$calordersshipping    = F('CalcOrderShipping');
    	foreach($orderDatas as $key=>$orderData){//计算运费
    		$calordersshipping->setOrder($orderData);
    		$calData    = $calordersshipping->calcOrderCarrierAndShippingFee();
    		$updataDatas[$key]['id']              = $key;
    		$updataDatas[$key]['channelId']       = $calData['order']['channelId'];
    		$updataDatas[$key]['transportId']     = $calData['order']['transportId'];
    		$updataDatas[$key]['calcShipping']    = $calData['order']['calcShipping'];
    	}
    	
    	foreach($orderDatas as $key=>$orderData){//更新数据
    		//运输方式有变更就更新订单数据
    		if($orderData['order']['channelId']!==$updataDatas[$key]['channelId'] || $orderData['order']['transportId']!==$updataDatas[$key]['transportId'] || $orderData['order']['calcShipping']!==$updataDatas[$key]['calcShipping'] ){
    			
    		}
    	}
    }
    
    /**
     * 申请退款
     * @param 
     * @return array
     * @author yxd 2014/08/13
     */
    public function act_applyRefund(){
    	$id 		= isset($_POST['orderId']) ? intval($_POST['orderId']) : 0;
    	$ostatus 	= isset($_POST['orderStatus']) ? intval($_POST['orderStatus']) : 0;
    	$otype 		= isset($_POST['orderType']) ? intval($_POST['orderType']) : 0;
    	if(empty($id) || empty($ostatus) || empty($otype)) {
    		self::$errMsg[124]   = '参数非法！';
    		return false;
    	}
    	$refundedInfo    = M('OrderRefund')->getRefundSum($id);
    	$totalSum        = $refundedInfo['totalSum'];
    	$refundedSum     = $refundedInfo['refundSum'];
    	if(($refundedSum != 0) && ($refundedSum >= $totalSum)) {
    		self::$errMsg[102]   = '该订单累计申请退款金额已达订单金额，不可再申请！';
    		return false;
    	}
    	
    	#######################这里还需加判断，匹配已发货的情况##########################
    	$orderInfo           			   = M('order')->getFullUnshippedOrderById(array($id));//获取订单完整信息
    	$orderInfo          		       = $orderInfo[$id];
    	$returnData['id']    			   = $id;
		$returnData['recordNumber']        = $orderInfo['order']['recordNumber'];
		$returnData['accountId']           = $orderInfo['order']['accountId'];
		$returnData['platformId']          = $orderInfo['order']['platformId'];
		$returnData['ordersTime']          = $orderInfo['order']['ordersTime'];
		$returnData['paymentTime']         = $orderInfo['order']['paymentTime'];
		$returnData['actualTotal']         = $orderInfo['order']['actualTotal'];
		$returnData['calcShipping']        = $orderInfo['order']['calcShipping'];
		$returnData['countryName']         = $orderInfo['orderUserInfo']['countryName'];
		$returnData['PayPalPaymentId']     = $orderInfo['orderExtension']['payPalPaymentId'];
		$returnData['currency']            = $orderInfo['order']['currency'];
		$returnData['platformUsername']    = $orderInfo['orderUserInfo']['platformUsername'];
		$platform                          = A('Platform')->act_getPlatformById($orderInfo['order']['platformId']);
		$platform                          = $platform['platform'];
		$returnData['platform']            = $platform;
    	$returnData['detail']              = array();
			if($orderInfo['orderDetail']){
				foreach($orderInfo['orderDetail'] as $detailData){
					$detail['sku']            = $detailData['orderDetail']['sku'];
					$detail['amount']         = $detailData['orderDetail']['amount'];
					$detail['itemPrice']      = $detailData['orderDetail']['itemPrice'];
					$returnData['detail'][]   = $detail;
				}
			}
			
			if(!$returnData['accountId']){
				self::$errMsg[144]	= '对应账号ID为空！';
				return FALSE;
			}
			if($returnData['platformId'] == 1){//ebay平台
				$accountInfo = M('Paypal')->get_paypalByEbayAccount($returnData['accountId']);
				if(!$accountInfo) {
					self::$errCode  = 005;
					self::$errMsg   = '没有对应PayPal账号信息！';
					return FALSE;
				}
			
				$returnData['paypalAccount1']    = $accountInfo[0]['account1'];
				$returnData['pass1']             = $accountInfo[0]['pass1'];
				$returnData['signature1']        = $accountInfo[0]['signature1'];
				$returnData['paypalAccount2']    = $accountInfo[0]['account2'];
				$returnData['pass2']             = $accountInfo[0]['pass2'];
				$returnData['signature2']        = $accountInfo[0]['signature2'];
			}
			
	      $returnData['refundedSum']    = $refundedSum;
	      self::$errMsg[200]            = "获取数据成功 ";
	      return $returnData;
    }
    
    /**
     * 退款信息 写入退款单
     * 
     * @author yxd 2014/08/13
     */
    public function act_addRefundInfo(){
    	$refundInfo    = isset($_POST['orderobj']) ? $_POST['orderobj'] : 0;
    	#####退款与纠纷单据类型：1，默认值paypel退款；2,手动退款；3、纠纷单据#####
    	$orderType     = isset($refundInfo['orderType']) ? $refundInfo['orderType'] : 1;
    	if($refundInfo == '') {
    		self::$errMsg[123]   = '参数非法！';
    		return false;
    	}
    	$id         = isset($refundInfo['id']) ? $refundInfo['id'] : '';
    	if($id == '') {
    		self::$errMsg[124]   = '参数Id为空！';
    		return false;
    	}
    	if($orderType == 1){//paypel退款
    		F('ebay.package.PaypalRefund');
    		//$httpParsedResponseAr = curlRefund($refundInfo);
    		$refundResult    = "SUCCESS";//strtoupper($httpParsedResponseAr["ACK"]);//退款api返回结过
    		if("SUCCESS" === $refundResult || "SUCCESSWITHWARNING" === $refundResult) {
    			$dataArr     = $refundInfo;
    			$operator    = get_userid();//操作人
    			$time = time();
    			$refundLog    = array("order_id"         => "{$dataArr['id']}",
    			                      "trade_id"         => "{$dataArr['PayPalPaymentId']}",
    								  "ebay_account"     => "{$dataArr['accountId']}",
    			                      "buyer_id"         => "{$dataArr['platformUsername']}",
    			                      "refund_reson"     => "{$dataArr['reason']}",
    			                      "refund_type"      => "{$dataArr['refundType']}",
    					              "operator"         => "{$operator}",
    			                      "refund_time"      => "{$time}",
    			                      "paypal_account"   => "{$dataArr['paypalAccount']}",
    			                      "money"            => "{$dataArr['refundSum']}",
    			                      "currency"         => "{$dataArr['currency']}",
    			                      "country"          => "{$dataArr['countryName']}");
    			
    			M('EbayRefundLog')->insertData($refundLog);//记录操作信息
    			$EbayRefundLogId     =  M('EbayRefundLog')->getLastInsertId();
    			$refundDetailLog     = array();
    			$skuArr = $dataArr['skuArr'];
    			foreach($skuArr as $key=>$sku){
    				$refundDetailLog['order_id']    = $EbayRefundLogId;//$dataArr['id'];
    				$refundDetailLog['sku']         = $sku['sku'];
    				$refundDetailLog['amount']      = $sku['amount'];
    			    M('EbayRefundLogDetail')->insertData($refundDetailLog);//记录退款单个sku 信息
    			}
    			$status    = M("statusMenu")->getOrderStatusByStatusCode("ORDERS_FINISHED","id");
    			$type      = M("statusMenu")->getOrderStatusByStatusCode("SHIPPED_PAID","id");
    			M('OrderModify')->updateOrderStatusById(array($dataArr['id']),$status,$type);
    			############更新shippendorder方法#######################
    			//M('OrderModify')->updateOrderStatusById(array($dataArr['id']),660,710);
    			M('orderLog')->orderOperatorLog('no sql', "paypal退款成功", $dataArr['id']);
    	 }//end of 退款成功
    	 else{
    	 	$rtnMsg               = "订单编号: ".$dataArr['id']."退款失败";// urldecode($httpParsedResponseAr['L_LONGMESSAGE0']).'错误代码：'.$httpParsedResponseAr['L_ERRORCODE0'];
    	 	self::$errMsg[1004]   = $rtnMsg;
    	 	return false;
    	 }
     }//end of paypal退款
      M('orderRefund')->begin();
		$sysUserId                   = get_userid();
        $time                        = time();
        $data                        = array();
        $data['omOrderId']           = $refundInfo['id'];
        $data['recordNumber']        = $refundInfo['recordNumber'];
        $data['sellerAccountId']     = $refundInfo['accountId'];        
        $data['totalSum']            = $refundInfo['totalSum'];
        $data['refundSum']           = $refundInfo['refundSum'];
        $data['refundType']          = ($refundInfo['refundType'] == 'Full') ? 1 : 0;
        $data['platformUsername']    = $refundInfo['platformUsername'];
        $data['platformId']          = $refundInfo['platformId'];
        $data['platform']            = $refundInfo['platform'];
        $data['transId']             = $refundInfo['PayPalPaymentId'];
        $data['paypalAccount']       = $refundInfo['paypalAccount'];
        $data['pass']                = $refundInfo['pass'];
        $data['signature']           = $refundInfo['signature'];
        $data['reason']              = $refundInfo['reason'];  
        $data['note']                = $refundInfo['note'];  
        $data['currency']            = $refundInfo['currency'];
        $data['country']             = $refundInfo['countryName'];
		$data['orderType']           = $orderType;
        $data['addTime']             = $time;
		$data['creatorId']           = $sysUserId;
		M('Base')->commit();
        M('orderRefund')->insertData($data);  
  		$insertId                    = M('orderRefund')->getLastInsertId();
		if($insertId) {
            foreach($refundInfo['skuArr'] as $key => $orderInfo){
                $refundDetail = array();
                $refundDetail['orderRefundId'] = $insertId;
                $refundDetail['sku']           = $orderInfo['sku'];
                $refundDetail['amount']        = $orderInfo['amount'];
                $refundDetail['actualPrice']   = $orderInfo['actualPrice'];
                $refundDetail['addTime']       = $time;   
              	$table   = C('DB_PREFIX')."order_refund_detail";
               	$ret2   = M('OrderRefundDetail')->insertRefundDetail($refundDetail);
               	if($ret2 !== FALSE) {      	    
                    //
                } else {                  
                    self :: $errMsg[1005]  = "更新退款详情失败"; 
                    M('Base')->rollback();           
                    return false;
                }                          
            }
 		} else { 		 
            self :: $errMsg[1006]  = "更新退款信息失败";
            M('Base')->rollback();            
            return false;
		}
        M('orderRefund')->commit();
        M('orderLog')->orderOperatorLog('no sql', "生成退款单据{$insertId}", $refundInfo['id']);
        self::$errMsg[200]   = "生成退款单据成功！";
        return true;
   }
   
   /**
    * 启动发货
    */
   public function act_doshipping(){
   		$orderids    = $_POST['orderids'];
   		$orderArr    = explode(",", $orderids);
   		$success     = null;
   		$error       = null;
   		foreach($orderArr as $orderid){
   			$upres     = M('orderModify')->updateOrderStatusById(array($orderid),0,0);
   			//执行完毕开始走流程
   			M('orderLog')->orderOperatorLog('noSQL','启动发货,订单变为初始状态,开始走拦截逻辑',$orderid);
   			$order     = M('order')->getFullUnshippedOrderById(array($orderid));
   			$order     = $order[$orderid];
   			//unset($order['orderNote']);//去掉备注
   			//unset($order['orderTracknumber']);//去掉跟踪号
   			unset($order['orderWarehouse']);//去掉仓库相关
   			$fmtRes    = F('FormatOrder')->interceptOrder($order);
   			if($fmtRes){
   				$success     .= $orderid."启动成功\n";
   			}else{
   				$error       .= $orderid."启动失败\n";
   			}
   		}
   		if(strlen($error)>1){
   		    self::$errMsg[125]    = $error;
   		    return false;
   		}else{
   			self::$errMsg[200]    = $success;
   			return true;
   		} 
   }
   
/**
	 * 按照订单详情（sku/数量），拆分订单（这里没有验证传递过来的虚拟发货单对应的SKU或数量的正确性）,注意，必须在逻辑层调用事务处理
	 * @param int $omOrderId
	 * @param array $detailData 格式为array('虚拟发货单Id1'=>array('sku1'=>count1,'sku2'=>count2,...),'虚拟发货单id2'=>array(...),...));
	 * @return array array('虚拟发货单Id1'=>'omOrderId1','虚拟发货单id2'=>'omOrderId2'),//如果虚拟发货单Id1对应的单拆分失败，则其value值为false
	 * @author zqt
	 */
	public function act_splitOrderWithOrderDetail($omOrderId,$splitDetailData){
		$newOrderVirtRelaArr = array();//记录新生成的子单和虚拟发货单的关系数组,即返回的数组
        $omOrderId = intval($omOrderId);
		if ($omOrderId <=0){
			return get_promptmsg(10080);//订单号不存在
		}
        if (empty($splitDetailData) || !is_array($splitDetailData)){
            return get_promptmsg(10123);//detailData为空或者不是数组
        }
        
        $error_msg = array();
        
        //取出对应omOrderId的订单信息
        $origin_orderData = M('Order')->getFullUnshippedOrderById(array($omOrderId));
        
        if(empty($origin_orderData)){
            return get_promptmsg(10080);//订单号不存在
        }      
        
        $origin_orderData = $origin_orderData[$omOrderId];
        
        //如果已经是相关拆分或复制订单，则停止操作
        if($origin_orderData['order']['isSplit'] != 0 || $origin_orderData['order']['combinePackage'] != 0 || $origin_orderData['order']['isCopy'] != 0){
        	return get_promptmsg(10130);//该订单已经是拆分订单或复制订单，禁止再次操作！
        }   
           
        //将原订单改为被拆分订单isSplit=1并且逻辑删除订单相关
            //主单处理
            unset($origin_orderData['order']['id']);//去掉Id
            unset($origin_orderData['orderNote']);//去掉备注
            unset($origin_orderData['orderTracknumber']);//去掉跟踪号
            unset($origin_orderData['orderWarehouse']);//去掉仓库相关
            $origin_orderData['order']['isSplit'] = 2;//默认为拆分出的订单，为2
            $orderDetailData = $origin_orderData['orderDetail'];//订单详情
            
            $orderType = $origin_orderData['order']['orderType'];
            
            M('Base')->begin();
    
            //判断当前待拆分订单的状态，拆分后改订单作废。如果是待发货或者缺货状态，需要减少待发货数量
            if(in_array($orderType,getOrderStatusWaitingSend())){
            	M('orderLog')->orderOperatorLog('no sql', '该订单sku数量从待发货数量中扣除 ', $omOrderId);
            	//更新该订单中sku的待发货数量
				F('SkuDailyInfo')->updateDailyAverageInfoByOrder($omOrderId, 2);
            }
            
            $sku2OrderDetailAndExtensionData = array();//将$orderDetailData中的第一维度的key换成对应sku的值，方便后面根据SKU取得对应的其他值
            
            $splitDetailData_tmp = array();
            
            foreach($orderDetailData as $tmp){
                $sku2OrderDetailAndExtensionData[$tmp['orderDetail']['sku']] = $tmp;
                
            }
			M('orderLog')->orderOperatorLog('no sql', '总拆分规则 '.' '.json_encode($splitDetailData), $omOrderId);
            //echo '<pre>';print_r($splitDetailData);exit;
            //按照传递过来的$detailData拆分子单           
            foreach($splitDetailData as $virtId=>$splitInfo){//$virtId为虚拟的发货单号，$detailInfo为该发货单里有的sku及数量关系
            	
            	M('orderLog')->orderOperatorLog('no sql', '开始拆分规则 '.' '.json_encode($splitInfo), $omOrderId);
            	
                $newOrderData = $origin_orderData;//新的子单大数组,默认将主单复制过来，子单的内容除了订单详情，其他应该和主单一致
                
                $newOrderData['order']['isSplit'] = 2;//默认为拆分出的订单，为2
                
                //$actualToal =  $newOrderData['order']['actualTotal'];
                
                $tmp_sub_total = 0;
                $newOrderDetailData = array();//新子单的orderDetail数组
                foreach($splitInfo as $sku=>$amount){
                	
                    $tmp = $sku2OrderDetailAndExtensionData[$sku];
                    
                    if(empty($tmp)){
                    	$error_msg[] = '指定的sku:'.$sku.'在订单中不存在';
                    	M('orderLog')->orderOperatorLog('no sql', '指定的sku:'.$sku.'在订单中不存在', $omOrderId);
                    	continue;
                    }
                    
                    $tmp['orderDetail']['amount'] = $amount;
                    unset($tmp['orderDetail']['id']);
                    $newOrderDetailData[] = $tmp;
                    $tmp_sub_total = $tmp_sub_total + intval($amount) * floatval($tmp['orderDetail']['itemPrice']);
                }
                if(empty($newOrderDetailData)){
                	$error_msg[] = '拆分后的订单详情为空:';
                	 M('orderLog')->orderOperatorLog('no sql', '拆分后的订单详情为空:', $omOrderId);
                	 continue;
                }
                //拆分后的子订单的总金额
                $newOrderData['order']['actualTotal'] = $tmp_sub_total;
                
                $newOrderData['orderDetail'] = $newOrderDetailData;//子订单的详情
                //echo '<pre>77';print_r($newOrderData);
                
                $GLOBALS['allow_override_order'] = true;
                
                //echo '<pre>';print_r($newOrderData);
                M('orderLog')->orderOperatorLog('no sql', '开始插入新订单数据：', $omOrderId);
                
                if(A('OrderAdd')->act_insertOrder(array($newOrderData))){//插入子单
                    $newOrderId = M('OrderAdd')->getInsertOrderId();//生成的子单OmOrderId
                   //echo '$newOrderId:'.$newOrderId;exit;
                    M('orderLog')->orderOperatorLog('no sql', '拆分订单 '.$newOrderId.'成功', $omOrderId);
                    M('orderLog')->orderOperatorLog('no sql', '拆分订单 '.$newOrderId.'来自订单'.$omOrderId, $newOrderId);
                    
                    $splitOrderData = array();
                    $splitOrderData['main_order_id'] = $omOrderId;
                    $splitOrderData['split_order_id'] = $newOrderId;
                    $splitOrderData['creator'] = empty($_SESSION['sysUserId'])?'1':$_SESSION['sysUserId'];
                  
                    if(M('OrderAdd')->insertSplitOrderRecord($splitOrderData)){//添加拆分记录
                        $newOrderVirtRelaArr[$virtId] = $newOrderId;
                    }else{
                    	return M('OrderAdd')->getErrorMsg();
                    }                    
                }else{
                	//$newOrderVirtRelaArr[$virtId] = M('OrderAdd')->getErrorMsg();
                	$err = M('OrderAdd')->getErrorMsg();
                	M('orderLog')->orderOperatorLog('no sql', '拆分子订单 失败：'.$err, $omOrderId);
                	return $err;
                }
            }
            
            M('orderLog')->orderOperatorLog('no sql', '订单号：'.$omOrderId.'拆分完毕,子订单数据为：'.json_encode($newOrderVirtRelaArr), $omOrderId);
            
            
            
            /**
            $arr = M('OrderAdd')->getAllRunSql();
            echo '<pre>77';print_r($arr);
            echo '============66';
            print_r($newOrderVirtRelaArr);
            exit;
            /**/
            $GLOBALS['allow_override_order'] = false;

            //先删除主表，否则后面插入的订单会影响到待发货tongj
            M('OrderManage')->updateData($omOrderId, array('isSplit'=>1,'is_delete'=>'1'));
            
            
            M('Base')->commit();
            
            
            $error_arr = array_merge(A('OrderAdd')->act_getErrorMsg(),$error_msg);
            
            if(!empty($error_arr)){
            	return implode("\r\n", $error_arr);
            }
           
            return $newOrderVirtRelaArr;
        	
	}
}
?>