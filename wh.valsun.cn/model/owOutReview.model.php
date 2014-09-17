<?php
/*
 * 海外仓备货单复核扫描
 */
class OwOutReviewModel {
    public static $errCode  = 0;
    public static $errMsg   = '';
    private $dbConn         = NULL;
    
    /*
     * 构造函数
     */
    function __construct() {
        global $dbConn;
        $this->dbConn   = $dbConn;
    }
    
    /*
     * 增加一条复核信息
     */
    public function addNewRecheckRecord($orderSn, $orderid, $sku , $num, $opuser, &$isend){
    	$rtnInfo   = $this->updPurOrderQty($orderSn, $sku, $num);//更新采购系统备货单料号数量
    	if($rtnInfo['code'] == 200){
	    	//$isend  = $this->isLastSku($sku, $orderid);
	        $issend = $this->chkOrderReivewComplete($orderSn);
	    	$this->dbConn->begin();
	        $sku    = mysql_real_escape_string($sku);
	        $time   = time();
	        $sql    = "insert into wh_reviewRecord (orderid, sku, num, scantime, opuser) values ('$orderid', '$sku', '$num', '$time','$opuser')";
	        $query  = $this->dbConn->query($sql);
	        if (FALSE === $query) {
	            $this->dbConn->rollback();
	        	self::$errMsg  = '同步更新采购系统成功，复核失败!';
	        	return FALSE;
	        } else {
	            if ($issend == true) {                          //最后一个待复核 则完成后修改为配货完毕状态
	            	$updateSql = "update wh_prepGoodsOrder set status=4 where id='$orderid'";
	            	$upQuery   = $this->dbConn->query($updateSql);
	            	if (FALSE === $upQuery) {
	            		$this->dbConn->rollback();
	            		self::$errMsg  = '更新状态失败!';
	            		return FALSE;
	            	}
	            }
	            $this->dbConn->commit();
	            return TRUE; 
	        }
		}else{
			self::$errMsg = $rtnInfo['msg'];
			return FALSE;
		}
    }
    
    /*
     * 计算是否为最后一个复核sku
     */
    public function isLastSku($sku, $orderid){
        $sql    = "
                   select * from ( select wpd.* , wrr.sku as sku2 from wh_prepDetail as wpd left join wh_reviewRecord as wrr on wpd.sku=wrr.sku 
                   where wpd.orderid=$orderid ) as sub where sub.sku2 is null
                ";
        $result = $this->dbConn->fetch_array_all($this->dbConn->query($sql));
        if ( (count($result)==1) && ($result[0]['sku'] == $sku) ) {
        	return true;
        } else {
            return FALSE;
        }
    }
    
    /**
     * 验证备货单是否复核完成，复核完成条件为，备货单数量需与配货数量一致
     */
    public function chkOrderReivewComplete($orderSn){
    	$sql 	= "SELECT b.amount, b.scantnum FROM wh_prepGoodsOrder AS a JOIN wh_prepDetail AS b ON a.id = b.orderid WHERE a.ordersn = '{$orderSn}'";
    	$query 	= $this->dbConn->query($sql);
    	$data   = $this->dbConn->fetch_array_all($query);
    	$mark   = true;
    	if(!empty($data)){
    		foreach($data as $k => $v){
    			$amount  = $v['amount'];
    			$scannum = $v['scantnum'];
    			if($amount != $scannum){
    				$mark = false;
    				break;
    			}
    		}
    	}else{
    		$mark = false;
    	}
    	return $mark;
    }
    
    /*
     * 检测某个sku是否已经复核过
     */
    public function hasChecked($sku, $orderid){
        $sku    = mysql_real_escape_string($sku);
        $sql    = "select * from wh_reviewRecord where sku='$sku' and orderid='$orderid'";
        return $this->dbConn->fetch_first($sql);
    }
    
    /**
     * 备货单料号复核完成后，将配货数量同步到采购系统备货单
     * Enter description here ...
     * @param 备货单号 $ordersn
     * @param 料号 $sku
     * @param 数量 $num
     */
    public function updPurOrderQty($ordersn, $sku, $num){
    	$paramArr['method']   	= 'ow_updBOrderAmount';  //API名称
		$paramArr['ordersn']    = $ordersn;
    	$paramArr['sku'] 		= $sku;
		$paramArr['amount']		= $num;
        $rtnInfo	    		= UserCacheModel::callOpenSystem($paramArr);
        return $rtnInfo;
    }
}

