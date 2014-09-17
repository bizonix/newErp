<?php
/*
 * 海外仓订单运费计算
 */
class OwShippingHandleView extends BaseView {
    
    /*
     * 构造函数
     */
    function __construct() {
        parent::__construct();
    }
    
    /*
     * 重新计算运输方式
     */
    public function view_reculculateShippingWay(){
        $ids    = isset($_GET['ids']) ? trim($_GET['ids']) : '';
        $ids    = explode(',', $ids);
        $ids    = array_map('intval', $ids);
        $succesResult   = array();                                                  //存储处理成功的结果集
        $failureResult  = array();                                                  //处理存储失败的结果集
        $owOrderMG      = new OwOrderManageModel();
        $owShipfeeCul   = new OwShippingWayDesisionModel();
        $oderIndex      = new OrderindexAct();
        $exApply        = new ExpressLabelApplyModel();
        foreach ($ids as $orderId){
            $orderInfo  = $owOrderMG->getOrderInfoById($orderId, array('id', 'recordNumber', 'calcWeight'));
            if (FALSE == $orderInfo) {
            	$failureResult[] = array('oderid'=>$orderId, 'recordNumber'=>'', 'calcWeight'=>0, 'errMsg'=>'订单不存在');
            	continue;
            }
            
            if (0 == $orderInfo['calcWeight']){                                     //重量未0 不予打印
                $failureResult[] = array('oderid'=>$orderId,
                        'recordNumber'=>$orderInfo['recordNumber'],
                        'calcWeight'=>0,
                        'errMsg'=>'订单重量为0! 请确认重量!'
                );
                continue;
            }
            
            $userInfo   = $owOrderMG->getBuyerInfoById($orderId);
            if (FALSE == $userInfo) {
                $failureResult[] = array('oderid'=>$orderId,
                        'recordNumber'=>$orderInfo['recordNumber'],
                        'calcWeight'=>$orderInfo['calcWeight'],
                        'errMsg'=>'不能获取买家信息!',
                );
                continue;
            }
            
            $shipInfo   = $owOrderMG->getShippingInfo($orderId);
            if ($shipInfo) {
            	if ($shipInfo['isCanceled']==0){                                   //之前已经申请过跟踪号 如果未取消则不予重复申请
            	    $failureResult[] = array('oderid'=>$orderId, 
            	            'recordNumber'=>$orderInfo['recordNumber'], 
            	            'calcWeight'=>$orderInfo['calcWeight'],
            	            'shippingWay'=>$shipInfo['shippingWay'], 
            	            'errMsg'=>'原有跟踪号未取消!',
            	            'trackNumber'=>$shipInfo['trackNumber']
            	    );
            	    continue;
            	}
            }
            
            $skuList    = $oderIndex->act_getRealskulist($orderId);
            $outSide    = $owShipfeeCul->culPackageLWH($skuList);
            $zone       = $exApply->getZoneCode($userInfo['zipCode']);
            
            $shipingWay = $owShipfeeCul->chooseShippingWay($skuList, $orderInfo['calcWeight'], $outSide, $zone);
            if (FALSE == $shipingWay) {
                $failureResult[] = array('oderid'=>$orderId,
                        'recordNumber'=>$orderInfo['recordNumber'],
                        'calcWeight'=>$orderInfo['calcWeight'],
                        'errMsg'=>'未找到合适的运输方式!',
                        'trackNumber'=>''
                );
                continue;
            } else {                                                                                //执行成功 更新运输方式
                $transId    = $exApply->reflectCodeToId($shipingWay['shippingCode']);
                $owOrderMG->updateFeildData(array('transportId'=>$transId), $orderId);
                $owOrderMG->changeOrderStatus(911, 916, $orderId);                                  //移动到待打印
                $succesResult[] = array('oderid'=>$orderId,
                        'recordNumber'=>$orderInfo['recordNumber'],
                        'calcWeight'=>$orderInfo['calcWeight'],
                        'errMsg'=>'成功!',
                        'trackNumber'=>$shipingWay['shippingCode']
                );
                continue;
            }
        }
//         print_r($failureResult);
//         print_r($succesResult);
        $this->smarty->assign('success', $succesResult);
        $this->smarty->assign('failure', $failureResult);
        $this->smarty->display('owReculShipping.htm');
    }
}
