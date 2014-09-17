<?php
/*
 * 海外仓库存信息相关
 * 
 */
class OwStockInfoModel {
    public static $errCode  = 0;
    public static $errmsg   = '';  
    
    /*
     * 构造函数
     */
    public function __construct(){
        global $dbConn;
    }
   	
    /**
     * 获取料号海运在途数量，海运在途相当于补货单状态在发柜下面箱号的SKU数量
     */
    public static function getOnRoadStock($sku){
    	global $dbConn;
    	$sql 	     = "SELECT SUM(a.num) AS Qty FROM wh_boxDetail AS a JOIN wh_boxinuse AS b ON a.boxId = b.boxid ";
    	$sql        .= "JOIN wh_preplenshOrder AS c ON  b.replenshId = c.ordersn WHERE c.status IN (1,2) AND a.sku = '{$sku}'";
    	$rtnData     = $dbConn->fetch_first($sql);
        $qty 		 = $rtnData['Qty'];
    	if(!is_null($qty)){
        	return $qty;
        }else{
        	return 0;
        }
    	
    }
    
    /**
     * 返回海运料号个数
     */
    public static function rtnOwStockInfoCount(){
    	global $dbConn;
    	$sql 	= "SELECT COUNT(*) AS total FROM wh_inboxStock ";
    	$query  = $dbConn->fetch_first($sql);
    	return $query['total'];
    }

    /**
     * 返回料号海运在途数量及封箱库存数量
     */
    public static function rtnOwStockInfo($page, $pagenum){
    	global $dbConn;
    	$start	= ($page - 1) * $pagenum;
    	$sql 	= "SELECT sku, num FROM wh_inboxStock ORDER BY id LIMIT $start, $pagenum ";
    	$query  = $dbConn->query($sql);
    	if($query){
    		$rtnData = $dbConn->fetch_array_all($query);
    		if(!empty($rtnData)){
    			$mark = 0;
    			foreach($rtnData as $k => $v){
    				$sku 		= $v['sku'];
    				$num        = $v['num'];
    				$onRoadQty 	= self::getOnRoadStock($sku);
    				$rtnInfo[$mark]['sku'] 		= $sku;
    				$rtnInfo[$mark]['num'] 		= $num;//封箱库存
    				$rtnInfo[$mark]['roadQty'] 	= $onRoadQty;
    				$mark++;
    			}
    			return $rtnInfo;
    		}else{
    			return false;
    		}
    	}else{
    		return false;
    	}
    }
    
    /**
     *根据料号获取海运在途到港最短天数
     */
    public static function getOwSkuReachDay($sku){
    	global $dbConn;
    	$sql 		= "SELECT distinct(c.arriveday) as day FROM wh_boxDetail AS a JOIN wh_boxinuse AS b ON a.boxId = b.boxid ";
    	$sql       .= "JOIN wh_preplenshOrder AS c ON b.replenshId = c.ordersn WHERE a.sku = '{$sku}' AND c.status = 2 AND c.arriveday != 0";
    	$query  	= $dbConn->query($sql);
    	$data   	= $dbConn->fetch_array_all($query);
    	$tmpday     = 1000;
    	if(!empty($data)){
    		foreach($data as $k => $v){
    			$day = $v['day'];
    			if($day < $tmpday){
    				$tmpday = $day;
    			}else{
    				continue;
    			}
    		}
    	}
    	return $tmpday;
    }
    
	/**
	 * 获取海运在途箱子个数
	 */
	public static function getOwOnLoadBoxCount(){
		global $dbConn;
		$totalnum   = 0;
		$sql 		= "SELECT COUNT(*) AS totalnum FROM wh_preplenshOrder AS a JOIN wh_boxinuse AS b ON a.ordersn = b.replenshId ";
    	$sql 	   .= " JOIN wh_boxDetail AS c ON b.boxid = c.boxId WHERE a.status = 2";
		$data  		= $dbConn->fetch_first($sql);
		if(!empty($data)){
			$totalnum = $data['totalnum'];
		}
		return $totalnum;
	}
    
    /**
     * 获取海运在途箱子信息
     */
    public static function getOwOnLoadBoxInfo($page, $pagenum){
    	global $dbConn;
    	$rtnInfo 	= array();
    	$start      = ($page - 1) * 200;
		$pagenum    = 200;
    	$sql 		= "SELECT a.ordersn, a.status, c.boxId, c.sku, c.num FROM wh_preplenshOrder AS a JOIN wh_boxinuse AS b ON a.ordersn = b.replenshId ";
    	$sql 	   .= " JOIN wh_boxDetail AS c ON b.boxid = c.boxId WHERE a.status = 2 ";
    	$sql       .= " limit $start, $pagenum ";
    	$query  	= $dbConn->query($sql);
    	$data   	= $dbConn->fetch_array_all($query);
    	if(!empty($data)){
    		$rtnInfo = $data;	
    	}
    	return $rtnInfo;
    }

    /**
     * 获取料号已发货数量
     * time:2014-07-25
     * name:wangminwei
     */
    public static function getSkuSendQty($sku){
    	global $dbConn;
    	$sql 		= "SELECT sum(c.num) AS totalNum FROM wh_preplenshOrder AS a JOIN wh_boxinuse AS b ON a.ordersn = b.replenshId ";
    	$sql 	   .= " JOIN wh_boxDetail AS c ON b.boxid = c.boxId WHERE a.status IN(2,3) AND c.sku = '{$sku}'";
    	$data  	    = $dbConn->fetch_first($sql);
    	$totalNum   = $data['totalNum'];
    	if(is_null($totalNum)){
    		$totalNum = 0;
    	}
    	return $totalNum;
    }
    /**
    * 海外仓料号入库判断料号状态，如果状态为暂时停售，自动修改为在线
    * time:2014-07-29
    * name:wangminwei
    */
    public static function getAndUpdOverseaSkuStatus($sku){
    	$paramArr['method']   	= 'pur.AutoUpdOverseaSkuStatus';  //API名称
		$paramArr['sku'] 		= $sku;
        $rtnInfo	    		= UserCacheModel::callOpenSystem($paramArr);
        if (FALSE === $rtnInfo) {                                                   
            self::$errmsg   = '请求开放系统出错!';
        	return FALSE;
        }
        $code       = isset($rtnInfo['code']) ? trim($rtnInfo['code']) : '';
        if ($code !== 'success') {                                                    
        	self::$errmsg  = $messageInfo['msg'];
        	return FALSE;
        } 
    }
}
