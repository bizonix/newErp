<?php
/**
*
*功能：订单发货组复核
*作者：陈先钰
*2014-8-4
*/
class pda_shippingGroupReviewAct extends Auth{
   	public static $errCode = 0;
    public static $errMsg = '';   
    /**
     * pda_shippingGroupReviewAct::act_outReview()
     *  扫描口袋编号通过AJAX传递口袋编号来对该编号进行操作
     * @author cxy
     * @return boolean array
     */
    public function act_outReview(){
        $userId 	= $_SESSION['userId'];
		$packageId  = intval(trim($_POST['packageid']));
        //得到发货单分区列表信息
        $get_orderPartion  = WhOrderPartionPrintModel::get_OrderPartion($packageId);
        if(empty($get_orderPartion)){
            self::$errCode = '202';
            self::$errMsg  = '该口袋编号不存在分区列表中';
            return false;
        }
        if($get_orderPartion['status'] == 0){
            self::$errCode = '202';
            self::$errMsg  = '该口袋编号还没有进行打包！';
            return false;
        }
        //得到该口袋编号下的发货单
        $all_order =  WhOrderPartionRecordsModel::get_OrderPartionRecordsByPackageId($packageId);
        if($all_order){
            foreach($all_order as $value){
                $result_status = WhShippingOrderModel::get_order_info('orderStatus',array('id'=>$value['shipOrderId']));
                if($result_status[0]['orderStatus']!= PKS_WAITING_SHIPPING_CHECKING ){
                    self::$errCode = '202';
                    self::$errMsg  = '该口袋编号下有发货单的状态不是在待发货组复核！';
                    return false;      
               }
  
            }
        }
       //发货组复核信息
        $get_groupReview   = WhWaveOrderPartionShippingReviewModel::get_pocket($packageId);
        if($get_groupReview){
            self::$errCode = '211';
            self::$errMsg  = '该口袋编号已经在发货组复核中复核过了';
            return true;
        }
        //分区复核的信息
        $get_orderReview   = WhWaveOrderPartionScanReviewModel::get_shippingNews($packageId);
       // print_r($get_orderReview);
       // echo $get_orderReview['review_weight'];exit;
        if($get_orderPartion['totalWeight'] == $get_orderReview['review_weight'] && $get_orderPartion['totalNum'] == $get_orderReview['review_num']){
            $is_error      = 0;
            self::$errCode = '200';
            self::$errMsg  = '请随机扫描一个订单!';
            $data = array(
               'packageId' => $packageId,
               'is_error'  => $is_error,
               'userId'    => $userId,
               'scantime'  => time()
            );
            $partion_arr   = array();
            $review_arr    = array();
            //分区扫描得到的发货单
            $get_orderPartion_detail = WhOrderPartionRecordsModel::get_OrderPartionRecordsByPackageId($packageId);
           //得到分区复核的发货单列表
            $get_review_detail       = WhWaveOrderPartionScanReviewModel::get_shipping_review($packageId);
            
            foreach($get_orderPartion_detail as $v){
                $partion_arr[$v['shipOrderId']] = $v;
            }
            foreach($get_review_detail as $v){
                $review_arr[$v['shipOrderId']]  = $v;
            }
            $diversity_a = array_diff_key($partion_arr,$review_arr);
            $diversity_b = array_diff_key($review_arr,$partion_arr);
            if(!empty($diversity_a) || !empty($diversity_b)){
                self::$errCode = '500';
                self::$errMsg  = '分区的发货单与分区复核的发货单不符合';
                $order_str     = '';
                if($diversity_a){
                    foreach($diversity_a as $k=>$val){
                        $order_str .= $k.',';
                    }
                }elseif($diversity_b){
                    foreach($diversity_b as $k=>$val){
                        $order_str .= $k.',';
                    }
                }
                return trim($order_str,',');
            }
            $res = array();
            WhBaseModel::begin();
            $result = WhWaveOrderPartionShippingReviewModel::insert($data);
            if($result){
                foreach($get_review_detail as $ks=>$val){               
                    //更新发货操作记录表
                    $update = WhWaveOrderPartionShippingReviewModel::updateOrderRecords($val['shipOrderId'],$userId);
                    if(!$update){
                        self::$errCode = '206';
      	                self::$errMsg  = '更新操作记录失败,请联系负责人!'; 
                        WhBaseModel::rollback();
                        return false;
                    }
                   //更新发货表状态
                   $ostatus = WhWaveOrderPartionScanReviewModel::updateShippingOrderStatus($val['shipOrderId'],$status=PKS_WAITING_LOADING);
       	            if(!$ostatus){
  	                    self::$errCode = 608;
			            self::$errMsg  = "更新发货单状态失败！";
			            WhBaseModel :: rollback();
			            return false;
    	            }
                   WhPushModel::pushOrderStatus($val['shipOrderId'],'PKS_WAITING_LOADING',$_SESSION['userId'],time());        //状态推送，需要改为装车扫描（订单系统提供状态常量）		                    
                }
                self::$errCode                    = '200';
                self::$errMsg                     = '该口袋编号正确,请复核下该口袋编号下的发货单!'; 
                $get_orderPartion['totalWeight']  = round($get_orderPartion['totalWeight']/1000,3);
                $get_orderReview['review_weight'] = round($get_orderReview['review_weight']/1000,3);
                $res['partion_data'] = $get_orderPartion;
                $res['review_data']  = $get_orderReview;
                $res['packageid']    = $packageId;
            }else{
                self::$errCode       = '206';
  	            self::$errMsg        = '该发货组复核口袋编号失败,请联系负责人!'; 
                WhBaseModel::rollback();
                return false;
            }
            WhBaseModel::commit();
            return $res;  
        }else{
            self::$errCode = '201';
            self::$errMsg  = '分区和分区复核的数据不一致!';
        }
            $res['partion_data'] = $get_orderPartion;
            $res['review_data']  = $get_orderReview;
            $res['packageid']    = $packageId;
            return $res;
    }
	
   
    /**
     * pda_shippingGroupReviewAct::Act_orderReview()
     * 对随机扫描的发货单号进行判断
     * @author cxy
     * @return
     */
    public function Act_orderReview(){
        $userId      = $_SESSION['userId'];
        $packageId   = intval(trim($_POST['packageid']));
        $shipOrderId = trim($_POST['ebay_id']); 
        //先核对订单
    	//兼容 EUB或者 包裹 扫描的是 trackno 而非ebayid
    	$p_real_ebayid   = '#^\d+$#';
	    $p_trackno_eub   = '#^(LK|RA|RI|RL|RB|RC|RD|RM|RR|RF|LN|LM|AG)\d+(CN|HK|DE200)$#';
	    $p_trackno_ups   = '#^(1ZR)\d+$#';
    	$p_trackno_bpost = '#^(BLVS)\d+$#';
    	$is_eub_package_type=false;
    	if(	preg_match($p_real_ebayid,$shipOrderId)	){
    	}else if( preg_match($p_trackno_eub,$shipOrderId) ){
    		$is_eub_package_type = true;
    	}else if( preg_match($p_trackno_ups,$shipOrderId) ){
    		$is_eub_package_type = true;
    	}else if( preg_match($p_trackno_bpost,$shipOrderId) ){
    		$is_eub_package_type=true;			
    	}else if(strlen($shipOrderId) > 11){
            $is_eub_package_type = true;
    	}else{
    		self::$errCode = '001';
    		self::$errMsg  = '订单号['.$shipOrderId.']格式有误';
    		return false;	
    	}        
        if($is_eub_package_type == true){
            //$is_eub_package_type是真的时候$ebay_id是跟踪号
            $info = orderWeighingModel::selectOrderId($shipOrderId);
            if(!$info){
				self::$errCode = 501;
				self::$errMsg  = "此跟踪号不存在！";
				return false;
			}
            //得到发货单号
            $shipOrderId = $info[0]['shipOrderId']; 
        }
          //得到发货单明细
        $order_records   = WhOrderPartionRecordsModel::get_OrderPartionRecords($shipOrderId);
        if(empty($order_records)){
        	self::$errCode = 0;
			self::$errMsg  = "此跟踪号/发货单号还没有进行分区！";
            return false;
        }
        if($order_records['packageId'] != $packageId){
            self::$errCode = 0;
			self::$errMsg  = "此跟踪号/发货单号应该在{$order_records['packageId']}口袋编号中！";
            return false; 
        }
       	$where = "where id={$shipOrderId}";
		$order = orderPartionModel::selectOrder($where);
		if(!$order){
			self::$errCode = 0;
			self::$errMsg  = "此发货单不存在！";
			return false;
		}
        if($order[0]['orderStatus'] != PKS_WAITING_LOADING){
           	self::$errCode = 0;
			self::$errMsg  = "此发货单没有在待装车扫描状态下！";
			return false; 
        }
        $order_str           = $shipOrderId;
        $result_group_review = WhWaveOrderPartionShippingReviewModel::get_pocket($packageId);
        if(empty($result_group_review)){
           	self::$errCode = 0;
			self::$errMsg  = "此口袋编号没有进行过分区复核！";
			return false; 
        }else{
            if($result_group_review['orders'] != 0){
                $order_str .= ','.$result_group_review['orders'];
                $order_str = trim($order_str,',');
                $order_str = explode(',',$order_str);
                $order_str = array_unique($order_str);
                $order_str = implode(',',$order_str);
            }
            $update_group_shipping = WhWaveOrderPartionShippingReviewModel::update_shipping_review($packageId,$order_str);
            if($update_group_shipping){
                self::$errCode = '200';
			    self::$errMsg  = "请复核下一个发货单或者下一个口袋编号";
                return true;
            }else{
                self::$errCode = '20';
			    self::$errMsg  = "扫描发货单号失败，请联系负责人";
                return false;
           }
        }
    }
}