<?php
/*
 * 名称：OrderModifyAct
 * 功能：订单修改查看操作
 * 版本：v 1.0
 * 日期：2013/09/12
 * 作者：zyp
 *@modify by : linzhengxiang ,date : 20140603
 */
class OrderModifyAct extends CheckAct{
	
	public function __construct(){
		parent::__construct();
	}
    
    /**
     * 编辑订单发货信息
     */
    public function act_updateOrderMailInfo(){
    	
    }
    
	/**
     * 编辑订单运输方式
     */
    public function act_updateOrderShipInfo(){
        $order    = $_POST['order'];
        $id       = $_POST['id'];

        if(M('orderModify')->updateData($id,$order)){
            M('orderLog')->orderOperatorLog('noSQL','订单明细点击保存开始拦截流程',$id);
            $order = M('order')->getFullUnshippedOrderById(array($id));
            $order = $order[$id];
            unset($order['orderNote']);//去掉备注
            unset($order['orderTracknumber']);//去掉跟踪号
            unset($order['orderWarehouse']);//去掉仓库相关
            F('FormatOrder')->interceptOrder($order);
            self::$errMsg['200'] = '订单修改完毕,已经重新拦截,订单状态可能会发生改变';
        }else{
            self::$errMsg[105]    = '订单修改失败,请联系系统管理员';
        }

    }
    
	/**
     * 编辑订单客户联系方式
     */
    public function act_updateOrderUserContact(){
        $userInfo = $_POST['userInfo'];
        $id       = $_POST['id'];
        if(M('orderModify')->updateOrderUserContactById($id,$userInfo)){
            M('orderLog')->orderOperatorLog('noSQL','订单明细点击保存开始拦截流程',$id);
            $order = M('order')->getFullUnshippedOrderById(array($id));
            $order = $order[$id];
            unset($order['orderNote']);//去掉备注
            unset($order['orderTracknumber']);//去掉跟踪号
            unset($order['orderWarehouse']);//去掉仓库相关
            F('FormatOrder')->interceptOrder($order);
            self::$errMsg['200'] = '订单修改完毕,已经重新拦截,订单状态可能会发生改变';
        }else{
            self::$errMsg[105]    = '订单修改失败,请联系系统管理员';
        }
    }
    
	/**
     * 编辑订单金额和订单运费金额
     */
    public function act_updateOrderMoney(){
    	
    }
    
	/**
     * 编辑订单跟踪号包括添加跟踪号
     */
    public function act_updateOrderTrack(){
    	
    }
    
    /**
     * 添加订单详细  ps:添加订单明细的功能在orderAdd.model有实现
     */
    public function act_addOrderdetail(){
    	
    }
    
	/**
     * 编辑订单详细
     */
    public function act_updateOrderdetail(){

    }
    
	/**
     * 添加备注
     */
    public function act_addOrderNote(){
        $noteTypeForWh  = '';
        $omOrderId      = $_POST['id'];
        $content        = $_POST['orderNote'];
        $specialPack    = $_POST['specialPack'];
        $specialPick    = $_POST['specialPick'];
        if($specialPack){
            $noteTypeForWh = $specialPack;
        }
        if($specialPick){
            $noteTypeForWh .= ','.$specialPick;
        }
        $noteTypeForWh  = trim($noteTypeForWh,',');
        if(M('orderModify')->insertOrderNote($omOrderId, $content,$noteTypeForWh)){
            self::$errMsg[200]    = '添加订单备注成功';
        }else{
            self::$errMsg[102]    = '添加订单备注错误,请联系管理员';
        }
    }
    
    /**
     * 通过订单状态批量改变订单状态
     */
    public function act_updateOrderStatusByStatus(){
    	
    }
    
