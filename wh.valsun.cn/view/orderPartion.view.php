<?php
/**
 * 分区扫描
 * @author heminghua
 */
class orderPartionView extends CommonView {
    
    /*
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
    }
	public function view_orderPartion(){
		$navlist = array(array('url'=>'','title'=>'出库'),              //面包屑数据
				 array('url'=>'','title'=>'分区扫描'),
		);
        $secnev = 3;
        $toplevel = 2;
        $secondlevel = 27;
		global $memc_obj;
        $userId = $_SESSION['userId'];
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('secnev',  $secnev);
        $this->smarty->assign('toplevel',  $toplevel);
        $this->smarty->assign('secondlevel',  $secondlevel);
        /*
        $partion    =   array(
                            '中国邮政平邮(深圳)第一区',
                            '中国邮政平邮(深圳)第二区',
                            '中国邮政平邮(深圳)第三区',
                            '中国邮政平邮(深圳)第四区',
                            '中国邮政平邮(深圳)第五区',
                            '中国邮政平邮(深圳)第六区',
                            '中国邮政平邮(福建)第七区',
                            '中国邮政平邮(深圳)第八区',
                            '中国邮政平邮(泉州)第九区',
                            '中国邮政平邮(深圳)第十区',
                            '中国邮政挂号(深圳)',
                            '中国邮政挂号(深圳)第四区',
                            '中国邮政挂号(福建)第七区',
                            '中国邮政挂号(深圳)第八区',
                            '中国邮政挂号(泉州)第九区',
                            'EUB',
                            'UPS美国专线',
                            'Global Mail',
                            '非德国Global Mail',
                            '德国邮政挂号',
                            '香港小包平邮',
                            '香港小包挂号',
                            '俄速通挂号',
                            '俄速通平邮',
                            '新加坡DHL GM平邮',
                            '新加坡DHL GM挂号',
                            '瑞士小包平邮A区',
                            '瑞士小包平邮B区',
                            '瑞士小包平邮C区',
                            '瑞士小包平邮D区',
                            '瑞士小包平邮S区',
                            '瑞士小包挂号A区',
                            '瑞士小包挂号B区',
                            '瑞士小包挂号C区',
                            '瑞士小包挂号D区',
                            '瑞士小包挂号S区',
                            '比利时小包EU'
                        );
		*/
		
		$partion = WhTransportPartitionModel::select("is_delete=0 AND status=1");
		$this->smarty->assign("partion",$partion);

		$toptitle = '分区扫描操作';             //头部title
        $this->smarty->assign('toptitle', $toptitle);
        $this->smarty->display('orderPartion.htm');
	}
	/**
	 * orderPartionView::view_orderPartionPrint()
	 * 口袋模版打印界面
	 * @return
	 */
	public function view_orderPartionPrint(){
        $type   =   trim($_POST['type']); //口袋类型
        switch($type){
            case 'common':
                $file   =   'orderPartionPrint.php';
                break;
            case 'singapore':
                $file   =   'orderPartionPrint_singapore.php';
                break;
            case 'sailvan':
                $file   =   'orderPartionPrint_sailvan.php';
                break;
            default:
                return FALSE;
        }
		include_once "../html/template/v1/".$file;
	}
  
    public function view_savepacket(){
		$packageid    =   intval($_POST['packageid']);
		$packet       =   WhOrderPartionPrintModel::find($packageid, 'status,partion,partitionId');
		if(!$packet){
	    	$result = array(
	    		'status' 	=> 0,
	    		'msg' 		=> '口袋编号不存在'
	    	);
	    	echo json_encode($result);
            exit;
		}
        $status     =   $packet['status'];
        $partion    =   $packet['partion'];
        $partionId  =   $packet['partitionId'];        
        unset($packet);
		if($status != 0){
	    	$result = array(
	    		'status' 	=> 0,
	    		'msg' 		=> '该包裹已使用,请更换包裹编号！'
	    	);
	    	echo json_encode($result);
            exit;
		}
		if(!$_SESSION['userId']){
	    	$result = array(
	    		'status' 	=> 0,
	    		'msg' 		=> '请先登录系统'
	    	);
	    	echo json_encode($result);
            exit;
		}
		$record       =   WhOrderPartionRecordsModel::getPartionRecords($partionId, $_SESSION['userId']);
		$totalnum     =   $record['totalnum'];
		$totalweight  =   $record['totalweight'];
	    if(!$totalnum && !$totalweight){
	    	$result = array(
	    		'status' 	=> 0,
	    		'msg' 		=> '该包裹分区下没有订单'
	    	);
	    	echo json_encode($result);exit;
	    }
	    //更新口袋分区
	    $data = array(
	    	'packageId' 	=> $packageid,
	    	'modifyTime' 	=> time()
	    );
	    WhOrderPartionRecordsModel::update($data, "partitionId='".$partionId."' AND packageId=0 AND scanUserId='".$_SESSION['userId']."'");
	    //更新口袋打包
	    $packet_data = array(
	    	'totalWeight' 	=> $totalweight,
	    	'totalNum' 		=> $totalnum,
	    	'status' 		=> 1,
	    	'modifyTime' 	=> time()
	    );
	    WhOrderPartionPrintModel::update($packet_data, $packageid);
    	$result = array(
    		'status' 		=> 1,
    		'totalWeight' 	=> round($totalweight/1000, 3),
    		'totalNum' 		=> $totalnum,
    		'msg' 			=> '口袋打包成功'
    	);
    	//更新订单状态
    	$orders = WhOrderPartionRecordsModel::select("partitionId='".$partionId."' AND packageId='".$packageid."' AND scanUserId='".$_SESSION['userId']."'");
    	foreach($orders as $val){
    		$orderIds[] = $val['shipOrderId'];
            WhPushModel::pushOrderStatus($val['shipOrderId'],'PKS_DISTRICT_CHECKING',$_SESSION['userId'],time());        //状态推送，需要改为待分区复核（订单系统提供状态常量）		    			                 
    	}
    	$data = array(
    		'orderStatus' => PKS_DISTRICT_CHECKING
    	);
    	WhShippingOrderModel::update($data, array('id in'=>$orderIds));
     	echo json_encode($result);
    }
	
}
?>