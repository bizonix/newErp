<?php

/*
 * "发货单与跟踪号之间的关联表-记录表"Model
 * ADD BY cxy 2014.8.7
 */
class WhOrderTracknumberModel extends WhBaseModel {
    //根据发货单号查询数据表的记录
    /**
     * WhOrderTracknumberModel::select_TracknumberByOrderId()
     * @author cxy
     * @param mixed $shipOrderId 发货单号
     * @return array 二维数组
     */
    public static function select_TracknumberByOrderId($shipOrderId){
        self::initDB();
        $tableName = self::$tablename;
        $sql       = "select * from {$tableName} where `shipOrderId` = '{$shipOrderId}' and is_delete = 0";
        $sql       = self::$dbConn->query($sql);
        $res       = self::$dbConn->fetch_array_all($sql);
        return $res;       
    }
        //根据跟踪号查询数据表的记录
    /**
     * WhOrderTracknumberModel::select_ByTracknumber()
     * @author cxy
     * @param mixed $ebay_id 追踪号
     * @return array 一维数组
     */
    public static function select_ByTracknumber($ebay_id){
        self::initDB();
        $tableName = self::$tablename;
        $sql       = "select * from {$tableName} where `tracknumber` = '{$ebay_id}' and is_delete = 0";
        $sql       = self::$dbConn->query($sql);
        $res       = self::$dbConn->fetch_array($sql);
        return $res;       
    }
    /**
	 * WhOrderTracknumberModel::getTracknumberByShipOrderOd()
	 * 获取发货单的跟踪号 
	 * @param mixed $shipOrderId 发货单ID 单个发货单号 或 多个发货单数组
     * @author Gary
	 * @return void
	 */
	public function getTracknumberByShipOrderId($shipOrderId, $fetch_one = FALSE){
	   self::initDB();
       $shipOrderId =   array2where($shipOrderId);
       $sql         =   'select shipOrderId, tracknumber from '.self::$tablename.' where '.$shipOrderId;
       //echo $sql;exit;
       $sql         =   self::$dbConn->query($sql);
       $func        =   $fetch_one ? 'fetch_array' : 'fetch_array_all';
       $res         =   self::$dbConn->$func($sql);
       return $res;
	}
}