    /**
     * 标记已发货
     * @author yxd
     */
    public function act_setOperated(){
    	$orderids       = $_POST['orderids'];
    	$orderStatus    = $_POST['orderStatus'];
    	$orderType      = $_POST['orderType'];
    	$orderids       = explode(",", $orderids);
    	$orderStatus    = explode(",", $orderStatus);
    	$orderType      = explode(",", $orderType);
    	$addtime        = time();//添加时间
    	$addUser        = get_userid();//添加人
    	$msgerror       = "";
    	$msgsuccess     = '';
    	foreach($orderids as $key=>$value){
    		$data                 = array();
    		$data['omOrderId']    = $value;
    		$data['statusId']     = $orderStatus[$key];
    		$data['typeId']       = $orderType[$key];
    		$data['addTime']      = $addtime;
    		$data['userId']       = $addUser;
    		$exitdata             = M('orderOperated')->get_operateByAS($value,$orderStatus[$key],$orderType[$key]);
    		if(is_array($exitdata) && count($exitdata)>=1){
    			$msgerror    .= $value."已标记过，不要重复标记;\n";
    			continue;
    		}
    		if(!M('orderOperated')->insertData($data)){
    			self::$errMsg[123]    = $value."标记失败";
    			return false;
    		}else{
    			$msgsuccess    .= $value."标记成功;\n";
    		}
    	}
    	if(strlen($msgerror)>1){
    		self::$errMsg[124]    = $msgerror;
    	}else{
    		self::$errMsg[200]    = $msgsuccess;
    	}
    	  
    	return true;
    }

