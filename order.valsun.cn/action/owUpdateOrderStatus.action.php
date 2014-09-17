<?php
/*
* 海外仓同步订单
*/
class OwUpdateOrderStatusAct extends Auth{
    public static $errCode = 0;
    public static $errMsg = '';
    private $allowedStatus   = array(
    	array('911', '910'),
        array('911', '115'),
        array('911', '916'),
        array('911', '917'),
        array('911', '2004'),
        array('911', '926'),
        array('911', '925'),
        array('911', '924'),
        array('911', '923'),
        array('911', '922'),
        array('911', '921'),
        array('911', '920'),
        array('911', '303'),
        array('911', '928')
    );
    
    /*
     * 构造函数
     */
    public function __construct() {
    }
	
    public function act_updateOrderStatus(){
        $returnData = array('code'=>0, 'Msg'=>'');
        $orderId    = isset($_GET['orderId'])   ? $_GET['orderId']  : FALSE;
        $status_1   = isset($_GET['status_1'])  ? $_GET['status_1'] : FALSE;
        $status_2   = isset($_GET['status_2'])  ? $_GET['status_2'] : FALSE;
         //print_r($_GET);exit;
        if ( FALSE===$orderId || FALSE === $status_1 || FALSE === $status_2) {
            $returnData['code']    = 0;
            $returnData['Msg']     = '参数不完整!';
            return $returnData;
        }
        $orderMg    = new OwOrderManageModel();
        $orderInfo  = $orderMg->getOrderInfoById($orderId, array('orderStatus', 'orderType'));
        if (FALSE === $orderInfo) {
            $returnData['code']    = 0;
            $returnData['Msg']     = '不存在的订单!';
            return $returnData;
        }
        
        $origin_Lev1    = $orderInfo['orderStatus'];
        $origin_Lev2    = $orderInfo['orderType'];
        
        $checkResult    = $this->allowedStatusCheck(array('lev1'=>$status_1, 'lev2'=>$status_2));
        if (FALSE === $checkResult) {
        	$returnData['code']    = 0;
            $returnData['Msg']     = '不允许设置为该状态!';
            return $returnData;
        }
        
        $result = $orderMg->changeOrderStatus($status_1, $status_2, $orderId);
        if ($result) {
            $eventResult    = $this->eventDispatch(array('lev1'=>$status_1, 'lev2'=>$status_2, 'orderId'=>$orderId));
            OrderLogModel::orderLog($orderId, '', "原始状态 -- $origin_Lev1 | $origin_Lev2  更改后状态 -- $status_1 | $status_2");   //记录日志
            if ($eventResult) {
                $returnData['code']    = 1;
                $returnData['Msg']     = '更新成功!';
            } else {
                $returnData['code']    = 0;
                $returnData['Msg']     = '转移订单信息出错!';
            }
            return $returnData;
        } else {
            $returnData['code']    = 0;
            $returnData['Msg']     = '更新失败!';
            return $returnData;
        }
    }
    
    /*
     * 验证是否可以设置为某个状态
     */
    public function allowedStatusCheck($status){
        $lev1   = $status['lev1'];
        $lev2   = $status['lev2'];
        $inputstr   = $lev1 . $lev2;
        $combinStr  = array();
        foreach ($this->allowedStatus as $tuple){
            $combinStr[]    = $tuple[0].$tuple[1];
        }
        if (in_array($inputstr, $combinStr)){
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    /*
     * 调用相应的事件处理函数
     * $event array('lev1'=> 一级状态， 'lev2'=>二级状态, 'orderId'=>订单号)
     */
    public function eventDispatch($event){
        $lev1       = $event['lev1'];
        $lev2       = $event['lev2'];
        $orderId    = $event['orderId'];
        if ( (911==$lev1) || (921==$lev2) ) {                                       //已发货 触发转移事件
            $moveResult = OrderindexModel::shiftOrderList(" where id='$orderId'");
            return $moveResult;
        } else {                                                                    //未找到则默认返回true
            return true;
        }
    }
}
