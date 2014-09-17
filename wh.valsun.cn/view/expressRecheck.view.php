<?php

/** 
 * @author h
 * 快递复核
 */
class ExpressRecheckView extends CommonView
{

    /**
     * 构造函数
     */
    public function __construct ()
    {
        parent::__construct();
    }
    
    /*
     * 快递待复核列表
     */
    public function view_ExpressList ()
    {
        $pagesize = 100; // 页面大小
        
        $statusar = array(PKS_EX_TNRCK);
        $statusstr = implode(',', $statusar);
        
        $packing_obj = new PackingOrderModel();
        $count = $packing_obj->getRecordsNumByStatus($statusar); // 获得当前状态为待复核的发货单总数量
        
        $pager = new Page($count, $pagesize); // 分页对象
        
        $billlist = $packing_obj->getBillList(
                ' and orderStatus in (' . $statusstr . ') order by po.id ' .
                         $pager->limit);
        $this->smarty->assign('billlist', $billlist);
        
		$ShipingTypeList = CommonModel::getShipingTypeListKeyId();
		$count = count($billlist);
		for($i=0;$i<$count;$i++){
			$billlist[$i]['shipingname'] = isset($ShipingTypeList[$billlist[$i]['transportId']])?$ShipingTypeList[$billlist[$i]['transportId']]:'';
		}
		
		$acc_id_arr = array();
		foreach($billlist as $key=>$valbil){
			if(!in_array($valbil['accountId'],$acc_id_arr)){
				array_push($acc_id_arr,$valbil['accountId']);
			}
		}
		$salesaccountinfo = CommonModel::getAccountInfo($acc_id_arr);
		$this->smarty->assign('salesaccountinfo', $salesaccountinfo);
		
        if ($count > $pagesize) { // 分页链接
            $pagestr = $pager->fpage(
                    array(
                            0,
                            2,
                            3,
                            4,
                            5,
                            6,
                            7,
                            8,
                            9
                    ));
        } else {
            $pagestr = $pager->fpage(
                    array(
                            0,
                            2,
                            3
                    ));
        }
        $this->smarty->assign('pagestr', $pagestr);
        
        $navlist = array( // 面包屑
                array(
                        'url' => '',
                        'title' => '出库'
                ),
                array(
                        'url' => 'index.php?mod=expressRecheck&act=ExpressList',
                        'title' => '快递待复核'
                ),
                array(
                        'url' => '',
                        'title' => '快递待复核'
                )
        );
        $this->smarty->assign('navlist', $navlist);
        
        $toptitle = '待复核'; // 顶部链接
        $this->smarty->assign('toptitle', $toptitle);
        
        $toplevel = 2; // 顶层菜单
        $this->smarty->assign('toplevel', $toplevel);
        
        $secondlevel = '28'; // 当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
        
        $this->smarty->assign('secnev', 3); // 二级导航
        $this->smarty->display('expressrechecklist.htm');
    }
    
