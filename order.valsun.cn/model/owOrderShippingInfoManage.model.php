<?php
/*
 * 管理海外仓订单的运输方式信息
 */

class OwOrderShippingInfoManageModel {
    private $errCode    = 0;
    private $errMsg     = '';
    private $dbConn     = NULL;
    
    /*
     * 构造函数
     */
    function __construct() {
        global $dbConn;
        $this->dbConn   = $dbConn;
    }
    
    /*
     * 增加一条数据
     */
    public function addNewOne($data){
        $data           =  array_map('mysql_real_escape_string', $data);        //特殊字符过滤
        $orderID        = $data['orderId'];                         //订单号
        $shippingCode   = $data['shippingCode'];                    //运输方式代码
        $trackNumber    = $data['trackNumber'];                     //跟踪号
        $extensionInfo  = $data['extensionInfo'];                   //扩展信息
        $time           = time();
        $sql            = "insert into ow_order_shipping_info (orderid, shippingCode, trackNumber, extensionInfo, extensionInfo, updateTime) 
                            values ('$orderID', '$shippingCode', '$trackNumber', '$extensionInfo', '$time')";
        $result =  $this->dbConn->query($sql);
        if ($result) {
        	return  $this->dbConn->insert_id();;
        } else {
            return FALSE;
        }
    }
    
    /*
     * 更新一条数据
     */
    public function updateOneRow($data){
        $data           =  array_map('mysql_real_escape_string', $data);        //特殊字符过滤
        $orderID        = $data['orderId'];                         //订单号
        $shippingCode   = $data['shippingCode'];                    //运输方式代码
        $trackNumber    = $data['trackNumber'];                     //跟踪号
        $extensionInfo  = $data['extensionInfo'];                   //扩展信息
        $time           = time();
        $sql    = "update ow_order_shipping_info set shippingCode='$shippingCode', extensionInfo='$extensionInfo', 
                    trackNumber='$trackNumber', updateTime='$time' where orderid='$orderID'";
        $queryResult    = $this->dbConn->query($sql);
        if ($queryResult) {
        	return TRUE;
        } else {
            return FALSE;
        }
    }
    
    /*
     * 检测是否存在对应某个订单号的信息
     */
    public function checkOneRowExists($orderId){
        $sql    = "select id from ow_order_shipping_info where orderid=$orderId";
        $row    = $this->dbConn->fetch_first($sql);
        return $row ? TRUE : FALSE;
    }
    
}

?>