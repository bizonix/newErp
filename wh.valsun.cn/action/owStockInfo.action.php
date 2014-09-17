<?php
/** 
 * 提供采购系统数据
 * @author 王民伟
 */
class OwStockInfoAct{
    public static $errCode  = 0;   //错误码
    public static $errMsg   = '';   //错误消息
    
    public function act_rtnOwStockInfoCount(){
    	$type  = isset($_GET['type']) ? $_GET['type'] : '';
    	if(empty($type)){
    		self::$errCode = '404';
            self::$errMsg  = '参数错误';
            return false;
    	}else{
	    	$total = OwStockInfoModel::rtnOwStockInfoCount();
	    	self::$errCode = '200';
            self::$errMsg  = '获取数据成功';
	    	return $total;
    	}
    }
    
    public function act_rtnOwStockInfo(){
        $page    = isset($_GET['page']) ? $_GET['page'] : '1';
    	$pagenum = 100;
    	$rtnInfo = OwStockInfoModel::rtnOwStockInfo($page, $pagenum);
        if($rtnInfo !== false){
            self::$errCode = '200';
            self::$errMsg  = '数据获取成功';
            return json_encode($rtnInfo);
        }else{
            self::$errCode = '404';
            self::$errMsg  = '获取不到数据';
            return false;
        }
    }
    
    public function act_getOwSkuReachDay(){
    	$sku    = isset($_GET['sku']) ? $_GET['sku'] : '';
    	if(empty($sku)){
    		self::$errCode = '404';
            self::$errMsg  = '参数错误';
            return false;
    	}
    	$rtnDay  = OwStockInfoModel::getOwSkuReachDay($sku);
    	self::$errCode 	= '200';
        self::$errMsg  	= '天数获取成功';
        return $rtnDay;
    }
    
	/**
	 * 海外仓系统获取海运在途箱子总个数
	 */
	public static function act_getOwOnLoadBoxCount(){
		$totalnum 			= OwStockInfoModel::getOwOnLoadBoxCount();
		return $totalnum;
	}
    
    /**
     * 海外仓系统获取海运在途箱子信息
     */
    public function act_getOwOnLoadBoxInfo(){
    	$type    = isset($_GET['type']) ? $_GET['type'] : '';
    	if(empty($type)){
    		self::$errCode = '404';
            self::$errMsg  = '参数错误';
            return false;
    	}
    	$page    = isset($_GET['page']) ? $_GET['page'] : 1;
		$pagenum = isset($_GET['pagenum']) ? $_GET['pagenum'] : 200;
		$rtnData = OwStockInfoModel::getOwOnLoadBoxInfo($page, $pagenum);
		if(!empty($rtnData)){
			self::$errCode  = '200';
			self::$errMsg  	= '获取数据成功';
		}else{
			self::$errCode  = '404';
			self::$errMsg  	= '没有数据';
		}
		return json_encode($rtnData);
    }
    
     /**
     * 获取料号已发货数量
     * time:2014-07-25
     * name:wangminwei
     */
    public static function act_getSkuSendQty(){
    	$sku = isset($_GET['sku']) ? $_GET['sku'] : '';
    	if(empty($sku)){
    		self::$errCode  = '404';
			self::$errMsg  	= '参数传递有误';
			return false;
    	}
    	self::$errCode  = '200';
		self::$errMsg  	= '获取料号发货数量成功';
    	$totalNum 		= OwStockInfoModel::getSkuSendQty($sku);
    	return $totalNum;
    }
}

?>