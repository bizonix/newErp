<?php

/*
 * 区域负责人关系表Model
 * ADD BY cmf 2014.7.22
 */
class WhWaveAreaUserRelationModel extends WhBaseModel {
	
	public static function getUserAreas($uid = 0){
		if(!$uid)$uid = 0;
		$sql = "select a.areaId, b.areaName from wh_wave_area_user_relation a 
				left join wh_wave_area_info b ON(a.areaId=b.id AND b.is_delete=0)
				where a.userId='$uid' AND a.is_delete=0 group by b.id";
		$list = WhWaveAreaUserRelationModel::query($sql);
		return $list ? $list : array();
	}
    
    /**
     * WhWaveAreaUserRelationModel::get_user_by_areaName()
     * 
     * @param mixed $areas
     * @return void
     */
    public static function get_user_by_areaName($areas){
        self::initDB();
        $areas  =   array2select($areas);
        $areas  =   str_replace(',', "','", $areas);
        $sql    =   "select a.areaName as area,c.global_user_name as user from wh_wave_area_info a left join wh_wave_area_user_relation b
                        on a.id = b.areaId left join power_global_user c on c.global_user_id = b.userId where a.areaName in ('{$areas}')";
        //echo $sql;exit;
        $sql    =   self::$dbConn->query($sql);
        $res    =   self::$dbConn->fetch_array_all($sql);
        return $res;
    }
	
}
?>