    /**
     * @param string $type
     * @return array
     * 获取订单所有的详情
     * 这个函数主要是ajax调用 增加type规则 返回尽量少的数据
     */
    public function act_getOrderList(){
        $id  = $_POST['id'];
        $status = $_POST['status'];
        $ids = array($id);
        F('order');
        $SendReplaceOrderList = M('Order')->getFullUnshippedOrderById($ids);
        $carrierList          = get_carrierList();
        $pmList               = get_materList();

        if(is_array($SendReplaceOrderList)){
            foreach($SendReplaceOrderList as $k=>$v){
                $SendReplaceOrderList[$k]['order']['platformIdTrue']  =  $SendReplaceOrderList[$k]['order']['platformId']; // todu
                $SendReplaceOrderList[$k]['order']['platformId']      =  get_platnamebyid($SendReplaceOrderList[$k]['order']['platformId']);
                $SendReplaceOrderList[$k]['order']['accountId']       =  get_accountnamebyid($SendReplaceOrderList[$k]['order']['accountId']);
                $SendReplaceOrderList[$k]['order']['orderStatus']     =  get_orderStatusName($SendReplaceOrderList[$k]['order']['orderStatus']);
                $SendReplaceOrderList[$k]['order']['orderType']       =  get_orderTypeName($SendReplaceOrderList[$k]['order']['orderType']);
                $SendReplaceOrderList[$k]['order']['ordersTime']      =  date('Y-m-d H:i:s',$SendReplaceOrderList[$k]['order']['ordersTime']);
                $SendReplaceOrderList[$k]['order']['marketTime']      =  empty($SendReplaceOrderList[$k]['order']['marketTime'])?'-':date('Y-m-d H:i:s',$SendReplaceOrderList[$k]['order']['marketTime']);
                $transportId                                          =  $SendReplaceOrderList[$k]['order']['transportId'];
                $orderDetail = $v['orderDetail'];
                foreach($orderDetail as $dkey=>$detail){
                    $sku    = $detail['orderDetail']['sku'];
                    $amount = $detail['orderDetail']['amount'];
                    $SendReplaceOrderList[$k]['orderDetail'][$dkey]['orderDetail']['weight'] = (M('InterfacePc')->getSkuWeight($sku))*$amount;
                }
                $transportOption                                      =  '';
                if(is_array($carrierList)){
                    $transportOption                                 .=  '<select id="changeTransport">';
                    $transportOption                                 .=  '<option value="0">--请选择--</option>';
                    foreach($carrierList as $list){
                        if($transportId == $list['id']){
                            $transportOption .=  '<option value="'.$list['id'].'" selected>'.$list['carrierNameCn'].'</option>';
                        }else{
                            $transportOption .=  '<option value="'.$list['id'].'">'.$list['carrierNameCn'].'</option>';
                        }
                    }
                    $transportOption .=  '</select>';
                }

                $pmId             =  $SendReplaceOrderList[$k]['order']['pmId'];
                $pmIdOption       =  '';
                if(is_array($pmList)){
                    $pmIdOption      .=  '<select id="pmId">';
                    foreach($pmList as $list){
                        if($pmId == $list['id']){
                            $pmIdOption .=  '<option value="'.$list['id'].'" selected>'.$list['pmName'].'</option>';
                        }else{
                            $pmIdOption .=  '<option value="'.$list['id'].'">'.$list['pmName'].'</option>';
                        }
                    }
                    $pmIdOption                                       .=  '</select>';
                }
                $SendReplaceOrderList[$k]['order']['pmIdOption']       =  $pmIdOption;
                $SendReplaceOrderList[$k]['order']['transportOption']  =  $transportOption;

            }

            /**
             * $status
             * 1 编辑买家信息 2 编辑地址信息 3 编辑运输信息 4 编辑发货信息 5编辑备注
             */
            switch($status){
                case 1: case 2:  //只保留order 和 userInfo
                    unset($SendReplaceOrderList[$id]['orderExtension']);
                    unset($SendReplaceOrderList[$id]['orderWarehouse']);
                    unset($SendReplaceOrderList[$id]['orderDetail']);
                    unset($SendReplaceOrderList[$id]['orderTracknumber']);
                break;
                case 3: //只保留order
                    unset($SendReplaceOrderList[$id]['orderExtension']);
                    unset($SendReplaceOrderList[$id]['orderWarehouse']);
                    unset($SendReplaceOrderList[$id]['orderDetail']);
                    unset($SendReplaceOrderList[$id]['orderTracknumber']);
                    unset($SendReplaceOrderList[$id]['orderUserInfo']);
                break;
                case 5:
                    //替换note的展示
                    $orderNoteArray = $SendReplaceOrderList[$id]['orderNote'];
                    if(is_array($orderNoteArray)){
                        foreach($orderNoteArray as $j=>$list){
                            $SendReplaceOrderList[$id]['orderNote'][$j]['userId']      = get_usernamebyid($list['userId']);
                            $SendReplaceOrderList[$id]['orderNote'][$j]['content']     = $list['content'];
                            $SendReplaceOrderList[$id]['orderNote'][$j]['createdTime'] = date('Y-m-d H:i:s',$list['createdTime']);
                            $noteTypeForWh                                                = $list['noteTypeForWh'];
                            if(strpos($noteTypeForWh,',')){
                                $SendReplaceOrderList[$id]['orderNote'][$j]['noteTypeForWh'] = '特殊配货+特殊包装';
                            }elseif($noteTypeForWh == 1){
                                $SendReplaceOrderList[$id]['orderNote'][$j]['noteTypeForWh'] = '特殊配货';
                            }elseif($noteTypeForWh == 2){
                                $SendReplaceOrderList[$id]['orderNote'][$j]['noteTypeForWh'] = '特殊包装';
                            }else{
                                $SendReplaceOrderList[$id]['orderNote'][$j]['noteTypeForWh'] = '未知类型';
                            }
                        }
                    }
                    unset($SendReplaceOrderList[$id]['orderExtension']);
                    unset($SendReplaceOrderList[$id]['orderWarehouse']);
                    unset($SendReplaceOrderList[$id]['orderDetail']);
                    unset($SendReplaceOrderList[$id]['orderTracknumber']);
                    unset($SendReplaceOrderList[$id]['orderUserInfo']);
                    unset($SendReplaceOrderList[$id]['order']);
                break;

            }

        }
        return $SendReplaceOrderList;
    }


    /**
     * @param $omOrderId
     * @return mixed
     * 根据ID获取订单的全部信息
     */
    public function act_getOrderListById($omOrderId){
        F('order');
        $conditions['order'] = array(
            'id'        => array('$e'=>$omOrderId),
            'is_delete' => array('$e'=>'0')
        );
        $OrderList = M('Order')->getOrderList($conditions);
        return $OrderList;
    }

