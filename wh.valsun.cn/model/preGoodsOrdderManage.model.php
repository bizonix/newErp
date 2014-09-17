<?php
/*
 * 采购订单管理
 */
class PreGoodsOrdderManageModel {
    public static $errCode    = 0;
    public static $errMsg     = '';
    private $dbConn           = NULL;
    
    /*
     * 构造函数
     */
    public function __construct(){
        global $dbConn ;
        $this->dbConn   = $dbConn;
    }
    
	/**
     * 备货单计数
     */
    public function getBackOrderInfoCount($where){
    	$sql  		= "SELECT count(distinct(a.ordersn)) AS num FROM wh_prepGoodsOrder AS a ";
    	$sql       .= "JOIN wh_prepDetail AS b ON a.id = b.orderid WHERE 1 ".$where;
    	$reulst 	= $this->dbConn->fetch_first($sql);
    	if(!$reulst) {
            self::$errMsg   = '计算数量出错!';
            return FALSE;
        }
    	$num    	= $reulst['num'];
        return $num;
    }
    
    /**
     * 备货单列表
     */
    public function getBackOrderInfo($where){
    	$sql  = "SELECT distinct(a.ordersn) as ordersn, a.id, a.owner, a.createtime, a.status, a.synctime, a.isdelete FROM wh_prepGoodsOrder AS a ";
    	$sql .= "JOIN wh_prepDetail AS b ON a.id = b.orderid WHERE 1 ".$where;
    	return $this->dbConn->fetch_array_all($this->dbConn->query($sql));
    }
    
    /*
     * 获得订单信息
     */
    public function getOrderInfo($orderSN) {
        $orderSN    = mysql_real_escape_string($orderSN);
        $sql    = "select * from wh_prepGoodsOrder where ordersn='$orderSN'";
        return $this->dbConn->fetch_first($sql);
    }
    
    /*
     * 新增一个备货单
     */
    public function addNewPreGoodsOrder($orderInf, $goodsList){
        $orderInf   = array_map('mysql_real_escape_string', $orderInf);
        $orderSn    = $orderInf['orderSn'];                                                 //备货单号
        $createTime = $orderInf['createTime'];                                              //备货单生成时间
        $owner      = $orderInf['owner'];                                                   //备货单生成人
        $syncTime   = time();
        $orderInfoSql   = "
                insert into wh_prepGoodsOrder (ordersn, owner, createtime, status, synctime, isdelete) values ('$orderSn', 
                '$owner', '$createTime', '1', '$syncTime', '0')
                ";
        $this->dbConn->begin();                                                             //开启事务处理
        $insertQuery    = $this->dbConn->query($orderInfoSql);                              //插入
        if ( FALSE === $insertQuery ) {                                                     //插入失败 回滚
        	$this->dbConn->rollback();
        	self::$errMsg  = '插入备货单信息失败!';
        	return FALSE;
        }
        $newOrderId     = $this->dbConn->insert_id();                                       //新生成的id
        foreach ($goodsList as $row){                                                       //循环插入详情
            $sku    = mysql_real_escape_string($row['sku']);
            $num    = intval($row['amount']);
            $skuSql = "
                    insert into wh_prepDetail (orderid, sku, amount) values ('$newOrderId', '$sku', '$num') 
                    ";
//             echo $skuSql, "\n";
            $sku_query  = $this->dbConn->query($skuSql);
            if (FALSE === $sku_query) {
            	$this->dbConn->rollback();
            	self::$errMsg  = '插入sku详情失败!';
            	return FALSE;
            }
        }
        $this->dbConn->commit();
        return TRUE;
    }
    
    /*
     * 获得一个备货单号下某个sku的信息
     */
    public function getSKUinfo($orderId, $sku){
        $sku    = mysql_real_escape_string($sku);
        $sql    = "select * from wh_prepDetail where orderid='$orderId' and sku='$sku'";
        return  $this->dbConn->fetch_first($sql);
    }
    
    /*
     *更改某一个备货单下的某个sku的数量
     */
    public function updateSkuAmount($orderId, $sku, $num, $orderSn, $opuser){
        $sku        = mysql_real_escape_string($sku);
        $orderSn    = mysql_real_escape_string($orderSn);
        $this->dbConn->begin();
        $sql        = "
                update wh_prepDetail set amount='$num' where orderid='$orderId' and sku='$sku' 
                ";
        $upQuery    = $this->dbConn->query($sql);
        if (FALSE === $upQuery) {
        	$this->dbConn->rollback();
        	self::$errMsg  = '更新数量失败!';
        	return FALSE;
        }
        $time   = time();
        $logSql = "insert into wh_preOrderNumChangeLog (orderSn, sku, num,opUser, opTime) values ('$orderSn', '$sku', '$num', '$opuser', '$time')";
        $logQuery   = $this->dbConn->query($logSql);
        if (FALSE === $logQuery) {
        	self::$errMsg  = '写入日志失败!';
        	return FALSE;
        }
        $this->dbConn->commit();
        return true;
    }
    
    /*
     * 根据条件计算某备货单数量
     */
    public function culOrderCount($where){
        $sql   = "
                select count(1) as num from wh_prepGoodsOrder where 1 $where
                ";
//         echo $sql;exit;
        $reulst = $this->dbConn->fetch_first($sql);
        if (!$reulst) {
            self::$errMsg   = '计算数量出错!';
            return FALSE;
        }
        $num    = $reulst['num'];
        return $num;
    }
    
