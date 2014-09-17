<?php
/*
 * 封箱库存处理逻辑
 */
class OwInBoxStockModel {
    public static $errCode = 0;
    public static $errMsg  = '';
    private $dbConn     = NULL;
    
    /*
     * 构造函数
     */
    function __construct() {
        global $dbConn;
        $this->dbConn   = $dbConn;
    }
    
    /*
     * 获得一个料号的封箱库存信息
     */
    public function getInbocStockInfo($sku){
        $sku    = mysql_real_escape_string($sku);
        $sql    = "select * from wh_inboxStock where sku='$sku'";
//         echo $sql;exit;
        return $this->dbConn->fetch_first($sql);
    }
    
    /*
     * 生成一个装箱数据
     */
    public function addNewSkuBox($boxNum, $skuList, $user){
        $time   = time();
        $this->dbConn->begin();
        $sqlt   = "SELECT COUNT(*) AS total FROM wh_boxinuse WHERE boxid = '{$boxNum}'";
        $useNum = $this->dbConn->fetch_first($sqlt);
        if($useNum['total'] == 0){
	        $sql    = "insert into wh_boxinuse (boxid, addtime, adduser, status) values ('$boxNum', '$time', '$user', '1')";
	        $insert_query   = $this->dbConn->query($sql);
	        if (FALSE === $insert_query) {
	        	$this->dbConn->rollback();
	        	self::$errMsg  = '插入装箱信息失败!';
	        	return FALSE;
	        }
	        foreach ($skuList as $val){
	            $key    	= mysql_real_escape_string($val['sku']);
	            $num    	= $val['num'];
	            $sqld   	= "SELECT COUNT(*) AS total FROM wh_boxDetail WHERE boxId = '{$boxNum}'";
        		$hasExist 	= $this->dbConn->fetch_first($sqld);
	            if($hasExist['total'] == 0){
	        		$detailsql    	= "insert into wh_boxDetail (boxId, sku, num, is_delete) values ('$boxNum', '$key', '$num', '0')";
		            $detail_query   = $this->dbConn->query($detailsql);
		            if (FALSE === $detail_query) {
		            	$this->dbConn->rollback();
		            	self::$errMsg  = '记录装箱详情失败!';
		            	return FALSE;
		            }
	            }
	        }
	        $log_sql    = "insert into wh_boxOpLog (boxId, opcode, opuser, optime) values ('$boxNum', 'add', '$user', '$time')";
	        $log_query  = $this->dbConn->query($log_sql);
	        if (FALSE === $log_query) {
	        	$this->dbConn->rollback();
	        	self::$errMsg  = '记录日志失败!';
	        	return FALSE;
	        }
	        $updBoxUse = "UPDATE wh_boxApply SET isuse = 2 WHERE boxnum = '{$boxNum}'";
	        $updQuery  = $this->dbConn->query($updBoxUse);
	     	if ($updQuery === false) {
	        	$this->dbConn->rollback();
	        	self::$errMsg  = '更新箱子使用状态失败!';
	        	return FALSE;
	        }
	        $this->dbConn->commit();
	        
	        return TRUE;
        }else{
        	self::$errMsg = '装箱已存在';
        	return FALSE;
        }
    }
    
    /**
     * 计算某个料号已经扫箱未出柜的总数量 add by wangminwei 2014-05-06
     */
    public function getSkuLinkBoxNum($sku){
    	$sql 		= "select SUM(num) AS num FROM wh_boxDetail WHERE sku = '{$sku}' AND stu = 1 AND is_delete = 0";
    	$rtnData 	= $this->dbConn->fetch_first($sql);
    	return $rtnData['num'];
    }
    
    /*
     * 获得一个已经装箱的箱子信息 
     */
    public function getBoxInfo($boxId){
        $sql    = "select * from wh_boxinuse where boxid='$boxId'";
        return $this->dbConn->fetch_first($sql);
    }
    
    /*
     * 获得某个箱号下面某个sku的信息
     */
    public function getSkuInfoInBox($sku, $boxid){
        $sku    = mysql_real_escape_string($sku);
        $sql    = "select * from wh_boxDetail where boxId='$boxid' and sku='$sku'";
        return $this->dbConn->fetch_first($sql);
    }
    
