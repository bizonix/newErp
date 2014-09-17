<?php
/**
 * WaveBuildAct
 * 配货单生成逻辑（波次分配）
 * @package 仓库系统
 * @author Gary
 * @copyright 2014
 * @version $Id$
 * @access public
 */
class WaveBuildAct extends Auth{
    static $errCode =   0;
    static $errMsg  =   '';
    function __construct(){
        parent::__construct();
    }
    
    /**
     * WaveBuildAct::waveBuild()
     * 生成配货单入口
     * @param mixed $shipOrderId 发货单ID
     * @param mixed $is_wave 该发货单是否单独生成配货单
     * @return
     */
    public function waveBuild($shipOrderId, $is_wave = 0){
        $shipOrderId    =   intval(trim($shipOrderId)); //格式化发货单ID
        if(!$shipOrderId){
            self::$errCode  =   109;
            self::$errMsg   =   '发货单ID不合法!';
            return FALSE;
        }
        $order_status   =   WhShippingOrderModel::get_order_info('is_wave', array('id'=>$shipOrderId));
        if($order_status[0]['is_wave'] == 1){
            self::$errCode  =   110;
            self::$errMsg   =   '该发货单已经分配过波次!';
            return FALSE;
        }
        
        /** 获取订单详情**/
        $order_detail   =   WhShippingOrderdetailModel::getShipDetails($shipOrderId);
        if(empty($order_detail)){
            return FALSE;
        }
        //$order_detail   =   self::sort_order_detail($order_detail);
        $order_limit    =   self::get_order_limit_info($order_detail); //获取当前订单的重量、料号数量、体积信息
        //print_r($order_limit);exit;
        $is_wave        =   $is_wave ? $is_wave : self::judge_single_shiporder($order_limit, 1); //判断是否满足单发货单生成配货单条件
        //var_dump($is_wave);exit;
        if($is_wave === TRUE){ //需要生成配货单
            $is_split   =   self::judge_is_split($order_limit); //判断满足生成单个配货单的发货单是否需要拆分成多个配货单
            if($is_split === TRUE){ //满足拆分
                self::split_signle_shipOrder($shipOrderId, $order_detail); //将一个发货单拆分成多个配货单
            }else{
                self::make_wave($shipOrderId, 0, 1, $order_detail, 1); //生成单独一个配货单
            }
        }else{ //不需单独生成一个配货单
            $waveType   =   count($order_detail) == 1 ? 2 : 3; //判断单料号还是多料号
            if($waveType == 2){ //单料号发货单
                $sku    =   $order_detail['0']['sku'];
                //判断该料号的配货单是否有未完成配货的
                $waveinfo   =   WhWaveInfoModel::get_wave_info('id', array('sku'=>$sku, 'waveStatus'=>0, 'waveType'=>2, 'is_delete'=>0));
                $wave_id    =   empty($waveinfo) ? 0 : $waveinfo[0]['id'];
                //if($wave_id){
//                    echo '<font style="color:red;">'.$shipOrderId.'---'.$wave_id.'</font>'."<br />";
//                }
                self::make_wave($shipOrderId, $wave_id, 0, $order_detail, $waveType);
            }else{ //多料号发货单
                self::process_multi_sku_shipOrder($shipOrderId, $order_detail); //处理多料号订单
            }
        }
    }
    
    /**
     * WaveBuildAct::get_order_limit_info()
     * 获取订单的所有料号总重、总体积、总料号数
     * @param mixed $order_detail
     * @return
     */
    public function get_order_limit_info($order_detail){
        $skus   =   array();
        $skuArr =   array(); //料号数组
        $amount =   array(); //每个料号的数量
        $order_limit    =   array();
        foreach($order_detail as $val){
            $order_limit['limitSkuNums']   +=  $val['amount'];
            $skuArr[]   =   $val['sku'];
            $amount[$val['sku']]    =   $val['amount'];
        }
        $skuArr =   array_unique($skuArr);
        $select     =   array('id','sku','goodsWeight','goodsLength','goodsWidth','goodsHeight');
        $sku_info   =   PcGoodsModel::get_sku_info($select, $skuArr);
        if(!empty($sku_info)){
            foreach($sku_info as $val){
                $order_limit['limitWeight']   +=  $val['goodsWeight'];
                //echo $val['goodsWeight'];
                $order_limit['limitVolume']   +=  $amount[$val['sku']]*$val['goodsLength']*$val['goodsWidth']*$val['goodsHeight']/1000000;               
            }
        }
        $order_limit['limitVolume'] =   sprintf("%01.6f", $order_limit['limitVolume']);
        return $order_limit;
    }
    
