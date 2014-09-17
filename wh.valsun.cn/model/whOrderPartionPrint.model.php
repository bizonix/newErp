<?php

/*
 * 订单分区Model
 * ADD BY cxy 2014.7.30
 */
class WhOrderPartionPrintModel extends WhBaseModel {
    //根据口袋编号查询分区列表
    /**
     * WhOrderPartionPrintModel::get_OrderPartion()
     *  @author cxy
     * @param mixed $packageId 口袋编号
     * @return array 一维数组
     */
    public static function get_OrderPartion($packageId){
		self::initDB();
        $tableName = self::$tablename;
        $sql       = "select * from {$tableName} where `id` = '{$packageId}'";
        $sql       = self::$dbConn->query($sql);
        $res       = self::$dbConn->fetch_array($sql);
        return $res;  
    }
}