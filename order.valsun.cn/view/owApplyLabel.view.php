<?php
class OwApplyLabelView {
    
    public function view_applyLabel(){
        $returnData = array('code'=>0, 'msg'=>'');
        $orderId    = isset($_GET['oid']) ? intval($_GET['oid']) : 0;
        if (!$orderId) {
            $returnData['msg']  = '未指定订单号';
            echo json_encode($returnData);
        	return FALSE;
        }
        
        $orderInfo  = OrderInfoModel::getOrderInfo($orderId);
        if (FALSE === $orderInfo) {                                                         //不存在的订单号
        	$returnData['msg'] = '不存在的订单号!';
        	echo json_encode($returnData);
        	return FALSE;
        }
        if ( $orderInfo['orderStatus']!=911 || $orderInfo['orderType']!= 916 ) {            //订单状态不合法   
        	$returnData['msg'] = '该订单不是海外仓待打印订单!';
        	echo json_encode($returnData);
        	return FALSE;
        }
        
        if ( $orderInfo['transportId'] == 0 ) {                                             //订单未设置运输方式   
        	$returnData['msg'] = '该订单还未设定运输方式!';
        	echo json_encode($returnData);
        	return FALSE;
        }
        
        $orderActObj    = new OrderindexAct();
        $skuList        = $orderActObj->act_getRealskulist($orderId);                       //获得全部的sku列表
        if ( FALSE == $skuList ) {
            $returnData['msg'] = '获取料号信息出错!';
            echo json_encode($returnData);
            return FALSE;
        }
        
        $owOrderMg   = new OwOrderManageModel();
        
        $owShipDes   = new OwShippingWayDesisionModel();
        $outside     = $owShipDes->culPackageLWH($skuList);
        $buyerInfo   = $owOrderMg->getUnshippedOrderBuyerInfo($orderId);                    //买家信息
        if (FALSE === $buyerInfo) {
            $returnData['msg'] = '获取买家信息失败!';
            echo json_encode($returnData);
            return FALSE;
        }
        $zipCode     = $buyerInfo['zipCode'];                                                //邮编
        $weight      = $orderInfo['calcWeight'];                                             //订单重量
        if ($weight == 0) {                                                                  //重量为0 则无法处理
        	$returnData['msg'] = '订单重量为0,请先确认重量!';
        	echo json_encode($returnData);
        	return FALSE;
        }
        
        $owOrderMG   = new OwOrderManageModel();
        $transInfo   = $owOrderMG->getShippingInfo($orderId);
        if (!empty($transInfo) && ( $transInfo['shippingWay']== 'USPS') && ($transInfo['isCanceled'] == 0) ) {
            /* 当之前有申请过usps 并且申请的usps还没有退款的时 则 不予申请*/
            $returnData['msg'] = '请先退款已申请的USPS!';
            echo json_encode($returnData);
            return FALSE;
        }
        
        $shipping    = new ExpressLabelApplyModel();
        $zone        = $shipping->getZoneCode($zipCode);
        $zone        = (FALSE !== $zone) ? $zone : 6;                                                        //如果没找到分区则默认为6区
        $shippingInfo    = $owShipDes->chooseShippingWay($skuList, $weight, $outside, $zone);
        
        if ( $shippingInfo ) {
            $transId    = $shipping->reflectCodeToId($shippingInfo['shippingCode']);
        } else {
            $transId    = 0;
        }

        if ($transId != $orderInfo['transportId'] ) {                                                           //计算出的运输方式和系统的不符合
            $returnData['msg']  = "计算的运输方式和初始运输方式不符合! 请重新生成运输方式 ! 计算运输方式为 ".$shippingInfo['shippingCode'];
            echo json_encode($returnData);
            return FALSE;
        }
        
        if ( 0 == $transId ) {                                                                                  //没找到正确的运输方式
        	$returnData['msg'] = OwShippingWayDesisionModel::$errMsg;
        	echo json_encode($returnData);
        	return FALSE;
        }
        
        $data      = array();
        $data['recipients']         = $buyerInfo['username'] ;                                                   //收件人
        $data['re_phone']           = $buyerInfo['landline'].'/'.$buyerInfo['phone'];                            //电话
        $data['re_address1']        = $buyerInfo['street'];                                                      //街道地址一
        $data['re_address2']        = $buyerInfo['address2'];                                                    //街道地址二
        $data['re_city']            = $buyerInfo['city'];                                                        //市
        $data['re_post_code']       = $zipCode;                                                                  //邮编
        $data['re_country_code']    = 'US';                                                                      //国家简称
        $data['weight']             = $weight;                                                                   //重量
        $data['orderId']            = $orderId;                                                                  //订单号
        
        $owSkuMG                    = new OwSkuInfoManageModel();                                                //生成料号仓位数据
        $skuLocation                = $owSkuMG->getAsetOfSkusLocation(array_keys($skuList));
        $positionStr                = '';
        $skuDetailStr               = '';
        foreach ($skuList as $k=>$_num){
            $position       = $skuLocation[$k];
            $positionStr   .= " $k [$position] ";
            $skuDetailStr  .= " $k * $_num ";
        }
        $data['sku_position']       = $positionStr;
        $data['show_detail']        = $skuDetailStr;
        
        $handResult     = FALSE;
        $errMsg         = '';
//         print_r($shippingInfo['shippingCode']);exit;
        if ( 'UPS Ground' == $shippingInfo['shippingCode'] ) {                                                   //申请 UPS Label
        	$upsApplyObj   = new ApplyUpsLabelModel();
        	$data['re_state_code']      = $upsApplyObj->getStateAbbreviationName($buyerInfo['state']);           //州简称
        	if (FALSE == $data['re_state_code']) {                                                                                //没找到则使用原始值
        	    $data['re_state_code'] = $buyerInfo['state'];
        	}
//         	print_r($data);exit;
        	$applyResult                 = $upsApplyObj->applyUPSLabel($data);

        	if (FALSE === $applyResult) {                                                                         //申请标签失败
        		$errMsg   = ApplyUpsLabelModel::$errMsg;
        	} else {
        	    $handResult    = TRUE;
        	    $this->deal_img_ups($applyResult['imagePath'], $applyResult['imagePath']);
        	}
        } else if ('USPS' == $shippingInfo['shippingCode'] ) {                                                   //申请usps label
            $uspsApplyObj               = new ApplyUSPSLabelModel();
            $data['re_state_code']      = $uspsApplyObj->getStateAbbreviationName($buyerInfo['state']);           //州简称
            if (FALSE == $data['re_state_code']) {                                                                                //没找到则使用原始值
        	    $data['re_state_code'] = $buyerInfo['state'];
        	}
            $mailClass      = $shippingInfo['extensionInfo']['mailclass'];                                        //运输类型
            $packageType    = $shippingInfo['extensionInfo']['packageType'];
            $typeInfo       = array('mailClass'=>$mailClass, 'packageType'=>$packageType);
            $applyResult     = $uspsApplyObj->aplyUSPSLabel($data, $typeInfo);
            if (FALSE === $applyResult) {                                                                          //申请标签失败
        		$errMsg   = ApplyUSPSLabelModel::$errMsg;
        	} else {
        	    $handResult    = TRUE;
        	    if ($packageType=='Letter' || $packageType=='Flat') {
        	    	$this->deal_img_usps($applyResult['imagePath'], $applyResult['imagePath']);                     //后期处理图片
        	    }
        	}
        }
        
        
        if (FALSE == $handResult) {
        	$returnData['msg'] = $errMsg;
        } else {
            $returnData['code'] = 1;
//               array('trackNumber'=>$trackNumber, 'shippFee'=>$totalMoney, 'imagePath'=>$labelSavePath
            $result = $owOrderMG->insertNewTrackNumber($orderId, $applyResult['trackNumber'], $shippingInfo['shippingCode'], $_SESSION['sysUserId'], 0);       //更新跟踪号信息
            $result2= $owOrderMG->changeOrderStatus(911, 910, $orderId);
        }
        echo json_encode($returnData);
        return FALSE;
    }
    
