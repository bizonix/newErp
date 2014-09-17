<?php

/*
 * 订单分区明细表Model
 * ADD BY cxy 2014.7.31
 */
class WhOrderPartionRecordsModel extends WhBaseModel {
	
	/**
	 *	通过分区ID和用户ID获取订单分区记录
	 *  @param $partitionId
	 *  @param $userId
	 *  @return array()
	 *  @author cmf
	 */
	public function getPartionRecords($partitionId = 0, $userId = 0){
		if(!$partitionId || !$userId){
			return array();
		}
		$result = WhOrderPartionRecordsModel::find("partitionId='".$partitionId."' AND packageId=0 AND scanUserId='".$userId."' group by packageId", "count(shipOrderId) as totalnum,sum(weight) as totalweight");
		return $result;
	}	
	
    //根据发货单号口袋编号查询分区明细列表
    /**
     * WhOrderPartionRecordsModel::get_OrderPartionRecords()
     * @author cxy
     * @param mixed $shipOrderId 口袋编号
     * @return array 一维数组
     */
    public static function get_OrderPartionRecords($shipOrderId){
        self::initDB();
        $tableName = self::$tablename;
        $sql       = "select * from {$tableName} where `shipOrderId` = '{$shipOrderId}'";
        $sql       = self::$dbConn->query($sql);
        $res       = self::$dbConn->fetch_array($sql);
        return $res;
    } 
   
   //根据口袋编号得到分区明细表的总数
    /**
     * WhOrderPartionRecordsModel::get_partionCount()
     * @author cxy
     * @param mixed $packageId 口袋编号
     * @return array一维数组
     */
    public static function get_partionCount($packageId){
        self::initDB();
        $tableName = self::$tablename;
        $sql       = "select count(*) mun from {$tableName} where `packageId` = '{$packageId}'";
        $sql       = self::$dbConn->query($sql);
        $res       = self::$dbConn->fetch_array($sql);
        return $res;  
    }
    //根据口袋编号查询分区明细列表
    /**
     * WhOrderPartionRecordsModel::get_OrderPartionRecordsByPackageId()
     * @author cxy
     * @param mixed $packageId 口袋编号
     * @return array 二维数组
     */
    public static function get_OrderPartionRecordsByPackageId($packageId){
        self::initDB();
        $tableName = self::$tablename;
        $sql       = "select * from {$tableName} where `packageId` = '{$packageId}'";
        $sql       = self::$dbConn->query($sql);
        $res       = self::$dbConn->fetch_array_all($sql);
        return $res;
    } 
}