    /**
     * WaveBuildAct::judge_single_shiporder()
     * 判断发货单是否满足单发货单生成波次的条件
     * @param array $order_limit Array ('sku_nums' => 6, 'weight_limit' => 0.414 ,'volume_limit' => 1716)
     * @param int $waveType 配货单类型 1 单发货单 2单料号 3多料号
     * @return void
     */
    public function judge_single_shiporder($order_limit, $waveType = 1){
        //$single_limit   =   C('wave_limit');
        //$single_limit   =   array_filter($single_limit[$waveType]); //获取对应配货单类型生成波次规则并剔除未配置条件
        
        $single_limit   =   array_filter(self::get_wave_config($waveType));
        
        $is_wave        =   FALSE; //是否可以生成单个波次
        foreach($single_limit as $key=>$val){
            if($order_limit[$key] > $val){
                $is_wave    =   TRUE;
                break;
            }
        }
        return $is_wave;
    }
    
    /**
     * WaveBuildAct::judge_is_split()
     * 判断单个发货单生成的配货单是否需要拆分成多个配货单
     * @param array $order_limit  订单的总料号重量、总体积、总sku数量
     * @return void
     */
    public function judge_is_split($order_limit){
        //$single_limit   =   array_filter(C('single_limit')); //获取上限
        $single_limit   =   self::get_wave_config(1, 2);
        $is_wave        =   FALSE; //是否可以拆分
        foreach($single_limit as $key=>$val){
            if($order_limit[$key] > $val){ //有一个条件大于设置就退出
                $is_wave    =   TRUE;
                break;
            }
        }
        return $is_wave;
    }
    
