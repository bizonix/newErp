<?php

/*
 * 发货单表Model
 * ADD BY cmf 2014.7.22
 */
class whWaveMultiShipAreaRecordModel extends WhBaseModel {
	
    /**
     * whWaveMultiShipAreaRecordModel::insert_multi_ship_record()
     * 插入多料号订单区域集合表数据
     * @param array $data
     * @return void
     */
    public static function insert_multi_ship_record($data){
        self::initDB();
        $sql    =   'insert ignore into '.self::$tablename.' set '.array2sql($data);
        return self::$dbConn->query($sql);
    }
    
    /**
     * whWaveMultiShipAreaRecordModel::get_multi_ship_records()
     * 获取多料号订单区域临时信息 
     * @param array $select 查询字段 多个数组，单个字符串
     * @param array $where 数组
     * @author Gary
     * @return $res
     */
    public static function get_multi_ship_records($select, $where){
        self::initDB();
        $select     =   array2select($select);
        $where      =   array2where($where);
        $sql        =   "select $select from ".self::$tablename." where $where";
        //echo $sql;exit;
        $sql        =   self::$dbConn->query($sql);
        $res        =   self::$dbConn->fetch_array_all($sql);
        return $res;
    }
}
?>
