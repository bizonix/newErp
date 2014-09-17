<?php
/*
 * 订单通用操作类模型
 * @add by lzx ,date 20140603
 */
class OrderManageModel extends CommonModel{
	public function __construct(){
		parent::__construct();
	}
    
	/**
     * 合并订单，根据POST过来的订单编号进行合并
     * 以下为demo
     * @param array $orderids 需要被合并的订单,需要遵循从小到大的排序
     * @return bool 是否合并成功
     * @author lzx
     */
    public function combineOrder($insertOrder,$ordermainid,$orderids){
    	$order 			= $insertOrder['order'];
    	$orderdetail	=  $insertOrder['orderdetail'];
    	if(!$this->updateData($ordermainid,$order)){
    		return false;
    	}
    	//$orderAddObj = M('OrderAdd');
    	foreach($orderdetail as $detail){
			/*if(!$orderAddObj->insertOrderDetailPerfect($detail)){
				return false;
			}*/
			if(!$this->insertOrderdetail($detail)){
				return false;
			}
		}
		$data = array(
				'is_delete' => 1,
				'combineOrder' => 1
		);
		foreach ($orderids AS $oid){
			if(!$this->updateData($oid, $data)){
				//删除订单(逻辑删除)
				return false;
			}
		}
		return true;						
    }
    
    /**
     * 合并包裹，无输入参数，直接在对应的用户权限账号下面合并
     */
    public function combineOrderPackage(){
    	
    }
       
  	/**
  	 * 手动拆分订单
  	 * @param array $insertOrder
  	 * @author czq
  	 */
    public function handSplitOrder($insertOrder,$orderid){
    	
    	$GLOBALS['allow_override_order'] = true;
    	
    	$orderAddObj = M('OrderAdd');//echo '<pre>';print_r($insertOrder);exit;
		if($orderAddObj->insertOrderPerfect($insertOrder)){
			$insert_id = $orderAddObj->getInsertOrderId();
			//插入拆分记录
			$splitRecord = array(
				'main_order_id'  	=>	$orderid,
				'split_order_id' 	=>	$insert_id,
				'creator'			=>	$_SESSION['sysUserId'],
				'createdTime'		=>  time()
			);
			$tableName = C('DB_PREFIX').'records_splitOrder';
			if(!$this->insertData($splitRecord,$tableName)){
				self::$errMsg[10092] = get_promptmsg(10092);
				return false;
			}
			//修改原订单为拆分订单
			$orderData = array(
					'isSplit' => 1
			);
			
			
			$GLOBALS['allow_override_order'] = false;
			
			if(!$this->updateData($orderid, $orderData)){
				self::$errMsg[10093] = get_promptmsg(10093);
				return false;
			}
			return $insert_id;
		}
		
		$GLOBALS['allow_override_order'] = false;
		
		return false;
    }
    
    /**
     *超重拆单，只能是针对一个订单拆分
     *@param array $inserOrder
     *@return bool
     *@author czq
     */
    public function overWeightSplit($insertOrder,$orderid){
    	 if(A('OrderAdd')->act_insertOrder($insertOrder)){
    	 	$insert_id = M('OrderAdd')->getInsertOrderId();
    	 	//插入拆分记录关系表
    	 	$splitRecord = array(
    	 			'main_order_id'  	=>	$orderid,
    	 			'split_order_id' 	=>	$insert_id,
    	 			'creator'			=>	$_SESSION['sysUserId'],
    	 			'createdTime'		=>  time()
    	 	);
    	 	$tableName = C('DB_PREFIX').'records_splitOrder';
    	 	if(!$this->insertData($splitRecord,$tableName)){
    	 		self::$errMsg[10092] = get_promptmsg(10092);
    	 		return false;
    	 	}
    	 	return $insert_id;
    	 }
    	 
    }
    
    /**
     *自动拆分订单 
     */
    public function autoSplitOrder(){
    	
    }
    
