<?php
/**
 * 发货单打印
 * @author cmf
 */
class WaveOrderPrintingView extends CommonView {
	public static $errCode = 0;
	public static $errMsg = "";
    /**
     * 发货单打印首页
     * @author cmf
     */
    public function view_index(){
		$navlist = array(
			array('url' => '', 'title' => '出库 '),
			array('url' => '', 'title' => ' 发货单打印'),
		);
		$toplevel = 2;
		$secondlevel = 22;
		$toptitle = '仓库出库 - 发货单打印';
    	$this->smarty->assign('toptitle', $toptitle);
		$this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('toplevel', $toplevel);
        $this->smarty->assign('secondlevel', $secondlevel);
        
        $this->smarty->display('waveOrderPrinting_index.htm');
    }
    
    public function view_orderlist(){
    	$waveId = WhWaveInfoModel::number_decode($_POST['waveId']);
    	if(!$waveId){
    		$return = array(
    			'status'	=> 0,
    			'msg'		=> '请输入配货单号',
    		);
    		echo json_encode($return);
    		exit;
    	}
    	$wave = WhWaveInfoModel::find($waveId);
    	if($wave){
	    	//波次配货单状态
	    //	$waveTypes = array(
	    //		'many'	=> array(5, 6),		//多料号
	    //		'one'	=> array(2, 3, 4),	//单料号
	    //		'order' => array(1)			//单发货单
	    //	); 
    		if($wave['waveStatus'] < 3){
    			//未完成配货 waveStatus:3 完成配货
    			$return = array(
	    			'status'	=> 0,
	    			'msg'		=> '该配货单未完成配货，不能打印',
	    		);
	    		echo json_encode($return);
	    		exit; 
    		}
    	//	if(in_array($wave['waveType'], $waveTypes['many']) && $wave['waveStatus'] < 5){
    			//多料号检查是否分拣完成 waveStatus:5 分拣完成
	    	//	$return = array(
	    	//		'status'	=> 0,
	    	//		'msg'		=> '该配货单未完成分拣，不能打印',
	    	//	);
	    	//	echo json_encode($return);
	    	//	exit;    			
    	//	}
			if($wave['waveType']==2){
	    		//单料号的配货单
	    		$temp_orderlist = WhWaveScanRecordModel::getShipOrders($waveId);
	    	}else{
	    		$temp_orderlist = WhWaveShippingRelationModel::getShipOrders($waveId);
	    	}
    		if(!$temp_orderlist){
	    		$return = array(
	    			'status'	=> 0,
	    			'msg'		=> '当前波次无发货单',
	    		);
    		}else{
	    		foreach($temp_orderlist as $val){
	    			$orderlist[] = $val['shipOrderId'];
	    		}
	    		$return = array(
	    			'status'	=> 1,
	    			'msg'		=> $orderlist,
	    		);
	    	}
    		echo json_encode($return);
    		exit;
    	}else{
    		$return = array(
    			'status'	=> 0,
    			'msg'		=> '配货单不存在，请检查是否输入正确',
    		);
    		echo json_encode($return);
    		exit;
    	}
    }
    
