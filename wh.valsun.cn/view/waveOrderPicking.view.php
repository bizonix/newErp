<?php
/*
 *待分拣
 *@author cmf
 */
class waveOrderPickingView extends CommonView{
	/*
	* 构造函数
	*/
    public function __construct() {
        parent::__construct();
    }
	
	public function view_index(){
		$navlist = array(
			array('url' => '', 'title' => '出库 '),
			array('url' => '', 'title' => ' 待分拣'),
		);
		$toplevel = 2;
		$secondlevel = 30;
		$toptitle = '仓库出库 - 待分拣';
    	$this->smarty->assign('toptitle', $toptitle);
		$this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('toplevel', $toplevel);
        $this->smarty->assign('secondlevel', $secondlevel);
		
        $this->smarty->display('waveOrderPicking_index.htm');
	}
	
	public function view_waveinit(){
		$waveId = WhWaveInfoModel::number_decode($_POST['waveId']);
		//判断每个区域是否已配货完结
		$list = WhWaveReceiveRecordModel::select("waveId='$waveId'");
		if(!$list){
			$result = array(
				'status'	=> 'A00',
				'msg'		=> '配货单收货记录不存在'
			);
			echo json_encode($result);
			exit;
		}else{
			$areas = array();
			foreach($list as $val){
				if($val['scanStatus'] < 2){
					$areas[] = $val['area'];
				}
			}
			if($areas){
				$result = array(
					'status'	=> 'A00',
					'msg'		=> '部分区域未完成收货，暂不能执行分拣<br/>'.implode(', ', $areas)
				);
				echo json_encode($result);
				exit;
			}
		}
		//分配筒号(亮灯)
		$orderlist = WhWaveShippingRelationModel::select("waveId='$waveId' AND is_delete=0 order by shipOrderId asc");
		$light = 1;
		$shipOrderPickData = array();
		foreach($orderlist as $val){
			$data = array(
				'pickLight'		=> $light,
				'pickTime'		=> time(),
				'pickUserId'	=> $_SESSION['userId'],
			);
			WhWaveShippingRelationModel::update($data, $val['id']);
			
			//生成发货单投放记录表
			$shipOrderPickData[] = array(
					'waveId'		=> $waveId,
					'shipOrderId'	=> $val['shipOrderId'],
					'pickStatus'	=> 0,
					'pickTime'		=> 0,
					'pickUserId'	=> 0,
					'is_delete'		=> 0,
			);
				
			$light++;
		}
		//查看是否有此波次的发货单投放记录
		$shipOrderPick = WhWaveShippingPickRecordModel::find(' waveId='.$waveId);
		if(!$shipOrderPick){
			WhWaveShippingPickRecordModel::insert($shipOrderPickData,true);
		}
		
		//生成分拣记录表
		$pick = WhWavePickRecordModel::find("waveId='$waveId'");
		if(!$pick){
			$list = WhShippingOrderdetailModel::getShippingOrderSkuList($waveId);
			foreach($list as $val){
				$data = array(
					'waveId'		=> $val['waveId'],
					'shipOrderId'	=> $val['shipOrderId'],
					'sku'			=> $val['sku'],
					'skuAmount'		=> $val['amount'],
					'amount'		=> 0,
					'pickStatus'	=> 0,
					'pickUserId'	=> 0,
					'pickTime'		=> 0,
					'deleteUserId'	=> '0',
					'deleteTime'	=> '0',
					'is_delete'		=> '0',				
				);
				$picklist[] = $data;
			}
			WhWavePickRecordModel::insert($picklist, true);
		}
		//扫描配货单的时候，查看配货单是否已经配货完成
		$shipOrderPicks = WhWaveShippingPickRecordModel::select(' waveId='.$waveId.' AND pickStatus = 0','*');
		$hasShippingPick = false;
		$message = '配货单筒号分配正常，可以执行分拣（当前流程是SKU分拣）';
		if(count($shipOrderPicks) > 0){
			$hasShippingPick = true;
			$message = '配货单筒号分配正常，可以执行分拣（当前流程是发货单投放）';
		}
		
		$result = array(
			'status'		 	=> 'A99',
			'hasShippingPick'	=> $hasShippingPick,
			'msg'				=> $message
		);
		echo json_encode($result);
	}
	
