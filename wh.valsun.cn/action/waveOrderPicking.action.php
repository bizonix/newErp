<?php
/**
 * 分拣操作
 * @author czq
 * 日期：2014-09-06
 */
class waveOrderPickingAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";
	
	/**
	 * 发货单投放
	 * @author czq
	 */
	public function act_shipOrderpick(){
		$waveId 		= isset($_POST['waveId']) ? intval($_POST['waveId']) : '';
		$shipOrderId 	= isset($_POST['shipOrderId']) ? intval(trim($_POST['shipOrderId'])) : '';

		$waveId = WhWaveInfoModel::number_decode($waveId);
		if(!$waveId || !$shipOrderId){
			$msg = array(
					'status' 		=> 'A00',
					'waveStatus'	=> 'A00',
					'msg'			=> '波次配货单不存在，参数错误',
			);
			echo json_encode($msg);
			exit;
		}
		
		$pickRecord = WhWaveShippingPickRecordModel::getRecordInfoByShipOrderId($shipOrderId);
		if(!$pickRecord){
			$msg = array(
					'status' 		=> 'A00',
					'waveStatus'	=> 'A00',
					'msg'			=> '此波次的发货单'.$shipOrderId.'不存在',
			);
			echo json_encode($msg);
			exit;
		}else if($pickRecord[0]['pickStatus'] == 1){
			$msg = array(
					'status' 		=> 'A00',
					'waveStatus'	=> 'A00',
					'msg'			=> '此发货单已投放过，桶号为:'.$pickRecord[0]['pickLight'],
			);
			echo json_encode($msg);
			exit;
		}
        
		//更新发货单投放记录表
		$data = array(
			'pickStatus' => 1,
			'pickUserId' => $_SESSION['userId'],
			'pickTime'	 => time(),
		);
		
		WhWaveShippingPickRecordModel::update($data,' shipOrderId='.$shipOrderId);
		//检查当前发货单是否已完结分拣
		$ShipOrderpickrecord = WhWaveShippingPickRecordModel::select("waveId='".$waveId."' AND pickStatus=0 AND is_delete=0");
		if(!$ShipOrderpickrecord){
			//最后一个投放
			$msg = array(
				'status' 		=> 'A'.($pickRecord[0]['pickLight'] >= 10 ? $pickRecord[0]['pickLight'] : '0'.$pickRecord[0]['pickLight']),
				'pickLight' 	=> $pickRecord[0]['pickLight'].'号桶',
				'shipOrderId' 	=> $pickRecord[0]['shipOrderId'],
				'waveStatus'	=> 'A99',
				'msg'			=> '发货单已投放完毕，请投放料号！',
				);
		}else{
				$msg = array(
				'status' 		=> 'A'.($pickRecord[0]['pickLight'] >= 10 ? $pickRecord[0]['pickLight'] : '0'.$pickRecord[0]['pickLight']),
				'pickLight' 	=> $pickRecord[0]['pickLight'].'号桶',
				'shipOrderId' 	=> $pickRecord[0]['shipOrderId'],
				'waveStatus'	=> 'A00',
				'msg'			=> '',
				);
			}
		echo json_encode($msg);
		exit;
	}
}	
?>	