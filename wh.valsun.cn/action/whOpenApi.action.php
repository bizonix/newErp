<?php
/*
 * 仓库系统Api
 * ADD BY hws
 */
class WhOpenApiAct extends Auth {
	static $errCode = 0;
	static $errMsg  = "";

	//取得sku库存
	function act_getSkuStock() {
		$sku = isset($_GET['sku']) ? trim($_GET['sku']) : '';
		$storeId = isset($_GET['storeId']) ? $_GET['storeId'] : 1;
		if(empty($sku)){   //参数不完整
            self::$errCode = 101;
            self::$errMsg = '参数信息不完整';
            return false;
        }
		$sku_info = OmAvailableModel::getTNameList("wh_sku_location","actualStock","where sku='$sku' and storeId={$storeId}");
		if($sku_info){
			return $sku_info[0]['actualStock'];
		}else{
			self::$errCode = 102;
            self::$errMsg = '找不到对应sku的库存信息';
            return false;
		}
	}
	
	//获取仓库名称列表
	function act_getStoreList() { 
		//global $memc_obj;
		//$cacheName = "wh_getStoreList";		
		//$list = $memc_obj->get_extral($cacheName);	
		//if($list){
			//return json_encode($list);
		//}else{			
			$storetList = OmAvailableModel::getTNameList("wh_store","id,whName","where status=1");
			if(!$storetList){
				self::$errCode = 101;
				self::$errMsg = "没取到仓库列表！";
				return ;
			}else{
				/*
				$isok = $memc_obj->set_extral($cacheName, $storetList);
				if(!$isok){
					self::$errCode = 102;
					self::$errMsg = 'memcache缓存账号信息出错!';
					return json_encode($storetList);
				}
				*/
				return json_encode($storetList);
			}
		//}
	}
	
	//检测sku所在仓库有库存的sku
	function act_detectSkuStoreInfo() { 
		$skuArr  = isset($_POST['skuArr']) ? json_decode(gzinflate($_POST['skuArr'])) : '';
		$storeId = isset($_POST['storeId']) ? $_POST['storeId'] : '';
		if(!is_array($skuArr)){
			self::$errCode = 401;
			self::$errMsg = "skuArr不是数组！";
			return ;
		}
		if(empty($storeId)){
			self::$errCode = 402;
			self::$errMsg = "storeId不能为空";
			return ;
		}
		
		$sku_str = implode("','",$skuArr);
		$sku_str = "('".$sku_str."')";

		$sku_list = OmAvailableModel::getTNameList("wh_sku_location","sku,actualStock","where sku in {$sku_str} and actualStock>0 and storeId='$storeId'");
		if($sku_list){
			$return_info = array();
			foreach($sku_list as $list){
				$return_info[] = $list['sku'];
			}
			return json_encode($return_info);
		}else{
			return '';
		}
	}
	
	//获取未订单列表信息
    function act_getUnusualOrderList(){
		$sku 		= isset($_GET['sku']) ? $_GET['sku'] : '';
		$purid 	 	= isset($_GET['purid']) ? base64_decode($_GET['purid']) : '';
		$abStatus 	= isset($_GET['abStatus']) ? $_GET['abStatus'] : '';
		$isConfirm 	= isset($_GET['isConfirm']) ? $_GET['isConfirm'] : '';
		$startTime 	= isset($_GET['startTime']) ? $_GET['startTime'] : '';
		$endTime 	= isset($_GET['endTime']) ? $_GET['endTime'] : '';
		$page       = isset($_GET['page']) ? $_GET['page'] : '1';
		
		$where = "where storeId=1 and isConfirm=1 and abStatus=0 ";		
		if(!empty($sku)){
			$where .= "and sku='$sku' ";
		}
		if(!empty($purid)){
			$where .= "and purchaseId in ({$purid}) ";
		}
		if(!empty($abStatus)){
			$where .= "and abStatus='$abStatus' ";
		}
		if(is_numeric($isConfirm)){
			$where .= "and isConfirm='$isConfirm' ";
		}
		if(!empty($startTime)){
			$where .= "and createdTime>='$startTime' ";
		}
		if(!empty($endTime)){
			$where .= "and createdTime<='$endTime' ";
		}
		
		$totalrow  = OmAvailableModel::getTNameCount("wh_abnormal_purchase_orders",$where);		
		$pagesize 	= 100;//每页显示条数
		$pageindex  = $page;
		$limit      = "limit ".($pageindex-1)*$pagesize.",$pagesize";
		$where     .= 'order by id desc '.$limit;	
		$list  	   = OmAvailableModel::getTNameList("wh_abnormal_purchase_orders","*",$where);
		
		$datalist[0] = $totalrow;
		$datalist[1] = $list;
		return json_encode($datalist);
	}
	
