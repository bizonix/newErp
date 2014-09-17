<?php

/*
 * 区域表Model
 * ADD BY cmf 2014.7.22
 */
class WhWaveAreaInfoModel extends WhBaseModel {

	/**
	 * WhWaveAreaInfoModel::get_area_info()
	 * 获取区域信息
	 * @param mixed $select  查询字段 多个字段数组，单个字符串即可
	 * @param array $area_ids 区域ID 多个字段数组，单个字符串即可
	 * @param array $areaName 区域名称 多个字段数组，单个字符串即可
	 * @return
	 */
	public static function get_area_info($select, $area_ids = '', $areaName = ''){
	   self::initDB();
       $tableName   =   self::$tablename;
       $select  =   array2select($select);
       $sql     =   "select {$select} from {$tableName} where ";
       if($area_ids){
            $area_ids   =   is_array($area_ids) ? array_unique($area_ids) : $area_ids;
            //$area_ids   =   array2select($area_ids);
            //$area_ids   =   str_replace('`', "'", $area_ids);
            $area_ids   =   is_array($area_ids) ? implode("','", $area_ids) : $area_ids;
            $sql            .=  "id in ('{$area_ids}') and ";
       }
       if($areaName){
            $areaName   =   is_array($areaName) ? array_unique($areaName) : $areaName;
            //$areaName   =   array2select($areaName);
            //$areaName   =   str_replace('`', "'", $areaName);
            $areaName   =   is_array($areaName) ? implode("','", $areaName) : $areaName;
            $sql            .=  "areaName in ('{$areaName}') and ";
       }
       //echo $sql;
       $sql     .=  'is_delete = 0';
       $sql     =   self::$dbConn->query($sql);
       $res     =   self::$dbConn->fetch_array_all($sql);
       return $res;
	}
    
    /**
     * WhWaveAreaInfoModel::get_area_by_floorId()
     * 联表查询区域信息 根据楼层索引排序获取 
     * @param mixed $floor
     * @return void
     */
    public static function get_area_by_floorId(){
        self::initDB();
        //$floor  =   is_array($floor) ? $floor : array($floor);
//        $floor  =   array_filter(array_map('intval', $floor));
//        $floor  =   implode(',', $floor);
//        $sql    =   'select * from '.self::$tablename.' where floorId in ('.$floor.') and is_delete = 0';
        $sql    =   'select a.* from wh_wave_area_info a left join wh_wave_route_relation b on a.floorId = b.routeId where b.routeType = 2 order by b.route asc, a.areaName asc';
        //echo $sql;exit;
        $sql    =   self::$dbConn->query($sql);
        $res    =   self::$dbConn->fetch_array_all($sql);
        return $res;
    }
}
?>