    public function view_startprint(){	
    	$_POST['waveId'] = $_POST['waveId'] ? $_POST['waveId'] : $_GET['waveId'];
    	$_POST['waveIds'] = $_POST['waveIds'] ? $_POST['waveIds'] : $_GET['waveIds'];
    	$_POST['shipOrderId'] = $_POST['shipOrderId'] ? $_POST['shipOrderId'] : $_GET['shipOrderId'];
    	$confirm = $_GET['confirm'] ? true : false;
    	$waveId = WhWaveInfoModel::number_decode($_POST['waveId']);
    	if(!$waveId){
    		echo '无配货单号，请检查是否输入有误';
    		exit;
    	}
    	$wave = array();
    	if($waveId){
    		$wave = WhWaveInfoModel::find($waveId);
    	}
    	//波次配货单状态
    	$waveTypes = array(
    		'many'	=> array(5, 6),		//多料号
    		'one'	=> array(2, 3, 4),	//单料号
    		'order' => array(1)			//单发货单
    	); 
    	if(!$wave){
	    	echo '该配货单不存在，不能打印';           
	    	exit;
    	}else{
    		if($wave['waveStatus'] < 3 && !$confirm){
    			//未完成配货 waveStatus:3 完成配货
    			//echo '该配货单未完成配货，是否确认要打印？<button onclick="location.href=\'index.php?mod=waveOrderPrinting&act=startprint&confirm=1&waveId='.$_POST['waveId'].'&waveIds='.$_POST['waveIds'].'&shipOrderId='.$_POST['shipOrderId'].'\'" >确认打印</button>';
    			echo '该配货单未完成配货，不能打印';
	    		exit;
    		}
    		if(in_array($wave['waveType'], $waveTypes['many']) && $wave['waveStatus'] < 5){
    			//多料号检查是否分拣完成 waveStatus:5 分拣完成
	    		echo '该配货单未完成分拣，不能打印';
	    		exit;
    		}
    		if(in_array($wave['waveType'], $waveTypes['order'])){
    			//单发货单，检查是否被拆分多波次，验证全部波次
    			$vo = WhWaveShippingRelationModel::find("waveId='".$waveId."' AND is_delete=0");
    			if(!$vo){
    				echo '该配货单下无发货单，不能打印';
	    			exit; 
    			}
    			$orders = WhWaveShippingRelationModel::select("shipOrderId='".$vo['shipOrderId']."'");
    			//已收到波次
    			$temp_waveIds = explode(',', $_POST['waveIds']);
    			foreach($temp_waveIds as $val){
    				if($val){
    					$waveIds[] = WhWaveInfoModel::number_decode($val);
    				}
    			}
    			$notWaveIds = array();
    			foreach($orders as $val){
    				if(!in_array($val['waveId'], $waveIds)){
    					$notWaveIds[] = $val['waveId'];
    				}
    			}
    			if($notWaveIds){
    				echo '该波次为单发货单拆分波次，请连续扫描多个配货单后再打印';
	    			exit; 
    			}
    		}
    	}
    	if(in_array($wave['waveType'], $waveTypes['one'])){
    		//单料号的配货单
    		$orderlist = WhWaveScanRecordModel::getShipOrders($waveId, $_POST['shipOrderId']);
    		$this->smarty->assign('onesku', true);
    	}else{
    		$orderlist = WhWaveShippingRelationModel::getShipOrders($waveId, $_POST['shipOrderId']);	
    		$this->smarty->assign('onesku', false);
    	}
    	if(!$orderlist){
	    	echo '该配货单下无发货单，不能打印';
          //   echo "<script>alert('该配货单下无发货单，不能打印');</script>";
           //         redirect_to("index.php?mod=waveOrderPrinting&act=index");
	    	exit;
    	}
    	//if(!in_array($wave['waveType'], $waveTypes['many'])){
    		//非多料号波次，更新状态为待复核
    		//WhWaveInfoModel::update(array('waveStatus' => 6), $wave['id']);
    	//}
    	$carries = WhBaseModel::cache('trans.carrier.info.get');
    	if(!$carries){
	    	//接口获取快递运输方式
			require_once WEB_PATH."html/api/include/opensys_functions.php";
			$paramArr = array(
				'method'	=> 'trans.carrier.info.get',
				'format'	=> 'json',
				'v'			=> '1.0',
				'username'	=> 'purchase',
				'type'		=> 1	//0非快递，1-快递，2-全部
			);
	    	$result = json_decode(callOpenSystem($paramArr), true);
	    	$templist = $result['data'];
	    	if($templist){
		    	foreach($templist as $val){
		    		$carries[$val['id']] = $val;
		    		$carries['express_ids'][] = $val['id'];
		    	}
		    }
		    WhBaseModel::cache('trans.carrier.info.get', $carries);
		}
    	foreach($orderlist as $key => $val){
    		if($val['transportId'] && in_array($val['transportId'], $carries['express_ids'])){
    			$val['isexpress'] = 1;
    			$express_ordids[] = $val['shipOrderId'];
    		}else{
    			$val['isexpress'] = 0;
    			$ordids[] = $val['shipOrderId'];
    		}
    		$orderlist[$key] = $val;
    	}
    	if($ordids){
    		//小包待复核
    		$data = array(
    			'orderStatus' => PKS_WIQC
    		);
    	//	WhShippingOrderModel::update($data, "id IN('".implode("','", $ordids)."')");
    	}
    	if($express_ordids){
    		//快递待复核
    		$data = array(
    			'orderStatus' => PKS_EX_TNRCK
    		);
    	//	WhShippingOrderModel::update($data, "id IN('".implode("','", $express_ordids)."')");
    	}
    	$this->smarty->assign('orderlist', $orderlist);
    	$this->smarty->display('waveOrderPrinting_startprint.htm');
    }
    
