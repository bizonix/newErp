<?php
/*
 * 海外仓订单管理
 */
class OwOrderManageModel {
    public static $errCode  = 0;
    public static $errMsg   = '';
    private $dbConn = NULL;
    
    /*
     * 构造函数
     */
    public function __construct(){
        global $dbConn;
        $this->dbConn   = $dbConn;
    }
    
    /*
     * 改变某一个订单的状态 在unshiped表中
     * $orderType   一级状态
     * $orderStatus 二级状态
     * $orderId     订单号
     */
    public function changeOrderStatus($orderType, $orderStatus, $orderId) {
        $sql    = "update om_unshipped_order set orderStatus='$orderType', orderType='$orderStatus' where id='$orderId'";
//         echo $sql;exit;
        return $this->dbConn->query($sql);
    }
    
    /*
     * 更新一条跟踪号信息到跟踪号信息表中
     */
    public function insertNewTrackNumber($orderId, $trackNumber, $shippingCode, $user, $isCanceled){
        $trackNumber    = mysql_real_escape_string($trackNumber);
        $time           = time();
        $sql            = "REPLACE INTO ow_order_tracknumber (omOrderId, shippingWay, tracknumber, addUser, createdTime, isCanceled) 
                            values ( '$orderId', '$shippingCode', '$trackNumber', '$user', $time, $isCanceled) ";
//         echo $sql;exit;
        return $this->dbConn->query($sql);
    }
    
    /*
     * 获取一个订单的跟跟踪号信息 海外仓
     */
    public function getShippingInfo($orderId){
        $sql    = "select * from ow_order_tracknumber where omOrderId='$orderId'";
        return $this->dbConn->fetch_first($sql);
    }
    
    /*
     * 根据平台id获取平台信息
     * $pid  平台id
     */
    public function getPlatformInfoByPid($pid){
        $sql    = "select * from om_platform where id='$pid'";
        $row    = $this->dbConn->fetch_first($sql);
        if ( empty($row) ) {
        	 return FALSE;
        } else {
            return $row;
        }
    }
    
    /*
     * 获取订单扩展信息
     */
    public function getExtensionInfo($tabel, $orderId){
        $sql    = "select * from $tabel where omOrderId = $orderId";
        return $this->dbConn->fetch_first($sql);
    }
    
    /*
     * 根据卖家ID找到卖家账号信息
     * $sid 卖家账号
     */
    public function getSellerInfoById($sid){
        $sql    = "select * from ebay_account where id='$sid'";
        $row    = $this->dbConn->fetch_first($sql);
        if ($row) {
        	return $row;
        } else {
            return FALSE;
        }
    }
    
    /*
     * 获得订单状态
     * $orderId         订单号
     * $feild           字段
     */
    public function getOrderInfoById($ordId, $feild){
        if (is_array($feild)) {
        	$feildSql   = implode(',', $feild);
        } else {
            $feildSql   = '*';
        }
        
        $sql        = "select $feildSql from om_unshipped_order where id='$ordId'";
        $row        = $this->dbConn->fetch_first($sql);
        if ($row) {
        	return $row;
        } else {
            return FALSE;
        }
    }
    
    /*
     * 订单相关的买家信息
     */
    public function getBuyerInfoById($orderId){
        $sql    = "select * from om_unshipped_order_userInfo where omOrderId='$orderId'";
        $row    = $this->dbConn->fetch_first($sql);
        if ($row) {
        	return $row;
        } else {
            return FALSE;
        }
    }
    
    /*
     * 更新数据库的字段
     */
    public function updateFeildData($data, $orderId){
        $data   = array_map('mysql_real_escape_string', $data);
        $updataArray    = array();
        foreach ($data as $key=>$val){
            $updataArray[]  = "`$key`='$val'";
        }
        
        $updataSql  = implode(',', $updataArray);
        $sql        = "update om_unshipped_order set $updataSql where id=$orderId";
        $result     = $this->dbConn->query($sql);
        if ($result) {
        	return TRUE;
        } else {
            return FALSE;
        }
    }
    
    /*
     * 获取订单买家信息
     */
    public function getUnshippedOrderBuyerInfo($orderInfo) {
        $sql    = "select * from om_unshipped_order_userInfo where omOrderId='$orderInfo'";
        $row    = $this->dbConn->fetch_first($sql);
        if ($row) {
        	return $row;
        } else {
            return FALSE;
        }
    }
    
    /*
     * 更新数据海外仓订单的运算符方式信息的数据
     */
    public function updateOwTransInfo($feild, $orderId) {
        $feild      = array_map('mysql_real_escape_string', $feild);
        $updateSql  = array();
        foreach ($feild as $key=>$val){
            $updateSql[]  = "$key='$val'";
        }
        $updateSql  = implode(', ', $updateSql);
        $sql    = "update ow_order_tracknumber set $updateSql where omOrderId='$orderId'";
        $result = $this->dbConn->query($sql);
        if ($result) {
        	return TRUE;
        } else {
            return FALSE;
        }
    }
    
    /*
     * 根据卖家账号名获取卖家
     */
    public function getSellerInfo($seller){
        $sql    = "select * from om_account where account='$seller'";
        $result = $this->dbConn->fetch_first($sql);
        if ($result) {
        	return $result;
        } else {
            return false;
        }
    }
    