    /**
     * 复制订单补寄，只针对已完结订单!!!
     * @param array $orderData
     * @param string $tablekey
     * @param int $orderid
     * @return $insertId
     * @author czq
     */
    public function copyOrderForResend($orderData,$tablekey,$orderid){
    	$OrderAddObj = M('OrderAdd');
		 if($OrderAddObj->insertOrderPerfect($orderData)){
		 	$tableName = C('DB_PREFIX').$tablekey.'_order';
		 		$updatesql = 'update ' . $tableName . ' set isCopy=1 where id=' . $orderid . ' and is_delete = 0 and storeId = 1';
		 	if(!$this->sql($updatesql)->update()){
		 		return false;
		 	}
		 	return true;
		 }
		 return false;
    }
    
    
    /**
     * 复制订单补寄，
     * @param array $orderData
     * @param string $tablekey
     * @param int $orderid
     * @return $insertId
     * @author yxd 
     */
    public function copyOrder($tablekey,$orderid){
    		$tableName = C('DB_PREFIX').$tablekey.'_order';
    		$updatesql = 'update ' . $tableName . ' set isCopy=1 where id=' . $orderid . ' and is_delete = 0 and storeId = 1';
    		if(!$this->sql($updatesql)->update()){
    			return false;
    		}
    		return true;
    }
    
	/**
     * 申请打印订单
     */
    public function applyPrintOrder(){
    	
    }
	
    /**
     * 修改订单状态  未完成
     * @return boolean
     * @author czq 2014年6月17日
     */
    public function updateOrderStatus(){
    	$_POST;
    	if (isset($condition['id'])){
    		$ids = format_array($condition['id']);
    		$fromstatus = M('Order')->getOrderStatusById($ids);
    		//是否有移动到对应分类权限验证  待开发
    		if (false){
    			return false;
    		}
    		return M('OrderManage')->updateOrderStatusById($condition['id'], $tostatus);
    	}
    	if (isset($condition['status'])) {
    		//是否有移动到对应分类权限验证
    		if (false){
    			return false;
    		}
    		return M('OrderManage')->updateOrderStatusByStatus($condition['status'], $tostatus);
    	}
    	self::$errMsg[10029] = get_promptmsg(10029, json_encode($condition));
    	return false;
    }
	
    /**
     * 插入信息(non-PHPdoc)
     * @see CommonModel::insertData()
     * @author czq
     */
    public function insertData($data,$tableName=''){
    	$fdata = $this->formatInsertField($tableName, $data);
    	if ($fdata===false){
    		self::$errMsg = $this->validatemsg;
    		return false;
    	}
    
    	if ($this->checkIsExists($fdata)){
    		return false;
    	}
    	return $this->sql("INSERT INTO ".$tableName." SET ".array2sql($fdata))->insert();
    }
	
    /**
     * 重新封装订单更新操作
     * (non-PHPdoc)
     * @see CommonModel::updateData()
     */
	public function updateData($id, $data, $tableName=''){
		$id = intval($id);
		if ($id==0){
			return false;
		}
		if(empty($tableName)){
			$tableName = C('DB_PREFIX').'unshipped_order';
		}
		$fdata = $this->formatUpdateField($tableName, $data);
		if ($fdata===false){
			self::$errMsg = $this->validatemsg;
			return false;
		}
		return $this->sql("UPDATE ".$tableName." SET ".array2sql($fdata)." WHERE id={$id}")->update();
	}
	
	public function updateDataByCondition($data,$where){
		$tableName = 'om_unshipped_order';
		$fdata = $this->formatUpdateField($tableName, $data);
		if ($fdata===false){
			self::$errMsg = $this->validatemsg;
			return false;
		}
		return $this->sql("UPDATE ".$tableName." SET ".array2sql($fdata)." WHERE $where")->update();
	}
	/**
	 * 插入订单明细（自用，以后会用公用方法）
	 * @param array $detail
	 * @author czq
	 */
	public function insertOrderdetail($detail){
		$result = $this->sql("INSERT INTO om_unshipped_order_detail SET ".array2sql($detail['orderDetail']))->insert();
		if($result){
			$orderdetailId = $this->getLastInsertId();
			$result = $this->sql("INSERT INTo om_unshipped_order_detail_extension_ebay SET ".array2sql($detail['orderDetailExtension']))->insert();
			if($result){
				return true;
			}
		}
		return false;
	}
	/**
	 * 合并关系表更新操作
	 * @param number $id
	 * @param array $data
	 * @param string $ifson
	 * @return boolean
	 * @author czq
	 */
	public function updateCombinePackageRecord($id,$data,$ifson=false){
		$id = intval($id);
		if ($id==0){
			return false;
		}
		$tableName = C('DB_PREFIX').'records_combinePackage';
		$fdata = $this->formatUpdateField($tableName, $data);
		if ($fdata===false){
			self::$errMsg = $this->validatemsg;
			return false;
		}
		$sql = "UPDATE ".$tableName." SET ".array2sql($fdata);
		if(!$ifson){
			$sql .= " WHERE main_order_id={$id}";
		}else{
			$sql .= " WHERE split_order_id={$id}";
		}
		return $this->sql($sql)->update();
	}
    