    /*
     * 复核逻辑
     */
    public function boxReview($sku, $num, $boxid, $user, &$isend){
        $inboxSkuInfo   = $this->getSkuInfoInBox($sku, $boxid);
        if (FALSE === $inboxSkuInfo) {
            self::$errMsg   = '不存在的sku!';
            return FALSE;
        }
        
        if ($inboxSkuInfo['num'] != $num) {                                 //相等 复核成功
             self::$errMsg  = '复核数量不对!';
             return FALSE;
        }
        
        $isend  = $this->isLastSku($sku, $boxid);
//         var_dump($isend);exit;
        $this->dbConn->begin();
        $sku    = mysql_real_escape_string($sku);
        $time   = time();
        $insertReview   = "insert into wh_boxReview (boxId, sku, number, time, opuser) values ('$boxid', '$sku', '$num', '$time', '$user')";
//         echo $insertReview;exit;
        $insert_query   = $this->dbConn->query($insertReview);
        if (FALSE === $insert_query) {
        	$this->dbConn->rollback();
        	self::$errMsg  = '写入复核记录失败!';
        	return FALSE;
        }
        if ($isend) {
        	$updateSql  = "update wh_boxinuse set status=2, reviewTime=$time, reviewUser=$user where boxid='$boxid'";
//         	echo $updateSql;exit;
        	$updateQuery   = $this->dbConn->query($updateSql);
        	if (FALSE === $updateQuery) {                                          //更新状态失败 回滚
        		$this->dbConn->rollback();
        		self::$errMsg = '更新订单状态回滚';
        		return FALSE;
        	}
        }

        $this->dbConn->commit();
        return TRUE;
    }
    
    /*
     * 判断一个sku是否为最后一个待复核的sku
     */
    public function isLastSku($sku, $boxid){
        $sql    = "
                select * from (select wbd.* , wbr.boxId as rid from wh_boxDetail as wbd left join wh_boxReview as wbr on wbd.boxId=wbr.boxId where wbd.boxId='$boxid')
                as sub where sub.rid is null
                ";
        $rows   = $this->dbConn->fetch_array_all($this->dbConn->query($sql));
        if (count($rows) >1 ) {
            return FALSE;
        } else {
            return TRUE;
        }
    }
    
    /**
     * 箱号长、宽、高、重量录入时箱验证
     * add time:2014-05-07
     * add name:wangminwei
     */
    public function checkBoxPass($boxId){
    	$sql 	= "SELECT * FROM wh_boxinuse WHERE boxid = '{$boxId}' AND status IN (1,2)";
    	$result = $this->dbConn->fetch_first($sql);
    	return $result;
    }
    
    /**
     * 更新箱号长、宽、高、重量
     * add time:2014-05-07
     * add name:wangminwei
     */
    public function updBoxLWG($boxId, $length, $width, $hight, $grossweight, $netweight){
    	$volume     = $length * $width * $hight;
    	$upd 		= "UPDATE wh_boxinuse SET width = '{$width}', length = '{$length}', high = '{$hight}', grossWeight = '{$grossweight}', volume = '{$volume}', netWeight = '{$netweight}' WHERE boxid = '{$boxId}' AND status in(1,2)";
    	$rtnData   	= $this->dbConn->query($upd);
    	return $rtnData;
    }
    
    /**
     * 计算箱子净重，只计算料号重量
     * add time:2014-05-09
     * add name:wangminwei
     */
    public function calcBoxNetWeight($boxId){
    	$sql 		= "SELECT  sku, num FROM wh_boxDetail WHERE boxId = '{$boxId}' AND is_delete = 0";
    	$query      = $this->dbConn->query($sql);
    	$rtnData   	= $this->dbConn->fetch_array_all($query);
    	$totalWeight = 0;
    	if(!empty($rtnData)){
    		foreach($rtnData as $k => $v){
    			$sku 		= $v['sku'];
    			$num 		= $v['num'];
    			$get 		= "SELECT goodsWeight FROM pc_goods WHERE sku = '{$sku}'";
    			$skuInfo 	= $this->dbConn->fetch_first($get);
    			if(!empty($skuInfo)){
    				$weight = $skuInfo['goodsWeight'];
    				$totalWeight += $weight * $num;
    			}
    			
    		}
    	}
    	return $totalWeight;
    }
    