    /**
     * WaveBuildAct::make_wave()
     * 生成配货单  （单料号和单发货单）
     * @param int $shipOrderId  发货单ID
     * @param int $wave_id  发货单ID
     * @param integer $wave_status wave_id 没有传递时 生成的配货单状态 0可以继续添加 1完结配货单不许再添加
     * @param array $order_detail  订单明细
     * @param int $wave_type 配货单类型  1=》单个发货单 2-单料号 3-多料号
     * @return bool
     */
    public function make_wave($shipOrderId, $wave_id = 0, $wave_status = 0, $order_detail = '', $wave_type = 1){
        $shipOrderId    =   intval(trim($shipOrderId));
        $wave_id        =   intval(trim($wave_id));
        $wave_status    =   intval(trim($wave_status));
        //print_r($shipOrderId);exit;
        if(!$shipOrderId){
            self::$errCode  =   200;
            self::$errMsg   =   '无效发货单ID!';
            return FALSE;
        }
        if(empty($order_detail)){
            $order_detail   =   WhShippingOrderdetailModel::getShipDetails($shipOrderId); //获取订单明细
            if(empty($order_detail)){ //没有订单明细则直接返回
                self::$errCode  =   201;
                self::$errMsg   =   '该发货单没有料号明细!';
                return FALSE;
            }
        }
        
        $order_limit    =   self::get_order_limit_info($order_detail); //获取当前订单的重量、料号数量、体积信息
        $area_info      =   self::get_area_info($order_detail); //获取订单区域、楼层信息
        //print_r($area_info);exit;
        WhBaseModel::begin();
        if(!$wave_id){ //不存在配货单号

            $startArea  =   current($area_info['areas']);
            //print_r($startArea);exit;
            $startArea  =   WhWaveAreaInfoModel::get_area_info('areaName', $startArea);
            //判断配货单区域类型
            
            $waveZone   =   self::judge_wave_zone($area_info['storey'], $area_info['areas']); //判断配货单区域类型
            if($waveZone === FALSE){
                self::$errCode  =   203;
                self::$errMsg   =   '获取配货单区域类型失败!';
                return FALSE;
            }
            $data       =   array( //拼接配货单信息
                                    //'number'        =>  $wave_number,
                                    'storey'        =>  implode(',', $area_info['storey']),
                                    'startArea'     =>  $startArea[0]['areaName'],
                                    'totalWeight'   =>  $order_limit['limitWeight'],
                                    'totalVolume'   =>  $order_limit['limitVolume'],
                                    'totalSkus'     =>  $order_limit['limitSkuNums'],
                                    'totalOrders'   =>  1,
                                    'waveStatus'    =>  $wave_status,
                                    'waveZone'      =>  $waveZone,
                                    'waveType'      =>  $wave_type,
                                    'createTime'    =>  time(),
                                    'printStorey'   =>  $area_info['storey'][0]
                                );
            if($wave_type == 2){ //单料号配货单
                $data['sku']    =   $order_detail[0]['sku'];
            }
            //print_r($data);exit;
            $wave_id    =   WhWaveInfoModel::insert_wave_info($data);
            //echo '生成配货单'.$wave_id."<br />";
            if($wave_id === FALSE){
                self::$errCode  =   100;
                self::$errMsg   =   '生成配货单失败!';
                WhBaseModel::rollback();
                return FALSE;
            }
            $wave_number    =   WhWaveInfoModel::number_encode($wave_id); //生成配货单编号
            //print_r($wave_number);exit;
            /** 更新配货单编号**/
            $info           =   WhWaveInfoModel::update_wave_info(array('number'=>$wave_number), array('id'=>$wave_id));
            if(!$info){
                self::$errCode  =   108;
                self::$errMsg   =   '更新配货单编号失败!';
                WhBaseModel::rollback();
                return FALSE;
            }
            //echo '更新配货单编号'.$wave_number."<br />";
            /** end**/
            
        }else{  //已存在配货单号
            $where          =   array('id'=>$wave_id);
            $wave_info      =   WhWaveInfoModel::get_wave_info('*', $where); //获取配货单信息
            if(empty($wave_info)){
                self::$errCode  =   101;
                self::$errMsg   =   '没有该配货单信息!';
                return FALSE;
            }
            $wave_info  =   $wave_info[0];
            if($wave_info['waveStatus'] != 0){
                self::$errCode  =   102;
                self::$errMsg   =   '该配货单已不能添加订单!';
                return FALSE;
            }
            $wave_type      =   $wave_info['waveType']; //配货单类型 单料号 多料号
            $order_limit['limitSkuNums']    +=  $wave_info['totalSkus'];
            $order_limit['limitWeight']     +=  $wave_info['totalWeight'];
            $order_limit['limitVolume']     +=  $wave_info['totalVolume'];
            $order_limit['limitOrderNums']  =  $wave_info['totalOrders'] + 1;
            
            $is_wave    =   self::judge_single_shiporder($order_limit, $wave_type); //判断加上新订单是否超过单个配货单限制
            //var_dump($is_wave);exit;
            if($is_wave === TRUE){
                //完结配货单
                $update =   array('waveStatus'=>1);
                $where  =   array('id'=>$wave_id);
                $info   =   WhWaveInfoModel::update_wave_info($update, $where); //超过限制则完结该配货单
                return self::make_wave($shipOrderId, 0, $wave_status, $order_detail, $wave_type); //重新分配配货单
            }else{
                /** 更新配货单信息**/
                $update =   array(
                                    'totalSkus'     =>  $order_limit['limitSkuNums'],
                                    'totalWeight'   =>  $order_limit['limitWeight'],
                                    'totalVolume'   =>  $order_limit['limitVolume'],
                                    'totalOrders'   =>  $order_limit['limitOrderNums'],
                                );
                $combine_area   =   self::combine_area_info($wave_info['startArea'], $area_info['areas']); //合并新加入订单的区域信息
                //print_r($combine_area);exit;
                $startArea      =   WhWaveAreaInfoModel::get_area_info('areaName', current($combine_area));
                
                $combine_storey =   self::combine_storey_info($wave_info['storey'], $area_info['storey']); //合并楼层
                
                $waveZone       =   self::judge_wave_zone($combine_storey, $combine_area); //判断配货单区域类型
                
                $update['storey']   =   implode(',', $combine_storey);
                $update['startArea']=   $startArea[0]['areaName'];
                $update['waveZone'] =   $waveZone;
                $update['printStorey']  =   $combine_storey[0];
                $where  =   array('id'=>$wave_id);
                $info   =   WhWaveInfoModel::update_wave_info($update, $where);
                if(!$info){
                    self::$errCode  =   103;
                    self::$errMsg   =   '更新配货单信息失败!';
                    WhBaseModel::rollback();
                    return FALSE;
                }
                //echo '更新配货单'.$wave_id."<br />";
                /** 更新配货单信息end**/
            }
        }
        
        /** 插入收货记录表数据**/
        if(empty($area_info['areas'])){
            self::$errCode  =   111;
            self::$errMsg   =   '发货单区域信息为空!';
            WhBaseModel::rollback();
            return false;
        }
        
        $area_names     =   self::get_area_names($area_info['areas']);
        $storey         =   self::get_storey_list(); //获取楼层列表
        //print_r($area_names);exit;
        $info   =   WhWaveReceiveRecordModel::insert_receive_data($wave_id, $area_names, $storey); //插入收获记录表区域信息
        if($info === FALSE){
            self::$errCode  =   104;
            self::$errMsg   =   '插入收货记录表失败!';
            WhBaseModel::rollback();
            return false;
        }
        //echo '插入收货记录'.json_encode($area_names)."<br />";
        /** end**/
        
        /** 插入配货记录表数据**/
        $order_detail   =   self::merge_order_detail($order_detail); //合并同一生成时间下相同料号，相同仓位的料号信息
        //print_r($order_detail);exit;
        $scan_details   =   self::process_scan_details($order_detail); //处理订单详情生成相对应配货记录
        $info   =   WhWaveScanRecordModel::inser_scan_data($wave_id, $scan_details);
        if($info === FALSE){
            self::$errCode  =   105;
            self::$errMsg   =   '插入配货记录表失败!';
            WhBaseModel::rollback();
            return false;
        }
        //echo '插入配货记录'.json_encode($scan_details)."<br />";
        /** end**/
        
        /** 插入配货单、发货单关系表**/
        $info   =   WhWaveShippingRelationModel::insert_relation_data($wave_id, $shipOrderId);
        if($info === FALSE){
            self::$errCode  =   106;
            self::$errMsg   =   '插入配货单关系表失败!';
            WhBaseModel::rollback();
            return false;
        }
        //echo '插入配货单关系'.$wave_id.'----'.$shipOrderId."<br />";
        /** end**/
        
        /** 更新发货单分配状态**/
        $info   =   WhShippingOrderModel::update(array('is_wave'=>1), array('id'=>$shipOrderId));
        if($info === FALSE){
            self::$errCode  =   107;
            self::$errMsg   =   '更新发货单分配状态失败!';
            WhBaseModel::rollback();
            return false;
        }
        //echo '更新发货单分配状态'.$shipOrderId."<br />";
        /** end**/
        
        /** 更新多料号临时存放表**/
        if($wave_type == 3){ //多料号发货单
            $info       =   whWaveMultiShipAreaRecordModel::update(array('is_wave'=>1), array('shipOrderId'=>$shipOrderId));
            if($info === FALSE){
                self::$errCode  =   108;
                self::$errMsg   =   '更新多料号订单临时存放表状态失败!';
                WhBaseModel::rollback();
                return false;
            }
            //echo '更新多料号订单临时存放表'.$shipOrderId."<br />";
        }
        /** end**/
        WhBaseModel::commit();
        return TRUE;
    }
    
