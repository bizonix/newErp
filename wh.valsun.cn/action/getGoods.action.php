<?php

/** 
 * 仓库配货功能
 * @author 涂兴隆
 */
class GetGoodsAct
{
    public static $errCode=0;   //错误码
    public static $errMsg='';   //错误消息
    public static $data = NULL;       //附加信息

    /**
     * 构造函数
     */
    function __construct ()
    {
    	
    }
    
    /*
     * 快递配货列表(快递)
     */
    public function act_getSkuListEX(){
        /*
         * 权限验证。。。
         */
        $this->getSkuList(1);
        return self::$data;
    }
    
    /*
     * 配货列表(小包)
    */
    public function act_getSkuListSm(){
        /*
         * 权限验证。。。
        */
        $this->getSkuList(2);
        return self::$data;
    }
    
    /*
     * 快递配货列表(国内)
     */
    public function act_getSkuListInland(){
        /*
         * 权限验证。。。
        */
        $this->getSkuList(3);
        return self::$data;
    }
    
    /*
     * 获得某个发货单/配货单SKU列表功能
     * $ordertype 1快递 2非快递
     * 成功返回true 失败返回false
     */
    private function getSkuList($ordertype){
        $orderid = isset($_POST['orderid']) ? abs(intval($_POST['orderid'])) : 0;
        if(empty($orderid)){    //没有传id过来
            self::$errCode = 0;
            self::$errMsg = '没有导入id';
            return FALSE;
        }
        
        $packingorder_obj = new PackingOrderModel();
        $orderinfo = $packingorder_obj->getOrderInfoById($orderid);
        if(empty($orderinfo)){  //没有找到订单信息
            self::$errCode=0;
            self::$errMsg='发货单不存在！';
            return FALSE;
        }
        
        if($orderinfo['orderStatus'] != PKS_WGETGOODS){ //该单不处于待配货状态
            self::$errCode = 0;
            self::$errMsg = '该订单不在待配货状态!';
            return FALSE;
        }
        
        /* 验证改订单的类型是否符合要求 */
        if($ordertype == 1){    //为快递单
            if (!ShipingTypeModel::isExpressShiping($orderinfo['transportId'])) {
                self::$errCode = 0;
                self::$errMsg = '该订单为非快递单！';
                return FALSE;
            }
        } elseif ($ordertype == 2) {   //为非快递单
            if (!ShipingTypeModel::isSmallpressShiping($orderinfo['transportId'])) {
                self::$errCode = 0;
                self::$errMsg = '该订单为非小包订单！';
                return FALSE;
            }
        } elseif ($ordertype == 3){
            if (!ShipingTypeModel::isInlandShiping($orderinfo['transportId'])) {
                self::$errCode = 0;
                self::$errMsg = '该订单为非国内快递订单！';
                return FALSE;
            }
        }
        
        $sod_obj = new ShipingOrderDetailModel();
        $skulist = $sod_obj->getSkuListInOneOrder($orderid);
        
        /***添加返回料号库存 Start add by wangminwei 2014-03-07***/
        if($ordertype == 1){
            $sign       = 0;
            $skulists    = array();
            foreach($skulist as $k => $v){
                $sku        = $v['sku'];
                $amount     = $v['amount'];//配货数量
                $posId      = $v['positionId'];//仓位ID
                $pName      = $v['pName'];//仓位名称
                $storeId    = $v['storeId'];//仓库ID,默认1:深圳赛维
                $skuInfo    = getSkuInfoBySku($sku);//获取料号信息
                $skuId      = $skuInfo['id'];//料号ID
                $stockQty   = get_SkuStockQty($skuId, $posId, $storeId);//库存数量
                $skulists[$sign]['sku']          = $sku;
                $skulists[$sign]['amount']       = $amount;
                $skulists[$sign]['pName']        = $pName;
                $skulists[$sign]['positionId']   = $posId;
                $skulists[$sign]['stockqty']     = $stockQty;
                $sign++;
            }
			$skulist = $skulists;
        }
        /***添加返回料号库存 End ***/
        self::$errCode = 1;
        self::$errMsg = '拉取成功!';
        self::$data = $skulist;
        return  TRUE;
    }
    
