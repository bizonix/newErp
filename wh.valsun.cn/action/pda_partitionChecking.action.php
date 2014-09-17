<?php
/**
*
*功能：订单分区复核
*作者：陈先钰
*2014-7-31
*/
class pda_partitionCheckingAct extends Auth{
	public static $errCode = 0;
    public static $errMsg = '';
    /*
     * 构造函数
     */
    public function __construct() {
        
    }  
    /**
     * pda_partitionCheckingAct::act_partitionChecking()
     *   对扫描分区编号的检查
     * @author cxy
     * @return boolean
     */
    public function act_partitionChecking(){
        $userId 	= $_SESSION['userId'];
		$packageId  = intval(trim($_POST['partion_id']));
        $no_express_delivery = array('俄速通平邮', '俄速通挂号','中国邮政平邮','中国邮政挂号','EUB','UPS美国专线','Global Mail','香港小包平邮',
                            '香港小包挂号','德国邮政挂号','新加坡小包挂号','新加坡DHL GM平邮','新加坡DHL GM挂号','瑞士小包平邮',
                            '瑞士小包挂号','USPS FirstClass','UPS SurePost', 'UPS Ground Commercia','比利时小包EU');
        if(empty($userId)){
            self::$errCode = '0';
            self::$errMsg  = '系统登录超时,请先关闭浏览器 然后登录扫描!!';
            return false;
        }
         //得到一维数组分区记录,
        $partion = WhOrderPartionPrintModel::get_OrderPartion($packageId);
        if(empty($partion)){
 	      	self::$errCode       = '0';
		   	self::$errMsg        = '该分区编号不存在，请重新输入!';
            return false;
	    }
        if($partion['status']==0){
           	self::$errCode       = '0';
		   	self::$errMsg        = '该分区编号没有进行打包，请检查!';
            return false;
        }
        $is_review = WhWaveOrderPartionScanReviewModel::get_shipping_review($packageId);
        if($is_review){
            self::$errCode       = '205';
		   	self::$errMsg        = '该口袋编号已经复核过了，请确认是否要重新扫描!';
            $res['res_info']     = $partion['partion'];
            $res['real_partion'] = $partion['partion'];
            $res['package_id']   = $packageId;
            return $res; 
        }
        if(!empty($partion)){
            self::$errCode       = '200';
		   	self::$errMsg        = '请扫描该分区下的订单!';
            $res['res_info']     = $partion['partion'];
            $res['real_partion'] = $partion['partion'];
            $res['package_id']   = $packageId;
            return $res; 
        }
    }
        
