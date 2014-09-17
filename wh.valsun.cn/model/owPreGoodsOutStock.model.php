<?php
/*
 * 海外仓补货出库逻辑
 */
class OwPreGoodsOutStockModel {
    public static $errCode  = 0;
    public static $errmsg   = '';
    private $dbConn         = NULL;    
    
    /*
     * 构造函数
     */
    public function __construct(){
        global $dbConn;
        $this->dbConn   = $dbConn;
    }
    
    /*
     *出库操作
     * 成功 返回true 失败返回false
     */
    public function outStock($sku, $num, $opuser, $orderid, &$isEnd, $waitScan, $orderSn){
        
        $isLastOne      = true;                                                 //是否为最后一个待配货sku
        $preGoods_Obj   = new PreGoodsOrdderManageModel();
        $allSkuInfo     = $preGoods_Obj->getSKUDetail($orderid);                //订单的全部sku列表
        
        foreach ($allSkuInfo as $row){                                          //判断该料号配完以后是否可以终结该备货单
            if ( ($row['amount'] != $row['scantnum']) && ($row['sku'] != $sku) ) {
                $isLastOne = FALSE;
            }
        }
        if ($waitScan != $num) {
        	$isLastOne = FALSE;
        }
        
        $isEnd  = $isLastOne;
        
        //$this->logRequest(" sku==> $sku 数量==> $num");                           //记录请求日志

        $paramArr['method']   	= 'ow_pregood_changestock';  //API名称
		$paramArr['sku'] 		= $sku;
		$paramArr['num']		= $num;
		$paramArr['orderSn']    = $orderSn;
		$paramArr['operUser']   = getUserNameById($opuser);
        $messageInfo	    = UserCacheModel::callOpenSystem2($paramArr);                //先到老系统扣库存
        if (FALSE === $messageInfo) {                                                   //请求开发系统出错
            self::$errmsg   = '请求开放系统出错!';
        	return FALSE;
        }
        $code       = isset($messageInfo['code']) ? trim($messageInfo['code']) : '';
        if ($code !== 'success') {                                                      //扣库存失败
        	self::$errmsg  = $messageInfo['msg'];
        	return FALSE;
        } 
        
        $sku    = mysql_real_escape_string($sku);
        
        $this->dbConn->begin();                                                         
        $rcordInfo  = $this->getSkuInboxRecords($sku);
        if (FALSE === $rcordInfo) {                                                     //还没有封箱库存记录 则新增一条记录
        	$insertResult  = $this->insertNewInboxRecords($sku, $num);
        	if (FALSE === $insertResult) {                                              //插入失败 回滚
        		$this->dbConn->rollback();
        		self::$errmsg = "新增封箱库存记录失败!";
        		return FALSE;
        	}
        } else {                                                                        //追加库存
            $updateSql  = "update wh_inboxStock set num=num+$num where sku='$sku'";
            $updateQuery= $this->dbConn->query($updateSql);
            if (FALSE === $updateQuery) {                                               //更新封箱库存失败 回滚
            	$this->dbConn->rollback();
            	self::$errmsg  = "更新封箱库存失败!";
            	return FALSE;
            }
        }
        $time       = time();
        $updateOrder    = "
                update wh_prepDetail set scantnum=scantnum+$num, scantime=$time, scanuser='$opuser' where 
                sku='$sku' and orderid='$orderid'
            ";
        $upOrderQuery   = $this->dbConn->query($updateOrder);                           //更新备货单的扫描数量
        if (FALSE === $upOrderQuery) {
        	$this->dbConn->rollback();
        	self::$errmsg  = '更新备货单数据失败!';
        	return FALSE;
        }
        
        if (TRUE === $isLastOne) {                                                      //配货完成 修改备货单状态
        	$upStatusSql   = "update wh_prepGoodsOrder set status=3 where id='$orderid'";
        	$upStatusQuery = $this->dbConn->query($upStatusSql);
        	if (FALSE === $upStatusQuery) {
        		$this->dbConn->rollback();
        		self::$errmsg = '更新备货单状态失败!';
        		return FALSE;
        	}
        }
        
        $originNum  = isset($rcordInfo['num']) ? $rcordInfo['num'] : 0; 
        $logSql     = "insert into wh_skuscanLog (orderId, sku, scanNum, originNum, opuser, scanTime) values
                    ($orderid, '$sku', '$num', '$originNum', '$opuser', '$time')
                ";
        $logResult  = $this->dbConn->query($logSql);                                    //记录操作日志
        if (FALSE === $logResult) {                                                     //写日志失败 回滚
        	$this->dbConn->rollback();
        	self::$errmsg  = '写入操作日志失败!';
        	return FALSE;
        }
        
        $this->dbConn->commit();
        return TRUE;
    }
    
    /*
     * 记录请求日志
     */
    public function logRequest($str){
        //$day        = date('Y-m-d');
       // $filePath   = WEB_PATH."log/owRequestlog/".$day.'log';
        //$fp         = fopen($filePath, 'a+');
        //$timd       = date('Y-m-d H:i:s');
        //$logstr     = "[ $timd ] -- ".$str;
        //fwrite($fp, $logstr);
        //fclose($fp);
    }
    
    /*
     * 检测一个sku是否存在封箱库存记录
     */
    public function getSkuInboxRecords($sku){
        $sql    = "select * from wh_inboxStock where sku='$sku'";
        $rowInfo    = $this->dbConn->fetch_first($sql);
        return $rowInfo;
    }
    
    /*
     * 增加提条sku的封箱库存记录
     */
    public function insertNewInboxRecords($sku, $num){
        $sql    = "insert into wh_inboxStock (sku, num) values ('$sku', '$num')";
        return $this->dbConn->query($sql);
    }
}