    /**
     * WaveBuildAct::make_wave_number()
     * 获取配货单编号
     * @return void
     */
    public function make_wave_number(){
        $max_id     =   WhWaveInfoModel::get_max_id(); //获取配货单表最大自增id
        $id         =   $max_id + 1;
        return $id;
    }
    
    /**
     * WaveBuildAct::get_area_info()
     * 获取订单区域、楼层信息
     * @param array $order_detail 发货单明细
     * @author Gary
     * @return void
     */
    public function get_area_info($order_detail){
        if(empty($order_detail)){
            return FALSE;
        }
        $storey             =   array(); //楼层集合
        $areas              =   array(); //区域集合
        $position_ids       =   array();
        foreach($order_detail as $val){
            $position_ids[] =   $val['positionId'];   
        }
        $select             =   array('storeId', 'storey', 'areaId');
        $position_infos     =   WhPositionDistributionModel::get_position_info($select, $position_ids);
        if(!empty($position_infos)){
            foreach($position_infos  as $val){
                $storey[]   =   $val['storey'];
                $areas[]    =   $val['areaId'];
            }
            $storey =   array_unique($storey);
            $areas  =   array_unique($areas);
            rsort($storey);
            //sort($areas);
            $areas  =   self::sort_area($areas);
        }
        return array('storey'=>$storey,'areas'=>$areas);
    }
    