    /**
     * pda_partitionCheckingAct::act_scanOrderReview()
     * 在PDA扫描分区编号后对订单号进行分区复核
     * @author cxy
     * @return boolean
     */
    public function act_scanOrderReview(){
        $userId 	= $_SESSION['userId'];
        $ebay_id    = trim($_POST['ebay_id']);
        $ebay_id    = substr($ebay_id, -22);
	    $partion    = trim($_POST['partion']);
        $package_id = intval(trim($_POST['package_id']));
		if(empty($userId)){
            self::$errCode = '0';
            self::$errMsg  = '系统登录超时,请先关闭浏览器 然后登录扫描!!';
            return false;
        }
        //先核对订单
    	//兼容 EUB或者 包裹 扫描的是 trackno 而非ebayid 拿LN229773466CN追踪号对应1012970发货单号测试
    	$p_real_ebayid = '#^\d+$#';
    	$p_trackno_eub = '#^(LK|RA|RI|RL|RB|RM|RC|RD|RR|RF|LN|LM|AG)\d+(CN|HK|DE200)$#';
    	$p_trackno_ups = '#^(BLVS|1ZR)\d+$#';
        $p_ups         = '/^(1ZA)/';
    	$is_eub_package_type = false;
    	if(	preg_match($p_real_ebayid,$ebay_id)	){
    	}else if( preg_match($p_trackno_eub,$ebay_id) ){
    		$is_eub_package_type = true;
    	}else if( preg_match($p_trackno_ups,$ebay_id) ){
    		$is_eub_package_type = true;
    	}else if(preg_match($p_ups, $ebay_id)){
    	    $is_eub_package_type = true;
    	}else{
    		self::$errCode = '001';
         	self::$errMsg  = '订单号['.$ebay_id.']格式有误';
    		 return false;
    	}
        if(strlen($ebay_id)> 20){
            $is_eub_package_type = true;
    	}
        if($is_eub_package_type == true){
            //$is_eub_package_type是真的时候$ebay_id是跟踪号
            $info = orderWeighingModel::selectOrderId($ebay_id);
            if(!$info){
				self::$errCode = 501;
				self::$errMsg  = "此跟踪号不存在！";
				return false;
			}
            //得到发货单号
            $shipOrderId = $info[0]['shipOrderId']; 
        }else{
            $shipOrderId = $ebay_id;
        }
        //得到发货单明细
        $order_records   = WhOrderPartionRecordsModel::get_OrderPartionRecords($shipOrderId);
        if(empty($order_records)){
        	self::$errCode = 0;
			self::$errMsg  = "此跟踪号/发货单号还没有进行分区！";
            return false;
        }
        if($order_records['packageId'] != $package_id){
            self::$errCode = 0;
			self::$errMsg  = "此跟踪号/发货单号应该在{$order_records['packageId']}口袋编号中！";
            return false; 
        }
        if($order_records['partion'] != $partion){
            self::$errCode = 0;
			self::$errMsg  = "此跟踪号/发货单号没有在{$partion}分区中！";
            return false; 
        }
       	$where = "where id={$shipOrderId}";
		$order = orderPartionModel::selectOrder($where);
		if(!$order){
			self::$errCode = 0;
			self::$errMsg  = "此发货单不存在！";
			return false;
		}
        if($order[0]['orderStatus'] != PKS_DISTRICT_CHECKING){
           	self::$errCode = 0;
			self::$errMsg  = "此发货单状态不是在待分区复核！";
			return false; 
        }
        $review_list =  WhWaveOrderPartionScanReviewModel::get_reviewById($shipOrderId);
        if(!empty($review_list)){
            self::$errCode = 0;
			self::$errMsg  = "此发货单已经分区复核过了！";
			return false;
        }else{
            WhBaseModel::begin();
            $data = array(
            'shipOrderId'   => $shipOrderId,
            'partion'       => $partion,
            'userId'        => $userId,
            'scantime'      => time(),
            'packageId'     => $package_id
            );
            $result = WhWaveOrderPartionScanReviewModel::insert($data);
            if($result){
                $update = WhWaveOrderPartionScanReviewModel::updateOrderRecords($shipOrderId,$userId);
                if(!$update){
                    self::$errCode = '206';
  	                self::$errMsg  = '更新操作记录失败,请联系负责人!'; 
                    WhBaseModel::rollback();
                    return false;
                }
               //更新发货表状态
                $ostatus = WhWaveOrderPartionScanReviewModel::updateShippingOrderStatus($shipOrderId,$status=PKS_PRINT_SHIPPING_INVOICE);
       	        if(!ostatus){
  	                self::$errCode = 608;
			        self::$errMsg  = "更新发货单状态失败！";
			        WhBaseModel :: rollback();
			        return false;
    	        }
                WhPushModel::pushOrderStatus($shipOrderId,'PKS_PRINT_SHIPPING_INVOICE',$_SESSION['userId'],time());        //状态推送，需要改为待打印面单（订单系统提供状态常量）		
    
            }else{
                self::$errCode       = '206';
  	            self::$errMsg        = '该订单分区复核失败,请联系负责人!'; 
                WhBaseModel::rollback();
                return false;
            }
                     
            self::$errCode       = '200';
            self::$errMsg        = '该订单分区正确,请复核下一订单!';            
            WhBaseModel::commit(); 
            return true; 
        }
    }

    /**
     * pda_partitionCheckingAct::act_comparison()
     *    对发货单分区和发货单复核的数量进行对比
     * @author cxy
     * @return
     */
    public function act_comparison(){
        $userId 	= $_SESSION['userId'];
        $package_id = intval(trim($_POST['package_id']));
		if(empty($userId)){
            self::$errCode = '0';
            self::$errMsg  = '系统登录超时,请先关闭浏览器 然后登录扫描!!';
            return false;
        }
        if(empty($package_id)){
            self::$errCode = '206';
  	        self::$errMsg  = '请扫描分区的口袋编号!'; 
            return false;
        }
        //分区复核的总数
        $review_count  = WhWaveOrderPartionScanReviewModel::get_countReview($package_id);
        $review_count  = $review_count['mun'];
       // 发货单分区的总数
        $partion_count = WhOrderPartionRecordsModel::get_partionCount($package_id);
        $partion_count = $partion_count['mun'];
        if($review_count != $partion_count){
            if($review_count < $partion_count){
                $counts        = $partion_count-$review_count;
                self::$errCode = '206';
  	            self::$errMsg  = '该口袋编号的分区发货数量单比分区复核的数量多'.$counts; 
                return false;
            }else{
                $counts        = $review_count-$partion_count; 
                self::$errCode = '206';
  	            self::$errMsg  = '该口袋编号的分区发货数量单比分区复核的数量少'.$counts; 
                return false;
            }
        }else{
            self::$errCode       = '200';
  	        self::$errMsg        = '该口袋编号的分区发货单与分区复核的数量符合!'; 
            $res['review_total'] = $partion_count;
            return $res;
        }
    }
    
}
?>