	//未订单处理操作
    function act_operatUnusualOrder(){
		$purid 	  = isset($_GET['purid']) ? $_GET['purid'] : '';
		$oid 	  = isset($_GET['oid']) ? base64_decode($_GET['oid']) : '';
		$category = isset($_GET['category']) ? $_GET['category'] : '';

		if(empty($purid) || empty($oid) || empty($category)){   //参数不完整
            self::$errCode = 101;
            self::$errMsg = '参数信息不完整';
            return false;
        }

		$nowtime = time();
		if($category=='comfirmorder'){
			$rtn = OmAvailableModel::updateTNameRow("wh_abnormal_purchase_orders","set isConfirm=1,confirmUserId='{$purid}'","where id in {$oid}");
		}else if($category=='patchorder'){
			$recordnumber 	  = isset($_GET['recordnumber']) ? $_GET['recordnumber'] : '';
			$rtn = OmAvailableModel::updateTNameRow("wh_abnormal_purchase_orders","set abStatus=1,ioOrdersn='$recordnumber'","where isConfirm=1 and id in {$oid}");
		}else if($category=='setzero'){
			$rtn = OmAvailableModel::updateTNameRow("wh_abnormal_purchase_orders","set abStatus=3,nums=0","where isConfirm=1 and id in {$oid}");
		}else if($category=='secondstockin'){
			$rtn = OmAvailableModel::updateTNameRow("wh_abnormal_purchase_orders","set abStatus=3,nums=0","where isConfirm=1 and id in {$oid}");
		}else if($category=='backorder'){
			$rtn = OmAvailableModel::updateTNameRow("wh_abnormal_purchase_orders","set abStatus=2,cancelTime='{$nowtime}',cancelUserId='{$purid}'","where isConfirm=1 and id in {$oid}");
		}

		if($rtn===false){
			self::$errCode = 102;
            self::$errMsg = '操作失败';
            return false;
		}else{
			return true;
		}
		
	}
	
	//取得订单sku需配及配货数量
	function act_getOrderSkuPickingRecords() {
		$orderId = isset($_GET['orderId']) ? intval(trim($_GET['orderId'])) : '';
		$sku 	 = isset($_GET['sku']) ? trim($_GET['sku']) : '';
		if(empty($orderId) || empty($sku)){   //参数不完整
            self::$errCode = 101;
            self::$errMsg = '参数信息不完整';
            return false;
        }
		$order_info = OmAvailableModel::getTNameList("wh_shipping_order_relation","shipOrderId","where originOrderId='$orderId' and storeId=1");
		if($order_info){
			$amount    = 0;
			$totalNums = 0;
			$scanUserId = '';
			$scanTime   = '';
			$order_detail = OmAvailableModel::getTNameList("wh_shipping_orderdetail","amount","where shipOrderId={$order_info[0]['shipOrderId']} and sku='$sku' and storeId=1");
			if($order_detail){
				foreach($order_detail as $detail){
					$totalNums += $detail['amount'];
				}
				$picking_info = OmAvailableModel::getTNameList("wh_order_picking_records","*","where sku='$sku' and shipOrderId={$order_info[0]['shipOrderId']} and isScan=1 and is_delete=0");
				if($picking_info){
					foreach($picking_info as $picking){
						$amount    += $picking['amount'];
					}
					$scanUserId = $picking_info[0]['scanUserId'];
					$scanTime   = $picking_info[0]['scanTime'];
				}
				return json_encode(array('amount'=>$amount,'totalNums'=>$totalNums,'scanUserId'=>$scanUserId,'scanTime'=>$scanTime));
			}else{
				self::$errCode = 103;
				self::$errMsg = '找不到该订单对应发货单的详细';
				return false;
			}
		}else{
			self::$errCode = 102;
			self::$errMsg = '找不到该订单对应的发货单id';
			return false;
		}
	}
	