    /*
     * 跟踪号导入功能
     */
    public function view_trackNumberInput ()
    {
        $navlist = array( // 面包屑
                array(
                        'url' => '',
                        'title' => '出库'
                ),
                array(
                        'url' => '',
                        'title' => '快递待复核'
                ),
                array(
                        'url' => '',
                        'title' => '跟踪号导入'
                )
        );
        $this->smarty->assign('navlist', $navlist);
        
        $toptitle = '待复核'; // 顶部链接
        $this->smarty->assign('toptitle', $toptitle);
        
        $toplevel = 2; // 顶层菜单
        $this->smarty->assign('toplevel', $toplevel);
        
        $secondlevel = '28'; // 当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
        $this->smarty->assign('currenttime', date('Y-m-d', time()));
        
        $this->smarty->display('tracknumberimport.htm');
    }
    
    
    /*
     * 联邦excel数据导入
     */
    public function view_fileDataImport ()
    {
        
       // var_dump($_FILES);exit;
        // 处理联邦快递数据导入
        $_FILES['excelsheet'];
        if ($_FILES['excelsheet']['error'] != 0) {
            // 文件没有上传成功
            $data = array(
                    "data" => array(
                            '文件上传失败！'
                    ),
                    'link' => 'index.php?mod=expressRecheck&act=trackNumberInput'
            );
            goErrMsgPage($data);
            exit();
        } else {
            $tir_obj = new TrackInfoRecordModel();
            $result = $tir_obj->recordDataFromExecl_all();
          //  var_dump($result);exit();
            if (empty($result)) { // 么有成功
                $data = array(
                        "data" => array(
                                '操作失败！'
                        ),
                        'link' => 'index.php?mod=expressRecheck&act=trackNumberInput'
                );
                goErrMsgPage($data);
                exit();
            }
        }
        
        $navlist = array( // 面包屑
                array(
                        'url' => '',
                        'title' => '出库'
                ),
                array(
                        'url' => '',
                        'title' => '快递待复核数据导入'
                )
        );
        $this->smarty->assign('navlist', $navlist);
        
        $toptitle = '复核数据导入'; // 顶部链接
        $this->smarty->assign('toptitle', $toptitle);
        
        $toplevel = 2; // 顶层菜单
        $this->smarty->assign('toplevel', $toplevel);
        
        $secondlevel = '28'; // 当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
        
        $this->smarty->assign('list', TrackInfoRecordModel::$data);
        $this->smarty->display('tracknumimport.htm');
    }
    
