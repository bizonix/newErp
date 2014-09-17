<?php

/*
 * 发货组复核Model
 * ADD BY cxy 2014.7.30
 */
class WhWaveOrderPartionShippingReviewModel extends WhBaseModel {
    
    
    /**
     * WhWaveOrderPartionShippingReviewModel::get_pocket()
     * 查询口袋编号的信息
     * @author cxy
     * @param mixed $shipOrderGroup 口袋编号
     * @return array $res 一维数组
     */
    public static function get_pocket($shipOrderGroup){
        self::initDB();
        $tableName = self::$tablename;
        $sql       = "select * from {$tableName} where `packageId` = '{$shipOrderGroup}' and is_error = 0";
        $sql       = self::$dbConn->query($sql);
        $res       = self::$dbConn->fetch_array($sql);
        return $res;
    } 
    
   
   //更新发货操作记录表
    /**
     * WhWaveOrderPartionShippingReviewModel::updateOrderRecords()
     * @author cxy
     * @param mixed $orderid 发货单号
     * @param mixed $userId  操作人ID
     * @return mixed true是更新成功返回1，false是更新失败，返回0
     */
    public static function updateOrderRecords($orderid,$userId){
		self::initDB();
		$sql	 =	"UPDATE wh_shipping_order_records SET shippingGroupId={$userId},shippingGroupTime=".time()." where shipOrderId={$orderid} ";
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			return true;
		}else{
			return false;	
		}
    }
    //更新发货组复核表
    /**
     * WhWaveOrderPartionShippingReviewModel::update_shipping_review()
     * @author cxy
     * @param mixed $packageId 口袋编号
     * @param mixed $orders 随机扫描的发货单号
     * @return mixed true是更新成功返回1，false是更新失败，返回0
     */
    public static function update_shipping_review($packageId,$orders){
        self::initDB();
        $tableName = self::$tablename;
        $sql	   = "UPDATE $tableName SET orders ='{$orders}' where packageId = {$packageId} ";
		$query	   = self::$dbConn->query($sql);		
		if($query){
			return true;
		}else{
			return false;	
		}
    }
  
}
?>