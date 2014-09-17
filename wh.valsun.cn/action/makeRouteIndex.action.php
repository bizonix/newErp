<?php
/**
 * MakeRouteIndexAct
 * 生成区域、仓位索引
 * @package 仓库系统
 * @author Gary
 * @copyright 2014
 * @version $Id$
 * @access public
 */
class MakeRouteIndexAct extends Auth{
    static $errCode	  = 0;
	static $errMsg	  = '';
    
    function __construct(){
        parent::__construct();
    }
    
    /**
     * MakeRouteIndexAct::act_makeAreaIndex()
     * 生成区域索引
     * @author Gary
     * @return
     */
    public function act_makeAreaIndex(){
        $areaInfo   =   WhWaveAreaInfoModel::get_area_by_floorId();
        if(empty($areaInfo)){
            self::$errCode  =   201;
            self::$errMsg   =   '没有关联区域信息！';
            return FALSE;
        }
        //print_r($areaInfo);exit;
        $new_arr    =   array();
        foreach($areaInfo as $area){ //按照楼层和纵坐标生成新区域数组
            $y  =   $area['start_y_alixs'];
            $new_arr[$area['floorId']][$y][]  =   $area;
        }
        unset($areaInfo);
        $i  =   1;
        $insert_data    =   array();
        //print_r($new_arr);exit;
        foreach($new_arr as $val1){ //遍历排序
            foreach($val1 as $val2){
                foreach($val2 as $area){
                    $insert_data[]  =   array(
                                        'routeId'   =>  $area['id'],
                                        'name'      =>  $area['areaName'],
                                        'route'     =>  $i,
                                        'routeType' =>  3
                                    );
                    $i++;
                }
            }
        }
        WhBaseModel::begin();
        $where  =   'routeType = 3';
        $info   =   WhWaveRouteRelationModel::delete_relation($where);
        if(!$info){
            WhBaseModel::rollback();
            self::$errCode  =   202;
            self::$errMsg   =   '删除旧区域索引失败!';
            return FALSE;
        }
        $info   =   WhWaveRouteRelationModel::insert_data($insert_data);
        if(!$info){
            WhBaseModel::rollback();
            self::$errCode  =   203;
            self::$errMsg   =   '插入新区域索引失败!';
            return FALSE;
        }
        WhBaseModel::commit();
        self::$errCode  =   200;
        self::$errMsg   =   '更新区域索引成功!';
        return TRUE;
    }
    
    /**
     * MakeRouteIndexAct::act_makePositionIndex()
     * 生成仓位索引
     * @return void
     */
    public function act_makePositionIndex(){
        $positionInfo   =   WhPositionDistributionModel::get_position_info_union_area();
        if(empty($positionInfo)){
            self::$errCode  =   204;
            self::$errMsg   =   '没有关联的仓位信息！';
            return FALSE;
        }
        //print_r($positionInfo);exit;
        $y  =   range(0, 10); //纵坐标范围
        $num    =   floor(count($y)/2); //循环次数
        
        $y  =   -2;
        $sort   =   array(); //存放横纵坐标索引值
        while($num > 0){ //生成排序Y坐标索引，键名是Y坐标，值是索引级别，值越大越优先
            $y_max  =   $y<0 ? 0 : ($y+2);
            $sort[$y]   =   $sort[$y_max]   =   $num;
            //$sort[$num]   = array($y, $y_max);
            $y += 3;
            $num--;
        }

        $new_arr    =   array();
        foreach($positionInfo as $position){
            $new_arr[$position['areaId']][$sort[$position['y_alixs']]][$position['x_alixs']][$position['z_alixs']][]    =   $position;
        }
        //print_r($new_arr);exit;
        unset($positionInfo);
        
        WhBaseModel::begin();
        $where  =   'routeType = 4';
        $info   =   WhWaveRouteRelationModel::delete_relation($where);
        if(!$info){
            WhBaseModel::rollback();
            self::$errCode  =   202;
            self::$errMsg   =   '删除旧仓位索引失败!';
            return FALSE;
        }
        
        $string =   '';
        $i  =   1; //仓位排序判断 蛇形配货
        $route  =   1; //仓位索引排序
        $data_arr   =   array(); //插入数据
        $routeType  =   4; //索引类型为仓位。
        foreach($new_arr as $area){
            foreach($area as $y_sort){ //按照Y轴排序的结果
                //print_r($y_sort);exit;
                foreach($y_sort as $x_sort){ //按照X轴排序的结果
                    $i%2 === 0 ? rsort($x_sort) : sort($x_sort);
                    //print_r($x_sort);exit;
                    foreach($x_sort as $z_sort){ //按照Z轴排序的结果
                        sort($z_sort);
                        //print_r($z_sort);exit();
                        foreach($z_sort as $position){
                            $data_arr[] =   array(
                                                'name'      =>  $position['pName'],
                                                'routeId'   =>  $position['id'],
                                                'route'     =>  $route,
                                                'routeType' =>  $routeType
                                            );        
                            if($route%100 == 0){ //每3000条插入一次数据\
                                //var_dump($data_arr);exit;
                                $info   =   WhWaveRouteRelationModel::insert_data($data_arr);
                                if(!$info){
                                    WhBaseModel::rollback();
                                    self::$errCode  =   203;
                                    self::$errMsg   =   '插入新仓位索引失败!';
                                    return FALSE;
                                }
                                $data_arr   =   array();
                            };
                            $route++;
                        }
                    }
                }
            }
            $i++;
        }
        //print_r($data_arr);exit;
        unset($new_arr);
        if(!empty($data_arr)){
            $info   =   WhWaveRouteRelationModel::insert_data($data_arr);
            if(!$info){
                WhBaseModel::rollback();
                self::$errCode  =   203;
                self::$errMsg   =   '插入新仓位索引失败!';
                return FALSE;
            }
        }
        WhBaseModel::commit();
        self::$errCode  =   200;
        self::$errMsg   =   '更新仓位索引成功!';
        return TRUE;
    }
    
}
?>