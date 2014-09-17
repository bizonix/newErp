<?php
/*
 * 补货单管理
 */
class PreplenshOrderModel {
    public static $errMsg   = '';
    public static $errCode  = 0;
    private $dbConn         = NULL;
    
    /*
     * 构造函数
     */
    public function __construct(){
        global $dbConn ;
        $this->dbConn   = $dbConn;
    }
    
    /*
     * 获取某个补货单的数据
     */
    public function getPrePlenshOrderInfo($ordersn){
        $sql    = "select * from wh_preplenshOrder where ordersn='$ordersn'";
        return $this->dbConn->fetch_first($sql);
    }
    
    /*
     * 将一个箱子增加到某个补货单下面并扣除相应的封箱库存
     */
    public function addBoxToaOrder($boxId, $orderid, $opuser){
        $time   = time();
        $box_obj    = new BoxManageModel();
        $inBox_obj  = new OwInBoxStockModel();
        $skuDetail  = $box_obj->getBoxSkuDetail($boxId);
        $this->dbConn->begin();
        foreach ($skuDetail as $skuInfo){
            $tmp        = $skuInfo['num'];
            $inboxInfo  = $inBox_obj->getInbocStockInfo($skuInfo['sku']);
            if (FALSE === $inboxInfo) {                                                 //该料号没有封箱库存信息
            	$this->dbConn->rollback();
            	self::$errMsg  = $skuInfo['sku']."没有封箱库存信息!";
            	return FALSE;
            }
            if ($inboxInfo['num'] < $skuInfo['num']) {                                  //如果封箱库存小于箱子中的库存数量 则会导致封箱库存不够扣
                $this->dbConn->rollback();
                self::$errMsg  = $skuInfo['sku']."封箱库存小于发货数量!";
                return FALSE;
            }
            $upsql      = "update wh_inboxStock set num=num-$tmp where sku='{$skuInfo['sku']}'";        //扣除封箱库存
            $upquery    = $this->dbConn->query($upsql);
            if (FALSE === $upquery) {
                $this->dbConn->rollback();
                self::$errMsg  = $skuInfo['sku']."扣除封箱库存失败!";
                return FALSE;
            }
        }
        $sql    = "
                update wh_boxinuse set replenshId='$orderid', sendScanTime='$time', 
                sendScanUser='$opuser', status='3' where boxid='$boxId'
        ";
        $query  = $this->dbConn->query($sql);
        if (FALSE === $query) {                                                                         //更新状态失败
        	$this->dbConn->rollback();
        	self::$errMsg  = '更新箱子状态失败!';
        	return FALSE;
        }
        
        $logSql = "
                insert into wh_boxOpLog (boxId, opcode, opuser, optime) values ('$boxId', 'send', '$opuser', '$time');
                ";
        $logquery   = $this->dbConn->query($logSql);
        if (FALSE === $logquery) {                                                                      //记录日志出错
        	$this->dbConn->rollback();
        	self::$errMsg  = '记录日志出错！';
        	return FALSE;
        }
        
        $upd    = "UPDATE wh_boxDetail SET stu = '2' WHERE boxId = '{$boxId}' AND is_delete = 0";
        $rtnUpd = $this->dbConn->query($upd);
        if($rtnUpd === FALSE){
        	$this->dbConn->rollback();
        	self::$errMsg  = '更新装箱料号状态出错';
        	return FALSE;
        	
        }
        
        $this->dbConn->commit();
        return TRUE;
    }
    
    /*
     * 获得总数
     */
    public function culPreshOrder($where){
        $sql        = "select count(1) as num from wh_preplenshOrder where 1 $where";
        $rowInfo    = $this->dbConn->fetch_first($sql);
        return isset($rowInfo['num']) ? $rowInfo['num'] : 0;
    }
    
    /*
     * 获得补货单列表
     */
    public function culPershOrder($where){
        $sql    = "select * from wh_preplenshOrder where 1 $where";
        return $this->dbConn->fetch_array_all($this->dbConn->query($sql));
    }
    
    /*
     * 状态和名称的映射
     */
    public static  function status2Str($id){
        switch ($id){
            case 1:
                return '待处理';
            case 2:
                return '已发货';
            case 3:
                return '海外仓已收货';
            default:
                return '';
        }
    }
    
    /*
     * 验证是否为合法的状态码
     */
    public static function isValidStatusCode($code){
        $status = array(1,2,3);
        return in_array($code, $status);
    }
    
    /*
     * 修改补货单状态
     */
    public function changePreOrderStatus($orderId, $status, $arriveday){
        $sql    = "update wh_preplenshOrder set status = '{$status}', arriveday = '{$arriveday}' where id='$orderId'";
        $query  = $this->dbConn->query($sql);
        return $query;
    }
}