    /*
     * 手工数据导入
     */
    public function view_formDataImport ()
    {
       // $tir_obj = new TrackInfoRecordModel();
        $ebay_id = $_POST['order'][1];
        $tracking    =  $_POST['express'][1];
        if(empty($ebay_id)&&empty($tracking)){
              $data = array(
                 "data" => array(
                    '发货单和跟踪号不能是空！'
                  ),
                 'link' => 'index.php?mod=expressRecheck&act=trackNumberInput'
             );
            goErrMsgPage($data);
        }
         $where = "where id={$ebay_id}";
		$order = orderPartionModel::selectOrder($where);
		if(!$order){
            $data = array(
                 "data" => array(
                    '发货单不存在！'
                  ),
                 'link' => 'index.php?mod=expressRecheck&act=trackNumberInput'
             );
            goErrMsgPage($data);
            exit();
		}
        if($order[0]['orderStatus'] != PKS_PRINT_SHIPPING_INVOICE){
            $data = array(
                 "data" => array(
                    $ebay_id.'此发货单状态不是在待打印面单状态！'
                  ),
                 'link' => 'index.php?mod=expressRecheck&act=trackNumberInput'
             );
            goErrMsgPage($data);
            exit();
        }
        $array = array(
          'shipOrderId' => $ebay_id,
          'is_delete'   => 0
        );
         //根据发货单号获取快递需要的箱子和跟踪号数量
        $result_tracking_count = WhWaveTrackingBoxModel::select_by_shipOrderId($ebay_id);
        $count_binding         = WhOrderTracknumberModel::count($array);//发货单已经绑定跟踪号的数量
        if(empty($result_tracking_count)){
            if($count_binding >0){
                $data = array(
                 "data" => array(
                    $ebay_id."该发货单已经绑定好跟踪号了！"
                  ),
                 'link' => 'index.php?mod=expressRecheck&act=trackNumberInput'
             );
            goErrMsgPage($data);
            exit();
            }
        }else{            
            $result_tracking_count = $result_tracking_count['trackingCount'];//发货单需要绑定跟踪号的数量
            if($result_tracking_count <= $count_binding){
               $data = array(
                 "data" => array(
                    $ebay_id."该发货单已经绑定好跟踪号了！"
                  ),
                 'link' => 'index.php?mod=expressRecheck&act=trackNumberInput'
             );
            goErrMsgPage($data);
            exit();
            }
        }
         //查询扫描的跟踪号是否已经扫描过了的
        $result_select     = WhOrderTracknumberModel::select_ByTracknumber($tracking);
        if($result_select){
             $data = array(
                 "data" => array(
                   '该跟踪号已经绑定'.$result_select['shipOrderId'].'，请检查！'
                  ),
                 'link' => 'index.php?mod=expressRecheck&act=trackNumberInput'
             );
            goErrMsgPage($data);
            exit();
        }
        
          $data_insert = array(
            'tracknumber' => $tracking,
            'shipOrderId' => $ebay_id,
            'createdTime' => time()
        );
        $array_count = array(
            'shipOrderId' => $ebay_id,
            'is_delete'   => 0
        );
      //根据发货单号获取快递需要的箱子和跟踪号数量
        $result_tracking_count = WhWaveTrackingBoxModel::select_by_shipOrderId($ebay_id);
         //说明该快递单只有一个箱子和一个跟踪号，所以不需要在wh_wave_tracking_box "快递单号的箱子与跟踪号数量表"添加记录
        if(empty($result_tracking_count)){
            $count_binding = WhOrderTracknumberModel::count($array_count);//发货单已经绑定跟踪号的数量
            if($count_binding > 0){
               $data = array(
                 "data" => array(
                   '该发货单号已经绑定好跟踪号了，不在需要和这个跟踪号绑定！'
                  ),
                 'link' => 'index.php?mod=expressRecheck&act=trackNumberInput'
                 );
                goErrMsgPage($data);
                exit();
            }
            WhBaseModel::begin();
            $result_insert = WhOrderTracknumberModel::insert($data_insert);
            if(!$result_insert){
                WhBaseModel::rollback();
                $data = array(
                 "data" => array(
                   '跟踪号绑定失败，请联系负责人！'
                  ),
                 'link' => 'index.php?mod=expressRecheck&act=trackNumberInput'
                 );
                goErrMsgPage($data);
                exit();
            }
            //更新发货表状态
            $ostatus = WhWaveOrderPartionScanReviewModel::updateShippingOrderStatus($ebay_id,$status=PKS_WAITING_LOADING);
            if(!$ostatus){
                WhBaseModel::rollback();
                $data = array(
                 "data" => array(
                   '更新发货单状态失败，请联系负责人！'
                  ),
                 'link' => 'index.php?mod=expressRecheck&act=trackNumberInput'
                 );
                goErrMsgPage($data);
                exit();
            }
            WhPushModel::pushOrderStatus($ebay_id,'PKS_WAITING_LOADING',$_SESSION['userId'],time());   //状态推送，需要改为待装车扫描（订单系统提供状态常量）		                     
            WhBaseModel::commit();
            $data = array(
                 "data" => array(
                   '绑定成功，请扫描另外一个发货单！'
                  ),
                 'link' => 'index.php?mod=expressRecheck&act=trackNumberInput'
             );
            goOkMsgPage($data);
        }else{ 
            $result_tracking_count = $result_tracking_count['trackingCount'];//发货单需要绑定跟踪号的数量
            $count_binding = WhOrderTracknumberModel::count($array_count);//发货单已经绑定跟踪号的数量
            if($result_tracking_count > $count_binding){
                 WhBaseModel::begin();
                $result_insert = WhOrderTracknumberModel::insert($data_insert);
                if(!$result_insert){
                    WhBaseModel::rollback();
                    $data = array(
                         "data" => array(
                           '跟踪号绑定失败，请联系负责人！'
                          ),
                         'link' => 'index.php?mod=expressRecheck&act=trackNumberInput'
                     );
                    goErrMsgPage($data);
                    exit();
                }
                $num = $result_tracking_count - $count_binding - 1 ;
                if($num ==0){
                     //更新发货表状态
                    $ostatus = WhWaveOrderPartionScanReviewModel::updateShippingOrderStatus($ebay_id,$status=PKS_WAITING_LOADING);
                    if(!$ostatus){
                        WhBaseModel::rollback();
                         $data = array(
                             "data" => array(
                               '更新发货单状态失败，请联系负责人！'
                              ),
                             'link' => 'index.php?mod=expressRecheck&act=trackNumberInput'
                         );
                        goErrMsgPage($data);
                        exit();
                    }
                    WhPushModel::pushOrderStatus($ebay_id,' ',$_SESSION['userId'],time());   //状态推送，需要改为待装车扫描（订单系统提供状态常量）		                    
                    WhBaseModel::commit();
                     $data = array(
                             "data" => array(
                               '绑定成功，该发货单不需要绑定跟踪号了！'
                              ),
                             'link' => 'index.php?mod=expressRecheck&act=trackNumberInput'
                         );                          
                        goErrMsgPage($data);                 
                }else{      
                    WhBaseModel::commit();
                      $data = array(
                             "data" => array(
                               '绑定成功，该发货单还需要绑定'.$num.'个跟踪号！'
                              ),
                             'link' => 'index.php?mod=expressRecheck&act=trackNumberInput'
                         );                          
                     goOkMsgPage($data);
                }
            }else{
                $data = array(
                     "data" => array(
                       '绑定失败，绑定的跟踪号已经够'.$result_tracking_count.'个，请扫描另外一个发货单！'
                      ),
                     'link' => 'index.php?mod=expressRecheck&act=trackNumberInput'
                 );
                goErrMsgPage($data);
                exit();
            }
        }
        
        $navlist = array( // 面包屑
                array(
                        'url' => '',
                        'title' => '出库'
                ),
                array(
                        'url' => 'index.php?mod=expressRecheck&act=ExpressList',
                        'title' => '快递待复核'
                ),
                array(
                        'url' => '',
                        'title' => '快递待复核数据导入 '
                )
        );
        $this->smarty->assign('navlist', $navlist);
        
        $toptitle = '待复核'; // 顶部链接
        $this->smarty->assign('toptitle', $toptitle);
        
        $toplevel = 2; // 顶层菜单
        $this->smarty->assign('toplevel', $toplevel);
        
        $secondlevel = '28'; // 当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
        
        $this->smarty->assign('secnev', 3); // 二级导航
        
        $this->smarty->assign('list', TrackInfoRecordModel::$data);
        $this->smarty->display('tracknumimport.htm');
    }
    
