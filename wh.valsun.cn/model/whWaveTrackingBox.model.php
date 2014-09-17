<?php

/*
 * 快递称重的时候箱子和跟踪号数量表Model
 * ADD BY cxy 2014.8.6
 */
class WhWaveTrackingBoxModel extends WhBaseModel {
    
    /**
     * WhWaveTrackingBoxModel::select_by_shipOrderId()
     * 根据发货单号获取快递需要的箱子和跟踪号数量
     * @author cxy
     * @param mixed $shipOrderId
     * @return
     */
    public static function select_by_shipOrderId($shipOrderId){
        self::initDB();
        $tableName = self::$tablename;
        $sql       = "select * from {$tableName} where `shipOrderId` = '{$shipOrderId}' and is_delete = 0";
        $sql       = self::$dbConn->query($sql);
        $res       = self::$dbConn->fetch_array($sql);
        return $res; 
    }


}    
 
 
 ?>