    /**
     * WaveBuildAct::judge_wave_zone()
     * 判断配货单区域类型
     * @param array $storey 楼层集合
     * @param array $area 区域集合
     * @return void
     */
    public function judge_wave_zone($storey, $area){
        if(is_array($storey) && is_array($area)){
            if(count($storey) == 1){ //同楼层
                $waveZone   =   count($area) == 1 ? 1 : 2;
            }else{
                $waveZone   =   3;
            }
            return $waveZone;
        }
        return FALSE;
    }
    
    /**
     * WaveBuildAct::combine_area_info()
     * 多料号配货单添加新发货单时合并区域信息
     * @param string $areaName 原配货单起始区域名
     * @param array $area_info 新合并的发货单区域集合
     * @return
     */
    public function combine_area_info($areaName, $area_info){
        $areaId     =   WhWaveAreaInfoModel::get_area_info('id', '', $areaName);
        if(empty($areaId)){
            return $area_info;
        }
        $area_info[]=   $areaId[0]['id'];
        $area_info  =   self::sort_area(array_unique($area_info));
        return $area_info;
    }
    
    /**
     * WaveBuildAct::combine_area_info()
     * 多料号配货单添加新发货单时合并楼层信息
     * @param string $storey 原配货单楼层
     * @param array $storey_info 新合并的发货单楼层集合
     * @return
     */
    public function combine_storey_info($storey, $storey_info){
        if($storey && is_array($storey_info)){
            $storey         =   explode(',', $storey);
            $storey_info    =   array_merge($storey, $storey_info);
            $storey_info    =   array_unique($storey_info);
            rsort($storey_info);
            return $storey_info;
        }
        return FALSE;
    }
    
    /**
     * WaveBuildAct::get_area_names()
     * 通过区域ID获取区域名称
     * @param array $areaArr
     * @return array
     */
    public function get_area_names($areaArr){
        $return     =   array();
        if(is_array($areaArr) && !empty($areaArr)){
            $area_names     =   WhWaveAreaInfoModel::get_area_info('id, areaName, floorId', $areaArr); //获取区域名称结果集
            //print_r($area_names);exit;
            if(!empty($area_names)){
                foreach($area_names as $val){
                    $new_area[$val['id']]   =   $val;
                }
                foreach($areaArr as $area){
                    $return[]   =   $new_area[$area];
                }
            }
        }
        return $return;
    }
    
    /**
     * WaveBuildAct::process_scan_details()
     * 处理订单详情生成对应的配货记录 
     * @param array $order_detail
     * @return void
     */
    public function process_scan_details($order_detail){
        $return     =   array();
        if(is_array($order_detail)){
            foreach($order_detail as $key=>$val){
                $position_info      =   WhPositionDistributionModel::get_position_info(array('storey', 'areaId'), $val['positionId']);
                $areaName           =   WhWaveAreaInfoModel::get_area_info('areaName', $position_info[0]['areaId']);
                $return[$key]       =   array(
                                            'sku'       =>  $val['sku'],
                                            'skuAmount' =>  $val['amount'],
                                            'pName'     =>  $val['pName'],
                                            'storey'    =>  $position_info[0]['storey'],
                                            'area'      =>  $areaName[0]['areaName']
                                        );
            }
        }
        return $return;
    }
    
