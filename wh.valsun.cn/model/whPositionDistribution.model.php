<?php

/*
 * 仓位表Model
 * ADD BY cmf 2014.7.22
 */
class WhPositionDistributionModel extends WhBaseModel {
    
	/**
	 * WhPositionDistributionModel::get_position_info()
	 * 获取仓位信息
     * @param $select 查询字段 数组
     * @param array $position_ids 仓位id集合 多个数组 单个string
     * @param $pName 字符串
	 * @return void
	 */
	public static function get_position_info($select, $position_ids = '', $pName = ''){
	   self::initDB();
       $tableName   =   self::$tablename;
       $select  =   array2select($select);
       $sql     =   "select {$select} from {$tableName} where ";
       if(!empty($position_ids)){
            $position_ids   =   is_array($position_ids) ? array_unique($position_ids) : $position_ids;
            $position_ids   =   array2select($position_ids);
            $position_ids   =   str_replace('`', "'", $position_ids);
            $sql            .=  "id in ({$position_ids}) and ";
       }
       if($pName){
            $sql            .=  "pName = '{$pName}' and ";
       }
       $sql     .=  'id > 0';
       //echo $sql;exit;
       $sql     =   self::$dbConn->query($sql);
       $res     =   self::$dbConn->fetch_array_all($sql);
       return $res;
	}
    
    /**
     * WhPositionDistributionModel::get_position_info_union_area()
     * 通过区域索引获取关联区域
     * @author Gary
     * @return void
     */
    public static function get_position_info_union_area(){
        self::initDB();
        $sql    =   'select a.id, a.pName,a.x_alixs,a.y_alixs,a.z_alixs,a.areaId from wh_position_distribution a
                        left join wh_wave_route_relation b on a.areaId = b.routeId where b.routeType = 3 and b.is_delete = 0
                        order by b.route asc, a.pName asc';
        //echo $sql;exit;
        $sql    =   self::$dbConn->query($sql);
        $res    =   self::$dbConn->fetch_array_all($sql);
        return $res;
    }
    
    /**
     * WhPositionDistributionModel::delete_data()
     * 逻辑删除仓位数据
     * @param mixed $where
     * @return void
     */
    public static function delete_data($where){
        self::initDB();
        $where  =   array2where($where);
        $sql    =   'delete from '.self::$tablename.' where '.$where;
        return self::$dbConn->query($sql);
    }
}
?>
