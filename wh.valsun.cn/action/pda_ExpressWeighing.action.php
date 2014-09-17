<?php
/**
*
*功能：快递称重
*作者：陈先钰
*2014-8-18
*/
class pda_ExpressWeighingAct extends Auth{
	public static $errCode = 0;
    public static $errMsg = '';
    /*
     * 构造函数
     */
    public function __construct() {
        
    }
    //对发货单号进行判断
    /**
     * pda_ExpressWeighingAct::act_pda_ExpressId()
     * @author cxy
     * @return
     */
    public function act_pda_ExpressId(){
        $userId  = $_SESSION['userId'];
        $ebay_id = trim($_POST['ebay_id']);
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
        $where = "where id={$ebay_id}";
		$order = orderPartionModel::selectOrder($where);
		if(!$order){
			self::$errCode = 0;
			self::$errMsg  = $ebay_id.'发货单不存在！';
			return false;
		}
        if($order[0]['isExpressDelivery']!= 1){
            self::$errCode = 0;
			self::$errMsg  = $ebay_id.'此发货单不是快递发货单！';
			return false;   
        }
        if($order[0]['orderStatus'] != PKS_WWEIGHING){
           	self::$errCode = 0;
			self::$errMsg  = $ebay_id.'此发货单状态不是在待称重状态！';
			return false; 
        }
        $result         = OmAvailableModel::getTNameList("wh_shipping_order_note_record ","content","where shipOrderId='$ebay_id'  and is_delete =0 ");
        $note           = $result[0]['content'];
        $res['content'] = $note;
    	self::$errCode  = 200;
		self::$errMsg   = $ebay_id."发货单号正确，请填写该发货单的重量，注意填写为的重量单位为KG！";
		return $res; 
    }
    //对重量进行判断
    /**
     * pda_ExpressWeighingAct::act_pda_ExpressWeighing()
     * @author cxy
     * @return
     */
    public function act_pda_ExpressWeighing(){
        $userId  = $_SESSION['userId'];
        $ebay_id = trim($_POST['ebay_id']);
        $weight  = trim($_POST['weighing']);
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
        if ($weight <= 0) {
            self::$errCode = 0;
            self::$errMsg  = '请输入正确的重量！';
            return;
        }
       		
        self::$errCode = 200;
        self::$errMsg  = '称重成功，请填写下箱子数量和跟踪号数量';              
        return true;
    }
    /**
     * pda_ExpressWeighingAct::act_boxCount()
     * 判断箱子的数量
     * @return
     */
    public function act_boxCount(){
        $userId         = $_SESSION['userId'];
        $ebay_id        = trim($_POST['ebay_id']);
        $count_box      = trim(intval($_POST['count_box']));
        $weighing = trim($_POST['weighing']);
        $weighing =$weighing*1000;
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
        if ($count_box < 1) {
            self::$errCode = 0;
            self::$errMsg  = '请输入正确的箱子数量，最小是2！';
            return false;
        }
        if($count_box == 1){
            WhBaseModel::begin();
            $status = PKS_PRINT_SHIPPING_INVOICE;
            $result = WhShippingOrderModel::update_shipping_order_by_id("id = '{$ebay_id}' and is_delete = 0","orderStatus = '{$status}',orderWeight = '{$weighing}'");
            if(!$result){
                WhBaseModel::rollback();
                self::$errCode = 0;
                self::$errMsg  = '称重添加失败，请联系负责人';
                return false;
            }
            $msg = orderWeighingModel::insertRecord($ebay_id,$userId);
    		if(!$msg){
    			self::$errCode = 511;
    			self::$errMsg  = "插入称重记录失败！";
    			WhBaseModel:: rollback();
    			return false;
    		}
            $msg_update = orderWeighingModel::updateRecord($ebay_id,$weighing,$userId);
    		if(!$msg_update){
    			self::$errCode = 512;
    			self::$errMsg  = "更新操作记录表失败！";
    			WhBaseModel :: rollback();
    			return false;
    		}        
            WhPushModel::pushOrderStatus($ebay_id,'PKS_PRINT_SHIPPING_INVOICE',$_SESSION['userId'],time());   //状态推送，需要改为待打印面单（订单系统提供状态常量）
            WhBaseModel::commit();
            self::$errCode  = 20;
            self::$errMsg   = '操作成功，输入箱子数量为1，则不用填写跟踪号数量,请扫描下一个发货单号！';
            return true;
        }else{
            self::$errCode  = 200;
            self::$errMsg   = '输入箱号成功，请输入跟踪号数量！';
            return $count_box;
        }
    }
    /**
     * pda_ExpressWeighingAct::act_trackingCount()
     * @author cxy 
     *快递箱子和跟踪号数量添加到数据库
     * @return
     */
    public function act_trackingCount(){
        $userId         = $_SESSION['userId'];
        $ebay_id        = trim($_POST['ebay_id']);
        $count_box      = trim(intval($_POST['count_box']));
        $tracking_count = trim(intval($_POST['tracking']));
        $weighing = trim($_POST['weighing']);
         $weighing =$weighing*1000;
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
        if ($count_box <= 1) {
            self::$errCode = 0;
            self::$errMsg  = '请输入正确的箱子数量，最小是2！';
            return false;
        }
        if($tracking_count<1){
            self::$errCode  = 0;
            self::$errMsg   = '请输入正确的跟踪号数量，最小是1！';
            return false;
        }
        if ($weighing <= 0) {
            self::$errCode = 0;
            self::$errMsg  = '请输入正确的重量！';
            return;
        }
        
   	    $where = "where id={$ebay_id}";
		$order = orderPartionModel::selectOrder($where);
		if($order[0]['transportId']==48){//顺丰快递的跟踪号值能是一个
            $tracking_count = 1;
		}else{
		  if($tracking_count != $count_box){
		     self::$errCode = 0;
             self::$errMsg  = '跟踪号数量与箱子数量不相等！';
             return;
		  }
		}
        //逻辑删除以前称重的记录
        $update = WhWaveTrackingBoxModel::update(array('is_delete'=>1),array('shipOrderId'=>$ebay_id));
        $data =array(
            'boxCount'        => $count_box,
            'trackingCount'   => $tracking_count,
            'shipOrderId'     => $ebay_id,
            'weighTime'       => time()
        );
        $result = WhWaveTrackingBoxModel::insert($data);
        if($result){
            WhBaseModel::begin();
            $status = PKS_PRINT_SHIPPING_INVOICE;
            $result = WhShippingOrderModel::update_shipping_order_by_id("id = '{$ebay_id}' and is_delete = 0","orderStatus = '{$status}',orderWeight = '{$weighing}'");
            if(!$result){
                WhBaseModel::rollback();
                self::$errCode = 0;
                self::$errMsg  = '称重添加失败，请联系负责人';
                return false;
            }
            $msg = orderWeighingModel::insertRecord($ebay_id,$userId);
    		if(!$msg){
    			self::$errCode = 511;
    			self::$errMsg  = "插入称重记录失败！";
    			WhBaseModel:: rollback();
    			return false;
    		}
            $msg_update = orderWeighingModel::updateRecord($ebay_id,$weighing,$userId);
    		if(!$msg_update){
    			self::$errCode = 512;
    			self::$errMsg  = "更新操作记录表失败！";
    			WhBaseModel :: rollback();
    			return false;
    		}        
            WhPushModel::pushOrderStatus($ebay_id,'PKS_PRINT_SHIPPING_INVOICE',$_SESSION['userId'],time());   //状态推送，需要改为待打印面单（订单系统提供状态常量）
            WhBaseModel::commit();
            self::$errCode = 200;
            self::$errMsg  = '操作成功，请称重下一个发货单号';  
            return true;
        }else{
            self::$errCode = 211;
            self::$errMsg  = '操作失败，请联系IT部门';  
            return false;
        }
    }
    
}      