    /**
     * WaveBuildAct::process_multi_sku_shipOrder()
     * 处理多料号订单
     * @param int $shipOrderId 发货单ID
     * @param array $order_detail 发货单详情
     * @return void
     */
    public function process_multi_sku_shipOrder($shipOrderId, $order_detail){
        $shipOrderId    =   intval($shipOrderId);
        $order_limit    =   self::get_order_limit_info($order_detail);
        //print_r($order_limit);exit();
        $areaInfo       =   self::get_area_info($order_detail);
        $data           =   array(
                                'shipOrderId'   =>  $shipOrderId,
                                'area'          =>  implode(',', $areaInfo['areas']),
                                //'weight'        =>  $order_limit['limitWeight'],
                                //'volume'        =>  $order_limit['limitVolume'],
                                //'skuNums'       =>  $order_limit['limitSkuNums'],
                            );
        $info           =   whWaveMultiShipAreaRecordModel::insert_multi_ship_record($data);
        //echo '保存多料号发货单区域信息'.$shipOrderId."<br />";
    }
    
    /**
     * WaveBuildAct::make_multi_wave()
     * 批量生成多料号配货单 
     * @param $time 操作时间
     * @author Gary
     * @return void
     */
    public function make_multi_wave($time){
        //$area_index     =  WhWaveAreaIndexModel::select('', '*');
//        $indexs         =   array();
//        foreach($area_index as $val){
//            $indexs[$val['areaInfo']]   =   $val['id']; //区域索引集合
//        }
//        unset($area_index);
        //获取多料号订单集合
        $time       =   $time ? $time : time();
        $multi_shipOrders   =   whWaveMultiShipAreaRecordModel::get_multi_ship_records('*', array('addTime <=' => $time, 'is_wave'=>0, 'is_delete'=>0));
        //var_dump($multi_shipOrders);exit;
        $new_orders         =   array(); //经过排序后的多料号订单集合
        if(empty($multi_shipOrders)){
            self::$errCode  =   100;
            self::$errMsg   =   '暂无可配货多料号订单';
            return FALSE;
        }
        foreach($multi_shipOrders as $val){
            $area_count     =   substr_count($val['area'], ',')+1;
            $area_id_count  =   self::get_area_ids_count($val['area']); //获取该订单所有区域ID索引值的总和
            $new_orders[$area_count][$area_id_count][]  =   $val;
        }
        
        ksort($new_orders); //按照订单区域数从小到大排列
        $success    =   array(); //生成成功发货单
        $fail       =   array(); //生成失败发货单
        foreach($new_orders as $val){
            ksort($val); //按照区域id综合从小到大排序
            //print_r($val);exit;
            foreach($val as $v){
                //print_r($v);exit;
                foreach($v as $order){
                    //print_r($order);exit;
                    $wave_id    =   WhWaveInfoModel::get_wave_info('id', array('waveType'=>3, 'waveStatus'=>0, 'is_delete'=>0));
                    $wave_id    =   empty($wave_id) ? 0 : $wave_id[0]['id'];
                    $info       =   self::make_wave($order['shipOrderId'], $wave_id, 0, '', 3);
                    if($info){
                        $success[]  =   $order['shipOrderId'];
                    }else{
                        $fail[]     =   array('shipOrderId'=>$order['shipOrderId'], 'reason'=>self::$errMsg);
                    }
                }
            }
        }
        //var_dump($fail);exit;
        return array('success'=>$success, 'fail'=>$fail);
    }
    
    /**
     * WaveBuildAct::split_signle_shipOrder()
     * 单个发货单拆分成多个配货单
     * @param int $shipOrderId 订单ID
     * @param array $order_detail 订单明细
     * @return void
     */
    public function split_signle_shipOrder($shipOrderId, $order_detail = ''){
        $shipOrderId    =   intval($shipOrderId);
        if(empty($order_detail)){
            $order_detail   =   WhShippingOrderdetailModel::getShipDetails($shipOrderId);
        }
        $new_order_detail   =   array();
        foreach($order_detail as $val){
            if($val['amount'] > 1){
                for($i= 1; $i<=$val['amount']; $i++){
                    $new_order_detail[] =   array(
                                                'sku'   =>  $val['sku'],
                                                'amount'=>  1,
                                                'positionId'    =>  $val['positionId'],
                                                'pName' =>  $val['pName']
                                            );
                }
            }else{
                $new_order_detail[] =   $val;
            }
        }
        
        $per_wave   =   array(); //每个波次料号临时存放表
        foreach($new_order_detail as $val){
            $per_wave[] =   $val;
            $limit_info =   self::get_order_limit_info($per_wave); //获取波次临时存放表的总重量、体积、料号数量信息。
            $is_wave    =   self::judge_split_wave($limit_info); //判断是否符合波次拆分规则
            if($is_wave == TRUE){
                array_pop($per_wave); //将最后一个加入的料号剔除
                $wave_detail    =   $per_wave;
                $per_wave       =   array();
                if(empty($wave_detail)){
                    $wave_detail[] =   $val;
                }else{
                    $per_wave[]     =   $val;
                }
                self::make_wave($shipOrderId, 0, 1, $wave_detail, 1);
            }
        }
        if(!empty($per_wave)){
            self::make_wave($shipOrderId, 0, 1, $per_wave, 1);
        }
    }
    
