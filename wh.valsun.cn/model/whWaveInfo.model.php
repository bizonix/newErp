<?php
/*
 * 配货单基本信息表Model
 * ADD BY cmf 2014.7.22
 * Modify By czq 2014.07.23
 * 
 */
class WhWaveInfoModel extends WhBaseModel {
	/*
		通过波次ID生成编号
	*/
	public static function number_encode($id){
        $wave_rules =   C('wave_rules');
        $length     =   $wave_rules['length'] - strlen($wave_rules['pre']); //出去前缀长度后填充的长度
        $number     =   parent::number_encode($id, $length, $wave_rules['pre']);
        return $number;
	}
	
    /**
     * WhWaveInfoModel::getWaveList()
     * 获取用户负责区域的配货单列表
     * @param  $uid
     * @author cmf
     * @return
     */
	public static function getWaveList(){
		$uid = $_SESSION['userId'];
		$wavelist = array();
		/*$sql = "select a.id as waveId from wh_wave_info a 
				left join wh_wave_receive_record b ON(a.id=b.waveId)
				left join wh_wave_area_info c ON(c.areaName=b.area AND c.is_delete=0)
				left join wh_wave_route_relation d ON(c.id=d.name AND d.is_delete=0)
				left join wh_wave_area_user_relation e ON(e.userId='".$uid."' AND e.areaId=c.id)					
				where b.scanStatus='0' AND b.is_delete=0 AND e.userId IS NOT NULL group by a.id order by a.printStorey DESC, d.route asc, a.id asc limit 20";*/
		$sql = "select a.id as waveId,a.number from wh_wave_info a 
				left join wh_wave_receive_record b ON(a.id=b.waveId)
				left join wh_wave_area_info c ON(c.areaName=b.area AND c.is_delete=0)
				left join wh_wave_route_relation d ON(c.areaName=d.name AND d.is_delete=0)
				left join wh_wave_area_user_relation e ON(e.userId='".$uid."' AND e.areaId=c.id)				
				where b.scanStatus='0' AND b.is_delete=0 AND e.userId IS NOT NULL group by a.id order by a.printStorey DESC, d.route asc, a.id asc limit 20";
		$wavelist = WhWaveInfoModel::query($sql);
		//foreach($wavelist as $key => $val){
//			$val['number'] = WhWaveInfoModel::number_encode($val['waveId']);
//			$wavelist[$key] = $val;
//		}
		return $wavelist;
	}
    
    /**
     * WhWaveInfoModel::get_wave_info()
     * 获取配货单信息
     * @param array $select
     * @param array $where
     * @return
     */
    public static function get_wave_info($select, $where){
        self::initDB();
        $select     =   array2select($select);
        $where      =   array2where($where);
        $tablename  =   self::$tablename;
        $sql    =   "select {$select} from {$tablename} where $where and is_delete = 0";
        $sql    =   self::$dbConn->query($sql);
        $res    =   self::$dbConn->fetch_array_all($sql);
        return $res;
    }
    
    /**
     * WhWaveInfoModel::get_wave_info_by_union_table()
     * 联表查询配货单信息（配货单管理界面查询功能）
     * @param mixed $select
     * @param array $where
     * @return void
     */
    public static function get_wave_info_by_union_table($select, $where){
        self::initDB();
        $select =   array2select($select);
        $where  =   array2where($where);
        $sql    =   'select '.$select.' from wh_wave_info a left join wh_wave_receive_record c on c.waveId = a.id
                        left join wh_wave_area_info b on b.areaName = c.area left join wh_wave_area_user_relation d
                        on d.areaId = b.id where '.$where;
        //echo $sql;
        $sql    =   self::$dbConn->query($sql);
        $res    =   self::$dbConn->fetch_array_all($sql);
        return $res;
    }
    /**
     * WhWaveInfoModel::count_all_results()
     * 获取总数
     * @param mixed $where
     * @return void
     */
    public static function count_all_results($where){
        self::initDB();
        $where  =   array2where($where);
        $sql    =   'select count(*) from wh_wave_info a left join wh_wave_receive_record c on c.waveId = a.id
                        where c.area=a.startArea and '.$where;
        $sql    =   self::$dbConn->query($sql);
        $res    =   self::$dbConn->fetch_array_all($sql);
        return count($res);
    }
    
    /**
     * WhWaveInfoModel::get_max_id()
     * 获取配货信息表中最大的ID
     * @return void
     */
    public static function get_max_id(){
        self::initDB();
        $tablename  =   self::$tablename;
        $sql    =   "select max(id) from {$tablename}";
        $sql    =   self::$dbConn->query($sql);
        $res    =   self::$dbConn->fetch_array($sql);
        if(empty($res)){
            return FALSE;
        }
        return $res['id'] ? $res['id'] : 0;
    }
    
    /**
     * WhWaveInfoModel::insert_wave_info()
     * 配货信息表插入数据
     * @param array $data 插入数据的键值对(针对一条数据)
     * @return void
     */
    public static function insert_wave_info($data){
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
