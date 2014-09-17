<?php
/**
*
*功能：快递跟踪号绑定
*作者：陈先钰
*2014-9-3
*/
class pda_TrackingAct extends Auth{
	public static $errCode = 0;
    public static $errMsg = '';
    /*
     * 构造函数
     */
    public function __construct() {
        
    }
    //对发货单号进行判断
    /**
     * 
     * @author cxy
     * @return
     */
    public function act_pda_Tracking(){
        $userId  = $_SESSION['userId'];
        $ebay_id = trim($_POST['ebay_id']);
	    if(empty($userId)){
            self::$errCode = '0';
            self::$errMsg  = '系统登录超时,请先关闭浏览器 然后登录扫描!!';
            return false;
        }
        if (empty($ebay_id)) {
            self::$errCode = 0;
            self::$errMsg = '请填写单号！';
            return;
        }
        $where = "where id={$ebay_id}";
		$order = orderPartionModel::selectOrder($where);
		if(!$order){
			self::$errCode = 0;
			self::$errMsg  = $ebay_id.'发货单不存在！';
			return false;
		}
        if($order[0]['orderStatus'] != PKS_PRINT_SHIPPING_INVOICE){
           	self::$errCode = 0;
			self::$errMsg  = $ebay_id.'此发货单状态不是在待打印面单状态！';
			return false; 
        }
         $array = array(
          'shipOrderId' => $ebay_id,
          'is_delete'   => 0
        );
         //根据发货单号获取快递需要的箱子和跟踪号数量
        $result_tracking_count = WhWaveTrackingBoxModel::select_by_shipOrderId($ebay_id);
        $count_binding         = WhOrderTracknumberModel::count($array);//发货单已经绑定跟踪号的数量
        if(empty($result_tracking_count)){
            if($count_binding >0){
                self::$errCode = 211;
		        self::$errMsg  = $ebay_id."该发货单已经绑定好跟踪号了！";
		        return true; 
            }
        }else{            
            $result_tracking_count = $result_tracking_count['trackingCount'];//发货单需要绑定跟踪号的数量
            if($result_tracking_count <= $count_binding){
                self::$errCode = 212;
		        self::$errMsg  = $ebay_id."该发货单已经绑定好跟踪号了！";
		        return true; 
            }
        }
    	self::$errCode = 200;
		self::$errMsg  = $ebay_id."发货单号正确，请扫描该发货单的跟踪号，注意跟踪号的数量！";
		return true; 
    }

    /**
     * pda_TrackingAct::act_tracking_binding()
     * 快递绑定跟踪号
     * @author cxy
     * @return
     */
    public function act_tracking_binding(){
        $userId   = $_SESSION['userId'];
        $ebay_id  = trim($_POST['ebay_id']);
        $tracking =trim($_POST['tracking']);
	    if(empty($userId)){
            self::$errCode = '0';
            self::$errMsg  = '系统登录超时,请先关闭浏览器 然后登录扫描!!';
            return false;
        }
        if (empty($ebay_id)) {
            self::$errCode = 0;
            self::$errMsg  = '请填写单号！';
            return;
        }
        if (empty($tracking)) {
            self::$errCode = 0;
            self::$errMsg  = '请输入正确的跟踪号！';
            return;
        }
        //查询扫描的跟踪号是否已经扫描过了的
        $result_select     = WhOrderTracknumberModel::select_ByTracknumber($tracking);
        if($result_select){
            self::$errCode = 211;
            self::$errMsg  = '该跟踪号已经绑定'.$result_select['shipOrderId'].'，请检查！';
            return false; 
        }
        $data = array(
            'tracknumber' => $tracking,
            'shipOrderId' => $ebay_id,
            'createdTime' => time()
        );
        $array = array(
            'shipOrderId' => $ebay_id,
            'is_delete'   => 0
        );
      //根据发货单号获取快递需要的箱子和跟踪号数量
        $result_tracking_count = WhWaveTrackingBoxModel::select_by_shipOrderId($ebay_id);
         //说明该快递单只有一个箱子和一个跟踪号，所以不需要在wh_wave_tracking_box "快递单号的箱子与跟踪号数量表"添加记录
        if(empty($result_tracking_count)){
            $count_binding = WhOrderTracknumberModel::count($array);//发货单已经绑定跟踪号的数量
            if($count_binding > 0){
                self::$errCode = 24;
                self::$errMsg  = '该发货单号已经绑定好跟踪号了，不在需要绑定';
                return false;
            }
            WhBaseModel::begin();
            $result_insert = WhOrderTracknumberModel::insert($data);
            if(!$result_insert){
                WhBaseModel::rollback();
                self::$errCode = 20;
                self::$errMsg  = '跟踪号绑定失败，请联系负责人';
                return false;
            }
            //更新发货表状态
            $ostatus = WhWaveOrderPartionScanReviewModel::updateShippingOrderStatus($ebay_id,$status=PKS_WAITING_LOADING);
            if(!$ostatus){
                WhBaseModel::rollback();
                self::$errCode = 20;
                self::$errMsg  = '更新发货单状态失败，请联系负责人';
                return false;
            }
            WhPushModel::pushOrderStatus($ebay_id,'PKS_WAITING_LOADING',$_SESSION['userId'],time());   //状态推送，需要改为待装车扫描（订单系统提供状态常量）		                     
            WhBaseModel::commit();
            self::$errCode = 200;
            self::$errMsg  = '绑定成功，请扫描另外一个发货单！';
            return true;
        }else{ 
            $result_tracking_count = $result_tracking_count['trackingCount'];//发货单需要绑定跟踪号的数量
            $count_binding = WhOrderTracknumberModel::count($array);//发货单已经绑定跟踪号的数量
            if($result_tracking_count > $count_binding){
                 WhBaseModel::begin();
                $result_insert = WhOrderTracknumberModel::insert($data);
                if(!$result_insert){
                    WhBaseModel::rollback();
                    self::$errCode = 21;
                    self::$errMsg  = '跟踪号绑定失败，请联系负责人';
                    return false;
                }
                $num = $result_tracking_count - $count_binding - 1 ;
                if($num ==0){
                     //更新发货表状态
                    $ostatus = WhWaveOrderPartionScanReviewModel::updateShippingOrderStatus($ebay_id,$status=PKS_WAITING_LOADING);
                    if(!$ostatus){
                        WhBaseModel::rollback();
                        self::$errCode = 20;
                        self::$errMsg  = '更新发货单状态失败，请联系负责人';
                        return false;
                    }
                    WhPushModel::pushOrderStatus($ebay_id,' ',$_SESSION['userId'],time());   //状态推送，需要改为待装车扫描（订单系统提供状态常量）		                    
                    WhBaseModel::commit();
                    self::$errMsg  = '绑定成功，该发货单不需要绑定跟踪号了'; 
                    self::$errCode = 200;
                    return true;                   
                }else{      
                    WhBaseModel::commit();
                    self::$errMsg  = '绑定成功，该发货单还需要绑定'.$num.'个跟踪号';
                    self::$errCode = 400;
                    return true;  
                }
            }else{
                self::$errCode = 22;
                self::$errMsg  = '绑定失败，绑定的跟踪号已经够'.$result_tracking_count.'个，请扫描另外一个发货单！';
                return true; 
            }
        }
        
    }  
}      