<?php

/** 
 * @author 涂兴隆
 * 打印操作
 */
class printAct
{

    public static $errCode = 0;
    public static $errMsg = '';

    /**
     * 构造函数
     */
    function __construct ()
    {
    	
    }
    
    /*
     * 申请打印
     */
    public function act_addPrintLists(){
        $orderids = isset($_GET['orderids']) ? $_GET['orderids'] : '';
		$storeId  = isset($_GET['storeId']) ? $_GET['storeId'] : 1;
        if (empty($orderids)) {
            self::$errCode = 0;
            self::$errMsg = '请选择发货单号!';
            return;
        }
        
        $pm_obj = new printModel();
        if($pm_obj->insertPrintGroup($orderids,$storeId)) {
			self::$errCode = 200;
			self::$errMsg = '申请发货单成功!';
			return true;
        }else{
			self::$errCode = 0;
            self::$errMsg = '该单号不存在！';
            return false;
		}
    }
	
	/*
     * 批量申请打印
     */
    public function act_addBatchPrintLists(){
		$appnum = intval(trim($_POST['appnum']));
		$num 	= $appnum>0 ? $appnum:2000;
		$storeId = isset($_POST['storeId']) ? trim($_POST['storeId']) : 1; 
        $where  = " and po.orderStatus=400 and po.storeId={$storeId}";                                                                      //订单状态
        $ordertimestart = isset($_POST['ordertimestart']) ? trim($_POST['ordertimestart']) : '';                       //下单日期 开始
        if ($ordertimestart != 0) {           //开始时间
            $ordertimestart_int = strtotime($ordertimestart);
            $where .= " and po.createdTime >= $ordertimestart_int";
        }
        $ordertimeend = isset($_POST['ordertimeend']) ? trim($_POST['ordertimeend']) : '';                             //下单日期 结束
        if ($ordertimeend != 0) {             //结束时间
            $ordertimeend_int = strtotime($ordertimeend);
            //$ordertimeend_int += 86400;
            $where .= " and po.createdTime < $ordertimeend_int";
        }
        $goodsouttimestart = isset($_POST['goodsouttimestart']) ? trim($_POST['goodsouttimestart']) : '';              //出库日期 开始
        if ($goodsouttimestart != 0) {        //出库开始时间
            $goodsouttimestart_int = strtotime($goodsouttimestart);
            $where .= " and po.weighTime >= $goodsouttimestart";
        }
        $goodsouttimeend = isset($_POST['goodsouttimeend']) ? trim($_POST['goodsouttimeend']) : '';                    //出库日期 结束
        if ($goodsouttimeend != 0) {          //出库结束时间
            $goodsouttimeend_int = strtotime($goodsouttimeend);
            $goodsouttimeend += 86400;
            $where .= " and po.weighTime < $goodsouttimeend";
        }
        $isNote = intval($_POST['isNote']);
        switch ($isNote){
            case 1: //有留言
                $where .= ' and po.isNote=1';
                break;
            case 2: //没留言
                $where .= ' and po.isNote=0';
                break;
        }
		$orderTypeId = intval($_POST['orderTypeId']);
        switch ($orderTypeId){
            case 1: //发货单
                $where .= ' and po.orderTypeId=1';
                break;
            case 2: //配货单
                $where .= ' and po.orderTypeId=2';
                break;
        }
        $shiptype = trim($_POST['shiptype']);                                                                       //运输方式
        if ($shiptype != 0) {                 //运输方式
			if($shiptype==200){
				$nshiptype = "1,2,3";
			}else if($shiptype==200){
				$nshiptype = "6,10,52,53";
			}else{
				$nshiptype = $shiptype;
			}
			$where .= " and po.transportId in($nshiptype)";
        }else{
			if(empty($_SESSION['shippingList'])){
				$where = " and po.transportId =''";
			}else{
				$nshiptype  = implode(',',$_SESSION['shippingList']);
				$where .= " and po.transportId in($nshiptype)";
			}
		}
        $client_name = trim($_POST['clientname']);
        if ($client_name != '') {   //按客户id搜索
        	$where .= " and po.platformUsername='$client_name'";
        }
        $salesaccount= trim($_POST['acc']);
        if ($salesaccount != '') {   
            $where .= " and po.accountId='$salesaccount'";
        }else{
			if(empty($_SESSION['accountList'])){
				$where = " and po.accountId =''";
			}else{
				$accountInfo  = implode(',',$_SESSION['accountList']);
				$where .= " and po.accountId in($accountInfo)";
			}
		}
        $hunhe = intval($_POST['hunhe']);
        switch ($hunhe){
            case 2: //单料号
                $where .= ' and po.orderAttributes='.SOA_SINGLE;
                break;
            case 1: //多料号
                $where .= ' and po.orderAttributes='.SOA_MULTIY;
                break;
            case 3: //组合订单
                $where .= ' and po.orderAttributes='.SOA_COMBIN;
                break;
        }
        $platformName = trim($_POST['platformName']);
        if ($platformName != '') {                 //平台
            $where .= " and po.platformId= $platformName";
        }else{
			if(empty($_SESSION['platformList'])){
				$where .= " and po.platformId =''";
			}else{
				$platformInfo  = implode(',',$_SESSION['platformList']);
				$where .= " and po.platformId in($platformInfo)";
			}	
		}
		//print_r($where);exit;
		$packorder_obj = new PackingOrderModel(); 
		$billlist = $packorder_obj->getBillList($where.' group by po.id order by pd.pName limit '.$num); 
		if(empty($billlist)){
			self::$errCode = 0;
            self::$errMsg = '没有符合条件的订单!';
            return;
		}
		
		$orderids 	= '';
		$orderidArr = array();
		foreach($billlist as $list){
			$orderidArr[] = $list['id'];
		}
		$orderids = implode(',',$orderidArr);
		$pm_obj = new printModel();
        if($pm_obj->insertPrintGroup($orderids,$storeId)) {
			self::$errCode = 200;
			self::$errMsg = '申请打印成功!';
			return true;
        }else{
			self::$errCode = 0;
            self::$errMsg = '申请打印失败！';
            return false;
		}
	}
    