    /**
     * WaveBuildAct::judge_split_wave()
     * 判断是否超过单个发货单拆分规则 
     * @param mixed $limit_info 发货单总重、体积、料号数、订单数信息
     * @return void
     */
    public function judge_split_wave($limit_info){
        //$per_limit   =   array_filter(C('per_limit')); //获取上限
        $per_limit      =   self::get_wave_config(1, 3);
        $is_wave        =   FALSE; //是否可以拆分
        foreach($per_limit as $key=>$val){
            if($limit_info[$key] > $val){ //有一个条件大于设置就退出
                $is_wave    =   TRUE;
                break;
            }
        }
        return $is_wave;
    }
    
    /**
     * WaveBuildAct::get_area_ids_count()
     * 获取传入区域索引ID值的总和
     * @param string $areas
     * @return void
     */
    public function get_area_ids_count($areas){
        $area_arr   =   explode(',', $areas);
        $area_arr   =   self::sort_area($area_arr);
        $count      =   0;
        $area_arr   =   array_keys($area_arr);
        
        if(!empty($area_arr)){
            foreach($area_arr as $val){
                $count  +=  $val;
            }
        }
        return $count;
    }
    
    /**
     * WaveBuildAct::get_wave_config()
     * 获取波次配置表信息
     * @param int $waveType 配货单类型。1=>>单个发货单  2=>>单SKU 3=>>多SKU
     * @param integer $limitType 限制条件种类：1配货单 2单发货单上限 3单发货单拆分规则
     * @return void
     */
    public function get_wave_config($waveType, $limitType = 1){
        $waveType   =   intval($waveType);
        $limitType  =   intval($limitType);
        $waveType   =   $waveType ? $waveType : 1;
        $limitType  =   $limitType ? $limitType : 1;
        $return     =   array();
        $config     =   WhWaveConfigModel::select(array('waveType'=>$waveType, 'limitType'=>$limitType));
        $config     =   $config[0];
        unset($config['id'],$config['waveType'], $config['limitType']);
        //print_r($config);exit;
        return $config;
    }
    
    /**
     * WaveBuildAct::merge_order_detail()
     * 合并相同料号相同仓位的料号信息
     * @param mixed $order_detail
     * @return void
     */
    public function merge_order_detail($order_detail){
        $return     =   array();
        foreach($order_detail as $val){
            $key    =   $val['sku'].'-'.$val['pName'];
            if(isset($return[$key])){
                $return[$key]['amount']   +=   $val['amount'];
            }else{
                $return[$key]   =   $val;
            }
        }
        return array_values($return);
    }
    
    /**
     * WaveBuildAct::sort_area()
     * 根据区域索引对区域进行排序
     * @param mixed $areas 区域一维数组
     * @author Gary
     * @return void
     */
    public function sort_area($areas){
        $where  =   array('routeId in'=>$areas, 'routeType'=>3, 'is_delete'=>0, 'order by'=>'route asc');
        $field  =   'route,routeId';
        $areas  =   WhWaveRouteRelationModel::select($where, $field);
        $return =   array();
        if(!empty($areas)){
            foreach($areas as $val){
                $return[$val['route']]   =   $val['routeId'];
            }
        }
        unset($areas);
        return $return;
    }
    /**
     * WaveBuildAct::get_storey_list()
     * 获取楼层列表
     * @author Gary
     * @return void
     */
    public function get_storey_list(){
        $storey =   WhFloorModel::getFloorList('id, whCode','');
        $return =   array();
        if(!empty($storey)){
            foreach($storey as $val){
                $return[$val['id']] =   $val['whCode'];
            }
        }
        //print_r($return);exit;
        return $return;
    }
}
?>