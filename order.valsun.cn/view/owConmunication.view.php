<?php
class OwConmunicationView extends BaseView {
    
    /*
     * 构造函数
     */
    function __construct() {
        parent::__construct();
    }
    
    /*
     * 更新订单状态 由海外仓系统同步过来
     */
    public function view_updateOrderStatus(){
        $returnData = array('code'=>0, 'Msg'=>'');
        $orderId    = isset($_GET['orderId'])   ? $_GET['orderId']  : FALSE;
        $status_1   = isset($_GET['status_1'])  ? $_GET['status_1'] : FALSE;
        $status_2   = isset($_GET['status_2'])  ? $_GET['status_2'] : FALSE;
//         print_r($_GET);exit;
        if ( FALSE===$orderId || FALSE === $status_1 || FALSE === $status_2) {
        	$returnData['code']    = 0;
        	$returnData['Msg']     = '参数不完整!';
        	echo json_encode($returnData);
        	exit;
        }
        $orderMg    = new OwOrderManageModel();
        $orderInfo  = $orderMg->getOrderInfoById($orderId, array('orderStatus', 'orderType'));
        if (FALSE === $orderInfo) {
            $returnData['code']    = 0;
            $returnData['Msg']     = '不存在的订单!';
            echo json_encode($returnData);
            exit;
        }
        
        /* if ( 911!=$orderInfo['orderStatus'] || 917!=$orderInfo['orderType']) {
            $returnData['code']    = 0;
            $returnData['Msg']     = '当前订单状态不可改变!';
            echo json_encode($returnData);
            exit;
        } */
        
        $result = $orderMg->changeOrderStatus($status_1, $status_2, $orderId);
        if ($result) {
        	$returnData['code']    = 1;
            $returnData['Msg']     = '更新成功!';
            echo json_encode($returnData);
            exit;
        } else {
            $returnData['code']    = 0;
            $returnData['Msg']     = '更新失败!';
            echo json_encode($returnData);
            exit;
        }
    }
    
    /*
     * 同步上门取件订单到系统
     */
    public function view_syncLocalPickUpOrder(){
        $orderId    = isset($_GET['orderId']) ? $_GET['orderId'] : FALSE;
        $returnData = array('code'=>0 , 'msg'=>'');
        if (FALSE == $orderId) {
        	$returnData['msg'] = ' 未指定订单号';
        	echo json_encode($returnData);
        	exit;
        }
        
        $owOrderMg  = new OwOrderManageModel();
        $orderInfo  = $owOrderMg->getOrderInfoById($orderId, '*');
        if (FALSE == $orderInfo) {
        	$returnData['msg'] = '不存在的订单';
        	echo json_encode($returnData);
        	exit;
        }
        
        if ($orderInfo['orderStatus'] != 911 || $orderInfo['orderType'] !=928 ) {
            $returnData['msg'] = '该订购单不是上门取件订单';
            echo json_encode($returnData);
            exit;
        }
        
        $orderAct   = new OrderindexAct();
        $orderSync  = new OwOrderSyncModel();
        
        $skuList    = $orderAct->act_getRealskulist($orderId);                                      //获取sku信息列表
        if ( empty($skuList) ) {
            $returnData['msg'] = '未找到sku';
            echo json_encode($returnData);
            exit;
        }
        
        $transInfo  = $owOrderMg->getShippingInfo($orderId);
        if ( empty($transInfo) ) {                                                                  //获取运输方式信息
            $transInfo  = array('shippingWay'=>'localPickup', 'tracknumber'=>'');
        }
        
        $platformInfo   = $owOrderMg->getPlatformInfoByPid($orderInfo['platformId']);
        if (FALSE == $platformInfo) {
                $returnData['msg'] = '无法获取平台信息';
                echo json_encode($returnData);
                exit;
        }
        
        $platSuffix     = $platformInfo['suffix'];
        $extensionTabel =  'om_unshipped_order_extension_'.$platSuffix;                                 //扩展信息表名
        $extensionInfo  = $owOrderMg->getExtensionInfo($extensionTabel, $orderId);
        if ($extensionInfo) {
              if ( "amazon" == $platSuffix ) {                                                            //亚马逊订单
                    $orderInfo['note'] = $extensionInfo['note'];
              } else if ( 'ebay' == $platSuffix ) {                                                       //ebay订单
                   $orderInfo['note']     = $extensionInfo['feedback'];
              }
        }
        
        $UserInfo   = $owOrderMg->getBuyerInfoById($orderId);
            
        $sellerInfo     = $owOrderMg->getSellerInfoById($orderInfo['accountId']);                             //获得卖家账号信息
        if ($sellerInfo) {
                $orderInfo['account']    = $sellerInfo['ebay_account'] ;
        } else {
                $orderInfo['account']    = '' ;
        }
        $orderInfo['putstatus'] = 16;
            
        $submitData = array(
                'orderInfo'    => $orderInfo,
                'userInfo'     => $UserInfo,
                'transInfo'    => $transInfo,
                'skuList'      => $skuList
        );
        
//         print_r($submitData);exit;
        $reuslt = $orderSync->pushPrintedOrderToUsWh($orderId, $submitData);
        if (TRUE == $reuslt) {
        	$returnData['code']    = 1;
        } else {
            $returnData['msg']     = OwOrderSyncModel::$errMsg;
        }
        
        echo json_encode($returnData);
    }
    
}

?>