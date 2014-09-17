<?php
/**
 * 配货单管理界面
 * @author Gary
 * @date 2014-07-31
 * @version 1.0
 */
class WaveInfoManageView extends CommonView {
    private static $toplevel    =   2; //一级列表排序
    private static $secondlevel =   20; //二级列表排序
    private static $navlist     =   array(); //面包屑数组
    private static $toptitle    =   '配货单'; //标题
    private static $waveTypes   =   array(1=>'单发货单', 2=>'单料号', 3=>'多料号');
    private static $waveZones   =   array(1=>'同区域', 2=>'同楼层跨区域', 3=>'跨楼层');
    private static $pagesize    =   100;    //页面显示数据条数
    

    /**
     * WaveInfoManageView::view_index()
     * 配货单管理首页
     * @author Gary
     * @return void
     */
    public function view_index(){
        $navlist = array(
			array('url' => '', 'title' => '出库 '),
			array('url' => '', 'title' => '配货单管理'),
		);
 	    self::bulidNav($navlist);
        $areas      =   WhWaveAreaInfoModel::select(array('is_delete'=>0)); //区域列表
        $this->smarty->assign('areas', $areas);
        //$this->smarty->assign('storey', $storey);
        
        $search_where         =   self::get_search_where(); //处理配货单搜索条件
        /** 分页处理**/
        $totalNums      =   WhWaveInfoModel::count_all_results($search_where); //查询总数
        $pager          =   new Page($totalNums, self::$pagesize);
        if ($totalNums > self::$pagesize) {       //分页
            $pagestr =  $pager->fpage(array(0, 2, 3, 4, 5, 6, 7, 8, 9));
        } else {
            $pagestr =  $pager->fpage(array(0, 2, 3));
        }
        $this->smarty->assign('pagestr', $pagestr);
        /** 结束**/
        
        $search_where['limit']  =   ltrim($pager->limit, 'Limit ');
        $result     =   WhWaveInfoModel::get_wave_info_by_union_table(array('a.*', 'd.userId'), $search_where); //联表查询配货单信息
        $color_config=  self::get_color_config(); //获取箱子颜色配置
        $waveStatusArr =   self::get_wave_status();   //配货单状态
        //print_r($color_config);exit;
        $this->smarty->assign('result', $result);
        $this->smarty->assign('color_config', $color_config);
        $this->smarty->assign('waveStatusArr', $waveStatusArr);
        $this->smarty->assign('waveTypes', self::$waveTypes);
        $this->smarty->assign('waveZones', self::$waveZones);
        $this->smarty->display('waveIndex.htm');
    }
    
    /**
     * WaveInfoManageView::view_makeWave()
     * 生成配货单
     * @author Gary
     * @return void
     */
    public function view_waveMake(){
        $navlist = array(
			array('url' => '', 'title' => '出库 '),
			array('url' => '', 'title' => '生成配货单'),
		);
        self::bulidNav($navlist); //导航及面包屑配置
        
        $time           =   time();
        $single_info    =   self::makeSingleWave($time); //单料号和单发货单 配货单生成信息
        $single_info    =   $single_info ? $single_info : 0;
        //var_dump($single_info);exit;
        $multi_info     =   self::makeMultiWave($time); //多料号配货单 生成信息
        $this->smarty->assign('single_info', $single_info);
        $this->smarty->assign('multi_info', $multi_info);
        $this->smarty->display('waveMake.htm');
    }
         
    /**
     * WaveInfoManageView::pritnWave()
     * 打印配货单
     * @return void
     */
    public function view_pritnWave(){
        include '../html/template/v1/printWave.php';
    }
    
    /**
     * WaveInfoManageView::view_makeMultiWave()
     * 生成多料号配货单
     * @param $time 操作时间
     * @author Gary
     * @return void
     */
    public function makeMultiWave($time){
        
        $waveBuild  =   new WaveBuildAct;
        $info       =   $waveBuild->make_multi_wave($time); //返回多料号配货单生成情况
        $update     =   array('createUserId'=>$_SESSION['userId'], 'createTime'=>time(), 'waveStatus'=>1);
        $where      =   array('waveType'=>3, 'createUserId'=>0, 'is_delete'=>0);
        WhBaseModel::begin();
        $a          =   WhWaveInfoModel::update_wave_info($update, $where);
        if($a){
            $shipOrderIds   =   empty($info['success']) ? 0 : $info['success'];
            WhShippingOrderModel::update(array('orderStatus'=>PKS_PROCESS_GET_GOODS), array('id in'=>$shipOrderIds));
            $info['success']=   WhBaseModel::affected_rows();
        }
        WhBaseModel::commit();
        return $info;    
    }
    
    /**
     * WaveInfoManageView::makeSingleWave()
     * 生成单料号及单发货单配货单
     * @author Gary 
     * @return void
     */
    public function makeSingleWave($time){
        $update     =   array('createUserId'=>$_SESSION['userId'], 'createTime'=>time(), 'waveStatus'=>1);
        $where      =   array('waveType in'=>array(1,2), 'createTime <='=>$time,'createUserId'=>0, 'is_delete'=>0);
        $waveinfo   =   WhWaveInfoModel::get_wave_info('id', $where); //获取所有符合条件的配货单号
        WhBaseModel::begin();
        //更新配货单状态
        $info       =   WhWaveInfoModel::update_wave_info($update, $where);
        if(!$info){
            WhBaseModel::rollback();
            return $info;
        }
        $waveIds    =   get_filed_array('id', $waveinfo);
        $waveIds    =   empty($waveIds) ? array(0) : $waveIds;
        $shipOrderIds   =   WhWaveShippingRelationModel::select(array('waveId in'=>$waveIds), 'shipOrderId');
        //print_r($waveIds);exit;
        $shipOrderIds   =   get_filed_array('shipOrderId', $shipOrderIds);
        $shipOrderIds   =   empty($shipOrderIds) ? array(0) : $shipOrderIds;
        $info       =   WhShippingOrderModel::update(array('orderStatus'=>PKS_PROCESS_GET_GOODS), array('id in'=>$shipOrderIds));
        if(!$info){
            WhBaseModel::rollback();
            return $info;
        }
        $info       =   WhBaseModel::affected_rows();
        WhBaseModel::commit();
        return $info;
    }
    