	//订单废弃操作
    function act_ordersDiscard(){
		$oidStr  = isset($_GET['oidStr']) ? $_GET['oidStr'] : '';
		$storeId = isset($_GET['storeId']) ? intval($_GET['storeId']) : 1;
		if(empty($oidStr) || !is_numeric($storeId)){  
            self::$errCode = 101;
            self::$errMsg = '参数有误';
            return false;
        }
		$rtn = WhPushModel::orderDiscard($oidStr,$storeId);
		if($rtn){
			return true;
		}else{
			self::$errCode = WhPushModel::$errCode;
            self::$errMsg  = WhPushModel::$errMsg;
            return false;
		}
	}
	
	//取得sku仓位
	function act_getSkuPositions() {
		$sku = isset($_GET['sku']) ? trim($_GET['sku']) : '';
		$storeId = isset($_GET['storeId']) ? intval($_GET['storeId']) : 1;
		if(empty($sku)){   //参数不完整
            self::$errCode = 101;
            self::$errMsg = '参数信息不完整';
            return false;
        }
		$sku_info = OmAvailableModel::getSkuPositions($sku,$storeId);
		if($sku_info){
			return json_encode($sku_info);	
		}else{
			self::$errCode = 102;
            self::$errMsg = '找不到对应sku的仓位信息';
            return false;
		}
	}
	
	//取得无仓位sku列表
	function act_getSkuListUnPosition() {
		$storeId = isset($_GET['storeId']) ? intval($_GET['storeId']) : 0;
		$spu = isset($_GET['spu']) ? trim($_GET['spu']) : '';
		$sku_list = OmAvailableModel::getSkuListUnPositions($spu, $storeId);
		if($sku_list){
			return json_encode($sku_list);
		}else{
			self::$errCode = 101;
            self::$errMsg = '找不到无仓位sku信息';
            return false;
		}
	}
	
	//取得sku所在仓库
	function act_getSkuStores() {
		$sku = isset($_GET['sku']) ? trim($_GET['sku']) : '';
		if(empty($sku)){   //参数不完整
            self::$errCode = 101;
            self::$errMsg = '参数信息不完整';
            return false;
        }
		$sku_info = OmAvailableModel::getSkuStores($sku);
		if($sku_info){
			return json_encode($sku_info);	
		}else{
			self::$errCode = 102;
            self::$errMsg = '找不到对应sku的仓库信息';
            return false;
		}
	}
	
	//插入qc检测良品
	function act_updateTallying() {
		$batchNum 	 = isset($_GET['batchNum']) ? trim($_GET['batchNum']) : '';
		$sku 	     = isset($_GET['sku']) ? trim($_GET['sku']) : '';
		$ichibanNums = isset($_GET['ichibanNums']) ? intval(trim($_GET['ichibanNums'])) : '';
        
        /** 添加接收日志**/
        $log_file   =   'QCReturnLog/'.date('Y-m-d').'.txt';
        $date       =   date('Y-m-d H:i:s');
        $log_info   = sprintf("料号：%s, 时间：%s, 批次号:%s, 良品数：%s \r\n", $sku, $date, $batchNum, $ichibanNums);
        write_log($log_file, $log_info);
        /** end**/
        
		if(empty($batchNum) || empty($sku)){   //参数不完整
            self::$errCode = 101;
            self::$errMsg = '参数信息不完整';
            return false;
        }
        
        /** 添加检测是否存在数据判断**/
        $tallying_info  =   whShelfModel::selectTallyingList('id', array('batchNum'=>$batchNum, 'sku'=>$sku));
        if(empty($tallying_info)){ //不存在对应的点货记录
            self::$errCode = 104;
            self::$errMsg  = '不存在该条点货信息';
            return FALSE;
        }
        
		$update_info = OmAvailableModel::updateTallying($batchNum, $sku, $ichibanNums, time());
		if($update_info){
			return true;	
		}else{
			self::$errCode = 102;
            self::$errMsg  = '插入良品数量失败';
            return false;
		}
	}
	
