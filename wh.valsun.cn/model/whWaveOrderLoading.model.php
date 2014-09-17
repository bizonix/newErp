<?php

/*
 * 装车扫描Model
 * ADD BY cxy 2014.8.6
 */
class WhWaveOrderLoadingModel extends WhBaseModel {
    
    //查询装车扫描纪录
    /**
     * WhWaveOrderLoadingModel::select_loading()
     * @author cxy
     * @param mixed $shipOrderGroup 口袋编号
     * @return array 一维数组
     */
    public static function select_loading($shipOrderGroup){
        self::initDB();
        $tableName = self::$tablename;
        $sql       = "select * from {$tableName} where `packageId` = '{$shipOrderGroup}' and is_delete = 0";
        $sql       = self::$dbConn->query($sql);
        $res       = self::$dbConn->fetch_array($sql);
        return $res;
    }
    //发货单的装车记录
     /**
      * WhWaveOrderLoadingModel::select_loading_express()
      * @author cxy
      * @param mixed $shipOrderGroup
      * @param mixed $ebay_id
      * @return
      */
     public static function select_loading_express($shipOrderGroup,$ebay_id){
        self::initDB();
        $tableName = self::$tablename;
        $sql       = "select * from {$tableName} where `packageId` = '{$shipOrderGroup}' and tracking = '{$ebay_id}' and is_delete = 0";
        $sql       = self::$dbConn->query($sql);
        $res       = self::$dbConn->fetch_array_all($sql);
        return $res;
    }
     /**
      * WhWaveOrderLoadingModel::select_loading_count()
      * 获取某个发货单的装车扫描的记录次数，特别是一个发货单多个跟踪号的数据
      * @author cxy
      * @param mixed $shipOrderGroup
      * @return
      */
     public static function select_loading_count($shipOrderGroup){
        self::initDB();
        $tableName = self::$tablename;
        $sql       = "select count(*) as muns from {$tableName} where `packageId` = '{$shipOrderGroup}' and is_delete = 0";
        $sql       = self::$dbConn->query($sql);
        $res       = self::$dbConn->fetch_array($sql);
        return $res;
    }
}    
 
 
 ?>