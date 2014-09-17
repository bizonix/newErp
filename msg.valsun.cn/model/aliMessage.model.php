<?php
/*
 * 速卖通message相关
 */
class AliMessageModel {
    public static $errMsg   = '';
    public static $errCode  = 0;
    private $dbconn;
    /*
     * 构造函数
     */
    public function __construct(){
        global $dbConn;
        $this->dbconn   = $dbConn;
    }
    
    public static function orderStatusToStr($statusCode){
        $map    = array(
        	'PLACE_ORDER_SUCCESS'       => '待付款',
            'RISK_CONTROL'              => '资金未到账',
            'WAIT_SELLER_SEND_GOODS'    => '等待卖家发货',
            'WAIT_BUYER_ACCEPT_GOODS'   => '等待买家收货',
            'FINISH'                    => '已结束的订单',
            'SELLER_PART_SEND_GOODS'    => '部分发货',
            'WAIT_SELLER_EXAMINE_MONEY' => '等待您确认金额',
            'IN_ISSUE'                  => '含纠纷订单',
            'IN_FROZEN'                 => '冻结中',
            'IN_CANCEL'                 => '买家申请取消'
        );
        if (array_key_exists($statusCode, $map)) {
        	return $map[$statusCode];
        } else {
            return $statusCode;
        }
    }
}