    /*
     * 快递复核扫描
     * 单一跟踪号
     */
    public function view_recheckScan(){
        
        $navlist = array( // 面包屑
                array(
                        'url' => '',
                        'title' => '出库'
                ),
                array(
                        'url' => 'index.php?mod=expressRecheck&act=ExpressList',
                        'title' => '快递待复核'
                ),
                array(
                        'url' => '',
                        'title' => '快递复核扫描(单跟踪号) '
                )
        );
        $this->smarty->assign('navlist', $navlist);
        
        $toptitle = '快递复核扫描(单跟踪号)'; // 顶部链接
        $this->smarty->assign('toptitle', $toptitle);
        
        $toplevel = 2; // 顶层菜单
        $this->smarty->assign('toplevel', $toplevel);
        
        $secondlevel = '28'; // 当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
        
        $this->smarty->assign('secnev', 3); // 二级导航
        
        $this->smarty->assign('list', TrackInfoRecordModel::$data);
        $this->smarty->display('trackscan.htm');
    }
    
    /*
     * 多跟踪号
     */
    public function view_recheckScanMul(){
        $navlist = array( // 面包屑
                array(
                        'url' => '',
                        'title' => '出库'
                ),
                array(
                        'url' => 'index.php?mod=expressRecheck&act=ExpressList',
                        'title' => '快递待复核'
                ),
                array(
                        'url' => '',
                        'title' => '快递复核扫描(多跟踪号) '
                )
        );
        $this->smarty->assign('navlist', $navlist);
        
        $toptitle = '快递复核扫描(多跟踪号)'; // 顶部链接
        $this->smarty->assign('toptitle', $toptitle);
        
        $toplevel = 2; // 顶层菜单
        $this->smarty->assign('toplevel', $toplevel);
        
        $secondlevel = '28'; // 当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
        
        $this->smarty->assign('secnev', 3); // 二级导航
        
        $this->smarty->assign('list', TrackInfoRecordModel::$data);
        $this->smarty->display('trackscanmul.htm');
    }
}

?>