       /**
        * WaveOrderPrintingView::view_print_all()
        * 对配货单进行判断是否符合要求
        * @author cxy
        * @return void
        */
       public function view_print_all(){	
    	$_POST['waveId'] = $_POST['waveId'] ? $_POST['waveId'] : $_GET['waveId'];
    	$_POST['waveIds'] = $_POST['waveIds'] ? $_POST['waveIds'] : $_GET['waveIds'];
    	$_POST['shipOrderId'] = $_POST['shipOrderId'] ? $_POST['shipOrderId'] : $_GET['shipOrderId'];
    	$confirm = $_GET['confirm'] ? true : false;
       // print_r($_POST);exit;
    	$waveId = WhWaveInfoModel::number_decode($_POST['waveId']);
    	if(!$waveId){
  	        $return = array(
    			'status'	=> 0,
    			'msg'		=> '配货单不存在，请检查是否输入正确',
    		);
            echo json_encode($return);
    		exit;
    	}
    	$wave = array();
    	if($waveId){
    		$wave = WhWaveInfoModel::find($waveId);
    	}
    	//波次配货单状态
    	$waveTypes = array(
    		'many'	=> array(5, 6),		//多料号
    		'one'	=> array(2, 3, 4),	//单料号
    		'order' => array(1)			//单发货单
    	); 
    	if(!$wave){
	    //	echo '该配货单不存在，不能打印'; 
            $return = array(
    			'status'	=> 1,
    			'msg'		=> '该配货单不存在，不能打印',
    		);
            echo json_encode($return);
    		exit;
    	}else{
 
    		if($wave['waveStatus'] < 3 && !$confirm){
                $return = array(
					'status'	=> 1,
					'msg'		=> '该配货单未完成配货，不能打印',
				);
				echo json_encode($return);
				exit;  
    		}
    		if(in_array($wave['waveType'], $waveTypes['order'])){
    			//单发货单，检查是否被拆分多波次，验证全部波次
    			$vo = WhWaveShippingRelationModel::find("waveId='".$waveId."' AND is_delete=0");
    			if(!$vo){
	    		    $return = array(
					'status'	=> 1,
					'msg'		=> '该配货单下无发货单，不能打印',
					);
					echo json_encode($return);
					exit;  
    			}
    			$orders = WhWaveShippingRelationModel::select("shipOrderId='".$vo['shipOrderId']."'");
    			//已收到波次
    			$temp_waveIds = explode(',', $_POST['waveIds']);
                $waveIds =array();
    			foreach($temp_waveIds as $val){
    				if($val){
    					$waveIds[] = WhWaveInfoModel::number_decode($val);
    				}
    			}
    			$notWaveIds = array();
    			foreach($orders as $val){
    				if(!in_array($val['waveId'], $waveIds)){
    					$notWaveIds[] = $val['waveId'];
    				}
    			}
    			if($notWaveIds){
    			 	$return = array(
					'status'	=> 1,
					'msg'		=> '该波次为单发货单拆分波次，请连续扫描多个配货单后再打印',
					);
					echo json_encode($return);
					exit; 
    			}
    		}
    	}
        /*
    	if(in_array($wave['waveType'], $waveTypes['one'])){
    		//单料号的配货单
    		$orderlist = WhWaveScanRecordModel::getShipOrders($waveId, $_POST['shipOrderId']);
            $onesku = true ;
    	//	$this->smarty->assign('onesku', true);
    	}else{
    		$orderlist = WhWaveShippingRelationModel::getShipOrders($waveId, $_POST['shipOrderId']);	
    		//$this->smarty->assign('onesku', false);
             $onesku = false ;
    	}
       // echo $_POST['shipOrderId'];
      //  print_r($orderlist);exit;
        
    	if(!$orderlist){
    	    $return = array(
    			'status'	=> 1,
    			'msg'		=> '该配货单下无发货单，不能打印',
    	    	);
                echo json_encode($return);
    	      	exit; 
	    //	echo '该配货单下无发货单，不能打印';
          //   echo "<script>alert('该配货单下无发货单，不能打印');</script>";
           //         redirect_to("index.php?mod=waveOrderPrinting&act=index");
	    //	exit;
    	}
    	//if(!in_array($wave['waveType'], $waveTypes['many'])){
    		//非多料号波次，更新状态为待复核
    		//WhWaveInfoModel::update(array('waveStatus' => 6), $wave['id']);
    	//}
    	$carries = WhBaseModel::cache('trans.carrier.info.get');
    	if(!$carries){
	    	//接口获取快递运输方式
			require_once WEB_PATH."html/api/include/opensys_functions.php";
			$paramArr = array(
				'method'	=> 'trans.carrier.info.get',
				'format'	=> 'json',
				'v'			=> '1.0',
				'username'	=> 'purchase',
				'type'		=> 1	//0非快递，1-快递，2-全部
			);
	    	$result = json_decode(callOpenSystem($paramArr), true);
	    	$templist = $result['data'];
	    	if($templist){
		    	foreach($templist as $val){
		    		$carries[$val['id']] = $val;
		    		$carries['express_ids'][] = $val['id'];
		    	}
		    }
		    WhBaseModel::cache('trans.carrier.info.get', $carries);
		}
    	foreach($orderlist as $key => $val){
    		if($val['transportId'] && in_array($val['transportId'], $carries['express_ids'])){
    			$val['isexpress'] = 1;
    			$express_ordids[] = $val['shipOrderId'];
    		}else{
    			$val['isexpress'] = 0;
    			$ordids[] = $val['shipOrderId'];
    		}
    		$orderlist[$key] = $val;
    	}
        */
			$return = array(
    			'status'       => 200,
    			'msg'	       => '请打印',
                'waveId'       => $_POST['waveId'],
                'shipOrderId'  => $_POST['shipOrderId'],
                
    	    	);
            echo json_encode($return);
    	    exit;  

    }
    /**
     * WaveOrderPrintingView::view_prints()
     * 对配货单进行打印预览
     * @author cxy 
     * @return void
     */
    public function view_prints(){
        $shipOrderId = trim($_GET['shipOrderId']);
        $waveId      = trim($_GET['waveId']);
       	$wave        = WhWaveInfoModel::find($waveId);
        if($wave['waveType']==2){
    		//单料号的配货单
            //if($shipOrderId !='null'){   
            //    echo $shipOrderId;
    	    //   	$orderlist = WhWaveScanRecordModel::getShipOrders($waveId, $shipOrderId);
           // }else{               
    	    	$orderlist = WhWaveScanRecordModel::getShipOrders($waveId);
           // }
            $onesku = true;
    	}else{    	
            // if($shipOrderId !='null'){
            //    	$orderlist = WhWaveShippingRelationModel::getShipOrders($waveId, $shipOrderId);	
            //}else{
                	$orderlist = WhWaveShippingRelationModel::getShipOrders($waveId);	
           // }
              $onesku = false;
    	}              
    	if(!$orderlist){
    	    $return = array(
    			'status'	=> 1,
    			'msg'		=> '该配货单下无此发货单，不能打印',
    	    	);
                echo $return;
    	      	exit; 
    	}
    	$carries = WhBaseModel::cache('trans.carrier.info.get');
    	if(!$carries){
	    	//接口获取快递运输方式
			require_once WEB_PATH."html/api/include/opensys_functions.php";
			$paramArr = array(
				'method'	=> 'trans.carrier.info.get',
				'format'	=> 'json',
				'v'			=> '1.0',
				'username'	=> 'purchase',
				'type'		=> 1	//0非快递，1-快递，2-全部
			);
	    	$result = json_decode(callOpenSystem($paramArr), true);
	    	$templist = $result['data'];
	    	if($templist){
		    	foreach($templist as $val){
		    		$carries[$val['id']] = $val;
		    		$carries['express_ids'][] = $val['id'];
		    	}
		    }
		    WhBaseModel::cache('trans.carrier.info.get', $carries);
		}
    	foreach($orderlist as $key => $val){
    		if($val['transportId'] && in_array($val['transportId'], $carries['express_ids'])){
    			$val['isexpress'] = 1;
    			$express_ordids[] = $val['shipOrderId'];
    		}else{
    			$val['isexpress'] = 0;
    			$ordids[] = $val['shipOrderId'];
    		}
    		$orderlist[$key] = $val;
        }
       // var_dump($onesku);
       // print_r($orderlist);
       
      	$this->smarty->assign('onesku', $onesku);
       	$this->smarty->assign('orderlist', $orderlist);
    	$this->smarty->display('waveOrderPrinting_startprint.htm');
    }


}