    public function act_getOrderLogs(){
        $omOderId = $_POST['id'];
        $logList  = M('orderModify')->getOrderLogs($omOderId);
        if(is_array($logList)){

            $user_id_arr = array();

            if(!empty($GLOBALS['memc_obj'])){
                $user_id_arr = $GLOBALS['memc_obj']->get('get_username_by_id_new');
                if(empty($user_id_arr)){
                    $user_id_arr = array();
                }
            }

            foreach($logList as $k=>$v){
                if(!isset($user_id_arr[$logList[$k]['operatorId']])){
                    $username = get_usernamebyid($logList[$k]['operatorId']);
                    $user_id_arr[$logList[$k]['operatorId']] = $username;
                }else{
                    $username = $user_id_arr[$logList[$k]['operatorId']];
                }
                $user_id_arr[$logList[$k]['operatorId']] = $username;

                $logList[$k]['operatorId']  =  $username;
                $logList[$k]['operatorId']  =  ($logList[$k]['operatorId']);
                $logList[$k]['createdTime']  =  empty($logList[$k]['createdTime'])?'----':date('Y-m-d H:i:s',$logList[$k]['createdTime']);
            }

            $GLOBALS['memc_obj']->set('get_username_by_id_new', $user_id_arr, 36000);
        }
        return $logList;
    }

    /**
     * 更新平台ID，获取权限允许的平台帐号
     */
    public function act_changePlatformId(){
        F('order');
        $platformId         = $_POST['platformId'];
        $accountList        = array();
        $accountReturnList  = array();
        if(empty($platformId)){
            self::$errMsg['104'] = '未获取到平台ID';
        }else{
            $platAccount = get_userplatacountpower(get_userid());
            $accountList = $platAccount[$platformId];
            if(is_array($accountList)){
                foreach($accountList as $v){
                    $accountReturnList[$v] = get_accountnamebyid($v);
                }
            }
            self::$errMsg['200'] = 'ok';
        }
        return $accountReturnList;
    }

