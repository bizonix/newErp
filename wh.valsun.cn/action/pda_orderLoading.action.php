<?php
/**
*类名：装车扫描出库
*功能：处理装车扫描出库相关操作
*作者：陈先钰
*
*/
class Pda_orderLoadingAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";
    /**
     * Pda_orderLoadingAct::act_orderLoading()
     * 	装车扫描的时候对口袋编号进行改变状态，如果是快递的就扫描发货单号
     * @author cxy
     * @return
     */
    public function act_orderLoading(){
        $userId    = $_SESSION['userId'];
		$packageId = trim($_POST['order_group']);
        //需要验证是否是口袋编号
        //if(strlen($packageId)<7){
        //  $a =  WhWaveAreaInfoModel::get_area_info(1,1,1);
        //  echo $a ;exit;
            $group_sql = WhWaveOrderPartionShippingReviewModel::get_pocket($packageId);
        //  print_r($group_sql);exit;
            if(empty($group_sql)){
    			self::$errCode = '003';
    			self::$errMsg  = '该编号不在发货复核中，请重新输入!';
    			return false;
    		}
            $ebay_id_all = WhWaveOrderPartionScanReviewModel::get_shipping_review($packageId);
            if(empty($ebay_id_all)){
                self::$errCode = '003';
                self::$errMsg  = '该编号不在分区复核中，请重新输入!';
    		    return false;
            }
           
            $data = array(
                'packageId' => $packageId,
                'scantime'  => time(),
                'userId'    => $userId,
                'isExpress' => 2
            );
            $select_loading = WhWaveOrderLoadingModel::select_loading($packageId);
            if($select_loading){
                self::$errCode = '003';
                self::$errMsg  = '该'.$packageId.'编号已经进行过装车扫描了!';
    		    return false;
            }
            WhBaseModel::begin();
            $insert_loading = WhWaveOrderLoadingModel::insert($data);
            if(empty($insert_loading)){
                self::$errCode = '003';
                self::$errMsg  = '装车扫描失败，请联系负责人！';
                WhBaseModel :: rollback();
    		    return false;
            }
            foreach($ebay_id_all as $values){ 
                $shipOrderId = $values['shipOrderId'];
                $result      = WhWaveOrderPartionScanReviewModel::updateShippingOrderStatus($shipOrderId,$status = PKS_DONE);
                if(!$result){
                    self::$errCode = '003';
                    self::$errMsg  = '更新'.$shipOrderId.'发货单号失败!';
                    WhBaseModel :: rollback();
    		        return false;
                }
                WhPushModel::pushOrderStatus($shipOrderId,'PKS_DONE',$_SESSION['userId'],time());        //状态推送，需要改为已发货（订单系统提供状态常量）		                    
             
            }
            self::$errCode = '200';
            self::$errMsg  = '扫描'.$packageId.'口袋编号成功!';
            WhBaseModel::commit();
            return true;
      //  }else{
          
       // }   
    }
    //快递发货单 需要判断是否是快递
    public function act_pda_loading_express(){
        $userId    = $_SESSION['userId'];
		$packageId = trim($_POST['order_group']);//发货单号1000013
      //这里是装车扫描快递的
        $shipOrderId = $packageId;
        $where = "where id ='{$shipOrderId}'";
    	$order = orderPartionModel::selectOrder($where);
     	if(!$order){
	  	    self::$errCode = 0;
	        self::$errMsg  = "此发货单不存在！";
		    return false;
	    }
        if($order[0]['isExpressDelivery']==0){
            self::$errCode = 10;
	        self::$errMsg  = "此发货单为非快递单号！";
		    return false;
        }
        //运输方式
        $shipping = CommonModel::getShipingNameById($order[0]['transportId']);
        $no_express_delivery = array('俄速通平邮', '俄速通挂号','中国邮政平邮','中国邮政挂号','EUB','UPS美国专线','Global Mail','香港小包平邮',
                '香港小包挂号','德国邮政挂号','新加坡小包挂号','新加坡DHL GM平邮','新加坡DHL GM挂号','瑞士小包平邮',
                '瑞士小包挂号','USPS FirstClass','UPS SurePost', 'UPS Ground Commercia','比利时小包EU');
        if(in_array($shipping,$no_express_delivery)){
		    self::$errCode = 604;
	     	self::$errMsg  = "此发货单不是快递！";
	    	return false;
	    }
        if($order[0]['orderStatus'] != PKS_WAITING_LOADING){
           	self::$errCode = 0;
			self::$errMsg  = "此发货单状态不是在待装车扫描状态！";
			return false; 
        }
        self::$errCode = '400';
        self::$errMsg  = '扫描'.$packageId.'成功!';
        $res['shipOrderId'] = $shipOrderId;           
        return $res;
    }
    //扫描跟踪号
    /**
     * Pda_orderLoadingAct::act_orderExpress()
     * 如果装车扫描的时候是扫描快递的，那么该模块接收的变量的值是追踪号$ebay_id，发货单号是$shipOrderId
     * @author cxy
     * @return
     */
    public function act_orderExpress(){
        $userId      = $_SESSION['userId'];
   	    $shipOrderId = trim($_POST['shipOrderId']);//快递单号
        $ebay_id     = trim($_POST['ebay_id']);//扫描的跟踪号
        if(empty($shipOrderId)){
            self::$errCode = '201';
            self::$errMsg  = '请输入发货单号!';
            return false; 
        }
        if(empty($ebay_id)){
            self::$errCode = '201';
            self::$errMsg  = '请输入跟踪号!';
            return false; 
        }
       // $arr_track[] = $ebay_id;
         //记录表里和发货单关联的跟踪号
        $tracknumber = WhOrderTracknumberModel::select_TracknumberByOrderId($shipOrderId);
        //print_r($tracknumber);exit();
        $tracking_arr = array();
        foreach($tracknumber as $track){
            $tracking_arr[] = $track['tracknumber'];
        }
        // print_r($arr_track);
       // print_r($tracking_arr);exit; 
        if(!in_array($ebay_id,$tracking_arr)){
            self::$errCode = '202';
            self::$errMsg  = '输入的跟踪号没有和该发货单号绑定!';
            return false; 
        }
             
        $data = array(
            'packageId' => $shipOrderId,
            'scantime'  => time(),
            'userId'    => $userId,
            'tracking'  => $ebay_id,
            'isExpress' => 1
            );
            $select_loading    = WhWaveOrderLoadingModel::select_loading_express($shipOrderId,$ebay_id);
            
            if($select_loading){
                self::$errCode = '003';
                self::$errMsg  = '该'.$shipOrderId.'下的跟踪号'.$ebay_id.'已经进行过装车扫描了!';
    		    return false;
            }
            $insert_loading    = WhWaveOrderLoadingModel::insert($data);
            if(empty($insert_loading)){
                self::$errCode = '003';
                self::$errMsg  = '装车扫描失败，请联系负责人！';
    		    return false;
            }
            $count_load = WhWaveOrderLoadingModel::select_loading_count($shipOrderId);//已经装车扫描的记录
            //当需要扫描的跟踪号等于已经扫描的跟踪号的时候就改变发货单的状态
            if($count_load['muns']== count($tracknumber)){
                 $result = WhWaveOrderPartionScanReviewModel::updateShippingOrderStatus($shipOrderId,$status = PKS_DONE);
                 if(!$result){
                    self::$errCode = '003';
                    self::$errMsg  = '更新'.$shipOrderId.'发货单号失败,请联系负责人!';
    		        return false;
                 }
                 WhPushModel::pushOrderStatus($shipOrderId,'PKS_DONE',$_SESSION['userId'],time());        //状态推送，需要改为已发货（订单系统提供状态常量）		                                                           
              self::$errMsg  = '扫描'.$shipOrderId.'快递单成功,请扫描下一个发货单号!';
            }else{
                self::$errMsg  = '扫描'.$shipOrderId.'快递单的跟踪号成功，请扫描该快递单的下一个跟踪号!';
            }          
            self::$errCode = '200';
           
            return true;
        // WhOrderTracknumberModel::select_ByTracknumber($ebay_id);
        // echo $ebay_id.'--'.$shipOrderId;exit;
    }
}
?>