	//插入qc检测良品
	function act_updatePostReturn() {
		$shipOrderId = isset($_GET['batchNum']) ? trim($_GET['batchNum']) : '';
		$sku 	     = isset($_GET['sku']) ? trim($_GET['sku']) : '';
		$ichibanNums = isset($_GET['ichibanNums']) ? intval(trim($_GET['ichibanNums'])) : '';
		if(empty($shipOrderId) || empty($sku) || empty($ichibanNums)){   //参数不完整
            self::$errCode = 101;
            self::$errMsg = '参数信息不完整';
            return false;
        }
        $log_file    = 'QC/'.date('YmdHis').'.txt';
        $content     = "批次号：{$shipOrderId}, 料号：{$sku}, 良品数：{$ichibanNums} ,";
		$update_info = PostReturnModel::updateIchibanNums($shipOrderId, $sku, $ichibanNums);
		if($update_info){
		    write_log($log_file, $content.' 更新成功!');
			return true;	
		}else{
			self::$errCode = 102;
            self::$errMsg  = '插入良品数量失败';
            write_log($log_file, $content.' 更新失败!');
            return false;
		}
	}
	
	//取得sku库存   参数为数组
	function act_getSkuStockByArr() {
		$skuArr  = isset($_GET['skuArr']) ? json_decode(trim($_GET['skuArr'])) : '';
		$storeId = isset($_GET['storeId']) ? $_GET['storeId'] : 1;
		if(!is_array($skuArr)){   //参数不完整
            self::$errCode = 101;
            self::$errMsg = '参数信息有误';
            return false;
        }
		$sku_stock_info = array();
		foreach($skuArr as $sku){
			$sku_info = OmAvailableModel::getTNameList("wh_sku_location","actualStock","where sku='$sku' and storeId={$storeId}");
			if($sku_info){
				$sku_stock_info[] = array(
					'sku' => $sku,
					'num' => $sku_info[0]['actualStock']
				);
			}else{
				$sku_stock_info[] = array(
					'sku' => $sku,
					'num' => ''
				);
			}
		}
		return json_encode($sku_stock_info);
	}
    
    /** 取得sku库存   参数为数组(新)**/
	function act_getSkuStockByArrNew() {
		$skuArr  = isset($_REQUEST['skuArr']) ? json_decode(trim($_REQUEST['skuArr']), true) : '';
		if(!is_array($skuArr)){   //参数不完整
            self::$errCode = 101;
            self::$errMsg = '参数信息有误';
            return false;
        }
        //print_r($skuArr);exit;
		$sku_stock_info = array();
		foreach($skuArr as $key=>$val){
            if(empty($val)){
                self::$errCode = 102;
                self::$errMsg  = '参数信息有误';
                return false;
            }
		    $skus = '';
		    if(is_array($val)){
		        foreach($val as $v){
		          $skus .= "'".$v."',";
		        }
                $skus   =   trim($skus, ',');
		    }else{
		      $skus   =   '"'.$val.'"';
		    } 
			$sku_info = OmAvailableModel::getTNameList("wh_sku_location","sku,actualStock","where sku in ($skus) and storeId='{$key}'");
			if(!empty($sku_info)){
				foreach($sku_info as $val){
				    $sku_stock_info[$val['sku']][$key] = $val['actualStock'];
				}
			}else{
			     return $sku_info;
			}
		}
		return $sku_stock_info;
	}
	
