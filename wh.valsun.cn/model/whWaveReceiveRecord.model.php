<?php

/*
 * 配货单收货记录表Model
 * ADD BY cmf 2014.7.22
 * Modify czq 2014.07.23
 */
class WhWaveReceiveRecordModel extends WhBaseModel {
	
    /**
     * WhWaveReceiveRecordModel::insert_receive_data()
     * 插入收获记录
     * @param int $wave_id 配货单ID
     * @param array $areaArr 配货单区域
     * @return
     */
    public static function insert_receive_data($wave_id, $areaArr, $storeyArr){
        self::initDB();
        $wave_id    =   intval($wave_id);
        if(is_array($areaArr) && $wave_id){
            $data   =   ''; //拼接插入语句
            foreach($areaArr as $area){
                $data   .=  "('{$wave_id}', '{$storeyArr[$area['floorId']]}', '{$area['areaName']}'),";
            }
            $data   =   trim($data, ',');
            $sql    =   'insert ignore into '.self::$tablename.' (waveId, floor, area) values '.$data;
            //echo $sql."<br />";
            return self::$dbConn->query($sql);
        }else{
            return FALSE;
        }
    }
    
    /**
     * 获取下个配货路由
     * @param  number $wave_id
     * @return array 
     * @author czq
     */
    public static function getNextReceiveRoute($wave_id){
    	$sql = "SELECT a.area FROM wh_wave_receive_record a 
    			LEFT JOIN wh_wave_route_relation b ON a.area = b.name 
    			WHERE a.waveId = '{$wave_id}' AND a.scanStatus != 2 AND a.is_delete = 0 
    			AND b.is_delete = 0 AND b.routeType = 3  ORDER BY b.route ASC limit 3 ";
    	return self::query($sql);
    }
    
}
?>
