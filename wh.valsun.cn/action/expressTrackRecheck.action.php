<?php

/** 
 * @author 涂兴隆
 * 快递跟踪号复核
 */
class ExpressTrackRecheckAct
{

    public static $errCode = 0;

    public static $errMsg = '';

    /**
     * 构造函数
     */
    function __construct ()
    {}
    
    /*
     * 处理提交结果
     */
    public function act_handelSubmit ()
    {
        $expressdata = null;
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            // 以post方式提交的数据为 一个订单有多个跟踪号的情况
            $orderid = trim($_POST['orderid']);
            $expressid = trim($_POST['expressid']);
            $expressid = trim($_POST['expressid'], "\n");
            $exidar = explode("\n", $expressid);
            $len = count($exidar);
            for ($i = 0; $i < $len; $i ++) { // 去除中间的空白
                $temp = trim($exidar[$i]);
                if (empty($temp)) {
                    unset($exidar[$i]);
                }
            }
            if (empty($exidar)) {
                // 提交的是空白
                self::$errCode = 0;
                self::$errMsg = '快递号为空!';
                return ;
            }
            $expressdata = $exidar;
            // $expressid = implode('和', $exidar);
        } else {
            $orderid = trim($_GET['orderid']);
            $expressid = trim($_GET['expressid']);
            $expressdata = $expressid;
        }
        if ($orderid < 1 || empty($orderid)) { // 不合法的id
            self::$errCode = 0;
            self::$errMsg = '单号不合法!';
            return;
        }
        if (empty($expressdata)) {
            self::$errCode = 0;
            self::$errMsg = '追踪号码不能为空！';
            return;
        }
        
        $po_obj = new PackingOrderModel();
        $orderinfo = $po_obj->getOrderInfoById($orderid);
        if(empty($orderinfo)){
            self::$errCode = 0;
            self::$errMsg = '单号不正确';
            return ;
        }
        
        if($orderinfo['orderStatus'] != PKS_EX_TNRCK){  //该订单不在快递待复核状态
            self::$errCode = 0;
            self::$errMsg ='该发货单不在快递待复核组！';
            return ;
        }
        
        $tir_obj = new TrackInfoRecordModel();
        $result = $tir_obj->validataTracnumber($orderid, $expressdata, 1);
        self::$errCode = TrackInfoRecordModel::$errCode;
        self::$errMsg = TrackInfoRecordModel::$errMsg;
        return ;
    }
}

?>