	//取得sku所在仓库   参数为数组
	function act_getSkuStoresByArr() {
		$skuArr  = isset($_GET['skuArr']) ? json_decode(trim($_GET['skuArr'])) : '';
		if(!is_array($skuArr)){   
            self::$errCode = 101;
            self::$errMsg = '参数信息有误';
            return false;
        }
		$sku_store_info = array();
		foreach($skuArr as $sku){
			$sku_info = OmAvailableModel::getSkuStores($sku);
			if($sku_info){
				$sku_store_info[$sku] = $sku_info;
			}else{
				$sku_store_info[$sku] = '';
			}
		}
		return json_encode($sku_store_info);
	}
    
    /** 取得sku所在仓库   参数为数组(新)
    * Gary
    **/
	function act_getSkuStoresByArrNew() {
		$skuArr  = isset($_GET['skuArr']) ? json_decode(trim($_GET['skuArr'])) : '';
		if(!is_array($skuArr)){   
            self::$errCode = 101;
            self::$errMsg = '参数信息有误';
            return false;
        }
		$sku_store_info = array();
		foreach($skuArr as $sku){
			$sku_info = OmAvailableModel::getSkuStores($sku);
			if($sku_info){
				$sku_store_info[$sku] = $sku_info;
			}else{
				$sku_store_info[$sku] = '';
			}
		}
		return $sku_store_info;
	}
	
	//采购更新料号录入异常
	function act_updateAbnormalOrder() {
		$orderArr = isset($_GET['orderArr']) ? json_decode($_GET['orderArr']) : '';
		if(!is_array($orderArr)){
            self::$errCode = 401;
            self::$errMsg = '参数信息有误';
            return false;
        }
        
        /** 添加接收日志**/
        $log_file   =   'PurchaseReturnRecive/'.date('Y-m-d').'.txt';
        $date       =   date('Y-m-d H:i:s');
        $log_info   = sprintf("时间：%s, 参数：%s \r\n", $date, is_array($orderArr) ? json_encode($orderArr) : $orderArr);
        write_log($log_file, $log_info);
        /** end**/

		$update_info = OmAvailableModel::updateTallyingStatus($orderArr);
		if($update_info){
			return true;
		}else{
			self::$errCode = 402;
            self::$errMsg  = '更新录入状态失败';
            return false;
		}
	}
	
	//获取异常订单列表
    function act_getAbOrderList(){
		$list = WhPushModel::getAbOrderList();
		if($list){
			return $list;
		}else{
			self::$errCode = WhPushModel::$errCode;
            self::$errMsg  = WhPushModel::$errMsg;
            return false;
		}
	}
	
	//获取异常订单配货信息
    function act_getAbOrderInfo(){
		$orderId = isset($_GET['orderId']) ? intval($_GET['orderId']) : 0;
		if($orderId==0){
            self::$errCode = 101;
            self::$errMsg = '参数有误';
            return false;
        }
		$info = WhPushModel::getAbOrderInfo($orderId);
		if($info){
			return $info;
		}else{
			self::$errCode = WhPushModel::$errCode;
            self::$errMsg  = WhPushModel::$errMsg;
            return false;
		}
	}
	
	//异常订单操作
    function act_operateAbOrder(){
		$orderId    = isset($_GET['orderId']) ? intval($_GET['orderId']) : 0;
		$calcWeight = isset($_GET['calcWeight']) ? $_GET['calcWeight'] : 0;
		if($orderId==0 || $calcWeight==0){
            self::$errCode = 101;
            self::$errMsg = '参数有误';
            return false;
        }
		$rtn = WhPushModel::orderUnusual($orderId,$calcWeight);
		if($rtn){
			return true;
		}else{
			self::$errCode = WhPushModel::$errCode;
            self::$errMsg  = WhPushModel::$errMsg;
            return false;
		}
	}
		
