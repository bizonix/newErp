<?php
	//脚本参数检验
    $nowtime = time();
	include_once dirname(__DIR__)."/common.php";
	//实例化消息队列
	$rabbitMQ = E('RabbitMQ');
	$rabbitMQ->connection('fetchorder');
	$exchange = "WH_STATUS_EXCHANGE";
	$queue = 'GET_WH_STATUS_EXCHANGE_TEST_QUEUE';
	$message = $rabbitMQ->queueSubscribeLimit($exchange, $queue, 100);
	echo "\n<<<<=====[".date('Y-m-d H:i:s', $nowtime)."]系统开始接收推送过来的 订单仓库状态信息 =====>>>>\n";
    error_log("\n<<<<=====[".date('Y-m-d H:i:s', $nowtime)."]系统开始接收推送过来的 订单仓库状态信息 =====>>>>\n",3,WEB_PATH."log/get_wh_order_status_info_from_wh_log.txt");
	$count = count($message);
    foreach($message as $value){
        echo "[ok]--\n 接收到的String:";
        print_r($value);
        echo "\n\n";
        error_log("[ok]--\n 接收到的String:".json_encode($value)."\n\n",3,WEB_PATH."log/get_wh_order_status_info_from_wh_log.txt");
        $statusArr = json_decode($value, true);
        $orderId = $statusArr['originOrderId'];
        $whStatus = $statusArr['orderStatus'];
        $operateUserId = $statusArr['operateUserId'];
        $operateTime = $statusArr['operateTime'];
        $storeId = $statusArr['storeId']?$statusArr['storeId']:1;
        $dataArr = array();
        $dataArr['omOrderId'] = $orderId;
        $dataArr['operateUserId'] = $operateUserId;
        $dataArr['operateTime'] = $operateTime;
        $dataArr['storeId'] = $storeId;
        if($whStatus == 'PKS_DONE'){//已发货标识
            $orderStatus = M('StatusMenu')->getOrderStatusByStatusCode('ORDERS_FINISHED', 'id');
            $orderType = M('StatusMenu')->getOrderStatusByStatusCode('ORDERS_SHIPPED', 'id');
        }else{
            $orderStatus = M('StatusMenu')->getOrderStatusByStatusCode('ORDERS_SHIPPING', 'id');
            $orderType = M('StatusMenu')->getOrderStatusByStatusCode($whStatus, 'id');
        }
        if(intval($orderStatus) <= 0 || intval($orderType) <= 0){
            echo "订单Id: $orderId 获取状态有误,跳过\n\n";
            error_log("订单Id: $orderId 获取状态有误,跳过\n\n",3,WEB_PATH."log/get_wh_order_status_info_from_wh_log.txt");
            continue;
        }
        $dataArr['orderStatus'] = $orderType;
        echo "插入仓库状态表的数据格式为：";print_r($dataArr);echo "\n\n";
        error_log("插入仓库状态表的数据格式为：".json_encode($dataArr)."\n\n",3,WEB_PATH."log/get_wh_order_status_info_from_wh_log.txt");
        $orderObj = M('OrderAdd');
        $orderObj->setInsertOrderId($orderId);
        if($orderObj->insertOrderWh($dataArr)){
            echo "添加仓库状态记录成功 \n\n";
            error_log("添加仓库状态记录成功 \n\n",3,WEB_PATH."log/get_wh_order_status_info_from_wh_log.txt");
            $dataArr2 = array();
            $dataArr2['orderStatus'] = $orderStatus;
            $dataArr2['orderType'] = $orderType;
            $dataArr2['completeTime'] = $operateTime;//订单完结时间
            echo "插入订单表的数据格式为：";print_r($dataArr2);echo "\n\n";
            error_log("插入订单表的数据格式为：".json_encode($dataArr2)."\n\n",3,WEB_PATH."log/get_wh_order_status_info_from_wh_log.txt");
            if(M('OrderManage')->updateData($orderId, $dataArr2)){
                echo "更新订单状态成功 \n\n";
                error_log("更新订单状态成功 \n\n",3,WEB_PATH."log/get_wh_order_status_info_from_wh_log.txt");
            }else{
                echo "更新订单状态失败 \n\n";
                error_log("更新订单状态失败 \n\n",3,WEB_PATH."log/get_wh_order_status_info_from_wh_log.txt");
            }
        }else{
            echo "添加仓库状态记录失败 \n\n";
            error_log("添加仓库状态记录失败 \n\n",3,WEB_PATH."log/get_wh_order_status_info_from_wh_log.txt");
            //print_r(M('OrderAdd')->getErrorMsg());
            echo "\n\n";
        }
        //print_r(M('OrderAdd')->getAllRunSql());       
    }
	echo "\n\t------- [耗时:".ceil((time()-$nowtime))."秒] -------\n";
    echo "\n\t------- [一共接收到 $count 条消息] -------\n";
	echo "\n<<<<=====[".date('Y-m-d H:i:s')."]系统结束接收=====>>>>\n";
    error_log("\n\t------- [耗时:".ceil((time()-$nowtime))."秒] -------\n",3,WEB_PATH."log/get_wh_order_status_info_from_wh_log.txt");
    error_log("\n\t------- [一共接收到 $count 条消息] -------\n",3,WEB_PATH."log/get_wh_order_status_info_from_wh_log.txt");
    error_log("\n<<<<=====[".date('Y-m-d H:i:s')."]系统结束接收=====>>>>\n",3,WEB_PATH."log/get_wh_order_status_info_from_wh_log.txt");
	exit;
	
?>