	/*
     * 标记为异常发货单
     */
    public function act_markUnusual(){
		$userId   = $_SESSION['userId'];
        $orderids = isset($_POST['orderids']) ? $_POST['orderids'] : '';
        if (empty($orderids)) {
            self::$errCode = 0;
            self::$errMsg = '请选择发货单号!';
            return;
        }
		$order_arr = explode(',',$orderids);
		
		OmAvailableModel::begin();
        $po_obj  = new PackingOrderModel();
        $qresult = $po_obj->changeStatusToUnusual($orderids);
        if($qresult) {
			foreach($order_arr as $order){
				$order_info = orderWeighingModel::selectOrderDetail($order);
				foreach($order_info as $o_info){
					$data     = array();
					$sku_info = InvRecordModel::getSkuInfo($o_info['sku']);
					$data = array(
						'sku' 	   		   => $o_info['sku'],
						'applicantId' 	   => $userId,
						'applicantionTime' => time(),
						'invReasonId'	   => 4,
						'invStatus'        => 0,
						'systemNums'  	   => $sku_info['actualStock'],
					);
					
					$insertid = WaitInventoryModel::insertRow($data);
					if(!$insertid){
						self::$errCode = 0;
						self::$errMsg = '标记异常发货单失败！';
						OmAvailableModel::rollback();
						return false;
					}
				}
			}
			self::$errCode = 200;
			self::$errMsg = '标记异常发货单成功!';
			OmAvailableModel::commit();
			return true;
        }else{
			self::$errCode = 0;
            self::$errMsg = '标记异常发货单失败！';
            return false;
		}
    }
	
	
	/*
     * 标记为异常发货单
     */
    public function act_markUnusual1(){
		$userId   = $_SESSION['userId'];
        $orderids = isset($_POST['orderids']) ? $_POST['orderids'] : '';
        if (empty($orderids)) {
            self::$errCode = 0;
            self::$errMsg = '请选择发货单号!';
            return;
        }
		if(!is_numeric($orderids)){
			$tracknumber = $orderids;
			$info = orderWeighingModel::selectOrderId($tracknumber);
			if(!$info){
				self::$errCode = 501;
				self::$errMsg = "此跟踪号不存在！";
				return false;
			}
			$orderids = $info[0]['shipOrderId'];
			
		}
		$order_arr = explode(',',$orderids);
		
		OmAvailableModel::begin();
        $po_obj  = new PackingOrderModel();
        $qresult = $po_obj->changeStatusToUnusual($orderids);
        if($qresult) {
			foreach($order_arr as $order){
				$order_info = orderWeighingModel::selectOrderDetail($order);
				foreach($order_info as $o_info){
					$data     = array();
					$sku_info = InvRecordModel::getSkuInfo($o_info['sku']);
					$data = array(
						'sku' 	   		   => $o_info['sku'],
						'applicantId' 	   => $userId,
						'applicantionTime' => time(),
						'invReasonId'	   => 4,
						'invStatus'        => 0,
						'systemNums'  	   => $sku_info['actualStock'],
					);
					
					$insertid = WaitInventoryModel::insertRow($data);
					if(!$insertid){
						self::$errCode = 0;
						self::$errMsg = '标记异常发货单失败！';
						OmAvailableModel::rollback();
						return false;
					}
				}
			}
			self::$errCode = 200;
			self::$errMsg = '标记异常发货单成功!';
			OmAvailableModel::commit();
			return true;
        }else{
			self::$errCode = 0;
            self::$errMsg = '标记异常发货单失败！';
            return false;
		}
    }
    
    /**
     * printAct::act_abnormalRestore()
     * 异常发货单恢复
     * @return
     */
    public function act_abnormalRestore(){
        $userId   = $_SESSION['userId'];
        $orderids = isset($_POST['orderids']) ? trim($_POST['orderids']) : '';
        if(empty($userId)){
            self::$errCode = 0;
            self::$errMsg = '请重新登录!';
            return false;  
        }
        if (empty($orderids)) {
            self::$errCode = 0;
            self::$errMsg  = '请选择发货单号!';
            return false;
        }
         $order_arr       = explode(',',$orderids);
        foreach($order_arr as $shipOrderId){
             $where = "where id={$shipOrderId}";
    		$order = orderPartionModel::selectOrder($where);
    		if(!$order){
    			self::$errCode = 0;
    			self::$errMsg  .= "此发货单'{$shipOrderId}'不存在！";
    			continue;
    		}else{
    		    $status        = PKS_WIQC;
    		    $update_result = WhShippingOrderModel::update_shipping_order_by_id("id = '{$shipOrderId}' and is_delete = 0","orderStatus = '{$status}'");
    	        if($update_result){
    	            self::$errMsg  .= "此发货单'{$shipOrderId}'状态还原为待复核状态！";
    	        }else{
    	            self::$errMsg  .= "此发货单'{$shipOrderId}'异常恢复失败！";
    	        }
        	}
            
        }
		self::$errCode = 200;
        self::$errMsg  .='操作完成';
        return true;
    }
}

?>