	//获取订单配货信息（参数：订单系统订单号）
    function act_getOrderPickingInfo(){
		$orderId = isset($_GET['orderId']) ? intval($_GET['orderId']) : 0;
		if($orderId==0){
            self::$errCode = 101;
            self::$errMsg = '参数有误';
            return false;
        }
		$info = WhPushModel::getOrderPickingInfo($orderId);
		if($info){
			return $info;
		}else{
			self::$errCode = WhPushModel::$errCode;
            self::$errMsg  = WhPushModel::$errMsg;
            return false;
		}
	}
	
	//获取当前时间一小时前入库记录
    function act_getInRecords(){
		$time = time()-3600;
		$info = WhIoRecordsModel::getTNameList("wh_iorecords", "*", "where createdTime>{$time} and ioType=2");
		if($info){
			return $info;
		}else{
			self::$errCode = WhIoRecordsModel::$errCode;
            self::$errMsg  = WhIoRecordsModel::$errMsg;
            return false;
		}
	}
	
	//获取当前时间一小时前出库记录
    function act_getOutRecords(){
		$time = time()-3600;
		$info = WhIoRecordsModel::getTNameList("wh_iorecords", "*", "where createdTime>{$time} and ioType=1");
		if($info){
			return $info;
		}else{
			self::$errCode = WhIoRecordsModel::$errCode;
            self::$errMsg  = WhIoRecordsModel::$errMsg;
            return false;
		}
	}
	
	//返回sku入库信息（pc系统专用）
    function act_getSKUInInfo(){
		$page       = isset($_GET['page']) ? intval($_GET['page']) : 1;		
		$pagesize 	= 200;//每页显示条数
		$pageindex  = $page;
		$limit      = "limit ".($pageindex-1)*$pagesize.",$pagesize";
        $condition  = " order by id asc ".$limit;
		$where      = "where is_delete=0".($_GET['sku']?" AND sku='".$_GET['sku']."'":'');

		$skuTotal   = OmAvailableModel::getTNameList("pc_goods", "count(*) as totalnum", $where);
        //print_r($skuTotal);print_r(count($skuTotal));exit;
        //$skuTotal   =   array_filter(array_map('array_filter', $skuTotal));
		$totalNum   = $skuTotal[0]['totalnum'];
		$whereCon   = $where.$condition;
		$skuInfos   = OmAvailableModel::getTNameList("pc_goods", "id, sku", $whereCon);
        $data       = array();
		if(!empty($skuInfos)){
			$store_skus = array();
			foreach($skuInfos as $info){
			 
                $whId       = 1; //仓库id
                $isHasStock    = 2; //默认没库存
                $isHasLocation = 2; //默认没有仓位
                $location       = '';
                $storageTime    = '';
                
				$positionRelations = OmAvailableModel::getTNameList("wh_product_position_relation", "*", "where pId={$info['id']} and is_delete=0 and positionId!=0 order by storeId asc, nums DESC");
				if(!empty($positionRelations)){
					$timeInfos   = OmAvailableModel::getTNameList("wh_iorecords", "createdTime", "where ioTypeId in(13, 33) and ioType=2 and sku='{$info['sku']}' order by id asc limit 1");
					if(!empty($timeInfos)){
						$storageTime = $timeInfos[0]['createdTime'];
					}
					foreach($positionRelations as $pos){
						if($store_skus[$info['sku']][$pos['storeId']]){
							continue;
						}
						//foreach($positionRelations as $positionRelation){
						$positionInfos = OmAvailableModel::getTNameList("wh_position_distribution", "pName", "where id={$pos['positionId']}");
	                    $whId   =   $pos['storeId'];
	                    if($pos['nums'] > 0){
	                    	$isHasStock    = 1;
	                    }else{
	                    	$isHasStock    = 2;
	                    }
	                    $isHasLocation = 1;
	                    $location       = $positionInfos[0]['pName'];
	                    $data[] = array(
	    					'sku'  		  => $info['sku'],
	                        'isHasStock'  => $isHasStock,
	                        'isHasLocation'=>$isHasLocation,
	    					'whId' 		  => $whId,
	    					'location' 	  => $location,
	    					'storageTime' => $storageTime
					    );
					    $store_skus[$info['sku']][$pos['storeId']] = $location;
	                }
					//}
				}else{
	                $data[] = array(
	    					'sku'  		  => $info['sku'],
	                        'isHasStock'  => $isHasStock,
	                        'isHasLocation'=>$isHasLocation,
	    					'whId' 		  => $whId,
	    					'location' 	  => $location,
	    					'storageTime' => $storageTime 
					    );
				}
			}
		}		
		return array('totalNum'=>$totalNum,'skuInfo'=>$data);
	}
	