    /**
	 * 逻辑删除订单及其相关（目前根据表结构只有order及detail,以后其他扩展可能是物理删除）,注意，必须在逻辑层调用事务处理
	 * @param int $omOrderId
	 * @param array $detailData
	 * @return array
	 * @author zqt
	 */
	public function deleteOrderRelation($omOrderId){
		$omOrderId = intval($omOrderId);
		if ($omOrderId <=0){
			return false;
		}
        $orderData = array();
        $orderData['is_delete'] = 1;
        if($this->updateData($omOrderId, $orderData) !== false){//逻辑删除unshipped_order表
            $orderDetailData = array();
            $orderDetailData['is_delete'] = 0;
            $this->updateData($omOrderId, $orderDetailData, C('DB_PREFIX').'unshipped_order_detail');//逻辑删除unshipped_orderdetail表
        }
        return true;//只要存在这个单都返回true;
	}
    
    
/**
	 * 超重订单判断
	 * @eturn array 返回ordertype
	 * @author andy 
	 */
	public function handleOverWeightOrder($orders = array(),$PACKAGE_MAX_WEIGHT=0){
			
    		$order 				= $orders['order'];
    		$orderExtenData 	= $orders['orderExtension'];
    		$orderUserInfoData 	= $orders['orderUserInfo'];
    		
    		$_actualTotal 		= $order['actualTotal'];
    		$_actualShipping 	= $order['actualShipping'];
    		$_platformId 		= $order['platformId'];
    		$order_id 			= $order['id'];
    		$_transportId 		= $order['transportId'];
    		$isExpressDelivery = empty($order['isExpressDelivery'])? 0 : $order['isExpressDelivery'];
    		$ORDER_STATUS = C('ORDER_STATUS');
    		//快递订单，不存在是否超重情况
    		if($isExpressDelivery){
    			return false;
    		}
    		
    		$is_order_splitted = false;
    		
    		$weightlists = array();
    		$skuinfo = array();
    		$shippfee_arr = array();
    		
    		$sku_total_number = 0;
    		
    		//如果已经是相关拆分或复制订单，则停止操作
	        if($order['isSplit'] != 0 || $order['combinePackage'] != 0 || $order['isCopy'] != 0){
	        	//return get_promptmsg(10130);//该订单已经是拆分订单或复制订单，禁止再次操作！
	        } 
	        
        	$orderType = $order['orderType'];
        	/**/
			 //判断当前待拆分订单的状态，拆分后改订单作废。如果是待发货或者缺货状态，需要减少待发货数量
            if(in_array($orderType,$ORDER_STATUS['waitingsend'])){
            	M('orderLog')->orderOperatorLog('no sql', '该订单sku数量从待发货数量中扣除 ', $order_id);
            	//更新该订单中sku的待发货数量
				F('SkuDailyInfo')->updateDailyAverageInfoByOrder($order_id, 2);
            }
            /**/
    		foreach($orders['orderDetail'] as $detailinfo){
    			//订单只有一个sku，也同样需要判断超重处理
    			if (count($orders['orderDetail']) ==1 && $detailinfo['orderDetail']['amount']<=1) {
    				//return false;//return get_promptmsg(10082);
    			}
    			
    			$sku_total_number = $sku_total_number + $detailinfo['orderDetail']['amount'];
    			
    			$skuweight = M('InterfacePc')->getSkuWeight($detailinfo['orderDetail']['sku']);
    			
    			M('orderLog')->orderOperatorLog('no sql', 'sku：'.$detailinfo['orderDetail']['sku'].'的重量为：'.$skuweight, $order_id);
    			
    			//$skuweight = 0.401;
    			$shippfee_arr[$detailinfo['orderDetail']['sku']] = round($detailinfo['orderDetail']['shippingFee']/$detailinfo['orderDetail']['amount'],3);//单个料号的运费
    			$skuinfo[$detailinfo['orderDetail']['sku']] = $detailinfo;
    			for($i=1; $i<=$detailinfo['orderDetail']['amount']; $i++){
    				$weightlists[$detailinfo['orderDetail']['sku']][] = $skuweight;
    			}
    		}
    		$splitWeigths = array();
    		
    		if(empty($PACKAGE_MAX_WEIGHT)){
    			$PACKAGE_MAX_WEIGHT = C('PACKAGE_MAX_WEIGHT');
    		}
    		//echo '<pre>$PACKAGE_MAX_WEIGHT:'.$PACKAGE_MAX_WEIGHT;print_r($weightlists);exit;
    		
    		$is_over_weight = false;
    		
    		foreach($weightlists as $sku =>$weights){
    			
    			$checkweight = 0;
    			$keyarray = array();
    			
    			foreach($weights as $weight){
    				$checkweight += $weight;
    				
    				if($checkweight > $PACKAGE_MAX_WEIGHT ){
    					if(empty($keyarray)){
    						$keyarray[$sku] = 1;
    					}
    					
    					$is_over_weight = true;
    					
    					$splitWeigths[] = $keyarray;
    					$keyarray = array();
    					$checkweight = $weight;
    					$keyarray[$sku] = 1;
    				}else{
    					
    					$keyarray[$sku] += 1;
    					
    				}
    				//echo '$PACKAGE_MAX_WEIGHT:'.$PACKAGE_MAX_WEIGHT.',$checkweight:'.$checkweight.'===';print_r($splitWeigths);
    			}
    			
	    		//处理最后一组
	    		if(!empty($keyarray)){
	    			$splitWeigths[] = $keyarray;
	    		}
    		}
   
    		//没有超重sku
    		if(empty($splitWeigths) || !$is_over_weight){
    			return false;
    		}

    		//echo '$PACKAGE_MAX_WEIGHT：'.$PACKAGE_MAX_WEIGHT.'<pre>';print_r($weightlists);print_r($splitWeigths);exit;
    		
    		//订单只有一个sku，并且超重
    		if(count($weightlists) == 1 && count($splitWeigths)<=1 && $is_over_weight){
    			return 'ONLY_ONE_SKU_OVER_HEIGHT';
    		}

    		//echo '<pre>$$weightlists:'.$weightlists.'===';print_r($splitWeigths);exit;
    		//订单中所有的单个sku都超重，则无法拆分
    		if($sku_total_number == count($splitWeigths) && $is_over_weight){
    			return 'ONLY_ONE_SKU_OVER_HEIGHT';
    		}
    		
    		M('orderLog')->orderOperatorLog('no sql', '超重拆分规则：'.json_encode($splitWeigths), $order_id);
    		
    		$orderManageObj = M('OrderManage');
    		$orderManageObj->begin();			//开始事物
    			
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
    			$insert_obj_order_data['orderType'] = 0;//超重拆分后的订单orderType默认为0
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
    			
    			//把主订单逻辑删除
    			$data = array(
    				'is_delete'=>1,
    				'isSplit'=>1
    			);
    			if(!$orderManageObj->updateData($order_id,$data)){
    				$orderManageObj->rollback();
    				return get_promptmsg(10083);
    			}
    			//拆分记录
    			
    			$is_order_splitted = true;
    			
    			//插入拆分的子订单
    			$GLOBALS['allow_override_order'] = true;//echo '<pre>';print_r($insert_orderData);exit;
    			$GLOBALS['is_from_over_weight_order'] = true;
    			
    			if(A('OrderAdd')->act_insertOrder(array($insert_orderData))){
    				$insert_id = M('OrderAdd')->getInsertOrderId();
    				
    				M('orderLog')->orderOperatorLog('no sql', '超重拆分生成子订单'.$insert_id, $order_id);
    				
    				//插入拆分记录关系表
    				$splitRecord = array(
		    	 			'main_order_id'  	=>	$this->getMainIdBySplittedId($order_id),
		    	 			'split_order_id' 	=>	$insert_id,
		    	 			'creator'			=>	empty($_SESSION['sysUserId'])?0:$_SESSION['sysUserId'],
		    	 			'createdTime'		=>  time(),
    						'main_order_type'	=>	'1'
		    	 	);
		    	 	$tableName = C('DB_PREFIX').'records_splitOrder';
		    	 	if(!$this->insertData($splitRecord,$tableName)){
		    	 		$orderManageObj->rollback();
		    	 	}
		    	 	
    			}else{
    				
    				$error_arr = A('OrderAdd')->act_getErrorMsg();
		            if(!empty($error_arr)){
		            	return implode("\r\n", $error_arr);
		            }
		            
    				$orderManageObj->rollback();
    			}	
    			
    			$GLOBALS['allow_override_order'] = false;
    		}
    		/**
            $arr = M('OrderAdd')->getAllRunSql();
            echo '<pre>77';print_r($arr);
            /**/
            
