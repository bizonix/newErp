<?php
/*
 * 修改备货单信息
 */
class ChangePreGoodsOrderAct {
    
    public static $errCode    = 0;
    public static $errMsg     = '';
    
    /*
     * 修改备货单状态
     */
    public function act_changeStatus(){
        $retrunData = array('code'=>0, 'msg'=>'');
        $orderId    = isset($_GET['orderId']) ? intval($_GET['orderId']) : NULL;
        $status     = isset($_GET['status'])  ? intval($_GET['status'])  : NULL;
        if (empty($orderId) || empty($status)) {
        	$retrunData['msg'] = '缺少参数!';
        	return $retrunData;
        }
        if (!PreGoodsOrdderManageModel::validateStatusCode($status)) {                  //不合法的订单状态
            $retrunData['msg'] = '不合法的订单状态!';
            return $retrunData;
        }
        $preGood_OBJ    = new PreGoodsOrdderManageModel();
        
        $orderInfo  = $preGood_OBJ->getOrderInfroByid($orderId);
        if (FALSE === $orderInfo) {
            $retrunData['msg'] = '备货单不存在!';
            return $retrunData;
        }
        
        $chageResult    = $preGood_OBJ->changeOrderStatus($orderId, $status, $_SESSION['userId']);
        if (true === $chageResult) {                                                        //更新成功
            $retrunData['code'] = 1;
            return $retrunData;
        } else {
            $retrunData['msg'] = '更新状态失败!';
            return $retrunData;
        }
    }
}
