<?php

/** 
 * 异常订单扫描
 * @author 王长先
 */
class DispatchBillScanAct extends Auth
{
    static $errCode	=	0;
    static $errMsg	=	"";
    /**
     * 构造函数
     */
    function __construct ()
    {
         
    }
    /*
     * 将缺货订单改为异常订单
    */
    public function act_changeOutOfStockToDispathBill(){
        $orderids = $_REQUEST['orderids'];
        if(empty($orderids)){
            self::$errCode="001";
            self::$errMsg="发货单号为空！";
            return false;
        }

		if(!is_numeric($orderids)){
			$tracknumber = $orderids;
			$info = orderWeighingModel::selectOrderId($tracknumber);
			if(!$info){
				self::$errCode = 501;
				self::$errMsg = "此跟踪号不存在！";
				return false;
			}
			$orderids = $info[0]['shipOrderId'];
			
		}
		
        $orderStatus    =   ShippingOrderModel::getShippingOrder("orderStatus"," where id='$orderids'");
        $orderStatus    =   $orderStatus[0]['orderStatus'];
        if($orderStatus=='402'||$orderStatus=='703'){
            $changeStatus    =   new printAct();
            $changeStatus->act_markUnusual1();
            if(empty($ret)||$changeStatus::$errCode!='200'){
                self::$errCode  =   $changeStatus::$errCode;
                self::$errMsg   =   $changeStatus::$errMsg;
                return false;//失败
            }else{
                self::$errCode  =   $changeStatus::$errCode;
                self::$errMsg   =   $changeStatus::$errMsg;
                return true;
            }
        }
        self::$errCode  =   "002";
        self::$errMsg   =   "非待配货!";
        return false;
    }
    
}