    		$orderManageObj->commit();
    		
    		
    		return $is_order_splitted;
    	
	}
	/**
	 * 合并待发货的订单包裹
	 * @return array 没有合并的订单 返回  array('status'=>'success','msg'=>'no_combined_order')
	 * @return array 合并成功 返回  array('status'=>'success','msg'=>'combin_order_finish')
	 * @author andy 
	 */
	public function handleCombineOrders(){
		
		$StatusMenu = M('StatusMenu');
		$wait_ship_status = intval($StatusMenu->getOrderStatusByStatusCode('ORDER_WAIT_SHIP','id'));
		
		$PACKAGE_MAX_WEIGHT = C('PACKAGE_MAX_WEIGHT');
		$transport_id_eub = intval(C('TRANSPORT_ID_EUB'));//EUB的运输方式id
	
		$group_by_fields = array('o.transportId','o.platformId','o.accountId',
								 'ou.platformUsername','ou.city','ou.state','ou.address1',
								 'ou.address2',
							);
							
		$select = "select c.calOrderWeight, o.id,o.calcWeight, ".implode(' , ',$group_by_fields)." from om_unshipped_order AS o 
				left join om_unshipped_order_userInfo AS ou ON o.id=ou.omOrderId 
				left join om_order_calculation AS c on c.omOrderId=o.id 
				  ";
		
		$where = " where o.orderStatus=".$wait_ship_status." and o.isAllowCombineOrder !=1 and o.combinePackage=0 
				and o.isSplit=0 and o.is_delete=0 and o.transportId != ".$transport_id_eub;
		
		
							
		$group_by = " GROUP BY ".implode(' , ',$group_by_fields)." 
					  HAVING count(*)>1 ";
		
		$combine_sql = $select.$where . $group_by;
		
		$wait_combine_orders = M('Base')->sql($combine_sql)->select();
		//echo $combine_sql;echo '<Pre>';print_r($wait_combine_orders);exit;
		
		$result_arr = array('status'=>'success','msg'=>'no_combined_order');
		$orders_combined = false;
		
		if(empty($wait_combine_orders)){
			return $result_arr;
		}
		
		foreach ($wait_combine_orders AS $combine_order){
			
			$where_some_order = $where;
			//根据上面的group by字段，组装where条件
			foreach($group_by_fields as $tmp_field){
				$tmp = preg_replace('/^\w*\./i','',$tmp_field);
				$where_some_order .= " and ".$tmp_field."='".$combine_order[$tmp]."'";
			}
			
			$sql_for_some_order = $select . $where_some_order;
			$buyer_orders = $this->sql($sql_for_some_order)->select();
			
			//echo $sql_for_some_order;echo '<Pre>';print_r($buyer_orders);
			
			//只有一个订单，无需合并
			if(count($buyer_orders)<2){
				continue;
			}
			
			
			
			//获取该批订单的总重量
			$buyer_orders_weight_total = 0;
			
			foreach($buyer_orders as $tmp){
				$platformId = $tmp['platformId'];
				$omOrderId = $tmp['id'];
				$order_detail_ids[$platformId][] = $omOrderId;
				$buyer_orders_weight_total = $buyer_orders_weight_total + $tmp['calOrderWeight'];
			}
			//echo '$package_numbers:'.$buyer_orders_weight_total;exit;
			$package_numbers = ceil($buyer_orders_weight_total / $PACKAGE_MAX_WEIGHT);
			
			for($package=0; $package<$package_numbers; $package++){
				$tmp_package_weight  		= 0;
				$tmp_package_shipfee 		= 0;
				$tmp_package_totalmoney   	= 0;
				$tmp_buyer_orders_weight_arr  = array();
				$shippinglist = array();
				$packages_id_arr   = array();
				$tmp_one_package_orders  = array();
				
				//遍历组装一个包裹
				foreach($buyer_orders as $tmp_key=>$tmp_order){
					$packages_id_arr[$tmp_key]    = $tmp_order['id'];
					$tmp_package_weight 	   += $tmp_order['calOrderWeight'];
					$tmp_package_shipfee 	   += $tmp_order['calcShipping'];
					$tmp_package_totalmoney 			    += $tmp_order['actualTotal'];
					$tmp_buyer_orders_weight_arr[$tmp_key]    = $tmp_order['calOrderWeight'];
					$tmp_one_package_orders[$tmp_key]    = $tmp_order;
					
					//当重量+到超过2公斤时，将本次ebay_id信息删除，跳到下一次匹配，直到循环完毕
					if($tmp_package_weight > $PACKAGE_MAX_WEIGHT){
						unset($packages_id_arr[$tmp_key]);
						$tmp_package_weight = $tmp_package_weight - $tmp_order['calOrderWeight'];
						$tmp_package_totalmoney  = $tmp_package_totalmoney  - $tmp_order['actualTotal'];	
						unset($tmp_buyer_orders_weight_arr[$tmp_key]);
						unset($tmp_one_package_orders[$tmp_key]);
					}
					
				}
				//echo '<Pre>$packages_id_arr';print_r($tmp_one_package_orders);exit;
				
				
				if(empty($tmp_one_package_orders)) continue;
				
				$main_order    = $tmp_one_package_orders[0];
				$main_order_id = $main_order['id'];
				
				try{
					M('Base')->begin();
					
					//该订单添加合并日志
					$log_msg = get_promptmsg(10133, implode(',',$packages_id_arr));
					M('orderLog')->orderOperatorLog('no sql', $log_msg, $main_order_id);
					
					
					//合并后的订单和子订单的对应关系写入表
					
					$main2child_arr = array();
					$createdTime = time();
					$creator = $_SESSION['sysUserId'];
					
					//更新包裹订单状态，合并后的订单和子订单的对应关系写入表
					foreach($packages_id_arr as $tmp_key=>$tmp_order_id){
						if($tmp_order_id == $main_order_id){
							$update_data = array('combinePackage'=>1);//主包裹
						}else{
							$update_data = array('combinePackage'=>2);//子包裹
							
							//仅写入 主订单对应的子订单（主订单对应主订单不写入）
							$main2child_arr[] = "($main_order_id,$tmp_order_id,$createdTime,'$creator')";//主包裹对应的子包裹
						}
						//更新订单为主包裹或者子包裹
						M('orderModify')->updateOrderInfo($tmp_order_id, $update_data);
						
						//更新子订单包裹的操作日志
						$log_msg = get_promptmsg(10134, $main_order_id);
						M('orderLog')->orderOperatorLog('no sql', $log_msg, $tmp_order_id);
					}
					
					$orders_combined = true;
					
					$combined_sql = "insert into  om_records_combinePackage (main_order_id,split_order_id,createdTime,creator) values";
					$combined_sql .= implode(',',$main2child_arr);
					$result = M('Base')->sql($combined_sql)->insert();
					
					if($result){
						M('Base')->commit();
					}else{
						M('Base')->rollback();
					}
					
				} catch (Exception $e) {
					M('Base')->rollback();
				}
				
				//该批包裹合并后，从$buyer_orders中删除掉,然后从剩下的$buyer_orders中继续组装包裹
				foreach($buyer_orders as $tmp_key=>$tmp_order){
					if(in_array($tmp_order['id'], $packages_id_arr)){
						unset($buyer_orders[$tmp_key]);
					}
				}
	
			}
		}
		
		if($orders_combined){
			$result_arr = array('status'=>'success','msg'=>'combin_order_finish');
		}
		
		return $result_arr;
	}
	
	public function updateOrderCalcByOmOrderId($data,$omOrderId){
	    $omOrderId = intval($omOrderId);
		$tableName = 'om_order_calculation';
		$fdata = $this->formatUpdateField($tableName, $data);
		if ($fdata===false){
			self::$errMsg = $this->validatemsg;
			return false;
		}
		return $this->sql("UPDATE ".$tableName." SET ".array2sql($fdata)." WHERE omOrderId=$omOrderId")->update();
	}
    
    public function updateOrderAuditByWhereArr($data,$whereArr){
		$tableName = 'om_records_order_audit';
		$data = $this->formatUpdateField($tableName, $data);
		if ($data===false){
			self::$errMsg = $this->validatemsg;
			return false;
		}
		if ($this->formatWhereField($tableName, $data)===false){
			self::$errMsg = $this->validatemsg;
			return false;
		}
		return $this->sql("UPDATE ".$tableName." SET ".array2sql($data)." WHERE ".implode(' AND ',array2where($whereArr)))->update();
	}
	
	public function getMainIdBySplittedId($split_id){
		$result = $this->sql("select main_order_id from ".C('DB_PREFIX').'records_splitOrder'." where split_order_id=".$split_id)->select();
		if(empty($result)){
			return $split_id;
		}
		return $result[0]['main_order_id'];
	}
	
	
}
?>