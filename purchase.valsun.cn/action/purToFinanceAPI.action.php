<?php
/**
 * 类名: PurToFinanceAPIAct
 * 功能：采购系统推送数据到财务系统交互业务逻辑类,推送数据到财务系统
 * 版本：1.0 
 * 日期：2014-03-31
 * 作者：王民伟
 */
class PurToFinanceAPIAct {
    public static $errCode	 = 0;
  	public static $errMsg	   = "";
    //结款订单信息返回确认
    public static function getEndPayOrder(){
        $data     = $_POST['dataArr'];
        $rtnData  = array();
        if(!empty($data)){
          $rtnData = PurToFinanceAPIModel::getEndPayOrder($data);
        }
        return json_encode($rtnData);
    }

    //推送结款订单号到财务软件
    public static function pushEndPayOrder(){
        $orderArr       = $_POST['ordersn'];
        $ordersn        = substr($orderArr, 0, strlen($orderArr) - 1);
        $note           = $_POST['note'];
        $paramArr   = array(
            'method'    => 'purtofinance.endPayMoney',  //API名称
            'format'    => 'json',  //返回格式
            'v'         => '1.0',   //API版本号
            'username'  => 'purchase',
            'ordersn'   => $ordersn,
            'note'      => $note
        );
        $rtnData    = callOpenSystem($paramArr, 'local');
        $rtn        = json_decode($rtnData, true);
        $rtnCode    = $rtn['rtnCode'];
        if($rtnCode == '1'){
            $rtnResult = PurToFinanceAPIModel::updEndPayOrderStatus($ordersn, 'pay');//申请结款成功,更新订单请款状态
            if($rtnResult){
                return $rtnData;
            }else{
                $result['rtnCode'] = '1001';
                $result['rtnMsg']  = 'updStatusFailure';
                $result['data']    = '申请结款成功,采购系统状态更新失败';
                return json_encode($result);
            }
        }else{
            return $rtnData;
        }
    }

    //全额预付订单信息返回确认
    public static function getPreAllPayOrder(){
        $data     = $_POST['dataArr'];
        $rtnData  = array();
        if(!empty($data)){
            $rtnData = PurToFinanceAPIModel::getPreAllPayOrder($data);
        }
        return json_encode($rtnData);
    }

    //推送全额预付订单号到财务软件
    public static function pushPreAllPayOrder(){
        $orderArr       = $_POST['ordersn'];
        $ordersn        = substr($orderArr, 0, strlen($orderArr) - 1);
        $note           = $_POST['note'];
        $paramArr   = array(
            'method'    => 'purtofinance.preAllPayMoney',  //API名称
            'format'    => 'json',  //返回格式
            'v'         => '1.0',   //API版本号
            'username'  => 'purchase',
            'ordersn'   => $ordersn,
            'note'      => $note
        );
        $rtnData    = callOpenSystem($paramArr, 'local');
        $rtn        = json_decode($rtnData, true);
        $rtnCode    = $rtn['rtnCode'];
        if($rtnCode == '1'){
            $rtnResult = PurToFinanceAPIModel::updEndPayOrderStatus($ordersn, 'preAllPay');//申请全额预付成功,更新订单请款状态
            if($rtnResult){
                return $rtnData;
            }else{
                $result['rtnCode'] = '1001';
                $result['rtnMsg']  = 'updStatusFailure';
                $result['data']    = '申请全额预付成功,采购系统状态更新失败';
                return json_encode($result);
            }
        }else{
            return $rtnData;
        }
    }

    //部份预付订单信息返回确认
    public static function getPrePartPayOrder(){
        $data     = $_POST['dataArr'];
        $rtnData  = array();
        if(!empty($data)){
            $rtnData = PurToFinanceAPIModel::getPrePartPayOrder($data);
        }
        return json_encode($rtnData);
    }

    //推送部分预付订单号到财务软件
    public static function pushPrePartPayOrder(){
        $orderArr       = $_POST['ordersn'];
        $ordersn        = substr($orderArr, 0, strlen($orderArr) - 1);
        $note           = $_POST['note'];
        $cate           = $_POST['cate'];
        $digitial       = $_POST['digitial'];
        $paramArr   = array(
            'method'    => 'purtofinance.prePartPayMoney',  //API名称
            'format'    => 'json',  //返回格式
            'v'         => '1.0',   //API版本号
            'username'  => 'purchase',
            'ordersn'   => $ordersn,
            'cate'      => $cate,//预付类型,百分比、金额
            'digitial'  => $digitial,//百分比、金额
            'note'      => $note
        );
        $rtnData    = callOpenSystem($paramArr, 'local');
        $rtn        = json_decode($rtnData, true);
        $rtnCode    = $rtn['rtnCode'];
        if($rtnCode == '1'){
            $rtnResult = PurToFinanceAPIModel::updPartPreOrderStatus($ordersn, $cate, $digitial);//申请部份预付成功,更新订单请款状态
            if($rtnResult){
                return $rtnData;
            }else{
                $result['rtnCode'] = '1001';
                $result['rtnMsg']  = 'updStatusFailure';
                $result['data']    = '申请部份预付成功,采购系统状态更新失败';
                return json_encode($result);
            }
        }
        return $rtnData;
    }

    //全额退款订单信息返回确认
    public static function getBackAllPayOrder(){
        $data     = $_POST['dataArr'];
        $rtnData  = array();
        if(!empty($data)){
            $rtnData = PurToFinanceAPIModel::getBackAllPayOrder($data);
        }
        return json_encode($rtnData);
    }

    //推送全额退款订单号到财务软件
    public static function pushBackAllOrder(){
         $orderArr      = $_POST['ordersn'];
        $ordersn        = substr($orderArr, 0, strlen($orderArr) - 1);
        $note           = $_POST['note'];
        $paramArr   = array(
            'method'    => 'purtofinance.returnAllMoney',  //API名称
            'format'    => 'json',  //返回格式
            'v'         => '1.0',   //API版本号
            'username'  => 'purchase',
            'ordersn'   => $ordersn,
            'note'      => $note
        );
        $rtnData    = callOpenSystem($paramArr, 'local');
        $rtn        = json_decode($rtnData, true);
        $rtnCode    = $rtn['rtnCode'];
        if($rtnCode == '1'){
            $rtnResult = PurToFinanceAPIModel::updEndPayOrderStatus($ordersn, 'backAllPay');//申请全额预付成功,更新订单请款状态
            if($rtnResult){
                return $rtnData;
            }else{
                $result['rtnCode'] = '1001';
                $result['rtnMsg']  = 'updStatusFailure';
                $result['data']    = '全额退款推送成功,采购系统状态更新失败';
                return json_encode($result);
            }
        }else{
            return $rtnData;
        }
    }
}
?>