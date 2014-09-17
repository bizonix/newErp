<?php

/*
 * 分区复核Model
 * ADD BY cxy 2014.7.30 wh_wave_order_partion_scan_review 
 */
class WhWaveOrderPartionScanReviewModel extends WhBaseModel {                  
    //根据口袋编号获得发货单号WhBaseModel
    /**
     * WhWaveOrderPartionScanReviewModel::get_shipping_review()
     * @author cxy
     * @param mixed $packageId 口袋编号
     * @return array 二维数组
     */
    public static function get_shipping_review($packageId){
        self::initDB();
        $tableName =   self::$tablename;
        $sql       = "select * from {$tableName} where `packageId` = '{$packageId}' and errorPartion is NULL";
        $sql       =   self::$dbConn->query($sql);
        $res       =   self::$dbConn->fetch_array_all($sql);
        return $res;
    } 
   
   //根据发货单号获取分区复核信息
    /**
     * WhWaveOrderPartionScanReviewModel::get_reviewById()
     * @author cxy
     * @param mixed $shipOrderId 发货单号
     * @return array 一维数组
     */
    public static function get_reviewById($shipOrderId){
        self::initDB();
        $tableName = self::$tablename;
        $sql       = "select * from {$tableName} where `shipOrderId` = '{$shipOrderId}'";
        $sql       = self::$dbConn->query($sql);
        $res       = self::$dbConn->fetch_array($sql);
        return $res;
    }
   
   //更新发货操作记录表
    /**
     * WhWaveOrderPartionScanReviewModel::updateOrderRecords()
     * @author cxy
     * @param mixed $orderid 发货单号
     * @param mixed $userId 操作人ID
     * @return mixed true是更新成功返回1，false是更新失败，返回0
     */
    public static function updateOrderRecords($orderid,$userId){
		self::initDB();
		$sql	 =	"UPDATE wh_shipping_order_records SET partionCheckingId={$userId},partionCheckingTime=".time()." where shipOrderId={$orderid} ";
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			return true;;	
		}else{
			return false;	
		}
    }
    
    //更新发货单表的发货状态
    /**
     * WhWaveOrderPartionScanReviewModel::updateShippingOrderStatus()
     * @author cxy
     * @param mixed $orderid 发货单号
     * @param mixed $status 发货单状态
     * @return  true是更新成功返回1，false是更新失败，返回0
     */
    public static function updateShippingOrderStatus($orderid,$status){
		self::initDB();
		$sql	 =	"UPDATE wh_shipping_order SET orderStatus=$status where id={$orderid} ";
		$query	 =	self::$dbConn->query($sql);		
		if($query){		
			return true;;	
		}else{
			return false;	
		}
	}
    
    //统计分区复核表的总数
    /**
     * WhWaveOrderPartionScanReviewModel::get_countReview()
     * @author cxy
     * @param mixed $packageId 口袋编号
     * @return array 一维数组
     */
    public static function get_countReview($packageId){
        self::initDB();
        $tableName =   self::$tablename;
        $sql       = "select count(*) mun from {$tableName} where `packageId` = '{$packageId}'";
        $sql       =   self::$dbConn->query($sql);
        $res       =   self::$dbConn->fetch_array($sql);
        return $res;
    }
     
    //根据口袋编号得到发货单的数量和重量，扫描人
    /**
     * WhWaveOrderPartionScanReviewModel::get_shippingNews()
     * @author cxy
     * @param mixed $packageId 口袋编号
     * @return array 一维数组 
     */
    public static function get_shippingNews($packageId){
        self::initDB();
        $tableName = self::$tablename;
        $sql       = "select count(a.packageId) as review_num, sum(b.orderWeight) as review_weight,a.userId from $tableName a left join wh_shipping_order b on a.shipOrderId = b.id where a.packageId = '{$packageId}'   and a.errorPartion is NULL";
        $sql       = self::$dbConn->query($sql);
        $res       = self::$dbConn->fetch_array($sql);
        return $res;
    }
}
?>