    public function act_changeOrderDetail(){
        $data               = $_POST;
        $skuList            = $_POST['sku'];
        $omOrderId          = $_POST['omOrderId'];
        $platformIdTrue     = $_POST['platformId'];
        $del                = $_POST['del'];
        $orderAttribute     = $_POST['orderAttribute'];
        $actualTotal        = $_POST['actualTotal'];
        $actualShipping     = $_POST['actualShipping'];
        $updateActualTotal  = 0;
        $updateOrderArray   = array();
        $orderDetail        = array();
        $orderDetailExtend  = array();
        $insertOrderDetail  = array();
        $num                = sizeof($skuList);
        $err                = false;
        $interceptOrder     = true;
        $errMsg             = '';
        for($i=0;$i<$num;$i++){
            if(empty($data['id'][$i])){
                $err     = true;
                $errMsg .= '传入ID错误';
            }else{
                $orderDetail[$i]['id']                      = $data['id'][$i];
                $orderDetail[$i]['omOrderId']               = $omOrderId;
                $orderDetailExtend[$i]['omOrderdetailId']   = $data['id'][$i];
                //$orderDetailExtend[$i]['omOrderId'] = $omOrderId;
            }

            if(empty($data['itemId'][$i])){
                $err     = true;
                $errMsg .= 'itemId不允许为空<br />';
            }else{
                $orderDetail[$i]['itemId'] = $data['itemId'][$i];
            }

            if(empty($data['recordNumber'][$i])){
                $err     = true;
                $errMsg .= 'Record No不允许为空<br />';
            }else{
                $orderDetail[$i]['recordNumber'] = $data['recordNumber'][$i];
            }

            if(empty($data['itemPrice'][$i])){
                $err     = true;
                $errMsg .= 'Price不允许为空<br />';
            }else{
                $orderDetail[$i]['itemPrice'] = $data['itemPrice'][$i];
            }

            if(empty($data['sku'][$i])){
                $err     = true;
                $errMsg .= 'Customer Label不允许为空<br />';
            }else{
                $orderDetail[$i]['sku'] = $data['sku'][$i];
            }

            if(empty($data['amount'][$i])){
                $err     = true;
                $errMsg .= 'Quantity不允许为空<br />';
            }else{
                $orderDetail[$i]['amount'] = $data['amount'][$i];
            }

            if(empty($data['shippingFee'][$i])){
                $err     = true;
                $errMsg .= 'Shipping Free不允许为空<br />';
            }else{
                $orderDetail[$i]['shippingFee'] = $data['shippingFee'][$i];
            }

            if(empty($data['itemTitle'][$i])){
                $err     = true;
                $errMsg .= 'Item Title不允许为空<br />';
            }else{
                $orderDetailExtend[$i]['itemTitle'] = addslashes($data['itemTitle'][$i]);
            }

        }

        if($err){
            self::$errMsg['104'] = $errMsg;
        }else{
            if(!empty($del)){   //如果传递过来的删除ID不为空 准备执行删除操作  dataType 1,2,3
                $del = trim($del);
                $delList = explode(',',$del);
                foreach($delList as $detailId){
                    if(!M('orderModify')->delOrderDetail($detailId)){
                        self::$errMsg['104'] = $detailId.'删除部分产品出错';
                        $interceptOrder      = false;
                    }
                }
            }

            //读取该订单以前的参数,一些没有值的参数从这里获取
            $oldOrderDetailFull = A('orderModify')->act_getOrderListById($omOrderId);
            $oldOrderDetail     = $oldOrderDetailFull[$omOrderId]['orderDetail'];
            sort($oldOrderDetail);
            $oldOrderExtension  = $orderDetail[0]['orderDetailExtension'];
            unset($oldOrderExtension['omOrderdetailId']);

            foreach($orderDetail as $k=>$details){
                $id = $details['id'];
                unset($details['id']);
                if($id != 'add'){
                    //执行更新操作
                    if(!M('orderModify')->updateOrderDetail($id,$details)){
                        self::$errMsg['104'] = '更新订单详情出错';
                        $interceptOrder      = false;
                    }
                    if(!M('orderModify')->updateOrderDetailExtend($platformIdTrue,$orderDetailExtend[$k])){
                        self::$errMsg['104'] = '更新订单详情扩展出错';
                        $interceptOrder      = false;
                    }
                }else{
                    //执行写入操作
                    // M('Base')->rollback();
                    $oldOrderExtension['itemTitle'] = $orderDetailExtend[$k]['itemTitle'];
                    $insertOrderDetail['orderDetail']          = $details;
                    $insertOrderDetail['orderDetailExtension'] = $oldOrderExtension;
                    if(!M('OrderAdd')->insertOrderDetailPerfect($insertOrderDetail)){
                        self::$errMsg['104'] = '插入订单详情和订单扩展出错';
                        $interceptOrder      = false;
                    }
                }
                $updateActualTotal += $details['itemPrice']*$details['amount'];
            }

            //更新订单的属性,是否是多料号 订单总价
            $update = false;
            if($orderAttribute == 1 && sizeof($orderDetail)>1){
                $update                             = true;
                $updateOrderArray['orderAttribute'] = 3;
            }
            $updateActualTotal = $updateActualTotal+$actualShipping;
            if($updateActualTotal != $actualTotal){
                $update                                = true;
                $updateOrderArray['actualTotal'] = $updateActualTotal;
            }
            if($update){
                if(!M('orderManage')->updateData($omOrderId,$updateOrderArray)){
                    self::$errMsg['104'] = '更新订单的价格出错';
                    $interceptOrder      = false;
                }
            }

            if($interceptOrder){
                //执行完毕开始走流程
                M('orderLog')->orderOperatorLog('noSQL','订单明细点击保存开始拦截流程',$omOrderId);
                $order = M('order')->getFullUnshippedOrderById(array($omOrderId));
                $order = $order[$omOrderId];
                unset($order['orderNote']);//去掉备注
                unset($order['orderTracknumber']);//去掉跟踪号
                unset($order['orderWarehouse']);//去掉仓库相关
                F('FormatOrder')->interceptOrder($order);
                self::$errMsg['200'] = '订单修改完毕,已经重新拦截,订单状态可能会发生改变';
            }
        }
    }
    
