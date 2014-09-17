<?php
//脚本参数检验
$nowtime 	= time();
include_once dirname(__DIR__)."/common.php";

$rabbitMQ = E('RabbitMQ');
$rabbitMQ->connection('fetchorder');
$exchange = "ORDER_WAIT_SHIP";

echo "\n<<<<=====[".date('Y-m-d H:i:s', $nowtime)."]系统【开始】推送订单 ====>>>>\n";
error_log("\n<<<<=====[".date('Y-m-d H:i:s', $nowtime)."]系统【开始】推送订单 ====>>>>\n",3,WEB_PATH."log/send_order_2_wh_log.txt");
$handleCombineOrders = M('OrderManage')->handleCombineOrders();//将待发货的订单进行合并包裹
echo 'handleCombineOrders：'.json_encode($handleCombineOrders)."\n\n";
$orderObj = M('order');
$orderIdsArr = $orderObj->getOrderIdByOrderStatus(M('StatusMenu')->getOrderStatusByStatusCode('ORDER_WAIT_SHIP','id'));//取得待发货的不是合并包裹子订单的有效订单
//$orderIdsArr = array('2041723');
$count = count($orderIdsArr);
//var_dump($orderIdsArr);exit;
echo "\n<====[".date('Y-m-d H:i:s')."]系统共有 $count 个订单信息要发送\n";
error_log("\n<====[".date('Y-m-d H:i:s')."]系统共有 $count 个订单信息要发送\n",3,WEB_PATH."log/send_order_2_wh_log.txt");
$noDetailOrderCount = 0;
foreach($orderIdsArr as $OmOrderId){
    $orderIdTmpArr = array();
    $orderIdTmpArr[] = $OmOrderId;
    $orderData = $orderObj->getFullUnshippedOrderById($orderIdTmpArr);
    foreach($orderData as $key=>$value){
        if(!array_filter($orderData[$key]['orderDetail'])){
            unset($orderData[$key]);
            $noDetailOrderCount++;
            continue;
        }
        if($value['order']['combinePackage'] == 1){//如果是合并包裹主订单，则要带出子包裹的关系
            $EOrderIdArr = $orderObj->getECombinePackageOrderIdByMOrderId($value['order']['id']);//取得合并包裹子订单号数组
            if(!empty($EOrderIdArr)){
                $orderData[$key]['combinePackageOrderData'] = $orderObj->getFullUnshippedOrderById($EOrderIdArr);
            }
        }
        if($value['order']['isExpressDelivery'] == 1){//如果是快递订单，则需要加上快递描述，针对DHL中快递描述只能出现3个SPU这个情况由仓库处理，因为是由仓库最后决定运输方式
            $expressRemarkxObj = M('ExpressRemark');
            $orderData[$key]['OrderDeclarationContent'] = $expressRemarkxObj->getRemarkById($value['order']['id']);
            if(!empty($orderData[$key]['combinePackageOrderData'])){
                $cpodOrderArr = array();
                foreach($orderData[$key]['combinePackageOrderData'] as $cpodKey=>$cpodValue){
                    $orderData[$key]['combinePackageOrderData'][$cpodKey]['OrderDeclarationContent'] = $expressRemarkxObj->getRemarkById($cpodValue['order']['id']);
                }
            }
        }
        $message = json_encode($orderData[$key]);
        $rabbitMQ->basicPublish($exchange, $message);
        echo "[send]--$message \n\n";
        error_log("[send]--$message \n\n",3,WEB_PATH."log/send_order_2_wh_log.txt");
        //这里要将该订单update成发货中的待配货状态(已经去掉了，这个状态改变放到接口实现)
        //if(M('OrderManage')->updateData($OmOrderId, array('orderStatus'=>M('StatusMenu')->getOrderStatusByStatusCode('ORDERS_SHIPPING','id'),'orderType'=>M('StatusMenu')->getOrderStatusByStatusCode('ORDERS_UNPICKED','id')))){
//            echo "更新 $OmOrderId 为发货中 成功 \n\n";
//        }else{
//            echo "更新 $OmOrderId 订单状态 失败 \n\n";
//        }
        
    }
}


//print_r($orderData);exit;

echo "\n<====[".date('Y-m-d H:i:s')."]系统【结束】推送订单, 本次共发送 $count 条数据,其中没有订单明细的有 $noDetailOrderCount 条\n";
error_log("\n<====[".date('Y-m-d H:i:s')."]系统【结束】推送订单, 本次共发送 $count 条数据,其中没有订单明细的有 $noDetailOrderCount 条\n",3,WEB_PATH."log/send_order_2_wh_log.txt");
################################## end 这里可以扩展时间分页  ##################################
?>