<?php
/**
 * WhOldPositionRelationModel
 * 新旧仓位关联model
 * @package 仓库系统
 * @author Gary
 * @copyright 2014
 * @version $Id$
 * @access public
 */
class WhOldPositionRelationModel extends WhBaseModel {
    
    /**
     * WhWaveInfoModel::insert_wave_info()
     * 配货信息表插入数据
     * @param array $data 插入数据的键值对(针对一条数据)
     * @return void
     */
    public static function insert_data($data){
        self::initDB();
        $sql    =   'insert into '.self::$tablename.' set '.array2sql($data);
        $sql    =   self::$dbConn->query($sql);
        if($sql){
            return self::$dbConn->insert_id();
        }else{
            return FALSE;
        }
    }
    
    /**
     * WhWaveInfoModel::update_wave_info()
     * 更新配货单信息
     * @param array $update 更新字段内容
     * @param array $where 条件内容
     * @return void
     */
    public static function update_wave_info($update, $where){
        self::initDB();
        $sql    =   'update '.self::$tablename.' set '.array2sql($update).' where '.array2where($where);
        //echo $sql;
        $sql    =   self::$dbConn->query($sql);
        return $sql;
    }
	
}
?>
