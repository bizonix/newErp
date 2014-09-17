<?php

/*
 * 新加坡DHL GM箱号打印记录表Model
 * ADD BY Gary 2014.8.17
 * 
 */
class WhOrderPartionPrintDhlModel extends WhBaseModel {
	/**
	 * WhOrderPartionPrintDHLModel::get_packageInfo()
	 * 获取新加坡DHL口袋信息
	 * @param mixed $select
	 * @param mixed $where
	 * @param bool $fetch_row
	 * @return void
	 */
	public static function get_packageInfo($select, $where, $fetch_row=FALSE){
	   self::initDB();
       $select  =   array2select($select);
       $where   =   array2where($where);
       $sql     =   "select {$select} from ".self::$tablename.' where '.$where;
       $sql     =   self::$dbConn->query($sql);
       $fetch   =   $fetch_row ? 'fetch_array' : 'fetch_array_all';
       $res     =   self::$dbConn->$fetch($sql);
       return $res;
	}
    
    /**
     * WhOrderPartionPrintDHLModel::insert_data()
     * 插入新加坡口袋信息
     * @param mixed $data
     * @return void
     */
    public static function insert_data($data){
        self::initDB();
        $sql    =   'insert into '.self::$tablename.' set '.array2sql($data);
        $sql    =   self::$dbConn->query($sql);
        return self::$dbConn->insert_id();
    }
}
?>
