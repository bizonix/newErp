<?php

/*
 * 路由关系表Model
 * ADD BY cmf 2014.7.22
 */
class WhWaveRouteRelationModel extends WhBaseModel {
	/**
	 * WhWaveRouteRelationModel::delete_relation()
	 * 物理删除索引关系 
	 * @param mixed $where
     * @author Gary
	 * @return
	 */
	public static function delete_relation($where){
	   self::initDB();
       $where   =   array2where($where);
       $sql     =   'delete from '.self::$tablename.' where '.$where;
       //echo $sql;exit;
       return self::$dbConn->query($sql);
	}
    
    /**
     * WhWaveRouteRelationModel::insert_data()
     * 插入新索引关系 
     * @param mixed $insert_data
     * @autor Gary
     * @return void
     */
    public static function insert_data($insert_data){
        self::initDB();
        $string     =   '';
        foreach($insert_data as $data){
            $string .=  "('{$data['routeId']}', '{$data['name']}', '{$data['route']}', '{$data['routeType']}'),";
        }
        $string =   trim($string, ',');
        $sql    =   'insert into '.self::$tablename.' (routeId, name, route, routeType) values '.$string;
        //echo $sql;exit;
        return  self::$dbConn->query($sql);
    }
}
?>
