<?php

/** 
 * @author 涂兴隆
 * 称重扫描
 */
class WeighingScanAct
{

    public static $errCode = 0;

    public static $errMsg = '';

    /**
     * 构造函数
     */
    function __construct ()
    {}
    
    /*
     * 验证是否和合法的单号
     */
    public function act_isOrderValide ()
    {
        $orderid = isset($_GET['orderid']) ? intval($_GET['orderid']) : 0;
        if (empty($orderid)) {
            self::$errCode = 0;
            self::$errMsg = '请填写单号！';
            return;
        }
        
        $po_obj = new PackingOrderModel();
        $orderinfo = $po_obj->getOrderInfoById($orderid);
        if (empty($orderinfo)) {
            self::$errCode = 0;
            self::$errMsg = '单号不存在!';
            return;
        }
        
        if ($orderinfo['orderStatus'] != PKS_WWEIGHING_EX) { // 该订单不在快递待称重状态
            self::$errCode = 0;
            self::$errMsg = '该发货单不在待称重组！';
            return;
        }
		
		$mes = 'ok!';
		if ($orderinfo['orderTypeId'] == 2) { // 配货单验证是否全部配货完成
			$isLast  = printLabelModel::adjustIsLast($orderinfo['id']);
			$doneStr = printLabelModel::getAllOriginOrderId($orderinfo['id']);
			
			if($isLast&&empty($doneStr)){
				$mes = "第一个包裹,全部打印,请输入重量";
			}elseif($isLast&&!empty($doneStr)){
				$mes = "分包裹,全部打印!该订单和[".$packInfo."]为同一订单,请输入重量";
			}elseif(!$isLast&&empty($doneStr)){
				$mes = "第一个包裹，部分打印";
				self::$errCode = 0;
				self::$errMsg  = $mes;
				return;
			}else{
				$mes = "分包裹,部分打印!该订单和[".$packInfo."]为同一订单";
				self::$errCode = 0;
				self::$errMsg  = $mes;
				return;
			}
        }

        self::$errCode = 1;
        self::$errMsg = $mes;
        return;
    }
    
    /*
     * 验证是否和合法的单号 （芬哲）
    */
    public function act_isOrderValideFZ ()
    {
        $orderid = isset($_GET['orderid']) ? intval($_GET['orderid']) : 0;
        if (empty($orderid)) {
            self::$errCode = 0;
            self::$errMsg = '请填写单号！';
            return;
        }
    
        $po_obj = new PackingOrderModel();
        $orderinfo = $po_obj->getOrderInfoById($orderid);
        if (empty($orderinfo)) {
            self::$errCode = 0;
            self::$errMsg = '单号不存在!';
            return;
        }
    
        if ($orderinfo['orderStatus'] != PKS_WWEIGHING_EX) { // 该订单不在国内快递待称重核状态
            self::$errCode = 0;
            self::$errMsg = '该发货单不在待称重组！';
            return;
        }
        self::$errCode = 1;
        self::$errMsg = 'ok';
        return;
    }
    
    /*
     * 提交包裹重量数据
     */
    public function act_weighingSubmit ()
    {
        $weight = isset($_POST['num']) ? floatval($_POST['num']) : 0;
        if ($weight <= 0) {
            self::$errCode = 0;
            self::$errMsg = '请输入正确的重量！';
            return;
        }
        
        $weight = $weight * 1000;
        $weight = round($weight);
        
       // $packinguser = isset($_POST['userid']) ? intval($_POST['userid']) : 0;
        $packinguser	= $_SESSION['userId'];
        $orderid = isset($_POST['orderid']) ? intval($_POST['orderid']) : 0;
        if (empty($orderid)) {
            self::$errCode = 0;
            self::$errMsg = '请填写单号！';
            return;
        }
        
        $po_obj = new PackingOrderModel();
        $orderinfo = $po_obj->getOrderInfoById($orderid);
        if (empty($orderinfo)) {
            self::$errCode = 0;
            self::$errMsg = '单号不存在!';
            return;
        }
        
        if ($orderinfo['orderStatus'] != PKS_WWEIGHING_EX) { // 该订单不在称重核组
            self::$errCode = 0;
            self::$errMsg = '该发货单不在称重核组！';
            return;
        }
        
        $data = array(
                'weight' => $weight,
                'userid' => $packinguser,
                'orderid' => $orderid,
                'status' => PKS_EX_TNRCK   
        );
        $result = $po_obj->recordWeightInfo($data);
        if($result){    //成功
            self::$errCode = 1;
            self::$errMsg = 'OK';
            return ;
        }else {
            self::$errCode = 0;
            self::$errMsg = '操作失败!';
            return ;
        }
    }
    

    /*
     * 称重扫描 （芬哲）
    */
    public function act_weighingSubmitFZ ()
    {
        $weight = isset($_POST['num']) ? floatval($_POST['num']) : 0;
        if ($weight <= 0) {
            self::$errCode = 0;
            self::$errMsg = '请输入正确的重量！';
            return;
        }
    
       // $weight = $weight * 1000;
        $weight = round($weight);
    
        $packinguser = isset($_POST['userid']) ? intval($_POST['userid']) : 0;
        if ($packinguser <= 1) {
            self::$errCode = 0;
            self::$errMsg = '请输入配货人员！';
            return;
        }
    
        $orderid = isset($_POST['orderid']) ? intval($_POST['orderid']) : 0;
        if (empty($orderid)) {
            self::$errCode = 0;
            self::$errMsg = '请填写单号！';
            return;
        }
        
        $expressstr = isset($_POST['express']) ? trim($_POST['express']) : '';
        if (empty($expressstr)) {
            self::$errCode = 0;
            self::$errMsg = '请填写快递单号！';
            return;
        }
    
        $po_obj = new PackingOrderModel();
        $orderinfo = $po_obj->getOrderInfoById($orderid);
        if (empty($orderinfo)) {
            self::$errCode = 0;
            self::$errMsg = '单号不存在!';
            return;
        }
    
        if ($orderinfo['orderStatus'] != PKS_WWEIGHING_EX) { // 该订单不在国内称重状态
            self::$errCode = 0;
            self::$errMsg = '该发货单不在国内称重组！';
            return;
        }
    
        $data = array(
                'weight' => $weight,
                'userid' => $packinguser,
                'orderid' => $orderid,
                'status' => PKS_DONE,
                'express'=>$expressstr,
                'storid'=>1
        );
        $result = $po_obj->recordWeightInfoFZ($data);
        if($result){    //成功
			WhPushModel::pushOrderStatus($orderid,'STATEHASSHIPPED',$_SESSION['userId'],time(),$weight);        //状态推送
            self::$errCode = 1;
            self::$errMsg = 'OK';
            return ;
        }else {
            self::$errCode = 0;
            self::$errMsg = '操作失败!';
            return ;
        }
    }
}

?>