    /**
     * 提供给采购系统审核超大订单的方法
     */
    public function act_updateOrderAuditFromPh(){
        F('order');
        $returnFlag = false;
        $omOrderId = intval($_REQUEST['omOrderId']);//主订单号
        $omOrderdetailId = intval($_REQUEST['omOrderdetailId']);//订单详情号
        $auditUser = intval($_REQUEST['auditUser']);//审核人Id
        $auditStatus = intval($_REQUEST['auditStatus']);//审核状态，1为审核通过，2为拦截
        $note = $_REQUEST['note']?$_REQUEST['note']:'';//备注，拦截时为必填
        if($omOrderId <= 0){
            self::$errMsg['10089'] = get_promptmsg(10089);//omOrderId有误
            return false;
        }
        if($omOrderdetailId <= 0){
            self::$errMsg['10135'] = get_promptmsg(10135);//omOrderDetailId有误
            return false;
        }
        if($auditUser <= 0){
            self::$errMsg['10136'] = get_promptmsg(10136);//不存在该人
            return false;
        }
        if(!in_array($auditStatus, array(1,2))){
            self::$errMsg['10137'] = get_promptmsg(10137);//审核操作只能是1,或者2
            return false;
        }
        if($auditStatus == 2 && empty($note)){
            self::$errMsg['10138'] = get_promptmsg(10138);//omOrderDetailId有误
            return false;
        }
        $updateData = array();
        $updateData['auditUser'] = $auditUser;
        $updateData['auditStatus'] = $auditStatus;
        $updateData['auditTime'] = time();
        $updateData['note'] = $note;
        $whereData = array();
        $whereData['omOrderId'] = array('$e'=>$omOrderId);
        $whereData['omOrderdetailId'] = array('$e'=>$omOrderdetailId);
        if(M('OrderManage')->updateOrderAuditByWhereArr($updateData, $whereData)){
            $returnFlag = true;
            M('orderLog')->orderOperatorLog('noSQL','超大订单接口调用成功，更新数据'.json_encode($updateData),$omOrderId);
        }
        //这里要根据该订单是否已经全部审核完成了再次进行订单拦截
        $orderAuditList = M('Order')->getOrderAuditListById($omOrderId);
        $tmpFlag = true;//该订单下是否全部审核通过，默认为true,全部审核通过
        foreach($orderAuditList as $value){
            if($value['auditStatus'] != 1){
                $tmpFlag = false;//如果存在没有审核通过的项，则标记
                break;
            }
        }
        if($tmpFlag){//超大审核通过完成，自动添加已处理记录，并重新跑拦截
            M('orderLog')->orderOperatorLog('noSQL','该超大订单已全部确认通过',$omOrderId);
            $orderStatusId = M('StatusMenu')->getOrderStatusByStatusCode('ORDER_AUTOMATIC','id');//取得自动拦截订单status的id值
            $orderTypeId = M('StatusMenu')->getOrderStatusByStatusCode('ORDER_SUPER_LARGE_BLOCKED','id');//取得自动拦截订单status下超大订单拦截的的id值
            $orderOperObj = M('OrderOperated');
            $dataArr = array();
            $dataArr['omOrderId'] = $omOrderId;
            $dataArr['statusId'] = $orderStatusId;
            $dataArr['typeId'] = $orderTypeId;
            $dataArr['addTime'] = time();
            if($orderOperObj->insertData($dataArr)){
                M('orderLog')->orderOperatorLog('noSQL','该超大订单拦截标记已处理，重新跑拦截逻辑',$omOrderId);
                $orderData = M('Order')->getFullUnshippedOrderById(array($omOrderId));
                F('FormatOrder')->interceptOrder(array_shift($orderData));//重新跑拦截逻辑
            }
        }else{
            M('orderLog')->orderOperatorLog('noSQL','该超大订单没有全部确认通过，状态不变',$omOrderId);
        }
        //
        return $returnFlag;
    }
}
?>