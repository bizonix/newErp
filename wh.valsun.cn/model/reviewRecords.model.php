<?php

/** 
 * @author 涂兴隆
 * 复核扫描记录模型
 */
class ReviewRecordsModel
{
    private $dbconn = null;

    /**
     * 构造函数
     */
    function __construct ()
    {
    	global $dbConn;    //初始化数据库连接
    	$this->dbconn = $dbConn;
    }
    
    /*
     * 根据发货单号获得一件配货的sku列表
     * $orderid 发货单号
     */
    public function getRiewRecordsByOrderid($orderid, $sku = ''){
        $sql = 'select shipOrderdetailId, sku, amount, totalNums from wh_order_review_records where shipOrderId='.
                $orderid.' and is_delete=0'.($sku ? " AND sku='".$sku."'" : '');
        return $this->dbconn->fetch_array_all($this->dbconn->query($sql));
    }
    
    /*
     * 记录复核信息到数据库
     * $data 数组 所需的信息
     */
    public function recordReviewInfo($data) {
    	$orderid = $data['orderid'];
    	$orderdetailid = $data['detailid'];
    	$sku = $data['sku'];
    	$sku = mysql_real_escape_string($sku);
    	$amount = $data['amount'];
    	$totalNums = $data['totalNums'];
    	$time = time();
    	$userid = $data['userid'];
    	$storeId = $data['storeId'];
    	$islast = $data['islast'];
    	
    	$skuinfo = getSkuInfoBySku($sku);
    	$skuname = empty($skuinfo) ? '':$skuinfo['goodsName'];
    	$skuname = mysql_real_escape_string($skuname);
    	
    	$insert_sql = "insert into wh_order_review_records values (null, $orderid, $orderdetailid, '$sku', '$skuname', 
    	$amount, $totalNums, $time, $userid, null, null, 1, 0, $storeId)";
    	
    	if ($islast) { //最后一次复核 则更新发货单状态
    		$updatesql = "update wh_shipping_order set orderStatus=".PKS_WWEIGHING.' where id='.$orderid;
    	}
    	
    	$this->dbconn->begin();
    	
    	$insertresult = $this->dbconn->query($insert_sql); //插入复核数据
    	if($insertresult == FALSE){    //插入数据失败 则回滚
    	    $this->dbconn->rollback();
    	    $this->dbconn->query('SET AUTOCOMMIT=1');
    	    return FALSE;
    	}
    	
    	if ($islast){
    	    $updateresult = $this->dbconn->query($updatesql);
    	    if ($updateresult == FALSE) {  //更新状态失败
    	        $this->dbconn->rollback();
    	        $this->dbconn->query('SET AUTOCOMMIT=1');
    	        return FALSE;
    	    } 
    	}
    	
    	$this->dbconn->commit();
    	return TRUE;
    }
}

?>