	public function view_skupick(){
		$waveId = $_POST['waveId'] ? $_POST['waveId'] : $_GET['waveId'];
		$waveId = WhWaveInfoModel::number_decode($waveId);
		$sku = get_goodsSn($_POST['sku'] ? $_POST['sku'] : $_GET['sku']);
		if($waveId)$wave = WhWaveInfoModel::find($waveId);
		if(!$waveId || !$wave){
			$msg = array(
				'status' 		=> 'A00',
				'waveStatus'	=> 'A00',
				'msg'			=> '波次配货单不存在，参数错误',
			);
			echo json_encode($msg);
			exit;
		}
		$recordlist = array();
		$recordlist = WhWaveScanRecordModel::getRecordInfoBySku($sku, $waveId);
		if(!$recordlist){
			$msg = array(
				'status' 		=> 'A00',
				'waveStatus'	=> 'A00',
				'msg'			=> '料号对应的发货单不存在，请确认料号输入正确',
			);
			echo json_encode($msg);
			exit;			
		}
		$record = array();
		$firstRecord = array();
		foreach($recordlist as $val){
			if(!$firstRecord)$firstRecord = $val;
			if(!$val['pickStatus']){
				$record = $val;
				break;
			}
		}
		if($record['pickLight']){
			if($record && !$record['record_id']){
				//料号无分拣，插入新记录
				$new_record = array(
					'waveId'		=> $record['waveId'],
					'shipOrderId'	=> $record['shipOrderId'],
					'sku'			=> $record['sku'],
					'skuAmount'		=> $record['skuAmount'],
					'amount'		=> '1',
					'pickStatus'	=> $record['skuAmount'] == 1 ? 1 : 0,
					'pickUserId'	=> intval($_SESSION['userId']),
					'pickTime'		=> time(),
					'deleteUserId'	=> '0',
					'deleteTime'	=> '0',
					'is_delete'		=> '0',
				);
				$record_id = WhWavePickRecordModel::insert($new_record);
			}else if($record){
				$data = array();
				$data['amount'] = $record['pickcount'] + 1;
				if($data['amount'] >= $record['skuAmount']){
					$data['pickStatus'] = 1;	
				}
				if(!$record['pickUserId']){
					$data['pickUserId'] = intval($_SESSION['userId']);
					$data['pickTime'] = time();
				}
				WhWavePickRecordModel::update($data, $record['record_id']);
			}
			$msg = array(
				'status' 		=> 'A'.($record['pickLight'] >= 10 ? $record['pickLight'] : '0'.$record['pickLight']),
				'pickLight' 	=> $record['pickLight'].'号桶',
				'shipOrderId' 	=> $record['shipOrderId'],
				'waveStatus'	=> 'A00',
				'msg'			=> '',
			);
			
			//检查当前发货单是否已完结分拣
			$pickrecord = WhWavePickRecordModel::find("shipOrderId='".$record['shipOrderId']."' AND pickStatus=0 AND is_delete=0");
			if(!$pickrecord){    
	    		//快递小包通用待复核
	    		$data = array(
	    			'orderStatus' => PKS_WIQC
	    		);
	    		WhShippingOrderModel::update($data, "id='".$record['shipOrderId']."'");	
	    		WhPushModel::pushOrderStatus($record['shipOrderId'], 'PKS_WIQC', $_SESSION['userId'], time());	    		
			}
		}else{
			if($firstRecord){
				$msg = array(
					'status' 		=> 'A00',
					'pickLight' 	=> $firstRecord['pickLight'].'号桶',
					'shipOrderId' 	=> $firstRecord['shipOrderId'],
					'waveStatus'	=> 'A00',
					'msg'			=> '料号['.$sku.']已完成分拣',
				);
			}else{
				$msg = array(
					'status' 		=> 'A00',
					'waveStatus'	=> 'A00',
					'msg'			=> '料号['.$sku.']未找到对应发货单和筒号',
				);
			}
		}
		//检查当前波次是否已分拣完结
		$pickstatus = WhWavePickRecordModel::checkPickStatus($waveId);
		if($pickstatus === true){
			/*$data = array(
				'waveStatus' => 5
			);
			WhWaveInfoModel::update($data, $waveId);*/
			//返回波次完结信息
			$msg['waveStatus'] 	= 'A99';
			$msg['msg'] 		= '波次配货单已分拣完结';
		}
		echo json_encode($msg);
	}
	
	public function view_stoppicking(){
	    $_POST['waveId'] = $_POST['waveId'] ? $_POST['waveId'] : $_GET['waveId'];
		$waveId = WhWaveInfoModel::number_decode($_POST['waveId']);
		if($waveId)$wave = WhWaveInfoModel::find($waveId);
		if(!$waveId || !$wave){
			$msg = array(
				'status' 		=> 'A00',
				'waveStatus'	=> 'A00',
				'msg'			=> '波次配货单不存在，参数错误',
			);
			echo json_encode($msg);
		}
		//检查当前波次是否已分拣完结
		$list = WhWavePickRecordModel::getSkuPickRecord($waveId);
		if($list){
			//把未完结的发货记录手动完结，修改配货记录状态为手动完结
			$Pickmessage = '异常发货单：<br/>';
			foreach($list as $val){
				if(WhWavePickRecordModel::update(array('pickStatus'=>3),' shipOrderId='.$val['shipOrderId'].' AND pickStatus = 0 AND is_delete = 0')){
					//修改配货单的状态为待复核
					if(WhShippingOrderModel::update(array('orderStatus'=>PKS_UNUSUAL_SHIPPING_INVOICE),' id='.$val['shipOrderId'])){
						$Pickmessage .= "发货单：{$val['shipOrderId']}--桶号：{$val['pickLight']}<br/>";
					}
				}
			}
			$msg = array(
				'waveStatus'	=> 'A99',
				'msg'			=> $Pickmessage,
			);
		}else{
			$msg = array(
				'status' 		=> 'A00',
				'waveStatus'	=> 'A99',
				'msg'			=> '波次已完成分拣，不需要手动完结',
			);			
		}
		echo json_encode($msg);
	}
}
?>