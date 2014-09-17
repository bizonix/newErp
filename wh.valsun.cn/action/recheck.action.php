<?php

/** 
 * @author 发货单复核
 * 
 */
class RecheckAct
{
    public static $errCode =0;
    public static $errMsg = '';

    /**
     * 构造函数
     */
    function __construct (){
    	
    }
    
    /*
     * 获取复核sku信息列表
     */
    public function act_getSkuList(){
        $orderid = isset($_REQUEST['orderid']) ? intval($_REQUEST['orderid']) : 0;
        if(empty($orderid)){    
            self::$errCode = 0;
            self::$errMsg = '请输入订单号!';
            return ;
        }
        
        $po_obj = new PackingOrderModel();
        $orderinfo = $po_obj->getOrderInfoById($orderid);
        if(empty($orderinfo)){
            self::$errCode = 0;
            self::$errMsg = '该发货单不存在!';
            return ;
        }
        
        if($orderinfo['orderStatus'] != PKS_WIQC){  //该订单不在待复核状态
            self::$errCode = 0;
            self::$errMsg ='该发货单不在待复核组！';
            return ;
        }
        
        $sod_obj = new ShipingOrderDetailModel();
        $skulist = $sod_obj->getSkuListByOrderId($orderid);
        if(count($skulist) == 0){   //没有料号
            self::$errCode = 0;
            self::$errMsg = '该订单下没有料号信息，请及时反馈!';
            return ;
        }
        
        $rr_obj = new  ReviewRecordsModel();
        $scan_rocords = $rr_obj->getRiewRecordsByOrderid($orderid);
        foreach ($scan_rocords as $sval){
            if(array_key_exists($sval['shipOrderdetailId'], $skulist)){
                unset($skulist[$sval['shipOrderdetailId']]);
            }
        }
        
        self::$errCode = 1;
        self::$errMsg ='OK';
        return $skulist;
    }
    
    /*
     * 复核信息提交
     */
    public function act_recheckInfoSubmit(){
        $orderid = isset($_POST['orderid']) ? intval($_POST['orderid']) : 0;
        if(empty($orderid)){
            self::$errCode = 0;
            self::$errMsg = '请输入发货单信息！';
            return ;
        }
        $sku = isset($_POST['sku']) ? trim($_POST['sku']) : 0;
        $sku = get_goodsSn($sku);
		if(empty($sku)){
            self::$errCode = 0;
            self::$errMsg = '请输入sku';
            return ;
        }
        $num = isset($_POST['num']) ? intval($_POST['num']) : 0;
        if ($num<1) {
        	self::$errCode = 0;
        	self::$errMsg = '请输入正确的数量';
        	return ;
        }
        
        $po_obj = new PackingOrderModel();
        $orderinfo = $po_obj->getOrderInfoById($orderid);
        if(empty($orderinfo)){
            self::$errCode = 0;
            self::$errMsg = '订单不存在！';
            return ;
        }
        if($orderinfo['orderStatus'] != PKS_WIQC){  //该订单不在待复核状态
            self::$errCode = 0;
            self::$errMsg ='该发货单不在待复核组！';
            return ;
        }
        
        $sod_obj = new ShipingOrderDetailModel();
        $skulist = $sod_obj->getSkuListByOrderId($orderid);
        if(count($skulist) == 0){   //没有料号
            self::$errCode = 0;
            self::$errMsg = '该订单下没有料号信息，请及时反馈!';
            return ;
        }
        
        $rr_obj = new  ReviewRecordsModel();
        $scan_rocords = $rr_obj->getRiewRecordsByOrderid($orderid);
        
        $scanskus = array();
        foreach ($scan_rocords as $sval){
            if(array_key_exists($sval['shipOrderdetailId'], $skulist)){
            	$scanskus[] = $sval['sku'];
                unset($skulist[$sval['shipOrderdetailId']]);
            }
        }
        
       if(in_array($sku,$scanskus)){
            self::$errCode = 0;
            self::$errMsg = $sku.'已复核过了';
            return ;        	
        }
        
        $isfound = FALSE;
        $numcorrect = true;
        foreach ($skulist as $skuval){
            if($skuval['sku'] == $sku){
                $isfound = $skuval;
                if($skuval['amount'] != $num){
                	$numcorrect = false;
                }
                break;
            }
        }
                
        if($isfound === FALSE){  //没找到对应的料号信息
            self::$errCode = 0;
            self::$errMsg = '料号['.$sku.']不存在';
            return ;
        }
        
        if($numcorrect == false){
            self::$errCode = 0;
            self::$errMsg = '复核数量与分拣数不符';
            return ;        	
        }        
             
        $data = array();
        $data['orderid'] = $orderid;        //单号
        $data['detailid'] = $isfound['id']; //detailid号
        $data['sku'] = $sku;    //sku
        $data['amount'] = $num; //复核数量
        $data['totalNums'] = $isfound['amount'];    //料号原始总数
        $data['userid'] = $_SESSION['userId'];      //复核扫描用户id
        $data['storeId'] = 1;               //仓库id
        $islast = 0;
        if (count($skulist) == 1) {    //当前为最后一个复核sku
        	$data['islast'] = TRUE;
        	$islast = 1;
        }else {
            $data['islast'] = FALSE;
        }
        
        $result = $rr_obj->recordReviewInfo($data);
        if($result == FALSE){
            self::$errCode = 0;
            self::$errMsg = '复核失败，复核记录插入失败';
            return ;
        } else {
            self::$errMsg = '成功!';
            if ($islast) {  //最后一个复核
				WhPushModel::pushOrderStatus($orderid,'PKS_WWEIGHING',$_SESSION['userId'],time());        //状态推送
				//更新状态为待包装称重
				$data = array(
		    		'orderStatus' => PKS_WWEIGHING
		    	);
		    	WhShippingOrderModel::update($data, "id='".$orderinfo['shipOrderId']."'");
		    	
            	self::$errCode = 2;
            	return ;
            } else {
                self::$errCode = 1;
                unset($skulist[$isfound['id']]);
                return $skulist;
            }
        }
    }
    
    /**
     * 快递复核标记为发货异常
     * @return boolean
     * @author czq
     */
    public function act_signUnusual(){
    	$shipOrderId = isset($_POST['shipOrderId']) ? intval($_POST['shipOrderId']) : 0;
    	if(empty($shipOrderId)){
    		self::$errCode = 0;
    		self::$errMsg = '请输入发货单信息！';
    		return ;
    	}
    	
    	$shipOrder = WhShippingOrderModel::find(' id='.$shipOrderId.' AND is_delete = 0','id');
    	if(!$shipOrder){
    		self::$errCode = 0;
    		self::$errMsg = '发货单'.$shipOrder.'不存在！';
    		return;
    	}
    	WhShippingOrderModel::begin();
    	//更新状态为 发货单异常
    	if(!WhShippingOrderModel::update(array('orderStatus'=>PKS_UNUSUAL_SHIPPING_INVOICE),$shipOrderId)){
    		self::$errCode = 0;
    		self::$errMsg = '发货单'.$shipOrder.'不存在！';
    		WhShippingOrderModel::rollback();
    		return false;
    	}
    	self::$errCode = 1;
    	self::$errMsg  = '发货单标记异常成功';
    	WhShippingOrderModel::commit();
    	return true;
    }
}

?>