    /*
     * 快递发货单配货提交
     */
    public function act_scanSubmitEx(){
        /*
         * 这儿要做权限控制
         */
        $result = $this->handelScanSubmit(1);
        return self::$data;
    }
    
    /*
     * 小包发货单配货提交
     */
    public function act_scanSubmitSm(){
        /*
         * 这儿要做权限控制
        */
        $result = $this->handelScanSubmit(2);
        return self::$data;
    }
    
    /*
     * 小包发货单配货提交
    */
    public function act_scanSubmitInland(){
        /*
         * 这儿要做权限控制
        */
        $result = $this->handelScanSubmit(3);
        return self::$data;
    }
    
    /*
     * 处理发货单配货请求
     * $ordertype  订单类型 快递订单为1 小包订单为2
     * 成功为true 失败为false
     */
    private function handelScanSubmit($ordertype){
        
        $sku = isset($_POST['sku']) ? trim($_POST['sku']) : 0;  //sku
		$sku = get_goodsSn($sku);
        if(empty($sku)){
            self::$errCode = 0 ;
            self::$errMsg = '请输入sku';
            return FALSE;
        }
        
        $orderid = isset($_POST['orderid']) ? abs(intval(($_POST['orderid']))) : 0;     //单号
        if(empty($orderid)){
            self::$errCode = 0;
            self::$errMsg = '请输入单号!';
            return FALSE;
        }
        
        $num = isset($_POST['num']) ? intval($_POST['num']) : 0;        //数量
        if( !(is_int($num) && $num>0) ){
            self::$errCode = 0;
            self::$errMsg = '请输入正确的数量!';
            return FALSE;
        }
		
		$pName = isset($_POST['pname']) ? trim($_POST['pname']) : '';        //仓位
        if(empty($pName)){
            self::$errCode = 0 ;
            self::$errMsg = '仓位有误，请联系it';
            return FALSE;
        }
		
        $pcko_obj = new PackingOrderModel();
        $orderinfo = $pcko_obj->getOrderInfoById($orderid, ' and orderStatus='.PKS_WGETGOODS);      //发货单完整信息
        if(empty($orderinfo)){  //订单号不存在
            self::$errCode = 0 ;
            self::$errMsg = '订单号不存在！';
            return FALSE;
        }


        /* 验证该订单的类型是否符合要求 */
        if($ordertype == 1){    //为快递单
            if (!ShipingTypeModel::isExpressShiping($orderinfo['transportId'])) {
            	self::$errCode = 0;
            	self::$errMsg = '该订单为非快递单！';
            	return FALSE;
            }
        }elseif ($ordertype == 2){   //为非快递单
            if (!ShipingTypeModel::isSmallpressShiping($orderinfo['transportId'])) {
            	self::$errCode = 0;
            	self::$errMsg = '该订单非小包订单！';
            	return FALSE;
            }
        } elseif ($ordertype == 3){
            if (!ShipingTypeModel::isInlandShiping($orderinfo['transportId'])) {
                self::$errCode = 0;
                self::$errMsg = '该订单非国内快递订单！';
                return FALSE;
            }
        }
        
        /***添加料号、配货数量验证 start ***/
        if($ordertype == 1){
            $sod_obj = new ShipingOrderDetailModel();
            $rtnNum  = $sod_obj->checkOrderSku($orderid, $sku);
            if($rtnNum == 0){
                self::$errCode  = 20;
                self::$errMsg   = '发货单号不存在料号['.$sku.']';
                return false;
            }else{
                $rtnNum  = $sod_obj->checkSkuPickRecord($orderid, $sku, $pName);
                if($rtnNum != 0){
                    self::$errCode  = 20;
                    self::$errMsg   = '料号['.$sku.']已配货';
                    return false;
                }else{
                    $actualNum  = $sod_obj->checkSkuQty($orderid, $sku);
                    $diffNum    = $num - $actualNum;
                    if($diffNum > 0){
                        self::$errCode  = 20;
                        self::$errMsg   = '料号['.$sku.']需配货'.$actualNum.'件现配'.$num.'件多配'.$diffNum.'件';
                        return false;
                    }else if($diffNum < 0){
                        self::$errCode  = 20;
                        self::$errMsg   = '料号['.$sku.']需配货'.$actualNum.'件现配'.$num.'件少配-'.$diffNum.'件';
                        return false;
                    }else{}
                }
            }
        }
        /***添加料号、配货数量验证 end ***/
        $sod_obj = new ShipingOrderDetailModel();
        $skulist = $sod_obj->getSkuListByOrderId($orderid);  //该发货单下的全部sku列表
        $sku_scaned = $sod_obj->getSkuHavedone($orderid);           //该订单下的已配货的sku列表
        foreach ($sku_scaned as $skval){        //去掉已配货的
            if(array_key_exists($skval['shipOrderdetailId'], $skulist)){
                unset($skulist[$skval['shipOrderdetailId']]);
            }
        }
        
        $matched = null;
        foreach ($skulist as $val){
            if( ($val['amount'] == $num) && ($val['sku'] == $sku) ){ //找到匹配的
                $matched = $val;
                break;
            }
        }
        $islast = FALSE;
        if(count($skulist) == 1){   //为最后一个配货料号
            $islast = TRUE;
        }
        
        if(empty($matched)){    //没找到数量匹配的
            self::$errCode = 0;
            self::$errMsg = 'sku数量不对！';
            return FALSE;
        }
        
        //删除库存 插入配货记录
        $data = array(
            	'num'=>$num,
                'sku'=>$sku,
				'pName'=>$pName,
                'orderid'=>$orderid,
                'detailid'=>$matched['id'],
                'amount'=>$num,
                'totalnum'=>$matched['amount'],
                'userid'=>$_SESSION['userId'],
                'islast'=>$islast,
                'orderTypeId'=>$orderinfo['orderTypeId'],
                'shiptype'=>$ordertype
            );
        $dbresult = $sod_obj->recordDataToSystem($data);
        
        if(!$dbresult){ //插入数据失败
            self::$errCode = 0;
            self::$errMsg = '配货失败，请重试！';
            return FALSE;
        }
        
        if($islast){    //最后一个配货完成
// 			WhPushModel::pushOrderStatus($orderid,'STATESHIPPED_PENDREVIEW',$_SESSION['userId'],time());        //状态推送
            self::$errCode = 2;
            self::$errMsg = '配货成功!';
            return TRUE;
        } else {
            $skulist = $sod_obj->getSkuListInOneOrder($orderid);
            $sign       = 0;
            $skulists    = array();
            foreach($skulist as $k => $v){
                $sku        = $v['sku'];
                $amount     = $v['amount'];//配货数量
                $posId      = $v['positionId'];//仓位ID
                $pName      = $v['pName'];//仓位名称
                $storeId    = $v['storeId'];//仓库ID,默认1:深圳赛维
                $skuInfo    = getSkuInfoBySku($sku);//获取料号信息
                $skuId      = $skuInfo['id'];//料号ID
                $stockQty   = get_SkuStockQty($skuId, $posId, $storeId);//库存数量
                $skulists[$sign]['sku']          = $sku;
                $skulists[$sign]['amount']       = $amount;
                $skulists[$sign]['pName']        = $pName;
                $skulists[$sign]['positionId']   = $posId;
                $skulists[$sign]['stockqty']     = $stockQty;
                $sign++;
            }
            $skulist = $skulists;
            self::$errCode = 1;
            self::$errMsg = '配货成功!';
            self::$data = $skulist;
            return TRUE;
        }
    }

    //验证发货单号是否存在料号、验证发货单号是否存在料号
    public function act_checkOrderSku(){
        $orderid = isset($_POST['orderid']) ? trim($_POST['orderid']) : '';
        $sku     = isset($_POST['sku']) ? trim($_POST['sku']) : '';
        $pname   = isset($_POST['pname']) ? trim($_POST['name']) : '';
        $sod_obj = new ShipingOrderDetailModel();
        $rtnNum  = $sod_obj->checkOrderSku($orderid, $sku);
        if($rtnNum == 0){
            self::$errCode  = 1;
            self::$errMsg   = '发货单号不存在料号['.$sku.']';
        }else{
            $rtnNum  = $sod_obj->checkSkuPickRecord($orderid, $sku, $pname);
            if($rtnNum != 0){
                self::$errCode  = 2;
                self::$errMsg   = '料号['.$sku.']已配货';
            }else{
                self::$errCode  = 0;
                self::$errMsg   = '料号['.$sku.']验证通过';
            }
        }
        return true;
    }

}

?>