    /*
     * 获取某个买家 在某个账号下的购买历史记录
     */
    public function getBuyerHistory($buyer, $sellerId){
        /* 先处理unshipped表发货的信息  */
        $recentEndTime  = time() - 7776000;                                                                 //三个月之前
        /* $buyer      ='1zar1';
        $sellerId   = 432; */
        $sql    = "select * from om_unshipped_order as ouo join om_unshipped_order_userInfo as ouou on ouo.id=ouou.omOrderId
                 and ouo.accountId=$sellerId and ouou.platformUsername='$buyer' and ouo.ordersTime>$recentEndTime";
        $result_u = $this->dbConn->fetch_array_all($this->dbConn->query($sql));
        
        $sql    = "select * from om_shipped_order as ouo join om_shipped_order_userInfo as ouou on ouo.id=ouou.omOrderId
        and ouo.accountId=$sellerId and ouou.platformUsername='$buyer' and ouo.ordersTime>$recentEndTime";
        $result_s = $this->dbConn->fetch_array_all($this->dbConn->query($sql));
        $finalResult    = array();
        
        foreach ($result_u as $row){                                                                        //未处理
            $platFormInfo   = $this->getPlatformInfoByPid($row['platformId']);                              //获取平台信息
            if ($platFormInfo) {
            	$extensionTb   = 'om_unshipped_order_detail_extension_'.$platFormInfo['suffix'];
            } else {
                continue ;
            }
            
            /*-------------------------  订单运输方式信息    ------------------------------------------*/
            if (911 == $row['orderStatus']) {                                                               //海外仓订单
                $tansInfo          = $this->getShippingInfo($row['id']);
            } else {                                                                                        //普通订单
                $tansInfo          = $this->getNormalTransInfo($row['id']);
            }
            
            if ($tansInfo) {
            	$row['trackNumber']    = $tansInfo['tracknumber'];
            }
            
            /*--------------------------  仓库处理信息       ------------------------------------------- */
            $whInfo = $this->getWhInfo($row['id'], 'om_unshipped_order_warehouse');
            if ($whInfo) {
            	$row['scanTime']   = $whInfo['weighTime'];
            }
            
            $skuDetail  = "select * from om_unshipped_order_detail as ouod join $extensionTb as exttb on ouod.id=exttb.omOrderdetailId   where ouod.omOrderId=$row[id] ";
//             echo $skuDetail;exit;
            $skuResult  = $this->dbConn->fetch_array_all($this->dbConn->query($skuDetail));
            $statusInfo = $this->getStatusInfo($row['orderStatus'], $row['orderType']);                     //订单状态转换
            $row['catename']    = $statusInfo   ? $statusInfo['statusName'] : '状态未知';
            $finalResult[]  = array(
            	'orderInfo' => $row,
                'skuList'   => $skuResult
            );
        }
        /*  ------------------------      已发货处理      -------------------------------------------- */
        foreach ($result_s as $row_x){
            $platFormInfo   = $this->getPlatformInfoByPid($row_x['platformId']);
            if ($platFormInfo) {
                $extensionTb   = 'om_shipped_order_detail_extension_'.$platFormInfo['suffix'];
            } else {
                continue ;
            }
            
            if (911 == $row_x['orderStatus']) {                                           //海外仓订单
                $tansInfo          = $this->getShippingInfo($row_x['id']);
            } else {                                                                      //普通订单
                $tansInfo          = $this->getNormalTransInfo($row_x['id']);
            }
            
            if ($tansInfo) {
                $row_x['trackNumber']    = $tansInfo['tracknumber'];
            }
            
            $whInfo = $this->getWhInfo($row_x['id'], 'om_shipped_order_warehouse');
            if ($whInfo) {
                $row_x['scanTime']   = $whInfo['weighTime'];
            }
            
            $skuDetail  = "select * from om_shipped_order_detail as osod join $extensionTb as exttb on osod.id=exttb.omOrderdetailId 
                    where osod.omOrderId=$row_x[id]";
            $skuResult  = $this->dbConn->fetch_array_all($this->dbConn->query($skuDetail));
            $statusInfo = $this->getStatusInfo($row_x['orderStatus'], $row_x['orderType']);                 //订单状态转换
            $row_x['catename']    = $statusInfo   ? $statusInfo['statusName'] : '状态未知';
            $finalResult[]  = array(
                    'orderInfo' => $row_x,
                    'skuList'   => $skuResult
            );
        }//print_r($finalResult);exit;
        return $finalResult;
    }
    
    /*
     * 根据已经状态码和二级状态码获取状态信息
     */
    public function getStatusInfo($lev1, $lev2){
        $sql    = "select * from om_status_menu where groupId='$lev1' and statusCode='$lev2'";
        $row    = $this->dbConn->fetch_first($sql);
        if ($row) {
        	return $row;
        } else {
            FALSE;
        }
    }
    
    /*
     * 获得普通订单跟踪号信息
     */
    public function getNormalTransInfo($orderId){
        $sql    = "select * from om_order_tracknumber where omOrderId='$orderId'";
//         echo $sql;exit;
        $row    = $this->dbConn->fetch_first($sql);
        if ($row) {
        	return $row;
        } else {
            return FALSE;
        }
    }
    
    /*
     * 获得订单的发货状态信息
     */
    public function getWhInfo($orderId, $tabel){
        $sql    = "select * from $tabel where omOrderId='$orderId'";
        $result = $this->dbConn->fetch_first($sql);
        if ($result) {
        	return $result;
        } else {
            return FALSE;
        }
    }
    
}
