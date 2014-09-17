<?php
	//脚本参数检验
    $nowtime = time();
	include_once dirname(__DIR__)."/common.php";
	//实例化消息队列
	$rabbitMQ = E('RabbitMQ');
	$rabbitMQ->connection('fetchorder');
	$exchange = "WH_PUSH_ORDER_TRACK";
	$queue = 'GET_WH_PUSH_ORDER_TRACK_TEST_QUEUE';
	$message = $rabbitMQ->queueSubscribeLimit($exchange, $queue, 1);
	echo "\n<<<<=====[".date('Y-m-d H:i:s', $nowtime)."]系统开始接收推送过来的 运费/跟踪号订单 =====>>>>\n";
	$count = count($message);
    foreach($message as $value){
        echo "[ok]--\n 接收到的String:";
        print_r($value);       
        echo "\n\n";
        //下面处理
        $ttArr = json_decode($value, true);
        $orderList = M('Order')->getUnshippedOrderById(array($ttArr['orderId']));
        if(!empty($orderList[0])){
            $combinePackageSidList = M('Order')->getECombinePackageOrderIdByMOrderId($ttArr['orderId']);//检测是否是合并包裹
            if(empty($combinePackageSidList)){//如果不存在合并包裹时
                if(M('OrderManage')->updateData($ttArr['orderId'], array('transportId'=>$ttArr['transportId'],'channelId'=>$ttArr['channelId'],'calcWeight'=>$ttArr['orderWeight']/1000,'calcShipping'=>$ttArr['actualShipping']))){
                    echo "更新订单运输方式/渠道/真实重量/真实运费等信息成功\n\n";                    
                }
            }else{//如果存在合并包裹时，需要根据传过来的重量，运费等信息按照重量分摊
                $orderCalcList = M('Order')->getOrderCalcListById($ttArr['orderId']);//先找出主单的估算信息
                $weightArr = array();
                $weightArr[$ttArr['orderId']] = $orderCalcList[0]['calOrderWeight'];//主单的重量
                foreach($combinePackageSidList as $sOmOrderId){
                    $weightArr[$sOmOrderId] = M('Order')->getOrderCalcListById($ttArr['orderId']);
                }
                $totalWeight = array_sum($weightArr);
                foreach($weightArr as $tmpOmOrderId=>$tmpWeight){
                    if(M('OrderManage')->updateData($tmpOmOrderId, array('transportId'=>$ttArr['transportId'],'channelId'=>$ttArr['channelId'],'calcWeight'=>($ttArr['orderWeight']/1000)*($tmpWeight/$totalWeight),'calcShipping'=>$ttArr['actualShipping']*($tmpWeight/$totalWeight)))){
                        echo "更新订单:$tmpOmOrderId  运输方式/渠道/真实重量(分摊)/真实运费(分摊)等信息 成功\n\n"; 
                    }else{
                        echo "更新订单:$tmpOmOrderId  运输方式/渠道/真实重量(分摊)/真实运费(分摊)等信息 失败\n\n"; 
                    }                    
                }
            }
            $orderAddObj = M('OrderAdd');
            $orderAddObj->setInsertOrderId($ttArr['orderId']);
            if(M('OrderAdd')->insertOrderTrack(array('omOrderId'=>$ttArr['orderId'],'accountId'=>$orderList[0]['accountId'],'recordNumber'=>$orderList[0]['recordNumber'],'transportId'=>$ttArr['transportId'],'tracknumber'=>$ttArr['tracknumber'],'createdTime'=>$ttArr['tracknumberTime']))){
                echo "插入 订单跟踪号信息 成功\n\n";
            }           
        }else{
            echo "未找到该订单记录\n\n";
        }
        
        //print_r(M('OrderAdd')->getErrorMsg());
        //print_r(M('OrderAdd')->getAllRunSql());
    }
	echo "\n\t------- [耗时:".ceil((time()-$nowtime))."秒] -------\n";
    echo "\n\t------- [一共接收到 $count 条消息] -------\n";
	echo "\n<<<<=====[".date('Y-m-d H:i:s')."]系统结束接收=====>>>>\n";
	exit;
	
?>