     /**
     * pda退箱料号扫描验证
     */
    public function pdaCheckReturnSku($sku, $boxId){
    	$sql 	= "SELECT * FROM wh_boxDetail WHERE boxid = '{$boxId}' AND sku = '{$sku}'";
    	$result = $this->dbConn->fetch_first($sql);
    	if(!empty($result)){
    		return true;
    	}else{
    		return false;
    	}
    }
     /**
     * pda退箱扫描验证
     * add time:2014-05-16
     * add name:wangminwei
     * note:整箱退货，箱子状态需为已装柜，部分退货状态限制在已配货、已复核、已装柜状态;状态 1:已配货 2:已复核 3:已装柜 4:海外已收货
     */
    public function pdaCheckReturnBox($boxId, $sku, $num, $ismark){
    	$boxManage = new BoxManageModel();
    	$this->dbConn->begin();
    	if($ismark == 'all'){//整箱
    		$sql 	= "SELECT status FROM wh_boxinuse WHERE boxid = '{$boxId}'";
    		$result = $this->dbConn->fetch_first($sql);
    		if(!empty($result)){
    			$status = $result['status'];
    			if($status != 3){
    				return 'noPass';
    			}else{
    				$updLink = "UPDATE wh_boxinuse SET replenshId = '', sendScanTime = 0, sendScanUser = 0, status='2' WHERE boxid = '{$boxId}'";
    				$rtnUpdLink = $this->dbConn->query($updLink);//去除补货清单关联
    				if($rtnUpdLink === false){
    					echo '1';
    					$this->dbConn->rollback();
            			return 'failure';
    				}
    				$skuInfo 			= $boxManage->getBoxSkuDetail($boxId);
    				$skuArr  			= $skuInfo[0]['sku'];
    				$stock              = $skuInfo[0]['num'];
    				$updBoxStock    	= "UPDATE wh_inboxStock SET num = num + $stock WHERE sku = '{$skuArr}'";
            		$rtnUpdBoxStock  	= $this->dbConn->query($updBoxStock);//添加封箱库存数量
            		if($rtnUpdBoxStock === false) {
                		echo '2';
            			$this->dbConn->rollback();
            			return 'failure';
            		}
    				$time   	= time();
			        $logSql 	= "INSERT INTO wh_boxOpLog(boxId, opcode, opuser, optime) VALUES ('{$boxId}', 'return', '{$_SESSION['userId']}', '{$time}')";
			        $logQuery   = $this->dbConn->query($logSql);//添加退箱记录
			        if($logQuery === false) {
			            echo '3';
			        	$this->dbConn->rollback();
			        	return 'failure';
			        }
			        $this->dbConn->commit();
			        return 'success';
    			}
    		}else{
    			return 'Null';
    		}
    	}
    	if($ismark == 'part'){//部份
    		$sql 	= "SELECT b.num FROM wh_boxinuse AS a JOIN wh_boxDetail AS b ON a.boxid = b.boxId WHERE a.boxid = '{$boxId}' AND b.sku = '{$sku}'";
    		$result = $this->dbConn->fetch_first($sql);
    		if(!empty($result)){
    			$qty = $result['num'];
    			if($num > $qty){
    				return 'moreQty';
    			}else if($num == $qty){
    				return 'sameQty';
    			}else{
    				$updBoxQty    	= "UPDATE wh_boxDetail SET num = num - $num WHERE boxId = '{$boxId}' AND sku = '{$sku}'";
	            	$rtnUpdBoxQty  	= $this->dbConn->query($updBoxQty);//扣除装箱数量
	            	if($rtnUpdBoxQty === false) {
	                	$this->dbConn->rollback();
	            		return 'failure';
	            	}
    				$time   	= time();
			        $logSql 	= "INSERT INTO wh_boxReturnPartSku (boxId, sku, num, user, time) VALUES ('{$boxId}', '{$sku}', '{$num}', '{$_SESSION['userId']}', '{$time}')";
			        $logQuery   = $this->dbConn->query($logSql);//添加箱子部份退货记录
			        if($logQuery === false) {
			            $this->dbConn->rollback();
			        	return 'failure';
			        }
            		$sql 	= "SELECT status FROM wh_boxinuse WHERE boxid = '{$boxId}'";
            		$result = $this->dbConn->fetch_first($sql);
    				$status = $result['status'];
    				if($status == '3'){//箱子在已发柜状态
	    				$updBoxStock    	= "UPDATE wh_inboxStock SET num = num + $num WHERE sku = '{$sku}'";
	            		$rtnUpdBoxStock  	= $this->dbConn->query($updBoxStock);//扣除封箱库存数量
	            		if($rtnUpdBoxStock === false) {
	                		$this->dbConn->rollback();
	            			return 'failure';
	            		}
    				}
    				$this->dbConn->commit();
			        return 'success';
    			}
    		}else{
    			return 'Null';
    		}
    	}
    }
}
