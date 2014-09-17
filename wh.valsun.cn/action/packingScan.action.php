<?php

/** 
 * @author 涂兴隆
 * 包装扫描
 * 
 */
class PackingScanAct
{

    public static $errCode = 0;

    public static $errMsg = '';

    /**
     * 构造函数
     */
    function __construct ()
    {
    	
    }
    
    /*
     * 包装扫描
     */
    public function act_packingscan()
    {
        $orderid = isset($_GET['orderid']) ? intval($_GET['order']) : 0;
        if (empty($orderid)) {
            self::$errCode = 0;
            self::$errMsg = '请输入订单号!';
            return;
        }
        
        $po_obj = new PackingOrderModel();
        $orderinfo = $po_obj->getOrderInfoById($orderid);
        if (empty($orderinfo)) {
            self::$errCode = 0;
            self::$errMsg = '该单号不存在！';
            return;
        }
        
        if ($orderinfo['orderStatus'] != PKS_WPACKING) { // 该订单不在待包装状态
            self::$errCode = 0;
            self::$errMsg = '该发货单不在待包装组！';
            return;
        }
        
        self::$errCode = 1;
        self::$errMsg = 'Ok!';
        return ;
    }
    
}

?>