    /*
     * 取消运输方式
     */
    public function view_cancelShippingWay(){
        $returnData = array('code'=>0, 'msg'=>'');
        $orderId    = isset($_GET['orderId'])   ? intval($_GET['orderId']) : FALSE;
        if (empty($orderId)) {
        	$returnData['msg'] = '未指定订单号';
        	echo json_encode($returnData);
        	exit;
        }
        
        $owOrderMg  = new OwOrderManageModel();
        $transInfo  = $owOrderMg->getShippingInfo($orderId);
        if (FALSE == $transInfo) {
        	$returnData['msg'] = '该订单不存未生产运输方式!';
        	echo json_encode($returnData);
        	exit;
        }
        
        if ( 1 == $transInfo['isCanceled'] ) {                                      //已经取消过
        	$returnData['code'] = 1;
        	echo json_encode($returnData);
        	exit;
        }
        
        $trackNumber    = $transInfo['tracknumber'];
        if ( ('UPS Ground' == $transInfo['shippingWay']) || (0 == strlen($trackNumber)) ) {     //若是UPS 或 跟踪号为空 则直接置为已取消
        	$result    = $owOrderMg->updateOwTransInfo(array('isCanceled'=>1), $orderId);
        	if (TRUE == $result) {
        		$returnData['code'] = 1;
            	echo json_encode($returnData);
            	exit;
        	} else {
        	    $returnData['msg'] = '申请退款失败!';
        	    echo json_encode($returnData);
        	    exit;
        	}
        }
        
        
        if ('USPS' == $transInfo['shippingWay']) {
        	$uspsApp   = new ApplyUSPSLabelModel();
        	$result    = $uspsApp->refoundUSPS($trackNumber);
        	if ($result) {
        	    $Upresult    = $owOrderMg->updateOwTransInfo(array('isCanceled'=>1), $orderId);
        		$returnData['code'] = 1;
        	    echo json_encode($returnData);
        	    exit;
        	} else {
        	    $returnData['msg'] = ApplyUSPSLabelModel::$errMsg;
        	    echo json_encode($returnData);
        	    exit;
        	}
        }
    }
    
    /*
     * 对UPS的图片进行后期处理
     */
    public function deal_img_ups($filename,$savename){
    	$degrees = -90;
    	$canvas = imagecreatetruecolor(1201, 800);
    	$curren_image = imagecreatefromgif($filename);
    	imagecopy($canvas, $curren_image, 0, 0, 0,0, 1201, 800);
    	imagegif($canvas, $savename, 100);
    	imagedestroy($curren_image);
    	$source = imagecreatefromgif($savename);
    	$rotate = imagerotate($source, $degrees, 0);
    	imagegif($rotate, $savename, 100);
    	imagedestroy($source);
    }
    
    /*
     * 处理图片
     */
    function deal_img_usps($filename, $savename){
        $degrees = -90;
        $source = imagecreatefromgif($savename);
        $rotate = imagerotate($source, $degrees, 0);
        imagegif($rotate, $savename, 100);
        imagedestroy($source);
    }
    
}