    /**
     * WaveInfoManageView::get_search_where()
     * 处理配货单搜索条件
     * @author Gary
     * @return void
     */
    private function get_search_where(){
        $areaUser       =   trim($_GET['areaUser']) ? intval(trim($_GET['areaUser'])) : ''; //区域负责人
        $shipOrderId    =   trim($_GET['shipOrderId']) ? intval(trim($_GET['shipOrderId'])) : ''; //发货单ID
        $waveType       =   trim($_GET['waveType']) ? intval(trim($_GET['waveType'])) : ''; //配货单类型
        $waveZone       =   trim($_GET['waveZone']) ? intval(trim($_GET['waveZone'])) : ''; //配货单区域类型
        $storey         =   isset($_REQUEST['storey']) ? intval(trim($_REQUEST['storey'])) : ''; //配货单打印楼层
        $choose_area    =   trim($_GET['choose_area']); //选择的区域
        $waveStatus     =   trim($_GET['waveStatus']); //配货单状态
        $waveNumber     =   trim($_GET['waveNumber']); //配货单编号
        $startdate      =   trim($_GET['startdate']); //开始日期
        $enddate        =   trim($_GET['enddate']); //结束日日
        
        foreach($_GET as $key=>$v){ //传递搜索条件到配货单管理页面
            $this->smarty->assign($key, $$key);
        }
        //print_r($choose_area);exit;
        $searchArr      =   array();
        $areas          =   array(); //区域集合
        $wave_ids       =   array(); //配货单ID数组
        if($choose_area){
            $areas[]    =   $choose_area;
        }
        if($areaUser){ //区域负责人
            $areaId     =   WhWaveAreaUserRelationModel::select(array('userId'=>$areaUser, 'is_delete'=>0), 'areaId');
            if(!empty($areaId)){
                $areaName   =   WhWaveAreaInfoModel::get_area_info('areaName', $areaId[0]['areaId']);
                if(!empty($areaName)){
                    $areas[]    =   $areaName[0]['areaName'];
                }
            }
        }
        if($waveNumber){
            $wave_id    =   WhBaseModel::number_decode($waveNumber); //获取配货单ID
            $wave_ids[] =   $wave_id;
        }
        if($startdate){
            $searchArr['a.createTime >=']   =   strtotime($startdate);
        }
        if($enddate){
            $searchArr['a.createTime <=']   =   strtotime($enddate);
        }
        if($shipOrderId){
            $ids   =   WhWaveShippingRelationModel::select(array('shipOrderId'=>$shipOrderId, 'is_delete'=>0), 'waveId');
            $tmp_ids    =   array();
            if(!empty($ids)){
                foreach($ids as $val){
                    $tmp_ids[] =   $val['waveId'];
                }
            }else{
                $tmp_ids[] =   0;
            }
            if(!empty($wave_ids)){
                $wave_ids   =   array_intersect($wave_ids, $tmp_ids);
                $wave_ids   =   empty($wave_ids) ? array(0) : $wave_ids;
            }else{
                $wave_ids   =   $tmp_ids;
            }
        }
        if($waveStatus){
            $searchArr['a.waveStatus']  =   $waveStatus;
        }
        if($waveType){
            $searchArr['a.waveType']    =   $waveType;
        }
        if($waveZone){
            $searchArr['a.waveZone']    =   $waveZone;
        }
        if(!empty($areas)){
            $searchArr['c.area in']     =   array_unique($areas);
        }
        if(!empty($wave_ids)){
            $searchArr['a.id in']       =   array_unique($wave_ids);
        }
        if($storey){
            $searchArr['a.printStorey'] =   $storey;
        }
        $searchArr['a.waveStatus !=']   =   0;
        $searchArr['a.createUserId !=']  =   0;
        $searchArr['a.is_delete']    =   0;
        $searchArr['group by']       =   'a.id';
        return $searchArr;
    }
    
    /**
     * whGoodsAssignView::bulidNav()
     * 构建面包屑及二级菜单等相关信息
     * @param array $navlist 标题
     * @return void
     */
    public function bulidNav($navlist, $toptitle = ''){
        
        $toptitle   =   $toptitle ? $toptitle : self::$toptitle;

        $this->smarty->assign('toptitle', $topTitle);  //标题 
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('toplevel', self::$toplevel);
        $this->smarty->assign('secondlevel', self::$secondlevel);
    }
    
    /**
     * WaveInfoManageView::get_color_config()
     * 获取箱子颜色配置
     * @return void
     */
    public function get_color_config(){
        $color_info =   WhWaveColorModel::getWaveBoxColor();
        $return     =   array();
        if(!empty($color_info)){
            foreach($color_info as $color){
                $return[$color['waveZone']] =   $color['color'];
            }
        }
        unset($color_info);
        return $return;
    }
    
    /**
     * WaveInfoManageView::get_wave_status()
     * 获取配货单状态列表
     * @return void
     */
    public function get_wave_status(){
         $waveStatus    =   WhWaveStatusModel::select('is_delete=0');
         $return        =   array();
         if(!empty($waveStatus)){
            foreach($waveStatus as $status){
                $return[$status['waveCode']]    =   $status['waveStatusName'];
            }
         }
         return $return;
    }

}