    /*
     * 根据条件获取一组备货单的详情
     */
    public function getOrderListInfo($where){
        $sql    = "
                select * from wh_prepGoodsOrder where 1 $where
                ";
        return $this->dbConn->fetch_array_all($this->dbConn->query($sql));
    }
    
    /*
     * 获得某个备货单下面的详情
     */
    public function getSKUDetail($orderId, $where){
        $sql    = "select * from wh_prepDetail where orderid='$orderId' ".$where;
        return $this->dbConn->fetch_array_all($this->dbConn->query($sql));
    }
    
    /**
     * 打印配货清单
     * Enter description here ...
     * @param $orderId
     */
 	public function getSKUDetailByStatus($orderId){
        $sql    = "select * from wh_prepDetail where orderid='$orderId' AND scantime = 0";
        return $this->dbConn->fetch_array_all($this->dbConn->query($sql));
    }
    
    /*
     *获取备货单信息 根据备货单id
     */
    public function getOrderInfroByid($orderId){
        $sql    = "select * from wh_prepGoodsOrder where id='$orderId'";
        return $this->dbConn->fetch_first($sql);
    }
    
    /*
     * 修改备货单的状态
     */
    public function changeOrderStatus($orderId, $status, $opUser){
        $orderInf   = $this->getOrderInfroByid($orderId);
        if (FALSE === $orderInf) {
        	self::$errMsg  = '不存在订单信息!';
        	return FALSE;
        }
        
        $originStatus   = $orderInf['status'];
        $optime         = time();
        $updataSql  = "update wh_prepGoodsOrder set status='$status' where id='$orderId'";
        $logSql     = "
                insert into wh_preGoodsStatusChLog (orderId, originalStatus, changedStatus, opuser, optime) values (
                '$orderId', '$originStatus', '$status', '$opUser', '$optime'
                )
                ";
        $this->dbConn->begin();
        $changeQuery    = $this->dbConn->query($updataSql);                                                 //更新状态
        if (FALSE === $changeQuery) {
        	$this->dbConn->rollback();
        	self::$errMsg  = '更新状态出错!';
        	return FALSE;
        }
        $logQuery   = $this->dbConn->query($logSql);                                                        //记录操作日志
        if (FALSE === $logQuery) {
        	$this->dbConn->rollback();
        	self::$errMsg  = '记录日志失败!';
        	return FALSE;
        }
        $this->dbConn->commit();
        return TRUE;
    }
    
    /*
     * 验证状态吗是否合法
     */
    public static function validateStatusCode($statusCode){
        $statusCodeAr = array(1,2,3,4);
        if (in_array($statusCode, $statusCodeAr)) {
        	return TRUE;
        } else {
            return FALSE;
        }
    }
    
    /*
     * 备货单状态买到名称的映射方式
     */
    public static function statusCodeToStr($code){
        switch ($code)
        {
            case 1 :
                return '待处理';
                break;
            case 2 :
                return '待配货';
                break;
            case 3 : 
                return '待复核';
                break;
            case 4 :
                return '配货完毕';
                break;
            default:
                return FALSE;
        }
    }
    
    /*
     * 生成一个新的补货单
     */
    public function createNewPreOrder($user){
        $time   	= time();
        $ordersn 	= 'OWS'.date('Ymd').substr($time, strlen($time) - 2);
	    $insert    		= "insert into wh_preplenshOrder (ordersn, createTime, status, createUser) values ('$ordersn', '$time', '1', '$user')";
	    $rtnInsert  	= $this->dbConn->query($insert);
	    if (FALSE === $rtnInsert) {
	    	return FALSE;
	    } else {
	        return TRUE;
        }
    }
    
    /**
     * 采购员在采购系统修改备货单数量后，验证备货单是否已配货完成
     * add name:wangminwei
     * add time:2014-05-15
     */
    public function updPreOrderStatus($orderSn){
    	$this->dbConn->begin();
    	$sql 	= "SELECT a.id, b.sku, b.amount FROM wh_prepGoodsOrder AS a JOIN wh_prepDetail AS b ON a.id = b.orderid WHERE a.ordersn = '{$orderSn}' AND a.isdelete = 0 AND b.is_delete = 0";
    	$query 	= $this->dbConn->query($sql);
    	if($query){
    		$dataInfo = $this->dbConn->fetch_array_all($query);
    		if(!empty($dataInfo)){
    			$sign = 'yes';
    			foreach($dataInfo as $k => $v){
    				$orderId    = $v['id'];
    				$sku 		= $v['sku'];
    				$amount 	= $v['amount'];//备货单料号数量
    				$scanNum    = 0;
    				$str 		= "SELECT SUM(scanNum) AS scanNum FROM wh_skuscanLog WHERE orderId = '{$orderId}' AND sku = '{$sku}'";
    				$detailInfo = $this->dbConn->fetch_first($str);
    				if(!empty($detailInfo)){
    					$scanNum = $detailInfo['scanNum'];//已配货数量
    				}
    				if($amount != $scanNum){
    					$sign = 'no';
    					break;
    				}else{
    					continue;
    				}
    			}
    			if($sign == 'yes'){
    				$upd = "UPDATE wh_prepGoodsOrder SET status = 3 WHERE ordersn = '{$orderSn}' AND status = 2 AND isdelete = 0";
    				$rtn = $this->dbConn->query($upd);//更新备货单为待复核状态
    				if($rtn){
    					$this->dbConn->commit();
    				}else{
    					$this->dbConn->rollback();
    				}
    			}
    		}
    	}
    }
}