	//获取sku出库记录
    function act_getOutRecordsBySku(){
		$sku       = isset($_GET['sku']) ? trim($_GET['sku']) : '';
		$startTime = isset($_GET['startTime']) ? trim($_GET['startTime']) : '';
		$endTime   = isset($_GET['endTime']) ? trim($_GET['endTime']) : '';
		
		if(empty($sku) || empty($startTime) || empty($endTime)){   //参数不完整
            self::$errCode = 101;
            self::$errMsg = '参数信息不完整';
            return false;
        }
		
		$totalNum = 0;
		$infos 	  = WhIoRecordsModel::getTNameList("wh_iorecords", "*", "where createdTime>={$startTime} and createdTime<={$endTime} and $sku='{$sku}' and ioType=1");
		if($info){
			if(!empty($infos)){
				foreach($infos as $info){
					$totalNum += $info['amount'];
				}
			}
			return $totalNum;
		}else{
			self::$errCode = WhIoRecordsModel::$errCode;
            self::$errMsg  = WhIoRecordsModel::$errMsg;
            return false;
		}
	}
	
	//获取发货单id
    function act_getWhOrderId(){
		$recordNumber   = isset($_GET['recordNumber']) ? trim($_GET['recordNumber']) : '';
		$accountAccount = isset($_GET['accountAccount']) ? trim($_GET['accountAccount']) : '';
		
		if(empty($recordNumber) || empty($accountAccount)){   //参数不完整
            self::$errCode = 101;
            self::$errMsg = '参数信息不完整';
            return false;
        }
		$accountId = 0;
		$salesaccountlist = CommonModel::getSalesaccountList();     //销售账号
		foreach($salesaccountlist as $list){
			if($list['account']==$accountAccount){
				$accountId = $list['id'];
				break;
			}
		}
		
		$data      = array();
		$orderInfo = OmAvailableModel::getTNameList("wh_shipping_order", "id", "where recordNumber='{$recordNumber}' and accountId='{$accountId}'");
		if($orderInfo){
			if(!empty($orderInfo)){
				$scanTime         = OmAvailableModel::getTNameList("wh_order_review_records", "scanTime", "where shipOrderId={$orderInfo[0]['id']}");
				$data['orderId']  = $orderInfo[0]['id'];
				if(!empty($scanTime)){
					$data['scanTime'] = date('Y/m/d',$scanTime[0]['scanTime']);
				}else{
					$data['scanTime'] = '';
				}
				return $data;
			}else{
				return '';
			}
		}else{
			self::$errCode = OmAvailableModel::$errCode;
            self::$errMsg  = OmAvailableModel::$errMsg;
            return false;
		}
	}
    
    /**
     * WhOpenApiAct::act_getWaitShelfNum()
     * 获取料号等待上架数量
     * @return void
     */
    function act_getWaitShelfNum(){
        $sku    =   isset($_GET['sku']) ? trim($_GET['sku']) : '';
        $sku    =   addslashes($sku);
        if($sku){
            $num    =   packageCheckModel::getSkuWaitShelfNum($sku);
        }else{
            $num    =   0;
        }
        $res['num']     =   $num;
        return $res;
    }
}
?>
